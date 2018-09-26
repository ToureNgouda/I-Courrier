**Récap des fonctionnalités :** 

    Possibilité de rattacher un document à un courrier sans projet de réponse (activable via le config.xml)
    Possibilité de rattacher un document à plusieurs courriers (activable via le config.xml)
    Possibilité de clore le courrier cible (activable via le config.xml)
    Possibilité de supprimer le projet de réponse du courrier cible (activable via le config.xml)
    Si le courrier cible à plusieurs projet de réponses et/ou plusieurs courriers cible sont selectionnés il est possible de choisir le numéro de chrono attribué au document 
    (le titre et les contacts sont automatiquement attribué en fonction du choix) 
    

# INSTALLATION DU MODULE RECONCILIATION
# =====================================

**Copier le répertoire reconciliation dans modules.**

**Les fichiers suivants seront à modifier**

    custom/modules/templates/datasources/letterbox_attachment.php
    custom/core/xml/actions_pages.xml
    custom/apps/maarch_entreprise/definition_mail_categories.php
    custom/apps/maarch_entreprise/xml/IVS/data_types.xml
    custom/apps/maarch_entreprise/js/indexing.js 
    custom/modules/attachments/get_chrono_attachment.php
    custom/modules/attachments/xml/IVS/validation_rules.xml
    custom/modules/entities/js/functions.js
    apps/maarch_entreprise/lang/fr.php
    apps/maarch_entreprise/lang/en.php
    

**Modifier le fichier modules/template/datasources/letterbox_attachment.php**
Remplacement de la ligne 192 à 208 par : 
    
    $res_id_master = $doc['res_id'];
    $data = ''.$res_id_master.'#'.$chronoAttachment;
    require_once('apps/maarch_entreprise/tools/phpqrcode/qrlib.php');
    QRcode::png($data,$img_file_name, QR_ECLEVEL_L, 1);
    $myAttachment['chronoBarCode'] = $img_file_name;
    $datasources['attachments'][] = $myAttachment;

**Modifier le fichier custom/apps/maarch_entreprise/xml/config.xml**

Ajout de la catégorie suivante:

	<category>
		<id>attachment</id>
		<label>_ATTACHMENT</label>
	</category>
            
Ajout du module suivant:

	<MODULES>
		<moduleid>reconciliation</moduleid>
		<comment>_RECONCILIATION_COMMENT</comment>
	</MODULES>
	
**Modifier le fichier custom/apps/maarch_entreprise/xml/IVS/data_types.xml**

Ajout de l'énumération suivante ligne 83 : 
	
	<dataType name="category_list" base="string">
      <enumeration value="incoming" />
      <enumeration value="outgoing" />
      <enumeration value="internal" />
      <enumeration value="attachment" />	<-- Ligne à rajouter
    </dataType>
    
**Modifier le fichier custom/apps/maarch_entreprise/definitions_mail_categories.php**

Ajout du tableau de la catégorie attachment

	$_ENV['categories']['attachment'] = array ();
    $_ENV['categories']['attachment']['img_cat'] = '<i class="fa fa-paperclip fa-2x"></i>';
    $_ENV['categories']['attachment']['other_cases'] = array ();
    $_ENV['categories']['attachment']['other_cases']['chrono_number'] = array (
        'type_form' => 'integer',
        'type_field' => 'integer',
        'mandatory' => true,
        'label' => _CHRONO_NUMBER,
        'table' => 'none',
        'img' => 'compass',
        'modify' => false,
        'form_show' => 'textfield'
    );
    $_ENV['categories']['attachment']['type_id'] = array (
    	'type_form' => 'integer',
    	'type_field' => 'integer',
    	'mandatory' => true,
    	'label' => _DOCTYPE_MAIL,
    	'table' => 'res',
    	'img' => 'file',
    	'modify' => true,
    	'form_show' => 'select'
    );
    $_ENV['categories']['attachment']['destination'] = array (
    	'type_form' => 'string',
    	'type_field' => 'string',
    	'mandatory' => true,
    	'label' => _DEPARTMENT_EXP,
    	'table' => 'res',
    	'img' => 'sitemap',
    	'modify' => false,
    	'form_show' => 'textarea'
    );
	
**Modifier le fichier custom/apps/maarch_entreprise/js/indexing.js**

Affichage des éléments de liaison de fichier (à la fin de la fonction change_category)

	if(cat_id == 'attachment'){
        document.getElementById("attachment_tr").style.display='table-row';
        document.getElementById("attach_show").style.display='table-row';
    }
    
