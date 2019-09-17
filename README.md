# Possible Stripe bug

# How to install

- clone this repository
- run `composer update` from command line
- copy file `config-distrib.php` to `config.php` and fill in proper Stripe test keys

# How to test

**So far so good**

- open it in browser `http://localhost/stripe-bug` for example
- open file `test1.php` and in first lines of it fill in your Stripe Plan ID and Tax rate ID which you will use for testing
- go to page `test1`
- fill in any card holder name and credit card number 4000002500003155 which as stated in https://stripe.com/docs/payments/cards/saving-cards-after-payment#testing should
  > This test card requires authentication for one-time payments. However, if you set up this card using the Setup Intents API and use the saved card for subsequent payments, no further authentication is needed.
- click Save Card and accept 3DS procedure
- it will then reload page and create customer and subscription in Stripe
- open Stripe dashboard and see that first payment is successful

**Now try next payment**

To force next payment without waiting for next subscription period you have to change `Subscription->billing_cycle_anchor`. This can be done by CURL or by included php console:
- in commandline run `php console.php`
- now in console load Subscription: `$sub = \Stripe\Subscription::retrieve('*subscription_id_here*');`
- update it `$sub->billing_cycle_anchor='now';` `$sub->prorate=false;` `$sub->save();`
- see in Stripe dashboard that next invoice was created, but payment is not successfull because it _requires user interaction_.

It **shouldn't require that** because first payment was successfull and card shouldn't require authorization on each transaction.
This looks like a bug for me.

**But API allows to pay this invoice without 3DS**

But we still can pay this same invoice by using API methods without any _user interaction_.

- in commandline run `php console.php`
- now in console load Subscription: `$sub = \Stripe\Subscription::retrieve('*subscription_id_here*');`
- load last invoice `$inv = \Stripe\Invoice::retrieve($sub->last_invoice);`
- pay last invoice `$inv->pay();`
- see in Stripe dashboard that now invoice was successfully paid without any need for _user interaction_ or 3DS.
