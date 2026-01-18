<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LDIMS - Legal Advisory Database</title>
    <!-- Bootstrap 4.6 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" xintegrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhCPEh8Wb5KzJbA3t/wF3V12" crossorigin="anonymous">
    <!-- Kendo UI CSS (for default theme) -->
    <link rel="stylesheet" href="https://kendo.cdn.telerik.com/themes/5.10.0/default/default-main.css" />
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f0f2f5;
        }
        .navbar-brand {
            font-weight: 700;
        }
        .container-fluid {
            padding-top: 2rem;
        }
        .card {
            border: none;
            border-radius: 1rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }
        .card-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
            border-top-left-radius: 1rem;
            border-top-right-radius: 1rem;
            font-weight: 600;
        }
        .k-grid {
            border-radius: 1rem;
            overflow: hidden;
        }
        .search-bar {
            margin-bottom: 2rem;
        }
        .search-input {
            border-radius: 25px;
            padding-left: 1.5rem;
            padding-right: 1.5rem;
            border: 1px solid #ced4da;
            transition: all 0.3s ease;
        }
        .search-input:focus {
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
            border-color: #80bdff;
        }
        .search-btn {
            border-radius: 25px;
            font-weight: bold;
            background-color: #007bff;
            border-color: #007bff;
        }
        .btn-link {
            color: #007bff;
        }
        .list-group-item {
            border: none;
            border-radius: 0.5rem;
            margin-bottom: 0.5rem;
            transition: background-color 0.3s ease;
        }
        .list-group-item:hover {
            background-color: #e9ecef;
        }
    </style>
</head>
<body>

<!-- Navigation Bar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">LDIMS</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item">
                    <a class="nav-link" href="#">Requests</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Drafting</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="#">Advisory Opinions <span class="sr-only">(current)</span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Reports</a>
                </li>
            </ul>
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" href="#">Jane Doe</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Logout</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Main Content -->
<div class="container-fluid py-5">
    <div class="row">
        <div class="col-12">
            <!-- Main Search Bar -->
            <div class="row justify-content-center search-bar">
                <div class="col-md-8">
                    <div class="input-group">
                        <input type="text" class="form-control search-input" placeholder="Search for legal opinions...">
                        <div class="input-group-append">
                            <button class="btn btn-primary search-btn" type="button">Search</button>
                        </div>
                    </div>
                    <div class="text-center mt-2">
                        <a class="btn btn-link" data-toggle="collapse" href="#advancedSearchCollapse" role="button" aria-expanded="false" aria-controls="advancedSearchCollapse">
                            Advanced Filters
                        </a>
                    </div>
                </div>
            </div>

            <!-- Advanced Search Collapse -->
            <div class="collapse" id="advancedSearchCollapse">
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="filterDate">Date Range</label>
                                <input type="text" class="form-control" id="filterDate" placeholder="e.g., Year of Issuance (2022)">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="filterType">Legal Matter Type</label>
                                <select class="form-control" id="filterType">
                                    <option>All</option>
                                    <option>Civil</option>
                                    <option>Criminal</option>
                                    <option>Administrative</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="filterDepartment">Department/Agency</label>
                                <input type="text" class="form-control" id="filterDepartment" placeholder="e.g., Ministry of Health">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="filterAttorney">Attorney General</label>
                                <select class="form-control" id="filterAttorney">
                                    <option>All</option>
                                    <option>John Smith</option>
                                    <option>Jane Doe</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Dashboard and Results -->
            <div class="row">
                <!-- Dashboard Section (left-hand side) -->
                <div class="col-md-4 mb-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-header">
                            <h6 class="mb-0">Your Dashboard</h6>
                        </div>
                        <div class="card-body">
                            <h6 class="text-secondary">Recent Opinions</h6>
                            <ul class="list-group list-group-flush mb-4">
                                <li class="list-group-item">Opinion on Data Protection Act</li>
                                <li class="list-group-item">Advisory on Public Procurement</li>
                                <li class="list-group-item">Legal Opinion on Land Reform</li>
                            </ul>
                            <h6 class="text-secondary">Saved Searches</h6>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item">Search for "Constitutional Law"</li>
                                <li class="list-group-item">Opinions by "Jane Doe"</li>
                                <li class="list-group-item">Labor Disputes in 2023</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Search Results Grid (right-hand side) -->
                <div class="col-md-8">
                    <div class="card shadow-sm h-100">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">Search Results</h6>
                            <button type="button" class="btn btn-primary btn-sm rounded-pill">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus-lg" viewBox="0 0 16 16">
                                    <path fill-rule="evenodd" d="M8 2a.5.5 0 0 1 .5.5v5h5a.5.5 0 0 1 0 1h-5v5a.5.5 0 0 1-1 0v-5h-5a.5.5 0 0 1 0-1h5v-5A.5.5 0 0 1 8 2"/>
                                </svg>
                                Submit New Opinion
                            </button>
                        </div>
                        <div class="card-body">
                            <div id="grid"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap and Kendo UI JS -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" xintegrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js" xintegrity="sha384-Fy6S3B9q64WdZWQUiU+q4/2Lc9npb8tCaSX9FK7E8HnRr0Jz8D6OP9dO5Vg3Q9ct" crossorigin="anonymous"></script>
<script src="https://kendo.cdn.telerik.com/2024.1.319/js/kendo.all.min.js"></script>

<script>
    $(document).ready(function() {
        // Sample data for the grid
        const opinionsData = [
            { id: '1', title: 'Advisory on the Data Protection Act, 2019', author: 'Jane Doe', date: '2023-08-15', status: 'Active', department: 'Ministry of ICT' },
            { id: '2', title: 'Legal Opinion on Public Procurement Regulations', author: 'John Smith', date: '2023-07-20', status: 'Active', department: 'National Treasury' },
            { id: '3', title: 'Interpretation of Land Reform Bill', author: 'Peter Jones', date: '2023-06-10', status: 'Withdrawn', department: 'Ministry of Lands' },
            { id: '4', title: 'Opinion on Cross-Border Trade Tariffs', author: 'Jane Doe', date: '2023-05-01', status: 'Active', department: 'Ministry of Trade' },
            { id: '5', title: 'Tax Implications for Non-Profit Organizations', author: 'John Smith', date: '2023-04-22', status: 'Amended', department: 'KRA' }
        ];

        // Initialize the Kendo UI Grid
        $("#grid").kendoGrid({
            dataSource: {
                data: opinionsData,
                pageSize: 10
            },
            height: 400,
            sortable: true,
            filterable: true,
            pageable: {
                refresh: true,
                pageSizes: true,
                buttonCount: 5
            },
            columns: [
                { field: "title", title: "Opinion Title" },
                { field: "author", title: "Author", width: "120px" },
                { field: "date", title: "Date", width: "120px" },
                { field: "status", title: "Status", width: "100px" },
                {
                    title: "Actions",
                    command: [
                        { name: "view", text: "View", click: function(e) {
                                e.preventDefault();
                                var dataItem = this.dataItem($(e.currentTarget).closest("tr"));
                                alert("Viewing details for: " + dataItem.title);
                            }},
                        { name: "download", text: "Download", click: function(e) {
                                e.preventDefault();
                                var dataItem = this.dataItem($(e.currentTarget).closest("tr"));
                                alert("Downloading: " + dataItem.title);
                            }}
                    ],
                    width: "180px"
                }
            ]
        });
    });
</script>
</body>
</html>
