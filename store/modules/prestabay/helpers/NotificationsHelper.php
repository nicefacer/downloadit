<?php

/**
 * File NotificationsHelper.php
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
class NotificationsHelper
{

    public static function receiveLatestApiNotifications()
    {
        $notificationModel = new NotificationsModel();

        $latestDate = $notificationModel->getLatestNotificationDate();

        ApiModel::getInstance()->reset();
        $notificationsResponse = ApiModel::getInstance()->api->notifications->receive(array(
            'latestDate' => $latestDate ? $latestDate : '0000-00-00 00:00:00',
        ))->post();

        if (!isset($notificationsResponse['success']) || !$notificationsResponse['success']) {
            return false;
        }

        $notificationsList = $notificationsResponse['messages'];
        if (count($notificationsList) > 0) {
            $notificationModel->updateNotificationList($notificationsList);
        }
    }

    public static function getFirstUnread()
    {
        $notificationModel = new NotificationsModel();
        $unreadNotification = $notificationModel->getFirstUnread();
        if (!isset($unreadNotification['id'])) {
            return false;
        }

        if (CoreHelper::isPS16()) {
            switch ($unreadNotification['level']) {
                case NotificationsModel::LEVEL_NOTICE:
                default:
                    $classOutput = 'info';
                    break;
                case NotificationsModel::LEVEL_ERROR:
                    $classOutput = 'danger';
                    break;
                case NotificationsModel::LEVEL_WARNING:
                    $classOutput = 'warning';
                    break;
            }
            $unreadNotification['class'] = $classOutput;
        } else {
            switch ($unreadNotification['level']) {
                case NotificationsModel::LEVEL_NOTICE:
                default:
                    $classOutput = 'conf';
                    break;
                case NotificationsModel::LEVEL_ERROR:
                    $classOutput = 'error';
                    break;
                case NotificationsModel::LEVEL_WARNING:
                    $classOutput = 'warn';
                    break;
            }
            $unreadNotification['class'] = $classOutput;
        }

        return $unreadNotification;
    }
}