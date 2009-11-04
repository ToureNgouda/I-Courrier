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
* @brief   Contains all the functions to use a maarch portal
*
* @file
* @author  Laurent Giovannoni  <dev@maarch.org>
* @author Claire Figueras <dev@maarch.org>
* @date $date$
* @version $Revision$
* @ingroup core
*/

/**
* @brief   Contains all the functions to use a maarch portal
*
* @ingroup core
*/
class portal extends functions
{

	/**
	* Loads Maarch portal configuration into sessions  from an xml configuration file (core/xml/config.xml)
	*/
	public function build_config()
	{
		$xmlconfig = simplexml_load_file('core/xml/config.xml');
		foreach($xmlconfig->CONFIG as $CONFIG)
		{
			$_SESSION['config']['corename'] = (string) $CONFIG->corename;
			$_SESSION['config']['corepath'] = (string) $CONFIG->corepath;
			$_SESSION['config']['tmppath'] = (string) $CONFIG->tmppath;
			$_SESSION['config']['unixserver'] = (string) $CONFIG->unixserver;
			$_SESSION['config']['defaultpage'] = (string) $CONFIG->defaultpage;
			$_SESSION['config']['defaultlang'] = (string) $CONFIG->defaultlanguage;
			//$_SESSION['config']['coreurl'] = (string) $CONFIG->coreurl;
			if ($_SERVER['SERVER_PORT'] <> 80)
			{
				$server_port = ":".$_SERVER['SERVER_PORT'];
			}
			else
			{
				$server_port = '';
			}
			$_SESSION['config']['coreurl'] = "http://".$_SERVER['SERVER_NAME'].$server_port.str_replace('index.php','',$_SERVER['SCRIPT_NAME']);
		}
		$i=0;
		foreach($xmlconfig->BUSINESSAPPS as $BUSINESSAPPS)
		{
			$_SESSION['businessapps'][$i] = array("appid" => (string) $BUSINESSAPPS->appid,
																			"comment" => (string) $BUSINESSAPPS->comment);
		$i++;
		}
	}

	/**
	* Unset session variabless
	*/
	public function unset_session()
	{
		unset($_SESSION['config']);
		unset($_SESSION['businessapps']);
	}
}
?>
