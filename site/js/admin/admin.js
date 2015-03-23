$(document).ready(function(){
	resizeDiv();
});

window.onresize = function(event) {
	resizeDiv();
}

function resizeDiv() 
{
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
	//$("#browser").treeview();

	/*$("dbname").select(function(){
		alert("hi");
	});*/
	$("#loadTables").click(function(){
		var db = $("#dbname").val();
		//alert(APP_BASE);
		$("#tablelist").html("<div class='col-md-10 text-left' id='dbtab' style='padding-left:0;'><img src='"+APP_BASE+"/site/img/loading.gif'/></div><div class='col-md-10 form-group' style='padding-left:0;margin-top:20px;' ><input type='button' class='btn btn-primary generator' value='Generate'><input type='button' class='btn btn-primary saveentity hide' value='Save Entity'></div>");
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
					$('.saveentity').removeClass('hide');
					$('.generator').addClass('hide');
				}});


			});
			$('.saveentity').click(function()
			{
				var code = $("#classes").text();
				$.ajax({url:APP_BASE+"/admin/entity/saveEntity",method:"post",data:{dbName:db,code:code},success:function(data){
					
					if(data>0)
					{
						showStatus("Entity file "+db+".php hasbeen created/updated successfully.","alert-info");
						
						hideStatus(false,{'module':'admin','controller':'entity'});
					}
					else
					{
						bootbox.alert("Some error occured.try to copy and paste code manually.");
					}

					
				}});
			});

	}});
	


});

	

});

function showStatus(message,cname)
{
	$("#statusBar").remove();
	var html="<div id='statusBar' style='position:fixed;bottom:15px;right:15px;min-width:300px;' class='alert "+cname+"'><span></span>"+message+"</div>";
	$('body').append(html);
	$("#statusBar").show();
	
}
function hideStatus(rel,obj)
{
	$("#statusBar").delay(1000).fadeOut('slow',function()
	{
		$("#statusBar").children('span').addClass('done');	
		if(rel)
		{
			if(obj!=null)
			{
				var recall="";
				if(obj['module'])
				{
					recall+="module="+obj['module'];
				}
				if(obj['controller'])
				{
					recall+="&controller="+obj['controller'];
				}
				recall = $.base64.encode(recall);
				window.location = APP_BASE+"/admin/home/modulemanager"+'?'+recall;
			}
			else
			window.location = APP_BASE+"/admin/home/modulemanager";
		}
		else
		{
			if(obj!=null)
			{
				var recall="";
				if(obj['module'])
				{
					recall+="/"+obj['module'];
				}
				if(obj['controller'])
				{
					recall+="/"+obj['controller'];
				}
				//	recall = $.base64.encode(recall);
				window.location = APP_BASE+recall;
			}
		}
	});
	
}
function getURLParameter(name) {
	var loc = location.search;
	loc = loc.split('');
	loc.shift();
	loc = loc.join('');
	loc ="?"+ $.base64.decode(loc);
	return decodeURIComponent((new RegExp('[?|&]' + name + '=' + '([^&;]+?)(&|#|;|$)').exec(loc)||[,""])[1].replace(/\+/g, '%20'))||null;
}


