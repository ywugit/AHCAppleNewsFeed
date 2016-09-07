<?php

/**
 * @file
 * Example: Delete articles
 */

require_once __DIR__ . '/../vendor/autoload.php';

use \ChapterThree\AppleNewsAPI\PublisherAPI;

$api_key_id = "b00422c1-b3f1-4917-82e4-373cb7474811";
$api_key_secret = "uqHwbBGYD0VKh0467qltp2tezi9P3/J2Ued3AgLUTk0=";
$endpoint = "https://news-api.apple.com";
$channelId = "14c12e14-d7b1-41f1-a61e-7b18501e8639";
//$articleId = "f41c5b9c-9f48-4471-b8f9-31b7c7fc39ef";
$articleId = "15d2475a-0ccb-4051-b669-bd121cd2b4fd";
$businessSecId = "4c9a9228-8203-37d2-9862-aea6f7dd978a";
$revision = "AAAAAAAAAAD//////////w==";


$PublisherAPI = new PublisherAPI(
  $api_key_id,
  $api_key_secret,
  $endpoint
);

// Deletes an article.
$response = $PublisherAPI->Delete('/articles/{article_id}',
  [
    'article_id' => $articleId
  ]
);
var_dump($response);