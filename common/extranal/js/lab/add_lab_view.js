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
// Dynamic Hematology Histogram Curve Generator (Rayto RT-7600)
function generateWbcCurveSvg(total_wbc, neut_pct, lymph_pct, mono_pct, eos_pct, baso_pct) {
    total_wbc = parseFloat(String(total_wbc || 7500).replace(/,/g, '')) || 7500;
    neut_pct = parseFloat(neut_pct || 55) || 55;
    lymph_pct = parseFloat(lymph_pct || 35) || 35;
    var mid_pct = (parseFloat(mono_pct || 4) || 4) + (parseFloat(eos_pct || 3) || 3) + (parseFloat(baso_pct || 0) || 0);
    if (mid_pct <= 0) mid_pct = 10;

    var scale = Math.max(0.75, Math.min(1.30, total_wbc / 8500));
    var h_lymph = Math.max(14, Math.min(58, (lymph_pct / 65) * 48 * scale));
    var h_neut = Math.max(16, Math.min(62, (neut_pct / 70) * 52 * scale));
    var h_mid = Math.max(6, Math.min(28, (mid_pct / 18) * 22 * scale));

    var y1 = (72 - h_lymph).toFixed(1);
    var y2 = (72 - h_neut).toFixed(1);
    var yv = (72 - h_mid).toFixed(1);

    var d = "M 12 72 L 18 72 C 24 72, " + (34 - 5) + " " + y1 + ", 34 " + y1 + " C " + (34 + 6) + " " + y1 + ", " + (52 - 6) + " " + yv + ", 52 " + yv + " C " + (52 + 6) + " " + yv + ", " + (76 - 6) + " " + y2 + ", 76 " + y2 + " C " + (76 + 7) + " " + y2 + ", " + (76 + 20) + " 66, 112 72 L 118 72 L 12 72 Z";

    var svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 130 95" width="120" height="85">' +
        '<defs><pattern id="wbcHatchDynamic" width="2" height="4" patternUnits="userSpaceOnUse"><line x1="1" y1="0" x2="1" y2="4" stroke="#68727d" stroke-width="0.9"/></pattern></defs>' +
        '<rect x="0.5" y="0.5" width="129" height="94" fill="#ffffff" stroke="#777777" stroke-width="1"/>' +
        '<path d="' + d + '" fill="#9ba5b0" stroke="#333d47" stroke-width="1.5"/>' +
        '<path d="' + d + '" fill="url(#wbcHatchDynamic)"/>' +
        '<line x1="6" y1="72" x2="124" y2="72" stroke="#222222" stroke-width="1.2"/>' +
        '<text x="65" y="87" font-family="Arial, Helvetica, sans-serif" font-size="9.5" font-weight="bold" fill="#000000" text-anchor="middle" letter-spacing="0.5">WBC CURVE</text></svg>';

    return 'data:image/svg+xml;base64,' + btoa(svg);
}

