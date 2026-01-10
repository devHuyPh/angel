<?php

namespace App\Helpers;

class FCMHelper
{
    public static function sendToToken($token, $title, $body, $url = null)
    {
        $keyFile = base_path('pvKey.json');
        if (!file_exists($keyFile)) return false;

        $keyData = json_decode(file_get_contents($keyFile), true);
        $scope = 'https://www.googleapis.com/auth/firebase.messaging';
        $jwt = self::makeJWT($keyData, $scope);
        $accessToken = self::getAccessToken($jwt, $keyData['token_uri']);
        if (!$accessToken) return false;

        $message = [
            'message' => [
                'token' => $token,
                'notification' => [
                    'title' => $title,
                    'body' => $body
                ],
                'webpush' => [
                    'fcm_options' => [
                        'link' => $url ?? ''
                    ]
                ]
            ]
        ];

        $ch = curl_init('https://fcm.googleapis.com/v1/projects/' . $keyData['project_id'] . '/messages:send');
        curl_setopt_array($ch, [
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $accessToken
            ],
            CURLOPT_POSTFIELDS => json_encode($message),
            CURLOPT_RETURNTRANSFER => true
        ]);
        $result = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        \Log::info("FCM push result", ['code' => $httpCode, 'response' => $result, 'token' => $token]);

        return $httpCode === 200;
    }

    private static function makeJWT($keyData, $scope)
    {
        $header = [
            'alg' => 'RS256',
            'typ' => 'JWT'
        ];
        $now = time();
        $payload = [
            'iss' => $keyData['client_email'],
            'scope' => $scope,
            'aud' => $keyData['token_uri'],
            'iat' => $now,
            'exp' => $now + 3600
        ];
        $base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode(json_encode($header)));
        $base64UrlPayload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode(json_encode($payload)));
        $data = $base64UrlHeader . '.' . $base64UrlPayload;
        openssl_sign($data, $signature, $keyData['private_key'], 'sha256');
        $base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));
        return $data . '.' . $base64UrlSignature;
    }

    private static function getAccessToken($jwt, $tokenUri)
    {
        $postFields = http_build_query([
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion' => $jwt
        ]);
        $ch = curl_init($tokenUri);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/x-www-form-urlencoded'
        ]);
        $result = curl_exec($ch);
        curl_close($ch);
        $data = json_decode($result, true);
        return $data['access_token'] ?? null;
    }
}
