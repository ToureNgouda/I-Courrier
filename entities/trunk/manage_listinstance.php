<?php
/**
* File : manage_listinstance.php
*
* Pop up used to create and modify diffusion lists instances
*
* @package Maarch LetterBox 2.3
* @version 1.0
* @since 06/2006
* @license GPL
* @author  Claire Figueras  <dev@maarch.org>
*/

require 'modules/entities/entities_tables.php';
require 'core/core_tables.php';

function cmpEntity($a, $b)
{
    return strcmp($a["entity_label"], $b["entity_label"]);
}
function cmpUsers($a, $b)
{
    return strcmp($a["lastname"], $b["lastname"]);
}

$core = new core_tools();
$core->load_lang();

$func = new functions();


$db = new dbquery();
$db->connect();

if (isset($_POST['what_users']) && ! empty($_POST['what_users'])) {
    $_GET['what_users'] = $_POST['what_users'];
}
if (isset($_POST['what_services']) && ! empty($_POST['what_services'])) {
    $_GET['what_services'] = $_POST['what_services'];
}

$users = array();
$entities = array();
$whereUsers = '';
$whereEntities = '';
$orderByUsers = '';
$orderByEntities = '';
$whereEntitiesUsers = '';
$what = "";
$whatUsers = '';
$whatServices = '';
$onlyCc = false;
$noDelete = false;
$redirect_groupbasket = false;

if (isset($_SESSION['current_basket']) && count($_SESSION['current_basket']) > 0) {
    if(is_array($_SESSION['user']['redirect_groupbasket'][$_SESSION['current_basket']['id']])) {
        $redirect_groupbasket = current($_SESSION['user']['redirect_groupbasket'][$_SESSION['current_basket']['id']]);
    }
    if(empty($redirect_groupbasket['entities'])) {
        $redirect_groupbasket['entities'] = $db->empty_list();
    }
    if(empty($redirect_groupbasket['users_entities'])) {
        $redirect_groupbasket['users_entities'] = $db->empty_list();
    }
}

if (isset($_REQUEST['only_cc'])) {
    $onlyCc = true;
}

if (isset($_REQUEST['no_delete'])) {
    $noDelete = true;
}
$isWhatUsers = false;
if (isset($_GET['what_users']) && ! empty($_GET['what_users'])) {
    $whatUsers = $func->protect_string_db(
        $func->wash($_GET['what_users'], "no", "", "no")
    );
    $whereUsers .= " and (
                lower(u.lastname) like lower('%" . $whatUsers . "%')
                or lower(u.firstname) like lower('%" . $whatUsers . "%')
                or lower(u.user_id) like lower('%" . $whatUsers . "%')
            )";
    $orderByUsers = " order by u.lastname asc, u.firstname asc, "
                  . "e.entity_label asc";
    $whereEntitiesUsers .= " and (
                lower(u.lastname) like lower('%" . $whatUsers . "%')
                or lower(u.firstname) like lower('%" . $whatUsers . "%')
                or lower(u.user_id) like lower('%" . $whatUsers . "%')
            )";
    $orderByEntities = " order by e.entity_label asc";
    $isWhatUsers = true;
}
$isWhatServices = false;
if (isset($_GET['what_services']) && ! empty($_GET['what_services'])) {
    $whatServices = addslashes(
        $func->wash($_GET['what_services'], "no", "", "no")
    );
    /*$whereUsers .= " and (e.entity_label like '%" . strtolower($whatServices)
                    . "%' or e.entity_id like '%" . strtoupper($whatServices)
                    . "%')";*/
    $whereEntities .= " and (
                        lower(e.entity_label) like lower('%". $whatServices . "%')
                        or lower(e.entity_id) like lower('%". $whatServices . "%'))";
    $orderByUsers = " order by e.entity_label asc, "
                  . "u.lastname asc, u.firstname asc";
    $orderByEntities = " order by e.entity_label asc";
    $isWhatServices = true;
}
// Redirect to user in entities
$query = "select distinct u.user_id, u.firstname, u.lastname,e.entity_id, e.entity_label "
    . "FROM " . USERS_TABLE . " u, " . ENT_ENTITIES . " e, "
    . ENT_USERS_ENTITIES . " ue WHERE u.status <> 'DEL' and u.enabled = 'Y' "
    . "and  e.entity_id = ue.entity_id and u.user_id = ue.user_id "
    . "and e.enabled = 'Y' ". $whereUsers . $whereEntities;
