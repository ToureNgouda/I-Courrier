<?php
/*
 *
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

/*********************** SERVICES ***********************************/
if (!defined("_ADMIN_FOLDERTYPES"))
    define("_ADMIN_FOLDERTYPES", "Types of folders");
if (!defined("_ATTACH_DOC_TO_FOLDER"))
    define("_ATTACH_DOC_TO_FOLDER", "Attach a document to a folder");
if (!defined("_ADMIN_FOLDERTYPES_DESC"))
    define("_ADMIN_FOLDERTYPES_DESC", "Administrate folder's types. For each type, define the linked qualifiers and the mandatory folder's types for the folder completeness.");

/*********************** Menu ***********************************/
if (!defined("_FOLDER_SEARCH"))
    define("_FOLDER_SEARCH", "Search a folder");
if (!defined("_SALARY_SHEET"))
    define("_SALARY_SHEET", "Folder card");
if (!defined("_FOLDER_OUT"))
    define("_FOLDER_OUT", "Removed from the archives");

//////////////Research folder 
if (!defined("_SELECT_FOLDER_TITLE"))
    define("_SELECT_FOLDER_TITLE", "Folder selection");
if (!defined("_FOLDER_NUM"))
    define("_FOLDER_NUM", "Folder number");
if (!defined("_COMPLETE"))
    define("_COMPLETE", "Complete");
if (!defined("_INCOMPLETE"))
    define("_INCOMPLETE", "Incomplete");
if (!defined("_FOUND_FOLDER"))
    define("_FOUND_FOLDER", "Found folders");
if (!defined("_CHOOSE"))
    define("_CHOOSE", "Choose");
if (!defined("_ADV_SEARCH_FOLDER_TITLE"))
    define("_ADV_SEARCH_FOLDER_TITLE", "Folder search");
if (!defined("_SEARCH_ADV_FOLDER"))
    define("_SEARCH_ADV_FOLDER", "Folder search");
if (!defined("_NEW_SEARCH"))
    define("_NEW_SEARCH", "Delete the criterion");
if (!defined("_SELECT_FOLDER"))
    define("_SELECT_FOLDER", "Folder selection");
if (!defined("_CREATE_FOLDER"))
    define("_CREATE_FOLDER", "Create a folder");
if (!defined("_CREATE_FOLDER2"))
    define("_CREATE_FOLDER2", "Create a folder");
if (!defined("_FOLDER"))
    define("_FOLDER", "Folder");
if (!defined("_MODIFY_FOLDER"))
    define("_MODIFY_FOLDER", "Modify folder's index");
if (!defined("_FOLDERID"))
    define("_FOLDERID", "Folder's number");
if (!defined("_FOLDERSYSTEMID"))
    define("_FOLDERSYSTEMID", "Maarch's system number");
if (!defined("_FOLDERID_LONG"))
    define("_FOLDERID_LONG", "Folder's ID");
if (!defined("_FOLDER_DESTINATION_QUESTION"))
    define("_FOLDER_DESTINATION_QUESTION", "Do you want to make this folder only accessible to your department ?");
if (!defined("_FOLDER_DESTINATION_SHORT"))
    define("_FOLDER_DESTINATION_SHORT", "Department folder");
if (!defined("_FOLDERNAME"))
    define("_FOLDERNAME", "Folder's name");
if (!defined("_FOLDERDATE"))
    define("_FOLDERDATE", "Date of creation");
if (!defined("_FOLDERDATE_START"))
    define("_FOLDERDATE_START", "Beginning date of creation ");
if (!defined("_FOLDERDATE_END"))
    define("_FOLDERDATE_END", "Ending date of creation ");
if (!defined("_FOLDERHASNODOC"))
    define("_FOLDERHASNODOC","No attachment for this folder");
if (!defined("_OTHER_INFOS"))
    define("_OTHER_INFOS","Other information : folder's history and missing files for the completeness");
