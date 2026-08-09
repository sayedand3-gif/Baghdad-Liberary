<?php

class PrayerService {

    /**
     * Get User Location based on IP Address
     */
    public static function getUserLocation() {
        $userIp = $_SERVER['REMOTE_ADDR']; 
        if ($userIp === '127.0.0.1' || $userIp === '::1') {
            $userIp = '156.211.0.1'; // Fallback IP for local testing
        }

        $json = @file_get_contents("http://ip-api.com/json/{$userIp}");
        $details = json_decode($json, true);

        if ($details && isset($details['status']) && $details['status'] === 'success') {
            return [
                'city' => $details['city'],
                'country' => $details['country']
            ];
        }

        return ['city' => 'Cairo', 'country' => 'Egypt'];
    }

    /**
     * Fetch prayer timings from Aladhan API
     */
    public static function getPrayerTimings($city, $country) {
        $encodedCity = urlencode($city);
        $encodedCountry = urlencode($country);
        $url = "https://api.aladhan.com/v1/timingsByCity?city={$encodedCity}&country={$encodedCountry}&method=5";

        $response = @file_get_contents($url);
        
        $defaultTimings = [
            'Fajr' => '04:12',
            'Sunrise' => '05:40',
            'Dhuhr' => '12:03',
            'Asr' => '15:35',
            'Maghrib' => '18:45',
            'Isha' => '20:12'
        ];

        if ($response !== false) {
            $data = json_decode($response, true);
            if (isset($data['code']) && $data['code'] === 200) {
                return $data["data"]["timings"];
            }
        }

        return $defaultTimings;
    }
}