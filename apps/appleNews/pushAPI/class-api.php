<?php
namespace pushAPI;


require_once __DIR__ . '/../../../vendor/autoload.php';

use \ChapterThree\AppleNewsAPI\PublisherAPI;
//use \pushAPI\LOGGING;
 
/**
 * This class will post provided specified format articles to a channel using
 * the API.
 *
 * @since 0.2.0
 */
class API {
	
	private $api_key_id;
	private $api_key_secret;
	private $endpoint;
	private $channelId;
	private $PublisherAPI;
	private $isSponsored = false;
	
	
	private $isPreview;
	/**
	 * Constructor.
	 */
	function __construct( $channelInfo  ) {
		$log = new LOGGING();
		
		// set path and name of log file (optional)
		$log->lfile(__DIR__ . '/../../../logs/appleNewsFormat.log');
		$this->api_key_id = $channelInfo['api_key_id'];
		$this->api_key_secret = $channelInfo['api_key_secret'];
		$this->endpoint = $channelInfo['endpoint'];
		$this->channelId = $channelInfo['channelId'];
		$this->PublisherAPI = new PublisherAPI($this->api_key_id, $this->api_key_secret, $this->endpoint);

		$util = new UTILS();
		$props = $util->getProps();		
		$this->isPreview = $props["ahc.applenewsformat.isPreview"];
		$log->lwrite("class-api:constructor: isPreview:: " . $this->isPreview);
		$this->isSponsored = false;
	    
	}

	
	public function get_channel( ) {
        // Fetches information about a channel.
        $response = $this->PublisherAPI->Get('/channels/{channel_id}',
        [
          'channel_id' =>$this->channelId,
        ]
        );

		return $response;
	}
	public function get_sections( ) {
		// Fetches information about a section.
	   $response = $this->PublisherAPI->Get('/channels/{channel_id}/sections',
       [
         'channel_id' =>$this->channelId,
       ]
       );
	
		return $response;
	}
	public function delete_article($articleId ) 
	{
		$log = new LOGGING();
		
		// set path and name of log file (optional)
		$log->lfile(__DIR__ . '/../../../logs/appleNewsFormat.log');
				
      $response = $this->PublisherAPI->Delete('/articles/{article_id}',
       [
        'article_id' => $articleId
       ]
      );
       var_dump($response);
        //var_dump($http_response_header);
		return $response;
	}
	public function post_article($jsonLine, $sectionId, $bundleFiles) 
	{
		$log = new LOGGING();
		
		// set path and name of log file (optional)
		$log->lfile(__DIR__ . '/../../../logs/appleNewsFormat.log');
		
		$metadata =  [
		'data' => [
		'isSponsored' => $this->isSponsored,
		'isPreview' => $this->isPreview,
		'links' => [
		'sections' => [
		$this->endpoint . '/sections/' . $sectionId,
		],
		],
		],
		];
		$bundleArray = array();
		
		if ($bundleFiles != null)
		{
			 $log->lwrite("class-api:post_article bundleFiles is Not null ");
		   //'bundle://bodyGraphic1.png' => __DIR__ . '/files/images/bodyGraphic1.png',
		  foreach ( $bundleFiles  as $bundleFile) {
			$bundleTemp = __DIR__ . "/../../../files/images/" . $bundleFile;
			$bundleFileTemp = "bundle://" . $bundleFile;
			$bundleArray[$bundleFileTemp] = $bundleTemp ;
		  }
		  $response = $this->PublisherAPI->Post('/channels/{channel_id}/articles',
		  		[
		  		'channel_id' => $this->channelId
		  		],
		  		[
		  		// required. Apple News Native formatted JSON string.
		  		'json' => "$jsonLine",
		  		// List of files to POST
		  		'files' => $bundleArray,
		  		// optional
		  		// JSON metadata string
		  		'metadata' => json_encode($metadata, JSON_UNESCAPED_SLASHES), // optional
		  		]
		  );
		}//end of if ($bundleFiles != null)
		else
		{
			//"URL": "bundle://
			$log->lwrite("class-api:post_article bundleFiles is null ");
			//$log->lwrite("class-api:post_article bundleFiles is null, before replace : dump: " . var_export($jsonLine));
			//$jsonLine = str_replace('"' . 'URL' . '": ' . '"bundle://', '', $jsonLine);
			//$log->lwrite("class-api:post_article bundleFiles is null, after replace: dump: " . var_export($jsonLine));
			
			$response = $this->PublisherAPI->Post('/channels/{channel_id}/articles',
					[
					'channel_id' => $this->channelId
					],
					[
					// required. Apple News Native formatted JSON string.
					'json' => "$jsonLine",
					// optional
					// JSON metadata string
					'metadata' => json_encode($metadata, JSON_UNESCAPED_SLASHES), // optional
					]
			);
		}
		//var_dump($bundleArray);
		 $log->lwrite("class-api:post_article:bundleArray: " .  var_export($bundleArray, true));
		 $log->lwrite("class-api:post_article:json posted:: " . $jsonLine );
		 $log->lwrite("class-api:post_article:Done with post of articleAppleId:: " . $response->data->id);
		 //$log->lwrite("class-api:post_article:isSponosered:: " . $this->isSponsored );
		 //$log->lwrite("class-api:post_article:isPreview:: " . $this->isPreview );
		 	
		return $response;
	}	//end of post_article
	public function post_article_from_file($templateFile, $sectionId, $bundleFiles) 
	{
		$log = new LOGGING();
		
		// set path and name of log file (optional)
		$log->lfile(__DIR__ . '/../../../logs/appleNewsFormat.log');
		
         $metadata =  [
            'data' => [
		    'isSponsored' => $this->isSponsored,
		    'isPreview' => $this->isPreview,
            'links' => [
            'sections' => [
             $this->endpoint . '/sections/' . $sectionId,
           ],
          ],
         ],
        ];
        
        $bundleArray = array();
        //'bundle://bodyGraphic1.png' => __DIR__ . '/files/images/bodyGraphic1.png',
        foreach ( $bundleFiles  as $bundleFile) {
          	$bundleTemp = __DIR__ . "/../../../files/images/$bundleFile";
          	$bundleFileTemp = "bundle://$bundleFile";
          	$bundleArray[$bundleFileTemp] = $bundleTemp ;
        }
        var_dump($bundleArray);
        
        //$json = json_decode(file_get_contents($file));
        $json = file_get_contents(__DIR__ . "/../../../files/$templateFile");

        $jsonLine = str_replace(array("\n", "\r"),  '', $json);
       // echo 'json line : ' . $jsonLine . "\r\n";
        // Publishes a new article to a channel.
        $response = $this->PublisherAPI->Post('/channels/{channel_id}/articles',
         [
            'channel_id' => $this->channelId
          ],
         [
      // required. Apple News Native formatted JSON string.   
	  'json' => "$jsonLine",
	   // List of files to POST
       'files' => $bundleArray, 
        // optional
        // JSON metadata string
       'metadata' => json_encode($metadata, JSON_UNESCAPED_SLASHES), // optional
      ]
     );	
     return $response;
	}	//end of post_article_from_file
	public function update_article($jsonLine, $sectionId, $bundleFiles, $articleId, $revision) 
	{
		$log = new LOGGING();
		
		// set path and name of log file (optional)
		$log->lfile(__DIR__ . '/../../../logs/appleNewsFormat.log');
		
		$metadata =  [
		'data' => [
	       'isSponsored' => $this->isSponsored,
		   'isPreview' => $this->isPreview,
		   'links' => [
		   'sections' => [
		   $this->endpoint . '/sections/' . $sectionId,
		   ],
		  ],
		  'revision' => $revision // required.
		 ],
		];
	
		$bundleArray = array();
		//'bundle://bodyGraphic1.png' => __DIR__ . '/files/images/bodyGraphic1.png',
		foreach ( $bundleFiles  as $bundleFile) {
			$bundleTemp = __DIR__ . "/../../../files/images/$bundleFile";
			$bundleFileTemp = "bundle://$bundleFile";
			$bundleArray[$bundleFileTemp] = $bundleTemp ;
		}
		var_dump($bundleArray);
	
		//$json = json_decode(file_get_contents($file));
		//$json = file_get_contents(__DIR__ . "/../../../files/$templateFile");
	
		//$jsonLine = str_replace(array("\n", "\r"),  '', $json);
		//echo 'json line : ' . $jsonLine . "\r\n";
		// Publishes a new article to a channel.
		$response = $this->PublisherAPI->post('/articles/{article_id}',
				[
				  'article_id' => $articleId
				],
				[
				// required. Apple News Native formatted JSON string.
		        'json' => "$jsonLine",
				// List of files to POST
				'files' => $bundleArray,
				// optional
				// JSON metadata string
				'metadata' => json_encode($metadata, JSON_UNESCAPED_SLASHES), // optional
				]
		);
		echo "\r\n class-api: update_article: Done with update of articleid: " . $articleId . "\r\n";
		//$log->lwrite("class-api:update_article:bundleArray: " .  var_export($bundleArray, true));
		$log->lwrite("class-api:update_article:json updated:: " . $jsonLine );
		$log->lwrite("class-api:update_article:Done with post of articleAppleId:: " . $response->data->id);
		$log->lwrite("class-api:update_article:isSponosered:: " . $this->isSponsored );
		$log->lwrite("class-api:update_article:isPreview:: " . $this->isPreview );
			
	    return $response;
	}	//end of update_article
	
	
}//end of class
