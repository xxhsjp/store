var util = (function(){
	return {

		browser: function(){ 
			var u = navigator.userAgent, app = navigator.appVersion; 
			return {//移动终端浏览器版本信息 
				trident: u.indexOf('Trident') > -1, //IE内核 
				presto: u.indexOf('Presto') > -1, //opera内核 
				webKit: u.indexOf('AppleWebKit') > -1, //苹果、谷歌内核 
				gecko: u.indexOf('Gecko') > -1 && u.indexOf('KHTML') == -1, //火狐内核 
				mobile: !!u.match(/AppleWebKit.*Mobile.*/)||!!u.match(/AppleWebKit/), //是否为移动终端 
				ios: !!u.match(/\(i[^;]+;( U;)? CPU.+Mac OS X/), //ios终端 
				android: u.indexOf('Android') > -1 || u.indexOf('Linux') > -1, //android终端或者uc浏览器 
				iPhone: u.indexOf('iPhone') > -1 || u.indexOf('Mac') > -1, //是否为iPhone或者QQ HD浏览器 
				iPad: u.indexOf('iPad') > -1, //是否iPad 
				webApp: u.indexOf('Safari') == -1 //是否web应该程序，没有头部与底部 
			}; 
		}(),
		
		trim: String.prototype.trim ? function (s){
			return String.prototype.trim.call(s);
		} : function(s){
			var re = /^\s* | \s*$/g;
			return s.replace(re, '');
		},
		
		htmlEscape: function(html){
			var escaped = "";
			if(html != null){
				escaped = html.replace(/&/g, '&amp;').replace(/"/g, '&quot;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/'/g, '&#39;');
			}
			return escaped;
		},
		
		getMailNickName: function(mailAddr){
			var re = /<.+@.+(?:\..+)+>/;
			mailAddr = mailAddr || '';
			return util.trim(mailAddr.replace(re, '').replace(/"/g, '')) || mailAddr.replace(/<|>/g, '');
		},
		
		getMailAccount: function(mailAddr){
			var re = /<(.+@.+(?:\..+)+)>/;
			mailAddr = mailAddr || '';
			if(re.test(mailAddr)){
				 mailAddr = RegExp.$1;
			}
			
			return $.trim(mailAddr);
		},
		
		isMailAddr: function(addr, strict){
			var re = /^[a-z0-9]([a-z0-9]*[-_]?[a-z0-9]+)*@([a-z0-9]*[-_]?[a-z0-9]+)+[\.][a-z]{2,3}([\.][a-z]{2})?$/i;
			if(!strict && addr.indexOf('<') != -1 && addr.indexOf('>') != -1){
				re = /^(?:('|")[^;,"]*\1)?\s*<[[a-z]([a-z0-9]*[-_]?[a-z0-9]+)*@([a-z0-9]*[-_]?[a-z0-9]+)+[\.][a-z]{2,3}([\.][a-z]{2})?>$/i;
			}
			
			return re.test(addr);
		},
		
		getFileSize: function(sizeByte){
			if(sizeByte==null)return "";
			else if(sizeByte==0) return 0+"KB";
			else if(1073741824>sizeByte&&sizeByte>1048576) return this.money_format(parseFloat(sizeByte/1048576).toFixed(2),2)+"MB";
			else if(sizeByte>=1073741824) return this.money_format(parseFloat(sizeByte/1073741824).toFixed(2),2)+"GB";
			else return this.money_format(parseFloat(sizeByte/1024).toFixed(2),2)+"KB";
		},

		getMailDate: function(millisecond){
			var result = {type: '', val: ''};
			var today_mills = new Date().getTime(); 
			var minutes_1 = 1000*60;
			var minutes_5 = minutes_1*5;
			var hour_1 = 1000*60*60;
			var day_1 = hour_1*24;
			var week_1 = day_1*7;
			var month_1 = week_1*4;
			var year_1 = month_1*12;
			var mills = today_mills - millisecond;
			if(mills < hour_1){
				result.type = "mu";
				result.val = Math.ceil(mills/minutes_1);
			}else if(mills < day_1){
				result.type = "ho";
				result.val = Math.ceil(mills/hour_1);
			}else if(mills < week_1){
				result.type = "dy";
				result.val = Math.ceil(mills/day_1);
			}else if(mills < month_1){
				result.type = "wk";
				result.val = Math.ceil(mills/week_1);
			}else if(mills < year_1){
				var date_o = this.getDateObj(millisecond);
				result.type = "date";
				result.val = date_o.month+'/'+date_o.day;
			}else {
				var date_o = this.getDateObj(millisecond);
				result.type = 'fudate';
				result.val = date_o.year+'/'+date_o.month+'/'+date_o.day;
			}
			
			return result;
		},
		
		getDateObj: function(millisecond){
			if(millisecond){
				var date = new Date(millisecond);

				var year = date.getFullYear();
				var month = date.getMonth()+1; 
				var day = date.getDate(); 
				var hour = date.getHours();
				if(hour>=0 && hour<=9){
					hour="0"+hour;
				}
				var minutes = date.getMinutes(); 
				if(minutes>=0 && minutes<=9){
					minutes="0"+minutes;
				}
				var second = date.getSeconds();
				return {year:year,month:month,day:day,hour:hour,minutes:minutes,second:second};
			}else{
				return null;
			}
		},
		
		getDate: function(millisecond, isByDay){
			if(millisecond){
				var date = new Date(millisecond);

				var year = date.getFullYear();
				var month = date.getMonth()+1; 
				var day = date.getDate(); 
				var hour = date.getHours();
				if(hour>=0 && hour<=9){
					hour="0"+hour;
				}
				var minutes = date.getMinutes(); 
				if(minutes>=0 && minutes<=9){
					minutes="0"+minutes;
				}
				var second = date.getSeconds();
				if(second>=0 && second<=9){
					second="0"+second;
				}
				var dateStr = year+"/"+month+"/"+day;
				if(!isByDay){
					dateStr += " "+hour+":"+minutes+":"+second;
				}
				return dateStr;
			}else{
				return '';
			}
		},

		money_format: function(value,fixed,currency){
			var fixed = fixed || 0;
			var currency = currency || '';
			isNaN(parseFloat(value))? value=0 : value=parseFloat(value);
			v = value.toFixed(fixed).toString();
			var ps = v.split('.');
			var whole = ps[0];
			var sub = ps[1] ? '.' + ps[1] : '';
			var r = /(\d+)(\d{3})/;
			while (r.test(whole)) {
					whole = whole.replace(r, '$1' + ',' + '$2');
			}
			v = whole + sub;
			if (v.charAt(0) == '-') {
					return currency + '-' + v.substr(1);
			}
			return currency  +v;
		},

		getParamsFromSearch: function(search){
		    var re = /[\?|&]([^&]+)=([^&]+)/g;
		    var data = {};
		    search.replace(re, function(s, s1, s2){
		        data[s1] = s2;
		    })

		    return data;
		},

		getByteLength: function(str){
			var re = /[\u4e00-\u9fa5]/g;
			var newstr = str.replace(re,"**");
			return newstr.length;
		}
	};
})();