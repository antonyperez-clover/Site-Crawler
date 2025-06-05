<?php

/*
 * This file is part of the SiteOne Crawler.
 *
 * (c) Ján Regeš <jan.reges@siteone.cz>
 */

declare(strict_types=1);

namespace Crawler\ContentProcessor;

use Crawler\Crawler;
use Crawler\FoundUrl;
use Crawler\FoundUrls;
use Crawler\ParsedUrl;
use Crawler\Utils;

class CssProcessor extends BaseProcessor implements ContentProcessor
{
    protected array $relevantContentTypes = [
        Crawler::CONTENT_TYPE_ID_HTML,
        Crawler::CONTENT_TYPE_ID_STYLESHEET,
    ];

    private readonly bool $imagesSupported;
    private readonly bool $fontsSupported;

    /**
     * @param Crawler $crawler
     */
    public function __construct(Crawler $crawler)
    {
        parent::__construct($crawler);

        $this->imagesSupported = !$this->options->disableImages;
        $this->fontsSupported = !$this->options->disableFonts;
    }

    /**
     * @inheritDoc
     */
    public function findUrls(string $content, ParsedUrl $sourceUrl): ?FoundUrls
    {
        preg_match_all('/url\s*\(\s*["\']?([^"\')]+)["\']?\s*\)/im', $content, $matches);
        $foundUrlsTxt = $matches[1];

        $foundUrlsTxt = array_filter($foundUrlsTxt, function ($url) {
            $isImage = preg_match('/\.(jpg|jpeg|png|gif|webp|avif|svg|ico|tif|bmp)(|\?.*)$/i', $url) === 1;
            $isFont = preg_match('/\.(eot|ttf|woff2|woff|otf)(|\?.*)$/i', $url) === 1;
            return ($this->imagesSupported && $isImage) || ($this->fontsSupported && $isFont);
        });

        $foundUrls = new FoundUrls();
        $foundUrls->addUrlsFromTextArray($foundUrlsTxt, $sourceUrl->getFullUrl(true, false), FoundUrl::SOURCE_CSS_URL);
        return $foundUrls;
    }

    /**
     * @inheritDoc
     */
    public function applyContentChangesForOfflineVersion(string &$content, int $contentType, ParsedUrl $url): void
    {
        $pattern = '/url\((["\']?)([^)]+\.[a-z0-9]{1,10}[^)]*)\1\)/i';

        $content = preg_replace_callback($pattern, function ($matches) use ($url) {
            // if is data URI, dotted relative path or #anchor, do not convert
            $foundUrl = $matches[2];
            if (!Utils::isHrefForRequestableResource($foundUrl) || str_starts_with($foundUrl, '.') || str_starts_with($matches[2], '#')) {
                return $matches[0];
            }
            $relativeUrl = $this->convertUrlToRelative($url, $foundUrl);

            return 'url(' . $matches[1] . $relativeUrl . $matches[1] . ')';
        }, $content);

        $content = $this->removeUnwantedCodeFromCss($content);

    }

    /**
     * Remove all unwanted code from CSS with respect to --disable-* options
     *
     * @param string $css
     * @return string
     */
    private function removeUnwantedCodeFromCss(string $css): string
    {
        if (!$this->fontsSupported) {
            $css = Utils::stripFonts($css);
        }
        if (!$this->imagesSupported) {
            $css = Utils::stripImages($css);
        }

        return $css;
    }

}