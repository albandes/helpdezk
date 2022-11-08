<?php 
/* 
 * Smarty plugin 
 * ------------------------------------------------------------- 
 * File:     function.head_item.php 
 * Type:     function 
 * Name:     head_item 
 * Purpose:  assign items to global template <head> section 
 * ------------------------------------------------------------- 
 */ 

/** 
 * Smarty head_item function 
 * 
 * This function provides an easy way to add javascript files, css files 
 * and meta information into your templates <head> section 
 * 
 * @author Chris York (echoDreamz) <cyork@echodreamz.com> 
 * @copyright 2009 Chris York (echoDreamz) 
 * 
 * @param Array $params Array of parameters 
 * @param Smarty $smarty Smarty object reference 
 * @return String 
 */ 

 function smarty_function_head_item($params = array(), &$smarty) 
{

   // Sanitize check for required parameters 
   if (empty($params["type"])) 
   { 
      $smarty->trigger_error("head_item: Missing required parameter type. Available types: js, css, meta.", E_USER_ERROR); 
   } 
   if (!in_array($params["type"], array("js", "js_async", "css", "meta"))) 
   { 
      $smarty->trigger_error("head_item: Invalid type specified. Valid types: js, js_async, css, meta.", E_USER_ERROR); 
   } 
   if ($params["type"] == "js" or $params["type"] == "js_async") 
   { 
      if (empty($params["src"])) 
      { 
         $smarty->trigger_error("head_item: Missing required parameter src.", E_USER_ERROR); 
      } 
      if (empty($params["files"])) 
      { 
         $smarty->trigger_error("head_item: Missing required parameter files.", E_USER_ERROR); 
      } 
      if (empty($params["jsType"])) 
      { 
         $params["jsType"] = "text/javascript"; 
      } 
   } 
   if ($params["type"] == "css") 
   { 
      if (empty($params["src"])) 
      { 
         $smarty->trigger_error("head_item: Missing required parameter src.", E_USER_ERROR); 
      } 
      if (empty($params["files"])) 
      { 
         $smarty->trigger_error("head_item: Missing required parameter files.", E_USER_ERROR); 
      } 
      if (empty($params["media"])) 
      { 
         $params["media"] = "screen"; 
      } 
   } 
   if ($params["type"] == "meta") 
   { 
      if (empty($params["name"])) 
      { 
         $smarty->trigger_error("head_item: Missing required parameter name.", E_USER_ERROR); 
      } 
      if (empty($params["content"])) 
      { 
         $smarty->trigger_error("head_item: Missing required parameter content.", E_USER_ERROR); 
      } 
   } 
    
    
   // Final output 
   $output = ""; 
   if ($params["type"] != "meta") 
   { 
      $files = explode(",", $params["files"]); 
   } 
    
   // Do the correct output 
   switch ($params["type"]) 
   { 
      // javascript 
      case "js": 
         foreach ($files as $file) 
         { 
            $output .= "<script  type=\"{$params['jsType']}\" src=\"{$params['src']}{$file}\"></script>\n"; 
         } 
      break; 
      
	  // javascript executed asynchronously
      case "js_async": 
         foreach ($files as $file) 
         { 
            $output .= "<script  type=\"{$params['jsType']}\" async src=\"{$params['src']}{$file}\"></script>\n"; 
         } 
      break;
	  
      // CSS 
      case "css": 
         foreach ($files as $file) 
         { 
            $output .= "<link rel='stylesheet' type=\"text/css\" media=\"{$params['media']}\" href=\"{$params['src']}{$file}\" />\n"; 
         } 
      break; 
      
      // Meta 
      case "meta": 
         $output = "<meta name=\"{$params['name']}\" content=\"{$params['content']}\" />\n"; 
      break; 
   } 
   //removing extra space at the end 
   return substr($output,0,-1); 
} 
?>