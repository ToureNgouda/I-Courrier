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

/*********************** TAGS ***********************************/
if (!defined("_TAG_DEFAULT"))
    define("_TAG_DEFAULT", "Tag");
if (!defined("_TAGS_DEFAULT"))
    define("_TAGS_DEFAULT", "Tags");
if (!defined("_TAGS_COMMENT"))
    define("_TAGS_COMMENT", "Tags");
if (!defined("_TAGS_LIST"))
    define("_TAGS_LIST", "Tags list");
if (!defined("_MODIFY_TAG"))
    define("_MODIFY_TAG", "Modify the tag");
if (!defined("_MANAGE_TAGS"))
    define("_MANAGE_TAGS", "Manage the tag");
if (!defined("_ADMIN_TAGS"))
    define("_ADMIN_TAGS", "Tags");
if (!defined("_ADMIN_TAGS_DESC"))
    define("_ADMIN_TAGS_DESC", "Allows to modify, erase, add or merge tags");
if (!defined("_ALL_TAGS"))
    define("_ALL_TAGS", "All the tags");
if (!defined("_TAG_DELETED"))
    define("_TAG_DELETED", _TAG_DEFAULT." erased");
if (!defined("_TAG_ADDED"))
    define("_TAG_ADDED", _TAG_DEFAULT." added");
if (!defined("_TAG_UPDATED"))
    define("_TAG_UPDATED", _TAG_DEFAULT." modified");
if (!defined("_TAG_LABEL_IS_EMPTY"))
    define("_TAG_LABEL_IS_EMPTY", "The wording is empty");
if (!defined("_NO_TAG"))
    define("_NO_TAG", "No "._TAG_DEFAULT);
if (!defined("_TAG_VIEW"))
    define("_TAG_VIEW", "View the "._TAGS_DEFAULT." of the documents");
if (!defined("_TAG_VIEW_DESC"))
    define("_TAG_VIEW_DESC", "Allows to view "._TAGS_DEFAULT);
if (!defined("_ADD_TAG"))
    define("_ADD_TAG", "Add a "._TAG_DEFAULT);
if (!defined("_ADD_TAG_TO_RES"))
    define("_ADD_TAG_TO_RES", "Associate the available "._TAGS_DEFAULT." to a document");
if (!defined("_CREATE_TAG"))
    define("_CREATE_TAG", "Create "._TAGS_DEFAULT);
if (!defined("_ADD_TAG_TO_RES_DESC"))
    define("_ADD_TAG_TO_RES_DESC", "Allows to add "._TAGS_DEFAULT." to a resource");
if (!defined("_DELETE_TAG_TO_RES"))
    define("_DELETE_TAG_TO_RES", "Erase "._TAGS_DEFAULT." to a resource");
if (!defined("_DELETE_TAG_TO_RES_DESC"))
    define("_DELETE_TAG_TO_RES_DESC", "Allows to erase "._TAGS_DEFAULT." to a resource");
if (!defined("_NEW_TAG_IN_LIBRARY_RIGHTS"))
    define("_NEW_TAG_IN_LIBRARY_RIGHTS", "Create new "._TAGS_DEFAULT." in the Maarch's library");
if (!defined("_NEW_TAG_IN_LIBRARY_RIGHTS_DESC"))
    define("_NEW_TAG_IN_LIBRARY_RIGHTS_DESC", "By activating this tag, the user will be able to add new "._TAGS_DEFAULT." in the Maarch's library");
if (!defined("_TAG"))
    define("_TAG", _TAG_DEFAULT);
if (!defined("_TAGS"))
    define("_TAGS", _TAGS_DEFAULT);
if (!defined("_TAG_SEPARATOR_HELP"))
    define("_TAG_SEPARATOR_HELP", "Separate tags by pressing on Enter or with commas");
if (!defined("_NB_DOCS_FOR_THIS_TAG"))
    define("_NB_DOCS_FOR_THIS_TAG", "tagged documents");
if (!defined("_TAGOTHER_OPTIONS"))
    define("_TAGOTHER_OPTIONS", "Other options");
if (!defined("_TAG_FUSION_ACTIONLABEL"))
    define("_TAG_FUSION_ACTIONLABEL", "Merge the tag with");
if (!defined("_TAGFUSION"))
    define("_TAGFUSION", "Merger");
if (!defined("_TAGFUSION_GOODRESULT"))
    define("_TAGFUSION_GOODRESULT", "Those tags are now merged");
if (!defined("_TAG_ALREADY_EXISTS"))
    define("_TAG_ALREADY_EXISTS", "This tag already exists");
if (!defined("_CHOOSE_TAG"))
    define("_CHOOSE_TAG", "Choice of tags");
if (!defined("_TAG_SEARCH"))
    define("_TAG_SEARCH", "Tags");
if (!defined("_TAGNONE"))
    define("_TAGNONE", "None");
if (!defined("_ALL_TAG_DELETED_FOR_RES_ID"))
    define("_ALL_TAG_DELETED_FOR_RES_ID", "All the tags are erased for the resource");
if (!defined("_TAGCLICKTODEL"))
    define("_TAGCLICKTODEL", "Erase");
if (!defined("_NAME_TAGS"))
    define("_NAME_TAGS", "Tag's name");
if (!defined("_ADD_TAG_CONFIRM"))
    define("_ADD_TAG_CONFIRM", "This word will be added as ".strtolower(_TAG_DEFAULT).".");
