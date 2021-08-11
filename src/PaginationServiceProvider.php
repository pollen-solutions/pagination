<?php

declare(strict_types=1);

namespace Pollen\Pagination;

use Pollen\Container\BootableServiceProvider;
use Pollen\Pagination\Adapters\WpPaginationAdapter;
use Pollen\Pagination\Partial\PaginationPartial;
use Pollen\Partial\PartialManagerInterface;

class PaginationServiceProvider extends BootableServiceProvider
{
    /**
     * @var string[]
     */
    protected $provides = [
        PaginationManagerInterface::class,
    ];

    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->getContainer()->share(PaginationManagerInterface::class, function () {
            return new PaginationManager([], $this->getContainer());
        });

        $this->registerAdapters();
        $this->registerPartials();
    }

    /**
     * Register adapter services.
     *
     * @return void
     */
    public function registerAdapters(): void
    {
        $this->getContainer()->share(
            WpPaginationAdapter::class,
            function () {
                return new WpPaginationAdapter($this->getContainer()->get(PaginationManagerInterface::class));
            }
        );
    }

    /**
     * Register partial drivers.
     *
     * @return void
     */
    public function registerPartials(): void
    {
        $this->getContainer()->add(
            PaginationPartial::class,
            function () {
                return new PaginationPartial(
                    $this->getContainer()->get(PaginationManagerInterface::class),
                    $this->getContainer()->get(PartialManagerInterface::class)
                );
            }
        );
    }
}