<?php
core_tools::load_lang();
$core_tools = new core_tools();
$core_tools->test_admin('admin_notif', 'notifications');

// Default mode is add
$mode = 'add';
if (isset($_REQUEST['mode']) && !empty($_REQUEST['mode'])) {
    $mode = $_REQUEST['mode'];
}



try{
    require_once 'core/class/ActionControler.php';
    require_once 'core/class/ObjectControlerAbstract.php';
    require_once 'core/class/ObjectControlerIF.php';
    require_once 'modules/templates/class/templates_controler.php' ;
    require_once 'modules/notifications/class/notifications_controler.php';
    require_once 'modules/notifications/class/diffusion_type_controler.php';
    
    if ($mode == 'list') {
        require_once 'core/class/class_request.php' ;
        require_once 'apps' . DIRECTORY_SEPARATOR
                     . $_SESSION['config']['app_id'] . DIRECTORY_SEPARATOR
                     . 'class' . DIRECTORY_SEPARATOR . 'class_list_show.php' ;
    }
} catch (Exception $e) {
    echo $e->getMessage();
}

//Get list of aff availables actions
$al = new ActionControler();
$actions_list = $al->getAllActions();

//Get list of all diffusion types
$dt = new diffusion_type_controler();
$diffusion_types = $dt->getAllDiffusion();

//Get list of all templates
$tp = new templates_controler();
$templates_list = $tp->getAllTemplatesForSelect();


if (isset($_REQUEST['id']) && !empty($_REQUEST['id'])) {
    $notification_sid = $_REQUEST['id'];
}


if (isset($_REQUEST['notif_submit'])) {
    // Action to do with db
    validate_notif_submit();

} else {
    // Display to do
    $state = true;
    switch ($mode) {
        case 'up' :
            $state = display_up($notification_sid);
            $_SESSION['service_tag'] = 'notif_init';
            core_tools::execute_modules_services(
                $_SESSION['modules_services'], 'notif_init', 'include'
            );
            location_bar_management($mode);
            break;
        case 'add' :
            display_add();
            $_SESSION['service_tag'] = 'notif_init';
            core_tools::execute_modules_services(
                $_SESSION['modules_services'], 'notif_init', 'include'
            );
            location_bar_management($mode);
            break;
        case 'del' :
            display_del($notification_sid);
            break;
        case 'list' :
            $notifsList = display_list();
            location_bar_management($mode);
           // print_r($statusList); exit();
            break;
    }
    include('manage_notifications.php');
}

/**
 * Management of the location bar
 */
function location_bar_management($mode)
{
    $pageLabels = array('add'  => _ADDITION,
                    'up'   => _MODIFICATION,
                    'list' => _MANAGE_NOTIFS
               );
    $pageIds = array('add' => 'notif_add',
                  'up' => 'notif_up',
                  'list' => 'notif_list'
            );
    $init = false;
    if (isset($_REQUEST['reinit']) && $_REQUEST['reinit'] == 'true') {
        $init = true;
    }

    $level = '';
    if (isset($_REQUEST['level'])
        && ($_REQUEST['level'] == 2 || $_REQUEST['level'] == 3
            || $_REQUEST['level'] == 4 || $_REQUEST['level'] == 1)) {
        $level = $_REQUEST['level'];
    }

    $pagePath = $_SESSION['config']['businessappurl'] . 'index.php?page='
               . 'manage_notifications_controler&module=notifications&mode=' . $mode ;
    $pageLabel = $pageLabels[$mode];
    $pageId = $pageIds[$mode];
    $ct = new core_tools();
    $ct->manage_location_bar($pagePath, $pageLabel, $pageId, $init, $level);
}

/**
 * Initialize session parameters for update display
 * @param String $statusId
 */
function display_up($notification_sid)
{
	
    $notifCtrl = new notifications_controler();
    $state = true;
    $notif = $notifCtrl->get($notification_sid);

    if (empty($notif)) {
        $state = false;
    } else {
		//var_dump($notif);
        put_in_session('notification', $notif->getArray());
    }
    return $state;
}

/**
 * Initialize session parameters for add display
 */
function display_add()
{
    if (!isset($_SESSION['m_admin']['init'])) {
        init_session();
    }
}

/**
 * Initialize session parameters for list display
 */
