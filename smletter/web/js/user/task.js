var taskController = (function(){
	var currAjax = null;
	
	var isSending = false;
	
	var frmInfo = null;
	
	var pagingBar = null;
	
	var trTpl = '<tr tid="{{id}}" time_task="{{timed_task}}" mid="{{mail_id}}" sum="{{send_count}}">'+
				    '<td class="text-ellipsis">{{addrlist_name}}</td>'+
				    '<td class="text-ellipsis">{{tpl_name}}</td>'+
//				    '<td class="text-ellipsis">{{from}}</td>'+
				    '<td class="text-ellipsis">{{subject}}</td>'+
				    '<td>{{send_count}}</td>'+
				    '<td class="text-ellipsis">{{create_time}}</td>'+
				    '<td>{{task_oper}}</td>'+
			    '</tr>';
	
	return {
		
		init: function(){
			var _this = this;
			
			pagingBar = new PagingBar('#task_nav_page', null, function(pageInfo){
				_this.listTask(pageInfo);
			});
			
			gMainframe.addListener('viewloaded', function(vname){
				if(vname == 'create_task'){
					pagingBar.go(1, true);
				}
			});
			
			gMainframe.addListener('viewchanged', function(vname){
				if(vname != 'create_task'){
					_this.reset();
				}
			});
			
			$(".form_datetime").datetimepicker({
			    	language:  'zh-CN',
			        format: "yyyy/mm/dd hh:ii:ss",
			        minView: "hour",
			        autoclose: true,
			        endDate:new Date("2050/12/31"), 
			        startDate:new Date("2000/01/01"), 
			        todayBtn: true
			});
			
			$('#optionsRadios2').click(function(){
				$('#task_input7').removeAttr('disabled');
			});
			
			$('#optionsRadios1').click(function(){
				$('#task_input7').attr('disabled', 'disabled');
			});
			
			$('#task_tb').delegate('A', 'click', function(){
				var oper = $(this).attr('oper');
				var tr = $(this).parent().parent();
				var tid = tr.attr('tid');
				if(oper == 'oper_detail'){
					var mailId = tr.attr('mid');
					var sum = tr.attr('sum');
					_this.showDetail(mailId, sum);
					$('#task_detail_modal').modal('show');
				}else if(oper == 'oper_test'){
					$('#task_test_input2').val(tid);
				}else if(oper == 'oper_send'){
//					_this.startTask(tid);
					$('#task_send_input2').val(tid);
				}else if(oper == 'oper_clone'){
					
				}else if(oper == 'oper_del'){
					var time_task = tr.attr('time_task');
					var info = Lang.task_oper_confirm_del;
					if(time_task === '1'){
						info = Lang.task_oper_confirm_del_time_task;
					}
					gMainframe.confirm(Lang.task_oper_confirm_title, info, function(){
						_this.removeTask(tid, time_task);
					});
				}
			});
			
			$('#task_modal').on('show.bs.modal', function(event) {
				if(frmInfo){
					return true;
				}
				$('#task_frm input').val(Lang.task_loading);
				var post = {method: "get_frm_info"};
				_this.request(post, function(data){
					frmInfo = data;
					var addrs = data.addr;
					if(addrs){
						var html = [];
						for(var i=0;i<addrs.length;i++){
							html.push('<li><a biz_id="'+addrs[i]['id']+'" href="javascript:void(0);">'+addrs[i]['addrlist_name']+'</a></li>');
						}
						if(html.length == 0){
							html.push('<li><a href="#upload_addr">'+Lang.task_addrlist_name_add+'</a></li>');
						}
						$('#task_select_1').html(html.join(''));
					}
					
					var tpls = data.tpl;
					if(tpls){
						var html = [];
						for(var i=0;i<tpls.length;i++){
							html.push('<li><a biz_id="'+tpls[i]['id']+'" href="javascript:void(0);">'+tpls[i]['tpl_name']+'</a></li>');
						}
						if(html.length == 0){
							html.push('<li><a href="#upload_tpl">'+Lang.task_tpl_name_add+'</a></li>');
						}
						$('#task_select_2').html(html.join(''));
					}
					
					var from = data.from;
					if(from){
						var html = [];
						html.push('<li><a href="javascript:void(0);" ran="1">'+Lang.task_mail_random+'</a></li>');
						for(var i=0;i<from.length;i++){
							html.push('<li><a href="javascript:void(0);">'+from[i]['mail_from']+'</a></li>');
						}
						$('#task_select_3').html(html.join(''));
					}
					
					$('#task_frm input').val('');
				});
			});
			
			$('#task_frm').delegate('A', 'click', function(){
//				if($(this).attr("ran") == "1"){
//					$('#task_mail_from_type').val("1");
//				}else {
//					$('#task_mail_from_type').val("0");
//				}
				var html = $(this).html();
				var biz_id = $(this).attr('biz_id');
				if(biz_id){
					$(this).parents('.input-group').find('input.form-control').val(html);
					$(this).parents('.input-group').find('input:hidden').val(biz_id);
				}
//				$('#task_input6').val('');
//				for(var i=0;frmInfo.from&&i<frmInfo.from.length;i++){
//	        		if(frmInfo.from[i]['mail_from'] == html){
//	        			$('#task_input6').val(frmInfo.from[i]['reply_to']);
//	        			break;
//	        		}
//	        	}
			});
			
			$('#task_refr_btn').click(function(){
				_this.refresh();
			});
			
			var options = {  
		        beforeSubmit: function(arr, $form, options){},  
		        success: function(data){
		        	var info = Lang.task_add;
		        	if(data && data.code == 200){
		        		info += Lang.task_success;
		        	}else if(data && data.code == 403){
		        		info = Lang.task_addr_tpl_error;
		        	}else if(data && data.code == 424){
		        		info = Lang.task_addr_tpl_error;
		        	}else {
		        		info += Lang.task_failure;
		        	}
		        	gMainframe.showInfo(info, {time: 1000});
		        	$('#submit_btn').removeAttr('disabled');
		        	$('#task_modal').modal('hide');
		        	_this.refresh();
		        },  
		        error: function(){
		        	gMainframe.showInfo(Lang.task_add + Lang.task_failure, {time: 1000});
		        	$('#submit_btn').removeAttr('disabled');
		        },
		        resetForm: true,  
		        dataType: 'json'  
		    };  
			$('#submit_btn').click(function(){
				var flag = true;
				$('#task_frm input').each(function(idx){
	        		var val = $.trim($(this).val());
	        		if(!val && $(this).attr("norequired") != "true"){
	        			$(this)[0].focus();
	        			flag = false;
	        			return false;
	        		}
	        	});
				if(flag === false){
					return false;
				}
				
	        	$(this).attr('disabled', 'disabled');
				$('#task_frm').ajaxSubmit(options);
			});
			
			var options_test = {  
		        success: function(data){
		        	var info = Lang.task_oper_test;
		        	if(data && data.code == 200){
		        		info += Lang.task_success;
		        	}else if(data && data.code == 404){
						info = Lang.task_addr_tpl_notfound;
					}else if(data && data.code == 406){
						info = Lang.task_order_expire;
					}else if(data && data.code == 424){
						info = Lang.task_uavailable_count;
					}else {
		        		info += Lang.task_failure;
		        	}
		        	window.setTimeout(function(){
						gMainframe.showInfo(info, {time: 1000});
					}, 500)
		        	$('#task_test_submit_btn').removeAttr('disabled');
		        	$('#task_test_modal').modal('hide');
		        },  
		        error: function(){
		        	gMainframe.showInfo(Lang.task_oper_test + Lang.task_failure, {time: 1000});
		        	$('#task_test_submit_btn').removeAttr('disabled');
		        },
		        resetForm: true,  
		        dataType: 'json'  
			};  
			
			$('#task_download_detail').click(function(){
				var mail_id = $('#task_detail_info').attr("mail_id");
				window.open('web/php/user/task.php?method=download_detail&mail_id='+mail_id);
			});
			
			$('#task_test_submit_btn').click(function(){
	        	var data = $('#task_test_input1').val();
	        	var tid = $('#task_test_input2').val();
				
	        	if(!data || !tid){
	        		return false;
	        	}
	        	
	        	$(this).attr('disabled', 'disabled');
				$('#task_test_frm').ajaxSubmit(options_test);
			});
			
			
			var options_send = {  
		        success: function(data){
		        	var info = Lang.task_oper_send;
					if(data && data.code == 200){
						info += Lang.task_success;
					}else if(data && data.code == 404){
						info = Lang.task_addr_tpl_notfound;
					}else if(data && data.code == 406){
						info = Lang.task_order_expire;
					}else if(data && data.code == 424){
						info = Lang.task_uavailable_count;
					}else if(data && data.code == 407){
						info = Lang.task_daily_limit;
					}else {
						info += Lang.task_failure;
					}
					window.setTimeout(function(){
						gMainframe.showInfo(info, {time: 1000});
					}, 500)
					$('#task_input7').val('');
					$('#task_input7').attr('disabled', 'disabled');
					$('#task_send_submit_btn').removeAttr('disabled');
		        	$('#task_send_modal').modal('hide');
					_this.refresh(pagingBar.info.currPage);
		        },  
		        error: function(){
		        	gMainframe.showInfo(Lang.task_oper_send + Lang.task_failure, {time: 1000});
		        	$('#task_send_submit_btn').removeAttr('disabled');
		        	$('#task_input7').val('');
		        	$('#task_input7').attr('disabled', 'disabled');
		        },
		        resetForm: true,  
		        dataType: 'json'  
			}; 
			
			$('#task_send_submit_btn').click(function(){
	        	var tid = $('#task_send_input2').val();
	        	var val = $('#task_input7:enabled').val();
	        	if(!tid || val === ""){
	        		return false;
	        	}
	        	$(this).attr('disabled', 'disabled');
				$('#task_send_frm').ajaxSubmit(options_send);
			});
		},
		
		showDetail: function(mailId, sum){
			var _this = this;
			var post = {method: "task_detail", mail_id: mailId};
			
			$('#task_chart_area').remove();
			$('#task_canvas_holder').append('<canvas id="task_chart_area" width="260" height="260" class="center-block"/>');
			$('#task_detail_info').attr("mail_id", mailId);
			$('#task_detail_info').html(Lang.task_loading);
			this.request(post, function(data){
				var ok = data['.ok'] || 0;
				var invalid = data['.err.invalid_address'] || 0;
				var blacklist = data['.err.blacklist_address'] || 0;
				var err = data['.err.500'] || 0;
				var err2 = data['.err.400'] || 0;
				err = err*1 + err2*1;
				if(!ok && !err && !invalid && !blacklist){
					$('#task_detail_info').html(Lang.task_detail_info1);
					return;
				}
				var infoStr = '<p>'+Lang.task_detail_total+":"+sum+'</p>';
				infoStr += '<p>'+Lang.task_success_percent+":"+Math.floor((1-err/sum)*100)+'%</p>';
				infoStr += '<p><span class="glyphicon glyphicon-stop" style="color:#46BFBD;"></span>'+Lang.task_success+":"+ok+'</p>';
				infoStr += '<p><span class="glyphicon glyphicon-stop" style="color:#FF5A5E;"></span>'+Lang.task_failure+":"+err+'</p>';
				infoStr += '<p><span class="glyphicon glyphicon-stop" style="color:#000000;"></span>'+Lang.task_blacklist+":"+blacklist+'</p>';
				infoStr += '<p><span class="glyphicon glyphicon-stop" style="color:#FFDE00;"></span>'+Lang.task_invalid+":"+invalid+'</p>';
				$('#task_detail_info').html(infoStr);
				var pieData = [
								{
									value: err,
									color:"#F7464A",
									highlight: "#FF5A5E",
									label: Lang.task_failure
								},
								{
									value: invalid,
									color: "#FFDE00",
									highlight: "#FFEC6B",
									label: Lang.task_invalid
								},
								{
									value: ok,
									color: "#46BFBD",
									highlight: "#5AD3D1",
									label: Lang.task_success
								},
								{
									value: blacklist,
									color: "#000000",
									highlight: "#666666",
									label: Lang.task_blacklist
								}
							];
				var ctx = $("#task_chart_area")[0].getContext("2d");
				new Chart(ctx).Pie(pieData);
			});
		},
		
		removeTask: function(tid, time_task){
			var _this = this;
			var post = {method: "del_task", tid: tid, time_task: time_task};
			this.request(post, function(data){
				var info = Lang.task_oper_del;
				if(data && data.code == 200){
					info += Lang.task_success;
				}else {
					info += Lang.task_failure;
				}
				gMainframe.showInfo(info, {time: 1000});
				_this.refresh(pagingBar.info.currPage);
			});
		},
		
		startTask: function(tid){
			var _this = this; 
			if(isSending === true){
				return;
			}
			isSending = true;
			var post = {method: "start_task", tid: tid};
			this.request(post, function(data){
				var info = Lang.task_oper_send;
				if(data && data.code == 200){
					info += Lang.task_success;
				}else if(data && data.code == 404){
					info = Lang.task_addr_tpl_notfound;
				}else if(data && data.code == 406){
					info = Lang.task_order_expire;
				}else if(data && data.code == 424){
					info = Lang.task_uavailable_count;
				}else if(data && data.code == 407){
					info = Lang.task_daily_limit;
				}else {
					info += Lang.task_failure;
				}
				gMainframe.showInfo(info, {time: 1000});
				isSending = false;
				_this.refresh(pagingBar.info.currPage);
			});
		},
		
		listTask: function(_pageInfo){
			var _this = this;
			_pageInfo = _pageInfo;
			var post = {method: "list_task", page: _pageInfo.currPage, size: _pageInfo.pageSize};
			this.request(post, function(data){
				if(data.code && data.code == 500){
					gMainframe.showInfo(Lang.exception_info_serv, {time: 1000});
					return;
				}
				_pageInfo.totalPage = Math.floor((1*data.total+_pageInfo.pageSize-1)/_pageInfo.pageSize) || 1;
				if(_pageInfo.currPage > _pageInfo.totalPage){
					_this.refresh(_pageInfo.totalPage);
					return;
				}
				pagingBar.render();
				var html = _this.buildList(data.result);
				$('#task_tb').html(html);
			});
		},
		
		refresh: function(page){
			page = page || 1;
			this.clearList();
			pagingBar.go(page, true);
		},
		
		clearList: function(){
			$('#task_tb').html('');
		},
		
		buildList: function(datas){
			var re = /{{(.+?)}}/g;
			var listHtmls = [];
			var l = datas.length;
			for(var i=0;i<l;i++){
				var itemHtml = trTpl.replace(re, function(s, s1){
					var escaped = '';
					if(s1 == 'create_time'){
						escaped = util.getDate(datas[i][s1]*1000);
					}else if(s1 == 'timed_task'){
						escaped = datas[i]['timed_task'] === '1' ? "1" : "0";
					}else if(s1 == 'subject'){
						var time_send = "";
						if(datas[i]['timed_task'] === '1'){
							time_send = "<span title='"+util.getDate(datas[i]['send_time']*1000)+"' class='glyphicon glyphicon-time'></span>&nbsp;";
						}
						escaped = time_send + datas[i][s1];
					}else if(s1 == 'success_count'){
						escaped = datas[i][s1] || 0;
					}else if(s1 == 'err_count'){
						escaped = 0;
						for(var j=1;j<6;j++){
							escaped += datas[i]['err'+j+'_count'] || 0;
						}
					}else if(s1 == 'task_oper'){
						if(datas[i]['status'] == 0){
							escaped += '<a data-toggle="modal" data-target="#task_test_modal" href="#" class="task-oper" oper="oper_test">'+Lang.task_oper_test+'</a>';
							escaped += '<a data-toggle="modal" data-target="#task_send_modal" href="#" class="task-oper" oper="oper_send">'+Lang.task_oper_send+'</a>';
							escaped += '<a href="javascript:void(0);" class="task-oper" oper="oper_del">'+Lang.task_oper_del+'</a>';
						}else{
							if(datas[i]['timed_task'] === '1' && datas[i]['has_send'] === false){
								escaped += '<a href="javascript:void(0);" class="task-oper" oper="oper_del">'+Lang.task_oper_del+'</a>';
							}
							escaped += '<a href="javascript:void(0);" class="task-oper" oper="oper_detail">'+Lang.task_oper_detail+'</a>';
						}
					}else if(s1 == 'from'){
						escaped = datas[i][s1] ? datas[i][s1] : "";
					}else {
						escaped = datas[i][s1];
					}
					return escaped;
				});
				listHtmls.push(itemHtml);
			}
			
			return listHtmls.join('');
		},
		
		reset: function(){
			currAjax = null;
			frmInfo = null;
			$('#task_frm').resetForm();
		},
		
		request: function(post, callback){
			if(currAjax){
	    		currAjax.abort();
			}
	    	currAjax = $.ajax({
	    	   url: 'web/php/user/task.php',
			   type: "POST",
			   dataType: 'json',
			   data: post,
			   success: function(data, textStatus, qXHR){
				   if($.isFunction(callback)){
					   callback(data, textStatus);
				   }
			   }
			});
		}
	
	};
	
})();

taskController.init();