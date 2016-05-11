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


if  ( $_GET['del'] == 'yes' ){
     $req="delete from bl where number=$_GET[number] ";
     mysql_query("$req"); 
    }elseif ( $_POST['add'] == 'yes' ){
     $req="insert into bl set number=$_POST[number], date=now()";
     mysql_query("$req");
    }elseif ( $_POST['search'] == 'yes' ){
     $req="select * from bl where  number like \"%$_POST[number]\"";
     $query = mysql_query("$req"); 
     $row = mysql_fetch_array($query);
      if (isset($row[0])) { 
        echo "<h3 align=center>номер: $row[0] <br>добавлен: $row[1] <br><br><a href=report.php?del=yes&number=$row[0]>удалить номер с черного списка</a></h3>"; 
        } else {
         echo "<h2 align=center>Номер в черном списке отстутствует</h2>";
        }
     echo "<br><h4 align=center><a href=\"report.php\">На главную страницу</a></h4>";
     exit;
}


if  ( isset($_GET['start'])){
     $start=$_GET['start'];
     $stop=$_GET['stop'];
    }else{
     $start=0;
     $stop=$limit_pages;
    }

$cnt="select count(*) from bl";
$req="select * from bl limit $start,$limit_pages ";


$querycnt = mysql_query("$cnt"); 
$rowcnt = mysql_fetch_array($querycnt);
$pages= ceil($rowcnt[0]/$limit_pages)-1;
$cnt_pages=$pages+1;

echo "<h3 align=center>Черный список</h3>";

echo "<table align=center border=0 width=800><tr><td>";

echo "<table align=center border=0 width=400>";
echo "<tr><td align=center>Добавить номер в черный список</td></tr>";
echo "<tr><td align=center><FORM METHOD=\"POST\" ACTION=\"report.php\">";
echo "<input type=\"text\" name=\"number\" size=\"11\" maxlength=\"11\" value=\"79110000000\">";
echo "<input name=\"add\" type=\"hidden\" value=\"yes\">";
echo "<input name=\"Submit\" type=submit value=\"добавить\">";
echo "</FORM></td></tr>";
echo "</table>";

echo "</td><td>";

echo "<table align=center border=0 width=400>";
echo "<tr><td align=center>Поиск номера</td></tr>";
echo "<tr><td align=center><FORM METHOD=\"POST\" ACTION=\"report.php\">";
echo "<input type=\"text\" name=\"number\" size=\"11\" maxlength=\"11\" value=\"79110000000\">";
echo "<input name=\"search\" type=\"hidden\" value=\"yes\">";
echo "<input name=\"Submit\" type=submit value=\"найти\">";
echo "</FORM></td></tr>";
echo "</table>";

echo "</td></td></table>";

echo "<h4 align=center><a href=\"report.php\">Обновить страницу</a></h4>";

echo "<table border=2 align=center class=\"sort\" width=80%><thead><tr>";
echo "<tr align=center bgcolor='#ffefd5'><td> № </td><td>номер</td><td>дата</td><td>удалить запись</td></thead><tbody>";



 $query = mysql_query("$req"); 

 while ($row = mysql_fetch_array($query)){
    $a+=1;
    if($a & 1)  { $color='#fff5ee'; } else { $color='white';}
    echo "<tr align=center  bgcolor=$color><td>$a</td><td>$row[0]</td><td>$row[1]</td><td><a href=report.php?del=yes&number=$row[0]>X</a></td></tr>";

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

