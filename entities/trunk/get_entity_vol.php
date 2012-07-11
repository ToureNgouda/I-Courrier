<?php
require_once("core".DIRECTORY_SEPARATOR."class".DIRECTORY_SEPARATOR."class_request.php");
require_once("core".DIRECTORY_SEPARATOR."class".DIRECTORY_SEPARATOR."class_security.php");
require_once("core".DIRECTORY_SEPARATOR."class".DIRECTORY_SEPARATOR."class_manage_status.php");
require_once("apps".DIRECTORY_SEPARATOR.$_SESSION['config']['app_id'].DIRECTORY_SEPARATOR."class".DIRECTORY_SEPARATOR."class_list_show.php");
require_once('modules'.DIRECTORY_SEPARATOR.'reports'.DIRECTORY_SEPARATOR."class".DIRECTORY_SEPARATOR."class_graphics.php");

$_ENV['date_pattern'] = "/^[0-3][0-9]-[0-1][0-9]-[1-2][0-9][0-9][0-9]$/";


$graph = new graphics();
$req = new request();
$db = new dbquery();
$sec = new security();
$period_type = $_REQUEST['period_type'];
$status_obj = new manage_status();
$ind_coll = $sec->get_ind_collection('letterbox_coll');
$table = $_SESSION['collections'][$ind_coll]['table'];
$view = $_SESSION['collections'][$ind_coll]['view'];
$search_status = $status_obj->get_searchable_status();
$default_year = date('Y');
$report_type = $_REQUEST['report_type'];
$core_tools = new core_tools();
$core_tools->load_lang();

//Limitation aux documents pouvant être recherchés
$str_status = '(';
for($i=0;$i<count($search_status);$i++)
{
	$str_status .= "'".$search_status[$i]['ID']."',";
}
$str_status = preg_replace('/,$/', ')', $str_status);

if($period_type == 'period_year')
{
	if(empty($_REQUEST['the_year']) || !isset($_REQUEST['the_year']))
	{
		?>
		<div class="error"><?php echo _YEAR.' '._MISSING;?></div>
		<?php
		exit();
	}
	if(	!preg_match('/^[1-2](0|9)[0-9][0-9]$/', $_REQUEST['the_year']))
	{
		?>
		<div class="error"><?php echo _YEAR.' '._WRONG_FORMAT;?></div>
		<?php
		exit();
	}
	$where_date = " and ".$req->extract_date('creation_date', 'year')." = '".$_REQUEST['the_year']."'";
	$date_title = _FOR_YEAR.' '.$_REQUEST['the_year'];
}
else if($period_type == 'period_month')
{
	$arr_month = array('01','02','03','04','05','06','07','08','09','10','11','12');
	if(empty($_REQUEST['the_month']) || !isset($_REQUEST['the_month']))
	{
		?>
		<div class="error"><?php echo _MONTH.' '._MISSING;?></div>
		<?php
		exit();
	}
	if(	!in_array($_REQUEST['the_month'], $arr_month))
	{
		?>
		<div class="error"><?php echo _MONTH.' '._WRONG_FORMAT;?></div>
		<?php
		exit();
	}
	$where_date = " and ".$req->extract_date('creation_date', 'year')." = '".$default_year."' and ".$req->extract_date('creation_date', 'month')." = '".$_REQUEST['the_month']."'";
	$month = '';
	switch($_REQUEST['the_month'])
	{
		case '01':
			$month = _JANUARY;
			break;
		case '02':
			$month = _FEBRUARY;
			break;
		case '03':
			$month = _MARCH;
			break;
		case '04':
			$month = _APRIL;
			break;
		case '05':
			$month = _MAY;
			break;
		case '06':
			$month = _JUNE;
			break;
		case '07':
			$month = _JULY;
			break;
		case '08':
			$month = _AUGUST;
			break;
		case '09':
			$month = _SEPTEMBER;
			break;
		case '10':
			$month = _OCTOBER;
			break;
		case '11':
			$month = _NOVEMBER;
		case '12':
			$month = _DECEMBER;
			break;
		default:
			$month = '';
	}
	$date_title = _FOR_MONTH.' '.$month;
}
else if($period_type == 'custom_period')
{
	if (empty($_REQUEST['date_start']) && empty($_REQUEST['date_fin'])){
		echo '<div class="error">'._DATE.' '._IS_EMPTY.''.$_REQUEST['date_start'].'</div>';
		exit();
	}
	
	
	if( preg_match($_ENV['date_pattern'],$_REQUEST['date_start'])==false  && $_REQUEST['date_start'] <> ''  )
	{
		
		echo '<div class="error">'._WRONG_DATE_FORMAT.' : '.$_REQUEST['date_start'].'</div>';
		exit();
	
	}
	if( preg_match($_ENV['date_pattern'],$_REQUEST['date_fin'])==false && $_REQUEST['date_fin'] <> '' )
	{
		
		echo '<div class="error">'._WRONG_DATE_FORMAT.' : '.$_REQUEST['date_fin'].'</div>';
		exit();

	}

	if(isset($_REQUEST['date_start']) && $_REQUEST['date_start'] <> '')
	{
		$where_date  .= " AND ".$req->extract_date('creation_date')." > '".$db->format_date_db($_REQUEST['date_start'])."'";
		$date_title .= strtolower(_SINCE).' '.$_REQUEST['date_start'].' ';
	}

	if(isset($_REQUEST['date_fin']) && $_REQUEST['date_fin'] <> '')
	{
		$where_date  .= " AND ".$req->extract_date('creation_date')." < '".$db->format_date_db($_REQUEST['date_fin'])."'";
		$date_title.= strtolower(_FOR).' '.$_REQUEST['date_fin'].' ';
	}
	if(empty($where_date))
	{
		$where_date = $req->extract_date('creation_date', 'year')." = '".$default_year."'";
		$date_title = _FOR_YEAR.' '.$default_year;
	}
}
else
{
	?>
	<div class="error"><?php echo _PERIOD.' '._MISSING;?></div>
	<?php
	exit();
}





