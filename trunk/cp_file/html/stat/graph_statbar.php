<?php 
include_once(dirname(__FILE__) . "/lib/defines.php");
include_once(dirname(__FILE__) . "/lib/Class.Table.php");
include_once(dirname(__FILE__) . "/jpgraph_lib/jpgraph.php");
include_once(dirname(__FILE__) . "/jpgraph_lib/jpgraph_bar.php");
include_once(dirname(__FILE__) . "/jpgraph_lib/jpgraph_line.php");

// this variable specifie the debug type (0 => nothing, 1 => sql result, 2 => boucle checking, 3 other value checking)
$FG_DEBUG = 0;


getpost_ifset(array('min_call', 'fromstatsday_sday', 'days_compare', 'fromstatsmonth_sday', 'dsttype', 'sourcetype', 'clidtype', 'channel', 'resulttype', 'dst', 'src', 'clid', 'userfieldtype', 'userfield', 'accountcodetype', 'accountcode', 'type_gr'));


// http://localhost/Asterisk/asterisk-stat-v1_4/graph_stat.php?min_call=0&fromstatsday_sday=11&days_compare=2&fromstatsmonth_sday=2005-02&dsttype=1&sourcetype=1&clidtype=1&channel=&resulttype=&dst=1649&src=&clid=&userfieldtype=1&userfield=&accountcodetype=1&accountcode=

// The variable FG_TABLE_NAME define the table name to use
$FG_TABLE_NAME=DB_TABLENAME;

//$link = DbConnect();
$DBHandle  = DbConnect();

// The variable Var_col would define the col that we want show in your table
// First Name of the column in the html page, second name of the field
$FG_TABLE_COL = array();

/*******
Calldate Clid Src Dst Dcontext Channel Dstchannel Lastapp Lastdata Duration Billsec Disposition Amaflags Accountcode Uniqueid Serverid
*******/

$FG_TABLE_COL[]=array ("Calldate", "calldate", "18%", "center", "SORT", "19");
$FG_TABLE_COL[]=array ("Channel", "channel", "13%", "center", "", "30");
$FG_TABLE_COL[]=array ("Source", "src", "10%", "center", "", "30");
$FG_TABLE_COL[]=array ("Clid", "clid", "12%", "center", "", "30");
$FG_TABLE_COL[]=array ("Lastapp", "lastapp", "8%", "center", "", "30");

$FG_TABLE_COL[]=array ("Lastdata", "lastdata", "12%", "center", "", "30");
$FG_TABLE_COL[]=array ("Dst", "dst", "9%", "center", "SORT", "30");
//$FG_TABLE_COL[]=array ("Serverid", "serverid", "10%", "center", "", "30");
$FG_TABLE_COL[]=array ("Disposition", "disposition", "9%", "center", "", "30");
$FG_TABLE_COL[]=array ("Duration", "billsec", "6%", "center", "SORT", "30");


$FG_TABLE_DEFAULT_ORDER = "calldate";
$FG_TABLE_DEFAULT_SENS = "DESC";

// This Variable store the argument for the SQL query
$FG_COL_QUERY='calldate, billsec';
$FG_COL_QUERY_GRAPH='calldate, billsec';

// The variable LIMITE_DISPLAY define the limit of record to display by page
$FG_LIMITE_DISPLAY=100;

// Number of column in the html table
$FG_NB_TABLE_COL=count($FG_TABLE_COL);





if ($FG_DEBUG == 3) echo "<br>Table : $FG_TABLE_NAME  	- 	Col_query : $FG_COL_QUERY";
$instance_table = new Table($FG_TABLE_NAME, $FG_COL_QUERY);
$instance_table_graph = new Table($FG_TABLE_NAME, $FG_COL_QUERY_GRAPH);


