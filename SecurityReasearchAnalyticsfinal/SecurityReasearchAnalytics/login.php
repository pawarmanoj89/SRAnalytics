<?php

$con = mysql_connect("localhost","swati","swati007");
if (!$con)
   {
   die('Could not connect: ' . mysql_error());
   } 
 
mysql_select_db("cse_department", $con);

if($_SERVER["REQUEST_METHOD"] == "POST")
{
 // username and password sent from Form 
 $myusername = addslashes($_POST['username']); 
 $mypassword = addslashes($_POST['password']); 

 $sql = "SELECT id FROM admin WHERE username='$myusername' and password='$mypassword'";
 $result = mysql_query($sql);
 $row = mysql_fetch_array($result);
 $active = $row['active'];
 $count = mysql_num_rows($result);

if($count == 1)
 {
  session_register("myusername");
  $_SESSION['login_user'] = $myusername;
  header("location: why.php");
 }
else 
 {
  $error = "Your Login Name or Password is invalid";
 }

}
mysql_close($con);

?>