<?php

class SQLiteManager
{
	protected $__db_name;
	protected $__account;

	public function __construct($account, $db_name)
    {
		$this->__db_name = $db_name;
		$this->__account = $account;
    }
	
	/**
	 * @param unknown $sql
	 * @param array $pre_process    array("need_process_key"=>array(class, method),...);
	 * @return mixed
	 */
	public function execute($sql, array $pre_process = null){
		$c = new SQLiteCmmnd($this->__account, $this->__db_name, $sql);
		$json = SQLiteHttpClinetHelper::callSQLiteServer($c);
		Log::debug(__METHOD__.'@'.__LINE__.LOG_SEP.$this->__account.' - '.$json);
		$array = json_decode($json, true);
		if(isset($array['result'])){
			foreach($array['result'] as &$v){
				foreach($v as $_k => &$_v){
					$_v = base64_decode($_v);
					if(isset($pre_process) && isset($pre_process[$_k])){
						$_v = call_user_func($pre_process[$_k], $_v);
					}
				}
			}
		}
		Log::w_log(__METHOD__.'@'.__LINE__.LOG_SEP.$this->__account.LOG_SEP.$array['code'].LOG_SEP.$sql, LOG_OUTPUT_SQLITE_FILE);

		return $array;
	}

	public static function sql_escape_string($keyWord){
		if(!empty($keyWord)){
			$keyWord = SQLite3::escapeString($keyWord);
		}
		return $keyWord;
	}
}

?>