if (!defined("_SEARCH_FOLDER"))
    define("_SEARCH_FOLDER","Folder search");
if (!defined("_SELECTED_FOLDER"))
    define("_SELECTED_FOLDER","Selected folder");
if (!defined("_FOUND_FOLDERS"))
    define("_FOUND_FOLDERS","Found folders");
if (!defined("_FOLDERTYPE_LABEL"))
    define("_FOLDERTYPE_LABEL","Folder wording");
if (!defined("_INFOS_FOLDERS"))
    define("_INFOS_FOLDERS","Folder's information");
if (!defined("_CHOOSE_FOLDER"))
    define("_CHOOSE_FOLDER", "Choose a folder");
if (!defined("_ON_FOLDER_NUM"))
    define("_ON_FOLDER_NUM", "On the folder number");

//////////////create_folder.php
if (!defined("_CREATE_THE_FOLDER"))
    define("_CREATE_THE_FOLDER", "Create the folder");
if (!defined("_NEW_EMPLOYEES_LIST"))
    define("_NEW_EMPLOYEES_LIST","List of the new collaborators");
if (!defined("_FOLDERS_LIST"))
    define("_FOLDERS_LIST", "folders' list");
if (!defined("_FOLDERS"))
    define("_FOLDERS", "Folders");
if (!defined("_FOLDERS_COMMENT"))
    define("_FOLDERS_COMMENT", "Folders");
if (!defined("_CHOOSE2"))
    define("_CHOOSE2", "Choose");
if (!defined("_IS_MANDATORY"))
    define("_IS_MANDATORY", "Is mandatory");
if (!defined("_FOLDER_CREATION"))
    define("_FOLDER_CREATION", "Folder's creation");

///////////////delete_popup.php
if (!defined("_DEL_FOLDER_NUM"))
    define("_DEL_FOLDER_NUM", "Deletion of the folder number");
if (!defined("_DEL_FOLDER"))
    define("_DEL_FOLDER", "Delete the folder");

//Step in add_batch.php for physical_archive
if (!defined("_STEP_ONE"))
    define("_STEP_ONE", "1 - Choose a folder");

/////////create folder
if (!defined("_CHOOSE_SOCIETY"))
    define("_CHOOSE_SOCIETY", "Choose a company");
if (!defined("_THE_SOCIETY"))
    define("_THE_SOCIETY", "The company ");
if (!defined("_MISSING_DOC"))
    define("_MISSING_DOC", "Missing attachment(s)");
if (!defined("_MISSING_DOC2"))
    define("_MISSING_DOC2", "Missing attachment(s)");
if (!defined("_PLEASE_SELECT_FOLDER"))
    define("_PLEASE_SELECT_FOLDER", "You have to select a folder");
if (!defined("_FOLDER_HISTORY"))
    define("_FOLDER_HISTORY", "Folder's history");
if (!defined("_CHOOSE_FOLDERTYPE"))
    define("_CHOOSE_FOLDERTYPE", "Choose a type of folder");
if (!defined("_BROWSE_BY_FOLDER"))
    define("_BROWSE_BY_FOLDER", "Folder search");
if (!defined("_CHAR_ERROR"))
    define("_CHAR_ERROR", "The ID cannot contain the following characters : ", "");

/*************************** Folder types management *****************/
if (!defined("_FOLDERTYPE_ADDITION"))
    define("_FOLDERTYPE_ADDITION", "Add a type of folder");
if (!defined("_FOLDERTYPE_MODIFICATION"))
    define("_FOLDERTYPE_MODIFICATION", "Modification of the folder's type");
if (!defined("_FOLDERTYPES_LIST"))
    define("_FOLDERTYPES_LIST", "Folder types list");
if (!defined("_TYPES"))
    define("_TYPES", "type(s)");
if (!defined("_ALL_FOLDERTYPES"))
    define("_ALL_FOLDERTYPES", "All of the types");