Ajout du tableau contenant les champs à afficher pour la catégorie attachment (fonction change_category)
	
	else if(cat_id == 'attachment'){
        var category = [
            {id:'doctype_mail', type:'label', state:'display'},
            {id:'doctype_res', type:'label', state:'hide'},
            {id:'priority_tr', type:'tr', state:'hide'},
            {id:'doc_date_label', type:'label', state:'hide'},
            {id:'doc_date_tr', type:'label', state:'hide'},
            {id:'mail_date_label', type:'label', state:'hide'},
            {id:'author_tr', type:'tr', state:'hide'},
            {id:'admission_date_tr', type:'tr', state:'hide'},
            {id:'contact_check', type:'tr', state:'hide'},
            {id:'nature_id_tr', type:'tr', state:'hide'},
            {id:'department_tr', type:'tr', state:'hide'},
            {id:'label_dep_dest', type:'label', state:'hide'},
            {id:'label_dep_exp', type:'label', state:'hide'},
            {id:'process_limit_date_use_tr', type:'tr', state:'hide'},
            {id:'process_limit_date_tr', type:'tr', state:'hide'},
            {id:'box_id_tr', type:'tr', state:'hide'},
            {id:'confidentiality_tr', type:'tr', state:'hide'},
            {id:'contact_choose_tr', type:'tr', state:'hide'},
            {id:'contact_choose_2_tr', type:'tr', state:'hide'},
            {id:'contact_choose_3_tr', type:'tr', state:'hide'},
            {id:'dest_contact_choose_label', type:'label', state:'hide'},
            {id:'exp_contact_choose_label', type:'label', state:'hide'},
            {id:'contact_id_tr', type:'tr', state:'hide'},
            {id:'dest_contact', type:'label', state:'display'},
            {id:'exp_contact', type:'label', state:'hide'},
            {id:'author_contact', type:'label', state:'hide'},
            {id:'type_multi_contact_external_icon', type:'label', state:'hide'},
            {id:'type_contact_internal', type:'radiobutton', state:typeContactInternal},
            {id:'type_contact_external', type:'radiobutton', state:typeContactExternal},
            {id:'type_multi_contact_external', type:'radiobutton', state:typeMultiContactExternal},
            {id:'folder_tr', type:'tr', state:'hide'},
            {id:'category_id_mandatory', type:'label', state:'hide'},
            {id:'type_id_mandatory', type:'label', state:'hide'},
            {id:'type_id_tr', type:'tr', state:'hide'},
            {id:'diff_list_tr', type:'tr', state:'hide'},
            {id:'priority_mandatory', type:'label', state:'hide'},
            {id:'doc_date_mandatory', type:'label', state:'hide'},
            {id:'author_mandatory', type:'label', state:'hide'},
            {id:'admission_date_mandatory', type:'label', state:'hide'},
            {id:'type_contact_mandatory', type:'label', state:'hide'},
            {id:'contact_mandatory', type:'label', state:'hide'},
            {id:'nature_id_mandatory', type:'label', state:'hide'},
            {id:'subject_mandatory', type:'label', state:'hide'},
            //{id:'subject_tr', type:'label', state:'hide'},
            {id:'destination_mandatory', type:'label', state:'hide'},
            {id:'process_limit_date_use_mandatory', type:'label', state:'hide'},
            {id:'process_limit_date_mandatory', type:'label', state:'hide'},
            {id:'chrono_number', type:'label', state:'hide'},
            {id:'chrono_number_tr', type:'tr', state:'hide'},
            {id:'chrono_number_mandatory', type:'label', state:'hide'},
            {id:'folder_mandatory', type:'label', state:'hide'},
            {id:'res_id_link', type:'label', state:'hide'},
            {id:'status', type:'tr', state:'hide'},
            {id:'add_multi_contact_tr', type:'tr', state:'hide'},
            {id:'show_multi_contact_tr', type:'tr', state:'hide'}
        ];
    }
    
Suppression à l'écran de certains champs inutiles au module Reconciliation

	if(cat_id == 'ged_doc'){
        document.getElementById("diff_list_tr").style.display = 'none';
    }else if(cat_id == 'attachment'){
        document.getElementById("subject_tr").style.display = 'none';
        document.getElementById("diff_list_tr").style.display = 'none';
    }else{
        document.getElementById("diff_list_tr").style.display = 'table-row';
        document.getElementById("subject_tr").style.display = 'table-row';
    }
    
Modification de la fonction change_contact_type ligne 1430. Il faut remplacer : 
    
    Element.setStyle(contact_id_tr, {display : 'table-row'});
    
