var loginCtrler = (function(){
	var g_cookie_prefix = Lang.g_cookie_prefix;
	var cookie_lang = '__'+g_cookie_prefix+'_LANG__';
	var cookie_skin = '__'+g_cookie_prefix+'_SKIN__';
	
	var trim = String.prototype.trim ? function (s){
		return String.prototype.trim.call(s);
	} : function(s){
		var re = /^\s* | \s*$/g;
		return s.replace(re, '');
	}

	function showInfo(info){
		var info_c = document.getElementById("msg_container");
		if(info_c){
			info_c.innerHTML = info;
		}
		info_c.style.display = "block";
	}
	
	return {
		
		lang: function(l){
			var days = 30;
			var exp = new Date();
			exp.setTime(exp.getTime() + days*24*60*60*1000);
			document.cookie = cookie_lang + "="+ l.toLowerCase() + ";expires=" + exp.toGMTString()+";path=/";;
			window.location.href = window.location.href;
		},
		
		skin: function(skin){
			
		},
		
		validateRegForm: function(form){
			var act = form.account;
			var email = form.email;
			var pwd1 = document.getElementById('exampleInputPassword1');
			var pwd2 = document.getElementById('exampleInputPassword2');
			if(act.value == ''){
				act.focus();
				alert('账号不能为空');
				return false;
			}else if(email.value == ''){
				email.focus();
				alert('邮件地址不能为空');
				return false;
			}else if(pwd1.value != pwd2.value){
				pwd.focus();
				alert('密码为空或密码不一致');
				return false;
			}
			document.getElementById('regPassword').value = hex_md5(pwd1.value);
			return true;
		},
		
		validateForm: function(form){
			var act = form.account;
			var pwd = document.getElementById('inputPassword');
			act.value = trim(act.value);
			pwd.value = trim(pwd.value);
			if(act.value == ''){
				act.focus();
				showInfo(Lang.page_info_1);
				return false;
			}else if(pwd.value == ''){
				pwd.focus();
				showInfo(Lang.page_info_2);
				return false;
			}
			document.getElementById('submitPassword').value = hex_md5(pwd.value);
			return true;
		}
	
	};
	
})();