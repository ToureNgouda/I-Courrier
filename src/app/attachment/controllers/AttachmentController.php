<?php

/**
* Copyright Maarch since 2008 under licence GPLv3.
* See LICENCE.txt file at the root folder for more details.
* This file is part of Maarch software.
*
*/

/**
* @brief Attachment Controller
* @author dev@maarch.org
*/

namespace Attachment\controllers;

use Attachment\models\AttachmentModel;
use Slim\Http\Request;
use Slim\Http\Response;

class AttachmentController
{
    public function setInSignatureBook(Request $request, Response $response, array $aArgs)
    {
        //TODO Controle de droit de modification de cet attachment

        $data = $request->getParams();

        $attachment = AttachmentModel::getById(['id' => $aArgs['id'], 'isVersion' => $data['isVersion']]);

        if (empty($attachment)) {
            return $response->withStatus(400)->withJson(['errors' => 'Attachment not found']);
        }

        AttachmentModel::setInSignatureBook(['id' => $aArgs['id'], 'isVersion' => $data['isVersion'], 'inSignatureBook' => !$attachment['in_signature_book']]);

        return $response->withJson(['success' => 'success']);
    }
}
