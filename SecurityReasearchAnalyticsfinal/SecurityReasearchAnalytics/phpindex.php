<?php
/*
* Author Kapil Sutariya
* Draws Graph page
*/
ini_set('max_execution_time',300);
include ("index.html");
include ("youtubegraph.php");
include ("facebookgraph.php");
include ("mantisbt.php");
include ("bitbucket.php");
include ("libchart/libchart/classes/libchart.php");
include_once ("dbhandler.php");

$srusername=trim($_POST["username"]);

$db = new dbhandler();
$userNameArray = $db->GetAllUserId($srusername);
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

$fbcnt=0;
$mbcnt=0;
$ytcnt=0;
$bbcnt=0;
$fbCommentCnt=0;
$fbPostCnt=0;
$fbLikeCnt=0;
$ytCommentCnt=0;
$ytFavouriteCnt=0;
$ytUploadCnt=0;
$mbReportedIssueCnt=0;
$mbAssignedIssueCnt=0;
$bbCommitCnt=0;
$bbFollowedRepositoryCnt=0;



$arrayDate=GetWeeklyDate();
$chart = new LineChart(800,250);
$dataSet = new XYSeriesDataSet();


if($fbuserName!="")
{
	$fb=new FacebookData();
	$fbarray=$fb->GetFacebookData($fbuserName,$arrayDate);

	$serie1 = new XYDataSet();
	$i=0;
	foreach ($fbarray as $data)
	{
		$date = date("d-m-Y", strtotime($arrayDate[$i]['startdate']));
		$serie1->addPoint(new Point($date,$data['comment']+$data['post']));
		//$fbcnt+=$data['comment']+$data['post'];
		$fbCommentCnt+=$data['comment'];
		$fbPostCnt+=$data['post'];
		$i++;
	}
	$fbLikeCnt=$fbarray[0]['like'];
	$fbcnt=$fbCommentCnt+$fbLikeCnt+$fbPostCnt;
	$dataSet->addSerie("Facebook", $serie1);
}



if($ytuserName!="")
{
	$yt=new YoutubeData();
	$ytarray=$yt->GetYoutubeGraphData($ytuserName, $arrayDate);
	
	$serie2 = new XYDataSet();
	$i=0;
	foreach ($ytarray as $data)
	{
		$date = date("d-m-Y", strtotime($arrayDate[$i]['startdate']));
		$serie2->addPoint(new Point($date,$data['comment']+$data['favorite']+$data['upload']));
		
		//$ytcnt+=$data['comment']+$data['favourite']+$data['upload'];
		$ytCommentCnt+=$data['comment'];
		$ytFavouriteCnt+=$data['favorite'];
		$ytUploadCnt+=$data['upload'];
		$i++;
	}
	$ytcnt=$ytCommentCnt+$ytFavouriteCnt+$ytUploadCnt;
	$dataSet->addSerie("Youtube", $serie2);
}


if($mbuserName!="")
{
	$mb=new MantisBt();
	$mbarray=$mb->IssuesCountByTime($mbuserName, $arrayDate);
	$serie3 = new XYDataSet();
	$i=0;
	foreach ($mbarray as $data)
	{
		$date = date("d-m-Y", strtotime($arrayDate[$i]['startdate']));
		$serie3->addPoint(new Point($date,$data['reportedCount']+$data['assignedCount']));
		$mbReportedIssueCnt+=$data['reportedCount'];
		$mbAssignedIssueCnt+=$data['assignedCount'];
		$i++;
	}
	$mbcnt=$mbReportedIssueCnt+$mbAssignedIssueCnt;
	$dataSet->addSerie("Mantisbug", $serie3);
}

