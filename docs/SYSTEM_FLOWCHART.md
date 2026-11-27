# CreativeKit 3A — Comprehensive System Flowchart & Architecture Specification

> **Project:** CreativeKit 3A Merchandising Platform  
> **Standard:** ANSI / ISO 5807 Standard Flowchart Symbols & Syntax  
> **Database:** MySQL `creativekit3a_db` (InnoDB)  
> **Subsystems:** Authentication (Login/RBAC), Catalog Search & Browsing, B2B Quotation Pipeline, Purchasing & Procurement, Inventory Movement Ledger, User Control Panel (UCP), and MIS Dashboard

---

## 1. Standard Flowchart Shape Legend (ANSI / ISO 5807)

Every diagram in this document strictly follows international standard flowcharting shapes and conventions:

| Shape Name | Mermaid Syntax | Visual Shape | Functional Purpose |
| :--- | :---: | :---: | :--- |
| **Terminator** | `(["Start / End"])` | **Oval / Stadium** | Starting point or terminal ending point of a system process. |
| **Input / Output (I/O)** | `[/"Data Input or Screen Output"/]` | **Parallelogram** | User data entry (forms, clicks, search keywords) or system output (web pages, modals, CSV exports, alerts). |
| **Process / Operation** | `["Execute Task / Calculation"]` | **Rectangle** | Internal computations, variable assignments, DOM state updates, or validations. |
| **Decision / Condition** | `{"Condition Check?"}` | **Diamond / Rhombus** | Conditional branching (`Yes` / `No`, `Valid` / `Invalid`, `Role Check`). |
| **Predefined Process / Subroutine** | `[["API Endpoint / Stored Procedure"]]` | **Double-Border Box** | External or modular routine, REST API call, PHP action handler, or MySQL stored procedure. |
| **Database Storage** | `[("Database Table / Persistence")]` | **Cylinder** | Database table query/write in `creativekit3a_db` or `localStorage` persistence. |

---

## 2. Master System Flowchart (End-to-End Platform Overview)

This high-level diagram connects all subsystems of CreativeKit 3A from initial visitor entry through storefront browsing, quoting, purchasing, inventory control, and MIS reporting.

```mermaid
flowchart TD
    START(["Start: User visits CreativeKit 3A"]) --> DISP_HOME[/"Output: Render Storefront Homepage (Hero Carousel & Mega Menu)"/]
    DISP_HOME --> CHECK_AUTH{"Is User Authenticated?"}

    CHECK_AUTH -->|No: Guest Visitor| GUEST_TOPBAR[/"Output: Show 'Sign In' & 'Register' Buttons"/]
    GUEST_TOPBAR --> STRIP_ADMIN["Process: Remove Admin Management Link from Public DOM"]

    CHECK_AUTH -->|Yes: Signed In| CHECK_ROLE{"Role == 'admin' or 'staff'?"}
    CHECK_ROLE -->|No: Customer| CUST_TOPBAR[/"Output: Display Customer Profile Badge & 'Sign Out'"/]
    CHECK_ROLE -->|Yes: Admin / Staff| INJECT_ADMIN["Process: Inject Management Portal Button & Hover Sidebar"]
    INJECT_ADMIN --> ADMIN_TOPBAR[/"Output: Display Staff / Management Portal Access"/]

    STRIP_ADMIN --> CHOOSE_ACTION{"Select System Module"}
    CUST_TOPBAR --> CHOOSE_ACTION
    ADMIN_TOPBAR --> CHOOSE_ACTION

    CHOOSE_ACTION -->|Search or Browse Catalog| SUB_SEARCH[["Figure 2: Catalog Search & Browsing Flow"]]
    CHOOSE_ACTION -->|Request B2B Quotation| SUB_QUOTE[["Figure 3: Multi-Step Quotation Pipeline"]]
    CHOOSE_ACTION -->|Admin: Purchasing| SUB_PO[["Figure 4: Purchasing & Procurement Flow"]]
    CHOOSE_ACTION -->|Admin: Inventory| SUB_INV[["Figure 5: Inventory & Double-Entry Ledger"]]
    CHOOSE_ACTION -->|Admin: User Control Panel| SUB_UCP[["Figure 6: User Control Panel (RBAC)"]]
    CHOOSE_ACTION -->|Admin: MIS & Audit Logs| SUB_MIS[["Figure 7: MIS Dashboard & Activity Log Flow"]]

    SUB_SEARCH --> END_SYS(["End: Catalog Rendered / Session Active"])
    SUB_QUOTE --> END_SYS
    SUB_PO --> END_SYS
    SUB_INV --> END_SYS
    SUB_UCP --> END_SYS
    SUB_MIS --> END_SYS
```

