<?php
include ('db_conn/db_conn.php');

$classTypes = [];
$sql = mysqli_query($conn, "SELECT * FROM class_type");
while ($row = mysqli_fetch_array($sql)) {
    $classTypes[] = [
        'id' => $row['class'], 
        'name' => $row['className']
    ];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Pest Control Service Report — JC Pest & Hygiene</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
</head>
<body>

<header class="site-header">
  <div class="header-inner">
    <div class="brand">
      <div class="brand-logo">JC</div>
      <div class="brand-text">
        <span class="brand-name">JC Pest &amp; Hygiene Services</span>
        <span class="brand-sub">SDN. BHD.</span>
      </div>
    </div>
    <div class="header-actions">
      <span class="sr-badge">S/R NO: <strong id="srDisplay">—</strong></span>
      <a href="login.php" class="btn-admin">Admin Panel</a>
    </div>
  </div>
</header>

<div class="progress-bar">
  <div class="step active" data-step="1">
    <span class="step-num">1</span><span class="step-label">Customer Info</span>
  </div>
  <div class="step" data-step="2">
    <span class="step-num">2</span><span class="step-label">Pesticides</span>
  </div>
  <div class="step" data-step="3">
    <span class="step-num">3</span><span class="step-label">Pest Status</span>
  </div>
  <div class="step" data-step="4">
    <span class="step-num">4</span><span class="step-label">Recommendations</span>
  </div>
  <div class="step" data-step="5">
    <span class="step-num">5</span><span class="step-label">Signatures</span>
  </div>
</div>

<main class="form-container">
<form action="form-process.php" method="POST" onsubmit="return false;">
  <div class="form-header-bar">
    <h2>FORM H</h2>
    <p class="form-sub">Office Copy / Account Dept Copy / Customer Copy &nbsp;|&nbsp; Record Of Pesticide Usage</p>
  </div>

  <!-- STEP 1: Customer Info -->
  <section class="form-step active" id="step1">
    <div class="section-label">A — CUSTOMER INFORMATION</div>

    <div class="field-row">
      <div class="field-group">
        <label>Company Name</label>
        <select type="select" id="companyName" placeholder="Company name">
          <option value="">Select Company Name</option>
          <?php
            $sql = mysqli_query($conn, "SELECT * FROM company" );
            while ($row = mysqli_fetch_array($sql)) {
          ?>
          <option value="<?php echo $row['comId']; ?>"><?php echo $row['comName']; ?></option>
          <?php } ?>
        </select>
      </div>
      <div class="field-group">
        <label>factory/lot</label>
        <select type="select" id="factoryLot" placeholder="Factory/Lot">
          <option value="">Select Factory/Lot</option>
          <?php
            $factoryData = array();
            $sql = mysqli_query($conn, "SELECT * FROM factory_lot " );
            while ($row = mysqli_fetch_array($sql)) {
              $factoryData[] = array(
                'factorNo' => $row['factorNo'],
                'factorName' => $row['factorName'],
                'comId' => $row['comId']
              );
            }
          ?>
        </select>
      </div>
    </div>

    <script>
      // Populate factory/lot options based on selected company
      const companySelect = document.getElementById('companyName');
      const factorySelect = document.getElementById('factoryLot');
      const factoryData = <?php echo json_encode($factoryData); ?>;

      companySelect.addEventListener('change', function() {
        const selectedComId = this.value;
        factorySelect.innerHTML = '<option value="">Select Factory/Lot</option>';
        factoryData.forEach(function(factory) {
          if (factory.comId === selectedComId) {
            const option = document.createElement('option');
            option.value = factory.factorNo;
            option.textContent = factory.factorName;
            factorySelect.appendChild(option);
          }
        });
      });
    </script>
    
    <div class="field-row">
      <div class="field-group full">
        <label>Purpose Of Treatment</label>
        <div class="radio-group">
          <label class="radio-label"><input type="radio" name="purpose" value="routine" required> Routine</label>
          <label class="radio-label"><input type="radio" name="purpose" value="complain"> Complain</label>
          <label class="radio-label"><input type="radio" name="purpose" value="followup"> Follow Up</label>
        </div>
      </div>
    </div>

    <div class="field-row">
      <div class="field-group">
        <label>Planned Date</label>
        <input type="date" id="plannedDate">
      </div>
      <div class="field-group">
        <label>Type Of Premises</label>
        <input type="text" id="typeOfPremises" placeholder="e.g. Restaurant, Office, Factory">
      </div>
      <div class="field-group">
        <label>Standard Treatment Time</label>
        <input type="text" id="standardTreatmentTime" placeholder="e.g. 2 hours">
      </div>
    </div>

    <div class="field-row">
      <div class="field-group full">
        <label>Service Address</label>
        <textarea id="serviceAddress" rows="3" placeholder="Full service address..."></textarea>
      </div>
    </div>

    <div class="field-row">
      <div class="field-group">
        <label>Special Instruction / Call Out</label>
        <textarea id="specialInstruction" rows="2" placeholder="Any special instructions..."></textarea>
      </div>
      <div class="field-group">
        <label>Remarks</label>
        <textarea id="remarks" rows="2" placeholder="Remarks..."></textarea>
      </div>
    </div>

    <div class="section-label">Type Of Pests Covered</div>
    <div class="pest-grid">
      <label class="pest-check"><input type="checkbox" id="pestAnts" required> <span>A) Common Ants</span></label>
      <label class="pest-check"><input type="checkbox" id="pestCockroaches"> <span>B) Cockroaches</span></label>
      <label class="pest-check"><input type="checkbox" id="pestRats"> <span>C) Rats</span></label>
      <label class="pest-check"><input type="checkbox" id="pestMosquitoes"> <span>D) Mosquitoes</span></label>
      <label class="pest-check"><input type="checkbox" id="pestFlies"> <span>E) Flies</span></label>
      <label class="pest-check"><input type="checkbox" id="pestTermites"> <span>F) Termites</span></label>
    </div>

    <div class="step-nav">
      <span></span>
      <button type="button" class="btn-next" onclick="goStep(2)">Next: Pesticides →</button>
    </div>
  </section>

  <!-- STEP 2: Pesticides -->
  <section class="form-step" id="step2">
    <div class="section-label">B — PESTICIDES APPLIED</div>
    
    <div class="table-scroll">
      <table class="data-table" id="pesticidesTable">
        <thead>
          <tr>
            <th>#</th>
            <th>Active Ingredients</th>
            <th>Trade Name</th>
            <th>Class</th>
            <th>Dilution Solution Applied (%)</th>
            <th>Method of Application</th>
            <th>Total Area Treated (M²)</th>
            <th>Total Qty Diluted Solution Used (Litre/KG)</th>
          </tr>
        </thead>
        <tbody id="pesticidesBody">
          <script>
          // Store class types from database
          const classTypes = <?php echo json_encode($classTypes); ?>;

          // Function to populate class dropdown options
          function populateClassDropdown(selectElement) {
              if (!selectElement) return;
              
              // Clear existing options except the default one
              selectElement.innerHTML = '<option value="">—</option>';
              
              // Add options from database
              classTypes.forEach(function(classType) {
                  const option = document.createElement('option');
                  option.value = classType.id;
                  option.textContent = classType.id;
                  selectElement.appendChild(option);
              });
          }

          // Modified addRecommendationRowPest function to populate class dropdown
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
                          <option>Spray</option>
                          <option>Fogging</option>
                          <option>Misting</option>
                          <option>Gel Bait</option>
                          <option>Dusting</option>
                          <option>Injection</option>
                      </select>
                  </td>
                  <td><input type="number" placeholder="m²" data-field="area" data-row="${rowCount}"></td>
                  <td><input type="text" placeholder="Litre/KG" data-field="qty" data-row="${rowCount}"></td>
              `;

              body.appendChild(tr);
              
              // Populate the class dropdown for this new row
              const classSelect = tr.querySelector('[data-field="class"]');
              populateClassDropdown(classSelect);
          }

          // Modified buildPesticidesTable function to populate class dropdowns
          function buildPesticidesTable() {
              const body = document.getElementById('pesticidesBody');
              body.innerHTML = '';

              for (let i = 1; i <= recRowCountPest; i++) {
                  addRecommendationRowPest();
                  // No need to call populate separately as it's now inside addRecommendationRowPest
              }
          }
          </script>
        </tbody>
      </table>
    </div>
   <button type="button" class="btn-add-row" onclick="addRecommendationRowPest()">+ Add Row</button>

    <div class="label-precaution">
      <div class="label-row">
        <span class="lbl-title">Label Precautionary Statement</span>
        <span class="lbl-item kelas1a"><strong>Kelas 1A</strong><br>Beracun Amat Bisa</span>
        <span class="lbl-item kelas1b"><strong>Kelas 1B</strong><br>Beracun Biasa</span>
        <span class="lbl-item kelas2"><strong>Kelas II</strong><br>Beracun</span>
        <span class="lbl-item kelas3"><strong>Kelas III</strong><br>Merbahaya</span>
        <span class="lbl-item kelas4"><strong>Kelas IV</strong><br>Merbahaya</span>
      </div>
      <p class="keep-away">KEEP AWAY FROM FOODSTUFF &amp; CHILDREN<br><em>(Jauhkan Daripada Makanan &amp; Kanak-Kanak)</em></p>
    </div>

    <div class="section-label">C — NOTES FOR TERMITE POST TREATMENT ONLY</div>
    <div class="field-row">
      <div class="field-group">
        <label>No Of Holes</label>
        <input type="text" id="noOfHoles" placeholder="e.g. 12">
      </div>
      <div class="field-group">
        <label>Distance Between Holes (cm)</label>
        <input type="text" id="distanceBetweenHoles" placeholder="e.g. 30">
      </div>
      <div class="field-group">
        <label>Quantity Of Termicide / Holes</label>
        <input type="text" id="qtyTermicide" placeholder="e.g. 5 holes">
      </div>
    </div>

    <div class="step-nav">
      <button class="btn-back" onclick="goStep(1)">← Back</button>
      <button class="btn-next" onclick="goStep(3)">Next: Pest Status →</button>
    </div>
  </section>

  <!-- STEP 3: Pest Status -->
  <section class="form-step" id="step3">
    <div class="section-label">D — PEST STATUS</div>
    <div class="table-scroll">
      <table class="data-table">
        <thead>
          <tr>
            <th>#</th>
            <th>Pest</th>
            <th>Level</th>
            <th>Location Found</th>
            <th>Remarks</th>
          </tr>
        </thead>
        <tbody id="pestStatusBody">
          <!-- Rows generated by JS -->
        </tbody>
      </table>
    </div>
    <button type="button" class="btn-add-row" onclick="addPestStatusRow()">+ Add Row</button>

    <div class="step-nav">
      <button type="button" class="btn-back" onclick="goStep(2)">← Back</button>
      <button type="button" class="btn-next" onclick="goStep(4)">Next: Recommendations →</button>
    </div>
  </section>

  <!-- STEP 4: Recommendations & Feedback -->
  <section class="form-step" id="step4">
    <div class="section-label">E — RECOMMENDATION BY JC PEST &amp; HYGIENE SERVICES</div>
    <div class="table-scroll">
      <table class="data-table">
        <thead>
          <tr>
            <th style="width:30%">Type</th>
            <th>Recommendation</th>
          </tr>
        </thead>
        <tbody id="recommendationsBody">
          <!-- Rows generated by JS -->
        </tbody>
      </table>
    </div>
    <button class="btn-add-row" onclick="addRecommendationRow()">+ Add Row</button>

    <div class="section-label" style="margin-top:28px">F — CUSTOMER FEEDBACK</div>
    <div class="field-group full">
      <textarea id="customerFeedback" rows="4" placeholder="Customer feedback and comments..."></textarea>
    </div>

    <div class="section-label" style="margin-top:28px">G — APPLICATOR'S INFORMATION</div>
    <div class="field-row">
      <div class="field-group">
        <label>Applicator's Name</label>
        <input type="text" id="applicatorName" placeholder="Full name">
      </div>
      <div class="field-group">
        <label>Licence No</label>
        <input type="text" id="licenceNo" placeholder="Licence number">
      </div>
      <div class="field-group">
        <label>Treatment Date</label>
        <input type="date" id="treatmentDate">
      </div>
    </div>
    <div class="field-row">
      <div class="field-group">
        <label>Name Of Other Applicator</label>
        <div id="otherApplicatorsContainer">
        <div class="applicator-row">
          <input type="text" placeholder="Other Applicator Name">
        </div>
    </div>
    <button type="button" class="btn-add-row" onclick="addApplicator()">+ Add Applicator</button>
      </div>
      <div class="field-group">
        <label>Vehicle No</label>
        <input type="text" id="vehicleNo" placeholder="e.g. WXX 1234">
      </div>
      <div class="field-group">
        <label>Time In</label>
        <input type="time" id="timeIn">
      </div>
      <div class="field-group">
        <label>Time Out</label>
        <input type="time" id="timeOut">
      </div>
    </div>

    <div class="customer-info">
      <div class="field-row">
        <div class="field-group">
          <label>Name Of Customer</label>
          <input type="text" id="customerName" placeholder="Customer full name">
        </div>
        <div class="field-group">
          <label>Date</label>
          <input type="date" id="customerDate">
        </div>
      </div>
    </div>

    <div class="step-nav">
      <button type="button" class="btn-back" onclick="goStep(3)">← Back</button>
      <button type="button" class="btn-next" onclick="goStep(5)">Next: Signatures →</button>
    </div>
  </section>

  <!-- STEP 5: Signatures -->
  <section class="form-step" id="step5">
    <div class="section-label">SIGNATURES</div>
    
    <div class="sig-grid">
      <div class="sig-block">
        <div class="sig-title">Applicator's Signature</div>
        <p class="sig-desc">I declare the information above is true and correct</p>
        <canvas id="sigApplicator" class="sig-canvas" width="400" height="200"></canvas>
        <div class="sig-actions">
         <button type="button" class="btn-clear-sig" onclick="sig1.clear()">Clear</button>
        </div>
      </div>

      <div class="sig-block">
        <div class="sig-title">Customer's Signature</div>
        <p class="sig-desc">I acknowledge receipt of the above report</p>
        <canvas id="sigCustomer" class="sig-canvas" width="400" height="200"></canvas>
        <div class="sig-actions">
          <button type="button" class="btn-clear-sig" onclick="sig2.clear()">Clear</button>
        </div>
      </div>
    </div>

    <div class="submit-area">
      <div id="saveStatus" class="save-status"></div>
      <button type="button" class="btn-submit" onclick="saveForm('complete')">✅ Submit & Complete</button>
    </div>

    <div class="step-nav">
      <button type="button" class="btn-back" onclick="goStep(4)">← Back</button>
      <span></span>
    </div>
  </section>
</form>
</main>

<!-- SUCCESS MODAL -->
<div id="successModal" class="modal-overlay" style="display:none">
  <div class="modal-box">
    <div class="modal-icon">✅</div>
    <h3>Report Submitted!</h3>
    <p>Service Report <strong id="modalSrNum"></strong> has been saved successfully.</p>
    <div class="modal-actions">
      <button onclick="" class="btn-modal-new">+ New Report</button>
      <a href="admin/dashboard.php" class="btn-modal-admin">View in Admin</a>
    </div>
  </div>
</div>

<script src="assets/form.js"></script>
</body>
</html>
