<?php
class SubscriberManager extends SQLiteManager{
	const DB_NAME = "letter_subscriber.db";
	public function __construct($account)
	{
		parent::__construct($account, self::DB_NAME);
		Log::debug(__METHOD__.'@'.__LINE__.LOG_SEP.$this->__account.LOG_SEP.$this->__db_name);
	}
	
	public function query_by_id($id){
		$sql = 'SELECT * FROM letter_subscriber';
		$sql .= (' WHERE id='.self::sql_escape_string($id));
		Log::debug(__METHOD__.'@'.__LINE__.LOG_SEP.$this->__account.LOG_SEP.$sql);
		$data = $this->execute($sql);
		if($data['code'] == 200){
			return $data['result'][0];
		}
		Log::error(__METHOD__.'@'.__LINE__.LOG_SEP.$this->__account.LOG_SEP.$sql.LOG_SEP.$data['code']);
		return null;
	}
	
	public function query_by_page(array $param=null){
		$sql = 'SELECT id, subscriber,subscribed_time FROM letter_subscriber WHERE 1=1';
		$sql2 = 'SELECT count(id) AS total FROM letter_subscriber WHERE 1=1';
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
	 * 		'id' => INTEGER ''    id
	 *     'subscriber' => text ''  订阅者
	 *     'subscribed_time' => INTEGER '' 订阅时间
	 *     'group' => text ''  组
	 *     'level' => text '' 	等级
	 *     'total_opens' => text '' 		打开总数
	 *     'unique_opens' => text '' 	特别打开总数
	 *     'total_click' => text ''	点击总数
	 *     'unique_clicks' => text '' 	特别点击总数
	 *     'last_open' => text '' 			上次打开
	 * @return mixed|NULL
	 */

	public function update(array $rec){
		$sql = "UPDATE letter_subscriber SET ";
		$sets = array();
		// 		$rec['last_modify_time'] = time();
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
	
	/**
	 * @param array $rec
	 *     'subscriber' => string ''  订阅者
	 *     'subscribed_time' => integer '' 订阅时间
	 * @return mixed|NULL
	 */
	public function add(array $param=null){
		//一次最多 500条
		$count = 500;
	
		$length = count($param);
		$round = round($length/$count) + 1;
		$time = 0;
		$total = 0;
		$subs_time = time();
		
		for($time=0;$time<$round;$time++) {
			$sql = "INSERT INTO letter_subscriber(subscriber, subscribed_time) values";
			$b = $count*$time; $len = ($count*($time+1))< $length?($count*($time+1)):$length;
			for($j=$b;$j<$len;$j++) {
				$em = $param[$j];
				$sql .="('".self::sql_escape_string($em)."','".($subs_time)."'),";
			}
			$sql = substr($sql, 0,strlen($sql)-1);
			 			Log::debug(__METHOD__.'@'.__LINE__.LOG_SEP.$this->__account.LOG_SEP.$sql);
			$data = $this->execute($sql);
			//  			sleep(0.001);
			if($data['code'] == 200){
				$total += (int)($data['changed_rows']);
			}
		}
		//		Log::debug(__METHOD__.'@total'.LOG_SEP.$this->__account.LOG_SEP.$sql.LOG_SEP.$total);
		return $total;
	}
	
	public function del($id){
		$sql = "DELETE FROM letter_subscriber ";
		if(isset($id)) {
			$sql .= (' WHERE id='.self::sql_escape_string($id));
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