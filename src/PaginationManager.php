<?php

declare(strict_types=1);

namespace Pollen\Pagination;

use Pollen\Pagination\Adapters\WpPaginationAdapter;
use Pollen\Pagination\Partial\PaginationPartial;
use Pollen\Support\Concerns\BootableTrait;
use Pollen\Support\Concerns\ConfigBagAwareTrait;
use Pollen\Support\Concerns\ResourcesAwareTrait;
use Pollen\Support\Exception\ManagerRuntimeException;
use Pollen\Support\Proxy\ContainerProxy;
use Pollen\Support\Proxy\PartialProxy;
use Psr\Container\ContainerInterface as Container;

class PaginationManager implements PaginationManagerInterface
{
    use BootableTrait;
    use ConfigBagAwareTrait;
    use ResourcesAwareTrait;
    use ContainerProxy;
    use PartialProxy;

    /**
     * Pagination manager main instance.
     * @var PaginationManagerInterface|null
     */
    private static ?PaginationManagerInterface $instance;

    /**
     * Pagination adapter instance.
     * @var PaginationAdapterInterface|null
     */
    private ?PaginationAdapterInterface $adapter = null;

    /**
     * Current paginator instance.
     * @var PaginatorInterface|null
     */
    protected ?PaginatorInterface $paginator = null;

    /**
     * @param array $config
     * @param Container|null $container
     *
     * @return void
     */
    public function __construct(array $config = [], ?Container $container = null)
    {
        $this->setConfig($config);

        if ($container !== null) {
            $this->setContainer($container);
        }

        $this->setResourcesBaseDir(dirname(__DIR__) . '/resources');

        if ($this->config('boot_enabled', true)) {
            $this->boot();
        }

        if (!self::$instance instanceof static) {
            self::$instance = $this;
        }
    }

    /**
     * Retrieve pagination manager main instance.
     *
     * @return static
     */
    public static function getInstance(): PaginationManagerInterface
    {
        if (self::$instance instanceof self) {
            return self::$instance;
        }
        throw new ManagerRuntimeException(sprintf('Unavailable [%s] instance', __CLASS__));
    }

    /**
     * @inheritDoc
     */
    public function __toString(): string
    {
        return $this->render();
    }

    /**
     * @inheritDoc
     */
    public function boot(): PaginationManagerInterface
    {
        if (!$this->isBooted()) {
            $this->partial()->register(
                'pagination',
                $this->containerHas(PaginationPartial::class)
                    ? PaginationPartial::class
                    : new PaginationPartial($this, $this->partial())
            );

            if ($this->adapter === null && defined('WPINC')) {
                $this->setAdapter(new WpPaginationAdapter($this));
            }

            $this->setBooted();
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getAdapter(): ?PaginationAdapterInterface
    {
        return $this->adapter;
    }

    /**
     * @inheritDoc
     */
    public function paginator(): ?PaginatorInterface
    {
        return $this->paginator;
    }

    /**
     * @inheritDoc
     */
    public function render(array $args = []): string
    {
        return (string) $this->partial()->get('pagination', $args);
    }

    /**
     * @inheritDoc
     */
    public function setAdapter(PaginationAdapterInterface $adapter): PaginationManagerInterface
    {
        $this->adapter = $adapter;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setPaginator(PaginatorInterface $paginator): PaginationManagerInterface
    {
        $this->paginator = $paginator;

        return $this;
    }
}