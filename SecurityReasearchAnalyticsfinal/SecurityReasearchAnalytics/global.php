<?php
/*
 * Author Kapil Sutariya
*/
class Constant
{
	/*
	 * This function takes url as input and returns content of url
	 */
	public static function GetURLContent($url)
	{
		$crl = curl_init();
		$timeout = 1000;	
		curl_setopt($crl, CURLOPT_URL,$url);
		curl_setopt($crl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($crl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($crl, CURLOPT_CONNECTTIMEOUT, $timeout);
		curl_setopt($crl, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($crl, CURLOPT_POST, false);
		$d = curl_exec($crl);
		return $d;
	
	}
	
}
?>