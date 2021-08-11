<?php
/**
 * @var Pollen\Pagination\Partial\PaginationPartialTemplateInterface $this
 */
?>
<?php if ($this->getCurrentPage() < $this->getLastPage()) : ?>
    <li class="Pagination-item Pagination-item--next">
        <?php echo $this->partial('tag', $this->get('links.next')); ?>
    </li>
<?php endif;