if ( is_null ($order) || is_null($sens) ){
	$order = $FG_TABLE_DEFAULT_ORDER;
	$sens  = $FG_TABLE_DEFAULT_SENS;
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

  if ($_GET['before']) {
    if (strpos($SQLcmd, 'WHERE') > 0) { 	$SQLcmd = "$SQLcmd AND ";
    }else{     								$SQLcmd = "$SQLcmd WHERE "; }
    $SQLcmd = "$SQLcmd calldate<'".$_POST['before']."'";
  }
  if ($_GET['after']) {    if (strpos($SQLcmd, 'WHERE') > 0) {      $SQLcmd = "$SQLcmd AND ";
  } else {      $SQLcmd = "$SQLcmd WHERE ";    }
    $SQLcmd = "$SQLcmd calldate>'".$_GET['after']."'";
  }
  $SQLcmd = do_field($SQLcmd, 'clid');
  $SQLcmd = do_field($SQLcmd, 'src');
  $SQLcmd = do_field($SQLcmd, 'dst');
  $SQLcmd = do_field($SQLcmd, 'channel');
  
  $SQLcmd = do_field($SQLcmd, 'userfield');
  $SQLcmd = do_field($SQLcmd, 'accountcode');
  

$date_clause='';

$min_call= intval($min_call);
if (($min_call!=0) && ($min_call!=1)) $min_call=0;

if (!isset($fromstatsday_sday)){	
	$fromstatsday_sday = date("d");
	$fromstatsmonth_sday = date("Y-m");	
}


 

if (DB_TYPE == "postgres"){	
	if (isset($fromstatsday_sday) && isset($fromstatsmonth_sday)) 
	$date_clause.=" AND calldate < date'$fromstatsmonth_sday-$fromstatsday_sday'+ INTERVAL '1 DAY' AND calldate >= '$fromstatsmonth_sday-$fromstatsday_sday'";
}else{
	if (isset($fromstatsday_sday) && isset($fromstatsmonth_sday)) $date_clause.=" AND calldate < ADDDATE('$fromstatsmonth_sday-$fromstatsday_sday',INTERVAL 1 DAY) AND calldate >= '$fromstatsmonth_sday-$fromstatsday_sday'";  
}

//-- $date_clause=" AND calldate < date'$fromstatsmonth_sday-$fromstatsday_sday'+ INTERVAL '1 DAY' AND calldate >= '$fromstatsmonth_sday-$fromstatsday_sday 12:00:00'";
  
if (strpos($SQLcmd, 'WHERE') > 0) { 
	$FG_TABLE_CLAUSE = substr($SQLcmd,6).$date_clause; 
}elseif (strpos($date_clause, 'AND') > 0){
	$FG_TABLE_CLAUSE = substr($date_clause,5); 
}

if ($FG_DEBUG == 3) echo $FG_TABLE_CLAUSE;


//$list = $instance_table -> Get_list ($FG_TABLE_CLAUSE, $order, $sens, null, null, null, null);


$list_total = $instance_table_graph -> Get_list ($FG_TABLE_CLAUSE, 'calldate', 'ASC', null, null, null, null);


/**************************************/


$table_graph=array();
$table_graph_hours=array();
$numm=0;
foreach ($list_total as $recordset){
		$numm++;
		$mydate= substr($recordset[0],0,10);
		$mydate_hours= substr($recordset[0],0,13);
		//echo "$mydate<br>";
		if (is_array($table_graph_hours[$mydate_hours])){
			$table_graph_hours[$mydate_hours][0]++;
			$table_graph_hours[$mydate_hours][1]=$table_graph_hours[$mydate_hours][1]+$recordset[1];
		}else{
			$table_graph_hours[$mydate_hours][0]=1;
			$table_graph_hours[$mydate_hours][1]=$recordset[1];
		}
		
		
		if (is_array($table_graph[$mydate])){
			$table_graph[$mydate][0]++;
			$table_graph[$mydate][1]=$table_graph[$mydate][1]+$recordset[1];
		}else{
			$table_graph[$mydate][0]=1;
			$table_graph[$mydate][1]=$recordset[1];
		}
		if ($recordset[1]) {
			$table_graph_hours[$mydate_hours][2]++;
			$table_graph[$mydate][2]++;
		}
		    		
}

//print_r($table_graph_hours);
//exit();

$mmax=0;
$totalcall==0;
$totalminutes=0;
$totalcallsanswered=0;
foreach ($table_graph as $tkey => $data){	
	if ($mmax < $data[1]) $mmax=$data[1];
	$totalcall+=$data[0];
	$totalminutes+=$data[1];
	$totalcallsanswered+=$data[2];
}




/************************************************/


$datax1 = array_keys($table_graph_hours);
$datay1 = array_values ($table_graph_hours);

//$days_compare // 3
$nbday=1;  // in tableau_value and tableau_hours to select the day in which you store the data
//$min_call=0; // min_call variable : 0 > get the number of call 1 > number minutes


$table_subtitle[0]="Статистика : Звонки по часам";
$table_subtitle[1]="Статистика : ASR % по часам";
$table_subtitle[2]="Статистика : Звонки & ASR % по часам";



$table_colors[]="orange";
$table_colors[]="red@0.3";
$table_colors[]="blue@0.3";
$table_colors[]="red@0.3";

$jour = substr($datax1[0],8,2); //le jour courant 
$legend[0] = "Звонки";
$legend[1] = "ASR %";

//print_r ($table_graph_hours);
// Create the graph to compare the day
// extract all minutes/nb call for each hours 
$max_calls=0;
$max_asr=0;
foreach ($table_graph_hours as $key => $value) {
/*	
	$jour_suivant = substr($key,8,2);
	
	if($jour_suivant != $jour) {
		  $nbday++; 
		  $legend[$nbday] = substr($key,0,10);
		  $jour = $jour_suivant;
	}
  
*/	
	$heure = intval(substr($key,11,2));

	$tableau_value[0][$heure] = $value[0];
	if ($value[0] > $max_calls) $max_calls=$value[0];
	if ($value[0] ==0) $tableau_value[1][$heure] = 0;
	else $tableau_value[1][$heure] = $value[2]/$value[0]*100;
	if ($tableau_value[1][$heure] > $max_asr) $max_asr=$tableau_value[1][$heure];
	
	
	
}

if ($max_asr > 0) $coeff = $max_calls/$max_asr; 
else $coeff=1;

if ($type_gr == "callsasr") {
      for ($j=0; $j<24; $j++)
 {
              $tableau_value[1][$j]*=$coeff;
	      if ($tableau_value[1][$j]==0) $tableau_value[1][$j]=null;
      }
}  

// je remplie les cases vide par des 0
for ($i=0; $i<=$nbday; $i++)
      for ($j=0; $j<24; $j++)
              if (!isset($tableau_value[$i][$j])) $tableau_value[$i][$j]=0;

//Je remplace les 0 par null pour pour les heures 
$i = 23;
while ($tableau_value[$nbday][$i] == 0) {
      $tableau_value[$nbday][$i] = null;
      $i--;
}

// Setup the graph
$graph = new Graph(750,450);
$graph->SetMargin(40,40,45,90); //droit,gauche,haut,bas
$graph->SetMarginColor('white');
//$graph->SetScale("linlin");
$graph->SetScale("textlin");
$graph->yaxis->scale->SetGrace(3);

// Hide the frame around the graph
$graph->SetFrame(false);

// Setup title
$graph->title->SetFont(FF_VERDANA,FS_NORMAL,11);

// Note: requires jpgraph 1.12p or higher
$graph->SetBackgroundGradient('#FFFFFF','#CDDEFF:0.8',GRAD_HOR,BGRAD_PLOT);

$graph->tabtitle->SetFont(FF_VERDANA,FS_NORMAL,8);
$graph->tabtitle->SetWidth(TABTITLE_WIDTHFULL);

// Enable X and Y Grid
$graph->xgrid->Show();
$graph->xgrid->SetColor('gray@0.5');
$graph->ygrid->SetColor('gray@0.5');

$graph->yaxis->HideZeroLabel();
$graph->xaxis->HideZeroLabel();
$graph->ygrid->SetFill(true,'#EFEFEF@0.5','#CDDEFF@0.5');

// initialisaton fixe de AXE X
$tableau_hours[0] = array("00","01","02","03","04","05","06","07","08","09","10","11","12","13","14","15","16","17","18","19","20","21","22","23");
$graph->xaxis->SetTickLabels($tableau_hours[0]);  

// Setup X-scale
//$graph->xaxis->SetTickLabels($tableau_hours[0]);
$graph->xaxis->SetLabelAngle(90);

// Format the legend box
$graph->legend->SetColor('navy');
$graph->legend->SetFillColor('gray@0.8');
$graph->legend->SetLineWeight(1);
//$graph->legend->SetFont(FF_ARIAL,FS_BOLD,8);
$graph->legend->SetShadow('gray@0.4',3);
$graph->legend->SetAbsPos(40,90,'right','bottom');
$graph->legend->SetFont(FF_VERDANA,FS_NORMAL,8);

$indgraph=0;
while ($indgraph<=1){
	
	$bplot[$indgraph] = new BarPlot($tableau_value[$indgraph]);
	$bplot[$indgraph]->SetColor($table_colors[$indgraph]);
	$bplot[$indgraph]->SetWeight(2);
	$bplot[$indgraph]->SetFillColor($table_colors[$indgraph]);
	$bplot[$indgraph]->SetShadow();
	
$bplot[$indgraph]->SetLegend($legend[$indgraph]);

	$indgraph++;
	
}


if ($type_gr == "calls" ) {
    $bplot[0]->value->Show();
    $graph->tabtitle->Set($table_subtitle[0]);
    $graph->Add($bplot[0]);
}
elseif ($type_gr == "asr" ) {
    $bplot[1]->value->Show();
    $graph->tabtitle->Set($table_subtitle[1]);
    $graph->Add($bplot[1]);
}
else {

	$bp = new LinePlot($tableau_value[1]);
	$bp->SetColor("red");
	$bp->SetWeight(2);
	
$bp->SetLegend($legend[1]);
        $bplot[0]->value->Show();
//      $gbp = new GroupBarPlot (array($bplot[0],$bplot[2]));
     
      $graph->tabtitle->Set($table_subtitle[2]);
 
	$graph->Add($bplot[0]);
	$graph->Add($bp);
}


// Output the graph
$graph->Stroke();

?>
