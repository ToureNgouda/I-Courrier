<?php

/*
 *    Copyright 2008,2015 Maarch
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
*  Login Class
*
* Contains all pre-login function
*
* @package  Maarch
* @version 2.1
* @since 10/2005
* @license GPL
* @author  Loïc Vinet  <dev@maarch.org>
*
*/


abstract class login_Abstract extends functions
{
    function build_login_method()
    {
        $pathtoxmllogin = '';

        if (file_exists(
            $_SESSION['config']['corepath'] . 'custom' . DIRECTORY_SEPARATOR
            . $_SESSION['custom_override_id'] . DIRECTORY_SEPARATOR . 'apps'
            . DIRECTORY_SEPARATOR . $_SESSION['config']['app_id']
            . DIRECTORY_SEPARATOR . 'xml' . DIRECTORY_SEPARATOR
            . 'login_method.xml'
        )) {
            $pathtoxmllogin = $_SESSION['config']['corepath'] . 'custom'
                            . DIRECTORY_SEPARATOR
                            . $_SESSION['custom_override_id']
                            . DIRECTORY_SEPARATOR . 'apps' . DIRECTORY_SEPARATOR
                            . $_SESSION['config']['app_id']
                            . DIRECTORY_SEPARATOR . 'xml' . DIRECTORY_SEPARATOR
                            . 'login_method.xml';
        } else {
            $pathtoxmllogin = 'apps' . DIRECTORY_SEPARATOR
                            . $_SESSION['config']['app_id']
                            . DIRECTORY_SEPARATOR . 'xml' . DIRECTORY_SEPARATOR
                            . 'login_method.xml';
        }

        $login_method = array();

        $xmlconfig = simplexml_load_file($pathtoxmllogin);
        if (! $xmlconfig) {
            exit();
        }

        foreach ($xmlconfig->METHOD as $METHOD) {
            $id = (string) $METHOD->ID;
            $brut_label = (string) $METHOD->NAME;
            $scriptname = (string) $METHOD->SCRIPT;
            $activated = (string) $METHOD->ENABLED;

            array_push(
                $login_method,
                array(
                    'ID'         => $id,
                    'BRUT_LABEL' => $brut_label,
                    'SCRIPTNAME' => $scriptname,
                    'ACTIVATED'  =>$activated
                )
            );
        }

        $_SESSION['login_method_memory'] = $login_method;
        return $login_method;
    }

    public function execute_login_script($array_method, $restMode = false)
    {
        $tmp_engine = array();
        $_SESSION['login_method_bool'] = false;

        foreach ($array_method as $only) {
            if ($only['ACTIVATED'] == 'true') {
                $tmp_engine = $only;
            }
        }
        
        $script_method = $tmp_engine['SCRIPTNAME'];

        if (file_exists(
            $_SESSION['config']['corepath'] . 'custom' . DIRECTORY_SEPARATOR
            . $_SESSION['custom_override_id'] . DIRECTORY_SEPARATOR . 'apps'
            . DIRECTORY_SEPARATOR . $_SESSION['config']['app_id']
            . DIRECTORY_SEPARATOR . $script_method
        )) {
            include
                $_SESSION['config']['corepath'] . 'custom' . DIRECTORY_SEPARATOR
                . $_SESSION['custom_override_id'] . DIRECTORY_SEPARATOR . 'apps'
                . DIRECTORY_SEPARATOR . $_SESSION['config']['app_id']
                . DIRECTORY_SEPARATOR . $script_method;
        } else {
            include
                'apps' . DIRECTORY_SEPARATOR . $_SESSION['config']['app_id']
                . DIRECTORY_SEPARATOR . $script_method;
        }
    }

}