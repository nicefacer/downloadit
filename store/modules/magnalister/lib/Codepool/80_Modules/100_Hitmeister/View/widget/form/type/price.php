<?php class_exists('ML', false) or die() ?>
 <span style="width: 160px; display: block;">
	<input style="width: 115px" class="<?php echo (isset($aField['cssclasses']) ? ' ' . implode(' ', $aField['cssclasses']) : '') ?>"
		type="text" <?php echo isset($aField['id']) ? "id='{$aField['id']}'" : ''; ?>
		name="<?php echo MLHttp::gi()->parseFormFieldName($aField['name']) ?>"
		<?php if (isset($aField['enabled']) === true && $aField['enabled'] === false) {
			echo 'disabled';
		} ?>
		<?php echo (isset($aField['value']) ? 'value="'. htmlspecialchars($aField['value'], ENT_COMPAT) . '"' : '') ?> />
	<label><?= $aField['currency'] ?></label>
</span>
<script type="text/javascript">
    (function($) {
        $(document).ready(function() {
        <?php if ((isset($aField['issingleview'])) && ($aField['issingleview'] === false)) : ?>
             $('#hitmeister_prepare_apply_form_fieldset_details').hide();
             $('#hitmeister_prepare_apply_form_field_price').closest('.js-field').hide();
             $('#hitmeister_prepare_match_manual_field_price').closest('.js-field').hide();
        <?php endif ?>
       });
    })(jqml);

</script>