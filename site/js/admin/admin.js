$(document).ready(function(){
resizeDiv();
});

window.onresize = function(event) {
resizeDiv();
}

function resizeDiv() {
var vph = $(window).height();
var vpw = $(window).width();
if(vpw < 768)
{
	vph = vph/2;
	$(".file_list").css({'position':'relative'});
	
	$(".file_list").css({'height': vph + 'px'});
	$("#sourceeditor").css({'height': vph + 'px'});
}
else
{
vph = vph - 50;
$(".file_list").css({'height': vph + 'px'});
$(".file_list").css({'position':'fixed'});
	$("#sourceeditor").css({'height': vph + 'px'});

}
}
$(document).ready(function(){
	$("#browser").treeview();

	/*$("dbname").select(function(){
		alert("hi");
	});*/
	$("#loadTables").click(function(){
		var db = $("#dbname").val();
		//alert(APP_BASE);
		$("#tablelist").html("<div class='col-md-10 text-left' id='dbtab' style='padding-left:0;'><img src='"+APP_BASE+"/site/img/loading.gif'/></div><div class='col-md-10 form-group' style='padding-left:0;margin-top:20px;' ><input type='button' class='btn btn-primary generator' value='Generate'></div>");
		$.ajax({url:APP_BASE+"/admin/entity/getTables",data:{dbName:db},success:function(data){
			var html = "<label>Chose Tables</label><select multiple class='form-control tablelist'>";
			var tables = $.parseJSON(data);
			for (var i = 0; i < tables.length; i++) {
				html += "<option value='"+tables[i]+"'>"+tables[i]+"</option>";
			};
			html +="</select>";
			html +="<div class='col-md-12' style='display:none;padding:0;margin-top:10px;' ><label> Entity Class Code</label><div style='overflow:auto;max-height:300px;'><pre ><code class='language-php' id='classes'></code></pre></div></div>";
			$("#dbtab").html(html);
			$(".generator").on("click",function(e){

				var tables = $('.tablelist').val();
				//alert(tables);
				if(!tables)
				{
					bootbox.alert("Please select atleast one table.");
					return;
				}
				$.ajax({url:APP_BASE+"/admin/entity/generateEntity",method:"post",data:{dbName:db,tables:tables},success:function(data){
					
					//var html = "";
					$("#classes").html(data);
					$("#classes").parent().parent().parent().slideDown('slow');
				}});


			});

	}});
	


});



});

$(document).ready(function()
{



});