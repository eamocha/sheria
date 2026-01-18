<style>
    .dataTables_wrapper .dataTables_paginate .paginate_button {
        padding: 0.5em 1em;
    }
    .dataTables_wrapper .dataTables_filter {
        float: right;
        text-align: right;
    }
    .dataTables_wrapper .dataTables_length {
        float: left;
    }
    .dataTables_wrapper .dataTables_info {
        float: left;
    }
    .dataTables_wrapper .dataTables_filter input {
        margin-left: 0.5em;
        padding: 0.25em 0.5em;
        border: 1px solid #ced4da;
        border-radius: 0.25rem;
    }
    .action-buttons {
        white-space: nowrap;
    }
    #performanceBondGrid {
        font-size: 0.85rem;
    }
    .k-grid-header th {
        white-space: normal;
        vertical-align: top;
        background-color: #f8f9fa;
        font-weight: 600;
    }
    .k-grid td {
        vertical-align: middle;
    }
    .k-grid tr[data-pb-status="Active"],
    .k-grid tr[data-pb-status="Pending"],
    .k-grid tr[data-pb-status="Released"],
    .k-grid tr[data-pb-status="Received"] {
        background-color: #e6f7e6;
    }
    .k-grid tr[data-pb-status="Expired"] {
        background-color: #f8d7da;
    }
    .k-grid tr[data-pb-status="Claimed"] {
        background-color: #fff3e6;
    }
    .k-grid tr[data-pb-status="Other"] {
        background-color: #f8f9fa;
    }
    .dropdown.more {
        display: inline-block;
        position: relative;
    }
    .dropdown-menu {
        min-width: 100px;
    }
    .flagged-gridcell {
        text-align: center;
    }
    @media (max-width: 1200px) {
        #performanceBondGrid {
            font-size: 0.8rem;
        }
        .k-grid-header th,
        .k-grid td {
            padding: 0.3rem;
        }
    }
</style>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title">Performance Bond Tracker</h3>

    </div>
    <div class="card-body">
        <div class="table-responsive" id="performanceBondGrid"></div>
    </div>
</div>

