<?php
/**
 * @var Pollen\Pagination\Partial\PaginationPartialTemplateInterface $this
 */
?>
<?php if ($this->getCurrentPage() > 1) : ?>
    <li class="Pagination-item Pagination-item--previous">
        <?php echo $this->partial('tag', $this->get('links.previous')); ?>
    </li>
<?php endif;