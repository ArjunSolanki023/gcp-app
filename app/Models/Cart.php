<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;
    protected $fillable = ['user_id','product_id','add_by_bid'];
    protected $hidden = ['selling','author_en','author_ar','sale_type','status','category_id','user_id','created_at','updated_at','is_del','max_bid_price'];
    protected $appends = ['img'];
    public function getImgAttribute()
    {     
        //return @$image['image'] ? env('APP_IMAGE_URL').$image['image'] : null;
        // return $this->image ? env('APP_IMAGE_URL').$this->image : null;
        return $this->image ? asset('storage/'.$this->image) : null;
    }
    /* public function getnameAttribute()
    {
        return $this->{'name_' . App::getLocale()};
    }
    */
}
