<?php
/*
* @version 0.1 (wizard)
*/
  if ($this->owner->name=='panel') {
   $out['CONTROLPANEL']=1;
  }
  $table_name='sensors';
  $rec=SQLSelectOne("SELECT * FROM $table_name WHERE ID='$id'");
  if ($rec['TYPE_SENSOR']=='') {
    $out['TYPE_SENSOR']='All';
  }
   
  if ($this->mode=='update') {
   $ok=1;
   global $enable;
    $rec['ENABLE']=$enable; 
   global $title;
   $rec['TITLE']=$title;
   if ($rec['TITLE']=='') {
    $out['ERR_TITLE']=1;
    $ok=0;
   }
  //updating 'provider' (varchar)
   global $provider;
   $rec['PROVIDER']=$provider;
  //updating 'provider_settings' (varchar)
   global $provider_settings;
   $rec['PROVIDER_SETTINGS']=$provider_settings;
  //updating 'type_sensor' (varchar)
   global $type_sensor;
   $rec['TYPE_SENSOR']=$type_sensor;
  //updating 'subtype_sensor' (varchar)
   global $subtype_sensor;
   $rec['SUBTYPE_SENSOR']=$subtype_sensor;
  //updating 'unit_sensor' (varchar)
   global $unit_sensor;
   $rec['UNIT_SENSOR']=$unit_sensor;
   global $linked_object;
   $rec['LINKED_OBJECT']=$linked_object;
   global $linked_property;
   if ($linked_property == '' && $type_sensor!='All')
       $linked_property = $title;
   $rec['LINKED_PROPERTY']=$linked_property;
  //UPDATING RECORD
   if ($ok) {
    if ($rec['ID']) {
     SQLUpdate($table_name, $rec); // update
    } else {
     $new_rec=1;
     $rec['ID']=SQLInsert($table_name, $rec); // adding new record
    }
    $out['OK']=1;
   } else {
    $out['ERR']=1;
   }
  }
  if (is_array($rec)) {
   foreach($rec as $k=>$v) {
    if (!is_array($v)) {
     $rec[$k]=htmlspecialchars($v);
    }
   }
  }
  
  $type_sensors = array();
  
  function find_sensor($data, $text, $url, $img, &$in_arr = array())
  {
      foreach ($data as $child)
      {
        if (count($child["Children"])>0)
          find_sensor($child["Children"],$text.'-'.$child['Text'],$url, $child['ImageURL'],$in_arr);
        else
          $in_arr[] = array('ID'=>$child['id'],'TITLE'=>$text.'-'.$child['Text'], "IMAGE"=>$url."/".$img);
      }
      return $in_arr;
  }
  
    if ($rec['PROVIDER']!="ohm")
    {
        $type_sensors[] = array('ID'=>"",'TITLE'=>"","IMAGE"=>"../templates/sysinfo/img/transparent.png");
        $type_sensors[] = array('ID'=>"cpu",'TITLE'=>"CPU","IMAGE"=>"../templates/sysinfo/img/cpu.png");
        $type_sensors[] = array('ID'=>"ram",'TITLE'=>"RAM","IMAGE"=>"../templates/sysinfo/img/ram.png");
        $type_sensors[] = array('ID'=>"hdd",'TITLE'=>"HDD","IMAGE"=>"../templates/sysinfo/img/hdd.png");
        $type_sensors[] = array('ID'=>"uptime",'TITLE'=>"Uptime","IMAGE"=>"../templates/sysinfo/img/computer.png");
    }
    else
    {
        $json = getUrl($rec['PROVIDER_SETTINGS']+"/data.json",5);
        $data = json_decode($json,true);
        $type_sensors = find_sensor($data["Children"],$data["Text"],$rec['PROVIDER_SETTINGS']);
        array_unshift($type_sensors,array('ID'=>"All",'TITLE'=>"All","IMAGE"=>"../templates/sysinfo/img/computer.png"));
    }
//  print_r ($type_sensors);
  $out["SENSORS"] = $type_sensors;

  outHash($rec, $out);
