# Pagination Component

[![Latest Stable Version](https://img.shields.io/packagist/v/pollen-solutions/pagination.svg?style=for-the-badge)](https://packagist.org/packages/pollen-solutions/pagination)
[![MIT Licensed](https://img.shields.io/badge/license-MIT-green?style=for-the-badge)](LICENSE.md)
[![PHP Supported Versions](https://img.shields.io/badge/PHP->=7.4-8892BF?style=for-the-badge&logo=php)](https://www.php.net/supported-versions.php)

Pollen Solutions **Pagination** Component provides layer and utilities to paginate items in web applications.
It provides an adapter for Wordpress application.

## Installation

```bash
composer require pollen-solutions/pagination
```

## Basic Usage

```php
use Pollen\Pagination\PaginationManager;
use Pollen\Pagination\Paginator;

$pagination = new PaginationManager();
$paginator = new Paginator(
    [
        'per_page' => 20,
        'total'    => 60,
    ]
);

var_dump($paginator->toArray());

$pagination->setPaginator($paginator);

echo $pagination;
```

## Wordpress Usage
In this example, we consider that the current HTTP request corresponds to a search results page.
ex. https://example.com/?s=a

```php
use Pollen\Pagination\PaginationManager;

add_action('wp', function () {
    echo new PaginationManager();
    exit;
});
```

## Through a controller

```php

```