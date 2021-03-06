<?php

/**
 * Copyright Maarch since 2008 under licence GPLv3.
 * See LICENCE.txt file at the root folder for more details.
 * This file is part of Maarch software.
 */

/**
 * @brief Service Model Abstract
 * @author dev@maarch.org
 */

namespace Group\models;

use SrcCore\models\CoreConfigModel;
use SrcCore\models\DatabaseModel;
use SrcCore\models\ValidatorModel;

class ServiceModelAbstract
{
    public static function getServicesByXML()
    {
        $xmlfile = ServiceModel::getLoadedXml(['location' => 'apps']);
        $services = [];

        if ($xmlfile) {
            foreach ($xmlfile->SERVICE as $value) {
                if ((string) $value->enabled === 'true') {
                    $name = defined((string) $value->name) ? constant((string) $value->name) : (string) $value->name;
                    $comment = defined((string) $value->comment) ? constant((string) $value->comment) : (string) $value->comment;
                    $services['application'][] = [
                        'id' => (string) $value->id,
                        'name' => $name,
                        'comment' => $comment,
                        'servicepage' => (string) $value->servicepage,
                        'style' => (string) $value->style,
                        'system_service' => (string) $value->system_service == 'true' ? true : false,
                        'servicetype' => (string) $value->servicetype,
                    ];
                }
            }
        }

        $loadedXml = CoreConfigModel::getXmlLoaded(['path' => 'apps/maarch_entreprise/xml/config.xml']);

        foreach ($loadedXml->MODULES as $mod) {
            $module = (string) $mod->moduleid;
            $xmlModuleFile = ServiceModel::getLoadedXml(['location' => $module]);

            if ($xmlModuleFile) {
                foreach ($xmlModuleFile->SERVICE as $value) {
                    if ((string) $value->enabled === 'true') {
                        $name = defined((string) $value->name) ? constant((string) $value->name) : (string) $value->name;
                        $comment = defined((string) $value->comment) ? constant((string) $value->comment) : (string) $value->comment;
                        $services[$module][] = [
                            'id' => (string) $value->id,
                            'name' => $name,
                            'comment' => $comment,
                            'servicepage' => (string) $value->servicepage,
                            'style' => (string) $value->style,
                            'system_service' => (string) $value->system_service == 'true' ? true : false,
                            'servicetype' => (string) $value->servicetype,
                        ];
                    }
                }
            }
        }

        return $services;
    }