if($redirect_groupbasket) {
    $query .= " and ( e.entity_id in (" . $redirect_groupbasket['users_entities'] . ") "
        . " or e.entity_id in (" . $redirect_groupbasket['entities'] . "))";
}
$query .= $orderByUsers;
if (($isWhatUsers || $isWhatServices) || $_REQUEST['action'] == 'all_list') {
    $db->query($query);
    //$db->show();
}
$i = 0;
while ($line = $db->fetch_object()) {
    array_push(
        $users,
        array(
            "ID" => $db->show_string($line->user_id),
            "PRENOM" => $db->show_string($line->firstname),
            "NOM" => $db->show_string($line->lastname),
            "DEP_ID" => $db->show_string($line->entity_id),
            "DEP" => $db->show_string($line->entity_label)
        )
    );
}
// Redirect to entities
$query = "select distinct e.entity_id,  e.entity_label FROM " . USERS_TABLE . " u, "
            . ENT_ENTITIES . " e, " . ENT_USERS_ENTITIES . " ue WHERE"
            . " u.status <> 'DEL' and u.enabled = 'Y' and "
            . " e.entity_id = ue.entity_id and u.user_id = ue.user_id "
            . "and e.enabled = 'Y' " . $whereEntities;
if($redirect_groupbasket) {
    $query .= " and e.entity_id in (" . $redirect_groupbasket['entities'] . ") ";
}
$query .= $orderByEntities;
if (($isWhatUsers || $isWhatServices) || $_REQUEST['action'] == 'all_list') {
    $db->query($query);
    //$db->show();
}
$i = 0;
while ($line = $db->fetch_object()) {
    array_push(
        $entities,
        array(
            "ID"  => $db->show_string($line->entity_id),
            "DEP" => $db->show_string($line->entity_label),
        )
    );
}

$origin = $_REQUEST['origin'];
$id = '';
$desc = '';


if (! isset($_SESSION[$origin]['diff_list']['copy']['users'])) {
    $_SESSION[$origin]['diff_list']['copy']['users'] = array();
}
if (! isset($_SESSION[$origin]['diff_list']['copy']['entities'])) {
    $_SESSION[$origin]['diff_list']['copy']['entities'] = array();
}

// 1.4 custom listinstance modes
if(count($_SESSION['listinstance_roles']) > 0) {
    foreach($_SESSION['listinstance_roles'] as $role_id => $role_config) {
        if(! isset($_SESSION[$origin]['diff_list'][$role_id]['users']))
            $_SESSION[$origin]['diff_list'][$role_id]['users'] = array();
        if($role_config['allow_entities'] && ! isset($_SESSION[$origin]['diff_list'][$role_id]['entities']))
            $_SESSION[$origin]['diff_list'][$role_id]['entities'] = array();
    }
}

// Action ?
if (isset($_GET['action']))
    $action = $_GET['action'];
else  
    $action = false;

// Id ?
if(isset($_GET['id']))
    $id = $_GET['id'];
else  
    $id = false;

// Rank for remove/move ?
if(isset($_GET['rank']))
    $rank = $_GET['rank'];
else
    $rank = false;

// Mode (dest/copy or custom copy mode)
if(isset($_GET['listinstance_role']) && !empty($_GET['listinstance_role']))
    $role_id = $_GET['listinstance_role'];
else 
    $role_id = 'copy';

// Dest user    
if(isset($_SESSION[$origin]['diff_list']['dest']['user_id']) 
    && !empty($_SESSION[$origin]['diff_list']['dest']['user_id']))
    $dest_is_set = true;
else
    $dest_is_set = false;

