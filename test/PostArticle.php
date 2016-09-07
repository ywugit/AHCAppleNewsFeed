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
$businessSecId = "4c9a9228-8203-37d2-9862-aea6f7dd978a";

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
  ],
];

//$file = __DIR__ . "/../files/Example_News_Article.json";
//$file = __DIR__ . "/../files/Example_Recipe_Article.json";
$file = __DIR__ . "/../files/Example_Longform_Travel_Article.json";


//$json = json_decode(file_get_contents($file));
$json = file_get_contents($file);

$jsonLine = str_replace(array("\n", "\r"),  '', $json);
echo 'json line : ' . $jsonLine . "\r\n";
// Publishes a new article to a channel.
$response = $PublisherAPI->Post('/channels/{channel_id}/articles',
  [
    'channel_id' => $channelId
  ],
  [
    // required. Apple News Native formatted JSON string.
  //'json' => ' {"version":"0.10.13","identifier":"10","title": "Test article Business 6","language":"en","layout":{"columns":7,"width":1024},"components":[{"text":"Test article content Business 6\n\n","format":"markdown","role":"body"},{"URL":"bundle://article.png","role":"photo"}],"componentTextStyles":{"default":{}}}',
  // 'json' => '{  "version": "1.0",  "identifier": "Apple_Demo",  "title": "Simple with Headline under Header Image",  "language": "en",  "layout": {    "columns": 7,    "width": 1024,    "margin": 70,    "gutter": 40  },  "components": [    {      "role": "header",      "layout": "headerImageLayout",      "style": {        "fill": {          "type": "image",          "URL": "bundle://header.jpg",          "fillMode": "cover",          "verticalAlignment": "center"        }      }    },    {      "role": "title",      "layout": "titleLayout",      "text": "Headline Under Image",      "textStyle": "titleStyle"    },    {      "role": "intro",      "layout": "introLayout",      "text": "Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto.",      "textStyle": "introStyle"    },    {      "role": "author",      "layout": "authorLayout",      "text": "Byline | Publisher | Date",      "textStyle": "authorStyle"    },    {      "role": "caption",      "text": "Sed ut perspi ciatis unde omnis iste natus error sit volupta accusa sic dolor emque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritas et quasi architecto.",      "layout": "captionLayout",      "textStyle": "captionStyle"    },    {      "role": "body",      "text": "Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur?\n\nQuis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur? At vero eos et accusamus et iusto odio dignissimos ducimus qui blanditiis praesentium voluptatum deleniti atque corrupti quos dolores et quas molestias excepturi sint occaecati cupiditate non provident, similique sunt in culpa qui officia deserunt mollitia animi, id est laborum et dolorum fuga. Et harum quidem rerum facilis est et expedita distinctio. Nam libero tempore, cum soluta nobis est eligendi optio cumque nihil impedit quo minus id quod maxime placeat facere possimus, omnis voluptas assumenda est, omnis dolor repellendus.\n\nTemporibus autem quibusdam et aut officiis debitis aut rerum necessitatibus saepe eveniet ut et voluptates repudiandae sint et molestiae non recusandae. Itaque earum rerum hic tenetur a sapiente delectus, ut aut reiciendis voluptatibus maiores alias consequatur aut perferendis doloribus asperiores repellat. Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt.",      "layout": "bodyLayout",      "textStyle": "bodyStyle"    },    {      "role": "heading1",      "layout": "heading1Layout",      "text": "Lorem Ipsum Sic Dolor",      "textStyle": "heading1Style"    },    {      "role": "body",      "text": "Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur? At vero eos et accusamus et iusto odio dignissimos ducimus qui blanditiis praesentium voluptatum deleniti atque corrupti quos dolores et quas molestias excepturi sint occaecati cupiditate non provident, similique sunt in culpa qui officia deserunt mollitia animi, id est laborum et dolorum fuga. Et harum quidem rerum facilis est et expedita distinctio.",      "layout": "bodyLayout",      "textStyle": "bodyStyle"    },    {      "role": "pullquote",      "layout": "pullquoteLayout",      "text": "�Sed ut perspi ciatis unde omnis iste natus error sit volupta accusa sic dolor emque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritas et quasi architecto.�",      "textStyle": "pullquoteStyle"    },    {      "role": "body",      "text": "Nam libero tempore, cum soluta nobis est eligendi optio cumque nihil impedit quo minus id quod maxime placeat facere possimus, omnis voluptas assumenda est, omnis dolor repellendus. Temporibus autem quibusdam et aut officiis debitis aut rerum necessitatibus saepe eveniet ut et voluptates repudiandae sint et molestiae non recusandae. Itaque earum rerum hic tenetur a sapiente delectus, ut aut reiciendis voluptatibus maiores alias consequatur aut perferendis doloribus asperiores repellat. Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt.\n\nNeque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur? At vero eos et accusamus et iusto odio dignissimos ducimus qui blanditiis praesentium voluptatum deleniti atque corrupti quos dolores et quas molestias excepturi sint occaecati cupiditate non provident, similique sunt in culpa qui officia deserunt mollitia animi, id est laborum et dolorum fuga. Et harum quidem rerum facilis est et expedita distinctio. Nam libero tempore, cum soluta nobis est eligendi optio cumque nihil impedit quo minus id quod maxime placeat facere possimus, omnis voluptas assumenda est, omnis dolor repellendus. Temporibus autem quibusdam et aut officiis debitis aut rerum necessitatibus saepe eveniet ut et voluptates repudiandae sint et molestiae non recusandae. Itaque earum rerum hic tenetur a sapiente delectus, ut aut reiciendis voluptatibus maiores alias consequatur aut perferendis doloribus asperiores repellat. Sed ut perspiciatis.\n\n",      "layout": "bodyLayout",      "textStyle": "bodyStyle"    }  ],  "componentTextStyles": {    "titleStyle": {      "textAlignment": "left",      "fontName": "HelveticaNeue-Bold",      "fontSize": 64,      "lineHeight": 74,      "textColor": "#000"    },    "introStyle": {      "textAlignment": "left",      "fontName": "HelveticaNeue-Medium",      "fontSize": 24,      "textColor": "#000"    },    "authorStyle": {      "textAlignment": "left",      "fontName": "HelveticaNeue-Bold",      "fontSize": 16,      "textColor": "#000"    },    "bodyStyle": {      "textAlignment": "left",      "fontName": "Georgia",      "fontSize": 18,      "lineHeight": 26,      "textColor": "#000"    },    "captionStyle": {      "textAlignment": "left",      "fontName": "HelveticaNeue-Medium",      "fontSize": 12,      "lineHeight": 17,      "textColor": "#000"    },    "heading1Style": {      "textAlignment": "left",      "fontName": "HelveticaNeue-Medium",      "fontSize": 28,      "lineHeight": 41,      "textColor": "#000"    },    "pullquoteStyle": {      "textAlignment": "left",      "fontName": "HelveticaNeue-Bold",      "fontSize": 28,      "lineHeight": 41,      "textColor": "#000"    }  },  "componentLayouts": {    "headerImageLayout": {      "columnStart": 0,      "columnSpan": 7,      "ignoreDocumentMargin": true,      "minimumHeight": "42vh"    },    "titleLayout": {      "columnStart": 0,      "columnSpan": 7,      "margin": {        "top": 30,        "bottom": 10      }    },    "introLayout": {      "columnStart": 0,      "columnSpan": 7,      "margin": {        "top": 15,        "bottom": 15      }    },    "authorLayout": {      "columnStart": 0,      "columnSpan": 7,      "margin": {        "top": 15,        "bottom": 15      }    },    "bodyLayout": {      "columnStart": 0,      "columnSpan": 5,      "margin": {        "top": 15,        "bottom": 15      }    },    "captionLayout": {      "columnStart": 5,      "columnSpan": 2,      "margin": {        "top": 15,        "bottom": 30      }    },    "heading1Layout": {      "columnStart": 0,      "columnSpan": 5,      "margin": {        "top": 15      }    },    "pullquoteLayout": {      "columnStart": 0,      "columnSpan": 7,      "margin": {        "top": 15,        "bottom": 15      }    }  }}',
  // 'json' => '{  "version": "0.10.13",  "identifier": "Apple_Demo",  "title": "Simple with Headline under Header Image",  "language": "en",  "layout": {    "columns": 7,    "width": 1024,    "margin": 70,    "gutter": 40  },  "components": [    {      "role": "header",      "layout": "headerImageLayout",      "style": {        "fill": {          "type": "image",          "URL": "bundle://header.jpg",          "fillMode": "cover",          "verticalAlignment": "center"        }      }    },    {      "role": "title",      "layout": "titleLayout",      "text": "Headline Under Image",      "textStyle": "titleStyle"    },    {      "role": "intro",      "layout": "introLayout",      "text": "Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto.",      "textStyle": "introStyle"    },    {      "role": "author",      "layout": "authorLayout",      "text": "Byline | Publisher | Date",      "textStyle": "authorStyle"    },    {      "role": "caption",      "text": "Sed ut perspi ciatis unde omnis iste natus error sit volupta accusa sic dolor emque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritas et quasi architecto.",      "layout": "captionLayout",      "textStyle": "captionStyle"    },    {      "role": "body",      "text": "Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur?\n\nQuis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur? At vero eos et accusamus et iusto odio dignissimos ducimus qui blanditiis praesentium voluptatum deleniti atque corrupti quos dolores et quas molestias excepturi sint occaecati cupiditate non provident, similique sunt in culpa qui officia deserunt mollitia animi, id est laborum et dolorum fuga. Et harum quidem rerum facilis est et expedita distinctio. Nam libero tempore, cum soluta nobis est eligendi optio cumque nihil impedit quo minus id quod maxime placeat facere possimus, omnis voluptas assumenda est, omnis dolor repellendus.\n\nTemporibus autem quibusdam et aut officiis debitis aut rerum necessitatibus saepe eveniet ut et voluptates repudiandae sint et molestiae non recusandae. Itaque earum rerum hic tenetur a sapiente delectus, ut aut reiciendis voluptatibus maiores alias consequatur aut perferendis doloribus asperiores repellat. Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt.",      "layout": "bodyLayout",      "textStyle": "bodyStyle"    },    {      "role": "heading1",      "layout": "heading1Layout",      "text": "Lorem Ipsum Sic Dolor",      "textStyle": "heading1Style"    },    {      "role": "body",      "text": "Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur? At vero eos et accusamus et iusto odio dignissimos ducimus qui blanditiis praesentium voluptatum deleniti atque corrupti quos dolores et quas molestias excepturi sint occaecati cupiditate non provident, similique sunt in culpa qui officia deserunt mollitia animi, id est laborum et dolorum fuga. Et harum quidem rerum facilis est et expedita distinctio.",      "layout": "bodyLayout",      "textStyle": "bodyStyle"    },    {      "role": "pullquote",      "layout": "pullquoteLayout",      "text": "�Sed ut perspi ciatis unde omnis iste natus error sit volupta accusa sic dolor emque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritas et quasi architecto.�",      "textStyle": "pullquoteStyle"    },    {      "role": "body",      "text": "Nam libero tempore, cum soluta nobis est eligendi optio cumque nihil impedit quo minus id quod maxime placeat facere possimus, omnis voluptas assumenda est, omnis dolor repellendus. Temporibus autem quibusdam et aut officiis debitis aut rerum necessitatibus saepe eveniet ut et voluptates repudiandae sint et molestiae non recusandae. Itaque earum rerum hic tenetur a sapiente delectus, ut aut reiciendis voluptatibus maiores alias consequatur aut perferendis doloribus asperiores repellat. Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt.\n\nNeque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur? At vero eos et accusamus et iusto odio dignissimos ducimus qui blanditiis praesentium voluptatum deleniti atque corrupti quos dolores et quas molestias excepturi sint occaecati cupiditate non provident, similique sunt in culpa qui officia deserunt mollitia animi, id est laborum et dolorum fuga. Et harum quidem rerum facilis est et expedita distinctio. Nam libero tempore, cum soluta nobis est eligendi optio cumque nihil impedit quo minus id quod maxime placeat facere possimus, omnis voluptas assumenda est, omnis dolor repellendus. Temporibus autem quibusdam et aut officiis debitis aut rerum necessitatibus saepe eveniet ut et voluptates repudiandae sint et molestiae non recusandae. Itaque earum rerum hic tenetur a sapiente delectus, ut aut reiciendis voluptatibus maiores alias consequatur aut perferendis doloribus asperiores repellat. Sed ut perspiciatis.\n\n",      "layout": "bodyLayout",      "textStyle": "bodyStyle"    }  ],  "componentTextStyles": {    "titleStyle": {      "textAlignment": "left",      "fontName": "HelveticaNeue-Bold",      "fontSize": 64,      "lineHeight": 74,      "textColor": "#000"    },    "introStyle": {      "textAlignment": "left",      "fontName": "HelveticaNeue-Medium",      "fontSize": 24,      "textColor": "#000"    },    "authorStyle": {      "textAlignment": "left",      "fontName": "HelveticaNeue-Bold",      "fontSize": 16,      "textColor": "#000"    },    "bodyStyle": {      "textAlignment": "left",      "fontName": "Georgia",      "fontSize": 18,      "lineHeight": 26,      "textColor": "#000"    },    "captionStyle": {      "textAlignment": "left",      "fontName": "HelveticaNeue-Medium",      "fontSize": 12,      "lineHeight": 17,      "textColor": "#000"    },    "heading1Style": {      "textAlignment": "left",      "fontName": "HelveticaNeue-Medium",      "fontSize": 28,      "lineHeight": 41,      "textColor": "#000"    },    "pullquoteStyle": {      "textAlignment": "left",      "fontName": "HelveticaNeue-Bold",      "fontSize": 28,      "lineHeight": 41,      "textColor": "#000"    }  },  "componentLayouts": {    "headerImageLayout": {      "columnStart": 0,      "columnSpan": 7,      "ignoreDocumentMargin": true,      "minimumHeight": "42vh"    },    "titleLayout": {      "columnStart": 0,      "columnSpan": 7,      "margin": {        "top": 30,        "bottom": 10      }    },    "introLayout": {      "columnStart": 0,      "columnSpan": 7,      "margin": {        "top": 15,        "bottom": 15      }    },    "authorLayout": {      "columnStart": 0,      "columnSpan": 7,      "margin": {        "top": 15,        "bottom": 15      }    },    "bodyLayout": {      "columnStart": 0,      "columnSpan": 5,      "margin": {        "top": 15,        "bottom": 15      }    },    "captionLayout": {      "columnStart": 5,      "columnSpan": 2,      "margin": {        "top": 15,        "bottom": 30      }    },    "heading1Layout": {      "columnStart": 0,      "columnSpan": 5,      "margin": {        "top": 15      }    },    "pullquoteLayout": {      "columnStart": 0,      "columnSpan": 7,      "margin": {        "top": 15,        "bottom": 15      }    }  }}',
   
	'json' => "$jsonLine",
	/*
	// List of files to POST Example_News_Article.json
    'files' => [
    'bundle://galleryImage1.jpg' => __DIR__ . '/../files/images/galleryImage1.jpg',
    'bundle://galleryImage2.jpg' => __DIR__ . '/../files/images/galleryImage2.jpg',
    'bundle://galleryImage3.jpg' => __DIR__ . '/../files/images/galleryImage3.jpg',
    'bundle://bodyGraphic.png' => __DIR__ . '/../files/images/bodyGraphic.png',
    'bundle://header.jpg' => __DIR__ . '/../files/images/header.jpg'
    ], // optional
    */
/*
    // List of files to POST Example_Recipe_Article.json
    'files' => [
    'bundle://header.jpg' => __DIR__ . '/../files/images/header.jpg',
     'bundle://bodyImage1.jpg' => __DIR__ . '/../files/images/bodyImage1.jpg'
    ], // optional
    */
    // List of files to POST Example_Longform_travel_Article.json
    'files' => [
    'bundle://galleryImage1.jpg' => __DIR__ . '/../files/images/galleryImage1.jpg',
    'bundle://galleryImage2.jpg' => __DIR__ . '/../files/images/galleryImage2.jpg',
    'bundle://galleryImage3.jpg' => __DIR__ . '/../files/images/galleryImage3.jpg',
    'bundle://header.jpg' => __DIR__ . '/../files/images/header.jpg',
    'bundle://bodyImage1.jpg' => __DIR__ . '/../files/images/bodyImage1.jpg',
    'bundle://mosaicImage1.jpg' => __DIR__ . '/../files/images/mosaicImage1.jpg',
    'bundle://mosaicImage2.jpg' => __DIR__ . '/../files/images/mosaicImage2.jpg',
    	'bundle://mosaicImage3.jpg' => __DIR__ . '/../files/images/mosaicImage3.jpg'
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