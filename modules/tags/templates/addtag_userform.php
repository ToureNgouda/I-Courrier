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

if (!$tag_customsize) {
    $tag_customsize = '400px';
}

if (!$tag_customsize) {
    $tag_customcols = '35';
}

if (!is_array($_SESSION['tagsuser'])) {
    $_SESSION['tagsuser'] = array();
}

$tags_list=$tags->get_all_tags();

if ($modify_keyword) {
    $frm_str .='<select  id="tag_userform" name="tag_userform[]" multiple="" data-placeholder=" ">';
} else {
    $frm_str .='<select disabled="disabled" id="tag_userform" title="Vous n\'avez pas le droit d\'associer de mots-clés" name="tag_userform[]" multiple="" data-placeholder=" ">';
}

if (!empty($tags_list)) {
    foreach ($tags_list as $key => $value) {
        if (in_array($value['tag_id'], $_SESSION['tagsuser'])) {
            $frm_str .= '<option selected="selected" value="'.$value['tag_id'].'">'.$value['tag_label'].'</option>';
        } else {
            $frm_str .= '<option value="'.$value['tag_id'].'">'.$value['tag_label'].'</option>';
        }
    }
}


$frm_str .='</select>';
