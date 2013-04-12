<?php
print('autoCompl');
include_once ("dbhandler.php");
$string=$_GET["q"];

$db = new dbhandler();
$hintarray=$db->GetSuggestion($string);
if ($hintarray != null)
		{ 
               $count = 0;
            
				print("<script src='script/script.js' type='text/javascript'></script>");
				print("<table id=\"suggestionTable\"  cellspacing=\"0\" cellpadding=\"0\" width=\"100\" style=\"font-weight: bold,text-align: left;\">");
               foreach($hintarray as $hint)
			   {
                    print("<tr onmouseover=\"style.color='red'\" onmouseout=\"style.color='black'\" style=\"background:white; color:black;\" >");
        			print("<td name=\"row".$count."\" id=\"row".$count."\" onclick=\"set(this.id);\">");
					print($hint['srusername']);
                    print("</td>");
                    print("</tr>");
					$count++;
                    if ($hint == null || $count == 10) {
                        break;
                    }
				
                }
		}
?>