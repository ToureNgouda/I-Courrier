<?php
$confirm = false;
$etapes = array('form');
$frm_width='355px';
$frm_height = '500px';

 function get_form_txt($values, $path_manage_action,  $id_action, $table, $module, $coll_id, $mode )
 {
	$services = array();
	$db = new dbquery();
	$db->connect();
	if(!empty($_SESSION['user']['redirect_groupbasket'][$_SESSION['current_basket']['id']][$id_action]['entities']))
	{
		$db->query("select entity_id, entity_label from ".$_SESSION['tablename']['ent_entities']." where entity_id in (".$_SESSION['user']['redirect_groupbasket'][$_SESSION['current_basket']['id']][$id_action]['entities'].") and enabled= 'Y' order by entity_label");
		while($res = $db->fetch_object())
		{
			array_push($services, array( 'ID' => $res->entity_id, 'LABEL' => $db->show_string($res->entity_label)));
		}
	}
	$users = array();
	if(!empty($_SESSION['user']['redirect_groupbasket'][$_SESSION['current_basket']['id']][$id_action]['users_entities']) )
	{
		$db->query("select distinct ue.user_id, u.lastname, u.firstname from ".$_SESSION['tablename']['ent_users_entities']." ue, ".$_SESSION['tablename']['users']." u where ue.entity_id in (".$_SESSION['user']['redirect_groupbasket'][$_SESSION['current_basket']['id']][$id_action]['users_entities'].") and u.user_id = ue.user_id  order by u.lastname asc");
		while($res = $db->fetch_object())
		{
			array_push($users, array( 'ID' => $res->user_id, 'NOM' => $db->show_string($res->lastname), "PRENOM" => $db->show_string($res->firstname)));
		}
	}

	$frm_str = '<div id="frm_error_'.$id_action.'" class="error"></div>';
	$frm_str .= '<h2 class="title">'._REDIRECT_MAIL.' '._NUM;
	$values_str = '';
	for($i=0; $i < count($values);$i++)
	{
		$values_str .= $values[$i].', ';
	}
	$values_str = preg_replace('/, $/', '', $values_str);
	$frm_str .= $values_str;
	$frm_str .= '</h2><br/><br/>';
	if(!empty($_SESSION['user']['redirect_groupbasket'][$_SESSION['current_basket']['id']][$id_action]['entities']))
	{
		$frm_str .= '<hr />';
		$frm_str .='<div id="form2">';
		$frm_str .= '<form name="frm_redirect_dep" id="frm_redirect_dep" method="post" class="forms" action="#">';
		$frm_str .= '<input type="hidden" name="chosen_action" id="chosen_action" value="end_action" />';
				$frm_str .='<p>';
					$frm_str .= '<label><b>'._REDIRECT_TO_OTHER_DEP.' :</b></label>';
					$frm_str .= '<select name="department" id="department" onchange="change_entity(this.options[this.selectedIndex].value, \''.$_SESSION['urltomodules'].'entities/load_listinstance.php'.'\', \'diff_list_div_redirect\', \'redirect\');">';
						$frm_str .='<option value="">'._CHOOSE_DEPARTMENT.'</option>';
					   for($i=0; $i < count($services); $i++)
					   {
							$frm_str .='<option value="'.$services[$i]['ID'].'" >'.$db->show_string($services[$i]['LABEL']).'</option>';
					   }
					$frm_str .='</select>';
					$frm_str .=' <input type="button" name="redirect_dep" value="'._REDIRECT.'" id="redirect_dep" class="button" onclick="valid_action_form( \'frm_redirect_dep\', \''.$path_manage_action.'\', \''. $id_action.'\', \''.$values_str.'\', \''.$table.'\', \''.$module.'\', \''.$coll_id.'\', \''.$mode.'\');" />';

				$frm_str .= '<div id="diff_list_div_redirect" class="scroll_div" style="height:150px;display:none;"></div>';
				$frm_str .='</p>';
			$frm_str .='</form>';
		$frm_str .='</div>';
	}
	if(!empty($_SESSION['user']['redirect_groupbasket'][$_SESSION['current_basket']['id']][$id_action]['users_entities']))
	{
		$frm_str .='<hr />';
			$frm_str .='<div id="form3">';
				$frm_str .= '<form name="frm_redirect_user" id="frm_redirect_user" method="post" class="forms" action="#">';
				$frm_str .= '<input type="hidden" name="chosen_action" id="chosen_action" value="end_action" />';
				$frm_str .='<p>';
					$frm_str .='<label><b>'._REDIRECT_TO_USER.' :</b></label>';
				    $frm_str .='<select name="user" id="user">';
						$frm_str .='<option value="">'._CHOOSE_USER2.'</option>';
					    for($i=0; $i < count($users); $i++)
					   {
						$frm_str .='<option value="'.$users[$i]['ID'].'">'.$users[$i]['NOM'].' '.$users[$i]['PRENOM'].'</option>';
					   }
					$frm_str .='</select>';
					$frm_str .=' <input type="button" name="redirect_user" id="redirect_user" value="'._REDIRECT.'" class="button" onclick="valid_action_form( \'frm_redirect_user\', \''.$path_manage_action.'\', \''. $id_action.'\', \''.$values_str.'\', \''.$table.'\', \''.$module.'\', \''.$coll_id.'\', \''.$mode.'\');"  />';
				$frm_str .='</p>';
			$frm_str .='</form>';
		$frm_str .='</div>';
	}
	$frm_str .='<hr />';
	$frm_str .='<div align="center">';
			$frm_str .='<input type="button" name="cancel" id="cancel" class="button"  value="'._CANCEL.'" onclick="destroyModal(\'modal_'.$id_action.'\');"/>';
	$frm_str .='</div>';
	return addslashes($frm_str);
 }

 function check_form($form_id,$values)
 {
	if($form_id == 'frm_redirect_dep')
	{
		if(count($values) < 1)
		{
			$_SESSION['action_error'] = _MUST_CHOOSE_DEP;
			return false;
		}
		else
		{
			return true;
		}
	}
	else if($form_id == 'frm_redirect_user')
	{
		if(count($values) < 1)
		{
			$_SESSION['action_error'] = _MUST_CHOOSE_USER;
			return false;
		}
		else
		{
			return true;
		}
	}
	else
	{
		$_SESSION['action_error'] = _FORM_ERROR;
		return false;
	}
 }

 function manage_form($arr_id, $history, $id_action, $label_action, $status,  $coll_id, $table, $values_form )
 {
	if(empty($values_form) || count($arr_id) < 1)
	{
		return false;
	}
	$db = new dbquery();
	$db->connect();

	require_once($_SESSION['pathtomodules'].'entities'.DIRECTORY_SEPARATOR.'class'.DIRECTORY_SEPARATOR.'class_manage_listdiff.php');
	$list = new diffusion_list();
	$arr_list = '';
	//$db->show_array($arr_id);
	for($j=0; $j<count($values_form); $j++)
	{
		$msg = _TO." ".$db->protect_string_db($values_form[$j]['VALUE']);
		if($values_form[$j]['ID'] == "department")
		{
			for($i=0; $i < count($arr_id); $i++)
			{
				$arr_list .= $arr_id[$i].'#';;
				$db2 = new dbquery();
				$db2->connect();
				$db->query("update ".$table." set destination = '".$db->protect_string_db($values_form[$j]['VALUE'])."' where res_id = ".$arr_id[$i]);
				if(isset($_SESSION['redirect']['diff_list']['dest']['user_id']) && !empty($_SESSION['redirect']['diff_list']['dest']['user_id']))
				{
					$db->query("update ".$table." set dest_user = '".$db->protect_string_db($_SESSION['redirect']['diff_list']['dest']['user_id'])."' where res_id = ".$arr_id[$i]);
				}

				if($_SESSION['features']['dest_to_copy_during_redirection'] == 'true')
				{
					array_push($_SESSION['redirect']['diff_list']['copy']['users'] ,array('user_id' => $_SESSION['user']['UserId'], 'lastname' => $_SESSION['user']['LastName'], 'firstname' => $_SESSION['user']['FirstName'], 'entity_id' => $_SESSION['user']['primaryentity']['id']) );
				}
				$params = array('mode'=> 'listinstance', 'table' => $_SESSION['tablename']['ent_listinstance'], 'coll_id' => $coll_id, 'res_id' => $arr_id[$i], 'user_id' => $_SESSION['user']['UserId'], 'concat_list' => true);
				$list->load_list_db($_SESSION['redirect']['diff_list'], $params);
			}
			$_SESSION['action_error'] = _REDIRECT_TO_DEP_OK;

			return array('result' => $arr_list, 'history_msg' => $msg );
		}
		elseif($values_form[$j]['ID'] == "user")
		{
			for($i=0; $i < count($arr_id); $i++)
			{
				$arr_list .= $arr_id[$i].'#';
				// Update dest_user in res table
				$db->query("update ".$table." set dest_user = '".$db->protect_string_db($values_form[$j]['VALUE'])."' where res_id = ".$arr_id[$i]);
				//$db->show();
				// Update listinstance
				$difflist['dest'] = array();
				$difflist['copy'] = array();
				$difflist['copy']['users'] = array();
				$difflist['copy']['entities'] = array();

				$difflist['dest']['user_id'] = $values_form[$j]['VALUE'];
				if($_SESSION['features']['dest_to_copy_during_redirection'] == 'true')
				{
					array_push($difflist['copy']['users'],array('user_id' => $_SESSION['user']['UserId'], 'lastname' => $_SESSION['user']['LastName'], 'firstname' => $_SESSION['user']['FirstName'], 'entity_id' => $_SESSION['user']['primaryentity']['id'] ));
				}
				$params = array('mode'=> 'listinstance', 'table' => $_SESSION['tablename']['ent_listinstance'], 'coll_id' => $coll_id, 'res_id' => $arr_id[$i], 'user_id' => $_SESSION['user']['UserId'], 'concat_list' => true);
				$list->load_list_db($difflist, $params);
			}
			$_SESSION['action_error'] = _REDIRECT_TO_USER_OK;
			return array('result' => $arr_list, 'history_msg' => $msg);
		}
	}
	return false;
 }


function manage_status($arr_id, $history, $id_action, $label_action, $status, $coll_id, $table)
{
	$db = new dbquery();
	$db->connect();
	for($i=0; $i<count($arr_id );$i++)
	{
		$req = $db->query("update ".$table. " set status = '".$status."' where res_id = ".$arr_id[$i], true);
		if(!$req)
		{
			$_SESSION['action_error'] = _SQL_ERROR;
			return false;
		}
	}
	return true;
 }

function manage_unlock($arr_id, $history, $id_action, $label_action, $status, $coll_id, $table)
{
	$db = new dbquery();
	$db->connect();
	for($i=0; $i<count($arr_id );$i++)
	{
		$req = $db->query("update ".$table. " set video_user = '', video_time = 0 where res_id = ".$arr_id[$i], true);

		if(!$req)
		{
			$_SESSION['action_error'] = _SQL_ERROR;
			return false;
		}
	}
	return true;
 }
?>
