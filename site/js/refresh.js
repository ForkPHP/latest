
var refresh = function(){


	this.paths = {}; // array for url mapping
	this.elements = [];
	this.base= '';
	this.addPath = function(data){
	var object = this;
		try{

			if(!data.url || !data.modules){
				throw "Invalid path object data.";
			}
			this.paths[data.url] = data.modules;

		}catch(err){

			console.log("Refresh.js : \n"+err);
			return;

		}

	};
	this.init = function(path,base){
		//var pathname = window.location.pathname;
		this.base = base;
		//alert(path)
		this.resolve(path,true);
	}
	this.resolve = function(path,init){
		
	  var modules = this.paths['/'+path];
	 if(modules){ 
		      // body...
      var prev = window.location; // must be a stack
      var next = this.base+'/'+path;
      
    $.ajax(
        {
          url:this.base+'/'+path,
          method:'POST',
          data:{terminal:1,modules:modules},
          dataType:'json',
          beforeSend:function(){
            $('body').disablify();
          },
          success:function(data)
          {
            
          //$(target).html(data.html);
          
           document.title = data.title;
           $('body').enablify();
           if(!init)
           	window.history.pushState({"html":data.html,"pageTitle":data.title},"", next);
          }
        });
		  }else{
		  	$.ajax(
	        {
	          url:this.base+'/'+path,
	          method:'POST',
	          beforeSend:function(){
	            $('body').disablify();
	          },
	          success:function(data)
	          {
	            
	          //$(target).html(data.html);
	          
	           // /document.title = data.title;
	           $('body').html(data);
	           //if(!init)
	           	//window.history.pushState({"html":data.html,"pageTitle":data.title},"", next);
	          }
	        });
		  }
	}
	this.elements.push('.refresh');

	$(document).on('click',this.elements.join(','),{paths:this.paths},function (event) {
		console.log(this)
		var url = $(this).attr('url');
		var modules = event.data.paths[url];
		console.log(modules)

	});









return this;


};

$(document).on('click','.changepage',function () {




});



var forkApp = new refresh();
forkApp.addPath({
	
	url:"/Noreload/index/ajax",
	modules:[
				{
					target:"#content",
					keytoken:"clock"
				},
				{
					target:"#sidebar",
					keytoken:"fileslist"
				}
			]

	});
forkApp.addPath({
	
	url:"/Noreload/index/index",
	modules:[
				{
					target:"#content",
					keytoken:"clock"
				},
				{
					target:"#sidebar",
					keytoken:"fileslist"
				}
			]

	});
console.log(forkApp);




 $(window).bind('popstate', function() {
    var nprev= window.location;
    $.ajax(
      {
        url:prev,
        method:'POST',
        data:{terminal:1},
        dataType:'json',
        success: function(data){
          document.title = data.title;
          $('#content').html(data.html);
          prev = nprev;
          }
    });
 
  });
    $(window).bind('pushstate', function() {


    });
  $.fn.disablify = function() {
        $(this).css('position', 'relative');

        $(this).append("<div class='disablified' style='position:absolute;background-size:16px;;background:white url("+forkApp.base+"/site/img/preloader.gif) center no-repeat !important;' ></div>");
        if(!$(this).is('body'))
        	$(this).children('.disablified').css('height', $(this).outerHeight() - 2);
        else
        	$(this).children('.disablified').css('height', $(window).height());
        $(this).children('.disablified').css('width', $(this).outerWidth() - 2);
            $(this).children('.disablified').css('top', 0);
            $(this).children('.disablified').css('left', 0);
        
        $(this).children('.disablified').css('opacity', .7);
    }
    $.fn.enablify = function() {
        $(this).children('.disablified').remove();
    }