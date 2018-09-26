<?php

/**
*   @copyright 2017 Maarch
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

require_once 'core/class/class_core_tools.php';
require_once 'core/services/MaarchException.php';

/**
 * 
 */
abstract class Core_Abstract_Service {
	/**
	 * Récupération de la liste des méthodes disponibles via api
	 * 
	 * @return string[] La liste des méthodes
	 */
	public static function getApiMethod() {
		return [
			'getApiMethod' => 'getApiMethod',
		];
	}

	/**
	 * Vérifie que l'user est bien les droits requis
	 * @param array $aRequired
	 * @return boolean true
	 * @throws Exception denied
	**/
	protected static function checkAllow(array $aRequired) {
		$core = new core_tools();
		foreach ($aRequired as $permission) {
			if ( ! $core->test_service($permission, 'apps', false) ) {
				throw new Core_MaarchException_Service('missing permission required : '.$permission);
			}
		}
		return true;
	}

	/**
	 * Vérifie que l'argument est bien existant
	 * @param array $aArgs
	 * @param array $aRequired
	 * @param string $sErrorTxt
	**/
	protected static function checkIsset(array $aArgs, $aRequired, $sErrorTxt='$required is not set') {
		if ( is_string($aRequired) ) {
			$aRequired = [$aRequired];
		}
		if ( ! is_array($aRequired) ) {
			throw new Core_MaarchException_Service("aRequired is not a array", 1);
		}
		foreach ($aRequired as $required) {
			if ( !isset($aArgs[$required]) ) {
				throw new Core_MaarchException_Service(str_replace('$required', $required, $sErrorTxt));
			}
		}
		return true;
	}

	/**
	 * Vérifie que l'argument est bien rempli
	 * @param array $aArgs
	 * @param array $aRequired
	 * @param string $sErrorTxt
	**/
	protected static function checkRequired(array $aArgs, $aRequired, $sErrorTxt='$required is required') {
		if ( is_string($aRequired) ) {
			$aRequired = [$aRequired];
		}
		if ( ! is_array($aRequired) ) {
			throw new Core_MaarchException_Service("aRequired is not a array", 1);
		}
		foreach ($aRequired as $required) {
			if ( !isset($aArgs[$required]) ) {
				throw new Core_MaarchException_Service(str_replace('$required', $required, $sErrorTxt));
			}
			if ( empty($aArgs[$required]) ) {
				throw new Core_MaarchException_Service(str_replace('$required', $required, $sErrorTxt));
			}
		}
		return true;
	}

	/**
	 * Vérifie que l'argument est bien inexistant ou un string
	 * @param array $aArgs
	 * @param array $aTry
	 * @param string $sErrorTxt
	**/
	protected static function checkString(array $aArgs, $aTry, $sErrorTxt='$try must be a string') {
		if ( is_string($aTry) ) {
			$aTry = [$aTry];
		}
		if ( ! is_array($aTry) ) {
			throw new Core_MaarchException_Service("aTry is not a array", 1);
		}
		foreach ($aTry as $try) {
			if ( !isset($aArgs[$try]) ) {
				continue;
			}
			if ( empty($aArgs[$try]) ) {
				continue;
			}
			if ( ! is_string($aArgs[$try]) ) {
				throw new Core_MaarchException_Service(str_replace('$try', $try, $sErrorTxt));
			}
		}
		return true;
	}

	/**
	 * Vérifie que l'argument est bien inexistant ou un nombre
	 * @param array $aArgs
	 * @param array $aTry
	 * @param string $sErrorTxt
	**/
	protected static function checkNumeric(array $aArgs, $aTry, $sErrorTxt='$try must be a number') {
		if ( is_string($aTry) ) {
			$aTry = [$aTry];
		}
		if ( ! is_array($aTry) ) {
			throw new Core_MaarchException_Service("aTry is not a array", 1);
		}
		foreach ($aTry as $try) {
			if ( !isset($aArgs[$try]) ) {
				continue;
			}
			if ( empty($aArgs[$try]) ) {
				continue;
			}
			if ( ! is_numeric($aArgs[$try]) ) {
				throw new Core_MaarchException_Service(str_replace('$try', $try, $sErrorTxt));
			}
		}
		return true;
	}

	/**
	 * Vérifie que l'argument est bien inexistant ou un tableau
	 * @param array $aArgs
	 * @param array $aTry
	 * @param string $sErrorTxt
	**/
	protected static function checkArray(array $aArgs, $aTry, $sErrorTxt='$try must be a array') {
		if ( is_string($aTry) ) {
			$aTry = [$aTry];
		}
		if ( ! is_array($aTry) ) {
			throw new Core_MaarchException_Service("aTry is not a array", 1);
		}
		foreach ($aTry as $try) {
			if ( !isset($aArgs[$try]) ) {
				continue;
			}
			if ( empty($aArgs[$try]) ) {
				continue;
			}
			if ( ! is_array($aArgs[$try]) ) {
				throw new Core_MaarchException_Service(str_replace('$try', $try, $sErrorTxt));
			}
		}
		return true;
	}

