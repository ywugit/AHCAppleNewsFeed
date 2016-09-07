<?php

/**
 * @file
 * Example: Get article
 */

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../apps/appleNews/pushAPI/autoload.php';

use \ChapterThree\AppleNewsAPI\PublisherAPI;
use \pushAPI\LOGGING;


$api_key_id = "b00422c1-b3f1-4917-82e4-373cb7474811";
$api_key_secret = "uqHwbBGYD0VKh0467qltp2tezi9P3/J2Ued3AgLUTk0=";
$endpoint = "https://news-api.apple.com";
$channelId = "14c12e14-d7b1-41f1-a61e-7b18501e8639";
//$articleId = "19fc463-07fa-42b7-a3ce-0310d70f8b9f";
$articleId = "bab5f86f-5957-42bb-a6d9-18c365a00ad5";
//214a4e2b-7537-4c0e-9034-8b114e1983c3
$log = new LOGGING();

// set path and name of log file (optional)
$log->lfile(__DIR__ . '/../logs/appleNewsFormat.txt');

$test = array();
if ($test == null)
{
	//echo "test is null\r\n";
}
else 
{
	//echo "test is not null: test:" . var_export($test, true);
}
$text = "База данни грешка";
//echo $text . "\r\n";
//echo json_encode($text,JSON_UNESCAPED_UNICODE);
//“
// write message to the log file
$log->lwrite('Test message1');
$PublisherAPI = new PublisherAPI(
  $api_key_id,
  $api_key_secret,
  $endpoint
);

// Fetches an article.
$response = $PublisherAPI->Get('/articles/{article_id}',
  [
    'article_id' => $articleId
  ]
);
//$log->lwrite('Test message1 ::' .var_export($response, true));
var_export($response);


