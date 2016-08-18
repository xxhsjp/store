<?php

class SessionManager
{
	const LOGIN_INFO = 'SESSION_LOGIN_INFO' ;
	const USER_PERMISSION = 'SESSION_USER_PERMISSION';
	const ADMIN_ACCOUNT = 'SESSION_ADMIN_ACCOUNT';
	const EFFECTIVE_ORDER = 'SESSION_EFFECTIVE_ORDER';
	
	public static function destroySession() {
		$_SESSION = array();
		return session_destroy();
	}
	
	public static function increaseOrderUsedCount($count) {
		$_SESSION[self::EFFECTIVE_ORDER]['used_count'] = (string)((int)$_SESSION[self::EFFECTIVE_ORDER]['used_count']+(int)$count);
	}
	
	public static function setEffectiveOrder($info) {
		$_SESSION[self::EFFECTIVE_ORDER] = $info;
	}
	
	public static function getEffectiveOrder() {
		$info = isset($_SESSION[self::EFFECTIVE_ORDER])?$_SESSION[self::EFFECTIVE_ORDER]:null;
		return $info;
	}
	
	public static function setAdminAccount($account) {
		$_SESSION[self::ADMIN_ACCOUNT] = $account;
	}
	
	public static function getAdminAccount() {
		$info = isset($_SESSION[self::ADMIN_ACCOUNT])?$_SESSION[self::ADMIN_ACCOUNT]:null;
		return $info;
	}
	
	public static function setUserPermission(array $info) {
		$_SESSION[self::USER_PERMISSION] = $info;
	}
	
	public static function getUserPermission() {
		$info = isset($_SESSION[self::USER_PERMISSION])?$_SESSION[self::USER_PERMISSION]:null;
		return $info;
	}
	
	public static function setLoginInfo(array $info) {
		$_SESSION[self::LOGIN_INFO] = $info;
	}
	
	public static function getLoginInfo() {
		$info = isset($_SESSION[self::LOGIN_INFO])?$_SESSION[self::LOGIN_INFO]:null;
		return $info;
	}
	
	public static function getAccount() {
		$info = isset($_SESSION[self::LOGIN_INFO]['account'])?$_SESSION[self::LOGIN_INFO]['account']:null;
		return $info;
	}
	
	
	public static function getInfoByKey($key) {
		$info = isset($_SESSION[self::LOGIN_INFO][$key])?$_SESSION[self::LOGIN_INFO][$key]:null;
		return $info;
	}
	
	public static function setInfoByKey($key, $value) {
		$_SESSION[self::LOGIN_INFO][$key] = $value;
	}
	
	public static function getUserAndDomain() {
		$account = self::getAccount();
		if($account !== null){
			return explode("@", $account);
		}
		return null;
	}
}

?>
