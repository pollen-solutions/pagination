<?php

declare(strict_types=1);

namespace Pollen\Pagination;

use Pollen\Http\UrlManipulator;
use Pollen\Support\Proxy\HttpRequestProxy;
use Psr\Http\Message\UriInterface;
use RuntimeException;
use Throwable;

class Paginator implements PaginatorInterface
{
    use HttpRequestProxy;

    /**
     * Base url instance.
     * @var UriInterface|null
     */
    protected ?UriInterface $baseUrl;

    /**
     * Current page number.
     * @var int
     */
    protected int $currentPage = 1;

    /**
     * Number of records for current page.
     * @var int|null
     */
    protected ?int $count = null;

    /**
     * Last page number.
     * @var int
     */
    protected int $lastPage = 1;

    /**
     * Offset of records.
     * @var int
     */
    protected int $offset = 0;

    /**
     * Url page index identifier for url.
     * @var string
     */
    protected string $pageIndex = 'page';

    /**
     * Number of records by page.
     * @var int|null
     */
    protected ?int $perPage;

    /**
     * Query builder instance.
     * @var object|null
     */
    protected ?object $queryBuilder = null;

    /**
     * Url segmentation format indicator.
     * @internal false : {{ base_url }}/?{{ pageIndex }}={{ num }} | true :{{ base_url }}/{{ pageIndex }}/{{ num }}
     * @var bool
     */
    protected bool $segmented = false;

    /**
     * Total number of records.
     * @var int
     */
    protected int $total = 0;

    /**
     * @param array $args
     */
    public function __construct(array $args = [])
    {
        if (!empty($args)) {
            $this->parseArgs($args);
        }
    }

    /**
     * @inheritDoc
     */
    public function getBaseUrl(): ?UriInterface
    {
        if ($this->baseUrl === null) {
            $this->baseUrl = (new UrlManipulator($this->httpRequest()->getUri()))->get();
        }

        return $this->baseUrl;
    }

    /**
     * @inheritDoc
     */
    public function getCount(): int
    {
        $count = $this->count;
        if ($count === null) {
            $count = $this->getPerPage();
        }
        return $count;
    }

    /**
     * @inheritDoc
     */
    public function getCurrentPage(): int
    {
        return $this->currentPage;
    }

    /**
     * @inheritDoc
     */
    public function getFirstPageUrl(): string
    {
        return $this->getPageNumUrl(1);
    }

    /**
     * @inheritDoc
     */
    public function getLastPageUrl(): string
    {
        return $this->getPageNumUrl($this->getLastPage());
    }

    /**
     * @inheritDoc
     */
    public function getLastPage(): int
    {
        return $this->lastPage = (int)ceil($this->getTotal()/$this->getPerPage());
    }

    /**
     * @inheritDoc
     */
    public function getNextPage(): int
    {
        $num = $this->getCurrentPage() + 1;

        return $num < $this->getLastPage() ? $num : 0;
    }

    /**
     * @inheritDoc
     */
    public function getNextPageUrl(): string
    {
        return ($next = $this->getNextPage()) ? $this->getPageNumUrl($next) : '';
    }

    /**
     * @inheritDoc
     */
    public function getOffset(): int
    {
        return $this->offset;
    }

    /**
     * @inheritDoc
     */
    public function getPageIndex(): string
    {
        return $this->pageIndex;
    }

    /**
     * @inheritDoc
     */
    public function getPageNumUrl(int $num): string
    {
        $url = new UrlManipulator($this->getBaseUrl());

        if (preg_match('/%d/', $url->decoded())) {
            return urlencode(sprintf($url->decoded(), $num));
        }
        if ($this->isSegmented()) {
            $url = $url->deleteSegment("/{$this->getPageIndex()}/\d+");

            return $num > 1 ? $url->appendSegment("/{$this->getPageIndex()}/$num")->render() : $url->render();
        }

        $url = $url->without([$this->getPageIndex()]);

        return $num > 1 ? $url->with([$this->getPageIndex() => $num])->render() : $url->render();
    }

