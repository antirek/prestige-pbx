<?php
//////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////
//Source of IPline DialFox, The Open Source Asterisk Phone Directory
//Copyright (C) 2008 A-Enterprise GmbH Switzerland - Claude Fanac 
//
//This program is free software; you can redistribute it and/or
//modify it under the terms of the GNU General Public License
//as published by the Free Software Foundation; either version 2
//of the License, or (at your option) any later version.
//
//This program is distributed in the hope that it will be useful,
//but WITHOUT ANY WARRANTY; without even the implied warranty of
//MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//GNU General Public License for more details. 
//////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////



require("config.php");



if($E){header("location:new_number.php?E=$E&P=$P&X=$X");exit;}

if(!$limit)$limit=100;

if($LL)
{
$result = mysql_query("select * from pbook where id='$LL'",$conn)or die (mysql_error());
$set = mysql_fetch_array($result);
$del=$set["calld"];
mysql_query ("delete from pbook where id = '$LL'",$conn)or die (mysql_error());
require("cli.php");
}

$search=$d_name;
if(trim($search) > "")
{
	if(!$bemerkung)$bemerkung=" ";
	{
	$result = mysql_query("select * from pbook where name LIKE '%$search%' OR calld LIKE '%$search%' OR bemerkung LIKE '%$search%' order by name LIMIT 1000",$conn)or die (mysql_error());
	$result2 = mysql_query("select * from users where $local_where AND name LIKE '%$search%' order by name LIMIT $limit",$conn)or die (mysql_error());
	}
}

if($REGISTER>"")
{
$search=$REGISTER;

$RK=strtolower($REGISTER);
$RG=$REGISTER;

	if(!$bemerkung)$bemerkung="&nbsp;";
	{
	$result = mysql_query("select * from pbook where name LIKE '$RG%' OR name LIKE '$RK%' order by name LIMIT 1000",$conn)or die (mysql_error());
	
	
	$result4 = mysql_query("select * from users",$conn)or die (mysql_error()); 
	$anz4= mysql_num_rows($result4);
	
		if($anz4>$anz_numbers) // nur suchen, wenn gen�gend nummern vorhanden sind !!
		{
		$result2 = mysql_query("select * from users where $local_where name LIKE '$RG%' OR name LIKE '$RK%' order by name LIMIT 1000",$conn)or die (mysql_error());
	    }
		else
	    {
		$result2 = mysql_query("select * from users where $local_where order by name",$conn)or die (mysql_error());
		}

	}
}


if(!$search)
{
$result = mysql_query("select * from pbook  order by name LIMIT $limit",$conn)or die (mysql_error());
$result2 = mysql_query("select * from users where $local_where order by name LIMIT $limit",$conn)or die (mysql_error());
}

WHILE($set = mysql_fetch_array($result))
{

$call01="";
$call02="";
$call03="";
$call04="";
$call05="";

$name=$set["name"];
$call=$set["calld"];
$bemerkung=$set["bemerkung"];
$ID=$set["id"];

	// aufl�sung 9-stellige nummern
	if(strlen($call)==9)
	{
	$call01=substr($call,0,2);
	$call02=substr($call,2,3);
	$call03=substr($call,5,2);
	$call04=substr($call,7,2);
	$call05= "$call01 <b>$call02 $call03 $call04</b>";
	}
	
	// aufl�sung 10-stellige nummern
	if(strlen($call)==10)
	{
	$call01=substr($call,0,3);
	$call02=substr($call,3,3);
	$call03=substr($call,6,2);
	$call04=substr($call,8,2);
	$call05= "$call01 <b>$call02 $call03 $call04</b>";
	}

	// internationale Nummern
	if(strlen($call)>10)
	{
	$call05= $call;
	}

if(!$call05)$call05=$call;

$call=$X.$call;


$callto="<A HREF=\"manager.php?call=$call&ext=$P\" TARGET=\"fen\" onclick=\"window.open('', 'fen','width=400, height=250, toolbar=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no')\">$call05</A>";


if($P>0)$href=$callto;
else $href=$call05;


if($_SESSION['login']==1)
$canedit="
   <td  nowrap><a href=\"book.php?E=$ID&P=$P&X=$X\"><img src=\"images/edit.gif\" border=\"0\" alt=\"edit\"></a></td>
   <td  nowrap><a href=\"book.php?L=$ID&P=$P&X=$X\"><img src=\"images/def_delete.gif\" border=\"0\" alt=\"delete\"></a></td>
";

else

$canedit="
   <td  bgcolor=\"#CCCC33\" nowrap>&nbsp;</td>
   <td  bgcolor=\"#CCCC33\" nowrap>&nbsp;</td>
";


$bemerkung=nl2br(trim($bemerkung));

$i=$i. "
<tr onMouseOver=\"this.style.backgroundColor='#CCFF99';\" onMouseOut=\"this.style.backgroundColor='#FFFFFF';\" bgcolor=\"#FFFFFF\">
 <td ><b></b>$name</b></td>
 <td  nowrap>$href</td>
 <td >$bemerkung</td>
 $canedit
</tr>
";
}   // und jetzt Variable in Tabelle speichern




