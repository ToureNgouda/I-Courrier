<?php

/*
 *
 *   Copyright 2011 Maarch
 *
 *   This file is part of Maarch Framework.
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
 *   along with Maarch Framework.  If not, see <http://www.gnu.org/licenses/>.
 */

/*********************** ADMIN ***********************************/
if (!defined("_LIFE_CYCLE_COMMENT"))
    define("_LIFE_CYCLE_COMMENT", "Life cycle management");
if (!defined("_ADMIN_LIFE_CYCLE"))
    define("_ADMIN_LIFE_CYCLE", " Archiving politics");
if (!defined("_ADMIN_LIFE_CYCLE_DESC"))
    define("_ADMIN_LIFE_CYCLE_DESC", "Archiving politics definition, cycles and steps until final disposition");
if (!defined("_LIFE_CYCLE"))
    define("_LIFE_CYCLE", "Cycle de vie");
if (!defined("_ADMIN_LIFE_CYCLE_SHORT"))
    define("_ADMIN_LIFE_CYCLE_SHORT", " Life cycles administration");
if (!defined("_MANAGE_LC_CYCLES"))
    define("_MANAGE_LC_CYCLES", "Manage the life cycles ");
if (!defined("_MANAGE_LC_CYCLE_STEPS"))
    define("_MANAGE_LC_CYCLE_STEPS", "Manage the life cycles steps");
if (!defined("_MANAGE_LC_POLICIES"))
    define("_MANAGE_LC_POLICIES", "Manage archiving politics");

/*****************CYCLE_STEPS************************************/
if (!defined("_LC_CYCLE_STEPS"))
    define("_LC_CYCLE_STEPS", "");
if (!defined("_LC_CYCLE_STEP"))
    define("_LC_CYCLE_STEP", "Life cycle step");
if (!defined("_LC_CYCLE_STEP_ID"))
    define("_LC_CYCLE_STEP_ID", "Life cycle step ID");
if (!defined("_COLLECTION_IDENTIFIER"))
    define("_COLLECTION_IDENTIFIER", "Collection ID");
if (!defined("_LC_CYCLE_STEPS_LIST"))
    define("_LC_CYCLE_STEPS_LIST", "Life cycle steps list");
if (!defined("_ALL_LC_CYCLE_STEPS"))
    define("_ALL_LC_CYCLE_STEPS", "Display everything");
if (!defined("_POLICY_ID"))
    define("_POLICY_ID", "Archiving policy ID");
if (!defined("_CYCLE_STEP_ID"))
    define("_CYCLE_STEP_ID", "Life cycle step ID ");
if (!defined("_CYCLE_STEP_DESC"))
    define("_CYCLE_STEP_DESC","Life cycle step description");
if (!defined("_STEP_OPERATION"))
    define("_STEP_OPERATION", "Action on the life cycles steps");
if (!defined("_IS_ALLOW_FAILURE"))
    define("_IS_ALLOW_FAILURE", "Allow failures");
if (!defined("_IS_MUST_COMPLETE"))
    define("_IS_MUST_COMPLETE", "Must be complete");
if (!defined("_PREPROCESS_SCRIPT"))
    define("_PREPROCESS_SCRIPT", "Pre process script");
if (!defined("_POSTPROCESS_SCRIPT"))
    define("_POSTPROCESS_SCRIPT", "Post Process script");
if (!defined("_LC_CYCLE_STEP_ADDITION"))
    define("_LC_CYCLE_STEP_ADDITION","Add a step on the life cycle");
if (!defined("_LC_CYCLE_STEP_UPDATED"))
    define("_LC_CYCLE_STEP_UPDATED", "Updated life cycle step");
if (!defined("_LC_CYCLE_STEP_ADDED"))
    define("_LC_CYCLE_STEP_ADDED", " Added a life cycle step");
if (!defined("_LC_CYCLE_STEP_DELETED"))
    define("_LC_CYCLE_STEP_DELETED", "Deleted life cycle step");
if (!defined("_LC_CYCLE_STEP_MODIFICATION"))
    define("_LC_CYCLE_STEP_MODIFICATION","Modification of a life cycle step");

/****************CYCLES*************************************/
if (!defined("_CYCLE_ID"))
    define("_CYCLE_ID", "Life cycle ID");
if (!defined("_LC_CYCLE_ID"))
    define("_LC_CYCLE_ID", "Life cycle ID");
if (!defined("_LC_CYCLE"))
    define("_LC_CYCLE", "A life cycle");
if (!defined("_LC_CYCLES"))
    define("_LC_CYCLES", "Life cycle(s)");
if (!defined("_CYCLE_DESC"))
    define("_CYCLE_DESC", "Description of life cycle");
if (!defined("_VALIDATION_MODE"))
    define("_VALIDATION_MODE", "Validation mode");
if (!defined("_ALL_LC_CYCLES"))
    define("_ALL_LC_CYCLES", "Show everything");
if (!defined("_LC_CYCLES_LIST"))
    define("_LC_CYCLES_LIST", "life cycles lists");
if (!defined("_SEQUENCE_NUMBER"))
    define("_SEQUENCE_NUMBER", "Sequence number");
