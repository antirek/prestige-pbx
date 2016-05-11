<?php 
include_once(dirname(__FILE__) . "/lib/defines.php");
include_once(dirname(__FILE__) . "/lib/Class.Table.php");


// correct 31 +1 = 32 for the date
//session_start();

getpost_ifset(array('posted', 'Period', 'frommonth', 'fromstatsmonth', 'tomonth', 'tostatsmonth', 'fromday', 'fromstatsday_sday', 'fromstatsmonth_sday', 'today', 'tostatsday_sday', 'tostatsmonth_sday', 'dsttype', 'sourcetype', 'clidtype', 'channel', 'resulttype', 'stitle', 'atmenu', 'current_page', 'order', 'sens', 'dst', 'src', 'clid', 'userfieldtype', 'userfield', 'accountcodetype', 'accountcode', 'duration1', 'duration1type', 'duration2', 'duration2type'));

//echo "'posted=$posted', 'Period=$Period', 'frommonth=$frommonth', 'fromstatsmonth=$fromstatsmonth', 'tomonth=$tomonth', 'tostatsmonth=$tostatsmonth', 'fromday=$fromday', 'fromstatsday_sday=$fromstatsday_sday', 'fromstatsmonth_sday=$fromstatsmonth_sday', 'today=$today', 'tostatsday_sday=$tostatsday_sday', 'tostatsmonth_sday=$tostatsmonth_sday', 'dsttype=$dsttype', 'sourcetype=$sourcetype', 'clidtype=$clidtype', 'channel=$channel', 'resulttype=$resulttype', 'stitle=$stitle', 'atmenu=$atmenu', 'current_page=$current_page', 'order=$order', 'sens=$sens', 'dst=$dst', 'src=$src', 'clid=$clid', 'userfieldtype=$userfieldtype', 'userfield=$userfield', 'accountcodetype=$accountcodetype', 'accountcode=$accountcode', 'duration1=$duration1', 'duration1type=$duration1type', 'duration2=$duration2', 'duration2type=$duration2type'";

 

if (!isset ($current_page) || ($current_page == "")){	
		$current_page=0; 
	}


// this variable specifie the debug type (0 => nothing, 1 => sql result, 2 => boucle checking, 3 other value checking)
$FG_DEBUG = 0;

// The variable FG_TABLE_NAME define the table name to use
$FG_TABLE_NAME=DB_TABLENAME;



// THIS VARIABLE DEFINE THE COLOR OF THE HEAD TABLE
$FG_TABLE_HEAD_COLOR = "#D1D9E7";


$FG_TABLE_EXTERN_COLOR = "#7F99CC"; //#CC0033 (Rouge)
$FG_TABLE_INTERN_COLOR = "#EDF3FF"; //#FFEAFF (Rose)




// THIS VARIABLE DEFINE THE COLOR OF THE HEAD TABLE
$FG_TABLE_ALTERNATE_ROW_COLOR[] = "#FFFFFF";
$FG_TABLE_ALTERNATE_ROW_COLOR[] = "#F2F8FF";



//$link = DbConnect();
$DBHandle  = DbConnect();

// The variable Var_col would define the col that we want show in your table
// First Name of the column in the html page, second name of the field
$FG_TABLE_COL = array();


/*******
Calldate Clid Src Dst Dcontext Channel Dstchannel Lastapp Lastdata Duration Billsec Disposition Amaflags Accountcode Uniqueid Serverid
*******/
$resulttype="min";
$FG_TABLE_COL[]=array ("Дата звонка", "calldate", "18%", "left", "SORT", "19");
//$FG_TABLE_COL[]=array ("Channel", "channel", "8%", "left", "", "30", "", "", "", "", "", "display_acronym");
$FG_TABLE_COL[]=array ("Источник", "src", "11%", "center", "", "30");
$FG_TABLE_COL[]=array ("CallerID", "clid", "13%", "center", "", "30");
//$FG_TABLE_COL[]=array ("Lastapp", "lastapp", "0%", "center", "", "30");

//$FG_TABLE_COL[]=array ("Lastdata", "lastdata", "0%", "center", "", "30");
$FG_TABLE_COL[]=array ("Направление", "dst", "17%", "center", "SORT", "30");
//$FG_TABLE_COL[]=array ("APP", "dst", "15%", "center", "", "30","list", $appli_list);
//$FG_TABLE_COL[]=array ("Serverid", "serverid", "7%", "center", "", "30");
$FG_TABLE_COL[]=array ("Статус", "disposition", "11%", "center", "", "30");
if ((!isset($resulttype)) || ($resulttype=="min")) $minute_function= "display_minute";
//$FG_TABLE_COL[]=array ("Длительность", "duration", "6%", "center", "SORT", "30", "", "", "", "", "", "$minute_function");
$FG_TABLE_COL[]=array ("Длительность", "billsec", "5%", "center", "SORT", "30", "", "", "", "", "", "$minute_function");

$FG_TABLE_COL[]=array ("Userfield", "userfield", "9%", "center", "", "20");
$FG_TABLE_COL[]=array ("Accountcode", "accountcode", "12%", "center", "", "20");

$FG_TABLE_COL[]=array ("PDD", "(duration-billsec)", "4%", "center", "", "20");


$FG_TABLE_DEFAULT_ORDER = "calldate";
$FG_TABLE_DEFAULT_SENS = "DESC";

// This Variable store the argument for the SQL query
//$FG_COL_QUERY='calldate, channel, src, clid, lastapp, lastdata, dst, dst, serverid, disposition, duration';
$FG_COL_QUERY="calldate, src, clid, dst, disposition, billsec, userfield, accountcode, IF(disposition ='ANSWERED',(duration-billsec),NULL)";
$FG_COL_QUERY_GRAPH='calldate, billsec';

// The variable LIMITE_DISPLAY define the limit of record to display by page
$FG_LIMITE_DISPLAY=30;

// Number of column in the html table
$FG_NB_TABLE_COL=count($FG_TABLE_COL);

// The variable $FG_EDITION define if you want process to the edition of the database record
$FG_EDITION=true;

//This variable will store the total number of column
$FG_TOTAL_TABLE_COL = $FG_NB_TABLE_COL;
if ($FG_DELETION || $FG_EDITION) $FG_TOTAL_TABLE_COL++;

//This variable define the Title of the HTML table
$FG_HTML_TABLE_TITLE=" - Call Logs - ";

//This variable define the width of the HTML table
$FG_HTML_TABLE_WIDTH="100%";

$FG_DEBUG=0;


