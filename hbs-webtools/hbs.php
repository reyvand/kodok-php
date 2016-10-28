<?php
	
	/******************************************************************
	|                      'Hungry_Birds_Squad'                       /
	|                    "expect what unexpected"                     /
	|                                                                 /
	|        we are : [t]0x1c || madfizh || atrph0s ||                /
	|                                                                 /
	|_________________________________________________________________/
	******************************************************************/

	//
	//	External Links & Reference : https://github.com/NTICompass/PHP-Base32
	//                               https://stackoverflow.com
	//

	error_reporting(0);
	class Base32{
    var $encode, $decode, $type;

    // Supports RFC 4648 (default) or Crockford (http://www.crockford.com/wrmg/base32.html)
    function __construct($alphabet='rfc4648'){
        $alphabet = strtolower($alphabet);
        $this->type = $alphabet;
        // Crockford's alphabet removes I,L,O, and U
        $crockfordABC = range('A', 'Z');
        unset($crockfordABC[8], $crockfordABC[11], $crockfordABC[14], $crockfordABC[20]);
        $crockfordABC = array_values($crockfordABC);

        $alphabets = array(
            'rfc4648' => array_merge(range('A','Z'), range(2,7), array('=')),
            'crockford' => array_merge(range(0,9), $crockfordABC, array('='))
        );
        $this->encode = $alphabets[$alphabet];
        $this->decode = array_flip($this->encode);
        // Add extra letters for Crockford's alphabet
        if($alphabet === 'crockford'){
            $this->decode['O'] = 0;
            $this->decode['I'] = 1;
            $this->decode['L'] = 1;
        }
    }

    private function bin_chunk($binaryString, $bits){
        $binaryString = chunk_split($binaryString, $bits, ' ');
        if($this->endsWith($binaryString, ' ')){
            $binaryString = substr($binaryString, 0, strlen($binaryString)-1);
        }
        return explode(' ', $binaryString);
    }

    // String <-> Binary conversion
    // Based off: http://psoug.org/snippet/PHP-Binary-to-Text-Text-to-Binary_380.htm

    private function bin2str($binaryString){
        // Make sure binary string is in 8-bit chunks
        $binaryArray = $this->bin_chunk($binaryString, 8);
        $string = '';
        foreach($binaryArray as $bin){
            // Pad each value to 8 bits
            $bin = str_pad($bin, 8, 0, STR_PAD_RIGHT);
            // Convert binary strings to ascii
            $string .= chr(bindec($bin));
        }
        return $string;
    }

    private function str2bin($input){
        $bin = '';
        foreach(str_split($input) as $s){
            // Return each character as an 8-bit binary string
            $s = decbin(ord($s));
            $bin .= str_pad($s, 8, 0, STR_PAD_LEFT);
        }
        return $bin;
    }

    // starts/endsWith from:
    // http://stackoverflow.com/questions/834303/php-startswith-and-endswith-functions/834355#834355

    private function startsWith($haystack, $needle){
        $length = strlen($needle);
        return substr($haystack, 0, $length) === $needle;
    }

    private function endsWith($haystack, $needle){
        $length = strlen($needle);
        return substr($haystack, -$length) === $needle;
    }

    // base32 info from: http://www.garykessler.net/library/base64.html

    // base32_encode
    public function base32_encode($string){
        // Convert string to binary
        $binaryString = $this->str2bin($string);

        // Break into 5-bit chunks, then break that into an array
        $binaryArray = $this->bin_chunk($binaryString, 5);

        // Pad array to be divisible by 8
        while(count($binaryArray) % 8 !== 0){
            $binaryArray[] = null;
        }

        $base32String = '';

        // Encode in base32
        foreach($binaryArray as $bin){
            $char = 32;
            if(!is_null($bin)){
                // Pad the binary strings
                $bin = str_pad($bin, 5, 0, STR_PAD_RIGHT);
                $char = bindec($bin);
            }
            // Base32 character
            $base32String .= $this->encode[$char];
        }

        return $base32String;
    }

    // base32_decode
    public function base32_decode($base32String){
        $base32Array = str_split(str_replace('-', '', strtoupper($base32String)));
        $binaryArray = array();
        $string = '';
        foreach($base32Array as $str){
            $char = $this->decode[$str];
            if($char !== 32){
                $char = decbin($char);
                $string .= str_pad($char, 5, 0, STR_PAD_LEFT);
            }
        }
        while(strlen($string) %8 !== 0){
            $string = substr($string, 0, strlen($string)-1);
        }
        return $this->bin2str($string);
    }
	}
	function b64($type,$text) {
		$act = ($type == 'enc') ? base64_encode($text) : (($type == 'dec') ? base64_decode($text) : die('invalid input'));
		return $act;
	}
	function r13($type=NULL,$text) {
		$act = str_rot13($text);
		return $act;
	}
	function rotate($type=NULL,$text,$count) {
		$alphabets = array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z','A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
		if(empty($count)) {
			$act = '"n" is required';
		} else {
			$act = '';
			for($i=0; $i<strlen($text); $i++) {
				if(array_search($text[$i], $alphabets)) {
					$y = array_search($text[$i], $alphabets) + $count;
					($y > 25 && array_search($text[$i], $alphabets) < 26) ? $y -= 26 : (($y > 51 && array_search($text[$i], $alphabets) > 25) ? $y -= 26 : $y = $y);
					$x = $alphabets[$y];
				} else {
					$x = $text[$i];
				}
				$act .= $x;
			}
		}
		return $act;
	}
	function hex($type,$text) {
		$act = ($type == 'enc') ? bin2hex($text) : (($type == 'dec') ? hex2bin($text) : die('invalid input'));
		return $act;
	}
	function ascii($type,$text) {
		if($type == 'enc') {
			$act = '';
			for($i=0; $i<strlen($text); $i++) {
				$act .= ord($text[$i]).' ';
			}
			$act = substr($act, 0, -1);
		} elseif($type == 'dec') {
			if(strpos($text,' ')) {
				$c = substr_count($text, ' ');
				$s = explode(' ', $text);
				$act = '';
				for($i=0; $i<=$c; $i++) {
					$act .= chr($s[$i]);
				}
			} else {
				$act = 'no whitespace detected, this method requires a whitespace';
			}
		}
		return $act;
	}
	function binary($type,$text) {
		if($type == 'enc') {
			$act = '';
			for($i=0; $i<strlen($text); $i++) {
				$act .= str_pad(decbin(ord($text[$i])), 8, ' ', STR_PAD_LEFT);
			}
			if(strpos($act, '  ')) {
				$act = str_replace('  ', ' ', substr($act, 1));
			} else {
				if(strlen($act) < 8) {
					$act = str_replace(' ', '', $act);
				} else {
					$act = substr($act, 1);
				}
			}
		} elseif($type == 'dec') {
			$x = explode(' ', $text);
			$act = '';
			for($i=0; $i<=substr_count($text, ' '); $i++) {
				$act .= chr(bindec($x[$i]));
			}
		}
		return $act;
	}
	function md5_enc($type,$text) {
		$act = ($type == 'enc') ? md5($text) : (($type == 'dec') ? 'unavailable' : die('invalid input'));
		return $act;
	}
	function sha1_enc($type,$text) {
		$act = ($type == 'enc') ? sha1($text) : (($type == 'dec') ? 'unavailable' : die('invalid input'));
		return $act;
	}
	function overflow($type=NULL,$text,$count) {
		if(empty($count)) {
			echo '"n" is required';
		} else {
			for ($i=1; $i<=$count ; $i++) { 
				echo $text;
			}
		}
	}
	function str_cnt($type=NULL,$text) {
		return strlen($text);
	}
	function reverse($type=NULL,$text) {
		$act = strrev($text);
		return $act;
	}
	function b32($type,$text) {
		$x = new Base32;
		$act = ($type == 'enc') ? $x->base32_encode($text) : (($type == 'dec') ? $x->base32_decode($text) : die('invalid input'));
		return $act;
	}
	function hexchar($type=NULL,$text) {
		return preg_replace("/[^A-Fa-f0-9 ]/", '', $text);
	}
	function split_char($type,$text,$count) {
		if(empty($count)) {
			$act = 'invalid';
		} else {
			$act = ($type == 'enc') ? chunk_split($text,$count,' ') : (($type == 'dec') ? str_replace(' ', '', $text) : die('invalid input'));
		}
		return $act;
	}
