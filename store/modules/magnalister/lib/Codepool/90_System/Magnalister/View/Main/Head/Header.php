<?php 
    /* @var $this  ML_Productlist_Controller_Widget_ProductList_Abstract */ 
class_exists('ML',false) or die()?>
<style>
@-moz-keyframes ml-css-spin {
	0% {-moz-transform: rotate(0deg);}
	100% {-moz-transform: rotate(360deg);}
}
@-webkit-keyframes ml-css-spin {
	0% {-webkit-transform: rotate(0deg);}
	100% {-webkit-transform: rotate(360deg);}
}
@keyframes ml-css-spin {
	0% {transform: rotate(0deg);}
	100% {transform: rotate(360deg);}
}

.ml-css-loading {
	-o-box-sizing: border-box;
	-ie-box-sizing: border-box;
	-moz-box-sizing: border-box;
	-webkit-box-sizing: border-box;
	box-sizing: border-box;
	-moz-animation: ml-css-spin .8s infinite linear;
	-webkit-animation: ml-css-spin .8s infinite linear;
	animation: ml-css-spin .8s infinite linear;
}
</style>
<script type="text/javascript">/*<![CDATA[*/
        var debugging = <?php echo (MLSetting::gi()->get('blDebug')) ? 'true' : 'false'; ?>;
        if ((debugging === true) && window.console) {
            var myConsole = console;
        } else {
            var myConsole = {
                log: function(){},
                debug: function(){},
                info: function(){},
                warn: function(){},
                error: function(){},
                assert: function(){},
                dir: function(){},
                dirxml: function(){},
                trace: function(){},
                table: function(){},
                group: function(){},
                groupEnd: function(){},
                time: function(){},
                timeEnd: function(){},
                profile: function(){},
                profileEnd: function(){},
                count: function(){},
                table: function(){}
            }
        }

        var blockUICSS = {
            'border': 'none',
            'padding': '15px',
            'background-color': 'green',
            'border-radius': '10px',
            '-moz-border-radius': '10px',
            '-webkit-border-radius': '10px',
            'opacity': '0.8',
            'color': '#000',
            'font-size': '15px',
            'font-weight': 'bold'
        };
        var blockUIMessage = '<span><?php echo MLI18n::gi()->get('ML_TEXT_PLEASE_WAIT'); ?></span>';

        var blockUILoading = {
            overlayCSS: { 
                'background-color': '#fff',
                'opacity': '0.8',
                'z-index': '9000'
            },
            css: {
                'width': '32px',
                'height': '32px',
                'border-width': '4px',
                'border-style': 'solid',
                'border-color': 'rgba(199, 53, 47, 0.25) rgba(199, 53, 47, 0.25) rgba(199, 53, 47, 0.25) rgba(199, 53, 47, 1)',
                'border-radius': '32px',
                'padding': '0',
                'left': '50%',
                'margin': '0 0 0 -16px',
                'padding': '0',
                'top': '300px',
                'z-index': '9999',
                'background': 'transparent'
            },
            blockMsgClass: 'ml-css-loading',
            message: '<div></div>',
            onBlock: function() {
                jqml('.blockUI.ml-css-loading.blockPage').bind('dblclick', function() {
                    jqml.unblockUI();
                });
            }
        };
        var blockUIProgress = {
            overlayCSS: { 
                'background': '#000',
                'opacity': '0.1',
                'z-index': '9000'
            },
            css: {
                'background': '#fff',
                'width': '200px',
                'margin-left': '-100px',
                'height': '16px',
                'left': '50%',
                'padding': '10px',
                'border': 'none',
                'border-radius': '10px',
                '-moz-border-radius': '10px',
                '-webkit-border-radius': '10px',
                'box-shadow': '0 0 20px #000000',
                '-moz-box-shadow': '0 0 20px #000000',
                '-webkit-box-shadow': '0 0 20px #000000',
                'z-index': '9001'
            },
            message: '<div class="progressBarContainer"><div class="progressBar"></div><div class="progressPercent">0%</div></div>'
        };
			
        /* Preload Loading Animation */
        progressbarImage = new Image(); 
        progressbarImage.src = "<?php echo MLHttp::gi()->getResourceUrl('images/progressbar.png')?>";
        jqml(document).ready(function() {
        jqml("body").everyTime('120s', 'keepAlive', function(i) {
            jqml.get(
                "<?php echo MLHttp::gi()->getUrl(); ?>", {
                    '<?php echo MLHttp::gi()->parseFormFieldName('do')?>':'keepAlive'
                },
                function(data) {
                //myConsole.log(data);
                }
                );
        });
    });
