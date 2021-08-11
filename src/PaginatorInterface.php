<?php

declare(strict_types=1);

namespace Pollen\Pagination;

use Psr\Http\Message\UriInterface;

interface PaginatorInterface
{
    /**
     * Get base url instance.
     *
     * @return UriInterface|null
     */
    public function getBaseUrl(): ?UriInterface;

    /**
     * Get number of current records.
     *
     * @return int
     */
    public function getCount(): int;

    /**
     * Gets current page number.
     *
     * @return int
     */
    public function getCurrentPage(): int;

    /**
     * Gets the first page url.
     *
     * @return string
     */
    public function getFirstPageUrl(): string;

    /**
     * Gets the last page number.
     *
     * @return int
     */
    public function getLastPage(): int;

    /**
     * Gets the last page url.
     *
     * @return string
     */
    public function getLastPageUrl(): string;

    /**
     * Gets the next page number.
     *
     * @return int
     */
    public function getNextPage(): int;

    /**
     * Gets the next page url.
     *
     * @return string
     */
    public function getNextPageUrl(): string;

    /**
     * Gets the offset of records.
     *
     * @return int
     */
    public function getOffset(): int;

    /**
     * Gets url page index identifier for url.
     *
     * @return string
     */
    public function getPageIndex(): string;

    /**
     * Gets page url by number.
     *
     * @param int $num
     *
     * @return string
     */
    public function getPageNumUrl(int $num): string;

    /**
     * Gets number of records by page.
     *
     * @return int|null
     */
    public function getPerPage(): ?int;

    /**
     * Gets the previous page number.
     *
     * @return int
     */
    public function getPreviousPage(): int;

    /**
     * Gets the previous page url.
     *
     * @return string
     */
    public function getPreviousPageUrl(): string;

    /**
     * Gets the query builder instance.
     *
     * @return object|null
     */
    public function getQueryBuilder(): ?object;

    /**
     * Gets Total number of records.
     *
     * @return int
     */
    public function getTotal(): int;

    /**
     * Check if url segmentation format is enabled.
     *
     * @return bool
     */
    public function isSegmented(): bool;

    /**
     * Resolve class as a json serialized object.
     *
     * @return array
     */
    public function jsonSerialize(): array;

    /**
     * Parse list of configuration arguments.
     *
     * @param array $args
     *
     * @return void
     */
    public function parseArgs(array $args): void;

    /**
     * Set the base url.
     * {@internal Uses %d as page number variable.}
     *
     * @param string $baseUrl
     *
     * @return static
     */
    public function setBaseUrl(string $baseUrl): PaginatorInterface;

    /**
     * Set the number of records for current page.
     *
     * @param int $count
     *
     * @return static
     */
    public function setCount(int $count): PaginatorInterface;

    /**
     * Set the current page number.
     *
     * @param int $page
     *
     * @return static
     */
    public function setCurrentPage(int $page): PaginatorInterface;

    /**
     * Set the last page number.
     *
     * @param int $lastPage
     *
     * @return static
     */
    public function setLastPage(int $lastPage): PaginatorInterface;

    /**
     * Set the offset of records.
     *
     * @param int $offset
     *
     * @return static
     */
    public function setOffset(int $offset): PaginatorInterface;

    /**
     * Sets the page index identifier for url.
     *
     * @param string $index
     *
     * @return static
     */
    public function setPageIndex(string $index = 'page'): PaginatorInterface;

    /**
     * Sets number of records by page.
     *
     * @param int|null $perPage
     *
     * @return static
     */
    public function setPerPage(?int $perPage= null): PaginatorInterface;

    /**
     * Enable|Disable url segmentation format.
     *
     * @param bool $segmented
     *
     * @return static
     */
    public function setSegmenting(bool $segmented = true): PaginatorInterface;

    /**
     * Sets total number of records.
     *
     * @param int $total
     *
     * @return static
     */
    public function setTotal(int $total): PaginatorInterface;

    /**
     * Resolve class as array.
     *
     * @return array
     */
    public function toArray(): array;

    /**
     * Resolve class as a JSON string.
     *
     * @return string
     */
    public function toJson(): string;
}