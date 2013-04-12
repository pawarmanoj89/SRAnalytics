
<?php
		
	

include_once ("dbhandler.php");
$string=$_GET["q"];

$db = new dbhandler();
$alluserids=$db->GetAllUserId($string);

	
			
if ($alluserids != null)
		{ echo "<script src=\"script/script.js\" type=\"text/javascript\"></script>";
					echo "<script text=\"text/javascript\">";
		echo "$('ajax').alert(\"check\");";
		echo "</script>";
		
		}



?>