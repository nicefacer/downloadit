<?php 
    class_exists('ML', false) or die();
	MLSettingRegistry::gi()->addJs(array('jquery-ui-timepicker-addon.js'));

	$timeParts = explode(':', $aField['value']);
	if (isset($timeParts[1]) === false) {
		$timeParts[1] = '00';
	}
	
	if (isset($timeParts[2]) === false) {
		$timeParts[2] = '00';
	}
?>
<div class="datetimepicker">
	<input type="text" id="<?php echo $aField['id']; ?>"
	   <?php echo (isset($aField['value']) ? 'value="'. htmlspecialchars($aField['value'], ENT_COMPAT) . '"' : '') ?>
	   name="<?php echo MLHttp::gi()->parseFormFieldName($aField['name']) ?>" readonly="readonly" class="autoWidth rightSpacer"/>
</div>
<script type="text/javascript">
	(function($) {
		$(document).ready(function() {		
			$.timepicker.setDefaults($.timepicker.regional['']);
			$("#<?= $aField['id'] ?>").timepicker(
				$.timepicker.regional['de']
			).datetimepicker("option", {
				onClose:  function(dateText, inst) {
					var t = $("#<?= $aField['id'] ?>").val();
					var tArray = t.split(':');
					if ((t !== null) && (tArray.length === 2)) {
						$("#<?= $aField['id'] ?>").val(t + ':00');
					}
				}
			})<?php if (isset($aField['value']) === true) : ?>.datetimepicker(
				"setDate", new Date(2000, 1, 1, <?= $timeParts[0] ?>, <?= $timeParts[1] ?>, <?= $timeParts[2] ?>)
			)<?php endif ?>;

			$('#ricardo_prepare_form_field_duration').change(function() {
				if (this.value === '10') {
					$('#<?= $aField['id'] ?>').closest('span').parent().hide();
				} else {
					$('#<?= $aField['id'] ?>').closest('span').parent().show();
				}
			});
		});
	})(jqml);
</script>



