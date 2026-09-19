<?php

namespace App\Models;

use App\Traits\AlertTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;

class Mpesa extends Model
{
    use HasFactory;
    use AlertTrait;

    private $access_token;

    private string $consumerSecret;
    private string $consumerKey;
    private string $domain;
    private string $passkey;
    private string $callbackUrl;
    private string $confirmationUrl;
    private string $validationUrl;
    private string $shortCode;
    private string $partyB;
    private string $accountReference;
    private string $mpesa_env;

    public function __construct()
    {
        parent::__construct();

        $this->mpesa_env = env('MPESA_ENVIRONMENT', 'sandbox');

        $this->consumerSecret = env('MPESA_CONSUMER_SECRET');
        $this->consumerKey = env('MPESA_CONSUMER_KEY');

        $this->domain = $this->mpesa_env === 'production'
            ? 'https://api.safaricom.co.ke'
            : 'https://sandbox.safaricom.co.ke';

        $this->passkey = env('MPESA_PASSKEY');
        $this->shortCode = env('MPESA_SHORTCODE');
        $this->partyB = env('MPESA_PARTY_B');
        $this->callbackUrl = env('MPESA_STKCALLBACK_URL');
        $this->accountReference = env('MPESA_ACCOUNT_REFERENCE');
    }

    public function generateAccessToken()
    {
        $url = $this->domain . '/oauth/v1/generate?grant_type=client_credentials';

        $response = Http::withBasicAuth(
            $this->consumerKey,
            $this->consumerSecret
        )->get($url);

        $response->throw();

        return $response->json('access_token');
    }

    public function sendSTKPush(
        string $phoneNumber,
        int $amount,
        string $transactionDescription
    ) {
        $utils = new Utils();

        $accessToken = $this->generateAccessToken();

        $phoneNumber = $utils->convertPhoneNumber($phoneNumber);

        $timestamp = Carbon::now()->format('YmdHis');

        $password = base64_encode(
            $this->shortCode .
                $this->passkey .
                $timestamp
        );

        $info = [
            'BusinessShortCode' => $this->shortCode,
            'Password' => $password,
            'Timestamp' => $timestamp,
            'TransactionType' => 'CustomerPayBillOnline',
            'Amount' => $amount,
            'PartyA' => (int) $phoneNumber,
            'PartyB' => $this->partyB,
            'PhoneNumber' => (int) $phoneNumber,
            'CallBackURL' => $this->callbackUrl,
            'AccountReference' => $this->accountReference,
            'TransactionDesc' => $transactionDescription,
        ];

        try {
            $url = $this->domain . '/mpesa/stkpush/v1/processrequest';

            $response = Http::withToken($accessToken)
                ->post($url, $info);

            $data = $response->json();

            if (($data['ResponseCode'] ?? null) === '0') {
                $this->showAlert(
                    'success',
                    'STK PUSH SUCCESS!',
                    'Customer has been sent an STK push'
                );
            } else {
                $this->showAlert(
                    'warning',
                    'STK PUSH Notification!',
                    json_encode($data)
                );
            }

            return $data;
        } catch (\Throwable $e) {
            $this->showAlert(
                'error',
                $e->getMessage(),
                'STK PUSH ERROR!'
            );

            throw $e;
        }
    }
}
