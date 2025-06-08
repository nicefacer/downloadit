<?php class_exists('ML', false) or die() ?>
<input class="fullwidth<?php echo ((isset($aField['required']) && empty($aField['value']))? ' ml-error' : '') . (isset($aField['cssclasses']) ? ' ' . implode(' ', $aField['cssclasses']) : '') ?>"
	type="text" <?php echo isset($aField['id']) ? "id='{$aField['id']}'" : ''; ?>
    name="<?php echo MLHttp::gi()->parseFormFieldName($aField['name']) ?>" 
    <?php echo (isset($aField['value']) ? 'value="'. htmlspecialchars($aField['value'], ENT_COMPAT) . '"' : '') ?> 
    <?php echo isset($aField['maxlength']) ? "maxlength='{$aField['maxlength']}'" : ''; ?> />
