<?php


function get_peers() {
    require '/var/www/html/var.php';
    $socket = fsockopen("127.0.0.1","5038", $errno, $errstr, 10);
    if (!$socket){
        echo "$errstr ($errno)\n";
    }
    else{
        fputs($socket, "Action: Login\r\n");
        fputs($socket, "UserName: $AMIUSER\r\n");
        fputs($socket, "Secret: $AMIPWD\r\n\r\n");

        fputs($socket, "Action: SIPpeers\r\n\r\n");

        fputs($socket, "Action: Logoff\r\n\r\n");
        while (!feof($socket)){
            $data .= fgets($socket);
        }
        fclose($socket);
    }
    return $data;
}

function get_peer($peername) {
    require '/var/www/html/var.php';
    $socket = fsockopen("127.0.0.1","5038", $errno, $errstr, 10);
    if (!$socket){
        echo "$errstr ($errno)\n";
    }
    else{

        fputs($socket, "Action: Login\r\n");
        fputs($socket, "UserName: $AMIUSER\r\n");
        fputs($socket, "Secret: $AMIPWD\r\n\r\n");

        fputs($socket, "Action: SIPshowpeer\r\n");
        fputs($socket, "Peer: {$peername}\r\n\r\n");

        fputs($socket, "Action: Logoff\r\n\r\n");
        while (!feof($socket)){
            $data .= fgets($socket);
        }
        fclose($socket);
    }
    return $data;
}

$list = get_peers();
$list = explode("\r\n", $list); 
$obj = array();
$obj2 = array();
$count = 0;
$count2 = 0;

foreach ($list as $str) {
    if ($str != "" && substr_count($str, ":")) {
        $tmp = explode(": ", $str);
        $tmp_obj->$tmp[0]=trim($tmp[1]);
    }
    else {
        if ($tmp_obj->Event && $tmp_obj->Event == "PeerEntry") {
            $obj[$count] = $tmp_obj;
            unset($tmp_obj);
            $count++;
        }
    }
}

foreach ($obj as $item) {
    $peername = $item->ObjectName;
    $data = get_peer($peername);
    $data = explode("\r\n", $data); 
    foreach ($data as $str) {
        if ($str != "" && substr_count($str, ":")) {
            $tmp = explode(": ", $str);
            if ($tmp[0]=="ChanVariable") {
                $ttt = explode("=", $tmp[1]);
                $var = $tmp[0]."_".$ttt[0];
                $tmp_obj->$var = $ttt[1];
            }
            else {    
                $tmp_obj->$tmp[0]=trim($tmp[1]);
            }
        }
        else {
            if ($tmp_obj->ChanObjectType && $tmp_obj->ChanObjectType == "peer") {
                $obj2[$count2] = $tmp_obj;
                unset($tmp_obj);
                $count2++;
            }
        }
    }
}

?>

<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title></title>
        <link rel="stylesheet" href="./asterisk_sip_monitoring/jquery.dataTables.css" type="text/css" />
        <link rel="stylesheet" href="./asterisk_sip_monitoring/jquery-ui-1.8.4.custom.css" type="text/css" />
        <style type="text/css">
            table.dataTable thead th {
                padding: 3px 5px 3px 5px;
            }
        </style>
        <script type="text/javascript" src="./asterisk_sip_monitoring/jquery.js"></script>
        <script type="text/javascript" src="./asterisk_sip_monitoring/jquery.dataTables.min.js"></script>
        <script type="text/javascript" src="./asterisk_sip_monitoring/dataTables.scroller.min.js"></script>
        <script type="text/javascript">
/* Define two custom functions (asc and desc) for string sorting */
jQuery.fn.dataTableExt.oSort['string-case-asc']  = function(x,y) {
    return ((x < y) ? -1 : ((x > y) ?  1 : 0));
};
 
jQuery.fn.dataTableExt.oSort['string-case-desc'] = function(x,y) {
    return ((x < y) ?  1 : ((x > y) ? -1 : 0));
};
 
