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
 * $Id: applicationviews.php 5036 2015-01-09 16:48:09Z masoud.khodaparast $
 *
 * (c) 2010 RedGecko GmbH -- http://www.redgecko.de
 *     Released under the GNU General Public License v2 or later
 * -----------------------------------------------------------------------------
 */

	$productImagesHTML = '';
        $iImages=0;
	if (!empty($data['Images'])) {
                $oImage= MLImage::gi();
		foreach ($data['Images'] as $img => $checked) {
                    $iImages++;
                        try{
                            $aImage=$oImage->resizeImage($img, 'products', 60, 60) ;
                            
			$productImagesHTML .= '
				<table class="imageBox"><tbody>
					<tr><td class="image"><label for="image_'.$img.'">'.'<img width="'.$aImage['width'].'" height="'.$aImage['height'].'" src="'.$aImage['url'].'" alt="'.$aImage['alt'].'"/>'.'</label></td></tr>
					<tr><td class="cb"><input type="checkbox" id="image_'.$img.'" name="'.MLHttp::gi()->parseFormFieldName('Images['.$img.']').'" value="true" '.(($checked == 'true') ? 'checked="checked"' : '').'/></td></tr>
				</tbody></table>';
                        }catch(Exception $oEx){
                            MLMessage::gi()->addNotice($oEx);
                        }
		}
	}
	$charset = (isset($_SESSION['language_charset']) && (stripos($_SESSION['language_charset'], 'utf') !== false)) ? 'UTF-8' : 'ISO-8859-1';
	if (empty($productImagesHTML)) $productImagesHTML = '&nbsp;';
	$html = '
		<tbody>
			<tr class="headline">
				<td colspan="3"><h4>'.ML_LABEL_DETAILS.'</h4></td>
			</tr>
			<tr class="odd">
				<th>'.ML_LABEL_PRODUCT_NAME.' <span>&bull;</span></th>
				<td class="input"><input class="fullwidth" type="text" name="'.MLHttp::gi()->parseFormFieldName('ItemTitle').'" value="'.fixHTMLUTF8Entities($data['ItemTitle'], ENT_QUOTES).'"/></td>
				<td class="info">&nbsp;</td>
			</tr>
			<tr class="even">
				<th>'.ML_GENERIC_MANUFACTURER_NAME.' <span>&bull;</span></th>
				<td class="input"><input class="fullwidth" type="text" name="'.MLHttp::gi()->parseFormFieldName('Manufacturer').'" value="'.fixHTMLUTF8Entities($data['Manufacturer']).'"/></td>
				<td class="info">'.ML_AMAZON_TEXT_APPLY_MANUFACTURER_NAME.'</td>
			</tr>
			<tr class="odd">
				<th>'.ML_LABEL_BRAND.' <span>&bull;</span></th>
				<td class="input"><input class="fullwidth" type="text" name="'.MLHttp::gi()->parseFormFieldName('Brand').'" value="'.fixHTMLUTF8Entities($data['Brand']).'"/></td>
				<td class="info">'.ML_AMAZON_TEXT_APPLY_BRAND.'</td>
			</tr>
			<tr class="even">
				<th>'.ML_GENERIC_MANUFACTURER_PARTNO.'</th>
				<td class="input"><input class="fullwidth" type="text" name="'.MLHttp::gi()->parseFormFieldName('ManufacturerPartNumber').'" value="'.fixHTMLUTF8Entities($data['ManufacturerPartNumber']).'"/></td>
				<td class="info">'.ML_AMAZON_TEXT_APPLY_MANUFACTURER_PARTNO.'</td>
			</tr>
			<tr class="odd">
				<th>'.ML_GENERIC_EAN.' <span>&bull;</span></th>
				<td class="input"><input class="fullwidth" type="text" name="'.MLHttp::gi()->parseFormFieldName('EAN').'" value="'.fixHTMLUTF8Entities($data['EAN']).'"/></td>
				<td class="info">'.ML_AMAZON_TEXT_APPLY_REQUIERD_EAN.'</td>
			</tr>
			<tr class="spacer">
				<td colspan="3">&nbsp;</td>
			</tr>
			<tr class="headline">
				<td colspan="3"><h4>'.ML_AMAZON_LABEL_APPLY_ADDITIONAL_DETAILS.'</h4></td>
			</tr>
			<tr class="odd">
				<th>'.ML_LABEL_PRODUCTS_IMAGES.'</th>
				<td class="input">'.$productImagesHTML.'</td>
				<td class="info">'.ML_AMAZON_TEXT_APPLY_PRODUCTS_IMAGES.'</td>
			</tr>
			<tr class="even">
				<th>'.ML_AMAZON_LABEL_APPLY_BULLETPOINTS.'</th>
				<td class="input">
				    <input type="text" class="fullwidth" name="'.MLHttp::gi()->parseFormFieldName('BulletPoints[0]').'" value="'.fixHTMLUTF8Entities($data['BulletPoints'][0]).'"/><br/>
				    <input type="text" class="fullwidth" name="'.MLHttp::gi()->parseFormFieldName('BulletPoints[1]').'" value="'.fixHTMLUTF8Entities($data['BulletPoints'][1]).'"/><br/>
				    <input type="text" class="fullwidth" name="'.MLHttp::gi()->parseFormFieldName('BulletPoints[2]').'" value="'.fixHTMLUTF8Entities($data['BulletPoints'][2]).'"/><br/>
				    <input type="text" class="fullwidth" name="'.MLHttp::gi()->parseFormFieldName('BulletPoints[3]').'" value="'.fixHTMLUTF8Entities($data['BulletPoints'][3]).'"/><br/>
				    <input type="text"class="fullwidth"  name="'.MLHttp::gi()->parseFormFieldName('BulletPoints[4]').'" value="'.fixHTMLUTF8Entities($data['BulletPoints'][4]).'"/><br/></td>
				<td class="info">'.ML_AMAZON_TEXT_APPLY_BULLETPOINTS.'</td>
			</tr>
			<tr class="odd">
				<th>'.ML_GENERIC_PRODUCTDESCRIPTION.'</th>
				<td class="input"><textarea class="fullwidth" name="'.MLHttp::gi()->parseFormFieldName('Description').'" rows="10">'.
					html_entity_decode(fixHTMLUTF8Entities(
                                                /*amazonSanitizeDesc(*/
                                                $data['Description']
                                                /*)*/
                                                ), ENT_NOQUOTES, $charset).
				'</textarea></td>
				<td class="info">'.ML_AMAZON_TEXT_APPLY_PRODUCTDESCRIPTION.'</td>
			</tr>
			<tr class="even">
				<th>'.ML_AMAZON_LABEL_APPLY_KEYWORDS.'</th>
				<td class="input">
				    <input type="text" class="fullwidth" name="'.MLHttp::gi()->parseFormFieldName('Keywords[0]').'" value="'.fixHTMLUTF8Entities($data['Keywords'][0]).'"/><br/>
				    <input type="text" class="fullwidth" name="'.MLHttp::gi()->parseFormFieldName('Keywords[1]').'" value="'.fixHTMLUTF8Entities($data['Keywords'][1]).'"/><br/>
				    <input type="text" class="fullwidth" name="'.MLHttp::gi()->parseFormFieldName('Keywords[2]').'" value="'.fixHTMLUTF8Entities($data['Keywords'][2]).'"/><br/>
				    <input type="text" class="fullwidth" name="'.MLHttp::gi()->parseFormFieldName('Keywords[3]').'" value="'.fixHTMLUTF8Entities($data['Keywords'][3]).'"/><br/>
				    <input type="text" class="fullwidth" name="'.MLHttp::gi()->parseFormFieldName('Keywords[4]').'" value="'.fixHTMLUTF8Entities($data['Keywords'][4]).'"/><br/></td>
				<td class="info">'.ML_AMAZON_TEXT_APPLY_KEYWORDS.'</td>
			</tr>
			<tr class="spacer">
				<td colspan="3">&nbsp;</td>
			</tr>
		</tbody>';
	return $html;