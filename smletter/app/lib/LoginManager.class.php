<?php

class LoginManager
{
	
	public static $responseMap = array(
			"200" => "ok",
			"10403" => "domain_locked_er",
			"10415" => "domain_expired_er",
			"10608" => "user_login_ip_er",
			"10609" => "user_password_er",//password_er
			"10603" => "user_locked_er",
			"10608" => "user_expired_er",
			"10601" => "user_unopen_er",
			"user_error" => "other_er",
			"unknown_error" => "unknown_error",
			"user_info_parse_error" => "info_parse_error",
			"http_error" => "http_error"
	);
	
    public function login($account, $password) {
    	$am = new AccountInfoManager($account);
    	$login_info = $am->auth_login($account, $password);
    	$login_info = empty($login_info[0]) ? null : $login_info[0];
    	$code = self::$responseMap['10609'];
    	$info = array("account"=>$account, "lang"=>null, "skin"=>null);
    	if (!empty($login_info)) {
    		$time = time();
    		if((int)$login_info['status'] !== 1){
    			$code =  self::$responseMap['10601'];
    		}else if((int)$login_info['expired_time'] < $time && !empty($login_info['expired_time'])){
    			$code =  self::$responseMap['10608'];
    		}else if((int)$login_info['freeze_time'] > $time){
    			$code =  self::$responseMap['10603'];
    		}else {
    			$code = self::$responseMap['200'];
    			$info += $login_info;
    			SessionManager::setLoginInfo($info);
    		}
    	}
    	
    	if(Log::level()){
    		Log::debug(__METHOD__.'@'.__LINE__.LOG_SEP.$code.LOG_SEP.json_encode($info).LOG_SEP.json_encode($order));
    	}
    	Log::w_log(__METHOD__.'@'.__LINE__.LOG_SEP.CommonUtils::getClientIp().LOG_SEP.$code.LOG_SEP.json_encode($info), LOG_OUTPUT_LOGIN_FILE);
    	 
    	return $code;
    }
    
    public function register($account, $password){
    	$am = new AccountInfoManager($account);
    	$reg_result = $am->register($account, $password);
    	
    	return $reg_result;
    }
    
    public function get_user_type() {
    	$user_type = null;
    	$loginInfo = SessionManager::getLoginInfo();
    	$account = SessionManager::getAccount();
    	$type_mapping = array("1"=>"admin", "2"=>"agent", "3"=>"user");
    	if(isset($type_mapping[$loginInfo['type']])){
    		$user_type = $type_mapping[$loginInfo['type']];
    	}
    	
    	Log::debug(__METHOD__.'@'.__LINE__.LOG_SEP.$account.LOG_SEP.$user_type);
    
    	return $user_type;
    }
    
    public function logout() {
    	CookieManager::clearSessionId();
    	$account = SessionManager::getAccount();
    	$flag = SessionManager::destroySession();
    	Log::debug(__METHOD__.'@'.__LINE__.LOG_SEP.$account.LOG_SEP.$flag);
    	return $flag;
    }
    
    public function isLogin() {
    	$isLogin = true;
    	$loginInfo = SessionManager::getLoginInfo();
    	$account = SessionManager::getAccount();
    	if($loginInfo === null || $account === null){
    		$isLogin = false;
    	}
    	Log::debug(__METHOD__.'@'.__LINE__.LOG_SEP.$account.LOG_SEP.$isLogin);
    	 
    	return $isLogin;
    }
    
}

?>
