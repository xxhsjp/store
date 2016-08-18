<?php
require_once "./include/base_include.php";

echo 'smletter test', '<br/>===============<br/>';


// $mcd = new MemCachedManager();

// $mcd->set("key_test1", "5555611111111111", false, 60);

// echo $mcd->get("key_test1" ), '<br/>============';

// echo function_exists('file_exist');

//  $r = get_loaded_extensions();
// var_dump($r);
// phpinfo();

// $tmq = new TasksQManager(TasksQManager::Q_LV1);
// $rec = array(
// 	"id" => 5,
// 	"account" => "xul@send.com",
// 	"level" => 3
// );
// $r= $tmq->add($rec);
// // $r = $tmq->query();
// // $r = $tmq->get_curr_id();
// var_dump($r);

// $stary_time = strtotime(date('Y-m-d',time()));
// $end_time = strtotime(date('Y-m-d',time()).' 23:59:59');

// echo $stary_time, '<br/>', $end_time;

// echo strtotime(date('Y-m-d',time()).'  23:59:59');
// $am = new AccountInfoManager($account);
// // $r = $am->auth_login($account, "96e79218965eb72c92a549dd5a330112");
// // $r = $am->get_admin("agent_1");
// // $r = $am->get_effective_order("2");
// $r = $am->increase_used_count(2, 100);
// var_dump($r);

// $tplm = new TemplatesManager($account);
// $tpls = $tplm->is_audited(5);
// echo empty($tpls);
// var_dump($tpls);

// $tm = new TasksManager($account);
// $r= $tm->query_by_page(array("where"=>array("2"=>2),  "order" => array("create_time DESC"), "limit" => array(0, 20)));
// var_dump($r);
// $tm->add(array(
// 		'tpl_name' => '测试模板1',
// 		'tpl_file' => 'tpl_update2',
// 		'addrlist_name' => '测试地址1',
// 		'addrlist_file' => 'addrlist_name2',
// 		'mail_from' => 'xinne123@xinnet.com',
// 		'from' => '',
// 		'raw_from' => '',
// 		'reply_to' => '',
// 		'raw_reply_to' => '',
// 		'subject' => '你好你好你好你好你好你好你好你好你好你好你航空',
// 		'raw_subject' => '',
// 		'mail_id' => '',
// 		'mail_from_type' => '0',
// 		'status' => '',
// 		'tag' => 'tag2',
// 		'send_count' => '99'));
// $r = $tm->query();
// var_dump($r);

// $tm = new MailFromManager($account);
// $tm->add(array(
// 		'type' =>  '1',   //发信账号类型（触发：0, 群发: 1）
// 	     'mail_from' =>  'aaa@xinnet.com', //发信账号
// 	     'status' =>  '1', 	//状态
// 	     'reply_to' =>  'reply@xinnet.com', 	//回复地址
// 	     'desciption' =>  'desciption' , //描述
// 	     'used_count' =>  '55' //使用计数		
// ));
// $r = $tm->query();
// var_dump($r);


// $account = 'xul@edm.cn';
// $tm = new TasksManager($account);
// $tm->add(array(
// 		'tpl_name' => '测试模板1',
// 		'tpl_file' => 'tpl_update2',
// 		'addrlist_name' => '测试地址1',
// 		'addrlist_file' => 'addrlist_name2',
// 		'mail_from' => 'xinne123@xinnet.com',
// 		'from' => '',
// 		'raw_from' => '',
// 		'reply_to' => '',
// 		'raw_reply_to' => '',
// 		'subject' => '你好你好你好你好你好你好你好你好你好你好你航空',
// 		'raw_subject' => '',
// 		'mail_id' => '',
// 		'mail_from_type' => '0',
// 		'status' => '',
// 		'tag' => 'tag2',
// 		'send_count' => '99'));
// $r = $tm->query();
// var_dump($r);

