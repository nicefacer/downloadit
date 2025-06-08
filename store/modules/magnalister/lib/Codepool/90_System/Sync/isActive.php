<?php
return count(array_intersect(
    explode(',', strtolower(MLRequest::gi()->data('do'))), 
    array(
        'importorders',
        'syncorderstatus',
        'syncinventory',
        'syncproductidentifiers',
        'update',
        'updateorders',
    )
)) > 0;