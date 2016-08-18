var addr_control = (function() {
	var currAjax = null;
	var $data = {};
	var $type = ["TXT","CSV","XLS"];
	var $form =  $("#addr_form");
	var $tbody = $('#addr_tb');
	var $error = $("#ar_error");
	
	var pagingBar = null;	
	function _validate() {
		//ar_lname ar_als ar_des ar_file
		var r = true;
		$form.find("input[type=text], input[type=file]").each(function() {
			if(undefined!==$(this).attr("required")) {
				var v = $(this).val();
				if(v.length==0) {
					$(this).focus();
					r = false;
				}
			}
		});
		if(r) {
			r = _checkType();
		}
		return r;
	};

	function _getErrMsg(code) {
		var err = "";
		switch(code) {
			case 99:
				err = Lang.addr_file_type;
			break;
			case 9:
				err = Lang.addr_list_not_unique;
			break;
			case 88:
				err = Lang.addr_upload_off_limits;
			break;
			case 90:
				err = Lang.addr_upload_empty;
			break;
			case 97:
				err = Lang.addr_outof_gauge;
			break;			
			default:
				err = Lang.addr_upload_error;
		}
		return err;
			
	}
	var _opts = {
			type:"POST",
			dataType: "json",
			url: "web/php/user/addr.php"
		};
	
//	文件type检查
	function _checkType() {
		var r = false;
		var filepath = $("#ar_file").val();
		var val = $.trim(filepath);
		if(val.length > 0){
	        var extStart=filepath.lastIndexOf(".");
	        var ext=filepath.substring(extStart+1).toUpperCase();
	        if($.inArray(ext, $type) >= 0) {
	        	r = true;
	        }
		}
		if(!r) {
			$("#ar_file").focus();
		}
		return r;
	}
	
	return {
		init: function() {
			var _this = this;
			
			pagingBar = new PagingBar('#addr_nav_page', null, function(pageInfo){
				_this.listAddr(pageInfo);
			});
			
			gMainframe.addListener('viewloaded', function(vname){
				if(vname == 'upload_addr'){
					pagingBar.go(1, true);
				}
			});
			
			gMainframe.addListener('viewchanged', function(vname){
				if(vname != 'upload_addr'){
//					_this.reset();
				}
			});
			
			$("#addr_sub").click(function () {
				$this = $(this);
				if(!_validate()) {
					return;
				}
				var opt = _opts;
				opt.data = {method: "add"};
				opt.beforeSend = function() {
					$this.button("loading");
				};
				opt.success = function(msg) {
					if(1==msg.code) {
						$("#addr_modal").modal("hide");
						_this.refresh();
						$form.resetForm();
					} else{
						_this.showError(_getErrMsg(msg.code));
					}
				};
				opt.complete = function() {
					$this.button("reset");
				};
				$form.ajaxSubmit(opt);
			});
			
//			刷新按钮
			$("#addr_refresh").click(function() {
				_this.refresh();
			});
//			删除modal
			$tbody.delegate("a.del", "click", function() {
				var id = $(this).parent().attr("v");
				if(undefined!=id&&null!=id) {
					gMainframe.confirm(Lang.addr_warning,Lang.addr_warning_info, function() {
						_this.del(id);
					} );
				}
			});
			$tbody.delegate(".down", "click", function() {
				var id = $(this).parent().attr("v");
				if(undefined!=id&&null!=id) {
					var file = $data[id] || null;
					if(file) {
						window.location = "web/php/user/addr.php?method=download&file="+file;
					}
				}
			});
//			去前后空格
			$form.find("input[type=text], textarea").blur(function() {
				var val = $.trim($(this).val());
				$(this).val(val);
			});
//			模板下载
			$("#ar_file").next("p").find("a").click(function() {
				window.location = "web/php/user/addr.php?method=template&t="+$(this).text();
			});

		},
		
		buildList: function(data) {
			var html = [];
			var lang_down = Lang.addr_download, lang_rem = Lang.addr_remove;
			var len = data.length;
			for(var i=0;i<len;i++) {
				var dt = data[i];
				var t = util.getDate(dt['create_time']*1000);
				html.push ('<tr>'+
			    '<td class="text-ellipsis">' + dt['addrlist_name'] + '</td>'+
			    '<td class="text-ellipsis">' + dt['description'] + '</td>'+
			    '<td class="text-ellipsis">' + dt['addrs_sum'] + '</td>'+
			    '<td class="text-ellipsis">' + t + '</td>'+
			    '<td v="' + dt['id'] + '"><a href="javascript:void(0);" class="down task-oper">' + lang_down + '</a><a href="javascript:void(0);" class="del task-oper">' + lang_rem + '</a></td>'+
		    '</tr>');
				$data[dt['id']] = dt['addrlist_file'];
			}
//			$tbody.html(html.join(''));
			return html.join('');
		},		
		
		listAddr: function(_pageInfo){
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
			var file = $data[id] || null;
			var data = {method:"del", "id": id, "file": file};
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
	    	   url: 'web/php/user/addr.php',
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
	addr_control.init();
})

