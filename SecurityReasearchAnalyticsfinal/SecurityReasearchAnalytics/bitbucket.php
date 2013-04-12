<?php

include_once ('dbhandler.php');
include_once ('global.php');

/**
 * This class contains all the function related to bitbucket
 * @author Priyanka Jaiswal
 */
class BitBucket
{
	/**
	* Whether the particular user follow the given repository or not
	* @param string $userName is the owner of the repository
	* @param string $repositoryName is the repository name
	* @param string $user is the bitbucket username
	* @return boolean 
	*/
	private function IsUserFollowRepository($userName,$repositoryName,$user)
	{
		$url = "https://api.bitbucket.org/1.0/repositories/".$userName."/".$repositoryName."/followers/";
		$response = Constant::getURLContent($url);
		$respArray = json_decode($response);
		
		$exist = false;
		
		if ($respArray !== false)
		foreach($respArray->followers as $follower)
		{
			if($follower->username==$user)
			{
				$exist = true;
				break;
			}
		} 
		return $exist;	
	}

	/**
	* number of repository followed by the user
	* @param string $user is the bitbucket username
	* @return integer 
	*/
	function GetRepositoryCount($user)
	{
		$db=new DbHandler();
		$repos=$db->GetBitBucketRepository();
		$count = 0;
		
		if ($repos !== false)
		foreach($repos as $repo)
		{
			if($this->IsUserFollowRepository($repo['username'],$repo['repositoryname'],$user))
			{
				$count += 1;
			}
		}
		return $count;
	}

	/**
	* commit message,timestamp of a repository done by the user
	* @param string $userName is the owner of the repository
	* @param string $repositoryName is the repository name
	* @param string $user is the bitbucket username
	* @return array 
	*/
	private function ChangeSetsForRepository($userName,$repositoryName,$user)
	{
		$urlInitial="https://api.bitbucket.org/1.0/repositories/".$userName.'/'.$repositoryName."/changesets?limit=1";
		$responseInitial = Constant::getURLContent($urlInitial);
		$respArrayInitial = json_decode($responseInitial);
		
		if($respArrayInitial === false)
			return array();

		$startInitial=$respArrayInitial->count;
		$start=$startInitial-1;
		
		$commits = array();
		do
		{
			$url = "https://api.bitbucket.org/1.0/repositories/".$userName.'/'.$repositoryName."/changesets?limit=30&start=".$start;

			$response = Constant::getURLContent($url);
			$respArray = json_decode($response);
			
			if ($respArray !== false)
			foreach($respArray->changesets as $change)
			{
				if($change->author == $user)
				{
					array_push($commits,array('date'=>$change->timestamp,'author'=>$change->author,'message' => $change->message,'repository'=>$repositoryName));
				}
			} 
			
			$start=$start-30;
		}
		while($start>0);

		return $commits;
	}

	/**
	* number of commits done by the user
	* @param string $user is the bitbucket username
	* @returns array
	*/
	function ChangeSetsCountsForUser($user)
	{
		$db=new DbHandler();
		$repos=$db->GetBitBucketRepository();
		$totalCommits = array();
		
		if($repos !== false)
		foreach($repos as $repo)
		{
			$totalCommits = array_merge($totalCommits,$this->ChangeSetsForRepository($repo['username'],$repo['repositoryname'],$user));
		}
		return $totalCommits;	
	}

	/**
	* number of commits for a particular period of time
	* @param string $userName is the bitbucket username
	* @param $dates array of start date and end date
	* @return array 
	*/
	function CommitCountByTime($userName,$dates)
	{	
		$totalCommits = $this->ChangeSetsCountsForUser($userName);

		$counts = array();

		foreach($dates as $date)
		{
			$count = 0;
			
			if($totalCommits !== false)
			foreach($totalCommits as $commit)
			{
				if(strtotime($date['startdate']) <= strtotime(substr($commit['date'],0,10)) && strtotime(substr($commit['date'],0,10)) <= strtotime($date['enddate']))
				{
					$count = $count + 1;
				}
			}
			array_push($counts,array('startdate'=>$date['startdate'],'enddate'=>$date['enddate'],'count'=>$count));	
		}	
		return $counts;
	}
	
	
	/**
	* follow and unfollow of a repository
	* @param string $userName is the bitbucket username
	* @return array 
	*/
	function FollowUnfollowDate($userName)
	{	
		$iurl = "https://api.bitbucket.org/1.0/users/".$userName."/events/?limit=1";
		$iresponse = Constant::getURLContent($iurl);
		$ievents = json_decode($iresponse);	
		
		if($ievents === false)
			return array();
	
		$count=$ievents->count;
		$count=$count-1;
		$start = 0;
		$followUnfollowEvents = array();
		
		do
		{	
			$url = "https://api.bitbucket.org/1.0/users/".$userName."/events/?limit=20&start=".$start;
			$response = Constant::getURLContent($url);
			$events = json_decode($response);
			
			if($events !== false)	
			foreach($events->events as $event)
			{
				if($event->event == 'start_follow_repo' || $event->event == 'stop_follow_repo')
				{
					if(isset($event->repository))
					{
						array_push($followUnfollowEvents,array('date'=>substr($event->created_on,0,10),'event'=>$event->event,'ropository'=>$event->repository->name,'owner'=>$event->repository->owner));
					}
				}
				
			}
			$start=$start+20;
			
		}while($count>=$start);
		
		return $followUnfollowEvents;
	}
}

?>