<div class="primary-style">
    <div class="modal fade" id="partyModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title font-weight-bold" id="modalTitle">Matter Party</h5>
                    <button type="button" class="close" data-dismiss="modal" >
                        <span >&times;</span>
                    </button>
                </div>

                <form id="partyForm" class="p-4">
                    <input type="hidden" id="case_id" name="case_id" value="<?php echo $case_id; ?>" />
                    <input type="hidden" id="party_id" name="party_id" value="<?php echo $party_id??0; ?>" />
                    <input type="hidden" id="mode" name="mode" value="<?php echo $mode ?? 'add'; ?>" />
                  

                    <div class="form-group">
                        <label class="d-block mb-2 font-weight-bold small text-muted">Type</label>
                        <select name="opponent_member_type" id="party-type" class="form-control select-picker" tabindex="-1">
                            <option value="company" <?php echo isset($selected_values["container_common_fields"]["client_data"]["type"]) && $selected_values["container_common_fields"]["client_data"]["type"] == "Company" ? "selected='selected'" : ""; ?>>
                                <?php echo $this->lang->line("company_or_group"); ?>
                            </option>
                            <option value="contact" <?php echo isset($selected_values["container_common_fields"]["client_data"]["type"]) && $selected_values["container_common_fields"]["client_data"]["type"] == "Person" ? "selected='selected'" : ""; ?>>
                                <?php echo $this->lang->line("contact"); ?>
                            </option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="party-lookup" class="font-weight-bold small text-muted">Party Name</label>
                        <input type="hidden" name="opponent_member_id" id="party-member-id" value="<?php echo $party_record["party_member_id"] ?? ""; ?>" />
                        <input type="text" name="partyLookup" id="party-lookup" class="form-control lookup" value="<?php echo $party_record["opponentName"] ?? ""; ?>" class="form-control" title="<?php echo $this->lang->line("start_typing"); ?>" />
                    </div>
                    <div class="form-group">
                        <label for="party_position" class="font-weight-bold small text-muted">Party Position
                            <a href="javascript:void(0)" onclick="quickAdministrationDialog('case_opponent_positions', jQuery('#partyModal'), true, false, false, jQuery('[data-field-id=administration-case_party_positions]'));" class="icon-alignment btn btn-link px-0 administration-case_party_position-quick-add"><i class="icon fa fa-square-plus p-1 font-18 purple_color"> </i></a>
                                                        
                        </label>
                        <?php echo form_dropdown("opponent_position", $party_positions, $partyData["opponent_position"] ?? "", 'id="party-position" class="form-control select-picker" data-live-search="true" data-field-id="administration-case_party_positions"'); ?>
                    </div>

                    <div class="modal-footer d-flex justify-content-end border-top pt-4">
                        <a href="javascript;" id="cancelBtn" class="btn btn-secondary" data-dismiss="modal">
                            Cancel
</a>
                        <a  href="javascript:;" class="btn btn-primary shadow-sm" id="submitPartyBtn">Add Party</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    jQuery(document).ready(function() {
        var container = jQuery('#partyModal');

        function setPartyToForm(data) {
            if (data) {
                jQuery('#party-member-id').val(data.id);
                var fullName = '';
                if (data.firstName) fullName += data.firstName + ' ';
                if (data.father) fullName += data.father + ' ';
                if (data.lastName) fullName += data.lastName;
                fullName = fullName.trim();
                // Use .typeahead('val', ...) to set value without triggering clearing
                jQuery('#party-lookup').typeahead('val', fullName);
            } else {
                jQuery('#party-member-id').val('');
                jQuery('#party-lookup').typeahead('val', '');
            }
        }

        function setCompanyToForm(data) {
            if (data) {
                jQuery('#party-member-id').val(data.id);
                jQuery('#party-lookup').typeahead('val', data.name);
            } else {
                jQuery('#party-member-id').val('');
                jQuery('#party-lookup').typeahead('val', '');
            }
        }

        function initLookup() {
            var partyType = jQuery('#party-type', container).val();
            var lookupDetails = {
                'lookupField': jQuery('#party-lookup', container),
                'errorDiv': 'party_member_id',
                'hiddenId': '#party-member-id',
                'resultHandler': partyType === 'company' ? setCompanyToForm : setPartyToForm
            };
            // Remove previous typeahead instance
            if (jQuery('#party-lookup', container).data('typeahead')) {
                jQuery('#party-lookup', container).typeahead('destroy');
            }
            if (partyType === 'company') {
                lookUpCompanies(lookupDetails, jQuery(container));
            } else {
                lookUpContacts(lookupDetails, jQuery(container));
            }
        }

        container.on('shown.bs.modal', function () {
            jQuery('#party-lookup').trigger('focus');
        });

        jQuery('#party-type', container).on('change', function() {
            jQuery('#party-member-id', container).val('');
            jQuery('#party-lookup', container).typeahead('val', '');
            initLookup();
        });

        // Prevent clearing the field after selection
        jQuery('#party-lookup', container).on('typeahead:select typeahead:autocomplete', function(e, suggestion) {
            var partyType = jQuery('#party-type', container).val();
            if (partyType === 'company') {
                setCompanyToForm(suggestion);
            } else {
                setPartyToForm(suggestion);
            }
        });

        // Also handle manual input blur to keep value if selected
        jQuery('#party-lookup', container).on('blur', function() {
            var partyId = jQuery('#party-member-id', container).val();
            if (!partyId) {
                jQuery(this).typeahead('val', '');
            }
        });

        initLookup();

    });
</script>
