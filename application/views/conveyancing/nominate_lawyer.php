<div class="modal fade" id="nominateExternalCounselModal" tabindex="-1" role="dialog" aria-labelledby="nominateExternalCounselModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content rounded">
            <div class="modal-header">
                <h5 class="modal-title" id="nominateExternalCounselModalLabel">Nominate External Counsel</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <?php echo form_open("", 'id="nominateExternalCounselForm" method="post"'); ?>
                <?php
                // Hidden field for conveyancing_instrument_id
                echo form_input([
                    "type"  => "hidden",
                    "name"  => "conveyancing_instrument_id",
                    "id"    => "conveyancing_instrument_id_for_nomination",
                    "value" => "" // This will be set by JavaScript when the modal is opened
                ]);
                ?>

                <div class="form-group">
                    <label for="external_counsel_name">External Counsel Name <span class="text-danger">*</span></label>
                    <?php
                    // Input for external counsel name with Typeahead
                    echo form_input([
                        "type"        => "text",
                        "name"        => "external_counsel_name",
                        "id"          => "external_counsel_name",
                        "class"       => "form-control rounded typeahead-counsel",
                        "placeholder" => "Search for a company...",
                        "required"    => true
                    ]);
                    ?>
                    <?php
                    // Hidden field to store the ID of the selected external counsel
                    echo form_input([
                        "type"  => "hidden",
                        "name"  => "external_counsel_id",
                        "id"    => "external_counsel_id",
                        "value" => "" // This will be set by JavaScript on typeahead selection
                    ]);
                    ?>
                    <small class="form-text text-muted">Select an existing company or add a new one.</small>
                    <div data-field="external_counsel_name" class="inline-error d-none text-danger"></div>
                </div>

                <div class="form-group">
                    <label for="nomination_notes">Notes</label>
                    <?php
                    // Textarea for nomination notes
                    echo form_textarea([
                        "name"        => "nomination_notes",
                        "id"          => "nomination_notes",
                        "class"       => "form-control rounded",
                        "rows"        => "3",
                        "placeholder" => "Any specific notes for this nomination..."
                    ]);
                    ?>
                </div>

                <?php echo form_close(); ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary rounded" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary rounded" id="submitNominationBtn">Nominate</button>
            </div>
        </div>
    </div>
</div>