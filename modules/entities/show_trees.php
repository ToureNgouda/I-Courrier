<?php

require_once("core/class/class_request.php");
require_once("modules/entities/entities_tables.php");
require_once('modules/entities/class/class_manage_entities.php');

$core_tools = new core_tools();
$core_tools->load_lang();
$func = new functions();
$core_tools->load_html();
$core_tools->load_header();
?>

<body>
<?php

if (count($_SESSION['tree_entities']) < 1) {
    echo _NO_DEFINED_TREES;
} else {
    if (isset($_SESSION['entities_chosen_tree']) && !empty($_SESSION['entities_chosen_tree'])) {
        $ent = new entity();
        $db = new Database();
        $_SESSION['EntitiesIdExclusion'] = array();
        $whereExclusion = ' and (1=1)';
        if ($_SESSION['user']['UserId'] != 'superadmin') {
            require_once('modules/entities/class/class_manage_entities.php');
            $ent = new entity();
            $my_tab_entities_id = $ent->get_all_entities_id_user($_SESSION['user']['entities']);
            $my_tab_entities_id = array_unique($my_tab_entities_id);
            //var_dump($my_tab_entities_id);
            if (count($my_tab_entities_id) > 0) {
                $listOfMyEntities = implode(',', $my_tab_entities_id);
                $stmt = $db->query(
                    "select entity_id from "
                    . ENT_ENTITIES . " where entity_id not in (" . $listOfMyEntities .") and enabled= 'Y' order by entity_id"
                );
                //$ent->show();
                while ($res = $stmt->fetchObject()) {
                    array_push($_SESSION['EntitiesIdExclusion'], "'". $res->entity_id . "'");
                }
            }
        }
        ?>
        <script type="text/javascript" src="<?php echo $_SESSION['config']['businessappurl'] . 'tools/tafelTree/';?>js/scriptaculous.js"></script>
        <script type="text/javascript" src="<?php echo $_SESSION['config']['businessappurl'] . 'tools/tafelTree/';?>Tree.js"></script>
        <?php
        //$where = "";
        $level1 = array();
        $stmt = $db->query("select u.user_id, u.lastname, u.firstname from  " . ENT_USERS_ENTITIES . " ue, " 
            . $_SESSION['tablename']['users'] . " u where ue.entity_id  = ? and ue.user_id = u.user_id and u.status <> 'DEL' " 
            . $whereExclusionUE . " order by u.lastname, u.firstname",array($_SESSION['entities_chosen_tree']));
        

        //$ent->show();
        while ($res = $stmt->fetchObject()) {
            array_push(
                $level1, array(
                    'id' => $res->user_id, 
                    'tree' => $_SESSION['entities_chosen_tree'], 
                    'key_value' => $res->user_id, 
                    'label_value' => functions::show_string($res->lastname.' '.$res->firstname, true), 
                    'is_entity' => false
                )
            );
        }
        $stmt = $db->query("select entity_id, entity_label from " . ENT_ENTITIES . " where parent_entity_id = ? and enabled ='Y' order by entity_label",array($_SESSION['entities_chosen_tree']));
        //$ent->show();
        $level1 = array();
        while ($res = $stmt->fetchObject()) {
            if (!is_integer(array_search("'" . $res->entity_id . "'", $_SESSION['EntitiesIdExclusion'])) || count($_SESSION['EntitiesIdExclusion']) == 0) {
                $labelValue = '<span class="entity_tree_element_ok"><i class="fa"></i><a href="index.php?page=entity_up&module=entities&id=' 
                            . $res->entity_id . '" target="_top">' . functions::show_string($res->entity_label, true) . '</a></span>';
            } else {
                $labelValue = '<small><i>' . functions::show_string($res->entity_label, true) . '</i></small>';
            }
            array_push(
                $level1, 
                array(
                    'id' => $res->entity_id, 
                    'tree' => $_SESSION['entities_chosen_tree'], 
                    'key_value' => $res->entity_id, 
                    'label_value' => $labelValue, 
                    'script' => "", 
                    'is_entity' => true
                )
            );
        }
        for ($i=0;$i<count($_SESSION['tree_entities']);$i++) {
            if ($_SESSION['tree_entities'][$i]['ID'] == $_SESSION['entities_chosen_tree']) {
                if (!is_integer(array_search("'" . $_SESSION['tree_entities'][$i]['ID'] . "'", $_SESSION['EntitiesIdExclusion'])) || count($_SESSION['EntitiesIdExclusion']) == 0) {
                    $label = $_SESSION['tree_entities'][$i]['ID'] . ' - <a href="index.php?page=entity_up&module=entities&id=' 
                                . $_SESSION['tree_entities'][$i]['ID'] . '" target="_top">' . $ent->show_string($_SESSION['tree_entities'][$i]['LABEL'], true) . '</a>';
                } else {
                    $label = "<b>" . $_SESSION['tree_entities'][$i]['ID'] . ' - '  
                        . $_SESSION['tree_entities'][$i]['LABEL'] . "</b>";
                }
                $valId=$_SESSION['tree_entities'][$i]['ID'];
            }
        }
        $stmt = $db->query("select u.user_id, u.lastname, u.firstname, u.enabled, ue.entity_id as entity_id from  " 
            . ENT_USERS_ENTITIES . " ue, " . $_SESSION['tablename']['users'] 
            . " u where ue.entity_id  = ? and ue.user_id = u.user_id and u.status <> 'DEL' order by u.lastname, u.firstname",array($_SESSION['entities_chosen_tree']));
        while ($res = $stmt->fetchObject()) {
            if (!is_integer(array_search("'" . $res->entity_id . "'", $_SESSION['EntitiesIdExclusion'])) || count($_SESSION['EntitiesIdExclusion']) == 0) {
                if($res->enabled == 'N'){
                    $labelValue = '<span class="entity_tree_element_ok">' . functions::show_string('<a style="color:red;" href="index.php?page=users_management_controler&mode=up&admin=users&id='
                            . $res->user_id . '" target="_top">' . $res->lastname . ' ' . $res->firstname . '</a>', true) . '</span>';
                }else{
                   $labelValue = '<span class="entity_tree_element_ok">' . functions::show_string('<a href="index.php?page=users_management_controler&mode=up&admin=users&id='
                            . $res->user_id . '" target="_top">' . $res->lastname . ' ' . $res->firstname . '</a>', true) . '</span>'; 
                }

            } else {
                $labelValue = '<small><i>' . functions::show_string($res->lastname . ' ' . $res->firstname, true) . '</i></small>';
            }
            array_push(
                $level1, 
                array(
                    'id' => $res->user_id, 
                    'tree' => $_SESSION['entities_chosen_tree'], 
                    'key_value' => $res->user_id, 
                    'label_value' => $labelValue, 
                    'is_entity' => false
                )
            );
        }
        ?>
        <div class="tree" id='divTree'>
            <ul>
                <li id=<?php echo "'".$valId."'"; ?> >
                    <span class="root">
                                <span class="icon"><i class="fa fa-plus-square" style='cursor:pointer' onClick=<?php echo "\"getChildrenHtml('".$valId."','divTree','index.php?display=true&module=entities&page=get_tree_childs','fa fa-minus-square','fa fa-plus-square')\""?>></i></span>                                
                                <?php echo $label;?>
                                
                    </span>
                </li>
            </ul>
        </div>
                <script type="text/javascript">
                
                    var tree = $j('#divTree');
                    BootstrapTree.init(tree);                    
                </script>   
        <div id="trees_div"></div>
        <?php
    } else {
        //echo "<div align='left'>&nbsp;&nbsp;&nbsp;"._CHOOSE_FOLDERTYPE."</div>";
    }
}
?>
</body>
</html>