function generatePltCurveSvg(plt_count, mpv, pdw) {
    plt_count = parseFloat(String(plt_count || 280000).replace(/,/g, '')) || 280000;
    mpv = parseFloat(mpv || 8.5) || 8.5;
    pdw = parseFloat(pdw || 13.5) || 13.5;

    var peak_x = Math.max(18.0, Math.min(36.0, 20.0 + ((mpv - 6.5) / 5.0) * 14.0));
    var plt_scale = Math.max(0.55, Math.min(1.35, plt_count / 300000));
    var h_plt = Math.max(16.0, Math.min(63.0, 52.0 * plt_scale));
    var peak_y = (72.0 - h_plt).toFixed(1);
    var tail_x = Math.max(65.0, Math.min(112.0, 75.0 + ((pdw - 12.0) / 6.0) * 18.0));

    var d = "M 12 72 C 14 72, " + (peak_x - 3.5).toFixed(1) + " " + (parseFloat(peak_y) + 10.0).toFixed(1) + ", " + peak_x.toFixed(1) + " " + peak_y + " C " + (peak_x + 5.0).toFixed(1) + " " + peak_y + ", " + (peak_x + 18.0).toFixed(1) + " 56, " + tail_x.toFixed(1) + " 69 C " + (tail_x + 12.0).toFixed(1) + " 71, 108 72, 118 72 L 12 72 Z";

    var svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 130 95" width="120" height="85">' +
        '<defs><pattern id="pltHatchDynamic" width="2" height="4" patternUnits="userSpaceOnUse"><line x1="1" y1="0" x2="1" y2="4" stroke="#68727d" stroke-width="0.9"/></pattern></defs>' +
        '<rect x="0.5" y="0.5" width="129" height="94" fill="#ffffff" stroke="#777777" stroke-width="1"/>' +
        '<path d="' + d + '" fill="#9ba5b0" stroke="#333d47" stroke-width="1.5"/>' +
        '<path d="' + d + '" fill="url(#pltHatchDynamic)"/>' +
        '<line x1="6" y1="72" x2="124" y2="72" stroke="#222222" stroke-width="1.2"/>' +
        '<text x="65" y="87" font-family="Arial, Helvetica, sans-serif" font-size="9.5" font-weight="bold" fill="#000000" text-anchor="middle" letter-spacing="0.5">PLT CURVE</text></svg>';

    return 'data:image/svg+xml;base64,' + btoa(svg);
}

function generateRbcCurveSvg(rbc_count, mcv, rdw_cv) {
    rbc_count = parseFloat(rbc_count || 5.0) || 5.0;
    mcv = parseFloat(mcv || 85.0) || 85.0;
    rdw_cv = parseFloat(rdw_cv || 13.5) || 13.5;

    var center_x = Math.max(42.0, Math.min(86.0, 64.0 + (mcv - 85.0) * 1.15));
    var half_width = Math.max(13.0, Math.min(26.0, 17.0 + (rdw_cv - 13.0) * 1.5));
    var rbc_scale = Math.max(0.65, Math.min(1.30, rbc_count / 5.2));
    var h_rbc = Math.max(24.0, Math.min(62.0, 54.0 * rbc_scale));
    var peak_y = (72.0 - h_rbc).toFixed(1);

    var left_base = Math.max(10.0, center_x - half_width * 1.6);
    var right_base = Math.min(120.0, center_x + half_width * 1.6);

    var d = "M 10 72 L " + left_base.toFixed(1) + " 72 C " + (left_base + 5.0).toFixed(1) + " 72, " + (center_x - half_width * 0.5).toFixed(1) + " " + peak_y + ", " + center_x.toFixed(1) + " " + peak_y + " C " + (center_x + half_width * 0.5).toFixed(1) + " " + peak_y + ", " + (right_base - 5.0).toFixed(1) + " 72, " + right_base.toFixed(1) + " 72 L 120 72 L 10 72 Z";

    var svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 130 95" width="120" height="85">' +
        '<defs><pattern id="rbcHatchDynamic" width="2" height="4" patternUnits="userSpaceOnUse"><line x1="1" y1="0" x2="1" y2="4" stroke="#68727d" stroke-width="0.9"/></pattern></defs>' +
        '<rect x="0.5" y="0.5" width="129" height="94" fill="#ffffff" stroke="#777777" stroke-width="1"/>' +
        '<path d="' + d + '" fill="#333d4b" stroke="#11161d" stroke-width="1.5"/>' +
        '<path d="' + d + '" fill="url(#rbcHatchDynamic)"/>' +
        '<line x1="6" y1="72" x2="124" y2="72" stroke="#222222" stroke-width="1.2"/>' +
        '<text x="65" y="87" font-family="Arial, Helvetica, sans-serif" font-size="9.5" font-weight="bold" fill="#000000" text-anchor="middle" letter-spacing="0.5">RBC CURVE</text></svg>';

    return 'data:image/svg+xml;base64,' + btoa(svg);
}

