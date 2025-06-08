<?php

class PrestaController extends BaseAdminController
{
    /**
     * Ajax handler for save PS product tab data
     * This is duplicate for hook handle
     */
    public function saveProductDataAction()
    {
        RenderHelper::cleanOutput();

        $idProduct = UrlHelper::getPost('id_product', false);
        $productData = UrlHelper::getPost('prestabay', array());

        if (!$idProduct || empty($productData)) {
            echo json_encode(array('success' => false, 'message' => 'Empty Request'));
            return;
        }

        $shopId = 0;
        if (CoreHelper::isPS15()) {
            $shopId = (int)Context::getContext()->shop->id;
        }
        $productEbayData = ProductEbayDataModel::loadByProductStoreId($idProduct, $shopId);
        $productEbayData->setData($productData);
        $productEbayData->product_id = $idProduct;
        $productEbayData->store_id = $shopId;
        $productEbayData->save();

        echo json_encode(array('success' => true, 'message' => 'Data saved'));
    }
}