<?php

use Illuminate\Support\Facades\Password;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public string $email = '';

    /**
     * Send a password reset link to the provided email address.
     */
    public function sendPasswordResetLink(): void
    {
        $this->validate([
            'email' => ['required', 'string', 'email'],
        ]);

        $status = Password::sendResetLink(
            $this->only('email')
        );

        if ($status != Password::RESET_LINK_SENT) {
            $this->addError('email', __($status));
            return;
        }

        $this->reset('email');

        session()->flash('status', __($status));
    }
};
?>

<div class="relative">

    <div class="mb-4 text-sm text-gray-600 dark:text-gray-400">
        {{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.') }}
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form wire:submit.prevent="sendPasswordResetLink" class="space-y-4">

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input
                wire:model.defer="email"
                id="email"
                class="block mt-1 w-full bg-white dark:bg-gray-700 dark:text-white placeholder-gray-400 focus:ring-blue-500 focus:border-blue-500"
                type="email"
                name="email"
                required
                autofocus
                autocomplete="email"
            />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Submit Button with Loading -->
        <div class="flex items-center justify-end mt-4">
            <button
                type="submit"
                class="btn btn-primary flex items-center justify-center gap-2"
                wire:loading.attr="disabled"
                wire:target="sendPasswordResetLink"
            >
                <!-- Normal State -->
                <span wire:loading.remove wire:target="sendPasswordResetLink">
                    {{ __('Email Password Reset Link') }}
                </span>

                <!-- Loading State -->
                <span wire:loading wire:target="sendPasswordResetLink" class="flex items-center gap-2">
                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                    {{ __('Sending...') }}
                </span>
            </button>
        </div>
    </form>

</div>
