<?php
/*
 * Author Kapil Sutariya
 * this class has been used for getting userId of facebook user from SR facebook page.
 * facebook userid along with username,emailID is inserted into database
 */
ini_set('max_execution_time', 300);
include ("menu.html");
include ("dbhandler.php");
include ("global.php");

$APPID='127747617355509';
$APPSECREAT='7d569432d41e9a1f534508d595a36d08';
$SITEID='138651952862910';

$accessToken=GetAccessToken($APPID,$APPSECREAT);
$accessType='feed';
$url='https://graph.facebook.com/'.$SITEID.'?fields='.$accessType.'&access_token='.$accessToken;
$d=Constant::GetURLContent($url);
$data = json_decode($d,true);

$arrayData=array();

foreach($data[$accessType]['data'] as $data1)
{
	array_push($arrayData,$data1['from']['id']);
	if(isset($data1['comments']['data']))
	{
		foreach($data1['comments']['data'] as $comment)
		{
			array_push($arrayData,$comment['from']['id']);
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
					array_push($arrayData,$comment['from']['id']);
				}
			}
		}
	}
	if(isset($data1['likes']['data']))
	{
		foreach($data1['likes']['data'] as $like)
		{
			array_push($arrayData,$like['id']);
			//echo 'like'.$like['id'].'<br>';
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
		array_push($arrayData,$data1['from']['id']);
		if(isset($data1['comments']['data']))
		{
			foreach($data1['comments']['data'] as $comment)
			{
				array_push($arrayData,$comment['from']['id']);
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
						array_push($arrayData,$comment['from']['id']);
					}
				}
			}
		}
		if(isset($data1['likes']['data']))
		{
			foreach($data1['likes']['data'] as $like)
			{
				array_push($arrayData,$like['id']);
			}
		}
	}

}

$resultArray=array_unique($arrayData);

$arrayData=array();

foreach ($resultArray as $data)
{
	$url1='https://graph.facebook.com/'.$data.'?fields=username,name,email&access_token='.$accessToken;
	$d1=Constant::GetURLContent($url1);
	parse_str($d1,$output);

	$uname = json_decode($d1,true);

	if (!isset($uname['name'])&&!isset($uname['username']))
	{
		array_push($arrayData,array('userid'=>$data,'username'=>'SecurityResearch.in','realname'=>'SecurityResearch.in','emailid'=>''));
		continue;
	}
	else
	{
		if (!isset($uname['name']))
			$uname['name']='';
		if (!isset($uname['username']))
			$uname['username']=$uname['name'];
		if (!isset($uname['email']))
			$uname['email']='';
		array_push($arrayData,array('userid'=>$data,'username'=>$uname['username'],'realname'=>$uname['name'],'emailid'=>$uname['email']));
	}
}

$db = new dbhandler();
$config = $db->SetFacebookData($arrayData);


echo '<head>
<LINK href="style.css" rel="stylesheet" type="text/css">
</head>
<body>
<header>
';
echo '<br>';
if($config==1)
	echo "<b>UserId generated</b>";
else
	echo "<b>Error</b>";
echo '</header>
</body>';

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
?>