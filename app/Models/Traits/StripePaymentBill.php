<?php


namespace App\Models\Traits;


use Illuminate\Support\Facades\Log;
use Stripe\Account;
use Stripe\Balance;
use Stripe\Card;
use Stripe\Charge;
use Stripe\Customer;
use Stripe\Exception\ApiErrorException;
use Stripe\PaymentIntent;
use Stripe\PaymentMethod;
use Stripe\Payout;
use Stripe\Stripe;
use Stripe\Subscription as StripeSubscription;
use Stripe\Subscription;
use Stripe\Token;
use Stripe\Transfer;

trait StripePaymentBill
{
    /**
     * StripePaymentBill
     * phone is required
     * stripe_customer_id is required
     */

    public function isStripeCustomer()
    {
        return !empty($this->stripe_customer_id);
    }

    public function createAsStripeCustomer($option = [])
    {
        try {
            if ($this->isStripeCustomer()) {
                throw new \Exception('Is already a Stripe customer');
            }
            $customer = Customer::create($option);
            $this->stripe_customer_id = $customer->id;
            $this->save();
            return $customer;
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return null;
        }
    }

    public function validateCardInfo($card)
    {
        if (!array_key_exists('number', $card)) {
            return false;
        }
        if (!array_key_exists('exp_month', $card)) {
            return false;
        }
        if (!array_key_exists('exp_year', $card)) {
            return false;
        }
        if (!array_key_exists('cvc', $card)) {
            return false;
        }
        if (!array_key_exists('card_holder_name', $card)) {
            return false;
        }
        return true;
    }

    public function addPaymentMethod($data)
    {
        try {
            if (!$this->validateCardInfo($data)) {
                throw new \Exception('Invalid card');
            }

            try {
                /**
                 * Create card token
                 */
                $token = Token::create([
                    'card' => [
                        'number' => $data['number'],
                        'exp_month' => $data['exp_month'],
                        'exp_year' => $data['exp_year'],
                        'cvc' => $data['cvc'],
                        'currency' =>'usd'
                    ]
                ]);

                /**
                 * Attach card to external account for payout
                 */
                $card = Account::createExternalAccount(
                    config('services.stripe.connected_account'),
                    [
                        'external_account' => $token->id
                    ]
                );
            } catch (\Exception $e) {
                Log::error($e->getMessage());
            }

            $paymentMethod = PaymentMethod::create([
                'type' => 'card',
                'billing_details' => [
                    'name' => $data['card_holder_name']
                ],
                'card' => [
                    'number' => $data['number'],
                    'exp_month' => $data['exp_month'],
                    'exp_year' => $data['exp_year'],
                    'cvc' => $data['cvc'],
                ],
                'metadata' => [
                    'payout_id' => $card->id ?? NULL
                ]
            ]);

            return $paymentMethod;
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return null;
        }
    }

    public function addCard($data)
    {
        try {
            if (!($this->isStripeCustomer())) {
                $this->createAsStripeCustomer(['phone' => $this->phone]);
            }
            $paymentMethod = $this->addPaymentMethod($data);
            if (!$paymentMethod) {
                throw new \Exception(trans('message.user.card.error.add'));
            }
            $paymentMethod->attach([
                'customer' => $this->stripe_customer_id,
            ]);
            return $paymentMethod;
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return null;
        }
    }

    public function getDefaultPaymentMethod() {
        if (!$this->isStripeCustomer()) {
            $this->createAsStripeCustomer(['phone' => $this->phone]);
        }
        $customer = Customer::retrieve($this->stripe_customer_id);
        $defaultPaymentMethodId = $customer->invoice_settings->default_payment_method ?? NULL;
        return ($defaultPaymentMethodId == NULL) ? NULL : PaymentMethod::retrieve($defaultPaymentMethodId);
    }

    public function getDefaultPaymentMethodId() {
        if (!$this->isStripeCustomer()) {
            $this->createAsStripeCustomer(['phone' => $this->phone]);
        }
        $customer = Customer::retrieve($this->stripe_customer_id);
        return $customer->invoice_settings->default_payment_method ?? NULL;
    }

