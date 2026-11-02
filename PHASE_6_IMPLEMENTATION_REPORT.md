# PHASE 6 — PROFIT & LOSS (P&L) REPORTS: IMPLEMENTATION REPORT
**Lifecare Diagnostic Center | Hospital Management System (ERP)**
*Generated: November 2, 2026*

---

## 1. Executive Summary

Phase 6 of the financial governance overhaul for Lifecare Diagnostic Center has been successfully implemented. This phase delivers an enterprise-grade **Profit & Loss (P&L) Reporting Suite** that provides hospital directors, chartered accountants, and management with dual-basis profitability intelligence:
1. **Cash Basis (Collection-Based)**: Measures actual physical liquidity and cash operating surplus based strictly on realized patient deposits, completed voucher disbursements, and settled commission payouts.
2. **Accrual Basis (Billing-Based)**: Measures economic profitability and operational performance recognized at the time of diagnostic service delivery, invoice creation, and voucher recognition.

Crucially, this phase solves the historic accounting vulnerability of double-counting commission expenses (doctor ultrasound fees and referral commissions) by systematically isolating voucher disbursement line items from general operating overhead.

---

## 2. Dual Accounting Architecture

### 2.1 Cash Basis (Collection-Based P&L)
Designed for liquidity assessment, treasury cash-flow forecasting, and vault settlement.
$$\text{Net Operating Cash Surplus} = \text{Net Collections} - (\text{Operating Expenses Paid} + \text{Doctor Commission Paid} + \text{Referral Commission Withdrawn})$$

- **Net Collections**: Total positive deposits from `patient_deposit` minus confirmed refunds (`deposit_type = 'refund'` or negative receipts).
- **Operating Expenses Paid**: Confirmed cash/bank disbursements from `expense`, strictly excluding doctor fees and referral payouts.
- **Doctor Commission Paid**: Direct doctor clinical/USG fees paid out via expense vouchers or doctor accounts.
- **Referral Commission Withdrawn**: Only completed and paid cash withdrawals from `referral_withdrawals` (`status = 'Paid'`). Unwithdrawn wallet credits are recognized as balance sheet liabilities, not cash outflows.

### 2.2 Accrual Basis (Billing-Based P&L)
Designed for diagnostic center performance evaluation, departmental contribution assessment, and corporate governance.
$$\text{Net Operating Accrual Profit} = \text{Net Operating Revenue} - (\text{Recognized Operating Expenses} + \text{Doctor Commission Accrued} + \text{Referral Commission Accrued})$$

- **Net Operating Revenue**: `Gross Billing` minus total discounts (`flat_discount` / `discount`) from `payment`.
- **Doctor Commission Accrued**: Total doctor obligations accrued at the time of invoice generation (`payment.doctor_amount`).
- **Referral Commission Accrued**: Total referral obligations earned and credited to marketer wallets (`payment.referral_amount`).
- **Recognized Operating Expenses**: All operating expense vouchers logged for the billing period.

### 2.3 Non-Duplication Architecture
In historical records, doctor ultrasound fees and referral marketing payments were frequently categorized directly into the general `expense` table. Phase 6 enforces an anti-double-counting filter:
- When calculating General Hospital Operating Expenses, any vouchers categorized as doctor commissions, ultrasound interpretation fees, or referral disbursements are segregated into their dedicated commission lines.
- This guarantees that hospital surplus is never artificially depressed by deducting doctor and referral payouts twice.

---

## 3. Implemented Reports & Capabilities

The Phase 6 suite provides 13 dedicated reports, views, and dashboards:

