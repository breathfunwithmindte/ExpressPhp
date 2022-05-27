<?php
  
  namespace ExpressPhp {
    require_once("php_modules/ExpressPhp/System.php");

    class Main extends System {
      
      public $fullpath = "/";
      public $fullpath_array = array();
      private $schema = array();
      
      function __construct($controller_folder, $testing)
      {
        $this->testing = $testing;
        $this->fullpath = $this->current_url();
        $this->http_method = $_SERVER['REQUEST_METHOD'];
        $this->fullpath_array = array_slice(explode("/", $this->fullpath), 1);
        $this->controller_folder = $controller_folder;
      }
  
      function exe ()
      {
        $ll = json_encode($this->isMatch($this->schema, $this->fullpath_array));
        $result = $this->isMatch($this->schema, $this->fullpath_array);
        if(isset($result["controller"])) {
          $controllers = explode(",", $result["controller"]);
          $request = array(
            "fullpath" => $this->fullpath,
            "schema" => $this->schema,
            "params" => $result["params"],
            "method" => $this->http_method,
            "headers" => getallheaders(),
            "cookies" => $_COOKIE,
            "authorization" => isset(getallheaders()["authorization"]) ? explode(" ", getallheaders()["authorization"])[1] : null,
            "authetication" => isset($_COOKIE["authetication"]) ? $_COOKIE["authetication"] : null
          );
          $next = true;
          $index = 0;
          while ($index <= count($controllers) - 1) {
            if($next === false) break;
            require("./" . $this->controller_folder . "/" . $controllers[$index] . ".php");
            $index ++;
          }
        }else {
          echo "<div style='padding: 2rem'><h1>404 page not found</h1></div>";
        }
      
      }
  
      function __toString()
      {
        $schema = json_encode($this->schema);
        return "
          <ul style='list-style-type: circle; padding: 1rem'>
          <li> <em> <b>testing</b> {$this->testing} </em> </li>
            <li> <em> <b>fullpath</b> {$this->fullpath} </em> </li>
            <li> <em> <b>controller_folder</b> {$this->controller_folder} </em> </li>
            <li> <em> <b>schema</b> {$schema} </em> </li>
          </ul>
        ";
      }
  
      private function isMatch($schema, $currentpath)
      {
        $matched_index = 0;
        $matched_item = null;
        $matched_controller = null;
        $matched_params = array();
        for ($i=0; $i < count($schema) ; $i++) { 
          // echo $i;
          $match = true;
          if(count($currentpath) > count($schema[$i]["params"])) continue;
          if($this->http_method !== $schema[$i]["method"]) continue;
          for ($j=0; $j < count($schema[$i]["params"]) ; $j++) { 
            $curr = $schema[$i]["params"][$j];
            if(str_starts_with($curr, ":"))
            {
              if(isset($currentpath[$j])) {
                $matched_params[substr($curr, 1)] = $currentpath[$j];
              }else{
                $match = false;
              }
              continue;
            }else{
              if(!isset($currentpath[$j])) {
                $match = false;
              }else {
                if($curr != $currentpath[$j]) {
                  $match = false;
                }
              }
            }
          }
          if($match === true){
            $matched_index = $i;
            $matched_item = $schema[$i];
            $matched_controller = $schema[$i]["controller"];
            break;
          };
        }
  
        return array(
          "index" => $matched_index, "item" => $matched_item, "controller" => $matched_controller, "params" => $matched_params
        );
      }
  
      function json ($array)
      {
        header('Content-Type: application/json; charset=utf-8');
        $json_result = json_encode($array);
        echo $json_result;
      }
      
      function get ($path, $controllername)
      {
        array_push($this->schema, array(
          "params" => array_slice(explode("/", $path), 1),
          "controller" => $controllername,
          "method" => "GET"
        ));
      }
      function post ($path, $controllername)
      {
        array_push($this->schema, array(
          "params" => array_slice(explode("/", $path), 1),
          "controller" => $controllername,
          "method" => "POST"
        ));
      }
      function put ($path, $controllername)
      {
        array_push($this->schema, array(
          "params" => array_slice(explode("/", $path), 1),
          "controller" => $controllername,
          "method" => "PUT"
        ));
      }
      function delete ($path, $controllername)
      {
        array_push($this->schema, array(
          "params" => array_slice(explode("/", $path), 1),
          "controller" => $controllername,
          "method" => "DELETE"
        ));
      }
      function patch ($path, $controllername)
      {
        array_push($this->schema, array(
          "params" => array_slice(explode("/", $path), 1),
          "controller" => $controllername,
          "method" => "PATCH"
        ));
      }
  
    }

  }

    


?>