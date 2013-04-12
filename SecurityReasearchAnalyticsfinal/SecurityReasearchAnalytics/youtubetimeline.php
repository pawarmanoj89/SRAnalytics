<?php
ini_set('max_execution_time',300);
set_include_path('library');
require_once 'Zend/Loader.php';
Zend_Loader::loadClass('Zend_Gdata_YouTube');

class YoutubeTimelineData
{
	public $CHANNELNAME='SecurityResearchIN';
	public $userName='';
	public $srVideoList='';
	public $currentVideo='';
	public $returnString=array();

	function YoutubeData($ytusername){
		set_include_path('library');
		require_once 'Zend/Loader.php';
		Zend_Loader::loadClass('Zend_Gdata_YouTube');		
		$this->userName= $ytusername;
		
		//Subscription
		$this->GetSubscription();
		
		//Uploads  & comments
		$this->GetComments();
		
		// "Favorite";
		$this->GetFavouriteVideos();
				
		return $this->returnString;
	}
	
	function GetSubscription(){
		
		// Subscription...
		$yt = new Zend_Gdata_YouTube();
		$yt->getHttpClient()->setConfig(array('timeout'=>180));
		$yt->setMajorProtocolVersion(2);
		
		//Enter Channel Name to retrive activities over that channel 
		$this->CHANNELNAME = 'SecurityResearchIN';
		$subscriptionFeed = $yt->getSubscriptionFeed($this->userName);
		
		// loop through feed entries and print information about each subscription
		foreach($subscriptionFeed as $subscriptionEntry) 
		{
			// get the array of categories to find out what type of subscription it is
			$title = $subscriptionEntry->getTitle();
			if($title=='Activity of: ' . $this->CHANNELNAME)
			{
				array_push($this->returnString,array('date' => $subscriptionEntry->getPublished()->text,
					'message' => $this->userName. ' subscribed to ' . $subscriptionEntry->getTitle()->text ));
				
			}  
		}
		
	}
	
	function GetComments(){
		
		// Subscription...
		$yt = new Zend_Gdata_YouTube();
		$yt->getHttpClient()->setConfig(array('timeout'=>180));
		$yt->setMajorProtocolVersion(2);
		
				
		//Enter Channel Name to retrive activities over that channel 
		$this->CHANNELNAME = 'SecurityResearchIN';
		$this->retriveVideoFeed($yt->getuserUploads($this->CHANNELNAME));
		
	}

	function retriveVideoFeed($videoFeed)
	{
		foreach ($videoFeed as $videoEntry)
		{
			$this->currentVideo=$videoEntry->getVideoTitle();
			$this->srVideoList.= $videoEntry->getVideoTitle();  //create SRvideoList , which will be used in filtering Favorite video names 
			
			
			if($this->userName == $this->CHANNELNAME)
			{
				array_push($this->returnString,array('date' => $videoEntry->getPublished(),
					'message' => $this->userName . ' has uploaded ' . $this->currentVideo  ));
			}
			
			$this->GetAndPrintCommentFeed($videoEntry->videoId);  
		}
	}

	function GetAndPrintCommentFeed($videoId)
	{			
		//global $returnString;
		$yt = new Zend_Gdata_YouTube();
		$yt->getHttpClient()->setConfig(array('timeout'=>180));
		$yt->setMajorProtocolVersion(2);
		$commentFeed = $yt->getVideoCommentFeed($videoId);
		foreach ($commentFeed as $commentEntry)
		{
			
			if($commentEntry->author[0]->name->text == $this->userName )
			{ 
				array_push($this->returnString,array('date' => $commentEntry->published->text,
					'message' => $this->userName ." Comments on: " . $this->currentVideo . ' " ' . $commentEntry->content->text  ));
				
			}
			
		}
	}
	
	
	function GetFavouriteVideos()
	{
		$yt = new Zend_Gdata_YouTube();
		$yt->getHttpClient()->setConfig(array('timeout'=>180));
		$yt->setMajorProtocolVersion(2);
		
		$favoritesFeed = $yt->getUserFavorites($this->userName);
		foreach($favoritesFeed as $favorite) 
		{
			
			if(strpos($this->srVideoList , $favorite->getTitle()->text) !== false)
			{ 
				array_push($this->returnString,array('date' => $favorite->getPublished()->text,
					'message' => $this->userName . ' added ' .$favorite->getTitle()->text . " as Favorite  " ));
				
			}
			
		}
	}


}
?>