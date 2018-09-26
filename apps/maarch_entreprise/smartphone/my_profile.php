<?php
if (file_exists('../../../core/init.php')) {
    include_once '../../../core/init.php';
}
if (!isset($_SESSION['config']['corepath'])) {
    header('location: ../../../');
}
require_once('core/class/class_functions.php');
require_once('core/class/class_core_tools.php');
require_once('core/class/class_db_pdo.php');
require_once('core/core_tables.php');
require_once('apps/maarch_entreprise/apps_tables.php');
$core = new core_tools();
$core->load_lang();
$db = new Database();

$stmt = $db->query(
    "SELECT u.group_desc FROM " . USERGROUP_CONTENT_TABLE . " uc, "
    . USERGROUPS_TABLE ." u where uc.user_id = ? and uc.group_id = u.group_id"
    . " order by u.group_desc",array($_SESSION['user']['UserId']));
$groupList = '';
if ($stmt->rowCount() < 1) {
    $groupList = 'no group';
} else {
    while ($line = $stmt->fetchObject()) {
        $groupList .= '<div class="row">
        <br>
            ' . $line->group_desc . '&nbsp;&nbsp;
        </div>';
    }
}
if($core->is_module_loaded("entities")) {
    $stmt = $db->query(
        "SELECT e.entity_label FROM " . $_SESSION['tablename']['ent_users_entities'] 
        . " ue, ".$_SESSION['tablename']['ent_entities'] 
        . " e where ue.user_id = ? and ue.entity_id = e.entity_id order by e.entity_label",array($_SESSION['user']['UserId']));
    $entityList = '';
    if ($stmt->rowCount() < 1) {
        $entityList = 'no entity';
    } else {
        while ($line = $stmt->fetchObject()) {
            $entityList .= '<div class="row">
            <br>
                ' . $line->entity_label . '&nbsp;&nbsp;
            </div>';
        }
    }
}
?>
<div id="settings" title="<?php echo _MY_INFO;?>" class="panel">
    <h2><span class="fa fa-user"></span></a></li>My infos</h2>
    <fieldset>
		<div class="row">
			<table>
				<tr>
					<td width="50%" align="left">
						<label><b>ID</b></label>
					</td>
					<td width="50%" align="right">
						<input type="text" name="userId" readonly="readonly" value="<?php functions::xecho($_SESSION['user']['UserId']); ?>"/>
					</td>
				</tr>
			</table>
		</div>
        <div class="row">
			<table>
				<tr>
					<td width="50%" align="left">
						<label><b><?php echo _LASTNAME ?></b></label>
					</td>
					<td width="50%" align="right">
						<input name="Lastname" readonly="readonly" value="<?php functions::xecho(functions::show_string($_SESSION['user']['LastName'])); ?>"/>
					</td>
				</tr>
			</table>
        </div>
        <div class="row">
			<table>
				<tr>
					<td width="50%" align="left">
						<label><b><?php echo _FIRSTNAME ?></b></label>
					</td>
					<td width="50%" align="right">
						<input name="Firstname" readonly="readonly" value="<?php functions::xecho(functions::show_string($_SESSION['user']['FirstName'])); ?>"/>
					</td>
				</tr>
			</table>
        </div>
        <!--div class="row">
            <label>Department</label>
            <input name="Department" readonly="readonly" value="<?php functions::xecho(functions::show_string($_SESSION['user']['department'])); ?>"/>
        </div-->
        <div class="row">
			<table>
				<tr>
					<td width="50%" align="left">
						<label><b><?php echo _PHONE_NUMBER ?></b></label>
					</td>
					<td width="50%" align="right">
                        <a href="tel:<?php functions::xecho(functions::show_string($_SESSION['user']['Phone'])) ?>"><input name="Phone" readonly="readonly" value="<?php functions::xecho(functions::show_string($_SESSION['user']['Phone'])); ?>"/></a>
					</td>
				</tr>
			</table>
        </div>
        <div class="row">
			<table>
				<tr>
					<td width="50%" align="left">
						<label><b><?php echo _EMAIL ?></b></label>
					</td>
					<td width="50%" align="right">
						<input name="Mail" readonly="readonly" value="<?php functions::xecho(functions::show_string($_SESSION['user']['Mail'])); ?>"/>
					</td>
				</tr>
			</table>
        </div>
    </fieldset>
    <h2><span class="fa fa-users"></span>My groups</h2>
    <fieldset>
        <?php
        echo $groupList;
        ?>
    </fieldset>
    <?php
    if($core->is_module_loaded("entities")) {
        ?>
        <h2><span class="fa fa-sitemap"></span>My entities</h2>
        <fieldset>
            <?php
            echo $entityList;
            ?>
        </fieldset>
        <?php
    }
    ?>
</div>
