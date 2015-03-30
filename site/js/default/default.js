$(function(){

	$("#custom_puny").change(function () {
		if($("#custom_puny").is(':checked'))
		{
			$("#customPunyText").removeAttr('disabled');
			$("#createPuny").attr('disabled','disabled');
			$(".is_available").html('');
		}
		else
		{
			$("#customPunyText").attr('disabled','disabled');
			$("#createPuny").removeAttr('disabled');
			$(".is_available").html('');
		}
	});
	

});