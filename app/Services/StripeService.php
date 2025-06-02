<?php

namespace App\Services;

use Stripe\Stripe;
use Stripe\Customer;
use Stripe\Subscription;
use Stripe\Exception\ApiErrorException;

class StripeService
{
    public function __construct()
    {


        Stripe::setApiKey(env('STRIPE_SECRET'));
    }

    public function createCustomer($user)
    {
        return Customer::create([
            'email' => $user->email,
            'name' => $user->name,
        ]);
    }

    public function createSubscription($customerId, $priceId, $paymentMethod)
    {
        try {
            return Subscription::create([
                'customer' => $customerId,
                'items' => [['price' => $priceId]],
                'default_payment_method' => $paymentMethod,
            ]);
        } catch (ApiErrorException $e) {
            throw new \Exception($e->getMessage());
        }
    }
}