    /**
     * @inheritDoc
     */
    public function getPerPage(): ?int
    {
        return $this->perPage;
    }

    /**
     * @inheritDoc
     */
    public function getPreviousPage(): int
    {
        $num = $this->getCurrentPage() - 1;

        return $num > 0 ? $num : 0;
    }

    /**
     * @inheritDoc
     */
    public function getPreviousPageUrl(): string
    {
        return ($prev = $this->getPreviousPage()) ? $this->getPageNumUrl($prev) : '';
    }

    /**
     * @inheritDoc
     */
    public function getQueryBuilder(): ?object
    {
        return $this->queryBuilder;
    }

    /**
     * @inheritDoc
     */
    public function getTotal(): int
    {
        return $this->total;
    }

    /**
     * @inheritDoc
     */
    public function isSegmented(): bool
    {
        return $this->segmented;
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    /**
     * @inheritDoc
     */
    public function parseArgs(array $args): void
    {
        if ($baseUrl = $args['base_url'] ?? null) {
            $this->setBaseUrl($baseUrl);
        }

        if ($count = $args['count'] ?? null) {
            $this->setCount($count);
        }

        if ($currentPage = $args['current_page'] ?? null) {
            $this->setCurrentPage($currentPage);
        }

        if ($lastPage = $args['last_page'] ?? null) {
            $this->setLastPage($lastPage);
        }

        if ($pageIndex = $args['page_index'] ?? null) {
            $this->setPageIndex($pageIndex);
        }

        if ($per_page = $args['per_page'] ?? null) {
            $this->setPerPage($per_page);
        }

        if ($segmentUrl = $args['segmented'] ?? null) {
            $this->setSegmenting($segmentUrl);
        }

        if ($total = $args['total'] ?? null) {
            $this->setTotal($total);
        }

        if ($offset = $args['offset'] ?? null) {
            $this->setOffset($offset);
        }
    }

    /**
     * @inheritDoc
     */
    public function setBaseUrl(string $baseUrl): PaginatorInterface
    {
        $this->baseUrl = (new UrlManipulator($baseUrl))->get();

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setCount(int $count): PaginatorInterface
    {
        $this->count = $count;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setCurrentPage(int $page): PaginatorInterface
    {
        $this->currentPage = $page > 0 ? $page : 1;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setLastPage(int $lastPage): PaginatorInterface
    {
        $this->lastPage = $lastPage;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setOffset(int $offset): PaginatorInterface
    {
        $this->offset = $offset;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setPageIndex(string $index = 'page'): PaginatorInterface
    {
        $this->pageIndex = $index;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setPerPage(?int $perPage = null): PaginatorInterface
    {
        $this->perPage = $perPage > 0 ? $perPage : null;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setSegmenting(bool $segmented = true): PaginatorInterface
    {
        $this->segmented = $segmented;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setTotal(int $total): PaginatorInterface
    {
        $this->total = $total;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        return [
            'base_url'       => (string)$this->getBaseUrl(),
            'current_page'   => $this->getCurrentPage(),
            'count'          => $this->getCount(),
            'first_page_url' => $this->getFirstPageUrl(),
            'last_page'      => $this->getLastPage(),
            'last_page_url'  => $this->getLastPageUrl(),
            'next_page'      => $this->getNextPage(),
            'next_page_url'  => $this->getNextPageUrl(),
            'offset'         => $this->getOffset(),
            'prev_page'      => $this->getPreviousPage(),
            'prev_page_url'  => $this->getPreviousPageUrl(),
            'page_index'     => $this->getPageIndex(),
            'per_page'       => $this->getPerPage(),
            'total'          => $this->getTotal(),
        ];
    }

    /**
     * @inheritDoc
     */
    public function toJson(): string
    {
        try {
            return json_encode($this->jsonSerialize(), JSON_THROW_ON_ERROR);
        } catch (Throwable $e) {
            throw new RuntimeException('Paginator could not encode to JSON');
        }
    }
}