?>

<!DOCTYPE html>
<html>
<head>
	<title>priv8 | hungrybirds_squad</title>
	<style type="text/css">
		* {
			margin: 0;
			padding: 0;
		}
		body {
			background-color: #222;
		}
		code {
			color: #ccc;
			text-align: center;
			font-family: consolas,'Courier 10 Pitch',sans-serif;
			display: block;
			margin-top: 2px;
			font-size: 15px;
		}
		textarea {
			background-color: #222;
			color: #fff;
			padding: 5px 8px 5px 8px;
			border: 0;
			border: 1px solid #777;
			webkit-transition: all 0.4s ease;
			moz-transition: all 0.4s ease;
			transition: all 0.4s ease;
		}
			textarea:focus {
				border-color: #fff;
			}
		input {
			height: 20px;
			width: 37px;
			font-size: 14px;
		}
		select {
			width: 80px;
			height: 28px;
		}
		.head {
			color: #00FF30;
			text-align: center;
			font-family: consolas,'Courier 10 Pitch',sans-serif;
			display: block;
			margin-top: 7px;
			font-weight: normal;
		}
		.main {
			width: 60%;
			margin: 40px auto;
			overflow: hidden;
		}
		.options {
			margin: 3% auto 3% auto;
			width: 450px;
			overflow: hidden;
		}
		.encrypt {
			float: left;
			width: 70px;
			cursor: pointer;
			border: 0;
			height: 30px;
			background-color: #222;
			color: blue;
			border: 1px solid #777;
			webkit-transition: all 0.4s ease;
			moz-transition: all 0.4s ease;
			transition: all 0.4s ease;
		}
			.encrypt:hover {
				border-color: blue;
			}
		.decrypt {
			float: right;
			width: 70px;
			cursor: pointer;
			border: 0;
			height: 30px;
			background-color: #222;
			color: red;
			border: 1px solid #777;
			webkit-transition: all 0.4s ease;
			moz-transition: all 0.4s ease;
			transition: all 0.4s ease;
		}
			.decrypt:hover {
				border-color: red;
			}
	</style>