---

## 3. Individual System Flowcharts (Figures 1–7)

### Figure 1: User Login, Registration & Role-Based Access Control (RBAC)

Illustrates credential validation, Bcrypt password verification, `activity_log` audit recording (`action = 'login'` / `'register'` / `'logout'`), and role-based portal routing across [auth/login.php](../auth/login.php), [auth/register.php](../auth/register.php), and [assets/js/admin-system.js](../assets/js/admin-system.js).

```mermaid
flowchart TD
    F1_START(["Start: Figure 1 — User Log In / Auth"]) --> F1_MODE{"Auth Action?"}

    F1_MODE -->|Register New Customer| F1_REG_IN[/"Input: First Name, Last Name, Email, Phone, Password"/]
    F1_REG_IN --> F1_REG_VAL{"Email unique & Password >= 6 chars?"}
    F1_REG_VAL -->|No| F1_REG_ERR[/"Output: Display Validation / Duplicate Email Error"/]
    F1_REG_ERR --> F1_REG_IN
    F1_REG_VAL -->|Yes| F1_REG_HASH["Process: Hash Password with Bcrypt (PASSWORD_DEFAULT)"]
    F1_REG_HASH --> F1_REG_DB[("Database: INSERT INTO users (role='customer') & activity_log (action='register')")]
    F1_REG_DB --> F1_SESS["Process: Regenerate Session ID & Store User Session"]

    F1_MODE -->|Sign In| F1_IN_CREDS[/"Input: Email Address & Password"/]
    F1_IN_CREDS --> F1_API_AUTH[["Subroutine: POST auth/login.php or verify ck_user credentials"]]
    F1_API_AUTH --> F1_DB_USER[("Database: SELECT * FROM users WHERE email = ? LIMIT 1")]
    F1_DB_USER --> F1_CHK_ACTIVE{"User exists & is_active == 1?"}

    F1_CHK_ACTIVE -->|No| F1_ERR_AUTH[/"Output: Display 'Invalid Email or Password' / 'Account Deactivated'"/]
    F1_ERR_AUTH --> F1_END_FAIL(["End: Login Rejected"])

    F1_CHK_ACTIVE -->|Yes| F1_CHK_PW{"Password Matches Bcrypt Hash?"}
    F1_CHK_PW -->|No| F1_ERR_AUTH
    F1_CHK_PW -->|Yes| F1_DB_AUDIT[("Database: UPDATE users.last_login_at & INSERT INTO activity_log (action='login')")]
    F1_DB_AUDIT --> F1_SESS

    F1_SESS --> F1_CHK_ROLE{"Check user.role"}
    F1_CHK_ROLE -->|role == 'customer'| F1_CUST_UI[/"Output: Redirect to Storefront & Display Customer Topbar"/]
    F1_CUST_UI --> F1_END_CUST(["End: Customer Session Active"])

    F1_CHK_ROLE -->|role == 'admin' or 'staff'| F1_ADMIN_UI["Process: Unlock Staff Portal (pages/dashboard.php) & Management Portal (/admin)"]
    F1_ADMIN_UI --> F1_OUT_ADMIN[/"Output: Render Admin Portal, Sidebar & RBAC Controls"/]
    F1_OUT_ADMIN --> F1_END_ADMIN(["End: Admin / Staff Session Active"])
```

---

### Figure 2: Universal Catalog Search & Product Browsing System

Illustrates keyword search (`?q=` / `?s=`), Mega Menu category navigation (`?cat=`), MySQL 4-table `JOIN` queries, static fallback rendering for Vercel deployments, and product variant selection.

