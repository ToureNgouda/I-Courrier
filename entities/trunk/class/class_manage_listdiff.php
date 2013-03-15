<?php
/*
*    Copyright 2008,2009 Maarch
*
*  This file is part of Maarch Framework.
*
*   Maarch Framework is free software: you can redistribute it and/or modify
*   it under the terms of the GNU General Public License as published by
*   the Free Software Foundation, either version 3 of the License, or
*   (at your option) any later version.
*
*   Maarch Framework is distributed in the hope that it will be useful,
*   but WITHOUT ANY WARRANTY; without even the implied warranty of
*   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*   GNU General Public License for more details.
*
*   You should have received a copy of the GNU General Public License
*    along with Maarch Framework.  If not, see <http://www.gnu.org/licenses/>.
*/


/**
* @brief   Contains all the functions to manage diffusion list
*
* @file
* @author Claire Figueras <dev@maarch.org>
* @date $date$
* @version $Revision$
* @ingroup entities
*/
require_once 'modules/entities/entities_tables.php';
require_once 'core/core_tables.php';
/**
* @brief   Contains all the functions to manage diffusion list
*
* <ul>
* <li>Loads diffusion list from an model linked to an entity</li>
* <li>Updates the listinstance or listmodel table in the database</li>
* <li>Gets a diffusion list for a given resource identifier</li>
*</ul>
*
* @ingroup entities
*/
class diffusion_list extends dbquery
{
    public function select_listmodels(
        $objectType='entity_id'
    ) {
        $listmodels = array();
        $this->connect();
        
        $query = 
            "SELECT distinct object_type, object_id, description"
            . " FROM " . ENT_LISTMODELS
            . " WHERE object_type = '".$objectType."'" 
            . " GROUP BY object_type, object_id, description " 
            . " ORDER BY object_type ASC, object_id ASC";

        $this->query($query);
        
        while ($listmodel = $this->fetch_array()) {
            if($listmodel['description'] == '')
                $listmodel['description'] = $listmodel['object_id'];
                
            $listmodels[] = $listmodel;
        }
        
        return $listmodels;
    }
    
    public function select_listmodel(
        $objectType='entity_id',
        $objectId
    ) {
        $listmodel = array();
        $this->connect();
        
        $query = 
            "SELECT distinct object_type, object_id, description"
            . " FROM " . ENT_LISTMODELS
            . " WHERE object_type = '".$objectType."'" 
                . "and object_id = '" . $objectId . "'"
            . " GROUP BY object_type, object_id, description";

        $this->query($query);
        
        $listmodel = $this->fetch_array();
        
        return $listmodel;
    }
    
