<?php

declare(strict_types=1);

namespace Pollen\Pagination\Adapters;

use Pollen\Pagination\Partial\PaginationPartial;
use Pollen\Pagination\AbstractPaginationAdapter;

class WpPaginationAdapter extends AbstractPaginationAdapter
{
    /**
     * @inheritdoc
     */
    public function boot(): void
    {
        if (!$this->isBooted()) {
            $this->pagination()->partial()->register(
                'pagination',
                $this->pagination()->containerHas(PaginationPartial::class)
                    ? WpPaginationPartial::class
                    : new WpPaginationPartial($this->pagination(), $this->pagination()->partial())
            );

            $this->setBooted();
        }
    }
}