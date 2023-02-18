<?php

namespace App\Models;
use App;
use App\Models\User;    
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    protected $fillable = ['user_id','address','landmark','city','state'];
    protected $hidden = ['user_id','created_at','updated_at','is_del'];
    //protected $appends = ['house_type'];

   /*  public function gethousetypeAttribute()
    {
        $house_type = $this->{'house_type'};
        dd($house_type);
        return $house_type ? 1 : 0;
    }
    public function getisDefaultAttribute($value)
    {
         return (int) $value;
    } */
      public function address()
    {
        return $this->belongsTo('App\Models\Address', 'user_id');
    }
}
