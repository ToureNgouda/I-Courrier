<?php

require_once __DIR__. DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Transfer.php';
require_once __DIR__. DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'RequestSeda.php';

class AdapterWS
{
    private $db;
    public function __construct()
    {
        $this->db = new RequestSeda();
    }

    public function send($messageObject, $messageId, $type)
    {
        $transfer = new Transfer();

        $message = $this->db->getMessageByIdentifier($messageId);
        $res = $transfer->transfer('maarchcourrier', $message->reference, $type);

        if ($res['status'] == 1) {
            $this->db->updateStatusMessage($message->reference, 'E');
            return $res;
        }

        $this->db->updateStatusMessage($message->reference, 'S');
    }
}
