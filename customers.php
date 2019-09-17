<?php
require 'init.php';


$list = get_stripe_list('Customer');
$app->add('Header')->set('Customers ('.count($list).')');

$app->add('View')->setelement('pre')->set(print_r($list, true));