    public static function getApplicationAdministrationServicesByXML()
    {
        $xmlfile = ServiceModel::getLoadedXml(['location' => 'apps']);
        $applicationServices = [];

        if ($xmlfile) {
            foreach ($xmlfile->SERVICE as $value) {
                if ((string) $value->servicetype == 'admin' && (string) $value->enabled === 'true') {
                    $category = (string) $value->category;
                    $name = defined((string) $value->name) ? constant((string) $value->name) : (string) $value->name;
                    $comment = defined((string) $value->comment) ? constant((string) $value->comment) : (string) $value->comment;
                    $applicationServices[$category][] = [
                        'name' => $name,
                        'comment' => $comment,
                        'servicepage' => (string) $value->servicepage,
                        'style' => (string) $value->style,
                        'angular' => empty((string) $value->angular) ? 'false' : (string) $value->angular,
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
                if ((string) $value->servicetype == 'admin' && (string) $value->enabled === 'true' && ((string) $value->system_service == 'true' || in_array((string) $value->id, $aArgs['userServices']))) {
                    $category = (string) $value->category;
                    $name = defined((string) $value->name) ? constant((string) $value->name) : (string) $value->name;
                    $comment = defined((string) $value->comment) ? constant((string) $value->comment) : (string) $value->comment;
                    $applicationServices[$category][] = [
                        'name' => $name,
                        'comment' => $comment,
                        'servicepage' => (string) $value->servicepage,
                        'style' => (string) $value->style,
                        'angular' => empty((string) $value->angular) ? 'false' : (string) $value->angular,
                    ];
                }
            }
        }

        return $applicationServices;
    }

    public static function getModulesAdministrationServicesByXML()
    {
        $modulesServices = [];

        $loadedXml = CoreConfigModel::getXmlLoaded(['path' => 'apps/maarch_entreprise/xml/config.xml']);
        foreach ($loadedXml->MODULES as $mod) {
            $module = (string) $mod->moduleid;
            $xmlModuleFile = ServiceModel::getLoadedXml(['location' => $module]);

            if ($xmlModuleFile) {
                foreach ($xmlModuleFile->SERVICE as $value) {
                    if ((string) $value->servicetype == 'admin' && (string) $value->enabled === 'true') {
                        $category = (string) $value->category;
                        $name = defined((string) $value->name) ? constant((string) $value->name) : (string) $value->name;
                        $comment = defined((string) $value->comment) ? constant((string) $value->comment) : (string) $value->comment;
                        $modulesServices[$category][] = [
                            'name' => $name,
                            'comment' => $comment,
                            'servicepage' => (string) $value->servicepage,
                            'style' => (string) $value->style,
                            'angular' => empty((string) $value->angular) ? 'false' : (string) $value->angular,
                        ];
                    }
                }
            }
        }

        return $modulesServices;
    }

    public static function getModulesAdministrationServicesByUserServices(array $aArgs)
    {
        ValidatorModel::notEmpty($aArgs, ['userServices']);
        ValidatorModel::arrayType($aArgs, ['userServices']);

        $modulesServices = [];

        $loadedXml = CoreConfigModel::getXmlLoaded(['path' => 'apps/maarch_entreprise/xml/config.xml']);
        foreach ($loadedXml->MODULES as $mod) {
            $module = (string) $mod->moduleid;
            $xmlModuleFile = ServiceModel::getLoadedXml(['location' => $module]);

            if ($xmlModuleFile) {
                foreach ($xmlModuleFile->SERVICE as $value) {
                    if ((string) $value->servicetype == 'admin' && (string) $value->enabled === 'true' && ((string) $value->system_service == 'true' || in_array((string) $value->id, $aArgs['userServices']))) {
                        $category = (string) $value->category;
                        $name = defined((string) $value->name) ? constant((string) $value->name) : (string) $value->name;
                        $comment = defined((string) $value->comment) ? constant((string) $value->comment) : (string) $value->comment;
                        $modulesServices[$category][] = [
                            'name' => $name,
                            'comment' => $comment,
                            'servicepage' => (string) $value->servicepage,
                            'style' => (string) $value->style,
                            'angular' => empty((string) $value->angular) ? 'false' : (string) $value->angular,
                        ];
                    }
                }
            }
        }

        return $modulesServices;
    }

    public static function getApplicationAdministrationMenuByXML()
    {
        $modulesServices = [];

        $loadedXml = CoreConfigModel::getXmlLoaded(['path' => 'apps/maarch_entreprise/xml/menu.xml']);
        foreach ($loadedXml->MENU as $value) {
            $label = defined((string) $value->libconst) ? constant((string) $value->libconst) : (string) $value->libconst;

            $modulesServices['menuList'][] = [
                'id' => (string) $value->id,
                'label' => $label,
                'link' => (string) $value->url,
                'icon' => (string) $value->style,
                'angular' => empty((string) $value->angular) ? 'false' : (string) $value->angular,
            ];
        }

        $customId = CoreConfigModel::getCustomId();
        if (file_exists("custom/{$customId}/apps/maarch_entreprise/xml/config.xml")) {
            $path = "custom/{$customId}/apps/maarch_entreprise/xml/config.xml";
        } else {
            $path = 'apps/maarch_entreprise/xml/config.xml';
        }
        $xmlfile = simplexml_load_file($path);
        $modulesServices['applicationName'] = $xmlfile->CONFIG->applicationname;
        foreach ($xmlfile->MODULES as $mod) {
            $path = false;
            $module = (string) $mod->moduleid;

            if (file_exists("custom/{$customId}/modules/{$module}/xml/menu.xml")) {
                $path = "custom/{$customId}/modules/{$module}/xml/menu.xml";
            } elseif (file_exists("modules/{$module}/xml/menu.xml")) {
                $path = "modules/{$module}/xml/menu.xml";
            }
            if ($path) {
                $xmlfile = simplexml_load_file($path);
                foreach ($xmlfile->MENU as $value) {
                    $label = defined((string) $value->libconst) ? constant((string) $value->libconst) : (string) $value->libconst;

                    $modulesServices['menuList'][] = [
                        'id'        => (string) $value->id,
                        'label'     => $label,
                        'link'      => (string) $value->url,
                        'icon'      => (string) $value->style,
                        'angular'   => empty((string) $value->angular) ? 'false' : (string) $value->angular,
                    ];
                }
            }
        }

        return $modulesServices;
    }

    public static function getApplicationAdministrationMenuByUserServices(array $aArgs)
    {
        ValidatorModel::notEmpty($aArgs, ['userServices']);
        ValidatorModel::arrayType($aArgs, ['userServices']);

        $customId = CoreConfigModel::getCustomId();

        if (file_exists("custom/{$customId}/apps/maarch_entreprise/xml/menu.xml")) {
            $path = "custom/{$customId}/apps/maarch_entreprise/xml/menu.xml";
        } else {
            $path = 'apps/maarch_entreprise/xml/menu.xml';
        }

        $modulesServices = [];

        $xmlfile = simplexml_load_file($path);
        foreach ($xmlfile->MENU as $value) {
            if (in_array((string) $value->id, $aArgs['userServices'])) {
                $label = defined((string) $value->libconst) ? constant((string) $value->libconst) : (string) $value->libconst;

                $modulesServices['menuList'][] = [
                    'id' => (string) $value->id,
                    'label' => $label,
                    'link' => (string) $value->url,
                    'icon' => (string) $value->style,
                    'angular' => empty((string) $value->angular) ? 'false' : (string) $value->angular,
                ];
            }
        }
        if (file_exists("custom/{$customId}/apps/maarch_entreprise/xml/config.xml")) {
            $path = "custom/{$customId}/apps/maarch_entreprise/xml/config.xml";
        } else {
            $path = 'apps/maarch_entreprise/xml/config.xml';
        }
        $xmlfile = simplexml_load_file($path);
        $modulesServices['applicationName'] = $xmlfile->CONFIG->applicationname;
        foreach ($xmlfile->MODULES as $mod) {
            $path = false;
            $module = (string) $mod->moduleid;

            if (file_exists("custom/{$customId}/modules/{$module}/xml/menu.xml")) {
                $path = "custom/{$customId}/modules/{$module}/xml/menu.xml";
            } elseif (file_exists("modules/{$module}/xml/menu.xml")) {
                $path = "modules/{$module}/xml/menu.xml";
            }
            if ($path) {
                $xmlfile = simplexml_load_file($path);
                foreach ($xmlfile->MENU as $value) {
                    if (in_array((string) $value->id, $aArgs['userServices'])) {
                        $label = defined((string) $value->libconst) ? constant((string) $value->libconst) : (string) $value->libconst;

                        $modulesServices['menuList'][] = [
                            'id' => (string) $value->id,
                            'label' => $label,
                            'link' => (string) $value->url,
                            'icon' => (string) $value->style,
                            'angular' => empty((string) $value->angular) ? 'false' : (string) $value->angular,
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

        $rawServicesStoredInDB = ServiceModel::getByUserId(['userId' => $aArgs['userId']]);
        $servicesStoredInDB = [];
        foreach ($rawServicesStoredInDB as $value) {
            $servicesStoredInDB[] = $value['service_id'];
        }

        $administration = [];
        $administrationMenu = ServiceModel::getApplicationAdministrationMenuByUserServices(['userServices' => $servicesStoredInDB]);
        $administrationApplication = ServiceModel::getApplicationAdministrationServicesByUserServices(['userServices' => $servicesStoredInDB]);
        $administrationModule = ServiceModel::getModulesAdministrationServicesByUserServices(['userServices' => $servicesStoredInDB]);

        $administration['administrations'] = array_merge_recursive($administrationApplication, $administrationModule);
        $administration = array_merge_recursive($administration, $administrationMenu);

        return $administration;
    }

    public static function getByUserId(array $aArgs)
    {
        ValidatorModel::notEmpty($aArgs, ['userId']);
        ValidatorModel::stringType($aArgs, ['userId']);

        $aServices = DatabaseModel::select([
            'select'    => ['usergroups_services.service_id'],
            'table'     => ['usergroup_content, usergroups_services'],
            'where'     => ['usergroup_content.group_id = usergroups_services.group_id', 'usergroup_content.user_id = ?'],
            'data'      => [$aArgs['userId']]
        ]);

        return $aServices;
    }

    public static function hasService(array $aArgs)
    {
        ValidatorModel::notEmpty($aArgs, ['id', 'userId', 'location', 'type']);
        ValidatorModel::stringType($aArgs, ['id', 'userId', 'location', 'type']);

        if ($aArgs['userId'] == 'superadmin') {
            return true;
        }
        $rawServicesStoredInDB = ServiceModel::getByUserId(['userId' => $aArgs['userId']]);
        $servicesStoredInDB = [];
        foreach ($rawServicesStoredInDB as $value) {
            $servicesStoredInDB[] = $value['service_id'];
        }

        $xmlfile = ServiceModel::getLoadedXml(['location' => $aArgs['location']]);

        if ($xmlfile) {
            foreach ($xmlfile->SERVICE as $value) {
                if ((string) $value->servicetype == $aArgs['type'] && (string) $value->id == $aArgs['id'] && (string) $value->enabled === 'true'
                    && ((string) $value->system_service == 'true' || in_array((string) $value->id, $servicesStoredInDB))) {
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
