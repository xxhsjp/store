<?php

class TasksQManager extends SQLiteManager
{
	const BASE_NAME = "send_taskqueue";
	const Q_TEST = "_test.db";
	const Q_LV0 = "_lv0.db";
	const Q_LV1 = "_lv1.db";
	const Q_LV2 = "_lv2.db";
	const Q_LV3 = "_lv3.db";
	const Q_LV4 = "_lv4.db";
	const Q_LV5 = "_lv5.db";
	
	public function __construct($lv=null)
    {
    	$lv = isset($lv) ? $lv : self::Q_LV1;
		parent::__construct(TASK_SYS_ACCOUNT, self::BASE_NAME.$lv);
		Log::debug(__METHOD__.'@'.__LINE__.LOG_SEP.$this->__account.LOG_SEP.$this->__db_name);
    }

	public function query(array $param=null){
		
		$sqlArr = array('SELECT * FROM send_taskqueue WHERE 1=1 ');
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
		Log::debug(__METHOD__.'@'.__LINE__.LOG_SEP.$this->__account.LOG_SEP.$this->__db_name.LOG_SEP.$sql);
		$data = $this->execute($sql);
		if($data['code'] == 200){
			return $data['result'];
		}

		Log::error(__METHOD__.'@'.__LINE__.LOG_SEP.$this->__account.LOG_SEP.$this->__db_name.LOG_SEP.$sql.LOG_SEP.$data['code']);
   
		return null;
	}
	
	public function query_by_page(array $param=null){
		
		$sql = 'SELECT * FROM send_taskqueue WHERE 1=1';
		$sql2 = 'SELECT count(*) AS total FROM send_taskqueue WHERE 1=1';
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
		Log::debug(__METHOD__.'@'.__LINE__.LOG_SEP.$this->__account.LOG_SEP.$this->__db_name.LOG_SEP.$sql);
		$data1 = $this->execute($sql);
		Log::debug(__METHOD__.'@'.__LINE__.LOG_SEP.$this->__account.LOG_SEP.$this->__db_name.LOG_SEP.$sql2);
		$data2 = $this->execute($sql2);
		
		if($data1['code'] == 200 && $data2['code'] == 200){
			return array("result"=>$data1['result'], "total"=>$data2['result'][0]['total']);
		}
	
		Log::error(__METHOD__.'@'.__LINE__.LOG_SEP.$this->__account.LOG_SEP.$this->__db_name.LOG_SEP.$sql);
		 
		return null;
	}
	
	/**
	 * @param array $task = array (
	 *	    account => '' , 账号
	 *		level => '', 等级
	 *		testinfo => '' 测试信息
	 *		sendtime => '' 发送时间
	 *		mailfrom => '' 发信人
	 *		from => '' 信体发信人
	 *		replyto => '' 回复地址
	 *		subject => '' 主题
	 *		template => '' 模板dfs_id
	 *		addrlist => '' 地址列表sqlite db
	 *		mailid => '' mail 唯一标识
	 *		status => '' 状态 预留
	 *		flag => '' 标识 预留
	 *		type => '' 0: 非定时任务, 1: 定时任务
	 *		)
	 * @return mixed|NULL
	 */
	public function add(array $rec){
		
		$sql = "INSERT INTO send_taskqueue ";
		$keys = array();
		$values = array();
		$time = time();
		$rec['create_time'] = $time;
		$rec['last_modify_time'] = $time;
		$rec['status'] = "0";
		foreach ($rec as $key => $value) {
			$keys[] = "'".$key."'";
			$values[] = "'".self::sql_escape_string($value)."'";
		}
		$sql .= '('.implode(',', $keys).') VALUES ('.implode(',', $values).')';
		
		Log::debug(__METHOD__.'@'.__LINE__.LOG_SEP.$this->__account.LOG_SEP.$this->__db_name.LOG_SEP.$sql);
		$data = $this->execute($sql);
		if($data['code'] == 200){
			return $data['changed_rows'];
		}
	
		Log::error(__METHOD__.'@'.__LINE__.LOG_SEP.$this->__account.LOG_SEP.$this->__db_name.LOG_SEP.$sql.LOG_SEP.$data['code']);
		 
		return null;
	}
	
	public function update(array $rec){
		
		$sql = "UPDATE send_taskqueue SET ";
		$sets = array();
		$task_id = self::sql_escape_string($rec['id']);
		unset($rec['id']);
		$rec['last_modify_time'] = time();
		foreach ($rec as $key => $value) {
			$sets[] = "'".$key."' = '".self::sql_escape_string($value)."'";
		}
		$sql .= implode(',', $sets);
		$sql .= (' WHERE id='.$task_id);
	
		Log::debug(__METHOD__.'@'.__LINE__.LOG_SEP.$this->__account.LOG_SEP.$this->__db_name.LOG_SEP.$sql);
		$data = $this->execute($sql);
		if($data['code'] == 200){
			return $data['changed_rows'];
		}
	
		Log::error(__METHOD__.'@'.__LINE__.LOG_SEP.$this->__account.LOG_SEP.$this->__db_name.LOG_SEP.$sql.LOG_SEP.$data['code']);
			
		return null;
	}
	
	public function get_curr_id(){
		
		$sql = "SELECT MAX(id) as id FROM send_taskqueue";
		
		Log::debug(__METHOD__.'@'.__LINE__.LOG_SEP.$this->__account.LOG_SEP.$this->__db_name.LOG_SEP.$sql);
		$data = $this->execute($sql);
		if($data['code'] == 200){
			return (int)$data['result'][0]['id'] + 1;
		}
		
		Log::error(__METHOD__.'@'.__LINE__.LOG_SEP.$this->__account.LOG_SEP.$this->__db_name.LOG_SEP.$sql.LOG_SEP.$data['code']);
			
		return null;
	}
	
	public function del($id){
		
		$sql = "DELETE FROM send_taskqueue ";
		$sql .= (' WHERE id='.self::sql_escape_string($id));
	
		Log::debug(__METHOD__.'@'.__LINE__.LOG_SEP.$this->__account.LOG_SEP.$this->__db_name.LOG_SEP.$sql);
		$data = $this->execute($sql);
		if($data['code'] == 200){
			return $data['changed_rows'];
		}
	
		Log::error(__METHOD__.'@'.__LINE__.LOG_SEP.$this->__account.LOG_SEP.$this->__db_name.LOG_SEP.$sql.LOG_SEP.$data['code']);
			
		return null;
	}

}

?>