if ($FG_DEBUG == 3) echo "<br>Table : $FG_TABLE_NAME  	- 	Col_query : $FG_COL_QUERY";
$instance_table = new Table($FG_TABLE_NAME, $FG_COL_QUERY);
$instance_table_graph = new Table($FG_TABLE_NAME, $FG_COL_QUERY_GRAPH);


if ( is_null ($order) || is_null($sens) ){
	$order = $FG_TABLE_DEFAULT_ORDER;
	$sens  = $FG_TABLE_DEFAULT_SENS;
}

if ($posted==1){

  function do_field_duration($sql,$fld, $fldsql){
  		$fldtype = $fld.'type';
		global $$fld;
		global $$fldtype;				
        if (isset($$fld) && ($$fld!='')){
                if (strpos($sql,'WHERE') > 0){
                        $sql = "$sql AND ";
                }else{
                        $sql = "$sql WHERE ";
                }
				$sql = "$sql $fldsql";
				if (isset ($$fldtype)){                
                        switch ($$fldtype) {
							case 1:	$sql = "$sql ='".$$fld."'";  break;
							case 2: $sql = "$sql <= '".$$fld."'";  break;
							case 3: $sql = "$sql < '".$$fld."'";  break;							
							case 4: $sql = "$sql > '".$$fld."'";  break;
							case 5: $sql = "$sql >= '".$$fld."'";  break;
						}
                }else{ $sql = "$sql = '".$$fld."'"; }
		}
        return $sql;
  }

  function do_field($sql,$fld){
  		$fldtype = $fld.'type';
		global $$fld;
		global $$fldtype;
        if (isset($$fld) && ($$fld!='')){
                if (strpos($sql,'WHERE') > 0){
                        $sql = "$sql AND ";
                }else{
                        $sql = "$sql WHERE ";
                }
				$sql = "$sql $fld";
				if (isset ($$fldtype)){                
                        switch ($$fldtype) {
							case 1:	$sql = "$sql='".$$fld."'";  break;
							case 2: $sql = "$sql LIKE '".$$fld."%'";  break;
							case 3: $sql = "$sql LIKE '%".$$fld."%'";  break;
							case 4: $sql = "$sql LIKE '%".$$fld."'";
						}
                }else{ $sql = "$sql LIKE '%".$$fld."%'"; }
		}
        return $sql;
  }  
  $SQLcmd = '';
  
  $SQLcmd = do_field($SQLcmd, 'clid');
  $SQLcmd = do_field($SQLcmd, 'src');
  $SQLcmd = do_field($SQLcmd, 'dst');
  $SQLcmd = do_field($SQLcmd, 'userfield');
  $SQLcmd = do_field($SQLcmd, 'accountcode');
  $SQLcmd = do_field($SQLcmd, 'channel');
  $SQLcmd = do_field_duration($SQLcmd, 'duration1', 'billsec');
  $SQLcmd = do_field_duration($SQLcmd, 'duration2', 'billsec');
	
	
  
}


$date_clause='';
// Period (Month-Day)
if (DB_TYPE == "postgres"){		
	 	$UNIX_TIMESTAMP = "";
}else{		
		$UNIX_TIMESTAMP = "UNIX_TIMESTAMP";
}

if ($Period=="Month"){
		if ($frommonth && isset($fromstatsmonth)) $date_clause.=" AND $UNIX_TIMESTAMP(calldate) >= $UNIX_TIMESTAMP('$fromstatsmonth-01')";
		if ($tomonth && isset($tostatsmonth)) $date_clause.=" AND $UNIX_TIMESTAMP(calldate) <= $UNIX_TIMESTAMP('$tostatsmonth-31 23:59:59')";
}else{
		if ($fromday && isset($fromstatsday_sday) && isset($fromstatsmonth_sday)) $date_clause.=" AND $UNIX_TIMESTAMP(calldate) >= $UNIX_TIMESTAMP('$fromstatsmonth_sday-$fromstatsday_sday')";
		if ($today && isset($tostatsday_sday) && isset($tostatsmonth_sday)) $date_clause.=" AND $UNIX_TIMESTAMP(calldate) <= $UNIX_TIMESTAMP('$tostatsmonth_sday-".sprintf("%02d",intval($tostatsday_sday)/*+1*/)." 23:59:59')";
}
//echo "<br>$date_clause<br>";
/*
Month
fromday today
frommonth tomonth (true)
fromstatsmonth tostatsmonth

fromstatsday_sday
fromstatsmonth_sday
tostatsday_sday
tostatsmonth_sday
*/


  
if (strpos($SQLcmd, 'WHERE') > 0) { 
	$FG_TABLE_CLAUSE = substr($SQLcmd,6).$date_clause; 
}elseif (strpos($date_clause, 'AND') > 0){
	$FG_TABLE_CLAUSE = substr($date_clause,5); 
}



if (!isset ($FG_TABLE_CLAUSE) || strlen($FG_TABLE_CLAUSE)==0){
		
		$cc_yearmonth = sprintf("%04d-%02d",date("Y"),date("n")); 	
		$FG_TABLE_CLAUSE=" $UNIX_TIMESTAMP(calldate) >= $UNIX_TIMESTAMP('$cc_yearmonth-01')";
}
//--$list_total = $instance_table_graph -> Get_list ($FG_TABLE_CLAUSE, null, null, null, null, null, null);


if ($posted==1){
	//> function Get_list ($clause=null, $order=null, $sens=null, $field_order_letter=null, $letters = null, $limite=null, $current_record = NULL)
	$list = $instance_table -> Get_list ($FG_TABLE_CLAUSE, $order, $sens, null, null, $FG_LIMITE_DISPLAY, $current_page*$FG_LIMITE_DISPLAY);
	
	$_SESSION["pr_sql_export"]="SELECT $FG_COL_QUERY FROM $FG_TABLE_NAME WHERE $FG_TABLE_CLAUSE";
	
	
	/************************/

 
	$QUERY = "SELECT substring(calldate,1,10) AS day, sum(billsec) 
	AS calltime, count(*) as nbcall, count(IF(disposition = 'ANSWERED',1,NULL)) AS callans, 
	sum(IF(disposition = 'ANSWERED', (duration-billsec),0)) AS pdd 
	FROM cdr WHERE ".$FG_TABLE_CLAUSE." GROUP BY substring(calldate,1,10)"; //extract(DAY from calldate) 
	
	//echo "$QUERY";
				
	
       /***********************/

			$res = $DBHandle -> query($QUERY);
			$num = $DBHandle -> num_rows();
//			echo "numrows = $num"; echo "<P>";
			for($i=0;$i<$num;$i++)
				{				
					$DBHandle -> next_record();
					$list_total_day [] =$DBHandle -> Record;				 
				}
				
	if ($FG_DEBUG == 3) echo "<br>Clause : $FG_TABLE_CLAUSE";
	$nb_record = $instance_table -> Table_count ($FG_TABLE_CLAUSE);

}