```mermaid
flowchart TD
    F2_START(["Start: Figure 2 — Catalog Search & Browsing"]) --> F2_IN_ENTRY{"User Navigation Source?"}

    F2_IN_ENTRY -->|Header Search Bar| F2_IN_KW[/"Input: Enter Search Keyword & Click Search Icon or Press Enter"/]
    F2_IN_KW --> F2_CHK_EMPTY{"Is keyword non-empty?"}
    F2_CHK_EMPTY -->|No| F2_FOCUS["Process: Focus Search Input & Abort Submit"]
    F2_FOCUS --> F2_END_IDLE(["End: Search Cancelled"])

    F2_CHK_EMPTY -->|Yes| F2_CHK_PAGE{"Already on Category Page?"}
    F2_CHK_PAGE -->|Yes| F2_LIVE[["Subroutine: Filter DOM Product Cards Live (category-page.js)"]]
    F2_CHK_PAGE -->|No| F2_REDIR_S["Process: Redirect to category-page.php?q=keyword (or .html?s=keyword)"]

    F2_IN_ENTRY -->|Mega Menu / Category Card| F2_REDIR_C["Process: Redirect to category-page with ?cat=category_slug"]

    F2_REDIR_S --> F2_CTRL[["Subroutine: Execute Category Page Controller"]]
    F2_REDIR_C --> F2_CTRL
    F2_LIVE --> F2_DISP_GRID

    F2_CTRL --> F2_CHK_DB{"Is MySQL PDO Available?"}
    F2_CHK_DB -->|Yes: PHP Backend| F2_SQL_JOIN[("Database: SELECT products JOIN categories (parent & sub) LEFT JOIN product_variants WHERE LIKE ?")]
    F2_CHK_DB -->|No: Static Fallback| F2_STATIC_DATA["Process: Query In-Memory STATIC_CATEGORY_DATA Array"]

    F2_SQL_JOIN --> F2_DISP_GRID[/"Output: Display Filtered Product Grid, Category Header & Subcategory Tabs"/]
    F2_STATIC_DATA --> F2_DISP_GRID

    F2_DISP_GRID --> F2_CLICK_PROD[/"Input: Click Product Card (e.g., Car Charger Adapters)"/]
    F2_CLICK_PROD --> F2_FETCH_VAR[("Database: SELECT * FROM product_variants WHERE product_id = ?")]
    F2_FETCH_VAR --> F2_OUT_PROD[/"Output: Display Variant Tabs, Specs, Pricing & 'Request a Quote' CTA"/]
    F2_OUT_PROD --> F2_END(["End: Product Detail Viewed"])
```

---

### Figure 3: Multi-Step B2B Quotation Pipeline & Staff Review Workflow

Illustrates the customer 3-step quotation request modal (`includes/quote-modal.php`), backend insertion into `quote_requests`, and the staff processing lifecycle (`new` → `reviewing` → `quoted` → `accepted` / `declined`).

```mermaid
flowchart TD
    F3_START(["Start: Figure 3 — B2B Quotation System"]) --> F3_OPEN[/"Input: User Clicks 'Request a Quote' or Cart Button"/]
    F3_OPEN --> F3_STEP1[/"Output: Display Modal Step 1 — Contact Information"/]
    F3_STEP1 --> F3_IN1[/"Input: Full Name, Company, Email, Phone"/]
    F3_IN1 --> F3_VAL1{"Required Contact Fields Valid?"}
    F3_VAL1 -->|No| F3_ERR1[/"Output: Show Step 1 Field Validation Message"/]
    F3_ERR1 --> F3_IN1

    F3_VAL1 -->|Yes| F3_STEP2[/"Output: Display Modal Step 2 — Product & Volume Specs"/]
    F3_STEP2 --> F3_IN2[/"Input: Target Product, Quantity, Estimated Budget, Target Deadline"/]
    F3_IN2 --> F3_VAL2{"Is Quantity >= 1?"}
    F3_VAL2 -->|No| F3_ERR2[/"Output: Show Minimum Quantity Error"/]
    F3_ERR2 --> F3_IN2

    F3_VAL2 -->|Yes| F3_STEP3[/"Output: Display Modal Step 3 — Branding & Customization Details"/]
    F3_STEP3 --> F3_IN3[/"Input: Logo Imprint Specs, Packaging Notes & Click Submit"/]
    F3_IN3 --> F3_POST[["Subroutine: POST to /api-php/quote.php or /api/quote"]]

    F3_POST --> F3_DB_INS[("Database: INSERT INTO quote_requests (status='new') & activity_log (action='submit_quote')")]
    F3_DB_INS --> F3_SUCCESS[/"Output: Display Step 4 Confirmation Screen with Reference Number"/]

    F3_SUCCESS --> F3_STAFF_VIEW[/"Input: Staff / Admin Opens Quotes Page (pages/quotes.php)"/]
    F3_STAFF_VIEW --> F3_ASSIGN["Process: Admin Assigns Quote (assigned_to = staff_user_id) & Logs 'assign_quote'"]
    F3_ASSIGN --> F3_UPDATE_STAT["Process: Staff Updates Status to 'reviewing' -> Computes Pricing -> Sends Proposal"]
    F3_UPDATE_STAT --> F3_DB_UPD[("Database: UPDATE quote_requests SET status='quoted' & INSERT activity_log (action='update_quote')")]

    F3_DB_UPD --> F3_DEC{"Customer Accepts Official Quote?"}
    F3_DEC -->|Yes| F3_ACC[("Database: UPDATE quote_requests SET status='accepted'")]
    F3_DEC -->|No| F3_DECL[("Database: UPDATE quote_requests SET status='declined'")]

    F3_ACC --> F3_END(["End: Quote Approved for Fulfillment"])
    F3_DECL --> F3_END_CLOSED(["End: Quote Closed / Archived"])
```

