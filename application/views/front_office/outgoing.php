<style>
    .k-grid {
        font-size: 14px;
    }

</style>

<div class="container-fluid mt-3">
    <h2><?php echo $title?></h2>

    <div class="filter-section">
        <div class="row filter-row">
            <div class="col-md-4">
                <label for="search">Search:</label>
                <input type="text" class="k-textbox" id="search" placeholder="Enter search term..." style="width: 100%;"/>
            </div>
            <div class="col-md-2">
                <label for="date_from">Date From:</label>
                <input type="date" id="date_from" class="k-textbox" style="width: 100%;"/>
            </div>
            <div class="col-md-2">
                <label for="date_to">Date To:</label>
                <input type="date" id="date_to" class="k-textbox" style="width: 100%;"/>
            </div>
            <div class="col-md-2">
                <label for="addressee">Addressee:</label>
                <select id="addressee" style="width: 100%;">
                    <option value="">All</option>
                    <option value="Safaricom">Safaricom</option>
                    <option value="Ministry of ICT">Ministry of ICT</option>
                    <option value="KeBS">KeBS</option>
                </select>
            </div>
            <div class="col-md-2">
                <label for="doc_type">Document Type:</label>
                <select id="doc_type" style="width: 100%;">
                    <option value="">All</option>
                    <option value="Letter">Letter</option>
                    <option value="MOU">MOU</option>
                    <option value="Response">Response</option>
                </select>
            </div>
        </div>
        <div class="row filter-row">
            <div class="col-md-2">
                <label for="reference_number">Reference Number:</label>
                <input type="text" id="reference_number" class="k-textbox" placeholder="Enter ref number..." style="width: 100%;"/>
            </div>
            <div class="col-md-2">
                <label for="method_of_dispatch">Method of Dispatch:</label>
                <select id="method_of_dispatch" style="width: 100%;">
                    <option value="">All</option>
                    <option value="Email">Email</option>
                    <option value="Courier">Courier</option>
                    <option value="Post">Post</option>
                </select>
            </div>
            <div class="col-md-2">
                <label for="requires_signature">Requires Signature/Review:</label>
                <select id="requires_signature" style="width: 100%;">
                    <option value="">All</option>
                    <option value="1">Yes</option>
                    <option value="0">No</option>
                </select>
            </div>
            <div class="col-md-2">
                <label for="status">Status:</label>
                <select id="status" style="width: 100%;">
                    <option value="">All</option>
                    <option value="Draft">Draft</option>
                    <option value="Pending Approval">Pending Approval</option>
                    <option value="Sent">Sent</option>
                    <option value="Dispatched">Dispatched</option>
                </select>
            </div>
            <div class="col-md-2">
                <button id="reset-filters" class="k-button" style="margin-top: 24px; width: 100%;">Reset Filters</button>
            </div>
        </div>
    </div>

    <div id="outgoingGrid"></div>
</div>
