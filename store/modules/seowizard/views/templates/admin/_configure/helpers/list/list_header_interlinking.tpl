<form action="{$REQUEST_URI|escape:'html':'UTF-8'}" method="post">
    <div class="alert alert-warning">
        <h4>{l s='How do you want to delete the selected keyword?' mod='seowizard'}</h4>
        <p>{l s='Are you want to remove the linking from this keyword. Please choose your preferred method.' mod='seowizard'}</p>
        <br>
        <ul class="listForm list-unstyled">
            <li>
                <label for="deleteMode_real" class="control-label">
                    <input type="radio" name="deleteLinking" value="1" id="removelinking" />
                    {l s='Yes, Remove linking from this keyword. This process may take some time.' mod='seowizard'}
                </label>
            </li>
            <li>
                <label for="deleteMode_deleted" class="control-label">
                    <input type="radio" name="deleteLinking" value="0" id="removelinking" checked="checked" />
                    {l s='No, Do not remove linking from this keyword.' mod='seowizard'}
                </label>
            </li>
        </ul>
        <input type="submit" class="btn btn-default" value="{l s='Delete' mod='seowizard'}" />
    </div>
</form>

{*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer tohttp://www.prestashop.com for more information.
* We offer the best and most useful modules PrestaShop and modifications for your online store.
*
* @category  PrestaShop Module
* @author    knowband.com <support@knowband.com>
* @copyright 2017 Knowband
* @license   see file: LICENSE.txt
*
* Description
*
* Admin tpl file
*}