| Route / Action | Report Name | Description & Key Capabilities |
| :--- | :--- | :--- |
| `finance/profitLossDashboard` | **Executive P&L Dashboard** | 9 interactive KPI cards (Gross Billing, Discount, Net Billing, Collections, Dues, Expenses, Doctor Comm, Referral Comm, Net Profit/Surplus), 9 responsive Chart.js visual analytics (Billing vs Collections, Collections vs Expenses, Revenue Trend, Expense Trend, Category Distribution, Doctor Comm Trend, Referral Comm Trend, Dept Revenue, Test Revenue), and dual-basis switcher. |
| `finance/dailyProfitLoss` | **Daily P&L Report** | 24-hour time-slot matrix showing hourly billing, hourly cash receipts, hourly refund disbursements, and hourly expense vouchers. Includes full itemized drilldown tables for day's invoices, cashier deposits, and expense vouchers. |
| `finance/monthlyProfitLoss` | **Monthly P&L Report** | 31-day daily profitability breakdown showing daily collections, operational costs, commissions, and daily net cash surplus. Features interactive Month-over-Month (MoM) comparison with absolute (৳) variance and percentage growth, plus Year-to-Date (YTD) cumulative metrics. |
| `finance/yearlyProfitLoss` | **Yearly P&L Report** | 12-month calendar matrix aggregating annual revenue, collections, operating expenses, and net profit. Features Year-over-Year (YoY) comparative benchmarking against the previous fiscal year. |
| `finance/departmentProfitability`| **Department-wise Profitability** | Department-level P&L tracking (Pathology, Radiology/Imaging, Ultrasound, Cardiology, General Diagnostics). Displays Gross Billing, Discounts, Net Revenue, Doctor Share, Referral Share, Direct Departmental Expenses, Contribution Margin (৳), and Contribution Margin (%). Notes unallocated overhead for transparent gross-margin analysis. |
| `finance/testProfitability` | **Test-wise Profitability** | Granular investigation-level profitability report. Displays test catalog pricing, total quantity performed, gross revenue generated, total discounts conceded, doctor commission incurred, referral commission incurred, net diagnostic contribution, and profit margin (%). Filterable by test department. |
| `finance/collectionBasedPnl` | **Collection-Based P&L** | High-level cash accounting statement showing physical liquidity movements, deposit receipts by payment channel (Cash, Card, bKash, Nagad, Bank), and net cash operating surplus. |
| `finance/billingBasedPnl` | **Billing-Based P&L** | Pure accrual statement reflecting recognized revenue, patient accounts receivable (outstanding dues), accrued service costs, and net operating profit margin. |
| `finance/expenseProfitLossBreakdown` | **Operating Expense Breakdown** | Tri-dimensional expense audit: (1) Category-wise distribution, (2) Payment method breakdown (Cash, Petty Cash, Bank, Cheque), and (3) Top Vendor/Payee disbursements. Guarantees clean exclusion of commission payouts. |
| `finance/commissionExpenseAnalysis` | **Commission Expense Analysis** | Reconciles clinical doctor obligations and referral marketing liabilities. Displays generated commission vs paid commission for doctors, and generated commission vs credited wallet amounts vs completed cash withdrawals for referrers, clearly exposing outstanding wallet liability. |
| `finance/profitLossComparison` | **Financial Performance Comparison** | Line-by-line comparative variance matrix supporting both Month-over-Month (MoM) and Year-over-Year (YoY) modes with percentage indicators and direction tags. |
| `finance/incomeVsExpenseReport` | **Income vs Expense Report** | Comprehensive ledger report displaying side-by-side revenue/collection entries and operating disbursements, supported by a 12-month comparative financial performance chart. |
| `finance/profitLossReconciliation` | **Financial Reconciliation Audit** | Automated 6-point mathematical integrity audit engine verifying Net Billing formulas, Net Collection integrity, expense ledger audit trails, wallet liability isolation, withdrawal cash outflows, and non-duplication compliance. |

---

## 4. Automated 6-Point Reconciliation Audit Engine

The built-in reconciliation engine (`finance/profitLossReconciliation`) runs real-time mathematical checks across all primary database tables to detect data anomalies:

1. **Net Billing Integrity Rule**: Verifies that $\text{Gross Billing} - \text{Discounts} = \text{Net Billing}$.
   - *Status*: Flags historical manual-discount variances inherited from legacy billing.
2. **Net Collections Integrity Rule**: Verifies that $\text{Deposits} - \text{Refunds} = \text{Net Collections}$.
   - *Status*: `VERIFIED` across all transaction records.
3. **Operating Expense Audit Trail Rule**: Verifies that completed vouchers equal ledger disbursements.
   - *Status*: `VERIFIED` with zero unaccounted variances.
4. **Referral Liability Separation Rule**: Verifies that wallet credits are recorded as liabilities and NOT counted as cash disbursements until formally withdrawn.
   - *Status*: `VERIFIED`.
5. **Referral Cash Disbursement Rule**: Verifies that only `status = 'Paid'` withdrawal requests are booked as physical cash outflows.
   - *Status*: `VERIFIED`.
6. **Non-Duplication Rule**: Verifies that clinical fees and marketing commissions are not double-counted inside general administrative expenses.
   - *Status*: `VERIFIED`.

---

## 5. UI/UX, Navigation & Print Layout

1. **Dedicated Submenu Navigation**:
   - `application/modules/finance/views/inc_pnl_reports_nav.php` provides an intuitive pill-navigation bar linking all 13 reports across the top of every P&L page.
   - Integrated into the main application sidebar menu (`application/modules/home/views/menu.php`) under "Financial Activities" with a dedicated chart icon.
