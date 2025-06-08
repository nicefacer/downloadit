<?php
    class_exists('ML',false) or die();
    ob_start();
    $totaltime= 0;
    $tpR = MLDatabase::getDbInstance()->getTimePerQuery();
    $tpR = is_array($tpR)?$tpR:array();
//    $tpR2 = MLDatabase::getDbInstance()->getTimePerQuery();
//    $tpR2 = is_array($tpR2)?$tpR2:array();
//    $tpR=array_merge($tpR, $tpR2);
    if(!empty($tpR)){
        ?>
            <table style="width:100%">
                <tr><th>Time</th><th>Query</th></tr>
                <?php foreach ($tpR as $item) {?>
                    <tr>
                        <td<?php echo $item['error'] ? ' class="error"' : '' ?>><?php echo microtime2human($item['time']) ?></td>
                        <td><textarea style="width:100%;"><?php echo trim(htmlentities($item['query'],ENT_COMPAT , 'UTF-8')); ?></textarea><?php echo $item['error'] ? $item['error'] : ''; ?></td>
                    </tr>
                    <?php $totaltime +=$item['time']; ?>
                <?php } ?>
            </table>
        <?php
    }
    $sContent=  ob_get_contents();
    ob_end_clean();
    echo "Total query execution time :<b> ".microtime2human($totaltime).'</b><br /><br />'.$sContent;
?>