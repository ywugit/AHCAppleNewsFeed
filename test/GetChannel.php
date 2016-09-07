<?php

/**
 * @file
 * Example: GET Channel
 */
require_once __DIR__ . '/../vendor/autoload.php';


use \ChapterThree\AppleNewsAPI\PublisherAPI;

$api_key_id = "b00422c1-b3f1-4917-82e4-373cb7474811";
$api_key_secret = "uqHwbBGYD0VKh0467qltp2tezi9P3/J2Ued3AgLUTk0=";
$endpoint = "https://news-api.apple.com";
$channelId = "14c12e14-d7b1-41f1-a61e-7b18501e8639";

$PublisherAPI = new PublisherAPI(
  $api_key_id,
  $api_key_secret,
  $endpoint
);

// Fetches information about a channel.
$response = $PublisherAPI->Get('/channels/{channel_id}',
  [
    'channel_id' => '14c12e14-d7b1-41f1-a61e-7b18501e8639'
  ]
);

   echo 'channel id: ' . $response->data->id . "\r\n" .
		'channel name: ' .      $response->data->name . "\r\n" .
		'links self: '   .    $response->data->links->self . "\r\n" .
		'defaultSection: '   .    $response->data->links->defaultSection . "\r\n" 
		;