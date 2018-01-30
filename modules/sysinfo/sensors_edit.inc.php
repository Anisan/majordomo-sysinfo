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
   if ($linked_property == '')
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
  
  function find_sensor($data, $text, &$in_arr = array())
  {
      foreach ($data as $child)
      {
        if (count($child["Children"])>0)
          find_sensor($child["Children"],$text.'-'.$child['Text'],$in_arr);
        else
          $in_arr[] = array('ID'=>$child['id'],'TITLE'=>$text.'-'.$child['Text']);
      }
      return $in_arr;
  }
  
    if ($rec['PROVIDER']!="ohm")
    {
        $type_sensors[] = array('ID'=>"cpu",'TITLE'=>"CPU");
        $type_sensors[] = array('ID'=>"ram",'TITLE'=>"RAM");
        $type_sensors[] = array('ID'=>"hdd",'TITLE'=>"HDD");
        $type_sensors[] = array('ID'=>"uptime",'TITLE'=>"Uptime");
    }
    else
    {
        $json = '{"id": 0, "Text": "Sensor", "Children": [{"id": 1, "Text": "HIBERNATE", "Children": [{"id": 2, "Text": "Gigabyte Z77X-UD3H", "Children": [{"id": 3, "Text": "ITE IT8728F", "Children": [{"id": 4, "Text": "Voltages", "Children": [{"id": 5, "Text": "Voltage #1", "Children": [], "Min": "1,056 V", "Value": "1,068 V", "Max": "1,068 V", "ImageURL": "images/transparent.png"}, {"id": 6, "Text": "Voltage #2", "Children": [], "Min": "2,004 V", "Value": "2,004 V", "Max": "2,004 V", "ImageURL": "images/transparent.png"}, {"id": 7, "Text": "Voltage #3", "Children": [], "Min": "2,040 V", "Value": "2,040 V", "Max": "2,052 V", "ImageURL": "images/transparent.png"}, {"id": 8, "Text": "Voltage #4", "Children": [], "Min": "1,992 V", "Value": "2,004 V", "Max": "2,016 V", "ImageURL": "images/transparent.png"}, {"id": 9, "Text": "Voltage #5", "Children": [], "Min": "0,012 V", "Value": "0,048 V", "Max": "1,008 V", "ImageURL": "images/transparent.png"}, {"id": 10, "Text": "Voltage #6", "Children": [], "Min": "1,068 V", "Value": "1,080 V", "Max": "1,116 V", "ImageURL": "images/transparent.png"}, {"id": 11, "Text": "Voltage #7", "Children": [], "Min": "1,524 V", "Value": "1,524 V", "Max": "1,524 V", "ImageURL": "images/transparent.png"}, {"id": 12, "Text": "Standby +3.3V", "Children": [], "Min": "3,384 V", "Value": "3,408 V", "Max": "3,408 V", "ImageURL": "images/transparent.png"}, {"id": 13, "Text": "VBat", "Children": [], "Min": "3,120 V", "Value": "3,120 V", "Max": "3,120 V", "ImageURL": "images/transparent.png"}], "Min": "", "Value": "", "Max": "", "ImageURL": "images_icon/voltage.png"}, {"id": 14, "Text": "Temperatures", "Children": [{"id": 15, "Text": "Temperature #1", "Children": [], "Min": "38,0 °C", "Value": "38,0 °C", "Max": "38,0 °C", "ImageURL": "images/transparent.png"}, {"id": 16, "Text": "Temperature #2", "Children": [], "Min": "46,0 °C", "Value": "46,0 °C", "Max": "46,0 °C", "ImageURL": "images/transparent.png"}, {"id": 17, "Text": "Temperature #3", "Children": [], "Min": "44,0 °C", "Value": "57,0 °C", "Max": "58,0 °C", "ImageURL": "images/transparent.png"}], "Min": "", "Value": "", "Max": "", "ImageURL": "images_icon/temperature.png"}, {"id": 18, "Text": "Fans", "Children": [{"id": 19, "Text": "Fan #1", "Children": [], "Min": "2129 RPM", "Value": "2136 RPM", "Max": "2150 RPM", "ImageURL": "images/transparent.png"}], "Min": "", "Value": "", "Max": "", "ImageURL": "images_icon/fan.png"}, {"id": 20, "Text": "Controls", "Children": [{"id": 21, "Text": "Fan Control #1", "Children": [], "Min": "-", "Value": "-", "Max": "-", "ImageURL": "images/transparent.png"}, {"id": 22, "Text": "Fan Control #2", "Children": [], "Min": "-", "Value": "-", "Max": "-", "ImageURL": "images/transparent.png"}, {"id": 23, "Text": "Fan Control #3", "Children": [], "Min": "-", "Value": "-", "Max": "-", "ImageURL": "images/transparent.png"}], "Min": "", "Value": "", "Max": "", "ImageURL": "images_icon/control.png"}], "Min": "", "Value": "", "Max": "", "ImageURL": "images_icon/chip.png"}], "Min": "", "Value": "", "Max": "", "ImageURL": "images_icon/mainboard.png"}, {"id": 24, "Text": "Intel Core i7-3770", "Children": [{"id": 25, "Text": "Clocks", "Children": [{"id": 26, "Text": "Bus Speed", "Children": [], "Min": "100 MHz", "Value": "100 MHz", "Max": "100 MHz", "ImageURL": "images/transparent.png"}, {"id": 27, "Text": "CPU Core #1", "Children": [], "Min": "3703 MHz", "Value": "3704 MHz", "Max": "3704 MHz", "ImageURL": "images/transparent.png"}, {"id": 28, "Text": "CPU Core #2", "Children": [], "Min": "3703 MHz", "Value": "3704 MHz", "Max": "3704 MHz", "ImageURL": "images/transparent.png"}, {"id": 29, "Text": "CPU Core #3", "Children": [], "Min": "3703 MHz", "Value": "3704 MHz", "Max": "3704 MHz", "ImageURL": "images/transparent.png"}, {"id": 30, "Text": "CPU Core #4", "Children": [], "Min": "3703 MHz", "Value": "3704 MHz", "Max": "3804 MHz", "ImageURL": "images/transparent.png"}], "Min": "", "Value": "", "Max": "", "ImageURL": "images_icon/clock.png"}, {"id": 31, "Text": "Temperatures", "Children": [{"id": 32, "Text": "CPU Core #1", "Children": [], "Min": "45,0 °C", "Value": "61,0 °C", "Max": "64,0 °C", "ImageURL": "images/transparent.png"}, {"id": 33, "Text": "CPU Core #2", "Children": [], "Min": "52,0 °C", "Value": "65,0 °C", "Max": "69,0 °C", "ImageURL": "images/transparent.png"}, {"id": 34, "Text": "CPU Core #3", "Children": [], "Min": "49,0 °C", "Value": "62,0 °C", "Max": "67,0 °C", "ImageURL": "images/transparent.png"}, {"id": 35, "Text": "CPU Core #4", "Children": [], "Min": "47,0 °C", "Value": "61,0 °C", "Max": "65,0 °C", "ImageURL": "images/transparent.png"}, {"id": 36, "Text": "CPU Package", "Children": [], "Min": "52,0 °C", "Value": "65,0 °C", "Max": "68,0 °C", "ImageURL": "images/transparent.png"}], "Min": "", "Value": "", "Max": "", "ImageURL": "images_icon/temperature.png"}, {"id": 37, "Text": "Load", "Children": [{"id": 38, "Text": "CPU Total", "Children": [], "Min": "12,5 %", "Value": "100,0 %", "Max": "100,0 %", "ImageURL": "images/transparent.png"}, {"id": 39, "Text": "CPU Core #1", "Children": [], "Min": "14,1 %", "Value": "100,0 %", "Max": "100,0 %", "ImageURL": "images/transparent.png"}, {"id": 40, "Text": "CPU Core #2", "Children": [], "Min": "0,0 %", "Value": "100,0 %", "Max": "100,0 %", "ImageURL": "images/transparent.png"}, {"id": 41, "Text": "CPU Core #3", "Children": [], "Min": "0,0 %", "Value": "100,0 %", "Max": "100,0 %", "ImageURL": "images/transparent.png"}, {"id": 42, "Text": "CPU Core #4", "Children": [], "Min": "0,0 %", "Value": "100,0 %", "Max": "100,0 %", "ImageURL": "images/transparent.png"}], "Min": "", "Value": "", "Max": "", "ImageURL": "images_icon/load.png"}, {"id": 43, "Text": "Powers", "Children": [{"id": 44, "Text": "CPU Package", "Children": [], "Min": "28,8 W", "Value": "46,5 W", "Max": "49,0 W", "ImageURL": "images/transparent.png"}, {"id": 45, "Text": "CPU Cores", "Children": [], "Min": "23,3 W", "Value": "40,6 W", "Max": "42,2 W", "ImageURL": "images/transparent.png"}, {"id": 46, "Text": "!!!!!!!", "Children": [], "Min": "0,2 W", "Value": "0,2 W", "Max": "4,4 W", "ImageURL": "images/transparent.png"}], "Min": "", "Value": "", "Max": "", "ImageURL": "images_icon/power.png"}], "Min": "", "Value": "", "Max": "", "ImageURL": "images_icon/cpu.png"}, {"id": 47, "Text": "Generic Memory", "Children": [{"id": 48, "Text": "Load", "Children": [{"id": 49, "Text": "Memory", "Children": [], "Min": "64,7 %", "Value": "64,9 %", "Max": "74,4 %", "ImageURL": "images/transparent.png"}], "Min": "", "Value": "", "Max": "", "ImageURL": "images_icon/load.png"}, {"id": 50, "Text": "Data", "Children": [{"id": 51, "Text": "Used Memory", "Children": [], "Min": "10,3 GB", "Value": "10,3 GB", "Max": "11,8 GB", "ImageURL": "images/transparent.png"}, {"id": 52, "Text": "Available Memory", "Children": [], "Min": "4,1 GB", "Value": "5,6 GB", "Max": "5,6 GB", "ImageURL": "images/transparent.png"}], "Min": "", "Value": "", "Max": "", "ImageURL": "images_icon/power.png"}], "Min": "", "Value": "", "Max": "", "ImageURL": "images_icon/ram.png"}, {"id": 53, "Text": "Generic Hard Disk", "Children": [{"id": 54, "Text": "Load", "Children": [{"id": 55, "Text": "Used Space", "Children": [], "Min": "34,4 %", "Value": "34,4 %", "Max": "34,4 %", "ImageURL": "images/transparent.png"}], "Min": "", "Value": "", "Max": "", "ImageURL": "images_icon/load.png"}], "Min": "", "Value": "", "Max": "", "ImageURL": "images_icon/hdd.png"}, {"id": 56, "Text": "ST500DM002-1BD142", "Children": [{"id": 57, "Text": "Temperatures", "Children": [{"id": 58, "Text": "Temperature", "Children": [], "Min": "37,0 °C", "Value": "37,0 °C", "Max": "37,0 °C", "ImageURL": "images/transparent.png"}], "Min": "", "Value": "", "Max": "", "ImageURL": "images_icon/temperature.png"}, {"id": 59, "Text": "Load", "Children": [{"id": 60, "Text": "Used Space", "Children": [], "Min": "97,4 %", "Value": "97,4 %", "Max": "97,4 %", "ImageURL": "images/transparent.png"}], "Min": "", "Value": "", "Max": "", "ImageURL": "images_icon/load.png"}], "Min": "", "Value": "", "Max": "", "ImageURL": "images_icon/hdd.png"}, {"id": 61, "Text": "SAMSUNG SSD CM871 2.5 7mm 128GB", "Children": [{"id": 62, "Text": "Load", "Children": [{"id": 63, "Text": "Used Space", "Children": [], "Min": "62,1 %", "Value": "62,1 %", "Max": "62,1 %", "ImageURL": "images/transparent.png"}], "Min": "", "Value": "", "Max": "", "ImageURL": "images_icon/load.png"}], "Min": "", "Value": "", "Max": "", "ImageURL": "images_icon/hdd.png"}], "Min": "", "Value": "", "Max": "", "ImageURL": "images_icon/computer.png"}], "Min": "Min", "Value": "Value", "Max": "Max", "ImageURL": ""}';
        $json = getUrl($rec['PROVIDER_SETTINGS'],5);
        $data = json_decode($json,true);
        $type_sensors = find_sensor($data["Children"]);
    }
//  print_r ($type_sensors);
  $out["SENSORS"] = $type_sensors;

  outHash($rec, $out);
