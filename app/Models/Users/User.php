<?php

namespace App\Models\Users;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Http\Request;
use Spatie\Permission\Traits\HasRoles;


class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];



    /**
     * Validate the fields for the password change
     *
     * @param  Request $request
     * @return boolean
     */
    public function validateChangePassword(Request $request)
    {
        return $request->validate([
            'currentpassword' => 'required|string|min:6',
            'password'       => 'nullable|confirmed|min:6'
        ]);
    }


    // Scopes
    public function scopeExactUsername($query, $nombre)
    {
        $nombre = mb_strtolower(trim($nombre));
        return $query->where('username', $nombre);
    }

    public function scopeUsername($query, $nombre)
    {
        $nombre = mb_strtolower(trim($nombre));
        return $query->where('username', 'like', '%'.$nombre.'%');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'Active');
    }

    public function scopeStatus($query, $estado)
    {
        return $query->where('status', $estado);
    }

    public function scopeCustomerId($query, $customerId)
    {
        return $query->where('customer_id', $customerId);
    }

}
