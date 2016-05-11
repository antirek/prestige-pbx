<?php

require_once '/var/www/html/var.php';

$server="$SQLSERVER";
$user = "$ASTERISKUSER";
$password = "$SQLPWDASTERISK";
$db = "$ASTERISKDB";
$limit_pages="$LIMITONPAGE";

if (!mysql_connect($server, $user, $password)) {
 echo "Error connect to MySQL";
 exit;
}

mysql_select_db($db);

require_once("class.css");
require_once("sort.js");


if ( $_POST['search'] == 'yes' ){
     $req="select * from codes where $_POST[number] BETWEEN concat(7,code_abcdef)*10000000+code_from AND concat(7,code_abcdef)*10000000+code_to";
     $query = mysql_query("$req"); 
     $row = mysql_fetch_array($query);
//     echo "$req";
      if (isset($row[0])) { 
        echo "<h3 align=center> $_POST[number]<br>$row[region]: $row[operator]  <br></h3>"; 
        } else {
         echo "<h2 align=center>Номер не найден </h2>";
        }
     echo "<br><h4 align=center><a href=\"codes.php\">На главную страницу</a></h4>";
     exit;
} elseif( isset($_POST['operator']) ){
       echo "<br><h4 align=center><a href=\"codes.php\">На главную страницу</a></h4>";
	$operator=$_POST['operator'];
	echo "<table border=2 align=center class=\"sort\" width=80%><thead><tr>";
	echo "<tr align=center bgcolor='#ffefd5'><td> № </td><td>code_id</td><td>префикс</td><td>от</td><td>до</td><td>кол-во номеров</td><td>Оператор</td><td>Регион</td></thead><tbody>";
	$req="select * from codes where operator like \"%$operator%\" limit 1000";
	$query = mysql_query("$req"); 
	while ($row = mysql_fetch_array($query)){
	    $a+=1;
	    if($a & 1)  { $color='#fff5ee'; } else { $color='white';}
	    echo "<tr align=center  bgcolor=$color><td>$a</td><td>$row[code_id]</td><td>$row[code_abcdef]</td><td>$row[code_from]</td><td>$row[code_to]</td><td>$row[code_volume]</td><td>$row[operator]</td><td>$row[region]</td></tr>";
	    }
	echo "</tbody></table>";
     exit;
} elseif( isset($_POST['region']) ){
       echo "<br><h4 align=center><a href=\"codes.php\">На главную страницу</a></h4>";
	$region=$_POST['region'];
	echo "<table border=2 align=center class=\"sort\" width=80%><thead><tr>";
	echo "<tr align=center bgcolor='#ffefd5'><td> № </td><td>code_id</td><td>префикс</td><td>от</td><td>до</td><td>кол-во номеров</td><td>Оператор</td><td>Регион</td></thead><tbody>";
	$req="select * from codes where region like \"%$region%\" limit 1000";
	$query = mysql_query("$req"); 
	while ($row = mysql_fetch_array($query)){
	    $a+=1;
	    if($a & 1)  { $color='#fff5ee'; } else { $color='white';}
	    echo "<tr align=center  bgcolor=$color><td>$a</td><td>$row[code_id]</td><td>$row[code_abcdef]</td><td>$row[code_from]</td><td>$row[code_to]</td><td>$row[code_volume]</td><td>$row[operator]</td><td>$row[region]</td></tr>";
	    }
	echo "</tbody></table>";
     exit;
}


if  ( isset($_GET['start'])){
     $start=$_GET['start'];
     $stop=$_GET['stop'];
    }else{
     $start=0;
     $stop=$limit_pages;
    }

$cnt="select count(*) from codes";
$req="select * from codes limit $start,$limit_pages ";

$querycnt = mysql_query("$cnt"); 
$rowcnt = mysql_fetch_array($querycnt);
$pages= ceil($rowcnt[0]/$limit_pages)-1;
$cnt_pages=$pages+1;

echo "<h3 align=center>Коды России</h3>";

echo "<table align=center border=0 width=800>";
echo "<tr><td align=center>Поиск номеру</td><td align=center>Поиск по оператору</td><td align=center>Поиск по региону</td></tr>";

echo "<tr><td align=center><FORM METHOD=\"POST\" ACTION=\"codes.php\">";
echo "<input type=\"text\" name=\"number\" size=\"11\" maxlength=\"11\" value=\"74991234567\">";
echo "<input name=\"search\" type=\"hidden\" value=\"yes\">";
echo "<input name=\"Submit\" type=submit value=\"найти\">";
echo "</FORM></td>";

echo "<td align=center><FORM METHOD=\"POST\" ACTION=\"codes.php\">";
echo "<input type=\"text\" name=\"operator\" size=\"21\" maxlength=\"21\" value=\"Мегафон\">";
//echo "<input name=\"operator\" type=\"hidden\" value=\"yes\">";
echo "<input name=\"Submit\" type=submit value=\"найти\">";
echo "</FORM></td>";

echo "<td align=center><FORM METHOD=\"POST\" ACTION=\"codes.php\">";
echo "<input type=\"text\" name=\"region\" size=\"21\" maxlength=\"21\" value=\"Новгород\">";
//echo "<input name=\"operator\" type=\"hidden\" value=\"yes\">";
echo "<input name=\"Submit\" type=submit value=\"найти\">";
echo "</FORM></td>";

echo "</tr>";
echo "</table>";


echo "<h4 align=center><a href=\"codes.php\">Обновить страницу</a></h4>";

echo "<table border=2 align=center class=\"sort\" width=80%><thead><tr>";
echo "<tr align=center bgcolor='#ffefd5'><td> № </td><td>code_id</td><td>префикс</td><td>от</td><td>до</td><td>кол-во номеров</td><td>Оператор</td><td>Регион</td></thead><tbody>";

$query = mysql_query("$req"); 
while ($row = mysql_fetch_array($query)){
    $a+=1;
    if($a & 1)  { $color='#fff5ee'; } else { $color='white';}
    echo "<tr align=center  bgcolor=$color><td>$a</td><td>$row[code_id]</td><td>$row[code_abcdef]</td><td>$row[code_from]</td><td>$row[code_to]</td><td>$row[code_volume]</td><td>$row[operator]</td><td>$row[region]</td></tr>";
}
echo "</tbody></table>";

echo "<h3 align=center>Общее количиство страниц: $cnt_pages  </h3> <h5 align=center>";


for ( $i=0; $i<=$pages; $i++) {
   $start=$i*$limit_pages;
   $stop=$start+$limit_pages;
   $cnt2_pages=$i+1;
  echo " <a href=\"codes.php?start=$start\" target=ablank>$cnt2_pages</a> ";
 
}
echo "</h5>";






mysql_close();

?>