    /**
    * Gets the diffusion list model for a given entity
    *
    * @param string $entityId Entity identifier
    * @param array $collId Collection identifier ('letterbox_coll' by default)
    * @return array $listmodel['dest] : Data of the dest_user
    *                                ['user_id'] : identifier of the dest_user
    *                                ['lastname'] : Lastname of the dest_user
    *                                ['firstname'] : firstname of the dest_user
    *                                ['entity_id'] : entity identifier of the dest_user
    *                                ['entity_label'] : entity label of the dest_user
    *                         ['copy'] : Data of the copies
    *                                ['users'][$i] : Users in copy data
    *                                       ['user_id'] : identifier of the user in copy
    *                                       ['lastname'] : Lastname of the user in copy
    *                                       ['firstname'] : firstname of the user in copy
    *                                       ['entity_id'] : entity identifier of the user in copy
    *                                       ['entity_label'] : entity label of the user in copy
    *                                ['entities'][$i] : Entities in copy data
    *                                       ['entity_id'] : entity identifier of the entity in copy
    *                                       ['entity_label'] : entity label of the entity in copy
    */
    public function get_listmodel(
        $objectType='entity_id', 
        $objectId
    ) {
        $objectId = $this->protect_string_db($objectId);
        $objectType = $this->protect_string_db($objectType);
        
        $this->connect();
        $roles = $this->get_workflow_roles();
        $listmodel = array();
        
        if (empty($objectId) || empty($objectType)) 
            return $listmodel;
        
        # Dest user
        $this->query(
            "SELECT l.item_id, u.firstname, u.lastname, e.entity_id, e.entity_label, l.visible "
            . " FROM " . ENT_LISTMODELS . " l "
                . " JOIN " . USERS_TABLE . " u ON l.item_id = u.user_id " 
                . " JOIN " . ENT_USERS_ENTITIES . " ue ON u.user_id = ue.user_id " 
                . " JOIN " . ENT_ENTITIES . " e ON ue.entity_id = e.entity_id"
            . " WHERE "
                . "ue.primary_entity = 'Y' "
                . "and l.item_mode = 'dest' "
                . "and l.item_type = 'user_id' " 
                . "and l.object_type = '". $objectType ."' "
                . "and l.object_id = '" . $objectId . "'"
        );

        $res = $this->fetch_object();

        if ($this->nb_result() > 0 && isset($res)) {
            $listmodel['dest'] = array();
            $listmodel['dest']['user_id'] = $this->show_str($res->item_id);
            $listmodel['dest']['lastname'] = $this->show_str($res->lastname);
            $listmodel['dest']['firstname'] = $this->show_str($res->firstname);
            $listmodel['dest']['entity_id'] = $this->show_str($res->entity_id);
            $listmodel['dest']['entity_label'] = $this->show_str($res->entity_label);
            $listmodel['dest']['visible'] = $this->show_str($res->visible);
        }
        
        # Users in copy and other roles
        foreach($roles as $role_id => $role_label) {
            if($role_id == 'copy')
                $item_mode = 'cc';
            else 
                $item_mode = $role_id;
            
            # Users
            $this->query(
                "SELECT l.item_id, l.item_mode, u.firstname, u.lastname, e.entity_id, e.entity_label, l.visible "
                . " FROM " . ENT_LISTMODELS . " l "
                    . " JOIN " . USERS_TABLE . " u ON l.item_id = u.user_id " 
                    . " JOIN " . ENT_USERS_ENTITIES . " ue ON u.user_id = ue.user_id " 
                    . " JOIN " . ENT_ENTITIES . " e ON ue.entity_id = e.entity_id"
                . " WHERE "
                    . "ue.primary_entity = 'Y' "
                    . "and l.item_mode = '".$item_mode."' "
                    . "and l.item_type = 'user_id' " 
                    . "and l.object_type = '". $objectType ."' "
                    . "and l.object_id = '" . $objectId . "'"
                . "ORDER BY l.sequence"
            );

            while ($user = $this->fetch_object()) {
                if(!isset($listmodel[$role_id]))
                    $listmodel[$role_id] = array();
                if(!isset($listmodel[$role_id]['users']))
                    $listmodel[$role_id]['users'] = array();
                                
                array_push(
                    $listmodel[$role_id]['users'],
                    array(
                        'user_id' => $this->show_string($user->item_id),
                        'lastname' => $this->show_string($user->lastname),
                        'firstname' => $this->show_string($user->firstname),
                        'entity_id' => $this->show_string($user->entity_id),
                        'entity_label' => $this->show_string($user->entity_label),
                        'visible' => $user->visible
                    )
                );
            }
            
            # Entities
            $this->query(
                "SELECT l.item_id, e.entity_label, l.item_mode, l.visible "
                . "FROM " . ENT_LISTMODELS . " l "
                    . "JOIN " . ENT_ENTITIES . " e ON l.item_id = e.entity_id "
                . "WHERE "
                    . " l.item_mode = '".$item_mode."' "
                    . "and l.item_type = 'entity_id' "
                    . "and l.object_type = '" . $objectType . "' "
                    . "and l.object_id = '" . $objectId . "' "
                . "ORDER BY l.sequence "
            );

            while ($entity = $this->fetch_object()) {
                if(!isset($listmodel[$role_id]))
                    $listmodel[$role_id] = array();
                if(!isset($listmodel[$role_id]['entities']))
                    $listmodel[$role_id]['entities'] = array();
                    
                array_push(
                    $listmodel[$role_id]['entities'],
                    array(
                        'entity_id' => $this->show_string($entity->item_id),
                        'entity_label' => $this->show_string($entity->entity_label),
                        'visible' => $entity->visible
                    )
                );
            }
        }
        return $listmodel;
    }
    
