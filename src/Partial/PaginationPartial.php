<?php

declare(strict_types=1);

namespace Pollen\Pagination\Partial;

use Pollen\Pagination\PaginationManagerInterface;
use Pollen\Pagination\PaginationProxy;
use Pollen\Pagination\Paginator;
use Pollen\Pagination\PaginatorInterface;
use Pollen\Partial\PartialDriver;
use Pollen\Partial\PartialManagerInterface;
use Throwable;

class PaginationPartial extends PartialDriver implements PaginationPartialInterface
{
    use PaginationProxy;

    /**
     * Related Paginator instance
     * @var PaginatorInterface|null
     */
    protected ?PaginatorInterface $paginator = null;

    /**
     * @param PaginationManagerInterface $pagination
     * @param PartialManagerInterface $partialManager
     */
    public function __construct(PaginationManagerInterface $pagination, PartialManagerInterface $partialManager)
    {
        $this->setPaginationManager($pagination);

        parent::__construct($partialManager);
    }

    /**
     * @inheritDoc
     */
    public function defaultParams(): array
    {
        return array_merge(parent::defaultParams(), [
            /**
             * Pagination url.
             * ---------------------------------------------------------------------------------------------------------
             * @var string|array|null $url {
             * Uses %d as page number variable. If null, the current HTML page url is used.
             * @var string|null $base
             * Enable/disable url segmentation format.
             * @var bool $segment
             * Name of page index identifier in url.
             * @var string $index
             * }
             */
            'url'   => null,

            /**
             * Pagination links.
             * ---------------------------------------------------------------------------------------------------------
             * @var array $links {
             * Enable/disable first page|Set list of HTML attributes.
             * @var bool|array $first
             * Enable/disable last page|Set list of HTML attributes.
             * @var bool|array $last Activation du lien vers la dernière page|Liste d'attributs.
             * Enable/disable previous page|Set list of HTML attributes.
             * @var bool|array $previous Activation du lien vers la page précédente|Liste d'attributs.
             * Enable/disable next page|Set list of HTML attributes.
             * @var bool|array $next
             * Enable/disable page numbering|Page numbering parameters.
             * @var bool|array $numbers {
             * @var int $range
             * @var int $anchor
             * @var int $gap
             * }
             */
            'links' => [
                'first'    => true,
                'last'     => true,
                'previous' => true,
                'next'     => true,
                'numbers'  => true,
            ],
            /**
             * Paginator definition.
             * @var array|PaginatorInterface|object $paginator
             */
            'paginator' => null,
        ]);
    }

    /**
     * @inheritDoc
     */
    public function ellipsis(array &$numbers): void
    {
        $numbers[] = [
            'tag'     => 'span',
            'content' => '...',
            'attrs'   => 'Pagination-itemEllipsis',
        ];
    }

    /**
     * @inheritDoc
     */
    public function numLoop(array &$numbers, int $start, int $end): void
    {
        for ($num = $start; $num <= $end; $num++) {
            if ($num === 1 && !$this->paginator()->getCurrentPage()) {
                $current = 'true';
            } elseif ($this->paginator()->getCurrentPage() === $num) {
                $current = 'true';
            } else {
                $current = 'false';
            }

            $numbers[] = [
                'tag'     => 'a',
                'content' => $num,
                'attrs'   => [
                    'class'        => 'Pagination-itemPage Pagination-itemPage--link',
                    'href'         => $this->paginator()->getPageNumUrl($num),
                    'aria-current' => $current,
                ],
            ];
        }
    }

    /**
     * @inheritDoc
     */
    public function paginator(): PaginatorInterface
    {
        if ($this->paginator === null) {
            $paginator = $this->get('paginator');

            if ($paginator instanceof PaginatorInterface) {
                $this->paginator = $paginator;
            } elseif (is_array($paginator)) {
                $this->paginator = new Paginator($paginator);
            } elseif (is_object($paginator)) {
                $this->paginator = new Paginator(get_object_vars($paginator));
            } else {
                $this->paginator = $this->pagination()->paginator() ?: new Paginator();
            }
        }

        return $this->paginator;
    }

    /**
     * @inheritDoc
     */
    public function render(): string
    {
        $this->parseUrl();
        $this->parseLinks();

        if ($this->get('links.numbers')) {
            $this->parseNumbers();
        }

        return parent::render();
    }

