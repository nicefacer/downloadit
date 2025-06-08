<?php
/**
 * 888888ba                 dP  .88888.                    dP                
 * 88    `8b                88 d8'   `88                   88                
 * 88aaaa8P' .d8888b. .d888b88 88        .d8888b. .d8888b. 88  .dP  .d8888b. 
 * 88   `8b. 88ooood8 88'  `88 88   YP88 88ooood8 88'  `"" 88888"   88'  `88 
 * 88     88 88.  ... 88.  .88 Y8.   .88 88.  ... 88.  ... 88  `8b. 88.  .88 
 * dP     dP `88888P' `88888P8  `88888'  `88888P' `88888P' dP   `YP `88888P' 
 *
 *                          m a g n a l i s t e r
 *                                      boost your Online-Shop
 *
 * -----------------------------------------------------------------------------
 * $Id$
 *
 * (c) 2010 - 2014 RedGecko GmbH -- http://www.redgecko.de
 *     Released under the MIT License (Expat)
 * -----------------------------------------------------------------------------
 */
class_exists('ML', false) or die();
?>
<?php
$blInstalled = ML::isInstalled();
$sJquerySelector = $blInstalled ? '' : ' > #ml-loader-installation-wrap > #ml-loader-installation';
if (MLHttp::gi()->isAjax()) {
    ?>
    <?php
    MLSetting::gi()->add('aAjaxPlugin', array('dom' => array(
            '#' . $this->getId() . $sJquerySelector . '>.ml-js-modalPushMessages' => $this->includeViewBuffered('widget_progressbar_messages'),
            '#' . $this->getId() . $sJquerySelector . '>.viaAjax' => $this->includeViewBuffered('widget_progressbar_content'),
            '#' . $this->getId() . $sJquerySelector . '>.progressBarContainer' => $this->includeViewBuffered('widget_progressbar_bar'),
            '#' . $this->getId() . $sJquerySelector . '>.console>.console-content' => array(
                'action' => 'append',
                'content' => $this->includeViewBuffered('widget_progressbar_log')
            ),
    )));
    ?>
<?php } else { ?>
    <div <?php echo ($blInstalled ? 'style="display:none;" ' : '') ?>id="<?php echo $this->getId(); ?>"<?php if ($blInstalled) { ?> class="ml-modal ml-modal-notcloseable"<?php } ?> title="<?php echo $this->getTitle(); ?>">
        <?php if (!$blInstalled) { ?>
            <div id="ml-loader-installation-wrap">
                <div id="ml-loader-installation">
                <?php } ?>
                <div class="ml-js-modalPushMessages ml-js-mlMessages">
                    <?php $this->includeView('widget_progressbar_messages'); ?>
                </div>
                <div class="viaAjax">
                    <?php $this->includeView('widget_progressbar_content'); ?>
                </div>

                <div class="progressBarContainer">
                    <?php $this->includeView('widget_progressbar_bar'); ?>
                </div>
                <?php if (!$blInstalled) { ?>
                    <h1><?php echo MLI18n::gi()->get('installation_content_title') ?></h1>
                    <div id="ml-loader-wrap">
                        <img src="<?php echo MLHttp::gi()->getResourceUrl('images/installation_graphic_' . MLI18n::gi()->getLang() . '.jpg'); ?>" alt="" title="">
                        <div id="installationBox1" class="installationBox">
                            <p><?php echo MLI18n::gi()->get('installation_content_firststep') ?> <a target="_blank" href="<?php echo MLI18n::gi()->get('installation_content_freetest_url') ?>"><?php echo MLI18n::gi()->get('installation_content_notcutomer') ?></a></p>
                        </div>
                        <div id="installationBox2" class="installationBox">
                            <p><?php echo MLI18n::gi()->get('installation_content_secondstep') ?></p>
                        </div>
                        <div  id="installationBox3"  class="installationBox">
                            <p><?php echo MLI18n::gi()->get('installation_content_thirdstep') ?><a target="_blank" href="<?php echo MLI18n::gi()->get('installation_content_help_url') ?>"><?php echo MLI18n::gi()->get('installation_content_help') ?></a></p>
                        </div>
                        <div  id="installationBox4"  class="installationBox">
                            <p><?php echo MLI18n::gi()->get('installation_content_fourthstep') ?></p>
                        </div>
                    </div>
                <?php } ?>
                <?php if (MLSetting::gi()->get('blDebug')) { ?>
                    <div id="<?php echo $this->getId(); ?>Log" class="console">
                        <div class="console-head">Log</div>
                        <div class="console-content">
                            <?php $this->includeView('widget_progressbar_log'); ?>
                        </div>
                    </div>
                    <?php
                }
                if (!$blInstalled) {
                    ?>
                </div>
            </div>
        <?php } ?>
    </div>
    <?php
}