;(function($) {

    var b64 = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/",
        a256 = '',
        r64 = [256],
        r256 = [256],
        i = 0;

    var UTF8 = {

        
        encode: function(strUni) {
            // use regular expressions & String.replace callback function for better efficiency
            // than procedural approaches
            var strUtf = strUni.replace(/[\u0080-\u07ff]/g, // U+0080 - U+07FF => 2 bytes 110yyyyy, 10zzzzzz
            function(c) {
                var cc = c.charCodeAt(0);
                return String.fromCharCode(0xc0 | cc >> 6, 0x80 | cc & 0x3f);
            })
            .replace(/[\u0800-\uffff]/g, // U+0800 - U+FFFF => 3 bytes 1110xxxx, 10yyyyyy, 10zzzzzz
            function(c) {
                var cc = c.charCodeAt(0);
                return String.fromCharCode(0xe0 | cc >> 12, 0x80 | cc >> 6 & 0x3F, 0x80 | cc & 0x3f);
            });
            return strUtf;
        },

        
        decode: function(strUtf) {
            // note: decode 3-byte chars first as decoded 2-byte strings could appear to be 3-byte char!
            var strUni = strUtf.replace(/[\u00e0-\u00ef][\u0080-\u00bf][\u0080-\u00bf]/g, // 3-byte chars
            function(c) { // (note parentheses for precence)
                var cc = ((c.charCodeAt(0) & 0x0f) << 12) | ((c.charCodeAt(1) & 0x3f) << 6) | (c.charCodeAt(2) & 0x3f);
                return String.fromCharCode(cc);
            })
            .replace(/[\u00c0-\u00df][\u0080-\u00bf]/g, // 2-byte chars
            function(c) { // (note parentheses for precence)
                var cc = (c.charCodeAt(0) & 0x1f) << 6 | c.charCodeAt(1) & 0x3f;
                return String.fromCharCode(cc);
            });
            return strUni;
        }
    };

    while(i < 256) {
        var c = String.fromCharCode(i);
        a256 += c;
        r256[i] = i;
        r64[i] = b64.indexOf(c);
        ++i;
    }

    function code(s, discard, alpha, beta, w1, w2) {
        s = String(s);
        var buffer = 0,
            i = 0,
            length = s.length,
            result = '',
            bitsInBuffer = 0;

        while(i < length) {
            var c = s.charCodeAt(i);
            c = c < 256 ? alpha[c] : -1;

            buffer = (buffer << w1) + c;
            bitsInBuffer += w1;

            while(bitsInBuffer >= w2) {
                bitsInBuffer -= w2;
                var tmp = buffer >> bitsInBuffer;
                result += beta.charAt(tmp);
                buffer ^= tmp << bitsInBuffer;
            }
            ++i;
        }
        if(!discard && bitsInBuffer > 0) result += beta.charAt(buffer << (w2 - bitsInBuffer));
        return result;
    }

    var Plugin = $.base64 = function(dir, input, encode) {
            return input ? Plugin[dir](input, encode) : dir ? null : this;
        };

    Plugin.btoa = Plugin.encode = function(plain, utf8encode) {
        plain = Plugin.raw === false || Plugin.utf8encode || utf8encode ? UTF8.encode(plain) : plain;
        plain = code(plain, false, r256, b64, 8, 6);
        return plain + '===='.slice((plain.length % 4) || 4);
    };

    Plugin.atob = Plugin.decode = function(coded, utf8decode) {
        coded = String(coded).split('=');
        var i = coded.length;
        do {--i;
            coded[i] = code(coded[i], true, r64, a256, 6, 8);
        } while (i > 0);
        coded = coded.join('');
        return Plugin.raw === false || Plugin.utf8decode || utf8decode ? UTF8.decode(coded) : coded;
    };
}(jQuery));


 $(document).ready(function()
    {
        $("body").on('click','.file',function(){

          var name = $(this).text().replace('.','_');
          var path = $(this).attr('file');
          var ext = $(this).attr('ext');
          addTab(name,ext,path);
        });
          $('body').addClass('skin-black');
         $("#updateClass").click(function(){

         	var class_desc = $(this).prev().val();
         	var class_id = $(this).attr('classid');
         	$.ajax({
         		url:APP_BASE+'/admin/home/updatedocs',
         		method:'post',
         		data:{class_id:class_id,class_desc:class_desc,type:'class_desc'},
         		success:function(){
         			console.log('done');
         		}
         	});

         });
         $(".updateMethod").click(function(){
         	var method_desc = $(this).prev().prev().val();
          //  conseole.log($(this).prev().prev());
         var method_id = $(this).prev().prev().attr('id').replace('method_','');
            var method_status = ($("#status_"+method_id).parent().hasClass("checked")==true)?1:0;
         	$.ajax({
         		url:APP_BASE+'/admin/home/updatedocs',
         		method:'post',
         		data:{method_desc:method_desc,type:'method_desc',method_id:method_id,method_status:method_status},
         		success:function(){
         			console.log('done');
         		}
         	});
         });
         $("#saveAll").click(function(){
         	var methods =[];
         	$(".methods").each(function(index,obj){
         		console.log(obj)
         		var method_desc = $(obj).val();
         		var method_id = $(obj).attr('id').replace('method_','');
                var method_status =  ($("#status_"+method_id).parent().hasClass("checked")==true)?1:0;
         		var method = {'method_desc':method_desc,'method_id':method_id,'method_status':method_status};
         		methods.push(method);
         	});
         	$.ajax({
         		url:APP_BASE+'/admin/home/updatedocs',
         		method:'post',
         		data:{methods:methods,type:'methods'},
         		success:function(){
         			console.log('done');
         		}
         	});

         });
         $(".classes_drop").change(function(){

         	var class_name = $(this).val();
         	if(class_name.length>0)
         		location.href = APP_BASE+'/admin/home/docs/classname/'+class_name;
         	else
         		location.href = APP_BASE+'/admin/home/docs/';
         });
    });
