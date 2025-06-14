<?php
/*
* 2007-2014 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2014 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/
class CmsController extends CmsControllerCore
{
	/*
    * module: smartshortcode
    * date: 2016-02-15 15:40:28
    * version: 2.3.0
    */
    public function initContent()
	{
		parent::initContent();
		  	if((bool)Module::isEnabled('smartshortcode'))
		  	    {
		  	      	if(context::getcontext()->controller->controller_type == 'front')
		  	      	{
		  	      		$smartshortcode = Module::getInstanceByName('smartshortcode');
		  	          $this->cms->content = $smartshortcode->parse($this->cms->content );
		  	      	}
		  	    }
	}
}
