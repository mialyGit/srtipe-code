<?php

require_once '../vendor/autoload.php';
    
define('STRIPE_SECRET','sk_test_51MeI8rBZdY37cyhHzMJLxuecAiU0tPgQO6otj42LxGE3PUvOKQr6uiaUtOTiz59tkgnXfTbZKTL2ERsedzwV6nbf00Qq3HXPy1');
//define('STRIPE_SECRET','sk_test_51MTLqRFK5DNI6OOHENPvSsITOLDVNpQyQgx3JqLXLvM0wKh5LQTchgMKfmMkfSqtUpJJmjpI1aarKPvNlSluepHC00Bpudbp0U');
$payment = new App\StripePayement(STRIPE_SECRET);

$payment->startPayement();


?>