	/**
	 * Vérifie que l'argument est bien inexistant ou une instance
	 * @param array $aArgs
	 * @param array $aTry
	 * @param string $sErrorTxt
	**/
	protected static function checkObject(array $aArgs, $aTry, $sErrorTxt='$try must be an instance') {
		if ( is_string($aTry) ) {
			$aTry = [$aTry];
		}
		if ( ! is_array($aTry) ) {
			throw new Core_MaarchException_Service("aTry is not a array", 1);
		}
		foreach ($aTry as $try) {
			if ( !isset($aArgs[$try]) ) {
				continue;
			}
			if ( empty($aArgs[$try]) ) {
				continue;
			}
			if ( ! is_object($aArgs[$try]) ) {
				throw new Core_MaarchException_Service(str_replace('$try', $try, $sErrorTxt));
			}
		}
		return true;
	}
	protected static function formatDatestring($sDate) {
		$sDate = trim($sDate);
		$sDate = preg_replace('#^(\w{2})/(\w{2})/(\w{4})\s(\d{2}):(\d{2})#', '$3-$2-$1 $4:$5:00', $sDate);
		$sDate = preg_replace('#^(\w{2})/(\w{2})/(\w{4})$#', '$3-$2-$1', $sDate);
		return $sDate;
	}

	/**
	 * Vérifie que l'argument est bien inexistant ou un string representant une date
	 * @param array $aArgs
	 * @param array $aTry
	 * @param string $sErrorTxt
	**/
	protected static function checkDatestring(array $aArgs, $aTry, $sErrorTxt='$try must be a date (string) : $value') {
		if ( is_string($aTry) ) {
			$aTry = [$aTry];
		}
		if ( ! is_array($aTry) ) {
			throw new Core_MaarchException_Service("aTry is not a array", 1);
		}
		self::checkString($aArgs, $aTry, $sErrorTxt);
		foreach ($aTry as $try) {
			if ( !isset($aArgs[$try]) ) {
				continue;
			}
			$aArgs[$try] = trim($aArgs[$try]);
			if ( empty($aArgs[$try]) ) {
				continue;
			}
			if ( ! strtotime($aArgs[$try]) ) {
				throw new Core_MaarchException_Service(str_replace(['$try','$value',], [$try,$aArgs[$try],], $sErrorTxt));
			}
		}
		return true;
	}

	/**
	 * Vérifie que l'argument est bien inexistant ou un objet Date
	 * @param array $aArgs
	 * @param array $aTry
	 * @param string $sErrorTxt
	**/
	protected static function checkDateobject(array $aArgs, $aTry, $sErrorTxt='$try must be a date (instance)') {
		if ( is_string($aTry) ) {
			$aTry = [$aTry];
		}
		if ( ! is_array($aTry) ) {
			throw new Core_MaarchException_Service("aTry is not a array", 1);
		}
		self::checkObject($aArgs, $aTry, $sErrorTxt);
		foreach ($aTry as $try) {
			if ( !isset($aArgs[$try]) ) {
				continue;
			}
			if ( empty($aArgs[$try]) ) {
				continue;
			}
			if ( $aArgs[$try] instanceof \Date || $aArgs[$try] instanceof \DateTime ) {
				throw new Core_MaarchException_Service(str_replace('$try', $try, $sErrorTxt));
			}
		}
		return true;
	}

	/**
	 * Vérifie que l'argument est bien inexistant ou un tableau de string
	 * @param array $aArgs
	 * @param array $aTry
	 * @param string $sErrorTxt
	**/
	protected static function checkArrayString(array $aArgs, $aTry, $sErrorTxt='$try must be a array of string') {
		self::checkArray($aArgs, $aTry, $sErrorTxt); // Je testerai que la sous partie des tableaux, et je délégue la vérification du typage tableau
		if ( is_string($aTry) ) {
			$aTry = [$aTry];
		}
		if ( ! is_array($aTry) ) {
			throw new Core_MaarchException_Service("aTry is not a array", 1);
		}
		foreach ($aTry as $array) {
			if ( empty($aArgs[$array]) ) {
				continue;
			}
			foreach ($aArgs[$array] as $try) {
				if ( !isset($aArgs[$try]) ) {
					continue;
				}
				if ( empty($aArgs[$try]) ) {
					continue;
				}
				if ( ! is_string($aArgs[$try]) ) {
					throw new Core_MaarchException_Service(str_replace('$try', $try, $sErrorTxt));
				}
			}
		}
		return true;
	}

	/**
	 * Vérifie que l'argument est bien inexistant ou un tableau de numeric
	 * @param array $aArgs
	 * @param array $aTry
	 * @param string $sErrorTxt
	**/
	protected static function checkArrayNumeric(array $aArgs, $aTry, $sErrorTxt='$try must be a array of numeric') {
		self::checkArray($aArgs, $aTry, $sErrorTxt); // Je testerai que la sous partie des tableaux, et je délégue la vérification du typage tableau
		if ( is_string($aTry) ) {
			$aTry = [$aTry];
		}
		if ( ! is_array($aTry) ) {
			throw new Core_MaarchException_Service("aTry is not a array", 1);
		}
		foreach ($aTry as $array) {
			if ( empty($aArgs[$array]) ) {
				continue;
			}
			foreach ($aArgs[$array] as $try) {
				if ( !isset($aArgs[$try]) ) {
					continue;
				}
				if ( empty($aArgs[$try]) ) {
					continue;
				}
				if ( ! is_numeric($aArgs[$try]) ) {
					throw new Core_MaarchException_Service(str_replace('$try', $try, $sErrorTxt));
				}
			}
		}
		return true;
	}
}