$has_data = false;
$title = _MAIL_VOL_BY_ENT_REPORT.' '.$date_title ;
$db = new dbquery();
if($report_type == 'graph')
{
	$vol_an = array();
	$vol_mois = array();
	$_SESSION['labels1'] = array();
}
elseif($report_type == 'array')
{
	$data = array();
}
//Utilisation de la clause de sécurité de Maarch

$where_clause = $sec->get_where_clause_from_coll_id('letterbox_coll');
//var_dump($where_clause);
if ($where_clause)
	$where_clause = " and ".$where_clause;


$db->query("select count(*) as total, entity_label, destination from ".$view." where status in ".$str_status."   ".$where_date.$where_clause." group by destination,entity_label");
//$db->show();
while($res = $db->fetch_object())
{
/*
	$db->query("select count(*) as total from ".$view." where status in ".$str_status."   ".$where_date." and category_id = '".$key."'");
*/
	
	if($report_type == 'graph')
	{
		array_push($_SESSION['labels1'], $db->wash_html($res->entity_label, 'NO_ACCENT'));
		array_push($vol_an, $res->total);
	}
	elseif($report_type == 'array')
	{
		array_push($data, array('LABEL' => $res->entity_label, 'VALUE' => $res->total ));
	}

	if($res->total > 0)
	{
		$has_data = true;
	}
}
if($report_type == 'graph')
{
	$src1 = $_SESSION['config']['businessappurl']."index.php?display=true&module=reports&page=graphs&type=pie&largeur=1000&hauteur=300&title=".$title;

	for($i=0;$i<count($vol_an);$i++)
	{
		$src1 .= "&values[]=".$vol_an[$i];
	}
}
elseif($report_type == 'array')
{
	array_unshift($data, array('LABEL' => _ENTITY, 'VALUE' => _NB_MAILS1));
}
	//echo $src1;
if($has_data)
{
	if($report_type == 'graph')
	{
		?>
			<img src="<?php echo $src1;?>" alt=""/><br/><br/>
		<?php
	}
	elseif($report_type == 'array')
	{
		$graph->show_stats_array($title, $data);
	}
}
else
{
	echo '<br/><br/><div class="error">'._NO_DATA_MESSAGE.'</div>';
}
exit();

?>