<?php
/**
* SystemInfo 
* @package project
* @author Eraser <eraser1981@gmail.com>
* @copyright http://majordomo.smartliving.ru/ (c)
* @version 0.2 (wizard, 20:01:09 [Jan 27, 2018])
*/
//
//
require_once("./modules/sysinfo/SystemInfo.php");

class sysinfo extends module {
/**
* sysinfo
*
* Module class constructor
*
* @access private
*/
function sysinfo() {
  $this->name="sysinfo";
  $this->title="SystemInfo";
  $this->module_category="<#LANG_SECTION_SYSTEM#>";
  $this->checkInstalled();
}
/**
* saveParams
*
* Saving module parameters
*
* @access public
*/
function saveParams($data=0) {
 $p=array();
 if (IsSet($this->id)) {
  $p["id"]=$this->id;
 }
 if (IsSet($this->view_mode)) {
  $p["view_mode"]=$this->view_mode;
 }
 if (IsSet($this->edit_mode)) {
  $p["edit_mode"]=$this->edit_mode;
 }
 if (IsSet($this->tab)) {
  $p["tab"]=$this->tab;
 }
 return parent::saveParams($p);
}
/**
* getParams
*
* Getting module parameters from query string
*
* @access public
*/
function getParams() {
  global $id;
  global $mode;
  global $view_mode;
  global $edit_mode;
  global $tab;
  if (isset($id)) {
   $this->id=$id;
  }
  if (isset($mode)) {
   $this->mode=$mode;
  }
  if (isset($view_mode)) {
   $this->view_mode=$view_mode;
  }
  if (isset($edit_mode)) {
   $this->edit_mode=$edit_mode;
  }
  if (isset($tab)) {
   $this->tab=$tab;
  }
}
/**
* Run
*
* Description
*
* @access public
*/
function run() {
 global $session;
  $out=array();
  if ($this->action=='admin') {
   $this->admin($out);
  } else {
   $this->usual($out);
  }
  if (IsSet($this->owner->action)) {
   $out['PARENT_ACTION']=$this->owner->action;
  }
  if (IsSet($this->owner->name)) {
   $out['PARENT_NAME']=$this->owner->name;
  }
  $out['VIEW_MODE']=$this->view_mode;
  $out['EDIT_MODE']=$this->edit_mode;
  $out['MODE']=$this->mode;
  $out['ACTION']=$this->action;
  $out['TAB']=$this->tab;
  $this->data=$out;
  $p=new parser(DIR_TEMPLATES.$this->name."/".$this->name.".html", $this->data, $this);
  $this->result=$p->result;
}
/**
* BackEnd
*
* Module backend
*
* @access public
*/
function admin(&$out) {
 $this->getConfig();
 if ((time() - gg('cycle_sysinfoRun')) < $this->config['UPDATE_TIMEOUT']*2 ) {
        $out['CYCLERUN'] = 1;
    } else {
        $out['CYCLERUN'] = 0;
    }
    $out['OBJECT_LINK']=$this->config['OBJECT_LINK'];
    $out['UPDATE_TIMEOUT']=$this->config['UPDATE_TIMEOUT'];
    if (!$out['UPDATE_TIMEOUT']) {
        $out['UPDATE_TIMEOUT']='5';
    } 
    if ($this->view_mode=='update_settings') {
        global $object_link;
        $this->config['OBJECT_LINK']=$object_link;
        global $update_timeout;
        $this->config['UPDATE_TIMEOUT']=$update_timeout;
        $this->saveConfig();
        $this->redirect("?");
    }

 if (isset($this->data_source) && !$_GET['data_source'] && !$_POST['data_source']) {
  $out['SET_DATASOURCE']=1;
 }
 if ($this->data_source=='sensors' || $this->data_source=='') {
  if ($this->view_mode=='' || $this->view_mode=='search_sensors') {
   $this->search_sensors($out);
  }
  if ($this->view_mode=='edit_sensors') {
   $this->edit_sensors($out, $this->id);
  }
  if ($this->view_mode=='delete_sensors') {
   $this->delete_sensors($this->id);
   $this->redirect("?");
  }
 }
}
/**
* FrontEnd
*
* Module frontend
*
* @access public
*/
function usual(&$out) {
 $this->admin($out);
}
/**
* sensors search
*
* @access public
*/
 function search_sensors(&$out) {
  require(DIR_MODULES.$this->name.'/sensors_search.inc.php');
 }
/**
* sensors edit/add
*
* @access public
*/
 function edit_sensors(&$out, $id) {
  require(DIR_MODULES.$this->name.'/sensors_edit.inc.php');
 }
/**
* sensors delete record
*
* @access public
*/
 function delete_sensors($id) {
  $rec=SQLSelectOne("SELECT * FROM sensors WHERE ID='$id'");
  // some action for related tables
  SQLExec("DELETE FROM sensors WHERE ID='".$rec['ID']."'");
 }
 function propertySetHandle($object, $property, $value) {
  $this->getConfig();
   $table='sensors';
   $properties=SQLSelect("SELECT ID FROM $table WHERE LINKED_OBJECT LIKE '".DBSafe($object)."' AND LINKED_PROPERTY LIKE '".DBSafe($property)."'");
   $total=count($properties);
   if ($total) {
    for($i=0;$i<$total;$i++) {
     //to-do
    }
   }
 }
function processCycle() {
 $this->getConfig();
 $sensors = SQLSelect("SELECT * FROM sensors WHERE ENABLE=1");
 $total_sensor=count($sensors);
 //echo date("H:i:s") ."Count sensors = " . $total_sensor . PHP_EOL;
 for($i=0;$i<$total_sensor;$i++) {
   $value = "Not supported";
   $property = $sensors[$i]["LINKED_OBJECT"].".".$sensors[$i]["LINKED_PROPERTY"];
   if ($sensors[$i]["LINKED_PROPERTY"]=='')
    $property = $sensors[$i]["LINKED_OBJECT"].".".$sensors[$i]["TITLE"];
   if ($sensors[$i]["PROVIDER"] == "custom")
   {
       $value = exec($sensors[$i]["PROVIDER_SETTINGS"]);
       sg($property, $value);
   }
   if ($sensors[$i]["PROVIDER"] == "local")
   {
       $system = new SystemInfo();
       if ($sensors[$i]["TYPE_SENSOR"] == "cpu")
         $value = round($system->getCpuLoadPercentage(),1);
       if ($sensors[$i]["TYPE_SENSOR"] == "uptime")
         $value = $system->getUpTime();
       if ($sensors[$i]["TYPE_SENSOR"] == "ram")
       {
           $ramTotal = $system->getRamTotal();
           $ramFree = $system->getRamFree();
           if ($sensors[$i]["SUBTYPE_SENSOR"] == "total")
             $value= $ramTotal;
           if ($sensors[$i]["SUBTYPE_SENSOR"] == "free")
             $value= $ramFree;
           if ($sensors[$i]["SUBTYPE_SENSOR"] == "use")
             $value= $ramTotal-$ramFree;
           $value = $this->convert_unit($value,$ramTotal,$sensors[$i]["UNIT_SENSOR"]);
       }
       if ($sensors[$i]["TYPE_SENSOR"] == "hdd")
       {
           $disc = $system->getDiskSize($sensors[$i]["PROVIDER_SETTINGS"]);
           $total = $disc['size'];
           $free = $disc['free'];
           if ($sensors[$i]["SUBTYPE_SENSOR"] == "total")
             $value= $total;
           if ($sensors[$i]["SUBTYPE_SENSOR"] == "free")
             $value= $free;
           if ($sensors[$i]["SUBTYPE_SENSOR"] == "use")
             $value= $total-$free;
           $value = $this->convert_unit($value,$total,$sensors[$i]["UNIT_SENSOR"]);
       }
       sg($property, $value);
   }
   if ($sensors[$i]["PROVIDER"] == "ohm")
   {
        $json = getUrl($sensors[$i]['PROVIDER_SETTINGS']."/data.json",5);
        if ($json !="")
        {
            $data = json_decode($json,true);
            $sens = $this->find_sensor($data["Children"],$sensors[$i]["TYPE_SENSOR"]);
            //print_r($sens);
            if (count($sens)>0)
            {
                foreach ($sens as $sensor)
                {
                    $svalue = $sensor["VALUE"];
                    if (strpos($svalue,' '))
                        $svalue = substr($svalue, 0,strpos($svalue,' '));
                    $value = $svalue;
                    if ($sensors[$i]["TYPE_SENSOR"]!='All')
                        sg($property, $value);
                    else
                        sg($sensors[$i]["LINKED_OBJECT"].".".$sensor["TITLE"], $value);
                }
            }
        }
   }
 }
}

function find_sensor($data, $id, &$in_arr = array())
  {
      foreach ($data as $child)
      {
        if (count($child["Children"])>0)
          $this->find_sensor($child["Children"],$id,$in_arr);
        else
        {
            if ($id == 'All')
            {
                $in_arr[] = array('ID'=>$child['id'],'TITLE'=>$child['Text'],'VALUE'=>$child['Value']);
            }
            else
            {
                if ($child['id']==$id)
                    $in_arr[] = array('ID'=>$child['id'],'TITLE'=>$child['Text'],'VALUE'=>$child['Value']);
            }
        }
      }
      return $in_arr;
  }
 
function convert_unit($value,$total,$unit) {
     if ($unit == "kbyte")
       $value= round($value / 1024,1);
     if ($unit == "mbyte")
       $value= round($value / 1024 / 1024,1);
     if ($unit == "gbyte")
       $value= round($value / 1024 /1024 / 1024,1);
     if ($unit == "procent")
       $value= round($value * 100 / $total,2);
     return $value;
}
/**
* Install
*
* Module installation routine
*
* @access private
*/
 function install($data='') {
  parent::install();
 }
/**
* Uninstall
*
* Module uninstall routine
*
* @access public
*/
 function uninstall() {
  SQLExec('DROP TABLE IF EXISTS sensors');
  parent::uninstall();
 }
/**
* dbInstall
*
* Database installation routine
*
* @access private
*/
 function dbInstall($data = '') {
/*
sensors - 
*/
  $data = <<<EOD
 sensors: ID int(10) unsigned NOT NULL auto_increment
 sensors: TITLE varchar(100) NOT NULL DEFAULT ''
 sensors: ENABLE int(3) unsigned NOT NULL DEFAULT '0'
 sensors: PROVIDER varchar(255) NOT NULL DEFAULT ''
 sensors: PROVIDER_SETTINGS varchar(255) NOT NULL DEFAULT ''
 sensors: TYPE_SENSOR varchar(255) NOT NULL DEFAULT ''
 sensors: SUBTYPE_SENSOR varchar(255) NOT NULL DEFAULT ''
 sensors: UNIT_SENSOR varchar(255) NOT NULL DEFAULT ''
 sensors: LINKED_OBJECT varchar(100) NOT NULL DEFAULT ''
 sensors: LINKED_PROPERTY varchar(100) NOT NULL DEFAULT ''
EOD;
  parent::dbInstall($data);
 }
// --------------------------------------------------------------------
}
/*
*
* TW9kdWxlIGNyZWF0ZWQgSmFuIDI3LCAyMDE4IHVzaW5nIFNlcmdlIEouIHdpemFyZCAoQWN0aXZlVW5pdCBJbmMgd3d3LmFjdGl2ZXVuaXQuY29tKQ==
*
*/
