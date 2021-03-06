<?php
/*
*    Copyright 2008,2009 Maarch
*
*  This file is part of Maarch Framework.
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
*    along with Maarch Framework.  If not, see <http://www.gnu.org/licenses/>.
*/

/**
* @brief   Notification Administration summary Page
*
* Notification Administration summary Page
*
* @file
* @author Loic Vinet <dev@maarch.org>
* @date $date$
* @version $Revision$
* @ingroup admin
*/

$admin = new core_tools();
$admin->test_admin('admin_notif', 'notifications');
/****************Management of the location bar  ************/
$init = false;
if(isset($_REQUEST['reinit']) && $_REQUEST['reinit'] == "true")
{
    $init = true;
}
$level = "";
if(isset($_REQUEST['level']) && ($_REQUEST['level'] == 2 || $_REQUEST['level'] == 3 || $_REQUEST['level'] == 4 || $_REQUEST['level'] == 1))
{
    $level = $_REQUEST['level'];
}
$page_path = $_SESSION['config']['businessappurl'].'index.php?page=admin_notif&module=notifications';
$page_label = _NOTIFICATIONS;
$page_id = "admin_notif";
$admin->manage_location_bar($page_path, $page_label, $page_id, $init, $level);
/***********************************************************/
unset($_SESSION['m_admin']);
?>
<h1><i class='fa fa-bell fa-2x'> </i> <?php echo _ADMIN_NOTIFICATIONS;?></h1>
<div id="inner_content" class="clearfix">
    <h2 class="admin_subtitle block" ><?php echo _ADMIN_NOTIFICATIONS;?></h2>
    <div  class="admin_item" id="admin_structures" title="<?php echo _MANAGE_EVENTS;?>" onclick="window.top.location='<?php echo $_SESSION['config']['businessappurl'];?>index.php?page=manage_events_list_controler&module=notifications&mode=list';">
        <div class="sum_margin">
                <strong><?php echo _MANAGE_EVENTS;?></strong><br/>
                <em><?php echo _MANAGE_EVENTS_DESC;?></em>
        </div>
    </div>

    <div class="admin_item" id="admin_subfolders" title="<?php echo _TEST_SENDMAIL;?>" onclick="window.top.location='<?php echo $_SESSION['config']['businessappurl'];?>index.php?page=test_sendmail&module=notifications';">
        <div class="sum_margin">
                <strong><?php echo _TEST_SENDMAIL;?></strong><br/>
                <em><?php echo _TEST_SENDMAIL_DESC;?></em>
         </div>
    </div>

  
</div>
