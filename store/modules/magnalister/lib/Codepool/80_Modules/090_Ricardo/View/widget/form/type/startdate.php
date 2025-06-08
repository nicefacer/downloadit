<?php 
    class_exists('ML', false) or die();
	MLSettingRegistry::gi()->addJs(array('jquery-ui-timepicker-addon.js'));
?>
<div class="datetimepicker">
	<input type="text" id="<?php echo $aField['id']; ?>"
		<?php echo (isset($aField['value']) ? 'value="'. htmlspecialchars($aField['value'], ENT_COMPAT) . '"' : '') ?>
		readonly="readonly" class="autoWidth rightSpacer"/>
	<input type="hidden" id="<?php echo $aField['id'] . '_hidden'; ?>" name="<?php echo MLHttp::gi()->parseFormFieldName($aField['name']) ?>" value="<?php echo $aField['value'] ?>"/>
</div>
<script type="text/javascript">
	(function($) {
		$(document).ready(function() {		
			$.datepicker.setDefaults($.datepicker.regional['']);
			$.timepicker.setDefaults($.timepicker.regional['']);
			$("#<?php echo $aField['id']; ?>").datetimepicker(
				$.extend(
					$.datepicker.regional['de'],
					$.timepicker.regional['de']
				)
			).datetimepicker("option", {
				onClose:  function(dateText, inst) {
					var d = $("#<?php echo $aField['id']; ?>").datetimepicker("getDate");
					if (d !== null) {
						var s = $.datepicker.formatDate("yy-mm-dd", d) + ' ' +
						$.datepicker.formatTime("HH:mm:ss", {
							hour: d.getHours(),
							minute: d.getMinutes(),
							second: d.getSeconds()
						}, { ampm: false });
						$("#<?php echo $aField['id']; ?>_hidden").val(s);
					}
				}
			}).datetimepicker(
				"option", "minDate", 0
			).datetimepicker(
				"option", "maxDate", <?= $aField['MaxStartDate'] ?>
			)<?php if (isset($aField['value']) === true) : ?>.datetimepicker(
				"setDate", new Date('<?= $aField['value'] ?>')
			)<?php endif ?>;
		});
	})(jqml);
</script>

