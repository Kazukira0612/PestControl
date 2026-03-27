/* ─── VIEW NAVIGATION ──────────────────────────── */
function showView(viewName) {
  document.querySelectorAll('.view').forEach(v => v.classList.remove('active'));
  document.querySelectorAll('.nav-item').forEach(n => n.classList.remove('active'));
 
  const el = document.getElementById(viewName);
  if (el) el.classList.add('active');
 
  if (event && event.target) event.target.classList.add('active');
 
  // Load data for the view
  if (viewName === 'viewDashboard') loadDashboard();
  if (viewName === 'viewReports')   loadReports();
  if (viewName === 'viewApplicators') loadApplicators();
  if (viewName === 'viewCustomers') loadCustomers();
}

/* ─── HTML ESCAPE HELPER ───────────────────────── */
function esc(str) {
  return String(str ?? '')
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&#039;');
}

/* ─── DASHBOARD ────────────────────────────────── */
async function loadDashboard() {
  const data = await request('dashboardStats');
  if (!data.success) return;
 
  document.getElementById('statTotal').textContent  = data.total  ?? 0;
  document.getElementById('statSigned').textContent = data.signed ?? 0;
  document.getElementById('statDraft').textContent  = data.draft  ?? 0;
  document.getElementById('statToday').textContent  = data.today  ?? 0;
 
  const container = document.getElementById('recentReports');
  if (!data.recent || data.recent.length === 0) {
    container.innerHTML = '<p class="empty-state">No reports yet.</p>';
    return;
  }
 
  container.innerHTML = `
    <table class="data-table">
      <thead><tr>
        <th>SR No.</th><th>Company</th><th>Address</th>
        <th>Plan Date</th><th>Status</th><th></th>
      </tr></thead>
      <tbody>
        ${data.recent.map(r => `
          <tr>
            <td><strong>${esc(r.srNum)}</strong></td>
            <td>${esc(r.comName)}</td>
            <td class="text-muted">${esc(r.servAdd)}</td>
            <td>${formatDate(r.planDate)}</td>
            <td><span class="badge badge-${esc(r.status)}">${esc(r.status)}</span></td>
            <td><button class="btn-link" onclick="viewReport('${esc(r.srNum)}')">View</button></td>
          </tr>`).join('')}
      </tbody>
    </table>`;
}

/* ─── REPORTS ──────────────────────────────────── */
let allReports = [];
 
async function loadReports() {
  const data = await request('getReports');
  if (!data.success) return;
  allReports = data.reports || [];
  renderReportsTable(allReports);
 
  // Wire up search & filter
  document.getElementById('searchInput').oninput = filterReports;
  document.getElementById('statusFilter').onchange = filterReports;
}
 
function filterReports() {
  const q      = document.getElementById('searchInput').value.toLowerCase();
  const status = document.getElementById('statusFilter').value;
  const filtered = allReports.filter(r => {
    const matchQ = !q ||
      r.srNum?.toLowerCase().includes(q) ||
      r.comName?.toLowerCase().includes(q) ||
      r.servAdd?.toLowerCase().includes(q);
    const matchS = !status || r.status === status;
    return matchQ && matchS;
  });
  renderReportsTable(filtered);
}
 
function renderReportsTable(reports) {
  const el = document.getElementById('reportsTable');
  if (!reports.length) {
    el.innerHTML = '<p class="empty-state">No reports found.</p>';
    return;
  }
  el.innerHTML = `
    <table class="data-table">
      <thead><tr>
        <th>SR No.</th><th>Company</th><th>Address</th>
        <th>Plan Date</th><th>Treatment</th><th>Status</th><th></th>
      </tr></thead>
      <tbody>
        ${reports.map(r => `
          <tr>
            <td><strong>${esc(r.srNum)}</strong></td>
            <td>${esc(r.comName)}</td>
            <td class="text-muted">${esc(r.servAdd)}</td>
            <td>${formatDate(r.planDate)}</td>
            <td>${esc(r.purposeTreat)}</td>
            <td><span class="badge badge-${esc(r.status)}">${esc(r.status)}</span></td>
            <td>
              <button class="btn-link" onclick="viewReport('${esc(r.srNum)}')">View</button>
            </td>
          </tr>`).join('')}
      </tbody>
    </table>`;
}
 
