<?php

declare(strict_types=1);

namespace Pollen\Pagination\Adapters;

use Pollen\Pagination\Paginator;
use RuntimeException;
use WP_Query;

class WpQueryPaginator extends Paginator implements WpQueryPaginatorInterface
{
    /**
     * @param WP_Query|null $wpQuery
     */
    public function __construct(?WP_Query $wpQuery = null)
    {
        if ($wpQuery === null) {
            global $wp_query;

            $wpQuery = $wp_query;
        }

        if (!$wpQuery instanceof WP_Query) {
            throw new RuntimeException('WpQueryPaginator must have a valid WP_Query instance to run');
        }

        $this->queryBuilder = $wpQuery;
        $args = $this->getWpQueryArgs($wpQuery);

        parent::__construct($args);
    }

    /**
     * @inheritDoc
     */
    public function getQueryBuilder(): ?object
    {
        return $this->queryBuilder instanceof WP_Query ? $this->queryBuilder : null;
    }

    /**
     * @inheritDoc
     */
    public function getWpQueryArgs(WP_Query $wpQuery): array
    {
        $total = (int)$wpQuery->found_posts;
        $per_page = (int)$wpQuery->get('posts_per_page');
        $current = $wpQuery->get('paged') ?: 1;

        return [
            'count'         => (int)$wpQuery->post_count,
            'current_page'  => $per_page < 0 ? 1 : (int)$current,
            'last_page'     => $per_page < 0 ? 1 : (int)$wpQuery->max_num_pages,
            'per_page'      => $per_page,
            'query_builder' => $wpQuery,
            'total'         => $total,
        ];
    }
}