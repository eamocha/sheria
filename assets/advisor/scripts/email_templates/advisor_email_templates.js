var activeEmailTemplateId = '';
var emailTemplate = null;
var emailTemplateEditibleContent = null;
var tinymceInstance = null;
var tinymceSelector = 'textarea#advisor-email-template-content-editor';
var tinymceInstanceSelector = 'advisor-email-template-content-editor';

jQuery(document).ready(function () {
    getEmailTemplate(activeEmailTemplateId);
});

function initEditor(initialContent) {
    tinymce.init({
        height: 400,
        width: '98%',
        selector: tinymceSelector,
        images_upload_url: 'advisors/upload_email_image',
        relative_urls: false,
        remove_script_host: false,
        plugins: [
            'advlist autolink link image lists charmap print preview hr anchor pagebreak',
            'searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking',
            'table emoticons template paste help'
        ],
        menu: {
            edit: {
                title: 'Edit',
                items: 'undo redo | cut copy paste pastetext | selectall searchreplace'
            },
            insert: {
                title: 'Insert',
                items: 'image link charmap'
            },
            format: {
                title: 'Format',
                items: 'bold italic underline strikethrough superscript subscript | formats | removeformat'
            },
            table: {
                title: 'Table',
                items: 'inserttable tableprops deletetable | cell row column'
            }
        },
        paste_data_images: true,
        menubar: 'edit insert format table',
        setup: function (editor) {
            tinymceInstance = editor;
            editor.on("change", function () {
                setEmailTemplateEditibleContent(editor);
            })
            editor.on("keyup", function () {
                setEmailTemplateEditibleContent(editor);
            })
            editor.on('init', function (e) {
                editor.setContent(initialContent);
            });
        }
    });

}

function handleImageUpload(blobInfo, success, failure, progress) {
    console.log("tinymceInstance.getContent()");
    console.log(tinymceInstance.getContent());
    console.log(blobInfo.filename());
}


function getEmailTemplate(templateId) {
    jQuery.ajax({
        url: getBaseURL() + 'advisors/set_get_email_template/' + templateId,
        dataType: "json",
        error: defaultAjaxJSONErrorsHandler,
        success: function (response) {
            if (response.status) {
                emailTemplate = response.data;
                emailTemplateEditibleContent = emailTemplate['content'] ?? '';

                initEditor(emailTemplateEditibleContent);

                loadActiveEmailTemplate();
            }
        }
    });
}

function loadActiveEmailTemplate() {
    let emailTemplateContent = emailTemplate['static_content'];

    emailTemplateContent = emailTemplateContent.replace('[content]', emailTemplateEditibleContent);

    jQuery('.advisor-email-template-preview').html(emailTemplateContent);
}

function setEmailTemplateEditibleContent(inst) {
    emailTemplateEditibleContent = inst.getBody().innerHTML;

    loadActiveEmailTemplate();
}

function submitAdvisorEmailTemplate() {
    jQuery.ajax({
        url: getBaseURL() + 'advisors/set_get_email_template/' + activeEmailTemplateId,
        type: "POST",
        data: {
            content: tinymceInstance.getContent()
        },
        dataType: "json",
        error: defaultAjaxJSONErrorsHandler,
        success: function (response) {
            if (response.status) {
                emailTemplate = response.data;
                emailTemplateEditibleContent = emailTemplate['content'] ?? '';

                pinesMessage({ ty: 'success', m: _lang.feedback_messages.templateSavedSuccessfully });
                initEditor(emailTemplateEditibleContent);
                loadActiveEmailTemplate();
            }
        }
    });
}