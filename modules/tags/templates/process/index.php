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

include_once 'modules/tags/route.php';
include_once 'modules/tags/templates/init.php';

if (!$core_tools)
{
    $core_tools = new core_tools();
}


$tags = new tag_controler();
$tags->load_sessiontag($res_id,$coll_id);   

$frm_str .= '<tr>';
$frm_str .= '<td colspan="2">'. _TAGS . '</td>';
$frm_str .= '</tr>';

$tag_customsize = '200px';
$tag_customcols = '26';

$frm_str .='<tr><td colspan="2">';
if ($core->test_service('add_tag_to_res', 'tags',false) == 1)
{
	$modify_keyword = true;
}
include_once 'modules/tags/templates/addtag_userform.php'; //CHARGEMENT DU FORMULAIRE D'AJOUT DE DROITS 

$frm_str .='</td></tr><style>#tag_userform_chosen{width:95% !important;}</style>';
$frm_str .= '<input type="hidden" name="res_id" id="res_id"  value="'.$res_id.'" />';
$frm_str .= '<input type="hidden" id="new_tag_label" name="new_tag_label"/>';
$frm_str .= '<script type="text/javascript">load_tags('.$route_tag_ui_script.', \''.$res_id.'\', \''.$coll_id.'\');';
$frm_str .= ' $j("#tag_userform").chosen({width: "95%", disable_search_threshold: 10, search_contains: true});';
if ($core_tools->test_service('create_tag', 'tags', false) == 1) {
    $frm_str .= '$j( "#tag_userform_chosen input" ).focusout(function() {$j("#new_tag_label").val($j("#tag_userform_chosen input").val());if($j( "#tag_userform_chosen .no-results" ).length){if(confirm("'._ADD_TAG_CONFIRM.'")){add_this_tags('.$route_tag_add_tags_from_res.', '.$route_tag_ui_script.');}}});';
}
$frm_str .= '</script>';
   
?>
