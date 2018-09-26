<?php

/**
 * Copyright Maarch since 2008 under licence GPLv3.
 * See LICENCE.txt file at the root folder for more details.
 * This file is part of Maarch software.
 *
 */

/**
 * @brief Batch to convert
 *
 * @file
 * @author  Laurent Giovannoni  <dev@maarch.org>
 * @date $date$
 * @version $Revision$
 * @ingroup convert
 */

/**
 * *****   LIGHT PROBLEMS without an error semaphore
 *  101 : Configuration file missing
 *  102 : Configuration file does not exist
 *  103 : Error on loading config file
 *  104 : SQL Query Error
 *  105 : a parameter is missin
 *  106 : Maarch_CLITools is missing
 *  107 : Stack full for the policy and the cycle requested
 *  108 : Problem with the php include path
 *  109 : An instance of the batch for the required policy and cyle is already
 *        in progress
 *  110 : Problem with collection parameter
 *  111 : No resource found
 * ****   HEAVY PROBLEMS with an error semaphore
 *  11  : Cycle not found
 *  12  : Previous cycle not found
 *  13  : Error persists
 *  14  : Cycle step not found
 */

date_default_timezone_set('Europe/Paris');
// load the config and prepare to process
include('load_fill_stack.php');

//TODO ONLY FOR DEBUG
// $query = "truncate table convert_stack";
// $stmt = Bt_doQuery(
//     $GLOBALS['db'], 
//     $query
// );
// $query = "truncate table adr_letterbox";
// $stmt = Bt_doQuery(
//     $GLOBALS['db'], 
//     $query
// );
// $query = "update res_letterbox set convert_result = 0";
// $stmt = Bt_doQuery(
//     $GLOBALS['db'], 
//     $query
// );

