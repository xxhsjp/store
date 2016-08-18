var composeController = (function(){
	var formName = null;
	var isSending = false;
	
	function validateData(form){
		var r = true;
		
		var formName = form || "compose_form";
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
			url: "web/php/user/compose.php",
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
	
	
	return {
		init: function(){
			var _this = this;

			if(!CKEDITOR.instances['editor1']){
				window.editor1 = CKEDITOR.replace('editor1');
			}
			
			//定时保存草稿
			//timeAutoMail = setInterval(timePostmail,180000);
			
			gMainframe.addListener('viewloaded', function(vname){ 
				if(vname == "compose"){
					if(!CKEDITOR.instances['editor1']){
						CKEDITOR.replace('editor1');
					}
				}
			});
			
			gMainframe.addListener('viewchanged', function(vname){
				if(vname == "compose"){
					if(isSending){
						return;
					}
				}
				
				if(vname=="replies" || vname == "drafts"){
					_this.timePostmail();
					_this.reset();
				}
				
				if(vname == "sent"){
					_this.timePostmail();
					_this.reset();
					gMainframe.changeView("sent");
				}
			});
			
			//刷新
			$('#uploadReferash').click(function () {
				
			});
			
			//send now
			$("#sendNowButton").click(function(){
				_this.sendMail();
			});
			
			//preview
			$("#sendPreviewButton").click(function(){
				
			});
			
			//save draft
			$("#saveDraftButton").click(function(){
				//_this.timePostmail();
				gMainframe.showInfo(Lang.compose_save_draft_ok, {time: 1000});
				_this.reset();
				return;
				
			});
			
			//del mail
			$("#delMailButton").click(function(){
				gMainframe.showInfo(Lang.compose_del_ok, {time: 1000});
				_this.reset();
				return;
				
			});
		},
		
		//发送
		sendMail : function(){
			gMainframe.showInfo(Lang.compose_send_ok, {time: 1000});
			this.reset();
			return;
			
			if(isSending){
				return;
			}
			
			gMainframe.showInfo(Lang.compose_sending, null, 'sending');
			isSending = true;
			var subject = $('#composeSubject').val() || '';
			var editCon = CKEDITOR.instances.editor1.getData();
			var data = {method:"send",content:editCon,subject:subject};
			
			this.request(data, function(result){
				if(result.success == "false"){
					gMainframe.changeInfo(Lang.compose_send_fail, {time: 2000}, 'sending');
				}else {
					gMainframe.changeInfo(Lang.compose_send_ok, {fn: function(){
						this.reset();
						gMainframe.fireEvent('viewchanged', 'sent');
					}, time: 2000}, 'sending')
				}
			});
		},
		
		//自动保存draft
		timePostmail : function(){
			//gMainframe.showInfo(Lang.compose_save_draft_ok, {time: 1000});
			this.reset();
			return;
		},
		
		reset: function(){
			$("#compose_form").resetForm();
			
			//CKEDITOR.remove(CKEDITOR.instances['editor1']);
			CKEDITOR.instances.editor1.setData('');	
			
			isSending = false;
			mailName = '';
			jsonName = '';
			sendType = '';
		},
		
		request: function(post, callback){
			$.ajax({
			   type: "POST",
			   url: "web/php/user/compose.php",
			   data: post,
			   dataType: 'json',
			   success: function(data){
				   if($.isFunction(callback)){
					   callback(data);
				   }
			   }
			});
		}
	};
})();

composeController.init();