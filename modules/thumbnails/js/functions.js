function showThumb(divName, resId, collId)
{
    var path_manage_script = 'index.php?module=thumbnails&page=ajaxShowThumb&display=true';

    new Ajax.Request(path_manage_script,
    {
        method:'post',
        parameters: { resId : resId, collId : collId},
        onSuccess: function(answer) {
            eval("response = "+answer.responseText);
            $(divName+resId).innerHTML = response.toShow;
        }
    });
}
