$(function(){

	$.ajaxSetup({
		cache: false
	});
	
	$(document).ajaxError(function(event,xhr,options,exc){
		var flag = 'content="__web_login__"';
		if(xhr.responseText && xhr.responseText.indexOf(flag) > -1){
			window.location.href = window.location.href;
		}else if(xhr.status != 0){
			var info = "";
			switch (xhr.status) {
	            case 12002:
	            case 12029:
	            case 12030:
	            case 12031:
	            case 12152:
	            case 13030:
	            	info = Lang.exception_info_net;
	                break;
	            default:
	            	info = Lang.exception_info_serv;
	        }
//			alert("Error code: "+xhr.status+"\n"+info+"\n"+exc);
			gMainframe.showInfo("Error code: "+xhr.status+"\n"+info+"\n"+exc);
		}
	});
	
	window.onhashchange = function(){
		var viewName = window.location.hash.replace('#', '') ;
		if(viewName){
			gMainframe.changeView(viewName);
		}
	}
	
//	$(document).ajaxStart(function(event,xhr,options){
//		gMainframe.showInfo();
//	});
	
//	$(document).ajaxComplete(function(event,xhr,options){
//		gMainframe.hideInfo();
//	});
	
	window.gMainframe = new MainFrame();
	
	gMainframe.addListener('viewchanged', function(vname){
		$('.modal').modal('hide');
	});
	
	var viewName = window.location.hash.replace('#', '') || 'compose';
	gMainframe.changeView(viewName);
	
});