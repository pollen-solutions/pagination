<?php

declare(strict_types=1);

namespace Pollen\Pagination;

use Pollen\Support\Concerns\BootableTraitInterface;
use Pollen\Support\Concerns\ConfigBagAwareTraitInterface;
use Pollen\Support\Concerns\ResourcesAwareTraitInterface;
use Pollen\Support\Proxy\ContainerProxyInterface;
use Pollen\Support\Proxy\PartialProxyInterface;

interface PaginationManagerInterface extends
    BootableTraitInterface,
    ConfigBagAwareTraitInterface,
    ResourcesAwareTraitInterface,
    ContainerProxyInterface,
    PartialProxyInterface
{
    /**
     * Resolve class as a string and return partial pagination render.
     *
     * @return string
     */
    public function __toString(): string;

    /**
     * Booting.
     *
     * @return static
     */
    public function boot(): PaginationManagerInterface;

    /**
     * Retrieve pagination instance.
     *
     * @return PaginationAdapterInterface|null
     */
    public function getAdapter(): ?PaginationAdapterInterface;

    /**
     * Retrieve current paginator instance
     *
     * @return PaginatorInterface|null
     */
    public function paginator(): ?PaginatorInterface;

    /**
     * Pagination partial render
     * @see \Pollen\Pagination\Partial\PaginationPartial
     *
     * @param array $args
     *
     * @return string
     */
    public function render(array $args = []): string;

    /**
     * Set related pagination adapter instance.
     *
     * @param PaginationAdapterInterface $adapter
     *
     * @return static
     */
    public function setAdapter(PaginationAdapterInterface $adapter): PaginationManagerInterface;

    /**
     * Set related paginator instance.
     *
     * @param PaginatorInterface $paginator
     *
     * @return PaginationManagerInterface
     */
    public function setPaginator(PaginatorInterface $paginator): PaginationManagerInterface;
}