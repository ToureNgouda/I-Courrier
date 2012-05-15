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
*
*
* @file
* @author Loic Vinet <dev@maarch.org>
* @date $date$
* @version $Revision$
* @ingroup admin
*/


try{
    require_once 'core/class/ObjectControlerAbstract.php';
    require_once 'core/class/ObjectControlerIF.php';
    require_once 'core/class/class_request.php' ;
   	require_once 'modules/tags/class/TagControler.php' ;
} catch (Exception $e) {
    echo $e->getMessage();
}

include_once 'modules/tags/route.php';
$tag = new tag_controler;

$tag_resid_return = array();
$json_txt .= " 'tags_chosen' : [";
//$tags_chosen_tmp = array();
for ($getag_i = 0; $getag_i <count($_REQUEST['tags_chosen']); $getag_i++) {
	$return_tags_res_id = array();
	$return_tags_res_id  = $tag->getresarray_byLabel($_REQUEST['tags_chosen'][$getag_i], 'letterbox_coll');
	//array_push($tags_chosen_tmp, $func->protect_string_db($_REQUEST['tags_chosen'][$getag_i]));
	$json_txt .= "'".$_REQUEST['tags_chosen'][$getag_i]."',";
	if ($return_tags_res_id)
	{
		foreach ($return_tags_res_id as $elem){
			array_push($tag_resid_return, $elem);
		}
	}
	else
	{
		array_push($tag_resid_return, 0);
	}
	
}

foreach ($tag_resid_return as $finaltagsearch) {
	
	$tag_resid_in .= "'".$finaltagsearch."',";
}
$tag_resid_in = substr($tag_resid_in, 0, -1);
$where_request .= " res_id in (".$tag_resid_in.") and ";

$json_txt = substr($json_txt, 0, -1);
$json_txt .= '],';

                

   
?>
