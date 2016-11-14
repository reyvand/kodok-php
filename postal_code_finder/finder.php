<?php
	
	if($_SERVER['argc'] == 1) {
		echo "[+] usage : php ".basename(__FILE__)." city_name \n\n";
	} else {
		$param = '';
		for($i=1; $i<count($_SERVER['argv']); $i++) {
			if($i>1) {
				$param .= ' '.$_SERVER['argv'][$i];
			}
			else {
				$param .= $_SERVER['argv'][$i];
			}
		}
		$x = curl_init('http://carikodepos.com/?s='.urlencode($param).' ');
		$fp = fopen("res.txt", "w");

		curl_setopt($x, CURLOPT_FILE, $fp);
		curl_setopt($x, CURLOPT_HEADER, 0);
		curl_setopt($x, CURLOPT_USERAGENT, 'Mozilla/5.0 (X11; Ubuntu; Linux i686; rv:49.0) Gecko/20100101 Firefox/49.0');
		curl_setopt($x, CURLOPT_FOLLOWLOCATION, 1);
		
		curl_exec($x);
		curl_close($x);

		fclose($fp);
		
		$string = fread(fopen('res.txt', 'r'), filesize('res.txt'));
		preg_match_all('/<td>(.*?)<\/td>/', $string, $matches);
		$match = $matches[0];

		for($i=0; $i<count($match); $i++) {
			if(($i+1)%5 == 0) {
				echo strip_tags($match[$i])."\n";
			} else {
				echo strip_tags($match[$i])." ";
			}
		}
	}
 
