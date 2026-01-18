import React, { useRef } from 'react';
import './APTinyMceEditor.scss';
import { Editor } from '@tinymce/tinymce-react';
import { CKEditor } from '@ckeditor/ckeditor5-react';
import ClassicEditor from '@ckeditor/ckeditor5-build-classic';

export default React.memo((props) => {
    const btnRef = useRef();
    
    return (
        <React.Fragment>
            <input
                id="my-file"
                type="file"
                name="my-file"
                style={{
                    display:"none"
                }}
                onChange=""
                accept="*"
            />
            {/* <Editor
                init={{
                    height: 300,
                    menubar: false,
                    statusbar: false,
                    branding: false,
                    resize: false,
                    relative_urls: false,
                    remove_script_host: false,
                    plugins: [
                        'advlist autolink lists link image charmap print preview anchor',
                        'searchreplace wordcount visualblocks code fullscreen',
                        'insertdatetime media table contextmenu paste code'
                    ],
                    link_assume_external_targets: true,
                    paste_text_sticky: true,
                    apply_source_formatting: true,
                    toolbar: 'undo redo | formatselect | bold italic underline | link | image insert insertfile file | removeformat | help',
                    paste_word_valid_elements: "b,strong,i,em,h1,h2,span,br,div,p",
                    paste_webkit_styles: "color font-size",
                    paste_remove_styles: false,
                    paste_retain_style_properties: "all",
                    block_formats: 'Paragraph=p;Huge=h4;Normal=h5;Small=h6;',
                    // // here we add custom filepicker only to Image dialog
                    // file_picker_types: 'file image',
                    // file_picker_callback: function(callback, value, meta) {
                    //     // File type
                    //     if (meta.filetype =="media" || meta.filetype =="image") {
                    
                    //     //   // Trigger click on file element
                    //     //   jQuery("#fileupload").trigger("click");
                    //     //   $("#fileupload").unbind('change');
                    //     //   // File selection
                    //     //   jQuery("#fileupload").on("change", function() {
                    //     //      var file = this.files[0];
                    //     //      var reader = new FileReader();
                                            
                    //     //      // FormData
                    //     //      var fd = new FormData();
                    //     //      var files = file;
                    //     //      fd.append("file",files);
                    //     //      fd.append('filetype',meta.filetype);
                    
                    //     //      var filename = "";
                                            
                    //     //      // AJAX
                    //     //  jQuery.ajax({
                    //     //         url: "upload.php",
                    //     //     type: "post",
                    //     //     data: fd,
                    //     //     contentType: false,
                    //     //     processData: false,
                    //     //     async: false,
                    //     //     success: function(response){
                    //     //        filename = response;
                    //     //         }
                    //     //      });
                                        
                    //     //     reader.onload = function(e) {
                    //     //         callback("upload/"+filename);
                    //     //     };
                    //     //     reader.readAsDataURL(file);
                    //     //    });
                    //     }
                    // },
                    formats: {
                        underline: {
                            inline: 'u',
                            exact: true
                        }
                    },
                    paste_preprocess: (plugin, args) => {
                        'tinymce'.activeEditor.windowManager.alert('Some of the pasted content will not stay with the same format');
                    },
                    // automatic_uploads: false,
                    // images_upload_url: Api.API_BASE_URL + '/testing-upload',
                    // images_upload_credentials: true,
                    // file_picker_types: 'file image media',
                    // file_picker_callback: (callback, value, meta) => {
                    //     var input = document.getElementById('my-file');
                        
                    //     input.click();
                     
                    //     // if (meta.filetype == 'image') {
                    //         input.onchange = () => {
                    //             var file = input.files[0];
                    //             var reader = new FileReader();
                                
                    //             reader.onload = (e) => {
                    //                 console.log('name',e.target.result);
                                    
                    //                 callback(e.target.result, {
                    //                     alt: file.name
                    //                 });
                    //             };

                    //             reader.readAsDataURL(file);
                    //         };
                    //     // }
                    // },
                    ...props.init
                }}
                onEditorChange={props.handleEditorChange}
                value={props.value}
            /> */}

<CKEditor 
                    editor={ ClassicEditor }
                    data={props.value}
                    onReady={ editor => {
                        // You can store the "editor" and use when it is needed.
                        console.log( 'Editor is ready to use!', editor );
                    } }
                    onChange={ ( event, editor ) => {
                        const data = editor.getData();
                        console.log( { event, editor, data } );
                        props.handleEditorChange(data);
                    } }
                    onBlur={ ( event, editor ) => {
                        console.log( 'Blur.', editor );
                    } }
                    onFocus={ ( event, editor ) => {
                        console.log( 'Focus.', editor );
                    } }
                />
        </React.Fragment>
    );
});
