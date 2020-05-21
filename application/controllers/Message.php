<?php

class PushyAPI {
	
    static public function sendPushNotification($data, $to, $options) {
        // Insert your Secret API Key here
        $apiKey = "382f9b86160df028e37ef4058da82ac1d96e638177573d1bb63d0834e3cfb77a";
        // Default post data to provided options or empty array
        $post = $options ?: array();

        // Set notification payload and recipients
        $post['to'] = $to;
        $post['data'] = $data;

        // Set Content-Type header since we're sending JSON
        $headers = array(
            'Content-Type: application/json'
        );

        // Initialize curl handle
        $ch = curl_init();

        // Set URL to Pushy endpoint
        curl_setopt($ch, CURLOPT_URL, 'https://api.pushy.me/push?api_key=' . $apiKey);

        // Set request method to POST
        curl_setopt($ch, CURLOPT_POST, true);

        // Set our custom headers
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        // Get the response back as string instead of printing it
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Set post data as JSON
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post, JSON_UNESCAPED_UNICODE));

        // Actually send the push
        $result = curl_exec($ch);

        // Display errors
        if (curl_errno($ch)) {
            echo curl_error($ch);
        }

        // Close curl handle
        curl_close($ch);

        // Attempt to parse JSON response
        $response = @json_decode($result);

        // Throw if JSON error returned
        if (isset($response) && isset($response->error)) {
            //throw new Exception('Pushy API returned an error: ' . $response->error);
        }
    }
    
    static public function send_message($token, $notificationType, $showNotification, $title, $body, $action, $data) {
      $to = array($token);
      $data['title'] = $title;
      $data['body'] = $body;
	  $data['action'] = $action;
      $data['notification_type'] = intval($notificationType);
      $data['show_notification'] = intval($showNotification);
      $options = array(
        'notification' => array(
          'badge' => 1,
          'body'  => $body
        )
      );
      PushyAPI::sendPushNotification($data, $to, $options);
    }
}