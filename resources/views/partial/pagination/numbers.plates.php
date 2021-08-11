<?php
/**
 * @var Pollen\Pagination\Partial\PaginationPartialTemplateInterface $this
 */
?>
<?php foreach ($this->get('numbers', []) as $number) : ?>
    <li class="Pagination-item Pagination-item--num">
        <?php echo $this->partial('tag', $number); ?>
    </li>
<?php endforeach;