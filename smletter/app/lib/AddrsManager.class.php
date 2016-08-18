<?php

class AddrsManager extends SQLiteManager
{
	const DB_NAME = "send_addrs.db";

	public function __construct($account)
    {
		parent::__construct($account, self::DB_NAME);
		Log::debug(__METHOD__.'@'.__LINE__.LOG_SEP.$this->__account.LOG_SEP.$this->__db_name);
    }

	public function query(array $param=null){
		$sqlArr = array("SELECT * FROM send_addrs WHERE 1=1 ");
		
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
		$sql = 'SELECT * FROM send_addrs WHERE 1=1';
		$sql2 = 'SELECT count(*) AS total FROM send_addrs WHERE 1=1';
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
	 *     'addrlist_name' => string ''  地址列表名
	 *     'addrlist_alias' => string '' 地址列表别名
	 *     'addrlist_file' => string ''  地址列表对应文件名
	 *     'addrs_sum' => string '' 	列表包含地址数
	 *     'status' => string '' 		状态
	 *     'create_time' => string '' 	创建时间UNIX时间戳
	 *     'used_count' => string ''	使用计数
	 *     'description' => string '' 	描述
	 *     'tag' => string '' 			标签
	 * @return mixed|NULL
	 */
	public function add(array $rec){
		$sql = "INSERT INTO send_addrs ";
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
		$sql = "UPDATE send_addrs SET ";
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
		$sql = "DELETE FROM send_addrs ";
		$sql .= (' WHERE id='.self::sql_escape_string($id));
	
		Log::debug(__METHOD__.'@'.__LINE__.LOG_SEP.$this->__account.LOG_SEP.$sql);
		$data = $this->execute($sql);
		if($data['code'] == 200){
			return $data['changed_rows'];
		}
	
		Log::error(__METHOD__.'@'.__LINE__.LOG_SEP.$this->__account.LOG_SEP.$sql.LOG_SEP.$data['code']);
			
		return null;
	}
	//查询现有 db index
	public function query_db() {
		$sql = "SELECT DISTINCT addrlist_file AS idx FROM send_addrs WHERE 1=1 ORDER BY idx";
		$data = $this->execute($sql);
		Log::debug(__METHOD__.'@'.__LINE__.LOG_SEP.$this->__account.LOG_SEP.$sql.LOG_SEP.json_encode($data));
		
		if($data['code'] == 200){
			Log::debug(__METHOD__.'@'.__LINE__.LOG_SEP.$this->__account.LOG_SEP.json_encode($data['result']));
			return $data['result'];
		}
		
		Log::error(__METHOD__.'@'.__LINE__.LOG_SEP.$this->__account.LOG_SEP.$sql.LOG_SEP.$data['code']);
		
		return null;
	}

}

?>