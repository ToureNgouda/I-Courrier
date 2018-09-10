<?php

/**
 * Copyright Maarch since 2008 under licence GPLv3.
 * See LICENCE.txt file at the root folder for more details.
 * This file is part of Maarch software.
 *
 */

/**
 * @brief List Instance Controller
 * @author dev@maarch.org
 */

namespace Entity\controllers;

use Entity\models\ListInstanceModel;
use Slim\Http\Request;
use Slim\Http\Response;

class ListInstanceController
{
    public function getById(Request $request, Response $response, array $aArgs)
    {
        $listinstance = ListInstanceModel::getById(['id' => $aArgs['id']]);

        return $response->withJson($listinstance);
    }
}