/* ─── REPORT DETAIL ────────────────────────────── */
async function viewReport(srNum) {
  showView('viewDetail');
  const data = await request('getReportDetail', { srNum });
  if (!data.success) {
    document.getElementById('reportDetail').innerHTML = '<p class="empty-state">Failed to load report.</p>';
    return;
  }
  const r = data.report;
  const a = data.applicator || {};
  const pests = (data.pests || []).map(p => p.pestName).join(', ');
  const team  = (data.team  || []).map(m => m.empName).join(', ');
 
  document.getElementById('reportDetail').innerHTML = `
    <div class="detail-grid">
      <div class="detail-section">
        <h3>Service Report</h3>
        <table class="detail-table">
          <tr><th>SR Number</th><td>${esc(r.srNum)}</td></tr>
          <tr><th>Company</th><td>${esc(r.comName)}</td></tr>
          <tr><th>Address</th><td>${esc(r.servAdd)}</td></tr>
          <tr><th>Plan Date</th><td>${formatDate(r.planDate)}</td></tr>
          <tr><th>Purpose</th><td>${esc(r.purposeTreat)}</td></tr>
          <tr><th>Premise Type</th><td>${esc(r.premiseType)}</td></tr>
          <tr><th>Treatment Time</th><td>${esc(r.treatTime)}</td></tr>
          <tr><th>Pests Targeted</th><td>${pests || '—'}</td></tr>
          <tr><th>Special Instructions</th><td>${esc(r.specIstruct)}</td></tr>
          <tr><th>Remarks</th><td>${esc(r.remarks)}</td></tr>
          <tr><th>Customer Feedback</th><td>${esc(r.custFeedback)}</td></tr>
        </table>
      </div>
      <div class="detail-section">
        <h3>Treatment Details</h3>
        <table class="detail-table">
          <tr><th>Team Leader</th><td>${esc(a.leaderName)}</td></tr>
          <tr><th>Team Members</th><td>${team || '—'}</td></tr>
          <tr><th>Treatment Date</th><td>${formatDate(a.treatDate)}</td></tr>
          <tr><th>Vehicle No.</th><td>${esc(a.vehicleNo)}</td></tr>
          <tr><th>Time In</th><td>${esc(a.timeIn)}</td></tr>
          <tr><th>Time Out</th><td>${esc(a.timeOut)}</td></tr>
          <tr><th>Customer Rep</th><td>${esc(a.custName)}</td></tr>
          <tr><th>Cust. Sign Date</th><td>${formatDate(a.custSignDate)}</td></tr>
        </table>
      </div>
    </div>`;
 
  // Wire back button
  document.querySelector('.btn-back-detail').onclick = () => showView('viewReports');
}

/* ─── APPLICATORS ──────────────────────────────── */
let allApplicators = [];
 
async function loadApplicators() {
  const data = await request('getApplicators');
  if (!data.success) return;
  allApplicators = data.applicators || [];
  renderApplicatorsTable(allApplicators);
  document.getElementById('applicatorCount').textContent = `${allApplicators.length} registered`;
  document.getElementById('searchApplicator').oninput = filterApplicators;
}
 
function filterApplicators() {
  const q = document.getElementById('searchApplicator').value.toLowerCase();
  renderApplicatorsTable(allApplicators.filter(a =>
    a.empId?.toLowerCase().includes(q) || a.empName?.toLowerCase().includes(q)
  ));
}
 
