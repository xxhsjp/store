<?php

class SQLiteCmmnd
{
    public $type = "";
	public $username = "";
	public $dbname = "";
	public $sql_type = "";
	public $sql = "";

    public function __construct($username, $dbname, $sql, $db_type="")
    {
		$this->type = empty($db_type) ? SQLiter::getDBType($dbname) : $db_type;
		Log::debug(__METHOD__.'@'.__LINE__.LOG_SEP.$this->type);
		$this->username = $username;
		$this->dbname = $dbname;
		$this->sql = trim($sql);
		if (strpos(strtolower($this->sql), "select") === 0) {
			$this->sql_type = SQLiter::SQL_TYPE_SELECT;
		} else {
			$this->sql_type = SQLiter::SQL_TYPE_EXECUTE;
		}
    }

    
    public function getServerId() 
	{
		$userName = $this->username;
		$domain = null;
		$array = explode("@", trim($userName));
		if (count($array) == 1) {
			$domain = $array[0];
		} else if (count($array) == 2) {
			$domain = $array[1];
		} else {
			Log::error(__METHOD__.'@'.__LINE__.LOG_SEP. $userName);
		}
		$sid = CommonUtils::getHash32($domain);
		Log::debug(__METHOD__.'@'.__LINE__.LOG_SEP.'sqlite server id='.$sid);
		
		return $sid;
	}
    
}

?>