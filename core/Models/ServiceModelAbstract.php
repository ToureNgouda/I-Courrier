<?php

/**
* Copyright Maarch since 2008 under licence GPLv3.
* See LICENCE.txt file at the root folder for more details.
* This file is part of Maarch software.
*
*/

/**
* @brief Service Model
* @author dev@maarch.org
* @ingroup core
*/

namespace Core\Models;

class ServiceModelAbstract
{
    public static function getApplicationAdministrationServicesByXML()
    {
        $xmlfile = ServiceModel::getLoadedXml(['location' => 'apps']);
        $applicationServices = [];

        if ($xmlfile) {
            foreach ($xmlfile->SERVICE as $value) {
                if ((string)$value->servicetype == 'admin' && (string)$value->enabled === 'true') {
                    $name = defined((string) $value->name) ? constant((string) $value->name) : (string) $value->name;
                    $comment = defined((string) $value->comment) ? constant((string) $value->comment) : (string) $value->comment;
                    $applicationServices[] = [
                        'name'          => $name,
                        'comment'       => $comment,
                        'servicepage'   => (string)$value->servicepage,
                        'style'         => (string)$value->style,
                        'angular'       => empty((string)$value->angular) ? 'false' : (string)$value->angular
                    ];
                }
            }
        }

        return $applicationServices;
    }

    public static function getApplicationAdministrationServicesByUserServices(array $aArgs = [])
    {
        ValidatorModel::notEmpty($aArgs, ['userServices']);
        ValidatorModel::arrayType($aArgs, ['userServices']);

        $xmlfile = ServiceModel::getLoadedXml(['location' => 'apps']);
        $applicationServices = [];

        if ($xmlfile) {
            foreach ($xmlfile->SERVICE as $value) {
                if ((string)$value->servicetype == 'admin' && (string)$value->enabled === 'true' && ((string)$value->system_service == 'true' || in_array((string)$value->id, $aArgs['userServices']))) {
                    $name = defined((string)$value->name) ? constant((string)$value->name) : (string)$value->name;
                    $comment = defined((string)$value->comment) ? constant((string)$value->comment) : (string)$value->comment;
                    $applicationServices[] = [
                        'name' => $name,
                        'comment' => $comment,
                        'servicepage' => (string)$value->servicepage,
                        'style' => (string)$value->style,
                        'angular' => empty((string)$value->angular) ? 'false' : (string)$value->angular
                    ];
                }
            }
        }

        return $applicationServices;
    }

    public static function getModulesAdministrationServicesByXML()
    {
        $customId = CoreConfigModel::getCustomId();

        if (file_exists("custom/{$customId}/apps/maarch_entreprise/xml/config.xml")) {
            $path = "custom/{$customId}/apps/maarch_entreprise/xml/config.xml";
        } else {
            $path = 'apps/maarch_entreprise/xml/config.xml';
        }

        $modulesServices = [];

        $xmlfile = simplexml_load_file($path);
        foreach ($xmlfile->MODULES as $mod) {
            $module = (string)$mod->moduleid;
            $xmlModuleFile = ServiceModel::getLoadedXml(['location' => $module]);

            if ($xmlModuleFile) {
                foreach ($xmlModuleFile->SERVICE as $value) {
                    if ((string)$value->servicetype == 'admin' && (string)$value->enabled === 'true') {
                        $name = defined((string)$value->name) ? constant((string)$value->name) : (string)$value->name;
                        $comment = defined((string)$value->comment) ? constant((string)$value->comment) : (string)$value->comment;
                        $modulesServices[] = [
                            'name' => $name,
                            'comment' => $comment,
                            'servicepage' => (string)$value->servicepage,
                            'style' => (string)$value->style,
                            'angular' => empty((string)$value->angular) ? 'false' : (string)$value->angular
                        ];
                    }
                }
            }
        }

        return $modulesServices;
    }

