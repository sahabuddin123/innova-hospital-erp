/**
 * Lifecare Diagnostic Center — Centralized Report Export Client Engine
 * 
 * Handles client-side click events for Print, PDF, Excel (.xlsx), and CSV exports.
 * Preserves all active filters and form inputs with zero data divergence.
 */
(function ($) {
    'use strict';

    $(document).on('click', '.rep-export-btn[data-format]', function (e) {
        e.preventDefault();

        var $btn = $(this);
        var format = $btn.data('format') || 'pdf';
        var $toolbar = $btn.closest('.rep-export-toolbar');
        var reportKey = $toolbar.data('report-key') || '';

        // Locate active filter form
        var $form = $('form.rep-filter-form');
        if (!$form.length) {
            $form = $('form[method="GET"]');
        }
        if (!$form.length) {
            $form = $('form').first();
        }

        // Build target URL
        var currentUrl = window.location.pathname;
        var queryParams = {};

        // 1. Read from URL search parameters first
        if (window.location.search) {
            var searchParams = new URLSearchParams(window.location.search);
            searchParams.forEach(function (value, key) {
                if (key !== 'export_format') {
                    queryParams[key] = value;
                }
            });
        }

        // 2. Overlay current form inputs
        if ($form.length) {
            var formArray = $form.serializeArray();
            $.each(formArray, function (i, field) {
                if (field.value !== '' && field.value !== null && field.name !== 'export_format') {
                    queryParams[field.name] = field.value;
                }
            });

            var formAction = $form.attr('action');
            if (formAction && formAction !== '#' && formAction !== '') {
                currentUrl = formAction;
            }
        }

        // Append export format
        queryParams['export_format'] = format;
        if (reportKey && !queryParams['report_key']) {
            queryParams['report_key'] = reportKey;
        }

        // Construct query string
        var queryString = $.param(queryParams);
        var exportUrl = currentUrl + (currentUrl.indexOf('?') >= 0 ? '&' : '?') + queryString;

        // Visual loading feedback
        var origHtml = $btn.html();
        $btn.addClass('is-loading').prop('disabled', true);
        $btn.html('<i class="fas fa-spinner fa-spin"></i> <span>Exporting...</span>');

        setTimeout(function () {
            $btn.removeClass('is-loading').prop('disabled', false);
            $btn.html(origHtml);
        }, 2000);

        // Execute export action
        if (format === 'print') {
            // Open dedicated print preview window
            window.open(exportUrl, '_blank', 'width=1050,height=800,scrollbars=yes,status=no,toolbar=no');
        } else if (format === 'pdf') {
            // Open PDF inline/view in new tab
            window.open(exportUrl, '_blank');
        } else {
            // Trigger direct file download for Excel (.xlsx) and CSV
            window.location.href = exportUrl;
        }
    });

})(jQuery);
