<?php
require_once __DIR__ . '/apps/appleNews/pushAPI/autoload.php';

use \pushAPI\API;
use \pushAPI\UTILS;
use \pushAPI\LOGGING;
//test
$log = new LOGGING();
$log->lfile(__DIR__ . '/logs/appleNewsFormat.log');
//define resource information
$channelInfo = array("channelId"=>"",
		"endpoint"=>"https://news-api.apple.com",
		"api_key_id"=>"",
		"api_key_secret"=>""
); 

$util = new UTILS();
//ahc.applenewsformat.env=dev or prod or stage
$props = $util->getProps();
//env represents app running mode, prod/dev/stage
$serverType = $props["ahc.applenewsformat.serverType"];
$isPreview = $props["ahc.applenewsformat.isPreview"];
$debug = $props["ahc.applenewsformat.debug"];
$wordsCount = $props["ahc.applenewsformat.wordsCount"];
$DBInfo = $util->getDBInfo($serverType);
//var_dump("isPreview:" . $isPreview);
//var_dump("debug:" . $debug);
//var_dump( $DBInfo);


//$feedUrlArray = array("res-curate"=>"http://res.dallasnews.com/apps/flagship/curate.php","sportsday-app"=>"http://sportsday.dallasnews.com/feeds/atom/app?appTag&tagId=home");
$feedUrlArray = array("res-curate"=>"http://res.dallasnews.com/apps/flagship/curate.php");
//$feedUrlArray = array("sportsday-app"=>"http://sportsday.dallasnews.com/feeds/atom/app?appTag&tagId=home");


$fileDir = __DIR__ . "/files";
$historyFileDir = $fileDir . "/history";
$imageDir = $fileDir . "/images/";
$sectionFile = "section.txt";
$imageUrl;
$imageCaption;
$articleText;
$articleByline;
$imageName;
$articleInfo;


