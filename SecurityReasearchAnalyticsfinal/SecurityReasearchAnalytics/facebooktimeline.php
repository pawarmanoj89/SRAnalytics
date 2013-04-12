<?php
/*
* Author Kapil Sutariya
* This class is used for getting comments,like and post from Securityresearch facebook page.
*/
class FacebookTimeline
{
	public $APPID='127747617355509';
	public $APPSECREAT='7d569432d41e9a1f534508d595a36d08';
	public $SITEID='138651952862910';

	/*<summary>
	*get likes,comments and post of facebook page
	*</summary>
	*<param name=userid>facebook id of user</param>
	*<returns>array of likes,comment and post with timestamp</returns>
	**/	
	function GetFacebookTimeline($userid)
	{
		$accessToken=$this->GetAccessToken($this->APPID,$this->APPSECREAT);
		$cnt=0;
		$accessType='feed';
		$url='https://graph.facebook.com/'.$this->SITEID.'?fields='.$accessType.'&access_token='.$accessToken;
		$d=Constant::GetURLContent($url);
		$data = json_decode($d,true);
		
		$dataArray=array();		

		foreach($data[$accessType]['data'] as $data1)
		{
			$data1['from']['name'];
			if(!isset($data1['message']))
			{
				$data1['message']="";
			}
			if(!isset($data1['name']))
			{
				$data1['name']="";
			}
			$data1['created_time'];
			if(!isset($data1['description']))
			{
				$data1['description']="";
			}

			if($data1['from']['id']==$userid && ($data1['message']!=""||$data1['name']!=""))  //in some post there is nothing to show except userId
			{
				array_push($dataArray,array('date'=>$data1['created_time'],'name'=>$data1['name'],'message'=>$data1['message'],'description'=>$data1['description']));
			}
				
			if(isset($data1['comments']['data']))
			{
				foreach($data1['comments']['data'] as $comment)
				{
					$comment['from']['id'];
					$comment['message'];
					$comment['created_time'];
					if($comment['from']['id']==$userid)
					{
						array_push($dataArray,array('date'=>$comment['created_time'],'name'=>'Commented on '.$data1['name'],'message'=>$comment['message']));
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
							$comment['from']['id'];
							$comment['message'];
							$comment['created_time'];

							if($comment['from']['id']==$userid)
							{
								array_push($dataArray,array('date'=>$comment['created_time'],'name'=>'Commented on '.$data1['name'],'message'=>$comment['message']));
							}
						}

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
				$data1['from']['name'];
				if(!isset($data1['message']))
				{
					$data1['message']="";
				}
				if(!isset($data1['name']))
				{
					$data1['name']="";
				}
				$data1['created_time'];
				if(!isset($data1['description']))
				{
					$data1['description']="";
				}
					
				if($data1['from']['id']==$userid  && ($data1['message']!=""||$data1['name']!=""))
				{
					array_push($dataArray,array('date'=>$data1['created_time'],'name'=>$data1['name'],'message'=>$data1['message'],'description'=>$data1['description']));
				}
					
				if(isset($data1['comments']['data']))
				{
					foreach($data1['comments']['data'] as $comment)
					{
						$comment['from']['id'];
						$comment['message'];
						$comment['created_time'];

						if($comment['from']['id']==$userid)
						{
							array_push($dataArray,array('date'=>$comment['created_time'],'name'=>'Commented on '.$data1['name'],'message'=>$comment['message']));
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
								$comment['from']['id'];
								$comment['message'];
								$comment['created_time'];

								if($comment['from']['id']==$userid)
								{
									array_push($dataArray,array('date'=>$comment['created_time'],'name'=>'Commented on '.$data1['name'],'message'=>$comment['message']));
								}
							}

						}
					}
				}
			}
		}
		return  $dataArray;
	}


	/*<summary>
	*get access token for accessing facebook page
	*</summary>
	*<returns>access token</returns>
	**/
	function GetAccessToken($appId,$appSecreat)
	{
		$url='https://graph.facebook.com/oauth/access_token?client_id='.$appId.'&client_secret='.$appSecreat.'&grant_type=client_credentials';
		$content=Constant::GetURLContent($url);
		$accessToken=substr($content, 13);
		return  $accessToken;
	}
}
?>