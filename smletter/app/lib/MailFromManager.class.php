<?php

class MailFromManager extends SQLiteManager
{
	const DB_NAME = "send_mailfrom.db";

	public function __construct($account)
    {
		parent::__construct($account, self::DB_NAME);
		Log::debug(__METHOD__.'@'.__LINE__.LOG_SEP.$this->__account.LOG_SEP.$this->__db_name);
    }

	public function query(array $param=null){
		$sqlArr = array('SELECT * FROM send_mailfrom WHERE 1=1 ');
		if(isset($param['where'])){
			if(is_array($param['where'])){
				foreach($param['where'] as $k => $v){
					$sqlArr[] = 'AND '.$k.'='.self::sql_escape_string($v);
				}
			}else {
				$sqlArr[] = 'AND '.$param['where'];
			}
		}
		
		if(isset($param['order'])){
			$sqlArr[] = 'ORDER BY '.implode(',', $param['order']);
		}
		if(isset($param['limit'])){
			$sqlArr[] = 'LIMIT '.implode(',', $param['limit']);
		}
		
		$sql = implode(' ', $sqlArr);
		
		Log::debug(__METHOD__.'@'.__LINE__.LOG_SEP.$this->__account.LOG_SEP.$sql);
		$data = $this->execute($sql);
		if($data['code'] == 200){
			return $data['result'];
		}

		Log::error(__METHOD__.'@'.__LINE__.LOG_SEP.$this->__account.LOG_SEP.$sql.LOG_SEP.$data['code']);
   
		return null;
	}
	
	public function query_by_page(array $param=null){
		$sql = 'SELECT * FROM send_mailfrom WHERE 1=1';
		$sql2 = 'SELECT count(*) AS total FROM send_mailfrom WHERE 1=1';
		$where = "";
		$order = "";
		$limit = "";
		if(isset($param['where'])){
			if(is_array($param['where'])){
				foreach($param['where'] as $k => $v){
					$where .= ' AND '.$k.'='.self::sql_escape_string($v);
				}
			}else {
				$where = ' AND '.$param['where'];
			}
		}
		if(isset($param['order'])){
			$order = ' ORDER BY '.implode(',', $param['order']);
		}
		if(isset($param['limit'])){
			$limit = ' LIMIT '.implode(',', $param['limit']);
		}
	
		$sql .= $where.$order.$limit;
		$sql2 .= $where;
		Log::debug(__METHOD__.'@'.__LINE__.LOG_SEP.$this->__account.LOG_SEP.$sql);
		$data1 = $this->execute($sql);
		Log::debug(__METHOD__.'@'.__LINE__.LOG_SEP.$this->__account.LOG_SEP.$sql2);
		$data2 = $this->execute($sql2);
	
		if($data1['code'] == 200 && $data2['code'] == 200){
			return array("result"=>$data1['result'], "total"=>$data2['result'][0]['total']);
		}
	
		Log::error(__METHOD__.'@'.__LINE__.LOG_SEP.$this->__account.LOG_SEP.$sql.LOG_SEP.$data['code']);
			
		return null;
	}
	
	/**
	 * @param array $rec
	 *     'type' => string ''   发信账号类型（触发：0, 群发: 1）
	 *     'mail_from' => string '' 发信账号
	 *     'status' => string '' 	状态
	 *     'reply_to' => string '' 	回复地址
	 *     'create_time' => string '' 创建时间UNIX时间戳
	 *     'desciption' => string ''  描述
	 *     'used_count' => string ''  使用计数
	 * @return mixed|NULL
	 */
	public function add(array $rec){
		$sql = "INSERT INTO send_mailfrom ";
		$keys = array();
		$values = array();
		$time = time();
		$rec['create_time'] = $time;
		$rec['last_modify_time'] = $time;
		foreach ($rec as $key => $value) {
			$keys[] = "'".$key."'";
			$values[] = "'".self::sql_escape_string($value)."'";
		}
		$sql .= '('.implode(',', $keys).') VALUES ('.implode(',', $values).')';
	
		Log::debug(__METHOD__.'@'.__LINE__.LOG_SEP.$this->__account.LOG_SEP.$sql);
		$data = $this->execute($sql);
		if($data['code'] == 200){
			return $data['changed_rows'];
		}
	
		Log::error(__METHOD__.'@'.__LINE__.LOG_SEP.$this->__account.LOG_SEP.$sql.LOG_SEP.$data['code']);
			
		return null;
	}
	
	public function update(array $rec){
		$sql = "UPDATE send_mailfrom SET ";
		$sets = array();
		$rec['last_modify_time'] = time();
		foreach ($rec as $key => $value) {
			$sets[] = "'".$key."' = '".self::sql_escape_string($value)."'";
		}
		$sql .= implode(',', $sets);
		$sql .= (' WHERE id='.self::sql_escape_string($rec['id']));
	
		Log::debug(__METHOD__.'@'.__LINE__.LOG_SEP.$this->__account.LOG_SEP.$sql);
		$data = $this->execute($sql);
		if($data['code'] == 200){
			return $data['changed_rows'];
		}
	
		Log::error(__METHOD__.'@'.__LINE__.LOG_SEP.$this->__account.LOG_SEP.$sql.LOG_SEP.$data['code']);
			
		return null;
	}
	
	public function del($id){
		$sql = "DELETE FROM send_mailfrom ";
		$sql .= (' WHERE id='.self::sql_escape_string($id));
	
		Log::debug(__METHOD__.'@'.__LINE__.LOG_SEP.$this->__account.LOG_SEP.$sql);
		$data = $this->execute($sql);
		if($data['code'] == 200){
			return $data['changed_rows'];
		}
	
		Log::error(__METHOD__.'@'.__LINE__.LOG_SEP.$this->__account.LOG_SEP.$sql.LOG_SEP.$data['code']);
			
		return null;
	}

}

?>