// DEBUG 
/*
echo "<br/><pre>Action=$action rank=$rank id=$id list=$role_id";
print_r($_SESSION[$origin]['diff_list']);
echo "</pre>"; */
/****************************************************************************************
** SWITCH ON ACTION REQUEST
****************************************************************************************/ 
switch($action) {
// ADDS
//***************************************************************************************
// Add USER AS dest/copy/custom mode
case "add_user":
    $db->query(
        "SELECT u.firstname, u.lastname, u.department, e.entity_id, e.entity_label "
        . " FROM " . USERS_TABLE . " u "
        . " LEFT JOIN " . ENT_USERS_ENTITIES . " ue ON u.user_id = ue.entity_id "
        . " LEFT JOIN " . ENT_ENTITIES . " e ON ue.entity_id = e.entity_id" 
        . " WHERE u.user_id='" . $db->protect_string_db($id) . "'"
    );
    $line = $db->fetch_object();
    
    if ($role_id == 'copy' && !$dest_is_set) {
        // Dest not set => add as dest
        $_SESSION[$origin]['diff_list']['dest']['user_id'] = $db->show_string($id);
        $_SESSION[$origin]['diff_list']['dest']['firstname'] = $db->show_string($line->firstname);
        $_SESSION[$origin]['diff_list']['dest']['lastname'] = $db->show_string($line->lastname);
        $_SESSION[$origin]['diff_list']['dest']['entity_id'] = $db->show_string($line->entity_id);
        $_SESSION[$origin]['diff_list']['dest']['entity_label'] = $db->show_string($line->entity_label);
    } else {
        // Dest already set => add to listinstance mode (copy/custom)
        if(!isset($_SESSION[$origin]['diff_list'][$role_id]['users']))
                $_SESSION[$origin]['diff_list'][$role_id]['users'] = array();
        array_push(
            $_SESSION[$origin]['diff_list'][$role_id]['users'],
            array(
                'user_id' => $db->show_string($id),
                'firstname' => $db->show_string($line->firstname),
                'lastname' => $db->show_string($line->lastname),
                'entity_id' => $db->show_string($line->entity_id),
                'entity_label' => $db->show_string($line->entity_label),
            )
        ); 
    }
    usort($_SESSION[$origin]['diff_list'][$role_id]['users'], "cmpUsers");
    break;

// ADD ENTITY AS copy/custom mode
case 'add_entity':
    $db->query(
        "SELECT entity_id, entity_label FROM " . ENT_ENTITIES
        . " WHERE entity_id = '" . $db->protect_string_db($id) . "'"
    );
    $line = $db->fetch_object();
    if(!isset($_SESSION[$origin]['diff_list'][$role_id]['entities']))
                $_SESSION[$origin]['diff_list'][$role_id]['entities'] = array();
    array_push(
        $_SESSION[$origin]['diff_list'][$role_id]['entities'],
        array(
            'entity_id'    => $db->show_string($id),
            'entity_label' => $db->show_string($line->entity_label)
        )
    );
    usort($_SESSION[$origin]['diff_list'][$role_id]['entities'], "cmpEntity");   
    break;    

// REMOVE
//***************************************************************************************
// Remove DEST
case 'remove_dest':
    unset($_SESSION[$origin]['diff_list']['dest']);
    break;

// Remove USER
case 'remove_user':
    if($rank !== false && $id && $role_id
        && $_SESSION[$origin]['diff_list'][$role_id]['users'][$rank]['user_id'] == $id
    ) {
        unset($_SESSION[$origin]['diff_list'][$role_id]['users'][$rank]);
        $_SESSION[$origin]['diff_list'][$role_id]['users'] = array_values(
            $_SESSION[$origin]['diff_list'][$role_id]['users']
        );
    }
    break;

// Remove ENTITY
case 'remove_entity':
    if($rank !== false && $id && $role_id
        && $_SESSION[$origin]['diff_list'][$role_id]['entities'][$rank]['entity_id'] == $id
    ) {
        unset($_SESSION[$origin]['diff_list'][$role_id]['entities'][$rank]);
        $_SESSION[$origin]['diff_list'][$role_id]['entities'] = array_values(
            $_SESSION[$origin]['diff_list'][$role_id]['entities']
        );
    }
    break;

// MOVE
//***************************************************************************************    
case 'dest_to_copy':
    if ($dest_is_set) {
        array_push(
            $_SESSION[$origin]['diff_list']['copy']['users'],
            array(
                'user_id' => $_SESSION[$origin]['diff_list']['dest']['user_id'],
                'firstname' => $_SESSION[$origin]['diff_list']['dest']['firstname'],
                'lastname' => $_SESSION[$origin]['diff_list']['dest']['lastname'],
                'entity_id' => $_SESSION[$origin]['diff_list']['dest']['entity_id'],
                'entity_label' => $_SESSION[$origin]['diff_list']['dest']['entity_label'],
            )
        );
        unset($_SESSION[$origin]['diff_list']['dest']);
        usort($_SESSION[$origin]['diff_list']['copy']['users'], "cmpUsers");
    }
    break;

case 'copy_to_dest':
    if ($dest_is_set) {
        array_push(
            $_SESSION[$origin]['diff_list'][$role_id]['users'],
            array(
                'user_id' => $_SESSION[$origin]['diff_list']['dest']['user_id'],
                'firstname' => $_SESSION[$origin]['diff_list']['dest']['firstname'],
                'lastname' => $_SESSION[$origin]['diff_list']['dest']['lastname'],
                'entity_id' => $_SESSION[$origin]['diff_list']['dest']['entity_id'],
                'entity_label' => $_SESSION[$origin]['diff_list']['dest']['entity_label'],
            )
        );
        unset($_SESSION[$origin]['diff_list']['dest']);
    }
    if (isset($_SESSION[$origin]['diff_list'][$role_id]['users'][$rank]['user_id'])
        && !empty($_SESSION[$origin]['diff_list'][$role_id]['users'][$rank]['user_id'])
    ) {
        $_SESSION[$origin]['diff_list']['dest']['user_id'] = $_SESSION[$origin]['diff_list'][$role_id]['users'][$rank]['user_id'];
        $_SESSION[$origin]['diff_list']['dest']['firstname'] = $_SESSION[$origin]['diff_list'][$role_id]['users'][$rank]['firstname'];
        $_SESSION[$origin]['diff_list']['dest']['lastname'] = $_SESSION[$origin]['diff_list'][$role_id]['users'][$rank]['lastname'];
        $_SESSION[$origin]['diff_list']['dest']['entity_id'] = $_SESSION[$origin]['diff_list'][$role_id]['users'][$rank]['entity_id'];
        $_SESSION[$origin]['diff_list']['dest']['entity_label'] = $_SESSION[$origin]['diff_list'][$role_id]['users'][$rank]['entity_label'];
        unset( $_SESSION[$origin]['diff_list'][$role_id]['users'][$rank]);
        $_SESSION[$origin]['diff_list'][$role_id]['users'] = array_values(
            $_SESSION[$origin]['diff_list'][$role_id]['users']
        );
    }
    usort($_SESSION[$origin]['diff_list'][$role_id]['users'], "cmpUsers");
    break;    
}
 