    public static function getModulesAdministrationServicesByUserServices(array $aArgs = [])
    {
        ValidatorModel::notEmpty($aArgs, ['userServices']);
        ValidatorModel::arrayType($aArgs, ['userServices']);

        $customId = CoreConfigModel::getCustomId();

        if (file_exists("custom/{$customId}/apps/maarch_entreprise/xml/config.xml")) {
            $path = "custom/{$customId}/apps/maarch_entreprise/xml/config.xml";
        } else {
            $path = 'apps/maarch_entreprise/xml/config.xml';
        }

        $modulesServices = [];

        $xmlfile = simplexml_load_file($path);
        foreach ($xmlfile->MODULES as $mod) {
            $module = (string)$mod->moduleid;
            $xmlModuleFile = ServiceModel::getLoadedXml(['location' => $module]);

            if ($xmlModuleFile) {
                foreach ($xmlModuleFile->SERVICE as $value) {
                    if ((string)$value->servicetype == 'admin' && (string)$value->enabled === 'true' && ((string)$value->system_service == 'true' || in_array((string)$value->id, $aArgs['userServices']))) {
                        $name = defined((string)$value->name) ? constant((string)$value->name) : (string)$value->name;
                        $comment = defined((string)$value->comment) ? constant((string)$value->comment) : (string)$value->comment;
                        $modulesServices[] = [
                            'name' => $name,
                            'comment' => $comment,
                            'servicepage' => (string)$value->servicepage,
                            'style' => (string)$value->style,
                            'angular' => empty((string)$value->angular) ? 'false' : (string)$value->angular
                        ];
                    }
                }
            }
        }

        return $modulesServices;
    }

    public static function getAdministrationServicesByUserId(array $aArgs = [])
    {
        ValidatorModel::notEmpty($aArgs, ['userId']);
        ValidatorModel::stringType($aArgs, ['userId']);

        $rawServicesStoredInDB = UserModel::getServicesById(['userId' => $aArgs['userId']]);
        $servicesStoredInDB = [];
        foreach ($rawServicesStoredInDB as $value) {
            $servicesStoredInDB[] = $value['service_id'];
        }

        $administration = [];
        $administration['application'] = ServiceModel::getApplicationAdministrationServicesByUserServices(['userServices' => $servicesStoredInDB]);
        $administration['modules'] = ServiceModel::getModulesAdministrationServicesByUserServices(['userServices' => $servicesStoredInDB]);

        return $administration;
    }

    public static function hasService(array $aArgs = [])
    {
        ValidatorModel::notEmpty($aArgs, ['id', 'userId', 'location', 'type']);
        ValidatorModel::stringType($aArgs, ['id', 'userId', 'location', 'type']);

        if ($aArgs['userId'] == 'superadmin') {
            return true;
        }
        $rawServicesStoredInDB = UserModel::getServicesById(['userId' => $aArgs['userId']]);
        $servicesStoredInDB = [];
        foreach ($rawServicesStoredInDB as $value) {
            $servicesStoredInDB[] = $value['service_id'];
        }

        $xmlfile = ServiceModel::getLoadedXml(['location' => $aArgs['location']]);

        if ($xmlfile) {
            foreach ($xmlfile->SERVICE as $value) {
                if ((string)$value->servicetype == $aArgs['type'] && (string)$value->id == $aArgs['id'] && (string)$value->enabled === 'true'
                    && ((string)$value->system_service == 'true' || in_array((string)$value->id, $servicesStoredInDB))) {
                    return true;
                }
            }
        }

        return false;
    }

    protected static function getLoadedXml(array $aArgs = [])
    {
        ValidatorModel::notEmpty($aArgs, ['location']);
        ValidatorModel::stringType($aArgs, ['location']);

        $customId = CoreConfigModel::getCustomId();

        if ($aArgs['location'] == 'apps') {
            if (file_exists("custom/{$customId}/apps/maarch_entreprise/xml/services.xml")) {
                $path = "custom/{$customId}/apps/maarch_entreprise/xml/services.xml";
            } else {
                $path = 'apps/maarch_entreprise/xml/services.xml';
            }
        } else {
            if (file_exists("custom/{$customId}/modules/{$aArgs['location']}/xml/services.xml")) {
                $path = "custom/{$customId}/modules/{$aArgs['location']}/xml/services.xml";
            } else {
                $path = "modules/{$aArgs['location']}/xml/services.xml";
            }
        }

        if (!file_exists($path)) {
            return false;
        }

        $loadedXml = simplexml_load_file($path);

        return $loadedXml;
    }
}
