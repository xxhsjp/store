//仅对jquery生效
function PagingBar(nav, pageInfo, callback) {
	this.nav = $.type(nav) === "string" ? $(nav) : nav;
	
	this.info = pageInfo || {currPage: 1, pageSize: 20, totalPage: 1};
	
	this.callback = callback;
	
	var _this = this;
	this.nav.delegate('A', 'click', function(){
		var oper = $(this).attr('oper');
		var isDisabled = $(this).parent().hasClass('disabled');
		if(oper == 'prev'){
			if(!isDisabled){
				_this.go(_this.info.currPage-1);
			}
		}else if(oper == 'next'){
			if(!isDisabled){
				_this.go(_this.info.currPage+1);
			}
		}else if(oper == 'first'){
			if(!isDisabled){
				_this.go(1);
			}
		}else if(oper == 'last'){
			if(!isDisabled){
				_this.go(_this.info.totalPage);
			}
		}else {
			var page = $(this).html();
			_this.go(page);
		}
	});
	
}

PagingBar.prototype.render = function(info){
	var info = info || this.info;
	if(info.currPage > info.totalPage){
		info.currPage = info.totalPage;
	}
	if(info.currPage < 1){
		info.currPage = 1;
	}
	var prev_status = info.currPage == 1 ? "disabled" : "";
	var next_status = info.currPage == info.totalPage ? "disabled" : "";
	
	var first_status = info.currPage == 1 ? "disabled" : "";
	var last_status = info.currPage == info.totalPage ? "disabled" : "";
	
	var bar = '<li class="'+first_status+'">';
	bar +=    	'<a href="javascript:void(0);" oper="first">';
	bar +=      	'<span aria-hidden="true">&laquo;&laquo;</span>';
	bar +=      '</a>';
	bar +=    '</li>';
	
	bar += '<li class="'+prev_status+'">';
	bar +=    	'<a href="javascript:void(0);" oper="prev">';
	bar +=      	'<span aria-hidden="true">&laquo;</span>';
	bar +=      '</a>';
	bar +=    '</li>';
	
	if(info.currPage != 1 && info.currPage >= 4 && info.totalPage != 4){
		bar += '<li class=""><a href="javascript:void(0);">'+1+'</a></li>';
	}
	if(info.currPage-2 > 2 && info.currPage <= info.totalPage && info.totalPage > 5){
		bar +='<li class=""><span>...</span></li>';
	}
	var start = info.currPage -2;
	var end = info.currPage + 2;
	
	if((start > 1 && info.currPage < 4)||info.currPage == 1){
		end++;
	}
	if(info.currPage > info.totalPage-4 && info.currPage >= info.totalPage){
		start--;
	}
	for (;start <= end; start++) {
		if(start <= info.totalPage && start >= 1){
			if(start != info.currPage){
				bar += '<li class=""><a href="javascript:void(0);">'+start+'</a></li>';
			}else{
				bar += '<li class="active"><a href="javascript:void(0);">'+start+'</a></li>';
			}
		}
	}
	if(info.currPage + 2 < info.totalPage - 1 && info.currPage >= 1 && info.totalPage > 5){
		bar +='<li class=""><span>...</span></li>';
	}
	if(info.currPage != info.totalPage && info.currPage < info.totalPage -2  && info.totalPage != 4){
		bar += '<li class=""><a href="javascript:void(0);">'+info.totalPage+'</a></li>';
	}
		
	bar +=    '<li class="'+next_status+'">';
	bar +=    	'<a href="javascript:void(0);" oper="next">';
	bar +=      	'<span aria-hidden="true">&raquo;</span>';
	bar +=      '</a>';
	bar +=    '</li>';
	
	bar +=    '<li class="'+last_status+'">';
	bar +=    	'<a href="javascript:void(0);" oper="last">';
	bar +=      	'<span aria-hidden="true">&raquo;&raquo;</span>';
	bar +=      '</a>';
	bar +=    '</li>';
	
	this.nav.html(bar);
}

PagingBar.prototype.go = function(page, isRefresh){
	var page = page*1;
	var pageInfo = this.info;
	if(!isRefresh && page == pageInfo.currPage){
		return;
	}
	if(page <= 1){
		pageInfo.currPage = 1;
	}else if(page >= pageInfo.totalPage){
		pageInfo.currPage = pageInfo.totalPage;
	}else {
		pageInfo.currPage = page;
	}
	if($.isFunction(this.callback)) {
		this.callback(pageInfo);
	}
}

