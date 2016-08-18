<?php
class AccountInfoManager extends MySQLManager
{
	private $__account;
	private static $__keys = array('id','account','password','name','level','type','agent_id','group_id','status','expired','freeze_time','open_time','linkman','phone');
	public function __construct($account){
		$this->__account = isset($account) ? $account : SessionManager::getAccount();
		parent::__construct();
		Log::debug(__METHOD__.'@'.__LINE__.LOG_SEP.$this->__account);
	}
	
	public function auth_login($account, $password){
		$sql = "SELECT id,account,type,level,status,expired_time,freeze_time,register_time FROM account WHERE account = ? AND password = ?";
		$params = array($account, $password);
		$result = $this->execute($sql, $params);
		
		return $result;
	}
	
	public function register($account, $password) {
		$sql = "INSERT INTO account (account, password,status,type,register_time) values (?,?,?,?, unix_timestamp()) ";
		$params = array($account, $password, 1, 3);
		Log::debug(__METHOD__.'@'.__LINE__.LOG_SEP.$this->__account.LOG_SEP.$sql);
		$data = $this->execute($sql, $params);
		Log::debug(__METHOD__.'@'.__LINE__.LOG_SEP.$this->__account.LOG_SEP.json_encode($data));
		return $data;
	}
	
	public function update($group_id, array $rec) {
		$sql = "UPDATE account SET ";
		$sets = array();
		$params = array();
		$rec = $this->_validate_args($rec);
		foreach ($rec as $key => $value) {
			$sets[] = $key." = ?";
			if(strpos($key, "_time")||$key==="expired") {
				$params[] = empty($value)?null:(strtotime($value)?strtotime($value):$value);
			} else {
				$params[] = $value;
			}
		}
		$sql .= implode(',', $sets);
// 		$sql .= (' WHERE id = ? AND group_id = ?');
		$params[] = $rec['id'];
		$t = $this->prepare_group_id($group_id, $params);
		$sql .= (' WHERE id = ? AND '.$t);

		Log::debug(__METHOD__.'@'.__LINE__.LOG_SEP.$this->__account.LOG_SEP.$sql);
// 		Log::debug(__METHOD__.'@'.__LINE__.LOG_SEP.$this->__account.LOG_SEP.var_dump($params));
		$data = $this->execute($sql, $params);
		Log::debug(__METHOD__.'@'.__LINE__.LOG_SEP.$this->__account.LOG_SEP.json_encode($data));
		return $data;
	}
	
	public function del($id, $group_id) {
		$params = array($id);
		$t = $this->prepare_group_id($group_id, $params);
		
		$sql = "DELETE FROM account WHERE id = ? AND ".$t." AND account <> '".$this->__account."'";
		$result = $this->execute($sql, $params);
		return array("code"=>$result);
	}
	
	public function query($agent_id){
		$sql = "SELECT id, account,name,type,status,expired,freeze_time,open_time FROM account WHERE group_id = ?";
		$params = array($agent_id);
		$result = $this->execute($sql, $params);
		return $result;
	}
	
	public function query_by_page($group_id, array $param=null){
		$params = array();
		$t = $this->prepare_group_id($group_id, $params);
		
		$sql = 'SELECT id,account,name,level,type,agent_id,group_id,status,FROM_UNIXTIME(expired, "%Y/%m/%d") AS expired,FROM_UNIXTIME(freeze_time) AS freeze_time,FROM_UNIXTIME(open_time) AS open_time,FROM_UNIXTIME(create_time) AS create_time, linkman,phone, IF(IFNULL(freeze_time,0)<unix_timestamp(),1,0) AS isfreeze FROM account WHERE '.$t;
		$sql2 = 'SELECT count(account) AS total FROM account WHERE '.$t;
		$where = "";
		$order = "";
		$limit = "";
		if(isset($param['order'])){
			$order = ' ORDER BY '.implode(',', $param['order']);
		}
		if(isset($param['limit'])){
			$limit = ' LIMIT '.implode(',', $param['limit']);
		}
		
		$sql .= $where.$order.$limit;
		$sql2 .= $where;
		$data1 = $this->execute($sql, $params);
		Log::debug(__METHOD__.'@'.__LINE__.LOG_SEP.$this->__account.LOG_SEP.json_encode($data1));

		$data2 = $this->execute($sql2, $params);
 		Log::debug(__METHOD__.'@'.__LINE__.LOG_SEP.$this->__account.LOG_SEP.json_encode($data2));
		
 		if(!empty($data1)&&!empty($data2)&&!empty($data2[0])) {
			return array("result"=>$data1, "total"=>$data2[0]['total']);
		}
	
		Log::error(__METHOD__.'@'.__LINE__.LOG_SEP.$this->__account.LOG_SEP.$sql.LOG_SEP.$data['code']);
			
		return null;
	}
	
	public function search($group_id, $content, array $param=null){
		$params = array();
		$t = $this->prepare_group_id($group_id, $params);
		$t .= ' AND (account LIKE ? OR agent_id LIKE ?)';
		$sql = 'SELECT id,account,name,level,type,agent_id,group_id,status,FROM_UNIXTIME(expired, "%Y/%m/%d") AS expired,FROM_UNIXTIME(freeze_time) AS freeze_time,FROM_UNIXTIME(open_time) AS open_time,FROM_UNIXTIME(create_time) AS create_time, linkman,phone, IF(IFNULL(freeze_time,0)<unix_timestamp(),1,0) AS isfreeze FROM account WHERE '.$t;
		$sql2 = 'SELECT count(account) AS total FROM account WHERE '.$t;
		$where = "";
		$order = "";
		$limit = "";
		if(isset($param['order'])){
			$order = ' ORDER BY '.implode(',', $param['order']);
		}
		if(isset($param['limit'])){
			$limit = ' LIMIT '.implode(',', $param['limit']);
		}
	
		$sql .= $where.$order.$limit;
		$sql2 .= $where;
		$params[] = '%'.$content.'%';
		$params[] = '%'.$content.'%';
		$data1 = $this->execute($sql, $params);
		Log::debug(__METHOD__.'@'.__LINE__.LOG_SEP.$this->__account.LOG_SEP.json_encode($data1));
	
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
