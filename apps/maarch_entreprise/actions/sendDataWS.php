<?php

$confirm = true;

$etapes = ['send'];

function manage_send($aId)
{
    $result = '';

    foreach ($aId as $resId) {
        $document = \Resource\models\ResModel::getById(['resId' => $aId[0], 'select' => ['custom_t1']]);

        $bodyParams = [
            'custom_t1' => $document['custom_t1'],
        ];
        \SrcCore\models\CurlModel::exec(['curlCallId' => 'sendData', 'bodyData' => $bodyParams]);

        $result .= $resId . '#';
    }

    return ['result' => $result, 'history_msg' => ''];
}
