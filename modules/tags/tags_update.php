<?php
/*
*    Copyright 2008,2012 Maarch
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

if ($resId) 
{
	$ressource_tagmodule = $resId;
}
elseif($arr_id[0])
{
	$ressource_tagmodule = $arr_id[0];
}
elseif($s_id)
{
	$ressource_tagmodule = $s_id;
}

if ($collId){
	$collection_tagmodule = $collId;
}
elseif($coll_id){
	$collection_tagmodule = $coll_id;
}

include_once 'modules/tags/route.php';

if (empty($tags_list))
{
    $tags_list = array();
}

$tags = new tag_controler();	
$tagmodule_result = $tags->associateTagToRes($ressource_tagmodule,$collection_tagmodule, $tags_list);
unset($_SESSION['tagsuser']);

   
?>
