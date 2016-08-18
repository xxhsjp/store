var user_setting = (function(){

	var $form =  $("#password_form");
	var $error = $("#pw_error");
	
	function _validate_pass(v) {
		var reg = /^\w{6,20}$/;
		var regNum = /\d/;
		var regChar = /[a-zA-Z]/;
		return reg.test(v)&&regNum.test(v)&&regChar.test(v);
		
	}
	
	function _validate() {
		var r = true;
		var po = $("#pw_old");
		var pn = $("#pw_new");
		var pc = $("#pw_conf");
		if(po.val().length==0) {
			r = false;
			po.focus();
		} else if(!_validate_pass(pn.val())) {
			r = false;
			pn.focus();
		} else if(!_validate_pass(pc.val())) {
			r = false;
			pc.focus();
		} else if(pn.val()!=pc.val()) {
			r = false;
			pc.focus();
			_showError(_getErrMsg(400));
		} else if(pn.val()==po.val()) {
			r = false;
			pn.val();
			_showError(_getErrMsg(401))
		}
		return r;
	};

	var _opts = {
		type:"POST",
		dataType: "json",
		url: "web/php/user/setting.php",
		beforeSubmit: function() {
		},

		beforeSend: function() {
		}
	};
	
	function _doAjax(data, callback) {
		var opt = _opts;
		opt.data = data || {method: "query"};
		opt.success = function (data, textStatus, qXHR) {
			if(callback!=null&&($.isFunction(callback))) {
				callback(data, textStatus, qXHR);
			}
		}
		$.ajax(opt);
	};
	
	function _getErrMsg(code) {
		return new Function("return Lang.user_pass_error_" + code)();
	};
	
	function _showError(msg) {
		if(null!=msg||undefined!=msg) {
			$error.html("<strong>" + msg + "</strong>").fadeIn(1000, function() {
				window.setTimeout(function() {
					$error.fadeOut(1000);
				}, 2000);
			});
		}
	};
	return {
		init: function() {
			$("#password_sub").click(function () {
				if(!_validate()){
					return;
				}
				var po = hex_md5($("#pw_old").val()), pn = hex_md5($("#pw_new").val())
				$.post(_opts.url, {method: "pwd", pw_old: po, pw_new: pn}, function(data) {
					data =  JSON.parse(data);
					code = data.result;
					if(1==code) {
						$("#password_modal").modal("hide");
						$form.resetForm();
					} else{
						_showError(_getErrMsg(code));
					}
				});
				
			});			
//			去前后空格
			$form.find("input[type=password]").blur(function() {
				var val = $.trim($(this).val());
				$(this).val(val);
			});
			
		}
	};
	
})();
$(function() {
	user_setting.init();
})
