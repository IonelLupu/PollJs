PollJs
======

Simple Long Polling script

Examples
--------------------------------------
**Example 1**: Just a very basic chat

**Example 2**: A simple character constrol.Here the character (the black box) can be controlled by every user from the server using the mouse click


Documentation (not availabe)
--------------------------------------

Instalation - Client
--------------------------------------
Add the `poll.client.js` to your html file
```html
    <script type="text/javascript" src="poll.client.js"></script>
```

Instalation - Server
--------------------------------------
Add the `poll.server.php` to your server file
```php
    include "poll.server.php";
```


Basic usage - Client
--------------------------------------
```html
<script type="text/javascript">
    // create a new Poll object
  	var poll=new Poll({
  		file:"poll"
  	});
  	// when a key is pressed 
  	$(document).keypress(function(e){
  	  // emit a message to the server with the name 'key'
  		poll.emit("key",{
  			msg:e.which
  		})
  	});
  	// wait for a message from the server with the name 'key'
  	poll.on("key",function(data){
  		 $("body").append("A user just pressed: "+data.msg+"<br />");
  	})
</script>
```

Basic usage - Server
--------------------------------------
Create a new `Poll` object in you
```php
    
    $database['host']		= 'host'; // use 127.0.0.1 if you are using your localhost
    $database['user']		= 'user';
    $database['user_pw']	= 'password';
    $database['dbname']		= 'database';
    
    include "poll.server.php";
    // init the Poll
    Poll::init($database);
    // wait for the event with the name 'key'
    Poll::on("key",function($data){
      // emit a message with the name 'key' 
    	Poll::emit("key",$data);
    });

```



