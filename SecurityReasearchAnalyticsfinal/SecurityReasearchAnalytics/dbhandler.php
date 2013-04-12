<?php

class DbHandler
{
	public $connection;
	
	function __construct()
	{
		$this->connection = mysql_connect('10.100.41.232','sruser','sruser');
		
		if(!$this->connection)
		{
			echo mysql_error();	
		}
		else
		{
			mysql_select_db("sranalyticsdb",$this->connection);	
		}
	}
	
	/// 
	function GetMantisBtConfig()
	{
		$result = mysql_query("select url,admin_username,admin_password from mantisconfig",$this->connection);
		$config = mysql_fetch_array($result,MYSQL_ASSOC);
		
		return $config;
	}
	function SetMantisBtConfig($config)
	{
		mysql_query('delete from mantisconfig',$this->connection);
		
		$insertsql = "insert into mantisconfig values ('".$config['url']."','".$config['admin_username']."','".$config['admin_password']."')";
		
		$config = mysql_query($insertsql,$this->connection);
		
		return $config;
	}
	function GetMantisBtData()
	{
		$result = mysql_query("select userid,username,real_name,emailid from mantisbtdata",$this->connection);
		
		$mantisdata = array();
		while($row = mysql_fetch_array($result,MYSQL_ASSOC))
		{
			array_push($mantisdata,$row);
		}
		
		return $mantisdata;
	}
	function SetMantisBtData($mantisbtdata)
	{
		mysql_query('delete from mantisbtdata',$this->connection);
		
		$ret;
		
		foreach ($mantisbtdata as $row)
		{
			$insertsql = "insert into mantisbtdata values ('".$row['id']."','".$row['name']."','".
						$row['real_name']."','".$row['email']."')";
			
			$ret = mysql_query($insertsql,$this->connection);
		}
				
		return $ret;
	}
	function GetMantisBtUserId($username)
	{
		$result = mysql_query("select userid from mantisbtdata where username ='".$username."'",$this->connection);
		$userid = 0;
		if($row = mysql_fetch_array($result,MYSQL_ASSOC))
		{
			$userid = $row['userid'];
		}
		
		return $userid;	
	}
	
	function GetBitBucketData()
	{
		$result = mysql_query("select username,emailid from bitbucketdata",$this->connection);
		
		$bitbucketdata = array();
		while($row = mysql_fetch_array($result,MYSQL_ASSOC))
		{
			array_push($bitbucketdata,$row);
		}
		
		return $bitbucketdata;
	}
	function SetBitBucketData($bitbucketdata)
	{
		mysql_query('delete from bitbucketdata',$this->connection);
		
		$ret;
		
		foreach ($bitbucketdata as $row)
		{
			$insertsql = "insert into bitbucketdata values ('".$row['username']."','".$row['emailid']."')";
			$ret = mysql_query($insertsql,$this->connection);
		}
		
		return $ret;
	}
	function GetFacebookData()
	{
		$result = mysql_query("select userid,username,realname,emailid from facebookdata",$this->connection);
		
		$facebookdata = array();
		while($row = mysql_fetch_array($result,MYSQL_ASSOC))
		{
			array_push($facebookdata,$row);
		}
		
		return $facebookdata;
	}
	function SetFacebookData($facebookdata)
	{
		mysql_query('delete from facebookdata',$this->connection);
		
		$ret;
		
		foreach ($facebookdata as $row)
		{
			$insertsql = "insert into facebookdata values (".$row['userid'].",'".$row['username']."','".
				$row['realname']."','".$row['emailid']."')";
			
			$ret = mysql_query($insertsql,$this->connection);
		}
		
		return $ret;
	}
	function GetYouTubeData()
	{
		$result = mysql_query("select username,emailid from youtubedata",$this->connection);
		
		$youtubedata = array();
		while($row = mysql_fetch_array($result,MYSQL_ASSOC))
		{
			array_push($youtubedata,$row);
		}
		
		return $youtubedata;
	}
	function SetYouTubeData($youtubedata)
	{
		mysql_query('delete from youtubedata',$this->connection);
		
		$ret;
		
		foreach ($youtubedata as $row)
		{
			$insertsql = "insert into youtubedata values ('".$row['username']."','".$row['emailid']."')";
			$ret = mysql_query($insertsql,$this->connection);
		}
		
		return $ret;
	}
	function GetSrData()
	{
		$result = mysql_query("select srusername,bbusername,mbusername,fbusername,ytusername from srdata",$this->connection);
		
		$youtubedata = array();
		while($row = mysql_fetch_array($result,MYSQL_ASSOC))
		{
			array_push($youtubedata,$row);
		}
		
		return $youtubedata;
	}
	function SetSrData($srdata)
	{
		mysql_query('delete from srdata',$this->connection);
		
		$ret;
		
		foreach ($srdata as $row)
		{
			$insertsql = "insert into srdata values ('".$row['srusername']."','".$row['bbusername']."','".
				$row['mbusername']."','".$row['fbusername']."','".$row['ytusername']."')";
			$ret = mysql_query($insertsql,$this->connection);
		}
		
		return $ret;
	}
	function GetBitBucketRepository()
	{
		$result = mysql_query("select username,repositoryname from bitbucketrepository",$this->connection);
		
		$repos = array();
		while($row = mysql_fetch_array($result,MYSQL_ASSOC))
		{
			array_push($repos,$row);
		}
		
		return $repos;
	}
	function SetBitBucketRepository($repos)
	{
		mysql_query('delete from bitbucketrepository',$this->connection);
		
		$ret;
		
		foreach ($repos as $row)
		{
			$insertsql = "insert into bitbucketrepository values ('".$row['username']."','".$row['repositoryname']."')";
			$ret = mysql_query($insertsql,$this->connection);
		}
		
		return $ret;
	}
	function GetAllUserId($username)
	{
		$query="select sranalyticsdb.srdata.fbusername,sranalyticsdb.srdata.ytusername,sranalyticsdb.srdata.mbusername,sranalyticsdb.srdata.bbusername from sranalyticsdb.srdata where sranalyticsdb.srdata.srusername='".$username."'";
		$result = mysql_query($query,$this->connection);
		
		$ret = array();
		if($result!=false)
		{
			while($row = mysql_fetch_array($result,MYSQL_ASSOC))
			{
				array_push($ret,$row);
			}
		}		
		return $ret;
	}
	function GetFBUserId($username)
	{
		$query="select sranalyticsdb.facebookdata.userid from sranalyticsdb.facebookdata where sranalyticsdb.facebookdata.username='".$username."'";
		$result = mysql_query($query,$this->connection);
			
		$ret = array();
		if($result!=false)
		{
			while($row = mysql_fetch_array($result,MYSQL_ASSOC))
			{
				array_push($ret,$row);
			}
		}
		return $ret;
	}
	
	function GetSuggestion($text)
	{
		$query="SELECT sranalyticsdb.srdata.srusername FROM sranalyticsdb.srdata where sranalyticsdb.srdata.srusername like '%".$text."%'";
		$result = mysql_query($query,$this->connection);

		$ret = array();
		if($result!=false)
		{
			while($row = mysql_fetch_array($result,MYSQL_ASSOC))
			{
				array_push($ret,$row);
			}
		}
		return $ret;
	}
	function InsertSRData($srUserName,$fbUserName,$ytUserName,$mbUserName,$bbUserName)
	{
		$ret;
		$insertsql = "insert into srdata values ('".$srUserName."','".$bbUserName."','".$mbUserName."','".$fbUserName."','".$ytUserName."')";
		$ret = mysql_query($insertsql,$this->connection);
		
		return $ret;
	}
}

?>