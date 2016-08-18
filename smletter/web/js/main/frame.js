//MainFrame
function MainFrame(){
	this.currAjax = null;
	this.currAjax2 = null;
	this.moduleLoaded = {};
	this.viewName = null;
    this.events = {};
    this.init();
}

MainFrame.prototype = {
    constructor: MainFrame,
    
    init: function(){
    	var _this = this;
        this.addEvent('viewloaded', 'viewchanged', 'workarealoaded', 'workareachanged', 'framesizechanged');
        this.initEvent();
    },
    
    initEvent: function(){
    	var _this = this;
		$('#sidebar').delegate('A','click', function (e) {
			var viewName = $(this).attr('vname');
			if(viewName){
				$('#sidebar li').removeClass('active');
				$(this).parent().addClass('active');
				_this.loadView(viewName);
			}else {
				var lnav =  $(this).attr('lnav');
				if(lnav){
					$(this).find('i.fa').toggleClass('fa-angle-down').toggleClass('fa-angle-up');
					$('#'+lnav).toggleClass('hidden');
				}
			}
		});
		
		$('#main_dropmenu').on('show.bs.dropdown',function () {
			return;
			var post = {"method": "profile"};
			_this.request(post, function(data){
				var info = data.info;
				$('#main_expired').html(util.getDate(info['expired']*1000, true));
				var order = data.order;
				if(order){
					$('#main_free_count').html(1*order['total_count']-1*order['used_count']);
				}
			});
		});
		
		$('#logo').click(function(){
			if(_this.isXSSrceen()){
				$('#sidebar').toggleClass('hidden-xs');
			}
		});
		
		$(window).resize(function(){
			_this.layoutContent();
			_this.fireEvent('framesizechanged');
		});
		this.layoutContent();
    },
    
    changeView: function(viewName){
		$('#sidebar li').removeClass('active');
		$('#sidebar a[vname='+viewName+']').parent().addClass('active').parent().removeClass('hidden');
		this.loadView(viewName);
    },
    
    loadView: function(viewName){
    	var _this = this;
    	if(viewName == _this.viewName){
			return;
		}
		_this.viewName = viewName || null;
		if(_this.moduleLoaded[_this.viewName]){
			_this.renderView(_this.viewName);
			_this.fireEvent('viewchanged', viewName);
			return;
		}

		_this.load(_this.viewName, function(html ,ts){
			_this.moduleLoaded[_this.viewName] = true;
			_this.viewName = viewName || null;
			_this.renderView(_this.viewName, html);
			_this.fireEvent('viewloaded', viewName);
			_this.fireEvent('viewchanged', viewName);
		});
    },
    
    preLoadView: function(viewNames){
    	
    },
    
    isXSSrceen: function(){
    	return $('#media_test').is(":hidden");
    },
    
    renderView: function(viewName, html, lang){
    	$('.view_container').hide();
    	var vname = 'view_'+viewName;
    	html && (html = this.processTpl(html, lang));
    	if($('#'+vname).length > 0){
    		html && $('#'+vname).html(html);
    		$('#'+vname).show();
    	}else {
    		$('#content_area').append('<div id="'+vname+'" class="view_container">'+html+'</div>');
    	}
    },
    
    processTpl: function(tpl, lang){
    	lang = lang || Lang;
    	var re = /{\$(.+?)}/g;
    	tpl = tpl.replace(re, function(s, s1){
    		return lang[s1];
    	});
    	
    	return tpl;
    },
    
    load: function(viewName, callback){
    	if(this.currAjax){
    		this.currAjax.abort();
		}
    	this.currAjax = $.ajax({
    	   url: 'web/php/common/load.php',
		   type: "POST",
		   dataType: 'html',
		   data: "tpl_name="+viewName,
		   success: function(data, textStatus, qXHR){
			   if($.isFunction(callback)){
				   callback(data, textStatus);
			   }
		   }
		});
    },
    
    request: function(post, callback){
		if(this.currAjax2){
			this.currAjax2.abort();
		}
		this.currAjax2 = $.ajax({
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
	},
    
    layoutContent: function(){
		
    },
    
    confirm: function(title, content, callback){
    	$('#modal_confirm .modal-title').html(title || '');
    	$('#modal_confirm .modal-body').html(content || '');
    	var tempFn = function(){
    		if($.isFunction(callback)){
    			callback();
    		}
    		$('#modal_confirm').modal('hide');
    	};
    	$('#modal_confirm .btn-primary').off("click").on("click", tempFn);
    	
    	$('#modal_confirm').modal('show');
    },
    
    showInfo: function(text, opts, caller){
    	if(this.caller && this.caller != caller){
    		return;
    	}
    	this.caller = caller;
    	var _this = this;
    	if(!text){
    		$('#modal_info_content').hide();
    	}else{
    		$('#modal_info_content').show();
    		$('#modal_info_content').html(text);
    	}
    	$('#modal_info').modal('show');
    	if(opts){
    		window.setTimeout(function(){
    			_this.hideInfo();
    			if($.isFunction(opts.fn)){
    				opts.fn();
    			}
    		}, opts.time || 0);
    	}
    },
    
    changeInfo: function(text, opts, caller){
    	$('#modal_info_content').html(text);
    	if(opts){
    		this.hideInfo(opts, caller);
    	}
    },
    
    hideInfo: function(opts, caller){
    	if(this.caller && this.caller != caller){
    		return;
    	}
    	var _this = this;
    	if(opts){
    		window.setTimeout(function(){
    			_this.hideInfo(null, caller);
    			if($.isFunction(opts.fn)){
    				opts.fn();
    			}
    		}, opts.time || 0);
    	}else {
    		this.caller = null;
	    	$('#modal_info_content').html();
	    	$('#modal_info').modal('hide');
    	}
    },
    
    getLoginAccount: function(){
    	return __account__;
    },
    
    addEvent: function(eNames){
        var args = Array.prototype.slice.call(arguments, 0);
        for(var i=0;i<args.length;i++){
            this.events[args[i]] = new Event(this, args[i]);
        }
    },

	removeEvent: function(eNames){
        var args = Array.prototype.slice.call(arguments, 0);
        for(var i=0;i<args.length;i++){
            this.events[args[i]] = null;
			delete this.events[args[i]];
        }
    },

    addListener: function(eName, fn, scope){
        this.events[eName].addListener(fn, scope);
    },

	removeListener: function(eName, fn){
        this.events[eName].removeListener(fn, scope);
    },

    fireEvent: function(eName, params){
        var args = Array.prototype.slice.call(arguments, 0);
        var en = args.shift();
        var e = this.events[en];
        e.fireEvent.apply(e, args);
    } 
}

//Event
function Event(obj, name){
    this.name = name;
    this.obj = obj;
    this.listeners = [];
}

Event.prototype = {
    constructor: Event,

    addListener: function(fn, scope){
        if(Object.prototype.toString.call(fn) == '[object Function]'){
            scope = scope || this.obj;
            this.listeners.push({
                fn: fn,
                scope: scope
            });
        }
    },

	removeListener: function(fn){
		for(var i=0;i<this.listeners.length;){
			if(this.listeners[i].fn == fn){
				this.listeners.splice(i, 1);
			}else {
				i++;
			}
		}
    },

    fireEvent: function(){
        var args = Array.prototype.slice.call(arguments, 0);
		for(var i=0,l=this.listeners.length;i<l;i++){
			var ln = this.listeners[i];
			ln.fn.apply(ln.scope || window, args);
		}
    }
}