//$nb_record = count($list_total);
if ($FG_DEBUG >= 1) var_dump ($list);


if ($nb_record<=$FG_LIMITE_DISPLAY){ 
	$nb_record_max=1;
}else{ 
	if ($nb_record % $FG_LIMITE_DISPLAY == 0){
		$nb_record_max=(intval($nb_record/$FG_LIMITE_DISPLAY));
	}else{
		$nb_record_max=(intval($nb_record/$FG_LIMITE_DISPLAY)+1);
	}	
}


if ($FG_DEBUG == 3) echo "<br>Nb_record : $nb_record";
if ($FG_DEBUG == 3) echo "<br>Nb_record_max : $nb_record_max";

?>

<script language="JavaScript" type="text/JavaScript">
<!--
function MM_openBrWindow(theURL,winName,features) { //v2.0
  window.open(theURL,winName,features);
}

//-->
</script>



<!-- ** ** ** ** ** Part for the research ** ** ** ** ** -->
	<center>
	<FORM METHOD=POST ACTION="<?php  echo $PHP_SELF?>?s=<?php  echo $s?>&t=<?php  echo $t?>&order=<?php  echo $order?>&sens=<?php  echo $sens?>&current_page=<?php  echo $current_page?>">
	<INPUT TYPE="hidden" NAME="posted" value=1>
	<INPUT TYPE="hidden" NAME="current_page" value=0>	
		<table class="bar-status" width="75%" border="0" cellspacing="1" cellpadding="2" align="center">
			<tbody><tr>
        		<td class="bar-search" align="left" bgcolor="#555577">

					<input type="radio" name="Period" value="Month" <?php  if (($Period=="Month") || !isset($Period)){ ?>checked="checked" <?php  } ?>> 
					<font face="verdana" size="1" color="#ffffff"><b>Месяц</b></font>
				</td>
      			<td class="bar-search" align="left" bgcolor="#cddeff">
					<table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#cddeff"><tr><td>
	  				<input type="checkbox" name="frommonth" value="true" <?php  if ($frommonth){ ?>checked<?php }?>> 
					От : <select name="fromstatsmonth" class="intxt">
					<?php 	$year_actual = date("Y");  	
						for ($i=$year_actual;$i >= $year_actual-1;$i--)
						{		   
							   $monthname = array( "Январь", "Февраль","Март", "Апрель", "Май", "Июнь", "Июль", "Август", "Сентябрь", "Октябрь", "Ноябрь", "Декабрь");
							   if ($year_actual==$i){
									$monthnumber = date("n")-1; // Month number without lead 0.
							   }else{
									$monthnumber=11;
							   }		   
							   for ($j=$monthnumber;$j>=0;$j--){	
										$month_formated = sprintf("%02d",$j+1);
							   			if ($fromstatsmonth=="$i-$month_formated"){$selected="selected";}else{$selected="";}
										echo "<OPTION value=\"$i-$month_formated\" $selected> $monthname[$j]-$i </option>";				
							   }
						}
					?>		
					</select>
					</td><td>&nbsp;&nbsp;
					<input type="checkbox" name="tomonth" value="true" <?php  if ($tomonth){ ?>checked<?php }?>> 
					До : <select name="tostatsmonth" class="intxt">
					<?php 	$year_actual = date("Y");  	
						for ($i=$year_actual;$i >= $year_actual-1;$i--)
						{		   
							   $monthname = array( "Январь", "Февраль","Март", "Апрель", "Май", "Июнь", "Июль", "Август", "Сентябрь", "Октябрь", "Ноябрь", "Декабрь");
							   if ($year_actual==$i){
									$monthnumber = date("n")-1; // Month number without lead 0.
							   }else{
									$monthnumber=11;
							   }		   
							   for ($j=$monthnumber;$j>=0;$j--){	
										$month_formated = sprintf("%02d",$j+1);
							   			if ($tostatsmonth=="$i-$month_formated"){$selected="selected";}else{$selected="";}
										echo "<OPTION value=\"$i-$month_formated\" $selected> $monthname[$j]-$i </option>";				
							   }
						}
					?>
					</select>
					</td></tr></table>
	  			</td>
    		</tr>
			
			<tr>
        		<td align="left" bgcolor="#000033">
					<input type="radio" name="Period" value="Day" <?php  if ($Period=="Day"){ ?>checked="checked" <?php  } ?>> 
					<font face="verdana" size="1" color="#ffffff"><b>День</b></font>
				</td>
      			<td align="left" bgcolor="#acbdee">
					<table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#acbdee"><tr><td>
	  				<input type="checkbox" name="fromday" value="true" <?php  if ($fromday){ ?>checked<?php }?>> От : 
					<select name="fromstatsday_sday" class="intxt">
						<?php  
							for ($i=1;$i<=31;$i++){
								if ($fromstatsday_sday==sprintf("%02d",$i)){$selected="selected";}else{$selected="";}
								echo '<option value="'.sprintf("%02d",$i)."\"$selected>".sprintf("%02d",$i).'</option>';
							}
						?>	
					</select>
				 	<select name="fromstatsmonth_sday" class="intxt">
					<?php 	$year_actual = date("Y");  	
						for ($i=$year_actual;$i >= $year_actual-1;$i--)
						{		   
							   $monthname = array( "Январь", "Февраль","Март", "Апрель", "Май", "Июнь", "Июль", "Август", "Сентябрь", "Октябрь", "Ноябрь", "Декабрь");
							   if ($year_actual==$i){
									$monthnumber = date("n")-1; // Month number without lead 0.
							   }else{
									$monthnumber=11;
							   }		   
							   for ($j=$monthnumber;$j>=0;$j--){	
										$month_formated = sprintf("%02d",$j+1);
							   			if ($fromstatsmonth_sday=="$i-$month_formated"){$selected="selected";}else{$selected="";}
										echo "<OPTION value=\"$i-$month_formated\" $selected> $monthname[$j]-$i </option>";				
							   }
						}
					?>
					</select>
					</td><td>&nbsp;&nbsp;
					<input type="checkbox" name="today" value="true" <?php  if ($today){ ?>checked<?php }?>> До : 
					<select name="tostatsday_sday" class="intxt">
					<?php  
						for ($i=1;$i<=31;$i++){
							if ($tostatsday_sday==sprintf("%02d",$i)){$selected="selected";}else{$selected="";}
							echo '<option value="'.sprintf("%02d",$i)."\"$selected>".sprintf("%02d",$i).'</option>';
						}
					?>						
					</select>
				 	<select name="tostatsmonth_sday" class="intxt">
					<?php 	$year_actual = date("Y");  	
						for ($i=$year_actual;$i >= $year_actual-1;$i--)
						{		   
							   $monthname = array( "Январь", "Февраль","Март", "Апрель", "Май", "Июнь", "Июль", "Август", "Сентябрь", "Октябрь", "Ноябрь", "Декабрь");
							   if ($year_actual==$i){
									$monthnumber = date("n")-1; // Month number without lead 0.
							   }else{
									$monthnumber=11;
							   }		   
							   for ($j=$monthnumber;$j>=0;$j--){	
										$month_formated = sprintf("%02d",$j+1);
							   			if ($tostatsmonth_sday=="$i-$month_formated"){$selected="selected";}else{$selected="";}
										echo "<OPTION value=\"$i-$month_formated\" $selected> $monthname[$j]-$i </option>";				
							   }
						}
					?>
					</select>
					</td></tr></table>
	  			</td>
    		</tr>
			<tr>
				<td class="bar-search" align="left" bgcolor="#555577">			
					<font face="verdana" size="1" color="#ffffff"><b>&nbsp;&nbsp;Направление</b></font>
				</td>				
				<td class="bar-search" align="left" bgcolor="#cddeff">
				<table width="100%" border="0" cellspacing="0" cellpadding="0"><tr><td>&nbsp;&nbsp;<INPUT TYPE="text" NAME="dst" value="<?php  echo $dst?>" class="intxt"></td>
				<td class="bar-search" align="center" bgcolor="#cddeff"><input type="radio" NAME="dsttype" value="1" <?php if((!isset($dsttype))||($dsttype==1)){?>checked<?php }?>>Точно</td>
				<td class="bar-search" align="center" bgcolor="#cddeff"><input type="radio" NAME="dsttype" value="2" <?php if($dsttype==2){?>checked<?php }?>>В начале</td>
				<td class="bar-search" align="center" bgcolor="#cddeff"><input type="radio" NAME="dsttype" value="3" <?php if($dsttype==3){?>checked<?php }?>>Содержит</td>
				<td class="bar-search" align="center" bgcolor="#cddeff"><input type="radio" NAME="dsttype" value="4" <?php if($dsttype==4){?>checked<?php }?>>В конце</td>
				</tr></table></td>
			</tr>			
			<tr>
				<td align="left" bgcolor="#000033">					
					<font face="verdana" size="1" color="#ffffff"><b>&nbsp;&nbsp;Источник</b></font>
				</td>				
				<td class="bar-search" align="left" bgcolor="#acbdee">
				<table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#acbdee"><tr><td>&nbsp;&nbsp;<INPUT TYPE="text" NAME="src" value="<?php echo "$src";?>" class="intxt"></td>
				<td class="bar-search" align="center" bgcolor="#acbdee"><input type="radio" NAME="sourcetype" value="1" <?php if((!isset($sourcetype))||($sourcetype==1)){?>checked<?php }?>>Точно</td>
				<td class="bar-search" align="center" bgcolor="#acbdee"><input type="radio" NAME="sourcetype" value="2" <?php if($sourcetype==2){?>checked<?php }?>>В начале</td>
				<td class="bar-search" align="center" bgcolor="#acbdee"><input type="radio" NAME="sourcetype" value="3" <?php if($sourcetype==3){?>checked<?php }?>>Содержит</td>
				<td class="bar-search" align="center" bgcolor="#acbdee"><input type="radio" NAME="sourcetype" value="4" <?php if($sourcetype==4){?>checked<?php }?>>В конце</td>
				</tr></table></td>
			</tr>
			<tr>
				<td class="bar-search" align="left" bgcolor="#555577">				
					<font face="verdana" size="1" color="#ffffff"><b>&nbsp;&nbsp;CallerID</b></font>
				</td>				
				<td class="bar-search" align="left" bgcolor="#cddeff">
				<table width="100%" border="0" cellspacing="0" cellpadding="0"><tr><td>&nbsp;&nbsp;<INPUT TYPE="text" NAME="clid" value="<?php  echo $clid?>" class="intxt"></td>
				<td class="bar-search" align="center" bgcolor="#cddeff"><input type="radio" NAME="clidtype" value="1" <?php if((!isset($clidtype))||($clidtype==1)){?>checked<?php }?>>Точно</td>
				<td class="bar-search" align="center" bgcolor="#cddeff"><input type="radio" NAME="clidtype" value="2" <?php if($clidtype==2){?>checked<?php }?>>В начале</td>
				<td class="bar-search" align="center" bgcolor="#cddeff"><input type="radio" NAME="clidtype" value="3" <?php if($clidtype==3){?>checked<?php }?>>Содержит</td>
				<td class="bar-search" align="center" bgcolor="#cddeff"><input type="radio" NAME="clidtype" value="4" <?php if($clidtype==4){?>checked<?php }?>>В конце</td>
				</tr></table></td>
			</tr>
			<tr>
				<td align="left" bgcolor="#000033">					
					<font face="verdana" size="1" color="#ffffff"><b>&nbsp;&nbsp;Userfield</b></font>
				</td>				
				<td class="bar-search" align="left" bgcolor="#acbdee">
				<table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#acbdee"><tr><td>&nbsp;&nbsp;<INPUT TYPE="text" NAME="userfield" value="<?php echo "$userfield";?>" class="intxt"></td>
				<td class="bar-search" align="center" bgcolor="#acbdee"><input type="radio" NAME="userfieldtype" value="1" <?php if((!isset($userfieldtype))||($userfieldtype==1)){?>checked<?php }?>>Точно</td>
				<td class="bar-search" align="center" bgcolor="#acbdee"><input type="radio" NAME="userfieldtype" value="2" <?php if($userfieldtype==2){?>checked<?php }?>>В начале</td>
				<td class="bar-search" align="center" bgcolor="#acbdee"><input type="radio" NAME="userfieldtype" value="3" <?php if($userfieldtype==3){?>checked<?php }?>>Содержит</td>
				<td class="bar-search" align="center" bgcolor="#acbdee"><input type="radio" NAME="userfieldtype" value="4" <?php if($userfieldtype==4){?>checked<?php }?>>В конце</td>
				</tr></table></td>
			</tr>
			<tr>
				<td class="bar-search" align="left" bgcolor="#555577">				
					<font face="verdana" size="1" color="#ffffff"><b>&nbsp;&nbsp;Accountcode</b></font>
				</td>				
				<td class="bar-search" align="left" bgcolor="#cddeff">
				<table width="100%" border="0" cellspacing="0" cellpadding="0"><tr><td>&nbsp;&nbsp;<INPUT TYPE="text" NAME="accountcode" value="<?php  echo $accountcode?>" class="intxt"></td>
				<td class="bar-search" align="center" bgcolor="#cddeff"><input type="radio" NAME="accountcodetype" value="1" <?php if((!isset($accountcodetype))||($accountcodetype==1)){?>checked<?php }?>>Точно</td>
				<td class="bar-search" align="center" bgcolor="#cddeff"><input type="radio" NAME="accountcodetype" value="2" <?php if($accountcodetype==2){?>checked<?php }?>>В начале</td>
				<td class="bar-search" align="center" bgcolor="#cddeff"><input type="radio" NAME="accountcodetype" value="3" <?php if($accountcodetype==3){?>checked<?php }?>>Содержит</td>
				<td class="bar-search" align="center" bgcolor="#cddeff"><input type="radio" NAME="accountcodetype" value="4" <?php if($accountcodetype==4){?>checked<?php }?>>В конце</td>
				</tr></table></td>
			</tr>			

			<tr>
				<td class="bar-search" align="left" bgcolor="#000033">				
					<font face="verdana" size="1" color="#ffffff"><b>&nbsp;&nbsp;Длительность</b></font>
				</td>				
				<td class="bar-search" align="left" bgcolor="#acbdee">
				<table width="100%" border="0" cellspacing="0" cellpadding="0"><tr>
				<td>&nbsp;&nbsp;<INPUT TYPE="text" NAME="duration1" size="4" value="<?php  echo $duration1?>" class="intxt"></td>
				<td class="bar-search" align="center" bgcolor="#acbdee"><input type="radio" NAME="duration1type" value="4" <?php if($duration1type==4){?>checked<?php }?>>&gt;</td>
				<td class="bar-search" align="center" bgcolor="#acbdee"><input type="radio" NAME="duration1type" value="5" <?php if($duration1type==5){?>checked<?php }?>>&gt; равно</td>
				<td class="bar-search" align="center" bgcolor="#acbdee"><input type="radio" NAME="duration1type" value="1" <?php if((!isset($duration1type))||($duration1type==1)){?>checked<?php }?>>равно</td>
				<td class="bar-search" align="center" bgcolor="#acbdee"><input type="radio" NAME="duration1type" value="2" <?php if($duration1type==2){?>checked<?php }?>>&lt; равно</td>
				<td class="bar-search" align="center" bgcolor="#acbdee"><input type="radio" NAME="duration1type" value="3" <?php if($duration1type==3){?>checked<?php }?>>&lt;</td>	
				<td width="5%" class="bar-search" align="center" bgcolor="#acbdee"></td>
				
				<td>&nbsp;&nbsp;<INPUT TYPE="text" NAME="duration2" size="4" value="<?php  echo $duration2?>" class="intxt"></td>			
				<td class="bar-search" align="center" bgcolor="#acbdee"><input type="radio" NAME="duration2type" value="4" <?php if($duration2type==4){?>checked<?php }?>>&gt;</td>
				<td class="bar-search" align="center" bgcolor="#acbdee"><input type="radio" NAME="duration2type" value="5" <?php if($duration2type==5){?>checked<?php }?>>&gt; равно</td>								
				<td class="bar-search" align="center" bgcolor="#acbdee"><input type="radio" NAME="duration2type" value="2" <?php if($duration2type==1){?>checked<?php }?>>&lt; равно</td>
				<td class="bar-search" align="center" bgcolor="#acbdee"><input type="radio" NAME="duration2type" value="3" <?php if($duration2type==3){?>checked<?php }?>>&lt;</td>	
				</tr></table>
				</td>
			</tr>	


			<tr>
        		<td class="bar-search" align="left" bgcolor="#555577"> </td>
				<td class="bar-search" align="left" bgcolor="#cddeff">
				    <table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#cddeff">
				    <tr>
				    <td>
					
				    <input type="checkbox" name="onlystat" value="true" <?php  if ($onlystat){ ?>checked<?php }?>>
				    <font size=1> Показать только статистику</font>
