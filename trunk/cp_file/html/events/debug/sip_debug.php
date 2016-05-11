<?php

// error_reporting(0);

 if(!isset($hn)) $hn = "127.0.0.1";
 if(!isset($un)) $un = "uuuuuuuuuu";
 if(!isset($ps)) $ps = "pppppppppp";
 if(!isset($tn)) $tn = "events";
 if(!isset($db)) $db = "bbbbbbbbbb";

 $mylink = mysql_connect($hn,$un,$ps) or die("Error: can not connect to MySQL server\n");
 mysql_select_db($db) or die("Error: select database $db failed");

 $query = "select * from $tn";
 $result = mysql_query($query);

 while($list = mysql_fetch_assoc($result)) {
   $data  = trim($list['event']);
   $event  = split(" ", $data);
   if ($event[1] == "PeerStatus") {
     print_r($event);
     echo  "\n";
   }
 }
 mysql_close($mylink);
?>
