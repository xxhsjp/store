var repliesListController = (function(){
	var formName = null;
	var isSending = false;
	var pagingBar = null;
	
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

			pagingBar = new PagingBar('pageBar_replies', null, function(pageInfo){
				_this.repliesList(pageInfo);
			});
			
			gMainframe.addListener('viewloaded', function(vname){ 
				if(vname == "replies"){
					$("#repliesShow").hide();
					$("#repliesList").show();
					
					formName = "replies";
					pagingBar.go(1, true);
				}
			});
			
			gMainframe.addListener('viewchanged', function(vname){
				if(vname == "replies"){
					$("#repliesShow").hide();
					$("#repliesList").show();
					
					formName = "replies";
					pagingBar.go(1, true);
				}
				
				if(vname == "compose"){
					//保存草稿
					composeController.timePostmail();
				}
			});
			
			//删除 回复
			$("#repliesList_tb").delegate("a", "click", function() {
				var operTpl = $(this).attr('mailOper');
				var mailId = $(this).parent().parent().attr("id");
				
				if(operTpl == "delMail"){
					var delRTitle = Lang.compose_tip;
					var delRContent = Lang.compose_delete_tip;
					gMainframe.confirm(delRTitle,delRContent,function(){
						$("#"+mailId).remove();
						_this.delReplies(mailId);
					})
				}else if(operTpl == "replyMail"){
					_this.replyShowMail(mailId);
				}
				
				return false;
			});
			
			$("#repliesList_tb").delegate("tr", "click", function() {
				var mid = $(this).attr("id");
				_this.replyShowMail(mid);
			});
			
			$("#repliesList_tb").delegate("tr", "mouseover", function() {
				$(this).find(".glyphicon").addClass("glyphicon-remove");
			});
			
			$("#repliesList_tb").delegate("tr", "mouseout", function() {
				$(this).find(".glyphicon").removeClass("glyphicon-remove");
			});
						
			//回复
			$("#repliesButton").click(function () {
				gMainframe.showInfo(Lang.compose_reply_ok, {time: 1000});
				return;
				
				var rContent = CKEDITOR.instances.repliesMailEditor1.getData();
				var data = {method:"reply","content": rContent};
				this.request(data, function(msg) {
					$("#repliesList").hide();
					$("#repliesShow").show();
					$("#repliesContent").html(msg.data);
					if(!CKEDITOR.instances['repliesMailEditor1']){
						CKEDITOR.replace('repliesMailEditor1');
					}
				});
			});
			
			//取消
			$("#repliesCancelButton").click(function () {
				$("#repliesContent").html('');
				CKEDITOR.instances.repliesMailEditor1.setData('');	
				$("#repliesShow").hide();
				$("#repliesList").show();
			});
			
			//删除
			$("#repliesDelButton").click(function () {
				gMainframe.showInfo(Lang.compose_del_ok, {time: 1000});
				return;
			});
		},
		
		replyShowMail: function(id){
			data = "你好啊啊啊啊啊啊啊啊啊啊啊啊"+
				"测试测试测试测试测试测试测试测试<br>"+
				"测试测试测试测试测试测试测试测试测试测试测试测试<br>"+
				"测试测试测试测试测试测试测试测试测试测试测试测试<br>"+
				"测试测试测试测试测试测试测试测试测试测试测试测试<br>"+
				"测试测试测试测试测试测试测试测试测试测试测试测试测试测试<br>";
				
			$("#repliesList").hide();
			$("#repliesShow").show();
			$("#repliesContent").html(data);
			if(!CKEDITOR.instances['repliesMailEditor1']){
				CKEDITOR.replace('repliesMailEditor1');
			}
			return;
			
			var data = {method:"replyShow", "id": id};
			this.request(data, function(msg) {
				$("#repliesList").hide();
				$("#repliesShow").show();
				$("#repliesContent").html(msg.data);
				if(!CKEDITOR.instances['repliesMailEditor1']){
					CKEDITOR.replace('repliesMailEditor1');
				}
			});
		},
		
		delReplies:function(delId){
			gMainframe.showInfo(Lang.compose_del_ok, {time: 1000});
			return;
			
			var data = {method:"delMail", "id": delId , flag: "replies"};
			this.request(data, function(msg) {
				if(msg.code===1) {
					gMainframe.showInfo(Lang.compose_del_ok, {time: 1000});
				}else{
					gMainframe.showInfo(showErrorMsg(msg.code), {time: 1000});
				}
				if(pageInfo.currPage == pageInfo.totalPage){
					pageInfo.currPage = pageInfo.currPage - 1;
				}
				pageBar.go(pageInfo.currPage,true);
			});
		},
		
		repliesList : function(_pageInfo){
			var _this = this;
			_pageInfo = _pageInfo;
			var post = {method: "getList", page: _pageInfo.currPage, size: _pageInfo.pageSize, flag: "replies"};
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
				$('#repliesList_tb').html(html);
			});
		},
		
		buildList : function(data){
			data = [{'id': 1,'from': 'zxy@send.com', 'subject':'测试', 'time':'2016/04/11'},
			        {'id': 2,'from': 'zxy@send.com', 'subject':'tttt', 'time':'2016/04/10'},
			        {'id': 3,'from': 'zxy@send.com', 'subject':'测试', 'time':'2016/04/09'},
			        {'id': 4,'from': 'zxy@send.com', 'subject':'aaaa啊啊啊', 'time':'2016/04/08'},
			        {'id': 5,'from': 'zxy@send.com', 'subject':'ceshi', 'time':'2016/04/08'}];
			var html = [];
			var str = "";
			
			for(var i=0;i<data.length;i++){
				var delM = Lang.compose_del_mail;
				var time = util.getDate(data[i]['time']*1000);
				
				str = '<tr id="re_'+ data[i]['id']+'">'+
						'<td class="text-ellipsis">' +
						'<a href="javascript:void(0);" class="task-oper" mailOper="replyMail"  title="'+data[i]['from']+'">'+ data[i]['from'] +
						'</td><td class="text-ellipsis" title="'+data[i]['subject']+'">'+ data[i]['subject'] +
						'</td><td class="text-ellipsis" title="'+data[i]['time']+'">'+ data[i]['time'] +
						'</td><td class="text-ellipsis">'+
						'<a href="javascript:void(0);" class="task-oper" mailOper="delMail"><span class="glyphicon" ></span></a>' +
						'</td></tr>';
				html.push(str);
			}
			return html.join("");
		},
		
		refresh: function(page){
			page = page || 1;
			this.clearList();
			pagingBar.go(page, true);
		},
		
		clearList: function(){
			$('#repliesList_tb').html('');
		},
		
		reset: function(){
			$("#repliesList_tb").resetForm();
		},
		
		request: function(post, callback){
			$.ajax({
			   type: "POST",
			   url: "web/php/user/mailList.php",
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

repliesListController.init();