<?php

class CookieManager
{
	const LOGIN_ACCOUNT = '_ACCOUNT';
	const EXPIRE_SECS = 2592000; //60*60*24*30   30 days
	
	public static function clearSessionId() {
		setcookie(session_name(), '', time() - 86400, '/');
	}
	
	public static function setLoginAccount($account, $isRemember) {
		$cookie_key = PROJ_COOKIE_PREFIX.self::LOGIN_ACCOUNT;
		if($isRemember === true){
			setcookie($cookie_key, $account, time()+self::EXPIRE_SECS, '/');
		}else {
			setcookie($cookie_key, $account, time()-1000, '/');
		}
	}
	
	public static function getLoginAccount() {
		$cookie_key = PROJ_COOKIE_PREFIX.self::LOGIN_ACCOUNT;
		return isset($_COOKIE[$cookie_key]) ? $_COOKIE[$cookie_key] : "";
	}
	
}

?>
