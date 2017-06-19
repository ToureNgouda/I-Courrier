<?php
/*
*    Copyright 2008-2016 Maarch
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

/*abstract class PrioritiesAbstract extends Database
{

    protected function  getXMLPath() {
        if (file_exists(
            $_SESSION['config']['corepath'] . 'custom' . DIRECTORY_SEPARATOR
            . $_SESSION['custom_override_id'] . DIRECTORY_SEPARATOR
            . 'apps'.DIRECTORY_SEPARATOR . $_SESSION['config']['app_id']
            . DIRECTORY_SEPARATOR . 'xml' . DIRECTORY_SEPARATOR
            . 'entreprise.xml')
        ) {
            $path = $_SESSION['config']['corepath'] . 'custom'
                . DIRECTORY_SEPARATOR . $_SESSION['custom_override_id']
                . DIRECTORY_SEPARATOR . 'apps' . DIRECTORY_SEPARATOR
                . $_SESSION['config']['app_id'] . DIRECTORY_SEPARATOR . 'xml'
                . DIRECTORY_SEPARATOR . 'entreprise.xml';
        } else {
            $path = 'apps' . DIRECTORY_SEPARATOR . $_SESSION['config']['app_id']
                . DIRECTORY_SEPARATOR . 'xml' . DIRECTORY_SEPARATOR
                . 'entreprise.xml';
        }

        return $path;
    }

    protected function  setXML($priorities) {
        $path = $this->getXMLPath();

        $xmlfile = simplexml_load_file($path);
        $mailPriorities = $xmlfile->priorities;
        for ($i = 0; $priorities[$i]; $i++) {
            if (empty($priorities[$i]['add'])) {
                $mailPriorities->priority[$i] = $priorities[$i]['label'];
                if ($priorities[$i]['number'] === '*')
                    $mailPriorities->priority[$i]['with_delay'] = 'false';
                else
                    $mailPriorities->priority[$i]['with_delay'] = $priorities[$i]['number'];
                $mailPriorities->priority[$i]['working_days'] = $priorities[$i]['wdays'];
                $mailPriorities->priority[$i]['color'] = $priorities[$i]['color'];
            } else {
                $newPriority = $mailPriorities->addChild('priority', $priorities[$i]['label']);
                if ($priorities[$i]['number'] === '*')
                    $newPriority->addAttribute('with_delay', 'false');
                else
                    $newPriority->addAttribute('with_delay', $priorities[$i]['number']);
                $newPriority->addAttribute('working_days', $priorities[$i]['wdays']);
                $newPriority->addAttribute('color', $priorities[$i]['color']);
            }
        }

        $res = $xmlfile->asXML();
        $fp = @fopen($path, "w+");
        if ($fp) {
            fwrite($fp,$res);
        }
    }

    protected function  updateSession() {
        $path = $this->getXMLPath();

        $xmlfile = simplexml_load_file($path);
        $mailPriorities = $xmlfile->priorities;

        unset($_SESSION['mail_priorities'], $_SESSION['mail_priorities_attribute'], $_SESSION['mail_priorities_wdays']);
        $_SESSION['mail_priorities'] = [];
        $_SESSION['mail_priorities_attribute'] = [];
        $_SESSION['mail_priorities_wdays'] = [];
        $_SESSION['mail_priorities_color'] = [];

        for ($i = 0; $mailPriorities->priority[$i]; $i++) {
            $label = (string) $mailPriorities->priority[$i];
            $attribute = (string) $mailPriorities->priority[$i]['with_delay'];
            $workingDays = (string) $mailPriorities->priority[$i]['working_days'];
            $color = (string) $mailPriorities->priority[$i]['color'];
            if (!empty($label) && defined($label) && constant($label) != NULL) {
                $label = constant($label);
            }
            $_SESSION['mail_priorities'][$i] = $label;
            $_SESSION['mail_priorities_attribute'][$i] = $attribute;
            $_SESSION['mail_priorities_wdays'][$i] = ($workingDays != 'false' ? 'true' : 'false');
            $_SESSION['mail_priorities_color'][$i] = $color;
        }
    }

    protected function  checkPriorities($array) {
        if (empty($array))
            return false;
        foreach ($array as $value) {
            if (empty($value['label']) || empty($value['number']) || empty($value['wdays'])) {
                return false;
            } elseif ($value['wdays'] != 'true' && $value['wdays'] != 'false') {
                return false;
            } elseif ($value['number'] === '*') {
            } elseif (!ctype_digit($value['number'])) {
                return false;
            } elseif ((int)$value['number'] < 0) {
                return false;
            }
        }
        return true;
    }

    public function updatePriorities() {
        $priorities = [];
       
        for ($i = 0; isset($_REQUEST[('label_' . $i)]) && isset($_REQUEST[('priority_' . $i)]) && isset($_REQUEST[('working_' . $i)]) && isset($_REQUEST[('color_' . $i)]); $i++) {
            $priorities[] = ['label' => $_REQUEST[('label_' . $i)], 'number' => $_REQUEST[('priority_' . $i)], 'wdays' => $_REQUEST[('working_' . $i)], 'color' => $_REQUEST[('color_' . $i)]];
        }
        for ($i = 0; !empty($_REQUEST[('label_new' . $i)]) && !empty($_REQUEST[('priority_new' . $i)]) && !empty($_REQUEST[('working_new' . $i)]) && !empty($_REQUEST[('color_new' . $i)]); $i++) {
            $priorities[] = ['add' => 'add', 'label' => $_REQUEST[('label_new' . $i)], 'number' => $_REQUEST[('priority_new' . $i)], 'wdays' => $_REQUEST[('working_new' . $i)], 'color' => $_REQUEST[('color_new' . $i)]];
        }
        if ($this->checkPriorities($priorities)) {
            $this->setXML($priorities);
            $this->updateSession();
            $_SESSION['info'] = _PRIORITIES_UPDATED;
        } else {
            $_SESSION['error'] = _PRIORITIES_ERROR;
        }
    }

    public function deletePriority() {
        if (isset($_REQUEST['indexToDelete']) && ctype_digit($_REQUEST['indexToDelete'])) {
            $indexToDelete = (int) $_REQUEST['indexToDelete'];

            $db = new Database();

            $stmt = $db->query("SELECT priority FROM res_letterbox WHERE priority = ? ",
                [$indexToDelete]
            );

            if ($stmt->rowCount() == 0) {
                $path = $this->getXMLPath();

                $xmlfile = simplexml_load_file($path);
                $mailPriorities = $xmlfile->priorities;
                unset($mailPriorities->priority[$indexToDelete]);
                $res = $xmlfile->asXML();
                $fp = @fopen($path, "w+");
                if ($fp) {
                    fwrite($fp,$res);
                }
                $this->updateSession();
            } else {
                $_SESSION['error'] = _PRIORITIES_ERROR_TAKEN;
            }

        }

    }
}*/
namespace Core\Models;

