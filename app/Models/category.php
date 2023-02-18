<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class category extends Model
{
    use HasFactory;
    protected $hidden = ['created_at','updated_at','image'];
    protected $appends = ['img'];
    public function getImgAttribute()
    {
       
        return $this->image ? asset('storage/'.$this->image) : null;
    }
}
