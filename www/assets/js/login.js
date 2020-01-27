jQuery(document).ready(function() {
	jQuery('#changePwd').click(function(){
		xajax_changerPwd(document.getElementById('rappel_pwd').value);
	});
});