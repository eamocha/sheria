jQuery(document).ready(function () {
  // Show initial loader
  jQuery("#loader-global").show();

  // DataSource configuration
  var dataSource = new kendo.data.DataSource({
    transport: {
      read: {
        url: getBaseURL() + "front_office/incoming",
        dataType: "json",
        type: "GET",
        beforeSend: function () {
          jQuery("#loader-global").show();
        },
        complete: function () {
          jQuery("#loader-global").hide();
        },
        error: function (jqXHR, textStatus, errorThrown) {
          jQuery("#loader-global").hide();
          alert("Failed to load data: " + textStatus);
        },
      },
    },
    schema: {
      data: "data",
      model: {
        fields: {
          id: { type: "string" },
          document_date: { type: "date" },
          date_received: { type: "date" },
          reference_number: { type: "string" },
          sender: { type: "string" },
          correspondence_type_name: { type: "string" },
          subject: { type: "string" },
          assignee: { type: "string" },
          action_required: { type: "string" },
          priority: { type: "string" },
          requires_signature: {
            type: "boolean",
            parse: function (value) {
              return value === "1" || value === 1 || value === true;
            },
          },
          status_name: { type: "string" },
        },
      },
    },
    pageSize: 20,
    sort: { field: "date_received", dir: "desc" },
    serverPaging: true,
    serverFiltering: true,
    serverSorting: true,
  });

  // Create the Grid
  jQuery("#correspondenceGrid").kendoGrid({
    dataSource: dataSource,
    height: 550,
    scrollable: true,
    sortable: true,
    filterable: false,
    pageable: {
      refresh: true,
      pageSizes: [10, 20, 50, 100],
      buttonCount: 5,
    },
    columns: [
    
      {
        field: "id",
        title: "Actions", // Renamed for clarity
        width: 120,
         template:
          '<a href="front_office/view/#=id#" class="" title="View Details">#=id#</a>',
        attributes: { class: "text-center" },
        template: function (dataItem) {
          return `
            <div class="dropdown">
                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    #${dataItem.id}
                </button>
                <div class="dropdown-menu">
                <a class="dropdown-item text-danger" href="javascript:void(0)" onclick="deleteEntry('${dataItem.id}')"><i class="fa fa-trash"></i> Delete</a>
                    <a class="dropdown-item" href="front_office/view/${dataItem.id}"><i class="fa fa-eye"></i> View</a>
                    <a class="dropdown-item" href="front_office/edit/${dataItem.id}"><i class="fa fa-edit"></i> Edit</a>
                    <div class="dropdown-divider"></div>
                    
                </div>
            </div>`;
        },
      },

      {
        field: "document_date",
        title: "Doc Date",
        template: "#= kendo.toString(new Date(document_date), 'dd-MM-yyyy') #",
        width: 100,
      },
      {
        field: "date_received",
        title: "Received Date",
        template: "#= kendo.toString(new Date(date_received), 'dd-MM-yyyy') #",
        width: 120,
      },
      {
        field: "reference_number",
        title: "Reference No.",
        width: 100,
      },
      {
        field: "sender",
        title: "Source",
        width: 150,
      },
      {
        field: "correspondence_type_name",
        title: "Doc Type",
        width: 120,
      },
      {
        field: "subject",
        title: "Subject",
        width: 200,
        template:
          '<a href="front_office/view/#=id#" class="" title="View Details">#=subject#</a>',
      },
      {
        field: "assignee",
        title: "Assigned To",
        width: 150,
      },
      {
        field: "action_required",
        title: "Action",
        width: 100,
      },
      {
        field: "priority",
        title: "Priority",
        width: 100,
      },
      {
        field: "requires_signature",
        title: "Signature?",
        width: 100,
        template: '#= requires_signature ? "Yes" : "No" #',
        attributes: { class: "text-center" },
      },
      {
        field: "status_name",
        title: "Status",
        width: 120,
      },
    ],
    dataBound: function () {
      // Initialize tooltips
      this.element.kendoTooltip({
        filter: "td",
        position: "top",
        content: function (e) {
          var cellText = jQuery(e.target).text();
          return cellText.length > 2 ? cellText : "";
        },
      });
    },
    error: function (e) {
      jQuery("#loader-global").hide();
      alert("Error: " + (e.errorThrown || "Unknown error occurred"));
    },
  });

  // Filter implementation
  function applyFilters() {
    var filters = [];
    var grid = jQuery("#correspondenceGrid").data("kendoGrid");

    // Search filter
    var searchTerm = jQuery("#search").val().toLowerCase();
    if (searchTerm) {
      filters.push({
        field: "subject",
        operator: "contains",
        value: searchTerm,
      });
    }

    // Date range filter
    var dateFrom = jQuery("#date_from").val();
    var dateTo = jQuery("#date_to").val();

    if (dateFrom) {
      filters.push({
        field: "date_received",
        operator: "gte",
        value: new Date(dateFrom),
      });
    }

    if (dateTo) {
      filters.push({
        field: "date_received",
        operator: "lte",
        value: new Date(dateTo + "T23:59:59"),
      });
    }

    // Other filters
    var filterFields = [
      { id: "source", field: "sender" },
      { id: "doc_type", field: "correspondence_type_name" },
      { id: "assigned_to", field: "assignee" },
      { id: "action_required", field: "action_required" },
      { id: "priority", field: "priority" },
      { id: "status", field: "status_name" },
    ];

    jQuery.each(filterFields, function (index, item) {
      var value = jQuery("#" + item.id).val();
      if (value) {
        filters.push({
          field: item.field,
          operator: "eq",
          value: value,
        });
      }
    });

    // Requires signature filter
    var requiresSignature = jQuery("#requires_signature").val();
    if (requiresSignature !== "") {
      filters.push({
        field: "requires_signature",
        operator: "eq",
        value: requiresSignature === "1",
      });
    }

    // Apply filters
    if (filters.length > 0) {
      grid.dataSource.filter({
        logic: "and",
        filters: filters,
      });
    } else {
      grid.dataSource.filter([]);
    }
  }

  // Bind filter events
  jQuery(".filter-input").on("change keyup", function (e) {
    if (jQuery(this).is("select") && e.type !== "change") return;
    if (jQuery(this).is("input[type='text']") && e.type !== "keyup") return;

    applyFilters();
  });

  // Reset filters
  jQuery("#reset-filters").click(function () {
    jQuery(".filter-input").val("");
    applyFilters();
    jQuery("#correspondenceGrid").data("kendoGrid").dataSource.page(1);
  });
});


function deleteEntry(id) {
    if (confirm("Are you sure you want to delete record #" + id + "?")) {
        url: getBaseURL() + "front_office/incoming",
        window.location.href = getBaseURL() + 'front_office/delete/' + id;
    }
}