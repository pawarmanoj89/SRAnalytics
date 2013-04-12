<?php
print('qqqqqqq');


include_once ("dbhandler.php");
$string=$_GET["q"];

$db = new dbhandler();

$alluserids=$db->GetAllUserIds($string);
if ($alluserids != null)
		{ 
               $count = 0;
            
				print("<script src='script/script.js' type='text/javascript'>");
				print("setAllUsersId()");
				
				print("");
				print("");
				
				print("</script>");
				
		}
?>F:\SRAnalytics\SecurityReasearchAnalyticsfinal\SecurityReasearchAnalytics\script\getalluserid.php