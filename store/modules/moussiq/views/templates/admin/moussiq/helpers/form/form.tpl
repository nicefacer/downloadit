{**
* Moussiq PRO
*
* @category  Module
* @author    silbersaiten <info@silbersaiten.de>
* @support   silbersaiten <support@silbersaiten.de>
* @copyright 2014 silbersaiten
* @version   2.2.0
* @link      http://www.silbersaiten.de
* @license   See joined file licence.txt
*}
{extends file="helpers/form/form.tpl"}

{block name="other_input"}
	{if $key == 'availfields'}
		<div class="col-lg-12">
			{$field|escape:"email":"UTF-8"}
		</div>
	{/if}
{/block}