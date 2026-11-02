# TEST ITEM-WISE REPORT MODULE — IMPLEMENTATION & AUDIT REPORT
**Lifecare Hospital Management System (HMS) ERP**  
**Framework:** CodeIgniter 3 (HMVC) | **PHP:** 8.2 | **Database:** MySQL 8.0.31  
**Module:** Diagnostic / Pathology Reporting  
**Status:** Implementation Complete & Fully Verified  

---

## 1. EXECUTIVE SUMMARY

The **Test Item-Wise Report Module** has been designed, implemented, and validated for the Lifecare Hospital Management System. It delivers two distinct clinical diagnostic reporting tools:

1. **Report 1: Selected Test Item Report (`lab/selectedTestItemReport`)**
   Allows clinical directors, pathologists, and administrators to select any diagnostic test item (e.g. CBC, Blood Urea, ECG, CRP, FT4, TSH) via a searchable dropdown (`Select2`), choose date ranges with 1-click quick filters (Today, This Week, This Month, Last Month, This Year, Last Year, Custom), and view the **chronological date-wise breakdown of tests performed** along with grand totals and active day metrics.
2. **Report 2: All Test Items Summary Report (`lab/allTestItemsSummaryReport`)**
   Provides a high-level operational overview aggregating all diagnostic tests performed across the selected period, ranked from highest to lowest volume (`ORDER BY total_performed DESC`). Features an interactive **"View Details"** action with an AJAX modal presenting the exact date-wise breakdown for any selected investigation.

Both reports integrate directly with:
- **Phase 10 Navigation**: Placed inside Sub-menu 7 (*Diagnostic Performance Reports*) with zero broken routes.
- **Phase 11 Centralized Export Engine**: Full support for Print, PDF (mPDF), Excel (.xlsx via OpenXML), and RFC-4180 CSV exports respecting active filters.
- **Phase 12 Granular RBAC**: Protected under the `diagnostic_performance` group with role authorization.

---

## 2. DATABASE SCHEMA & ARCHITECTURAL MAPPING

All calculations reuse existing database tables and relationships. Zero new database tables were added.

### 2.1 Investigation Catalog Table: `payment_category`
Stores diagnostic test master definitions and pricing:
- `id` (int, PK): Unique identifier of the test item.
- `category` (varchar): Investigation test name (e.g. `CBC`, `Blood Urea`, `ASO Titre`, `ECG`).
- `code` (varchar): Clinical test code (e.g. `A90`, `K0001`, `2 hrs ABF`).
- `c_price` (decimal): Standard unit test charge.
- `type` (varchar): Category type filter (`type = 'diagnostic'`).
- `status` (varchar): Availability status (`status = 'Active'`).
- `hospital_id` (varchar): Multi-tenant isolation key.

### 2.2 Diagnostic Lab Execution Table: `lab`
Tracks every diagnostic test ordered, processed, completed, and delivered:
- `id` (int, PK): Unique lab transaction record ID.
- `invoice_id` (varchar): Foreign key referencing `payment.id`.
- `category_id` (varchar): Foreign key referencing `payment_category.id`.
- `category_name` (varchar): Snapshot test name fallback.
- `date` (varchar/int): Unix timestamp when invoice and lab order were created.
- `test_status` (varchar): Primary lab technician execution status (`done`, `not_done`, `cancelled`).
- `test_status_date` (varchar/int): Unix timestamp when test was marked `done`.
- `status` (varchar): Report finalization status (`pending`, `completed`, `delivered`, `cancelled`).
- `hospital_id` (varchar): Multi-tenant isolation key.

### 2.3 Invoice Line Items Table: `payment_items`
Tracks invoice-level billing lines and test quantities:
- `id` (int, PK): Unique invoice line item ID.
- `payment_id` (int): Invoice foreign key (`payment.id` / `lab.invoice_id`).
- `item_id` (int): Diagnostic test foreign key (`payment_category.id` / `lab.category_id`).
- `quantity` (decimal): Billed quantity (default `1.00`).
- `item_price` (decimal): Unit price.
- `subtotal` (decimal): Total line charge.

