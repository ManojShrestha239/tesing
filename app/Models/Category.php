<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name','status'
      ];

      public function products(){
          return $this->hasMany(Product::class);
      }
      public function challans(){
        return $this->hasMany(Challan::class,'category_id');
    }
      protected function serializeDate(\DateTimeInterface $date)
      {
          return $date->format('Y-m-d H:i:s');
      }
}