foreach($feedUrlArray as $feedName => $feedUrl)
{
   //load article history from files/history/article-history-flagship.txt
   $util = new UTILS();
   /*return a array of oject with $articleAppleId, $articleAppleVersion, $lastPubDate 
   * and indexed with articleAHCId
   */
   $feedHistoryFile = "article_history_$feedName.txt";
   $articleHistory = $util->getArticleHistory($historyFileDir, $feedHistoryFile);
   $articleHistoryNew = array();

   $sectionMap = $util->getSectionMap($fileDir, $sectionFile);
  
   $TLdmn = "dmn.json";

   //$bundleFiles = array("header.jpg", "galleryImage1.jpg", "galleryImage2.jpg", "galleryImage3.jpg", "bodyGraphic.png");
   $bundleFiles = array();


   $util = new UTILS();
   
   if($debug == "false")
   {     
     $simpleXml = simplexml_load_file($feedUrl);
   }
   else //debug == true
   {
	//$simpleXml = simplexml_load_file(__DIR__ . "/files/history/res-curate.xml");
	//$simpleXml = simplexml_load_file(__DIR__ . "/files/history/res-curate-small.xml");
	$simpleXml = simplexml_load_file(__DIR__ . "/files/history/$feedName-small.xml");
	//$simpleXml = simplexml_load_file(__DIR__ . "/files/history/$feedName.xml");
	//$simpleXml = simplexml_load_file(__DIR__ . "/files/history/error.xml");
   }

   $jsonTLdmn = json_decode(file_get_contents( $fileDir . '/' . $TLdmn)); //php object
   $reponse = null;
   
   $x = 1 ;
   $unseted = false;
  /*
   17 item is gallery, articleid 2642219
   http://www.dallasnews.com/news/community-news/best-southwest/headlines/20160208-desoto-bursting-with-pride-after-hometown-hero-von-miller-leads-broncos-to-super-bowl-victory.ece?appArticle
   5 item is image with + name http://www.dallasnews.com/news/20160209-mark-cuban-on-presidential-candidates-bing-about-everyone-else-is-not-leadership.ece?appArticle
   http://www.dallasnews.com/incoming/20160209-mark-cuban-in-sharknado-3.ece/IPHONEALTERNATES/w480I/Mark+Cuban+in+Sharknado+3

  */
  $loop = 16;
  foreach($simpleXml->children() as $article) {
   try 
   {
    if($unseted == true)
    {
  	  //if unset happened, we need to reload template, because some node could be removed.
      $jsonTLdmn = json_decode(file_get_contents(__DIR__ . '/files/' . $TLdmn)); //php object
      $unseted = false;
    }
	/*
  //use this loop to import specific story, comment this out if you want to loop all articles
  if($debug == "true") 
  {
   if($x != $loop)
   {  $x++;
 	continue;
   }// if($x  != 9)
  }*/
  $articleUrl =  $article->link['href'];
  $articleAHCId = $article['id'];
 // $articlePubDate = $article['published'];
  //2016-01-10T11:15:00+00:00
  date_default_timezone_set('America/Chicago');
  $articleModDate = date("c", strtotime($article['lastModified']));
  $articlePubDate = date("c", strtotime($article['published']));
  
  $log->lwrite('appleNewsBootstrap::articleUrl: ' . $articleUrl );
  $log->lwrite('appleNewsBootstrap::articleModDate: ' . $articleModDate );
  $util = new UTILS();
  //feedName could be res-curate, sportsday-app
  $appleSectionId = $util->getSectionId($articleUrl, $sectionMap, $feedName);
  
  $lastPubDateFromHistory = $articleHistory["$articleAHCId"]['lastPubDate'];
  $log->lwrite('appleNewsBootstrap::lastPubDateFromHistory: ' . $lastPubDateFromHistory );
  
	if( $articleHistory["$articleAHCId"] !=null && $lastPubDateFromHistory == $articleModDate )
	{
		//story was published before and there is no change, do nothing
		$log->lwrite('appleNewsBootstrap: It is existing/no change story, skip it:: ' );
		$arrayTemp = array("articleAppleId"=>$articleHistory["$articleAHCId"]['articleAppleId'], "articleAppleVersion"=>$articleHistory["$articleAHCId"]['articleAppleVersion'], "lastPubDate"=>$articleHistory["$articleAHCId"]['lastPubDate']);
		//$articleHistory["$articleAHCId"] = $arrayTemp;
		$articleHistoryNew["$articleAHCId"] = $arrayTemp;
		
		
	}
	else //story either was not published before or was published but updated, need to post to apple.
	{
     	$articleAHCInfo = $util->parseNitf($articleUrl, $imageDir, $wordsCount);
	    $jsonTLdmn->components[2]->text = $articleAHCInfo['articleTitle'];
	    
		if ( $articleAHCInfo['articleSum'] != null)
		{
	         $jsonTLdmn->components[3]->text = $articleAHCInfo['articleSum'];
	         $jsonTLdmn->metadata->excerpt = $articleAHCInfo['articleSum'];
		}
		else
		{
			unset($jsonTLdmn->components[3]);
			unset($jsonTLdmn->metadata->excerpt);
		}
		if ($articleAHCInfo['articleByline'] != null)
		{
		  $jsonTLdmn->components[4]->text = $articleAHCInfo['articleByline'] . ' | Dallas Morning News | ' . $articleAHCInfo['publishDate'];
		  $jsonTLdmn->metadata->authors[0] = $articleAHCInfo['articleByline'];
		}
		else
		{
			unset($jsonTLdmn->metadata->authors);
			//unset($jsonTLdmn->components[3]);
			//$unseted = true;
			$jsonTLdmn->components[4]->text = 'Dallas Morning News | ' . $articleAHCInfo['publishDate'];
		
		}
		$jsonTLdmn->components[5]->text = $articleAHCInfo['articleText'];
		//storyList
		if($articleAHCInfo['relatedStories'] != null)
		{
			$jsonTLdmn->components[6]->components[1]->text = $articleAHCInfo['relatedStories'];
		}//end of if($articleAHCInfo['storyList'] != null)
		else
		{
			unset($jsonTLdmn->components[6]);
			$unseted = true;
			
		}
		//end of storyList
		if($articleAHCInfo['imageList'] != null )
		{ 
			$items = array();
			$bundleFiles = array();
			foreach( $articleAHCInfo['imageList'] as $image)
			{
				$item = new \stdClass; // Instantiate stdClass object
				// $item->URL = $image['imageUrl'];
				$item->URL = "bundle://" . $image['imageName'];
				$item->caption = $image['imageCaption'];
				array_push($items, $item);
				// $bundleFiles = array($image['imageName']);
				array_push($bundleFiles, $image['imageName']);
			
			}
			if ($articleAHCInfo['thumbnail'] != null)
			{
				//$item = new \stdClass; // Instantiate stdClass object
				//$item->URL = "bundle://" . $articleAHCInfo['thumbnail']->imageThumbnailURL;
				//$item->caption = $articleAHCInfo['thumbnail']->imageThumbnailCaption;
				//$articleAHCInfo['thumbnail']['imageThumbnailName'];
				array_push($bundleFiles, $articleAHCInfo['thumbnail']['imageThumbnailName']);
			}
		  //if there is more than on pictures, use gallery component
		  if( count($articleAHCInfo['imageList']) > 1)
		  {
		   $jsonTLdmn->components[1]->items = $items;
		   $jsonTLdmn->components[1]->role = "gallery";
		   unset($jsonTLdmn->components[0]);
		   $unseted = true;
		  }// end of if( count($articleAHCInfo['imageList']) > 1)
		  else //there is only one story, use header component
		  {
		  	$imageName =  $articleAHCInfo['imageList'][0]['imageName'];
		  	$jsonTLdmn->components[0]->style->fill->URL = "bundle://" .  $imageName;
		  	$jsonTLdmn->components[0]->style->fill->caption = $articleAHCInfo['imageList'][0]['imageCaption'];
		  	unset($jsonTLdmn->components[1]);
		  	$unseted = true;
		  	
		  }
		}
		else
	    {
	    	    unset($jsonTLdmn->components[0]);
	    	    unset($jsonTLdmn->components[1]);
	    	    $unseted = true;
		   	$bundleFiles = array();
		   
		}
		//array_push($bundleFiles, "header.jpg"); //just for testing, delete this later
		// array_push($bundleFiles, "hea%2Bder"); //just for testing, delete this later
	    /*	
		if($articleAHCInfo['imageCaption'] != null)
		{
			
			$jsonTLDmnFlagship->components[4]->text = $articleAHCInfo['imageCaption'];
		}
		else
		{
			unset($jsonTLDmnFlagship->components[4]);
			
		}		
		*/
		$jsonTLdmn->title = $articleAHCInfo['articleTitle'];
		//$jsonTLdmn->subtitle = $articleAHCInfo['articleShortTitle'];
		//$jsonTLdmn->metadata->datePublished = $articleAHCInfo['lastPubDate'];
		$jsonTLdmn->metadata->datePublished = $articlePubDate;
		
		if($articleAHCInfo['thumbnail'] != null)
		{
		//"thumbnailURL": "bundle://header.jpg",
		 $jsonTLdmn->metadata->thumbnailURL = "bundle://" . $articleAHCInfo['thumbnail']["imageThumbnailName"];
		}
		else
		{
			unset($jsonTLdmn->metadata->thumbnailURL);
		}
		
		//$jsonTLdmn->metadata->datePublished = $articleAHCInfo['publishDate'];
		//$jsonTLdmn->metadata->dateModified = $articleAHCInfo['issueDate'];
		//do not use $articleAHCInfo['issueDate'];just because time format is not we want
		$jsonTLdmn->metadata->dateModified = $articleModDate;
		
		
		//$jsonTLdmn->metadata->thumbnailURL = $articleAHCInfo['articleTitle'];
		
		
		$articleAHCInfo['articleUrl'] = $articleUrl;
		$push = new API($channelInfo );
		
		$jsonTLdmn->components = array_values($jsonTLdmn->components);
		if(($articleHistory["$articleAHCId"] == null ) && $articleAHCId != null)
		{
			$util = new UTILS();	
	     	$isInDB = $util->isInDB($DBInfo, $articleAHCId);   
		}
		else
		{
			$isInDB = "false";
		}      
		$log->lwrite('appleNewsBootstrape: isInDB::' . $isInDB);
		//update story
	    if(($articleHistory["$articleAHCId"] !=null && $articleHistory["$articleAHCId"]['lastPubDate'] != $articleModDate) || $isInDB == "true" )
		{
			//story was published before and there is a change, update existing story.
			//if($articleHistory["$articleAHCId"]['articleAppleId'] != null)
			//{
				$util = new UTILS();			
				//$convertedJsonTLDmnFlagship = $util->convert_fancy_quotes(json_encode($jsonTLDmnFlagship));
				$convertedJsonTLdmn = $util->convert_fancy_quotes(json_encode($jsonTLdmn),JSON_UNESCAPED_UNICODE);
			    $response = $push->update_article($convertedJsonTLdmn, $appleSectionId, $bundleFiles, $articleHistory["$articleAHCId"]['articleAppleId'], $articleHistory["$articleAHCId"]['articleAppleVersion']);
		        $dbOpType = "update";
			//}
			//else
			//{
			//	$dbOpType = "noAction";
			//}
		}	
		else if( $articleHistory["$articleAHCId"] == NULL && $isInDB == "false" ) //new stories
		{
			$util = new UTILS();
			
			$convertedJsonTLdmn = $util->convert_fancy_quotes(json_encode($jsonTLdmn));
			$response = $push->post_article($convertedJsonTLdmn, $appleSectionId, $bundleFiles);
			$dbOpType = "insert";
		}//end of if( $articleHistory["$articleAHCId"] !=null && $articleHistory["$articleAHCId"]
		//var_dump($response);
		$log->lwrite('appleNewsBootstrape: response status::' . $response->data->state);
		//$log->lwrite('appleNewsBootstrape: response isPrevew(null is false, 1 is true)::' . $response->data->isPreview);
		//$log->lwrite('appleNewsBootstrape: response isSponsored::' . $response->data->isSponsored);
	   if($response != null)
	   {
		$articleAppleId = $response->data->id;
		$articleAppleVersion = $response->data->revision;
		if($articleAppleId != null)
		{
			$articleAppleAHCinfo = array("articleAHCId"=>$articleAHCId, "articleAppleId"=>$articleAppleId, "articleAppleVersion"=>$articleAppleVersion );
			$util = new UTILS();
			$util->saveArticleInfoToDB($DBInfo, $articleAppleAHCinfo, $dbOpType);
			$arrayTemp = array("articleAppleId"=>$articleAppleId, "articleAppleVersion"=>$articleAppleVersion, "lastPubDate"=>$articleModDate);
			//$articleHistory["$articleAHCId"] = $arrayTemp;
			$articleHistoryNew["$articleAHCId"] = $arrayTemp;
			$log->lwrite('appleNewsBootstrape: returned appleid::' . $articleAppleId . ' for ahcArtilceId: ' . $articleAHCId);
		}
		else
		{
			$log->lwrite('appleNewsBootstrape: returned appleid is null and ahcArticleId is ::' . $articleAHCId);
		}
		
			
	  }//end of response != null
	  else
	  {
		$log->lwrite('appleNewsBootstrap: response is null:: ');
	  }	// end of response !== null
	  //delete images downloade
	  if($serverType == "prod")
	  {
	  	$util->deleteImagesDownloaded();
	  }
	 }//end of if( $articleHistoryFlagShip["$articleAHCId"] !=null && $articleHistoryFlagShip
	// $log->lwrite('x:::::::::::::::' . $x );
	 $log->lwrite(':::::::::::::::::Another Story:::::::::::::::::::::::::::::::::' );
	 /*
    //use this loop to import $loop number story
    if($debug == "true")
    {           
  	  if ( $x == $loop) 
	  {	break;//for testing
	  }
    }
	$x++;
	*/
     }//end of try
     catch(Exception $e)
     {
     	$log->lwrite('appleNewsBootstrap:feedName:: ' . $feedName . '::::Exception:: ' . $e );
     }
   }//end of foreach($simpleXml->children() as $article) {
   //write $articleHistoryFlagShip to a file
   $util = new UTILS();
   $util->writeToFile($articleHistoryNew,  $historyFileDir, $feedHistoryFile);
  // var_export($articleHistoryNew) ;
}//end of foreach($feedUrlArray as $feedName => $feedUrl) 
    
