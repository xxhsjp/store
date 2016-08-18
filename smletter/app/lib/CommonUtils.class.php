<?php

class CommonUtils
{
	private static $__detect_charset = array('GB18030','ASCII','JIS','SJIS','UTF-8','EUC-CN','EUC-TW','EUC-JP','BIG-5');
	private static $__mapping_charset = array('GBK'=>'GB18030',
			'GB2312'=>'GB18030',
			'UTF-7'=>'UTF-8',
			'UTF8'=>'UTF-8'
			);
	
	
	public static function str_conv_to_utf8($string){
		if(empty($string)){
			return $string;
		}
		$ct = @mb_detect_encoding($string, self::$__detect_charset);
		if(in_array($ct, self::$__detect_charset) && strcasecmp($ct, 'utf-8') !== 0){
			$iconv_res =  self::iconv($ct, "utf-8", $string, false);
			return $iconv_res;
		}
		
		return $string;
	}
	
	public static function str_byte_length($str){
		if (empty($str)) {
			return 0;
		}
		$len = (strlen($str) + mb_strlen($str,'UTF8')) / 2;
		
		return $len;  
	}
	
	public static function write_to_file($filename, $content){
		$dir = dirname($filename);
		if(!file_exists($dir)){
			$r = mkdir($dir, 0777, true);
			if($r === false){
				Log::error(__METHOD__.'@'.__LINE__.LOG_SEP.'Fail to mkdir: '.$dir);
				exit;
			}
		}
		$tmpfile = $filename.'_'.rand(100000, 999999).'.temp';
		$r_p = file_put_contents($tmpfile, $content);
		$r_r = rename($tmpfile, $filename);
		$r = true;
		if($r_r === false || $r_p === false){
			Log::error(__METHOD__.'@'.__LINE__.LOG_SEP.'Fail to saveFile: '.$tmpfile.' mv to '.$filename);
			$r = false;
		}
		return $r;
	}
	
	public static function get_ini_array($ini_path){
		$c = file_get_contents($ini_path);
		$r = parse_ini_string($c, true);
		
		return $r;
	}
	
	public static function get_http_raw_body(){
		return @file_get_contents('php://input');
	}
	
	public static function generate_mail_id() {
		$mail_id = time().'_'.ip2long(self::getClientIp()).'_'.self::generate_rand_8_hex().'_'.self::generate_rand_8_hex();
		return $mail_id;
	}
	
	public static function generate_random_name() {
		$name = time().'_'.rand(10000, 99999).'_'.rand(100000, 999999);
		return $name;
	}
	
	public static function getLineCountOfFile($file_path, $strict=false){
		if(!file_exists($file_path)){
			return 0;
		}
		$line_info = exec("wc -l $file_path");
		$line_info = explode(" ", $line_info);
		$fix = 0;
		if($strict === true){
			$fp = fopen($file_path,"r");
			fseek($fp, -1, SEEK_END);
			$data = fread($fp, 1);
			fclose($fp);
			if($data != "\n"){
				$fix = 1;
			}
		}
		
		return (int)$line_info[0] + $fix;
	}
	
	public static function file_lastline($file){
		$fp = fopen($file, 'r');
		fseek($fp, -1, SEEK_END);
		$s = '';
		while(($c = fgetc($fp)) !== false) {
			if($c == "\n" && $s) break;
			$s = $c . $s;
			fseek($fp, -2, SEEK_CUR);
		}
		fclose($fp);
		return $s;
	}
	
	public static function handle_file_callback($file, $callback){
		if(!file_exists($file)){
			return;
		}
		$fp = fopen($file,"r");
		while(!feof($fp)){
			$content = fread($fp,1024);
			if(is_callable($callback)){
				$callback($content);
			}
		}
		fclose($fp);
	}
	
	public static function getClassConstants($cls_names){
		$val_arr = array();
		foreach ($cls_names as $cls_name) {
			$rc = new ReflectionClass($cls_name);
			$v = $rc->getConstants();
			$val_arr += $v;
		}
		
		return $val_arr;
	}
	
	public static function getClientIp(){
		if(isset($_SERVER['HTTP_X_FORWARDED_FOR'])){
			return $_SERVER['HTTP_X_FORWARDED_FOR'];
		}else {
			return isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : "UNKNOWN";
		}
	}

	public static function generate_rand_8_hex(){
		$hex8 = '';
		for($i=0;$i<8;$i++) {
			$hex8 = $hex8.dechex(rand(0, 15));
		}
		return $hex8;
	}
	
	public static function encoding_content($content, $encoding='base64') {
		$encoded = '';
		if(strcasecmp($encoding, 'base64') === 0){
			$encoded = base64_encode($content);
		}else if(strcasecmp($encoding, 'quoted-printable') === 0){
			$encoded = quoted_printable_decode($content);
		}else {
			$encoded = $content;
		}
		if(strlen($encoded) > 76){
			$encoded = chunk_split($encoded, 76, "\r\n");
		}
		return $encoded;
	}
	
