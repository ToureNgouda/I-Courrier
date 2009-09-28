<?php

/**
 * $_SESSION['m_admin']['entity']['listmodel'] Structure :
 *
 * $_SESSION['m_admin']['entity']['listmodel']['dest']['user_id'] = 'demo'
 * 													  ['lastname'] = 'Smith'
 * 													  ['firstname'] = 'John'
 * 													  ['entity_id'] = 'ENTITY1'
 * 													  ['entity_id'] = 'IT Departement'
 *
 * 											  ['copy']['entities'][$i]['entity_id'] = 'ENTITY1'
 * 											  						  ['entity_label'] = 'IT Departement'
 *
 *  										  		  ['users'][$i]['user_id'] = 'demo'
 *  															   ['lastname'] = 'Smith'
 * 													          	   ['firstname'] = 'John'
 * 													  			   ['entity_id'] = 'ENTITY1'
 * 													  			   ['entity_id'] = 'IT Departement'
 *
 **/
//print_r($_SESSION['m_admin']['entity']['listmodel']);exit;
if($_SESSION['service_tag'] == 'entity_add')
{
	if(!isset($_SESSION['m_admin']['entity']['listmodel']))
	{
		$_SESSION['m_admin']['entity']['listmodel'] = array();
		$_SESSION['m_admin']['entity']['listmodel']['dest'] = array();
		$_SESSION['m_admin']['entity']['listmodel']['copy'] = array();
		$_SESSION['m_admin']['entity']['listmodel']['copy']['users'] = array();
		$_SESSION['m_admin']['entity']['listmodel']['copy']['entities'] = array();
	}
}
elseif($_SESSION['service_tag'] == 'entity_up')
{
	if(!isset($_SESSION['m_admin']['entity']['listmodel']))
	{
		require_once($_SESSION['pathtomodules'].'entities'.DIRECTORY_SEPARATOR.'class'.DIRECTORY_SEPARATOR.'class_manage_listdiff.php');
		$listdiff = new diffusion_list();
		$_SESSION['m_admin']['entity']['listmodel'] =  $listdiff->get_listmodel_from_entity($_SESSION['m_admin']['entity']['entityId']);
	}
}
elseif($_SESSION['service_tag'] == 'entities_list_init')
{
	//
}
elseif($_SESSION['service_tag'] == 'entity_check')
{
	if((count($_SESSION['m_admin']['entity']['listmodel']['copy']['users']) > 0 || count($_SESSION['m_admin']['entity']['listmodel']['copy']['entities']) > 0) && (!isset($_SESSION['m_admin']['entity']['listmodel']['dest']['user_id']) || empty($_SESSION['m_admin']['entity']['listmodel']['dest']['user_id'])))
	{
		$_SESSION['error'] .= _DEST_MANDATORY;
	}
}
elseif($_SESSION['service_tag'] == 'entity_add_db' || $_SESSION['service_tag'] == 'entity_up_db')
{
	require_once($_SESSION['pathtomodules'].'entities'.DIRECTORY_SEPARATOR.'class'.DIRECTORY_SEPARATOR.'class_manage_listdiff.php');
	$params  = array('mode' => 'listmodel', 'table' => $_SESSION['tablename']['ent_listmodels'], 'object_id' => $_SESSION['m_admin']['entity']['entityId'], 'coll_id' => 'letterbox_coll');
	$diff_list = new diffusion_list();
	$diff_list->load_list_db($_SESSION['m_admin']['entity']['listmodel'], $params);
}
if($_SESSION['service_tag_form'] == 'formentity')
{
	$_SESSION['service_tag_form'] = "";
	?>
	<div id="inner_content" class="clearfix">
	<div id="listmodel_box" class="block">
		<p>
			<?php 
			if(isset($_SESSION['m_admin']['entity']['listmodel']['dest']['user_id']) && !empty($_SESSION['m_admin']['entity']['listmodel']['dest']['user_id']))
			{
				?>
				<h2 class="tit"><?php echo _LINKED_DIFF_LIST;?> : </h2>
				<p class="sstit"><?php echo _RECIPIENT;?></p>
				<table cellpadding="0" cellspacing="0" border="0" class="listingsmall list_diff spec">
					 <tr class="col">
					 	<td><img src="<?php echo $_SESSION['urltomodules'].'entities/img/manage_users_entities_b_small.gif';?>" alt="<? echo _USER;?>" title="<? echo _USER;?>" /></td>
						<td><?php echo $_SESSION['m_admin']['entity']['listmodel']['dest']['firstname'];?></td>
						<td><?php echo $_SESSION['m_admin']['entity']['listmodel']['dest']['lastname'];?></td>
						<td><?php echo $_SESSION['m_admin']['entity']['listmodel']['dest']['entity_label']; ?></td>
					 </tr>
				</table>
				<br/>
				<?php 
				if(count($_SESSION['m_admin']['entity']['listmodel']['copy']['users']) > 0 || count($_SESSION['m_admin']['entity']['listmodel']['copy']['entities']) > 0)
				{
					?>
					 <p class="sstit"><?php echo _TO_CC;?></p>
						<table cellpadding="0" cellspacing="0" border="0" class="listingsmall liste_diff spec">
							<?php $color = ' class="col"';
							for($i=0;$i<count($_SESSION['m_admin']['entity']['listmodel']['copy']['entities']);$i++)
							{
							 	if($color == ' class="col"')
								{
									$color = '';
								}
								else
								{
									$color = ' class="col"';
								}
									?>
								<tr <?php echo $color; ?>>
							  		<td><img src="<?php echo $_SESSION['urltomodules'].'entities/img/manage_entities_b_small.gif';?>" alt="<? echo _ENTITY;?>" title="<? echo _ENTITY;?>" /></td>
									<td><?php echo $_SESSION['m_admin']['entity']['listmodel']['copy']['entities'][$i]['entity_id'];?></td>
									<td colspan="2"><?php echo $_SESSION['m_admin']['entity']['listmodel']['copy']['entities'][$i]['entity_label'];?></td>
								</tr>
								<?php 
							}
						 	for($i=0;$i<count($_SESSION['m_admin']['entity']['listmodel']['copy']['users']);$i++)
							{
							 	if($color == ' class="col"')
								{
									$color = '';
								}
								else
								{
									$color = ' class="col"';
								}
								?>
							    <tr <?php echo $color; ?>>
									<td><img src="<?php echo $_SESSION['urltomodules'].'entities/img/manage_users_entities_b_small.gif';?>" alt="<? echo _USER;?>" title="<? echo _USER;?>" /></td>
									<td><?php echo $_SESSION['m_admin']['entity']['listmodel']['copy']['users'][$i]['firstname'];?></td>
									<td><?php echo $_SESSION['m_admin']['entity']['listmodel']['copy']['users'][$i]['lastname'];?></td>
									<td><?php echo $_SESSION['m_admin']['entity']['listmodel']['copy']['users'][$i]['entity_label']; ?></td>
								</tr>
								<?php
							}
							?>
						</table>
					<?php
				}
				/// TO DO : use modal instead of popup
				?>
				<p class="buttons">
					<input type="button" onclick="window.open('<?php echo $_SESSION['urltomodules'].'entities/';?>creation_listmodel.php?what=A', '', 'scrollbars=yes,menubar=no,toolbar=no,status=no,resizable=yes,width=1024,height=650,location=no');" class="button" value="<?php echo _MODIFY_LIST;?>" />
				</p>
				<?php 
			}
			else
			{
				?>
				<h2 class="tit"><?php echo _NO_LINKED_DIFF_LIST;?>.</h2>
				<p class="buttons">
					<p>
						<input type="button" onclick="window.open('<?php echo $_SESSION['urltomodules'].'entities/';?>creation_listmodel.php', '', 'scrollbars=yes,menubar=no,toolbar=no,status=no,resizable=yes,width=1024,height=650,location=no');" class="button" value="<?php echo _CREATE_LIST;?>" />
					</p>
				</p>
				<?php 
			}
		?>
		</p>
	</div>
	<?php
}
?>