/*]]>*/</script>	
<!--[if lt IE 9]><script type="text/javascript">/*<![CDATA[*/
    (function($) {
        $(document).ready(function() {
            $('div.magnamain').each(function() {
                $(this).css({height: this.scrollHeight < 181 ? "180px" : "auto"});
            });
        });
    })(jqml);
/*]]>*/</script><![endif]-->
<!--<devBar />-->
<h1 id="magnalogo" data-mlNeededFormFields='<?php echo count(MLHttp::gi()->getNeededFormFields()) == 0 ? '{}' : json_encode(MLHttp::gi()->getNeededFormFields());?>'>
    <a href="<?php echo $this->getUrl() ?>" title="<?php echo $this->__('ML_HEADLINE_MAIN'); ?>">
        <img src="<?php echo MLHttp::gi()->getResourceUrl('images/magnalister_logo.png') ; ?>" alt="<?php echo $this->__('ML_HEADLINE_MAIN'); ?>" width="165" height="42"/>
    </a>
</h1>
<?php if(MLSetting::gi()->get('blShowInfos')){
    try {
        $aModul = MLModul::gi()->getConfig();
    } catch (Exception $oEx) {
        $aModul = null;
    }
    ?>
<?php } ?>
<?php 
    $oProgress = 
        MLController::gi('widget_progressbar')
        ->setId('updatePlugin')
        ->setTitle(MLI18n::gi()->get('sModal_updatePlugin_title'))
        ->setContent(MLI18n::gi()->get('sModal_updatePlugin_content'))
        ->setTotal(isset($aAjaxData['Total']) ? $aAjaxData['Total'] : 100)
        ->setDone(isset($aAjaxData['Done']) ? $aAjaxData['Done'] : 0)
        ->render()
    ;

    if (!MLDatabase::factory('config')->set('mpid', 0)->set('mkey', 'after-update')->exists()) {// be sure update-scripts are done
        ?>
            <a id="ml-js-after-update" data-ml-modal="#<?php echo $oProgress->getId(); ?>" href="<?php echo $this->getUrl(array('do'=>'update', 'method'=>'afterUpdate')); ?>" data-ml-global-ajax='{"triggerAfterSuccess":"currentUrl"}' title="After update"  class="global-ajax ml-js-noBlockUi" style="display:none;"></a>
            <script type="text/javascript">/*<![CDATA[*/
            (function($) {
                $(document).ready(function(){
                    $('#ml-js-after-update').trigger('click');
                });
            })(jqml);
            /*]]>*/</script>
        <?php
    }
