<?php
function getHeaders() 
{

    foreach ($_SERVER as $h => $v ) 
    {      
      if( preg_match( '/HTTP_(.+)/', $h, $hp ) )
        $headers[$hp[1]] = $v ;
    }
    return $headers;
}

if ($restMode) {
    $userLogin = [];
    $http_header = getHeaders();
    //HTTP AUTH
    if (
        (isset($_SERVER["PHP_AUTH_USER"])
            && isset($_SERVER["PHP_AUTH_PW"])
            && isset($_SERVER["HTTP_AUTHORIZATION"])
        )
        && $_SERVER["PHP_AUTH_USER"] && $_SERVER["PHP_AUTH_PW"]
        && preg_match("/^Basic /", $_SERVER["HTTP_AUTHORIZATION"])
    ) {
        list($_SERVER["PHP_AUTH_USER"], $_SERVER["PHP_AUTH_PW"])
            = explode(":", base64_decode(substr($_SERVER["HTTP_AUTHORIZATION"], 6)));
    } elseif (isset($http_header['LOGIN']) && isset($http_header['PASSWORD'])) {
        $force_login = $http_header['LOGIN'];
        $force_psw = $http_header['PASSWORD'];
    } elseif(!isset($_SERVER["PHP_AUTH_USER"])) {
        header("WWW-Authenticate: Basic realm=\"Maarch WebServer Engine\"");
        if (preg_match("/Microsoft/", $_SERVER["SERVER_SOFTWARE"])) {
            header("Status: 401 Unauthorized");
            exit();
        } else {
            header("HTTP/1.0 401 Unauthorized");
            exit();
        }
    }
    if (
        (isset($_SERVER["PHP_AUTH_USER"])
            && isset($_SERVER["PHP_AUTH_PW"])
        )
        && ($_SERVER["PHP_AUTH_USER"] || $_SERVER["PHP_AUTH_PW"])
    ) {
        $_SESSION['user']['UserId'] = $_SERVER["PHP_AUTH_USER"];
        $password = $_SERVER["PHP_AUTH_PW"];
    }

    else if (isset($force_login) && isset($force_psw)){
        $_SESSION['user']['UserId'] = $force_login;
        $password = $force_psw;
    }

    $userLogin['user'] = $_SESSION['user']['UserId'];
    $userLogin['password'] = $password;

    require_once 'core/class/class_security.php';
    $sec = new security();
    $_SESSION['error'] = '';
    $res = $sec->login($userLogin['user'], $userLogin['password'], 'restMode');
    //var_dump($res);
    $_SESSION['user'] = $res['user'];
    if (!empty($res['error'])) {
        $_SESSION['error'] = $res['error'];
    } else {
        require_once('core/class/class_history.php');
        $trace = new history();
        $trace->add(
            "users",
            $userLogin['user'],
            "LOGIN",
            _CONNECTION_STANDARD_OK,
            $_SESSION['config']['databasetype'],
            "ADMIN",
            false
        );
    }
} elseif (isset($_REQUEST['askRACode']) && $_REQUEST['askRACode'] == 'true') {
    echo '<div>';
        echo '<p>';
            echo '&nbsp;&nbsp;&nbsp;&nbsp;<br /><br /><br /><br /><br /><br />';
            echo _ASK_RA_CODE_1 . functions::xssafe($_SESSION['user']['Mail']) . '<br />';
            echo '<br />';
            echo _ASK_RA_CODE_2 . '<br />';
            echo '<br />';

            echo '<input onclick="window.location.href=\'' 
                .  $_SESSION['config']['businessappurl']
                .'index.php?display=true&confirmAskRACode=true&page=login' 
                . '\'" type="button" class="button" name="submit" value="'._SEND.'" />';
            echo '&nbsp;&nbsp;';

            echo '<input onclick="window.location.href=\'' 
                . $_SESSION['config']['businessappurl'].'index.php?display=true&page=login' 
                . '\'" type="button" class="button" name="submit" value="'._CANCEL.'" />';
            
        echo '</p>';
    echo '</div>';
}
elseif(isset($_REQUEST['confirmAskRACode']) && $_REQUEST['confirmAskRACode'] == 'true') {
    //generation du remote_access_code aléatoirement
    $authorized_characters = '123456789';
    $cpt_motDePasse = 1;
    $cptMax_motDePasse = 4;
    $max_rand = strlen($authorized_characters);
    $raCodeGenerated = '';
    while (strlen($raCodeGenerated) < $cptMax_motDePasse) {
        $raCodeGenerated .= rand(1, $max_rand);
        $cpt_motDePasse++;
     }
    //calcul de la date d'expiration
    
    $pathToIPFilter = '';
    if(file_exists($_SESSION['config']['corepath'].'custom'.DIRECTORY_SEPARATOR
            .$_SESSION['custom_override_id'].DIRECTORY_SEPARATOR.'apps'
            .DIRECTORY_SEPARATOR.$_SESSION['config']['app_id']
            .DIRECTORY_SEPARATOR.'xml'.DIRECTORY_SEPARATOR.'ip_filter.xml')){
        $pathToIPFilter = $_SESSION['config']['corepath']
            .'custom'.DIRECTORY_SEPARATOR.$_SESSION['custom_override_id']
            .DIRECTORY_SEPARATOR.'apps'.DIRECTORY_SEPARATOR
            .$_SESSION['config']['app_id'].DIRECTORY_SEPARATOR.'xml'.DIRECTORY_SEPARATOR.'ip_filter.xml';
    } 
    else {
        $pathToIPFilter = 'apps'.DIRECTORY_SEPARATOR.$_SESSION['config']['app_id']
        .DIRECTORY_SEPARATOR.'xml'.DIRECTORY_SEPARATOR.'ip_filter.xml';
    }
    $ipArray = array();
    $ipArray = functions::object2array(simplexml_load_file($pathToIPFilter));
    
    $nextWeek  = mktime(0, 0, 0, date("m"),   date("d")+$ipArray['duration'],   date("Y"));
    $expiration_date = date("Y-m-d", $nextWeek);
    
    $db = new Database();
    $db->query("UPDATE users set ra_code = ? WHERE user_id = ?", array(md5($raCodeGenerated), $_SESSION['user']['UserId']), false);
    $db->query("UPDATE users set ra_expiration_date = ? WHERE user_id = ?", array($expiration_date, $_SESSION['user']['UserId']), false);
    
    $mailDest = $db->query("SELECT mail FROM users WHERE user_id = ?", array($_SESSION['user']['UserId']), false);
    
    $mailToSend = '<html>';
        $mailToSend .= '<body>';
            $mailToSend .= '<p>';
                $mailToSend .= _CONFIRM_ASK_RA_CODE_1 . '<br />';
                $mailToSend .= _CONFIRM_ASK_RA_CODE_2 . $raCodeGenerated . ' <br />';
                $mailToSend .= _CONFIRM_ASK_RA_CODE_3 . $expiration_date . '<br />';
                $mailToSend .= _CONFIRM_ASK_RA_CODE_4 . '<a href="';
                $mailToSend .= $_SESSION['config']['coreurl'].'index.php?withRA_CODE';
                $mailToSend .= '">' . _CONFIRM_ASK_RA_CODE_5 . '</a>';
            $mailToSend .= '</p>';
        $mailToSend .= '</body>';
    $mailToSend .= '</html>';
    
    if (!mail(
        $_SESSION['user']['Mail'], _CONFIRM_ASK_RA_CODE_6, $mailToSend, 
        "From: info@maarch.org\nReply-To: info@maarch.org \nContent-Type: text/html; charset=\"iso-8859-1\"\n")
    ) {
        echo 'mail not send';
    }
    
    $_SESSION['error'] = '_IP_NOT_ALLOWED';
    echo '<br /><br /><br /><br /><br /><br />';
    echo _CONFIRM_ASK_RA_CODE_7 . '<br /><br />';
    echo '<a href="';
    echo $_SESSION['config']['businessappurl'].'index.php?display=true&page=login';
    echo '">' . _CONFIRM_ASK_RA_CODE_8 . '</a>';
}
else {
    $userId = '';
echo '<form id="formlogin" method="post" action="'
    . $_SESSION['config']['businessappurl']
    . 'index.php?display=true&page=log';
    if (isset($_SESSION['config']['debug'])
        && $_SESSION['config']['debug'] == 'true'
    ) {
        echo '&XDEBUG_PROFILE';
    }
    echo '" class="forms">';
            echo '<div>';
                echo '<input type="hidden" name="display" id="display" value="true" />';
                echo '<input type="hidden" name="page" id="page" value="log" />';
                if ($_SESSION['error'] == '_IP_NOT_ALLOWED') {
                    $_SESSION['error'] = '';
                    $ipNotAllowed = true;
                    $ra_code = true;
                    $userId = functions::xssafe($_SESSION['user']['UserId']);
                    echo '<div>';
                        echo '<br /><br /><br /><br /><br /><br />';
                        echo _TRYING_TO_CONNECT_FROM_NOT_ALLOWED_IP;
                        echo '<br />';
                        echo _PLEASE_ENTER_YOUR_RA_CODE;
                    echo '</div>';
                }
                elseif ($_SESSION['error'] == '_IP_NOT_ALLOWED_NO_RA_CODE') {
                    $_SESSION['error'] = '';
                    $ipNotAllowed = true;
                    $ra_code = false;
                    $userId = $_SESSION['user']['UserId'];
                    echo '<div>';
                        echo _CAN_T_CONNECT_WITH_THIS_IP;
                        //echo 'Vous ne pouvez pas vous connecter depuis un emplacement non répertorié.<br />';
                    echo '</div>';
                }
                if ($ipNotAllowed && $ra_code) {
                    if (!isset($_SESSION['withRA_CODE'])) {
                        echo '<br /><p class="buttons">';
                                echo '<input onclick="window.location.href=\'' 
                                . $_SESSION['config']['businessappurl']
                                .'index.php?display=true&askRACode=true&page=login' 
                                . '\'" type="button" class="button" name="submit" value="';
                                echo _ASK_AN_RA_CODE;
                                echo '" />';
                            echo '&nbsp;&nbsp;';
                        echo '</p>';
                    } else {
                            $_SESSION['withRA_CODE'] = '';
                        }
                }
                echo '<p>';
                    echo '<br/><label for="login">'._ID.'</label>';
                    echo '<input name="login" id="login" value="'.functions::xssafe($userId)
                        .'" type="text"  />';
                echo '</p>';
                echo '<p>';
                    echo '<label for="pass">'._PASSWORD.'</label>';
                    echo '<input name="pass" id="pass" value="" type="password"  />';
                echo '</p>';
                if ($ipNotAllowed && $ra_code) {
                    echo '<p>';
                        echo '<label for="ra_code">' . _RA_CODE_1 . '</label>';
                        echo '<input name="ra_code" id="pass" value="" type="password"  />';
                    echo '</p><br />';
                }
                echo '<p>';
                echo '<label>&nbsp;</label>';
                    echo '<input type="submit" class="button" name="submit" value="'._CONNECT.'" />';
                echo '</p>';
            echo '<div class="error">';
            if(isset($_SESSION['error']))
            {
                echo functions::xssafe($_SESSION['error']);
            }
            $_SESSION['error'] = '';
            echo '</div>';
          echo '</div>';
        echo '</form>';
        /*require_once('core/class/class_core_tools.php');
        $core = new core_tools();
        echo '<br /><br /><br /><br /><br /><br /><br /><br /><br /><p id="footer">';
        $core->load_footer();
        echo '</p>';*/
}
