<?php

/*
* To change this license header, choose License Headers in Project Properties.
* To change this template file, choose Tools | Templates
* and open the template in the editor.
*/

/**
 * Description of FCM
 *
 * @author sushil
 */

namespace App\Traits;

use Google_Client;
use GuzzleHttp\Client;

trait FCM
{

    function sendGroupNotification($flag, $title, $content)
    {

        $client = new Client();

        $res = $client->request(
            'POST',
            config('app.NotificationAppUrl') . '/topticmessage',
            [
                'headers' =>
                [
                    'Content-Type' => "application/x-www-form-urlencoded"
                ],
                'form_params' =>
                [
                    // 'schoolid' => $schoolid,
                    'flag' => $flag,
                    'title' => $title,
                    'body' => $content,
                    'tag' => 'na',
                    'authKey' => config('app.FCMAuth')
                ]
            ]
        );
        if ($res->getStatusCode() === 200) {
            return true;
        } else {
            return false;
        }
    }

    function sendIndividualNotification($fcmid, $title, $flag, $content)
    {


        $client = new Client();

        $res = $client->request(
            'POST',
            config('app.NotificationAppUrl') . '/studentmessage',
            [
                'headers' =>
                [
                    'Content-Type' => "application/x-www-form-urlencoded"
                ],
                'form_params' =>
                [
                    'fcmid' => $fcmid,
                    'title' => $title,
                    'body' => $content,
                    'flag' => $flag,
                    'tag' => 'na',
                    'authKey' => config('app.FCMAuth')
                ]
            ]
        );
        if ($res->getStatusCode() === 200) {
            return true;
        } else {
            return false;
        }
    }

    function unique_multidim_array($array, $key)
    {
        $temp_array = array();
        $i = 0;
        $key_array = array();

        foreach ($array as $val) {
            if (!in_array($val[$key], $key_array)) {
                $key_array[$i] = $val[$key];
                $temp_array[$i] = $val;
            }
            $i++;
        }
        return $temp_array;
    }

