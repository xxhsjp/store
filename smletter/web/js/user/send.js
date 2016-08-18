var send_control = (function() {
	var currAjax = null;
	var $data = {};
	var $type = ["TXT","CSV"];
	var $form =  $("#send_form");
	var $tbody = $('#send_tb');
	var $delbtn = $('#send_del_modal .btn-primary');
	var $error = $("#sd_error");
	
	var pagingBar = null;	
	function _validate() {
		//ar_lname ar_als ar_des ar_file
		var r = true;
		$form.find("input[type=text]").each(function() {
			var v = $(this).val();
			if(!util.isMailAddr(v, true)){
				$(this).focus();
				r = false;
				return false;
			}
		});
		if(r){
			var t = $("#sd_type").val();
			if(t.length==0) {
				r = false;
				$("#send_type_menu").dropdown('toggle');
			}
		}
		return r;
	};

	var _opts = {
		type:"POST",
		dataType: "json",
		url: "web/php/user/send.php",
		beforeSubmit: function() {
			return _validate();
		},
		beforeSend: function() {
		}
	};
	
	function _getErrMsg(code) {
		var err = "";
		switch(code) {
			case 9:
				err = Lang.send_acc_not_unique;
			break;
			default:
				err = Lang.send_new_error;
		}
		return err;
			
	}
	

	return {
		init: function() {
			var _this = this;
			
			pagingBar = new PagingBar('#send_nav_page', null, function(pageInfo){
				_this.listSend(pageInfo);
			});
			
			gMainframe.addListener('viewloaded', function(vname){
				if(vname == 'send_account'){
					pagingBar.go(1, true);
				}
			});
			
			gMainframe.addListener('viewchanged', function(vname){
				if(vname != 'send_account'){
//					_this.reset();
				}
			});
			
			$("#send_sub").click(function () {
				if(!_validate()) {
					return;
				}
				$this = $(this);
				var data = $form.serialize() + "&method=add";
				_this.request(data, function(msg) {
					if(1==msg.code) {
						$("#send_modal").modal("hide");
						_this.refresh();
						$form.resetForm();
					} else{
						_this.showError(_getErrMsg(msg.code));
					}
				});
			});
			
//			刷新按钮
			$("#send_refresh").click(function() {
				_this.refresh();
			});
//			删除modal
			$tbody.delegate("a", "click", function() {
				var id = $(this).attr("v");
				if(undefined!=id&&null!=id) {
					gMainframe.confirm(Lang.send_warning,Lang.send_warning_info, function() {
						_this.del(id);
					} );
				}
			});

//			去前后空格
			$form.find("input[type=text], textarea").blur(function() {
				var val = $.trim($(this).val());
				$(this).val(val);
			});
//			类型选择	
			$form.find(".dropdown-menu").find("li").click(function() {
				var $li = $(this);
				var type = $li.text();
				$("#send_type_menu").html((type + '  <span class="caret"></span>'));
				$("#sd_type").val($li.index());
			});

		},
		
		buildList: function(data) {
			var html = [];
			var lang_down = Lang.addr_download, lang_rem = Lang.addr_remove;
			var len = data.length;
			for(var i=0;i<len;i++) {
				var dt = data[i];
				var t = new Function("return Lang.send_type_" + dt['type'])();
				html.push ('<tr>'+
			    '<td class="text-ellipsis">' + t + '</td>'+
			    '<td class="text-ellipsis">' + dt['mail_from'] + '</td>'+
			    '<td class="text-ellipsis">' + dt['status'] + '</td>'+
			    '<td class="text-ellipsis">' + dt['reply_to'] + '</td>'+
			    '<td><a href="javascript:void(0);" v="' + dt['id'] + '">' + Lang.send_remove + '</a></td>'+
		    '</tr>');
			}
			return html.join('');
		},		
		
		listSend: function(_pageInfo){
			var _this = this;
			_pageInfo = _pageInfo;
			var post = {method: "query", page: _pageInfo.currPage, size: _pageInfo.pageSize};
			this.request(post, function(data){
				if(data.code && data.code == 500){
					gMainframe.showInfo(Lang.exception_info_serv, {time: 1000});
					return;
				}
				_pageInfo.totalPage = Math.floor((1*data.total+_pageInfo.pageSize-1)/_pageInfo.pageSize) || 1;
				pagingBar.render();
				var html = _this.buildList(data.result);
				$tbody.html(html);
			});
		},
		
		refresh: function() {
			this.clearList();
			pagingBar.go(1, true);
		},

		clearList: function(){
			$tbody.html('');
		},
		
		del: function(id) {
			var data = {method:"del", "id": id};
			var _this = this;
			this.request(data, function(code) {
				if(code==1) {
					_this.refresh();
				}
			});
		},
		
		showError: function(msg) {
			if(null!=msg||undefined!=msg) {
				$error.html("<strong>" + msg + "</strong>").fadeIn(1000, function() {
					window.setTimeout(function() {
						$error.fadeOut(1000);
					}, 2000);
				});
			}
		},
		
		request: function(post, callback){
			if(currAjax){
	    		currAjax.abort();
			}
	    	currAjax = $.ajax({
	    	   url: 'web/php/user/send.php',
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
	}
})();
$(function() {
	send_control.init();
})

