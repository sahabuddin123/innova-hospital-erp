"use strict";
// var myEditor1;
// var myEditor2;
// var myEditor3;
// $(document).ready(function () {

//     ClassicEditor
//         .create(document.querySelector('#editor1'))
//         .then(editor1 => {
//             editor1.ui.view.editable.element.style.height = '200px';
//             myEditor1 = editor1;
//         })
//         .catch(error => {
//             console.error(error);
//         });

//     ClassicEditor
//         .create(document.querySelector('#editor2'))
//         .then(editor2 => {
//             editor2.ui.view.editable.element.style.height = '200px';
//             myEditor2 = editor2;
//         })
//         .catch(error => {
//             console.error(error);
//         });

//     ClassicEditor
//         .create(document.querySelector('#editor3'))
//         .then(editor3 => {
//             editor3.ui.view.editable.element.style.height = '200px';
//             myEditor3 = editor3;
//         })
//         .catch(error => {
//             console.error(error);
//         });


// });
tinymce.init({
    selector: '#editor1',
    height: 200,
    plugins: 'preview importcss searchreplace autolink autosave save directionality code visualblocks visualchars fullscreen image link media template codesample table charmap pagebreak nonbreaking anchor insertdatetime advlist lists wordcount help charmap quickbars emoticons',
    menubar: 'file edit view insert format tools table help',
    toolbar: 'undo redo | bold italic underline | fontselect fontsizeselect formatselect | alignleft aligncenter alignright alignjustify | outdent indent |  numlist bullist | forecolor backcolor removeformat | pagebreak | charmap emoticons | fullscreen  preview save | insertfile image media template link anchor codesample | ltr rtl',
    branding: false,
    promotion: false
});
tinymce.init({
    selector: '#editor2',
    height: 200,
    plugins: 'preview importcss searchreplace autolink autosave save directionality code visualblocks visualchars fullscreen image link media template codesample table charmap pagebreak nonbreaking anchor insertdatetime advlist lists wordcount help charmap quickbars emoticons',
    menubar: 'file edit view insert format tools table help',
    toolbar: 'undo redo | bold italic underline | fontselect fontsizeselect formatselect | alignleft aligncenter alignright alignjustify | outdent indent |  numlist bullist | forecolor backcolor removeformat | pagebreak | charmap emoticons | fullscreen  preview save | insertfile image media template link anchor codesample | ltr rtl',
    branding: false,
    promotion: false
});
tinymce.init({
    selector: '#editor3',
    height: 200,
    plugins: 'preview importcss searchreplace autolink autosave save directionality code visualblocks visualchars fullscreen image link media template codesample table charmap pagebreak nonbreaking anchor insertdatetime advlist lists wordcount help charmap quickbars emoticons',
    menubar: 'file edit view insert format tools table help',
    toolbar: 'undo redo | bold italic underline | fontselect fontsizeselect formatselect | alignleft aligncenter alignright alignjustify | outdent indent |  numlist bullist | forecolor backcolor removeformat | pagebreak | charmap emoticons | fullscreen  preview save | insertfile image media template link anchor codesample | ltr rtl',
    branding: false,
    promotion: false
});
$(document).ready(function () {
    "use strict";
    var selected = $('#my_select1_disabled').find('option:selected');
    var unselected = $('#my_select1_disabled').find('option:not(:selected)');
    selected.attr('data-selected', '1');
    $.each(unselected, function (index, value1) {
        "use strict";
        if ($(this).attr('data-selected') == '1') {
            var value = $(this).val();
            var res = value.split("*");

            var id = res[0];
            $('#med_selected_section-' + id).remove();


        }
    });


    var count = 0;
    $.each($('select.medicinee option:selected'), function () {
        "use strict";
        var value = $(this).val();
        var res = value.split("*");

        var id = res[0];

        var med_id = res[0];
        var med_name = res[1];
        var dosage = $(this).data('dosage');
        var frequency = $(this).data('frequency');
        var days = $(this).data('days');
        var instruction = $(this).data('instruction');
        if ($('#med_id-' + id).length) {

        } else {

            $(".medicine").append('<section id="med_selected_section-' + med_id + '" class="med_selected col-md-12" style="background-color: #f2f2f2; padding: 10px; margin-bottom: 10px;">\n\
         <div class="form-group medicine_sect">\n\
            <div class="row">\n\
                <div class="col-md-3">\n\
                    <label>Medicine</label>\n\
                    <input class="medi_div form-control form-control-lg" style="font-weight: bold;" readonly name="med_id[]" value="' + med_name + '" placeholder="" required>\n\
                    <input type="hidden" id="med_id-' + id + '" class="medi_div" name="medicine[]" value="' + med_id + '" placeholder="" required>\n\
                </div>\n\
                <div class="col-md-3">\n\
                    <label>Dosage</label>\n\
                    <input class="state medi_div form-control form-control-lg" name="dosage[]" value="' + dosage + '" placeholder="100 mg" required>\n\
                </div>\n\
                <div class="col-md-3">\n\
                    <label>Frequency</label>\n\
                    <input class="potency medi_div sale form-control form-control-lg" id="salee' + count + '" name="frequency[]" value="' + frequency + '" placeholder="1 + 0 + 1" required>\n\
                </div>\n\
                <div class="col-md-3">\n\
                    <label>Days</label>\n\
                    <input class="potency medi_div quantity form-control form-control-lg" id="quantity' + count + '" name="days[]" value="' + days + '" placeholder="7 days" required>\n\
                </div>\n\
            </div>\n\
            <div class="row">\n\
                <div class="col-md-12">\n\
                    <label>Instruction</label>\n\
                    <input class="potency medi_div quantity form-control form-control-lg" id="quantity' + count + '" name="instruction[]" value="' + instruction + '" placeholder="After Food" required>\n\
                </div>\n\
            </div>\n\
        </div>\n\
        <div class="del col-md-1"></div>\n\
    </section>');
        }
    });
});



