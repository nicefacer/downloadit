<?php

class SellingAjaxEditController extends BaseAdminController
{
    public function indexAction()
    {

        $sellingId = UrlHelper::getGet("id", null);
        if (is_null($sellingId)) {
            RenderHelper::addError(L::t("Selling Not Found"));
            UrlHelper::redirect("selling/index");
            return;
        }

        $sellingEditGrid = new AjaxGrids_SellingEdit($sellingId);

        $processingResult = $sellingEditGrid->handleRequest(
            UrlHelper::getGet(),
            UrlHelper::getPost()
        );

        if ($processingResult == false) {
            $defaultGridHtml = $this->view("widget/ajaxgrid.phtml", $sellingEditGrid->getConfig(), false);

            $this->view("selling/ajaxedit/index.phtml", array('grid' => $defaultGridHtml, 'sellingId' => $sellingId));
            return;
        }

        RenderHelper::cleanOutput();
        RenderHelper::setJSONHeader();

        echo json_encode($processingResult);
    }
}