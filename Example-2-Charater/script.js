
$(function(){

	var poll=new Poll({
		file:"poll"
	});
	var x,y;
	$(document).mousedown(function(e){
		x= e.pageX ;
		y= e.pageY;
		poll.emit("move",{
			x:x,
			y:y
		})
	});

	poll.on("move",function(data){
		 $("#ch1").stop().animate({
		 	top:data.y,
		 	left:data.x
		 },1000);
	})


})