///////// local book ///////////////////////////////////////////////////////

// list all sip peers
putenv("PATH=/bin:/sbin:/usr/bin:/usr/sbin:/usr/local/bin:/usr/local/sbin");	
exec ("asterisk -rx \"sip show peers\" | awk '{print $1 \": \" $6}'  ",$peers); // alles l�schen



if($local_freepbx==1)

{
	WHILE($set2 = mysql_fetch_array($result2))
	{
	
	$name="";
	$extension="";
	$name=$set2["name"];
	$call=$set2["extension"];
	
	// search sip status OK
	$sip_color="no";
	foreach($peers as $key => $val)
	{
		if ($key>0)
		{
		$arr_val=explode(":",trim($val));
		$arr_exten=explode("/",trim($arr_val[0]));
	    $sip_exten=trim($arr_exten[0]);
		$sip_stat=trim($arr_val[1]);
		if($sip_exten==$call AND $sip_stat=="OK")    $sip_color="ok";
		
		


		
		
		}
	}
	
$sip_stat_tab="<img src=\"images/$sip_color.gif\">";
			

	
	if($call>1)
	
	
	$callto="<A HREF=\"manager.php?call=$call&ext=$P\" TARGET=\"fen\" onclick=\"window.open('', 'fen','width=400, height=250, toolbar=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no')\">$call</A>";
	
	if($P>0)$href=$callto;
	else $href=$call;
	$sip_color="red";
	$i2=$i2. "
	<tr onMouseOver=\"this.style.backgroundColor='#CCFF99';\" onMouseOut=\"this.style.backgroundColor='#FFFFFF';\" bgcolor=\"#FFFFFF\">
	 <td  nowrap><b></b>$name</b></td>
	 <td  nowrap>$sip_stat_tab &nbsp $href </td>
	</tr>
	";
	
	}  
} // end of if($local_freepbx==1)
else
{

// if no freepbx get astersik sip peers :


	foreach($peers as $keys => $vals)
	{
		if ($keys>0)
		{
		$sip_color="no";
		$arr_val=explode(":",trim($vals));
		$arr_exten=explode("/",trim($arr_val[0]));
		$name=$arr_exten[1];
	    $call=trim($arr_exten[0]); 
		$sip_stat=trim($arr_val[1]);
		if($sip_stat=="OK") $sip_color="ok";

		$sip_stat_tab="<img src=\"images/$sip_color.gif\">";

		
			$callto="<A HREF=\"manager.php?call=$call&ext=$P\" TARGET=\"fen\" onclick=\"window.open('', 'fen','width=400, height=250, toolbar=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no')\">$call</A>";

	if($P>0)$href=$callto;
	else $href=$call;

	$i2=$i2. "
	<tr onMouseOver=\"this.style.backgroundColor='#CCFF99';\" onMouseOut=\"this.style.backgroundColor='#FFFFFF';\" bgcolor=\"#FFFFFF\">
	 <td  nowrap><b></b>$name</b></td>
	 <td  nowrap>$sip_stat_tab &nbsp $href</td>
	</tr>
	";
		
		
		} // end if ($key>0)
	} // end foreach($peers as $key => $val)



} // end of else if($local_freepbx==1)


 /////// end of local  /////////////////////////////////////////////////////////////////////////////// 

if(!$i2)
$i2="
<tr bgcolor=\"#FFFFFF\">
 <td >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
 <td >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
</tr>

<tr bgcolor=\"#FFFFFF\">
 <td >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
 <td >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
</tr>

<tr bgcolor=\"#FFFFFF\">
 <td >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
 <td >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
</tr>

";


if(!$i)
$i="
<tr bgcolor=\"#FFFFFF\">
 <td >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
 <td >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
</tr>

<tr bgcolor=\"#FFFFFF\">
 <td >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
 <td >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
</tr>

<tr bgcolor=\"#FFFFFF\">
 <td >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
 <td >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
</tr>

";

$GLS="book.php?P=$P&X=$X&REGISTER";

require ("head_link.php");
?>
  <br>

<form name="searchb" method="post" action="book.php">
<input type=HIDDEN name=P value="<?php echo $P?>">
<input type=HIDDEN name=X value="<?php echo $X?>">

