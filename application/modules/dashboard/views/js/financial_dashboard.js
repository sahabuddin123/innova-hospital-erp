/**
 * Lifecare Diagnostic Center - Centralized Financial Dashboard Engine
 * Phase 1 Implementation
 */

(function($) {
    'use strict';

    let currentPeriod = window.financialDashboardData.periodType || 'this_month';
    let currentTrendMode = 'collection';
    let mainTrendChartInstance = null;
    let paymentMethodChartInstance = null;
    let expenseCategoryChartInstance = null;
    let incomeVsExpenseChartInstance = null;

    const currencySymbol = window.financialDashboardData.currency || '৳';

    function formatMoney(amount) {
        const val = parseFloat(amount) || 0;
        return currencySymbol + ' ' + val.toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }

    function formatNumber(num) {
        return (parseInt(num, 10) || 0).toLocaleString();
    }

    // Initialize All Charts on Load
    function initCharts() {
        const chartData = window.financialDashboardData.chartData || {};

        // 1. Main Trend Chart
        const ctxTrend = document.getElementById('mainTrendChart');
        if (ctxTrend) {
            const datasets = getTrendDatasets(chartData, currentTrendMode);
            mainTrendChartInstance = new Chart(ctxTrend, {
                type: 'line',
                data: {
                    labels: chartData.labels || [],
                    datasets: datasets
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        mode: 'index',
                        intersect: false
                    },
                    plugins: {
                        legend: {
                            display: (currentTrendMode === 'comparison'),
                            position: 'top',
                            labels: {
                                font: { weight: '600', size: 12 },
                                padding: 15,
                                usePointStyle: true
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return context.dataset.label + ': ' + formatMoney(context.parsed.y);
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return currencySymbol + ' ' + value.toLocaleString();
                                },
                                font: { size: 11 }
                            },
                            grid: { color: '#f1f5f9' }
                        },
                        x: {
                            ticks: { font: { size: 11 } },
                            grid: { display: false }
                        }
                    }
                }
            });
        }

        // 2. Payment Method Donut Chart
        const ctxMethod = document.getElementById('paymentMethodChart');
        if (ctxMethod) {
            const mData = chartData.payment_methods || { labels: [], data: [] };
            const mColors = ['#10b981', '#3b82f6', '#e11d48', '#f97316', '#8b5cf6', '#06b6d4', '#64748b'];

            paymentMethodChartInstance = new Chart(ctxMethod, {
                type: 'doughnut',
                data: {
                    labels: mData.labels || [],
                    datasets: [{
                        data: mData.data || [],
                        backgroundColor: mColors.slice(0, mData.labels.length),
                        borderWidth: 2,
                        borderColor: '#ffffff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '70%',
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const val = context.parsed;
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const pct = total > 0 ? ((val / total) * 100).toFixed(1) : 0;
                                    return context.label + ': ' + formatMoney(val) + ' (' + pct + '%)';
                                }
                            }
                        }
                    }
                }
            });
        }

        // 3. Expense Category Doughnut Chart
        const ctxExpense = document.getElementById('expenseCategoryChart');
        if (ctxExpense) {
            const expData = chartData.expense_categories || { labels: [], data: [] };
            const expColors = ['#ef4444', '#f97316', '#f59e0b', '#84cc16', '#06b6d4', '#6366f1', '#ec4899', '#8b5cf6'];

            expenseCategoryChartInstance = new Chart(ctxExpense, {
                type: 'doughnut',
                data: {
                    labels: expData.labels || [],
                    datasets: [{
                        data: expData.data || [],
                        backgroundColor: expColors.slice(0, expData.labels.length),
                        borderWidth: 2,
                        borderColor: '#ffffff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '65%',
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const val = context.parsed;
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const pct = total > 0 ? ((val / total) * 100).toFixed(1) : 0;
                                    return context.label + ': ' + formatMoney(val) + ' (' + pct + '%)';
                                }
                            }
                        }
                    }
                }
            });
        }

        // 4. Income vs Expense Bar Chart
        const ctxIncomeExpense = document.getElementById('incomeVsExpenseChart');
        if (ctxIncomeExpense) {
            const ieData = chartData.income_vs_expense || {
                labels: ['Gross Billing', 'Net Billing', 'Collection', 'Expenses', 'Net Profit'],
                data: [0, 0, 0, 0, 0]
            };

            incomeVsExpenseChartInstance = new Chart(ctxIncomeExpense, {
                type: 'bar',
                data: {
                    labels: ieData.labels,
                    datasets: [{
                        label: 'Financial Flow',
                        data: ieData.data,
                        backgroundColor: [
                            '#3b82f6', // Gross: Blue
                            '#06b6d4', // Net: Cyan
                            '#10b981', // Collection: Green
                            '#ef4444', // Expenses: Red
                            '#059669'  // Profit: Dark Emerald
                        ],
                        borderRadius: 6,
                        borderSkipped: false
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return context.label + ': ' + formatMoney(context.parsed.y);
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return currencySymbol + ' ' + value.toLocaleString();
                                },
                                font: { size: 10 }
                            },
                            grid: { color: '#f1f5f9' }
                        },
                        x: {
                            ticks: { font: { size: 10, weight: '600' } },
                            grid: { display: false }
                        }
                    }
                }
            });
        }
    }

    function getTrendDatasets(chartData, mode) {
        if (mode === 'comparison') {
            return [
                {
                    label: 'Net Billing',
                    data: chartData.billing || [],
                    borderColor: '#3b82f6',
                    backgroundColor: 'rgba(59, 130, 246, 0.05)',
                    borderWidth: 2,
                    tension: 0.35,
                    fill: false
                },
                {
                    label: 'Collections',
                    data: chartData.collection || [],
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    borderWidth: 3,
                    tension: 0.35,
                    fill: true
                },
                {
                    label: 'Expenses',
                    data: chartData.expense || [],
                    borderColor: '#ef4444',
                    backgroundColor: 'transparent',
                    borderWidth: 2,
                    borderDash: [5, 5],
                    tension: 0.35,
                    fill: false
                }
            ];
        } else {
            return [
                {
                    label: 'Received Collections',
                    data: chartData.collection || [],
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16, 185, 129, 0.15)',
                    borderWidth: 3,
                    tension: 0.35,
                    fill: true,
                    pointBackgroundColor: '#10b981',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6
                }
            ];
        }
    }

    // AJAX Data Fetching & Dynamic Update
    function fetchDashboardData(period, dateFrom, dateTo) {
        const refreshBtn = $('#btn_refresh_dashboard');
        const icon = refreshBtn.find('i');
        icon.addClass('fa-spin');
        refreshBtn.prop('disabled', true);

        const postData = {
            period_type: period,
            date_from: dateFrom || '',
            date_to: dateTo || ''
        };

        $.ajax({
            url: 'dashboard/getFinancialData',
            type: 'POST',
            data: postData,
            dataType: 'json',
            success: function(response) {
                if (response && response.status === 'success' && response.data) {
                    updateDashboardUI(response.data);
                } else {
                    console.error('Error fetching dashboard data:', response);
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', error);
            },
            complete: function() {
                icon.removeClass('fa-spin');
                refreshBtn.prop('disabled', false);
            }
        });
    }

    function updateDashboardUI(data) {
        const summary = data.summary || {};
        const trends = summary.trends || {};
        const chartData = data.chart_data || {};
        const methods = data.methods || {};
        const expenses = data.expense_breakdown || {};
        const tests = data.service_perf || [];
        const doctors = data.doctor_breakdown || [];
        const referrers = data.referrer_breakdown || [];

        // 1. Period Label
        $('#current_period_label').text(summary.period_label || '');

        // 2. 9 KPI Values & Badges
        $('#kpi_gross_billing').text(formatMoney(summary.gross_billing));
        $('#kpi_total_invoices').text(formatNumber(summary.total_invoices) + ' Invoices');
        updateTrendBadge('#trend_gross_billing', trends.gross_billing);

        $('#kpi_total_discount').text(formatMoney(summary.total_discount));
        updateTrendBadge('#trend_total_discount', trends.total_discount);

        $('#kpi_net_billing').text(formatMoney(summary.net_billing));
        updateTrendBadge('#trend_net_billing', trends.net_billing);

        $('#kpi_total_collection').text(formatMoney(summary.total_collection));
        updateTrendBadge('#trend_total_collection', trends.total_collection);

        $('#kpi_total_due').text(formatMoney(summary.total_due));
        updateTrendBadge('#trend_total_due', trends.total_due);

        $('#kpi_total_expenses').text(formatMoney(summary.total_expenses));
        updateTrendBadge('#trend_total_expenses', trends.total_expenses);

        $('#kpi_doctor_commission').text(formatMoney(summary.doctor_commission));
        $('#kpi_doctor_paid_text').text('Paid: ' + formatMoney(summary.paid_doctor_comm));
        $('#kpi_doctor_pending_text').text('Pending: ' + formatMoney(summary.pending_doctor_comm));
        updateTrendBadge('#trend_doctor_commission', trends.doctor_commission);

        $('#kpi_referral_commission').text(formatMoney(summary.referral_commission));
        $('#kpi_ref_earned_text').text('Earned: ' + formatMoney(summary.earned_ref_comm));
        $('#kpi_ref_pending_text').text('Pending: ' + formatMoney(summary.pending_ref_comm));
        updateTrendBadge('#trend_referral_commission', trends.referral_commission);

        $('#kpi_net_profit').text(formatMoney(summary.net_profit));
        $('#kpi_net_margin_badge').text('Margin: ' + (parseFloat(summary.net_margin) || 0).toFixed(2) + '%');

        // 3. Update Charts
        if (mainTrendChartInstance) {
            mainTrendChartInstance.data.labels = chartData.labels || [];
            mainTrendChartInstance.data.datasets = getTrendDatasets(chartData, currentTrendMode);
            mainTrendChartInstance.options.plugins.legend.display = (currentTrendMode === 'comparison');
            mainTrendChartInstance.update();
        }

        if (paymentMethodChartInstance && chartData.payment_methods) {
            paymentMethodChartInstance.data.labels = chartData.payment_methods.labels || [];
            paymentMethodChartInstance.data.datasets[0].data = chartData.payment_methods.data || [];
            paymentMethodChartInstance.update();
        }

        if (expenseCategoryChartInstance && chartData.expense_categories) {
            expenseCategoryChartInstance.data.labels = chartData.expense_categories.labels || [];
            expenseCategoryChartInstance.data.datasets[0].data = chartData.expense_categories.data || [];
            expenseCategoryChartInstance.update();
        }

        if (incomeVsExpenseChartInstance && chartData.income_vs_expense) {
            incomeVsExpenseChartInstance.data.labels = chartData.income_vs_expense.labels;
            incomeVsExpenseChartInstance.data.datasets[0].data = chartData.income_vs_expense.data;
            incomeVsExpenseChartInstance.update();
        }

        // 4. Update Payment Method List DOM
        renderPaymentMethodsList(methods, summary.total_collection);

        // 5. Update Expense Categories List DOM
        renderExpenseCategoriesList(expenses, summary.total_expenses);

        // 6. Update Top Tests Table
        renderDiagnosticTestsTable(tests);

        // 7. Update Doctor Commission Table
        renderDoctorCommissionTable(doctors);

        // 8. Update Referral Commission Table
        renderReferralCommissionTable(referrers);
    }

    function updateTrendBadge(selector, trendObj) {
        const el = $(selector);
        if (!el.length || !trendObj) return;

        el.removeClass('trend-up trend-down trend-neutral');
        const dir = trendObj.dir || 'neutral';
        el.addClass('trend-' + dir);

        let arrow = '&bull;';
        if (dir === 'up') arrow = '&uarr;';
        if (dir === 'down') arrow = '&darr;';

        el.html(arrow + ' ' + (trendObj.text || '0%'));
    }

    function renderPaymentMethodsList(methods, totalCollection) {
        const container = $('#payment_method_list');
        container.empty();

        const methodColors = {
            'Cash': '#10b981',
            'Card': '#3b82f6',
            'Bkash': '#e11d48',
            'Nagad': '#f97316',
            'Rocket': '#8b5cf6',
            'Bank': '#06b6d4',
            'Others': '#64748b'
        };

        const keys = Object.keys(methods);
        if (keys.length === 0) {
            container.html('<div class="text-muted text-center py-2">No deposits recorded</div>');
            return;
        }

        keys.forEach(function(mName) {
            const m = methods[mName];
            const color = methodColors[mName] || '#64748b';
            const html = `
                <div class="fin-method-item">
                    <span class="fin-method-badge">
                        <span class="fin-method-dot" style="background-color: ${color};"></span>
                        ${escapeHtml(mName)}
                    </span>
                    <span class="fin-method-val">
                        ${formatMoney(m.amount)}
                        <small class="text-muted font-weight-normal">(${m.percentage}%)</small>
                    </span>
                </div>
            `;
            container.append(html);
        });
    }

    function renderExpenseCategoriesList(expenses, totalExpenses) {
        const container = $('#expense_category_list');
        container.empty();

        const tot = parseFloat(totalExpenses) || 0;
        const keys = Object.keys(expenses);
        if (keys.length === 0) {
            container.html('<div class="text-muted text-center py-4">No expenses recorded for this period</div>');
            return;
        }

        keys.forEach(function(cName) {
            const amt = parseFloat(expenses[cName]) || 0;
            const pct = tot > 0 ? ((amt / tot) * 100).toFixed(1) : 0;
            const html = `
                <div class="fin-method-item">
                    <span class="fin-method-badge">
                        <span class="fin-method-dot" style="background-color: #ef4444;"></span>
                        ${escapeHtml(cName)}
                    </span>
                    <span class="fin-method-val">
                        ${formatMoney(amt)}
                        <small class="text-muted font-weight-normal">(${pct}%)</small>
                    </span>
                </div>
            `;
            container.append(html);
        });
    }

    function renderDiagnosticTestsTable(tests) {
        const tbody = $('#tbody_diagnostic_tests');
        tbody.empty();

        if (!tests || tests.length === 0) {
            tbody.html('<tr><td colspan="8" class="text-center py-4 text-muted">No diagnostic services recorded for this period</td></tr>');
            return;
        }

        tests.forEach(function(test, idx) {
            const rank = idx + 1;
            const rankClass = rank <= 3 ? 'fin-rank-' + rank : '';
            const html = `
                <tr>
                    <td><span class="fin-rank-badge ${rankClass}">${rank}</span></td>
                    <td class="font-weight-bold text-dark">${escapeHtml(test.name)}</td>
                    <td class="text-center font-weight-bold">${formatNumber(test.qty)}</td>
                    <td class="text-right">${formatMoney(test.gross)}</td>
                    <td class="text-right text-danger">- ${formatMoney(test.discount)}</td>
                    <td class="text-right font-weight-bold text-dark">${formatMoney(test.net)}</td>
                    <td class="text-right text-purple">- ${formatMoney(test.ref_comm)}</td>
                    <td class="text-right font-weight-bold text-success">${formatMoney(test.hospital_net)}</td>
                </tr>
            `;
            tbody.append(html);
        });
    }

    function renderDoctorCommissionTable(doctors) {
        const tbody = $('#tbody_doctor_commissions');
        tbody.empty();

        if (!doctors || doctors.length === 0) {
            tbody.html('<tr><td colspan="7" class="text-center py-4 text-muted">No doctor commission records for this period</td></tr>');
            return;
        }

        doctors.forEach(function(doc) {
            const comm = parseFloat(doc.commission) || 0;
            const pending = parseFloat(doc.pending_commission) || 0;
            const isClear = (pending <= 0 && comm > 0);

            let statusBadge = '<span class="badge-status badge-eligible">None</span>';
            if (comm > 0) {
                statusBadge = isClear
                    ? '<span class="badge-status badge-paid">Fully Cleared</span>'
                    : '<span class="badge-status badge-pending">Pending</span>';
            }

            const html = `
                <tr>
                    <td class="font-weight-bold text-dark">${escapeHtml(doc.name)}</td>
                    <td class="text-center">${formatNumber(doc.invoices)}</td>
                    <td class="text-right">${formatMoney(doc.billing)}</td>
                    <td class="text-right font-weight-bold text-purple">${formatMoney(doc.commission)}</td>
                    <td class="text-right text-success">${formatMoney(doc.paid_commission)}</td>
                    <td class="text-right text-warning font-weight-bold">${formatMoney(doc.pending_commission)}</td>
                    <td class="text-center">${statusBadge}</td>
                </tr>
            `;
            tbody.append(html);
        });
    }

    function renderReferralCommissionTable(referrers) {
        const tbody = $('#tbody_referral_commissions');
        tbody.empty();

        if (!referrers || referrers.length === 0) {
            tbody.html('<tr><td colspan="7" class="text-center py-4 text-muted">No referral commission records for this period</td></tr>');
            return;
        }

        referrers.forEach(function(ref) {
            const html = `
                <tr>
                    <td class="font-weight-bold text-dark">${escapeHtml(ref.name)}</td>
                    <td class="text-center">${formatNumber(ref.patient_count)}</td>
                    <td class="text-center">${formatNumber(ref.invoice_count)}</td>
                    <td class="text-right">${formatMoney(ref.billing)}</td>
                    <td class="text-right text-success font-weight-bold">${formatMoney(ref.earned)}</td>
                    <td class="text-right text-warning font-weight-bold">${formatMoney(ref.pending)}</td>
                    <td class="text-right text-primary font-weight-bold">${formatMoney(ref.wallet_balance)}</td>
                </tr>
            `;
            tbody.append(html);
        });
    }

    function escapeHtml(text) {
        if (!text) return '';
        return String(text)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    // Event Bindings
    $(document).ready(function() {
        initCharts();

        // 1. Period Switcher Buttons
        $('#period_pills').on('click', '.fin-period-btn', function() {
            const btn = $(this);
            const period = btn.data('period');

            $('.fin-period-btn').removeClass('active');
            btn.addClass('active');
            currentPeriod = period;

            if (period === 'custom') {
                $('#custom_range_container').slideDown(150);
            } else {
                $('#custom_range_container').slideUp(150);
                fetchDashboardData(period);
            }
        });

        // 2. Custom Date Range Apply Button
        $('#btn_apply_custom').on('click', function() {
            const dateFrom = $('#date_from_input').val();
            const dateTo = $('#date_to_input').val();
            if (!dateFrom || !dateTo) {
                alert('Please select both From and To dates.');
                return;
            }
            fetchDashboardData('custom', dateFrom, dateTo);
        });

        // 3. Quick Refresh Button
        $('#btn_refresh_dashboard').on('click', function() {
            const dateFrom = $('#date_from_input').val();
            const dateTo = $('#date_to_input').val();
            fetchDashboardData(currentPeriod, dateFrom, dateTo);
        });

        // 4. Trend Chart Mode Toggle
        $('#btn_toggle_collections').on('click', function() {
            $('.fin-toggle-btn').removeClass('active');
            $(this).addClass('active');
            currentTrendMode = 'collection';
            if (mainTrendChartInstance) {
                const chartData = window.financialDashboardData.chartData || {};
                mainTrendChartInstance.data.datasets = getTrendDatasets(chartData, currentTrendMode);
                mainTrendChartInstance.options.plugins.legend.display = false;
                mainTrendChartInstance.update();
            }
        });

        $('#btn_toggle_comparison').on('click', function() {
            $('.fin-toggle-btn').removeClass('active');
            $(this).addClass('active');
            currentTrendMode = 'comparison';
            if (mainTrendChartInstance) {
                const chartData = window.financialDashboardData.chartData || {};
                mainTrendChartInstance.data.datasets = getTrendDatasets(chartData, currentTrendMode);
                mainTrendChartInstance.options.plugins.legend.display = true;
                mainTrendChartInstance.update();
            }
        });

        // 5. Business Intelligence Tabs
        $('.fin-tab-link').on('click', function() {
            const tabId = $(this).data('tab');
            $('.fin-tab-link').removeClass('active');
            $(this).addClass('active');

            $('.fin-tab-pane').removeClass('active');
            $('#' + tabId).addClass('active');
        });
    });

})(jQuery);
