<?php
require_once 'config.php';
require_once 'func.php';
require_once 'class.css';
require_once 'sort.js';

date_default_timezone_set('Europe/Moscow');
?>

<html><head>
<title>Asterisk Record Manager</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="css/main.css" rel="stylesheet" type="text/css"> 
<script src="js/calendar_ru.js"></script>
</head>

<body>
<table border=0 width=100% align=center bgcolor='#ffefd5'>
<tr>
<td>Откуда</td><td><form><input type=text name=from <?php if($_GET[from]) echo "value=$_GET[from]";?>></td>
<td>Куда</td><td><input type=text name=to <?php if($_GET[to]) echo "value=$_GET[to]";?>></td>
</tr>
<tr>
<td>CallerID</td><td><form><input type=text name=callerid <?php if($_GET[callerid]) echo "value=$_GET[callerid]";?>></td>
<td>служебное поле</td><td><input type=text name=userfield <?php if($_GET[userfield]) echo "value=$_GET[userfield]";?>></td>
</tr>

<tr>
<td>От</td><td><input name=date_from  type="text" value="<?php if($_GET[date_from]) echo "$_GET[date_from]"; else echo date("d-m-y",mktime (0, 0, 0, date ("m") , date ("d")-1, date ("Y"))); ?>" onfocus="this.select();lcs(this)"
    onclick="event.cancelBubble=true;this.select();lcs(this)"></td>
<td>По</td><td><input name=date_to type="text" value="<?php if($_GET[date_to]) echo "$_GET[date_to]"; else echo date("d-m-y");  ?>" onfocus="this.select();lcs(this)"
    onclick="event.cancelBubble=true;this.select();lcs(this)"></td>
</tr>
<tr>
<td>Только Answered</td><td><input name=oa type=checkbox <?php if ($_GET["oa"]=="on") echo "checked=checked";?>></td>
<td>Фильтр по дате</td><td><input name=cd type=checkbox <?php  if ($_GET["cd"]=="on") echo "checked=checked";?>></td>
</tr>
<tr>
<td>Применить</td><td><input type="submit" value="Применить фильтр"></form></td>
<td>Сбросить</td><td><form method=get><input type=hidden name=action value=2><input type="submit" value="Сбросить фильтр"></form></td>
</tr>
</table>

<?php
db_connect();

$zapros="";
if ($_GET["oa"]=="on")
$zapros="disposition=\"ANSWERED\"";
if ($_GET["from"]){
    if ($zapros=="")
	$zapros=$zapros . " src like \"%$_GET[from]%\"";
    else
	$zapros=$zapros . " and src like \"%$_GET[from]%\"";
}
if ($_GET["to"]){
    if ($zapros=="")
	$zapros=$zapros . " dst like \"%$_GET[to]%\"";
    else
	$zapros=$zapros . " and dst like \"%$_GET[to]%\"";
}

if ($_GET["userfield"]){
    if ($zapros=="")
	$zapros=$zapros . " userfield like \"%$_GET[userfield]%\"";
    else
	$zapros=$zapros . " and userfield like \"%$_GET[userfield]%\"";
}



if ($_GET["cd"]=="on")
if ($_GET["date_from"]){
    if ($zapros=="")
	$zapros=$zapros . " calldate BETWEEN STR_TO_DATE('$_GET[date_from] 00:00:00','%d-%m-%Y %H:%i:%s') AND STR_TO_DATE('$_GET[date_to] 23:59:59','%d-%m-%Y %H:%i:%s')";

    else
	$zapros=$zapros . " and calldate BETWEEN STR_TO_DATE('$_GET[date_from] 00:00:00','%d-%m-%Y %H:%i:%s') AND STR_TO_DATE('$_GET[date_to] 23:59:59','%d-%m-%Y %H:%i:%s')";
}




//echo $zapros;

 

if($zapros==""){
    $result = mysql_query("SELECT * FROM {$table} order by calldate desc limit 300");

}
else {
    $result = mysql_query("SELECT * FROM {$table} where $zapros ORDER BY calldate desc  limit 300");
//        echo "SELECT * FROM {$table} where $zapros ORDER BY calldate desc  limit 300";
}
if (!$result) {
    die("Query to show fields from table failed");
}

$fields_num = mysql_num_fields($result);
////////////////////////// 

echo "<table border='1' align=center width=100% class=\"sort\">";
echo "<thead><tr><td> № </td><td>Время звонка</td><td>Откуда</td><td>Куда</td><td>CallerID</td><td>User Fieled</td><td>Результат</td><td>Время разговора</td></tr></thead><tbody>";

$num=1;
while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
    $t= date( 'd.m, H:i', convdate("$row[calldate]"));
    if($num & 1)  { $color='#fff5ee'; } else { $color='white';}
    $d=$row["disposition"];
    if ($row["disposition"]== "ANSWERED") {
        $d="<a href=# onClick=window.open(\"play.php?id=$row[uniqueid]\",\"\",\"width=390,height=200\");>$row[disposition]</a>";
    }
    echo "<tr bgcolor=$color><td>$num</td><td>$t</td><td>$row[src]</td><td>$row[dst]</td><td>$row[clid]</td><td><a href=./index.php?userfield=$row[userfield]>$row[userfield]</a></td><td>$d</td><td>$row[billsec]</td></tr>";
    $num+=1;
}
echo "</tbody></table></body></html>";
mysql_free_result($result);
?>