function updateEditorCbcCurves() {
    if (typeof tinymce === 'undefined' || !tinymce.get('editor')) return;
    var html = tinymce.get('editor').getContent();
    if (!html || (html.indexOf('CURVE') === -1 && html.indexOf('HAEMATOLOGY') === -1)) return;

    var extractVal = function(regexStr, defVal) {
        var reg = new RegExp('<td[^>]*>[^<]*?' + regexStr + '[^<]*?<\\/td>\\s*<td[^>]*>([\\s\\S]*?)<\\/td>', 'i');
        var m = html.match(reg);
        if (m && m[1]) {
            var clean = m[1].replace(/<[^>]*>/g, '').trim();
            if (clean !== '') return clean;
        }
        return defVal;
    };

    var wbc = extractVal('TOTAL\\s+WBC(?:\\s+COUNT)?', '10850');
    var neut = extractVal('Neutrophils?', '52');
    var lymph = extractVal('Lymphocytes?', '41');
    var mono = extractVal('Monocytes?', '4');
    var eos = extractVal('Eosinophils?', '3');
    var baso = extractVal('Basophils?', '0');

    var plt = extractVal('TOTAL\\s+PLATELET(?:\\s+COUNT)?', '314000');
    var mpv = extractVal('MPV', '7.3');
    var pdw = extractVal('PDW', '13.7');

    var rbc = extractVal('RBC\\s+COUNT', '6.19');
    var mcv = extractVal('MCV', '72.9');
    var rdw = extractVal('RDW\\s*-\\s*CV', '15.2');

    var wbcUri = generateWbcCurveSvg(wbc, neut, lymph, mono, eos, baso);
    var pltUri = generatePltCurveSvg(plt, mpv, pdw);
    var rbcUri = generateRbcCurveSvg(rbc, mcv, rdw);

    html = html.replace(/<img[^>]+alt=["']WBC CURVE["'][^>]*>/gi, '<img src="' + wbcUri + '" alt="WBC CURVE" width="120" height="85" style="display:block; margin:auto; border:none;" />');
    html = html.replace(/<img[^>]+alt=["']PLT CURVE["'][^>]*>/gi, '<img src="' + pltUri + '" alt="PLT CURVE" width="120" height="85" style="display:block; margin:auto; border:none;" />');
    html = html.replace(/<img[^>]+alt=["']RBC CURVE["'][^>]*>/gi, '<img src="' + rbcUri + '" alt="RBC CURVE" width="120" height="85" style="display:block; margin:auto; border:none;" />');

    tinymce.get('editor').setContent(html);
}

tinymce.init({
    selector: '#editor', 
    plugins: 'preview importcss searchreplace autolink autosave save directionality code visualblocks visualchars fullscreen image link media template codesample table charmap pagebreak nonbreaking anchor insertdatetime advlist lists wordcount help charmap quickbars emoticons',
    menubar: 'file edit view insert format tools table help',
    toolbar: 'undo redo | updatecurves | bold italic underline | fontselect fontsizeselect formatselect | alignleft aligncenter alignright alignjustify | outdent indent |  numlist bullist | forecolor backcolor removeformat | pagebreak | charmap emoticons | fullscreen  preview save | insertfile image media template link anchor codesample | ltr rtl',
    branding: false,
    promotion: false,
    extended_valid_elements: 'img[class|src|border=0|alt|title|hspace|vspace|width|height|align|onmouseover|onmouseout|name|style],svg[*],path[*],g[*],polygon[*],line[*],polyline[*],rect[*],circle[*],text[*],textPath[*],style[*]',
    content_style: 'body { font-family: Arial, Helvetica, sans-serif; font-size: 13px; color: #111; } table { border-collapse: collapse; width: 100%; }',
    setup: function(editor) {
        editor.ui.registry.addButton('updatecurves', {
            text: '⚡ Update Curves',
            tooltip: 'Dynamically recalculate WBC, PLT, RBC curves from table results',
            onAction: function() {
                updateEditorCbcCurves();
            }
        });
    }
});

$(document).ready(function() {
    $('form#addLabForm, form.addLabForm, form[action*="lab/addLab"]').on('submit', function() {
        try {
            updateEditorCbcCurves();
        } catch(e) {}
    });
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