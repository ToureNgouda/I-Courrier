<?php

/*
*   Copyright 2008-2015 Maarch
*
*   This file is part of Maarch Framework.
*
*   Maarch Framework is free software: you can redistribute it and/or modify
*   it under the terms of the GNU General Public License as published by
*   the Free Software Foundation, either version 3 of the License, or
*   (at your option) any later version.
*
*   Maarch Framework is distributed in the hope that it will be useful,
*   but WITHOUT ANY WARRANTY; without even the implied warranty of
*   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*   GNU General Public License for more details.
*
*   You should have received a copy of the GNU General Public License
*   along with Maarch Framework.  If not, see <http://www.gnu.org/licenses/>.
*/

/*
* @brief  Lists the baskets for the current user (service)
*
*
* @file
* @author Loic Vinet <dev@maarch.org>
* @author Claire Figueras <dev@maarch.org>
* @author Laurent Giovannoni <dev@maarch.org>
* @date $date$
* @version $Revision$
* @ingroup basket
*/

require_once('core/class/class_request.php');
require_once('modules/basket/class/class_modules_tools.php');
$core_tools = new core_tools();
$core_tools->test_user();

$_SESSION['basket_used'] = '';
if (!isset($_REQUEST['noinit'])) {
    $_SESSION['current_basket'] = array();
}
/************/
$bask = new basket();
$db = new Database();

?>
<!--<div id="welcome_box_right">-->

