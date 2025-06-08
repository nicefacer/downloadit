<?php
MLSetting::gi()->add('aPredefinedQuerys',array(
    '<dl><dt>General:</dt><dd>processlist</dd></dl>'=>"SELECT concat ('<a href=\"".MLHttp::gi()->getUrl(array('mp'=>'tools','tools'=>'sql', 'SQL'=>"KILL QUERY"))." ',ID,'"."; \">Kill</a>') as `Kill`, pl.* FROM INFORMATION_SCHEMA.PROCESSLIST pl",
    '<dl><dt>General:</dt><dd>show all magnalister products</dd></dl>'=>'select * from magnalister_products;',
    '<dl><dt>General:</dt><dd>show all magnalister orders</dd></dl>'=>'select * from magnalister_orders;',
    '<dl><dt>General:</dt><dd>count orders per marketplace</dd></dl>'=>'select platform, mpid, count(*) as order_count from magnalister_orders group by platform,mpid;',
    '<dl><dt>Amazon:</dt><dd>show amazon prepare</dd></dl>'=>'select * from magnalister_amazon_prepare;',
));
