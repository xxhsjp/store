var draftsListController = (function(){
	var formName = null;
	var isSending = false;
	var pagingBar = null;
	
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

			pagingBar = new PagingBar('pageBar_drafts', null, function(pageInfo){
				_this.draftsList(pageInfo);
			});
			
			gMainframe.addListener('viewloaded', function(vname){ 
				if(vname == "drafts"){
					$("#draftsList").show();
					formName = "drafts";
					pagingBar.go(1, true);
				}
			});
			
			gMainframe.addListener('viewchanged', function(vname){
			});
			
			//删除
			$("#draftsList_tb").delegate("a", "click", function() {
				var operTpl = $(this).attr('mailOper');
				var mailId = $(this).parent().parent().attr("id");
				if(operTpl == "delMail"){
					var delRTitle = Lang.compose_tip;
					var delRContent = Lang.compose_delete_tip;
					gMainframe.confirm(delRTitle,delRContent,function(){
						$("#"+mailId).remove();
						_this.delDraftsFunction(mailId);
					})
				}else if(operTpl == "composeMail"){
					_this.composeShowMail(mailId);
				}
				return false;
			});
			
			$("#draftsList_tb").delegate("tr", "click", function() {
				var mid = $(this).attr("id");
				_this.composeShowMail(mid);
			});
			
			$("#draftsList_tb").delegate("tr", "mouseover", function() {
				$(this).find(".glyphicon").addClass("glyphicon-remove");
			});
			
			$("#draftsList_tb").delegate("tr", "mouseout", function() {
				$(this).find(".glyphicon").removeClass("glyphicon-remove");
			});
		},
		
		composeShowMail: function(id){
			var dataStr = "测试开始：你好啊啊啊啊啊啊啊啊啊啊啊啊"+
			"测试测试测试测试测试测试测试测试<br>"+
			"测试测试测试测试测试测试测试测试测试测试测试测试<br>"+
			"测试测试测试测试test测试草稿编辑试测试测试<br>"+
			"测试测试测试测试测试测试测试测试测试测试测试测试<br>"+
			"测试测试测试测试test测试草稿编辑试测试测试测试测试<br>";
			subject = "test测试草稿编辑";
			
			//this.reset();
			gMainframe.changeView("compose");
			
			window.setTimeout(function(){
				$("#composeSubject").val(subject);
				if(!window.editor1){
					CKEDITOR.replace('editor1');
				}
				CKEDITOR.instances.editor1.setData(dataStr);
    		}, 900);
		},
		
		delDraftsFunction:function(delId){
			gMainframe.showInfo(Lang.compose_del_ok, {time: 1000});
			return;
			
			var data = {method:"delMail", "id": delId , flag: "drafts"};
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
		
		draftsList : function(_pageInfo){
			var _this = this;
			_pageInfo = _pageInfo;
			var post = {method: "getList", page: _pageInfo.currPage, size: _pageInfo.pageSize, flag: "drafts"};
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
				$('#draftsList_tb').html(html);
			});
		},
		
		buildList : function(data){
			data = [{'id': 1,'subject':'测试aaaaaa', 'time':'2016/04/11'},
			        {'id': 2,'subject':'tttt啊啊啊啊啊啊啊啊', 'time':'2016/04/10'},
			        {'id': 3,'subject':'测试tttttttt', 'time':'2016/04/09'},
			        {'id': 4,'subject':'aaaa啊啊啊', 'time':'2016/04/08'},
			        {'id': 5,'subject':'ceshi顶顶顶顶顶顶顶顶顶', 'time':'2016/04/08'}];
			var html = [];
			var str = "";
			
			for(var i=0;i<data.length;i++){
				var delM = Lang.compose_del_mail;
				var time = util.getDate(data[i]['time']*1000);
				
				str = '<tr id="dr_'+ data[i]['id']+'">'+
						'<td class="text-ellipsis">'+
						'<a href="javascript:void(0);" class="task-oper" mailOper="composeMail" title="'+data[i]['subject']+'">'+ data[i]['subject'] +
						'</a></td><td class="text-ellipsis" title="'+data[i]['time']+'">'+ data[i]['time'] +
						'</td><td class="text-ellipsis">'+
						'<a href="javascript:void(0);" class="delRed" mailOper="delMail"><span class="glyphicon" ></span></a>' +
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
			$('#draftsList_tb').html('');
		},
		
		reset: function(){
			$("#draftsList_tb").resetForm();
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

draftsListController.init();