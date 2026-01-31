<?php

namespace App\Models;

use App\Traits\AlertTrait;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class Mpesa extends Model
{
    use HasFactory;
    use AlertTrait;

    private $access_token;

    private $consumerSecret;
    private $consumerKey;

    private $domain;

    private $passkey;

    private $stkUrl;

    private $callbackUrl;

    private $confirmationUrl;

    private $validationUrl;

    private $shortCode;

    private $mpesa_env;

    public function __construct()
    {
        $this->mpesa_env = env('MPESA_ENVIRONMENT');

        $this->consumerSecret = env('MPESA_CONSUMER_SECRET');
        $this->consumerKey = env('MPESA_CONSUMER_KEY');
        $this->domain = $this->mpesa_env == 'production' ? 'https://api.safaricom.co.ke' : 'https://sandbox.safaricom.co.ke';
        $this->passkey = env('MPESA_PASSKEY');
        $this->shortCode = env('MPESA_SHORTCODE');
        $this->partyB = env('NEXT_PUBLIC_MPESA_PARTYB');
        $this->callbackUrl = env('MPESA_STKCALLBACK_URL');
        $this->refference = env('MPESA_ACCOUNT_REFERENCE');
    }

    public function generateAccessToken()
    {
        // dd($this->mpesa_env);/

        $consumerKey = $this->consumerKey;
        $consumerSecret = $this->consumerSecret;
        $domain = $this->domain;
        $url = (string) $domain . '/oauth/v1/generate?grant_type=client_credentials';
        $response = Http::withBasicAuth($consumerKey, $consumerSecret)->get($url);
        $access_token = $response['access_token'];

        return $access_token;
    }

    public function sendSTKPush($phoneNumber, $amount, $transactionDescription)
    {
        $utils = new Utils();
        $accessToken = $this->generateAccessToken();

        $phoneNumber = $utils->convertPhoneNumber($phoneNumber);

        // dd($phoneNumber);
        $BusinessShortCode = $this->shortCode;
        $url = (string) $this->domain . '/mpesa/stkpush/v1/processrequest';
        $PassKey = $this->passkey;
        $Timestamp = Carbon::now()->format('YmdHis');
        $password = base64_encode($BusinessShortCode . $PassKey . $Timestamp);

        $CallbackUrl = $this->callbackUrl;
        $Amount = $amount;
        $PartyA = (int) $phoneNumber;
        $PartyB = $this->partyB;
        $PhoneNumber = (int) $phoneNumber;
        $AccountReference = $this->refference;
        $TransactionDesc = $transactionDescription;

        $info = [
            'BusinessShortCode' => $BusinessShortCode,
            'Password' => $password,
            'Timestamp' => $Timestamp,
            'TransactionType' => 'CustomerPayBillOnline',
            'Amount' => $Amount,
            'PartyA' => $PartyA,
            'PartyB' => $PartyB,
            'PhoneNumber' => $PhoneNumber,
            'CallBackURL' => $CallbackUrl,
            'AccountReference' => $AccountReference,
            'TransactionDesc' => $TransactionDesc,
        ];

        try {
            $response = Http::withToken($accessToken)->post($url, $info);
            $data = $response->json();

            // dd($data['ResponseCode']);

            if ($data['ResponseCode'] == '0') {
                $this->showAlert('success', 'STK PUSH SUCCESS!', 'Customer has been sent an stk push');
            } else {
                $this->showAlert(
                    'warning',
                    'STK PUSH Notification!',
                    json_encode($response->json()), // show real Safaricom response
                );
            }
        } catch (\Throwable $e) {
            $this->showAlert('error', $e->getMessage(), 'STK PUSH ERROR !');
        }
    }
}
