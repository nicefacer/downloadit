<?php
MLFilesystem::gi()->loadClass('Core_Controller_Abstract');
abstract class ML_Tabs_Controller_Widget_Tabs_Abstract extends ML_Core_Controller_Abstract {
    
    abstract public function getTabsWidget();
    /**
     * array(
     *  array(
     *      [title] => 
     *      [label] => 
     *      [url] => 
     *      [image] => 
     *      [class] => 
     *  ),
     *  ...
     * )
     * @return array 
     */
    abstract public function getTabs();
    
    /**
     * @return ML_Core_Controller_Abstract
     */
    abstract public function getTabContentController();
    
    /**
     * needed for prepare request-object, to add parent parameters, if not exists
     * array(
     *  'http-parameter'=>array('of','possible','parameters')
     * )
     * @return array 
     */
    abstract public function getTabUrlHierarchy();
    
}