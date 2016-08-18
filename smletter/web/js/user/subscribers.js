var subs_control = (function() {
	var currAjax = null;
	var $data = {};
	var $tbody = $('#subs_tb');
	var pagingBar = null;
	
	var _currentId = _currentAccount = null;
	var $c = $("#view_subscribers");
	var $subslist = $c.children("div:first"),$newsubs = $c.children("div:last");
	var $ainmiteTime = 250;
	
	function _validate() {
		var r = true;
		var $a = $("#subs_email");
		var $v = $.trim($a.val());
		if($v.length == 0) {
			$a.focus();
			r = false;
		} else	if(!util.isMailAddr($a.val(), true)) {
			$a.focus();
			r = false;
		}
		return r;
	};
	
	function _showError(msg) {
	};
	
	function _getAccount(id) {
		_currentId = id;
		_currentAccount = subs_control.getData(id);
		return _currentAccount;
	}
	
	function _clearAccount() {
		_currentId = _currentAccount = null;
	}
	
	return {
		init: function() {
			var _this = this;
			
			pagingBar = new PagingBar('#subs_nav_page', null, function(pageInfo){
				_this.listAccount(pageInfo);
			});
			
			gMainframe.addListener('viewloaded', function(vname){
				if(vname == 'subscribers'){
					pagingBar.go(1, true);
				}
			});
			
			gMainframe.addListener('viewchanged', function(vname){
				if(vname != 'subscribers'){
//					_this.reset();
				}
			});
			
			$tbody.delegate("tr" ,"click", function () {
				var id = $(this).attr("v");
				if(undefined!=id&&null!=id) {
					var data = {method: "querySubs", id: id};
					_this.request(data, function(data) {
						if(data) {
							var ct = data['subscribed_time'];
							if(ct!=undefined) {
								ct = util.getDate(ct*1000);
								data['subscribed_time'] = ct;
							}
							for(c in data) {
								var d = data[c];
								$("#sub_" + c).text(d).val(d);
							}
							$('#edit_subs_modal').modal("show");
						}
					});
					
				};
			});
			
//		modal 删除
			$("#subs_del").click(function() {
				var id = $("#sub_id").val();
				if(undefined!=id&&null!=id) {
					$('#edit_subs_modal').modal("hide");
					gMainframe.confirm(Lang.subs_delete_confirm, $data[id], function() {
						_this.del(id);
					} );
				}
			});
			
			$("#subs_save").click(function() {
				var id = $("#sub_id").val();
				if(undefined!=id&&null!=id) {
					var des = $.trim($("#sub_description").val());
					var data = {method:"update", id: id, des:des};
					_this.request(data, function(code) {
						if(code==1) {
							$('#edit_subs_modal').modal("hide");
						}
					});
					
				}
			});
			
			$("#subs_new").click(function() {
				$subslist.slideToggle($ainmiteTime||0,	function() {
					$newsubs.slideToggle($ainmiteTime||0);
					_clearAccount();
				});
			});
			
		    
			$("#subs_return").on("click", function() {
				$newsubs.slideToggle($ainmiteTime||0,	function() {
					$subslist.slideToggle($ainmiteTime||0);
					_clearAccount();
				});
			});
//			逐条删除			
			$tbody.delegate("span", "click", function(event) {
				var id = $(this).parent().parent().attr("v");
				
				if(undefined!=id&&null!=id) {
					gMainframe.confirm(Lang.subs_delete_confirm, $data[id], function() {
						_this.del(id);
					} );
				}
				event.stopPropagation();
			});
//			全部删除
			$("#subs_delete_all").click(function() {
				gMainframe.confirm(Lang.subs_warning, Lang.subs_del_all_warning, function() {
					_this.delall();
				} );
				
			});
			$("#subs_add").click(function () {
				$this = $(this);
				if(!_validate()) {
					return;
				}
				var dat = $.trim($("#subs_email").val());
				var data = {method: "add", emails: dat};
				_this.request(data, function(code) {
					if(code==1) {
						_this.refresh();
						$("#subs_email").val('');
						$("#subs_return").click();
					}
				});
			});
		    
		},    //////////////init end/////////////
		delall: function() {
			var data = {method:"delall"};
			var _this = this;
			this.request(data, function(code) {
				if(code>=1) {
					_this.refresh();
				}
			});
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
		
		buildList: function(data) {
			var html = [];
			var len = data.length;
			for(var i=0;i<len;i++) {
				var dt = data[i];
				var ct = dt['subscribed_time']; ct = ct?ct:"";
				ct = util.getDate(ct*1000);
				html.push ('<tr v="' + dt['id'] + '">'+
			    '<td class="text-ellipsis">' + dt['subscriber'] + '</td>'+
			    '<td class="text-ellipsis">' + ct + 
			    '&nbsp;&nbsp;&nbsp;<span class="glyphicon glyphicon-remove" aria-hidden="true"></span>' + 
			    '</td>'+
		    '</tr>');
				$data[dt['id']] = dt['subscriber'];
			}
			return html.join('');
		},
		
		listAccount: function(_pageInfo){
			var _this = this;
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
		
		showError: function(msg) {
		},
		
		request: function(post, callback){
			if(currAjax){
	    		currAjax.abort();
			}
	    	currAjax = $.ajax({
	    	   url: 'web/php/user/subs.php',
			   type: "POST",
			   dataType: 'json',
			   data: post,
			   success: function(data, textStatus, qXHR){
				   if($.isFunction(callback)){
					   callback(data, textStatus);
				   }
			   }
			});
		},
		
		getData: function(id) {
			return $data[id]||{};
		}
	}
})();
$(function() {
	subs_control.init();
})