<?php

set_include_path( 'lib' );
require_once( 'soap/nusoap.php' );

/**
 * This class provide mantisbt api
 * @author Mahesh Dhaduk
 */
class MantisBt
{
	public $soapclient;
	private $userName;
	private $password;
	 
	/**
	* This consttructor create object
	*/	
	function __construct()
	{
		$db = new DbHandler();
		$config = $db->GetMantisBtConfig();
		
		$this->soapclient =	new soapclient($config['url'],true);
		$this->username =	$config['admin_username'];
		$this->password =	$config['admin_password'];
	}
	
	/**
	* This function gives all the projects which accessible by administartor.
	* @return array 
	*/
	private function GetAllProjects()
	{
		
		$return = $this->soapclient->call('mc_projects_get_user_accessible',
			array('username' => $this->username, 'password'=> $this->password));	

		return $return;
	}
	
	/**
	* This function give all users which have at least one proceject assigned.
	* @return array 
	*/
	function GetUsers()
	{
		$projects = $this->GetAllProjects();
		
		$tusers = array();
		
		foreach ($projects as $project)
		{
			$tempusers = $this->soapclient->call('mc_project_get_users',
					array(
							'username' => $this->username, 
							'password' => $this->password,
							'project_id' => $project['id'],
							'access' => 0));	
			
			$tusers = array_merge($tusers,$tempusers);
		}
		
		$users = array();
		
		foreach ($tusers as $tuser)
		{			
			if (array_search($tuser,$users) === false)
			{
				array_push($users,$tuser);
			}
		}
			
		return $users;
	}
	
	/**
	* This function gives all the isses from all the projects.
	* @return array 
	*/
	private function GetAllIssues()
	{
		$projects = $this->GetAllProjects();
		
		$issues = array();
		
		foreach ($projects as $project)
		{
			$tempissues = $this->soapclient->call('mc_project_get_issue_headers',
					array(
							'username' => $this->username, 
							'password' => $this->password,
							'project_id' => $project['id'],
							'page_number' => 0,
							'per_page' => 0));	
					
			$issues = array_merge($issues,$tempissues);
		}
		
		return $issues;
	}
	
	/**
	* Get all the issues which are reported by user or assigned to user.
	* @return array 
	*/
	private function GetIssuesByUser($userid)
	{
		
		$issues = $this->GetAllIssues();
		
		$assignedIssues = array();
		
		foreach($issues as $issue)
		{
			if ($issue['reporter'] == $userid)
			{
				array_push($assignedIssues,$issue);	
			}
			else
			{
				if(isset($issue['handler']))
				{
					if($issue['handler'] == $userid)
					{
						array_push($assignedIssues,$issue);	
					}
				}
			}
			
		}
		
		return $assignedIssues;
	}
	
	/**
	* This function gives count of issue reported by or assigned to user in given period of time.
	* @param string $userName Mantisbt user name
	* @param string $dates 
	* @return array 
	*/
	function IssuesCountByTime($userName,$dates)
	{
		$db = new DbHandler();
		$userid = $db->GetMantisBtUserId($userName);
		
		$issues = $this->GetIssuesByUser($userid);
		
		$counts = array();
		foreach($dates as $date)
		{
			$countReported = 0;
			$countAssigned = 0;
			foreach($issues as $issue)
			{
				if(strtotime($date['startdate']) <= strtotime(substr($issue['last_updated'],0,10)) && strtotime(substr($issue['last_updated'],0,10)) <= strtotime($date['enddate']))
				{
					if ($issue['reporter'] == $userid)
					{
						$countReported += 1;	
					}
					else
					{
						if(isset($issue['handler']))
						{
							if($issue['handler'] == $userid)
							{
								$countAssigned += 1;
							}
						}
					}
				}
			}
				
			array_push($counts,array('startdate'=>$date['startdate'],'enddate'=>$date['enddate'],'reportedCount'=>$countReported,'assignedCount'=>$countAssigned));	
		}	
		return $counts;
	}
	
}

?>