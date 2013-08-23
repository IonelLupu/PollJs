
$(function(){

	var poll=new Poll({
		file:"poll"
	});

	$("#input").keypress(function(e){
		if ( e.which != 13 ) return;
		poll.emit("hello",{
			msg:$(this).val()
		})
	});

	poll.on("hello",function(data){
		 $("#messages").prepend(data.msg+"<br />");
	})


})