<?php /*				    
				    </td>			
				    <td>
					
				    <input type="checkbox" name="aggregate" value="true" <?php  if ($aggregate){ ?>checked<?php }?>> - Aggregate calls by destinations
				    
				    </td>			
*/?>			

				    <td class="bar-search" align="right" bgcolor="#cddeff">
					<input type="image"  name="image16" align="top" border="0" src="images/button-search.gif" />
					&nbsp;&nbsp;&nbsp;&nbsp;

	  			    </td>
				</tr></table>
				

			</td>	
    		</tr>
		</tbody></table>
	</FORM>
</center>


<br><br>
<?php 
if (!($onlystat || $aggregate)) {
?>
<!-- ** ** ** ** ** Part to display the CDR ** ** ** ** ** -->

			<center>Количество звонков : <?php  if (is_array($list) && count($list)>0){ echo $nb_record; }else{echo "0";}?></center>
      <table width="<?php  echo $FG_HTML_TABLE_WIDTH?>" border="0" align="center" cellpadding="0" cellspacing="0">
<TR bgcolor="#ffffff"> 
          <TD bgColor=#7f99cc height=16 style="PADDING-LEFT: 5px; PADDING-RIGHT: 3px"> 
            <TABLE border=0 cellPadding=0 cellSpacing=0 width="100%">
              <TBODY>
                <TR> 
                  <TD><SPAN style="COLOR: #ffffff; FONT-SIZE: 11px"><B><?php  echo $FG_HTML_TABLE_TITLE?></B></SPAN></TD>
                  <TD align=right> <IMG alt="Back to Top" border=0 height=12 src="images/btn_top_12x12.gif" width=12> 
                  </TD>
                </TR>
              </TBODY>
            </TABLE></TD>
        </TR>
        <TR> 
          <TD> <TABLE border=0 cellPadding=0 cellSpacing=0 width="100%">
<TBODY>
                <TR bgColor=#F0F0F0> 
				  <TD width="<?php  echo $FG_ACTION_SIZE_COLUMN?>" align=center class="tableBodyRight" style="PADDING-BOTTOM: 2px; PADDING-LEFT: 2px; PADDING-RIGHT: 2px; PADDING-TOP: 2px"></TD>					
				  
                  <?php  
				  	if (is_array($list) && count($list)>0){
					
				  	for($i=0;$i<$FG_NB_TABLE_COL;$i++){ 
						//$FG_TABLE_COL[$i][1];			
						//$FG_TABLE_COL[]=array ("Name", "name", "20%");
					?>				
				  
					
                  <TD width="<?php  echo $FG_TABLE_COL[$i][2]?>" align=middle class="tableBody" style="PADDING-BOTTOM: 2px; PADDING-LEFT: 2px; PADDING-RIGHT: 2px; PADDING-TOP: 2px"> 
                    <center><strong> 
                    <?php  if (strtoupper($FG_TABLE_COL[$i][4])=="SORT"){?>
                    <a href="<?php  echo $PHP_SELF."?s=0&t=$t&stitle=$stitle&atmenu=$atmenu&current_page=$current_page&order=".$FG_TABLE_COL[$i][1]."&sens="; if ($sens=="ASC"){echo"DESC";}else{echo"ASC";} 
					echo "&posted=$posted&Period=$Period&frommonth=$frommonth&fromstatsmonth=$fromstatsmonth&tomonth=$tomonth&tostatsmonth=$tostatsmonth&fromday=$fromday&fromstatsday_sday=$fromstatsday_sday&fromstatsmonth_sday=$fromstatsmonth_sday&today=$today&tostatsday_sday=$tostatsday_sday&tostatsmonth_sday=$tostatsmonth_sday&dsttype=$dsttype&sourcetype=$sourcetype&clidtype=$clidtype&channel=$channel&resulttype=$resulttype&dst=$dst&src=$src&clid=$clid";?>"> 
                    <span class="liens"><?php  } ?>
                    <?php  echo $FG_TABLE_COL[$i][0]?> 
                    <?php if ($order==$FG_TABLE_COL[$i][1] && $sens=="ASC"){?>
                    &nbsp;<img src="images/icon_up_12x12.GIF" width="12" height="12" border="0"> 
                    <?php }elseif ($order==$FG_TABLE_COL[$i][1] && $sens=="DESC"){?>
                    &nbsp;<img src="images/icon_down_12x12.GIF" width="12" height="12" border="0"> 
                    <?php }?>
                    <?php  if (strtoupper($FG_TABLE_COL[$i][4])=="SORT"){?>
                    </span></a> 
                    <?php }?>
                    </strong></center></TD>
				   <?php  } ?>		
				  	
                </TR>
                <TR> 
                  <TD bgColor=#e1e1e1 colSpan=<?php  echo $FG_TOTAL_TABLE_COL?> height=1><IMG 
                              height=1 
                              src="images/clear.gif" 
                              width=1></TD>
                </TR>
				<?php 
				
				
				  
				  	 $ligne_number=0;					 
					//print_r($list);
				  	 foreach ($list as $recordset){ 
						 $ligne_number++;
				?>
				
               		 <TR bgcolor="<?php  echo $FG_TABLE_ALTERNATE_ROW_COLOR[$ligne_number%2]?>"  onMouseOver="bgColor='#C4FFD7'" onMouseOut="bgColor='<?php  echo $FG_TABLE_ALTERNATE_ROW_COLOR[$ligne_number%2]?>'"> 
						<TD vAlign=top align="<?php  echo $FG_TABLE_COL[$i][3]?>" class=tableBody><?php  echo $ligne_number+$current_page*$FG_LIMITE_DISPLAY.".&nbsp;"; ?></TD>
							 
				  		<?php  for($i=0;$i<$FG_NB_TABLE_COL;$i++){ ?>
						
						  
						<?php 	//$FG_TABLE_COL[$i][1];			
							//$FG_TABLE_COL[]=array ("Name", "name", "20%");
							
							
							if ($FG_TABLE_COL[$i][6]=="lie"){


									$instance_sub_table = new Table($FG_TABLE_COL[$i][7], $FG_TABLE_COL[$i][8]);
									$sub_clause = str_replace("%id", $recordset[$i], $FG_TABLE_COL[$i][9]);																																	
									$select_list = $instance_sub_table -> Get_list ($sub_clause, null, null, null, null, null, null);
									
									
									$field_list_sun = split(',',$FG_TABLE_COL[$i][8]);
									$record_display = $FG_TABLE_COL[$i][10];
									//echo $record_display;
									
									for ($l=1;$l<=count($field_list_sun);$l++){										
										$record_display = str_replace("%$l", $select_list[0][$l-1], $record_display);	
									}
								
							}elseif ($FG_TABLE_COL[$i][6]=="list"){
									$select_list = $FG_TABLE_COL[$i][7];
									$record_display = $select_list[$recordset[$i]][0];
							
							}else{
									$record_display = $recordset[$i];
							}
							
							
							if ( is_numeric($FG_TABLE_COL[$i][5]) && (strlen($record_display) > $FG_TABLE_COL[$i][5])  ){
								$record_display = substr($record_display, 0, $FG_TABLE_COL[$i][5]-3)."";  
															
							}
							
							
				 		 ?>
                 		 <TD vAlign=top align="<?php  echo $FG_TABLE_COL[$i][3]?>" class=tableBody><?php 
						 if (isset ($FG_TABLE_COL[$i][11]) && strlen($FG_TABLE_COL[$i][11])>1){
						 		call_user_func($FG_TABLE_COL[$i][11], $record_display);
						 }else{
						 
						 		echo stripslashes($record_display);
								//echo "111";
						 }						 
						 ?></TD>
				 		 <?php  } ?>
                  
					</TR>
				<?php 
					 }//foreach ($list as $recordset)
					 while ($ligne_number < $FG_LIMITE_DISPLAY){
					 	$ligne_number++;
				?>
					<TR bgcolor="<?php  echo $FG_TABLE_ALTERNATE_ROW_COLOR[$ligne_number%2]?>"> 
				  		<?php  for($i=0;$i<$FG_NB_TABLE_COL;$i++){ 
							//$FG_TABLE_COL[$i][1];			
							//$FG_TABLE_COL[]=array ("Name", "name", "20%");
				 		 ?>
                 		 <TD vAlign=top class=tableBody>&nbsp;</TD>
				 		 <?php  } ?>
                 		 <TD align="center" vAlign=top class=tableBodyRight>&nbsp;</TD>				
					</TR>
									
				<?php 					 
					 } //END_WHILE
					 
				  }else{
				  		echo "Данные не найдены !!!";				  
				  }//end_if
				 ?>
                <TR> 
                  <TD class=tableDivider colSpan=<?php  echo $FG_TOTAL_TABLE_COL?>><IMG height=1 
                              src="images/clear.gif" 
                              width=1></TD>
                </TR>
                <TR> 
                  <TD class=tableDivider colSpan=<?php  echo $FG_TOTAL_TABLE_COL?>><IMG height=1 
                              src="images/clear.gif" 
                              width=1></TD>
                </TR>
              </TBODY>
            </TABLE></td>
        </tr>
        <TR bgcolor="#ffffff"> 
          <TD bgColor=#ADBEDE height=16 style="PADDING-LEFT: 5px; PADDING-RIGHT: 3px"> 
			<TABLE border=0 cellPadding=0 cellSpacing=0 width="100%">
              <TBODY>
                <TR> 
                  <TD align="right"><SPAN style="COLOR: #ffffff; FONT-SIZE: 11px"><B> 
                    <?php if ($current_page>0){?>
                    <img src="images/fleche-g.gif" width="5" height="10"> <a href="<?php  echo $PHP_SELF?>?s=0&t=<?php  echo $t?>&order=<?php  echo $order?>&sens=<?php  echo $sens?>&current_page=<?php  echo ($current_page-1)?><?php  if (!is_null($letter) && ($letter!="")){ echo "&letter=$letter";} 
					echo "&posted=$posted&Period=$Period&frommonth=$frommonth&fromstatsmonth=$fromstatsmonth&tomonth=$tomonth&tostatsmonth=$tostatsmonth&fromday=$fromday&fromstatsday_sday=$fromstatsday_sday&fromstatsmonth_sday=$fromstatsmonth_sday&today=$today&tostatsday_sday=$tostatsday_sday&tostatsmonth_sday=$tostatsmonth_sday&dsttype=$dsttype&sourcetype=$sourcetype&clidtype=$clidtype&channel=$channel&resulttype=$resulttype&dst=$dst&src=$src&clid=$clid&channel=$channel&resulttype=$resulttype&dst=$dst&src=$src&clid=$clid&userfieldtype=$userfieldtype&userfield=$userfield&accountcodetype=$accountcodetype&accountcode=$accountcode&duration1=$duration1&duration1type=$duration1type&duration2=$duration2&duration2type=$duration2type";?>"> 
                    Пред. </a> - 
                    <?php }?>
                    <?php echo ($current_page+1);?> / <?php  echo $nb_record_max;?> 
                    <?php if ($current_page<$nb_record_max-1){?>
                    - <a href="<?php  echo $PHP_SELF?>?s=0&t=<?php  echo $t?>&order=<?php  echo $order?>&sens=<?php  echo $sens?>&current_page=<?php  echo ($current_page+1)?><?php  if (!is_null($letter) && ($letter!="")){ echo "&letter=$letter";} 
					echo "&posted=$posted&Period=$Period&frommonth=$frommonth&fromstatsmonth=$fromstatsmonth&tomonth=$tomonth&tostatsmonth=$tostatsmonth&fromday=$fromday&fromstatsday_sday=$fromstatsday_sday&fromstatsmonth_sday=$fromstatsmonth_sday&today=$today&tostatsday_sday=$tostatsday_sday&tostatsmonth_sday=$tostatsmonth_sday&dsttype=$dsttype&sourcetype=$sourcetype&clidtype=$clidtype&channel=$channel&resulttype=$resulttype&dst=$dst&src=$src&clid=$clid&channel=$channel&resulttype=$resulttype&dst=$dst&src=$src&clid=$clid&userfieldtype=$userfieldtype&userfield=$userfield&accountcodetype=$accountcodetype&accountcode=$accountcode&duration1=$duration1&duration1type=$duration1type&duration2=$duration2&duration2type=$duration2type";?>"> 
                    След. </a> <img src="images/fleche-d.gif" width="5" height="10"> 
                    </B></SPAN> 
                    <?php }?>
                  </TD>
              </TBODY>
            </TABLE></TD>
        </TR>
      </table>
<?php }?>


