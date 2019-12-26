<?php
require_once('vendor/autoload.php');
require_once('config/db.php');
require_once('lib/pdo_db.php');
require_once('models/Customer.php');
require_once('models/Transaction.php');


\Stripe\Stripe::setApiKey('sk_test_Mz8cslD3LCRE4cmZpUKXPI1d00q1un0MwM');

//sanitize the input

$POST = filter_var_array($_POST, FILTER_SANITIZE_STRING); 

$firstname = $POST['firstname'];
$lastname = $POST['lastname']; 
$email = $POST['email']; 
$token = $POST['stripeToken']; 

//create customer in stripe

$customer = \Stripe\Customer::create(array(
    "email"=>$email,
    "source"=>$token
));

//charge the customer

$charge = \Stripe\Charge::create(array(
    "amount"=>5000,
    "currency"=>"usd",
    "description"=> "payment for food ordered",
    "customer"=> $customer->id
));

// print_r($charge);

//Customer Data
$customerData = [

    'id'=>$charge->customer,
    'firstname'=> $firstname,
    'lastname'=> $lastname,
    'email'=> $email,
    
    ];
 
    
    // Transaction Data
    $transactionData = [
        'id' => $charge->id,
        'customer_id' => $charge->customer,
        'product' => $charge->description,
        'amount' => $charge->amount,
        'currency' => $charge->currency,
        'status' => $charge->status,
      ];

  
    //instatiate Customer
    
    $customer = new Customer();
    $transaction = new Transaction();
    
    //Add Customer Method
    $customer->addCustomer($customerData);

    $transaction->addTransaction($transactionData);
    
    //redirect to a success page
    
    header("Location: success.php?tid=$charge->id &product= $charge->description")

?>