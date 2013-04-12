<?php


/*@author 
 Pawar Manoj
*/

ini_set('max_execution_time',300);
set_include_path('library');
require_once 'Zend/Loader.php';
Zend_Loader::loadClass('Zend_Gdata_YouTube');

/**
 * This is class YoutubeGraphData
 * It is used for Graph , It contains functions to retrive count of comments & favorite videos over SecurityResearchIN.
 */
class YoutubeData
{
	public $userName='';
	public $channelName='SecurityResearchIN';
	public $srVideoList='';

	public $commentDates=array();
	public $favoriteDates=array();
	public $uploadDates=array();
	public $graphArray=array();
	/**
	 * This is method getYoutubeGraphData
	 * This is main method which calls other functions.
	 * @param mixed $ytusername -It is Youtube username of user.
	 * @param mixed $arrayDate -It is range of dates over which total count is calculate.
	 * @return mixed $grapharray - It contains total count  of comments , favorite videos , uploaded videos.  
	 *
	 */
	function GetYoutubeGraphData($ytUserName,$arrayDate)
		{
			set_include_path('library');
			require_once 'Zend/Loader.php';
			Zend_Loader::loadClass('Zend_Gdata_YouTube');
			
		
				$this->userName= $ytUserName;		
				
				
				//Uploads  & comments
				$yt = new Zend_Gdata_YouTube();
				$yt->getHttpClient()->setConfig(array('timeout'=>180));
				$yt->setMajorProtocolVersion(2);
		
				$this->srVideoList='';
				$this->GetUploadDates($yt->getuserUploads($this->channelName));
		
				
				// "Favorite";
				$this->GetFavoriteDates();
				
				$this->CountDates($arrayDate);
					
				return $this->graphArray;
			}
			
			
	/**
	 * This is method GetUploadDates
	 * This method collect dates of upload videos if user is  SecurityResearchIN, and call to function which collect comment dates.
	 * Insert dates into $updateDates array.
	 * @param mixed $videoFeed Video feed of SecurityResearchIN channel's video
	 * 
	 *
	 */	
	function GetUploadDates($videoFeed)
	{
		foreach ($videoFeed as $videoEntry) 
		{
			$this->srVideoList.= $videoEntry->getVideoTitle();
			// check for uploads ...
			if($this->userName == $this->channelName)
			{
				
				array_push($this->uploadDates,$videoEntry->getPublished());

			}
			$this->GetcommentDates($videoEntry->videoId);
		}
	}

	/**
	 * This is method GetCommentDates
	 * This function collect dates of comments by user on particular video .
	 * Insert dates into $commentDates. 
	 * @param mixed $videoId Video id of single video of SecurityResearchIN channel.
	 * 
	 *
	 */	
	function GetCommentDates($videoId)
	{
		
		//global $commentDates,$favoriteDates,$uploadDates,$this->graphArray;
		$yt = new Zend_Gdata_YouTube();
		$yt->getHttpClient()->setConfig(array('timeout'=>180));
		$yt->setMajorProtocolVersion(2);
		$commentFeed = $yt->getVideoCommentFeed($videoId);
		foreach ($commentFeed as $commentEntry)
		{
			
			if($commentEntry->author[0]->name->text == $this->userName )
			{
				array_push($this->commentDates,$commentEntry->published->text);
				
			}
			
		}
	}


	/**
	 * This is method GetFavoriteDates
	 * This function collects dates when user added video as favorite video. 
	 *
	 */	
	function GetFavoriteDates()
	{
		$yt = new Zend_Gdata_YouTube();
		$yt->getHttpClient()->setConfig(array('timeout'=>180));
		$yt->setMajorProtocolVersion(2);
		$favoritesFeed = $yt->getUserFavorites($this->userName);
		foreach($favoritesFeed as $favorite)
		{
			if(strpos($this->srVideoList , $favorite->getTitle()->text) !== false)
			{
				array_push($this->favoriteDates,$favorite->getPublished()->text);
				
			}
			
		}
	}
	
	/**
	 * This is method CountDates
	 *
	 * @param mixed $arrayDate This is range of dates in which graph is shown.
	 * It update $graphData.
	 *
	 */	
	function CountDates($arrayDate)
		{
			
					foreach ($arrayDate as $data)
					{
						array_push($this->graphArray,array('comment'=>0,'favorite'=>0,'upload'=>0));
					}
		
		

					foreach ($this->commentDates as $data)
					{
						$i=0;
						foreach ($arrayDate as $date)
						{
							if($this->IsDateBetween($date['startdate'], $data,$date['enddate']))
							{
								$this->graphArray[$i]['comment']++;
								break;
							}
							$i++;
						}
			
					}



					foreach ($this->favoriteDates as $data)
					{
			
						$i=0;
						foreach ($arrayDate as $date)
						{
							if($this->IsDateBetween($date['startdate'], $data,$date['enddate']))
							{
								$this->graphArray[$i]['favorite']++;

								break;
							}
							$i++;
						}
			
					}

					if($this->userName==$this->channelName)
					if(isset($uploadDates))
					{
						foreach ($uploadDates as $data)
						{
							$i=0;
							foreach ($arrayDate as $date)
							{
								if($this->IsDateBetween($date['startdate'], $data,$date['enddate']))
								{
									$this->graphArray[$i]['upload']++;
						
									break;
								}
								$i++;
							}

						}		
					}
			
			}




	/**
	 * This is method IsDateBetween
	 * Check whether date is in between range or not
	 * @param mixed $startDate Start date to compare.
	 * @param mixed $checkDate Date to compare.
	 * @param mixed $endDate End date to compare.
	 * @return mixed boolean.
	 *
	 */
	function IsDateBetween($startDate, $checkDate, $endDate){
		if(strtotime($checkDate) >= strtotime($startDate) && strtotime($checkDate) <= strtotime($endDate)) {
			return true;
		}
		return false;
	}



}
?>