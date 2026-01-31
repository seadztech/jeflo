<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component {
    public LoginForm $form;

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        // dd('This is the endpoint');
        $this->validate();

        $this->form->authenticate();

        Session::regenerate();

        $this->redirectIntended(
            default: route('dashboard', absolute: false),
            navigate: true
        );
    }
};
?>

<div class="relative">

    <!-- Decorative background (FIXED: does not block inputs) -->
    <div class="absolute top-0 right-0 bg-blue-600 rounded-full opacity-5 w-96 h-96 blur pointer-events-none"></div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form wire:submit="login" class="space-y-4 relative z-10">

        <h4 class="mb-4 font-medium text-center">Login</h4>

        <!-- Email -->
        <div class="mb-3">
            <input
                type="email"
                class="form-control @error('form.email') is-invalid @enderror"
                placeholder="Email Address"
                wire:model="form.email"
                autocomplete="username" />
            @error('form.email')
            <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <!-- Password -->
        <div class="mb-4">
            <input
                type="password"
                class="form-control @error('form.password') is-invalid @enderror"
                placeholder="Password"
                wire:model="form.password"
                autocomplete="current-password" />
            @error('form.password')
            <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <!-- Remember Me -->
        <div class="flex flex-wrap items-center justify-between mt-1">
            <div class="form-check">
                <input
                    class="form-check-input input-primary"
                    type="checkbox"
                    id="remember_me"
                    wire:model="form.remember" />
                <label class="form-check-label text-muted" for="remember_me">
                    Remember me?
                </label>
            </div>

            <h6 class="mb-0 font-normal text-primary-500">
                <a href="{{ route('password.request') }}">Forgot Password?</a>
            </h6>
        </div>

        <div class="mt-4 text-center">
            <button
                type="submit"
                class="mx-auto shadow-2xl btn btn-primary w-full flex items-center justify-center gap-2"
                wire:loading.attr="disabled">
                <!-- Normal State -->
                <span wire:loading.remove>
                    Login
                </span>

                <!-- Loading State -->
                <span wire:loading class="flex items-center gap-2">
                    <span
                        class="spinner-border spinner-border-sm"
                        role="status"
                        aria-hidden="true"></span>
                    Logging in...
                </span>
            </button>
        </div>


        <!-- Google Login -->
        <div class="w-full mt-4">
            <a
                href="{{ route('redirect') }}"
                class="border w-full flex items-center py-3 px-2 gap-4 text-xl bg-slate-50 rounded cursor-pointer">
                <img
                    src="https://www.svgrepo.com/show/475656/google-color.svg"
                    alt="Google Icon"
                    class="w-5 h-5" />
                <span>Continue with Google</span>
            </a>
        </div>

        <!-- Register
        <div class="flex flex-wrap items-end justify-between mt-4">
            <h6 class="mb-0 font-medium">Don't have an Account?</h6>
            <a href="{{ route('register') }}" class="text-primary-500">
                Create Account
            </a>
        </div> -->

    </form>
</div>