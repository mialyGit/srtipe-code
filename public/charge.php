<?php
require_once '../vendor/autoload.php';
    
define('STRIPE_SECRET','sk_test_51MeI8rBZdY37cyhHzMJLxuecAiU0tPgQO6otj42LxGE3PUvOKQr6uiaUtOTiz59tkgnXfTbZKTL2ERsedzwV6nbf00Qq3HXPy1');

$stripe = new \Stripe\StripeClient(
    [
        "api_key" => STRIPE_SECRET,
        "stripe_version" => "2022-11-15"
    ]
);

$token = $stripe->tokens->create([
    'card' => [
      'number' => '4000051240000005',
      'exp_month' => 3,
      'exp_year' => 2024,
      'cvc' => '123'
    ],
]);

$accountId = 'acct_1MhSy5B0IcF9UdBe';

$account = $stripe->accounts->retrieve(
    $accountId
,[]);

$card_token = $account->external_accounts->create(
    [
        'external_account' => $token->id
    ]
);

die($card_token);