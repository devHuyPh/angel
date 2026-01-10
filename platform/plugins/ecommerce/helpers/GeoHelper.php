<?php

namespace App\Helpers;

use Botble\Marketplace\Models\Store;
use Botble\Marketplace\Http\Requests\StoreRequest;

class GeoHelper
{
  protected static $token;

  public static function setToken(string $token)
  {
    static::$token = $token;
  }
  public static function geocodeLocationIQ(string $address): ?array
  {
    $key = env('LOCATIONIQ_API_KEY');
    $url = "https://us1.locationiq.com/v1/search?key=$key&q=" . urlencode($address) . "&format=json";
  	  // dd($url);
      $response = file_get_contents($url);

      $data = json_decode($response, true);
      if (!empty($data[0]['lat']) && !empty($data[0]['lon'])) {
        return [
          'latitude' => (float) $data[0]['lat'],
          'longitude' => (float) $data[0]['lon'],
        ];
      }
    

    return null;
  }
  public static function getNameFromGHN(string $endpoint, string $filterKey, $filterValue, $parentId = null): ?string
{
    if (empty(static::$token)) {
        throw new \Exception('GHN API token is not set.');
    }
    $url = "https://online-gateway.ghn.vn/shiip/public-api/master-data/{$endpoint}";

    $body = match ($endpoint) {
        'province' => [],
        'district' => ['province_id' => $parentId],
        'ward' => ['district_id' => $parentId],
        default => [],
    };

    $options = [
        "http" => [
            "method" => "POST",
            "header" => "Content-Type: application/json\r\nToken: " . static::$token . "\r\n",
            "content" => json_encode($body),
            "timeout" => 5,
        ]
    ];

    $context = stream_context_create($options);
    $result = @file_get_contents($url, false, $context);
	
    if ($result === false) {
        return null;
    }

    $data = json_decode($result, true);
  
    if (!empty($data['data'])) {
        $item = collect($data['data'])->firstWhere($filterKey, $filterValue);
        if ($item) {
            return match ($endpoint) {
                'ward' => $item['WardName'] ?? null,
                'district' => $item['DistrictName'] ?? null,
                'province' => $item['ProvinceName'] ?? null,
                default => null,
            };
        }
    }

    return null;
}

  public static function saveCoordinates(Store $store, StoreRequest $request)
  {
    self::setToken(env('GHN_TOKEN'));
    // ✅ Lấy địa chỉ đầy đủ để geocode
    $fullAddress = implode(', ', array_filter([
      $request->input('address'),
      self::getNameFromGHN('ward', 'WardCode', $request->input('ward')),
      self::getNameFromGHN('district', 'DistrictID', $request->input('city')),
      self::getNameFromGHN('province', 'ProvinceID', $request->input('state')),
      'Việt Nam',
    ]));
    // ✅ Gọi API để lấy tọa độ
    $coords = self::geocodeLocationIQ($fullAddress);
    if ($coords) {
      $store->latitude = $coords['latitude'];
      $store->longitude = $coords['longitude'];
      $store->save();
    }
  }
}
