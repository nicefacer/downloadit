<?php /* Smarty version Smarty-3.1.19, created on 2025-06-02 14:05:35
         compiled from "/homepages/40/d657041287/htdocs/modules/dashproducts/views/templates/hook/dashboard_zone_two.tpl" */ ?>
<?php /*%%SmartyHeaderCode:85103920683d938f786df2-27631388%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '473b025d8db9d4889fdfcf9f659c6ed691bb5efc' => 
    array (
      0 => '/homepages/40/d657041287/htdocs/modules/dashproducts/views/templates/hook/dashboard_zone_two.tpl',
      1 => 1738189813,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '85103920683d938f786df2-27631388',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'allow_push' => 0,
    'dashproducts_config_form' => 0,
    'DASHPRODUCT_NBR_SHOW_LAST_ORDER' => 0,
    'DASHPRODUCT_NBR_SHOW_BEST_SELLER' => 0,
    'date_from' => 0,
    'date_to' => 0,
    'DASHPRODUCT_NBR_SHOW_TOP_SEARCH' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_683d938f7a1ae4_18863874',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_683d938f7a1ae4_18863874')) {function content_683d938f7a1ae4_18863874($_smarty_tpl) {?>

<section id="dashproducts" class="panel widget <?php if ($_smarty_tpl->tpl_vars['allow_push']->value) {?> allow_push<?php }?>">
	<header class="panel-heading">
		<i class="icon-bar-chart"></i> <?php echo smartyTranslate(array('s'=>'Products and Sales','mod'=>'dashproducts'),$_smarty_tpl);?>

		<span class="panel-heading-action">
			<a class="list-toolbar-btn" href="#" onclick="toggleDashConfig('dashproducts'); return false;" title="<?php echo smartyTranslate(array('s'=>'Configure','mod'=>'dashproducts'),$_smarty_tpl);?>
">
				<i class="process-icon-configure"></i>
			</a>
			<a class="list-toolbar-btn" href="#"  onclick="refreshDashboard('dashproducts'); return false;"  title="<?php echo smartyTranslate(array('s'=>'Refresh','mod'=>'dashproducts'),$_smarty_tpl);?>
">
				<i class="process-icon-refresh"></i>
			</a>
		</span>
	</header>

	<section id="dashproducts_config" class="dash_config hide">
		<header><i class="icon-wrench"></i> <?php echo smartyTranslate(array('s'=>'Configuration','mod'=>'dashproducts'),$_smarty_tpl);?>
</header>
		<?php echo $_smarty_tpl->tpl_vars['dashproducts_config_form']->value;?>

	</section>

	<section>
		<nav>
			<ul class="nav nav-pills">
				<li class="active">
					<a href="#dash_recent_orders" data-toggle="tab">
						<i class="icon-fire"></i>
						<span class="hidden-inline-xs"><?php echo smartyTranslate(array('s'=>'Recent Orders','mod'=>'dashproducts'),$_smarty_tpl);?>
</span>
					</a>
				</li>
				<li>
					<a href="#dash_best_sellers" data-toggle="tab">
						<i class="icon-trophy"></i>
						<span class="hidden-inline-xs"><?php echo smartyTranslate(array('s'=>'Best Sellers','mod'=>'dashproducts'),$_smarty_tpl);?>
</span>
					</a>
				</li>
				<li>
					<a href="#dash_most_viewed" data-toggle="tab">
						<i class="icon-eye-open"></i>
						<span class="hidden-inline-xs"><?php echo smartyTranslate(array('s'=>'Most Viewed','mod'=>'dashproducts'),$_smarty_tpl);?>
</span>
					</a>
				</li>
				<li>
					<a href="#dash_top_search" data-toggle="tab">
						<i class="icon-search"></i>
						<span class="hidden-inline-xs"><?php echo smartyTranslate(array('s'=>'Top Searches','mod'=>'dashproducts'),$_smarty_tpl);?>
</span>
					</a>
				</li>
			</ul>
		</nav>

		<div class="tab-content panel">
			<div class="tab-pane active" id="dash_recent_orders">
				<h3><?php echo smartyTranslate(array('s'=>'Last %d orders','sprintf'=>intval($_smarty_tpl->tpl_vars['DASHPRODUCT_NBR_SHOW_LAST_ORDER']->value),'mod'=>'dashproducts'),$_smarty_tpl);?>
</h3>
				<div class="table-responsive">
					<table class="table data_table" id="table_recent_orders">
						<thead></thead>
						<tbody></tbody>
					</table>
				</div>
			</div>
			<div class="tab-pane" id="dash_best_sellers">
				<h3>
					<?php echo smartyTranslate(array('s'=>'Top %d products','sprintf'=>intval($_smarty_tpl->tpl_vars['DASHPRODUCT_NBR_SHOW_BEST_SELLER']->value),'mod'=>'dashproducts'),$_smarty_tpl);?>

					<span><?php echo smartyTranslate(array('s'=>"From",'mod'=>'dashproducts'),$_smarty_tpl);?>
 <?php echo $_smarty_tpl->tpl_vars['date_from']->value;?>
 <?php echo smartyTranslate(array('s'=>"to",'mod'=>'dashproducts'),$_smarty_tpl);?>
 <?php echo $_smarty_tpl->tpl_vars['date_to']->value;?>
</span>
				</h3>
				<div class="table-responsive">
					<table class="table data_table" id="table_best_sellers">
						<thead></thead>
						<tbody></tbody>
					</table>
				</div>
			</div>
			<div class="tab-pane" id="dash_most_viewed">
				<h3>
					<?php echo smartyTranslate(array('s'=>"Most Viewed",'mod'=>'dashproducts'),$_smarty_tpl);?>

					<span><?php echo smartyTranslate(array('s'=>"From",'mod'=>'dashproducts'),$_smarty_tpl);?>
 <?php echo $_smarty_tpl->tpl_vars['date_from']->value;?>
 <?php echo smartyTranslate(array('s'=>"to",'mod'=>'dashproducts'),$_smarty_tpl);?>
 <?php echo $_smarty_tpl->tpl_vars['date_to']->value;?>
</span>
				</h3>
				<div class="table-responsive">
					<table class="table data_table" id="table_most_viewed">
						<thead></thead>
						<tbody></tbody>
					</table>
				</div>
			</div>
			<div class="tab-pane" id="dash_top_search">
				<h3>
					<?php echo smartyTranslate(array('s'=>'Top %d most search terms','sprintf'=>intval($_smarty_tpl->tpl_vars['DASHPRODUCT_NBR_SHOW_TOP_SEARCH']->value),'mod'=>'dashproducts'),$_smarty_tpl);?>

					<span><?php echo smartyTranslate(array('s'=>"From",'mod'=>'dashproducts'),$_smarty_tpl);?>
 <?php echo $_smarty_tpl->tpl_vars['date_from']->value;?>
 <?php echo smartyTranslate(array('s'=>"to",'mod'=>'dashproducts'),$_smarty_tpl);?>
 <?php echo $_smarty_tpl->tpl_vars['date_to']->value;?>
</span>
				</h3>
				<div class="table-responsive">
					<table class="table data_table" id="table_top_10_most_search">
						<thead></thead>
						<tbody></tbody>
					</table>
				</div>
			</div>
		</div>

	</section>
</section>
<?php }} ?>
