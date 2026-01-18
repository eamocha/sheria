
<div class="container-fluid mt-4">


    <div class="col-md-12">
        <ul class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="dashboard/admin"><?= html_escape($this->lang->line("administration")) ?></a>
            </li>
            <li class="breadcrumb-item active">
                <?= html_escape($this->lang->line("contract_numbering")) ?>
            </li>
        </ul>
    </div>
    <h3 class="mb-3">Contract Reference Numbering Formats <?php // echo $ref?></h3>
    <table class="table table-bordered table-striped">
        <thead class="">
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Description</th>
            <th>Pattern</th>
            <th>Example</th>
            <th>Prefix</th>
            <th>Suffix</th>
            <th>Sequence Reset</th>
            <th>Sequence Length</th>
            <th>Last Sequence</th>
            <th>Active</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php if (!empty($formats)): ?>
            <?php foreach ($formats as $f): ?>
                <tr id="row-<?php echo (int)$f['id']; ?>">
                    <td class="cell-id"><?php echo (int)$f['id']; ?></td>
                    <td class="cell-name"><?php echo htmlspecialchars($f['name']); ?></td>
                    <td class="cell-description"><?php echo htmlspecialchars($f['description']); ?></td>
                    <td class="cell-pattern"><code><?php echo htmlspecialchars($f['pattern']); ?></code></td>
                    <td class="cell-example"><span class="text-primary"><?php echo htmlspecialchars($f['example']); ?></span></td>
                    <td class="cell-prefix"><?php echo htmlspecialchars($f['prefix']); ?></td>
                    <td class="cell-suffix"><?php echo htmlspecialchars($f['suffix'] ?? ''); ?></td>
                    <td class="cell-reset"><?php echo ucfirst($f['sequence_reset']); ?></td>
                    <td class="cell-length"><?php echo (int)$f['sequence_length']; ?></td>
                    <td class="cell-length"><?php echo (int)$f['last_sequence']; ?></td>
                    <td class="cell-active">
                        <div class="custom-control custom-switch">
                            <input type="checkbox"
                                   class="custom-control-input activeSwitch"
                                   id="switch_<?php echo (int)$f['id']; ?>"
                                   data-id="<?php echo (int)$f['id']; ?>"
                                <?php echo $f['is_active'] ? 'checked' : ''; ?>>
                            <label class="custom-control-label" for="switch_<?php echo (int)$f['id']; ?>">
                                <?php echo $f['is_active'] ? 'Active' : 'Inactive'; ?>
                            </label>
                        </div>
                    </td>
                    <td>
                        <button type="button" class="btn btn-sm btn-primary editBtn"
                                data-id="<?php echo (int)$f['id']; ?>"
                                data-name="<?php echo htmlspecialchars($f['name'], ENT_QUOTES); ?>"
                                data-description="<?php echo htmlspecialchars($f['description'], ENT_QUOTES); ?>"
                                data-pattern="<?php echo htmlspecialchars($f['pattern'], ENT_QUOTES); ?>"
                                data-example="<?php echo htmlspecialchars($f['example'], ENT_QUOTES); ?>"
                                data-prefix="<?php echo htmlspecialchars($f['prefix'], ENT_QUOTES); ?>"
                                data-suffix="<?php echo htmlspecialchars($f['suffix'] ?? '', ENT_QUOTES); ?>"
                                data-sequence_reset="<?php echo htmlspecialchars($f['sequence_reset'], ENT_QUOTES); ?>"
                                data-sequence_length="<?php echo (int)$f['sequence_length']; ?>"
                                data-last_sequence="<?php echo (int)$f['last_sequence']; ?>"
                                data-is_active="<?php echo (int)$f['is_active']; ?>">
                            Edit
                        </button>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="11" class="text-center">No formats found</td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form id="editForm" method="post" action="<?php echo site_url('other_settings/edit'); ?>">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Format</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="editAlert" class="alert alert-danger d-none"></div>
                    <input type="hidden" name="id" id="edit_id">

                    <div class="form-group">
                        <label>Name</label>
                        <input type="text" name="name" id="edit_name" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description" id="edit_description" class="form-control" rows="2"></textarea>
                    </div>

                    <div class="form-group">
                        <label>Pattern</label>
                        <input type="text" name="pattern" id="edit_pattern" class="form-control" required>
                        <small class="form-text text-muted">
                            Tokens: <code>PREFIX</code>, <code>SEQ</code>, <code>YYYY</code>, <code>MM</code>, <code>DD</code>, <code>DEPT</code>, <code>SUFFIX</code>, <code>FIXED</code>
                        </small>
                    </div>

                    <div class="form-group">
                        <label>Example</label>
                        <input type="text" name="example" id="edit_example" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label>Prefix</label>
                        <input type="text" name="prefix" id="edit_prefix" class="form-control">
                    </div>

                    <div class="form-group">
                        <label>Suffix</label>
                        <input type="text" name="suffix" id="edit_suffix" class="form-control">
                    </div>

                    <div class="form-group">
                        <label>Sequence Reset</label>
                        <select name="sequence_reset" id="edit_sequence_reset" class="form-control">
                            <option value="never">Never</option>
                            <option value="monthly">Monthly</option>
                            <option value="yearly">Yearly</option>
                            <option value="daily">Daily</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Sequence Length</label>
                        <input type="number" min="1" max="10" name="sequence_length" id="edit_sequence_length" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label>Next Sequence</label>
                        <input type="number" name="last_sequence" id="edit_last_sequence" class="form-control" required>

                        <small class="form-text text-muted">If updated, it will reset the sequence counter.</small>
                    </div>

                 </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">
                        <span class="spinner-border spinner-border-sm d-none" id="editSpinner"></span>
                        Save Changes
                    </button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Open modal & populate
    jQuery(".editBtn").on("click", function(){
        jQuery("#editAlert").addClass("d-none").text("");
        jQuery("#edit_id").val(jQuery(this).data("id"));
        jQuery("#edit_name").val(jQuery(this).data("name"));
        jQuery("#edit_description").val(jQuery(this).data("description"));
        jQuery("#edit_pattern").val(jQuery(this).data("pattern"));
        jQuery("#edit_example").val(jQuery(this).data("example"));
        jQuery("#edit_prefix").val(jQuery(this).data("prefix"));
        jQuery("#edit_suffix").val(jQuery(this).data("suffix"));
        jQuery("#edit_sequence_reset").val(jQuery(this).data("sequence_reset"));
        jQuery("#edit_sequence_length").val(jQuery(this).data("sequence_length"));
        jQuery("#edit_last_sequence").val(jQuery(this).data("last_sequence"));
        jQuery("#edit_is_active").val(jQuery(this).data("is_active"));
        jQuery("#editModal").modal("show");
    });

    // AJAX: Edit form submit
    jQuery("#editForm").on("submit", function(e){
        e.preventDefault();
        jQuery("#editSpinner").removeClass("d-none");

        jQuery.ajax({
            url: jQuery(this).attr("action"),
            type: "POST",
            dataType: "json",
            data: jQuery(this).serialize(),
            success: function(res){
                jQuery("#editSpinner").addClass("d-none");
                if (!res || !res.ok) {
                    jQuery("#editAlert").removeClass("d-none").text(res && res.error ? res.error : "Failed to update.");
                    return;
                }
                var r = res.row;

                // update the row cells inline
                var $row = jQuery("#row-" + r.id);
                $row.find(".cell-name").text(r.name);
                $row.find(".cell-description").text(r.description || "");
                $row.find(".cell-pattern").html("<code>"+ (r.pattern || "") +"</code>");
                $row.find(".cell-example").html('<span class="text-primary">'+ (r.example || "") +'</span>');
                $row.find(".cell-prefix").text(r.prefix || "");
                $row.find(".cell-suffix").text(r.suffix || "");
                $row.find(".cell-reset").text(r.sequence_reset ? r.sequence_reset.charAt(0).toUpperCase() + r.sequence_reset.slice(1) : "");
                $row.find(".cell-length").text(r.sequence_length || "");
                $row.find(".cell-length").text(r.last_sequence || "");

                // Active switch & labels
                if (parseInt(r.is_active) === 1) {
                    // uncheck all other switches & labels/text
                    jQuery(".activeSwitch").prop("checked", false).each(function(){
                        jQuery(this).closest("td").find("label.custom-control-label").text("Inactive");
                    });
                    // check this one
                    jQuery("#switch_" + r.id).prop("checked", true)
                        .closest("td").find("label.custom-control-label").text("Active");
                } else {
                    // only update this row's label if inactive
                    jQuery("#switch_" + r.id).prop("checked", false)
                        .closest("td").find("label.custom-control-label").text("Inactive");
                }

                jQuery("#editModal").modal("hide");
            },
            error: function(xhr){
                jQuery("#editSpinner").addClass("d-none");
                jQuery("#editAlert").removeClass("d-none").text("Server error. Please try again.");
            }
        });
    });

    // AJAX: toggle active switch (enforce only one active)
    jQuery(document).on("change", ".activeSwitch", function(){
        var $switch = jQuery(this);
        var id = $switch.data("id");

        // If user is trying to turn OFF the active one, revert (we enforce one active)
        if (!$switch.is(":checked")) {
            // immediately re-check to keep at least one active
            $switch.prop("checked", true);
            return;
        }

        jQuery.ajax({
            url: "<?php echo site_url('other_settings/set_active'); ?>",
            type: "POST",
            dataType: "json",
            data: {
                id: id
            },
            success: function(res){
                if (!res || !res.status) {
                    alert(res && res.error ? res.error : "Could not set active.");
                    // revert UI if failed
                    $switch.prop("checked", false);
                    return;
                }
                // Uncheck all others & set labels
                jQuery(".activeSwitch").not($switch).prop("checked", false)
                    .each(function(){
                        jQuery(this).closest("td").find("label.custom-control-label").text("Inactive");
                    });
                // Set this one to Active label
                $switch.closest("td").find("label.custom-control-label").text("Active");
            },
            error: function(){
                alert("Server error while setting active.");
                $switch.prop("checked", false);
            }
        });
    });

</script>
