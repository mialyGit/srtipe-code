<?php 

namespace App;

use Stripe\Stripe;

class StripePayement {
    
  
    public function __construct(private string $clientSecret) {
        Stripe::setApiKey($this->clientSecret);
        Stripe::setApiVersion('2022-11-15');
    }

    public function startPayement(){
        $amount = 40000; // Argent payé par le médecin
        $application_fee_amount = 3000; // Argent reçue pour l'admin
        $customerFilter = 'email'; // Rechercher un medecin par email
        $accountFilter = 'email'; // Rechercher un lecteur par email

        // Données pour la création d'un medecin
        $medecin = [
            'name' => 'RABE Zandry Gasy',
            'email' => 'rabe@example.com',
            'source' => [
                "object" => "card",
                "number" => "4000002500003155",
                "exp_month" => 10,
                "exp_year" => 2021,
                "cvc" => "123"
            ]
        ];

        // Données pour la création d'un lecteur
        $lecteur = [
            'type' => 'standard',
            'country' => 'fr',
            'email' => 'mialisonavotrina@example.com',
            'external_account' => [
                'object' => 'bank_account',
                'country' => 'FR',
                'currency' => 'eur',
                'account_number' => "FR1420041010050500013M02606",
            ],
            'settings' => [
                'payouts' => [
                'schedule' => [
                    'interval' => 'monthly',
                    'monthly_anchor' => 25
                    ],
                ],
            ],
        ];

        // Rechecher si le medecin existe déjà dans Stripe
        $customers = $this->customerExist($customerFilter, $medecin[$customerFilter]);
        if($customers->count() > 0){
            $customer = $customers->data[0];
        } else {
            $customer = $this->createCustomer($medecin);
            if(isset($customer['error'])){
                die($customer['error']);
            }
        };

        // Rechecher si le lecteur existe déjà dans Stripe
        $accounts = $this->accountExist($accountFilter, $lecteur[$accountFilter]);
        if(empty($accounts)){
            $account = $this->createAccount($lecteur);
            if(isset($account['error'])){
                die($account['error']);
            }
        } else $account = $accounts[0];

        // Créer un session de payement pour le medecin
        $session = \Stripe\Checkout\Session::create([
            'customer' => $customer->id,
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
                'medecinId' => $customer->id,
                'lecteurId' => $account->id,
                'amount' => $amount,
                'application_amout' => $application_fee_amount
            ],
            'payment_intent_data' => [
                'application_fee_amount' => $application_fee_amount,
                'transfer_data' => [
                    'destination' => $account->id
                ],
            ],
        ]);

        header('HTTP/1.1 303 See Other');
        header('Location: '. $session->url);
    }

    private function createCustomer($array){
        try {
            $result = \Stripe\Customer::create($array);
        } catch (\Stripe\Exception\ApiConnectionException $e) {
            // Network communication with Stripe failed
            $result['error'] = 'La communication réseau avec Stripe a échoué: ' . $e->getMessage();
        } catch (\Stripe\Exception\AuthenticationException $e) {
            // Authentication with Stripe API failed
            $result['error'] = 'L\'authentification avec l\'API Stripe a échoué: ' . $e->getMessage();
        } catch (\Stripe\Exception\ApiErrorException $e) {
            // Display error message to user
            $result['error'] = 'Erreur lors de la création du client: ' . $e->getError()->message;
        } catch (\Stripe\Exception\InvalidRequestException $e) {
            // Invalid request to Stripe API
            $result['error'] = 'Requête invalide à l\'API Stripe: ' . $e->getMessage();
        }
         catch (\Stripe\Exception\CardException $e) {
            // Display error message to user
            $result['error'] = 'Erreur de la carte : ' . $e->getError()->message;
        }
        return $result;
    }

    private function createAccount($array){
        try {
            $result = \Stripe\Account::create($array);
        } catch (\Stripe\Exception\ApiConnectionException $e) {
            // Network communication with Stripe failed
            $result['error'] = 'La communication réseau avec Stripe a échoué: ' . $e->getMessage();
        } catch (\Stripe\Exception\AuthenticationException $e) {
            // Authentication with Stripe API failed
            $result['error'] = 'L\'authentification avec l\'API Stripe a échoué: ' . $e->getMessage();
        } catch (\Stripe\Exception\ApiErrorException $e) {
            // Display error message to user
            $result['error'] = 'Erreur lors de la création du client: ' . $e->getError()->message;
        } catch (\Stripe\Exception\InvalidRequestException $e) {
            // Invalid request to Stripe API
            $result['error'] = 'Requête invalide à l\'API Stripe: ' . $e->getMessage();
        } catch (\Stripe\Exception\CardException $e) {
            // Display error message to user
            $result['error'] = 'Erreur de la carte : ' . $e->getError()->message;
        } catch (\Stripe\Exception\RateLimitException $e) {
            // Handle any rate limit errors that may occur
            $result['error'] = 'Erreur de limite de débit : ' . $e->getMessage();
        }
        return $result;
    }

    public function customerExist($checkedKey, $checkedValue){
        return \Stripe\Customer::all([
            $checkedKey => $checkedValue,
            'limit' => 1
        ]);
    }

    public function accountExist($checkedKey, $checkedValue){
        $accounts = \Stripe\Account::all();
        return array_filter($accounts->data, function($account) use ($checkedKey, $checkedValue) {
            return $account[$checkedKey] == $checkedValue;
        });
    }

}