<?php

$stripe = array(
    'secret_key' => 'sk_test_***',
    'public_key' => 'pk_test_***',
);

\Stripe\Stripe::setApiKey($stripe['secret_key']);
\Stripe\Stripe::setApiVersion('2019-09-09');
\Stripe\Stripe::setAppInfo("SortMyBooksOnline", '2.6.31', 'https://sortmybooksonline.com'); // optional
?>
