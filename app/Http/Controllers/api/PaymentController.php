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
    // public function stkCallback()
    // {
    //     try {
    //         $data = file_get_contents('php://input');



    //         $jsonPayload = json_decode($data);
    //         if (json_last_error() !== JSON_ERROR_NONE) {
    //             Log::error('Invalid JSON payload:', [$data]);
    //             return response('Invalid JSON', 400);
    //         }

    //         $response = isset($jsonPayload->data) ? json_decode($jsonPayload->data) : $jsonPayload;
    //         if (json_last_error() !== JSON_ERROR_NONE) {
    //             Log::error('Invalid nested JSON payload:', [$jsonPayload]);
    //             return response('Invalid nested JSON', 400);
    //         }

    //         if (!isset($response->Body->stkCallback)) {
    //             Log::error('Missing stkCallback in response:', [$response]);
    //             return response('Missing stkCallback', 400);
    //         }

    //         $callback = $response->Body->stkCallback;
    //         $ResultCode = $callback->ResultCode ?? null;
    //         $ResultDesc = $callback->ResultDesc ?? null;

    //         Log::info('STK ResultCode:', [$ResultCode]);
    //         Log::info('STK ResultDesc:', [$ResultDesc]);

    //         if ($ResultCode == 0) {
    //             $metadata = $callback->CallbackMetadata->Item ?? [];
    //             $callbackData = [
    //                 'MerchantRequestID' => $callback->MerchantRequestID ?? null,
    //                 'CheckoutRequestID' => $callback->CheckoutRequestID ?? null,
    //             ];

    //             // Process metadata items
    //             foreach ($metadata as $item) {
    //                 switch ($item->Name ?? '') {
    //                     case 'Amount':
    //                         $callbackData['Amount'] = $item->Value ?? null;
    //                         break;
    //                     case 'MpesaReceiptNumber':
    //                         $callbackData['TransactionCode'] = $item->Value ?? null;
    //                         break;
    //                     case 'TransactionDate':
    //                         $callbackData['TransactionDate'] = $item->Value ?? null;
    //                         break;
    //                     case 'PhoneNumber':
    //                         $callbackData['PhoneNumber'] = $item->Value ?? null;
    //                         break;
    //                 }
    //             }

    //             // Create transaction
    //             $transaction = new Transactions();
    //             $transaction->type = 0;
    //             $transaction->transaction_code = $callbackData['TransactionCode'] ?? null;
    //             $transaction->response = json_encode($response);
    //             $transaction->amount = $callbackData['Amount'] ?? null;
    //             $transaction->save();

    //             $callbackData['transactionID'] = $transaction->id;
    //             $callbackData['ResultCode'] = $ResultCode;
    //             $callbackData['ResultDesc'] = $ResultDesc;

    //             MpesaTransactionReceived::dispatch($callbackData);
    //         } else {
    //             MpesaTransactionReceived::dispatch([
    //                 'ResultCode' => $ResultCode,
    //                 'ResultDesc' => $ResultDesc,
    //                 'rawData' => $data
    //             ]);
    //         }

    //         return response('Callback processed', 200);
    //     } catch (\Exception $e) {
    //         Log::error('STK Callback Error:', [
    //             'message' => $e->getMessage(),
    //             'trace' => $e->getTraceAsString()
    //         ]);
    //         return response('Processing error', 500);
    //     }
    // }

