<?php class_exists('ML', false) or die() ?>
<?php
MLSetting::gi()->add('aCss', array('magnalister.productlist.css?%s'), true);
/* @var $this   ML_Listings_Controller_Widget_Listings_InventoryAbstract */
$this->includeView('widget_listings_misc_listingbox');
?>
<form action="<?php echo $this->getCurrentUrl() ?>"  method="post" class="ml-plist ml-js-plist">
    <div>
        <?php 
            foreach(MLHttp::gi()->getNeededFormFields() as $sName=>$sValue ){
                ?><input type="hidden" name="<?php echo $sName ?>" value="<?php echo $sValue?>" /><?php
            }
            
            if (isset($this->aPostGet['sorting'])) { ?>
                <input type="hidden" name="ml[sorting]" value="<?php echo $this->aPostGet['sorting'] ?>" />
            <?php 
            }
        ?>
    </div>
    <?php
    $this->initAction();
    $this->prepareData();
    $this->includeView('widget_listings_misc_pagination'); ?>
    <table class="datagrid" >
        <thead >
            <tr>
                <th class="nowrap" style="width: 5px;">
                    <input type="checkbox" id="selectAll"/><label for="selectAll"><?php echo $this->__('ML_LABEL_CHOICE') ?></label>
                </th>
                <?php foreach ($this->getFields() as $aFiled) { ?>            
                    <th> <?php
                        echo $aFiled['Label'];
                        if (isset($aFiled['Sorter'])) {
                            if ($aFiled['Sorter'] != null) {
                                ?>
                                <input class="noButton ml-right arrowAsc" type="submit" value="<?php echo $aFiled['Sorter'] ?>-asc" title="<?php echo $this->__('Productlist_Header_sSortAsc') ?>"  name="<?php echo MLHttp::gi()->parseFormFieldName('sorting'); ?>" />
                                <input class="noButton ml-right arrowDesc" type="submit" value="<?php echo $aFiled['Sorter'] ?>-desc" title="<?php echo $this->__('Productlist_Header_sSortDesc') ?>"  name="<?php echo MLHttp::gi()->parseFormFieldName('sorting'); ?>" />
                        <?php } } ?>
                    </th>
                <?php } ?>
            </tr>
        </thead>
        <tbody>
            <?php
            if (empty($this->aData)) {
                ?>
                <tr>
                    <td colspan="<?php echo count($this->getFields()) + 1; ?>">
                        <?php echo $this->__($this->getEmptyDataLabel()) ?>
                    </td>
                </tr>
                <?php
            } else {
                $oddEven = false;
                foreach ($this->aData as $item) {
                    $sDetails = htmlspecialchars(str_replace('"', '\\"', serialize(array(
                        'SKU' => $item['SKU'],
                        'Price' => $item['Price'],
                        'Currency' => isset($item['Currency']) ? $item['Currency'] : '',
                    ))));
                    ?>
                    <tr class="<?php echo (($oddEven = !$oddEven) ? 'odd' : 'even') ?>">
                        <td>
                            <input type="checkbox" name="<?php echo MLHttp::gi()->parseFormFieldName('SKUs[]') ?>" value="<?php echo $item['SKU'] ?>">
                            <input type="hidden" name="<?php echo MLHttp::gi()->parseFormFieldName("details[{$item['SKU']}]") ?>" value="<?php echo $sDetails ?>">
                        </td>
                        <?php
                        foreach ($this->getFields() as $aField) {
                            if ($aField['Field'] != null) {?>
                                <td><?php
                                if (array_key_exists($aField['Field'], $item)) { 
                                    echo $item[$aField['Field']] ;
                                }?></td>
                                <?php
                            } else {
                                echo call_user_func(array($this, $aField['Getter']), $item);
                            }
                        }
                        ?>
                    </tr>
                    <?php
                }
            }
            ?>
        </tbody>
    </table>
    <?php $this->includeView('widget_listings_misc_action'); ?>

    <script type="text/javascript">/*<![CDATA[*/
        jqml(document).ready(function() {
            jqml('#selectAll').click(function() {
                state = jqml(this).attr('checked') !== undefined;
                jqml('.ml-js-plist input[type="checkbox"]:not([disabled])').each(function() {
                    jqml(this).attr('checked', state);
                });
            });
        });
        /*]]>*/</script>
</form>
<script type="text/javascript">/*<![CDATA[*/
    jqml(document).ready(function() {
        jqml('form.ml-js-plist').submit(function() {
            jqml.blockUI(blockUILoading);
        });
    });
    /*]]>*/</script>
