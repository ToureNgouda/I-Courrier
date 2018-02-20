<?php

/**
* Copyright Maarch since 2008 under licence GPLv3.
* See LICENCE.txt file at the root folder for more details.
* This file is part of Maarch software.
*
*/

/**
* @brief Notifications Schedule Controller
* @author dev@maarch.org
* @ingroup notifications
*/

namespace Notification\controllers;

use Respect\Validation\Validator;
use Notification\models\NotificationModel;
use Notification\models\NotificationScheduleModel;
use Core\Models\ServiceModel;
use Slim\Http\Request;
use Slim\Http\Response;
use SrcCore\models\CoreConfigModel;

class NotificationScheduleController
{
    public function get(Request $request, Response $response)
    {
        if (!ServiceModel::hasService(['id' => 'admin_notif', 'userId' => $GLOBALS['userId'], 'location' => 'notifications', 'type' => 'admin'])) {
            return $response->withStatus(403)->withJson(['errors' => 'Service forbidden']);
        }

        return $response->withJson([
            'crontab'                => NotificationScheduleModel::getCrontab(),
            'authorizedNotification' => NotificationScheduleController::getAuthorizedNotifications(),
        ]);
    }
    
    // Save Crontab
    public function create(Request $request, Response $response)
    {
        if (!ServiceModel::hasService(['id' => 'admin_notif', 'userId' => $GLOBALS['userId'], 'location' => 'notifications', 'type' => 'admin'])) {
            return $response->withStatus(403)->withJson(['errors' => 'Service forbidden']);
        }

        $data = $request->getParams();
        if (!NotificationScheduleController::checkCrontab($data)) {
            return $response->withStatus(500)->withJson(['errors' => 'Problem with crontab']);
        }

        foreach ($data as $id => $cronValue) {
            foreach ($cronValue as $key => $value) {
                if (!Validator::notEmpty()->validate($value)) {
                    $errors[] = $key." is empty";
                }
                if ($key != "cmd" && $key != "state" && !Validator::intVal()->validate($value) && $value != "*") {
                    $errors[] = "wrong format for ".$key;
                }
            }
        }
        if (!empty($errors)) {
            return $response->withStatus(500)->withJson(['errors' => $errors]);
        }

        NotificationScheduleModel::saveCrontab(["crontab" => $data]);

        return $response->withJson(true);
    }

    protected static function getAuthorizedNotifications()
    {
        $aNotification      = NotificationModel::getEnableNotifications(['select' => ['notification_sid', 'description']]);
        $notificationsArray = array();
        $customId           = CoreConfigModel::getCustomId();
        $corePath = str_replace("custom/".$customId."/src/app/notification/controllers", "", __DIR__);
        $corePath = str_replace("src/app/notification/controllers", "", $corePath);

        foreach ($aNotification as $result) {
            $filename = "notification";
            if (isset($customId) && $customId<>"") {
                $filename.="_".str_replace(" ", "", $customId);
            }
            $filename.="_".$result['notification_sid'].".sh";

            if ($customId <> '') {
                $pathToFolow = $corePath . 'custom/'.$customId . '/';
            } else {
                $pathToFolow = $corePath;
            }

            $path = $pathToFolow.'modules/notifications/batch/scripts/'.$filename;

            if (file_exists($path)) {
                $notificationsArray[$path] = $result['description'];
            }
        }
        
        return $notificationsArray;
    }

    protected static function checkCrontab($crontabToSave)
    {
        $customId          = CoreConfigModel::getCustomId();
        $crontabBeforeSave = NotificationScheduleModel::getCrontab();
        $corePath = str_replace("custom/".$customId."/src/app/notification/controllers", "", __DIR__);
        $corePath = str_replace("src/app/notification/controllers", "", $corePath);
        foreach ($crontabToSave as $id => $cronValue) {
            if ($cronValue['state'] != "hidden" && $crontabBeforeSave[$id]['state'] == "hidden") {
                $returnValue = false;
                break;
            } elseif ($cronValue['state'] == "hidden" && $crontabBeforeSave[$id]['state'] != "hidden") {
                $returnValue = false;
                break;
            } elseif ($cronValue['state'] == "new" || $cronValue['state'] == "normal") {
                if ($customId <> '') {
                    $pathToFolow = $corePath . 'custom/'.$customId . '/';
                } else {
                    $pathToFolow = $corePath;
                }
                $returnValue = true;
                if (strpos($crontabToSave[$id]['cmd'], $pathToFolow.'modules/notifications/batch/scripts/') !== 0) {
                    $returnValue = false;
                    break;
                }
            } else {
                $returnValue = true;
            }
        }

        return $returnValue;
    }

    public function createScriptNotification(Request $request, Response $response)
    {
        if (!ServiceModel::hasService(['id' => 'admin_notif', 'userId' => $GLOBALS['userId'], 'location' => 'notifications', 'type' => 'admin'])) {
            return $response->withStatus(403)->withJson(['errors' => 'Service forbidden']);
        }

        $errors = [];
        $data = $request->getParams();
        if (!Validator::intVal()->validate($data['notification_sid'])) {
            $errors[] = 'notification_sid is not a numeric';
        }
        if (!Validator::notEmpty()->validate($data['notification_sid']) ||
            !Validator::notEmpty()->validate($data['notification_id'])) {
            $errors[] = 'one of arguments is empty';
        }

        if (!empty($errors)) {
            return $response
                ->withStatus(500)
                ->withJson(['errors' => $errors]);
        }

        $notification_sid = $data['notification_sid'];
        $notification_id  = $data['notification_id'];

        NotificationScheduleModel::createScriptNotification(['notification_sid' => $notification_sid, 'notification_id' => $notification_id]);

        return $response->withJson(true);
    }
}