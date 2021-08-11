<?php

declare(strict_types=1);

namespace Pollen\Pagination\Partial;

use Pollen\Partial\PartialTemplateInterface;

/**
 * @method int getCurrentPage()
 * @method int getLastPage()
 */
interface PaginationPartialTemplateInterface extends PartialTemplateInterface
{
}