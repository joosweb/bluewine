<?php

namespace App\Http\Controllers\Auth;

use Auth;
use App\User;
use Socialite;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;

class GoogleController extends Controller
{
    // Metodo encargado de la redireccion a Facebook
    public function redirectToProvider($provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    // Metodo encargado de obtener la información del usuario
    public function handleProviderCallback($provider)
    {
        // Obtenemos los datos del usuario
        $social_user = Socialite::driver($provider)->user();
        // Comprobamos si el usuario ya existe
        if ($user = User::where('email', $social_user->email)->first()) {
            return $this->authAndRedirect($user); // Login y redirección
        } else {
            // En caso de que no exista creamos un nuevo usuario con sus datos.
            $user = User::create([
                'name' => $social_user->name,
                'email' => $social_user->email,
                'photo' => $social_user->avatar,
                'password' => encrypt('123456dummy')
            ]);

            $shop = DB::table('shop')->where('fk_user_id_admin', $user->id)->first();
            $user = User::find($user->id);
            $user->shop = $shop->id;
            $user->save();

            return $this->authAndRedirect($user); // Login y redirección
        }
    }

    // Login y redirección
    public function authAndRedirect($user)
    {
        Auth::login($user);

        return redirect()->to('/');
    }
}
