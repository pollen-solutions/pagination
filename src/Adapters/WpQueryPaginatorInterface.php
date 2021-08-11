<?php

declare(strict_types=1);

namespace Pollen\Pagination\Adapters;

use Pollen\Pagination\PaginatorInterface;
use WP_Query;

interface WpQueryPaginatorInterface extends PaginatorInterface
{
    /**
     * Gets query builder instance.
     *
     * @return WP_Query|null
     */
    public function getQueryBuilder(): ?object;

    /**
     * Gets WP_Query arguments.
     *
     * @param WP_Query $wpQuery
     *
     * @return array
     */
    public function getWpQueryArgs(WP_Query $wpQuery): array;
}