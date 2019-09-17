<?php
require 'init.php';
$plan_id = 'plan_FfYdrvSQiTgYzn'; // daily test plan without trial
$tax_id = 'txr_1EothbFnyWIayXxZPd8s0CaF'; // 23% VAT

// include Stripe.js
$app->requireJS('https://js.stripe.com/v3/');


$app->add('Header')->set('Testing subscribe workflow #2');

try {

    if (isset($_GET['payment_method'])) {

        // create customer
        $rnd = rand(1,1000);
        $customer = \Stripe\Customer::create([
            'name' => 'Stripe Test #'.$rnd,
            'email' => 'test-'.$rnd.'@example.com',
            //'payment_method' => $_GET['payment_method'],
        ]);

        // show customer data in UI
        $app->add('View')->setElement('pre')->set(print_r($customer, true));

        // attach payment method to existing customer and set it as default method
        $payment_method = \Stripe\PaymentMethod::retrieve($_GET['payment_method']);
        $payment_method->attach(['customer' => $customer->id]);
        $customer->invoice_settings['default_payment_method'] = $payment_method->id;
        $customer->save();
        
        // show payment method and customer data in UI
        $app->add('View')->setElement('pre')->set(print_r($payment_method, true));
        $app->add('View')->setElement('pre')->set(print_r($customer, true));

        // create subscription
        $subscription = \Stripe\Subscription::create([
            'customer' => $customer->id,
            'items' => [
                [
                    'plan' => $plan_id,
                    'quantity' => 1,
                ],
            ],
            'default_tax_rates' => [$tax_id],
            'off_session' => true,
            // setting this probably is not required because subscription should use customers default payment method anyway
            'default_payment_method' => $customer->invoice_settings['default_payment_method'],
            //'trial_period_days' => 1,
        ]);

        // show subscription data
        $app->add('View')->setElement('pre')->set(print_r($subscription, true));

    } else {

        $setup_intent = \Stripe\SetupIntent::create([
          'usage' => 'off_session', // The default usage is off_session
        ]);

        $app->add(['Text', 'content' => '
        <input id="cardholder-name" type="text" placeholder="Cardholder Name">
        <!-- placeholder for Elements -->
        <div id="card-element"></div>
        <button id="card-button" data-secret="'.$setup_intent->client_secret.'">Save Card</button>

        <script>
        var stripe = Stripe("'.$stripe['public_key'].'");
        var elements = stripe.elements();
        var cardElement = elements.create("card");
        cardElement.mount("#card-element");

        var cardholderName = document.getElementById("cardholder-name");
        var cardButton = document.getElementById("card-button");
        var clientSecret = cardButton.dataset.secret;

        cardButton.addEventListener("click", function(ev) {
          stripe.handleCardSetup(
            clientSecret, cardElement, {
              payment_method_data: {
                billing_details: {name: cardholderName.value}
              }
            }
          ).then(function(result) {
            console.log(result);
            
            if (result.error) {
              // Display error.message in your UI.
              alert(result.error.message);
            } else {
              // The setup has succeeded. Display a success message.
              //alert("success");
              
              // now make local API call to store this payment method for particular customer and actually create customer and subscription
              document.location="?payment_method="+result.setupIntent.payment_method;
            }
          });
        });

        </script>
        ']);
    }

} catch (\Stripe\Exception\ExceptionInterface $e) {
    throw new Exception((string) $e);
}

