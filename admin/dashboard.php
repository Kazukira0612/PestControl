<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Panel — JC Pest & Hygiene</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="stylesheet" href="../assets/dashboard.css">
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
</head>
<body>

<div id="adminMain">
  <aside class="sidebar">
    <div class="sidebar-brand">
      <div class="brand-logo">JC</div>
      <div class="brand-text">
        <span class="brand-name">JC Pest</span>
        <span class="brand-sub">Admin Panel</span>
      </div>
    </div>
    <nav class="sidebar-nav">
      <a class="nav-item active" onclick="showView('viewDashboard')">📊 Dashboard</a>
      <a class="nav-item" onclick="showView('viewReports')">📋 All Reports</a>
      <a class="nav-item" onclick="showView('viewApplicators')">👤 Applicator Register</a>
      <a class="nav-item" onclick="showView('viewCustomers')">🏢 Customer Register</a>
      <a class="nav-item" href="../index.php" target="_blank">➕ New Report</a>
    </nav>
    <div class="sidebar-footer">
      <span id="adminName"></span>
      <button type="button" class="btn-logout" onclick="handleLogout()">Logout</button>
    </div>
  </aside>

  <main class="admin-main">

    <!-- DASHBOARD VIEW -->
    <div id="viewDashboard" class="view active">
      <div class="page-header">
        <h1>Dashboard</h1>
        <span class="page-date" id="pageDate"></span>
      </div>
      <div class="stats-grid" id="statsGrid">
        <div class="stat-card">
          <div class="stat-num" id="statTotal">—</div>
          <div class="stat-label">Total Reports</div>
        </div>
        <div class="stat-card green">
          <div class="stat-num" id="statSigned">—</div>
          <div class="stat-label">Signed</div>
        </div>
        <div class="stat-card gold">
          <div class="stat-num" id="statDraft">—</div>
          <div class="stat-label">Drafts</div>
        </div>
        <div class="stat-card dark">
          <div class="stat-num" id="statToday">—</div>
          <div class="stat-label">Today</div>
        </div>
      </div>
      <div class="recent-label">Recent Reports</div>
      <div id="recentReports"></div>
    </div>

    <!-- REPORTS VIEW -->
    <div id="viewReports" class="view">
      <div class="page-header">
        <h1>All Reports</h1>
        <div class="header-controls">
          <input type="text" id="searchInput" placeholder="Search SR, address, customer..." class="search-input">
          <select id="statusFilter" class="filter-select">
            <option value="">All Status</option>
            <option value="draft">Draft</option>
            <option value="signed">Signed</option>
          </select>
        </div>
      </div>
      <div id="reportsTable"></div>
    </div>

    <!-- DETAIL VIEW -->
    <div id="viewDetail" class="view">
      <div class="page-header">
        <button class="btn-back-detail">← Back to Reports</button>
      </div>
      <div id="reportDetail"></div>
    </div>

    <!-- APPLICATORS VIEW -->
    <div id="viewApplicators" class="view">
      <div class="page-header">
        <h1>Applicators</h1>
        <div class="header-controls">
          <input type="text" id="searchApplicator" placeholder="Search ID or name…" class="search-input">
          <button class="btn btn-success" onclick="openApplicatorModal('add')">+ Add Applicator</button>
        </div>
      </div>
      <div class="panel">
        <div class="panel-header">
          <span class="panel-title">Registered Applicators</span>
          <span id="applicatorCount" style="font-size:12px;color:var(--text3)"></span>
        </div>
        <div id="applicatorsTable"></div>
      </div>
    </div>

    <!-- CUSTOMERS VIEW -->
    <div id="viewCustomers" class="view">
      <div class="page-header">
        <h1>Customers</h1>
        <div class="header-controls">
          <input type="text" id="searchCustomer" placeholder="Search company, lot…" class="search-input">
          <button class="btn btn-success" onclick="openCustomerModal('add')">+ Add Customer</button>
        </div>
      </div>
      <div class="panel">
        <div class="panel-header">
          <span class="panel-title">Customer Companies</span>
          <span id="customerCount" style="font-size:12px;color:var(--text3)"></span>
        </div>
        <div id="customersTable"></div>
      </div>
    </div>

  </main>
</div>

<!-- MODAL — ADD / EDIT APPLICATOR -->
<div class="modal-overlay" id="applicatorModal">
  <div class="modal">
    <div class="modal-header">
      <h3 id="applicatorModalTitle">Add Applicator</h3>
      <button class="modal-close">×</button>
    </div>
    <div class="modal-body">
      <div class="modal-error" id="applicatorError"></div>
      <input type="hidden" id="applicatorEditId">
      <div class="form-group">
        <label>Full Name</label>
        <input type="text" id="appName" placeholder="e.g. Ahmad bin Razak">
      </div>
      <div class="form-group">
        <label>Applicator ID</label>
        <input type="text" id="appId" placeholder="e.g. APP-001">
      </div>
      <div class="form-group">
        <label>License No.</label>
        <input type="text" id="appLicense" placeholder="e.g. PCO-12345">
      </div>
      <div class="form-group">
        <label id="appPassLabel">Password</label>
        <input type="password" id="appPass" placeholder="Min 6 characters">
        <div class="pw-strength"><div class="pw-strength-bar" id="pwBar"></div></div>
      </div>
      <div class="form-group">
        <label>Confirm Password</label>
        <input type="password" id="appPassConfirm" placeholder="Re-enter password">
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-ghost">Cancel</button>
      <button class="btn btn-success">Save Applicator</button>
    </div>
  </div>
</div>

<!-- MODAL — ADD / EDIT CUSTOMER -->
<div class="modal-overlay" id="customerModal">
  <div class="modal">
    <div class="modal-header">
      <h3 id="customerModalTitle">Add Customer</h3>
      <button class="modal-close">×</button>
    </div>
    <div class="modal-body">
      <div class="modal-error" id="customerError"></div>
      <input type="hidden" id="customerEditId">
      <div class="form-group">
        <label>Company Name</label>
        <input type="text" id="custName" placeholder="e.g. Kilang Biscuit Sdn Bhd">
      </div>
      <div class="form-group">
        <label>Contact Person</label>
        <input type="text" id="custContact" placeholder="e.g. Lim Wei Ming">
      </div>
      <div class="form-group">
        <label>Email Address</label>
        <input type="email" id="custEmail" placeholder="e.g. manager@company.com">
      </div>
      <div class="form-group">
        <label>Phone Number</label>
        <input type="tel" id="custPhone" placeholder="e.g. 03-1234 5678">
      </div>
      <div class="form-group">
        <label>Factories / Lot Numbers</label>
        <div id="factoryList"></div>
        <button type="button" class="btn btn-ghost" style="margin-top:6px;font-size:12px" onclick="addFactoryRow()">+ Add Factory</button>
     </div>
      <div class="form-group">
        <label>Address</label>
        <textarea id="custAddress" placeholder="Full factory address…"></textarea>
      </div>
      <div class="form-group">
        <label>Notes</label>
        <textarea id="custNotes" placeholder="Any special requirements or remarks…"></textarea>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-ghost">Cancel</button>
      <button class="btn btn-success">Save Customer</button>
    </div>
  </div>
</div>

<!-- TOAST -->
<div class="toast" id="toast"></div>

<script src="assets/js/form.js"></script>    
<script src="../assets/dashboard.js"></script>
</body>
</html>