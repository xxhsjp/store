<?php
class OrderManager extends MySQLManager
{
	private $__account;
	private static $__keys = array('id','number','type','account_id','saler_id','price','total_count',
			'used_count','addrlist_limit','tpl_count_limit','tpl_customized','daily_send_limit','sign_date','effective','expired');
	public function __construct($account){
		$this->__account = isset($account) ? $account : SessionManager::getAccount();
		parent::__construct();
		Log::debug(__METHOD__.'@'.__LINE__.LOG_SEP.$this->__account);
	}
	
	private function _validate_args($args=array()) {
		$arr = array();
		foreach ($args as $key=>$val) {
			$index = array_search($key, $this::$__keys);
			if($index!==FALSE) {
				$arr[$key] = $val;
			}
		}
		return $arr;
	}

	public function add(array $para) {
		$sql = "INSERT INTO orders SET ";
		$sets = array();
		$params = array();
		$para = $this->_validate_args($para);
		foreach ($para as $key => $value) {
			$sets[] = $key." = ?";
			if(($key==="sign_date")||($key==="effective")||($key==="expired")) {
				Log::debug(__METHOD__.'@'.__LINE__.LOG_SEP.$this->__account.LOG_SEP.$value);
				$params[] = strtotime($value);
				Log::debug(__METHOD__.'@'.__LINE__.LOG_SEP.$this->__account.LOG_SEP.strtotime($value));
			} else{
				$params[] = $value;
			}
		}
		$sql .= implode(',', $sets);
		Log::debug(__METHOD__.'@'.__LINE__.LOG_SEP.$this->__account.LOG_SEP.$sql);
		$data = $this->execute($sql, $params);
		Log::debug(__METHOD__.'@'.__LINE__.LOG_SEP.$this->__account.LOG_SEP.json_encode($data));
		return $data;
	}
	
	public function update(array $rec) {
		$sql = "UPDATE orders SET ";
		$sets = array();
		$params = array();
		$rec = $this->_validate_args($rec);
		foreach ($rec as $key => $value) {
			$sets[] = $key." = ?";
			if($key==="sign_date"||$key==="effective"||$key==="expired") {
				$params[] = strtotime($value)?strtotime($value):$value;
			} else {
				$params[] = $value=="0"?0:empty($value)?null:$value;
			}
		}
		$sql .= implode(',', $sets);
		$sql .= (' WHERE id = ? AND account_id = ?');
		$params[] = $rec['id'];
		$params[] = $rec['account_id'];;
		Log::debug(__METHOD__.'@'.__LINE__.LOG_SEP.$this->__account.LOG_SEP.$sql);
		
		$data = $this->execute($sql, $params);
		Log::debug(__METHOD__.'@'.__LINE__.LOG_SEP.$this->__account.LOG_SEP.json_encode($data));
		return $data;
	}
	
	public function del($id, $aid) {
		$sql = "DELETE FROM orders WHERE id = ? AND account_id = ? ";
		$params = array($id, $aid);
		$result = $this->execute($sql, $params);
		return array("code"=>$result);
	}
	
	public function query_by_page($account_id, array $param=null){
		$sql = 'SELECT id,number,type,account_id,saler_id,price,total_count,used_count,addrlist_limit,tpl_count_limit,tpl_customized,daily_send_limit,
				FROM_UNIXTIME(sign_date, "%Y/%m/%d") AS sign_date,
				FROM_UNIXTIME(effective, "%Y/%m/%d") AS effective,
				FROM_UNIXTIME(expired, "%Y/%m/%d") AS expired 
				FROM orders WHERE account_id = ?';
		$sql2 = 'SELECT count(account_id) AS total FROM orders WHERE account_id = ?';
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
		$params = array($account_id);
		$data1 = $this->execute($sql, $params);
		Log::debug(__METHOD__.'@'.__LINE__.LOG_SEP.$this->__account.LOG_SEP.json_encode($data1));

		$params = array($account_id);
		$data2 = $this->execute($sql2, $params);
 		Log::debug(__METHOD__.'@'.__LINE__.LOG_SEP.$this->__account.LOG_SEP.json_encode($data2));
		
 		if(!empty($data1)&&!empty($data2)&&!empty($data2[0])) {
			return array("result"=>$data1, "total"=>$data2[0]['total']);
		}
	
		Log::error(__METHOD__.'@'.__LINE__.LOG_SEP.$this->__account.LOG_SEP.$sql.LOG_SEP.$data['code']);
			
		return null;
	}
}

?>
