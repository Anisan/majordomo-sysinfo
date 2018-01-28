<?php
/*
* @version 0.1 (wizard)
*/
  if ($this->owner->name=='panel') {
   $out['CONTROLPANEL']=1;
  }
  $table_name='sensors';
  $rec=SQLSelectOne("SELECT * FROM $table_name WHERE ID='$id'");
  if ($this->mode=='update') {
   $ok=1;
  //updating '<%LANG_TITLE%>' (varchar, required)
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
  //updating '<%LANG_LINKED_OBJECT%>' (varchar)
   global $linked_object;
   $rec['LINKED_OBJECT']=$linked_object;
  //updating '<%LANG_LINKED_PROPERTY%>' (varchar)
   global $linked_property;
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
  outHash($rec, $out);
