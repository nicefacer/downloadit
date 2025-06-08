<?php

 MLFilesystem::gi()->loadClass('Core_Controller_Abstract') ;

 class ML_Magnalister_Controller_Main_Content_Changelog extends ML_Core_Controller_Abstract {
     public function getChangeLog() {
        $sChangeLog = file_get_contents(MLFilesystem::getLibPath() . 'ChangeLog');
        $sChangeLog = str_replace(array("\r\n", "\r"), "\n", $sChangeLog);
        $sChangeLog = fixHTMLUTF8Entities($sChangeLog);
//        $sHeader = substr($sChangeLog, 0, strpos($sChangeLog, '*/') + 2);
        $sChangeLog = substr($sChangeLog, strpos($sChangeLog, '*/') + 2);
        $sChangeLog = preg_replace('/(=+)\s(.*)\s(=+)/e', "'<h'.strlen('\\1').'>'.'$2'.'</h'.strlen('\\1').'>'", $sChangeLog);
        $sChangeLog = preg_replace('/\*\s(.*)/', '<ul><li>$1</li></ul>', $sChangeLog);
        $sChangeLog = preg_replace("/<\/li><\/ul>(\s*)<ul><li>/s", "</li>$1<li>", $sChangeLog);
        return $sChangeLog;
    }
 }

 