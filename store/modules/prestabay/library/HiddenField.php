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

class HiddenField
{
    protected $_id = null;
    protected $_params = null;
    protected $_defaultParams = array(
        'type' => 'hidden',
        'value' => '',
    );

    public function __construct($id, $params)
    {
        $this->_id = $id;

        $this->_params = $params;
        $this->_params+=$this->_defaultParams;
    }

    public function getHtml()
    {

        $completedHtml = "<input id='hidden_" . $this->_id . "'";
        foreach ($this->_params as $paramKey => $paramValue) {
            $completedHtml.=" " . $paramKey . "='" . $paramValue . "'";
        }

        $completedHtml.="/>";

        return $completedHtml;
    }

}