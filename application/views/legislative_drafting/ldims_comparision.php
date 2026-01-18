<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="mb-0">Automated Legislative Comparison</h3>
</div>

<div class="card">
    <div class="card-body">

        <!-- Real-Time Alerts -->
        <div class="alert alert-warning d-flex align-items-center" role="alert">
            <i class="fas fa-exclamation-triangle mr-2"></i>
            <strong>Conflict Found:</strong> Section 5 of the draft conflicts with Environment Protection Act, 2018, Clause 12.
            <a href="#conflict1" class="ml-auto btn btn-sm btn-outline-dark">View Details</a>
        </div>
        <div class="alert alert-info d-flex align-items-center" role="alert">
            <i class="fas fa-info-circle mr-2"></i>
            <strong>Duplication Detected:</strong> Section 8 repeats text from Regulation 4, 2020.
            <a href="#duplication1" class="ml-auto btn btn-sm btn-outline-dark">Review</a>
        </div>

        <!-- Side-by-Side Comparison -->
        <div class="row mt-4">
            <!-- Draft -->
            <div class="col-md-6">
                <div class="card border-primary">
                    <div class="card-header bg-primary text-white">
                        Draft Proposal - Environmental Management Bill, 2025
                    </div>
                    <div class="card-body" style="height: 350px; overflow-y: auto;">
                        <p><strong>Section 5:</strong> Any person found disposing waste into rivers without a permit shall be fined KES 200,000 or face imprisonment for up to 6 months.</p>
                        <p><strong>Section 8:</strong> Businesses producing over 50 tonnes of waste annually must submit quarterly reports to NEMA.</p>
                        <p><strong>Section 12:</strong> Local authorities shall be responsible for monitoring all water treatment facilities.</p>
                    </div>
                </div>
            </div>

            <!-- Master Statute Database -->
            <div class="col-md-6">
                <div class="card border-success">
                    <div class="card-header bg-success text-white">
                        Matched Sections - Master Statute Database
                    </div>
                    <div class="card-body" style="height: 350px; overflow-y: auto;">
                        <div id="conflict1">
                            <p><strong>Environment Protection Act, 2018 - Clause 12:</strong> Disposal of waste into public water sources without a permit is punishable by a fine of KES 100,000 or imprisonment for 3 months.</p>
                            <button class="btn btn-sm btn-outline-danger">Flag for Review</button>
                            <button class="btn btn-sm btn-outline-success">Accept Draft Version</button>
                        </div>
                        <hr>
                        <div id="duplication1">
                            <p><strong>Regulation 4, 2020:</strong> Businesses producing more than 50 tonnes of waste annually must submit quarterly reports to the National Environment Management Authority.</p>
                            <button class="btn btn-sm btn-outline-warning">Mark as Duplicate</button>
                            <button class="btn btn-sm btn-outline-primary">Keep Both</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="mt-4 d-flex justify-content-end">
            <button class="btn btn-outline-secondary mr-2"><i class="fas fa-sync"></i> Re-run Comparison</button>
            <button class="btn btn-success"><i class="fas fa-check"></i> Finalize Draft</button>
        </div>
    </div>
</div>