if (!defined("_FOLDERTYPE"))
    define("_FOLDERTYPE", "Folder type");
if (!defined("_FOLDERTYPE_MISSING"))
    define("_FOLDERTYPE_MISSING", "Missing folder type");

/************************** employees file ***************************/
if (!defined("_ARCHIVED_DOC"))
    define("_ARCHIVED_DOC", "*Archived files");
if (!defined("_SEND_RELANCE_MAIL"))
    define("_SEND_RELANCE_MAIL", "Send reminder mail");
if (!defined("_DIRECTION_DEP"))
    define("_DIRECTION_DEP", "Department / direction");
if (!defined("_DEP_AGENCY"))
    define("_DEP_AGENCY", "Agency/ department");
if (!defined("_DELETE_FOLDER"))
    define("_DELETE_FOLDER", "Delete a folder");
if (!defined("_DELETE_FOLDER_NOTES1"))
    define("_DELETE_FOLDER_NOTES1", "The folder deletion is irreversible, its files are saved but they're not available to consultation.");
if (!defined("_REALLY_DELETE_FOLDER"))
    define("_REALLY_DELETE_FOLDER", "Do you want to remove the folder ?");
if (!defined("_DELETE_FOLDER_NOTES2"))
    define("_DELETE_FOLDER_NOTES2","To definitely remove the folder, enter EFFACER / ERASE (in capital letters) on the box below");
if (!defined("_DELETE_FOLDER_NOTES3"))
    define("_DELETE_FOLDER_NOTES3", "The folder will be erased after validation.");
if (!defined("_DELETE_FOLDER_NOTES4"))
    define("_DELETE_FOLDER_NOTES4", "The folder can not be deleted because of a wrong confirmation");
if (!defined("_DELETE_FOLDER_NOTES5"))
    define("_DELETE_FOLDER_NOTES5", "The folder is now deleted from the database.");
if (!defined("_FOLDER_INDEX_MODIF"))
    define("_FOLDER_INDEX_MODIF", "Folder's index modification");
if (!defined("_FOLDERS_OUT"))
    define("_FOLDERS_OUT", "Folders removed from the archives");

///////////////// Class_admin_foldertype
//CUSTOM
if (!defined("_MANDATORY_DOCTYPES_COMP"))
    define("_MANDATORY_DOCTYPES_COMP", "The folder's types are mandatory for the folder completeness");
if (!defined("_FOLDER_ID"))
    define("_FOLDER_ID", "Folder's ID");
if (!defined("_INDEX_FOR_FOLDERTYPES"))
    define("_INDEX_FOR_FOLDERTYPES", "Possible index for the folder types");
if (!defined("_SELECTED_DOCTYPES"))
    define("_SELECTED_DOCTYPES", "Selected folder types");
if (!defined("_SHOW_FOLDER"))
    define("_SHOW_FOLDER", "Folder form");
if (!defined("_FOLDERTYPE_UPDATE"))
    define("_FOLDERTYPE_UPDATE", "Modified folder type");
if (!defined("_FOLDER_ATTACH"))
    define("_FOLDER_ATTACH", "Linked to a folder");
if (!defined("_INCOMPATIBILITY_MARKET_PROJECT"))
    define("_INCOMPATIBILITY_MARKET_PROJECT", "Incompatibility between the folder and the sub-folder");
if (!defined("_FOLDER_VIEW_STAT"))
    define("_FOLDER_VIEW_STAT", "Number of read folders");
if (!defined("_FOLDER_VIEW_STAT_DESC"))
    define("_FOLDER_VIEW_STAT_DESC", "Number of read folders");
if (!defined("_FOLDER_HISTORY_STAT"))
    define("_FOLDER_HISTORY_STAT", "History of a folder");
if (!defined("_FOLDER_HISTORY_STAT_DESC"))
    define("_FOLDER_HISTORY_STAT_DESC", "History of a folder");
