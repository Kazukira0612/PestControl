
function clearCanvas(id) {
  const canvas = document.getElementById(id);
  const ctx = canvas.getContext("2d");
  ctx.clearRect(0, 0, canvas.width, canvas.height);
}

let currentSR = null;
let sigPads = {};

// ─── INIT ─────────────────────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', async () => {
  const params = new URLSearchParams(window.location.search);
  const editId = params.get('edit');
 
  buildPesticidesTable();
  buildPestStatusTable();
  buildRecommendationsTable();
  initSignature();
 
  if (editId) {
    await loadForm(editId);
  } else {
    await initNewSR();
  }
 
  // Default dates to today
  const today = new Date().toISOString().split('T')[0];
  const pd = document.getElementById('plannedDate');
  const td = document.getElementById('treatmentDate');
  const cd = document.getElementById('customerDate');
  if (pd && !pd.value) pd.value = today;
  if (td && !td.value) td.value = today;
  if (cd && !cd.value) cd.value = today;
 
  // Wire up Save Draft button
  const saveBtn = document.querySelector('.btn-save');
  if (saveBtn) saveBtn.addEventListener('click', () => handleSubmit('draft'));
 
  // Wire up Submit button
  const submitBtn = document.querySelector('.btn-submit');
  if (submitBtn) submitBtn.addEventListener('click', () => handleSubmit('submitted'));
 
  // Populate company dropdown (if using dynamic data)
  loadCompanies();
});
// ─── SR NUMBER ───────────────────────────────────────────────────────────────
async function initNewSR() {
  const now   = new Date();
  const ymd   = now.getFullYear().toString()
              + String(now.getMonth()+1).padStart(2,'0')
  const rand  = Math.floor(1000 + Math.random() * 9000);
  currentSR   = `SR-${ymd}-${rand}`;
 
  const display = document.getElementById('srDisplay');
  if (display) display.textContent = currentSR;
}

// ─── TABLE BUILDERS ──────────────────────────────────────

let recRowCountPest = 6;
function buildPesticidesTable() {
  const body = document.getElementById('pesticidesBody');
  body.innerHTML = '';

  for (let i = 1; i <= recRowCountPest; i++) {
    addRecommendationRowPest();
  }
}

function addRecommendationRowPest() {
  const body = document.getElementById('pesticidesBody');
  const rowCount = body.querySelectorAll('tr').length + 1;

  const tr = document.createElement('tr');
  tr.innerHTML = `
    <td><span class="row-num">${rowCount}</span></td>
    <td><input type="text" placeholder="e.g. Cypermethrin" data-field="active_ingredients" data-row="${rowCount}"></td>
    <td><input type="text" placeholder="Trade name" data-field="trade_name" data-row="${rowCount}"></td>
    <td>
      <select data-field="class" data-row="${rowCount}">
        <option value="">—</option>
      </select>
    </td>
    <td><input type="text" placeholder="%" data-field="dilution" data-row="${rowCount}"></td>
    <td>
      <select data-field="method" data-row="${rowCount}">
        <option value="">—</option>
        <option>Spray</option><option>Fogging</option><option>Misting</option>
        <option>Gel Bait</option><option>Dusting</option><option>Injection</option>
      </select>
    </td>
    <td><input type="number" placeholder="m²" data-field="area" data-row="${rowCount}"></td>
    <td><input type="text" placeholder="Litre/KG" data-field="qty" data-row="${rowCount}"></td>
  `;

  body.appendChild(tr);
}

let recRowCountStat = 3;

function buildPestStatusTable() {
  const body = document.getElementById('pestStatusBody');
  body.innerHTML = '';

  for (let i = 1; i <= recRowCountStat; i++) {
    addPestStatusRow();
  }
}

function addPestStatusRow() {
  const body = document.getElementById('pestStatusBody');
  const rowCount = body.querySelectorAll('tr').length + 1;
  const tr = document.createElement('tr');
  tr.innerHTML = `
      <td><span class="row-num">${rowCount}</span></td>
      <td><input type="text" placeholder="Pest type" data-ps-field="pest" data-row="${rowCount}"></td>
      <td>
        <select data-ps-field="level" data-row="${rowCount}">
          <option value="">—</option>
          <option>Low</option>
          <option>Moderate</option>
          <option>High</option>
          <option>Severe</option>
        </select>
      </td>
      <td><input type="text" placeholder="e.g. Kitchen, Storeroom" data-ps-field="location" data-row="${rowCount}"></td>
      <td><input type="text" placeholder="Remarks" data-ps-field="remarks" data-row="${rowCount}"></td>
  `;

  body.appendChild(tr);
}


