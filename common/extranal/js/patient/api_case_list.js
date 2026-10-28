"use strict";
// var myEditor;
// var myEditor1;

// $(document).ready(function () {

//     ClassicEditor
//         .create(document.querySelector('#editor'))
//         .then(editor => {
//             editor.model.document.on('change:data', () => {
//                 $("#editor").html(editor.getData());;
//             });
//             editor.ui.view.editable.element.style.height = '200px';
//             myEditor = editor;
//         })
//         .catch(error => {
//             console.error(error);
//         });
//     ClassicEditor
//         .create(document.querySelector('#editor1'))
//         .then(editor1 => {
//             editor1.model.document.on('change:data', () => {
//                 $("#editor1").html(editor1.getData());;
//             });
//             editor1.ui.view.editable.element.style.height = '200px';
//             myEditor1 = editor1;

//         })

//         .catch(error => {
//             console.error(error);
//         });
// });
tinymce.init({
    selector: '.description',
    plugins: 'preview importcss searchreplace autolink autosave save directionality code visualblocks visualchars fullscreen image link media template codesample table charmap pagebreak nonbreaking anchor insertdatetime advlist lists wordcount help charmap quickbars emoticons',
    menubar: 'file edit view insert format tools table help',
    toolbar: 'undo redo | bold italic underline | fontselect fontsizeselect formatselect | alignleft aligncenter alignright alignjustify | outdent indent |  numlist bullist | forecolor backcolor removeformat | pagebreak | charmap emoticons | fullscreen  preview save | insertfile image media template link anchor codesample | ltr rtl',
    branding: false,
    promotion: false
});

tinymce.init({
    selector: '#editor2',
    plugins: 'preview importcss searchreplace autolink autosave save directionality code visualblocks visualchars fullscreen image link media template codesample table charmap pagebreak nonbreaking anchor insertdatetime advlist lists wordcount help charmap quickbars emoticons',
    menubar: 'file edit view insert format tools table help',
    toolbar: 'undo redo | bold italic underline | fontselect fontsizeselect formatselect | alignleft aligncenter alignright alignjustify | outdent indent |  numlist bullist | forecolor backcolor removeformat | pagebreak | charmap emoticons | fullscreen  preview save | insertfile image media template link anchor codesample | ltr rtl',
    branding: false,
    promotion: false
});