public function stkCallback()
{
    try {
        // 1. Get raw input
        $rawInput = file_get_contents('php://input');
        Log::info('Raw Callback:', [$rawInput]);

        // 2. Decode outer JSON
        $payload = json_decode($rawInput);

        if (json_last_error() !== JSON_ERROR_NONE) {
            Log::error('Invalid JSON payload:', [$rawInput]);
            return response()->json([
                'message' => 'Invalid JSON',
                'status' => 400
            ], 400);
        }

        // 3. Handle array-wrapped SMS payload
        if (is_array($payload) && isset($payload[0])) {
            $inner = json_decode($payload[0]);
            if (json_last_error() === JSON_ERROR_NONE) {
                $payload = $inner;
            } else {
                Log::error('Invalid inner JSON in array payload', [$payload[0]]);
                return response()->json([
                    'message' => 'Invalid nested JSON',
                    'status' => 400
                ], 400);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Process SMS Adapter Payload
        |--------------------------------------------------------------------------
        */
        if (isset($payload->reference)) {
            // Check for duplicate
            $existing = Transactions::where('transaction_code', $payload->reference)->first();
            if ($existing) {
                Log::info('Duplicate SMS Transaction ignored', ['transaction_code' => $payload->reference]);
                return response()->json([
                    'message' => 'Duplicate SMS Transaction ignored',
                    'transaction_id' => $existing->id,
                    'status' => 200
                ]);
            }

            // Save SMS transaction
            $transaction = new Transactions();
            $transaction->type = 'sms_mpesa';
            $transaction->transaction_code = $payload->reference ?? null;
            $transaction->amount = $payload->amount ?? null;
            $transaction->phone_number = $payload->phoneNumber ?? null;
            $transaction->phone_number = $payload->senderName;
            $transaction->response = json_encode($payload);
            $transaction->save();

            // Dispatch event
            $callbackData = [
                'transactionID' => $transaction->id,
                'TransactionCode' => $payload->reference ?? null,
                'Amount' => $payload->amount ?? null,
                'PhoneNumber' => $payload->phoneNumber ?? null,
                'SenderName' => $payload->senderName ?? null,
                'Message' => $payload->message ?? null,
                'Timestamp' => $payload->timestamp ?? null,
            ];
            
            MpesaTransactionReceived::dispatch($callbackData);

            Log::info('SMS Transaction Saved and dispatched', ['id' => $transaction->id]);

            return response()->json([
                'message' => 'SMS Transaction processed',
                'transaction_id' => $transaction->id,
                'status' => 200
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Process Daraja MPESA Callback
        |--------------------------------------------------------------------------
        */
        if (isset($payload->Body->stkCallback)) {

            $mpesaCallback = $payload->Body->stkCallback;
            $ResultCode = $mpesaCallback->ResultCode ?? null;
            $ResultDesc = $mpesaCallback->ResultDesc ?? null;

            Log::info('MPESA ResultCode:', [$ResultCode]);
            Log::info('MPESA ResultDesc:', [$ResultDesc]);

            if ($ResultCode == 0) {
                $metadata = $mpesaCallback->CallbackMetadata->Item ?? [];

                $callbackData = [
                    'MerchantRequestID' => $mpesaCallback->MerchantRequestID ?? null,
                    'CheckoutRequestID' => $mpesaCallback->CheckoutRequestID ?? null,
                ];

                $transactionCode = null;
                $amount = null;
                $phoneNumber = null;

                foreach ($metadata as $item) {
                    switch ($item->Name ?? '') {
                        case 'Amount':
                            $amount = $item->Value ?? null;
                            $callbackData['Amount'] = $amount;
                            break;
                        case 'MpesaReceiptNumber':
                            $transactionCode = $item->Value ?? null;
                            $callbackData['TransactionCode'] = $transactionCode;
                            break;
                        case 'TransactionDate':
                            $callbackData['TransactionDate'] = $item->Value ?? null;
                            break;
                        case 'PhoneNumber':
                            $phoneNumber = $item->Value ?? null;
                            $callbackData['PhoneNumber'] = $phoneNumber;
                            break;
                    }
                }

                // Check for duplicate
                $existing = Transactions::where('transaction_code', $transactionCode)->first();
                if ($existing) {
                    Log::info('Duplicate MPESA Transaction ignored', ['transaction_code' => $transactionCode]);
                    return response()->json([
                        'message' => 'Duplicate MPESA Transaction ignored',
                        'transaction_id' => $existing->id,
                        'status' => 200
                    ]);
                }

                // Save MPESA transaction
                $transaction = new Transactions();
                $transaction->type = 'stk_mpesa';
                $transaction->transaction_code = $transactionCode;
                $transaction->amount = $amount;
                $transaction->phone_number = $phoneNumber;
                $transaction->response = json_encode($payload);
                $transaction->save();

                $callbackData['transactionID'] = $transaction->id;
                $callbackData['ResultCode'] = $ResultCode;
                $callbackData['ResultDesc'] = $ResultDesc;

                MpesaTransactionReceived::dispatch($callbackData);

                return response()->json([
                    'message' => 'MPESA Callback processed',
                    'transaction_id' => $transaction->id,
                    'status' => 200
                ]);
            } else {
                // Failed MPESA transaction
                MpesaTransactionReceived::dispatch([
                    'ResultCode' => $ResultCode,
                    'ResultDesc' => $ResultDesc,
                    'rawData' => $payload
                ]);

                return response()->json([
                    'message' => 'MPESA Callback failed',
                    'ResultCode' => $ResultCode,
                    'ResultDesc' => $ResultDesc,
                    'status' => 200
                ]);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Unknown Payload
        |--------------------------------------------------------------------------
        */
        Log::warning('Unknown callback structure', [$payload]);
        return response()->json([
            'message' => 'Unknown payload structure',
            'status' => 400
        ], 400);

    } catch (\Exception $e) {

        Log::error('MPESA Callback Error:', [
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);

        return response()->json([
            'message' => 'Processing error',
            'status' => 500
        ], 500);
    }
}



}
