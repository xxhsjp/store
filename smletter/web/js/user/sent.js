var sent_control = (function() {
	var currAjax = null;
	var $data = {};
	var $tbody = $('#sent_tb');
	var $error = $("#ac_error");
	var pagingBar = null;
	
	var _currentId = _currentAccount = null;
	var $c = $("#view_sent");
	var $accList = $c.children("div:first"),$orderList = $c.children("div:last");
	var $ainmiteTime = 250;
	
	function _showError(msg) {
		if(null!=msg||undefined!=msg) {
			$error.html("<strong>" + msg + "</strong>").fadeIn(1000, function() {
				window.setTimeout(function() {
					$error.fadeOut(1000);
				}, 2000);
			});
		}
	};
	
	function _getAccount(id) {
		_currentId = id;
		_currentAccount = sent_control.getData(id);
		return _currentAccount;
	}
	
	function _clearAccount() {
		_currentId = _currentAccount = null;
	}
	
	return {
		init: function() {
			var _this = this;
			
			pagingBar = new PagingBar('#sent_nav_page', null, function(pageInfo){
				_this.listAccount(pageInfo);
			});
			
			gMainframe.addListener('viewloaded', function(vname){
				if(vname == 'sent'){
					pagingBar.go(1, true);
				}
			});
			
			gMainframe.addListener('viewchanged', function(vname){
				if(vname != 'sent'){
//					_this.reset();
				}
			});
			
			$tbody.delegate("tr" ,"click", function () {
				var id = $(this).attr("v");
				if(undefined!=id&&null!=id) {
					$accList.slideToggle($ainmiteTime||0, function() {
						
						$orderList.slideToggle($ainmiteTime||0);
						$(".sent_subject").text(_getAccount(id)["subject"]||"");
						var html = "<div><span style=\"color: rgb(85, 85, 85); font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 16px; font-style: normal; font-weight: normal; line-height: 24px;\">This just a test!<br/> This just a test!<br/> This just a test!<br/> This just a test!<br/> This just a test!<br/> This just a test!<br/> This just a test!<br/> This just a test!<br/> This just a test!<br/> This just a test!<br/> This just a test!</span><br/> &nbsp;</div>";
						$("#sent_content").html(html);
					});
				};
			});//初始化用户列表中的 订单按钮
			
		    
			$("#sent_return").on("click", function() {
				$orderList.slideToggle($ainmiteTime||0,	function() {
					$accList.slideToggle($ainmiteTime||0);
					_clearAccount();
				});
			});
		    
		},    //////////////init end/////////////
		
		
		buildList: function(data) {
			data = [{'id': 1,'account': 1, 'subject':'test', 'sent_time':'2016/04/11'},
			        {'id': 2,'account': 1, 'subject':'test 1', 'sent_time':'2016/04/10'},
			        {'id': 3,'account': 2, 'subject':'测试', 'sent_time':'2016/04/09'},
			        {'id': 4,'account': 1, 'subject':'大家好', 'sent_time':'2016/04/08'},
			        {'id': 5,'account': 4, 'subject':'订阅', 'sent_time':'2016/04/08'},];
			var html = [];
			var len = data.length;
			for(var i=0;i<len;i++) {
				var dt = data[i];
				var ct = dt['sent_time']; ct = ct?ct:"";
				html.push ('<tr v="' + dt['id'] + '">'+
			    '<td class="text-ellipsis">' + dt['account'] + '</td>'+
			    '<td class="text-ellipsis">' + dt['subject']+ '</td>'+
			    '<td class="text-ellipsis">' + ct + '</td>'+
		    '</tr>');
				$data[dt['id']] = dt;
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
	    	   url: 'web/php/user/sent.php',
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
	sent_control.init();
})