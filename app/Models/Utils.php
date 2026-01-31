<?php

namespace App\Models;

use DateTime;
use Illuminate\Support\Facades\Log;

class Utils
{
    // public function validatePhoneNumber($phoneNumber)
    // {
    //     $expectedPattern = '/^254\d{9}$/';

    //     if (!preg_match($expectedPattern, $phoneNumber)) {
    //         throw new \InvalidArgumentException('Invalid phone number format.');
    //     }

    //     return $phoneNumber; // Validation passed
    // }

    // this functions ensures a number starts with 254
    public function convertPhoneNumber($phoneNumber)
    {
        // Check if the phone number starts with '0'
        if (substr($phoneNumber, 0, 1) == '0') {
            // Replace the leading '0' with '254'
            $phoneNumber = '254' . substr($phoneNumber, 1);
        }

        return (int) $phoneNumber;
    }

   public static function formatDate($rawDate)
{
    if (empty($rawDate)) {
        return 'No date provided';
    }

    try {
        $date = DateTime::createFromFormat('YmdHis', $rawDate);
        if (!$date) {
            return 'Invalid date format';
        }
        return $date->format('jS F Y g:ia');
    } catch (\Exception $e) {
        Log::error('Date formatting error:', [
            'date' => $rawDate,
            'error' => $e->getMessage()
        ]);
        return 'Date formatting error';
    }
}
}