Par :

	var cat_id = $(category_id).options[$(category_id).selectedIndex].value;
	if(cat_id != 'attachment') Element.setStyle(contact_id_tr, {display : 'table-row'});

Ajout d'une nouvelle fonction affiche_chrono_reconciliation(), basé sur affiche_chrono()
    
    function affiche_chrono_reconciliation(){
        new Ajax.Request('index.php?display=true&module=attachments&page=get_chrono_attachment',
        {
            method:'post',
            onSuccess: function(answer){
                eval("response = "+answer.responseText);
                var chrono_number = $('chrono_number');
                chrono_number.value=response.chronoNB;
                chrono_number.setAttribute('readonly','readonly');
                chrono_number.setAttribute('class','readonly');
                $('chrono_number_generate').style.display = 'none';
            }
        });
    }
    
**Modifier le fichier custom/modules/attachments/get_chrono_attachment.php - Ligne 65**

    if ($category_id == "incoming" || ($category_id == "outgoing" && $nb_attachment > 0))
    ==>
	if ($category_id == "incoming" || $category_id == 'attachment' || ($category_id == "outgoing" && $nb_attachment > 0) || (isset($_POST['type_id']) && $_POST['type_id'] == 'attachment'))
    
**Modifier le fichier custom/modules/attachments/xml/IVS/validation_rules.xml - Ligne 29**

    <validationRule name="get_chrono_attachment" extends="standardForm" mode="error">
    		<parameter name="module" type="identifier" />
    		<parameter name="type_id" type="identifier" /> <-- Ligne à rajouter
    	</validationRule>

**Modifier le fichier custom/core/xml/action_pages.xml**

Rajout de la page d'action Reconciliation 
	
	<ACTIONPAGE>
        <ID>reconciliation</ID>
        <LABEL>_RECONCILIATION</LABEL>
        <NAME>reconciliation</NAME>
        <ORIGIN>module</ORIGIN>
        <MODULE>reconciliation</MODULE>
        <FLAG_CREATE>false</FLAG_CREATE>
        <COLLECTIONS>
            <COLL_ID>letterbox_coll</COLL_ID>
        </COLLECTIONS>
    </ACTIONPAGE>
    
**Modifier le fichier apps/maarch_entreprise/lang/fr.php**

    if (!defined("_ATTACHMENT")) define("_ATTACHMENT", "Pièce jointe");
    
**Modifier le fichier apps/maarch_entreprise/lang/en.php**

    if (!defined("_ATTACHMENT")) define("_ATTACHMENT", "Attachment");

**Modifier le fichier custom/modules/entities/js/functions.js**

Rajouter après la ligne 55 :

    if(category === 'attachment'){
        diff_list_tr.style.display = 'none';
    }



**Coté fonctionnel**

    Créer une nouvelle action
    Description : Rattacher une réponse à un courrier
    Statut associé : Supprimé
    Page de résultat de l'action : Réconciliation
    Mot clé (paramètres système): Indexation
    Tracer l'action : Oui
    Action de dossier : Non
    Choisissez une ou plusieurs catégories associées : 
    (Si aucune catégorie sélectionnée alors l'action est valable pour toutes les catégories)
    Aucune

**Créer une nouvelle bannette**

    Collection : Collection des courriers
    Identifiant : AttBasket
    Bannette : Courriers à rapprocher
    Description : Bannette des courriers à rapprocher
    Vue sur la table : status = 'PJQUAL'

**Ajouter un groupe**

    Liste de résultats: Liste avec filtre et réponses
    Action par défaut sur la ligne; Rattacher une réponse à un courrier

**Editer un nouveau modèle et insérer une nouvelle image avec les dimensions voulues et mettre dans la description de cette dernière**
    
    [attachments.chronoBarCode;ope=changepic;tagpos=inside;adjust;unique]


# Installation du watcher
# =======================

    cd /opt/maarch
    git clone https://github.com/splitbrain/Watcher.git
    sudo apt-get install python python-pyinotify

**Création du service**

    sudo vi /etc/systemd/system/watcher.service
    [Unit]
    Description=Files watcher for maarch 
    After=apache2.service

    [Service]
    User=edissyum
    ExecStart=/opt/maarch/Watcher/watcher.py -c /opt/maarch/Watcher/watcher.ini start
    ExecStop=/opt/maarch/Watcher/watcher.py stop
    ExecRestart=/opt/maarch/Watcher/watcher.py -c /opt/maarch/Watcher/watcher.ini restart

    [Install]
    WantedBy=default.target

    sudo systemctl start watcher
    sudo systemctl enable watcher

**Installation de paquets supplémentaires**

    sudo apt install python-lxml
    sudo pip install beautifulsoup4
