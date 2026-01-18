<div class="modal fade" id="assignOfficerModal" tabindex="-1" role="dialog" aria-labelledby="assignOfficerModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form id="assignOfficerForm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="assignOfficerModalLabel">Assign Officer</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="teamSelect">Select Team</label>
                        <?= form_dropdown(
                            'team_id',
                            $teams,
                            '',
                            'class="form-control" id="teamSelect" required'
                        ) ?>
                    </div>
                    <div class="form-group">
                        <label for="userSelect">Select User</label>
                        <select class="form-control" data-live-search="true" id="userSelect" name="user_id" required disabled>
                            <option value="">-- Select User --</option>
                        </select>
                        <div id="userSpinner" class="mt-2" style="display:none;">
                            <span class="text-info">Loading users...</span>
                        </div>
                        <div id="noUsersMsg" class="mt-2" style="display:none;">
                            <span class="text-warning">No users found in this team.</span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="comments">Comments</label>
                        <textarea class="form-control" id="comments" name="comments" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Assign</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
jQuery('#teamSelect').on('change', function() {
    var teamId = jQuery(this).val();
    jQuery('#userSelect').prop('disabled', true).html('<option value="">-- Select User --</option>');
    jQuery('#userSpinner').show();
    jQuery('#noUsersMsg').hide();
    if (teamId) {
       //   assignmentPerType(teamId, 'litigation', "assignment-dialog", true);
        jQuery.ajax({
            url: getBaseURL() + 'cases/assignOfficers/'+case_id,
        type: 'GET',
        dataType: 'JSON',
        data: {
            'type': "getUsersByTeam",
            'category': "criminal",
            'team_id': teamId
        },
            success: function(response) {
                jQuery('#userSpinner').hide();
                jQuery('#userSelect').html('<option value="">-- Select User --</option>');
                if (response.users && Object.keys(response.users).length > 0) {
                    jQuery('#userSelect').prop('disabled', false);
                    jQuery.each(response.users, function(id, name) {
                        jQuery('#userSelect').append(
                            jQuery('<option>', { value: id, text: name })
                        );
                    });
                } else {
                    jQuery('#noUsersMsg').show();
                }
            },
            error: function() {
                jQuery('#userSpinner').hide();
                jQuery('#noUsersMsg').show();
            }
        });
    } else {
        jQuery('#userSpinner').hide();
        jQuery('#noUsersMsg').hide();
    }
});
</script>