<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Panel — JC Pest & Hygiene</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../assets/dashboard.css">
</head>
<body>

<!-- MAIN ADMIN -->
<div id="adminMain" >
  <aside class="sidebar">
    <div class="sidebar-brand">
      <div class="brand-logo">JC</div>
      <div class="brand-text">
        <span class="brand-name">JC Pest</span>
        <span class="brand-sub">Admin Panel</span>
      </div>
    </div>
    <nav class="sidebar-nav">
      <a class="nav-item active" onclick="">📊 Dashboard</a>
      <a class="nav-item" onclick="">📋 All Reports</a>
      <a class="nav-item" href="index.html" target="_blank">➕ New Report</a>
    </nav>
    <div class="sidebar-footer">
      <span id="adminName"></span>
      <button onclick="" class="btn-logout">Logout</button>
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
          <input type="text" id="searchInput" placeholder="Search SR, address, customer..." class="search-input" oninput="">
          <select id="statusFilter" onchange="" class="filter-select">
            <option value="">All Status</option>
            <option value="draft">Draft</option>
            <option value="signed">Signed</option>
            <option value="completed">Completed</option>
          </select>
        </div>
      </div>
      <div id="reportsTable"></div>
    </div>

    <!-- DETAIL VIEW -->
    <div id="viewDetail" class="view">
      <div class="page-header">
        <button class="btn-back-detail" onclick="">← Back to Reports</button>
        <div class="detail-actions">
          <button class="btn-edit" onclick="">✏️ Edit</button>
          <button class="btn-pdf" onclick="">🖨️ Print / PDF</button>
        </div>
      </div>
      <div id="reportDetail"></div>
    </div>
  </main>
</div>

<script src="../assets/dahsboard.js"></script>
</body>
</html>
