<?php class_exists('ML', false) or die();?>
<?php foreach($aField['values'] as $sOptionKey=>$aImage){?>
                <td class="image">
                    <label for="<?php echo $aField['id']?>_<?php echo $sOptionKey ?>">
                        <?php if(is_array($aImage)){?>
                            <img height="<?php echo $aImage['height'] ?>" width="<?php echo $aImage['width']?>" alt="<?php echo $aImage['alt']?>" src="<?php echo $aImage['url'] ?>" />
                        <?php }else{ ?>
                            <div style="padding:.5em"><?php echo $aImage;?></div>
                        <?php }?>
                    </label>
                </td>
            <?php } ?>