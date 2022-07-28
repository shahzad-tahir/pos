<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserOrder extends Model
{
    protected $guarded = ['id'];

    protected $fillable = [
        'contact_id',
        'user_name',
        'address',
        'mobile',
        'latitude',
        'longitude',
        'status',
        'update_reason',
        'total_amount'
    ];

    public function contact()
    {
        return $this->belongsTo(Contact::class, 'contact_id');
    }

    public function orderDetails()
    {
        return $this->hasMany(OrderDetails::class, 'order_id');
    }

    public function scopeUserOrders($query, $userId)
    {
        return $query->where('contact_id', $userId);
    }

    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }
}
