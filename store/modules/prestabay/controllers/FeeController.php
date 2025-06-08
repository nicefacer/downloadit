<?php
/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * It is available through the world-wide-web at this URL:
 * http://involic.com/license.txt
 * If you are unable to obtain it through the world-wide-web,
 * please send an email to license@involic.com so
 * we can send you a copy immediately.
 *
 * PrestaBay - eBay Integration with PrestaShop e-commerce platform.
 * Adding possibility list PrestaShop Product dirrectly to eBay.
 *
 * @author      Involic <contacts@involic.com>
 * @copyright   Copyright (c) 2011- 2016 by Involic (http://www.involic.com)
 * @license     http://involic.com/license.txt
 */

/**
 * Class FeeController - Fee log
 */
class FeeController extends BaseAdminController
{
    /**
     * Fee Log
     */
    public function indexAction()
    {
        $selectedAccountId = UrlHelper::getGet("account_select", false);
        $myGrid = new Grids_FeeListings();
        if ($selectedAccountId) {
            $myGrid->setAccountFilter($selectedAccountId);
            $myGrid->init();
        }
        $grid = $myGrid->getHtml(false);

        $accountsModel = new AccountsModel();

        $this->view('feeListings/index.phtml', array(
            'grid' => $grid,
            'accountsList' => $accountsModel->getSelect()->getItems(),
            'selectedAccountId' => $selectedAccountId
        ));
    }

    /**
     * Fee Total
     */
    public function totalAction()
    {
        $selectedAccountId = UrlHelper::getGet("account_select", false);
        $range = UrlHelper::getGet("range", false);

        $myGrid = new Grids_FeeTotalListings();
        if ($selectedAccountId) {
            $myGrid->setAccountFilter($selectedAccountId);
            $myGrid->init();
        }

        if ($range) {
            $myGrid->setRangeFilter($range);
            $myGrid->init();
        }
        $grid = $myGrid->getHtml(false);

        $accountsModel = new AccountsModel();

        $this->view('feeListings/index.phtml', array(
            'grid' => $grid,
            'accountsList' => $accountsModel->getSelect()->getItems(),
            'selectedAccountId' => $selectedAccountId
        ));
    }
}