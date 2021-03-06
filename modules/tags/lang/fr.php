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
    define("_TAG_DEFAULT", "Mot-clé");
if (!defined("_TAGS_DEFAULT"))
    define("_TAGS_DEFAULT", "Mots-clés");
if (!defined("_TAGS_COMMENT"))
    define("_TAGS_COMMENT", "Mots-clés");
if (!defined("_TAGS_LIST"))
    define("_TAGS_LIST", "Liste des ".strtolower(_TAGS_DEFAULT));
if (!defined("_MODIFY_TAG"))
    define("_MODIFY_TAG", "Modifier le ".strtolower(_TAG_DEFAULT));
if (!defined("_MANAGE_TAGS"))
    define("_MANAGE_TAGS", "Gérer les ".strtolower(_TAGS_DEFAULT));
if (!defined("_ADMIN_TAGS"))
    define("_ADMIN_TAGS", "Mots-clés");
if (!defined("_ADMIN_TAGS_DESC"))
    define("_ADMIN_TAGS_DESC", "Permet de modifier, supprimer, ajouter ou fusionner des ".strtolower(_TAGS_DEFAULT)." depuis l'interface d'administration");
if (!defined("_ALL_TAGS"))
    define("_ALL_TAGS", "Tous les ".strtolower(_TAGS_DEFAULT));
if (!defined("_TAG_DELETED"))
    define("_TAG_DELETED", _TAG_DEFAULT." supprimé");
if (!defined("_TAG_ADDED"))
    define("_TAG_ADDED", _TAG_DEFAULT." ajouté");
if (!defined("_TAG_UPDATED"))
    define("_TAG_UPDATED", _TAG_DEFAULT." modifié");
if (!defined("_TAG_LABEL_IS_EMPTY"))
    define("_TAG_LABEL_IS_EMPTY", "Le libellé est vide");
if (!defined("_NO_TAG"))
    define("_NO_TAG", "Pas de "._TAG_DEFAULT);
if (!defined("_TAG_VIEW"))
    define("_TAG_VIEW", "Voir les ".strtolower(_TAGS_DEFAULT)." des documents");
if (!defined("_TAG_VIEW_DESC"))
    define("_TAG_VIEW_DESC", "Permet d'afficher le champ ".strtolower(_TAGS_DEFAULT)." depuis les pages d'actions et fiche détaillée.");
if (!defined("_ADD_TAG"))
    define("_ADD_TAG", "Ajouter un ".strtolower(_TAG_DEFAULT));
if (!defined("_ADD_TAG_TO_RES"))
    define("_ADD_TAG_TO_RES", "Associer des ".strtolower(_TAGS_DEFAULT)." disponibles pour un document");
if (!defined("_CREATE_TAG"))
    define("_CREATE_TAG", "Créer des ".strtolower(_TAGS_DEFAULT)." depuis les pages d'actions");
if (!defined("_CREATE_TAG_DESC"))
    define("_CREATE_TAG_DESC", "Permet d'enregistrer à la volée des ".strtolower(_TAGS_DEFAULT)." inexistants dans la base de données");
if (!defined("_ADD_TAG_TO_RES_DESC"))
    define("_ADD_TAG_TO_RES_DESC", "Permet d'associer des ".strtolower(_TAGS_DEFAULT)." à un document grâce au champ ".strtolower(_TAGS_DEFAULT)." depuis les pages d'actions et fiche détaillée");
if (!defined("_DELETE_TAG_TO_RES"))
    define("_DELETE_TAG_TO_RES", "Supprimer des ".strtolower(_TAGS_DEFAULT)." à une ressource");
if (!defined("_DELETE_TAG_TO_RES_DESC"))
    define("_DELETE_TAG_TO_RES_DESC", "Permet de supprimer des ".strtolower(_TAGS_DEFAULT)." à une ressource");
if (!defined("_NEW_TAG_IN_LIBRARY_RIGHTS"))
    define("_NEW_TAG_IN_LIBRARY_RIGHTS", "Créer des nouveaux ".strtolower(_TAGS_DEFAULT)." dans la librairie Maarch");
if (!defined("_NEW_TAG_IN_LIBRARY_RIGHTS_DESC"))
    define("_NEW_TAG_IN_LIBRARY_RIGHTS_DESC", "En activant ce ".strtolower(_TAG_DEFAULT).", l'utilistateur pourra ajouter de nouveaux ".strtolower(_TAGS_DEFAULT)." dans la librairie de Maarch");
if (!defined("_TAG"))
    define("_TAG", _TAG_DEFAULT);
if (!defined("_TAGS"))
    define("_TAGS", _TAGS_DEFAULT);
if (!defined("_TAG_SEPARATOR_HELP"))
    define("_TAG_SEPARATOR_HELP", "Séparez les ".strtolower(_TAGS_DEFAULT)." en appuyant sur Entrée ou avec des virgules");
if (!defined("_NB_DOCS_FOR_THIS_TAG"))
    define("_NB_DOCS_FOR_THIS_TAG", "document(s) associé(s)");
if (!defined("_TAGOTHER_OPTIONS"))
    define("_TAGOTHER_OPTIONS", "Autres options");
if (!defined("_TAG_FUSION_ACTIONLABEL"))
    define("_TAG_FUSION_ACTIONLABEL", "Fusionner le ".strtolower(_TAG_DEFAULT)." avec");
if (!defined("_TAGFUSION"))
    define("_TAGFUSION", "Fusion");
if (!defined("_TAGFUSION_GOODRESULT"))
    define("_TAGFUSION_GOODRESULT", "Ces ".strtolower(_TAGS_DEFAULT)." sont maintenant fusionnés");
if (!defined("_TAG_ALREADY_EXISTS"))
    define("_TAG_ALREADY_EXISTS", "Ce ".strtolower(_TAG_DEFAULT)." existe déjà");
if (!defined("_CHOOSE_TAG"))
    define("_CHOOSE_TAG", "Choix des ".strtolower(_TAGS_DEFAULT));
if (!defined("_TAG_SEARCH"))
    define("_TAG_SEARCH", "Mots-clés");
if (!defined("_TAGNONE"))
    define("_TAGNONE", "Aucun");
if (!defined("_ALL_TAG_DELETED_FOR_RES_ID"))
    define("_ALL_TAG_DELETED_FOR_RES_ID", "Tous les ".strtolower(_TAGS_DEFAULT)." sont supprimés pour la ressource");
if (!defined("_TAGCLICKTODEL"))
    define("_TAGCLICKTODEL", "supprimer");
if (!defined("_NAME_TAGS"))
    define("_NAME_TAGS", "Nom du ".strtolower(_TAG_DEFAULT));
if (!defined("_PRIVATE_TAGS"))
    define("_PRIVATE_TAGS", "Lier les ".strtolower(_TAGS_DEFAULT)." à l'entité de l'utilisateur (Niveau direction)");
if (!defined("_PRIVATE_TAGS_DESC"))
    define("_PRIVATE_TAGS_DESC", "L'utilisateur ne verra que les ".strtolower(_TAGS_DEFAULT)." qui ont été restreints à sa direction (l'ajout / modification associera le mot clé automatiquement à sa direction).");
if (!defined("_ADD_TAG_CONFIRM"))
    define("_ADD_TAG_CONFIRM", "Ce mot sera ajouté en tant que ".strtolower(_TAG_DEFAULT).".");
