

(function (window){
	var settings={
		key 		: 13, //the ENTER key
		file		: "",
		file_ext 	: ".php"
	}

	var last_id=0,messages=[],xhr;

	var Poll=function(user_settings){
		/**
		 * Poll Init
		 */
		settings 	=$.extend(settings, user_settings);

		//Run the first poll function 
		// setTimeout(function(){
		// 	poll();
		// },1);
		
		/***************************************
		 * Poll Methods
		 **************************************/
		init=$.get(settings.file+settings.file_ext,{e:'init'},function(data){
			last_id=data['id'];
		},'json');
		
		this.emit=function(evt,data){
			$.post(settings.file+settings.file_ext,{event:evt,data:data},function(data){
			});
		}

		this.on=function(evt,func){
			$.when(init).done(function(init){
				get();
				function get(){
					$.get(settings.file+settings.file_ext,{e:evt,i:last_id},function(data){
						if(data){
							last_id=data['id'];
							func(data.data);
						}
						get();
					},'json');
				}

			});
		}

	}


	window.Poll=Poll;
})(window)