---

## 3. STATUS RULES & DATE FIELD AUDIT

### 3.1 Exact Completion Statuses Included
Based on the existing clinical workflow in `Finance.php:735` and `Lab_model.php:2253`:
- A test item is considered **completed / performed** if:
  $$\text{test\_status} = \text{'done'} \quad\text{OR}\quad \text{status} \in (\text{'complete'}, \text{'completed'}, \text{'delivery_pending'}, \text{'delivered'})$$

### 3.2 Exact Statuses Excluded
- **Cancelled / Rejected**:
  $$\text{test\_status} = \text{'cancelled'} \quad\text{OR}\quad \text{status} = \text{'cancelled'}$$
- **Pending / Ordered but Not Done**:
  $$\text{test\_status} = \text{'not_done'} \quad\text{AND}\quad \text{status} = \text{'pending'}$$
  (Tests that have only been billed but have not yet been performed in the laboratory).

### 3.3 Date Field Chosen for Counting
- **Primary Timestamp**: `l.test_status_date` (Unix timestamp when laboratory technician executed the investigation).
- **Fallback Timestamp**: `l.date` (Invoice order creation timestamp if `test_status_date` is 0 or NULL).
- **SQL Effective Date Expression**:
  ```sql
  DATE(FROM_UNIXTIME(CASE 
      WHEN (l.test_status_date IS NOT NULL AND l.test_status_date > 0) 
      THEN l.test_status_date 
      ELSE l.date 
  END)) AS report_date
  ```
- **Rationale**: This guarantees that tests performed on a different date than the initial billing date are accurately attributed to the actual date of clinical performance, while gracefully handling any historical records where completion timestamp was omitted.

---

## 4. AGGREGATION FORMULAS & CARTESIAN IMMUNITY

### 4.1 Unit Quantity Respect
To support investigations where quantity is greater than one without creating Cartesian duplication when multiple items exist:
```sql
LEFT JOIN (
    SELECT payment_id, item_id, SUM(quantity) / COUNT(*) AS quantity
    FROM payment_items
    GROUP BY payment_id, item_id
) pi_sub ON pi_sub.payment_id = l.invoice_id AND pi_sub.item_id = l.category_id
```
The effective quantity performed per lab record is:
$$\text{performed\_qty} = \text{COALESCE}(\text{pi\_sub.quantity}, 1)$$

### 4.2 Report 1: Chronological Date Aggregation
```sql
SELECT 
    DATE(FROM_UNIXTIME(CASE WHEN (l.test_status_date IS NOT NULL AND l.test_status_date > 0) THEN l.test_status_date ELSE l.date END)) AS report_date,
    MAX(COALESCE(pc.category, l.category_name, 'Diagnostic Test')) AS test_name,
    CAST(ROUND(SUM(COALESCE(pi_sub.quantity, 1))) AS UNSIGNED) AS total_performed
FROM lab l
LEFT JOIN payment_category pc ON pc.id = l.category_id
LEFT JOIN (
    SELECT payment_id, item_id, SUM(quantity) / COUNT(*) AS quantity
    FROM payment_items
    GROUP BY payment_id, item_id
) pi_sub ON pi_sub.payment_id = l.invoice_id AND pi_sub.item_id = l.category_id
WHERE l.category_id = :test_id
  AND (l.test_status != 'cancelled' AND (l.status != 'cancelled' OR l.status IS NULL))
  AND (l.test_status = 'done' OR l.status IN ('complete', 'completed', 'delivery_pending', 'delivered'))
  AND (CASE WHEN (l.test_status_date IS NOT NULL AND l.test_status_date > 0) THEN l.test_status_date ELSE l.date END) >= :start_time
  AND (CASE WHEN (l.test_status_date IS NOT NULL AND l.test_status_date > 0) THEN l.test_status_date ELSE l.date END) <= :end_time
  AND (l.hospital_id = :hospital_id OR l.hospital_id IS NULL)
GROUP BY report_date
ORDER BY report_date ASC;
```

