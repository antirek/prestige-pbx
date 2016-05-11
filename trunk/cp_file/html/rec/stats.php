<?php

date_default_timezone_set('Europe/Moscow');
?>

<html><head>
<title>Asterisk Record Manager</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="css/main.css" rel="stylesheet" type="text/css"> 
<script src="js/calendar_ru.js"></script>
</head>


<form>
<table>
<td>from</td><td><input name=date_from  type="text" value="<?php if($_GET[date_from]) echo "$_GET[date_from]"; else echo date("d-m-y",mktime (0, 0, 0, date ("m") , date ("d")-1, date ("Y"))); ?>" onfocus="this.select();lcs(this)" onclick="event.cancelBubble=true;this.select();lcs(this)"></td>
<td>to  </td><td><input name=date_to type="text" value="<?php if($_GET[date_to]) echo "$_GET[date_to]"; else echo date("d-m-y");  ?>" onfocus="this.select();lcs(this)" onclick="event.cancelBubble=true;this.select();lcs(this)"></td>
<tr><td><input type="submit" value="Submin"></td><td></td><td></td><td></td></tr>
</form>
</table>

<?php        
$server="127.0.0.1";
$user = "root";
$password = "ds8nvslq";
$db = "asterisk";

if (!mysql_connect($server, $user, $password)) {
 echo "Error connect to MySQL";
  exit;
  }
mysql_select_db($db);

require_once("class.css");
require_once("sort.js");


$req="select src,dst,count(src) from cdr where calldate BETWEEN STR_TO_DATE('$_GET[date_from] 00:00:00','%d-%m-%Y %H:%i:%s') AND STR_TO_DATE('$_GET[date_to] 23:59:59','%d-%m-%Y %H:%i:%s') and userfield!='blacklist' group by src ";

echo "<table align=center><tr><td>uniq calls</td><td>calls from blacklist</td></tr><tr><td>";

echo "<table border=2 align=center class=\"sort\"><thead><tr>";
echo "<tr align=center><td> id </td><td>src</td><td>dst</td><td>cnt</td></thead><tbody>";


 $query = mysql_query("$req"); 

 while ($row = mysql_fetch_array($query)){
    $a=$a+1;
    echo "<tr><td>$a</td><td><a href=/rec/?from=$row[0] target=_blank>$row[0]</a></td><td>$row[1]</td><td>$row[2]</td></tr>";
 }

echo "</tbody></table>";


echo "</td><td>";

$a=0;
$req="select calldate,src,dst from cdr where userfield='blacklist'";

echo "<table border=2 align=center class=\"sort\"><thead><tr>";
echo "<tr align=center><td> id </td><td>calldate</td><td>src</td><td>dst</td></thead><tbody>";


 $query = mysql_query("$req"); 

 while ($row = mysql_fetch_array($query)){
    $a=$a+1;
    echo "<tr><td>$a</td><td>$row[0]</td><td><a href=/rec/?from=$row[1] target=_blank>$row[1]</a></td><td>$row[2]</td></tr>";
 }

 echo "</tbody></table>";


echo "</td></tr></table>";

mysql_close();

?>

