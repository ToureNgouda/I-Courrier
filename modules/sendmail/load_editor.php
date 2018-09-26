<?php  
?>
<!-- tinyMCE -->
<script type="text/javascript">
    tinymce.baseURL = "../../node_modules/tinymce";
    tinymce.suffix = '.min';
    tinymce.init({
        selector: "textarea#body_from_html",
        statusbar : false,
        language : "fr_FR",
        language_url: "tools/tinymce/langs/fr_FR.js",
        height : "150",
        plugins: [
                 "advlist autolink link lists charmap print preview hr",
                 "searchreplace visualblocks visualchars code fullscreen insertdatetime nonbreaking",
                 "save table contextmenu directionality paste textcolor"
        ],
        external_plugins: {
            'bdesk_photo': "../../apps/maarch_entreprise/tools/tinymce/bdesk_photo/plugin.min.js"
        },
        toolbar: "undo redo | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | preview fullpage | forecolor backcolor", 
        //save_callback : "customSave",
        paste_data_images: true,
        images_upload_handler: function (blobInfo, success, failure) {
            success("data:" + blobInfo.blob().type + ";base64," + blobInfo.base64());
        }
    });

    // Custom save callback, gets called when the contents is to be submitted
    function customSave(id, content) {
        alert(id + "=" + content);
    }
</script>
<!-- /tinyMCE -->
