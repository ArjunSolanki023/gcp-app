<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = ['user_id','payment_amt','payment_type','payment_status','order_date','address_id','status','payment_gateway'];
    protected $hidden = ['created_at','updated_at'];
   // protected $appends = ['total_price'];
    
}
