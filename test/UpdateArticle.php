<?php

/**
 * @file
 * Example: POST Article
 */

require_once __DIR__ . '/../vendor/autoload.php';

use \ChapterThree\AppleNewsAPI\PublisherAPI;

$api_key_id = "b00422c1-b3f1-4917-82e4-373cb7474811";
$api_key_secret = "uqHwbBGYD0VKh0467qltp2tezi9P3/J2Ued3AgLUTk0=";
$endpoint = "https://news-api.apple.com";
$channelId = "14c12e14-d7b1-41f1-a61e-7b18501e8639";
//$articleId = "226e5334-2db2-4071-9a13-363932354419";
$businessSecId = "4c9a9228-8203-37d2-9862-aea6f7dd978a";
$revision = "AAAAAAAAAAD//////////w==";
//AAAAAAAAAAAAAAAAAAAAAA==
$articleId = "6f67511c-cba7-414a-affe-b3df4924ad9e";
$PublisherAPI = new PublisherAPI(
  $api_key_id,
  $api_key_secret,
  $endpoint
);

// An optional metadata part may also be included, to provide additional
// non-Native data about the article. The metadata part also specifies any
// sections for the article, by URL. If this part is omitted,
// the article will be published to the channel's default section.


$metadata =  [
  'data' => [
    'isSponsored' => true,
    'links' => [
      'sections' => [
        $endpoint . '/sections/' . $businessSecId,
      ],
    ],
    'revision' => $revision // required.
  ],
];

// Updates an existing article.
// See $response variable to get a new revision ID.
$response = $PublisherAPI->post('/articles/{article_id}',
  [
    'article_id' => $articleId
  ],
  [
    // required. Apple News Native formatted JSON string.
    'json' => '{"version":"0.10.13","identifier":"10","title":"post changed Test article Business5 changed","language":"en","layout":{"columns":7,"width":1024},"components":[{"text":"Test article content Business5 changed\n\n","format":"markdown","role":"body"},{"URL":"bundle:\/\/article.png","role":"photo"}],"componentTextStyles":{"default":{}}}',
    // List of files to POST
    'files' => [
      'bundle://article.png' => __DIR__ . '/../files/images/article.png',
    ], // optional
    // JSON metadata string
    'metadata' => json_encode($metadata, JSON_UNESCAPED_SLASHES), // optional
  ]
);
echo 'article id: ' . $response->data->id . "\r\n" .
		'title: ' .      $response->data->title . "\r\n" .
		'revision: '   . $response->data->revision . "\r\n" .
		'links: '   .    $response->data->links->self . "\r\n" .
		'state: '   .    $response->data->state . "\r\n" .
		'isPreview: '  . $response->data->isPreview
		;