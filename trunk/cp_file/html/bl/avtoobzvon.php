<?php


require_once '/var/www/html/var.php';


$server="$SQLSERVER";
$user = "$ASTERISKUSER";
$password = "$SQLPWDASTERISK";
$db = "$ASTERISKDB";
$limit_pages="$LIMITONPAGE";

//$sound_dir = "/var/lib/asterisk/sounds/avtoobzvon";
$sound_dir = "/var/www/html/bl/avtoobzvon";

if (!mysql_connect($server, $user, $password)) {
 echo "Error connect to MySQL";
 exit;
}

mysql_select_db($db);



if  ( $_GET['download'] == 'yes'){
    $filename='items.csv';
    $query =  "SELECT * FROM masscall ";
    $result = mysql_query($query) or die("Query failed");
    echo "id;number;sound_file1;sound_file2;pitch_var;end_describe;wav;billsec;time_describe;ready\n";
    while (list ($id, $number, $sound_file1, $sound_file2, $pitch_var, $end_describe, $wav, $billsec, $time_describe, $ready) = mysql_fetch_row($result)) {               
	echo "$id;$number;$sound_file1;$sound_file2;$pitch_var;$end_describe;$wav;$billsec;$time_describe;$ready\n";
    }
    header('Content-type: application/vnd.ms-excel');
    header("Content-Disposition: attachment; filename=$filename");
    exit;
}

require_once("class.css");
require_once("sort.js");


if  ( isset($_POST[ready])) {
    $sql="update masscall set ready=\"$_POST[ready]\" where id=\"$_POST[id]\"";
    $query = mysql_query("$sql"); 

}

if  ( $_POST[add] == 'yes') {
    $sql="insert into masscall set number=\"$_POST[number]\", pitch_var=\"$_POST[pitch_var]\", ready=\"$_POST[ready]\"";
    $query = mysql_query("$sql"); 
}

if  ( $_GET[delrec] == 'yes') {
    $sql="delete from masscall where id=\"$_GET[id]\"";
    $query = mysql_query("$sql"); 
}

if  ( $_GET[analiz] == 'yes') {
    ignore_user_abort(1);
    set_time_limit(0);
    system('/var/lib/asterisk/agi-bin/flac_to_google.pl >/dev/null 2>&1 &');
}


if  ( $_GET[del] == 'yes') {
 echo " файл удален $sound_dir/$_GET[file]<br>";
 unlink("$sound_dir/$_GET[file]");
}


echo "<html><head>
<title>Asterisk Record Manager</title>
<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" >
<link href=\"css/main.css\" rel=\"stylesheet\" type=\"text/css\"> 
<script src=\"../rec/js/calendar_ru.js\"></script>
</head>";


echo "<form action=\"avtoobzvon.php\" method=\"post\">";

echo "<h4 align=center>для записи файла позвоните на номер 720</h4>";
echo "<h3 align=center>Выбираем звуковую запись для наших клиентов</h3> <table class=\"sort\" align=center width=80%>";
echo "<tr align=center  bgcolor='#ffefd5'><td>файл</td><td>начало фразы</td><td>конец фразы</td><td>удалить файл</td></tr>";
if (is_dir($sound_dir)) {
  if ($dh = opendir($sound_dir)) {
    while (($file = readdir($dh)) !== false) {
        if ($file != "." && $file != ".."  && $file != "records" ) {
	    echo "<tr align=center><td><a href=\"/bl/avtoobzvon/$file\">$file</a>" . filetype($dir . $file) . "</td><td> <input type=\"radio\" name=\"sound_file1\" value=\"$file\" > </td>
	    <td> <input type=\"radio\" name=\"sound_file2\" value=\"$file\" > </td><td><a href=\"avtoobzvon.php?del=yes&file=$file\">X</a></td></tr>\n";
    	}
    }
    closedir($dh);
  }
}
                                    
echo "<tr align=center><td>На сколько по времени растянуть процесс?</td><td colspan=3>";
?>

<select name="menu" size="1">
<option value="0.5">30 минут</option>
<option selected="selected" value="1">1 час</option>
<option value="2">2 часа</option>
<option value="3">3 часа</option>
<option value="4">4 часа</option>
<option value="5">5 часов</option>
<option value="6">6 часов</option>
<option value="7">7 часов</option>
<option value="8">8 часов</option>
<option value="9">9 часов</option>
<option value="10">10 часов</option>
<option value="11">11 часов</option>
<option value="12">12 часов</option>
</select>

<?php


echo "</td></tr>";
echo "<input type=\"hidden\" name=\"go\" value=\"yes\">";
echo "<tr align=center><td colspan=4><input type=\"submit\" value=\"Внести изменения\"> </td></tr></form>";

echo "</table>";

