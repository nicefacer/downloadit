<?php 
class_exists('ML', false) or die();
if(!MLHttp::gi()->isAjax()){
    MLSetting::gi()->add('aCss','magnalister.ebayprepareform.css', true);
}
$this->getFormWidget();
