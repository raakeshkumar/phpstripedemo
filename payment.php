<?php

use Stripe\StripeClient;

require_once './vendor/autoload.php';

$stripeSecretKey = "sk_test_51FyMShLQAYB7O7UfG9fuIXSLq7YIX5sMNxDKLsLCLQLOp1ZWfEGHAK29ezdedUiwFEzjhm9fDz43s6xkvfxOEjYb00dPC6WCMY";

$stripe = new StripeClient($stripeSecretKey);

$request = file_get_contents('php://input');

$requestObject = json_decode($request);

$paymentIntent = $stripe->paymentIntents->create([
    'amount' => 100.00,
    'currency' => 'INR'
]);

$clientSecret = $paymentIntent->client_secret;

echo json_encode(['clientSecret' => $clientSecret]);