<?php if($L){
$result = mysql_query("select * from pbook where id='$L'",$conn)or die (mysql_error());
$set = mysql_fetch_array($result);
$del=$set["calld"];
?>      
  <br>
  <br>
  <table width="1" border="0" cellspacing="2" cellpadding="10" bgcolor="#FF6633" align="center">
    <tr bgcolor="#FFFFFF"> 
      <td nowrap> 
        <table width="1" border="0" cellspacing="0" cellpadding="10">
          <tr> 
            <td nowrap><a href="<?php echo "book.php?LL=$L&P=$P&X=$X";?>">NR<b>&nbsp;&nbsp;<?php echo $del?></b></a></td>
            <td colspan="2" nowrap><a href="<?php echo "book.php?LL=$L&P=$P&X=$X";?>"><img src="images/ok_delete.gif" border="0"></a></td>
          </tr>
        </table>
      </td>
      <td nowrap>
        <table width="1" border="0" cellspacing="0" cellpadding="10">
          <tr> 
            <td nowrap colspan="3"><a href="<?php echo "book.php?P=$P&X=$X";?>"><b><?php echo $book_1;?></b></a></td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
  <br>
  <br>
  <br>
  <br>
 <br>
  <?php }?>


<table border="0" cellspacing="0" cellpadding="0">
  <tr align="left" valign="top" bgcolor="#FFFFFF"> 
    <td colspan="3" nowrap> 
        <table width="1" border="0" cellspacing="0" cellpadding="2">
          <tr bgcolor="#CCCCCC"> 
            <td> 
              <table border="0" cellspacing="0" cellpadding="0">
                <tr> 
                  <td width="63" height="0"> 
                    <input type="text" name="d_name" size="12">
                  </td>
                  <td width="8" height="0"> 
                    <input type="image" src="images/search.gif" value="suche" alt="Submit" name="image">
                  </td>
                </tr>
              </table>
            </td>
            <td> <b><a href="book.php<?php  echo "?P=$P&X=$X";?>">&nbsp;All&nbsp;</a> 
              </b></td>
            <td><b><a href="<?php echo $GLS;?>=A">&nbsp;A&nbsp;</a></b></td>
            <td><b><a href="<?php echo $GLS;?>=B">&nbsp;B&nbsp;</a></b></td>
            <td><b><a href="<?php echo $GLS;?>=C">&nbsp;C&nbsp;</a></b></td>
            <td><b><a href="<?php echo $GLS;?>=D">&nbsp;D&nbsp;</a></b></td>
            <td><b><a href="<?php echo $GLS;?>=E">&nbsp;E&nbsp;</a></b></td>
            <td><b><a href="<?php echo $GLS;?>=F">&nbsp;F&nbsp;</a></b></td>
            <td><b><a href="<?php echo $GLS;?>=G">&nbsp;G&nbsp;</a></b></td>
            <td><b><a href="<?php echo $GLS;?>=H">&nbsp;H&nbsp;</a></b></td>
            <td><b><a href="<?php echo $GLS;?>=I">&nbsp;I&nbsp;</a></b></td>
            <td><b><a href="<?php echo $GLS;?>=J">&nbsp;J&nbsp;</a></b></td>
            <td><b><a href="<?php echo $GLS;?>=K">&nbsp;K&nbsp;</a></b></td>
            <td><b><a href="<?php echo $GLS;?>=L">&nbsp;L&nbsp;</a></b></td>
            <td><b><a href="<?php echo $GLS;?>=M">&nbsp;M&nbsp;</a></b></td>
            <td><b><a href="<?php echo $GLS;?>=N">&nbsp;N&nbsp;</a></b></td>
            <td><b><a href="<?php echo $GLS;?>=O">&nbsp;O&nbsp;</a></b></td>
            <td><b><a href="<?php echo $GLS;?>=P">&nbsp;P&nbsp;</a></b></td>
            <td><b><a href="<?php echo $GLS;?>=Q">&nbsp;Q&nbsp;</a></b></td>
            <td><b><a href="<?php echo $GLS;?>=R">&nbsp;R&nbsp;</a></b></td>
            <td><b><a href="<?php echo $GLS;?>=S">&nbsp;S&nbsp;</a></b></td>
            <td><b><a href="<?php echo $GLS;?>=T">&nbsp;T&nbsp;</a></b></td>
            <td><b><a href="<?php echo $GLS;?>=U">&nbsp;U&nbsp;</a></b></td>
            <td><b><a href="<?php echo $GLS;?>=V">&nbsp;V&nbsp;</a></b></td>
            <td><b><a href="<?php echo $GLS;?>=W">&nbsp;W&nbsp;</a></b></td>
            <td><b><a href="<?php echo $GLS;?>=X">&nbsp;X&nbsp;</a></b></td>
            <td><b><a href="<?php echo $GLS;?>=Y">&nbsp;Y&nbsp;</a></b></td>
            <td><b><a href="<?php echo $GLS;?>=Z">&nbsp;Z&nbsp;</a></b></td>


            <td nowrap> 
              <select name="limit">
                <option value="100" <?php if($limit==100)echo "selected";?>>100</option>
                <option value="250" <?php if($limit==250)echo "selected";?>>250</option>
                <option value="500" <?php if($limit==500)echo "selected";?>>500</option>
                <option value="1000" <?php if($limit==1000)echo "selected";?>>1000</option>
              </select>
              <input type="submit" name="LIMI" value="limit">
            </td>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td></td>
            <td><b><a href="<?php echo $GLS;?>=А">&nbsp;А&nbsp;</a></b></td>
            <td><b><a href="<?php echo $GLS;?>=Б">&nbsp;Б&nbsp;</a></b></td>
            <td><b><a href="<?php echo $GLS;?>=В">&nbsp;В&nbsp;</a></b></td>
            <td><b><a href="<?php echo $GLS;?>=Г">&nbsp;Г&nbsp;</a></b></td>
            <td><b><a href="<?php echo $GLS;?>=Д">&nbsp;Д&nbsp;</a></b></td>
            <td><b><a href="<?php echo $GLS;?>=Е">&nbsp;Е&nbsp;</a></b></td>
            <td><b><a href="<?php echo $GLS;?>=Ж">&nbsp;Ж&nbsp;</a></b></td>
            <td><b><a href="<?php echo $GLS;?>=З">&nbsp;З&nbsp;</a></b></td>
            <td><b><a href="<?php echo $GLS;?>=И">&nbsp;И&nbsp;</a></b></td>
            <td><b><a href="<?php echo $GLS;?>=К">&nbsp;К&nbsp;</a></b></td>
            <td><b><a href="<?php echo $GLS;?>=Л">&nbsp;Л&nbsp;</a></b></td>
            <td><b><a href="<?php echo $GLS;?>=М">&nbsp;М&nbsp;</a></b></td>
            <td><b><a href="<?php echo $GLS;?>=Н">&nbsp;Н&nbsp;</a></b></td>
            <td><b><a href="<?php echo $GLS;?>=О">&nbsp;О&nbsp;</a></b></td>
            <td><b><a href="<?php echo $GLS;?>=П">&nbsp;П&nbsp;</a></b></td>
            <td><b><a href="<?php echo $GLS;?>=Р">&nbsp;Р&nbsp;</a></b></td>
            <td><b><a href="<?php echo $GLS;?>=С">&nbsp;С&nbsp;</a></b></td>
            <td><b><a href="<?php echo $GLS;?>=Т">&nbsp;Т&nbsp;</a></b></td>
            <td><b><a href="<?php echo $GLS;?>=У">&nbsp;У&nbsp;</a></b></td>
            <td><b><a href="<?php echo $GLS;?>=Ф">&nbsp;Ф&nbsp;</a></b></td>
            <td><b><a href="<?php echo $GLS;?>=Х">&nbsp;Х&nbsp;</a></b></td>
            <td><b><a href="<?php echo $GLS;?>=Ц">&nbsp;Ц&nbsp;</a></b></td>
            <td><b><a href="<?php echo $GLS;?>=Ч">&nbsp;Ч&nbsp;</a></b></td>
            <td><b><a href="<?php echo $GLS;?>=Ш">&nbsp;Ш&nbsp;</a></b></td>
            <td><b><a href="<?php echo $GLS;?>=Щ">&nbsp;Ц&nbsp;</a></b></td>
            <td><b><a href="<?php echo $GLS;?>=Э">&nbsp;Э&nbsp;</a></b></td>
            <td><b><a href="<?php echo $GLS;?>=Ю">&nbsp;Ю&nbsp;</a></b></td>
            <td><b><a href="<?php echo $GLS;?>=Я">&nbsp;Я&nbsp;</a></b></td>

          </tr>
        </table>
      </td>
  </tr>
  <tr align="left" valign="top" bgcolor="#CCCCCC"> 
    <td colspan="3">&nbsp;</td>
  </tr>
  <tr align="left" valign="top"> 
    <td width="100%" bgcolor="#CCCCCC"> 
      <table  border="0" cellspacing="1" cellpadding="2" bgcolor="#CCCC33" width="100%">
        <tr> 
            <td ><b>Global </b></td>
          <td ><b>Nr</b></td>
          <td >&nbsp;</td>
          <td >&nbsp;</td>
          <td >&nbsp;</td>
        </tr>
        <?php  echo "$i";?> 
      </table>
    </td>
  </tr>
</table>

</form>




<?php require("http_end.php");?>