if($bbuserName!="")
{
	$bb=new BitBucket();
	$bbarray=$bb->CommitCountByTime($bbuserName, $arrayDate);
	$serie4 = new XYDataSet();
	$bbFollowedRepositoryCnt=$bb->GetRepositoryCount($bbuserName);
	$i=0;
	foreach ($bbarray as $data)
	{
		$date = date("d-m-Y", strtotime($arrayDate[$i]['startdate']));
		$serie4->addPoint(new Point($date,$data['count']));
		$bbCommitCnt+=$data['count'];
		$i++;
	}
	$bbcnt=$bbCommitCnt+$bbFollowedRepositoryCnt;
	$dataSet->addSerie("Bitbucket", $serie4);

}

if(count($userNameArray)!=0)
{
	$chart->setDataSet($dataSet);
	$chart->setTitle("SR Analytics");
	$chart->render("image/linechart.png");
	echo '<html>
	<head>
	<LINK href="style.css" rel="stylesheet" type="text/css">
	<title>Facebook timeline</title>
	</head>
	<body style="background-color: #e7ebf2; font-family: arial; font-size: 12px;">
	<div class="header">
	<b>UserName : '.$srusername.'</b>
	<table><tr><td width="400px">
	<p>Facebook
	<ul><li>Post : '.$fbPostCnt.'</li>
	<li>Comment : '.$fbCommentCnt.'</li>
	<li>Like : '.$fbLikeCnt.'</li>
	</ul>
	</td><td width="400px">
	</p>
	<p>Youtube
	<ul><li>Upload : '.$ytUploadCnt.'</li>
	<li>Comment : '.$ytCommentCnt.'</li>
	<li>Favourite : '.$ytFavouriteCnt.'</li>
	</ul>
	</p></td></tr>
	<tr><td width="400px">
	<p>Mantisbug
	<ul><li>Reported Issue : '.$mbReportedIssueCnt.'</li>
	<li>Assigned Issue : '.$mbAssignedIssueCnt.'</li>
	</ul>
	</td><td width="400px">
	</p>
	<p>Bitbucket
	<ul><li>Commit : '.$bbCommitCnt.'</li>
	<li>Repository Followed : '.$bbFollowedRepositoryCnt.'</li>
	</ul>
	</p></td></tr>
	</table>
	</div>';

	echo '<br>';
	echo '<CENTER><img src="image/linechart.png" align="middle"></CENTER>';
	echo '<br>';



	$chart1 = new PieChart(800,250);

	$dataSet = new XYDataSet();
	$dataSet->addPoint(new Point("Facebook", $fbcnt));
	$dataSet->addPoint(new Point("YouTube", $ytcnt));
	$dataSet->addPoint(new Point("BitBucket",$mbcnt));
	$dataSet->addPoint(new Point("BugMantis", $bbcnt));
	$chart1->setDataSet($dataSet);
	$chart1->setTitle("SR Analytics");
	$chart1->render("image/piechart.png");
	echo '<CENTER><img src="image/piechart.png" align="center"><CENTER>';

	echo '</body>
	</html>';
}
else
{
	echo '<html>
	<head>
	<LINK href="style.css" rel="stylesheet" type="text/css">
	<title>Facebook timeline</title>
	</head>
	<body style="background-color: #e7ebf2; font-family: arial; font-size: 12px;">
	<div class="header">
	<b>UserName not exist</b></div>
	';	
}

function GetWeeklyDate()
{
	$date = date('Y-m-j');
	$date =  strtotime ( '+1 day' , strtotime ( $date ));
	$date = date ( 'Y-m-j' , $date );
	$arrayDate=array();
	for ($i=0;$i<15;$i++)
	{
		$newDate1 =  strtotime ( '-1 day' , strtotime ( $date ) ) ;
		$newDate = strtotime ( '-1 week' , strtotime ( $date ) ) ;
		$date1 = date ( 'Y-m-j' , $newDate1 );
		$date = date ( 'Y-m-j' , $newDate );
		array_push($arrayDate,array('startdate'=>$date,'enddate'=>$date1));
	}
	$arrayDate=array_reverse($arrayDate);
	return $arrayDate;
}

?>
