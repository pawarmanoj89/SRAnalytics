<?php 
/*
 * Author Kapil Sutariya
 * Draws timeline page
*/
ini_set('max_execution_time',300);
include ("timeline.html");
include ("youtubetimeline.php");
include ("facebooktimeline.php");
include ("mantisbt.php");
include ("bitbucket.php");

error_reporting(0);
$srUserName=trim($_POST["username"]);

echo '<html>
<head>
<LINK href="style.css" rel="stylesheet" type="text/css">
<title>Facebook timeline</title>
</head>
<body style="background-color: #e7ebf2; font-family: arial; font-size: 12px;">';

$db = new dbhandler();
$userNameArray = $db->GetAllUserId($srUserName);

if(count($userNameArray)!=0)
{

if(isset($userNameArray[0]['fbusername']))
{
	$data1= $db->GetFBUserId($userNameArray[0]['fbusername']);
	if(isset($data1[0]['userid']))
		$fbuserName=$data1[0]['userid'];
	else
		$fbuserName = '';
}
else
{
	$fbuserName = '';
}
if(isset($userNameArray[0]['ytusername']))
	$ytuserName = $userNameArray[0]['ytusername'];
else
	$ytuserName = '';
if(isset($userNameArray[0]['mbusername']))
	$mbuserName = $userNameArray[0]['mbusername'];
else
	$mbuserName = '';
if(isset($userNameArray[0]['bbusername']))
	$bbuserName = $userNameArray[0]['bbusername'];
else
	$bbuserName = '';


$fbCnt=0;
$mbCnt=0;
$ytCnt=0;
$bbCnt=0;
$fbArray=array();
$ytArray=array();
$bbArray=array();
$bbFollowArray=array();
if($fbuserName!="")
{
	$fb=new FacebookTimeline();
	$fbArray=$fb->GetFacebookTimeline($fbuserName);
}


if($ytuserName!="")
{
	try{
	$yt=new YoutubeTimelineData();
	$ytArray=$yt->YoutubeData($ytuserName);
	}
	catch(Exception $e)
	{
		//$ytArray='';
	}
}

if($bbuserName!="")
{
	$bb=new BitBucket();
	$bbArray=$bb->ChangeSetsCountsForUser($bbuserName);
	$bbFollowArray=$bb->FollowUnfollowDate($bbuserName);
}


$timeLineArray=array_merge($fbArray,$bbArray,$bbFollowArray,$ytArray);
usort($timeLineArray, 'CompareDate');
$timeLineArray=array_reverse($timeLineArray);
echo '<ol id="timeline">';
foreach ($timeLineArray as $data)
{
	echo '<li>';

	$siteName='';
	$str='';
	if(isset($data['name']))//For Facebook
	{
		$str='<b>'.$data['name'].'</b>'.'<br>';
		$siteName='Facebook::';
	}
	else if(isset($data['repository']))//For Bitbucket
	{
		$str='<b>'.$data['repository'].'</b>'.'<br>';
		$siteName='BitBucket::';
	}
	else
	{
		$siteName='Youtube::';
	}

	if(isset($data['message']))//For Bitbucket,Facebook & Youtube
		$str=$str.$data['message'].'<br>';

	if(isset($data['event']))//For Bitbucket
		$str=$str.$data['event'].'<br>';

	if(isset($data['owner']))//For Bitbucket
		$str=$str.$data['owner'].'<br>';

	if(isset($data['description']))//For Facebook
		$str=$str.$data['description'];

	if(isset($data['author']))//For Bitbucket
		$str=$str.$data['author'];

	if(isset($data['date']))
		echo '<div class="time">'.$siteName.substr($data['date'],0,10).'</div>';
	echo '<span class="corner"></span><p>'.$str.'</p></li>';
}
echo '</ol>';
}
else
{
	echo '<div class="header"><h1>UserName not exist</h1></div>';
}
echo '
</body>
</html>
';

//<summary>
//function for comparing two dates for sorting array
//</summary>
function CompareDate($a, $b) {
	return strnatcmp($a['date'], $b['date']);
} // sort alphabetically by name

?>