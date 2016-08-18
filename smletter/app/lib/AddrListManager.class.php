<?php

class AddrListManager extends SQLiteManager
{
	const BASE_NAME = "send_addrlist_";
	const L_LEV0 = 0;
	const L_LEVMAX = 99;
	
	public function __construct($account, $lev)
    {
		if(!isset($lev)) {
			throw new Exception($account.LOG_SEP." database inistal failed...");
		}else if($lev>=self::L_LEV0&&$lev<=self::L_LEVMAX) {
			parent::__construct($account, self::BASE_NAME.$lev.'.db');
    		Log::debug(__METHOD__.'@'.__LINE__.LOG_SEP.$this->__account.LOG_SEP.$this->__db_name);
    	}else {
    		throw new Exception(" $lev > ".self::L_LEVMAX);
    	}
    }

	public function query(array $param=null){
		
		$sqlArr = array("SELECT email, info FROM send_addrlist WHERE 1=1 ");
		
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
		
		$sql = 'SELECT * FROM send_addrlist WHERE 1=1';
		$sql2 = 'SELECT count(id) AS total FROM send_addrlist WHERE 1=1';
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
	 *     'email' => string ''  地址
	 *     'info' => string '' 地址列表内容
	 *     'status' => string ''  
	 *     'type' => string '' 	
	 *     'status' => string '' 		状态
	 * @return mixed|NULL
	 */
	public function add(array $param=null){
		$this->del();
		//一次最多 500条
		$count = 500;
		
		$length = count($param);
		$round = ceil($length/$count);
		$time = 0;
		$total = 0;
		for($time=0;$time<$round;$time++) {
			$sql = "INSERT INTO send_addrlist(email, info) values";
			$b = $count*$time; $len = ($count*($time+1))< $length?($count*($time+1)):$length;
			for($j=$b;$j<$len;$j++) {
				$arr = each($param);
				$sql .="('".self::sql_escape_string($arr['key'])."','".self::sql_escape_string($arr['value'])."'),";
			}
			$sql = substr($sql, 0,strlen($sql)-1);
// 			Log::debug(__METHOD__.'@'.__LINE__.LOG_SEP.$this->__account.LOG_SEP.$sql);
			$data = $this->execute($sql);
//  			sleep(0.001);
			if($data['code'] == 200){
				$total += (int)($data['changed_rows']);
			}
		}
//		Log::debug(__METHOD__.'@total'.LOG_SEP.$this->__account.LOG_SEP.$sql.LOG_SEP.$total);
		return $total;
	}
	
	public function del(){
		$sql = "DELETE FROM send_addrlist";
// 		Log::debug(__METHOD__.'@'.__LINE__.LOG_SEP.$this->__account.LOG_SEP.$sql);
		$data = $this->execute($sql);
		if($data['code'] == 200){
			return $data['changed_rows'];
		}
	
		Log::error(__METHOD__.'@'.__LINE__.LOG_SEP.$this->__account.LOG_SEP.$sql.LOG_SEP.$data['code']);
			
		return null;
	}
	

}

?>