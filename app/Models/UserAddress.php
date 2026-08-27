<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAddress extends Model
{
    use HasFactory;

    protected $table = 'user_addresses';

    protected $fillable = [
        'user_id',
        'session_id',
        'address_type',
        'full_name',
        'first_name',
        'last_name',
        'phone',
        'pincode',
        'house_no',
        'address_line_1',
        'road_area',
        'address_line_2',
        'city',
        'state',
        'landmark',
        'email',
        'full_address',
        'is_default',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getFirstNameAttribute($value)
    {
        if (!empty($value)) return $value;
        if (!empty($this->attributes['full_name'])) {
            return explode(' ', trim($this->attributes['full_name']))[0] ?? '';
        }
        return '';
    }

    public function getLastNameAttribute($value)
    {
        if (!empty($value)) return $value;
        if (!empty($this->attributes['full_name'])) {
            $parts = explode(' ', trim($this->attributes['full_name']));
            return count($parts) > 1 ? implode(' ', array_slice($parts, 1)) : '';
        }
        return '';
    }

    public function getAddressLine1Attribute($value)
    {
        return $value ?: ($this->attributes['house_no'] ?? '');
    }

    public function getAddressLine2Attribute($value)
    {
        return $value ?: ($this->attributes['road_area'] ?? '');
    }

    public function getCityAttribute($value)
    {
        if (!empty($value)) return $value;
        if (!empty($this->attributes['full_address'])) {
            $clean = preg_replace('/\s*-\s*\d{6}$/', '', $this->attributes['full_address']);
            $parts = array_map('trim', explode(',', $clean));
            if (count($parts) >= 3) {
                return $parts[count($parts) - 2] ?? '';
            }
        }
        return $this->attributes['road_area'] ?? '';
    }

    public function getStateAttribute($value)
    {
        if (!empty($value)) return $value;
        if (!empty($this->attributes['full_address'])) {
            $clean = preg_replace('/\s*-\s*\d{6}$/', '', $this->attributes['full_address']);
            $parts = array_map('trim', explode(',', $clean));
            if (count($parts) >= 2) {
                return end($parts);
            }
        }
        return '';
    }
}
