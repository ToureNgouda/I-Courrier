function showThumb(divName, resId, collId)
{
    var path_manage_script = 'index.php?module=thumbnails&page=ajaxShowThumb&display=true';
    /*new Ajax.Request(path_manage_script,
    {
        method:'post',
        parameters: { resId : resId, collId : collId},
        onSuccess: function(answer) {
            eval("response = "+answer.responseText);
            $(divName+resId).innerHTML = response.toShow;
        }
    });*/
    $j.ajax({
        url: path_manage_script,
        type: 'POST',
        data: {
            resId : resId,
            collId : collId
        },
        success: function(answer)
        {
            eval("response = "+answer);
            if(response.status==0){
                $j('#'+divName+resId).html(response.toShow);
            }
        },
        error: function(error)
        {
            alert(error);
        }
    });
}