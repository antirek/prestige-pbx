<html>
<head>
<title>Call info</title>
<link href="css/main.css" rel="stylesheet" type="text/css"> 
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" /> 
</head>
<body>
<?php
require_once 'config.php';
$id=$_GET['id'];

if($id)
{
    $id = htmlspecialchars((stripslashes($id)), ENT_QUOTES);
    $id = str_replace("/","",$id);
    $id = str_replace("`","",$id);
}
else
{
    echo "No id!";
    exit(0);
}

if (!mysql_connect($db_host, $db_user, $db_pwd))
    die("Can't connect to database");

if (!mysql_select_db($database))
    die("Can't select database");

$result = mysql_query("SELECT * FROM {$table} where uniqueid=$id;");
if (!$result) {
    die("Query to show fields from table failed");
}

$fields_num = mysql_num_fields($result);
if ($fields_num<1) {
    echo "Error!";
    exit(0);
}

$row = mysql_fetch_array($result, MYSQL_ASSOC);

echo "<table>
<tr><td>Дата:</td><td>$row[calldate]</td></tr>
<tr><td>Откуда:</td><td>$row[src]</td></tr>
<tr><td>Куда:</td><td>$row[dst]</td></tr>
<tr><td>Время разговора:</td><td>$row[billsec]</td></tr>
<tr><td>MP3:</td><td><a href=\"$url/mp3/$id.mp3\">Скачать</a></td></tr>
<tr><td>Прослушать:</td>
";
mysql_free_result($result);
?>
<td>
<object type="application/x-shockwave-flash" data="<?php echo $url;?>/rec/media/player_mp3_maxi.swf" width="200" height="20">

    <param name="movie" value="<?php echo $url;?>/rec/media/player_mp3_maxi.swf" />
    <param name="bgcolor" value="#000000" />
    <param name="FlashVars" value="mp3=<?php echo "$url/mp3/$id.mp3"?>" />
</object>
</td><tr>
</body>
</html>
