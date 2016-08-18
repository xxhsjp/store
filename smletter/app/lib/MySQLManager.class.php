<?php
require_once PATH_PROJ_ETC."conf_mysql.php";;

class MySQLManager
{
	private $__db = null;
	
	public function __construct($db_name="sml_account", $charset="utf8"){
		try {
			$dsn = "mysql:host=".MYSQL_SERVER_HOST1.";port=".MYSQL_SERVER_PORT1;
			$dsn .= ";dbname=$db_name;charset=$charset";
			$this->__db = new PDO($dsn, MYSQL_USER, MYSQL_USER_PWD);
			$this->__db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
			$this->__db->setAttribute(PDO::ATTR_STRINGIFY_FETCHES, true);
		} catch(PDOException $e){
			Log::error(__METHOD__.'@'.__LINE__.LOG_SEP.'Connect MySQL Error: '.$e->getMessage());
		}
		Log::debug(__METHOD__.'@'.__LINE__.LOG_SEP.'MySQL Server dsn: '.$dsn);
	}
	
	public function execute($sql, array $params=array()){
		$result = null;
		if(empty($this->__db)){
			Log::error(__METHOD__.'@'.__LINE__.LOG_SEP.'execute sql Error: pdb obj is null');
			return $result;
		}
		try {
			$rs = $this->__db->prepare($sql);
			$exec_result = $rs->execute($params);
			if($exec_result === false){
				Log::error(__METHOD__.'@'.__LINE__.LOG_SEP.'Error Info: '.json_encode($rs->errorInfo()));
				return false;
			}	
			if($this->_is_query($sql)){
				$rs->setFetchMode(PDO::FETCH_ASSOC);
				$result = $rs->fetchAll();
			}else {
				$result = $rs->rowCount();
			}
		} catch(PDOException $e){
			Log::error(__METHOD__.'@'.__LINE__.LOG_SEP.'execute sql Error: '.$e->getMessage());
		}
		if(Log::level()){
			Log::debug(__METHOD__.'@'.__LINE__.LOG_SEP.'MySQL SQL: '.$sql.' params:'.json_encode($params));
		}
		
		return $result;
	}
	
	private function _is_query($sql){
		$sql = trim(strtolower($sql));
		if(strpos($sql, "select") === 0){
			return true;
		}
		
		return false;
	}
	
	public static function sql_escape_string($keyWord){
// 		if(!empty($keyWord)){
// 			$keyWord = mysql_real_escape_string($keyWord);
// 		}
		return $keyWord;
	}
}

?>
