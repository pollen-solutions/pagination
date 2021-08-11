<?php
/**
 * @var Pollen\Pagination\Partial\PaginationPartialTemplateInterface $this
 */
?>
<?php if ($this->getLastPage() > 1) : ?>
    <?php $this->before(); ?>
    <ul <?php $this->attrs(); ?>>
        <?php $this->get('links.first') ? $this->insert('first', $this->all()) : false; ?>

        <?php $this->get('links.previous') ? $this->insert('previous', $this->all()) : false; ?>

        <?php $this->get('numbers') ? $this->insert('numbers', $this->all()) : false; ?>

        <?php $this->get('links.next') ? $this->insert('next', $this->all()) : false; ?>

        <?php $this->get('links.last') ? $this->insert('last', $this->all()) : false; ?>
    </ul>
    <?php $this->after(); ?>
<?php endif;