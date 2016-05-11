<?php


require_once '/var/www/html/var.php';

$server="$SQLSERVER";
$user = "$ASTERISKUSER";
$password = "$SQLPWDASTERISK";
$db = "$ASTERISKDB";
$limit_pages="$LIMITONPAGE";
$sound_dir = "/var/www/html/bl/avtoobzvon";

if (!mysql_connect($server, $user, $password)) {
 echo "Error connect to MySQL";
 exit;
}

mysql_select_db($db);

require_once("class.css");



echo "<html><head>
<title>Asterisk Record Manager</title>
<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" >
<link href=\"css/main.css\" rel=\"stylesheet\" type=\"text/css\"> 
</head>";

    
/////////////////////////////////////// if clear db  /////////////////////////////////
        
if  ( $_GET['clear'] == "all"){
    echo "Вы уверены что хотите почистить базу???<br><a href=go.php?clear=all_yes>ДА</a> &nbsp &nbsp &nbsp | &nbsp &nbsp &nbsp  <a href=/bl/avtoobzvon.php>НЕТ, вернуться на главную</a>";
    exit;
} elseif  ( $_GET['clear'] == "all_yes"){
    $sql="delete from masscall";
    $query = mysql_query("$sql");
    echo "Мой командир, база очищена!<br><a href=/bl/avtoobzvon.php>домой</a>";
    exit;
}




/////////////////////////////////////// if load csv file  /////////////////////////////////

if  ( isset($_FILES["file"]["name"]) ) {

$url = $_FILES["file"]["name"];

echo 'Запись из файла: <b>'.$url.'</b><br>'; // Имя файла

$file_exp = explode(".", $url);
if ($file_exp[1] != "csv") die ("Неправильный формат файла."); // Допустимы файлы только с расширением csv


if (file_exists("/tmp/" . $url)) { 
            echo $url . " already exists. "; 
        } else   { 
            move_uploaded_file($_FILES["file"]["tmp_name"], "/tmp/" . $url); 
            echo "Stored in: " . "/tmp/" . $url . "<br>"; 
        } 


$handle = fopen("/tmp/" .  $url, "r"); // Файл csv необходимо положить в папку tmp
while ($data =  fgetcsv($handle, 1000, ";")) { 
        $sql = mysql_query ("INSERT INTO masscall (number, pitch_var, ready) value ('$data[0]','$data[1]','$data[2]' )");
//      echo "INSERT INTO masscall number, pitch_var, ready value ('$data[0]','$data[1]','$data[2]' )<br>";
        $row++;
    }
echo "Загрузка прошла успешно. Всего записей $row<br>"; // Всё что в запросе, меняем на свои данные
fclose ($handle);

// Ниже на экран выводятся спарсенные значения //
echo '<br><br><b>Все записи:</b><br>';
echo '<div style="font-size:10px">';
$row = 1;

fclose ($fp);
unlink("/tmp/" .  $url);
echo "</div>";
echo "<a href=/bl/avtoobzvon.php>домой</a>";
exit;

}


//////////////////////////////////////////////////////////////////////////////////////////////



$callcnt=$_GET['callcnt'];

$req="select count(*) from masscall where ready=1";
$query = mysql_query("$req"); 
$total_item=mysql_fetch_row($query);
$delta=$callcnt*3600/$total_item[0];

echo "В течении $callcnt ч. будет сделано $total_item[0] звонка <br> Каждый последующий звонок будет совершен через $delta с.<br>";

//exit;

$req="select * from masscall where ready=1";
$query = mysql_query("$req"); 
 
while ($row = mysql_fetch_array($query)){
$file1=substr($row[sound_file1],0,-4);
$file2=substr($row[sound_file2],0,-4);

 $fp = fopen("/tmp/$row[number].call", "w");
 $text="Channel:Local/$row[number]@avtoobzvon
MaxRetries: 10
RetryTime: 120
WaitTime: 40
Context: avtoobzvon_bridge
Extension: s
Priority: 1
set: id=$row[id]
set: number=$row[number]
set: debt=$row[pitch_var]
set: file1=avtoobzvon/$file1
set: file2=avtoobzvon/$file2
";
 fwrite($fp, $text);
 fclose($fp);
 $time1=time()+$delta*$i;
 $time2=date("ymdHi.s",$time1);
 system ("touch -t $time2 /tmp/$row[number].call");
// echo "touch -t $time2 /tmp/$row[number].call<br>";
 system("chmod 666 /tmp/$row[number].call");
 system("mv -f /tmp/$row[number].call /var/spool/asterisk/outgoing/");
//copy("/tmp/$row[number].call", "/var/spool/asterisk/outgoing/$row[number].call");
//unlink("/tmp/$row[number].call");
 $i=$i+1;
}


echo "<a href=/bl/avtoobzvon.php>домой</a>";
mysql_close();

?>