function renderApplicatorsTable(list) {
  const el = document.getElementById('applicatorsTable');
  if (!list.length) {
    el.innerHTML = '<p class="empty-state">No applicators found.</p>';
    return;
  }
  el.innerHTML = `
    <table class="data-table">
      <thead><tr>
        <th>ID</th><th>Name</th><th>License No.</th><th>Actions</th>
      </tr></thead>
      <tbody>
        ${list.map(a => `
          <tr>
            <td><code>${esc(a.empId)}</code></td>
            <td>${esc(a.empName)}</td>
            <td>${esc(a.empLicense)}</td>
            <td>
              <button class="btn-link" onclick="openApplicatorModal('edit','${esc(a.empId)}')">Edit</button>
              <button class="btn-link danger" onclick="deleteApplicator('${esc(a.empId)}','${esc(a.empName)}')">Delete</button>
            </td>
          </tr>`).join('')}
      </tbody>
    </table>`;
}
 
/* Applicator Modal */
function openApplicatorModal(mode, empId = null) {
  const modal = document.getElementById('applicatorModal');
  const title = document.getElementById('applicatorModalTitle');
  const errEl = document.getElementById('applicatorError');
  errEl.textContent = '';
 
  if (mode === 'add') {
    title.textContent = 'Add Applicator';
    document.getElementById('applicatorEditId').value = '';
    document.getElementById('appName').value = '';
    document.getElementById('appId').value = '';
    document.getElementById('appPass').value = '';
    document.getElementById('appPassConfirm').value = '';
    document.getElementById('appLicense').value = '';
    document.getElementById('appPassLabel').textContent = 'Password';
    document.getElementById('appPass').required = true;
  } else {
    const a = allApplicators.find(x => x.empId === empId);
    if (!a) return;
    title.textContent = 'Edit Applicator';
    document.getElementById('applicatorEditId').value = a.empId;
    document.getElementById('appName').value = a.empName;
    document.getElementById('appId').value = a.empId;
    document.getElementById('appId').disabled = true;
    document.getElementById('appLicense').value = a.empLicense;
    document.getElementById('appPass').value = '';
    document.getElementById('appPassConfirm').value = '';
    document.getElementById('appPassLabel').textContent = 'New Password (leave blank to keep)';
    document.getElementById('appPass').required = false;
  }
  modal.classList.add('active');
}
 
function closeApplicatorModal() {
  document.getElementById('applicatorModal').classList.remove('active');
  document.getElementById('appId').disabled = false;
}
 
async function saveApplicator() {
  const errEl = document.getElementById('applicatorError');
  errEl.textContent = '';
 
  const editId  = document.getElementById('applicatorEditId').value;
  const empId   = document.getElementById('appId').value.trim();
  const empName = document.getElementById('appName').value.trim();
  const license = document.getElementById('appLicense').value.trim();
  const pass    = document.getElementById('appPass').value;
  const confirm = document.getElementById('appPassConfirm').value;
 
  if (!empId || !empName || !license) {
    errEl.textContent = 'ID, Name and License are required.';
    return;
  }
  if (!editId && !pass) {
    errEl.textContent = 'Password is required for new applicators.';
    return;
  }
  if (pass && pass.length < 6) {
    errEl.textContent = 'Password must be at least 6 characters.';
    return;
  }
  if (pass !== confirm) {
    errEl.textContent = 'Passwords do not match.';
    return;
  }
 
  const action = editId ? 'updateApplicator' : 'addApplicator';
  const payload = { empId, empName, empLicense: license };
  if (pass) payload.empPass = pass;
 
  const data = await request(action, payload);
  if (data.success) {
    showToast(editId ? 'Applicator updated!' : 'Applicator added!');
    closeApplicatorModal();
    loadApplicators();
  } else {
    errEl.textContent = data.message || 'Failed to save.';
  }
}
 
async function deleteApplicator(empId, empName) {
  if (!confirm(`Delete applicator "${empName}"? This cannot be undone.`)) return;
  const data = await request('deleteApplicator', { empId });
  if (data.success) {
    showToast('Applicator deleted.');
    loadApplicators();
  } else {
    showToast(data.message || 'Failed to delete.', 'error');
  }
}
 
/* ─── CUSTOMERS ────────────────────────────────── */
let allCustomers = [];
 
