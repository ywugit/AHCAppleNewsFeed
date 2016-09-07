<?php

/**
 * @file
 * Example: Delete articles
 */

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../apps/appleNews/pushAPI/autoload.php';

use \pushAPI\API;
use \pushAPI\UTILS;

use \ChapterThree\AppleNewsAPI\PublisherAPI;

$api_key_id = "b00422c1-b3f1-4917-82e4-373cb7474811";
$api_key_secret = "uqHwbBGYD0VKh0467qltp2tezi9P3/J2Ued3AgLUTk0=";
$endpoint = "https://news-api.apple.com";
$channelId = "14c12e14-d7b1-41f1-a61e-7b18501e8639";
//$articleId = "f41c5b9c-9f48-4471-b8f9-31b7c7fc39ef";
$articleId = "d8391ee4-9c2a-4a33-94e5-b6ca9098b6b8";
$businessSecId = "4c9a9228-8203-37d2-9862-aea6f7dd978a";
$revision = "AAAAAAAAAAD//////////w==";
$readFromFile = false; //true then read from file, false then read from DB
//DevDB
$DBInfo = array("serverDB"=>"tdevcwadb1.test.ahc.belotechnologies.com", "usernameDB"=>"dmn_main", "passwordDB"=>"tRaPrA5a", "nameDB"=>"DMN_MAIN");

if ($readFromFile == true)
{
  $articleToDeleteFileName = __DIR__ . "/../files/history/articleIdsToDelete.txt";
  $articleIdsArray = file($articleToDeleteFileName, FILE_IGNORE_NEW_LINES);
}
else //read from DB
{
	$util = new UTILS();
	$articleIdsArray = $util->getAppleArticleIdsFromDB($DBInfo);
}
  echo "\r\n articleIds to delete: ";
  var_dump($articleIdsArray);


for ($x = 0; $x<count($articleIdsArray); $x++)
{
	
	$PublisherAPI = new PublisherAPI(
			$api_key_id,
			$api_key_secret,
			$endpoint
	);
	
   // Deletes an article.
   $response = $PublisherAPI->Delete('/articles/{article_id}',
   [
    'article_id' => $articleIdsArray[$x]
   ]
   );
   echo "article: " . $articleIdsArray[$x] . "deleted\r\n";
   var_dump($response);
}