<script type="text/javascript">
    // Define _lang and gridActionIconHTML (adjust as per your application)
    var _lang = {
        archiveCheckboxTitle: "Select Surety Bond",
        viewEdit: "View/Edit",
        delete: "Delete"
    };
    var gridActionIconHTML = '<span class="k-icon k-i-more-vertical"></span>';


    // Stub for checkUncheckCheckboxes (implement as needed)
    function checkUncheckCheckboxes(checkbox, isMaster) {
        console.log("checkUncheckCheckboxes called:", checkbox, isMaster);
    }

    // Stub for confirmationDialog and contractDelete (implement as needed)
    function confirmationDialog(id, params) {
        console.log("confirmationDialog called:", id, params);
        if (confirm("Are you sure you want to delete this record?")) {
            params.resultHandler(params.parm.id);
        }
    }
    function contractDelete(id) {
        console.log("contractDelete called with id:", id);
        // Implement delete logic here
    }

    jQuery(document).ready(function() {
        var grid = jQuery("#performanceBondGrid").kendoGrid({
            dataSource: {
                transport: {
                    read: {
                        url: getBaseURL('contract') + 'surety_bonds/index',
                        type: "POST",
                        dataType: "json",
                        data: function() {
                            return {
                                model: "surety_bond"
                            };
                        },
                        beforeSend: function() {
                            jQuery('#loader-global').show();
                        },
                        complete: function() {
                            jQuery('#loader-global').hide();
                        }
                    },
                    parameterMap: function(options, operation) {
                        if (operation === "read") {
                            return {
                                take: options.take,
                                skip: options.skip,
                                page: options.page,
                                pageSize: options.pageSize,
                                sort: options.sort,
                                filter: options.filter,
                                loadWithSavedFilters: 0
                            };
                        }
                        return options;
                    }
                },
                schema: {
                    data: "data",
                    total: "totalRows",
                    model: {
                        id: "id",
                        fields: {
                            suretyId: { type: "string" },
                            id: { type: "string" },
                            name: { type: "string" },
                            reference_number: { type: "string" },
                            parties: { type: "string" },
                            start_date: { type: "date" },
                            end_date: { type: "date" },
                            value: { type: "number" },
                            currency: { type: "string" },
                            bond_type: { type: "string" },
                            surety_provider: { type: "string" },
                            bond_amount: { type: "number" },
                            bond_number: { type: "string" },
                            effective_date: { type: "date" },
                            expiry_date: { type: "date" },
                            bond_status: { type: "string" },
                            remarks: { type: "string" },
                            user_department: { type: "string" },
                            year: { type: "number" },
                            contract_period: { type: "string" }
                        }
                    }
                },
                pageSize: 20,
                serverPaging: true,
                serverFiltering: true,
                serverSorting: true
            },
            height: 550,
            filterable: {
                mode: "row",
                extra: false
            },
            sortable: true,
            pageable: {
                refresh: true,
                pageSizes: [10, 20, 50, 100],
                buttonCount: 5
            },
            toolbar: [
                {
                    template: `
                        <input type='text' class='form-control lookup' id='gridSearch' placeholder='Search all columns...' style='width: 200px; margin-right: 10px;' />
                    `
                }

            ],
            groupable: {
                messages: {
                    empty: ""
                }
            },
            columns: [
                {
                    field: "suretyId",
                    title: "Bond ID",
                    width: "70px",
                    filterable: false,
                    sortable: false,

                    template: `
                        <a href="javascript:;" class="surety-id-link" data-toggle="dropdown">#= suretyId #</a>
                        <div class="dropdown more">
                            <ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">
                                <li><a class="dropdown-item" href="javascript:;" onclick="suretyForm('#= id #', '#= suretyId #', 'edit')">${_lang.viewEdit}</a></li>
                                <li><a class="dropdown-item" href="javascript:;" onclick="confirmationDialog('confim_delete_action', {resultHandler: contractDelete, parm: {'id': '#= suretyId #'}});">${_lang.delete}</a></li>
                            </ul>
                        </div>
                    `,
                    attributes: { class: "flagged-gridcell" }
                },
                {
                    field: "year",
                    title: "Year",
                    width: "80px",
                    template: "#=year != null ? year : 'N/A' #",
                    filterable: {
                        cell: {
                            operator: "eq",
                            showOperators: false
                        }
                    }
                },
                {
                    field: "reference_number",
                    title: "Contract No.",
                    width: "150px",
                    template: "<a href='modules/contract/contracts/view/#=id#'>#=reference_number || 'N/A' #</a>",
                    filterable: {
                        cell: {
                            operator: "contains",
                            showOperators: false
                        }
                    }
                },
                {
                    field: "name",
                    title: "Contract",
                    width: "200px",
                    template: "<a href='modules/contract/contracts/view/#=id#'>#=name || 'N/A' #</a>",
                    filterable: {
                        cell: {
                            operator: "contains",
                            showOperators: false
                        }
                    }
                },
                {
                    field: "parties",
                    title: "Parties",
                    width: "200px",
                    template: "#=parties || 'N/A' #",
                    filterable: {
                        cell: {
                            operator: "contains",
                            showOperators: false
                        }
                    }
                },
                {
                    field: "start_date",
                    title: "Start Date",
                    width: "120px",
                    format: "{0:yyyy-MM-dd}",
                    template: "#=start_date ? kendo.toString(start_date, 'yyyy-MM-dd') : 'N/A' #",
                    filterable: {
                        cell: {
                            operator: "eq",
                            showOperators: false,
                            template: function(args) {
                                args.element.kendoDatePicker({
                                    format: "yyyy-MM-dd"
                                });
                            }
                        }
                    }
                },
                {
                    field: "contract_period",
                    title: "Contract Period",
                    width: "150px",
                    template: "#=contract_period || 'N/A' #"
                },
                {
                    field: "end_date",
                    title: "End Date",
                    width: "120px",
                    format: "{0:yyyy-MM-dd}",
                    template: "#=end_date ? kendo.toString(end_date, 'yyyy-MM-dd') : 'N/A' #",
                    filterable: {
                        cell: {
                            operator: "eq",
                            showOperators: false,
                            template: function(args) {
                                args.element.kendoDatePicker({
                                    format: "yyyy-MM-dd"
                                });
                            }
                        }
                    }
                },
                {
                    field: "value",
                    title: "Contract Price",
                    width: "120px",
                    format: "{0:n2}",
                    template: "#=value != null ? kendo.toString(value, 'n2') : 'N/A' #",
                    filterable: {
                        cell: {
                            operator: "eq",
                            showOperators: false
                        }
                    }
                },
                {
                    field: "user_department",
                    title: "User Department",
                    width: "150px",
                    template: "#=user_department || 'N/A' #",
                    filterable: {
                        cell: {
                            operator: "contains",
                            showOperators: false
                        }
                    }
                },
                {
                    field: "bond_type",
                    title: "Bond Type",
                    width: "120px",
                    template: "#=bond_type || 'N/A' #",
                    filterable: {
                        cell: {
                            operator: "contains",
                            showOperators: false
                        }
                    }
                },
                {
                    field: "surety_provider",
                    title: "Surety Provider",
                    width: "150px",
                    template: "#=surety_provider || 'N/A' #",
                    filterable: {
                        cell: {
                            operator: "contains",
                            showOperators: false
                        }
                    }
                },
                {
                    field: "bond_amount",
                    title: "Bond Amount",
                    width: "120px",
                    format: "{0:n2}",
                    template: "#=bond_amount != null ? kendo.toString(bond_amount, 'n2') : 'N/A' #",
                    filterable: {
                        cell: {
                            operator: "eq",
                            showOperators: false
                        }
                    }
                },
                {
                    field: "currency",
                    title: "Currency",
                    width: "80px",
                    template: "#=currency || 'N/A' #",
                    filterable: {
                        cell: {
                            operator: "eq",
                            showOperators: false
                        }
                    }
                },
                {
                    field: "bond_number",
                    title: "Bond Number",
                    width: "120px",
                    template: "#=bond_number || 'N/A' #",
                    filterable: {
                        cell: {
                            operator: "contains",
                            showOperators: false
                        }
                    }
                },
                {
                    field: "effective_date",
                    title: "Effective Date",
                    width: "120px",
                    format: "{0:yyyy-MM-dd}",
                    template: "#=effective_date ? kendo.toString(effective_date, 'yyyy-MM-dd') : 'N/A' #",
                    filterable: {
                        cell: {
                            operator: "eq",
                            showOperators: false,
                            template: function(args) {
                                args.element.kendoDatePicker({
                                    format: "yyyy-MM-dd"
                                });
                            }
                        }
                    }
                },
                {
                    field: "expiry_date",
                    title: "Expiry Date",
                    width: "120px",
                    format: "{0:yyyy-MM-dd}",
                    template: "#=expiry_date ? kendo.toString(expiry_date, 'yyyy-MM-dd') : 'N/A' #",
                    filterable: {
                        cell: {
                            operator: "eq",
                            showOperators: false,
                            template: function(args) {
                                args.element.kendoDatePicker({
                                    format: "yyyy-MM-dd"
                                });
                            }
                        }
                    }
                },
                {
                    field: "bond_status",
                    title: "PB Status",
                    width: "120px",
                    template: "#=bond_status === 'RECIVED' ? 'Received' : (bond_status || 'N/A') #",
                    filterable: {
                        cell: {
                            operator: "eq",
                            showOperators: false,
                            template: function(args) {
                                args.element.kendoDropDownList({
                                    dataSource: ["Active", "Expired", "Released", "Claimed", "Pending", "Other", "Received"],
                                    valuePrimitive: true
                                });
                            }
                        }
                    }
                },
                {
                    field: "remarks",
                    title: "Remarks",
                    width: "150px",
                    template: "#=remarks || 'N/A' #",
                    filterable: {
                        cell: {
                            operator: "contains",
                            showOperators: false
                        }
                    }
                }
            ],
            editable: "popup",
            groupable: {
                messages: {
                    empty: ""
                }
            },
            resizable: true,
            reorderable: true,
            dataBound: function(e) {
                var grid = this;
                console.log("Grid data:", grid.dataSource.data());
                jQuery.each(grid.tbody.find("tr"), function() {
                    var dataItem = grid.dataItem(this);
                    if (dataItem) {
                        console.log("Row data:", dataItem);
                        console.log("suretyId:", dataItem.suretyId);
                        var status = dataItem.bond_status === "RECIVED" ? "Received" : dataItem.bond_status;
                        jQuery(this).attr("data-pb-status", status);
                    }
                });
                // Initialize dropdown menus
                jQuery(".dropdown.more").hover(
                    function() { jQuery(this).find(".dropdown-menu").stop(true, true).delay(200).fadeIn(); },
                    function() { jQuery(this).find(".dropdown-menu").stop(true, true).delay(200).fadeOut(); }
                );
            }
        }).data("kendoGrid");

        // Handle search field input
        jQuery("#gridSearch").on("keyup", function() {
            var searchTerm = jQuery(this).val().toLowerCase();
            var grid = jQuery("#performanceBondGrid").data("kendoGrid");
            grid.dataSource.filter({
                logic: "or",
                filters: [
                    { field: "suretyId", operator: "contains", value: searchTerm },
                    { field: "reference_number", operator: "contains", value: searchTerm },
                    { field: "name", operator: "contains", value: searchTerm },
                    { field: "parties", operator: "contains", value: searchTerm },
                    { field: "user_department", operator: "contains", value: searchTerm },
                    { field: "bond_type", operator: "contains", value: searchTerm },
                    { field: "surety_provider", operator: "contains", value: searchTerm },
                    { field: "bond_number", operator: "contains", value: searchTerm },
                    { field: "bond_status", operator: "contains", value: searchTerm },
                    { field: "remarks", operator: "contains", value: searchTerm }
                ]
            });
        });
    });
</script>