/******************************************************************************/
/* beginning */
$state = 'CONTROL_STACK';
while ($state <> 'END') {
    if (isset($GLOBALS['logger'])) {
        $GLOBALS['logger']->write('STATE:' . $state, 'INFO');
    }
    switch ($state) {
        /**********************************************************************/
        /*                          CONTROL_STACK                             */
        /* Checking if the stack is full                                      */
        /**********************************************************************/
        case 'CONTROL_STACK' :
            //ONLY FOR TEST
            $query = "truncate table convert_stack";
            $stmt = Bt_doQuery(
                $GLOBALS['db'], 
                $query
            );

            $query = "select count(1) as cpt from convert_stack "
                   . " where coll_id = ? and regex = ?";
            $stmt = Bt_doQuery(
                $GLOBALS['db'], 
                $query, 
                array($GLOBALS['collection'], $GLOBALS['regExResId'])
            );
            $resultCpt = $stmt->fetchObject();
            if ($resultCpt->cpt > 0) {
                Bt_exitBatch(107, 'stack is full for collection:'
                             . $GLOBALS['collection'] . ', regex:'
                             . $GLOBALS['regExResId']
                             , 'WARNING');
                break;
            }
            $state = 'SELECT_RES';
            break;
        /**********************************************************************/
        /*                          SELECT_RES                                */
        /* Selects candidates                                                 */
        /**********************************************************************/
        case 'SELECT_RES' :
            $orderBy = ' order by res_id ';
            if ($GLOBALS['stackSizeLimit'] <> '') {
                $limit = ' LIMIT ' . $GLOBALS['stackSizeLimit'];
            }

            if ($GLOBALS['OnlyIndexes']) {
                $where_clause = " convert_result = '1' and ( (fulltext_result = '0' or fulltext_result = '' "
                    . "or fulltext_result is null) or (fulltext_result= '-1' and (convert_attempts < 3))) ";
                $where_clause .= $GLOBALS['creationDateClause'] 
                    . $GLOBALS['whereRegex'];
            } else {
                $where_clause = " (convert_result = '0' or convert_result = '' " 
                    . "or convert_result is null) or (convert_result= '-1' and (convert_attempts < 3)) "
                    . $GLOBALS['creationDateClause']
                    . $GLOBALS['whereRegex'];
            }
            
            $query = $GLOBALS['db']->limit_select(
                0, 
                $GLOBALS['stackSizeLimit'], 
                'res_id', 
                $GLOBALS['table'], 
                $where_clause, 
                $orderBy
            );
            $stmt = Bt_doQuery($GLOBALS['db'], $query);
            $resourcesArray = array();

            while ($resoucesRecordset = $stmt->fetchObject()) {
                array_push(
                    $resourcesArray,
                        array('res_id' => $resoucesRecordset->res_id)
                );
            }

            if (count($resourcesArray) == 0) {
                if ($GLOBALS['creationDateClause'] <> '') {
                    $GLOBALS['logger']->write('No resource found for collection', 'INFO');
                    // test if we have to change the current date
                    if ($GLOBALS['currentMonthOnly'] == 'false') {
                        if ($GLOBALS['OnlyIndexes']) {
                            $queryTestDate = " convert_result = '1' and ( (fulltext_result = '0' or fulltext_result = '' "
                                    . "or fulltext_result is null) or (fulltext_result= '-1' and (fulltext_attempts < 3))) ";
                            $queryTestDate .= $GLOBALS['creationDateClause'];
                        } else {
                            $queryTestDate = " select count(res_id) as totalres from " 
                                . $GLOBALS['table'] . " (convert_result = '0' or convert_result = '' " 
                                . "or convert_result is null) or (convert_result= '-1' and (convert_attempts < 3)) "
                                . $GLOBALS['creationDateClause'];
                        }
                        $stmt = Bt_doQuery(
                            $GLOBALS['db'], 
                            $queryTestDate
                        );
                        $resultTotal = $stmt->fetchObject();
                        if ($resultTotal->totalres == 0) {
                            Bt_computeNextMonthCurrentDate();
                            Bt_computeCreationDateClause();
                            Bt_updateCurrentDateToProcess();
                            if ($GLOBALS['OnlyIndexes']) {
                                $where_clause = " convert_result = '1' and ( (fulltext_result = '0' or fulltext_result = '' "
                                    . "or fulltext_result is null) or (fulltext_result= '-1' and (fulltext_attempts < 3))) ";
                                $where_clause .= $GLOBALS['creationDateClause']
                                    . $GLOBALS['whereRegex'];
                            } else {
                                $where_clause = " (convert_result = '0' or convert_result = '' " 
                                    . "or convert_result is null) or (convert_result= '-1' and (convert_attempts < 3)) "
                                    . $GLOBALS['creationDateClause']
                                    . $GLOBALS['whereRegex'];
                            }

                            $query = $GLOBALS['db']->limit_select(
                                0, 
                                $GLOBALS['stackSizeLimit'], 
                                'res_id', 
                                $GLOBALS['table'], 
                                $where_clause, 
                                $orderBy
                            );
                            $stmt = Bt_doQuery(
                                $GLOBALS['db'], 
                                $query
                            );
                            $resourcesArray = array();
                            while ($resoucesRecordset = $stmt->fetchObject()) {
                                array_push(
                                    $resourcesArray,
                                        array('res_id' => $resoucesRecordset->res_id)
                                    );
                            }
                            if (count($resourcesArray) == 0) {
                                $GLOBALS['logger']->write('No resource found for collection', 'INFO');
                            }
                        }
                    }
                } else {
                    $GLOBALS['logger']->write('No resource found for collection', 'INFO');
                }
            }
            $state = 'FILL_STACK';
            break;
        /**********************************************************************/
        /*                          FILL_STACK                                */
        /* Fill the stack of candidates                                       */
        /**********************************************************************/
        case 'FILL_STACK' :
            for ($cptRes = 0;$cptRes < count($resourcesArray);$cptRes++) {
                $query = "insert into convert_stack"
                       . " (coll_id, res_id, status, work_batch, regex) "
                       . "values (?, ?, 'I', ?, ?)";
                $stmt = Bt_doQuery(
                    $GLOBALS['db'], 
                    $query, 
                    array(
                        $GLOBALS['collection'],
                        $resourcesArray[$cptRes]["res_id"],
                        $GLOBALS['wb'],
                        $GLOBALS['regExResId']
                    )
                );
                //history
                $query = "insert into " . HISTORY_TABLE
                       . " (table_name, record_id, event_type, user_id, "
                       . "event_date, info, id_module) values (?, ?, 'ADD', 'CONVERT_BOT', "
                       . $GLOBALS['db']->current_datetime()
                       . ", ?, 'convert')";
                $stmt = Bt_doQuery(
                    $GLOBALS['db'], 
                    $query,
                    array(
                        $GLOBALS['table'],
                        $resourcesArray[$cptRes]["res_id"],
                        "convert fill stack for collection:" . $GLOBALS['collection']
                    )
                );
                $GLOBALS['totalProcessedResources']++;
            }
            $state = 'END';
            break;
    }
}
$GLOBALS['logger']->write('End of process fill stack', 'INFO');
include('process_stack.php');
exit($GLOBALS['exitCode']);
