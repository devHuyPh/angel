<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StoreLevel extends Model
{
  protected $table = 'store_levels';
  
  protected $fillable = ['name', 'value', 'commission'];

  public function stores()
  {
    return $this->hasMany(MpStore::class, 'store_level_id');
  }

}
