<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

use Animelhd\AnimesFavorite\Traits\Favoriter;
use Animelhd\AnimesView\Traits\Viewer;
use Animelhd\AnimesWatching\Traits\Watchinger;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;
    use Favoriter;
	use Viewer;
	use Watchinger;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function getToken($request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'device_name' => 'required',
        ]);
        $user = $this->where('email', $request->email)->first();
        if (!$user || !Hash::check($request->password, $user->password)) {
            return array(
                'msg' => 'Usuario y/o contraseña incorrectos.'
            );
        }
        return $user->createToken($request->device_name)->plainTextToken;
    }

    public function login($request)
    {
        return $request->user();
    }

    public function logout($request)
    {
        return $request->user()->tokens()->delete();
    }

}
