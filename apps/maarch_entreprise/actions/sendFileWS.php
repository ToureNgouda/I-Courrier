<?php

$confirm = true;

$etapes = ['send'];

function manage_send($aId)
{
    $result = '';

    foreach ($aId as $resId) {
        $document = \Resource\models\ResModel::getById(['resId' => $resId, 'select' => ['res_id', 'format', 'path', 'filename']]);
        $docserver = \Docserver\models\DocserverModel::getByCollId(['collId' => 'letterbox_coll', 'priority' => true, 'select' => ['path_template']]);

        $file = file_get_contents($docserver['path_template'] . str_replace('#', '/', $document['path']) . $document['filename']);
        $encodedFile = base64_encode($file);

        $bodyParams = [
            'resId'         => $document['res_id'],
            'encodedFile'   => $encodedFile,
            'fileFormat'    => $document['format']
        ];
        $response = \SrcCore\models\CurlModel::exec(['curlCallId' => 'sendFile', 'bodyData' => $bodyParams]);

        if (!empty($response['publikId'])) {
            \Resource\models\ResModel::update(['set' => ['custom_t1' => $response['publikId']], 'where' => ['res_id = ?'], 'data' => [$document['res_id']]]);
        }
        $result .= $resId . '#';
    }

    return ['result' => $result, 'history_msg' => ''];
}
