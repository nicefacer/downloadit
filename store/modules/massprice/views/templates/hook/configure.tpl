{*
* 2007-2022 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
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
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2022 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
<script type="text/javascript">
	$(document).ready(function(){
		var id_section = {$section_adminpage};
		var section = ["general", "configuracion", "ayuda"];
		var tabs = ["tab1", "tab2", "tab3"];

		switch(id_section) {
			case 2:
		        sectindex = "configuracion";
		        tabindex = "tab2";
		        break;
		    case 3:
		        sectindex = "ayuda";
		        tabindex = "tab3";
		        break;
		    case 1:
		    	sectindex = "general";
		        tabindex = "tab1";
		        break;
		    default:
		    	sectindex = "general";
		        tabindex = "tab1";
		        break;
		}

		loop_section(sectindex, tabindex);

		//click tab event
		$("#general_tab").click(function(){
			loop_section("general", "tab1");
		});
		$("#configuracion_tab").click(function(){
			loop_section("configuracion", "tab2");
		});
		$("#ayuda_tab").click(function(){
			loop_section("ayuda", "tab3");
		});


		function loop_section(contentindex, tab){
			var index;
			for (index = 0; index < section.length; ++index) {
			    console.log(section[index]+"=="+contentindex);

			    if(section[index] == contentindex){
			    	$("#"+contentindex).addClass("active");
			    }else{
			    	$("#"+section[index]).removeClass("active");
			    }
			}

			var indextab;
			for (indextab = 0; indextab < tabs.length; ++indextab) {
			    console.log(tabs[indextab]+"=="+tab);

			    if(tabs[indextab] == tab){
			    	console.log("#"+tab);

			    	$("#"+tab).addClass("active");
			    }else{
			    	$("#"+tabs[indextab]).removeClass("active");
			    }
			}
		}
	});	
</script>


<ul class="nav nav-tabs" id="wimhide">				
	<li id="tab1" class="active">
		<a href="#" id="general_tab">
			<i class="icon-home"></i>
			  {l s='Dashboard' mod='massprice'}
		</a>
	</li>
	<li id="tab2">
		<a href="#" id="configuracion_tab">
			<i class="icon-database"></i>
			  {l s='Configuration' mod='massprice'}
		</a>
	</li>
	<li id="tab3">
		<a href="#" id="ayuda_tab">
			<i class="icon-cogs"></i>
			  {l s='Help' mod='massprice'}
		</a>
	</li>


</ul>
<div class="tab-content panel">	
	<div class="tab-pane active" id="general">
	<h1><i class="icon icon-credit-card"></i> {l s='Mass price update' mod='massprice'}</h1>
    {$displayinfo}
	</div>
	<div class="tab-pane" id="configuracion">
		{$renderForm}
	</div>
	<div class="tab-pane" id="ayuda">
    {$displayadds}
	</div>	
</div>