let recRowCount = 3;
function buildRecommendationsTable() {
  const body = document.getElementById('recommendationsBody');
  body.innerHTML = '';
  for (let i = 1; i <= recRowCount; i++) {
    addRecommendationRow(body);
  }
}

function addRecommendationRow(body) {
  body = body || document.getElementById('recommendationsBody');
  const tr = document.createElement('tr');
  tr.innerHTML = `
    <td><input type="text" placeholder="e.g. Sanitation, Structural" data-rec-field="type"></td>
    <td><input type="text" placeholder="Recommendation details..." data-rec-field="recommendation"></td>
  `;
  body.appendChild(tr);
}

function addApplicator() {
  const container = document.getElementById('otherApplicatorsContainer');

  const div = document.createElement('div');
  div.classList.add('applicator-row');

  div.innerHTML = `
    <input type="text" placeholder="Other Applicator Name">
    <button type="button" class="btn-add-row" onclick="this.parentElement.remove()">❌</button>
  `;

  container.appendChild(div);
}

// ─── DATA COLLECTION ─────────────────────────────────────
function collectFormData(status = 'draft') {

  const otherApplicators = [];
document.querySelectorAll('#otherApplicatorsContainer input').forEach(input => {
  if (input.value.trim()) {
    otherApplicators.push(input.value.trim());
  }
});

  // Pesticides
  const pesticides = [];
  for (let i = 1; i <= 6; i++) {
    const row = {
      active_ingredients: document.querySelector(`[data-field="active_ingredients"][data-row="${i}"]`)?.value || '',
      trade_name: document.querySelector(`[data-field="trade_name"][data-row="${i}"]`)?.value || '',
      class: document.querySelector(`[data-field="class"][data-row="${i}"]`)?.value || '',
      dilution: document.querySelector(`[data-field="dilution"][data-row="${i}"]`)?.value || '',
      method: document.querySelector(`[data-field="method"][data-row="${i}"]`)?.value || '',
      area: document.querySelector(`[data-field="area"][data-row="${i}"]`)?.value || '',
      qty: document.querySelector(`[data-field="qty"][data-row="${i}"]`)?.value || '',
    };
    if (Object.values(row).some(v => v)) pesticides.push(row);
  }

  // Pest Status
  const pestStatus = [];
  for (let i = 1; i <= 3; i++) {
    const row = {
      pest: document.querySelector(`[data-ps-field="pest"][data-row="${i}"]`)?.value || '',
      level: document.querySelector(`[data-ps-field="level"][data-row="${i}"]`)?.value || '',
      location: document.querySelector(`[data-ps-field="location"][data-row="${i}"]`)?.value || '',
      remarks: document.querySelector(`[data-ps-field="remarks"][data-row="${i}"]`)?.value || '',
    };
    if (Object.values(row).some(v => v)) pestStatus.push(row);
  }

  // Recommendations
  const recommendations = [];
  document.querySelectorAll('[data-rec-field="type"]').forEach((el, i) => {
    const rec = el.value;
    const recText = document.querySelectorAll('[data-rec-field="recommendation"]')[i]?.value || '';
    if (rec || recText) recommendations.push({ type: rec, recommendation: recText });
  });

  return {
    sr_number:              currentSR,
    com_id:                 document.getElementById('companyName')?.value        || '',
    factory_no:             document.getElementById('factoryLot')?.value         || '',
    purpose_of_treatment:   document.querySelector('input[name="purpose"]:checked')?.value || 'routine',
    planned_date:           document.getElementById('plannedDate')?.value        || '',
    type_of_premises:       document.getElementById('typeOfPremises')?.value     || '',
    standard_treatment_time:document.getElementById('standardTreatmentTime')?.value || '',
    service_address:        document.getElementById('serviceAddress')?.value     || '',
    special_instruction:    document.getElementById('specialInstruction')?.value || '',
    remarks:                document.getElementById('remarks')?.value            || '',
    pest_common_ants:       document.getElementById('pestAnts')?.checked     ? 1 : 0,
    pest_cockroaches:       document.getElementById('pestCockroaches')?.checked ? 1 : 0,
    pest_rats:              document.getElementById('pestRats')?.checked     ? 1 : 0,
    pest_mosquitoes:        document.getElementById('pestMosquitoes')?.checked ? 1 : 0,
    pest_flies:             document.getElementById('pestFlies')?.checked    ? 1 : 0,
    pest_termites:          document.getElementById('pestTermites')?.checked  ? 1 : 0,
    pesticides_applied:     pesticides,
    no_of_holes:            document.getElementById('noOfHoles')?.value          || '',
    distance_between_holes: document.getElementById('distanceBetweenHoles')?.value || '',
    qty_termicide_per_hole: document.getElementById('qtyTermicide')?.value       || '',
    pest_status:            pestStatus,
    recommendations,
    customer_feedback:      document.getElementById('customerFeedback')?.value   || '',
    // Applicator info
    leader_id:              document.getElementById('applicatorName')?.value     || '',  // or employee dropdown
    licence_no:             document.getElementById('licenceNo')?.value          || '',
    treatment_date:         document.getElementById('treatmentDate')?.value      || '',
    other_applicators:      otherApplicators,
    vehicle_no:             document.getElementById('vehicleNo')?.value          || '',
    time_in:                document.getElementById('timeIn')?.value             || '',
    time_out:               document.getElementById('timeOut')?.value            || '',
    customer_name:          document.getElementById('customerName')?.value       || '',
    customer_date:          document.getElementById('customerDate')?.value       || '',
    // Signatures
    applicator_signature:   applicatorSig,
    customer_signature:     customerSig,
    status,
  };
}

