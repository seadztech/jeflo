<?php

namespace App\Http\Controllers\api;

use App\Events\MpesaTransactionReceived;
use App\Http\Controllers\Controller;
use App\Models\Transactions;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PhpParser\Node\Expr\Cast\String_;

class PaymentController extends Controller
{
   public function stkCallback()
{
    try {
        $data = file_get_contents('php://input');
        

        $data = [
        'message' => 'The sms was received well',
        'data' => $data,
        'status' => 200,
        ];

        Log::info('Raw Callback:'. json_encode($data));

        return $data;

        $jsonPayload = json_decode($data);
        if (json_last_error() !== JSON_ERROR_NONE) {
            Log::error('Invalid JSON payload:', [$data]);
            return response('Invalid JSON', 400);
        }

        $response = isset($jsonPayload->data) ? json_decode($jsonPayload->data) : $jsonPayload;
        if (json_last_error() !== JSON_ERROR_NONE) {
            Log::error('Invalid nested JSON payload:', [$jsonPayload]);
            return response('Invalid nested JSON', 400);
        }

        if (!isset($response->Body->stkCallback)) {
            Log::error('Missing stkCallback in response:', [$response]);
            return response('Missing stkCallback', 400);
        }

        $callback = $response->Body->stkCallback;
        $ResultCode = $callback->ResultCode ?? null;
        $ResultDesc = $callback->ResultDesc ?? null;

        Log::info('STK ResultCode:', [$ResultCode]);
        Log::info('STK ResultDesc:', [$ResultDesc]);

        if ($ResultCode == 0) {
            $metadata = $callback->CallbackMetadata->Item ?? [];
            $callbackData = [
                'MerchantRequestID' => $callback->MerchantRequestID ?? null,
                'CheckoutRequestID' => $callback->CheckoutRequestID ?? null,
            ];

            // Process metadata items
            foreach ($metadata as $item) {
                switch ($item->Name ?? '') {
                    case 'Amount':
                        $callbackData['Amount'] = $item->Value ?? null;
                        break;
                    case 'MpesaReceiptNumber':
                        $callbackData['TransactionCode'] = $item->Value ?? null;
                        break;
                    case 'TransactionDate':
                        $callbackData['TransactionDate'] = $item->Value ?? null;
                        break;
                    case 'PhoneNumber':
                        $callbackData['PhoneNumber'] = $item->Value ?? null;
                        break;
                }
            }

            // Create transaction
            $transaction = new Transactions();
            $transaction->type = 0;
            $transaction->transaction_code = $callbackData['TransactionCode'] ?? null;
            $transaction->response = json_encode($response);
            $transaction->amount = $callbackData['Amount'] ?? null;
            $transaction->save();

            $callbackData['transactionID'] = $transaction->id;
            $callbackData['ResultCode'] = $ResultCode;
            $callbackData['ResultDesc'] = $ResultDesc;

            MpesaTransactionReceived::dispatch($callbackData);
        } else {
            MpesaTransactionReceived::dispatch([
                'ResultCode' => $ResultCode,
                'ResultDesc' => $ResultDesc,
                'rawData' => $data
            ]);
        }

        return response('Callback processed', 200);
    } catch (\Exception $e) {
        Log::error('STK Callback Error:', [
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        return response('Processing error', 500);
    }
}
}
