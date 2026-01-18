
    <style>
         .result-card { border: none; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); margin-bottom: 15px; transition: transform 0.3s; }
        .result-card:hover { transform: translateY(-4px); }
        .file-icon { font-size: 2.5rem; color: #6c757d; }
        mark { background-color: #ffeb3b; padding: 2px 0; }

    </style>
<div class="container-fluid">
    <div class="header">
        <h1 ><i class="fas fa-search mr-2"></i>Document Text Search</h1>
        <p class="text-muted">Search text inside PDFs, Word, Excel, Images (OCR) and more</p>
    </div>

    <div class="search-box">
        <form id="searchForm">
            <div class="form-row">
                <div class="col-md-8 mb-3">
                    <label for="searchTerm" class="form-label">Search Term</label>
                    <input type="text" class="form-control form-control-lg lookup" id="searchTerm" placeholder="Enter text to search..." required>
                </div>
                <div class="col-md-4 mb-3">
                    <label for="fileType" class="form-label">File Type</label>
                    <select class="form-control form-control-lg" id="fileType">
                        <option value="all">All Supported Formats</option>
                        <option value="pdf">PDF Documents</option>
                        <option value="docx">Word Documents</option>
                        <option value="xlsx">Excel Spreadsheets</option>
                        <option value="image">Images (OCR)</option>
                        <option value="text">Text Files</option>
                    </select>
                </div>
            </div>
            <div class="mt-2">
                <button type="submit" class="btn btn-primary btn-lg"><i class="fas fa-search mr-2"></i>Search</button>
                <button type="button" class="btn btn-outline-secondary btn-lg ml-2" id="resetBtn"><i class="fas fa-redo mr-2"></i>Reset</button>
            </div>
        </form>
    </div>

    <div class="results-section">
        <h4 class="mb-3">
            Search Results
            <span class="badge badge-secondary" id="resultCount">0</span>
        </h4>

        <!-- Table format for results -->
        <div class="table-responsive">
            <table class="table table-hover" id="resultsTable" style="display:none;">
                <thead class="thead-light">
                <tr>
                    <th>File</th>
                    <th>Matches</th>
                    <th>Last Modified</th>
                    <th>Size</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody id="resultsContainer"><!-- Filled dynamically --></tbody>
            </table>
        </div>

        <!-- Pagination controls -->
        <nav>
            <ul class="pagination justify-content-center" id="pagination"></ul>
        </nav>

        <div class="text-center py-5" id="noResults">
            <i class="fas fa-search fa-3x text-muted mb-3"></i>
            <p class="text-muted">Enter a search term to find content in your documents</p>
        </div>
    </div>


    <!-- Content Modal -->
    <div class="modal fade" id="contentModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="contentModalTitle">Document Content</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="contentModalBody"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>


    <script>
        jQuery(function() {
            const searchForm = jQuery('#searchForm');
            const resetBtn = jQuery('#resetBtn');
            const noResults = jQuery('#noResults');
            const resultsContainer = jQuery('#resultsContainer');
            const resultCount = jQuery('#resultCount');
            const resultsTable = jQuery('#resultsTable');
            const pagination = jQuery('#pagination');
            const contentModal = jQuery('#contentModal');

            let resultsData = [];
            let currentPage = 1;
            const pageSize = 5; // results per page

            searchForm.on('submit', function(e) {
                e.preventDefault();
                const searchTerm = jQuery('#searchTerm').val();
                const fileType = jQuery('#fileType').val();

                if (searchTerm.trim() !== '') {
                    performSearch(searchTerm, fileType);
                }
            });

            resetBtn.on('click', function() {
                searchForm.trigger('reset');
                noResults.show();
                resultsTable.hide();
                resultsContainer.empty();
                pagination.empty();
                resultCount.text('0');
            });

            function performSearch(searchTerm, fileType) {
                noResults.hide();
                resultsTable.hide();
                resultsContainer.empty();
                pagination.empty();

                jQuery.ajax({
                    url: '<?php echo site_url("documents_search/search/"); ?>',
                    type: 'POST',
                    data: { search_term: searchTerm, file_type: fileType },
                    dataType: 'json',
                    beforeSend: function () {
                        jQuery('#loader-global').show();
                    },
                    success: function(data) {
                        if (data.status === 'success' && data.results.length > 0) {
                            displayResults(data.results, searchTerm);
                        } else {
                            resultsTable.hide();
                            pagination.empty();
                            noResults.show().html(`
                        <i class="fas fa-search fa-3x text-muted mb-3"></i>
                        <p class="text-muted">No results found for "<strong>${searchTerm}</strong>"</p>
                    `);
                        }
                    },
                    complete: function () {
                        jQuery("#loader-global").hide();
                    },
                    error: function(xhr, status, error) {
                        resultsTable.hide();
                        pagination.empty();
                        noResults.show().html(`
                    <i class="fas fa-exclamation-triangle fa-3x text-danger mb-3"></i>
                    <p class="text-danger">Search failed: ${error}</p>
                `);
                    }
                });
            }

            function displayResults(results, searchTerm) {
                resultsData = results;
                resultCount.text(results.length);

                if (results.length > 0) {
                    noResults.hide();
                    resultsTable.show();
                    displayPage(1, searchTerm);
                } else {
                    resultsTable.hide();
                    pagination.empty();
                    noResults.show();
                }
            }

            function displayPage(page, searchTerm) {
                currentPage = page;
                resultsContainer.empty();

                const start = (page - 1) * pageSize;
                const end = start + pageSize;
                const pageResults = resultsData.slice(start, end);

                pageResults.forEach(result => {
                    const iconClass = getIconClass(result.type);
                    const size = formatFileSize(result.size);

                    const row = `
                <tr>
                    <td><i class="fas ${iconClass} file-icon mr-2"></i>${result.filename}</td>
                    <td>${result.matches}</td>
                    <td>${result.modified}</td>
                    <td>${size}</td>
                    <td>
                        <button class="btn btn-outline-primary btn-sm view-content"
                                data-filename="${result.filename}"
                                data-search-term="${searchTerm}">
                            <i class="fas fa-eye mr-1"></i> View
                        </button>
                        <a href="<?php echo site_url('documents_search/download/'); ?>${result.filename}"
                           class="btn btn-outline-success btn-sm">
                           <i class="fas fa-download mr-1"></i> Download
                        </a>
                    </td>
                </tr>`;
                    resultsContainer.append(row);
                });

                setupPagination(searchTerm);
                bindViewContent();
            }

            function setupPagination(searchTerm) {
                pagination.empty();
                const totalPages = Math.ceil(resultsData.length / pageSize);

                if (totalPages <= 1) return;

                // Prev button
                pagination.append(`
            <li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
                <a class="page-link" href="#" data-page="${currentPage - 1}">Previous</a>
            </li>
        `);

                // Page numbers
                for (let i = 1; i <= totalPages; i++) {
                    pagination.append(`
                <li class="page-item ${i === currentPage ? 'active' : ''}">
                    <a class="page-link" href="#" data-page="${i}">${i}</a>
                </li>
            `);
                }

                // Next button
                pagination.append(`
            <li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
                <a class="page-link" href="#" data-page="${currentPage + 1}">Next</a>
            </li>
        `);

                // Bind click events
                jQuery('#pagination .page-link').on('click', function(e) {
                    e.preventDefault();
                    const page = parseInt(jQuery(this).data('page'));
                    if (!isNaN(page)) {
                        displayPage(page, jQuery('#searchTerm').val());
                    }
                });
            }

            function bindViewContent() {
                jQuery('.view-content').on('click', function() {
                    const filename = jQuery(this).data('filename');
                    const searchTerm = jQuery(this).data('search-term');
                    jQuery.post('<?php echo site_url("documents_search/get_file_content/"); ?>',
                        { filename, search_term: searchTerm },
                        function(data) {
                            if (data.status === 'success') {
                                jQuery('#contentModalTitle').text(`Content of ${filename}`);
                                jQuery('#contentModalBody').html(data.content);
                                contentModal.modal('show');
                            }
                        }, 'json').fail(function(xhr, status, error) {
                        alert('Failed to load content: ' + error);
                    });
                });
            }

            function getIconClass(fileType) {
                switch (fileType) {
                    case 'pdf': return 'fa-file-pdf';
                    case 'docx': case 'doc': return 'fa-file-word';
                    case 'xlsx': case 'xls': return 'fa-file-excel';
                    case 'jpg': case 'jpeg': case 'png': case 'gif': return 'fa-file-image';
                    case 'txt': return 'fa-file-alt';
                    case 'html': case 'htm': return 'fa-file-code';
                    default: return 'fa-file';
                }
            }

            function formatFileSize(bytes) {
                if (bytes === 0) return '0 Bytes';
                const k = 1024, sizes = ['Bytes', 'KB', 'MB', 'GB'];
                const i = Math.floor(Math.log(bytes) / Math.log(k));
                return (bytes / Math.pow(k, i)).toFixed(2) + ' ' + sizes[i];
            }
        });
    </script>