require_once 'apps/maarch_entreprise/services/Table.php';

abstract class PrioritiesModelAbstract extends \Apps_Table_Service
{

    protected static function  getXMLPath() {
        if (file_exists(
            $_SESSION['config']['corepath'] . 'custom' . DIRECTORY_SEPARATOR
            . $_SESSION['custom_override_id'] . DIRECTORY_SEPARATOR
            . 'apps'.DIRECTORY_SEPARATOR . $_SESSION['config']['app_id']
            . DIRECTORY_SEPARATOR . 'xml' . DIRECTORY_SEPARATOR
            . 'entreprise.xml')
        ) {
            $path = $_SESSION['config']['corepath'] . 'custom'
                . DIRECTORY_SEPARATOR . $_SESSION['custom_override_id']
                . DIRECTORY_SEPARATOR . 'apps' . DIRECTORY_SEPARATOR
                . $_SESSION['config']['app_id'] . DIRECTORY_SEPARATOR . 'xml'
                . DIRECTORY_SEPARATOR . 'entreprise.xml';
        } else {
            $path = 'apps' . DIRECTORY_SEPARATOR . $_SESSION['config']['app_id']
                . DIRECTORY_SEPARATOR . 'xml' . DIRECTORY_SEPARATOR
                . 'entreprise.xml';
        }

        return $path;
    }

    protected static function  setXML($priorities) {
        $path = self::getXMLPath();

        $xmlfile = simplexml_load_file($path);
        $mailPriorities = $xmlfile->priorities;
        for ($i = 0; $priorities[$i]; $i++) {
            if (isset($mailPriorities->priority[$i])) {
                $mailPriorities->removeChild('priority', $i);
            }
            $newPriority = $mailPriorities->addChild('priority', $priorities[$i]['label']);
            if ($priorities[$i]['number'] === '*') {
                $newPriority->addAttribute('with_delay', 'false');
            } else {
                $newPriority->addAttribute('with_delay', $priorities[$i]['number']);
            }
            $newPriority->addAttribute('working_days', $priorities[$i]['wdays']);
            $newPriority->addAttribute('color', $priorities[$i]['color']);
            
        }

        $res = $xmlfile->asXML();
        $fp = @fopen($path, "w+");
        if ($fp) {
            fwrite($fp,$res);
        }
    }

    protected static function  updateSession() {
        $path = self::getXMLPath();

        $xmlfile = simplexml_load_file($path);
        $mailPriorities = $xmlfile->priorities;

        unset($_SESSION['mail_priorities'], $_SESSION['mail_priorities_attribute'], $_SESSION['mail_priorities_wdays']);
        $_SESSION['mail_priorities'] = [];
        $_SESSION['mail_priorities_attribute'] = [];
        $_SESSION['mail_priorities_wdays'] = [];
        $_SESSION['mail_priorities_color'] = [];

        for ($i = 0; $mailPriorities->priority[$i]; $i++) {
            $label = (string) $mailPriorities->priority[$i];
            $attribute = (string) $mailPriorities->priority[$i]['with_delay'];
            $workingDays = (string) $mailPriorities->priority[$i]['working_days'];
            $color = (string) $mailPriorities->priority[$i]['color'];
            if (!empty($label) && defined($label) && constant($label) != NULL) {
                $label = constant($label);
            }
            $_SESSION['mail_priorities'][$i] = $label;
            $_SESSION['mail_priorities_attribute'][$i] = $attribute;
            $_SESSION['mail_priorities_wdays'][$i] = ($workingDays != 'false' ? 'true' : 'false');
            $_SESSION['mail_priorities_color'][$i] = $color;
        }
    }

