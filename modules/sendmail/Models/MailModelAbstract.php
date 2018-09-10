<?php

/**
* Copyright Maarch since 2008 under licence GPLv3.
* See LICENCE.txt file at the root folder for more details.
* This file is part of Maarch software.
*
*/

/**
* @brief Mail Model
* @author dev@maarch.org
* @ingroup sendmail
*/

namespace Sendmail\Models;

class MailModelAbstract
{
    public static function createMail($data)
    {
        try {
            \SrcCore\models\DatabaseModel::insert([
                'table'         => 'sendmail',
                'columnsValues' => [
                    'coll_id'                => $data->coll_id,
                    'res_id'                 => $data->res_id,
                    'user_id'                => $data->user_id,
                    'to_list'                => $data->to_list,
                    'cc_list'                => $data->cc_list,
                    'cci_list'               => $data->cci_list,
                    'email_object'           => $data->email_object,
                    'email_body'             => $data->email_body,
                    'is_res_master_attached' => $data->is_res_master_attached,
                    'email_status'           => $data->email_status,
                    'creation_date'          => $data->creation_date,
                    'sender_email'           => $data->sender_email,
                    'message_exchange_id'    => $data->message_exchange_id,
                ]
            ]);
        } catch (Exception $e) {
            return false;
        }
    }
}
