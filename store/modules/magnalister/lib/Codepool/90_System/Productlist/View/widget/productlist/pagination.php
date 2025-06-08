<?php 
    /* @var $this  ML_Productlist_Controller_Widget_ProductList_Abstract */
    /* @var $oList ML_Productlist_Model_ProductList_Abstract */
    /* @var $aStatistic array */
    class_exists('ML',false) or die();
?>
<?php if($this instanceof ML_Productlist_Controller_Widget_ProductList_Abstract){ ?>
    <?php 
        if(
                !isset($aStatistic['blPagination'])
                ||
                (
                        isset($aStatistic['blPagination'])
                        &&
                        $aStatistic['blPagination']==true
                )
        ){
    ?>
    <?php 
        $iPageCount = ((int)($aStatistic['iCountTotal'] / $aStatistic['iCountPerPage'])) - ($aStatistic['iCountTotal'] % $aStatistic['iCountPerPage'] > 0 ? 0 : 1);
        $iPageCount = ($iPageCount < 0) ? 0 : $iPageCount;
    ?>
    <?php 
        $this->includeView(
            'widget_productlist_pagination_form_snippet', 
            array(
                'oList' => $oList, 
                'iLinkedPage' => 0, 
                'sLabel' => $this->__('Productlist_Pagination_sFirstPage'), 
                'aStatistic' => $aStatistic
            )
        );
    ?>
    <?php
        if ($iPageCount>5) {
            $iStart = $aStatistic['iCurrentPage']-5;
            $iStart = 
                $iStart<0
                ? 0
                : $iStart
            ;
            $iEnd = $aStatistic['iCurrentPage']+5;
            $iEnd = 
                $iEnd>$iPageCount
                ? $iPageCount
                : $iEnd
            ;
        } else {
            $iStart = 0;
            $iEnd = $iPageCount;
        }
    ?>
    <?php 
        echo 
            $iStart !== 0
            ? '...'
            : ''
        ;
    ?>
    <?php for ($iCount=$iStart;$iCount<=$iEnd;++$iCount) { ?>
        <?php
            $this->includeView(
                'widget_productlist_pagination_form_snippet', 
                array(
                    'oList' => $oList, 
                    'iLinkedPage' => $iCount, 
                    'sLabel' => $iCount+1, 
                    'aStatistic' => $aStatistic
                )
            );
        ?>
    <?php } ?>
    <?php echo $iEnd!==$iPageCount?'...':'';?>
    <?php 
        $this->includeView(
            'widget_productlist_pagination_form_snippet', 
            array(
                'oList' => $oList, 
                'iLinkedPage' => $iPageCount, 
                'sLabel' => $this->__('Productlist_Pagination_sLastPage'), 
                'aStatistic' => $aStatistic
            )
        );
    ?>
    <?php } ?>
<?php } ?>