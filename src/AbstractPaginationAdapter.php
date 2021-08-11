<?php

declare(strict_types=1);

namespace Pollen\Pagination;

use Pollen\Support\Concerns\BootableTrait;

abstract class AbstractPaginationAdapter implements PaginationAdapterInterface
{
    use BootableTrait;
    use PaginationProxy;

    /**
     * @param PaginationManagerInterface $pagination
     */
    public function __construct(PaginationManagerInterface $pagination)
    {
        $this->setPaginationManager($pagination);

        $this->boot();
    }

    /**
     * @inheritDoc
     */
    abstract public function boot(): void;
}