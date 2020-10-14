<?php
defined('C5_EXECUTE') or die("Access Denied.");
/** @var \Concrete\Core\Entity\Express\Entity $entity */
/** @var integer $attributeKeyID */
?>
<input type="hidden" data-selectable="<?= $entity->getId().'-ak-'.$attributeKeyID ?>" style="width: 100%"
       name="<?= $view->field('value') ?>" value="<?= $value ?>"/>
<script type="text/javascript">
    $(function() {
        $('input[data-selectable=<?= $entity->getId().'-ak-'.$attributeKeyID ?>]').selectize({
            plugins: ['remove_button'],
            valueField: 'id',
            labelField: 'text',
            options: <?=json_encode($entryOptions)?>,
            items: <?=json_encode($selectedEntryIDs)?>
        });
    });
</script>

