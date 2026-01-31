<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Throwable;

class GoogleAuthController extends Controller
{
    //
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    public function callbackFunction()
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            // dd($googleUser);


            $user = User::where('google_id', $googleUser->getId())->first();


            if (!$user) {

                $new_user = new User();
                $new_user->name = $googleUser->getName();
                $new_user->email = $googleUser->getEmail();
                $new_user->google_id = $googleUser->getId();
                $new_user->avatar = $googleUser->getAvatar();
                $new_user->save();

                // dd($new_user);

                Auth::login($new_user);
                return redirect(route('dashboard'));
            } else {
                Auth::login($user);
                return redirect(route('dashboard'));
            }
        } catch (Throwable $th) {
            dd('Something went Wrong!', $th->getMessage());
        }
    }
}
