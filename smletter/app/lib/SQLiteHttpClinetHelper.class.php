<?php

class SQLiteHttpClinetHelper
{

	public static function callSQLiteServer(SQLiteCmmnd $command)
	{
		$server = SQLiter::getServer($command->getServerId());
		$origSql = $command->sql;
		$command->sql = base64_encode($origSql);
		$data = SQLiter::PREFIX_POST_JSON . urlencode(json_encode($command));
		$response = CURLHelper::call($server, $data, SQLiter::getTimeoutSec(), SQLiter::getReConnTimes());
		
		$http_code = $response['http_code'];
		$error_text = $response['error_text'];
		$output = '{"code": -1, "http_code": '.$http_code.', "msg": "'.$error_text.'"}';
		if($http_code == 200){
			$output = $response['response_body'];
		}
		return $output;
	}
    
}

?>