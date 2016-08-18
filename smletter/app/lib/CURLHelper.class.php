<?php

class CURLHelper
{

	public static function call($url, $post = null, $time_out = null, $retries = 0, $headers = null)
	{ 
		try {
			return self::_call($url, $post, $time_out, $retries, $headers);
		} catch (Exception $e) {
			Log::error(__METHOD__.'@'.__LINE__.LOG_SEP.$e->getMessage());
			return array("http_code"=>"-1", "error_text"=>"CURL ERROR", "response_body"=>"");
		}
	}
	
	private static function _call($url, $post = null, $time_out = null, $retries = 0, $headers = null) {
		$curl = curl_init($url);
		
		if(is_resource($curl) === true){
			curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, isset($time_out) ? $time_out : constant('CURL_TIMEOUT_SEC'));
			if(isset($headers)){
				curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
			}
				
			//			curl_setopt($curl, CURLOPT_FAILONERROR, true);
			//			curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
			//			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		
			if(isset($post) === true){
				curl_setopt($curl, CURLOPT_POST, true);
				curl_setopt($curl, CURLOPT_POSTFIELDS, (is_array($post) === true) ? http_build_query($post) : $post);
			}
		
			$result = false;
			$isReTry = false;
			while(($result === false) && ($retries-- >= 0)){
				if($isReTry){
					usleep(50);
				}
				$result = curl_exec($curl);
				$isReTry = true;
			}
		}
		
		
		$array = array();
		$array['http_code'] = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		$array['response_body'] = $result;
		$array['error_text'] = curl_error($curl);
		
		curl_close($curl);
		
		return $array;
	}
}

?>