if (!defined("_VIEW_FOLDER"))
    define("_VIEW_FOLDER", "Folder view");

////////// Reports label
if (!defined("_TITLE_STATS_CHOICE_FOLDER_TYPE"))
    define("_TITLE_STATS_CHOICE_FOLDER_TYPE","By folder type");
if (!defined("_TITLE_STATS_CHOICE_GROUP"))
    define("_TITLE_STATS_CHOICE_GROUP","By users' group");
if (!defined("_TITLE_STATS_CHOICE_USER"))
    define("_TITLE_STATS_CHOICE_USER","For one user");
if (!defined("_TITLE_STATS_CHOICE_USER2"))
    define("_TITLE_STATS_CHOICE_USER2","by the user");
if (!defined("_TITLE_STATS_NO_FOLDERS_VIEW"))
    define("_TITLE_STATS_NO_FOLDERS_VIEW","No read folders fo this period");
if (!defined("_STATS_ERROR_CHOSE_USER"))
    define("_STATS_ERROR_CHOSE_USER","You have to choose an existing user.");
if (!defined("_NB_FOLDERS"))
    define("_NB_FOLDERS", "Number of folders" );
if (!defined("_NB_VIEWED_FOLDERS"))
    define("_NB_VIEWED_FOLDERS","Number of read folders");
if (!defined("_TITLE_STATS_CHOICE_ACTION"))
    define("_TITLE_STATS_CHOICE_ACTION","By type of action");
if (!defined("_ACTION_TYPE"))
    define("_ACTION_TYPE", "Action type");
if (!defined("_NO_STRUCTURE_ATTACHED2"))
    define("_NO_STRUCTURE_ATTACHED2", "There is no document type attached to any document folder");
if (!defined("_FOLDER_ADDED"))
    define("_FOLDER_ADDED", "New created folder");
if (!defined("_FOLDER_DETAILLED_PROPERTIES"))
    define("_FOLDER_DETAILLED_PROPERTIES", "Folder information");
if (!defined("_FOLDER_PROPERTIES"))
    define("_FOLDER_PROPERTIES", "Folder properties");
if (!defined("_SYSTEM_ID"))
    define("_SYSTEM_ID", "System ID ");
if (!defined("_MODIFICATION_DATE"))
    define("_MODIFICATION_DATE", "Modification date");
if (!defined("_UPDATE_FOLDER"))
    define("_UPDATE_FOLDER", "Modify information");
if (!defined("_FOLDER_INDEX_UPDATED"))
    define("_FOLDER_INDEX_UPDATED", "Modified folder's Index");
if (!defined("_FOLDER_UPDATED"))
    define("_FOLDER_UPDATED", "Folder's update done");
if (!defined("_ALL_DOCS_AND_SUFOLDERS_WILL_BE_DELETED"))
    define("_ALL_DOCS_AND_SUFOLDERS_WILL_BE_DELETED", "All the documents from this folder and all the sub folders will also be erased !");
if (!defined("_STRING"))
    define("_STRING", "Characters chain");
if (!defined("_INTEGER"))
    define("_INTEGER", "Entire");
if (!defined("_FLOAT"))
    define("_FLOAT", "Floating");
if (!defined("_DATE"))
    define("_DATE", "Date");
if (!defined("_MAX"))
    define("_MAX", "maximum");
if (!defined("_MIN"))
    define("_MIN", "minimum");
if (!defined("_FOLDER_OR_SUBFOLDER"))
    define("_FOLDER_OR_SUBFOLDER", "Folder/sub folder");
if (!defined("_ERROR_COMPATIBILITY_FOLDER"))
    define("_ERROR_COMPATIBILITY_FOLDER", "Compatibility issue between the folder and the folder type");
if (!defined("_ADDED_TO_FOLDER"))
    define("_ADDED_TO_FOLDER", " Add to the folder");