if ( $_POST['go'] == 'yes' ){
    $callcnt = $_POST[menu];
    echo "<h4 align=center>Воспроизвести файл <a href=\"/bl/avtoobzvon/$_POST[sound_file1]\">$_POST[sound_file1]</a>, затем задолженность, затем  <a href=\"/bl/avtoobzvon/$_POST[sound_file2]\">$_POST[sound_file2]</a>. Система будет работать в течении $_POST[menu] ч.<h4>";
    $sql="update masscall set sound_file1=\"$_POST[sound_file1]\", sound_file2=\"$_POST[sound_file2]\"";    
    $query = mysql_query("$sql"); 

}

    echo " <h3 align=center><a href=\"/bl/avtoobzvon.php\">Обновить</a><br>\n";
    echo " <a href=\"/bl/avtoobzvon.php?analiz=yes\">запустить анализатор речи</a><br>\n";
    echo " <a href=\"/bl/avtoobzvon.php?download=yes\">Скачать</a></h3>\n";

///////////////////////////////////////////////////////////    load csv ///////////////////////////////////////////////////////////    
echo "<h3 align=center>
    <form action=\"/bl/go.php\" method=\"post\" enctype=\"multipart/form-data\">
    <label>Выберите файл для импорта:</label>
    <input name=\"file\" type=\"file\" size=\"50\">
    <input name=\"Load\" type=\"submit\" value=\"Load\">
    </form> </h3>
";
///////////////////////////////////////////////////////////    ///////////////////////////////////////////////////////////    
    
    
if  ( isset($_GET['start'])){
	$start=$_GET['start'];
	$stop=$_GET['stop'];
    }else{
	$start=0;
	$stop=$limit_pages;
    }
                            
$cnt="select count(*) from masscall";
$req="select * from masscall limit $start,$limit_pages ";

$querycnt = mysql_query("$cnt"); 
$rowcnt = mysql_fetch_array($querycnt);
$pages= ceil($rowcnt[0]/$limit_pages)-1;
$cnt_pages=$pages+1;


echo "<h3 align=center><a href=go.php?callcnt=$callcnt>Запустить звонилку</a>";
echo "<br><a href=go.php?clear=all>очистить всю базу</a></h3>";

echo "<form action=\"avtoobzvon.php\" method=\"post\">";
echo "<table border=2 align=center class=\"sort\" width=80%><thead><tr>";
echo "<tr align=center bgcolor='#ffefd5'><td> № </td><td>номер </td><td>старт файл</td><td>задолженность</td><td>стоп файл</td><td>ответ</td><td>запись</td><td>длительность</td><td>дата</td><td>статус</td></thead><tbody>";
echo "<tr align=center bgcolor=grey><td>add</td><td><input type=\"text\" name=\"number\" size=\"11\" maxlength=\"11\" value=\"\"></td><td>$_POST[sound_file1]</td>
<td><input type=\"text\" name=\"pitch_var\" size=\"5\" maxlength=\"5\" value=\"\"><td colspan=6><select name=\"ready\" size=\"1\">
<option value=0>выключить</option>
<option selected=\"selected\" value=\"1\">включить</option>
<option value=2>дозвонились</option><br>
</select>
<input type=\"hidden\" name=\"add\" value=\"yes\">
<input type=\"submit\" value=\"добавить\">
</td></td></tr>";

echo "</form>";

$query = mysql_query("$req"); 
 
while ($row = mysql_fetch_array($query)){
    $a+=1;
      if($a & 1)  { $color='#fff5ee'; } else { $color='white';}
         echo "<tr align=center  bgcolor=$color><td>$a</td>";
         echo "<td>$row[number]</td><td>$row[sound_file1]</td><td>$row[pitch_var]</td><td>$row[sound_file2]</td><td>$row[end_describe]</td>";
         $wav=preg_replace("/\/var\/lib\/asterisk\/sounds\/avtoobzvon\/records\//",'',$row[wav]);
         echo "<td><a href=/bl/avtoobzvon/records/$wav-in.wav>слушать</a></td>";
         echo "<td>$row[billsec]</td><td>$row[time_describe]</td>";
          if($row[ready]==1) {
             echo "<td bgcolor=yellow>Статус: позвонить";
            }elseif ($row[ready]==0) {
             echo "<td bgcolor=red>Статус: не звонить";
            }elseif ($row[ready]==2) {
             echo "<td bgcolor=green>Статус: дозвонились";
    	    }else {
    	     echo "<td >Статус: ???";
	    }

         echo "<br><a href=/bl/avtoobzvon.php?delrec=yes&id=$row[id]>удалить запись</a>";
         echo "<FORM METHOD=\"POST\" ACTION=\"/bl/avtoobzvon.php\">
	        <select name=\"ready\" size=\"1\">
		<option value=\"0\">не звонить</option>
		<option selected=\"selected\" value=\"1\">позвонить</option>
		<option value=\"2\">дозвонились</option>
		</select>
		<input name=\"id\" type=\"hidden\" value=\"$row[id]\">
		<input type=\"submit\" value=\"ok\">
		</FORM>";
         echo " </td></tr>";
       }
echo "</tbody></table>";    
echo "</form>";






mysql_close();

?>

