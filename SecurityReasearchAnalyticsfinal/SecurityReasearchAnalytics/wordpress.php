<?php

set_include_path( 'lib' );
require_once( 'xmlrpc/IXR_Library.php' );

/**
 * This class contains all the function related to wordpress blog api
 * @author Mahesh Dhaduk
 */

class Wordpress
{
	public $errorCode;
	public $errorMessage;
	private $hostUrl;
	private $adminUserName;
	private $adminPassword;
	
	function __construct()
	{
		$this->hostUrl = 'http://localhost/AndroidMobiles/xmlrpc.php';
		$this->adminUserName = 'admin';
		$this->adminPassword = 'admin';
	}
	
	/**
	* Execute xmlrpc request
	* @return array 
	*/
	Private function WpQuery()
	{
		$args = func_get_args();
		$client = new IXR_Client($this->hostUrl);
		$client->WpQuery($args);

		if($client->isError())
		{
			$errorCode = $client->getErrorCode();
			$errorMessage = $client->getErrorMessage();
			$response = false;
		}
		else
		{
			$errorCode = 0;
			$errorMessage = '';
			$response = $client->getResponse();
		}
		
		return $response;	
	}
	
	/**
	* Get all the blogs
	* @return array 
	*/
	Private function GetBlogs()
	{
		return $this->WpQuery('wp.getUsersBlogs',$this->adminUserName,$this->adminPassword);
	}	
	
	/**
	* Get all the authors from all the blogs
	* @return array 
	*/
	Private function GetAuthors()
	{
		$blogs = $this->GetBlogs();
		if($blogs === false)
			return false;
		
		$tauthors = array();
		foreach($blogs as $blog)
		{
			$tempAuthors = $this->WpQuery('wp.getAuthors',$blog['blogid'],$this->adminUserName,$this->adminPassword);
			
			if($tempAuthors !== false)
				$tauthors = array_merge($tauthors,$tempAuthors);
		}
		
		$authors = array();
		foreach ($tauthors as $tauthor)
		{			
			if (array_search($tauthor,$authors) === false)
			{
				array_push($authors,$tauthor);
			}
		}

		return $authors;
	}
	
	/**
	* Get author id from authorname
	* @param string $userName is the wordpress username for author
	* @return boolean 
	*/
	Private function GetAuthorId($userName)
	{
		$authorId = 0;
		$authors = $this->GetAuthors();
		
		if($authors !== false)
			foreach($authors as $author)
			{
				if($author['user_login'] == $userName)
				{
					$authorId = $author['user_id'];
					break;
				}
			}
		return $authorId;
	}
	
	/**
	* Get all the posts for given author from all the blogs
	* @param string $userName is the wordpress user name for authors on blogs.
	* @return array 
	*/	
	function GetPosts($userName)
	{
		$userId = $this->GetAuthorId($userName);
		
		$blogs = $this->GetBlogs();
		if($blogs === false)
			return false;
		
		$posts = array();	
		foreach($blogs as $blog)
		{
			$tposts = $this->WpQuery('metaWeblog.getRecentPosts',$blog['blogid'],$this->adminUserName,$this->adminPassword);
			
			if($tposts !== false)
				foreach($tposts as $tpost)
				{
					if($userId == $tpost['wp_author_id'])
					{
						array_push($posts,array(
							'date' => $tpost['dateCreated']->year.'-'.$tpost['dateCreated']->month.'-'.$tpost['dateCreated']->day,
							'title' => $tpost['title'],
							'link' => $tpost['link'],
							'author_id' => $tpost['wp_author_id'],
							'author_name' => $tpost['wp_author_display_name'],
							'postid' => $tpost['postid'],
							'blogName' => $blog['blogName']
							));
					}
				}	
			
		}
		
		return $posts;
	}	
	
	
	/**
	* number of post for a particular period of time by given user
	* @param string $userName is the bitbucket username
	* @param $dates array of start date and end date
	* @return array 
	*/
	function PostCountByTime($userName,$dates)
	{	
		$posts = $this->GetPosts($userName);

		$counts = array();

		foreach($dates as $date)
		{
			$count = 0;
			
			if($posts !== false)
				foreach($posts as $post)
				{
					if(strtotime($date['startdate']) <= strtotime($post['date']) && strtotime($post['date']) <= strtotime($date['enddate']))
					{
						$count = $count + 1;
					}
				}
			array_push($counts,array('startdate'=>$date['startdate'],'enddate'=>$date['enddate'],'count'=>$count));	
		}	
		return $counts;
	}	
}

?>
