
 function show_config_action( id_action, inside_scrollbox, show_when_disabled)
 {
    var chkbox = $j('#checkbox_'+id_action)

    if(chkbox && (chkbox.prop('disabled') == false || show_when_disabled == true) )
    {
        var main_div = $j('#config_actions')[0];

        if(main_div != null){
          
            var childs = main_div.childNodes;
    
            if(chkbox && chkbox.disabled == true && show_when_disabled == true){
                var actions_uses = $j('#'+id_action+'_actions_uses')[0];
                actions_uses.style.display = 'none';
            }

            for(i=0; i < childs.length-1; i++)
            {
                if(childs[i].id=='action_'+id_action){
                    childs[i].style.display = 'block';
                    
                    if($j("#"+id_action+"_entities_chosen_chosen")){
                        $j("#"+id_action+"_entities_chosen_chosen").width('95%');
                    } 

                    if($j("#"+id_action+"_usersentities_chosen_chosen")){
                        $j("#"+id_action+"_usersentities_chosen_chosen").width('95%');
                    }

                }
                else
                {
                    childs[i].style.display = 'none';
                }
            }
        }
    }


 }

 function manage_actions(id, inside_scrollbox, path_manage_script)
 {
    var hide_other_actions = false;
    
    $j.ajax({
        url: path_manage_script,
        type: 'POST',
        data: {
           id_action : id
        },
        success:function(answer)
        {
            eval('response='+answer);            

            if(response.status == 1 )
            {
                hide_other_actions = true;
            }
             
            var elem = $j('#allowed_basket_actions')[0].getElementsByTagName('input');
            
            for(var i=0; i < elem.length; i++)
            {

                var id_action = elem[i].id.substring(9);
                var label_action = $j('#label_'+id_action);
                var param_action = $j('#link_'+id_action);

                if(label_action)
                {
                    label_action.css('fontWeight','normal');
                    label_action.css('fontStyle','normal');
                }
                if(param_action)
                {
                    param_action.css('display','inline');
                }
                if(hide_other_actions)
                {
                    if(elem[i].id == 'checkbox_'+id )
                    {
                        elem[i].checked = false;
                        elem[i].disabled = true;
                        if(label_action)
                        {
                            label_action.css('fontWeight','bold');
                        }
                    }
                    else
                    {
                        elem[i].disabled = true;
                        if(label_action)
                        {
                            label_action.css('fontStyle','italic');
                        }
                        if(param_action)
                        {
                            param_action.css('display','none');
                        }
                    }
                }
                else
                {
                    if(elem[i].id == 'checkbox_'+id )
                    {
                        elem[i].checked = false;
                        elem[i].disabled = true;
                        if(label_action)
                        {
                            label_action.css('fontWeight','bold');
                        }
                    }
                    else
                    {
                        elem[i].disabled = false;
                    }

                }
            }
        },
        error: function (error) {
            alert(error);
        }

    });
        
    var main_div = $j('#config_actions')[0];
    if(main_div != null)
    {  
        var childs = main_div.childNodes;

        for(i=0; i < childs.length-1; i++)
        {
            childs[i].style.display ='none';
        }
    }
 }

 function autocomplete(indice_max, path_to_script)
 {
    for (var i=0;i<indice_max;i++)
    {
        new Ajax.Autocompleter("user_"+i, "options_"+i, path_to_script, {
            method:'get',
            paramName:'UserInput',
            parameters: 'baskets_owner='+$('baskets_owner').value, //si il y a besoin d'autres paramÃ¨tres
            minChars: 2,
            indicator: "indicator_"+i
        });
    }
}


function check_form_baskets(id_form)
{
    var form = $(id_form);
    var reg_user = new RegExp("^.+, .+ (.+)$");
    if(typeof(form) != 'undefined')
    {
        var found = false;
        var elems = document.getElementsByTagName('INPUT');
        for(var i=0; i<elems.length;i++)
        {
            if(elems[i].type == "text" && elems[i].id.indexOf('user_') >= 0)
            {
                if(elems[i].value != '')
                {
                    if(reg_user.test(elems[i].value))
                    {
                        found = true;
                    }
                    else
                    {
                        return 2;   // user field not in the right format
                    }
                }
            }
            else if(elems[i].type == "hidden")
            {
                if(elems[i].id == "baskets_owner" && elems[i].value == '')
                {
                    return 3;    // baskets_owner field is empty
                }

            }
        }
        if(found == true)
        {
            return 1;
        }
        else
        {
            return 4; // All user fields are empty
        }
    }
    else
    {
        return 5; // Error with the form id
    }

}

function check_form_baskets_secondary(id_form)
{
    var form = $(id_form);
    if (typeof(form) != 'undefined') {
        var found = false;
        var elems = document.getElementsByTagName('INPUT');
        for (var i=0; i<elems.length;i++) {
            if (elems[i].type == "checkbox") {
                if(elems[i].checked == true)
                {
                    found=true;
                }
            }
        }
        if (found == true) {
            return 1;
        } else {
            return 4; // no basket checked
        }
    } else {
        return 5; // Error with the form id
    }
}

/**
 * Validates the groupbasket popup form, selects all list options ending with _chosen (type select_multiple) to avoid missing elements
 *
 * @param id_form String Search form identifier
 **/
function valid_actions_param(id_form)
{
    var frm = $(id_form);
    var selects = frm.getElementsByTagName('select'); //Array
    for(var i=0; i< selects.length;i++)
    {
        if(selects[i].multiple && selects[i].id.indexOf('_chosen') >= 0 && selects[i].attributes.selected)
        {
            selectall_ext(selects[i].id);
        }
    }
}

function simpleAjaxReturn(url){
    new Ajax.Request(url,
    {
        method:'post',
        onSuccess: function(answer){
            window.opener.location.href = 'index.php?module=basket&page=manage_basket_order&mode=reload';
        }
    });
}
