<?php /* Smarty version Smarty-3.1.19, created on 2025-02-28 21:28:10
         compiled from "/homepages/40/d657041287/htdocs/modules/paypal/views/templates/installmentBanner/banner.tpl" */ ?>
<?php /*%%SmartyHeaderCode:113844499267c21c5a948458-17747248%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '087000da82c73eee1af2f15678bd7a6b78cce892' => 
    array (
      0 => '/homepages/40/d657041287/htdocs/modules/paypal/views/templates/installmentBanner/banner.tpl',
      1 => 1740773531,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '113844499267c21c5a948458-17747248',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_67c21c5a94c0e4_97656081',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_67c21c5a94c0e4_97656081')) {function content_67c21c5a94c0e4_97656081($_smarty_tpl) {?>

<?php echo $_smarty_tpl->getSubTemplate ('../_partials/javascript.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, null, array(), 0);?>


<div installment-container>
  <div banner-container>
    <div paypal-banner-message></div>
  </div>
</div>

<script>
  var Banner = function (conf) {

      this.placement = typeof conf.placement != 'undefined' ? conf.placement : null;

      this.amount = typeof conf.amount != 'undefined' ? conf.amount : null;

      this.layout = typeof conf.layout != 'undefined' ? conf.layout : null;

      this.color = typeof conf.color != 'undefined' ? conf.color : null;

      this.container = typeof conf.container != 'undefined' ? conf.container : null;

      this.textAlign = typeof conf.textAlign != 'undefined' ? conf.textAlign : null;
  };

  Banner.prototype.initBanner = function() {
      if (typeof paypalMessages == 'undefined') {
          setTimeout(this.initBanner.bind(this), 200);
          return;
      }

      var conf = {
          style: {
              ratio: '20x1'
          }
      };

      if (this.textAlign) {
          conf['style']['text'] = {
              'align': this.textAlign
          }
      }

      if (this.placement) {
          conf.placement = this.placement;
      }

      if (this.amount) {
          conf.amount = this.amount;
      }

      if (this.layout) {
          conf.style.layout = this.layout;
      }

      if (this.color && this.layout == 'flex') {
          conf.style.color = this.color;
      }

      paypalMessages.Messages(conf).render(this.container);
  };

  document.addEventListener('initPaypalBanner', Banner.init)
</script>
<?php }} ?>