    /**
     * @inheritDoc
     */
    public function parseLinks(): void
    {
        $defaults = [
            'first'    => [
                'tag'     => 'a',
                'content' => '&laquo;',
                'attrs'   => [
                    'class' => 'Pagination-itemPage Pagination-itemPage--link',
                    'href'  => $this->paginator()->getFirstPageUrl(),
                ],
            ],
            'last'     => [
                'tag'     => 'a',
                'content' => '&raquo;',
                'attrs'   => [
                    'class' => 'Pagination-itemPage Pagination-itemPage--link',
                    'href'  => $this->paginator()->getLastPageUrl(),
                ],
            ],
            'previous' => [
                'tag'     => 'a',
                'content' => '&lsaquo;',
                'attrs'   => [
                    'class' => 'Pagination-itemPage Pagination-itemPage--link',
                    'href'  => $this->paginator()->getPreviousPageUrl(),
                ],
            ],
            'next'     => [
                'tag'     => 'a',
                'content' => '&rsaquo;',
                'attrs'   => [
                    'class' => 'Pagination-itemPage Pagination-itemPage--link',
                    'href'  => $this->paginator()->getNextPageUrl(),
                ],
            ],
        ];

        foreach (array_keys($defaults) as $key) {
            $attrs = $this->get("links.$key", []);

            if ($attrs === false) {
                $attrs = [];
            } elseif ($attrs === true) {
                $attrs = $defaults[$key];
            } elseif (is_string($attrs)) {
                $attrs = array_merge($defaults[$key], ['content' => $attrs]);
            } else {
                $attrs = array_merge($defaults[$key], $attrs);
            }

            $this->set("links.$key", $attrs);
        }
    }

    /**
     * @inheritDoc
     */
    public function parseNumbers(): void
    {
        $range = (int) $this->get('links.numbers.range', 2);
        $anchor = (int)$this->get('links.numbers.anchor', 3);
        $gap = (int) $this->get('links.numbers.gap', 1);

        $min_links = ($range * 2) + 1;
        $block_min = min($this->paginator()->getCurrentPage() - $range, $this->paginator()->getLastPage() - $min_links);
        $block_high = max($this->paginator()->getCurrentPage() + $range, $min_links);

        $left_gap = ($block_min - $anchor - $gap) > 0;
        $right_gap = ($block_high + $anchor + $gap) < $this->paginator()->getLastPage();

        $numbers = [];
        if ($left_gap && !$right_gap) {
            $this->numLoop($numbers, 1, $anchor);
            $this->ellipsis($numbers);
            $this->numLoop($numbers, $block_min, $this->paginator()->getLastPage());
        } elseif ($left_gap && $right_gap) {
            $this->numLoop($numbers, 1, $anchor);
            $this->ellipsis($numbers);
            $this->numLoop($numbers, $block_min, $block_high);
            $this->ellipsis($numbers);
            $this->numLoop($numbers, ($this->paginator()->getLastPage() - $anchor + 1), $this->paginator()->getLastPage());
        } elseif (!$left_gap && $right_gap) {
            $this->numLoop($numbers, 1, $block_high);
            $this->ellipsis($numbers);
            $this->numLoop($numbers, ($this->paginator()->getLastPage() - $anchor + 1), $this->paginator()->getLastPage());
        } else {
            $this->numLoop($numbers, 1, $this->paginator()->getLastPage());
        }

        $this->set('numbers', $numbers);
    }

    /**
     * @inheritDoc
     */
    public function parseUrl(): void
    {
        if ($this->has('url.base')) {
            try {
                $this->paginator()->setBaseUrl($this->get('url.base'));
            } catch (Throwable $e) {
                unset($e);
            }
        }

        if ($this->has('url.segment')) {
            $this->paginator()->setSegmenting(filter_var($this->get('url.segment'), FILTER_VALIDATE_BOOLEAN));
        }

        if ($this->has('url.index')) {
            try {
                $this->paginator()->setPageIndex($this->get('url.index'));
            } catch (Throwable $e) {
                unset($e);
            }
        }

        if (!is_array($this->get('url'))) {
            try {
                $this->paginator()->setBaseUrl($this->get('url'));
            } catch (Throwable $e) {
                unset($e);
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function view(?string $name = null, array $data = [])
    {
        if ($this->view === null) {
            $this->view = parent::view();
            $this->view
                ->addExtension('getCurrentPage', function (): int {
                    return $this->paginator()->getCurrentPage();
                })
                ->addExtension('getLastPage', function (): int {
                    return $this->paginator()->getLastPage();
                });

        }

        return parent::view($name, $data);
    }

    /**
     * @inheritDoc
     */
    public function viewDirectory(): string
    {
        return $this->pagination()->resources('/views/partial/pagination');
    }
}