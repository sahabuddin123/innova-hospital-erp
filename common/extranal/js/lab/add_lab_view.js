"use strict";
$(document).ready(function () {
    "use strict";
    $('.pos_client').hide();
    $(document.body).on('change', '#pos_select', function () {
        "use strict";
        var v = $("select.pos_select option:selected").val()
        if (v === 'add_new') {
            $('.pos_client').show();
        } else {
            $('.pos_client').hide();
        }
    });

});

$(document).ready(function () {
    "use strict";
    $('.pos_doctor').hide();
    $(document.body).on('change', '#add_doctor', function () {
        "use strict";
        var v = $("select.add_doctor option:selected").val()
        if (v === 'add_new') {
            $('.pos_doctor').show();
        } else {
            $('.pos_doctor').hide();
        }
    });

});


// $(document).ready(function () {
//     "use strict";
//     $(document.body).on('change', '#template', function () {
//         "use strict";
//         var iid = $("select.template option:selected").val();
//         $.ajax({
//             url: 'lab/getTemplateByIdByJason?id=' + iid,
//             method: 'GET',
//             data: '',
//             dataType: 'json',
//             success: function (response) {
//                 "use strict";
//                 var data = myEditor.getData();
//                 if (response.template.template != null) {
//                     var data1 = data + response.template.template;
//                 } else {
//                     var data1 = data;
//                 }
//                 myEditor.setData(data1)
//             }
//         })
//     });
// });


$(document).ready(function () {
    "use strict";
    $(document.body).on('change', '#template1', function () {
        "use strict";
        var iid = $("select.template option:selected").val();
        if (!iid) {
            return;
        }
        var lab_id = $('input[name="id"]').val() || '';
        $.ajax({
            url: 'lab/getTemplateByIdByJason?id=' + iid + '&lab_id=' + lab_id,
            method: 'GET',
            data: '',
            dataType: 'json',
            success: function (response) {
                if (response && response.template && response.template.template != null) {
                    if (typeof tinymce !== 'undefined' && tinymce.get('editor')) {
                        tinymce.get('editor').setContent(response.template.template);
                    } else {
                        $('#editor').val(response.template.template);
                    }
                    try {
                        localStorage.setItem('lifecare_cached_template_' + iid, response.template.template);
                    } catch (e) {}
                }  
            },
            error: function () {
                // Offline fallback: load from cached template if available
                try {
                    var cachedHtml = localStorage.getItem('lifecare_cached_template_' + iid);
                    if (cachedHtml) {
                        if (typeof tinymce !== 'undefined' && tinymce.get('editor')) {
                            tinymce.get('editor').setContent(cachedHtml);
                        } else {
                            $('#editor').val(cachedHtml);
                        }
                    }
                } catch (e) {}
            }
        });
    });
});


// $(document).ready(function () {
//     "use strict";
//     $(document.body).on('change', '#template', function () {
//         "use strict";
//         var iid = $("select.template option:selected").val();
//         $.ajax({
//             url: 'lab/getTemplateByIdByJason?id=' + iid,
//             method: 'GET',
//             data: '',
//             dataType: 'json',
//             success: function (response) {
//                 "use strict";
//                 var data = myEditor.getData();
//                 if (response.template.template != null) {
//                     //  var data1 = data + response.template.template;
//                     myEditor.model.change(writer => {
//                         var html = response.template.template;
//                         var viewFragment = myEditor.data.processor.toView(html);
//                         var modelFragment = myEditor.data.toModel(viewFragment);

//                         var insertPosition = myEditor.model.document.selection.getFirstPosition();

//                         myEditor.model.insertContent(modelFragment, insertPosition);
//                     });
//                 } else {
//                     var data1 = data;
//                 }
//                 myEditor.setData(data1)
//             }
//         })
//     });
// });

