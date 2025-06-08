<?php 
    class_exists('ML',false) or die();
    if (MLHttp::gi()->isAjax()) {
        ob_start();
    } else {
        ?><div id="debug-setting-all"><?php
    }
?>
<form action="<?php echo $this->getCurrentUrl() ?>" method="post">
    <div>
        <?php foreach (MLHttp::gi()->getNeededFormFields() as $sName => $sValue) { ?>
            <input type="hidden" name="<?php echo $sName ?>" value="<?php echo $sValue ?>" />
        <?php } ?>
        <table style="width:100%">
            <?php foreach ($this->getData() as $sName => $mValue) { ?>
                <tr>
                    <th style="text-align: left">
                        <label for="<?php echo $sName ?>" style="width:300px">
                            <?php echo $sName ?>
                        </label>
                    </th>
                    <th>&nbsp;:&nbsp;</th>
                    <td style="width:100%;">
                        <?php if (substr($sName, 0, 2) == 'bl') { //bool ?>
                            <input type="hidden" name="<?php echo MLHttp::gi()->parseFormFieldName('setting['.$sName.']') ?>" value="0" />
                            <input id="<?php echo $sName ?>" type="checkbox" name="<?php echo MLHttp::gi()->parseFormFieldName('setting['.$sName.']') ?>"<?php echo ($mValue ? ' checked="checked"' : '') ?> value="1" />
                        <?php } elseif (
                                substr($sName, 0, 1) == 's'
                                ||
                                substr($sName, 0, 1) == 'i'
                         ) { //string ?>
                            <?php if ($sName == 'sShowToolsMenu') {?>
                                <select style="width:100%;" id="<?php echo $sName ?>" name="<?php echo MLHttp::gi()->parseFormFieldName('setting['.$sName.']') ?>">
                                    <?php foreach (array('', 'time', 'settings', 'sql', 'api', 'config', 'request', 'messages', 'session', 'tree') as $sOptionValue) { ?>
                                        <option <?php echo $sOptionValue==$mValue ? 'selected="selected"' : '';?>value="<?php echo $sOptionValue ?>"><?php echo $sOptionValue ?></option>
                                    <?php } ?>
                                </select>
                            <?php }else {?>
                                <input style="width:100%;" id="<?php echo $sName ?>" type="text" name="<?php echo MLHttp::gi()->parseFormFieldName('setting['.$sName.']') ?>" value="<?php echo $mValue ?>" />
                            <?php } ?>
                        <?php } ?>
                    </td>
                </tr>
            <?php } ?>
            <tr>
                <td>
                    <input class="button" type="submit" />
                </td>
                <td></td>
                <td>
                    <?php if($this->isChanged()){?>
                        <input class="button" type="submit" name="<?php echo MLHttp::gi()->parseFormFieldName('resetSetting') ?>" value="Reset"/>
                    <?php } ?>
                </td>
            </tr>
        </table>
    </div>
</form>
<?php
    if (MLHttp::gi()->isAjax()) {
        MLSetting::gi()->add('aAjaxPlugin', array('dom' => array('#debug-setting-all' => ob_get_contents())));
        ob_end_clean();
    } else {
        ?></div><?php
    }
?>