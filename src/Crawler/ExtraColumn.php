<?php

/*
 * This file is part of the SiteOne Crawler.
 *
 * (c) Ján Regeš <jan.reges@siteone.cz>
 */

declare(strict_types=1);

namespace Crawler;

class ExtraColumn
{

    public readonly string $name;
    public readonly ?int $length;
    public readonly bool $truncate;

    private static array $defaultColumnSizes = [
        'Title' => 20,
        'Description' => 20,
        'Keywords' => 20,
    ];

    public function __construct(string $name, ?int $length, bool $truncate)
    {
        $this->name = $name;
        $this->length = $length;
        $this->truncate = $truncate;
    }

    public function getLength(): int
    {
        return $this->length !== null ? $this->length : strlen($this->name);
    }

    public function getTruncatedValue(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $length = $this->getLength();
        if ($this->truncate && mb_strlen($value) > $length) {
            return trim(mb_substr($value, 0, $length)) . '…';
        }

        return str_pad($value, $length);
    }

    /**
     * Get ExtraColumn instance from text like "Column", "Column(20)" or "Column(20>)"
     * @param string $text
     * @return ExtraColumn
     */
    public static function fromText(string $text): ExtraColumn
    {
        $length = null;
        $truncate = true;

        if (preg_match('/^([^(]+)(\((\d+)(>?)\))?$/', $text, $matches)) {
            $name = trim($matches[1]);
            if (isset($matches[3])) {
                $length = (int)$matches[3];
                $truncate = !(isset($matches[4]) && $matches[4] === '>');
            } elseif (isset(self::$defaultColumnSizes[$name])) {
                $length = self::$defaultColumnSizes[$name];
            }
        } else {
            $name = trim($text);
        }

        return new ExtraColumn($name, $length, $truncate);
    }

}