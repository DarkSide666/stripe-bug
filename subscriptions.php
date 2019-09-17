<?php
require 'init.php';

$list = get_stripe_list('Subscription');
$app->add('Header')->set('Subscriptions ('.count($list).')');

$app->add('View')->setelement('pre')->set(print_r($list, true));

