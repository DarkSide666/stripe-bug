<?php
require 'init.php';

$list = get_stripe_list('Product');
$app->add('Header')->set('Products ('.count($list).')');

$app->add('View')->setelement('pre')->set(print_r($list, true));

