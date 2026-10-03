<?php

namespace App\Traits;

trait Geocodable
{
    public function reverseGeocode($lat, $lng)
    {
        $apiKey = "AIzaSyA3dbB5PCrN-1AW1ipmdM6RFrsdNdks3cU";

        $url = "https://maps.googleapis.com/maps/api/geocode/json?latlng=$lat,$lng&result_type=street_address|route|neighborhood&key=$apiKey";

        // $response = file_get_contents($url);

        // Use cURL instead of file_get_contents
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            curl_close($ch);
            return 'Address not found';
        }
        curl_close($ch);
        $data = json_decode($response, true);

        if ($data['status'] == "OK" && isset($data['results'][0]['formatted_address'])) {
            $address = $data['results'][0]['formatted_address'];
            return $address;
        } else {
            return 'Address not found';
        }
    }
}