    public function save_listmodel(
        $diffList, 
        $objectType = 'entity_id',
        $objectId,
        $description = false
    ) {
        $this->connect();
        $roles = $this->get_workflow_roles();
        
        require_once 'core/class/class_history.php';
        $hist = new history();
        
        $objectType = $this->protect_string_db(trim($objectType));
        $objectId = $this->protect_string_db(trim($objectId));
        $description = $this->protect_string_db(trim($description));
        
        # Delete all and replace full list
        #**********************************************************************
        $this->query(
            "delete from " . ENT_LISTMODELS 
            . " where "
                . "object_type = '" . $objectType . "' "
                . "and object_id = '" . $objectId . "' "
        );
        # Dest user
        #**********************************************************************
        if($dest_user_id = $this->protect_string_db(trim($diffList['dest']['user_id'])))
            $this->query(
                "insert into " . ENT_LISTMODELS
                    . " (coll_id, object_id, object_type, sequence, item_id, item_type, item_mode, listmodel_type, description, visible ) "
                . " values ("
                    . "'any', "
                    . "'" . $objectId . "' , " 
                    . "'" . $objectType . "', "
                    . "0, "
                    . "'" . $dest_user_id . "', "
                    . "'user_id', "
                    . "'dest', "
                    . "null, "
                    . "'" . $description . "',"
                    . "'" . $diffList['dest']['visible'] . "'"
                .")"
            );
                   
        # Roles
        #**********************************************************************
        foreach($roles as $role_id => $role_label) {
            if($role_id == 'copy')
                $item_mode = 'cc';
            else 
                $item_mode = $role_id;
            
            # users
            #**********************************************************************
            for ($i=0, $l=count($diffList[$role_id]['users']);
                $i<$l; 
                $i++
            ) {
                $user = $diffList[$role_id]['users'][$i];
                $this->query(
                    "insert into " . ENT_LISTMODELS
                        . " (coll_id, object_id, object_type, sequence, item_id, item_type, item_mode, listmodel_type, description, visible ) "
                    . " values ("
                        . "'any', "
                        . "'" . $objectId . "' , " 
                        . "'" . $objectType . "', "
                        . $i . ", "
                        . "'" . $user['user_id'] . "', "
                        . "'user_id', "
                        . "'".$item_mode."', "
                        . "null, "
                        . "'" . $description . "',"
                        . "'" . $user['visible']. "'"
                    . ")"
                );
            }
            # Entities
            #**********************************************************************
            for ($i=0, $l=count($diffList[$role_id]['entities']); $i<$l ; $i++) {
                $entity = $diffList[$role_id]['entities'][$i];
                $this->query(
                    "insert into " . ENT_LISTMODELS
                        . " (coll_id, object_id, object_type, sequence, item_id, item_type, item_mode, listmodel_type, description, visible ) "
                    . " values ("
                        . "'any', "
                        . "'" . $objectId . "' , " 
                        . "'" . $objectType . "', "
                        . $i . ", "
                        . "'" . $entity['entity_id'] . "', "
                        . "'entity_id', "
                        . "'".$item_mode."', "
                        . "null, "
                        . "'" . $description . "', "
                        . "'" . $entity['visible'] . "'"
                    . ")"
                );
            }
        }
    }
    
    public function delete_listmodel(
        $objectType = 'entity_id',
        $objectId
    ) {
        $this->connect();
       
        $objectType = $this->protect_string_db(trim($objectType));
        $objectId = $this->protect_string_db(trim($objectId));

        # Delete all and replace full list
        #**********************************************************************
        $this->query(
            "delete from " . ENT_LISTMODELS 
            . " where "
                . "object_type = '" . $objectType . "' "
                . "and object_id = '" . $objectId . "' "
        );
    
    }
    
