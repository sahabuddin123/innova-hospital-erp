/**
 * LifeCare Hospital - Financial Performance & Profit/Loss Report JavaScript
 */

(function($) {
    'use strict';

    var trendChartInstance = null;
    var donutChartInstance = null;

    $(document).ready(function() {
        initPeriodSwitcher();
        initDatePickers();
        initCharts();
        initActionButtons();
        initServiceTableToggle();
    });

    /**
     * Period switcher button clicks
     */
    function initPeriodSwitcher() {
        $('.fin-btn-period').on('click', function(e) {
            e.preventDefault();
            var period = $(this).data('period');
            $('#period_type_input').val(period);
            $('.fin-btn-period').removeClass('active');
            $(this).addClass('active');

            // Set appropriate default date value for selected period
            var today = new Date();
            var yyyy = today.getFullYear();
            var mm = String(today.getMonth() + 1).padStart(2, '0');
            var dd = String(today.getDate()).padStart(2, '0');

            if (period === 'daily' || period === 'today') {
                $('#date_input').val(yyyy + '-' + mm + '-' + dd);
            } else if (period === 'yesterday') {
                var yDate = new Date();
                yDate.setDate(yDate.getDate() - 1);
                var y_yyyy = yDate.getFullYear();
                var y_mm = String(yDate.getMonth() + 1).padStart(2, '0');
                var y_dd = String(yDate.getDate()).padStart(2, '0');
                $('#date_input').val(y_yyyy + '-' + y_mm + '-' + y_dd);
            } else if (period === 'weekly' || period === 'this_week') {
                $('#date_input').val(yyyy + '-' + mm + '-' + dd);
            } else if (period === 'monthly' || period === 'this_month') {
                $('#date_input').val(yyyy + '-' + mm);
            } else if (period === 'last_month') {
                var lmDate = new Date(today.getFullYear(), today.getMonth() - 1, 1);
                var lm_yyyy = lmDate.getFullYear();
                var lm_mm = String(lmDate.getMonth() + 1).padStart(2, '0');
                $('#date_input').val(lm_yyyy + '-' + lm_mm);
            } else if (period === 'yearly' || period === 'this_year') {
                $('#date_input').val(yyyy);
            }

            // Submit form to reload data cleanly
            $('#financial_report_form').submit();
        });
    }

    /**
     * Date pickers setup
     */
    function initDatePickers() {
        var period = $('#period_type_input').val() || 'monthly';
        var $dateInput = $('#date_input');

        if ($.fn.datepicker) {
            if (period === 'monthly') {
                $dateInput.datepicker({
                    format: 'yyyy-mm',
                    viewMode: 'months',
                    minViewMode: 'months',
                    autoclose: true
                });
            } else if (period === 'yearly') {
                $dateInput.datepicker({
                    format: 'yyyy',
                    viewMode: 'years',
                    minViewMode: 'years',
                    autoclose: true
                });
            } else {
                $dateInput.datepicker({
                    format: 'yyyy-mm-dd',
                    autoclose: true,
                    todayHighlight: true
                });
            }
        }
    }

    /**
     * Render Trend Line Chart & Payment Method Doughnut Chart
     */
    function initCharts() {
        if (typeof Chart === 'undefined') {
            return;
        }

        // 1. Trend Chart
        var trendCanvas = document.getElementById('financialTrendChart');
        if (trendCanvas && window.finChartData) {
            var ctx = trendCanvas.getContext('2d');
            var data = window.finChartData;

            if (trendChartInstance) {
                trendChartInstance.destroy();
            }

            trendChartInstance = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: data.labels || [],
                    datasets: [
                        {
                            label: 'Billing',
                            data: data.billing || [],
                            borderColor: '#2563eb',
                            backgroundColor: 'rgba(37, 99, 235, 0.08)',
                            borderWidth: 2.5,
                            pointBackgroundColor: '#2563eb',
                            pointRadius: 3,
                            pointHoverRadius: 5,
                            fill: true,
                            tension: 0.35
                        },
                        {
                            label: 'Collection',
                            data: data.collection || [],
                            borderColor: '#16a34a',
                            backgroundColor: 'rgba(22, 163, 74, 0.08)',
                            borderWidth: 2.5,
                            pointBackgroundColor: '#16a34a',
                            pointRadius: 3,
                            pointHoverRadius: 5,
                            fill: true,
                            tension: 0.35
                        },
                        {
                            label: 'Due',
                            data: data.due || [],
                            borderColor: '#dc2626',
                            backgroundColor: 'rgba(220, 38, 38, 0.08)',
                            borderWidth: 2.5,
                            pointBackgroundColor: '#dc2626',
                            pointRadius: 3,
                            pointHoverRadius: 5,
                            fill: true,
                            tension: 0.35
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    legend: {
                        display: true,
                        position: 'bottom',
                        labels: {
                            boxWidth: 12,
                            fontFamily: "'Segoe UI', Roboto, sans-serif",
                            fontSize: 12,
                            fontColor: '#475569'
                        }
                    },
                    tooltips: {
                        mode: 'index',
                        intersect: false,
                        backgroundColor: '#1e293b',
                        titleFontFamily: "'Segoe UI', Roboto, sans-serif",
                        bodyFontFamily: "'Segoe UI', Roboto, sans-serif",
                        callbacks: {
                            label: function(tooltipItem, chartData) {
                                var ds = chartData.datasets[tooltipItem.datasetIndex];
                                var val = Number(tooltipItem.yLabel).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                                return ds.label + ': ৳' + val;
                            }
                        }
                    },
                    scales: {
                        xAxes: [{
                            gridLines: {
                                display: false,
                                drawBorder: false
                            },
                            ticks: {
                                fontColor: '#64748b',
                                fontSize: 11
                            }
                        }],
                        yAxes: [{
                            gridLines: {
                                color: '#f1f5f9',
                                drawBorder: false
                            },
                            ticks: {
                                fontColor: '#64748b',
                                fontSize: 11,
                                callback: function(value) {
                                    if (value >= 1000) {
                                        return '৳' + (value / 1000).toLocaleString() + 'k';
                                    }
                                    return '৳' + value;
                                }
                            }
                        }]
                    }
                }
            });
        }

        // 2. Payment Method Donut Chart
        var donutCanvas = document.getElementById('paymentMethodChart');
        if (donutCanvas && window.finMethodData) {
            var dctx = donutCanvas.getContext('2d');
            var mData = window.finMethodData;

            var labels = [];
            var values = [];
            var colors = ['#16a34a', '#e11d48', '#ea580c', '#0284c7', '#8b5cf6', '#64748b', '#f59e0b'];

            for (var m in mData) {
                if (mData.hasOwnProperty(m)) {
                    labels.push(mData[m].name);
                    values.push(mData[m].amount);
                }
            }

            if (donutChartInstance) {
                donutChartInstance.destroy();
            }

            donutChartInstance = new Chart(dctx, {
                type: 'doughnut',
                data: {
                    labels: labels,
                    datasets: [{
                        data: values,
                        backgroundColor: colors.slice(0, labels.length),
                        borderWidth: 2,
                        borderColor: '#ffffff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutoutPercentage: 72,
                    legend: {
                        display: false
                    },
                    tooltips: {
                        backgroundColor: '#1e293b',
                        callbacks: {
                            label: function(tooltipItem, chartData) {
                                var idx = tooltipItem.index;
                                var lbl = chartData.labels[idx];
                                var val = Number(chartData.datasets[0].data[idx]).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                                return lbl + ': ৳' + val;
                            }
                        }
                    }
                }
            });
        }
    }

    /**
     * Action Buttons (Print, PDF, Excel)
     */
    function initActionButtons() {
        var getParams = function() {
            var period = $('#period_type_input').val() || 'monthly';
            var date = $('#date_input').val() || '';
            return 'period_type=' + encodeURIComponent(period) + '&date=' + encodeURIComponent(date);
        };

        $('#btn_print_report').on('click', function(e) {
            e.preventDefault();
            var url = 'finance/printFinancialReport?' + getParams();
            window.open(url, '_blank');
        });

        $('#btn_pdf_report').on('click', function(e) {
            e.preventDefault();
            var url = 'finance/exportFinancialReportPdf?' + getParams();
            window.location.href = url;
        });

        $('#btn_excel_report').on('click', function(e) {
            e.preventDefault();
            var url = 'finance/financialPerformanceReport?' + getParams() + '&export_format=excel';
            window.location.href = url;
        });

        $('#btn_csv_report').on('click', function(e) {
            e.preventDefault();
            var url = 'finance/financialPerformanceReport?' + getParams() + '&export_format=csv';
            window.location.href = url;
        });
    }

    /**
     * Service Performance Top 5 vs All toggle
     */
    function initServiceTableToggle() {
        $('#toggle_services_btn').on('click', function(e) {
            e.preventDefault();
            var $hiddenRows = $('.fin-service-extra-row');
            if ($hiddenRows.is(':visible')) {
                $hiddenRows.hide();
                $(this).text('Show All');
            } else {
                $hiddenRows.show();
                $(this).text('Show Top 5');
            }
        });
    }

})(jQuery);
