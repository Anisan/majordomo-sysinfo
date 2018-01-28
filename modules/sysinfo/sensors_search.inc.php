<?php
/*
* @version 0.1 (wizard)
*/
 global $session;
  if ($this->owner->name=='panel') {
   $out['CONTROLPANEL']=1;
  }
  $qry="1";
  // search filters
  // QUERY READY
  global $save_qry;
  if ($save_qry) {
   $qry=$session->data['sensors_qry'];
  } else {
   $session->data['sensors_qry']=$qry;
  }
  if (!$qry) $qry="1";
  $sortby_sensors="ID DESC";
  $out['SORTBY']=$sortby_sensors;
  // SEARCH RESULTS
  $res=SQLSelect("SELECT * FROM sensors WHERE $qry ORDER BY ".$sortby_sensors);
  if ($res[0]['ID']) {
   //paging($res, 100, $out); // search result paging
   $total=count($res);
   for($i=0;$i<$total;$i++) {
    // some action for every record if required
    $res[$i]['VALUE_SENSOR']=gg($res[$i]["LINKED_OBJECT"].".".$res[$i]["LINKED_PROPERTY"]);
   }
   $out['RESULT']=$res;
  }