$(document).ready(function () {
    "use strict";
    $(".medicine_div").on("change", ".medicinee", function () {

        "use strict";
        var count = 0;


        var selected = $('#my_select1_disabled').find('option:selected');
        var unselected = $('#my_select1_disabled').find('option:not(:selected)');
        selected.attr('data-selected', '1');
        $.each(unselected, function (index, value1) {
            "use strict";
            if ($(this).attr('data-selected') == '1') {
                var value = $(this).val();
                var res = value.split("*");

                var id = res[0];
                $('#med_selected_section-' + id).remove();


            }
        });

        $.each($('select.medicinee option:selected'), function () {
            "use strict";
            var value = $(this).val();
            var res = value.split("*");

            var id = res[0];

            var med_id = res[0];
            var med_name = res[1];


            if ($('#med_id-' + id).length) {

            } else {


                $(".medicine").append('\n\
                <section class="med_selected" id="med_selected_section-' + med_id + '" style="background-color: #f2f2f2; padding: 10px; margin-bottom: 10px;">\n\
                    <div class="form-group medicine_sect">\n\
                        <div class="row">\n\
                            <div class="col-md-3">\n\
                                    <label> Medicine </label>\n\
                                    <input class = "medi_div form-control form-control-lg" style="font-weight: bold;" readonly name = "med_id[]" value = "' + med_name + '" placeholder="" required>\n\
                                    <input type="hidden" class = "medi_div" id="med_id-' + id + '" name = "medicine[]" value = "' + med_id + '" placeholder="" required>\n\
                            </div>\n\
                            <div class="col-md-3">\n\
                                    <label>Dosage </label>\n\
                                    <input class = "state medi_div form-control form-control-lg" name = "dosage[]" value = "" placeholder="100 mg" required>\n\
                            </div>\n\
                            <div class="col-md-3">\n\
                                    <label>Frequency</label>\n\
                                    <input class = "potency medi_div sale form-control form-control-lg" id="salee' + count + '" name = "frequency[]" value = "" placeholder="1 + 0 + 1" required>\n\
                            </div>\n\
                            <div class="col-md-3">\n\
                                <label>Days </label>\n\
                                <input class = "potency medi_div quantity form-control form-control-lg" id="quantity' + count + '" name = "days[]" value = "" placeholder="7 days" required>\n\
                            </div>\n\
                        </div>\n\
                        <div class="row">\n\
                            <div class=col-md-12 d-flex flex-column>\n\
                                <label>Instructions</label>\n\
                                <input class = "potency medi_div quantity form-control form-control-lg" id="quantity' + count + '" name = "instruction[]" value = "" placeholder="After Food" required>\n\
                            </div>\n\
                        </div>\n\
                        </div>\n\
                    <div class="del col-md-1"></div>\n\
                </section>'
                );
            }
        });
    });
});


$(document).ready(function () {
    "use strict";
    $("#patientchoose").select2({
        placeholder: select_patient,
        allowClear: true,
        ajax: {
            url: 'patient/getPatientinfo',
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
    $("#doctorchoose").select2({
        placeholder: select_doctor,
        allowClear: true,
        ajax: {
            url: 'doctor/getDoctorinfo',
            type: "post",
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    searchTerm: params.term
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
    $("#doctorchoose1").select2({
        placeholder: select_doctor,
        allowClear: true,
        ajax: {
            url: 'doctor/getDoctorInfo',
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

$(document).ready(function () {
    "use strict";
    $('#my_select1').select2({
        multiple: true,
        placeholder: select_medicine,
        allowClear: true,
        closeOnSelect: true,
        ajax: {
            url: 'medicine/getMedicinenamelist',
            dataType: 'json',
            type: "post",
            delay: 250,
            data: function (params) {
                return {
                    searchTerm: params.term, // search term
                    page: params.page
                };
            },
            processResults: function (data, params) {

                params.page = params.page || 1;

                return {
                    results: data,
                    newTag: true,
                    pagination: {
                        more: (params.page * 1) < data.total_count
                    }
                };
            },
            cache: true
        },
    });
});

$(document).ready(function () {
    "use strict";
    $("#my_select1_disabled").select2({
        placeholder: select_medicine,
        multiple: true,
        allowClear: true,
        ajax: {
            url: 'medicine/getMedicineListForSelect2',
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