    /**
    * Loads a diffusion list into database (listinstance or listmodel table)
    *
    * @param array $diffList['dest] : Data of the dest_user
    *                                ['user_id'] : identifier of the dest_user
    *                                ['lastname'] : Lastname of the dest_user
    *                                ['firstname'] : firstname of the dest_user
    *                                ['entity_id'] : entity identifier of the dest_user
    *                                ['entity_label'] : entity label of the dest_user
    *                         ['copy'] : Data of the copies
    *                                ['users'][$i] : Users in copy data
    *                                       ['user_id'] : identifier of the user in copy
    *                                       ['lastname'] : Lastname of the user in copy
    *                                       ['firstname'] : firstname of the user in copy
    *                                       ['entity_id'] : entity identifier of the user in copy
    *                                       ['entity_label'] : entity label of the user in copy
    *                                ['entities'][$i] : Entities in copy data
    *                                       ['entity_id'] : entity identifier of the entity in copy
    *                                       ['entity_label'] : entity label of the entity in copy
    * @param array $params['mode'] : 'listmodel' or 'listinstance' (mandatory)
    *                     ['table'] : table to update (mandatory)
    *                     ['object_id'] : Object identifier linked to the diffusion list, entity identifier
    *                               (mandatory if mode =  'listmodel')
    *                     ['coll_id'] : Collection identifier (mandatory if mode = 'listinstance')
    *                     ['res_id'] : Resource identifier (mandatory if mode = 'listinstance')
    *                     ['user_id'] : User identifier of the person who add an item in the list
    *                     ['concat_list'] : True or false (can be set only in 'listinstance' mode )
    * @param string $listType List type, 'DOC' by default
    * @param string $objectType Object type, 'entity_id' by default
    **/
    function load_list_db($diffList, $params, $listType = 'DOC', $objectType = 'entity_id')
    {
        $this->connect();

        require_once 'core/class/class_history.php';
        $hist = new history();
        
        $coll_id = $this->protect_string_db(trim($params['coll_id']));
        $objectType = $this->protect_string_db(trim($objectType));
        $objectId = $this->protect_string_db(trim($params['object_id']));
        
        if (! isset($params['concat_list'])) 
            $concat = false;
        else 
            $concat = $params['concat_list'];

        if (! isset($params['only_cc'])) 
            $onlyCc = false;
        else 
            $onlyCc = $params['only_cc'];
        
        if (isset($params['user_id']) && ! empty($params['user_id'])) {
            require_once 'modules/entities/class/class_manage_entities.php';
            $ent = new entity();
            $creatorUser = $this->protect_string_db(trim($params['user_id']));
            $primary = $ent->get_primary_entity($creatorUser);
            $creatorEntity = $this->protect_string_db(trim($primary['ID']));
        } else {
            $creatorUser = '';
            $creatorEntity = '';
        }
        
        # DEST USER (only if not in onlyCC & dest changed)
        # *****************************************************************
        if (isset($diffList['dest']['user_id'])
            && !empty($diffList['dest']['user_id'])
            && !$onlyCc
            && $diffList['dest']['user_id'] != $oldDiffList['dest']['user_id']
        ) {
            $this->query(
                "delete from " . $params['table'] 
                . " where coll_id = '" . $coll_id . "'"
                    . " and res_id = " . $params['res_id']
                    . " and item_mode = 'dest'"
            );
            $user_id = $this->protect_string_db(trim($diffList['dest']['user_id']));
            $viewed = (integer)$diffList['dest']['viewed'];
            $this->query(
                "insert into " . $params['table'] 
                    . " (coll_id, res_id, listinstance_type, sequence, item_id, item_type, item_mode, added_by_user, added_by_entity, visible, viewed) "
                . "values ("
                    . "'" . $coll_id . "', " 
                    . $params['res_id'] . ", "
                    . "null, "
                    . "0, "
                    . "'" . $user_id . "', "
                    . "'user_id' ,"
                    . "'dest', "
                    . "'" . $creatorUser . "', "
                    . "'" . $creatorEntity . "', "
                    . "'Y', "
                    . $viewed 
                . " )"
            );
            
            $listinstance_id = $this->last_insert_id('listinstance_id_seq');
            $hist->add(
                $params['table'],
                $listinstance_id,
                'ADD',
                'diffdestuser',
                'Diffusion of document '.$params['res_id'] . ' to ' . $user_id,
                $_SESSION['config']['databasetype'],
                'apps'
            );
            
        } # End of dest user
                   
        # LISTINSTANCE ROLES
        #**********************************************************************************
        $this->query(
            "delete from " . $params['table'] 
            . " where coll_id = '" . $coll_id . "'"
                . " and res_id = " . $params['res_id']
                . " and item_mode != 'dest'"
        );
        $roles = $this->get_workflow_roles();
        foreach($roles as $role_id => $role_label) {
            if($role_id == 'copy')
                $item_mode = 'cc';
            else 
                $item_mode = $role_id;
            
            # CUSTOM USER ROLES IN NEW LIST
            for ($i=0, $l=count($diffList[$role_id]['users']);
                $i<$l;
                $i++
            ) {
                $user_id = $this->protect_string_db(trim($diffList[$role_id]['users'][$i]['user_id']));
                $visible = $diffList[$role_id]['users'][$i]['visible'];
                $viewed = (integer)$diffList[$role_id]['users'][$i]['viewed'];
                $this->query(
                    "insert into " . $params['table'] 
                        . " (coll_id, res_id, listinstance_type, sequence, item_id, item_type, item_mode, added_by_user, added_by_entity, visible, viewed) "
                    . "values ("
                        . "'" . $coll_id . "', "
                        . $params['res_id'] . ", "
                        . "null, "
                        . $i . ", "
                        . "'" . $user_id . "', " 
                        . "'user_id' , "
                        . "'".$item_mode."', "
                        . "'" . $creatorUser . "', "
                        . "'" . $creatorEntity. "', "
                        . "'" . $visible . "', "
                        . $viewed
                    . " )"
                );
                
                # History
                $listinstance_id = $this->last_insert_id('listinstance_id_seq');      
                $hist->add(
                    $params['table'],
                    $listinstance_id,
                    'ADD',
                    'diff'.$role_id.'user',
                    'Diffusion of document '.$params['res_id'].' to '. $user_id . ' as ' . $role_id,
                    $_SESSION['config']['databasetype'],
                    'apps'
                ); 

                
            } # End of foreach role users
            
            # CUSTOM ENTITY ROLES
            for ($i=0, $l=count($diffList[$role_id]['entities']);
                $i<$l;
                $i++
            ) {
                $entity_id = $this->protect_string_db(trim($diffList[$role_id]['entities'][$i]['entity_id']));
                $visible = $diffList[$role_id]['entities'][$i]['visible'];
                $viewed = (integer)$diffList[$role_id]['entities'][$i]['viewed'];
                
                $this->query(
                    "insert into " . $params['table'] 
                        . " (coll_id, res_id, listinstance_type, sequence, item_id, item_type, item_mode, added_by_user, added_by_entity, visible, viewed) "
                    . "values ("
                        . "'" . $coll_id . "', "
                        . $params['res_id'] . ", "
                        . "null, "
                        . $i . ", "
                        . "'" . $entity_id . "', " 
                        . "'entity_id' , "
                        . "'".$item_mode."', "
                        . "'" . $creatorUser . "', "
                        . "'" . $creatorEntity . "', "
                        . "'" . $visible . "',"
                        . $viewed
                    . " )"
                );
                
                # History
                $listinstance_id = $this->last_insert_id('listinstance_id_seq');      
                $hist->add(
                    $params['table'],
                    $listinstance_id,
                    'ADD',
                    'diff'.$role_id.'user',
                    'Diffusion of document '.$params['res_id'].' to '. $entity_id . ' as ' . $role_id,
                    $_SESSION['config']['databasetype'],
                    'apps'
                ); 

            } # End of foreach role entities
        } # End of foreach roles
    }
    
