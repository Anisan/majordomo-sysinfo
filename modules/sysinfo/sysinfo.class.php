<?php
/**
* SystemInfo 
* @package project
* @author Eraser <eraser1981@gmail.com>
* @copyright http://majordomo.smartliving.ru/ (c)
* @version 0.1 (wizard, 10:01:57 [Jan 23, 2018])
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
    if ((time() - gg('cycle_sysinfoRun')) < 60 ) {
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

function processCycle() {
    $this->getConfig();
    //to-do
    $object_link=$this->config['OBJECT_LINK'];
    $system = new SystemInfo();
    sg($object_link.".CpuLoad",$system->getCpuLoadPercentage());
    sg($object_link.".RamTotal",round($system->getRamTotal() / 1024 / 1024));
    sg($object_link.".RamFree",round($system->getRamFree() / 1024 / 1024));
    $disc = $system->getDiskSize(PHP_OS == 'WINNT' ? 'C:' : '/');
    sg($object_link.".DiscSize",$disc['size']);
    sg($object_link.".DiscFree",$disc['free']);
    $uptime = $system->getUpTime();
    sg($object_link.".Uptime",$uptime);
    
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
// --------------------------------------------------------------------
}
/*
*
* TW9kdWxlIGNyZWF0ZWQgSmFuIDIzLCAyMDE4IHVzaW5nIFNlcmdlIEouIHdpemFyZCAoQWN0aXZlVW5pdCBJbmMgd3d3LmFjdGl2ZXVuaXQuY29tKQ==
*
*/