// Call this when user clicks Save or Submit
function saveForm(status = 'draft') {

  // Step 1: collect everything (you already have this function!)
  const data = collectFormData(status);

  // Step 2: send it to PHP in the background
  fetch('save.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(data)   // converts the big object to text PHP can read
  })

  // Step 3: read what PHP replies
  .then(response => response.json())
  .then(result => {
    if (result.success) {
      document.getElementById('saveStatus').textContent = '✅ Saved! SR: ' + result.sr_number;
    } else {
      document.getElementById('saveStatus').textContent = '❌ Error: ' + result.error;
    }
  });
}

// ─── STEP NAVIGATION ─────────────────────────────────────
function goStep(n) {
  document.querySelectorAll('.form-step').forEach(s => s.classList.remove('active'));
  document.querySelectorAll('.step').forEach(s => s.classList.remove('active'));
  document.getElementById('step' + n).classList.add('active');
  document.querySelector('.step[data-step="' + n + '"]').classList.add('active');
  window.scrollTo({ top: 0, behavior: 'smooth' });
}

// ─── CANVAS ─────────────────────────────────────
function initSignature(canvasId) {
  const canvas = document.getElementById(canvasId);
  const ctx = canvas.getContext("2d");

  let painting = false;
  let hasDrawn = false;

  function getPos(e) {
    let rect = canvas.getBoundingClientRect();
    let clientX, clientY;

    if (e.type.startsWith("touch")) {
      clientX = e.touches[0].clientX;
      clientY = e.touches[0].clientY;
    } else {
      clientX = e.clientX;
      clientY = e.clientY;
    }

    return {
      x: clientX - rect.left,
      y: clientY - rect.top
    };
  }

  function start(e) {
    painting = true;
    hasDrawn = true;
    draw(e);
  }

  function end() {
    painting = false;
    ctx.beginPath();
  }

  function draw(e) {
    if (!painting) return;

    const pos = getPos(e);

    ctx.lineWidth = 2;
    ctx.lineCap = "round";
    ctx.strokeStyle = "black";

    ctx.lineTo(pos.x, pos.y);
    ctx.stroke();
    ctx.beginPath();
    ctx.moveTo(pos.x, pos.y);
  }

  // Events
  canvas.addEventListener("mousedown", start);
  canvas.addEventListener("mouseup", end);
  canvas.addEventListener("mousemove", draw);

  canvas.addEventListener("touchstart", start);
  canvas.addEventListener("touchend", end);
  canvas.addEventListener("touchmove", draw);

  // Clear function
  function clear() {
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    hasDrawn = false;
  }

  // Get image data
  function getData() {
    if (!hasDrawn) return null;
    return canvas.toDataURL("image/png");
  }

  return { clear, getData };
}

let sig1, sig2;

document.addEventListener("DOMContentLoaded", () => {
  sig1 = initSignature("sigApplicator");
  sig2 = initSignature("sigCustomer");
});

saveBtn.addEventListener("click", () => {
  if (drawStart) {
    const dataURL = canvas.toDataURL();
    let img = document.createElement("img");
    img.setAttribute("class", "signature-img");
    img.src = dataURL;
    const aFilename = document.createElement("a");
    aFilename.href = dataURL;
    aFilename.download = "signature.png";
    const filenameTextNode = document.createTextNode("signature.png");
    aFilename.appendChild(filenameTextNode);
    aFilename.appendChild(img);
    display.appendChild(img);
    display.appendChild(aFilename);
  } else {
    display.innerHTML = "<span class='error'>Please sign before saving</span>";
  }
});