    /**
    * Gets a diffusion list for a given resource identifier
    *
    * @param string $resId Resource identifier
    * @param string $collId Collection identifier, 'letterbox_coll' by default
    * @return array $listinstance['dest] : Data of the dest_user
    *                                ['user_id'] : identifier of the dest_user
    *                                ['lastname'] : Lastname of the dest_user
    *                                ['firstname'] : firstname of the dest_user
    *                                ['entity_id'] : entity identifier of the dest_user
    *                                ['entity_label'] : entity label of the dest_user
    *                         ['copy'] : Data of the copies
    *                                ['users'][$i] : Users in copy data
    *                                       ['user_id'] : identifier of the user in copy
    *                                       ['lastname'] : Lastname of the user in copy
    *                                       ['firstname'] : firstname of the user in copy
    *                                       ['entity_id'] : entity identifier of the user in copy
    *                                       ['entity_label'] : entity label of the user in copy
    *                                ['entities'][$i] : Entities in copy data
    *                                       ['entity_id'] : entity identifier of the entity in copy
    *                                       ['entity_label'] : entity label of the entity in copy
    **/
    public function get_listinstance($resId, $modeCc = false, $collId = 'letterbox_coll')
    {
        $this->connect();
        $roles = $this->get_workflow_roles();
        
        $listinstance = array();
        $listinstance['dest'] = array();
        
        foreach($roles as $role_id => $role_label) {
            $listinstance[$role_id] = array();
            $listinstance[$role_id]['users'] = array();
            $listinstance[$role_id]['entities'] = array();
        }
        
        
        if (empty($resId) || empty($collId)) {
            return $listinstance;
        }

        $this->connect();
        # DEST USER
        if (! $modeCc) {
            $this->query(
                "select l.item_id, u.firstname, u.lastname, e.entity_id, "
                . "e.entity_label, l.visible, l.viewed from " . ENT_LISTINSTANCE . " l, "
                . USERS_TABLE . " u, " . ENT_ENTITIES . " e, "
                . ENT_USERS_ENTITIES . " ue where l.coll_id = '"
                . $this->protect_string_db(trim($collId))
                . "' and l.item_mode = 'dest' "
                . "and l.item_type = 'user_id' and l.sequence = 0 "
                . "and l.item_id = u.user_id and u.user_id = ue.user_id "
                . "and e.entity_id = ue.entity_id and ue.primary_entity = 'Y' "
                . "and l.res_id = " . $resId
            );

            $res = $this->fetch_object();
           
            $listinstance['dest'] = array(
                'user_id' => $this->show_string($res->item_id),
                'lastname' => $this->show_string($res->lastname),
                'firstname' => $this->show_string($res->firstname),
                'entity_id' => $this->show_string($res->entity_id),
                'entity_label' => $this->show_string($res->entity_label),
                'visible' => $this->show_string($res->visible),
                'viewed' => $this->show_string($res->viewed)
            );
        }
        
        # OTHER ROLES USERS
        #**********************************************************************
        $this->query(
            "select l.item_id, u.firstname, u.lastname, e.entity_id, "
            . "e.entity_label, l.visible, l.viewed, l.item_mode from "
            . ENT_LISTINSTANCE . " l, " . USERS_TABLE
            . " u, " . ENT_ENTITIES . " e, " . ENT_USERS_ENTITIES
            . " ue where l.coll_id = '" . $collId
            . "' and l.item_mode != 'dest' "
            . "and l.item_type = 'user_id' and l.item_id = u.user_id "
            . "and l.item_id = ue.user_id and ue.user_id=u.user_id "
            . "and e.entity_id = ue.entity_id and l.res_id = " . $resId
            . " and ue.primary_entity = 'Y' order by l.sequence "
        );
        //$this->show();
        while ($res = $this->fetch_object()) {
            if($res->item_mode == 'cc') 
                $role_id = 'copy';
            else 
                $role_id = $res->item_mode;
                
            if(!isset($listinstance[$role_id]['users']))
                $listinstance[$role_id]['users'] = array();
            array_push(
                $listinstance[$role_id]['users'],
                array(
                    'user_id' => $this->show_string($res->item_id),
                    'lastname' => $this->show_string($res->lastname),
                    'firstname' => $this->show_string($res->firstname),
                    'entity_id' => $this->show_string($res->entity_id),
                    'entity_label' => $this->show_string($res->entity_label),
                    'visible' => $this->show_string($res->visible),
                    'viewed' => $this->show_string($res->viewed)
                )
            );
        }

        # OTHER ROLES ENTITIES
        #**********************************************************************
        $this->query(
            "select l.item_id,  e.entity_label, l.visible, l.viewed, l.item_mode from " . ENT_LISTINSTANCE
            . " l, " . ENT_ENTITIES . " e where l.coll_id =  '" . $collId . "' "
            . "and l.item_mode != 'dest' "
            . "and l.item_type = 'entity_id' and l.item_id = e.entity_id "
            . "and l.res_id = " . $resId . " order by l.sequence "
        );

        while ($res = $this->fetch_object()) {
            if($res->item_mode == 'cc') 
                $role_id = 'copy';
            else 
                $role_id = $res->item_mode;
                
            if(!isset($listinstance[$role_id]['entities']))
                $listinstance[$role_id]['entities'] = array();
            array_push(
                $listinstance[$role_id]['entities'],
                array(
                    'entity_id' => $this->show_string($res->item_id),
                    'entity_label' => $this->show_string($res->entity_label),
                    'visible' => $this->show_string($res->visible),
                    'viewed' => $this->show_string($res->viewed)
                )
            );
        }
        
        return $listinstance;
    }