---

### Figure 4: Enterprise Purchasing & Procurement System

Illustrates supplier management, Purchase Order (`PO-2026-XXX`) creation, goods receipt validation, and automated inventory restocking in [admin/index.html](../admin/index.html) and [assets/js/admin-system.js](../assets/js/admin-system.js).

```mermaid
flowchart TD
    F4_START(["Start: Figure 4 — Purchasing & Procurement"]) --> F4_OPEN[/"Input: Operator Opens Purchasing System Tab"/]
    F4_OPEN --> F4_LOAD[("Database: Load purchase_orders & suppliers (ck_admin_purchasing, ck_admin_suppliers)")]
    F4_LOAD --> F4_DISP[/"Output: Render Active POs, Total Spend KPI & Approved Suppliers Table"/]

    F4_DISP --> F4_ACT{"Select Purchasing Action"}

    F4_ACT -->|Create New Purchase Order| F4_MODAL[/"Output: Open Create PO Modal (#modal-create-po)"/]
    F4_MODAL --> F4_IN_PO[/"Input: Select Supplier, Product Name, Quantity, Unit Cost, Destination Warehouse, Expected Date"/]
    F4_IN_PO --> F4_VAL{"Qty > 0 & Unit Cost > 0 & Item Name Valid?"}
    F4_VAL -->|No| F4_ERR[/"Output: Alert Missing / Invalid PO Values"/]
    F4_ERR --> F4_IN_PO

    F4_VAL -->|Yes| F4_GEN_PO["Process: Compute PO Total (qty * unitCost) & Generate PO-2026-XXX"]
    F4_GEN_PO --> F4_SAVE_PO[("Database: INSERT Purchase Order (status='sent') & Log Audit Event")]
    F4_SAVE_PO --> F4_OUT_PO[/"Output: Refresh Purchasing Table & MIS Procurement Spend KPI"/]
    F4_OUT_PO --> F4_END_CREATE(["End: Purchase Order Issued to Supplier"])

    F4_ACT -->|Receive Delivered Goods| F4_CONF{"Confirm Goods Receipt for PO?"}
    F4_CONF -->|No| F4_END_CANCEL(["End: Receipt Cancelled"])
    F4_CONF -->|Yes| F4_SP_REC[["Subroutine: Execute AdminApp.receivePO / CALL receive_purchase_order()"]]
    F4_SP_REC --> F4_DB_REC[("Database: Set PO status='received', Increment Inventory Qty, Insert 'stock_in' Movement & Audit Log")]
    F4_DB_REC --> F4_REFRESH[/"Output: Update PO Row to 'Fulfilled', Refresh Stock & MIS KPIs"/]
    F4_REFRESH --> F4_END_REC(["End: Goods Received & Inventory Restocked"])
```

---

### Figure 5: Inventory Management & Double-Entry Stock Movement Ledger

