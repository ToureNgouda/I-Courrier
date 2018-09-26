/**
 * Fill the Folder field in indexing page (basing on the subfolder field value)
 *
 * @param path_to_script String Path to the Ajax script
 **/

function valid_viewfolder(url)
{
	var type_choice = '';
	var user_id = '';
	var datestart = '';
	var datefin = '';
	var error = '';
	var foldertype_item = $('foldertype');
	var usergroup_item = $('usergroup');
	var user_item = $('user');
	var period_item = $('period');
	if( foldertype_item && foldertype_item.checked)
	{
		type_choice = 'foldertype';
	}
	else if( usergroup_item && usergroup_item.checked)
	{
		type_choice = 'usergroup';
	}
	else if(user_item && user_item.checked)
	{
		type_choice = 'user';
		var user_id_item = $('user_id');
		if(user_id_item)
		{
			user_id = user_id_item.value;
		}
		if(user_id == '')
		{
			error = 'empty_user_id';
		}
	}
	else if(period_item && period_item.checked)
	{
		type_choice = 'period';
		var datestart_item = $('datestart');
		if(datestart_item)
		{
			datestart = datestart_item.value;
		}
		var datefin_item = $('datefin');
		if(datefin_item)
		{
			datefin = datefin_item.value;
		}
	} else {
		error = 'empty_type_choice';
	}

	if(type_choice  != '' && url && error == '' )
	{
		$j.ajax({
			url: url,
			type: 'POST',
			data: {
				type_report : type_choice,
				user : user_id,
				date_start : datestart,
				date_fin : datefin
			},
			success: function (answer) {
				var div_to_fill = $j('#result_folderviewstat');
				div_to_fill.html(answer);
			},
			error: function (error) {
				alert(error);

			}
		});
	}
}

/**
 * Gets the indexes for a given folder type and fills a div with it
 *
 * @param url String Url to the Ajax script
 * @param foldertype String Folder type identifier
 **/
function get_folder_index(url, foldertype, id_div)
{
	if(url && foldertype) {		
		$j.ajax({
			url: url,
			type: 'POST',
			data: { 
				foldertype_id : foldertype
			},
			success: function (answer) {
				var div_to_fill = $j('#'+id_div);
				if(div_to_fill) {						
					div_to_fill.html ( answer);
				}
			},
			error: function (error) {
				alert(error);
			}
  		});

	}
}

function search_change_coll(url, id_coll)
{
    if(url && id_coll)
    {
        var search_div = $j('#folder_search_div');
        if(search_div)
        {
            search_div.css("display","block");
        }

        var indexes_div = $j('#opt_indexes');
        if(indexes_div);
        {
            indexes_div.html('');
        }
    
        $j.ajax({
			url: url,
			type: 'POST',
			data: {
				coll_id : id_coll
			},
			success: function (answer) {
				var select_item = $j('#foldertype_id');
				if(select_item) {
					select_item.html(answer);
				}
			},
			error: function (error) {
				alert(error);
			}

    	});

    }
}



function get_ft_opt_index(url)
{
	if(url) {		 
		$j.ajax({
			url: url,
			type: 'POST',
			data: {},
			success: function (answer) {
				var div_to_fill = $j('#opt_index');
				if(div_to_fill) {
					div_to_fill.html(answer);
				}

			},
			error: function (error) {
				alert(error);
			}

		});


    }
}

function checkSubFolder(folderId)
{
	if (folderId == "") {
		$j('#folder_dest_div').css("display","");
	} else {
		$j('#folder_dest_div').css("display","none");
	}

}


function tabClick (TabId){
        
    var AllTab = $j(".folder-tab");        
    AllTab.removeClass("folder-tab-open")        
    var doc = $j("#"+TabId);        
    doc.addClass("folder-tab-open");

    $j(".frame-targ").css('display','none');
    $j('#frame-'+TabId).css('display','block');

}

function triggerChosen(){
	$j('#folder').trigger('chosen:updated');
}