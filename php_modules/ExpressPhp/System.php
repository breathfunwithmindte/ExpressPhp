<?php

    namespace ExpressPhp {
      class System {

        function regex ($reg, $string)
        {
          preg_match($reg, $string, $match);
          if($match == null) return null;
          return $match[0];
        }
    
        function current_url ()
        {
          $devregex = sprintf('(?<=\\%s)(.*)', $GLOBALS["project_dev_name"]);
          if($GLOBALS["mode"] === "dev") return $this->regex("/{$devregex}/i", $_SERVER['REQUEST_URI']);
          return $this->regex('/(.*)/i', $_SERVER['REQUEST_URI']);
        }
    
    
      }
    }

?>