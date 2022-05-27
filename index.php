<?php
 
 echo "Hello world";
 $project_dev_name = "/ExpressPhp";
 $mode = "dev";

 require_once("php_modules/ExpressPhp/Main.php");

  $app = new \ExpressPhp\Main("/pages", "some app");

  $app->get("/", "Passport,controllers/getusers");

  $app->exe();


  echo json_encode($app);

 

?>