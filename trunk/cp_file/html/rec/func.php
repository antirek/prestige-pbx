<?php
require_once 'config.php';

function convdate($date_time_string) {
    date_default_timezone_set('Europe/Moscow');
    $dt_elements = explode(' ',$date_time_string);
    $date_elements = explode('-',$dt_elements[0]);
    $time_elements =  explode(':',$dt_elements[1]);
    $timestamp= mktime($time_elements[0],$time_elements[1],$time_elements[2],$date_elements[1],$date_elements[2],$date_elements[0]);
    return $timestamp;
}

function db_connect() {
    if (!mysql_connect($GLOBALS["db_host"], $GLOBALS["db_user"], $GLOBALS["db_pwd"]))
        die("Can't connect to database");
    if (!mysql_select_db($GLOBALS["database"]))
        die("Can't select database");
}
?>
