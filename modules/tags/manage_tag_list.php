<?php
/**
* Copyright Maarch since 2008 under licence GPLv3.
* See LICENCE.txt file at the root folder for more details.
* This file is part of Maarch software.

*
* @brief   managage_tag_list
*
* @author  dev <dev@maarch.org>
* @ingroup tags
*/
if ($mode == 'list') {
    $list = new list_show();
    $list->admin_list(
        $tagslist['tab'],
        count($tagslist['tab']),
        $tagslist['title'],
        'tag_label',
        'manage_tag_list_controller&mode=list',
        'tags',
        'tag_id',
        true,
        $tagslist['page_name_up'],
        $tagslist['page_name_val'],
        $tagslist['page_name_ban'],
        $tagslist['page_name_del'],
        $tagslist['page_name_add'],
        $tagslist['label_add'],
        false,
        false,
        _ALL_TAGS,
        _TAG,
        'tags',
        true,
        true,
        false,
        true,
        $eventsList['what'],
        true,
        $tagslist['autoCompletionArray']
    );
} elseif ($mode == 'up' || $mode == 'add') {
    ?><h1><i class="fa fa-tags fa-2x"> </i>
        <?php
        if ($mode == 'up') {
            echo _MODIFY_TAG;
        } elseif ($mode == 'add') {
            echo _ADD_TAG;
        } ?>
    </h1>
    <div id="inner_content" class="clearfix" align="center">
        <br />
    <?php
    if ($state == false) {
        echo '<br /><br /><br /><br />'._THIS_EVENT.' '._IS_UNKNOWN
        .'<br /><br /><br /><br />';
    } else {
        ?>
    <div class="block">
        <form name="frmevent" id="frmevent" method="post" action="<?php
            echo $_SESSION['config']['businessappurl'].'index.php?display=true'
            .'&amp;module=tags&amp;page=manage_tag_list_controller&amp;mode='
            .$mode; ?>" class="forms addforms">
            <input type="hidden" name="display" value="true" />
            <input type="hidden" name="admin" value="tags" />
            <input type="hidden" name="page" value="manage_tag_list_controler" />
            <input type="hidden" name="mode" value="<?php functions::xecho($mode); ?>" />

            <input type="hidden" name="tag_id" id="tag_label" value="<?php functions::xecho($_SESSION['m_admin']['tag']['tag_id']); ?>" />

            <input type="hidden" name="order" id="order" value="<?php
                functions::xecho($_REQUEST['order']); ?>" />
            <input type="hidden" name="order_field" id="order_field" value="<?php
                functions::xecho($_REQUEST['order_field']); ?>" />
            <input type="hidden" name="what" id="what" value="<?php
                functions::xecho($_REQUEST['what']); ?>" />
            <input type="hidden" name="start" id="start" value="<?php
                functions::xecho($_REQUEST['start']); ?>" />
            
            <p>
                <label for="label"><?php echo _ID; ?> : </label>
                <input name="tag_label" type="text"  id="tag_label_id" class="readonly" readonly="readonly" value="<?php
                    echo $_SESSION['m_admin']['tag']['tag_id']; ?>"/>
            </p>
            <p>
                <label for="label"><?php echo _NAME_TAGS; ?> : </label>
                <input name="tag_label" type="text"  id="tag_label_id" value="<?php
                    echo functions::show_str(
                        $_SESSION['m_admin']['tag']['tag_label']
                    ); ?>"/>
            </p>
            <?php
                if ($core->test_service('private_tag', 'tags', false) == 1) {
                    ?>
            <p>
                <label for="label"><?php echo _VISIBLE_BY; ?> : </label>
                <?php
                require_once 'modules'.DIRECTORY_SEPARATOR.'entities'.DIRECTORY_SEPARATOR.'class'.DIRECTORY_SEPARATOR.'EntityControler.php';

                    $content .= '<select data-placeholder=" " name="entitieslist[]" id="entitieslist" size="7" style="width: 206px" ';
                    $content .= 'ondblclick=\'moveclick($(entitieslist), $(entities_chosen));\' multiple="multiple">';

                    //entitiesRestriction
                    $entitiesRestriction = array();
                    $entitiesDirection = users_controler::getParentEntitiesWithType($_SESSION['user']['UserId'], 'Direction');
                    //var_dump($entitiesDirection);
                    foreach ($entitiesDirection as $entity_id) {
                        $entitiesRestriction[] = $entity_id;
                        $tmp_arr = users_entities_Abstract::getEntityChildren($entity_id);
                        $entitiesRestriction = array_merge($entitiesRestriction, $tmp_arr);
                    }
                    //entitiesList
                    $entitiesList = array();
                    //$entitiesList = EntityControler::getAllEntities();
                    require_once 'modules'.DIRECTORY_SEPARATOR.'entities'.DIRECTORY_SEPARATOR.'class'.DIRECTORY_SEPARATOR.'class_manage_entities.php';
                    $ent = new entity();
                    $except = array();
                    $entitiesList = $ent->getShortEntityTree($entitiesList, 'all', '', $except);
                    for ($i = 0; $i < count($entitiesList); ++$i) {
                        $content .= '<option value="'
                    .$entitiesList[$i]['ID'].'" alt="'
                    .$entitiesList[$i]['LABEL'].'" title="'
                    .$entitiesList[$i]['LABEL'].'"';
                        if ($mode == 'add') {
                            if ($_SESSION['user']['UserId'] != 'superadmin') {
                                if (in_array($entitiesList[$i]['ID'], $entitiesRestriction)) {
                                    $content .= 'selected="selected"';
                                } else {
                                    $content .= 'disabled="disabled"';
                                }
                            }
                        } else {
                            if (in_array($entitiesList[$i]['ID'], $_SESSION['m_admin']['tag']['entities'])) {
                                $content .= 'selected="selected"';
                            }
                            if (!in_array($entitiesList[$i]['ID'], $entitiesRestriction) && $_SESSION['user']['UserId'] != 'superadmin') {
                                $content .= 'disabled="disabled"';
                            }
                        }
                        $content .= '>';
                        $content .= $entitiesList[$i]['LABEL'].'</option>';
                    }
                    $content .= '</select>';
                    $content .= '<p style="text-align: right;margin-right: 20px;"><a style="cursor: pointer;" onclick="resetSelect(\'entitieslist\')">'._UNSELECT_ALL.'</a></p>';
                    $content .= '<script> $j("#entitieslist").chosen({width: "95%", disable_search_threshold: 10, search_contains: true,display_disabled_options: false});</script>';
                    echo $content; ?>
            </p>
                <?php
                } ?>

           <?php
            if ($mode == 'up') {
                ?>
                <!--<p>
                    <label for="label"><?php echo _COLL_ID; ?> : </label>
                    <span><?php
                        echo functions::show_str(
                            $_SESSION['m_admin']['tag']['tag_coll']
                        ); ?>
                    </span>
                </p>-->
            <?php
            } else {
                $arrayColl = $_SESSION['m_admin']['tags']['coll_id']; ?>
                <p>
                    <label for="collection"><?php echo _COLLECTION; ?> : </label>
                    <select disabled name="collection" id="collection" >
                        <!--<option value="" ><?php echo _CHOOSE_COLLECTION; ?></option>-->
                    <?php
                    for ($i = 0; $i < count($arrayColl); ++$i ) {
                        ?>
                        <option  value="<?php
                        functions::xecho($arrayColl[$i]['id']); ?>" <?php
                        if (isset($_SESSION['m_admin']['doctypes']['COLL_ID'])
                            && $_SESSION['m_admin']['doctypes']['COLL_ID'] == $arrayColl[$i]['id']
                        ) {
                            echo 'selected="selected"';
                        } ?> ><?php functions::xecho($arrayColl[$i]['label']); ?></option>
                        <?php
                    } ?>
                    </select>
                </p>
            <?php
            }

        if ($mode == 'up') {
            ?>
                <p style="font-style: italic;color:#135F7F;">
                    <span><?php
                        echo $_SESSION['m_admin']['tag']['tag_count'].' '._NB_DOCS_FOR_THIS_TAG; ?>
                    </span>
                </p>
            <?php
        } ?>

            <p class="buttons">
                <?php
                if ($mode == 'up') {
                    ?>
                    <input class="button" type="submit" name="tag_submit" value=
                    "<?php echo _MODIFY; ?>" />
                    <?php
                } elseif ($mode == 'add') {
                    ?>
                    <input type="submit" class="button"  name="tag_submit" value=
                    "<?php echo _ADD; ?>" />
                    <?php
                } ?>
                <input type="button" class="button"  name="cancel" value="<?php
                 echo _CANCEL; ?>" onclick="javascript:window.location.href='<?php
                 echo $_SESSION['config']['businessappurl']; ?>index.php?page=manage_tag_list_controller&amp;mode=list&amp;module=tags'"/>

                <?php
                if ($mode == 'up') {
                    ?>
                    <hr/>
                    <p>
                        <label for="label"><?php echo _TAG_FUSION_ACTIONLABEL; ?> : </label>
                        <select name="tagfusion" id="tagfusion">
                        <?php
                            foreach ($_SESSION['tmp_all_tags'] as $tmp_selectvalue_tag) {
                                if ($tmp_selectvalue_tag['tag_id'] != $_SESSION['m_admin']['tag']['tag_id']) {
                                    ?>
                                <option value="<?php functions::xecho($tmp_selectvalue_tag['tag_id']); ?>">
                                    <?php //functions::xecho($tmp_selectvalue_tag['tag_label']." ::".$tmp_selectvalue_tag['coll_id']);?>
                                    <?php functions::xecho($tmp_selectvalue_tag['tag_label']); ?>
                                </option>
                                <?php
                                }
                            } ?>
                        </select>

                       <input type="button" class="button"  name="cancel" style="border-radius:8px;font-size:8px;"
                       onclick = "tag_fusion('<?php echo $_SESSION['m_admin']['tag']['tag_id']; ?>',
                        $('tagfusion').value, <?php functions::xecho($route_tag_fusion_tags); ?>,'<?php
                        echo _TAGFUSION_GOODRESULT; ?>' , '<?php
                        echo $_SESSION['config']['businessappurl'].'index.php?page=manage_tag_list_controller&module=tags'; ?>');" value="<?php echo _TAGFUSION; ?> ">

                    </p>
                    <?php
                } ?>
            </p>
        </form >
        </div>
    <?php
    } ?></div><?php
}
