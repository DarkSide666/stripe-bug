<?php
require 'init.php';


$list = get_stripe_list('Plan');
$app->add('Header')->set('Plans ('.count($list).')');

$app->add('View')->setelement('pre')->set(print_r($list, true));