?>
<div id="globalButtonBox"><?php 
    require_once MLFilesystem::getOldLibPath('php/callback/callbackFunctions.php');
    $aSteps=array();
    if(!MLMessage::gi()->haveFatal()){
        $aTabIdents=  MLDatabase::factory('config')->set('mpid',0)->set('mkey','general.tabident')->get('value');
        foreach(magnaGetInvolvedMarketplaces() as $sMarketPlace){
            foreach(magnaGetInvolvedMPIDs($sMarketPlace) as $iMarketPlace){
                $aSteps['all'][] = 
                $aSteps[$sMarketPlace] [] = array(
                    'sKey' =>  MLHttp::gi()->parseFormFieldName('mpid'),
                    'sValue' => $iMarketPlace,
                    'sI18n' => $sMarketPlace.' ('.(isset($aTabIdents[$iMarketPlace])&&$aTabIdents[$iMarketPlace]!=''?$aTabIdents[$iMarketPlace].' - ':'').$iMarketPlace.')'
                );
            }
        }
    }
    foreach ($this->getButtons() as $blargh) {
        if (isset($blargh['type']) && $blargh['type'] === 'cron' ) { 
            if ($blargh['enabled'] === true) { 
                if(isset($aSteps[(isset($blargh['mpFilter']) ? $blargh['mpFilter'] : 'all')])){?> 
                <a data-steps="<?php echo htmlentities(json_encode($aSteps[(isset($blargh['mpFilter']) ? $blargh['mpFilter'] : 'all')])); ?>" class="gfxbutton border cron ml-js-noBlockUi <?php echo $blargh['icon']; ?>" href="<?php echo $this->getUrl($blargh['link']); ?>" title="<?php echo $this->__($blargh['title']); ?>"></a>
                <?php }
            }else{ ?>  
                <a style="opacity:0.4"  id ="<?php echo $blargh['id']  ?>"  class="gfxbutton border ml-js-noBlockUi <?php echo $blargh['icon']; ?>" href="<?php echo $this->getCurrentUrl(); ?>" title="<?php echo $this->__($blargh['title']); ?>"></a>
                <script type="text/javascript">/*<![CDATA[*/
                    (function ($) {
                        $(document).ready(function () {
                            $('<?php echo '#' . $blargh['id'] ?>').click(function (event) {
                                event.preventDefault();
                                $('<div><?php echo str_replace(array("\n", "\r", "'"), array('', '', "\\'"), '<div class="ml-addAddonError"></div>' . $blargh['disablemessage']) ?></div>').dialog({
                                    modal: true,
                                    width: '600px',
                                    buttons: {
                                        "<?php echo str_replace('"', '\"', $this->__('ML_BUTTON_LABEL_OK')); ?>": function () {
                                            $(this).dialog("close");
                                        }
                                    }
                                });
                            });
                        });
                    })(jqml);
                /*]]>*/</script><?php
        }
    } else {
        $blUpdate = true;
            if ($blargh['title'] == 'ML_LABEL_UPDATE') {
                try {
                    MLHelper::getFilesystemInstance()->updateTest();
                } catch (ML_Core_Exception_Update $oEx) {
                    ?><a data-ml-modal="#ml-notUpdateable" href="#ml-notUpdateable" class="gfxbutton border global-ajax ml-js-noBlockUi <?php echo $blargh['icon']; ?> " title="<?php echo $this->__($blargh['title']) ?>"></a><?php
                    ?>
                        <div id="ml-notUpdateable" class="ml-modal" title="<?php echo $this->__('sNotUpdateable_title'); ?>"><?php echo $oEx->getTranslation(); ?></div>
                    <?php
                    continue;
                }
            }
            ?><a data-ml-modal="#<?php echo $oProgress->getId(); ?>"  href="<?php echo $this->getUrl($blargh['link']); ?>" data-ml-global-ajax='{"triggerAfterSuccess":"currentUrl", "retryOnError": true}' class="gfxbutton border global-ajax ml-js-noBlockUi <?php echo $blargh['icon']; ?> " title="<?php echo $this->__($blargh['title']) ?>"></a><?php
        }
    }
    if (MLSetting::gi()->get('blDebug')) {
        ?><a data-ml-modal="#<?php echo $oProgress->getId(); ?>" href="<?php echo $this->getUrl(array('do'=>'update', 'method'=>'afterUpdate')); ?>" data-ml-global-ajax='{"triggerAfterSuccess":"currentUrl", "retryOnError": true}' title="After update"  class="gfxbutton border global-ajax ml-js-noBlockUi update" style="background-color:#cdd4ff;"></a><?php
    }
    MLSettingRegistry::gi()->addJs('jquery.magnalisterRecursiveAjax.js');
    ?>
    <script type="text/javascript">/*<![CDATA[*/
    (function($) {
        $(document).ready(function(){
            $('#globalButtonBox a.cron').click(function(){
                var aSteps=[];
                if($(this).hasClass('cron')){
                    aSteps = $(this).data('steps');
                }
                $(this).magnalisterRecursiveAjax({
                    sOffset:'<?php echo MLHttp::gi()->parseFormFieldName('offset') ?>',
                    sAddParam:'<?php echo MLHttp::gi()->parseFormFieldName('ajax') ?>=true',
                    aSteps: aSteps,
                    oI18n:{
                            sProcess    : '<?php echo $this->__s('ML_STATUS_FILTER_SYNC_CONTENT',array('\'')) ?>',
                            sError      : '<?php echo $this->__s('ML_ERROR_LABEL',array('\'')) ?>',
                            sSuccess    : '<?php echo $this->__s('ML_STATUS_FILTER_SYNC_SUCCESS',array('\'')) ?>'
                    },
                    onFinalize: function(){
                        window.location=window.location;//reload without post
                    },
                    onProgessBarClick:function(data){
                        console.dir({data:data});
                    },
                });
                return false;
            });
        });
    })(jqml);
    /*]]>*/</script>
</div>
<div class="visualClear">&nbsp;</div>
