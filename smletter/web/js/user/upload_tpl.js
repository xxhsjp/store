var uploadTplController = (function(){
	var delTplId = null;
	var editTplId = null;
	var tplNum = null;
	var pageInfo = {currPage: 1, pageSize: 30, totalPage: 1}; 
	var pageBar = null;
	var tplTotal = null;
	var formName = null;
	var typeAll = ["zip"];
	var tplAuditReason = [Lang.template_audit_fail_reason_1,
	                   Lang.template_audit_fail_reason_2,
	                   Lang.template_audit_fail_reason_3,
	                   Lang.template_audit_fail_reason_4,
	                   Lang.template_audit_fail_reason_5
	                  ];
	
	function validateData(form){
		var r = true;
		
		var formName = form || "tpl_form";
		$("#"+formName).find("input[type=text]").each(function() {
			if("undefined"!==$(this).attr("required")) {
				var v = $(this).val();
				if(v.length===0) {
					$(this).focus();
					r = false;
				}
			}
		});
		return r;
	}
	
	var ajaxOptions = {
			type:"POST",
			dataType: "json",
			url: "web/php/user/template.php",
			beforeSubmit: function() {
				return validateData(formName);
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
			case 922:
				err = Lang.template_audit_tip;
				break;
			case 921:
				err = Lang.template_edit_tpl_fail;
				break;
			case 920:
				err = Lang.template_add_tpl_fail;
				break;
			case 919:
				err = Lang.template_name_null;
				break;
			case 918:
				err = Lang.template_subject_null;
				break;
			case 917:
				err = Lang.template_subject_limit;
				break;
			case 916:
				err = Lang.template_pic_info;
				break;
			case 915:
				err = Lang.template_zip_limit;
				break;
			case 914:
				err = Lang.template_no_be_audit;
				break;
			case 913:
				err = Lang.template_file_size_limit;
				break;
			case 912:
				err = Lang.template_num_limit;
				break;
			case 911:
				err = Lang.template_expired_no_open;
				break;
			case 909:
				err = Lang.template_preview_tpl_fail;
				break;
			case 907:
				err = Lang.template_del_tpl_fail;
				break;
			case 904:
				err = Lang.template_input_tpl_content;
				break;
			case 903:
				err = Lang.template_file_type_error;
				break;
			case 902:
				err = Lang.template_add_tpl_repeat;
				break;
			case 901:
				err = Lang.template_no_admin;
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
				status = Lang.template_auditing;
				break;
			case "2":
				status = Lang.template_audit_ok;
				break;
			case "3":
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
		var data = dataStr.data.result;
		tplNum = parseInt(dataStr.orderNum);
		tplTotal = parseInt(dataStr.data.total);
		
		for(var i=0;i<data.length;i++){
			var uploadT = Lang.template_upload_file;
			var delT = Lang.template_delete;
			var editT = Lang.template_edit;
			var viewT = Lang.template_preview;
			var auditT = Lang.template_submit_audit;
			var time = util.getDate(data[i]['create_time']*1000);
			var status = data[i]['status'];
			var descriptId = parseInt(data[i]['description']) - 1;
			var descriptTpl = (descriptId >= 0) ? tplAuditReason[descriptId] : data[i]['extend_1'];
			var allDTip1 = (descriptId >= 0) ? tplAuditReason[descriptId]+"\n" : '';
			var allDTip = ($.trim(data[i]['extend_1']).length > 0) ? allDTip1+data[i]['extend_1'] : allDTip1;
			
			str = '<tr id="'+ data[i]['id'] +'" sflag="'+ status +'">'+
				'<td class="text-ellipsis" title="'+data[i]['tpl_name']+'">'+ data[i]['tpl_name'] +
				'</td><td class="text-ellipsis" title="'+data[i]['subject']+'">'+ data[i]['subject'] +
				'</td><td class="text-ellipsis">'+ tplStatus(data[i]['status']) +
				'</td><td class="text-ellipsis" title="'+allDTip+'">'+ descriptTpl +
				'</td><td class="text-ellipsis">'+ data[i]['used_count'] +
				'</td><td class="text-ellipsis">'+ time +
				'</td><td class="text-ellipsis">'+
				'<a href="javascript:void(0);" class="task-oper" tplOper="viewTpl">' + viewT + '</a>' +
				'<a href="javascript:void(0);" class="task-oper" tplOper="delTpl">' + delT + '</a>' ;
			if(status != "1" && status != "2"){
				str += '<a href="javascript:void(0);" class="task-oper" tplOper="editTpl">' + editT + '</a>' +
						'<a href="javascript:void(0);" class="task-oper" tplOper="auditTpl" optId="'+ data[i]['id'] +'">'+ auditT + '</a>';
			}
			str += '</td></tr>';
			html.push(str);
		}
		$("#uploadtpl_tb").html(html.join(""));
	}
	
	return {
		init: function(){
			var _this = this;
			pageBar = new PagingBar(
					$("#pageBarTpl"), 
					pageInfo,
					function(pif) {
						var post = {method: "getList", page:pif.currPage, size:pif.pageSize};
						doAjax(post, function(data){
							if(data.code){
								gMainframe.showInfo(Lang.template_get_list_fail, {time: 1000});
								return;
							}
							pif.totalPage = Math.floor((1*data.data.total+pageInfo.pageSize-1)/pageInfo.pageSize) || 1;
							pageBar.render();
							showTplList(data);
						});
					}
				);
			
			gMainframe.addListener('viewloaded', function(vname){ 
				if(vname == "upload_tpl"){
					$("#tplNewAddShow").hide();
					$("#tplEditShow").hide();
					$("#tplList").show();
					pageBar.go(1, true);
				}
			});
			
			gMainframe.addListener('viewchanged', function(vname){
				if(vname == "upload_tpl"){
					_this.initNewTpl();
				}
			});
			
			//刷新
			$('#uploadReferash').click(function () {
				pageBar.go(1,true);
			});
			
			//新建模板触发
			$('#tplNewAdd').click(function () {
				if(tplTotal >= tplNum){
					gMainframe.showInfo(showErrorMsg(912), {time: 1000});
					return false;
				}
				$("#tplList").hide();
				$("#editorTplDiv").hide();
				$("#tplEditShow").hide();
				$("#tplNewAddShow").show();
				$("#uploadFileTplDiv").show();
				$("#inputTplfile").attr("disabled",false);
			});
			
			//新建模板
			$('#uploadAddButton').click(function () {
				$this = $(this);
				if(!validateData()) {
					return;
				}
				var opt = ajaxOptions;
				
				if(_this.checkUploadTplFile() == false){
					return false;
				}
				
				opt.data = {method: "addTpl"};
				
				opt.beforeSend = function() {
					$this.button("loading");
				};
				opt.success = function(msg) {
					if(1===msg.code) {
						$("#tplNewAddShow").hide();
						$("#tplEditShow").hide();
						$("#tplList").show();
						pageBar.go(1,true);
						doAjax(null, showTplList);
						$("#tpl_form").resetForm();
						gMainframe.showInfo(Lang.template_add_tpl_ok, {time: 1000});
					} else {
						gMainframe.showInfo(showErrorMsg(msg.code), {time: 1000});
					}
				}
				opt.complete = function() {
					$this.button("reset");
				};
				$("#tpl_form").ajaxSubmit(opt);
			});
			
			//编辑
			$('#editTplButton').click(function () {
				$this = $(this);
				if(!validateData("tpl_edit_form")) {
					return;
				}

				formName = "tpl_edit_form";
				var opt = ajaxOptions;
				var editCon = null;
				editCon = CKEDITOR.instances.editor1.getData();
				if(editCon.length<1){
					gMainframe.showInfo(showErrorMsg(904), {time: 1000});
					return false;
				}
				
				var editConSize = util.getByteLength(editCon);
				if(Math.ceil(editConSize/1024) > 300){
					gMainframe.showInfo(showErrorMsg(913), {time: 1000});
					return false;
				}
				
				opt.data = {method: "editTpl","tplId" : editTplId, "tplContent" : editCon};
				
				opt.beforeSend = function() {
					$this.button("loading");
				};
				opt.success = function(msg) {
					if(1===msg.code) {
						$("#tplNewAddShow").hide();
						$("#tplEditShow").hide();
						$("#tplList").show();
						pageBar.go(1,true);
						doAjax(null, showTplList);
						$("#tpl_edit_form").resetForm();
						CKEDITOR.instances.editor1.setData('');
						gMainframe.showInfo(Lang.template_edit_tpl_ok, {time: 1000});
					} else {
						gMainframe.showInfo(showErrorMsg(msg.code), {time: 1000});
					}
					formName = null;
				}
				opt.complete = function() {
					$this.button("reset");
				};
				$("#tpl_edit_form").ajaxSubmit(opt);
			});
			
			//删除  预览编辑触发 审核提交 
			$("#uploadtpl_tb").delegate("a", "click", function() {
				var operTpl = $(this).attr('tplOper');
				//var fileTplId = $(this).parent().parent().attr("tplF");
				var tplId = $(this).parent().parent().attr("id");
				var sFlag = $(this).parent().parent().attr("sflag");
				var tplTitle = $(this).parent().parent().children(":first").attr("title");
				var tplSub = $(this).parent().parent().children(":first").next().attr("title");

				if(operTpl == "delTpl"){
					var delTplTitle = Lang.template_message;
					var delTplContent = Lang.template_delete_tip;
					gMainframe.confirm(delTplTitle,delTplContent,function(){
						_this.delTplFunction(tplId);
					})
				}else if(operTpl == "viewTpl"){
					if(undefined!=tplId&&null!=tplId) {
						var newWindow = window.open('tpl/user/tpl_check_user.html?tplId='+tplId+'&sub='+encodeURIComponent(tplSub),'new');
					}
				}else if(operTpl == "editTpl"){
					if(sFlag == "1" || sFlag == "2"){
						gMainframe.showInfo(Lang.template_audit_tip, {time: 1000});
						return;
					}
					var data = {method:"showTpl", "tplId": tplId};
					doAjax(data, function(msg) {
						$("#tplList").hide();
						$("#tplNewAddShow").hide();
						$("#uploadFileTplDiv").hide();
						$("#tplEditShow").show();
						$("#editorTplDiv").show();
						$("#tplNameDisp").html(tplTitle);
						$("#tplSubjectDisp").val(tplSub);
						editTplId = tplId;
						if(!CKEDITOR.instances['editor1']){
							CKEDITOR.replace('editor1');
						}
						CKEDITOR.instances.editor1.setData(msg.tplHtml);
					});
					
				}else if(operTpl == "auditTpl"){
					var data = {method:"auditPostTpl", "id": tplId};
					doAjax(data, function(msg) {
						if(msg.code===1) {
							gMainframe.showInfo(Lang.template_post_audit_ok, {time: 1000});
						}else{
							gMainframe.showInfo(showErrorMsg(msg.code), {time: 1000});
						}
						pageBar.go(1,true);
					});
				}
			});
			
			//新建返回  清空表单数据
			$("#tplNewAddShow .btn-default").click(function(){
				$('#tpl_form').resetForm();
				$("#tplNewAddShow").hide();
				$("#tplEditShow").hide();
				$("#editorTplDiv").hide();
				$("#uploadFileTplDiv").hide();
				$("#tplList").show();
			});
			
			//编辑返回
			$("#tplEditShow .btn-default").click(function(){
				if(CKEDITOR.instances['editor1']){
					CKEDITOR.instances.editor1.setData('');
				}
				$("#tplNewAddShow").hide();
				$("#tplEditShow").hide();
				$("#editorTplDiv").hide();
				$("#uploadFileTplDiv").hide();
				$("#tplList").show();
			});
		},

		//删除
		delTplFunction:function(delId){
			var data = {method:"delTpl", "id": delId};
			doAjax(data, function(msg) {
				if(msg.code===1) {
					gMainframe.showInfo(Lang.template_del_tpl_ok, {time: 1000});
				}else{
					gMainframe.showInfo(showErrorMsg(msg.code), {time: 1000});
				}
				if(pageInfo.currPage == pageInfo.totalPage){
					pageInfo.currPage = pageInfo.currPage - 1;
				}
				pageBar.go(pageInfo.currPage,true);
			});
		},
		
		checkUploadTplFile : function(){
			var fNameTpl = $.trim($("#inputTplfile").val());
			var fTypeTpl = null;

			if(fNameTpl.lastIndexOf("\\") !== -1){
				fNameTpl = fNameTpl.toLowerCase().substr(fNameTpl.lastIndexOf("\\")+1);
			}
			
			if(fNameTpl.length < 1 || fNameTpl != "send.zip"){
				gMainframe.showInfo(Lang.template_file_type_error, {time: 1000});
				return false;
			}
			
			fTypeTpl = fNameTpl.toLowerCase().substr(fNameTpl.lastIndexOf(".")+1);
			if($.inArray(fTypeTpl, typeAll) < 0){
				gMainframe.showInfo(Lang.template_file_type_error, {time: 1000});
				return false;
			}
			return true;
		},
		 
		initNewTpl : function(){
			/*if(CKEDITOR.instances['editor1']){
			    CKEDITOR.remove(CKEDITOR.instances['editor1']);
			}*/
			$("#tpl_form").resetForm();
			$("#tplNewAddShow").hide();
			$("#tplList").show();
			//pageBar.go(1, true);
		}
	};
})();

uploadTplController.init();