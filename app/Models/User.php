<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
//use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'staff_id',
        'name',
        'email',
        'password',
        'phone',
        'gender',
        'dob',
        'doj',
        'address',
        'state_id',
        'city_id',
        'pincode',
        'aadhar_no',
        'pan_no',
        'email_verified_at',
        'password',
        'remember_token',
        'store_id',
        'user_type',
        'status',
        'created_at',
        'updated_at',
        'approve_discount',
        'photo',
        'avatar',
        'image',
        'pan_img',
        'aadhar_front',
        'aadhar_back'
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function getFirstNameAttribute()
    {
        $parts = explode(' ', trim($this->name ?? ''), 2);
        return $parts[0] ?? '';
    }

    public function getLastNameAttribute()
    {
        $parts = explode(' ', trim($this->name ?? ''), 2);
        return $parts[1] ?? '';
    }

    public function getImageAttribute($value)
    {
        return $value ?: ($this->attributes['avatar'] ?? ($this->attributes['photo'] ?? null));
    }

    public function getProfileImageUrlAttribute()
    {
        $img = $this->image;
        if (!empty($img)) {
            if (file_exists(public_path('uploads/website/profile/' . $img))) {
                return asset('uploads/website/profile/' . $img);
            }
            if (file_exists(public_path('uploads/profile/' . $img))) {
                return asset('uploads/profile/' . $img);
            }
            return asset('uploads/website/profile/' . $img);
        }
        return asset('assets/img/bg/profile.png');
    }

    public function b2cOrders()
    {
        return $this->hasMany(\App\Models\b2c\B2cOrder::class, 'user_id');
    }

    public function addresses()
    {
        return $this->hasMany(\App\Models\UserAddress::class, 'user_id');
    }
}