    protected static function  checkPriorities($array) {
        $errors = [];
        if (empty($array))
            //return false;;
        foreach ($array as $value) {
            if (empty($value['label']) || empty($value['number']) || empty($value['wdays'])) {
                array_push($errors,'Valeur vide');
                //return false;
            } elseif ($value['wdays'] != 'true' && $value['wdays'] != 'false') {
                array_push($errors,'Valeur wdays invalide');
                //return false;
            } elseif ($value['number'] === '*') {
            } elseif (!ctype_digit($value['number'])) {
                array_push($errors,'Valeur non numérique');
                //return false;
            } elseif ((int)$value['number'] < 0) {
                //return false;
            }
        }
        return true;
    }

    /*public function updatePriorities(){
        $priorities = [];
       
        for ($i = 0; isset($_REQUEST[('label_' . $i)]) && isset($_REQUEST[('priority_' . $i)]) && isset($_REQUEST[('working_' . $i)]) && isset($_REQUEST[('color_' . $i)]); $i++) {
            $priorities[] = ['label' => $_REQUEST[('label_' . $i)],
             'number' => $_REQUEST[('priority_' . $i)],
             'wdays' => $_REQUEST[('working_' . $i)],
             'color' => $_REQUEST[('color_' . $i)]];
        }
        for ($i = 0; !empty($_REQUEST[('label_new' . $i)]) && !empty($_REQUEST[('priority_new' . $i)]) && !empty($_REQUEST[('working_new' . $i)]) && !empty($_REQUEST[('color_new' . $i)]); $i++) {
            $priorities[] = ['add' => 'add', 'label' => $_REQUEST[('label_new' . $i)], 'number' => $_REQUEST[('priority_new' . $i)], 'wdays' => $_REQUEST[('working_new' . $i)], 'color' => $_REQUEST[('color_new' . $i)]];
        }
        if ($this->checkPriorities($priorities)) {
            self::setXML($priorities);
            self::updateSession();
            $_SESSION['info'] = _PRIORITIES_UPDATED;
        } else {
            $_SESSION['error'] = _PRIORITIES_ERROR;
        }
    }*/

    public function updatePriorities(array $aArgs = []){
        if(self::checkPriorities($aArgs)){
            var_dump('Checked');
            self::setXML($aArgs);
            self::updateSession();
            return $aArgs;
        } else {
            var_dump('Errors');
            return 'errors';
        }
        

    }

    /*public function updatePriorities(array $aArgs = []){
        
    }*/
    
    /*public static function getById(array $aArgs){
        static::checkRequired($aArgs, ['id']);
        static::String($aArgs,['id']);

        $path = self::getXMLPath();

            $xmlfile = simplexml_load_file($path);
            $mailPriorities = $xmlfile->priorities;
            unset($mailPriorities->priority[$indexToDelete]);
            $res = $xmlfile->asXML();
    }*/

    public function deletePriority(array $aArgs = []) {
        //if (isset($_REQUEST['indexToDelete']) && ctype_digit($_REQUEST['indexToDelete'])) {
            //var_dump('totototototottotototototo');
            /*var_dump($indexToDelete);
            exit;*/
        static::checkRequired($aArgs, ['id']);
        static::checkString($aArgs,['id']);
        //$indexToDelete = (int) $_REQUEST['indexToDelete'];
        $indexToDelete=(int)$aArgs['id'];
        $rq= static::select([
            'select'    => 'priority',
            'table'     => 'res_letterbox',
            'where'     => ['priority = ?'],
            'data'      => [$indexToDelete]
        ]);
        //$db = new Database();

        /*$stmt = $db->query("SELECT priority FROM res_letterbox WHERE priority = ? ",
            [$indexToDelete]
        );*/

        //if ($stmt->rowCount() == 0) {
        if(count($rq)==0){
            $path = self::getXMLPath();

            $xmlfile = simplexml_load_file($path);
            $mailPriorities = $xmlfile->priorities;
            unset($mailPriorities->priority[$indexToDelete]);
            $res = $xmlfile->asXML();
            $fp = @fopen($path, "w+");
            if ($fp) {
                fwrite($fp,$res);                
            }
            self::updateSession();
            return "OK";
        } else {
            $_SESSION['error'] = _PRIORITIES_ERROR_TAKEN;
            return "ERROR";
        }
        /*static::checkRequired($aArgs, ['id']);
        static::checkString($aArgs,['id']);

        $aReturn = static::deleteFrom([
            'table' => 'priorities',
            'where' => ['id = ?'],
            'data'  => [$aArgs['id']]
        ]);
        return $aReturn;*/
    }

    
    
}