Illustrates warehouse stock monitoring, low-stock threshold alerts, manual stock adjustments (`Stock In`, `Stock Out`, `Adjustment`), and ACID negative-balance guardrails.

```mermaid
flowchart TD
    F5_START(["Start: Figure 5 — Inventory & Stock Ledger"]) --> F5_LOAD[("Database: Load inventory_items & inventory_movements (ck_admin_inventory, ck_admin_movements)")]
    F5_LOAD --> F5_CALC["Process: Compute Available Stock = (On Hand - Reserved) & Check Reorder Threshold"]

    F5_CALC --> F5_CHK_LOW{"On Hand <= Reorder Level?"}
    F5_CHK_LOW -->|Yes| F5_BADGE_LOW[/"Output: Highlight Row with Red/Amber Low-Stock Warning & Increment Alert Badge"/]
    F5_CHK_LOW -->|No| F5_BADGE_OK[/"Output: Display Green Healthy Stock Indicator"/]

    F5_BADGE_LOW --> F5_ACT{"User Action?"}
    F5_BADGE_OK --> F5_ACT

    F5_ACT -->|Search / Filter Stock| F5_FILTER["Process: Filter Inventory Table by SKU/Name, Category & Stock Status"]
    F5_FILTER --> F5_OUT_FILT[/"Output: Render Filtered Warehouse Table"/]
    F5_OUT_FILT --> F5_END_FILT(["End: Filtered Inventory Displayed"])

    F5_ACT -->|Adjust Stock Level| F5_ADJ_MOD[/"Output: Open Stock Adjustment Modal (#modal-stock-adjust)"/]
    F5_ADJ_MOD --> F5_ADJ_IN[/"Input: Adjustment Type (stock_in / stock_out / set), Quantity & Remarks"/]
    F5_ADJ_IN --> F5_SP_MOV[["Subroutine: CALL record_stock_movement() / submitStockAdjust()"]]

    F5_SP_MOV --> F5_TX["Process: START TRANSACTION (ACID)"]
    F5_TX --> F5_CHK_BAL{"Resulting quantity_on_hand >= 0?"}
    F5_CHK_BAL -->|No: Negative Stock Violation| F5_ROLLBACK["Process: ROLLBACK TRANSACTION"]
    F5_ROLLBACK --> F5_ERR_BAL[/"Output: Reject Update ('Cannot remove more items than available on hand')"/]
    F5_ERR_BAL --> F5_END_FAIL(["End: Stock Adjustment Aborted"])

    F5_CHK_BAL -->|Yes: Valid Balance| F5_COMMIT[("Database: UPDATE inventory_items, INSERT inventory_movements & Audit Log, COMMIT")]
    F5_COMMIT --> F5_OUT_OK[/"Output: Refresh Stock Table, Movement Ledger & MIS Valuation KPI"/]
    F5_OUT_OK --> F5_END_OK(["End: Stock & Ledger Synchronized"])
```

---

### Figure 6: User Control Panel (UCP) & RBAC Account Governance

Illustrates administrator user provisioning, role assignment (`admin`, `staff`, `customer`), status activation/suspension, root administrator protection, and CSV directory export.

