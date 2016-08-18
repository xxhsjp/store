<?php

class TasksManager extends SQLiteManager
{
	const DB_NAME = "send_task.db";

	public function __construct($account)
    {
		parent::__construct($account, self::DB_NAME);
		Log::debug(__METHOD__.'@'.__LINE__.LOG_SEP.$this->__account.LOG_SEP.$this->__db_name);
    }

	public function query(array $param=null){
		$sqlArr = array('SELECT * FROM send_task WHERE 1=1 ');
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
		$sql = 'SELECT * FROM send_task WHERE 1=1';
		$sql2 = 'SELECT count(*) AS total FROM send_task WHERE 1=1';
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
	
		Log::error(__METHOD__.'@'.__LINE__.LOG_SEP.$this->__account.LOG_SEP.$sql);
		 
		return null;
	}
	
	public function send_count_today($time=null){
		$time = isset($time) ? $time : time();
		$stary_time = strtotime(date('Y-m-d', $time));
		$end_time = strtotime(date('Y-m-d', $time).' 23:59:59');
		
		$sql = "SELECT sum(send_count) AS sum FROM send_task WHERE (status = 1 OR status = 2) AND send_time < $end_time AND send_time > $stary_time";
		Log::debug(__METHOD__.'@'.__LINE__.LOG_SEP.$this->__account.LOG_SEP.$sql);
		$data = $this->execute($sql);
		
		if($data['code'] == 200){
			return $data['result'][0]['sum'];
		}
		
		Log::error(__METHOD__.'@'.__LINE__.LOG_SEP.$this->__account.LOG_SEP.$sql.LOG_SEP.$data['code']);
			
		return null;
	}
	
	/**
	 * @param array $task = array (
	 *	    'tpl_name' => '',	模板名
	 *	    'tpl_file' => '',	模板对应文件名
	 * 	    'tpl_id' => '',	模板id
	 *	    'addrlist_name' => '',	地址列表名
	 *	    'addrlist_file' => '',	地址列表对应文件名
	 *		'addrlist_id' => '',	地址列表id
	 *	    'mail_from' => '',	发信账号
	 *	    'from' => '',	信头From
	 *	    'raw_from' => '',	信头From未解码
	 *	    'reply_to' => '',	信头replay-to
	 *	    'raw_reply_to' => '',	信头replay-to未解码
	 *	    'subject' => '',	信头主题
	 *	    'raw_subject' => '',	信头主题未解码
	 *	    'mail_id' => '',	信头mail_id
	 *    	'mail_from_type' => '',	  发信账号类型（账号：0, 随机: 1）
	 *	    'status' => '',		状态  0 ：未发送，1：已发送, 2: 完成
	 *	    'send_count' => ''	发送计数
	 *		'create_time' => '' 创建时间UNIX时间戳
	 *		'last_modify_time' => '' UNIX时间戳
	 *		'send_time' => '' UNIX时间戳
	 *      'timed_task' => '' 是否为定时任务 0：非定时任务, 1: 定时任务
	 *      'task_qid' => '' 任务队列ID
	 *      'level' => '' 任务级别
     *		'type' => '' 任务类型（触发：0, 群发: 1）
	 *		)
	 * @return mixed|NULL
	 */
	public function add(array $rec){
		$sql = "INSERT INTO send_task ";
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
		
		Log::debug(__METHOD__.'@'.__LINE__.LOG_SEP.$this->__account.LOG_SEP.$sql);
		$data = $this->execute($sql);
		if($data['code'] == 200){
			return $data['changed_rows'];
		}
	
		Log::error(__METHOD__.'@'.__LINE__.LOG_SEP.$this->__account.LOG_SEP.$sql.LOG_SEP.$data['code']);
		 
		return null;
	}
	
	public function update(array $rec){
		$sql = "UPDATE send_task SET ";
		$sets = array();
		$task_id = self::sql_escape_string($rec['id']);
		unset($rec['id']);
		$rec['last_modify_time'] = time();
		foreach ($rec as $key => $value) {
			$sets[] = "'".$key."' = '".self::sql_escape_string($value)."'";
		}
		$sql .= implode(',', $sets);
		$sql .= (' WHERE id='.$task_id);
	
		Log::debug(__METHOD__.'@'.__LINE__.LOG_SEP.$this->__account.LOG_SEP.$sql);
		$data = $this->execute($sql);
		if($data['code'] == 200){
			return $data['changed_rows'];
		}
	
		Log::error(__METHOD__.'@'.__LINE__.LOG_SEP.$this->__account.LOG_SEP.$sql.LOG_SEP.$data['code']);
			
		return null;
	}
	
	public function del($id, $status=null){
		$sql = "DELETE FROM send_task ";
		$sql .= (' WHERE id='.self::sql_escape_string($id));
		
		if(isset($status)){
			$sql .= (' AND status='.$status);
		}
	
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