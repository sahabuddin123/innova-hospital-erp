// var myEditor;
// $(document).ready(function () {

//     ClassicEditor
//             .create(document.querySelector('#editor'))
//             .then(editor => {
//                 editor.ui.view.editable.element.style.height = '300px';

//                 myEditor = editor;
//             })
//             .catch(error => {
//                 console.error(error);
//             });

// });

tinymce.init({
    selector: '#editor', 
    plugins: 'preview importcss searchreplace autolink autosave save directionality code visualblocks visualchars fullscreen image link media template codesample table charmap pagebreak nonbreaking anchor insertdatetime advlist lists wordcount help charmap quickbars emoticons',
    menubar: 'file edit view insert format tools table help',
    toolbar: 'undo redo | bold italic underline | fontselect fontsizeselect formatselect | alignleft aligncenter alignright alignjustify | outdent indent |  numlist bullist | forecolor backcolor removeformat | pagebreak | charmap emoticons | fullscreen  preview save | insertfile image media template link anchor codesample | ltr rtl',
    branding: false,
    promotion: false,
    extended_valid_elements: 'img[class|src|border=0|alt|title|hspace|vspace|width|height|align|onmouseover|onmouseout|name|style],svg[*],path[*],g[*],polygon[*],line[*],polyline[*],rect[*],circle[*],text[*],textPath[*],style[*]',
    content_style: 'body { font-family: Arial, Helvetica, sans-serif; font-size: 13px; color: #111; } table { border-collapse: collapse; width: 100%; }'
});