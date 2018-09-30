<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require '../vendor/autoload.php';

$app = new \Slim\App;
$app->get('[/{params:.*}]', function (Request $request, Response $response, array $args) {
    $path = explode("/",$args['params']);
    
    $dbh = new PDO("pgsql:dbname=ets_logistic;host=192.168.2.10", "postgres", "pwd"); 
    
     //tree($dbh,$path);
   
    //$response->getBody()->write("params,". implode(",",$params));
    
    $parent_item_id = null;
    
    
    while($current = current($path)){
		
		if($current == "") break;
				
			if($parent_item_id != NULL)
		 $sql = "SELECT item_id,item_name,code,code_type FROM main_menu where item_name='$current' and parent_item_id=".$parent_item_id;
		 else 
		 $sql = "SELECT item_id,item_name,code,code_type FROM main_menu where item_name='$current'";
		 
		 $parent_item_id = NULL;
		 
		 try {
			
    		foreach ($dbh->query($sql) as $row) {
        $parent_item_id =  $row['item_id'] ;
        $item_name = $row['item_name'] ;
        $code = $row['code'] ;
        $code_type = $row['code_type'] ;
    		}
    		
		}
		catch (PDOException $e) {
		  print $e->getMessage();
		  }
		 	 
		
		 if(!$parent_item_id) {  
		  return $response->withStatus(404);
		  echo "not found";  
		  } else {
		  	if(key($path) == count($path)-1)
		  	try {
			
			if($code_type == "php")
		  	eval($code);
		  	else {
				
				$response->getBody()->write($code);
				
				return $response->withHeader('Content-type', 'application/javascript; charset=UTF-8');
				 
			} 
		  	
		  	
    		
			}
		catch (PDOException $e) {
		  print $e->getMessage();
		}
		  }
		 
		 
		// echo $sql."\t".$item_name."\t".$parent_item_id."\n";
		
		
		
		next($path);
	}
    
    echo "resp 2";

    //return $response;
});
$app->run();







 function tree($dbh,$path,$parent_item_id = null){
	$curr_iten_name = current($path);
	if(!$curr_iten_name) return false;
		
		try {
			if($parent_item_id != null)
		 $sql = "SELECT item_id,item_name FROM main_menu where item_name='.$curr_iten_name.' and parent_item_id=".$parent_item_id;
		 else 
		 $sql = "SELECT item_id,item_name FROM main_menu where item_name='.$curr_iten_name.'";
    		
    		foreach ($dbh->query($sql) as $row) {
        $item_id =  $row['item_id'] ;
        $item_name = $row['item_name'] ;
    		}
    		tree($dbh,$path,$item_id);
		}
		catch (PDOException $e) {
		  print $e->getMessage();
		}
		if(next($path))
     
	    
	return $item_name;
	}

?>
