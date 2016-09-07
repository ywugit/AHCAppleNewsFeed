<?php
namespace pushAPI;


require_once __DIR__ . '/../../../vendor/autoload.php';

//use \ChapterThree\AppleNewsAPI\PublisherAPI;
use League\HTMLToMarkdown\HtmlConverter;
//use \pushAPI\LOGGING;

 
/**
 * This class will post provided specified format articles to a channel using
 * the UTILS.
 *
 * @since 0.2.0
 */
class UTILS {
	
	private $articleUrl;
	private $log;
	
	/**
	 * Constructor.
	 */
	function __construct(   ) {
		
		
	}
	
	public function parseNitf($articleUrl, $imageDir, $wordsCount ) {
		$log = new LOGGING();
		
		// set path and name of log file (optional)
		$log->lfile(__DIR__ . '/../../../logs/appleNewsFormat.log');
		
        // Fetches information about a article.
		$converter = new HtmlConverter();
	    $this->articleUrl = $articleUrl;
	    $storyContentLink = preg_replace("/\?appArticle/", "", $articleUrl);
	    $log->lwrite('class-utils: parseNitf::storyContentLink: ' . $storyContentLink);
        $articleAHCInfo = array();
		$nitf = simplexml_load_file($articleUrl);
		
		foreach($nitf->children() as $content)
		{
			if($content->getName() == "head")
			{
				$issueDate = $content->docdata->{"date.issue"}['norm'];
				date_default_timezone_set('America/Chicago');
				$issueDate = date("F j, Y g:i a", strtotime($issueDate));
				//publishDate is 2016-02-08T15:42:25-0600
				$publishDate = $content->docdata->{"date.published"}['norm'];
				//March 28, 2016 9:49 am
				//date_default_timezone_set('America/Chicago');
				$publishDate = date("F j, Y g:i a", strtotime($publishDate));
				//$log->lwrite('class-utils: parseNitf::publishDate from xml: ' . $publishDate);
				
				
			}
			else if($content->getName() == "body")
			{    
				$articleTitle = trim($content->{"body.head"}->hedline->hl1);
			    $articleSum = trim($content->{"body.head"}->abstract);
			    $articleByline = trim($content->{"body.head"}->byline);
			    $articleText =  trim($content->{"body.content"}->text) ;
			    //DMN asked to replace text with abstract or limited text with Continue read link"
               /*
			   if( $articleSum != null  && str_word_count($articleSum) > 100)
			    {
			     	$articleText = $articleSum . " " . "<a href='$storyContentLink'>Continue read...</a>";
			     	$log->lwrite('class-utils: parseNitf::articleText from ArticleSum: ' . $articleText);
			    }
			    else 
			    {*/
			    	    $articleText = $this->trunc($articleText, $wordsCount) . " " . "<a href='$storyContentLink'>Continue reading</a>";
			    	    $log->lwrite('class-utils: parseNitf::articleTextFromTrunc: ' . $articleText);
			    	    
			   //  }
			     
			    //story relation
			    $storyArray = $content->{"body.content"}->{"related-articles"}->{"related-article"};
			    if( $storyArray != null)
			    {
			      	$storyList = array();
			    
			    	    foreach ( $storyArray as $article)
			    	    {
			    	       $story = array();
			    	    	   $storyId = trim($article->id);
			    	    	   $storyTitle = trim($article->title);
			    	    	   $storyUrl = trim($article->url);
			    	    	   $story = array("storyId"=>$storyId, "storyTitle"=>$storyTitle, "storyUrl"=>$storyUrl);
			    	    	  // $log->lwrite('class-utils: parseNitf::storyId from xml: ' . $storyId);
			    	    	  // $log->lwrite('class-utils: parseNitf::storyUrl from xml: ' . $storyUrl);
			    	    	   //$log->lwrite('class-utils: parseNitf::storyTitle: ' . $storyTitle);
			    	    	   
			    	    	   array_push($storyList, $story);
			    	    }
			    }//end of if($storyArray != null)
			    	
			    //end of story relation
			    //get Thumbnail
			    
			    $imagesArray = $content->{"body.content"}->media;
			    var_export($imagesArray);
			    //if( $content->{"body.content"}->media != null)
			    $imageThumbnailArray = array();
			    	if( count($imagesArray) > 0)
			    {
			    	  //var_dump("why i am here1");
			    	  
			    	  $imageThumbnailURL = $content->{"body.content"}->media[0]->{"media-reference"}[0]['source']; 
			    	  $imageThumbnailURL = str_replace(array("+"),  '%2B', $imageThumbnailURL);
			    	  $imageThumbnailName = $imageThumbnailURL;
			    	  $imageThumbnailName = preg_replace('/\?.*/',  '', $imageThumbnailName);
			    	  $imageThumbnailName = preg_replace('/.*\//',  '', $imageThumbnailName);
			    	  $imageThumbnailName = str_replace(array("\n", "\r"),  '', $imageThumbnailName);
			    	  $imageThumbnailName = str_replace(array("+"),  '%20', $imageThumbnailName);
			    	  shell_exec(" cd $imageDir && curl -o $imageThumbnailName $imageThumbnailURL ");
			    	 // $imageThumbnailNameTN = $imageThumbnailName ."TN";
			    	  $imageThumbnailCaption = $content->{"body.content"}->media[0]->{"media-caption"}[0]['source'];
			    	  $imageThumbnailArray = array("imageThumbnailName"=>$imageThumbnailName, "imageThumbnailURL"=>$imageThumbnailURL, "imageThumbnailCaption"=>$imageThumbnailCaption);

			    	 // $log->lwrite('class-utils: parseNitf::imageThumbnailName from xml: ' . $imageThumbnailName);
			    	 // $log->lwrite('class-utils: parseNitf::imageThumbnailURL from xml: ' . $imageThumbnailURL);
			    	 // $log->lwrite('class-utils: parseNitf::imageThumbnailCaption: ' . $imageThumbnailCaption);
			    	  
			    }
			    
			    
			   // $imageUrl = $content->{"body.content"}->media->{"media-reference"}['source'];
			    //get multi images in the story.			    
			    if( $imagesArray != null)
			    {
			    	  $imageList = array();
			    	  
			    	  foreach ( $imagesArray as $media)
			    	  {
			    	   $image = array();
			    	   $imageUrl = $media->{"media-reference"}[1]['source'];
			    	   $imageUrl = str_replace(array("+"),  '%2B', $imageUrl);
			    	   $log->lwrite('class-utils: parseNitf::imageUrLLLL: ' . $imageUrl);
			      // $imageName = shell_exec("echo $imageUrl | sed -e 's/.*\///' | sed -e 's/\?.*//'");
			    	   $imageName = $imageUrl;
			    	   $imageName = preg_replace('/\?.*/',  '', $imageName);
			    	   $imageName = preg_replace('/.*\//',  '', $imageName);
			    	   //$imageName = shell_exec("echo $imageName | sed -e 's/\?.*//'");
			      // $log->lwrite('class-utils: parseNitf::imageName1: ' . $imageName);
			      // $imageName = shell_exec("echo $imageName | sed -e 's/.*\///' ");
			      // $log->lwrite('class-utils: parseNitf::imageName2: ' . $imageName);
			       //remove newline. 
			      // $log->lwrite('class-utils: parseNitf::imageNameeee: ' . $imageName);
			       $imageName = str_replace(array("\n", "\r"),  '', $imageName);
			       $imageName = str_replace(array("+"),  '%20', $imageName);
			       $imageCaption = trim($media->{"media-caption"});
			       if($imageCaption == null)
			       {
			    	      $imageCaption = "There is no image caption";
			    	     // $log->lwrite('class-utils: parseNitf::imageCaption is null: ');
			        }
			      //  $log->lwrite('class-utils: parseNitf::imageName from xml: ' . $imageName);
			      //  $log->lwrite('class-utils: parseNitf::imageUrl from xml: ' . $imageUrl);
			      //  $log->lwrite('class-utils: parseNitf::imageCaption: ' . $imageCaption);
				    
			        //shell_exec(" cd $imageDir && curl -O $imageUrl ");
			        shell_exec(" cd $imageDir && curl -o $imageName $imageUrl ");
			        
			        $image = array("imageName"=>$imageName, "imageUrl"=>$imageUrl, "imageCaption"=>$imageCaption);
			        array_push($imageList, $image);
			    	  } //end of foreach ( $imageArray as $media)
			    }//end of if( $imagesArray != null)
			   // $this->download($imageUrl, $imageDir );
			    sleep(10);//5 seconds
			}//end of else if($content->getName() == "body")
		} //end of foreach($nitf->children() as $content)
			
		//$imageNameOrg = shell_exec("echo $imageUrl[0] | sed -e 's/.*\///'");
		//$imageName = str_replace(".","__","$imageNameOrg");
		
        $articleAHCInfo['issueDate'] = $issueDate;
        $articleAHCInfo['publishDate'] = $publishDate;
        $articleAHCInfo['articleTitle'] = $articleTitle;
        $articleAHCInfo['articleByline'] = $articleByline;
        $articleAHCInfo['articleSum'] = $articleSum;
        //this is just clean xml file with possible human mistake.
        $articleText = preg_replace("/http:\/\/ *http:\/\//",  "http://", $articleText );
        $articleText = preg_replace('/href="twitter.com/',  'href="http://twitter.com', $articleText );
        //remove inline images for temparary solution, revisit later to make it slide show
        $articleText = preg_replace('/<img.*?\/>/',  '', $articleText );
        
        
        echo $articleText;
        $articleAHCInfo['articleText'] = $this->cleanHtml($converter->convert($articleText));
        $articleAHCInfo['articleUrl'] = $articleUrl;
        $log->lwrite('class-utils: parseNitf::thumbnail type: ' . $this->getImageType($imageThumbnailName));
        if(count($imageThumbnailArray) >0 && $this->getImageType($imageThumbnailName) != ".gif")
        {
           $articleAHCInfo['thumbnail'] = $imageThumbnailArray;
                          
        }
        if($imageList != null)
        {
          //$articleAHCInfo['imageCaption'] = $imageCaption;
          //$articleAHCInfo['imageUrl'] = trim($imageUrl[0]); 
          //$articleAHCInfo['imageName'] = $imageName;
          
        	  $articleAHCInfo['imageList'] = $imageList;
         // $log->lwrite('class-utils: parseNitf::imageList size in articleAHCInfo: ' . count($imageList));
        }
        if($storyList != null)
        {       
         	$articleAHCInfo['relatedStories'] = $this->getRelatedStories($storyList);
         	//$log->lwrite('class-utils: parseNitf::convertedRelatedStories in articleAHCInfo: ' . $articleAHCInfo['relatedStories']);
         	
         	//$log->lwrite('class-utils: parseNitf::storyList size in articleAHCInfo: ' . count($storyList));
        }       
        	
       // shell_exec(" cd $imageDir && mv -f $imageNameOrg $imageName");
       // sleep(10);
        
		return $articleAHCInfo;
	}
	public function download($imageUrl, $imageDir ) {
		$imageName = shell_exec("echo $imageUrl[0] | sed -e 's/.*\///'");
		
		$file   = \file_get_contents($imageUrl[0]);
		$result = \file_put_contents($imageDir . $imageName, $file);
		sleep(10);
		return $result;
	}
	public function getDBConnection($DBInfo)
	{
		$log = new LOGGING();
		
		// set path and name of log file (optional)
		$log->lfile(__DIR__ . '/../../../logs/appleNewsFormat.log');
		
	    $conn = new \mysqli($DBInfo['serverDB'], $DBInfo['usernameDB'], $DBInfo['passwordDB'], $DBInfo['nameDB']);
	    // Check connection
	    if ($conn->connect_error) {
		   die("Connection failed: " . $conn->connect_error);
	    }
	    return $conn;
	}
	public function saveArticleInfoToDB($DBInfo, $articleAppleAHCinfo, $dbOpType)
	{
		$log = new LOGGING();
		
		// set path and name of log file (optional)
		$log->lfile(__DIR__ . '/../../../logs/appleNewsFormat.log');
		
		$conn = $this->getDBConnection($DBInfo);
		//$articleAHCId = $articleAppleAHCinfo[''];
		
		//$sql = "set names 'utf8'";
		//$result = $conn->query($sql);
			//  $sql = "update wp_posts set post_author='$authorID" . "' where id='" . $contentID . "'";
        //$isOldArticle = $this->getAHCArticleId($conn, $articleAppleAHCinfo);
        
		//if ($isOldArticle == NULL or $isOldArticle == "")
		if ($dbOpType == "insert")
        {
		  $sql = "insert into appleArticles( ahcArticleId, appleArticleId, appleArticleVersion) values('" . $articleAppleAHCinfo['articleAHCId'] . "', '" . $articleAppleAHCinfo['articleAppleId'] . "', '" . $articleAppleAHCinfo['articleAppleVersion'] . "')" ;
		
		  $log->lwrite('class-utils:saveArticleInfoToDB: insertSQL-----again:' . $sql);
		  		   
		
		  $result = $conn->query($sql);
		}
		else if ($dbOpType == "update")
		{
			$sql = "update appleArticles set appleArticleVersion='" . $articleAppleAHCinfo['articleAppleVersion'] .  "' where ahcArticleId='" . $articleAppleAHCinfo['articleAHCId'] . "'";
					
			$log->lwrite('class-utils:saveArticleInfoToDB: updateSQL-----again:' . $sql);
				
			$result = $conn->query($sql);
		}
		
		$conn->close();
	}
	public function isInDB($DBInfo, $ahcArticleId)
	{
		$log = new LOGGING();
		
		// set path and name of log file (optional)
		$log->lfile(__DIR__ . '/../../../logs/appleNewsFormat.log');
		$conn = $this->getDBConnection($DBInfo);
		
		$sql = "set names 'utf8'";
		$result = $conn->query($sql); 
		$sql = "select ahcArticleId from appleArticles where ahcArticleId = '" . $ahcArticleId . "'";
		//$log->lwrite('class-utils:getAHCArticleId:sql:  ' . $sql);		
		$result = $conn->query($sql);
		//$log->lwrite('class-utils:getAHCArticleId()::' .var_export($result, true));
		//$log->lwrite('class-utils:getAHCArticleId()::result->num_rows' . $result->num_rows);
		
		if( $result->num_rows > 0 != null)
		{
			return "true";
		}
		else
		{
			return "false" ;
		}
		
	} 
	 
/**
   * Create GET request to a specified endpoint.
   *
   * @param (string) $fileDir API endpoint path.
   * @param (string) $fileName Endpoint path arguments to replace tokens in the path.
   * @return object Preprocessed structured object.
   */	
	public function getArticleHistory($fileDir, $fileName)
	{
		$log = new LOGGING();
		
		// set path and name of log file (optional)
		$log->lfile(__DIR__ . '/../../../logs/appleNewsFormat.log');
		
		
		//$log->lwrite('class-utils:getArticleHistory:file: ' . $fileDir . '/' . $fileName);
		
		
		$articleHistory = array();
		$myfile = fopen($fileDir . '/' . $fileName, "r") or die("Unable to open file!");
		// Output one line until end-of-file
		while(!feof($myfile)) {	
		   $strTemp = fgets($myfile);
		   if($strTemp != NULL && $strTemp != "")
		   {
		     $arrayTemp = explode(',', $strTemp);
		     $arrayTemp1 = array("articleAppleId"=>$arrayTemp[1], "articleAppleVersion"=>$arrayTemp[2], "lastPubDate"=> str_replace(array("\n", "\r"),  '', $arrayTemp[3]));
		     $articleHistory["$arrayTemp[0]"] = $arrayTemp1;
		     
		   }
		}
		//  $log->lwrite('class-utils:getArticleHistory()::' .var_export($articleHistory, true));
				 
		fclose($myfile);
		return $articleHistory;
	}
	public function writeToFile($articleHistoryFlagShip,  $historyFileDir, $fileName)
	{
		 $strTemp = "";
		 $log = new LOGGING();
		 
		 // set path and name of log file (optional)
		 $log->lfile(__DIR__ . '/../../../logs/appleNewsFormat.log');
		 	
		
		 foreach ($articleHistoryFlagShip as $key => $value) 
		 {
		 	if ($strTemp == "")
		 	{
		 	   $strTemp = $key . ',' . $value['articleAppleId'] . ',' . $value['articleAppleVersion'] . ',' . $value['lastPubDate'];

		 	}
		 	else
		 	{
		 		$strTemp = $strTemp . "\n" . $key . ',' .  $value['articleAppleId'] . ',' . $value['articleAppleVersion'] . ',' . $value['lastPubDate'];
		 		 
		 	}
		 }
		 $result = file_put_contents($historyFileDir . '/' .  $fileName, $strTemp);
		 $log->lwrite('class-utils:writeToFile: done with write file result:' . $result);
		 	
	}
	public function getAppleArticleIdsFromDB($DBInfo)
	{
		$log = new LOGGING();
		
		// set path and name of log file (optional)
		$log->lfile(__DIR__ . '/../../../logs/appleNewsFormat.log');
		
		$conn = $this->getDBConnection($DBInfo);
		$sql = "select appleArticleId from appleArticles";
		//$log->lwrite('class-utils:getAppleArticleIdsFromDB:sql:  ' . $sql);
				
		$result = $conn->query($sql);
		if ($result->num_rows > 0) 
		{   
			$x = 0;
			while($row = $result->fetch_assoc())
			{
			  $articleIdsArray[$x] = $row['appleArticleId'];
			  $x++;
			}
		}
		else
		{
			$log->lwrite('class-utils: getAppleArticleIdsFromDB: 0 results');
				
		}
		return $articleIdsArray;
	}
	public function convert_fancy_quotes ($str) {
		return str_replace(array(chr(145),chr(146),chr(147),chr(148),chr(151)),array("'","'",'"','"','-'),$str);
	}
	public function getSectionMap($fileDir, $sectionFile)
	{
		$log = new LOGGING();
	
		// set path and name of log file (optional)
		$log->lfile(__DIR__ . '/../../../logs/appleNewsFormat.log');
	
	
		//$log->lwrite('class-utils:getSectionMap:file: ' . $fileDir . '/' . $sectionFile);
	
	
		$sectionMap= array();
		$myfile = fopen($fileDir . '/' . $sectionFile, "r") or die("Unable to open file!");
		// Output one line until end-of-file
		while(!feof($myfile)) {
			$strTemp = fgets($myfile);
			if($strTemp != NULL && $strTemp != "")
			{
				//4c57b6d5-d24f-384a-bd70-df720e7510b2=entertainment,lifestyle
				$strTemp = str_replace(array("\n", "\r"),  '', $strTemp);
				$arraySectionIdKey = explode('=', $strTemp);
				$arraySectionNameArray = explode(',', $arraySectionIdKey[1]);
				$sectionMap[$arraySectionIdKey[0]] = $arraySectionNameArray;
				//$log->lwrite('class-utils:getSectionMap()::' .var_export($sectionMap, true));
			  
			}
		}
		fclose($myfile);
		return $sectionMap;
	}
	public function getSectionId($articleUrl, $sectionMap, $feedName)
	{
		//http://www.dallasnews.com/business/20160225-luxury-hotel-just-one-of-the-additions-to-plano-s-2-billion-legacy-west-project.ece?appArticle
		$log = new LOGGING();
		$log->lfile(__DIR__ . '/../../../logs/appleNewsFormat.log');
		
	    if($feedName == "res-curate")
	    {
	    	   $strArray = explode('/', $articleUrl);
	    	   $ahcSectionName = $strArray[3];
	    	   $log->lwrite('class-utils:getSectionId:ahcSectionName: ' . $ahcSectionName );
	    	   foreach ($sectionMap as $key => $value)
	    	   {
	    	   	 if (in_array($ahcSectionName, $value) )
	    	   	 {
	    	   	 	$appleSectionId = $key;
	    	   	 }
	    	   }
	    	    	    	   
	    }//end of  if($feedName = "curate")
	    	else if($feedName == "sportsday-app")
	    	{
	    		$appleSectionId = "b86b8db2-2b3f-3de9-8031-241cb4b06f09";
	    		$log->lwrite('class-utils:getSectionId:ahcSectionName: sports ' );
	    	}
	    	return $appleSectionId;
	}
	/*convert should convert all html to markdown, but it has bug that <span> is not converted
	 * this function is responsible to remove unconverted html tag.
	 */
	public function cleanHtml($markdownContertedText)
	{
		$cleanedText = preg_replace('/<span.*?>/',  '', $markdownContertedText);
		$cleanedText = preg_replace('/<\/span>/',  '', $cleanedText );
		$cleanedText = preg_replace('/<u>/',  '_', $cleanedText );
		$cleanedText = preg_replace('/<\/u>/',  '_', $cleanedText );
		$cleanedText = preg_replace('/<table.*?>/',  '', $cleanedText );
		$cleanedText = preg_replace('/<\/table>/',  '', $cleanedText );
		$cleanedText = preg_replace('/<tr.*?>/',  '', $cleanedText );
		$cleanedText = preg_replace('/<\/tr>/',  '', $cleanedText );
		$cleanedText = preg_replace('/<td.*?>/',  '|', $cleanedText );
		$cleanedText = preg_replace('/<\/td>/',  '|', $cleanedText );
		/*this is used to convert image markdown to link markdown
		$cleanedText = preg_replace('/\!\[/',  '[', $cleanedText );
		*/
		
		
		
		
		
		
		return $cleanedText;
	}
    public function getRelatedStories($storyList)
    {
      	$log = new LOGGING();
      	$log->lfile(__DIR__ . '/../../../logs/appleNewsFormat.log');
      	$converter = new HtmlConverter();
    	 
       $relatedStories = "";
    	   foreach( $storyList as $story)
    	   {
    	   	 $relatedStories = $relatedStories . $converter->convert("<a href=\"" . $story['storyUrl'] . "\" >" . $story['storyTitle'] . "</a>") . "\n\n";
    	   }
    	   $log->lwrite('class-utils:getRelatedStories:$relatedStories: ' . $relatedStories );
    	   return $relatedStories;
    }
    public function getProps()
    {
     	$log = new LOGGING();
      	$log->lfile(__DIR__ . '/../../../logs/appleNewsFormat.log');
    	    // $log->lwrite('in class-utils:getProps()::' );
      	$myfile = fopen(__DIR__ . '/../../../files/appleNewsFormat.properties', "r") or die("Unable to open file!");
		// Output one line until end-of-file
	  $propArray = array();
		while(!feof($myfile)) {	
		   $strTemp = fgets($myfile);
		   if($strTemp != NULL && $strTemp != "")
		   {
		     $arrayTemp = explode('=', $strTemp);
		     $propArray[$arrayTemp[0]] = str_replace(array("\n", "\r"),  '', $arrayTemp[1]);
		     
		   }
		}
		//  $log->lwrite('class-utils:getProps()::' . var_export($propArray, true));
				 
		fclose($myfile);
    	    return $propArray;
    }
    public function getDBInfo($serverType)
    {
    	    //var_dump("in dbinfo: " . $serverType);
    	    
      	if ($serverType == 'dev')
      	{
    		 //DevDB
    		  $DBInfo = array("serverDB"=>"tdevcwadb1.test.ahc.belotechnologies.com", "usernameDB"=>"dmn_main", "passwordDB"=>"tRaPrA5a", "nameDB"=>"DMN_MAIN");
      	}
      	else if($serverType == "prod")
     	{
    		  //ProdDB
    		  $DBInfo = array("serverDB"=>"Kwebdb.ahc.belotechnologies.com", "usernameDB"=>"dmn_main", "passwordDB"=>"tRaPrA5a", "nameDB"=>"DMN_MAIN");
      	}
     	else if($serverType == "stage")
     	{
     		$DBInfo = array();
     	}
     	return $DBInfo;
    }
    public function deleteImagesDownloaded()
    {
      	$log = new LOGGING();
     	$log->lfile(__DIR__ . '/../../../logs/appleNewsFormat.log');
    	 
     	//$log->lwrite('class-utils:realpath::' . __DIR__ . "/../../../files/images/*");
    	    //unlink( realpath(__DIR__ . "/../../../files/images/*"));
     	//array_walk(glob(__DIR__ . '/../../../files/images/*'), 'unlink');
     	foreach(glob(__DIR__ . '/../../../files/images/*') as $fn) {
     		unlink($fn);
     	}
    	   // $log->lwrite('class-utils:deleteImageDownloded()::image should be gone' );
    	   
    	
    //	unlink(realpath($fileName)); it worked
    }
    public function getImageType($fileName)
    {
      	$info = getimagesize(__DIR__ . "/../../../files/images/$fileName");
      	return image_type_to_extension($info[2]);
    }
    public function trunc($phrase, $max_words) {
    	$phrase_array = explode(' ',$phrase);
    	if(count($phrase_array) > $max_words && $max_words > 0)
    		$phrase = implode(' ',array_slice($phrase_array, 0, $max_words)).'...';
    	return $phrase;
    }
}//end of class