    public function getCards()
    {
        try {
            if (!$this->isStripeCustomer()) {
                $this->createAsStripeCustomer(['phone' => $this->phone]);
            }
            $paymentMethods = PaymentMethod::all([
                'customer' => $this->stripe_customer_id,
                'type' => 'card',
            ]);
            if ($defaultPaymentMethodId = $this->getDefaultPaymentMethodId()) {
                $result = [];
                foreach ($paymentMethods as $paymentMethod) {
                    if($paymentMethod->id != $defaultPaymentMethodId) {
                        $paymentMethod->is_default = false;
                        array_push($result, $paymentMethod);
                    } else {
                        $paymentMethod->is_default = true;
                        array_unshift($result, $paymentMethod);
                    };
                }
                return $result;
            }

            return $paymentMethods;
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return null;

        }
    }

    public function formatCard($data)
    {
        $card = [
            'id' => $data->id,
            'type' => $data->card->brand,
            'country' => $data->card->country,
            'last4' => $data->card->last4,
            'exp_month' => $data->card->exp_month,
            'exp_year' => $data->card->exp_year,
            'card_holder_name' => $data->billing_details->name,
            'funding' => $data->card->funding ?? NULL,
            'is_default' => $data->is_default ?? false
        ];
        return (object)$card;
    }

    public function removeCard($cardId)
    {
        try {
            $paymentMethod = PaymentMethod::retrieve($cardId);
            if (optional($paymentMethod)->customer == $this->stripe_customer_id) {
                $paymentMethod->detach();
            }
            return true;
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return false;
        }
    }

    public function getCardById($cardId)
    {
        try {
            $paymentMethod = PaymentMethod::retrieve($cardId);
            return $paymentMethod;
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return null;
        }
    }

    public function charge($cardId, $amount, $currency = 'vnd')
    {
        try {
            $options['payment_method'] = $cardId;
            $options['amount'] = $amount;
            $options['currency'] = $currency;
            $options['confirm'] = true;

            if ($this->stripe_customer_id) {
                $options['customer'] = $this->stripe_customer_id;
            }
            $payment = PaymentIntent::create($options);
            return $payment->id;
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return null;
        }
    }

    public function setDefaultCard($cardId)
    {
        try {
            if (!$this->isStripeCustomer()) {
                $this->createAsStripeCustomer(['phone' => $this->phone]);
            }
            $paymentMethod = $this->getCardById($cardId);
            if (!$paymentMethod) {
                throw new \Exception('Card not found');
            }
            Customer::update(
                $this->stripe_customer_id,
                [
                    'invoice_settings' => [
                        'default_payment_method' => $paymentMethod->id
                    ]
                ]);
            return $paymentMethod;
        } catch (ApiErrorException $e) {
            Log::error($e->getMessage());
            return null;
        }
    }

    public function cancelSubscription($subscriptionId) {
        try {
            $subscription = Subscription::retrieve([
                'id' => $subscriptionId
            ]);
            if (!$subscription) {
                throw new \Exception(trans('Subscription not found'));
            }
            $subscription->cancel();
            return true;
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return false;
        }
    }

    public function subscribePlan($planId) {
        try {
            if (!$this->isStripeCustomer()) {
                $this->createAsStripeCustomer(['phone' => $this->phone]);
            }
            $subscription = Subscription::create([
                'customer' => $this->stripe_customer_id,
                'items' => [
                    [
                        'plan' => $planId
                    ]
                ],
            ]);
            return $subscription;
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return null;
        }
    }

    public function withdraw($amount, $cardId) {
        try {
            $balance = Balance::retrieve(config('services.stripe.secret'));
            $availableBalance = $balance->available[0]->amount;
            if ($amount >= $availableBalance) {
                throw new \Exception('message.transaction_histories.withdraw.system_not_enough');
            }

            $data = $this->getCardById($cardId);
            if (!$data) {
                throw new \Exception(trans('message.card.not_found'));
            }

            Payout::create([
                'amount' => $amount,
                'currency' => 'usd',
                'destination' => $data->metadata->payout_id,
                'source_type' => 'card'
            ], [
                'stripe_account' => config('services.stripe.connected_account'),
            ]);

            return ['status' => successStr(), 'message' => 'success', 'data' => $data->card->last4];
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return ['status' => errorStr(), 'message' => $e->getMessage()];
        }
    }
}