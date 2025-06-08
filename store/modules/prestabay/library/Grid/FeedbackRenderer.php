<?php

/**
 * File FeedbackRenderer.php
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
 * eBay Listing Import.
 * Adding possibility import all eBay Listing to PrestaShop
 *
 * @author      Involic <contacts@involic.com>
 * @copyright   Copyright (c) 2011-2012 by Involic (http://www.involic.com)
 * @license     http://involic.com/license.txt
 */
class Grid_FeedbackRenderer extends Grid_TextRenderer
{

    public function render($fieldKey = null, $row = null, $config = array(), $grid = null)
    {
        $row['buyer_comment'];
        $row['seller_comment'];

        $renderedContent = "<strong>".L::t("Buyer") . ":</strong> ";
        if ($row['buyer_comment'] != "") {
            $renderedContent .= $this->addFeedbackWrapBasedOnType($row['buyer_type'], $row['buyer_comment']);
        } else {
            $renderedContent .= "N/A";
        }
        $renderedContent .= "<br/>" . "<strong>". L::t("Seller") . ":</strong> ";

        if (empty($row['seller_comment'])) {
            $renderedContent .= "<a href class='leaveFeedback'>". L::t("Leave feedback") . "</a>";
        } else {
            $renderedContent .= $this->addFeedbackWrapBasedOnType($row['seller_type'], $row['seller_comment']);
        }

        return $renderedContent;
    }

    protected function addFeedbackWrapBasedOnType($type, $comment)
    {
        $color = "";
        switch ($type) {
            case "Positive":
                $color = "color: green;";
                break;
            case "Negative":
                $color = "color: red;";
                break;
        }
        return "<span style='{$color}'>{$comment} <strong>[".L::t($type)."]</strong></span>";
    }

}