function display_list() {
    $_SESSION['m_admin'] = array();
    $list = new list_show();
    $func = new functions();
    init_session();

    $select[NOTIFICATIONS] = array();
    array_push(
        $select[NOTIFICATIONS], 'notification_sid', 'notification_id', 'description'
    );
    $where = '';
    $what = '';
    if (isset($_REQUEST['what'])) {
        $what = $func->protect_string_db($_REQUEST['what']);
    }
    $where .= " (lower(description) like lower('"
				. $func->protect_string_db($what, $_SESSION['config']['databasetype'])
				. "%') or lower(description) like lower('"
				. $func->protect_string_db($what, $_SESSION['config']['databasetype'])
				. "%')) ";

    // Checking order and order_field values
    $order = 'asc';
    if (isset($_REQUEST['order']) && !empty($_REQUEST['order'])) {
        $order = trim($_REQUEST['order']);
    }

    $field = 'description';
    if (isset($_REQUEST['order_field']) && !empty($_REQUEST['order_field'])) {
        $field = trim($_REQUEST['order_field']);
    }

    $orderstr = $list->define_order($order, $field);
    $request = new request();
    $tab = $request->select(
        $select, $where, $orderstr, $_SESSION['config']['databasetype']
    );
	//$request->show();
	
    for ($i=0;$i<count($tab);$i++) {
        foreach ($tab[$i] as &$item) {
            switch ($item['column']) {
                case 'notification_sid':
                    format_item(
                        $item, _ID, '18', 'left', 'left', 'bottom', true
                    );
                    break;
                case 'notification_id':
                    format_item(
                        $item, _NOTIFICATION_ID, '18', 'left', 'left', 'bottom', true
                    );
                    break;
                case 'description':
                    format_item(
                        $item, _DESC, '55', 'left', 'left', 'bottom', true
                    );
                    break;
            }
        }
    }
    $_SESSION['m_admin']['init'] = true;
    $result = array(
        'tab'                 => $tab,
        'what'                => $what,
        'page_name'           => 'manage_notifications_controler&mode=list',
        'page_name_add'       => 'manage_notifications_controler&mode=add',
        'page_name_up'        => 'manage_notifications_controler&mode=up',
        'page_name_del'       => 'manage_notifications_controler&mode=del',
        'page_name_val'       => '',
        'page_name_ban'       => '',
        'label_add'           => _ADD_NOTIF,
        'title'               => _NOTIFS_LIST . ' : ' . $i,
        'autoCompletionArray' => array(
                                     'list_script_url'  =>
                                        $_SESSION['config']['businessappurl']
                                        . 'index.php?display=true&module=notifications'
                                        . '&page=manage_notifs_list_by_name',
                                     'number_to_begin'  => 1
                                 ),

    );
    return $result;
}

/**
 * Delete given status if exists and initialize session parameters
 * @param string $statusId
 */
function display_del($notification_sid) {
    $notifCtrl = new notifications_controler();
    $notif = $notifCtrl->get($notification_sid);
    if (isset($notif)) {
        // Deletion
        $control = array();
        $params  = array( 'log_status_del' => $_SESSION['history']['eventdel'],
                         'databasetype' => $_SESSION['config']['databasetype']
                        );
        $control = $notifCtrl->delete($notif, $params);
        if (!empty($control['error']) && $control['error'] <> 1) {
            $_SESSION['error'] = str_replace("#", "<br />", $control['error']);
        } else {
            $_SESSION['error'] = _NOTIF_DELETED.' : '.$notification_sid;
        }
        ?><script type="text/javascript">window.top.location='<?php
            echo $_SESSION['config']['businessappurl']
                . 'index.php?page=manage_notifications_controler&mode=list&module='
                . 'notifications&order=' . $_REQUEST['order'] . '&order_field='
                . $_REQUEST['order_field'] . '&start=' . $_REQUEST['start']
                . '&what=' . $_REQUEST['what'];
        ?>';</script>
        <?php
        exit();
    } else {
        // Error management
        $_SESSION['error'] = _NOTIF.' '._UNKNOWN;
    }
}

/**
 * Format given item with given values, according with HTML formating.
 * NOTE: given item needs to be an array with at least 2 keys:
 * 'column' and 'value'.
 * NOTE: given item is modified consequently.
 * @param $item
 * @param $label
 * @param $size
 * @param $labelAlign
 * @param $align
 * @param $valign
 * @param $show
 */
function format_item(
    &$item, $label, $size, $labelAlign, $align, $valign, $show, $order = true
) {
    $func = new functions();
    $item['value'] = $func->show_string($item['value']);
    $item[$item['column']] = $item['value'];
    $item['label'] = $label;
    $item['size'] = $size;
    $item['label_align'] = $labelAlign;
    $item['align'] = $align;
    $item['valign'] = $valign;
    $item['show'] = $show;
    if ($order) {
        $item['order'] = $item['value'];
    } else {
        $item['order'] = '';
    }
}