### 4.3 Report 2: All Test Items Summary Aggregation
```sql
SELECT 
    l.category_id AS test_id,
    COALESCE(pc.category, l.category_name, 'Unknown Test') AS test_name,
    COALESCE(pc.code, '') AS test_code,
    COALESCE(pc.c_price, 0) AS unit_price,
    CAST(ROUND(SUM(COALESCE(pi_sub.quantity, 1))) AS UNSIGNED) AS total_performed
FROM lab l
LEFT JOIN payment_category pc ON pc.id = l.category_id
LEFT JOIN (
    SELECT payment_id, item_id, SUM(quantity) / COUNT(*) AS quantity
    FROM payment_items
    GROUP BY payment_id, item_id
) pi_sub ON pi_sub.payment_id = l.invoice_id AND pi_sub.item_id = l.category_id
WHERE (l.test_status != 'cancelled' AND (l.status != 'cancelled' OR l.status IS NULL))
  AND (l.test_status = 'done' OR l.status IN ('complete', 'completed', 'delivery_pending', 'delivered'))
  AND (CASE WHEN (l.test_status_date IS NOT NULL AND l.test_status_date > 0) THEN l.test_status_date ELSE l.date END) >= :start_time
  AND (CASE WHEN (l.test_status_date IS NOT NULL AND l.test_status_date > 0) THEN l.test_status_date ELSE l.date END) <= :end_time
  AND (l.hospital_id = :hospital_id OR l.hospital_id IS NULL)
GROUP BY l.category_id, test_name, test_code, unit_price
ORDER BY total_performed DESC, test_name ASC;
```

---

## 5. MODIFIED & NEWLY CREATED FILES

