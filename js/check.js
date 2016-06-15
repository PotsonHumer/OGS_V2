var isset = function(val){
	if(typeof(val) == 'undefined' || val == '' || val == 'undefined'){
		return false;
	}

	return true;
}

var validateEmail = function(email){
    var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(email);
}

var goCheck = function(OBJ,CALLBACK){
	var totalField = OBJ.find('*[data-check=true]').length;
	var result = true;
	var resultTitle = '';

	if(totalField == 0){
		CALLBACK(true);
		return;
	}

	OBJ.find('*[data-check=true]').each(function(KEY){
		var title = $(this).attr('data-title');
		var msg = $(this).attr('data-msg');
		var name = $(this).attr('name');
		var value = $(this).val();
		var type = $(this).attr('type');

		switch(type){
			case "email":
				$rs = (validateEmail(value))?true:false;
			break;
			case "radio":
			case "checkbox":
				var lengthCK = OBJ.find('input[name='+ name +']:checked').length;
				$rs = (lengthCK > 0)?true:false;
			break;
			default:
				$rs = (isset(value))?true:false;
			break;
		}

		if(!$rs){
			CALLBACK(false,title,msg);
			return false;
		}

		if(totalField == (KEY + 1)){
			CALLBACK(true);
		}
	});
}

$(document).on('click','.check .checkBtn',function(E){
	E.preventDefault();

	var OBJ = $(this).parents('form.check');
	goCheck(OBJ,function(result,title,msg){
		if(result == false){
			if(isset(msg)){
				alert(msg);
			}else{
				alert('請確實填寫 "'+ title +'" 欄位');
			}
		}else{
			OBJ.submit();
		}
	});
});