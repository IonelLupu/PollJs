<?php 

ini_set("sessio.hash_bits_per_character", 12);
ini_set("soap.wsdl_cache_limit", 12);
ini_set('max_execution_time', 120);

register_shutdown_function('session_write_close');

class Poll{

	public static $data,$D; 
	public static $sql_table="_poll"; // name of the created table
	private static $msg_life=5; 	// number of seconds that a message can be alive  
	private static $seconds=20; 	// number of seconds being pendding  
	private static $fps= 120;  		// number of checks per second


	public static function init($database){
		$sql_code="
		CREATE TABLE IF NOT EXISTS `".self::$sql_table."` (
		  `id` bigint(25) NOT NULL AUTO_INCREMENT,
		  `event` varchar(50),
		  `data` varchar(255),
		  `time` int(16) DEFAULT NULL,
		  PRIMARY KEY (`id`)
		) ENGINE=MEMEORY DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ";
		$sql_clear="DELETE FROM `".self::$sql_table."` WHERE  time<".(time()-self::$msg_life)." ";
		$sql_init="SELECT SQL_CACHE id from `".self::$sql_table."` order by id desc limit 1 ";

		$db= new PDO('mysql:host=' .$database['host']. ';dbname=' .$database['dbname'],$database['user'],$database['user_pw'] );
		$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING); 
		$db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
		
		self::$D=$db;

		//session_start();
		//session_destroy();

		//create the table
		$q=$db->prepare($sql_code);
		$q->execute();
		//clear the table
		$q=$db->prepare($sql_clear);
		$q->execute();
		//get the last id
		$q=$db->query($sql_init);
		$init=$q->fetch();

		if($_GET && isset($_GET['e']))
			if($_GET['e']=='init'){
				if(!empty($init))
					self::send($init);
				else
					self::send(['id'=>1]);
			}
			else
				self::pending($_GET['e'],$_GET['i']);
	}

	public static function on($method,$callback){
		self::$data=$_REQUEST;
		if(!isset(self::$data['event']) && !isset(self::$data['data'])) return;
		if(self::$data['event']==$method){
			$callback(self::$data['data']);
		}
			
	}
	public static function emit($event,$data){
		self::addEvent($event,$data);
	}
	public static function pending($event,$id){
		$nr=0;
		$_getData=[
			"table"	=> self::$sql_table,
			"D"		=> self::$D,
			"seconds"=> self::$seconds,
			"fps"	=> self::$fps,
			"id"	=>$id
		];
		function _get($event,$_getData){
			global $nr;
			$table 	=$_getData['table'];
			$D 		=$_getData['D'];
			$seconds=$_getData['seconds'];
			$fps	=$_getData['fps'];
			$id		=$_getData['id'];
			usleep(1000000/$fps);

			$q=$D->query("SELECT SQL_CACHE  * from ".$table." where event='".$event."' and id>".$id."  order by time desc limit 1");
			$data=$q->fetch();
			
			if(!empty($data)){
				$id=$data['id'];
				$event=$data['event'];
				$data=json_decode($data['data']);

				$returned=array();

				$returned['id']=$id;
				$returned['event']=$event;
				$returned['data']=$data;

				echo json_encode($returned);
				return;
			}

			$nr++;
			if($nr<$seconds*$fps)
				_get($event,$_getData);
		}
		_get($event,$_getData);
	}

	public static function get($val=0){
		if($val)
			return self::$data['data'][$val];
		else
			return self::$data['data'];
	}

	public static function send($data){
		echo json_encode($data);
	}

	public static function addEvent($event,$data){
		$data=json_encode($data);
		$q=self::$D->prepare("INSERT INTO `".self::$sql_table."` values('',:event,:data,:time) ");
		$q->execute([
				":event"=>$event,
				":data"=>$data,
				":time"=>time()
			]);
	}


}

?>
