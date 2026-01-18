 jQuery(document).ready(function () {
        // Initialize Kendo UI DropDownLists
        jQuery("#addressee, #doc_type, #method_of_dispatch, #requires_signature, #status").kendoDropDownList();

        // Date pickers for date_from and date_to
        jQuery("#date_from, #date_to").kendoDatePicker({
            format: "yyyy-MM-dd"
        });

        // Initialize the Kendo UI Grid
        var outgoingGrid = jQuery("#outgoingGrid").kendoGrid({
            dataSource: {
                transport: {
                    read: {
                        url: getBaseURL() + 'front_office/outgoing',
                        dataType: "json",
                        type: "GET",
                        beforeSend: function() {
                            jQuery('#loader-global').show();
                        },
                        complete: function() {
                            jQuery('#loader-global').hide();
                        },
                        error: function(jqXHR, textStatus, errorThrown) {
                            defaultErrorHandler(jqXHR, textStatus, errorThrown);                           
                           
                        }
                    }
                },
                schema: {
                    data: "data",
                    total: "total",
                    model: {
                        fields: {
                            id: { type: "number" },
                            date_sent: { type: "date" },
                            recipient: { type: "string" },
                            sender: { type: "string" },
                            document_type_name: { type: "string" },
                            subject: { type: "string" },
                            ref_number: { type: "string" },
                            mode_of_dispatch: { type: "string" },
                            requires_signature: { type: "boolean" },
                            status_name: { type: "string" },
                            createdBy: { type: "string" }
                        }
                    }
                },
                serverPaging: true,
                serverFiltering: true,
                serverSorting: true,
                pageSize: 20
            },
            height: 550,
            filterable: false,
            sortable: true,
            pageable: {
                refresh: true,
                pageSizes: [20, 50, 100],
                buttonCount: 5
            },
            columns: [
                {
                    field: "id",
                    title: "ID",
                    template: '<a href="' + getBaseURL() + 'front_office/view/#= id #">#= id #</a>',
                    width: 80
                },
                 { field: "ref_number", title: "Reference Number", width: 150 },
                { field: "createdBy", title: "Author", width: 120 }
                , {
                    field: "date_sent",
                    title: "Date Sent",
                    width: 120,
                    template: "#= kendo.toString(kendo.parseDate(date_sent, 'yyyy-MM-dd'), 'yyyy-MM-dd') #"
                },
                { field: "sender", title: "Sender", width: 150 },
                 { field: "recipient", title: "Recipient", width: 150 },
                { field: "document_type_name", title: "Document Type", width: 120 },
                { field: "subject", title: "Subject ", width: 200,
                    template: '<a href="' + getBaseURL() + 'front_office/view/#= id #">#= subject #</a>',
                },

               
                { field: "mode_of_dispatch", title: "Method of Dispatch", width: 150 },
                {
                    field: "requires_signature",
                    title: "Requires Signature/Review",
                    width: 180,
                    template: "#= requires_signature ? 'Yes' : 'No' #"
                },
                { field: "status_name", title: "Status", width: 150 },
               
            ]
        }).data("kendoGrid");

        // Function to get all filter values
        function getFilters() {
            return {
                search: jQuery("#search").val(),
                date_from: jQuery("#date_from").val(),
                date_to: jQuery("#date_to").val(),
                addressee: jQuery("#sender").val(),
                recipient: jQuery("#recipient").val(),
                doc_type: jQuery("#doc_type").val(),
                reference_number: jQuery("#reference_number").val(),
                method_of_dispatch: jQuery("#method_of_dispatch").val(),
                requires_signature: jQuery("#requires_signature").val(),
                status: jQuery("#status").val()
            };
        }

        // Apply filters when filter controls change
        jQuery("#search").keyup(function() {
            outgoingGrid.dataSource.read();
        });

        jQuery("#date_from, #date_to, #addressee, #doc_type, #reference_number, #method_of_dispatch, #requires_signature, #status").change(function() {
            outgoingGrid.dataSource.read();
        });

        // Reset all filters
        jQuery("#reset-filters").click(function() {
            jQuery("#search").val("");
            jQuery("#date_from").data("kendoDatePicker").value("");
            jQuery("#date_to").data("kendoDatePicker").value("");
            jQuery("#sender").data("kendoDropDownList").value("");
            jQuery("#recipient").data("kendoDropDownList").value("");
            jQuery("#doc_type").data("kendoDropDownList").value("");
            jQuery("#reference_number").val("");
            jQuery("#method_of_dispatch").data("kendoDropDownList").value("");
            jQuery("#requires_signature").data("kendoDropDownList").value("");
            jQuery("#status").data("kendoDropDownList").value("");
            outgoingGrid.dataSource.read();
        });
    });
