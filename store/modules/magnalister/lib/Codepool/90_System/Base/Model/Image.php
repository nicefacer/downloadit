<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Image
 *
 * @author mba
 */
class ML_Base_Model_Image {
    public function __construct() {
    }
    public function resizeImage($sSrc, $sType, $iMaxWidth, $iMaxHeight, $blUrlOnly = false) {
        $sType = strtolower($sType) ;
        $this->checkDirectory(MLHttp::gi()->getImagePath(''), array($sType, $iMaxWidth . ( $iMaxWidth === $iMaxHeight ? '' : 'x' . $iMaxHeight ).'px'));
        $sDst = $this->getDestinationPath($sSrc, $sType, $iMaxWidth, $iMaxHeight);
        $oTable = MLDatabase::getTableInstance('image')
            ->init()
            ->set('sourcePath', $sSrc)
            ->set('destinationPath', MLHttp::gi()->getImagePath($sDst))
            ->load();
        ;
        if ($oTable->get('skipCheck') == 0 && !$oTable->destinationIsActual()) {// generate
            if (@getimagesize($sSrc)) {//@getimagesize($sSrc) used because file_existed cannot recognize image url which like http://www.usersite.com/image.jpg
                // some fatal error is not displayed to the customer , so regenerate it with own request to shop controller
                // eg. $this->callNotExistingMethod();
                MLCache::gi()->set('Model_Image__BrokenImageResize', array(
                    'sSrc' => $oTable->get('sourcePath'), 
                    'sDst' => $oTable->get('destinationPath'), 
                    'iMaxWidth' => $iMaxWidth, 
                    'iMaxHeight' => $iMaxHeight
                ));
                $this->resize($sSrc, $iMaxWidth, $iMaxHeight, MLHttp::gi()->getImagePath($sDst));
                MLCache::gi()->delete('Model_Image__BrokenImageResize');
                $oTable->destinationIsActual(true);
            } else { // delete
                $oTable->delete();
                MLMessage::gi()->addDebug('Image doesn\'t exist :' . $sSrc);
                throw new Exception;
            }
        } elseif ($oTable->get('skipCheck') == 1) { // setToActual
            $oTable->destinationIsActual(true);
        }
        if ($blUrlOnly) {
            return MLHttp::gi()->getImageUrl($sDst);
        } else {
            list($iWidth, $iHeight) = @getimagesize(MLHttp::gi()->getImagePath($sDst));
            return array(
                'url' => MLHttp::gi()->getImageUrl($sDst),
                'width' => $iWidth,
                'height' => $iHeight,
                'alt' => basename($sSrc)
            );
        }
    }
    
    protected function checkDirectory($sMainDir , $aDirs) {
        $sPath = $sMainDir;
        foreach ($aDirs as $sDir) {            
            $sPath .= $sDir.DIRECTORY_SEPARATOR;
            if(!file_exists($sPath)){
                mkdir($sPath);
            }
        }        
    }
    
    protected function resize($sSrc, $iMaxWidth, $iMaxHeight, $sDst, $iCompression=80){
        if (file_exists($sDst)) {
            unlink($sDst);
        }
        $src = array();
        $dst = array();
        $dimensions = getimagesize($sSrc);
        if (is_array($dimensions)) {
            $src['w'] = $dimensions[0];
            $src['h'] = $dimensions[1];
            $src['type'] = $dimensions[2];

            if ($iMaxWidth == '0') {
               $iMaxWidth = ($src['w'] / ($src['h'] / $iMaxHeight));
            }

            $thiso = ($src['w'] / $iMaxWidth);
            $thisp = ($src['h'] / $iMaxHeight);
            $dst['w'] = ($thiso > $thisp) ? $iMaxWidth : round($src['w'] / $thisp); // width
            $dst['h'] = ($thiso > $thisp) ? round($src['h'] / $thiso) : $iMaxHeight; // height
        }
        $src['image'] = @imagecreatefromstring(@file_get_contents($sSrc));

        if (!is_resource($src['image'])) {
            unset($src);
            unset($dst);            
            return false;
        }
        
        $success = true;
        if (function_exists('imagecreatetruecolor')) {
            $dst['image'] = imagecreatetruecolor($dst['w'], $dst['h']); // created thumbnail reference GD2
        } else {
            $dst['image'] = imagecreate($dst['w'], $dst['h']); // created thumbnail reference GD1
        }
        //use white background when png image has transparent background
        $white = imagecolorallocate($dst['image'], 255, 255, 255);
        imagefilledrectangle($dst['image'], 0, 0, $dst['w'], $dst['h'], $white);
        
        if (imagecopyresampled($dst['image'], $src['image'], 0, 0, 0, 0, $dst['w'], $dst['h'], $src['w'], $src['h'])) {
            $success = @imagejpeg($dst['image'], $sDst, $iCompression);
        } else {
            $success = false;
        }
        imagedestroy($src['image']);
        imagedestroy($dst['image']);

        unset($src);
        unset($dst);
        return $success;
    }
    
    public function getFallbackUrl($sSrc , $sDst , $iX , $iY) {
        throw new Exception('Not implemented.', 1449231812);
    }
    
    public function getDestinationPath($sSrc, $sType, $iMaxWidth, $iMaxHeight) {
        $sFileName = str_replace(array("<", ">", ":", '"', "/", "\\", "|", "?", "*") ,'', pathinfo($sSrc, PATHINFO_BASENAME));
        $sDst = $sType.'/'.$iMaxWidth.($iMaxWidth === $iMaxHeight ? '' : 'x'.$iMaxHeight ).'px/'.$sFileName;
        return $sDst;
    }
    
}