2. **Styling & Visualization**:
   - `common/extranal/css/finance/pnl_reports.css` enforces a clean, modern design with custom KPI cards, color-coded badges, variance meters, and high-contrast tables.
   - Dual-basis toggle switches allow seamless switching between "Cash Basis" and "Accrual Basis" on all major dashboards.
3. **Print Optimization**:
   - Clean `@media print` CSS rules formatted specifically for standard A4 and B5 paper.
   - Page-break-inside rules eliminate empty blank pages between report bodies and tables.
   - Integrated 3-tier professional hospital signature footer (Prepared By, Accounts Officer, Managing Director).

---

## 6. Files Created & Modified

### Modified Files:
- [Finance_model.php](file:///c:/wamp64/www/lifecare-hospital/application/modules/finance/models/Finance_model.php): Added Phase 6 P&L aggregation methods, dual-basis data engines, department/test profitability queries, commission analysis, and reconciliation engine.
- [Finance.php](file:///c:/wamp64/www/lifecare-hospital/application/modules/finance/controllers/Finance.php): Added 13 controller endpoints for P&L reporting suite with date filtering, export helpers, and permission checks.
- [system_syntax_lang.php](file:///c:/wamp64/www/lifecare-hospital/application/language/english/system_syntax_lang.php): Added 18 localization language keys for P&L suite.
- [menu.php](file:///c:/wamp64/www/lifecare-hospital/application/modules/home/views/menu.php): Integrated P&L reporting routes into main navigation sidebar.

### New Files Created:
- [inc_pnl_reports_nav.php](file:///c:/wamp64/www/lifecare-hospital/application/modules/finance/views/inc_pnl_reports_nav.php): Reusable sub-navigation bar for P&L reports.
- [pnl_reports.css](file:///c:/wamp64/www/lifecare-hospital/common/extranal/css/finance/pnl_reports.css): Dedicated styles and print stylesheets.
- [pnl_dashboard.php](file:///c:/wamp64/www/lifecare-hospital/application/modules/finance/views/pnl_dashboard.php): Executive P&L dashboard with 9 charts & 9 KPI cards.
- [pnl_daily.php](file:///c:/wamp64/www/lifecare-hospital/application/modules/finance/views/pnl_daily.php): Daily P&L hourly breakdown with transaction drilldowns.
- [pnl_monthly.php](file:///c:/wamp64/www/lifecare-hospital/application/modules/finance/views/pnl_monthly.php): 31-day P&L matrix with MoM variance and YTD metrics.
- [pnl_yearly.php](file:///c:/wamp64/www/lifecare-hospital/application/modules/finance/views/pnl_yearly.php): 12-month annual P&L matrix with YoY comparison.
- [pnl_department.php](file:///c:/wamp64/www/lifecare-hospital/application/modules/finance/views/pnl_department.php): Departmental profitability and contribution margins.
- [pnl_test.php](file:///c:/wamp64/www/lifecare-hospital/application/modules/finance/views/pnl_test.php): Test-wise unit sales, revenue, commission, and profit margin.
- [pnl_expense_breakdown.php](file:///c:/wamp64/www/lifecare-hospital/application/modules/finance/views/pnl_expense_breakdown.php): Category, payment method, and vendor breakdown.
- [pnl_commission_analysis.php](file:///c:/wamp64/www/lifecare-hospital/application/modules/finance/views/pnl_commission_analysis.php): Doctor & referral commission expense analysis.
- [pnl_comparison.php](file:///c:/wamp64/www/lifecare-hospital/application/modules/finance/views/pnl_comparison.php): Line-by-line comparative performance matrix.
- [pnl_income_vs_expense.php](file:///c:/wamp64/www/lifecare-hospital/application/modules/finance/views/pnl_income_vs_expense.php): Side-by-side financial statement and trend chart.
- [pnl_reconciliation.php](file:///c:/wamp64/www/lifecare-hospital/application/modules/finance/views/pnl_reconciliation.php): 6-point integrity audit engine.

---

## 7. Verification & Sign-Off

1. **Lint Verification**: All PHP controllers, models, and view templates have been linted with `php -l` and passed with zero syntax errors.
2. **Runtime Database Test**: Executed `scratch/test_phase6_runtime.php` against live MySQL database (`lifecare`). All 10 methods executed cleanly, generating accurate collections, billing, department contributions, test breakdowns, and audit checks.
3. **Phase Boundaries Respected**: Phase 6 implementation is strictly confined to reporting and reconciliation. No historical invoice records or transaction logs have been modified.
4. **Phase 7 Readiness**: The system is fully prepared for Phase 7 (Diagnostic Performance & Operational Reporting).
