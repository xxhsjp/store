var profileController = (function(){
	
	var currAjax = null;
	
	return {
		
		init: function(){
			var _this = this;
			
			gMainframe.addListener('viewchanged', function(vname){
				if(vname == 'profile'){
//					_this.showAccouontInfo();
				}
			});
		},
		
		showAccouontInfo: function(){
			var post = {"method": "profile"};
			this.request(post, function(data){
				
			});
		},
		
		request: function(post, callback){
			if(currAjax){
	    		currAjax.abort();
			}
	    	currAjax = $.ajax({
	    	   url: 'web/php/user/profile.php',
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
	
	};
	
})();

profileController.init();