if (!defined("_DELETED_FROM_FOLDER"))
    define("_DELETED_FROM_FOLDER", " Erased from the folder");
if (!defined("_CHOOSE_PARENT_FOLDER"))
    define("_CHOOSE_PARENT_FOLDER", "Associate this folder to an existing one");
if (!defined("_FOLDER_PARENT"))
    define("_FOLDER_PARENT", "Relative folder");
if (!defined("_FOLDER_PARENT_DESC"))
    define("_FOLDER_PARENT_DESC", "you can choose to create a sub folder related to a folder with the same type. There is only two levels : folder and subfolder.");
if (!defined("_THIS_FOLDER"))
    define("_THIS_FOLDER", "This folder");
if (!defined("_ALL_FOLDERS"))
    define("_ALL_FOLDERS", "All folders");
if (!defined("_FOLDER_DELETED"))
    define("_FOLDER_DELETED", "Erased folder");
if (!defined("_FREEZE_FOLDER_SERVICE"))
    define("_FREEZE_FOLDER_SERVICE", "Freeze and unfreeze folders");
if (!defined("_FREEZE_FOLDER"))
    define("_FREEZE_FOLDER", "Freeze the folder");
if (!defined("_UNFREEZE_FOLDER"))
    define("_UNFREEZE_FOLDER", "Unfreeze the folder");
if (!defined("_CLOSE_FOLDER"))
    define("_CLOSE_FOLDER", "Close the folder");
if (!defined("_FOLDER_CLOSED"))
    define("_FOLDER_CLOSED", "Closed folder");
if (!defined("_FROZEN_FOLDER"))
    define("_FROZEN_FOLDER", "Folder frost");
if (!defined("_UNFROZEN_FOLDER"))
    define("_UNFROZEN_FOLDER", "Folder thaw");
if (!defined("_REALLY_FREEZE_THIS_FOLDER"))
    define("_REALLY_FREEZE_THIS_FOLDER", "Are you sure to frost this folder");
if (!defined("_REALLY_CLOSE_THIS_FOLDER"))
    define("_REALLY_CLOSE_THIS_FOLDER", "Are you sure to want to enclose this folder?");
if (!defined("_SUBFOLDER"))                         
    define("_SUBFOLDER", "Sub folder");
if (!defined("_VIEW_FOLDER_TREE"))                  
    define("_VIEW_FOLDER_TREE", "Read a folder");
if (!defined("_SEARCH_FOLDER_TREE"))                
    define("_SEARCH_FOLDER_TREE", "Search folders");
if (!defined("_NB_DOCS_IN_FOLDER"))                 
    define("_NB_DOCS_IN_FOLDER", "Files number");
if (!defined("_IS_FOLDER_BASKET"))                  
    define("_IS_FOLDER_BASKET", "Folder's basket");
if (!defined("_IS_FOLDER_STATUS"))                  
    define("_IS_FOLDER_STATUS", "Folder's status");
if (!defined("_IS_FOLDER_ACTION"))                  
    define("_IS_FOLDER_ACTION", "Folder's action");
if (!defined("_CONFIRM_FOLDER_STATUS"))             
    define("_CONFIRM_FOLDER_STATUS", "Simple confirmation (folders)");
if (!defined("_REDIRECT_FOLDER"))                   
    define("_REDIRECT_FOLDER", "Folder redirection");
if (!defined("_REDIRECT_ALL_DOCUMENTS_IN_FOLDER"))  
    define("_REDIRECT_ALL_DOCUMENTS_IN_FOLDER", "Redirect all folder's documents");
if (!defined("_CHOOSE_ONE_FOLDER"))                 
    define("_CHOOSE_ONE_FOLDER", "Choose one folder at least");
if (!defined("_MUST_CHOOSE_DEP_OR_USER"))           
    define("_MUST_CHOOSE_DEP_OR_USER", "You have to select a department or an user!");
if (!defined('_LABEL'))
    define("_LABEL", "Wording");
