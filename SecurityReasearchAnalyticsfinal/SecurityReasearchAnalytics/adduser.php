<?php

$sruser=isset($_POST['srtext']) ? $_POST['srtext'] : null;
$fbuser=isset($_POST['fbtext']) ? $_POST['fbtext'] : null;
$ytuser=isset($_POST['yttext']) ? $_POST['yttext'] : null;
$mbuser=isset($_POST['mbtext']) ? $_POST['mbtext'] : null;
$bbuser=isset($_POST['bbtext']) ? $_POST['bbtext'] : null;

include_once ("dbhandler.php");
include ("menu.html");

	if($sruser !== null)
	{

		$db = new dbhandler();
			if( $db->InsertSRData($sruser,$fbuser,$ytuser,$mbuser,$bbuser))
			{
				echo $sruser . ' Added Successfully ';
			}

		
	}

include ("adduserindex.html");
?>
