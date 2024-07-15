<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PaymentContoller extends Controller
{
    private $gateway;
    public function __construct()
    {
        $this->gateway = Omnipay::create('PayPal_Rest');
        $this->gateway->setClientId(env('PAYPAL_SANDBOX_CLIENT_ID'));
        $this->gateway->setSecret(env('PAYPAL_SANDBOX_CLIENT_SECRET'));
        $this->gateway->setTestMode(true);
    }

    public function pay(Request $request){
        try{
            $response = $this->gateway->purchase(array(
                'amount' => $request->amount,
                'currency' => 'USD',
                'returnUrl' => route('payment.success'),
                'cancelUrl' => route('payment.cancel')
            ));

            if($response->isRedirect()){
                $response->redirect();
            }else{
                return $response->getMessage();
            }
        } catch(\Throwable $th){
            return $th->getMessage();
        }
    }
}