$(document).ready(function() {
    var calcDataTableHeight = function() {
        return $(window).height()-140;
    };
    
    /* Build the DataTable with third column using our custom sort functions */
    var oTable = $('#data').dataTable({
        "sScrollY": calcDataTableHeight(),
	"sDom": '<"H"Cfrl>tS<"F"ip>',
	"bDeferRender": true,
        "sScrollX": "100%",
        "bPaginate": false,
        "bScrollCollapse": true,
        "bJQueryUI": true
    });
    
    $(window).resize(function () {
    var oSettings = oTable.fnSettings();
    oSettings.oScroll.sY = calcDataTableHeight(); 
    oTable.fnDraw();
    });
} );
        
        </script>
    </head>
    <body>

        <?php
        
        echo '<table cellpadding="0" cellspacing="0" border="0" class="display" id="data">';
        echo '<thead><tr>
            <th>№ п/п</th>
            <th>ObjectName</th>
            <th>IPaddress:IPport</th>
            <th>Callerid</th>
            <th>SIP-Useragent</th>
            <th>Status</th>
            <th>ChanVariable_city</th>
            <th>ChanVariable_ert</th>
            <th>ChanVariable_mn</th>
            <th>ChanVariable_mg</th>
            <th>ChanVariable_cells</th>
            <th>ChanVariable_mg7</th>
            <th>ChanVariable_cells7</th>
            <th>ChanVariable_canque</th>
            <th>ChanVariable_rec</th>
            <th>VoiceMailbox</th>
            <th>CodecOrder</th>
            <th>SecretExist</th>
            <th>MD5SecretExist</th>
            <th>Context</th>
            <th>Callgroup</th>
            <th>Pickupgroup</th>
            <th>TransferMode</th>
            <th>Maxforwards</th>
            <th>Call-limit</th>
            <th>Busy-level</th>
            <th>MaxCallBR</th>
            <th>Dynamic</th>
            <th>SIP-CanReinvite</th>
            <th>SIP-PromiscRedir</th>
            <th>SIP-VideoSupport</th>
            <th>SIP-TextSupport</th>
            <th>SIP-T.38Support</th>
            <th>SIP-DTMFmode</th>
            </tr></thead>';
        echo '<tbody>';
        
        $n =0;
        foreach ($obj2 as $item) {
            $n++;
            echo "<tr><td>{$n}</td><td>{$item->ObjectName}</td><td>{$item->{'Address-IP'}}:{$item->{'Address-Port'}}</td><td>{$item->Callerid}</td><td>{$item->{'SIP-Useragent'}}</td><td>{$item->Status}</td><td>{$item->ChanVariable_city}</td><td>{$item->ChanVariable_ert}</td><td>{$item->ChanVariable_mn}</td><td>{$item->ChanVariable_mg}</td><td>{$item->ChanVariable_cells}</td><td>{$item->ChanVariable_mg7}</td><td>{$item->ChanVariable_cells7}</td><td>{$item->ChanVariable_canque}</td><td>{$item->ChanVariable_rec}</td><td>{$item->VoiceMailbox}</td><td>{$item->CodecOrder}</td><td>{$item->SecretExist}</td><td>{$item->MD5SecretExist}</td><td>{$item->Context}</td><td>{$item->Callgroup}</td><td>{$item->Pickupgroup}</td><td>{$item->TransferMode}</td><td>{$item->Maxforwards}</td><td>{$item->{'Call-limit'}}</td><td>{$item->{'Busy-level'}}</td><td>{$item->MaxCallBR}</td><td>{$item->Dynamic}</td><td>{$item->{'SIP-CanReinvite'}}</td><td>{$item->{'SIP-PromiscRedir'}}</td><td>{$item->{'SIP-VideoSupport'}}</td><td>{$item->{'SIP-TextSupport'}}</td><td>{$item->{'SIP-T.38Support'}}</td><td>{$item->{'SIP-DTMFmode'}}</td></tr>";
        }
        echo '</tbody>';
        echo "</table>";
        ?>

    </body>
</html>