```mermaid
flowchart TD
    F6_START(["Start: Figure 6 — User Control Panel"]) --> F6_LOAD[("Database: Load User Directory (ck_admin_users) & Sync Active ck_user")]
    F6_LOAD --> F6_KPI["Process: Count Total Users, Admins, Staff Officers & Customers"]
    F6_KPI --> F6_DISP[/"Output: Render 4 User KPI Cards & Searchable RBAC Table"/]

    F6_DISP --> F6_ACT{"Select UCP Action"}

    F6_ACT -->|Search / Filter| F6_FILT["Process: Filter Directory by Name/Email/Phone, Role & Active/Inactive Status"]
    F6_FILT --> F6_DISP

    F6_ACT -->|Add New User| F6_ADD_MOD[/"Output: Open Add New User Modal (#modal-add-user)"/]
    F6_ADD_MOD --> F6_ADD_IN[/"Input: First/Last Name, Email, Phone, Role, Status & Initial Password"/]
    F6_ADD_IN --> F6_ADD_VAL{"Valid Email, Password >= 6 & No Duplicate Email?"}
    F6_ADD_VAL -->|No| F6_ADD_ERR[/"Output: Show Validation / Duplicate Email Alert"/]
    F6_ADD_ERR --> F6_ADD_IN
    F6_ADD_VAL -->|Yes| F6_ADD_DB[("Database: INSERT User into ck_admin_users & Log 'User Provisioned'")]
    F6_ADD_DB --> F6_DONE_ACT[/"Output: Refresh User Table, KPIs & Show Toast Notification"/]

    F6_ACT -->|Edit Role / Reset Password| F6_EDIT_MOD[/"Output: Open Edit User Modal (#modal-edit-user)"/]
    F6_EDIT_MOD --> F6_EDIT_PROT{"Is Root Admin (admin@creativekit3a.com)?"}
    F6_EDIT_PROT -->|Yes| F6_LOCK["Process: Lock Role & Status Fields to Prevent Self-Lockout"]
    F6_EDIT_PROT -->|No| F6_UNLOCK["Process: Allow Full Role, Status & Password Reset Edits"]
    F6_LOCK --> F6_EDIT_SAVE[("Database: UPDATE User Profile in ck_admin_users & Log 'User Updated'")]
    F6_UNLOCK --> F6_EDIT_SAVE
    F6_EDIT_SAVE --> F6_DONE_ACT

    F6_ACT -->|Toggle Active / Delete User| F6_PROT_CHK{"Is Target Root Admin?"}
    F6_PROT_CHK -->|Yes| F6_DENY[/"Output: Alert 'Root administrator account is protected'"/]
    F6_DENY --> F6_END_ACT(["End: Action Blocked"])
    F6_PROT_CHK -->|No| F6_CONF{"Confirm Status Toggle or Permanent Delete?"}
    F6_CONF -->|Yes| F6_MUT_DB[("Database: Update Status or Remove Record & Write Audit Log")]
    F6_MUT_DB --> F6_DONE_ACT

    F6_ACT -->|Export Users CSV| F6_CSV[["Subroutine: Build UTF-8 CSV & Trigger CreativeKit3A_Users_Export.csv Download"]]
    F6_CSV --> F6_DONE_ACT
    F6_DONE_ACT --> F6_END(["End: UCP Operation Complete"])
```

---

### Figure 7: Management Information System (MIS) Dashboard & Audit Trail

Illustrates the executive telemetry loop, hover-activated left side navigation bar, real-time KPI aggregation, warehouse capacity snapshot, and recent website audit trail tracking.

```mermaid
flowchart TD
    F7_START(["Start: Figure 7 — MIS Dashboard & Audit Trail"]) --> F7_NAV{"Navigation Interaction?"}

    F7_NAV -->|Hover Left Edge / Click Pin| F7_SIDE["Process: Slide Open Dark Hover Sidebar (or Dock Pinned Mode)"]
    F7_NAV -->|Click Top Tab / Sidebar Link| F7_TAB["Process: Synchronize Active Tab & Sidebar Highlight State"]

    F7_SIDE --> F7_TAB
    F7_TAB --> F7_AGG[["Subroutine: Execute renderMISDashboard() / Live SQL Aggregations"]]

    F7_AGG --> F7_DB_READ[("Database: Read inventory_items, purchase_orders, quote_requests & activity_log")]
    F7_DB_READ --> F7_CALC["Process: Compute Inventory Valuation, Total Units, Low-Stock Count, Procurement Spend & Category %"]
    F7_CALC --> F7_OUT_KPI[/"Output: Update 5 Executive KPI Cards, Progress Bars & Warehouse Utilization"/]

    F7_OUT_KPI --> F7_LOG_CHK["Process: Fetch Latest 8–10 Recent Website Actions (login, create_product, submit_quote, stock_in)"]
    F7_LOG_CHK --> F7_OUT_LOG[/"Output: Render Enterprise Activity Log & Audit Trail Table"/]

    F7_OUT_LOG --> F7_EXP{"Export Inventory Report Clicked?"}
    F7_EXP -->|Yes| F7_GEN_CSV[["Subroutine: Generate & Download CreativeKit3A_Inventory_Report.csv + Log Export"]]
    F7_GEN_CSV --> F7_END(["End: MIS Analytics Updated & Exported"])
    F7_EXP -->|No| F7_END
```

---
*Document approved for CreativeKit 3A architecture documentation and academic project review.*
