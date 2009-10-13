<?php
session_name('PeopleBox');
session_start();
require_once($_SESSION['pathtocoreclass']."class_functions.php");
require_once($_SESSION['pathtocoreclass']."class_db.php");
require_once($_SESSION['pathtocoreclass']."class_core_tools.php");

$core_tools = new core_tools();
$core_tools->test_user();
$core_tools->load_lang();

require_once($_SESSION['pathtocoreclass']."class_request.php");
require_once($_SESSION['config']['businessapppath']."class".DIRECTORY_SEPARATOR."class_list_show.php");
$func = new functions();

$what = "all";
$where = "";


if(isset($_GET['what']) && !empty($_GET['what']))
{
	if($_GET['what'] == "all"){
		$what = "all";

	}
	else{
		$what = addslashes($func->wash($_GET['what'], "no", "", "no"));
		$where = "(".$_SESSION['tablename']['fold_folders'].".folder_name like '".strtolower($what)."%') ";
	}
}
	$db = new dbquery();
	$db->connect();

	$select[$_SESSION['tablename']['fold_folders']] = array();
	array_push($select[$_SESSION['tablename']['fold_folders']], "folder_id","folder_name" );

	$req = new request();

	$tab = $req->select($select, $where, " order by ".$_SESSION['tablename']['fold_folders'].".folder_name ", $_SESSION['config']['databasetype'], $limit="500",false);

for ($i=0;$i<count($tab);$i++)
{
		for ($j=0;$j<count($tab[$i]);$j++)
		{
			foreach(array_keys($tab[$i][$j]) as $value)
			{

				if($tab[$i][$j][$value]== "folder_id" )
				{
					$tab[$i][$j]["folder_id"]= $tab[$i][$j]['value'];
					$tab[$i][$j]["label"]= _ID;
					$tab[$i][$j]["size"]="10";
					$tab[$i][$j]["label_align"]="left";
					$tab[$i][$j]["align"]="left";
					$tab[$i][$j]["valign"]="bottom";
					$tab[$i][$j]["show"]=true;
				}

				if($tab[$i][$j][$value]=='folder_name')
				{
					$tab[$i][$j]['value']= $req->show_string($tab[$i][$j]['value']);
					$tab[$i][$j]['lastname']= $tab[$i][$j]['value'];
					$tab[$i][$j]["label"]=_LASTNAME;
					$tab[$i][$j]["size"]="50";
					$tab[$i][$j]["label_align"]="left";
					$tab[$i][$j]["align"]="left";
					$tab[$i][$j]["valign"]="bottom";
					$tab[$i][$j]["show"]=true;
				}
			}
		}
	}


	if(isset($_REQUEST['field']) && !empty($_REQUEST['field']))
	{
		//$_SESSION['chosen_folder'] = $_REQUEST['field'];
		?>
			<script language="javascript">
				var item = window.opener.$('folder_id');
				if(item)
				{
					item.value = '<?php echo $_REQUEST['field'];?>';
					self.close();
				}
				//window.opener.document.location.href = '<? echo $_SESSION['urltomodules'];?>stats/target_stats.php?id=folder_hist';self.close();
			</script>
			<?
		exit();
	}

//here we loading the html
$core_tools->load_html();
//here we building the header
$core_tools->load_header(_CHOOSE_FOLDER);
$time = $core_tools->get_session_time_expire();
?>
<body onLoad="javascript:setTimeout(window.close, <? echo $time;?>*60*1000);">
<?php
$nb = count($tab);

$list=new list_show();
$list->list_doc($tab, $nb, _FOLDERS_LIST,'folder_id',$name = "select_folder_report",'folder_id','',false,true,'get',$_SESSION['urltomodules'].'folder/select_folder_report.php',_CHOOSE_FOLDER, false, false, true,false, true, true,  true, false, '', '',  true, _ALL_FOLDERS,_FOLDER);
?>

</body>
</html>