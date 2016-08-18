var tplManageController = (function(){
	var delTplId = null;
	var pageInfo = {currPage: 1, pageSize: 30, totalPage: 1}; 
	var pageBar = null;
	var tplAuditTip = [Lang.template_audit_fail_reason_1,
	                   Lang.template_audit_fail_reason_2,
	                   Lang.template_audit_fail_reason_3,
	                   Lang.template_audit_fail_reason_4,
	                   Lang.template_audit_fail_reason_5
	                  ];
	
	var ajaxOptions = {
		type:"POST",
		dataType: "json",
		url: "web/php/admin/tplManage.php",
		beforeSubmit: function() {
		},
		beforeSend: function() {
		}
	};
	
	function doAjax(data, callback){
		var opt = ajaxOptions;
		opt.data = data || {method: "getList", page: pageInfo.currPage, size: pageInfo.pageSize};
		opt.success = function (data, textStatus, qXHR) {
			if(callback!=null&&($.isFunction(callback))) {
				callback(data, textStatus, qXHR);
			}
		}
		$.ajax(opt);
	}
	
	function showErrorMsg(code) {
		var err = "";
		switch(code) {
			case 905:
				err = Lang.template_post_audit_fail;
				break;
			case 904:
				err = Lang.template_illegal_opt;
				break;
			case 903:
				err = Lang.template_no_exist_audit;
				break;
			case 902:
				err = Lang.template_been_audit;
				break;
			case 901:
				err = Lang.template_get_fail;
				break;
			case 900:
				err = Lang.template_get_list_fail;
				break;
			default:
				err = Lang.template_oper_error;
		}
		
		return err;
	}
	
	function tplStatus(flag){
		var status = "";
		switch(flag) {
			case "1":
				status = Lang.template_audit_ok;
				break;
			case "2":
				status = Lang.template_audit_fail;
				break;
			default:
				status = Lang.template_audit_non;
		}
		return status;
	}
	
	function showTplList(dataStr){
		var html = [];
		var str = "";
		var data = dataStr.result;
		
		for(var i=0;i<data.length;i++){
			var uploadT = Lang.template_upload_file;
			var auditT = Lang.template_audit;
			var time = util.getDate(data[i]['create_time']*1000);
			var status = data[i]['status'];
			var descId = parseInt(data[i]['description']) - 1;
			var descTpl = (descId >= 0) ? tplAuditTip[descId] : data[i]['extend1'];
			var allTip1 = (descId >= 0) ? tplAuditTip[descId]+"\n" : '';
			var allTip = ($.trim(data[i]['extend1']).length > 0) ? allTip1+data[i]['extend1'] : allTip1;
			
			str = '<tr tplA="'+ data[i]['account'] +'" tplF="'+ data[i]['tpl_file'] +'" tplD="'+ data[i]['tpl_id'] +'" id="'+ data[i]['id'] +'">'+
				'<td class="text-ellipsis" title="'+data[i]['tpl_name']+'">'+ data[i]['tpl_name'] +
				'<td class="text-ellipsis" title="'+data[i]['subject']+'">'+ data[i]['subject'] +
				'</td><td class="text-ellipsis" title="'+data[i]['account']+'">'+ data[i]['account'] +
				'</td><td>'+ tplStatus(data[i]['status']) +
				'</td><td class="text-ellipsis" title="'+allTip+'">'+ descTpl +
				'</td><td>'+ time +
				'</td><td>'+
				'<a href="javascript:void(0);" class="task-oper" mOper="checkTpl">' + auditT + '</a>' +
				'</td></tr>';
			html.push(str);
		}
		$("#tplManage_tb").html(html.join(""));
	}
	
	return {
		init: function(){
			var _this = this;
			
			pageBar = new PagingBar(
					$("#tplPageBar"), 
					pageInfo,
					function(pif) {
						var post = {method: "getList", page:pif.currPage, size:pif.pageSize};
						doAjax(post, function(data){
							if(data.code){
								gMainframe.showInfo(Lang.template_get_list_fail, {time: 1000});
								return;
							}
							pif.totalPage = Math.floor((1*data.total+pageInfo.pageSize-1)/pageInfo.pageSize) || 1;
							pageBar.render();
							showTplList(data);
						});
					}
				);
			
			gMainframe.addListener('viewloaded', function(vname){
				if(vname == "tpl_manage"){
					pageBar.go(1, true);
				}
			});
			
			gMainframe.addListener('viewchanged', function(vname){ 
			//正在审核
			});
			
			//刷新
			$('#manageReferash').click(function () {
				pageBar.go(1,true);
			});
			
			//审核
			$("#tplManage_tb").delegate("a", "click", function() {
				var fileTplId = $(this).parent().parent().attr("tplF");
				var tplD = $(this).parent().parent().attr("tplD");
				var id = $(this).parent().parent().attr("id");
				var tplA = $(this).parent().parent().attr("tplA");
				var tplSub = $(this).parent().parent().children(":first").next().attr("title");
				
				if(undefined!=fileTplId&&null!=fileTplId) {
					var newWindow = window.open('tpl/admin/tplCheckManage.html?id='+id+'&tplD='+tplD+'&tplSub='+tplSub+'&tplA='+encodeURIComponent(tplA),"new");
				}
			});
		},
		
		mReferash : function(){
			pageBar.go(1,true);
		}
	};
})();

tplManageController.init();