if (!defined("_BREAK_KEY"))
    define("_BREAK_KEY", "Breach key");
if (!defined("_LC_CYCLE_ADDITION"))
    define("_LC_CYCLE_ADDITION", "Add a life cycle");
if (!defined("_LC_CYCLE_ADDED"))
    define("_LC_CYCLE_ADDED", "Added life cycle");
if (!defined("_LC_CYCLE_UPDATED"))
    define("_LC_CYCLE_UPDATED", "Updated life cycle");
if (!defined("_LC_CYCLE_DELETED"))
    define("_LC_CYCLE_DELETED", "Deleted life cycle");
if (!defined("_LC_CYCLE_MODIFICATION"))
    define("_LC_CYCLE_MODIFICATION", "Life cycle modification");
if (!defined("_PB_WITH_WHERE_CLAUSE"))
    define("_PB_WITH_WHERE_CLAUSE", "Problem on clause where");
if (!defined("_CANNOT_DELETE_CYCLE_ID"))
    define("_CANNOT_DELETE_CYCLE_ID", "Impossible to delete the cycle");

/*************CYCLE POLICIES*************************************/
if (!defined("_LC_POLICIES"))
    define("_LC_POLICIES", "");
if (!defined("_LC_POLICY"))
    define("_LC_POLICY", "Archiving policy");
if (!defined("_POLICY_NAME"))
    define("_POLICY_NAME", "Policy name");
if (!defined("_LC_POLICY_ID"))
    define("_LC_POLICY_ID", "Policy ID");
if (!defined("_LC_POLICY_NAME"))
    define("_LC_POLICY_NAME", "Policy name");
if (!defined("_POLICY_DESC"))
    define("_POLICY_DESC", "Policy description");
if (!defined("_LC_POLICY_ADDITION"))
    define("_LC_POLICY_ADDITION", "Add a life cycle policy");
if (!defined("_LC_POLICIES_LIST"))
    define("_LC_POLICIES_LIST", "Life cycle policy list");
if (!defined("_ALL_LC_POLICIES"))
    define("_ALL_LC_POLICIES", "Show everything");
if (!defined("_LC_POLICY_UPDATED"))
    define("_LC_POLICY_UPDATED", "Updated life cycle policy");
if (!defined("_LC_POLICY_ADDED"))
    define("_LC_POLICY_ADDED", "Policy of added life cycle");
if (!defined("_LC_POLICY_DELETED"))
    define("_LC_POLICY_DELETED", "Deleted life cycle policy");
if (!defined("_LC_POLICY_MODIFICATION"))
    define("_LC_POLICY_MODIFICATION","Archiving policy modification");
if (!defined("_MISSING_A_CYCLE_STEP"))
    define("_MISSING_A_CYCLE_STEP", "You have to add one life cycle step at least to complete your configuration");
if (!defined("_MISSING_A_CYCLE_AND_A_CYCLE_STEP"))
    define("_MISSING_A_CYCLE_AND_A_CYCLE_STEP", "You have to add one life cycle et one step at least to complete your configuration");

/*************BATCH*************************************/
if (!defined("_PI_COMMENT_ROOT"))
    define("_PI_COMMENT_ROOT", "Packaging information:  Archiving package utilisation (Archival Information package)");
if (!defined("_PI_COMMENT_FINGERPRINT"))
    define("_PI_COMMENT_FINGERPRINT", "Print associated to the CI/ IC file");
if (!defined("_PI_COMMENT_AIU"))
    define("_PI_COMMENT_AIU", "Number of present resources in the AIP");
if (!defined("_PI_COMMENT_CONTENT"))
    define("_PI_COMMENT_CONTENT", "Digital resources in their native format (name + file extension)");
if (!defined("_PI_COMMENT_PDI"))
    define("_PI_COMMENT_PDI","Preservation Description Information: Descriptors catalogue of Source, Reference, Context, Integrity and Access Rights of present resources in <CONTENT_FILE>. For practical reasons, the processing history is stored apart in. See pdi.xsd for the commented structure");
if (!defined("_HISTORY_COMMENT_ROOT"))
    define("_HISTORY_COMMENT_ROOT", "Preservation Description Information - History : Events list on the resource, each event game is identified by its file name in <CONTENT_FILE>. Chronologically classified");
if (!defined("_PDI_COMMENT_ROOT"))
    define("_PDI_COMMENT_ROOT","Preservation Description Information : Descriptors list of resource, classified by category : Source, Reference, Context, Integrity et access rights. There is a description by resource, each resource is identified by its file name in <CONTENT_FILE>");
if (!defined("_PDI_COMMENT_HISTORY"))
    define("_PDI_COMMENT_HISTORY", "Preservation Description Information - History : Events list on the resource, each event game is identified by its folder name in <CONTENT_FILE>. Chronologically classified.");

/*************OTHER*************************************/
if (!defined("_DOCS"))
    define("_DOCS", "Documents");
if (!defined("_LINK_EXISTS"))
    define("_LINK_EXISTS", "A link with an other object exists");
if (!defined("_VIEW_GENERAL_PARAMETERS_OF_THE_POLICY"))
    define("_VIEW_GENERAL_PARAMETERS_OF_THE_POLICY", "View the global setting of the life cycles policy");