async function loadCustomers() {
  const data = await request('getCustomers');
  if (!data.success) return;
  allCustomers = data.customers || [];
  renderCustomersTable(allCustomers);
  document.getElementById('customerCount').textContent = `${allCustomers.length} companies`;
  document.getElementById('searchCustomer').oninput = filterCustomers;
}
 
function filterCustomers() {
  const q = document.getElementById('searchCustomer').value.toLowerCase();
  renderCustomersTable(allCustomers.filter(c =>
    c.comName?.toLowerCase().includes(q) ||
    c.factorName?.toLowerCase().includes(q) ||
    c.comEmail?.toLowerCase().includes(q)
  ));
}
 
function renderCustomersTable(list) {
  const el = document.getElementById('customersTable');
  if (!list.length) {
    el.innerHTML = '<p class="empty-state">No customers found.</p>';
    return;
  }
  el.innerHTML = `
    <table class="data-table">
      <thead><tr>
        <th>Company</th><th>Contact</th><th>Phone</th><th>Email</th><th>Factory / Lot</th><th>Actions</th>
      </tr></thead>
      <tbody>
        ${list.map(c => `
          <tr>
            <td><strong>${esc(c.comName)}</strong></td>
            <td>${esc(c.comContact) || '—'}</td>
            <td>${esc(c.comPhone)}</td>
            <td>${esc(c.comEmail)}</td>
            <td>${c.factorName 
            ? c.factorName.split('||').map(f => `<div>${esc(f.trim())}</div>`).join('') 
            : '—'}</td>
            <td>
              <button class="btn-link" onclick="openCustomerModal('edit',${c.comId})">Edit</button>
              <button class="btn-link danger" onclick="deleteCustomer(${c.comId},'${esc(c.comName)}')">Delete</button>
            </td>
          </tr>`).join('')}
      </tbody>
    </table>`;
}
 
/* Customer Modal */
function openCustomerModal(mode, comId = null) {
  const modal = document.getElementById('customerModal');
  const errEl = document.getElementById('customerError');
  errEl.textContent = '';
 
  if (mode === 'add') {
    document.getElementById('customerModalTitle').textContent = 'Add Customer';
    document.getElementById('customerEditId').value = '';
    ['custName','custContact','custEmail','custPhone','custAddress','custNotes'].forEach(id => {
      document.getElementById(id).value = '';
    });
    document.getElementById('factoryList').innerHTML = '';
    addFactoryRow();
  } else {
    const c = allCustomers.find(x => x.comId == comId);
    if (!c) return;
    document.getElementById('customerModalTitle').textContent = 'Edit Customer';
    document.getElementById('customerEditId').value = c.comId;
    document.getElementById('custName').value    = c.comName    || '';
    document.getElementById('custContact').value = c.comContact || '';
    document.getElementById('custEmail').value   = c.comEmail   || '';
    document.getElementById('custPhone').value   = c.comPhone   || '';
    document.getElementById('factoryList').innerHTML = '';
    const factories = c.factorName 
      ? c.factorName.split('||').map(f => f.trim()).filter(Boolean) 
      : [];
    if (factories.length) factories.forEach(f => addFactoryRow(f));
    else addFactoryRow();
    document.getElementById('custAddress').value = c.comAddress || '';
    document.getElementById('custNotes').value   = c.notes      || '';
  }
  modal.classList.add('active');
}
 
function closeCustomerModal() {
  document.getElementById('customerModal').classList.remove('active');
}
 
async function saveCustomer() {
  const errEl  = document.getElementById('customerError');
  errEl.textContent = '';
 
  const editId  = document.getElementById('customerEditId').value;
  const comName = document.getElementById('custName').value.trim();
  const contact = document.getElementById('custContact').value.trim();
  const email   = document.getElementById('custEmail').value.trim();
  const phone   = document.getElementById('custPhone').value.trim();
  const factories = getFactoryValues();
  const lot = factories.join(' || ');
  const address = document.getElementById('custAddress').value.trim();
  const notes   = document.getElementById('custNotes').value.trim();
 
  if (!comName || !phone) {
    errEl.textContent = 'Company name and phone are required.';
    return;
  }
 
  const action  = editId ? 'updateCustomer' : 'addCustomer';
  const payload = { comName, comContact: contact, comEmail: email, comPhone: phone,
                    factorName: lot, comAddress: address, notes };
  if (editId) payload.comId = editId;
 
  const data = await request(action, payload);
  if (data.success) {
    showToast(editId ? 'Customer updated!' : 'Customer added!');
    closeCustomerModal();
    loadCustomers();
  } else {
    errEl.textContent = data.message || 'Failed to save.';
  }
}
 
