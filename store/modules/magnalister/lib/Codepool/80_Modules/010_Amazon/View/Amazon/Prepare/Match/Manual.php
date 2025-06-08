<?php class_exists('ML',false) or die()?>
<?php $aStatistic=$this->getProductlist()->getStatistic();?>
<h1><?php echo MLI18n::gi()->get(
        $this->isSingleMatching()?
        'Amazon_Productlist_Match_Manual_Title_Single':
        'Amazon_Productlist_Match_Manual_Title_Multi'
        )?></h1>
<?php $this->getProductListWidget();?>