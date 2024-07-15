<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Srmklive\PayPal\Services\PayPal as PayPalClient;
use Illuminate\Support\Facades\Log;

class PayPalController extends Controller
{
    /**
     * Show the PayPal payment view.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        return view('paypal');
    }

    /**
     * Process the PayPal payment.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function payment(Request $request)
    {
        try {
            $provider = new PayPalClient;
            $provider->setApiCredentials(config('paypal'));
            $paypalToken = $provider->getAccessToken();

            $response = $provider->createOrder([
                "intent" => "CAPTURE",
                "application_context" => [
                    "return_url" => route('paypal.payment.success'),
                    "cancel_url" => route('paypal.payment/cancel'),
                ],
                "purchase_units" => [
                    0 => [
                        "amount" => [
                            "currency_code" => "USD",
                            "value" => "100.00"
                        ]
                    ]
                ]
            ]);

            Log::info('PayPal Create Order Response:', ['response' => $response]);

            if (isset($response['id']) && $response['id'] != null) {
                foreach ($response['links'] as $links) {
                    if ($links['rel'] == 'approve') {
                        return redirect()->away($links['href']);
                    }
                }

                return redirect()
                    ->route('paypal.payment/cancel')
                    ->with('error', 'Something went wrong.');
            } else {
                return redirect()
                    ->route('paypal.payment/cancel')
                    ->with('error', $response['message'] ?? 'Something went wrong.');
            }
        } catch (\Exception $e) {
            Log::error('PayPal Create Order Error:', ['error' => $e->getMessage()]);
            return redirect()
                ->route('paypal.payment/cancel')
                ->with('error', 'Something went wrong.');
        }
    }

    /**
     * Handle payment cancellation.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function paymentCancel()
    {
        return redirect()
            ->route('paypal')
            ->with('error', 'You have canceled the transaction.');
    }

    /**
     * Handle payment success.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function paymentSuccess(Request $request)
    {
        try {
            $provider = new PayPalClient;
            $provider->setApiCredentials(config('paypal'));
            $provider->getAccessToken();
            $response = $provider->capturePaymentOrder($request['token']);

            Log::info('PayPal Capture Payment Order Response:', ['response' => $response]);

            if (isset($response['status']) && $response['status'] == 'COMPLETED') {
                return redirect()
                    ->route('paypal')
                    ->with('success', 'Transaction complete.');
            } else {
                return redirect()
                    ->route('paypal')
                    ->with('error', $response['message'] ?? 'Something went wrong.');
            }
        } catch (\Exception $e) {
            Log::error('PayPal Capture Payment Order Error:', ['error' => $e->getMessage()]);
            return redirect()
                ->route('paypal')
                ->with('error', 'Something went wrong.');
        }
    }
}
