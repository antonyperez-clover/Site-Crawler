# Automated Health Check for docs.clover.com

![Weekly Clover Docs Check](https://github.com/antonyperez-clover/Site-Crawler/actions/workflows/crawler.yml/badge.svg)
[![GitHub last commit](https://img.shields.io/github/last-commit/antonyperez-clover/Site-Crawler)](https://github.com/antonyperez-clover/Site-Crawler/commits/main)
[![License](https://img.shields.io/github/license/antonyperez-clover/Site-Crawler)](./LICENSE.md)

This repository contains an automated system for running a weekly health check on the `docs.clover.com` website. It uses **SiteOne Crawler** executed by **GitHub Actions** to find issues and publishes a new report every Sunday.

---

## 🚀 View the Live Report

The latest report is automatically published and available at the link below. The date the report was generated is displayed at the top of the page.

### [➡️ Click Here to View the Latest Health Report](https://antonyperez-clover.github.io/Site-Crawler/)

---

## ✨ What We Check For

This system proactively identifies a wide range of issues that can impact user experience, SEO, and overall site health. Key areas include:

-   🔗 **Broken Links & 404s:** Finds all internal links that lead to "Page Not Found" errors.
-   🔀 **Redirects:** Lists all page redirects, which can affect performance and crawl efficiency.
-   📈 **SEO & Content:** Checks for common on-page SEO problems like multiple `<h1>` tags, missing page titles, or duplicate meta descriptions.
-   ⚡ **Performance:** Identifies slow-loading pages and opportunities for image optimization.
-   ♿ **Accessibility:** Looks for common accessibility issues like missing image `alt` text or missing form labels.
-   🛡️ **Security:** Checks for best practices in HTTP security headers.

## ⚙️ How It Works

The process is fully automated and follows a simple pipeline:

**GitHub Actions** `(runs every Sunday)` ➡️ **SiteOne Crawler** `(audits the site)` ➡️ **Generates `index.html`** `(creates the report)` ➡️ **GitHub Pages** `(publishes the report to the live URL)`

## 📁 Repository Structure

A brief overview of the key files and directories in this project:

```
/
├── .github/workflows/
│   └── crawler.yml    # The main GitHub Actions workflow file.
├── docs/
│   └── index.html     # The latest HTML report, which is published to GitHub Pages.
└── README.md          # This file.
```

## 🔧 Modifying the Crawler

All configuration for the crawler is contained within the `crawler.yml` workflow file.

To change the crawler's behavior (e.g., adjust the number of workers, change the timeout, or add/remove checks), edit the single-line `./crawler` command inside the **Download and Run SiteOne Crawler** step of the workflow.

## 📜 License

This project is licensed under the terms of the license agreement specified in the [LICENSE.md](./LICENSE.md) file.
