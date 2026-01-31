<?php

namespace App\Traits;

use illuminate\Http\Request;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;

trait AlertTrait
{
    /**
     * Show a Livewire Alert with custom type, title, and message.
     *
     * @param string $type success|error|warning|info
     * @param string $title
     * @param string|null $message
     * @return void
     */
    public function showAlert(string $type, string $title, ?string $message = null, ?string $method = null): void
    {
        switch ($type) {
            case 'success':
                LivewireAlert::title($title)->text($message)->success()->show();
                break;
            case 'info':
                LivewireAlert::title($title)->text($message)->info()->show();
                break;
            case 'warning':
                LivewireAlert::title($title)->text($message)->warning()->show();
                break;
            case 'error':
                LivewireAlert::title($title)->text($message)->error()->show();
                break;
            case 'confirm':
                LivewireAlert::title($title)->withConfirmButton('Yes')->withDenyButton('Cancel')->onDeny($method)->onConfirm($method)->onDismiss($method)->show();
                break;
        }
    }
}