    public function set_main_dest(
        $dest, 
        $collId, 
        $resId,
        $listinstanceType = 'DOC', 
        $itemType = 'user_id', 
        $viewed
    ) {
        $this->connect();
        $this->query(
            "select item_id from " . ENT_LISTINSTANCE . " where res_id = "
            . $resId." and coll_id = '" . $this->protect_string_db($collId)
            . "' and sequence = 0 and item_type = '"
            . $this->protect_string_db($itemType) . "' and item_mode = 'dest'"
        );
        if ($this->nb_result() == 1) {
            $this->query(
                "update " . ENT_LISTINSTANCE . " set item_id = '"
                . $this->protect_string_db($dest) . "', viewed = " . $viewed
                . " where res_id = " . $resId . " and coll_id = '"
                . $this->protect_string_db($collId)
                . "' and sequence = 0 and item_type = '"
                . $this->protect_string_db($itemType)
                . "' and item_mode = 'dest'"
            );
        } else {
            $this->query(
                "insert into " . ENT_LISTINSTANCE . " (coll_id, res_id, "
                . "item_id, item_type, item_mode, sequence, "
                . "added_by_user, added_by_entity, viewed) values ('"
                . $this->protect_string_db($collId) . "', " . $resId . ", '"
                . $this->protect_string_db($dest) . "', '"
                . $this->protect_string_db($itemType) . "', 'dest', 0, '"
                . $_SESSION['user']['UserId'] . "','"
                . $_SESSION['primaryentity']['id'] . "', " . $viewed . ");"
            );
        }
    }
    
