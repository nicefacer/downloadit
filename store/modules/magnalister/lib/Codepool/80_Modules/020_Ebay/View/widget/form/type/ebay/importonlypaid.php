<?php
$aField['type'] = 'bool';
$this->includeType($aField);
?>
<script type="text/javascript">/*<![CDATA[*/
    (function ($) {
            $(document).ready(function () {
                $('<?php echo '#' . $aField['id'] ?>').change(function (event, rec) {
                    var blProp = $(this).prop('checked');//actual state
                    console.log(blProp);
                    $('<?php 
                        foreach ($aField['importonlypaid']['disablefields'] as &$sField) {
                            $sField = $this->getField($sField, 'id');
                        }
                        echo '#'.  implode(', #', $aField['importonlypaid']['disablefields']);
                    ?>').attr('disabled', blProp);
                }).trigger('change');
            });
        })(jqml);
/*]]>*/</script>