// 1.4 create indexed array of existing diffusion to search for users/entities easily
$indexed_diff_list = array();
if(isset($_SESSION[$origin]['diff_list']['dest']['user_id'])) {
    $user_id = $_SESSION[$origin]['diff_list']['dest']['user_id'];
    $indexed_diff_list['users'][$user_id] = _PRINCIPAL_RECIPIENT;
}
for($i=0, $l=count($_SESSION[$origin]['diff_list']['copy']['users']); $i<$l; $i++) {
    $user_id = $_SESSION[$origin]['diff_list']['copy']['users'][$i]['user_id'];
    $indexed_diff_list['users'][$user_id] = _TO_CC;
}
for($i=0, $l=count($_SESSION[$origin]['diff_list']['copy']['entities']); $i<$l; $i++) {
    $entity_id = $_SESSION[$origin]['diff_list']['copy']['entities'][$i]['entity_id'];
    $indexed_diff_list['entities'][$entity_id] = _TO_CC;
}
if(count($_SESSION['listinstance_roles']) > 0) {
    foreach($_SESSION['listinstance_roles'] as $role_id => $role_config) {
        for($i=0, $l=count($_SESSION[$origin]['diff_list'][$role_id]['users']); $i<$l; $i++) {
            $user_id = $_SESSION[$origin]['diff_list'][$role_id]['users'][$i]['user_id'];
            $indexed_diff_list['users'][$user_id] = $role_config['role_label'];
        }
        for($i=0, $l=count($_SESSION[$origin]['diff_list'][$role_id]['entities']); $i<$l; $i++) {
            $entity_id = $_SESSION[$origin]['diff_list'][$role_id]['entities'][$i]['entity_id'];
            $indexed_diff_list['entities'][$entity_id] = $role_config['role_label'];
        }
    }
}

$core->load_html();
$core->load_header(_USER_ENTITIES_TITLE);
$time = $core->get_session_time_expire();
$displayValue = "";
if (preg_match("/MSIE 6.0/", $_SERVER["HTTP_USER_AGENT"])) {
    $ieBrowser = true;
    $displayValue = 'block';
} elseif (preg_match('/msie/i', $_SERVER["HTTP_USER_AGENT"])
    && ! preg_match('/opera/i', $HTTP_USER_AGENT)
) {
    $ieBrowser = true;
    $displayValue = 'block';
} else {
    $ieBrowser = false;
    $displayValue = '' . $displayValue . '';
}
?>
<body onload="setTimeout(window.close, <?php echo $time;?>*60*1000);">
<?php
$link = $_SESSION['config']['businessappurl'] . "index.php?display=true"
      . "&module=entities&page=manage_listinstance&origin=" . $origin;
    if ($onlyCc) {
        $link .= '&only_cc';
    }
    if ($noDelete) {
        $link .= '&no_delete';
    }
    //print_r($_SESSION['process']['diff_list']);
        ?>
        <br/></br>
        <div align="center">
        <h2 class="tit"><?php
