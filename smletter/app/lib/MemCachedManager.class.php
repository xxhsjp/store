<?php
require_once PATH_PROJ_ETC."conf_memcached.php";

class MemCachedManager
{
	private $__memcached = null;
	
	public function __construct(){
		$this->__memcached = new Memcache();
		$this->__memcached->addserver(MEMCACHED_SERVER_HOST1, MEMCACHED_SERVER_PORT1);
		if(Log::level()){
			Log::debug(__METHOD__.'@'.__LINE__.LOG_SEP.'Memcached Server\'s version: '.$this->getVersion());
		}
	}
	
	public function getVersion(){
		$version = @$this->__memcached->getVersion();
		if($version === false){
			Log::error(__METHOD__.'@'.__LINE__.LOG_SEP.'Fail to getVersion of memcached');
		}
		return $version;
	}
	
	public function get($key, $flag=false){
		return @$this->__memcached->get($key, $flag);
	}
	
	public function set($key, $val, $flag=false, $expire=MEMCACHED_SERVER_EXPIRED){
		$result = @$this->__memcached->set($key, $val, $flag, $expire);
		if($result === false){
			Log::error(__METHOD__.'@'.__LINE__.LOG_SEP.'Fail to set into memcached:'.$key.LOG_SEP.$val);
		}
		return $result;
	}
	
	public function del($key){
		$result = @$this->__memcached->delete($key);
		if($result === false){
			Log::error(__METHOD__.'@'.__LINE__.LOG_SEP.'Fail to del key from memcached:'.$key);
		}
		return $result;
	}
	
}

?>