tinymce.init({
    selector: '#editor1',
    plugins: 'preview importcss searchreplace autolink autosave save directionality code visualblocks visualchars fullscreen image link media template codesample table charmap pagebreak nonbreaking anchor insertdatetime advlist lists wordcount help charmap quickbars emoticons',
    menubar: 'file edit view insert format tools table help',
    toolbar: 'undo redo | bold italic underline | fontselect fontsizeselect formatselect | alignleft aligncenter alignright alignjustify | outdent indent |  numlist bullist | forecolor backcolor removeformat | pagebreak | charmap emoticons | fullscreen  preview save | insertfile image media template link anchor codesample | ltr rtl',
    branding: false,
    promotion: false
});
$(".table").on("click", ".editbutton", function () {
    "use strict";
    // alert('hi');
    var iid = $(this).attr('data-id');
    $("#loader").show();
    $.ajax({
        url: 'patient/editMedicalHistoryByJason?id=' + iid,
        method: 'GET',
        data: '',
        dataType: 'json',
        success: function (response) {
            "use strict";
            var de = response.medical_history.date * 1000;
            var d = new Date(de);
            var da = d.getDate() + '-' + (d.getMonth() + 1) + '-' + d.getFullYear();
            $('.case_idd').text("Case No #" + response.medical_history.id);
            $('#medical_historyEditForm').find('[name="id"]').val(response.medical_history.id).end();
            $('#medical_historyEditForm').find('[name="date"]').val(da).end();

            $('#medical_historyEditForm').find('[name="title"]').val(response.medical_history.title).end();
            // myEditor.setData(response.medical_history.description);
            var option = new Option(response.patient.name + '-' + response.patient.id, response.patient.id, true, true);
            $('#medical_historyEditForm').find('[name="patient_id"]').append(option).trigger('change');

            $('#diagnosischoose1').empty().trigger('change');

            // Add and select diagnosis options
            if (response.diagnosis && response.diagnosis.length > 0) {
                response.diagnosis.forEach(function (diagnosis) {
                    // Create a new option using the diagnosis's id and text fields
                    var diagnosisOption = new Option(diagnosis.text, diagnosis.id, true, true);
                    $('#diagnosischoose1').append(diagnosisOption).trigger('change');
                });
            }

            $('#treatmentchoose1').empty().trigger('change');

            if (response.treatment && response.treatment.length > 0) {
                response.treatment.forEach(function (treatment) {
                    var treatmentOption = new Option(treatment.text, treatment.id, true, true);
                    $('#treatmentchoose1').append(treatmentOption).trigger('change');
                });
            }

            $('#symptomchoose1').empty().trigger('change');

            if (response.symptom && response.symptom.length > 0) {
                response.symptom.forEach(function (symptom) {
                    var symptomOption = new Option(symptom.text, symptom.id, true, true);
                    $('#symptomchoose1').append(symptomOption).trigger('change');
                });
            }

            $('#testchoose1').empty().trigger('change');

            if (response.test && response.test.length > 0) {
                response.test.forEach(function (test) {
                    var testOption = new Option(test.text, test.id, true, true);
                    $('#testchoose1').append(testOption).trigger('change');
                });
            }

            $('#advicechoose1').empty().trigger('change');

            if (response.advice && response.advice.length > 0) {
                response.advice.forEach(function (advice) {
                    var adviceOption = new Option(advice.text, advice.id, true, true);
                    $('#advicechoose1').append(adviceOption).trigger('change');
                });
            }

            tinymce.remove('#editor');
            tinymce.init({
                selector: '#editor',
                plugins: 'preview importcss searchreplace autolink autosave save directionality code visualblocks visualchars fullscreen image link media template codesample table charmap pagebreak nonbreaking anchor insertdatetime advlist lists wordcount help charmap quickbars emoticons',
                menubar: 'file edit view insert format tools table help',
                toolbar: 'undo redo | bold italic underline | fontselect fontsizeselect formatselect | alignleft aligncenter alignright alignjustify | outdent indent |  numlist bullist | forecolor backcolor removeformat | pagebreak | charmap emoticons | fullscreen  preview save | insertfile image media template link anchor codesample | ltr rtl',
                branding: false,
                promotion: false,
                init_instance_callback: function (editor) {
                    // editor.setContent();
                    editor.setContent(response.medical_history.description);
                }
            });

            $('#myModal2').modal('show');
        },
        complete: function () {
            // Hide the loader once the AJAX request is complete
            $("#loader").hide();
            $('.add_new').show();
        }
    })
});
$(".table").on("click", ".case", function () {
    "use strict";
    $("#loader").show();
    var iid = $(this).attr('data-id');

    $('.case_date').html("").end();
    $('.case_details').html("").end();
    $('.case_title').html("").end();
    $('.case_patient').html("").end();
    $('.case_patient_id').html("").end();
    $('.case_diagnosis').html("").end();  // Add this
    $('.case_treatment').html("").end();  // Add this
    $('.case_symptom').html("").end();
    $('.case_test').html("").end();
    $('.case_advice').html("").end();
    $.ajax({
        url: 'patient/getCaseDetailsByJason?id=' + iid,
        method: 'GET',
        data: '',
        dataType: 'json',
        success: function (response) {
            "use strict";
            var de = response.case.date * 1000;
            var d = new Date(de);


            var monthNames = [
                "January", "February", "March",
                "April", "May", "June", "July",
                "August", "September", "October",
                "November", "December"
            ];

            var day = d.getDate();
            var monthIndex = d.getMonth();
            var year = d.getFullYear();
            var da = day + ' ' + monthNames[monthIndex] + ', ' + year;
            $('.case_date').append(da).end();
            $('.case_patient').append(response.patient.name).end();
            $('.case_patient_id').append('ID: ' + response.patient.id).end();
            $('.case_title').append(response.case.title).end();
            $('.case_details').append(response.case.description).end();

            // Display Diagnosis List
            if (response.diagnosis && response.diagnosis.length > 0) {
                var diagnosisList = '<ul>';
                $.each(response.diagnosis, function (index, diagnosis) {
                    diagnosisList += '<li>' + diagnosis + '</li>';
                });
                diagnosisList += '</ul>';
                $('.case_diagnosis').append(diagnosisList).end();
            } else {
                $('.case_diagnosis').append('<p>No diagnosis available</p>').end();
            }

            // Display Treatment List
            if (response.treatment && response.treatment.length > 0) {
                var treatmentList = '<ul>';
                $.each(response.treatment, function (index, treatment) {
                    treatmentList += '<li>' + treatment + '</li>';
                });
                treatmentList += '</ul>';
                $('.case_treatment').append(treatmentList).end();
            } else {
                $('.case_treatment').append('<p>No treatment available</p>').end();
            }

            // Display Symptom List
            if (response.symptom && response.symptom.length > 0) {
                var symptomList = '<ul>';
                $.each(response.symptom, function (index, symptom) {
                    symptomList += '<li>' + symptom + '</li>';
                });
                symptomList += '</ul>';
                $('.case_symptom').append(symptomList).end();
            } else {
                $('.case_symptom').append('<p>No symptom available</p>').end();
            }

            // Display Test List
            if (response.test && response.test.length > 0) {
                var testList = '<ul>';
                $.each(response.test, function (index, test) {
                    testList += '<li>' + test + '</li>';
                });
                testList += '</ul>';
                $('.case_test').append(testList).end();
            } else {
                $('.case_test').append('<p>No test available</p>').end();
            }

            // Display Advice List
            if (response.advice && response.advice.length > 0) {
                var adviceList = '<ul>';
                $.each(response.advice, function (index, advice) {
                    adviceList += '<li>' + advice + '</li>';
                });
                adviceList += '</ul>';
                $('.case_advice').append(adviceList).end();
            } else {
                $('.case_advice').append('<p>No advice available</p>').end();
            }


            $('#caseModal').modal('show');
        },
        complete: function () {
            $("#loader").hide();
        }
    })
});
$(document).ready(function () {
    "use strict";
    var table = $('#editable-sample').DataTable({
        responsive: true,
        "processing": true,
        "serverSide": true,
        "searchable": true,
        "ajax": {
            url: "patient/getCaseList",
            type: 'POST',
        },
        scroller: {
            loadingIndicator: true
        },
        dom: "<'row'<'col-sm-3'l><'col-sm-5 text-center'B><'col-sm-4 text-right'f>>" +
            "<'row'<'col-sm-12'tr>>" +
            "<'row'<'col-sm-5'i><'col-sm-7'p>>",

        buttons: [
            { extend: 'copyHtml5', exportOptions: { columns: [0, 1, 2], } },
            { extend: 'excelHtml5', exportOptions: { columns: [0, 1, 2], } },
            { extend: 'csvHtml5', exportOptions: { columns: [0, 1, 2], } },
            { extend: 'pdfHtml5', exportOptions: { columns: [0, 1, 2], } },
            { extend: 'print', exportOptions: { columns: [0, 1, 2], } },
        ],
        aLengthMenu: [
            [10, 25, 50, 100, -1],
            [10, 25, 50, 100, "All"]
        ],
        iDisplayLength: 100,
        "order": [[0, "desc"]],
        "language": {
            "lengthMenu": "_MENU_",
            search: "_INPUT_",
            "url": "common/assets/DataTables/languages/" + language + ".json"
        },
    });

    table.buttons().container()
        .appendTo('.custom_buttons');
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
    $("#patientchoose1").select2({
        placeholder: select_patient,
        allowClear: true,
        ajax: {
            url: 'patient/getPatientinfo',
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


});

$(document).ready(function () {
    "use strict";
    $(".flashmessage").delay(3000).fadeOut(100);
});


$(document).ready(function () {
   var hospital_id = $('#hospitalId').val();

    // Initialize Select2 for #diagnosischoose
    $("#diagnosischoose").select2({
        placeholder: select_diagnosis,
        allowClear: true,
        ajax: {
            url: 'api/getDiagnosisInfo?hospital_id=' + hospital_id, // Include patient_ion_id in the URL
            type: "get", // Use GET method
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
});

$(document).ready(function () {
    "use strict";
  var hospital_id = $('#hospitalId').val();
    $("#treatmentchoose").select2({
        placeholder: select_treatment,
        allowClear: true,
        ajax: {
            url: 'api/getTreatmentInfo?hospital_id=' + hospital_id,
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
    $("#treatmentchoose1").select2({
        placeholder: select_treatment,
        allowClear: true,
        ajax: {
            url: 'treatment/getTreatmentInfo',
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


});


$(document).ready(function () {
  var hospital_id = $('#hospitalId').val();
    "use strict";
    $("#symptomchoose").select2({
        placeholder: select_symptom,
        allowClear: true,
        ajax: {
            url: 'api/getSymptomInfo?hospital_id=' + hospital_id,
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
    $("#symptomchoose1").select2({
        placeholder: select_symptom,
        allowClear: true,
        ajax: {
            url: 'symptom/getSymptomInfo',
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


});

$(document).ready(function () {
  var hospital_id = $('#hospitalId').val();
    "use strict";
    $("#advicechoose").select2({
        placeholder: select_advice,
        allowClear: true,
        ajax: {
            url: 'api/getAdviceInfo?hospital_id=' + hospital_id,
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
    $("#advicechoose1").select2({
        placeholder: select_advice,
        allowClear: true,
        ajax: {
            url: 'advice/getAdviceInfo',
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


});


$(document).ready(function () {
  var hospital_id = $('#hospitalId').val();
    "use strict";
    $("#testchoose").select2({
        placeholder: select_test,
        allowClear: true,
        ajax: {
            url: 'api/getTestInfo?hospital_id=' + hospital_id,
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
    $("#testchoose1").select2({
        placeholder: select_test,
        allowClear: true,
        ajax: {
            url: 'finance/getTestInfo',
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


});

$(document).on("click", '#addSymptom', function () {
    $('#addSymptom').find('.spinner-border').show();
    $('#addSymptom').find('#button-text').hide();
    $('#addSymptom').prop('disabled', true);

    let name = $('#name').val();
    let data = new FormData();
    data.append('name', name);

    $.ajax({
        url: 'api/addNewSymptom',
        type: 'POST',
        data: data,
        processData: false,
        contentType: false,
        success: function (response) {
            let newSymptom = JSON.parse(response);
            let newOption = new Option(newSymptom.name, newSymptom.id, true, true);
            $('#symptomchoose').append(newOption);
            let currentValues = $('#symptomchoose').val() || [];  // Get current selected values or empty array
            currentValues.push(newSymptom.id);  // Add the new ID to the selected values array
            $('#symptomchoose').val(currentValues);  // Update the multi-select with the new values
            $('#symptomchoose').trigger('change');
            $('#mySymptomModal').modal('hide');

        },
        error: function (xhr, status, error) {
            console.error('AJAX Error:', error);
        },
        complete: function () {
            // Hide spinner and enable button
            $('#name').val("");
            $('#addSymptom').find('.spinner-border').hide();
            $('#addSymptom').find('#button-text').show();
            $('#addSymptom').prop('disabled', false); // Re-enable the button
        }
    });
});

$(document).on("click", '#addSymptom1', function () {
    $('#addSymptom1').find('.spinner-border').show();
    $('#addSymptom1').find('#button-text').hide();
    $('#addSymptom1').prop('disabled', true);

    let name = $('#name').val();
    let data = new FormData();
    data.append('name', name);

    $.ajax({
        url: 'api/addNewSymptom',
        type: 'POST',
        data: data,
        processData: false,
        contentType: false,
        success: function (response) {
            let newSymptom = JSON.parse(response);
            let newOption = new Option(newSymptom.name, newSymptom.id, true, true);
            $('#symptomchoose1').append(newOption);
            let currentValues = $('#symptomchoose1').val() || [];  // Get current selected values or empty array
            currentValues.push(newSymptom.id);  // Add the new ID to the selected values array
            $('#symptomchoose1').val(currentValues);  // Update the multi-select with the new values
            $('#symptomchoose1').trigger('change');
            $('#mySymptomModal').modal('hide');

        },
        error: function (xhr, status, error) {
            console.error('AJAX Error:', error);
        },
        complete: function () {
            // Hide spinner and enable button
            $('#name').val("");
            $('#addSymptom1').find('.spinner-border').hide();
            $('#addSymptom1').find('#button-text').show();
            $('#addSymptom1').prop('disabled', false); // Re-enable the button
        }
    });
});




$(document).on("click", '#addTest', function () {
    $('#addTest').find('.spinner-border').show();
    $('#addTest').find('#button-text').hide();
    $('#addTest').prop('disabled', true);
    let category = $('#category').val();
    let type = $('#type').val();
    let data = new FormData();
    data.append('category', category);
    data.append('type', type);
    $.ajax({
        url: 'api/addLabTest',
        type: 'POST',
        data: data,
        processData: false,
        contentType: false,
        success: function (response) {
            let newTest = JSON.parse(response);
            let newOption = new Option(newTest.category, newTest.id, true, true);
            $('#testchoose').append(newOption);
            let currentValues = $('#testchoose').val() || [];  // Get current selected values or empty array
            currentValues.push(newTest.id);  // Add the new ID to the selected values array
            $('#testchoose').val(currentValues);  // Update the multi-select with the new values
            $('#testchoose').trigger('change');

            // Hide the modal and remove the backdrop
            $('#myTestModal').modal("hide");

        },
        error: function (xhr, status, error) {
            console.error('AJAX Error:', error);
        },
        complete: function () {
            // Hide spinner and enable button
            $('#category').val("");
            $('#addTest').find('.spinner-border').hide();
            $('#addTest').find('#button-text').show();
            $('#addTest').prop('disabled', false); // Re-enable the button
        }
    });
});

$(document).on("click", '#addTest1', function () {
    $('#addTest1').find('.spinner-border').show();
    $('#addTest1').find('#button-text').hide();
    $('#addTest1').prop('disabled', true);
    let category = $('#category').val();
    let type = $('#type').val();
    let data = new FormData();
    data.append('category', category);
    data.append('type', type);
    $.ajax({
        url: 'api/addLabTest',
        type: 'POST',
        data: data,
        processData: false,
        contentType: false,
        success: function (response) {
            let newTest = JSON.parse(response);
            let newOption = new Option(newTest.category, newTest.id, true, true);
            $('#testchoose1').append(newOption);
            let currentValues = $('#testchoose1').val() || [];  // Get current selected values or empty array
            currentValues.push(newTest.id);  // Add the new ID to the selected values array
            $('#testchoose1').val(currentValues);  // Update the multi-select with the new values
            $('#testchoose1').trigger('change');

            // Hide the modal and remove the backdrop
            $('#myTestModal').modal("hide");

        },
        error: function (xhr, status, error) {
            console.error('AJAX Error:', error);
        },
        complete: function () {
            // Hide spinner and enable button
            $('#category').val("");
            $('#addTest1').find('.spinner-border').hide();
            $('#addTest1').find('#button-text').show();
            $('#addTest1').prop('disabled', false); // Re-enable the button
        }
    });
});



$(document).on("click", '#addAdvice', function () {
    $('#addAdvice').find('.spinner-border').show();
    $('#addAdvice').find('#button-text').hide();
    $('#addAdvice').prop('disabled', true);

    let name = $('#name1').val();
    let data = new FormData();
    data.append('name', name);

    $.ajax({
        url: 'api/addNewAdvice',
        type: 'POST',
        data: data,
        processData: false,
        contentType: false,
        success: function (response) {
            let newAdvice = JSON.parse(response);
            let newOption = new Option(newAdvice.name, newAdvice.id, true, true);
            $('#advicechoose').append(newOption);
            let currentValues = $('#advicechoose').val() || [];  // Get current selected values or empty array
            currentValues.push(newAdvice.id);  // Add the new ID to the selected values array
            $('#advicechoose').val(currentValues);  // Update the multi-select with the new values
            $('#advicechoose').trigger('change');
            $('#myAdviceModal').modal('hide');

        },
        error: function (xhr, status, error) {
            console.error('AJAX Error:', error);
        },
        complete: function () {
            // Hide spinner and enable button
            $('#name1').val("");
            $('#addAdvice').find('.spinner-border').hide();
            $('#addAdvice').find('#button-text').show();
            $('#addAdvice').prop('disabled', false); // Re-enable the button
        }
    });
});

$(document).on("click", '#addAdvice1', function () {
    $('#addAdvice1').find('.spinner-border').show();
    $('#addAdvice1').find('#button-text').hide();
    $('#addAdvice1').prop('disabled', true);

    let name = $('#name1').val();
    let data = new FormData();
    data.append('name', name);

    $.ajax({
        url: 'api/addNewAdvice',
        type: 'POST',
        data: data,
        processData: false,
        contentType: false,
        success: function (response) {
            let newAdvice = JSON.parse(response);
            let newOption = new Option(newAdvice.name, newAdvice.id, true, true);
            $('#advicechoose1').append(newOption);
            let currentValues = $('#advicechoose1').val() || [];  // Get current selected values or empty array
            currentValues.push(newAdvice.id);  // Add the new ID to the selected values array
            $('#advicechoose1').val(currentValues);  // Update the multi-select with the new values
            $('#advicechoose1').trigger('change');
            $('#myAdviceModal').modal('hide');

        },
        error: function (xhr, status, error) {
            console.error('AJAX Error:', error);
        },
        complete: function () {
            // Hide spinner and enable button
            $('#name1').val("");
            $('#addAdvice1').find('.spinner-border').hide();
            $('#addAdvice1').find('#button-text').show();
            $('#addAdvice1').prop('disabled', false); // Re-enable the button
        }
    });
});


$(document).on("click", '#addDiagnosis', function () {
    $('#addDiagnosis').find('.spinner-border').show();
    $('#addDiagnosis').find('#button-text').hide();
    $('#addDiagnosis').prop('disabled', true);

    let name = $('#name2').val();
    let code = $('#code').val();
    let description = tinymce.get('editor2').getContent();
    let disease_with_outbreak_potential = $('#disease_with_outbreak_potential').val();
    let maximum_expected_number_of_patient_in_a_week = $('#maximum_expected_number_of_patient_in_a_week').val();
    let data = new FormData();
    data.append('name', name);
    data.append('code', code);
    data.append('description', description);
    data.append('disease_with_outbreak_potential', disease_with_outbreak_potential);
    data.append('maximum_expected_number_of_patient_in_a_week', maximum_expected_number_of_patient_in_a_week);

    $.ajax({
        url: 'api/addNewDiagnosis',
        type: 'POST',
        data: data,
        processData: false,
        contentType: false,
        success: function (response) {
            let newDiagnosis = JSON.parse(response);
            let newOption = new Option(newDiagnosis.name, newDiagnosis.id, true, true);
            $('#diagnosischoose').append(newOption);
            let currentValues = $('#diagnosischoose').val() || [];  // Get current selected values or empty array
            currentValues.push(newDiagnosis.id);  // Add the new ID to the selected values array
            $('#diagnosischoose').val(currentValues);  // Update the multi-select with the new values
            $('#diagnosischoose').trigger('change');
            $('#myDiagnosisModal').modal('hide');

        },
        error: function (xhr, status, error) {
            console.error('AJAX Error:', error);
        },
        complete: function () {
            // Hide spinner and enable button
            $('#name2').val("");
            $('#code').val("");
            tinymce.get('editor2').setContent('');
            $('#disease_with_outbreak_potential').val("");
            $('#maximum_expected_number_of_patient_in_a_week').val("");
            $('#addDiagnosis').find('.spinner-border').hide();
            $('#addDiagnosis').find('#button-text').show();
            $('#addDiagnosis').prop('disabled', false); // Re-enable the button
        }
    });
});

$(document).on("click", '#addDiagnosis1', function () {
    $('#addDiagnosis1').find('.spinner-border').show();
    $('#addDiagnosis1').find('#button-text').hide();
    $('#addDiagnosis1').prop('disabled', true);

    let name = $('#name2').val();
    let code = $('#code').val();
    let description = tinymce.get('editor1').getContent();
    let disease_with_outbreak_potential = $('#disease_with_outbreak_potential').val();
    let maximum_expected_number_of_patient_in_a_week = $('#maximum_expected_number_of_patient_in_a_week').val();
    let data = new FormData();
    data.append('name', name);
    data.append('code', code);
    data.append('description', description);
    data.append('disease_with_outbreak_potential', disease_with_outbreak_potential);
    data.append('maximum_expected_number_of_patient_in_a_week', maximum_expected_number_of_patient_in_a_week);

    $.ajax({
        url: 'api/addNewDiagnosis',
        type: 'POST',
        data: data,
        processData: false,
        contentType: false,
        success: function (response) {
            let newDiagnosis = JSON.parse(response);
            let newOption = new Option(newDiagnosis.name, newDiagnosis.id, true, true);
            $('#diagnosischoose1').append(newOption);
            let currentValues = $('#diagnosischoose1').val() || [];  // Get current selected values or empty array
            currentValues.push(newDiagnosis.id);  // Add the new ID to the selected values array
            $('#diagnosischoose1').val(currentValues);  // Update the multi-select with the new values
            $('#diagnosischoose1').trigger('change');
            $('#myDiagnosisModal').modal('hide');

        },
        error: function (xhr, status, error) {
            console.error('AJAX Error:', error);
        },
        complete: function () {
            // Hide spinner and enable button
            $('#name2').val("");
            $('#code').val("");
            tinymce.get('editor1').setContent('');
            $('#disease_with_outbreak_potential').val("");
            $('#maximum_expected_number_of_patient_in_a_week').val("");
            $('#addDiagnosis1').find('.spinner-border').hide();
            $('#addDiagnosis1').find('#button-text').show();
            $('#addDiagnosis1').prop('disabled', false); // Re-enable the button
        }
    });
});


$(document).on("click", '#addTreatment', function () {
    $('#addTreatment').find('.spinner-border').show();
    $('#addTreatment').find('#button-text').hide();
    $('#addTreatment').prop('disabled', true);

    let name = $('#name3').val();
    let data = new FormData();
    data.append('name', name);

    $.ajax({
        url: 'api/addNewTreatment',
        type: 'POST',
        data: data,
        processData: false,
        contentType: false,
        success: function (response) {
            let newTreatment = JSON.parse(response);
            let newOption = new Option(newTreatment.name, newTreatment.id, true, true);
            $('#treatmentchoose').append(newOption);
            let currentValues = $('#treatmentchoose').val() || [];  // Get current selected values or empty array
            currentValues.push(newTreatment.id);  // Add the new ID to the selected values array
            $('#treatmentchoose').val(currentValues);  // Update the multi-select with the new values
            $('#treatmentchoose').trigger('change');
            $('#myTreatmentModal').modal('hide');

        },
        error: function (xhr, status, error) {
            console.error('AJAX Error:', error);
        },
        complete: function () {
            // Hide spinner and enable button
            $('#name3').val("");
            $('#addTreatment').find('.spinner-border').hide();
            $('#addTreatment').find('#button-text').show();
            $('#addTreatment').prop('disabled', false); // Re-enable the button
        }
    });
});

$(document).on("click", '#addTreatment1', function () {
    $('#addTreatment1').find('.spinner-border').show();
    $('#addTreatment1').find('#button-text').hide();
    $('#addTreatment1').prop('disabled', true);

    let name = $('#name3').val();
    let data = new FormData();
    data.append('name', name);

    $.ajax({
        url: 'api/addNewTreatment',
        type: 'POST',
        data: data,
        processData: false,
        contentType: false,
        success: function (response) {
            let newTreatment = JSON.parse(response);
            let newOption = new Option(newTreatment.name, newTreatment.id, true, true);
            $('#treatmentchoose1').append(newOption);
            let currentValues = $('#treatmentchoose1').val() || [];  // Get current selected values or empty array
            currentValues.push(newTreatment.id);  // Add the new ID to the selected values array
            $('#treatmentchoose1').val(currentValues);  // Update the multi-select with the new values
            $('#treatmentchoose1').trigger('change');
            $('#myTreatmentModal').modal('hide');

        },
        error: function (xhr, status, error) {
            console.error('AJAX Error:', error);
        },
        complete: function () {
            // Hide spinner and enable button
            $('#name3').val("");
            $('#addTreatment1').find('.spinner-border').hide();
            $('#addTreatment1').find('#button-text').show();
            $('#addTreatment1').prop('disabled', false); // Re-enable the button
        }
    });
});


$(document).ready(function () {
    "use strict";
    $(".flashmessage").delay(3000).fadeOut(100);

    $("#pos_select_patient").select2({
        placeholder: select_patient,
        allowClear: false,
        ajax: {
            url: "patient/getPatientinfoWithId",
            type: "post",
            dataType: "json",
            delay: 250,
            data: function (params) {
                "use strict";
                return {
                    searchTerm: params.term, // search term
                };
            },
            processResults: function (response) {
                "use strict";
                return {
                    results: response,
                };
            },
            cache: true,
        },
    });
    $("#pos_select_patient").on("change", function () {
        var id = $(this).val();
        //   var url = "<?php echo site_url();?>" + "patient/medicalHistory?id=" + id;
        window.location.href = "patient/patientCaseList?id=" + id;
        // $(location).attr("href", url);
    });
});


$(document).ready(function () {
    $("#maximum").hide();

    // Toggle the display of the 'maximum' field when the checkbox is clicked
    $('.disease_with_outbreak_potential').on('change', function () {
        if ($(this).is(':checked')) {
            $('#maximum').show(); // Show the field when checkbox is checked
        } else {
            $('#maximum').hide(); // Hide the field when checkbox is unchecked
        }
    });


});

$(document).on("click", '#addNew', function () {
    $('.add_new').hide();
    $('#case_id').val("");
    $('#case_date').val("");
    $('#case_title').val("");
    tinymce.get('editor').setContent('');
    $('#diagnosischoose1').empty().trigger('change');
    $('#symptomchoose1').empty().trigger('change');
    $('#testchoose1').empty().trigger('change');
    $('#treatmentchoose1').empty().trigger('change');
    $('#advicechoose1').empty().trigger('change');
    $('.case_idd').text("Add a new case");


});


