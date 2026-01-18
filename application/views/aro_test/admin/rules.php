
<div class="container mt-4">
    <h4 class="mb-3">Advocates Remuneration Order – Rules Management</h4>

    <button id="btnAddRule" class="btn btn-primary mb-3">
        <i class="fa fa-plus"></i> Add New Rule
    </button>

    <div id="aroRulesGrid"></div>
</div>

<script>
    $(document).ready(function () {
        const aroRules = [
            {
                id: 1,
                schedule: 'Schedule I – Contentious Matters',
                item: 'Instruction Fee – Civil Suit',
                computation_type: 'percentage',
                min_value: 100000,
                max_value: 1000000,
                rate: 0.015,
                fixed_fee: null,
                remarks: '1.5% of value subject to discretion'
            },
            {
                id: 2,
                schedule: 'Schedule II – Conveyancing',
                item: 'Transfer of Property',
                computation_type: 'slab',
                min_value: 0,
                max_value: 10000000,
                rate: null,
                fixed_fee: null,
                remarks: 'Use sliding scale for property transfers'
            }
        ];

        $('#aroRulesGrid').kendoGrid({
            dataSource: {
                data: aroRules,
                pageSize: 10
            },
            pageable: true,
            sortable: true,
            filterable: true,
            columns: [
                { field: 'schedule', title: 'Schedule' },
                { field: 'item', title: 'Item' },
                { field: 'computation_type', title: 'Type', width: '120px' },
                { field: 'min_value', title: 'Min (KES)', format: '{0:n0}', width: '120px' },
                { field: 'max_value', title: 'Max (KES)', format: '{0:n0}', width: '120px' },
                { field: 'rate', title: 'Rate (%)', template: '#= rate ? rate * 100 : "-" #', width: '100px' },
                { field: 'fixed_fee', title: 'Fixed Fee', template: '#= fixed_fee ? kendo.toString(fixed_fee, "n0") : "-" #', width: '100px' },
                { field: 'remarks', title: 'Remarks' },
                {
                    title: 'Actions',
                    width: '160px',
                    template:
                        `<button class='btn btn-sm btn-outline-info btn-edit'><i class='fa fa-edit'></i></button>
             <button class='btn btn-sm btn-outline-danger btn-delete ml-2'><i class='fa fa-trash'></i></button>`
                }
            ]
        });

        // Event handlers
        $('#aroRulesGrid').on('click', '.btn-edit', function () {
            const grid = $('#aroRulesGrid').data('kendoGrid');
            const dataItem = grid.dataItem($(this).closest('tr'));
            alert(`Edit Rule: ${dataItem.item}`);
        });
    });
</script>

<div class="container mt-5">
    <h4 class="mb-3">Matter Fee Computation (Based on ARO)</h4>

    <form id="aroComputeForm" class="border p-3 rounded bg-light">
        <div class="form-row">
            <div class="form-group col-md-6">
                <label>Schedule</label>
                <select id="scheduleSelect" class="form-control">
                    <option value="I">Schedule I – Contentious Matters</option>
                    <option value="II">Schedule II – Conveyancing</option>
                </select>
            </div>
            <div class="form-group col-md-6">
                <label>Item</label>
                <select id="itemSelect" class="form-control">
                    <option value="instruction_fee">Instruction Fee – Civil Suit</option>
                    <option value="transfer_property">Transfer of Property</option>
                </select>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group col-md-6">
                <label>Value of Subject Matter (KES)</label>
                <input type="number" id="subjectValue" class="form-control" value="500000" />
            </div>
            <div class="form-group col-md-6">
                <label>Discretionary Adjustment (%)</label>
                <input type="number" id="discretionPercent" class="form-control" value="0" />
            </div>
        </div>

        <button type="button" id="btnCompute" class="btn btn-success">Compute Fee</button>
    </form>

    <div class="mt-4" id="resultContainer" style="display:none;">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Computed Fee</h5>
                <p><strong>Instruction Fee:</strong> <span id="instructionFee"></span> KES</p>
                <p><strong>Getting-Up Fee (⅓ of Instruction):</strong> <span id="gettingUpFee"></span> KES</p>
                <p><strong>Total:</strong> <span id="totalFee"></span> KES</p>
            </div>
        </div>
    </div>
</div>

<script>
    $(function() {
        $('#btnCompute').click(function() {
            const subjectValue = parseFloat($('#subjectValue').val()) || 0;
            const discretion = parseFloat($('#discretionPercent').val()) || 0;

            // Simplified logic for demo
            let instructionFee = subjectValue * 0.015; // 1.5%
            instructionFee += (instructionFee * discretion) / 100;

            const gettingUpFee = instructionFee / 3;
            const total = instructionFee + gettingUpFee;

            $('#instructionFee').text(kendo.toString(instructionFee, 'n0'));
            $('#gettingUpFee').text(kendo.toString(gettingUpFee, 'n0'));
            $('#totalFee').text(kendo.toString(total, 'n0'));

            $('#resultContainer').fadeIn();
        });
    });
</script>