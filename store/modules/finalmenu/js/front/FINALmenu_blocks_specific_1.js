$(document).ready(function () { // <![CDATA[
				window.menuSliders = new Array();
				window.menuAnimateInHorizontal = "zoomIn";
				window.menuAnimateOutHorizontal = "zoomOut";
				var value = $("input[name=search_query]").val();
				$("input[name=search_query]").focusin(function () {
					$(this).val("");
				});
				$("input[name=search_query]").change(function () {
					value = $("input[name=search_query]").val();
				});
				$("input[name=search_query]").focusout(function () {
					$(this).val(value);
				});
		// ]]>
				});