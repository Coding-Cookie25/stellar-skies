<?php

function getAccessToken() {
    static $cachedToken = null;
    static $expiresAt = null;

    if ($cachedToken && $expiresAt > time()) {
        return $cachedToken;
    }

    $apiKey = '';     // <-- put your real API key
    $apiSecret = '';  // <-- and secret here

    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => "https://test.api.amadeus.com/v1/security/oauth2/token",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => http_build_query([
            'grant_type' => 'client_credentials',
            'client_id' => $apiKey,
            'client_secret' => $apiSecret
        ]),
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/x-www-form-urlencoded'
        ]
    ]);

    $response = curl_exec($curl);
    $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);

    if ($status !== 200) {
        error_log("Failed to get access token. HTTP Status: $status");
        return null;
    }

    $data = json_decode($response, true);
    $cachedToken = $data['access_token'] ?? null;
    $expiresAt = time() + ($data['expires_in'] ?? 1700);

    return $cachedToken;
}

function getHotelOffers($cityCode, $checkInDate, $checkOutDate) {
    $token = getAccessToken();
    if (!$token) {
        return ['error' => 'Unable to authenticate with Amadeus API'];
    }

    $url = "https://test.api.amadeus.com/v3/shopping/hotel-offers?" . http_build_query([
        'cityCode' => $cityCode,
        'checkInDate' => $checkInDate,
        'checkOutDate' => $checkOutDate,
        'adults' => 1,
        'roomQuantity' => 1,
        'radius' => 50,
        'radiusUnit' => 'KM',
        'paymentPolicy' => 'NONE',
        'includeClosed' => false,
        'bestRateOnly' => true,
        'view' => 'FULL'
    ]);

    error_log("Making API request to: $url");

    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            "Authorization: Bearer $token",
            "Accept: application/vnd.amadeus+json"
        ]
    ]);

    $response = curl_exec($curl);
    $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

    error_log("API response status: $status");
    error_log("API response: " . substr($response, 0, 1000));

    curl_close($curl);

    if ($status !== 200) {
        return [
            'error' => "API request failed",
            'status' => $status,
            'url' => $url,
            'response' => json_decode($response, true)
        ];
    }

    $data = json_decode($response, true);

    if (isset($data['data'])) {
        $data['data'] = array_values(array_filter($data['data'], function ($hotel) {
            return isset($hotel['hotel']['rating']) && $hotel['hotel']['rating'] >= 3;
        }));
    }

    return $data;
}

function getHotelsByCity($cityCode) {
    $token = getAccessToken();
    if (!$token) {
        return ['error' => 'Unable to authenticate with Amadeus API'];
    }

    $url = "https://test.api.amadeus.com/v1/reference-data/locations/hotels/by-city?cityCode=$cityCode";

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Bearer $token"
    ]);

    $response = curl_exec($ch);
    curl_close($ch);

    return json_decode($response, true);
}
