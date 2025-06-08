<?php
/**
 * File ActionButton.php
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * It is available through the world-wide-web at this URL:
 * http://involic.com/license.txt
 * If you are unable to obtain it through the world-wide-web,
 * please send an email to license@involic.com so
 * we can send you a copy immediately.
 *
 * eBay Listener Itegration with PrestaShop e-commerce platform.
 * Adding possibilty list PrestaShop Product dirrectly to eBay.
 *
 * @author      Involic <contacts@involic.com>
 * @copyright   Copyright (c) 2011-2015 by Involic (http://www.involic.com)
 * @license     http://involic.com/license.txt
 */

class ActionButton
{
    protected $_id = null;
    protected $_params = null;
    protected $_defaultParams = array(
        'type' => 'button',
        'value' => 'Button',
    );

    protected $actionHandler;

    public function __construct($id, $params, $actionHandler = null)
    {
        $this->_id = $id;

        $this->_params = $params;
        $this->_params+=$this->_defaultParams;

        if (is_callable($actionHandler)) {
            $this->actionHandler = $actionHandler;
        }
    }

    /**
     * Execute button action
     *
     * @param array $params parameters to button
     *
     * @return mixed result of execution
     */
    public function execute($params = array(), $extraValue = false)
    {
        if (is_callable($this->actionHandler)) {
            return call_user_func($this->actionHandler, $params, $extraValue);
        }

        return false;
    }

    public function getHtml()
    {
        $text = '';
        $items = array();

        if (isset($this->_params['value'])) {
            $text = $this->_params['value'];
            unset($this->_params['value']);
        }
        if (isset($this->_params['items'])) {
            $items = $this->_params['items'];
            unset($this->_params['items']);
        }
        if (!isset($this->_params['inlineButton']) || !$this->_params['inlineButton']) {
            // ng-click only if this is not inLine button
            $this->_params['ng-click'] = 'gridCtrl.buttonAction("' . $this->_id . '", false)';
        }

        $completedHtml = "<button id='button_" . $this->_id . "'";
        foreach ($this->_params as $paramKey => $paramValue) {
            $completedHtml.=" " . $paramKey . "='" . $paramValue . "'";
        }

        $extraIcon = '';
        if (count($items) > 0) {
            $extraIcon = '&nbsp;<i class="icon-caret-down"></i>';
        }
        $completedHtml.=">{$text}{$extraIcon}</button>";
        if (count($items) > 0) {
            $completedHtml.='<ul class="dropdown-menu">';
                foreach ($items as $item) {
                    $ngOptionClick = 'gridCtrl.buttonAction("' . $this->_id . '","'.$item['id'].'")';
                    $completedHtml.="<li><a href='' ng-click='".$ngOptionClick."'>".$item['name'].'</a></li>';
                }
            $completedHtml.='</ul>';

            $completedHtml = "<div class='btn-group dropdown'>".$completedHtml."</div>";
        }
        return $completedHtml;
    }

}