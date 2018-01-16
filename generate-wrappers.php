#!/usr/bin/php 

<?php
 
 print "Started generation of Azure CLI wrappers for Ansible\n";
 
 $path = realpath('azure-docs-cli-python/latest/docs-ref-autogen');

 $Directory = new RecursiveDirectoryIterator($path);
 $Iterator = new RecursiveIteratorIterator($Directory);
 $objects = new RegexIterator($Iterator, '/^.+\.yml$/i', RecursiveRegexIterator::GET_MATCH);
 
 foreach($objects as $name => $object){
 
 echo realpath($name)."\n";  
 $parsed_yaml_data = yaml_parse_file(realpath($name));

 foreach ($parsed_yaml_data['items'] as $key => $value) {
    
    if(!isset($value['children'])) {
        $myfile = fopen("az-wrappers/".str_replace("_", "-",$value['uid']).".yml", "w") or die("Unable to open file!\n");        
          
        fwrite($myfile, "\n\n");
          
        fwrite($myfile, " - name: Set input params for ".$value['uid']."\n");
        fwrite($myfile, "   set_fact: \n");
        fwrite($myfile, "     ".$value['uid'].":\n"); 
        fwrite($myfile, "       input: \n");
        
        foreach($value['parameters'] as $param_number => $parameter)
        {
          $param_pieces = $pieces = explode(" ", $parameter['name']);
          $param_name = str_replace("--","",$param_pieces[0]);
          
          if($param_name == "tags") {
             $param_name = "rtags";
          }
          
          if(isset($parameter['isRequired'])){ 
            $parameter_requirement = "Required";
          } else { 
            $parameter_requirement = "Optional";
          }
           
          $parameter_description = " ###  ".$parameter_requirement." parameter. ".$parameter['summary']."  ###";
          fwrite($myfile, "        ".$parameter_description." \n"); 
          fwrite($myfile, "        '".$param_name."': \"{{ item['".$param_name."'] | default('') }}\" \n");          
        }
           
        fwrite($myfile, " - debug: msg=\"{{ ".$value['uid']." }}\"          \n");
        fwrite($myfile, " - name: \"".$value['name'].": ".$value['summary']."\" \n");
        fwrite($myfile, "   command: |                                      \n");
        fwrite($myfile, "     ".$value['name']."                            \n");
        
        foreach($value['parameters'] as $param_number => $parameter)
        {
          $param_pieces = $pieces = explode(" ", $parameter['name']);
          $param_name = str_replace("--","",$param_pieces[0]);
          
          if($param_name == "tags") {
             $param_name = "rtags";
          }
          
          if(isset($parameter['isRequired'])){ 
            $parameter_requirement = "Required";
          } else { 
            $parameter_requirement = "Optional";
          }
           
                 
          if(isset($parameter['isRequired'])){ 
            fwrite($myfile, "                 --".$param_name." {{ ".$value['uid'].".input['".$param_name."'] }} \n");     
          } else { 
            
            $param_str = "                 {%if ".$value['uid'].".input['".$param_name."'] %} --".$param_name." ".$value['uid'].".input['".$param_name."'] {%endif %} \n";  
            
            if($param_name=="rtags") {
              $param_str = "                 {%if ".$value['uid'].".input['".$param_name."'] %} --tags ".$value['uid'].".input['".$param_name."'] {%endif %} \n";      
            }
            
            if($param_name=="yes") {
              $param_str = "                 {%if ".$value['uid'].".input['".$param_name."'] %} --yes {%endif %} \n";      
            }
            
            fwrite($myfile, $param_str);
          }     
        }      
       
                           
        fwrite($myfile, "   register: output  \n");
        fwrite($myfile, " - set_fact: \n");
        fwrite($myfile, "    ".$value['uid'].": \"{{ ".$value['uid']."|combine({'output':output.stdout|from_json}) }}\"  \n");
        fwrite($myfile, " - debug: msg=\"{{ ".$value['uid']." }}\" \n");
         
        fclose($myfile);
        
    } else {
        continue;
    }
      
   }
 }


 

?>