    #  Get list of available roles for list models and diffusion lists definition
    public function get_workflow_roles(
        $user_id = false
    ) {
        $roles = array();
        
        $roles['copy'] = _TO_CC;
        
        require_once 'core' . DIRECTORY_SEPARATOR . 'core_tables.php';
        
        $query = 
            "SELECT distinct ug.group_id, ug.group_desc FROM " . USERGROUPS_TABLE . " ug "
            . " LEFT JOIN " . USERGROUPS_SERVICES_TABLE . " ugs "
                . " ON ug.group_id = ugs.group_id "
            . " LEFT JOIN " . USERGROUP_CONTENT_TABLE . " ugc "
                . " ON ug.group_id = ugc.group_id "
            . " WHERE ug.enabled = 'Y' and ugs.service_id = 'diffusion_list_role'";
        
        if($user_id)
            $query .= " AND ugc.user_id = '".$user_id."'";
        
        $query .= " GROUP BY ug.group_id, ug.group_desc";
        $query .= " ORDER BY ug.group_id ASC";
        
        $this->connect();
        $this->query($query);
        
        while($usergroup = $this->fetch_object()) {
            $group_id = $usergroup->group_id;
            $roles[$group_id] = $usergroup->group_desc;
        }
        
        # Default list : copy/cc
        /*$roles['copy'] = 
            array(
                'role_mode' => 'cc',
                'list_label' => _TO_CC,
                'role_label' => _TO_CC,
                'workflow_mode' => 'collaborative',
                'list_img' => 'manage_entities_b_small.gif&module=entities',
                'allow_entities' => true
            );
        
        while ($role = $this->fetch_object()) { 
            if ($role->allow_entities == 'Y') $ent = true;
            else $ent = false;
            $roles[(string) $role->role_id] = 
                array(
                    'role_mode' => (string)$role->role_id,
                    'list_label' => (string) $role->list_label,
                    'role_label' => (string) $role->role_label,
                    'workflow_mode' => (string) $role->workflow_mode,
                    'list_img' => (string) $role->list_img,
                    'allow_entities' => $ent
                );
        }*/
        return $roles;
    }
    
    #  Get list of available list model types
    public function get_listmodel_types()
    {
        $this->connect();
        $this->query('select * from ' . ENT_LISTMODEL_TYPES);
        
        $types = array();
        
        $types['entity_id'] = _ENTITY;
        $types['type_id'] = _DOCTYPE;
        $types['foldertype_id'] = _FOLDER;
                
        while ($type = $this->fetch_object()) { 
            $types[(string) $type->listmodel_type_id] = $type->listmodel_type_label;
        }
        return $types;
    }

    #  Get list of available list model types for a given groupbasket
    public function list_groupbasket_listmodel_types(
        $group_id,
        $basket_id,
        $action_id
    ) {
        $types = array();
        $this->connect();
        $this->query(
            "select listmodel_type_id from " . ENT_GROUPBASKET_LISTMODEL_TYPES
            . " where group_id = '".$group_id."'" 
                . " and basket_id = '".$basket_id."'"
                . " and action_id = ".$action_id
        );
        
        $types = array();
                
        while ($type = $this->fetch_object()) { 
            $types[] = (string) $type->listmodel_type_id;
        }
        return $types;
    }

    
}
