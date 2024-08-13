<?php

use Stripe\Exception\SignatureVerificationException;
use Stripe\Service\Sigma\SigmaServiceFactory;
use Stripe\StripeClient;
use Stripe\Webhook;

require_once './vendor/autoload.php';

$stripe = new StripeClient("sk_test_51FyMShLQAYB7O7UfG9fuIXSLq7YIX5sMNxDKLsLCLQLOp1ZWfEGHAK29ezdedUiwFEzjhm9fDz43s6xkvfxOEjYb00dPC6WCMY");
$endpointSecret = "whsec_f7zRKAbBhpKjv6VBChLeNAdMLHcYzI78";

$payload = @file_get_contents('php://input');
$signatureHeader = $_SERVER['HTTP_STRIPE_SIGNATURE'];

$server = "localhost";
$username = "admin";
$password = "redhat123";
$dbName = "phpstripedemo";

try {
    $event = Webhook::constructEvent(
        $payload,
        $signatureHeader,
        $endpointSecret
    );
} catch (UnexpectedValueException $e) {
    http_response_code(400);
    echo $e->getMessage();
} catch (SignatureVerificationException $e) {
    http_response_code(400);
    echo "Signature Mismatched";
}

try {
    $conn = new PDO("mysql:host=$server;dbname=$dbName", $username, $password);
} catch (PDOException $e) {
    echo $e->getMessage();
}

switch ($event->type) {
    case 'payment_intent.succeeded':
        $paymentInformation = $event->data->object;
        $sql = "insert into payments (event_data) values('$paymentInformation')";
        $conn->exec($sql);
        break;

    case 'payment_intent.payment_failed':
        $paymentInformation = $event->data->object;
        $sql = "insert into payments (event_data) values('$paymentInformation')";
        $conn->exec($sql);
        break;

    default:
        echo "Unknown status found" . $event->type;
}