loadState();
window.onload = (event) => {
  drawStart = false;
  context.clearRect(0, 0, canvas.width, canvas.height);
  saveState();
};

// ─── LOAD EXISTING FORM ───────────────────────────────────────────────────────
async function loadForm(srNum) {
  try {
    const res  = await fetch(`form/Get-form.php?sr=${encodeURIComponent(srNum)}`);
    const data = await res.json();
    if (!data.success) return;
 
    currentSR = data.sr_number;
    const display = document.getElementById('srDisplay');
    if (display) display.textContent = currentSR;
 
    const d = data.form;
 
    // Customer info
    setVal('plannedDate',             d.planDate);
    setVal('typeOfPremises',          d.premiseType);
    setVal('standardTreatmentTime',   d.treatTime);
    setVal('serviceAddress',          d.servAdd);
    setVal('specialInstruction',      d.specIstruct);
    setVal('remarks',                 d.remarks);
    setVal('customerFeedback',        d.custFeedback);
 
    const purposeRadio = document.querySelector(`input[name="purpose"][value="${d.purposeTreat}"]`);
    if (purposeRadio) purposeRadio.checked = true;
 
    // Pest checkboxes
    const pestMap = { A:'pestAnts', B:'pestCockroaches', C:'pestRats', D:'pestMosquitoes', E:'pestFlies', F:'pestTermites' };
    (d.pests || []).forEach(p => {
      const el = document.getElementById(pestMap[p.pestType]);
      if (el) el.checked = true;
    });
 
    // Pesticides
    if (d.pesticides?.length) {
      const body = document.getElementById('pesticidesBody');
      body.innerHTML = '';
      d.pesticides.forEach((p, i) => {
        addRecommendationRowPest();
        const tr = body.querySelectorAll('tr')[i];
        if (!tr) return;
        tr.querySelector('[data-field="active_ingredients"]').value = p.activeItem    || '';
        tr.querySelector('[data-field="trade_name"]').value         = p.tradeName     || '';
        tr.querySelector('[data-field="class"]').value              = p.class         || '';
        tr.querySelector('[data-field="dilution"]').value           = p.diluteSolution|| '';
        tr.querySelector('[data-field="method"]').value             = p.methodApply   || '';
        tr.querySelector('[data-field="area"]').value               = p.totalArea     || '';
        tr.querySelector('[data-field="qty"]').value                = p.totalQtyDilute|| '';
      });
    }
 
    // Pest status
    if (d.pestStatus?.length) {
      const body = document.getElementById('pestStatusBody');
      body.innerHTML = '';
      d.pestStatus.forEach((ps, i) => {
        addPestStatusRow();
        const tr = body.querySelectorAll('tr')[i];
        if (!tr) return;
        tr.querySelector('[data-ps-field="pest"]').value     = ps.pestStatName || '';
        tr.querySelector('[data-ps-field="level"]').value    = ps.statLvl      || '';
        tr.querySelector('[data-ps-field="location"]').value = ps.statLocation || '';
        tr.querySelector('[data-ps-field="remarks"]').value  = ps.statRemark   || '';
      });
    }
 
    // Recommendations
    if (d.recommendations?.length) {
      const body = document.getElementById('recommendationsBody');
      body.innerHTML = '';
      d.recommendations.forEach((r, i) => {
        addRecommendationRow();
        const tr = body.querySelectorAll('tr')[i];
        if (!tr) return;
        tr.querySelector('[data-rec-field="type"]').value           = r.recomType    || '';
        tr.querySelector('[data-rec-field="recommendation"]').value = r.recommendation|| '';
      });
    }
 
    // Applicator
    setVal('applicatorName',  d.applicator?.leaderId   || '');
    setVal('licenceNo',       d.applicator?.licenceNo  || '');
    setVal('treatmentDate',   d.applicator?.treatDate  || '');
    setVal('vehicleNo',       d.applicator?.vehicleNo  || '');
    setVal('timeIn',          d.applicator?.timeIn     || '');
    setVal('timeOut',         d.applicator?.timeOut    || '');
    setVal('customerName',    d.applicator?.custName   || '');
    setVal('customerDate',    d.applicator?.custSignDate|| '');
 
    // Termite
    if (d.termite) {
      setVal('noOfHoles',            d.termite.holeQty     || '');
      setVal('distanceBetweenHoles', d.termite.holeDistance|| '');
      setVal('qtyTermicide',         d.termite.termicideQty|| '');
    }
 
  } catch (e) {
    console.error('Load form error:', e);
  }
}
 
function setVal(id, val) {
  const el = document.getElementById(id);
  if (el && val !== undefined && val !== null) el.value = val;
}