	public static function get_addr_info($string){
		if(strpos($string, '<') === false){
			return array('', $string);
		}
		$r = preg_replace('/(.+?)?(\s*<.+@.+(?:\..+)+>)/',
				"$1,$2",
				$string);
		$r = str_replace('"', "", $r);
		$r = str_replace('<', "", $r);
		$r = str_replace('>', "", $r);
		return explode(',', $r);
	}
	
	public static function encode_rfc2047_mail($string){
		if(strpos($string, "@") === false){
			return CommonUtils::encode_rfc2047($string);
		}
		$r = preg_replace_callback('/(.+?)(\s*<.+@.+(?:\..+)+>)/',
				function ($matches){
					$addr = $matches[2];
					$name = $matches[1];
					return CommonUtils::encode_rfc2047($name).$addr;
				},
				$string);
	
		return $r;
	}
	
	public static function is_rfc2047($string){
		return preg_match('/=\?.*\?[Q|B|q|b]\?.+\?=/', $string);
	}
	
	public static function encode_rfc2047($string){
		if(empty($string)){
			return $string;
		}
		return '=?UTF-8?B?'.base64_encode($string).'?=';
	}
	
	public static function iconv($from_charset, $tar_charset, $content, $try=true){
		$charset = strtoupper($from_charset);
		if(isset(self::$__mapping_charset[$charset])){
			$charset = self::$__mapping_charset[$charset];
		}
		$r =  @iconv($charset, $tar_charset.'//IGNORE', $content);
		if($r === false && $try === true){
			$r = @mb_convert_encoding($content, $tar_charset, $charset);
		}
		
		return $r === false ? $content : $r;
	}
	
	public static function decode_rfc2047($string){
		if(0 === self::is_rfc2047($string)){
			$ct = @mb_detect_encoding($string, self::$__detect_charset);
			if(in_array($ct, self::$__detect_charset)){
				$iconv_res =  self::iconv($ct, "utf-8", $string, false);
				return $iconv_res;
			}
			return $string;
		}
		$r = preg_replace_callback('/=\?(.*?)\?([Q|B|q|b])\?(.+?)\?=/',
				function ($matches){
					$decoded = $matches[0];
					$content = $matches[3];
					$encoding = $matches[2];
					$charset = $matches[1];
						
					if(strcasecmp($encoding, 'q') === 0){
						$content = quoted_printable_decode($content);
					}else if(strcasecmp($encoding, 'b') === 0){
						$content = base64_decode($content);
					}else {
						return $decoded;
					}
					if(strcasecmp($charset, 'utf8') === 0 || strcasecmp($charset, 'utf-8') === 0){
						$decoded = $content;
					}else {
						$decoded = CommonUtils::iconv($charset, 'UTF-8', $content, false);
					}
					return $decoded;
				},
				$string);
	
		return $r;
	}
	
	public static function htmlFilter($str){
		if(!empty($str)){
			$str=preg_replace("/<(head.*?)>(.*?)<(\/head.*?)>/si","",$str); //过滤style标签
			$str=preg_replace("/<(style.*?)>(.*?)<(\/style.*?)>/si","",$str); //过滤style标签
			$str=preg_replace("/<(\/?style.*?)>/si","",$str); //过滤style标签
			$str=preg_replace("/<(script.*?)>(.*?)<(\/script.*?)>/si","",$str); //过滤script标签
			$str=preg_replace("/<(\/?script.*?)>/si","",$str); //过滤script标签
			$str=preg_replace("/javascript/si","#javascript",$str); //过滤script标签
			$str=preg_replace("/vbscript/si","#vbscript",$str); //过滤script标签
			$str=preg_replace("/\s+on([a-z]+)\s*=/si","#on\\1=",$str); //过滤script标签
		}
	
		return $str;
	}
	
	public static function html2text($str){
		if(!empty($str)){
			$str=preg_replace("/<(head.*?)>(.*?)<(\/head.*?)>/si","",$str); //过滤style标签
			$str=preg_replace("/<(style.*?)>(.*?)<(\/style.*?)>/si","",$str); //过滤style标签
			$str=preg_replace("/<(\/?style.*?)>/si","",$str); //过滤style标签
			$str=preg_replace("/<(script.*?)>(.*?)<(\/script.*?)>/si","",$str); //过滤script标签
			$str=preg_replace("/<(\/?script.*?)>/si","",$str); //过滤script标签
			
			$str=preg_replace_callback("/<(\/?[^>\s]+)[^>]*>/si", function($matchs){
				$m1 = $matchs[1];
				$return_str = "";
				$block_els = array('div','p');
				foreach ($block_els as $el) {
					if(strcasecmp($m1, '/'.$el)){
						$return_str = PHP_EOL;
						break;
					}
				}
				
				return $return_str;
			},$str);
		}
	
		return $str;
	}
	
	public static function getHash32($string) {
		$hash = sprintf("%02d", intval(fmod(floatval(sprintf("%u", crc32($string))), 32)));
		Log::debug(__METHOD__.'@'.__LINE__.LOG_SEP.$hash);
	
		return $hash;
	}
	
	public static function create_guid() {
		$uuid = com_create_guid();
	
		return trim($uuid, '{}');
	}
}

?>