    public function runFirebaseNotification($fcmid, $topic, $title, $body, $data)
    {
        $isProduction = config('app.env') === 'production';
        $credentialsPath = $isProduction ? config('app.prod-fcm_json') : config('app.dev-fcm_json');

        if (!file_exists($credentialsPath)) {
            throw new \Exception("FCM credentials file not found: {$credentialsPath}");
        }
        $fcmConfig = json_decode(
            file_get_contents($credentialsPath),
            true
        );

        $projectId = $fcmConfig['project_id'] ?? null;

        if (!$projectId) {
            throw new \Exception('project_id not found in FCM credentials file');
        }

        $url = "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send";

        if (!$projectId || !$credentialsPath) {
            return false;
        }

        $client = new Google_Client();
        $client->setAuthConfig($credentialsPath);
        $client->addScope('https://www.googleapis.com/auth/firebase.messaging');

        $token = $client->fetchAccessTokenWithAssertion();
        if (!isset($token['access_token'])) {
            throw new \Exception('Failed to fetch access token from Firebase.');
        }

        $headers = [
            'Authorization: Bearer ' . $token['access_token'],
            'Content-Type: application/json',
        ];

        // Build the message payload
        $message = [];

        if ($fcmid) {
            $message['token'] = $fcmid;
        }
        if ($topic) {
            $message['topic'] = $topic;
        }
        if ($title && $body) {
            $message['notification'] = [
                'title' => $title,
                'body'  => $body,
            ];
        }
        if ($data) {
            $message['data'] = $data;
        }

        // Add Android-specific options
        $message['android'] = [
            'priority' => 'high',
            'notification' => [
                'sound' => 'default',
                'click_action' => 'FLUTTER_NOTIFICATION_CLICK', // or your app-specific intent
            ],
        ];

        // Add iOS-specific (APNs) options
        $message['apns'] = [
            'headers' => [
                'apns-priority' => '10',
            ],
            'payload' => [
                'aps' => [
                    'sound' => 'default',
                    'content-available' => 1,
                ],
            ],
        ];


        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['message' => $message]));

        $response = curl_exec($ch);
        if ($response === false) {
            $error = curl_error($ch);
            curl_close($ch);
            throw new \Exception("Curl error: {$error}");
        }
        curl_close($ch);
    }

    private function getFcmAccessToken(): array
    {
        $isProduction    = config('app.env') === 'production';
        $credentialsPath = $isProduction ? config('app.prod-fcm_json') : config('app.dev-fcm_json');

        if (! file_exists($credentialsPath)) {
            throw new \Exception("FCM credentials file not found: {$credentialsPath}");
        }

        $fcmConfig = json_decode(file_get_contents($credentialsPath), true);
        $projectId = $fcmConfig['project_id'] ?? null;

        if (! $projectId) {
            throw new \Exception('project_id not found in FCM credentials file');
        }

        $client = new Google_Client();
        $client->setAuthConfig($credentialsPath);
        $client->addScope('https://www.googleapis.com/auth/firebase.messaging');

        $token = $client->fetchAccessTokenWithAssertion();

        if (! isset($token['access_token'])) {
            throw new \Exception('Failed to fetch access token from Firebase.');
        }

        return [
            'access_token' => $token['access_token'],
            'project_id'   => $projectId,
        ];
    }

    public function sendMulticast(array $tokens, string $title, string $body, array $data = []): array
    {
        // Sanitize: trim, drop empty/short strings, dedupe
        $tokens = array_values(array_unique(array_filter(
            array_map('trim', $tokens),
            fn($t) => is_string($t) && strlen($t) > 20
        )));

        $report = [
            'success'        => 0,
            'failure'        => 0,
            'invalid_tokens' => [],
            'responses'      => [],
        ];

        if (empty($tokens)) {
            return $report;
        }

        // One OAuth fetch for the entire batch
        $auth        = $this->getFcmAccessToken();
        $accessToken = $auth['access_token'];
        $projectId   = $auth['project_id'];

        foreach ($tokens as $token) {
            try {
                $result = $this->runFirebaseNotificationSendMulticast(
                    $token,
                    null,
                    $title,
                    $body,
                    $data,
                    $accessToken,
                    $projectId
                );

                if ($result['status'] === 200) {
                    $report['success']++;
                    continue;
                }

                $report['failure']++;
                $report['responses'][$token] = $result; // failures only — keep memory low

                $errorStatus = $result['body']['error']['status'] ?? null;

                // Only truly dead tokens
                if (in_array($errorStatus, ['UNREGISTERED', 'NOT_FOUND'], true)) {
                    $report['invalid_tokens'][] = $token;
                }
            } catch (\Throwable $e) {
                $report['failure']++;
                $report['responses'][$token] = ['exception' => $e->getMessage()];
            }
        }

        return $report;
    }

    public function runFirebaseNotificationSendMulticast(
        $fcmid,
        $topic,
        $title,
        $body,
        $data,
        ?string $accessToken = null,
        ?string $projectId = null
    ) {
        if (! $accessToken || ! $projectId) {
            $auth        = $this->getFcmAccessToken();
            $accessToken = $auth['access_token'];
            $projectId   = $auth['project_id'];
        }

        $url = "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send";

        $headers = [
            'Authorization: Bearer ' . $accessToken,
            'Content-Type: application/json',
        ];



        // ---- Payload: unchanged from your current version ----
        $message = [];

        if ($fcmid) {
            $message['token'] = $fcmid;
        }
        if ($topic) {
            $message['topic'] = $topic;
        }
        if ($title && $body) {
            $message['notification'] = [
                'title' => $title,
                'body'  => $body,
            ];
        }

        if ($data) {
            $data = array_map(function ($value) {
                if (is_bool($value)) {
                    return $value ? '1' : '0';
                }
                if (is_scalar($value) || $value === null) {
                    return (string) $value;
                }
                return json_encode($value);
            }, $data);

            $message['data'] = $data;
        }

        $message['android'] = [
            'priority' => 'high',
            'notification' => [
                'sound'        => 'default',
                'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
            ],
        ];

        $message['apns'] = [
            'headers' => [
                'apns-priority' => '10',
            ],
            'payload' => [
                'aps' => [
                    'sound'             => 'default',
                    'content-available' => 1,
                ],
            ],
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['message' => $message]));

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);


        if ($response === false) {
            $error = curl_error($ch);
            curl_close($ch);
            throw new \Exception("Curl error: {$error}");
        }

        curl_close($ch);

        return [
            'status'   => $httpCode,
            'body'     => json_decode($response, true),
            'target'   => $fcmid ?: $topic,
        ];
    }
}