echo _SEARCH_DIFF_LIST;
//echo "<pre>Redirect groupbasket for $basket_id = " . print_r($redirect_groupbasket,true). "</pre>";
?></h2>
  <form action="#" name="search_diff_list" method="" id="search_diff_list" >
    <input type="hidden" name="display" value="true" />
    <input type="hidden" name="module" value="entities" />
    <input type="hidden" name="page" value="manage_listinstance" />
    <input type="hidden" name="origin" id="origin" value="<?php echo $origin; ?>" />
    <center>
        <label for="all_list" class="bold">
            <a href="<?php
                echo $link;
                ?>&action=all_list"><?php 
            echo _ALL_LIST;?></a>
        </label>
    </center>
    <table cellpadding="2" cellspacing="2" border="0">
        <tr>
            <th>
                <label for="what_users" class="bold"><?php
                    echo _LASTNAME;
                    ?> / <?php
                    echo _FIRSTNAME;
                    ?> / <?php
                    echo _ID;
                    ?>
                </label>
            </th>
            <th>
                <input name="what_users" id="what_users" type="text" <?php
if (isset($_GET["what_users"])) {
    echo "value ='".$_GET["what_users"]."'";
}
                    ?> />
            </th>
         </tr>
         <tr>
            <th>
                <label for="what_services" class="bold"><?php
echo _DEPARTMENT;
?></label>
            </th>
            <th>
                <input name="what_services" id="what_services" type="text" <?php
if (isset($_GET["what_services"])) {
    echo "value ='".$_GET["what_services"]."'";
}
?>/>
            </th>
        </tr>
    </table>
 </form>
 <script type="text/javascript">
    repost('<?php echo $link;?>',new Array('diff_list'),new Array('what_users','what_services'),'keyup',250);
    
    function add_user(id) {
        user_id = $('user_id_' + id).value;
        user_role = $('user_role_' + id).value;
        goTo('<?php echo $link; ?>&what_users=<?php echo $whatUsers; ?>&what_services=<?php echo $whatServices; ?>&action=add_user&id='+user_id+'&listinstance_role='+user_role);        
    }
    function add_entity(id) {
        entity_id = $('entity_id_' + id).value;
        entity_role = $('entity_role_' + id).value;
        goTo('<?php echo $link; ?>&what_users=<?php echo $whatUsers; ?>&what_services=<?php echo $whatServices; ?>&action=add_entity&id='+entity_id+'&listinstance_role='+entity_role);        
    }

</script>
    <br/></br><br/></br><br/></br>
