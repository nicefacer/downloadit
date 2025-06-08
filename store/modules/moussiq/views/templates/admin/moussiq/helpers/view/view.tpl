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
{extends file="helpers/view/view.tpl"}

{block name="override_tpl"}
	{if !isset($errors) || $errors|count == 0}
		<div class="panel">
			<div style="margin-bottom: 1em;">
				<a href="{$back_url|escape}" class="btn btn-default"> {l s='Back to services list' mod='moussiq'}</a>
			</div>
			{*$exporter*}
			<div class="panel">
				<form method="post" class="form-horizontal">

					<div class="panel-heading">
						{l s='Service Info' mod='moussiq'}
					</div>
					<h2>{l s='Link to CSV File:' mod='moussiq'}</h2>
					<div class="margin-form">
					{if isset($file_exists) && $file_exists}
						<h3><a style="color: blue;" href="{$fileLink|escape}">{$fileLink|escape}</a></h3>
						<h4>{l s='Size:' mod='moussiq'} <b>{$fileSize|escape}</b></h4>
						<h4>{l s='Last Modified:' mod='moussiq'} <b>{$fileModified|escape}</b></h4>
					{else}
						<h2>{l s='CSV file have not been generated yet' mod='moussiq'}</h2>
					{/if}
					</div>
					{if isset($file_exists) && $file_exists}
						<h2>{l s='CSV Contents' mod='moussiq'}</h2>
						<div style="width: 100%; {if $fileNotTooBig}'height: 600px; overflow: auto; margin-bottom: 1em;'{/if}">
							{if $fileNotTooBig}
								{if isset($fileData)}
									<table class="table csvContents" cellspacing="0" cellpadding="0">
										{foreach from=$fileData item=row name="row_file"}
											<tr{if $smarty.foreach.row_file.index % 2} class="alt_row"{else}{/if}>
												{foreach from=$row item=row_column}
													{if $smarty.foreach.row_file.index == 0 && $header_exists === 1}
														<th>{$row_column|escape}</th>
													{else}
														<td>{$row_column|escape}</td>
													{/if}
												{/foreach}
											</tr>
										{/foreach}
									</table>
								{/if}
							{else}
								<h3>{l s='This file is too large to be displayed here, please use the link above to download it and view locally.' mod='moussiq'}</h3>
							{/if}
						</div>
					{/if}
					<div><input type="submit" value="{l s='Generate CSV' mod='moussiq'}" name="csvGen" class="btn btn-default" /></div>
				</form>
			</div>
			<div style="margin-top: 1em;">
				<a href="{$back_url|escape}" class="btn btn-default"> {l s='Back to services list' mod='moussiq'}</a>
			</div>
		</div>
	{/if}
{/block}