async function deleteCustomer(comId, comName) {
  if (!confirm(`Delete customer "${comName}"? All linked reports will also be removed.`)) return;
  const data = await request('deleteCustomer', { comId });
  if (data.success) {
    showToast('Customer deleted.');
    loadCustomers();
  } else {
    showToast(data.message || 'Failed to delete.', 'error');
  }
}

/* ─── FACTORY ROW HELPERS ───────────────────────── */
function addFactoryRow(value = '') {
  const list = document.getElementById('factoryList');
  const row = document.createElement('div');
  row.className = 'factory-row';
  row.style.cssText = 'display:flex;gap:6px;margin-bottom:6px;';
  row.innerHTML = `
    <input type="text" class="factory-input" placeholder="e.g. Lot 12, Jalan Perusahaan 3" 
           value="${esc(value)}" style="flex:1">
    <button type="button" class="btn btn-ghost" style="padding:4px 10px;font-size:13px;color:var(--text3)"
            onclick="this.closest('.factory-row').remove()">×</button>`;
  list.appendChild(row);
}

function getFactoryValues() {
  return [...document.querySelectorAll('.factory-input')]
    .map(i => i.value.trim())
    .filter(Boolean);
}
 
/* ─── PASSWORD STRENGTH BAR ────────────────────── */
function checkPwStrength(val) {
  const bar = document.getElementById('pwBar');
  if (!bar) return;
  let score = 0;
  if (val.length >= 6)  score++;
  if (val.length >= 10) score++;
  if (/[A-Z]/.test(val)) score++;
  if (/[0-9]/.test(val)) score++;
  if (/[^A-Za-z0-9]/.test(val)) score++;
  bar.style.width = (score * 20) + '%';
  bar.style.background = ['#e74c3c','#e67e22','#f1c40f','#2ecc71','#27ae60'][score - 1] || '#e74c3c';
}
 
/* ─── WIRE UP MODAL BUTTONS ────────────────────── */
function initModalBindings() {
  // Applicator modal
  document.querySelector('#applicatorModal .modal-close').onclick = closeApplicatorModal;
  document.querySelector('#applicatorModal .btn-ghost').onclick   = closeApplicatorModal;
  document.querySelector('#applicatorModal .btn-success').onclick = saveApplicator;
  document.querySelector('#viewApplicators .btn-success').onclick = () => openApplicatorModal('add');
  document.getElementById('applicatorModal').onclick = e => {
    if (e.target.id === 'applicatorModal') closeApplicatorModal();
  };
  document.getElementById('appPass').oninput = e => checkPwStrength(e.target.value);
 
  // Customer modal
  document.querySelector('#customerModal .modal-close').onclick = closeCustomerModal;
  document.querySelector('#customerModal .modal-footer .btn-ghost').onclick = closeCustomerModal;
  document.querySelector('#customerModal .btn-success').onclick = saveCustomer;
  document.querySelector('#viewCustomers .btn-success').onclick = () => openCustomerModal('add');
  document.getElementById('customerModal').onclick = e => {
    if (e.target.id === 'customerModal') closeCustomerModal();
  };
}

/* ─── INIT ─────────────────────────────────────── */
document.addEventListener('DOMContentLoaded', () => {
  document.getElementById('pageDate').textContent =
    new Date().toLocaleDateString('en-MY', { weekday:'long', year:'numeric', month:'long', day:'numeric' });
 
  initModalBindings();
  loadDashboard();
 
});

/* ─── LOGOUT ───────────────────────────────────── */
async function handleLogout() {
  await request('logout');
  window.location.href = '../login.php';
}