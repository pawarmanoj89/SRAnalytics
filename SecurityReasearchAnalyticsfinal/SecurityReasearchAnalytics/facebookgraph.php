<?php
/*
* Author Kapil Sutariya
*/
include_once ("global.php");
class FacebookData
{
	public $APPID='127747617355509';
	public $APPSECREAT='7d569432d41e9a1f534508d595a36d08';
	public $SITEID='138651952862910';
	

	/*<summary>
	*get likes,comments and post of facebook page 
	*</summary>
	*<param name=userid>facebook id of user</param>
	*<param name=arraydate>arraydate for which function is getting count of like,comment and post</param>
	*<returns>array of number of likes,comment and post</returns>
	**/
	function GetFacebookData($userid,$arrayDate)	{

		$accessToken=$this->getAccessToken($this->APPID,$this->APPSECREAT);
		$cnt=0;
		$commentCnt=0;
		$likeCnt=0;
		$postCnt=0;
		$accessType='feed';
		$url='https://graph.facebook.com/'.$this->SITEID.'?fields='.$accessType.'&access_token='.$accessToken;
		$d=Constant::getURLContent($url);

		$data = json_decode($d,true);

		$arrayComment=array();
		$arrayPost=array();
		//check using isset
		foreach($data[$accessType]['data'] as $data1)
		{
			$cnt++;
			if($data1['from']['id']==$userid)
			{
				$postCnt++;
				array_push($arrayPost,$data1['created_time']);
			}
			if(isset($data1['comments']['data']))
			{
				foreach($data1['comments']['data'] as $comment)
				{
					if($comment['from']['id']==$userid)
					{
						$commentCnt++;
						array_push($arrayComment,$comment['created_time']);
					}
				}
				if(isset($data1['comments']['paging']['next']))
				{
					while($data1['comments']['paging']['next'])
					{
						$url=$data1['comments']['paging']['next'];
						$d=Constant::GetURLContent($url);

						$data = json_decode($d,true);
						
						foreach($data['comments']['data'] as $comment)
						{
							if($comment['from']['id']==$userid)
							{
								$commentCnt++;
								array_push($arrayComment,$comment['created_time']);
							}
						}
					}
				}
			}
			if(isset($data1['likes']['data']))
			{
				foreach($data1['likes']['data'] as $like)
				{
					if($like['id']==$userid)
					{
						$likeCnt++;
					}
				}
			}
		}

		while(isset($data[$accessType]['paging']['next']))
		{
			$url=$data[$accessType]['paging']['next'];
			$d=Constant::GetURLContent($url);
			$data = json_decode($d,true);

			foreach($data[$accessType]['data'] as $data1)
			{
				//echo " Created Time:".$data1['created_time'];
				$cnt++;
				if($data1['from']['id']==$userid)
				{
					$postCnt++;
					array_push($arrayPost,$data1['created_time']);
				}
				if(isset($data1['comments']['data']))
				{
					foreach($data1['comments']['data'] as $comment)
					{
						if($comment['from']['id']==$userid)
						{
							$commentCnt++;
							array_push($arrayComment,$comment['created_time']);
						}
					}
					if(isset($data1['comments']['paging']['next']))
					{
						while($data1['comments']['paging']['next'])
						{
							$url=$data1['comments']['paging']['next'];
							$d=Constant::GetURLContent($url);
							$data = json_decode($d,true);
							foreach($data['comments']['data'] as $comment)
							{
								if($comment['from']['id']==$userid)
								{
									$commentCnt++;
									array_push($arrayComment,$comment['created_time']);
								}
							}
						}
					}
				}
				if(isset($data1['likes']['data']))
				{
					foreach($data1['likes']['data'] as $like)
					{
						if($like['id']==$userid)
						{
							$likeCnt++;
						}
					}
				}
			}
		}

		$graphArray=array();
		foreach ($arrayDate as $data)
		{
			array_push($graphArray,array('comment'=>0,'post'=>0,'like'=>0));
		}
		$graphArray[0]['like']=$likeCnt;
		foreach ($arrayComment as $data)
		{
			$i=0;
			foreach ($arrayDate as $date)
			{
				if($this->IsDateBetween($date['startdate'], $data,$date['enddate']))
				{
					$graphArray[$i]['comment']++;
					break;
				}
				$i++;
			}
		}
		foreach ($arrayPost as $data)
		{
			$i=0;
			foreach ($arrayDate as $date)
			{
				if($this->IsDateBetween($date['startdate'], $data,$date['enddate']))
				{
					$graphArray[$i]['post']++;
					break;
				}
				$i++;
			}
		}
		return $graphArray;
	}

	/*<summary>
	*check for date $dtCheck is in between dtStart and dtEnd
	*</summary>
	*<returns>true if date is inbetween else false</returns>
	**/
	function IsDateBetween($dtStart, $dtCheck, $dtEnd){
		if(strtotime($dtCheck) >= strtotime($dtStart) && strtotime($dtCheck) <= strtotime($dtEnd)) {
			return true;
		}
		return false;
	}

	/*<summary>
	*get access token for accessing facebook page
	*</summary>
	*<returns>access token</returns>
	**/
	public function GetAccessToken($appId,$appSecreat)
	{
		$url='https://graph.facebook.com/oauth/access_token?client_id='.$appId.'&client_secret='.$appSecreat.'&grant_type=client_credentials';
		$content=Constant::GetURLContent($url);
		$accessToken=substr($content, 13);
		return  $accessToken;
	}
}?>