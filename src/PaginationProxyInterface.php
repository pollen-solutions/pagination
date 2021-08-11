<?php

declare(strict_types=1);

namespace Pollen\Pagination;

interface PaginationProxyInterface
{
    /**
     * Retrieve pagination manager instance.
     *
     * @return PaginationManagerInterface
     */
    public function pagination(): PaginationManagerInterface;

    /**
     * Set related pagination manager instance.
     *
     * @param PaginationManagerInterface $pagination
     *
     * @return void
     */
    public function setPaginationManager(PaginationManagerInterface $pagination): void;
}