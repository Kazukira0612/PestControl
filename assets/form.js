
function clearCanvas(id) {
  const canvas = document.getElementById(id);
  const ctx = canvas.getContext("2d");
  ctx.clearRect(0, 0, canvas.width, canvas.height);
}

let currentSR = null;
let sigPads = {};

// ─── INIT ───────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', async () => {
  // Load SR from URL param or generate new
  const params = new URLSearchParams(window.location.search);
  const editId = params.get('edit');

  buildPesticidesTable();
  buildPestStatusTable();
  buildRecommendationsTable();
  initSignaturePads();

  if (editId) {
    await loadForm(null, editId);
  } else {
    await initNewSR();
  }

  // Set today's date defaults
  const today = new Date().toISOString().split('T')[0];
  if (!document.getElementById('plannedDate').value) document.getElementById('plannedDate').value = today;
  if (!document.getElementById('treatmentDate').value) document.getElementById('treatmentDate').value = today;
  if (!document.getElementById('customerDate').value) document.getElementById('customerDate').value = today;
});

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
        <option>1A</option><option>1B</option><option>II</option><option>III</option><option>IV</option>
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
    <button type="button" onclick="this.parentElement.remove()">❌</button>
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
    sr_number: currentSR,
    planned_date: document.getElementById('plannedDate').value,
    service_address: document.getElementById('serviceAddress').value,
    type_of_premises: document.getElementById('typeOfPremises').value,
    standard_treatment_time: document.getElementById('standardTreatmentTime').value,
    special_instruction: document.getElementById('specialInstruction').value,
    remarks: document.getElementById('remarks').value,
    purpose_of_treatment: document.querySelector('input[name="purpose"]:checked')?.value || 'routine',
    pest_common_ants: document.getElementById('pestAnts').checked ? 1 : 0,
    pest_cockroaches: document.getElementById('pestCockroaches').checked ? 1 : 0,
    pest_rats: document.getElementById('pestRats').checked ? 1 : 0,
    pest_mosquitoes: document.getElementById('pestMosquitoes').checked ? 1 : 0,
    pest_flies: document.getElementById('pestFlies').checked ? 1 : 0,
    pest_termites: document.getElementById('pestTermites').checked ? 1 : 0,
    pesticides_applied: pesticides,
    no_of_holes: document.getElementById('noOfHoles').value,
    distance_between_holes: document.getElementById('distanceBetweenHoles').value,
    qty_termicide_per_hole: document.getElementById('qtyTermicide').value,
    pest_status: pestStatus,
    recommendations,
    customer_feedback: document.getElementById('customerFeedback').value,
    applicator_name: document.getElementById('applicatorName').value,
    licence_no: document.getElementById('licenceNo').value,
    treatment_date: document.getElementById('treatmentDate').value,
    other_applicator: document.getElementById('otherApplicator').value,
    vehicle_no: document.getElementById('vehicleNo').value,
    time_in: document.getElementById('timeIn').value,
    time_out: document.getElementById('timeOut').value,
    customer_name: document.getElementById('customerName').value,
    customer_date: document.getElementById('customerDate').value,
    applicator_signature: getSigData('sigApplicator'),
    customer_signature: getSigData('sigCustomer'),
    customer_chop: getSigData('sigChop'),
    status,
  };
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