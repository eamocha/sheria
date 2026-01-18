

    <style>
        body { padding: 1.25rem; background:#f7f9fc; }
        .mono { font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, "Roboto Mono", monospace; }
        .small-muted { font-size:.85rem; color:#6c757d; }
        .table-sm td, .table-sm th { vertical-align: middle; }
        .print-area { padding:1rem; background:white; }
    </style>


<div class="container-fluid">
    <div class="d-flex align-items-center mb-4">
        <h3 class="me-3">Sheria360 — ARO Manager (Prototype)</h3>
        <div class="small-muted">Bootstrap 5.3 demo — client-side sample data</div>
    </div>

    <!-- Nav tabs -->
    <ul class="nav nav-tabs mb-3" id="aroTabs" role="tablist">
        <li class="nav-item" role="presentation"><button class="nav-link active" id="schedules-tab" data-bs-toggle="tab" data-bs-target="#schedules" type="button" role="tab">1. Fee Schedule Manager</button></li>
        <li class="nav-item" role="presentation"><button class="nav-link" id="map-tab" data-bs-toggle="tab" data-bs-target="#map" type="button" role="tab">2. Matter Type Configuration</button></li>
        <li class="nav-item" role="presentation"><button class="nav-link" id="calculator-tab" data-bs-toggle="tab" data-bs-target="#calculator" type="button" role="tab">3. Fee Calculator</button></li>
        <li class="nav-item" role="presentation"><button class="nav-link" id="approvals-tab" data-bs-toggle="tab" data-bs-target="#approvals" type="button" role="tab">4. Approvals & Overrides</button></li>
        <li class="nav-item" role="presentation"><button class="nav-link" id="bill-tab" data-bs-toggle="tab" data-bs-target="#bill" type="button" role="tab">5. Bill / Export</button></li>
    </ul>

    <div class="tab-content">
        <!-- 1. Fee Schedule Manager -->
        <div class="tab-pane fade show active" id="schedules" role="tabpanel">
            <div class="card mb-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div><strong>Fee Schedule Manager</strong><div class="small-muted">Manage ARO rules, slabs and effective dates</div></div>
                    <div>
                        <button id="btnNewRule" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg"></i> New Rule</button>
                        <button id="btnImport" class="btn btn-outline-secondary btn-sm ms-2"><i class="bi bi-upload"></i> Import CSV</button>
                    </div>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-bordered" id="rulesTable">
                        <thead class="table-light">
                        <tr>
                            <th style="width:140px">Schedule</th>
                            <th>Item</th>
                            <th style="width:120px">Type</th>
                            <th style="width:180px">Range (KSh)</th>
                            <th style="width:150px">Value</th>
                            <th style="width:160px">Effective</th>
                            <th style="width:120px">Actions</th>
                        </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                    <div class="small-muted">Note: This is demo data. Persist to backend in production.</div>
                </div>
            </div>
        </div>

        <!-- 2. Matter Type Configuration -->
        <div class="tab-pane fade" id="map" role="tabpanel">
            <div class="card">
                <div class="card-header"><strong>Matter Type Configuration</strong></div>
                <div class="card-body">
                    <form id="mapForm" class="row g-3 align-items-end">
                        <div class="col-md-4">
                            <label class="form-label">Matter Type</label>
                            <select class="form-select" id="matterTypeSelect"></select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">ARO Schedule / Rule</label>
                            <select class="form-select" id="ruleSelect"></select>
                        </div>
                        <div class="col-md-4">
                            <button class="btn btn-primary" id="btnMap">Link Matter Type</button>
                        </div>
                    </form>

                    <hr>
                    <h6>Mappings</h6>
                    <table class="table table-sm table-bordered" id="mappingsTable">
                        <thead><tr><th>Matter Type</th><th>Rule</th><th style="width:110px">Actions</th></tr></thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- 3. Fee Calculator -->
        <div class="tab-pane fade" id="calculator" role="tabpanel">
            <div class="card">
                <div class="card-header"><strong>Fee Calculator</strong></div>
                <div class="card-body">
                    <form id="calcForm" class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Matter Type</label>
                            <select class="form-select" id="calcMatterType"></select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Court Level</label>
                            <select class="form-select" id="calcCourtLevel">
                                <option value="Magistrate">Magistrate Court</option>
                                <option value="High">High Court</option>
                                <option value="Appeal">Court of Appeal</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Value of Subject Matter (KSh)</label>
                            <input class="form-control" id="calcValue" type="number" value="5000000" />
                        </div>

                        <div class="col-12">
                            <label class="form-label">Optional adjustments</label>
                            <div class="row g-2">
                                <div class="col-md-3"><input type="number" id="calcDiscretion" class="form-control" value="0"> <div class="small-muted">Discretion (%)</div></div>
                                <div class="col-md-3"><input type="number" id="calcAppearances" class="form-control" value="2"> <div class="small-muted">Appearances (#)</div></div>
                                <div class="col-md-3"><input type="number" id="calcDisbursements" class="form-control" value="2000"> <div class="small-muted">Disbursements (KSh)</div></div>
                                <div class="col-md-3 text-end"><button type="button" id="btnCalculate" class="btn btn-success">Compute Fee</button></div>
                            </div>
                        </div>
                    </form>

                    <hr>
                    <div id="calcResult" style="display:none">
                        <h6>Computation Breakdown</h6>
                        <table class="table table-sm table-bordered mono">
                            <tbody>
                            <tr><th style="width:250px">Instruction Fee</th><td id="resInstruction">-</td></tr>
                            <tr><th>Getting-Up Fee (1/3 of instruction)</th><td id="resGettingUp">-</td></tr>
                            <tr><th>Appearances (per appearance)</th><td id="resAppearances">-</td></tr>
                            <tr><th>Disbursements</th><td id="resDisbursements">-</td></tr>
                            <tr class="table-light"><th>Total (before VAT)</th><td id="resTotal">-</td></tr>
                            </tbody>
                        </table>
                        <div class="d-flex justify-content-end">
                            <button id="btnSaveCalc" class="btn btn-primary me-2">Save calculation</button>
                            <button id="btnExportToBill" class="btn btn-outline-secondary">Export to Bill</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 4. Approvals & Overrides -->
        <div class="tab-pane fade" id="approvals" role="tabpanel">
            <div class="card">
                <div class="card-header"><strong>Approvals & Overrides</strong></div>
                <div class="card-body">
                    <table class="table table-sm table-bordered" id="calcsTable">
                        <thead><tr><th>#</th><th>Matter Type</th><th>Instruction</th><th>Total</th><th>Status</th><th style="width:180px">Actions</th></tr></thead>
                        <tbody></tbody>
                    </table>
                    <div class="small-muted">Saved calculations appear here. Admins can adjust or approve.</div>
                </div>
            </div>
        </div>

        <!-- 5. Bill / Export -->
        <div class="tab-pane fade" id="bill" role="tabpanel">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <div><strong>Bill / Fee Note</strong></div>
                    <div>
                        <button id="btnPrintBill" class="btn btn-success btn-sm"><i class="bi bi-printer"></i> Print</button>
                        <button id="btnDownloadDoc" class="btn btn-outline-secondary btn-sm ms-2"><i class="bi bi-download"></i> Download .docx</button>
                    </div>
                </div>
                <div class="card-body" id="billBody">
                    <div class="alert alert-info small-muted">Select a saved calculation in Approvals & Overrides, then click "Export to Bill" to preview here.</div>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- Modals -->
<div class="modal fade" id="ruleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form id="ruleForm" class="modal-content">
            <div class="modal-header"><h5 class="modal-title">Create / Edit Rule</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body">
                <input type="hidden" id="ruleId">
                <div class="row g-3">
                    <div class="col-md-6"><label class="form-label">Schedule</label><input id="ruleSchedule" class="form-control"></div>
                    <div class="col-md-6"><label class="form-label">Item / Description</label><input id="ruleItem" class="form-control"></div>
                    <div class="col-md-4"><label class="form-label">Computation Type</label>
                        <select id="ruleType" class="form-select">
                            <option value="percentage">Percentage</option>
                            <option value="fixed">Fixed</option>
                            <option value="slab">Slab</option>
                            <option value="formula">Formula</option>
                        </select>
                    </div>
                    <div class="col-md-4"><label class="form-label">Min Value (KSh)</label><input id="ruleMin" type="number" class="form-control"></div>
                    <div class="col-md-4"><label class="form-label">Max Value (KSh) — leave blank for ∞</label><input id="ruleMax" type="number" class="form-control"></div>
                    <div class="col-md-6"><label class="form-label">Rate / Fixed (use decimal for percentage e.g., 0.015)</label><input id="ruleVal" class="form-control"></div>
                    <div class="col-md-6"><label class="form-label">Effective From</label><input id="ruleFrom" type="date" class="form-control"></div>
                    <div class="col-12"><label class="form-label">Notes</label><textarea id="ruleNotes" class="form-control" rows="2"></textarea></div>
                </div>
            </div>
            <div class="modal-footer"><button type="submit" class="btn btn-primary">Save</button><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button></div>
        </form>
    </div>
</div>

<!-- Approval adjust modal -->
<div class="modal fade" id="adjustModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form id="adjustForm" class="modal-content">
            <div class="modal-header"><h5 class="modal-title">Adjust Calculation</h5><button class="btn-close" type="button" data-bs-dismiss="modal"></button></div>
            <div class="modal-body">
                <input type="hidden" id="adjustCalcId">
                <div class="mb-3">
                    <label class="form-label">Adjustment (%) — positive or negative</label>
                    <input id="adjustPercent" type="number" step="0.1" class="form-control" value="0">
                </div>
                <div class="mb-3">
                    <label class="form-label">Note / Reason</label>
                    <textarea id="adjustNote" class="form-control" rows="3"></textarea>
                </div>
            </div>
            <div class="modal-footer"><button class="btn btn-primary">Apply</button><button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Cancel</button></div>
        </form>
    </div>
</div>

<!-- Dependencies: jQuery + Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
    /*
      ARO Front-end Prototype
      - Client-side sample data
      - Functions to CRUD rules, map matter types, compute fees, save calculations, approve/adjust and export bill.
    */

    const sampleRules = [
        // id, schedule, item, type, min, max (null = infinity), value (decimal for percentage or fixed), notes
        {id:1, schedule:'Schedule 6 - Litigation', item:'Instruction Fee - Civil (0-1,000,000)', type:'percentage', min:0, max:1000000, value:0.02, notes:'2% first 1M'},
        {id:2, schedule:'Schedule 6 - Litigation', item:'Instruction Fee - Civil (1,000,001-10,000,000)', type:'percentage', min:1000001, max:10000000, value:0.015, notes:'1.5% next 9M'},
        {id:3, schedule:'Schedule 2 - Conveyancing', item:'Transfer of Land (<=1M)', type:'percentage', min:0, max:1000000, value:0.02, notes:'2% up to 1M'},
        {id:4, schedule:'Schedule 2 - Conveyancing', item:'Registration of Charge', type:'fixed', min:null, max:null, value:10000, notes:'Fixed registration fee'},
        {id:5, schedule:'Schedule 6 - Litigation', item:'Getting-Up Fee', type:'fixed', min:null, max:null, value:null, notes:'1/3 of instruction fee (treated separately)'}
    ];

    const matterTypes = [
        {key:'civil', label:'Litigation - Civil'},
        {key:'convey', label:'Conveyancing - Transfer'},
        {key:'probate', label:'Probate & Administration'}
    ];

    let mappings = []; // {matterTypeKey, ruleId}
    let savedCalculations = []; // store calculated bills: {id, matterType, value, breakdown, total, status, createdAt, meta}

    // Utility
    function uid(prefix='id'){ return prefix + '_' + Math.random().toString(36).slice(2,9); }
    function fmt(n){ return Number(n).toLocaleString('en-KE', {maximumFractionDigits:0}); }
    function fmt2(n){ return Number(n).toFixed(2); }

    // RENDER RULES TABLE
    function renderRules(){
        const tbody = $('#rulesTable tbody').empty();
        sampleRules.forEach(r=>{
            const range = (r.min==null? '0' : fmt(r.min)) + ' - ' + (r.max==null? '∞' : fmt(r.max));
            const val = r.type === 'percentage' ? (r.value*100)+'%' : (r.value? 'KSh ' + fmt(r.value) : '-');
            const row = $('<tr>');
            row.append(`<td>${r.schedule}</td>`);
            row.append(`<td>${r.item}<div class="small-muted">${r.notes || ''}</div></td>`);
            row.append(`<td class="text-capitalize">${r.type}</td>`);
            row.append(`<td>${range}</td>`);
            row.append(`<td>${val}</td>`);
            row.append(`<td>${r.effectiveFrom || '2014-07-11'}</td>`);
            row.append(`<td>
      <button class="btn btn-sm btn-outline-secondary btn-edit" data-id="${r.id}"><i class="bi bi-pencil"></i></button>
      <button class="btn btn-sm btn-outline-danger btn-delete ms-2" data-id="${r.id}"><i class="bi bi-trash"></i></button>
    </td>`);
            tbody.append(row);
        });
    }

    // Populate selects for mapping & calc
    function renderMatterTypeSelectors(){
        const sel1 = $('#matterTypeSelect').empty();
        const sel2 = $('#calcMatterType').empty();
        const selMap = $('#calcMatterType'); // reuse
        matterTypes.forEach(m=>{
            sel1.append(`<option value="${m.key}">${m.label}</option>`);
            sel2.append(`<option value="${m.key}">${m.label}</option>`);
        });
        // populate rule select
        const ruleselect = $('#ruleSelect').empty();
        sampleRules.forEach(r=> ruleselect.append(`<option value="${r.id}">${r.schedule} — ${r.item}</option>`));
        // also for single selects
        const ruleSelectSmall = $('#ruleSelectSmall');
    }

    // MAPPINGS TABLE
    function renderMappings(){
        const tb = $('#mappingsTable tbody').empty();
        mappings.forEach(m=>{
            const rule = sampleRules.find(r=> r.id==m.ruleId);
            const mt = matterTypes.find(t=> t.key==m.matterTypeKey);
            const tr = $('<tr>');
            tr.append(`<td>${mt ? mt.label : m.matterTypeKey}</td>`);
            tr.append(`<td>${rule ? (rule.schedule + ' — ' + rule.item) : '—'}</td>`);
            tr.append(`<td><button class="btn btn-sm btn-outline-danger btn-unmap" data-key="${m.matterTypeKey}">Unlink</button></td>`);
            tb.append(tr);
        });
    }

    // CALCULATOR: find applicable rule(s) for a matter type and value
    function findRulesForMatter(matterKey, value){
        // mapping: find rule id
        const map = mappings.find(m=> m.matterTypeKey===matterKey);
        if(!map) return [];
        // for demo, take rule and (if slabs) also fallback to same schedule rules by range
        const baseRule = sampleRules.find(r=> r.id==map.ruleId);
        if(!baseRule) return [];
        // If rule has specific min/max that fit value, return it; otherwise search schedule
        if(baseRule.min!=null && baseRule.max!=null){
            if(value >= baseRule.min && value <= baseRule.max) return [baseRule];
        }
        // fallback: find all rules with same schedule and range that matches
        const sameSchedule = sampleRules.filter(r=> r.schedule === baseRule.schedule && (r.min==null || value>=r.min) && (r.max==null || r.max===null || value<=r.max));
        return sameSchedule.length? sameSchedule : [baseRule];
    }

    // compute using simple rules for demo
    function computeFees(matterType, value, appearances=0, disbursements=0, discretionPercent=0){
        const rules = findRulesForMatter(matterType, value);
        let instruction = 0;
        // For simple percent/fixed rules: sum applicable instruction parts (sliding)
        rules.forEach(r=>{
            if(r.type === 'percentage' && r.min!=null){
                // portion in this slab
                const lower = r.min || 0;
                const upper = r.max==null ? Infinity : r.max;
                const portion = Math.max(0, Math.min(value, upper) - lower + (lower===0 ? 0 : 0));
                // If min is 0 and upper is 1M, the formula is portion * rate
                instruction += portion * r.value;
            } else if(r.type === 'percentage' && r.min==null){
                instruction += value * r.value;
            } else if(r.type === 'fixed'){
                instruction += (r.value || 0);
            } else if(r.type === 'formula' && r.value){
                // naive evaluation (safe sanitized in production)
                try {
                    const expr = String(r.value).replace(/\{value\}/g, value);
                    instruction += Function('"use strict";return (' + expr + ')')();
                } catch(e){}
            }
        });

        // Apply discretion (increase/decrease instruction by percent)
        instruction = instruction * (1 + (discretionPercent/100));

        // getting-up = 1/3 of instruction if rule exists specifying it; otherwise 1/3
        const getup = instruction / 3;

        // appearances: use a fixed per appearance for demo (KSh 3000)
        const perAppearance = 3000;
        const appearancesTotal = appearances * perAppearance;

        // total
        const totalBeforeVAT = instruction + getup + appearancesTotal + disbursements;
        return {
            instruction: round2(instruction),
            getup: round2(getup),
            appearancesCount: appearances,
            appearancesTotal: round2(appearancesTotal),
            disbursements: round2(disbursements),
            totalBeforeVAT: round2(totalBeforeVAT)
        };
    }

    function round2(v){ return Math.round((v + Number.EPSILON) * 100)/100; }

    // Save calculation to savedCalculations
    function saveCalculation(obj){
        const id = uid('calc');
        const rec = {
            id, matterType: $('#calcMatterType option:selected').text(), matterKey: $('#calcMatterType').val(),
            value: Number($('#calcValue').val()), createdAt: new Date().toISOString(),
            breakdown: obj, total: obj.totalBeforeVAT, status: 'Draft', note: ''
        };
        savedCalculations.push(rec);
        renderSavedCalculations();
        return rec;
    }

    function renderSavedCalculations(){
        const tb = $('#calcsTable tbody').empty();
        savedCalculations.forEach((c,i)=>{
            const tr = $('<tr>');
            tr.append(`<td>${i+1}</td>`);
            tr.append(`<td>${c.matterType}</td>`);
            tr.append(`<td>KSh ${fmt2(c.breakdown.instruction)}</td>`);
            tr.append(`<td>KSh ${fmt2(c.total)}</td>`);
            tr.append(`<td><span class="badge bg-${c.status==='Approved' ? 'success' : (c.status==='Rejected'?'danger':'secondary')}">${c.status}</span></td>`);
            tr.append(`<td>
      <button class="btn btn-sm btn-outline-primary btn-view" data-id="${c.id}">View</button>
      <button class="btn btn-sm btn-outline-warning btn-adjust ms-1" data-id="${c.id}">Adjust</button>
      <button class="btn btn-sm btn-outline-success btn-approve ms-1" data-id="${c.id}">Approve</button>
    </td>`);
            tb.append(tr);
        });
    }

    // EXPORT bill HTML & preview in Bill tab
    function renderBill(record){
        const b = record;
        const html = `
    <div class="print-area">
      <div class="d-flex justify-content-between mb-3">
        <div><h5>Firm: Sheria360 Demo Law</h5><div class="small-muted">Bill of Costs (sample)</div></div>
        <div class="text-end"><strong>Date:</strong> ${new Date(b.createdAt).toLocaleDateString()}</div>
      </div>
      <hr>
      <div><strong>Client / Matter:</strong> ${b.matterType}</div>
      <div class="mt-3">
        <table class="table table-sm">
          <tbody>
            <tr><th style="width:60%">Instruction Fee</th><td>KSh ${fmt2(b.breakdown.instruction)}</td></tr>
            <tr><th>Getting-Up Fee</th><td>KSh ${fmt2(b.breakdown.getup)}</td></tr>
            <tr><th>Appearances (${b.breakdown.appearancesCount})</th><td>KSh ${fmt2(b.breakdown.appearancesTotal)}</td></tr>
            <tr><th>Disbursements</th><td>KSh ${fmt2(b.breakdown.disbursements)}</td></tr>
            <tr class="table-light"><th>Total</th><td><strong>KSh ${fmt2(b.total)}</strong></td></tr>
          </tbody>
        </table>
      </div>
      <div class="mt-4 small-muted">This bill was computed using ARO sample rules. In production, the rule id and effective date should be stored for audit.</div>
    </div>
  `;
        $('#billBody').html(html);
        // switch to bill tab
        const tab = new bootstrap.Tab($('#bill-tab'));
        tab.show();
    }

    // --- EVENT BINDERS ---
    $(function(){

        // initial data render
        renderRules();
        renderMatterTypeSelectors();

        // fill matter type selects & rule select
        matterTypes.forEach(m=>{
            $('#calcMatterType, #matterTypeSelect').append(`<option value="${m.key}">${m.label}</option>`);
        });
        sampleRules.forEach(r=> $('#ruleSelect').append(`<option value="${r.id}">${r.schedule} — ${r.item}</option>`));

        renderMappings();
        renderSavedCalculations();

        // New rule
        $('#btnNewRule').on('click', ()=> {
            $('#ruleForm')[0].reset();
            $('#ruleId').val('');
            var modal = new bootstrap.Modal(document.getElementById('ruleModal'));
            modal.show();
        });

        // Rule form save
        $('#ruleForm').on('submit', function(e){
            e.preventDefault();
            const idVal = $('#ruleId').val();
            const payload = {
                id: idVal ? Number(idVal) : (sampleRules.length? Math.max(...sampleRules.map(r=>r.id))+1 : 1),
                schedule: $('#ruleSchedule').val(),
                item: $('#ruleItem').val(),
                type: $('#ruleType').val(),
                min: $('#ruleMin').val() ? Number($('#ruleMin').val()) : null,
                max: $('#ruleMax').val() ? Number($('#ruleMax').val()) : null,
                value: $('#ruleVal').val() ? Number($('#ruleVal').val()) : null,
                effectiveFrom: $('#ruleFrom').val(),
                notes: $('#ruleNotes').val()
            };
            // update or insert
            if(idVal){
                const idx = sampleRules.findIndex(r=> r.id==payload.id);
                sampleRules[idx] = payload;
            } else { sampleRules.push(payload); }
            renderRules();
            // refresh ruleSelect
            $('#ruleSelect').empty(); sampleRules.forEach(r=> $('#ruleSelect').append(`<option value="${r.id}">${r.schedule} — ${r.item}</option>`));
            bootstrap.Modal.getInstance(document.getElementById('ruleModal')).hide();
        });

        // edit / delete events (delegation)
        $('#rulesTable').on('click', '.btn-edit', function(){
            const id = $(this).data('id');
            const r = sampleRules.find(rr=> rr.id==id);
            if(!r) return;
            $('#ruleId').val(r.id);
            $('#ruleSchedule').val(r.schedule);
            $('#ruleItem').val(r.item);
            $('#ruleType').val(r.type);
            $('#ruleMin').val(r.min == null ? '' : r.min);
            $('#ruleMax').val(r.max == null ? '' : r.max);
            $('#ruleVal').val(r.value == null ? '' : r.value);
            $('#ruleFrom').val(r.effectiveFrom || '');
            $('#ruleNotes').val(r.notes || '');
            new bootstrap.Modal(document.getElementById('ruleModal')).show();
        });

        $('#rulesTable').on('click', '.btn-delete', function(){
            const id = $(this).data('id');
            if(!confirm('Delete rule?')) return;
            const idx = sampleRules.findIndex(r=> r.id==id);
            if(idx>=0) sampleRules.splice(idx,1);
            renderRules();
            $('#ruleSelect').empty(); sampleRules.forEach(r=> $('#ruleSelect').append(`<option value="${r.id}">${r.schedule} — ${r.item}</option>`));
        });

        // Map button
        $('#btnMap').click(function(e){
            e.preventDefault();
            const mt = $('#matterTypeSelect').val();
            const ruleId = Number($('#ruleSelect').val());
            // replace mapping for that matter type
            mappings = mappings.filter(m=> m.matterTypeKey !== mt);
            mappings.push({matterTypeKey:mt, ruleId});
            renderMappings();
            alert('Mapped successfully');
        });

        $('#mappingsTable').on('click', '.btn-unmap', function(){
            const key = $(this).data('key');
            mappings = mappings.filter(m=> m.matterTypeKey !== key);
            renderMappings();
        });

        // Calculator compute
        $('#btnCalculate').on('click', function(){
            const matterKey = $('#calcMatterType').val();
            const value = Number($('#calcValue').val());
            const disp = Number($('#calcDisbursements').val() || 0);
            const app = Number($('#calcAppearances').val() || 0);
            const disc = Number($('#calcDiscretion').val() || 0);

            if(!matterKey){ alert('Select matter type'); return; }
            if(!value || value<=0){ alert('Enter positive subject value'); return; }

            // compute
            const breakdown = computeFees(matterKey, value, app, disp, disc);
            $('#resInstruction').text('KSh ' + fmt2(breakdown.instruction));
            $('#resGettingUp').text('KSh ' + fmt2(breakdown.getup));
            $('#resAppearances').text('KSh ' + fmt2(breakdown.appearancesTotal) + ' ('+breakdown.appearancesCount+' × KSh 3000)');
            $('#resDisbursements').text('KSh ' + fmt2(breakdown.disbursements));
            $('#resTotal').text('KSh ' + fmt2(breakdown.totalBeforeVAT));
            $('#calcResult').show();

            // store in current form dataset for save
            $('#btnSaveCalc').data('lastBreakdown', breakdown);
        });

        // save calculation
        $('#btnSaveCalc').on('click', function(){
            const breakdown = $(this).data('lastBreakdown');
            if(!breakdown){ alert('Compute first'); return; }
            const rec = saveCalculation(breakdown);
            alert('Calculation saved (ID: ' + rec.id + ')');
            // switch to approvals tab
            const tab = new bootstrap.Tab($('#approvals-tab'));
            tab.show();
        });

        // view/adjust/approve actions on saved calculations (delegated)
        $('#calcsTable').on('click', '.btn-view', function(){
            const id = $(this).data('id');
            const rec = savedCalculations.find(c=> c.id===id);
            if(!rec) return;
            renderBill(rec);
        });

        $('#calcsTable').on('click', '.btn-adjust', function(){
            const id = $(this).data('id');
            $('#adjustCalcId').val(id);
            $('#adjustPercent').val(0);
            $('#adjustNote').val('');
            new bootstrap.Modal(document.getElementById('adjustModal')).show();
        });

        $('#adjustForm').on('submit', function(e){
            e.preventDefault();
            const id = $('#adjustCalcId').val();
            const pct = Number($('#adjustPercent').val());
            const note = $('#adjustNote').val();
            const idx = savedCalculations.findIndex(s=> s.id===id);
            if(idx<0) return;
            // apply percent to total and breakdown
            const rec = savedCalculations[idx];
            rec.note = note;
            rec.breakdown.instruction = round2(rec.breakdown.instruction * (1 + pct/100));
            rec.breakdown.getup = round2(rec.breakdown.getup * (1 + pct/100));
            rec.breakdown.appearancesTotal = round2(rec.breakdown.appearancesTotal * (1 + pct/100));
            rec.breakdown.totalBeforeVAT = round2(rec.breakdown.totalBeforeVAT * (1 + pct/100));
            rec.total = rec.breakdown.totalBeforeVAT;
            rec.status = 'Adjusted';
            renderSavedCalculations();
            bootstrap.Modal.getInstance(document.getElementById('adjustModal')).hide();
        });

        $('#calcsTable').on('click', '.btn-approve', function(){
            const id = $(this).data('id');
            const idx = savedCalculations.findIndex(s=> s.id===id);
            if(idx<0) return;
            savedCalculations[idx].status = 'Approved';
            renderSavedCalculations();
            alert('Calculation approved');
        });

        // Export to bill: takes last saved calc (for demo) or selected calc
        $('#btnExportToBill').on('click', function(){
            // choose last saved
            if(!savedCalculations.length){ alert('No saved calculations found'); return; }
            const rec = savedCalculations[savedCalculations.length-1];
            renderBill(rec);
        });

        // Print bill
        $('#btnPrintBill').on('click', function(){
            const content = $('#billBody').html();
            if(!content){ alert('No bill to print'); return; }
            const w = window.open('', '_blank');
            w.document.write('<html><head><title>Bill</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"></head><body>');
            w.document.write(content);
            w.document.write('</body></html>');
            w.document.close();
            w.print();
        });

        // Download DOCX (demo: create simple .doc via data blob, not real docx)
        $('#btnDownloadDoc').on('click', function(){
            const content = $('#billBody').html();
            if(!content){ alert('No bill to download'); return; }
            const blob = new Blob(['\ufeff', content], {type: 'application/msword'});
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url; a.download = 'bill.doc';
            document.body.appendChild(a); a.click(); a.remove();
            URL.revokeObjectURL(url);
        });

    });
</script>