</div>
<?php
if ((isset($_GET['what_users']) && ! empty($_GET['what_users']))
    || (isset($_GET['what_services']) && !empty($_GET['what_services']))
    || ( !empty($indexed_diff_list))
) {
    ?>
    <div id="diff_list" align="center">
    <h2 class="tit"><?php echo _DIFFUSION_LIST;?></h2>
    <?php
    if (isset($_SESSION[$origin]['diff_list']['dest']['user_id'])
        && ! empty($_SESSION[$origin]['diff_list']['dest']['user_id'])
        && ! $onlyCc
    ) {
        ?><h2 class="sstit"><?php echo _PRINCIPAL_RECIPIENT;?></h2>
        <table cellpadding="0" cellspacing="0" border="0" class="listing spec">
        <tr >
            <td><img src="<?php echo $_SESSION['config']['businessappurl']; ?>static.php?filename=manage_users_entities_b.gif&module=entities" alt="<?php
                echo _USER; ?>" title="<?php echo _USER;?>" />
            </td>
            <td><?php echo $_SESSION[$origin]['diff_list']['dest']['lastname']; ?></td>
            <td><?php echo $_SESSION[$origin]['diff_list']['dest']['firstname'];?></td>
            <td><?php echo $_SESSION[$origin]['diff_list']['dest']['entity_label'];?></td>
            <td class="action_entities">
                <a href="<?php echo $link;?>&action=remove_dest" class="delete"><?php echo _DELETE;?></a>
            </td>
            <td class="action_entities"><a href="<?php echo $link; ?>&what_users=<?php echo $whatUsers; ?>&what_services=<?php echo $whatServices; 
                ?>&action=dest_to_copy" class="down"><?php echo _TO_CC;?></a>
            </td>
         </tr>
        </table>
    <?php
    }
    ?>
    <br/>
    <?php
    if (count($_SESSION[$origin]['diff_list']['copy']['users']) > 0
        || count($_SESSION[$origin]['diff_list']['copy']['entities']) > 0
    ) {
        ?>
        <h2 class="sstit"><?php echo _TO_CC;?></h2>
        <table cellpadding="0" cellspacing="0" border="0" class="listing liste_diff spec"><?php
        $color = ' class="col"';
        for ($i=0, $l=count($_SESSION[$origin]['diff_list']['copy']['entities']); $i<$l; $i++) {
            if ($color == ' class="col"') $color = '';
            else $color = ' class="col"'; ?>
            <tr <?php echo $color; ?> >
                <td>
                    <img src="<?php echo $_SESSION['config']['businessappurl']; ?>static.php?filename=manage_entities_b.gif&module=entities" alt="<?php echo _ENTITY; ?>" title="<?php echo _ENTITY; ?>" />
                </td>
                <td><?php echo $_SESSION[$origin]['diff_list']['copy']['entities'][$i]['entity_id'];?></td>
                <td colspan="2"><?php echo $_SESSION[$origin]['diff_list']['copy']['entities'][$i]['entity_label'];?></td>
                <td class="action_entities"><?php if (!$noDelete) { ?>
                    <a href="<?php echo $link; ?>&what_users=<?php echo $whatUsers; ?>&what_services=<?php echo $whatServices; 
                        ?>&action=remove_entity&listinstance_role=copy&rank=<?php echo $i; ?>&id=<?php
                        echo $_SESSION[$origin]['diff_list']['copy']['entities'][$i]['entity_id']; ?>" class="delete"><?php echo _DELETE; ?>
                    </a><?php } ?>
                </td>
                <td>&nbsp;</td>
            </tr>
        <?php }
        for ($i=0, $l=count($_SESSION[$origin]['diff_list']['copy']['users']); $i<$l; $i++) {
            if ($color == ' class="col"') $color = '';
            else $color = ' class="col"';
            ?>
            <tr <?php echo $color; ?> >
                <td>
                    <img src="<?php echo $_SESSION['config']['businessappurl']; ?>static.php?filename=manage_users_entities_b.gif&module=entities" alt="<?php echo _USER; ?>" title="<?php echo _USER; ?>" />
                </td>
                <td ><?php echo $_SESSION[$origin]['diff_list']['copy']['users'][$i]['lastname']; ?></td>
                <td ><?php echo $_SESSION[$origin]['diff_list']['copy']['users'][$i]['firstname']; ?></td>
                <td><?php echo $_SESSION[$origin]['diff_list']['copy']['users'][$i]['entity_label']; ?></td>
                <td class="action_entities"><?php if (!$noDelete) { ?>
                    <a href="<?php echo $link; ?>&what_users=<?php echo $whatUsers; ?>&what_services=<?php echo $whatServices; 
                        ?>&action=remove_user&listinstance_role=copy&rank=<?php echo $i; ?>&id=<?php echo $_SESSION[$origin]['diff_list']['copy']['users'][$i]['user_id'];
                        ?>" class="delete">
                        <?php echo _DELETE;?>
                    </a><?php } ?>
                </td>
                <td class="action_entities"><?php if (! $onlyCc) { ?>
                    <a href="<?php echo $link; ?>&what_users=<?php echo $whatUsers; ?>&what_services=<?php echo $whatServices; ?>&action=copy_to_dest&rank=<?php echo $i;?>" class="up">
                        <?php echo _TO_DEST;?>
                    </a><?php } ?>&nbsp;
                </td>
            </tr><?php
        } ?>
        </table>
        <br/>

    <?php
    } 
    ?>
    <br/>
    
    <?php
    // 1.4 custom listinstance modes
    if(count($_SESSION['listinstance_roles']) > 0) {
        foreach($_SESSION['listinstance_roles'] as $role_id => $role_config) {
            if (count($_SESSION[$origin]['diff_list'][$role_id]['users']) > 0
             || count($_SESSION[$origin]['diff_list'][$role_id]['entities']) > 0) 
            { ?><h2 class="sstit"><img src="<?php echo $_SESSION['config']['businessappurl']; ?>static.php?filename=<?php echo $role_config['list_img']; ?>&module=entities" alt=""/>&nbsp;<?php echo $role_config['list_label'];?></h2>
                <table cellpadding="0" cellspacing="0" border="0" class="listing liste_diff spec">
                <?php
                $color = ' class="col"';
                for ($i=0, $l=count($_SESSION[$origin]['diff_list'][$role_id]['users']) ; $i<$l ; $i++) {
                    if ($color == ' class="col"') $color = '';
                    else $color = ' class="col"';
                    ?>
                    <tr <?php echo $color; ?> >
                        <td>
                            <img src="<?php echo $_SESSION['config']['businessappurl']; ?>static.php?filename=manage_users_entities_b.gif&module=entities" alt="<?php echo _USER . " " . $role_config['role_label'] ;?>" title="<?php echo _USER . " " . $role_config['role_label'] ; ?>" />
                        </td>
                        <td ><?php echo $_SESSION[$origin]['diff_list'][$role_id]['users'][$i]['lastname']; ?></td>
                        <td ><?php echo $_SESSION[$origin]['diff_list'][$role_id]['users'][$i]['firstname'];?></td>
                        <td><?php echo $_SESSION[$origin]['diff_list'][$role_id]['users'][$i]['entity_label']; ?></td>
                        <td class="action_entities"><?php if (!$noDelete) 
                        { ?>
                            <a href="<?php echo $link; ?>&what_users=<?php echo $whatUsers; ?>&what_services=<?php echo $whatServices;?>&action=remove_user&listinstance_role=<?php echo $role_id ?>&rank=<?php echo $i; ?>&id=<?php echo $_SESSION[$origin]['diff_list'][$role_id]['users'][$i]['user_id'];?>" class="delete">
                                <?php echo _DELETE; ?>
                            </a><?php
                        } ?>
                        </td>
                        <td/>
                    </tr> <?php
                }
                for ($i = 0; $i < count(
                    $_SESSION[$origin]['diff_list'][$role_id]['entities']
                ); $i ++
                ) {
                    if ($color == ' class="col"') $color = '';
                    else $color = ' class="col"';
                    ?>
                    <tr <?php echo $color; ?> >
                        <td>
                            <img src="<?php echo $_SESSION['config']['businessappurl']; ?>static.php?filename=manage_entities_b.gif&module=entities" alt="<?php echo _ENTITY . " " . $role_config['role_label'] ;?>" title="<?php echo _ENTITY . " " . $role_config['role_label'] ; ?>" />
                        </td>
                        <td ><?php echo $_SESSION[$origin]['diff_list'][$role_id]['entities'][$i]['entity_id']; ?></td>
                        <td ><?php echo $_SESSION[$origin]['diff_list'][$role_id]['entities'][$i]['entity_label']; ?></td>
                        <td>&nbsp;</td>
                        <td class="action_entities"><?php if (!$noDelete) 
                        { ?>
                            <a href="<?php echo $link; ?>&what_users=<?php echo $whatUsers; ?>&what_services=<?php echo $whatServices;?>&action=remove_entity&listinstance_role=<?php echo $role_id ?>&rank=<?php echo $i; ?>&id=<?php echo $_SESSION[$origin]['diff_list'][$role_id]['entities'][$i]['entity_id'];?>" class="delete">
                                <?php echo _DELETE; ?>
                            </a><?php
                        } ?>
                        </td>
                    <td class="action_entities">&nbsp;</td>
                </tr>
                <?php
                }
                ?>
                    </table>
                    <br/>

            <?php
            }
        }
    }
    
    ?>
    <form name="pop_diff" method="post" >
        <div align="center">
            <?php
            //if(isset($_SESSION[$origin]['diff_list']['dest']['user_id']) && !empty($_SESSION[$origin]['diff_list']['dest']['user_id']))
                //{?>
            <input align="middle" type="button" value="<?php
            echo _VALIDATE;
            ?>" class="button" name="valid" onclick="change_diff_list('<?php
            echo $_SESSION['config']['businessappurl'] . 'index.php?display=true'
                . '&module=entities&page=load_listinstance&origin=' . $origin;
            if ($onlyCc) {
                echo '&only_cc';
            }
            if ($noDelete) {
                echo '&no_delete';
            }
            ?>', <?php
            echo "'" . $displayValue . "'";
            if ($_REQUEST['origin'] == 'redirect') {
                echo ",'diff_list_div_redirect'";
            }
            ?>);" />
            <?php //} ?>
            <!--<input align="middle" type="button" value="<?php echo _CANCEL;?>"  onclick="self.close();" class="button"/>-->
        </div>
    </form>
    <br/>
    <br/>
    <hr align="center" color="#6633CC" size="5" width="60%">
    <br/>
    <div align="center">
        <h2 class="tit"><?php echo _USERS_LIST;?></h2>
        <table cellpadding="0" cellspacing="0" border="0" class="listing spec">
            <thead>
                <tr>
                    <th ><?php echo _LASTNAME;?> </th>
                    <th ><?php echo _FIRSTNAME;?></th>
                    <th><?php echo _DEPARTMENT;?></th>
                    <th>&nbsp;</th>
                </tr>
            </thead>
            <?php
            $color = ' class="col"';
            for ($j=0, $m=count($users); $j<$m ; $j++) {
                $user_id = $users[$j]['ID'];
                if(isset($indexed_diff_list['users'][$user_id]))
                    $already_in_list_as = $indexed_diff_list['users'][$user_id];
                else 
                    $already_in_list_as = false;
                    
                if ($color == ' class="col"') $color = '';
                else $color = ' class="col"';
                ?>
                <tr <?php echo $color; ?> id="user_<?php echo $j; ?>">
                    <td><?php echo $users[$j]['NOM']; ?></td>
                    <td><?php echo $users[$j]['PRENOM']; ?></td>
                    <td><?php echo $users[$j]['DEP'];?></td>
                    <td class="action_entities"><?php
                    if($already_in_list_as) {
                        echo $already_in_list_as;
                    } elseif(count($_SESSION['listinstance_roles']) == 0) { ?>
                        <a href="<?php
                        echo $link;
                        ?>&what_users=<?php
                        echo $whatUsers;
                        ?>&what_services=<?php
                        echo $whatServices;
                        ?>&action=add_user&id=<?php
                        echo $users[$j]['ID'];
                        ?>" class="change"><?php
                        echo _ADD;
                        ?></a><?php
                    } else if(count($_SESSION['listinstance_roles']) > 0) { ?>
                        <input type="hidden" id="user_id_<?php echo $j; ?>" value="<?php echo $users[$j]['ID'];?>" />
                        <select name="listinstance_role" id="user_role_<?php echo $j; ?>">
                            <option value=""><?php echo _DEST_OR_COPY; ?></option><?php 
                            foreach($_SESSION['listinstance_roles'] as $role_id => $role_config) { ?>
                                <option value="<?php echo $role_id; ?>"><?php echo $role_config['role_label']; ?></option><?php 
                            } ?>
                        </select>&nbsp;
                        <span onclick="add_user(<?php echo $j; ?>);" class="change"/> 
                            <?php echo _ADD;?>
                        </span>
                        <?php 
                    }
                    ?>
                    </td>
                </tr> <?php
            } ?>
        </table>
        <br/>
        <h2 class="tit"><?php echo _ENTITIES_LIST;?></h2>
        <table cellpadding="0" cellspacing="0" border="0" class="listing spec">
            <thead>
                <tr>
                    <th><?php echo _ID;?></th>
                    <th><?php echo _LABEL;?></th>
                    <th>&nbsp;</th>
                </tr>
            </thead>
            <?php
            $color = ' class="col"';
            for ($j=0, $m=count($entities); $j<$m ; $j++) {
                $entity_id = $entities[$j]['ID'];
                if(isset($indexed_diff_list['entities'][$entity_id]))
                    $already_in_list_as = $indexed_diff_list['entities'][$entity_id];
                else 
                    $already_in_list_as = false;
                
                if ($color == ' class="col"') $color = '';
                else $color = ' class="col"';
                ?>
                <tr <?php echo $color; ?>>
                    <td><?php echo $entities[$j]['ID'];?></td>
                    <td><?php echo $entities[$j]['DEP']; ?></td>
                    <td class="action_entities"><?php
                    if($already_in_list_as) {
                        echo $already_in_list_as;
                    } else if(count($_SESSION['listinstance_roles']) == 0) { ?>
                    <a href="<?php
                    echo $link;
                    ?>&what_users=<?php
                    echo $whatUsers;
                    ?>&what_services=<?php
                    echo $whatServices;
                    ?>&action=add_entity&id=<?php
                    echo $entities[$j]['ID'];
                    ?>" class="change"><?php
                    echo _ADD_CC;
                    ?></a><?php
                    } else if(count($_SESSION['listinstance_roles']) > 0) { ?>
                        <input type="hidden" id="entity_id_<?php echo $j; ?>" value="<?php echo $entities[$j]['ID'];?>" />
                        <select name="listinstance_role" id="entity_role_<?php echo $j; ?>">
                            <option value=""><?php echo _TO_CC; ?></option><?php 
                            foreach($_SESSION['listinstance_roles'] as $role_id => $role_config) {
                                if($role_config['allow_entities']) { ?>
                                    <option value="<?php echo $role_id; ?>"><?php echo $role_config['role_label']; ?></option><?php 
                                }
                            } ?>
                        </select>&nbsp;
                        <span onclick="add_entity(<?php echo $j; ?>);" class="change"/> 
                            <?php echo _ADD;?>
                        </span> <?php 
                    }
            }?>
                    </td>
                </tr>
        </table>
    </div>
    <?php
} else {
    ?>
    <div id="diff_list" align="center">
    <input  type="button" value="<?php
    echo _CANCEL;
    ?>" class="button"  onclick="self.close();"/>
    </div>
    <?php
}
?>
<br/>
</body>
</html>
