<?php

/*
*   Copyright 2008-2016 Maarch
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

/**
* @defgroup convert convert Module
*/

/**
* @brief   Module convert :  Module Tools Class
*
* <ul>
* <li>Set the session variables needed to run the convert module</li>
* </ul>
*
* @file
* @author Laurent Giovannoni <dev@maarch.org>
* @date $date$
* @version $Revision$
* @ingroup convert
*/

/**
* @brief Module convert : Module Tools Class
*
* <ul>
* <li>Loads the tables used by the convert</li>
* <li>Set the session variables needed to run the convert module</li>
* </ul>
*
* @ingroup convert
*/
class convert extends Database
{
    function __construct()
    {
        parent::__construct();
        $this->index = array();
    }

    /**
    * Loads convert  tables into sessions vars from the
    * convert/xml/config.xml
    * Loads convert log setting into sessions vars from the
    * convert/xml/config.xml
    */
    public function build_modules_tables()
    {
        if (file_exists($_SESSION['config']['corepath'].'custom'
                        .DIRECTORY_SEPARATOR.$_SESSION['custom_override_id']
                        .DIRECTORY_SEPARATOR."modules".DIRECTORY_SEPARATOR
                        ."convert".DIRECTORY_SEPARATOR
                        ."xml".DIRECTORY_SEPARATOR."config.xml")
        ) {
            $path = $_SESSION['config']['corepath'].'custom'
                .DIRECTORY_SEPARATOR.$_SESSION['custom_override_id']
                .DIRECTORY_SEPARATOR."modules".DIRECTORY_SEPARATOR."convert"
                .DIRECTORY_SEPARATOR."xml".DIRECTORY_SEPARATOR."config.xml";
        } else {
            $path = "modules".DIRECTORY_SEPARATOR."convert"
                .DIRECTORY_SEPARATOR."xml".DIRECTORY_SEPARATOR."config.xml";
        }
        $xmlconfig = simplexml_load_file($path);
        //$CONFIG = $xmlconfig->CONFIG;
        // Loads the tables of the module convert
        // into session ($_SESSION['tablename'] array)

        // Loads the log setting of the module convert
        // into session ($_SESSION['history'] array)
        $HISTORY = $xmlconfig->HISTORY;
        $_SESSION['history']['convertadd'] = (string) $HISTORY->convertadd;
        $_SESSION['history']['convertup'] = (string) $HISTORY->convertup;
        $_SESSION['history']['convertdel'] = (string) $HISTORY->convertdel;
    }

    /**
    * Load into session vars all the convert specific vars :
    * calls private methods
    */
    public function load_module_var_session($userData)
    {
        //functions::show_array($_SESSION['convertFeatures']);
    }
}