/**
 * Validate a submit (add or up),
 * up to saving object
 */
function validate_notif_submit() {
	$dType = new diffusion_type_controler();
	$diffType = array();
	$diffType = $dType->getAllDiffusion();
   
    $notifCtrl = new notifications_controler();
    $pageName = 'manage_notifications_controler';

    $mode = $_REQUEST['mode'];
    $notifObj = new notifications();
    
    if ($mode <> 'add'){
		$notifObj->notification_sid = $_REQUEST['notification_sid'];
	}
    $notifObj->notification_id = $_REQUEST['notification_id'];
	$notifObj->description = $_REQUEST['description'];
    $notifObj->notification_mode = $_REQUEST['notification_mode'];
    $notifObj->event_id = $_REQUEST['event_id'];
    $notifObj->template_id = $_REQUEST['template_id'];
    $notifObj->diffusion_type = $_REQUEST['diffusion_type'];
    $notifObj->attachfor_type = $_REQUEST['attachfor_type'];
    
	foreach($diffType as $loadedType) 	{
		if ($loadedType->id == $notifObj->diffusion_type){
			if ($loadedType -> script <> '' && !empty($_REQUEST['diffusion_properties'])) {
				$diffusion_properties_string = implode(',', $_REQUEST['diffusion_properties']);
			} else {
				$error .= 'System : Unable to load Require Script';
			}
		}
		if ($loadedType->id == $notifObj->attachfor_type){
			if ($loadedType -> script <> '' && !empty($_REQUEST['attachfor_properties'])) {
				$attachfor_properties_string = implode(',', $_REQUEST['attachfor_properties']);
			} else {
				$error .= 'System : Unable to load Require Script';
			}
		}			
	}		
			
	$notifObj->diffusion_properties = (string)$diffusion_properties_string;
	$notifObj->attachfor_properties = (string)$attachfor_properties_string;
	
    $control = $notifCtrl->save($notifObj, $mode, $params);

    if (!empty($control['error']) && $control['error'] <> 1) {
        // Error management depending of mode
        $_SESSION['error'] = str_replace("#", "<br />", $control['error']);
        //put_in_session('event', $notif);
		put_in_session('notification', $notifObj->getArray());

        switch ($mode) {
            case 'up':
                if (!empty($notifObj->notification_sid)) {
                    header(
                        'location: ' . $_SESSION['config']['businessappurl']
                        . 'index.php?page=' . $pageName . '&mode=up&id='
                        . $notifObj->notification_sid . '&module=notifications'
                    );
                } else {
                    header(
                        'location: ' . $_SESSION['config']['businessappurl']
                        . 'index.php?page=' . $pageName . '&mode=list&module='
                        .'notifications&order=' . $status['order'] . '&order_field='
                        . $status['order_field'] . '&start=' . $status['start']
                        . '&what=' . $status['what']
                    );
                }
                exit();
            case 'add':
                header(
                    'location: ' . $_SESSION['config']['businessappurl']
                    . 'index.php?page=' . $pageName . '&mode=add&module=notifications'
                );
                exit();
        }
    } else {
        if ($mode == 'add') {
            $_SESSION['error'] = _NOTIF_ADDED;
        } else {
            $_SESSION['error'] = _NOTIF_MODIFIED;
        }
        unset($_SESSION['m_admin']);
        header(
            'location: ' . $_SESSION['config']['businessappurl']
            . 'index.php?page=' . $pageName . '&mode=list&module=notifications&order='
            . $status['order'] . '&order_field=' . $status['order_field']
            . '&start=' . $status['start'] . '&what=' . $status['what']
        );
    }
   
}

function init_session()
{
    $_SESSION['m_admin']['notification'] = array(
        'notification_sid'  	    	=> '',
		'notification_id'  	 	=> '',
        'event_id'  			=> '',
        'description'  			=> '',
        'notification_mode'		=> '',
        'template_id'    	 	=> '',
        'diffusion_type'    	=> '',
        'diffusion_properties'  => '',
		//'diffusion_content'   => '',
        'attachfor_type' 		=> '',
        'attachfor_properties' 	=> ''
    );
}

/**
 * Put given object in session, according with given type
 * NOTE: given object needs to be at least hashable
 * @param string $type
 * @param hashable $hashable
 */
function put_in_session($type, $hashable, $showString = true)
{
    $func = new functions();
	foreach ($hashable as $key=>$value) {
		if ($showString) {
			$_SESSION['m_admin'][$type][$key]=$func->show_string($value);
		} else {
			$_SESSION['m_admin'][$type][$key]=$value;
		}
	}
    //print_r($_SESSION['m_admin']);
}