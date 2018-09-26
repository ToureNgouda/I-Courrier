<?php
/*
*    Copyright 2008,2014 Maarch
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
* Module : Tags
* 
* This module is used to store ressources with any keywords
* V: 1.0
*
* @file
* @author Loic Vinet
* @date $date$
* @version $Revision$
*/

try{
    require_once 'core/class/ObjectControlerAbstract.php';
    require_once 'core/class/ObjectControlerIF.php';
    require_once 'core/class/class_request.php' ;
    require_once 'modules/tags/class/TagControler.php' ;
} catch (Exception $e) {
    functions::xecho($e->getMessage());
}

include_once 'modules/tags/route.php';
$tag = new tag_controler;

if ($coll_id == '') {
    $targetColl = 'letterbox';
} else {
    $targetColl = $coll_id;
}
$tag_resid_return = array();
$json_txt .= " 'tags_chosen' : [";
$json_txt .= "'".implode("','", $_REQUEST['tags_chosen'])."'";
$return_tags_res_id = array();
foreach ($_REQUEST['tags_chosen'] as $tagId) {
    $result  = $tag->getresarray_byId($tagId);
    $return_tags_res_id = array_merge($return_tags_res_id,$result);
}
$return_tags_res_id = "'".implode("','", $return_tags_res_id)."'";

if($return_tags_res_id == "''"){
    $return_tags_res_id = "0";
}

$where_request .= " res_id in (".$return_tags_res_id.") and ";
//$arrayPDO = array_merge($arrayPDO, array(":tags" => $return_tags_res_id));
$json_txt .= '],';