| # | File Path | Type | Nature of Change |
|---|---|---|---|
| 1 | [`Lab_model.php`](file:///c:/wamp64/www/lifecare-hospital/application/modules/lab/models/Lab_model.php) | Model | Added `last_year` period logic, `getActiveDiagnosticTests()`, `getSelectedTestItemReport()`, `getAllTestItemsSummaryReport()`, and `getTestItemDateBreakdown()`. |
| 2 | [`Lab.php`](file:///c:/wamp64/www/lifecare-hospital/application/modules/lab/controllers/Lab.php) | Controller | Added `selectedTestItemReport()`, `allTestItemsSummaryReport()`, and `getTestItemBreakdownAjax()` with full RBAC & export hooks. |
| 3 | [`diag_selected_test_report.php`](file:///c:/wamp64/www/lifecare-hospital/application/modules/lab/views/diag_selected_test_report.php) | View | New view: Searchable Select2 test dropdown, quick date filter pills, KPI cards, chronological data table, grand total footer, empty state. |
| 4 | [`diag_all_test_summary_report.php`](file:///c:/wamp64/www/lifecare-hospital/application/modules/lab/views/diag_all_test_summary_report.php) | View | New view: Quick date filter pills, KPI cards, ranked summary table, AJAX "View Details" date-breakdown modal, grand total footer. |
| 5 | [`inc_diagnostic_reports_nav.php`](file:///c:/wamp64/www/lifecare-hospital/application/modules/lab/views/inc_diagnostic_reports_nav.php) | View Partial | Added sub-navigation pills for Selected Test Item Report and All Test Items Report. |
| 6 | [`menu.php`](file:///c:/wamp64/www/lifecare-hospital/application/modules/home/views/menu.php) | View | Added two sidebar report links under Sub-menu 7 (*Diagnostic Performance Reports*). |
| 7 | [`Rbac.php`](file:///c:/wamp64/www/lifecare-hospital/application/libraries/Rbac.php) | Library | Registered `diag_test_item_selected` and `diag_test_items_summary` under `diagnostic_performance`. |
| 8 | [`ReportExport.php`](file:///c:/wamp64/www/lifecare-hospital/application/libraries/ReportExport.php) | Library | Registered column schema definitions for Print, PDF, Excel (.xlsx), and CSV exports. |
| 9 | [`Export.php`](file:///c:/wamp64/www/lifecare-hospital/application/modules/report/controllers/Export.php) | Controller | Added data resolver handlers for standalone direct export endpoints. |
| 10 | [`system_syntax_lang.php`](file:///c:/wamp64/www/lifecare-hospital/application/language/english/system_syntax_lang.php) | Language | Added translations for reports, column headers, and KPI labels. |

---

## 6. VALIDATION & TESTING RESULTS

The test suite (`scratch/test_test_item_reports.php`) evaluated all 14 mandatory validation scenarios:

```
========================================================================
  TEST ITEM-WISE REPORT MODULE: 14-POINT VALIDATION SUITE
========================================================================

[PASS] Scenario 1: Date range containing multiple records for test item (Blood Urea count = 2)
[PASS] Scenario 2: Date range containing no matching records returns clean empty state (Empty records confirmed)
[PASS] Scenario 3: Invoice containing multiple test items counts each test separately (Invoice #1 has 4 distinct lab test rows)
[PASS] Scenario 4: Multiple invoices containing the same test item aggregated correctly (Blood Urea across 2 invoices gives 2 records)
[PASS] Scenario 5: Cancelled test items strictly excluded from counting (Cancelled CBC excluded; active performed total remains 1)
[PASS] Scenario 6: Pending test items not marked done are strictly excluded (Pending CBC excluded; completed count remains 1)
[PASS] Scenario 7: Records with quantity greater than one respected correctly (Base 1 + Multi-quantity 3 = 4 total performed)
[PASS] Scenario 8: Tests performed on different dates sorted chronologically ascending (Dates: 2026-10-15, 2026-10-30)
[PASS] Scenario 9: Quick date filters boundaries computed accurately (Today, This Week, This Month, Last Month, This Year, Last Year validated)
[PASS] Scenario 10: Manual custom date range successfully queries within boundary (Custom range 28-31 Oct yields 2 records)
[PASS] Scenario 11: Export engine registry includes diag_test_item_selected & diag_test_items_summary (Print, PDF, Excel, and CSV schema registered)
[PASS] Scenario 12: RBAC permissions mapped to diagnostic_performance group (Granular VIEW, PRINT, EXPORT actions protected)
[PASS] Scenario 13: SQL aggregation execution performance (Executed in 1.04 ms (threshold < 50ms))
[PASS] Scenario 14: Strict reconciliation between Report 1 and Report 2 (100% match across all test items. Grand Total: 11 == 11 eligible lab records)

========================================================================
  TEST SUMMARY: 14 PASSED, 0 FAILED (100% SUCCESS)
========================================================================
```

### 6.1 Regression Suite Results
- **Phase 10 Navigation Suite (`test_phase10_verification.py`)**: **21 / 21 Passed (100%)**.
  - All 9 submenus preserved.
  - Submenu 7 now houses 21 operational reports (including the 2 new reports).
  - 0 broken links across the entire application.
- **Phase 12 Role-Based Access Control Suite (`test_phase12_rbac.php`)**: **25 / 25 Passed (100%)**.
  - Access properly authorized for Doctors, Laboratorists, Accountants, Admins, and Superadmins.
  - Patients and unauthorized roles strictly blocked with HTTP 403 / redirection.

---

## 7. CONFIRMATION OF SCOPE & DATA INTEGRITY

1. **Zero Data Modification**:
   - Zero historical records in `lab`, `payment`, `payment_items`, or `payment_category` were altered or deleted.
   - Database record count verified before and after testing (`lab`: 12 records, `payment_items`: 11 records).
2. **Financial Integrity**:
   - No financial calculations, doctor commission rates, or referral commissions were altered.
3. **Strict Scope Control**:
   - No other roadmap phases were initiated.
   - Implementation is strictly limited to the Test Item-Wise Report Module.
