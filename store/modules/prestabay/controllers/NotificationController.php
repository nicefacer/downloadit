<?php

/**
 * File NotificationController.php
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * It is available through the world-wide-web at this URL:
 * http://involic.com/license.txt
 * If you are unable to obtain it through the world-wide-web,
 * please send an email to license@involic.com so
 * we can send you a copy immediately.
 *
 * eBay Listener Itegration with PrestaShop e-commerce platform.
 * Adding possibilty list PrestaShop Product dirrectly to eBay.
 *
 * @author      Involic <contacts@involic.com>
 * @copyright   Copyright (c) 2011-2015 by Involic (http://www.involic.com)
 * @license     http://involic.com/license.txt
 */
class NotificationController extends BaseAdminController
{

    public function indexAction()
    {
        NotificationsHelper::receiveLatestApiNotifications();
        
        $notificationGrid = new Grids_Notifications();
        $notificationGrid->getHtml();
    }

    public function viewAction()
    {
        $notificationId = UrlHelper::getGet('id', false);
        $model = new NotificationsModel($notificationId);
        if (!$model->id) {
            throw new Exception('Invalid notification ID');
        }

        $model->is_read = NotificationsModel::READ_YES;
        $model->save();

        $this->view("notification/view.phtml", array("model" => $model));
    }

    public function markAsReadAction()
    {
        RenderHelper::setJSONHeader();
        RenderHelper::cleanOutput();

        $notificationId = UrlHelper::getPost('id', false);
        if (!$notificationId) {
            echo json_encode(array('success' => false));
            return;
        }
        $notificationModel = new NotificationsModel($notificationId);
        if (!$notificationModel) {
            echo json_encode(array('success' => false));
            return;
        }

        $notificationModel->is_read = NotificationsModel::READ_YES;
        $notificationModel->save();

        echo json_encode(array('success' => true));
        return;
    }

    public function markAsReadGridAction()
    {
        $notificationId = UrlHelper::getGet('id', false);
        if (!$notificationId) {
            RenderHelper::addError('Not found');
            UrlHelper::redirect('notification/index');
        }
        $notificationModel = new NotificationsModel($notificationId);
        if (!$notificationModel) {
            RenderHelper::addError('Not found');
            UrlHelper::redirect('notification/index');
        }

        $notificationModel->is_read = NotificationsModel::READ_YES;
        $notificationModel->save();

        RenderHelper::addSuccess('Notification marked as Read');
        UrlHelper::redirect('notification/index');
    }
}