<!-- ** ** ** ** ** Part to display the GRAPHIC ** ** ** ** ** -->
<br><br>

<?php 

if (is_array($list_total_day) && count($list_total_day)>0){
/*if (is_array($list) && count($list)>0){

$table_graph=array();
$numm=0;
foreach ($list_total as $recordset){
		$numm++;
		$mydate= substr($recordset[0],0,10);
		//echo "$mydate<br>";
		
		if (is_array($table_graph[$mydate])){
			$table_graph[$mydate][0]++;
			$table_graph[$mydate][1]=$table_graph[$mydate][1]+$recordset[1];
		}else{
			$table_graph[$mydate][0]=1;
			$table_graph[$mydate][1]=$recordset[1];
		}
		
}*/


$mmax=0;
$totalcall==0;
$totalminutes=0;
$totalcallanswered=0;
$totalpdd=0;
foreach ($list_total_day as $data){	

	if ($mmax < $data[1]) $mmax=$data[1];
	$totalcall+=$data[2];
	$totalminutes+=$data[1];
	$totalcallsanswered+=$data[3];
	$totalpdd+=$data[4];
}
//echo "<br/>$totalcall-$totalminutes";


/*foreach ($table_graph as $tkey => $data){	
	if ($mmax < $data[1]) $mmax=$data[1];
	$totalcall+=$data[0];
	$totalminutes+=$data[1];
}*/
//print_r($table_graph);

?>
 <!-- TITLE GLOBAL -->
<center>
 <table border="0" cellspacing="0" cellpadding="0" width="80%"><tbody><tr><td align="left" height="30">
		<table cellspacing="0" cellpadding="1" bgcolor="#000000" width="50%"><tbody><tr><td>
			<table cellspacing="0" cellpadding="0" width="100%"><tbody>
				<tr><td bgcolor="#600101" align="left"><font face="verdana" size="1" color="white"><b>Всего</b></font></td></tr>
			</tbody></table>
		</td></tr></tbody></table>
 </td></tr></tbody></table>
		  
<!-- FIN TITLE GLOBAL MINUTES //-->
				
<table border="0" cellspacing="0" cellpadding="0" width="80%">
<tbody><tr><td bgcolor="#000000">			
	<table border="0" cellspacing="1" cellpadding="2" width="100%"><tbody>
	<tr>	
		<td align="center" bgcolor="#600101"></td>
    	<td bgcolor="#b72222" align="center" colspan="6"><font face="verdana" size="1" color="#ffffff"><b>Астериск Трафик</b></font></td>
    </tr>
	<tr bgcolor="#600101">
		<td align="left" bgcolor="#b72222"><font face="verdana" size="1" color="#ffffff"><b>Дата</b></font></td>
        <td align="center"><font face="verdana" size="1" color="#ffffff"><b>Длит-ть</b></font></td>

		<td align="center"><font face="verdana" size="1" color="#ffffff"><b>График</b></font></td>
		<td align="center"><font face="verdana" size="1" color="#ffffff"><b>Звонков</b></font></td>
		<td align="center"><font face="verdana" size="1" color="#ffffff"><b> <acronym title="Средняя продолжительность звонка">ACD мин</acronym> </b></font></td>
		<td align="center"><font face="verdana" size="1" color="#ffffff"><b> <acronym title="Процент дозвона">ASR %</acronym> </b></font></td>
		<td align="center"><font face="verdana" size="1" color="#ffffff"><b> <acronym title="Среднее время соединения, сек">PDD сек</acronym> </b></font></td>
                			
		<!-- LOOP -->
	<?php  		
		$i=0;
		// #ffffff #cccccc
		foreach ($list_total_day as $data){	
		$i=($i+1)%2;		
		if ($data[3]) $tmc = $data[1]/$data[3];
		else $tmc=0;
		if ($data[2]) $asr = $data[3]/$data[2]*100;
		else $asr=0;
		if ($data[3]) $pdd = $data[4]/$data[3];
		else $pdd=0;
		
		if ((!isset($resulttype)) || ($resulttype=="min")){  
			$tmc = sprintf("%02d",intval($tmc/60)).":".sprintf("%02d",intval($tmc%60));		
		}else{
		
			$tmc =intval($tmc);
		}
		
		if ((!isset($resulttype)) || ($resulttype=="min")){  
				$minutes = sprintf("%02d",intval($data[1]/60)).":".sprintf("%02d",intval($data[1]%60));
		}else{
				$minutes = $data[1];
		}
		if ($mmax) $widthbar= intval(($data[1]/$mmax)*130);
		else $widthbar=0;
		
		//bgcolor="#336699" 
	?>
		</tr><tr>
		<td align="right" class="sidenav" nowrap="nowrap"><font face="verdana" size="1" color="#ffffff"><?php  echo $data[0]?></font></td>
		<td bgcolor="<?php  echo $FG_TABLE_ALTERNATE_ROW_COLOR[$i]?>" align="right" nowrap="nowrap"><font face="verdana" color="#000000" size="1"><?php  echo $minutes?> </font></td>
        <td bgcolor="<?php  echo $FG_TABLE_ALTERNATE_ROW_COLOR[$i]?>" align="left" nowrap="nowrap" width="<?php  echo $widthbar+60?>">
        <table cellspacing="0" cellpadding="0"><tbody><tr>
        <td bgcolor="#e22424"><img src="images/spacer.gif" width="<?php  echo $widthbar?>" height="6"></td>
        </tr></tbody></table></td>
        <td bgcolor="<?php  echo $FG_TABLE_ALTERNATE_ROW_COLOR[$i]?>" align="right" nowrap="nowrap"><font face="verdana" color="#000000" size="1"><?php  echo $data[2]?></font></td>
        <td bgcolor="<?php  echo $FG_TABLE_ALTERNATE_ROW_COLOR[$i]?>" align="right" nowrap="nowrap"><font face="verdana" color="#000000" size="1"><?php  echo $tmc?> </font></td>
	<td bgcolor="<?php  echo $FG_TABLE_ALTERNATE_ROW_COLOR[$i]?>" align="right" nowrap="nowrap"><font face="verdana" color="#000000" size="1"><?php  echo number_format($asr,1);?></font></td>
	<td bgcolor="<?php  echo $FG_TABLE_ALTERNATE_ROW_COLOR[$i]?>" align="right" nowrap="nowrap"><font face="verdana" color="#000000" size="1"><?php  echo number_format($pdd,1);?></font></td>
     <?php 	 }	 	 	
	 	
		if ((!isset($resulttype)) || ($resulttype=="min")){  
			if ($totalcall) $total_tmc = sprintf("%02d",intval(($totalminutes/$totalcall)/60)).":".sprintf("%02d",intval(($totalminutes/$totalcall)%60));
			else $total_tmc=0;				
			$totalminutesorig=$totalminutes;
			$totalminutes = sprintf("%02d",intval($totalminutes/60)).":".sprintf("%02d",intval($totalminutes%60));
		}else{
			if ($totalcall) $total_tmc = intval($totalminutes/$totalcall);
			else $total_tmc=0;			
		}
	 
	 ?>                   	
	</tr>
	<!-- FIN DETAIL -->		
	
				
				<!-- FIN BOUCLE -->

	<!-- TOTAL -->
	<tr bgcolor="#600101">
		<td align="left" nowrap="nowrap"><font face="verdana" size="1" color="#ffffff"><b>Всего</b></font></td>
		<td align="right" nowrap="nowrap"><font face="verdana" size="1" color="#ffffff"><b><?php  echo $totalminutes?></b></font></td>

		<td align="center" nowrap="nowrap"><font face="verdana" size="1" color="#ffffff"><b></b></font></td>

		<td align="right" nowrap="nowrap"><font face="verdana" size="1" color="#ffffff"><b><?php  echo $totalcall?></b></font></td>
		<td align="right" nowrap="nowrap"><font face="verdana" size="1" color="#ffffff"><b>
		

		<?php 
		if ($totalcallsanswered) $total_tmc = sprintf("%02d",intval(($totalminutesorig/$totalcallsanswered)/60)).":".sprintf("%02d",intval(($totalminutesorig/$totalcallsanswered)%60));
		else $total_tmc=0;
		echo $total_tmc;
		?>	
		<td align="right" nowrap="nowrap"><font face="verdana" size="1" color="#ffffff"><b>	
		<?php if ($totalcall) echo number_format(($totalcallsanswered/$totalcall*100),1); 
		else echo "0.0";?>
		</b></font></td>                        
		
		<td align="right" nowrap="nowrap"><font face="verdana" size="1" color="#ffffff"><b>	
		<?php if ($totalcallsanswered) echo number_format($totalpdd/$totalcallsanswered,1); 
		else echo "0.0"; ?>
		</b></font></td>                        
		
	</tr>
	<!-- FIN TOTAL -->

	  </tbody></table>
	  <!-- Fin Tableau Global //-->

</td></tr></tbody></table>

<br/>
<table width="60%"><tr><td>
<a href="export_pdf.php" target="_blank"><img src="./images/pdf.gif	" border="0"/></a> <a href="export_pdf.php" target="_blank">Экспорт в PDF файл</a>
</td>
<td>
<a href="export_csv.php" target="_blank" ><img src="./images/excel.gif" border="0"/></a> <a href="export_csv.php" target="_blank">Экспорт в CSV файл</a>
</td></tr></table>


<?php  }else{ ?>
	<center><h3>Нет звонков на условиях выборки.</h3></center>
<?php  } ?>
</center>
