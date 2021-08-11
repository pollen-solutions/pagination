<?php

declare(strict_types=1);

namespace Pollen\Pagination\Partial;

use Pollen\Pagination\PaginationProxyInterface;
use Pollen\Pagination\PaginatorInterface;
use Pollen\Partial\PartialDriverInterface;

interface PaginationPartialInterface extends PaginationProxyInterface, PartialDriverInterface
{
    /**
     * Handle page numbering ellipis.
     *
     * @param array $numbers
     *
     * @return void
     */
    public function ellipsis(array &$numbers): void;

    /**
     * Handle page numbers loop.
     *
     * @param array $numbers
     * @param int $start
     * @param int $end
     *
     * @return void
     */
    public function numLoop(array &$numbers, int $start, int $end): void;

    /**
     * Retrieve paginator instance.
     *
     * @return PaginatorInterface
     */
    public function paginator(): PaginatorInterface;

    /**
     * Parse pagination links.
     *
     * @return void
     */
    public function parseLinks(): void;

    /**
     * Parse pagination numbers.
     *
     * @return void
     */
    public function parseNumbers(): void;

    /**
     * Parse pagination url.
     *
     * @return void
     */
    public function parseUrl(): void;
}