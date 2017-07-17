jQuery(document).ready(function($){
	var iheight=$('.boxgrid').height();
	var iwidth=$('.boxgrid').width();
	var	stopx=iheight*0.75;
	
	$('.boxgrid.caption').hover(function(){
		$('.bottom-up-full', this).animate({top:'0px'},{queue:false,duration:400});
		$('.bottom-up-part', this).animate({top:'0px'},{queue:false,duration:400});
		$('.left-right', this).animate({left:'0px'},{queue:false,duration:400});
		$('.left-right-uncover', this).animate({left:iwidth+'px'},{queue:false,duration:400});
		$('.right-left', this).animate({left:'0px'},{queue:false,duration:400});
		$('.right-left-uncover', this).animate({left:-iwidth+'px'},{queue:false,duration:400}); 
		$('.fade-in', this).fadeIn(400); 
		$('.fade-out', this).fadeOut(400); 
		$('.top-down-full', this).animate({top:'0px'},{queue:false,duration:400});
		$('.top-down-part', this).animate({top:'0px'},{queue:false,duration:400});
	}, function() {
		$('.bottom-up-full', this).animate({top:iheight+'px'},{queue:false,duration:400});
		$('.bottom-up-part', this).animate({top:stopx+'px'},{queue:false,duration:400});
		$('.left-right', this).animate({left:-iwidth+'px'},{queue:false,duration:400});
		$('.left-right-uncover', this).animate({left:'0px'},{queue:false,duration:400});
		$('.right-left', this).animate({left:iwidth+'px'},{queue:false,duration:400});
		$('.right-left-uncover', this).animate({left:'0px'},{queue:false,duration:400}); 
		$('.fade-in', this).fadeOut(400); 
		$('.fade-out', this).fadeIn(400); 
		$('.top-down-full', this).animate({top:-iheight+'px'},{queue:false,duration:400});
		$('.top-down-part', this).animate({top:-stopx+'px'},{queue:false,duration:400});
	});
});	