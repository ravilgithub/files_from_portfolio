// Отправка запросов в PHP для ravil.zzz.com.ua
Function.prototype.ajaxQuery = function(yourWish, url, type, queryObj){
	$.ajax({
		'url': url,
		'type': type,
		'data': queryObj,
		'beforeSend': function(){},
		'success': function(data){
			var result = $.parseJSON(data);
			switch(yourWish){
				case 'captcha': createForm.setCaptcha(result); break;
				case 'formFieldsCheck': 
					createForm.uncorrectFieldsLight(result);
					if(result['form'] == 'comment_form'){
						createBlogObj.createBlog({'m':'carInDelails', 'p':result['blog_id'], 'authorized':result['nik_name']});
					}
					if((result['form'] == 'contact_form') && (result['status'] == 'ok')){
						window.location.href = result['fileName'];
					}
				break;
				case 'deleteCaptcha': createForm.timer = 0; break;
				case 'createBlog': 
					createBlogObj.createButtons(result[0][0].count);
					createBlogObj.createTable(result[1]);
				break;
				case 'carInDelails': createBlogObj.createTable(result); break;
			}
		},
		'error': function(){
			alert('Не удачный запрос');
		},
		'complete': function(){},
		'scriptCharset': 'utf-8'
	});
}