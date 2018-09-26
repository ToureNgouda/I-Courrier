<?php
/*
*    Copyright 2008,2017 Maarch
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
* File : aj_tag_fusion_tags.php
*
* Script called by an ajax object to replace a tag with an other
*
* @package  maarch
* @version 1
* @since 10/2016
* @license GPL v3
* @author Alex ORLUC  <dev@maarch.org>
*/

try{
    require_once 'core/class/ActionControler.php';
    require_once 'core/class/ObjectControlerAbstract.php';
    require_once 'core/class/ObjectControlerIF.php';
    require_once 'core/class/class_request.php' ;
    require_once 'modules/tags/class/TagControler.php' ;
    require_once 'modules/tags/tags_tables_definition.php';
} catch (Exception $e) {
    functions::xecho($e->getMessage());
}


$db = new Database();

$core = new core_tools();
$core->load_lang();
$tag = new tag_controler;

if(empty($_REQUEST['newTagId'])){
    echo "{status : 1, value : 'NO TAG'}";
    exit();
}
$tagIdBeforeFusion = $_REQUEST['tagIdBeforeFusion'];
$newTagId = $_REQUEST['newTagId'];

//check all res_id associate with newTagId
$stmt = $db->query(
	"SELECT res_id FROM tag_res"
	. " WHERE tag_id = ?"
	,array($newTagId));

while($tagRes = $stmt->fetchObject()){
    $stmt = $db->query(
                "DELETE FROM tag_res"
                . " WHERE res_id = ? and tag_id = ?"
        ,array($tagRes->res_id,$tagIdBeforeFusion));
}

//Clean restrictions
$stmt = $db->query(
        "DELETE FROM tags_entities"
        . " WHERE tag_id = ?"
,array($tagIdBeforeFusion));

//replace all res associate with tagIdBeforeFusion
$stmt = $db->query(
	"UPDATE tag_res SET tag_id = ?"
	. " WHERE tag_id = ?"
	,array($newTagId,$tagIdBeforeFusion));

//delete old tag
$tag->delete($tagIdBeforeFusion);

echo "{status : 0, value : 'ok'}";
exit();

?>