</head>
<body>
	<div id="wrapper">
		<h2 class="head">Hungry_Birds_Squad</h2>
		<code>"  expect what unexpected  "</code>
		<div class="main">
			<div class="input">
				<form action="" method="post">
				<center><textarea name="txtInput" rows="3" cols="60" placeholder="input ..."><?= $_POST['txtInput']; ?></textarea></center>
			</div>
			<div class="options">
				<center>
				<button type="submit" name="enc" class="encrypt">encrypt</button>
				<select name="opt">
					<option>----------</option>
					<option value="b64">base64</option>
					<option value="b32">base32</option>
					<option>----------</option>
					<option value="hex">hexadecimal</option>
					<option value="binary">binary</option>
					<option value="ascii">ascii code</option>
					<option>----------</option>
					<option value="r13">rot13</option>
					<option value="rotate">rot(n)</option>
					<option value="rotate_brute">rot(brute 1-25)</option>
					<option>----------</option>
					<option value="md5_enc">MD5 (enc)</option>
					<option value="sha1_enc">SHA1 (enc)</option>
					<option>----------</option>
					<option value="overflow">overflow</option>
					<option value="str_cnt">count str</option>
					<option value="reverse">reverse it</option>
					<option value="hexchar">hex char</option>
					<option value="split_char">split char</option>
				</select>
				<input type="number" name="x" placeholder="n">
				<button type="submit" name="dec" class="decrypt">decrypt</button></center>
			</form>
			</div>
			<div class="output">
				<center><textarea name="txtOutput" rows="3" cols="60" readonly><?= isset($_POST['enc']) ? $_POST['opt']('enc',$_POST['txtInput'],$_POST['x']) :  (isset($_POST['dec']) ? $_POST['opt']('dec',$_POST['txtInput'],NULL) : false) ?></textarea></center>
			</div>
		</div>
	</div>
</body>
</html>
