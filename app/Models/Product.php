<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $table='products';
   // protected $fillable=['name','mrp','selling','description','image'];
    protected $hidden = ['created_at','updated_at','is_del'];
    protected $appends = ['img','product_id'];
    public function getImgAttribute()
    {       
        //return @$image['image'] ? env('APP_IMAGE_URL').$image['image'] : null;
        return $this->image ? asset('storage/'.$this->image) : null;
    }
    public function getProductIdAttribute()
    {
        return $this->{'id'};
    }
}