<div id="welcome_box_left_baskets">
<br />
<?php
if ($core_tools->test_service('display_basket_list','basket', false)) {

        // Refresh personnal basket info
        $bask->load_module_var_session($_SESSION['user']);

        if (
            isset($_SESSION['user']['baskets'])
            && count($_SESSION['user']['baskets']) > 0
        ) {
            $collWithUserBaskets = array();
            $count = count($_SESSION['collections']);
            for ($cptColl=0;$cptColl<$count;$cptColl++) {
                if ($bask->isUserHasBasketInCollection($_SESSION['collections'][$cptColl]['id'])) {
                    array_push(
                        $collWithUserBaskets, 
                        array(
                            'coll_id' => $_SESSION['collections'][$cptColl]['id'],
                            'coll_label' => $_SESSION['collections'][$cptColl]['label']
                        )
                    );
                }
            }

            ?>
            <div class="block">
                <h2><?php echo _MY_BASKETS;?> : </h2>
            
            <?php
            $redirectedBaskets = \Basket\models\BasketModel::getRedirectedBasketsByUserId(['userId' => $_SESSION['user']['UserId']]);
            $coloredBaskets    = \Basket\models\BasketModel::getColoredBasketsByUserId(['userId' => $_SESSION['user']['UserId']]);
            $countColl         = count($collWithUserBaskets);
            $currentGroup = '';
            for ($cpt=0;$cpt<$countColl;$cpt++) {
                echo '<h4><i class="fa fa-inbox fa-2x"></i>&nbsp;'
                    . $collWithUserBaskets[$cpt]['coll_label'] . '</h4>';
                $abs_basket = false;
                echo '<ul class="fa-ul" style="font-size:14px;">';
                for ($i=0;$i<count($_SESSION['user']['baskets']);$i++) {
                    if ($_SESSION['user']['baskets'][$i]['is_visible'] === 'Y' 
                        && $_SESSION['user']['baskets'][$i]['id_page'] <> 'redirect_to_action'
                        && $_SESSION['user']['baskets'][$i]['coll_id'] == $collWithUserBaskets[$cpt]['coll_id'] 
                    ) {
                        if (
                            $_SESSION['user']['baskets'][$i]['group_desc'] <> $currentGroup
                            && $_SESSION['user']['baskets'][$i]['group_desc'] <> ''
                        ) {
                            $currentGroup = $_SESSION['user']['baskets'][$i]['group_desc'];
                            echo '<li style="padding-top: 5px;padding-bottom: 5px;"><i class="fa-li fa fa-users" style="padding-top: 5px;padding-bottom: 5px;"></i>'
                                . '<small>' . functions::xssafe($currentGroup) . '</small></li>';
                        }
                        $nb = '';
                        if (
                            preg_match('/^CopyMailBasket/', $_SESSION['user']['baskets'][$i]['id'])
                            && !empty($_SESSION['user']['baskets'][$i]['view'])
                        ) {
                            $stmt = $db->query('select res_id from '
                                . $_SESSION['user']['baskets'][$i]['view']
                                . ' where ' . $_SESSION['user']['baskets'][$i]['clause']
                            );
                            $nb = $stmt->rowCount();
                        }
                        if ($nb <> 0) {
                            $nb = '(' . $nb . ')';
                        } else {
                            $nb = '';
                        }
                        if ($_SESSION['user']['baskets'][$i]['id_page'] <> 'redirect_to_action') {
                            $redirectedTo = '';
                            foreach ($redirectedBaskets as $redirectBasketValue) {
                                if ($redirectBasketValue['basket_owner'] == $_SESSION['user']['UserId']) {
                                    if ($redirectBasketValue['basket_id'] == $_SESSION['user']['baskets'][$i]['id']) {
                                        $redirectedTo = $redirectBasketValue['user'];
                                    }
                                } elseif ($_SESSION['user']['baskets'][$i]['id'] == $redirectBasketValue['basket_id'] . '_' . $redirectBasketValue['basket_owner']) {
                                    $redirectedTo = $redirectBasketValue['user'];
                                }
                            }

                            $color = $_SESSION['user']['baskets'][$i]['color'];
                            foreach ($coloredBaskets as $coloredBasket) {
                                if ($coloredBasket['basket_id'] == $_SESSION['user']['baskets'][$i]['id'] && $coloredBasket['group_id'] == $_SESSION['user']['baskets'][$i]['group_id']) {
                                    $color = $coloredBasket['color'];
                                }
                            }

                            $fontweight = 'bold';
                            if($color == '#666666' || $color == '#666' || empty($color)){
                                $fontweight = 'normal';
                            }

                            if (empty($redirectedTo)) {

                                if ($_SESSION['user']['baskets'][$i]['abs_basket'] == true && !$abs_basket) {
                                    echo '</ul><br /><h3>' . _OTHER_BASKETS
                                        . ' :</h3><ul class="fa-ul">';
                                    $abs_basket = true;
                                }
                                
                                if ($core_tools->is_module_loaded('folder') && $_SESSION['user']['baskets'][$i]['is_folder_basket'] == 'Y') {
                                    echo '<li style="padding-top: 5px;padding-bottom: 5px;"><a title="'.$_SESSION['user']['baskets'][$i]['desc'].'" href="'
                                        . $_SESSION['config']['businessappurl']
                                        . 'index.php?page=view_baskets&amp;module=basket&amp;baskets='
                                        . $_SESSION['user']['baskets'][$i]['id'] . '&amp;groupId=' . $_SESSION['user']['baskets'][$i]['group_id']
                                        . '"><b><span id="nb_' . $_SESSION['user']['baskets'][$i]['id']
                                        . '" name="nb_' . $_SESSION['user']['baskets'][$i]['id']
                                        . '"><i class="fa-li fa fa-spinner fa-spin" style="margin-left: -10px;position: inherit;margin-right: -7px;"></i>'
                                        . '</span></b> <i class="fa-li fa fa-folder" style="padding-top: 5px;padding-bottom: 5px;"></i>'
                                        . '<span style="color: ' .$color . ';font-weight: '.$fontweight.'">' . functions::xssafe($_SESSION['user']['baskets'][$i]['name']) . '</span>'
                                        . ' </a></li>';
                                } else {
                                    echo '<li style="padding-top: 5px;padding-bottom: 5px;"><a title="'.$_SESSION['user']['baskets'][$i]['desc'].'" href="'
                                        . $_SESSION['config']['businessappurl']
                                        . 'index.php?page=view_baskets&amp;module=basket&amp;baskets='
                                        . $_SESSION['user']['baskets'][$i]['id'] . '&amp;groupId=' . $_SESSION['user']['baskets'][$i]['group_id']
                                        . '"><b><span id="nb_' . $_SESSION['user']['baskets'][$i]['id']
                                        . '" name="nb_' . $_SESSION['user']['baskets'][$i]['id']
                                        . '"><i class="fa-li fa fa-spinner fa-spin" style=";margin-left: -10px;position: inherit;margin-right: -7px;"></i>'
                                        . '</span></b> <i class="fa-li fa fa-tasks" style="padding-top: 5px;padding-bottom: 5px;"></i> '
                                        . '<span style="color: ' .$color . ';font-weight: '.$fontweight.'">' . functions::xssafe($_SESSION['user']['baskets'][$i]['name']) . '</span>'
                                        . ' </a></li>';
                                }
                            } else if(!$_SESSION['user']['baskets'][$i]['abs_basket']){
                                echo '<li style="padding-top: 5px;padding-bottom: 5px;"><a title="'.$_SESSION['user']['baskets'][$i]['desc'].'"><b><span id="nb_' . $_SESSION['user']['baskets'][$i]['id']
                                    . '" name="nb_' . $_SESSION['user']['baskets'][$i]['id']
                                    . '"><i class="fa-li fa fa-spinner fa-spin" style="margin-left: -10px;position: inherit;margin-right: -7px;"></i>'
                                    . '</span></b> <i class="fa-li fa fa-share" style="padding-top: 5px;padding-bottom: 5px;color: #c62b62"></i><span style="color: ' .$color . ';font-weight: '.$fontweight.'">' . functions::xssafe($_SESSION['user']['baskets'][$i]['name']) . ' (redirigé vers ' . $redirectedTo . ')'
                                    . ' </span></a></li>';
                            }
                        }
                    }
                }
                echo '<hr />';
                echo '</ul>';
            }
            echo '</div>';
        }
        ?>
    <div class="blank_space">&nbsp;</div>
    <?php
}
?>
</div>

<script language="javascript">
    var basketsSpan = $('welcome_box_left_baskets').select('span');
    //console.log(basketsSpan);
    var path_manage_script = '<?php functions::xecho($_SESSION["config"]["businessappurl"]);?>'
        + 'index.php?display=true&module=basket&page=ajaxNbResInBasket';
    for (i=0;i<basketsSpan.length;i++) {
        //console.log(basketsSpan[i]);
        
        new Ajax.Request(path_manage_script,
        {
            method:'post',
            parameters: { id_basket : basketsSpan[i].id},
            onSuccess: function(answer)
            {
                eval('response = '+answer.responseText);
                if (response.nb > 0 ) {
                    $j("[name='" + response.idSpan + "']").html("<span class='nbRes'>" + response.nb + "</span>");
                } else {
                    $j("[name='" + response.idSpan + "']").html("<span class='nbResZero'>" + response.nb + "</span>");
                }
                
            }
        });
    }
</script>
