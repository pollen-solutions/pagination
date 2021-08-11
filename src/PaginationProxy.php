<?php

declare(strict_types=1);

namespace Pollen\Pagination;

use Pollen\Support\ProxyResolver;
use RuntimeException;

trait PaginationProxy
{
    /**
     * Related pagination manager instance.
     * @var PaginationManagerInterface|null
     */
    private ?PaginationManagerInterface $paginationManager;

    /**
     * Retrieve pagination manager instance.
     *
     * @return PaginationManagerInterface
     */
    public function pagination(): PaginationManagerInterface
    {
        if ($this->paginationManager === null) {
            try {
                $this->paginationManager = PaginationManager::getInstance();
            } catch (RuntimeException $e) {
                $this->paginationManager = ProxyResolver::getInstance(
                    PaginationManagerInterface::class,
                    PaginationManager::class,
                    method_exists($this, 'getContainer') ? $this->getContainer() : null
                );
            }
        }

        return $this->paginationManager;
    }

    /**
     * Set related pagination manager instance.
     *
     * @param PaginationManagerInterface $pagination
     *
     * @return void
     */
    public function setPaginationManager(PaginationManagerInterface $pagination): void
    {
        $this->paginationManager = $pagination;
    }
}