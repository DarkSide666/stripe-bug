<?php
error_reporting(E_ALL | E_ALL);
require 'vendor/autoload.php';
require 'config.php';

// App
$app = new \atk4\ui\App();
$app->initLayout('Admin');

// Menu
$menu = $app->layout->menuLeft;
$menu->addItem('Dashboard', ['index']);
$menu->addItem('Products', ['products']);
$menu->addItem('Plans', ['plans']);
$menu->addItem('Customers', ['customers']);
$menu->addItem('Subscriptions', ['subscriptions']);
$menu->addItem('Test 1', ['test1']);



/**
 * Returns all list data from Stripe for particular objects.
 *
 * @param string $object Product, Plan, Charge etc.
 * @param array  $query
 * @param bool   $reverse
 *
 * @return array
 */
function get_stripe_list($object = 'Product', $query = [], $reverse = false)
{
    // request data from Stripe in portions
    $limit = 100; // how many objects to request in one shot (page)
    $last_id = null;
    $data = [];
    do {
        $query = array_merge($query, [
            'limit' => $limit,
        ]);
        if ($last_id) {
            $query['starting_after'] = $last_id;
        }

        $class = '\\Stripe\\' . $object;
        $rows = $class::all($query);

        $data = array_merge($data, $rows->data);
    } while ($rows->has_more && $last_id = $rows->data[count($rows->data)-1]->id);

    // reversing because Stripe gives newest records first, but we need oldest first
    if ($reverse) {
        $data = array_reverse($data);
    }

    return $data;
}
