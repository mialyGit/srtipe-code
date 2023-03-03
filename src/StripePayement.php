<?php 

namespace App;

use Stripe\Stripe;

class StripePayement {
    
  
    public function __construct(private string $clientSecret) {
        Stripe::setApiKey($this->clientSecret);
        Stripe::setApiVersion('2022-11-15');
    }

    public function startPayement($amount){

        //Create if not exist in base
        $medecin = \Stripe\Customer::create([
            'name' => 'AVOTRINIAINA Mialison',
            'email' => 'mialison@example.com',
            'source' => 'tok_visa'
        ]);
        
        $medecinId = $medecin->id;

        $lecteur = \Stripe\Account::create([
            'type' => 'standard',
            'country' => 'fr',
            'email' => 'kouli02@example.com',
            'external_account' => [
                'object' => 'bank_account',
                'country' => 'FR',
                'currency' => 'eur',
                'account_number' => "FR1420041010050500013M02606",
            ],
            'settings' => [
                'payouts' => [
                'schedule' => [
                    'interval' => 'weekly',
                    'weekly_anchor' => 'tuesday'
                    ],
                ],
            ],
        ]);

        $lecteurId = $lecteur->id;

        $session = \Stripe\Checkout\Session::create([
            'customer' => $medecinId,
            'payment_method_types' => ['card'],
            'line_items' => [
                [
                    'quantity' => 1,
                    'price_data' => [
                        'currency' => 'EUR',
                        'product_data'=> [
                            'name' => 'Facture'
                        ],
                        'unit_amount' => $amount
                    ]
                ]
            ],
            'mode' => 'payment',
            'success_url' => 'http://localhost:8000/success.php',
            'cancel_url' => 'http://localhost:8000/error.php',
            'metadata' => [
                'cartId' => '1'
            ],
            'payment_intent_data' => [
                'application_fee_amount' => 300,
                'transfer_data' => [
                    'destination' => $lecteurId
                ],
            ],
        ]);

        header('HTTP/1.1 303 See Other');
        header('Location: '. $session->url);
    }

}