$(document).ready(function () {
    "use strict";
    $(document.body).on('change', '#macro', function () {
        "use strict";
        var iid = $("select.macro option:selected").val();
        $.ajax({
            url: 'macro/getMacroByIdByJason?id=' + iid,
            method: 'GET',
            data: '',
            dataType: 'json',
            success: function (response) {
                "use strict";
                var data = myEditor.getData();
                // if (response.macro.description != null) {
                //     //  var data1 = response.macro.description;
                //     // data1 = data.insertHtml(response.macro.description);
                //     myEditor.model.change(writer => {
                //         var html = response.macro.description;
                //         var viewFragment = myEditor.data.processor.toView(html);
                //         var modelFragment = myEditor.data.toModel(viewFragment);

                //         var insertPosition = myEditor.model.document.selection.getFirstPosition();

                //         myEditor.model.insertContent(modelFragment, insertPosition);
                //     });
                    if (response.macro.description != null) {
                        tinymce.get('editor').insertContent(response.macro.description);
                    }
                // }
                //  else {
                //     var data1 = data;
                // }
                // myEditor.insertHtml(data1)
            }
        })
    });
});











$(document).ready(function () {
    "use strict";
    $("#pos_select").select2({
        placeholder: select_patient,
        allowClear: true,
        ajax: {
            url: 'patient/getPatientinfoWithAddNewOption',
            type: "post",
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    searchTerm: params.term // search term
                };
            },
            processResults: function (response) {
                return {
                    results: response
                };
            },
            cache: true
        }

    });

    $("#add_doctor").select2({
        placeholder: select_doctor,
        allowClear: true,
        ajax: {
            url: 'doctor/getDoctorWithAddNewOption',
            type: "post",
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    searchTerm: params.term // search term
                };
            },
            processResults: function (response) {
                return {
                    results: response
                };
            },
            cache: true
        }

    });

});
tinymce.init({
    selector: '#editor', 
    plugins: 'preview importcss searchreplace autolink autosave save directionality code visualblocks visualchars fullscreen image link media template codesample table charmap pagebreak nonbreaking anchor insertdatetime advlist lists wordcount help charmap quickbars emoticons',
    menubar: 'file edit view insert format tools table help',
    toolbar: 'undo redo | bold italic underline | fontselect fontsizeselect formatselect | alignleft aligncenter alignright alignjustify | outdent indent |  numlist bullist | forecolor backcolor removeformat | pagebreak | charmap emoticons | fullscreen  preview save | insertfile image media template link anchor codesample | ltr rtl',
    branding: false,
    promotion: false
  });
// var myEditor;
// $(document).ready(function () {

//     ClassicEditor
//             .create(document.querySelector('#editor'))
//             .then(editor => {
//                 editor.ui.view.editable.element.style.height = '300px';
//                 myEditor = editor;
//               editor.model.document.on('change:data', (evt, data) => {
//                   let text = editor.getData();
//                   text = text.replaceAll("<p>", "");
//                   text = text.replaceAll("</p>", "");
////                   text = text.replaceAll("<strong>", "");
////                   text = text.replaceAll("</strong>", "");
////                   text = text.replaceAll("<i>", "");
////                   text = text.replaceAll("</i>", "");
////                   text = text.replaceAll("<b>", "");
////                   text = text.replaceAll("</b>", "");
////                   text = text.replaceAll("<ul>", "");
////                   text = text.replaceAll("</ul>", "");
////                   text = text.replaceAll("<li>", "");
////                   text = text.replaceAll("</li>", "");
////                   text = text.replaceAll("<ol>", "");
////                   text = text.replaceAll("</ol>", "");
////                   text = text.replaceAll("<blockquote>", "");
////                   text = text.replaceAll("</blockquote>", "");
////                   text = text.replaceAll("<a", "");
////                   text = text.replaceAll("</a>", "");
////                   text = text.replaceAll("&nbsp;", "");
//                   let arr = text.split(" ");
//                   console.log(arr[arr.length - 1]);
//                   axios.get('macro/checkMarcoExists?word='+arr[arr.length-1])
//                           .then(response => {
//                               if(response.data) {
//                                   arr[arr.length-1] = response.data;
//                                   let finalText = arr.join(" ");
//                                   myEditor.setData(finalText)
//                               }
//                               console.log(response.data);
//                           })
//                });
// editor.model.document.on('change:data', (evt, data) => {
//     //let html = $('.ck-restricted-editing_mode_standard').text();  
//     //let html = $('.ck-restricted-editing_mode_standard').html();
//     //$('#report').val(html);
// //    let text = editor.getData();
// //    $('#report').val(text);
// })
//             })
//             .catch(error => {
//                 console.error(error);
//             });

// });