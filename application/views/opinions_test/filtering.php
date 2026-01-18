<div class="container mt-4">
    <h3 class="mb-3">Legal Advisory Opinions Database</h3>
    <p class="text-muted">Search, filter, and sort legal advisory opinions by metadata and tags.</p>

    <!-- Search Bar -->
    <div class="input-group mb-3">
        <input type="text" id="searchBox" class="form-control" placeholder="Search by keyword, agency, case type, or tag...">
        <div class="input-group-append">
            <button class="btn btn-primary" type="button" id="searchBtn">Search</button>
        </div>
    </div>

    <div id="opinionsGrid"></div>
</div>

<script>
    $(document).ready(function () {
        var data = [
            {
                date: "2025-07-21",
                agency: "Attorney General’s Office",
                caseType: "Constitutional Law",
                tags: ["Constitutional Law", "Data Protection"],
                summary: "Opinion on the interpretation of constitutional amendment procedures."
            },
            {
                date: "2025-06-14",
                agency: "Ministry of Justice",
                caseType: "Contract Law",
                tags: ["Public Procurement", "Contract Compliance"],
                summary: "Guidance on public procurement contract compliance."
            },
            {
                date: "2025-05-03",
                agency: "Parliamentary Counsel",
                caseType: "Administrative Law",
                tags: ["Delegated Legislation", "Administrative Law"],
                summary: "Clarification on the scope of delegated legislation powers."
            }
        ];

        var dataSource = new kendo.data.DataSource({
            data: data,
            pageSize: 10
        });

        $("#opinionsGrid").kendoGrid({
            dataSource: dataSource,
            sortable: true,
            pageable: true,
            filterable: true,
            columns: [
                { field: "date", title: "Date", width: "150px" },
                { field: "agency", title: "Agency", width: "200px" },
                { field: "caseType", title: "Case Type", width: "200px" },
                {
                    field: "tags",
                    title: "Tags",
                    width: "250px",
                    template: function(dataItem) {
                        return dataItem.tags.map(tag =>
                            `<span class="badge badge-primary mr-1">${tag}</span>`
                        ).join(" ");
                    }
                },
                { field: "summary", title: "Opinion Summary" }
            ]
        });

        // Global Search Handler
        function globalSearch() {
            var searchValue = $("#searchBox").val().toLowerCase();

            dataSource.filter({
                logic: "or",
                filters: [
                    { field: "agency", operator: "contains", value: searchValue },
                    { field: "caseType", operator: "contains", value: searchValue },
                    { field: "summary", operator: "contains", value: searchValue },
                    { field: "tags", operator: function(itemTags) {
                            return itemTags.some(tag => tag.toLowerCase().includes(searchValue));
                        }
                    }
                ]
            });
        }

        // Trigger search on button click
        $("#searchBtn").click(function() {
            globalSearch();
        });

        // Trigger search on Enter key
        $("#searchBox").on("keypress", function(e) {
            if (e.which === 13) {
                globalSearch();
            }
        });
    });
</script>
