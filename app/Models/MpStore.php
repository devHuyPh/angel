<?php

namespace App\Models;

use Botble\Ecommerce\Models\Product;
use Illuminate\Database\Eloquent\Model;
use Botble\Location\Models\State;
use Botble\Location\Models\city;

class MpStore extends Model
{
  protected $fillable = ['name', 'email', 'phone', 'address', 'country', 'state', 'city', '	ward', 'customer_id', 'store_level_id', 'government_id_file', 'certificate_file', 'tax_id', 'company', 'zip_code', 'updated_at', 'created_at', 'vendor_verified_at', 'status', 'content', 'description', 'cover_image', 'logo_square', 'logo'];

  public function level()
  {
    return $this->belongsTo(StoreLevel::class, 'store_level_id');
  }

  public function products()
  {
    return $this->hasMany(Product::class, 'store_id');
  }

  public function states()
  {
    return $this->belongsTo(State::class, 'state');
  }
  
  public function cities()
  {
    return $this->belongsTo(city::class, 'city');
  }
}
