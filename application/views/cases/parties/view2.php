
  <div class=" col-md-12 no-padding row no-margin">
               
        <div class="card-body p-0">
            <?php
            // --- Data Preparation and Dynamic Grouping ---
            
            $dynamic_groups = [];
            
            if (!empty($parties)) {
                foreach ($parties as $party) {
                    // CRITICAL CHECK: Only include parties with a valid ID
                    if (isset($party['opponent_id']) && $party['opponent_id'] > 0) {
                        
                        // Use opponent_position ID for grouping. Default to 0 or 'unspecified' if missing.
                        $group_key = $party['opponent_position'] ?? 0; 
                        
                        // Initialize the group array if it doesn't exist
                        if (!isset($dynamic_groups[$group_key])) {
                            // Store the position name as well for the title later
                            $dynamic_groups[$group_key] = [
                                'name' => $party['position_name'] ?? 'Unspecified Position',
                                'parties' => []
                            ];
                        }
                        
                        $dynamic_groups[$group_key]['parties'][] = $party;
                    }
                }
            }
            
            // Re-sort the groups, perhaps by the position name or the ID, 
            // to ensure a consistent display order. Sorting by key (position ID) is common.
            ksort($dynamic_groups);
            
            function get_ordinal_number($n) {
                $s = ["th", "st", "nd", "rd"];
                $v = $n % 100;
                return $n . ($s[($v - 20) % 10] ?? $s[$v] ?? $s[0]);
            }

            // Keep track of the group index for dynamic titles
            $group_index = 0;
            ?>

            <?php foreach ($dynamic_groups as $group_key => $group_data): 
                $group_index++;
                $group_parties = $group_data['parties'];
                $party_count = count($group_parties);
                $is_first_group = ($group_index === 1);
                
                // Define titles dynamically
                $title = $is_first_group ? 'Between' : 'AND';
                $section_class = $is_first_group ? "":"";//'text-success' : 'text-danger';
                
                // Human-readable title from the data
                $position_name = $group_data['name'];
            ?>
                <div class="party-side-section p-3 <?php echo ($group_index !== 1 ? 'border-top' : ''); ?>">
                    
                    <div class="mb-2 pb-1 border-bottom">
                        
                        <h5 class="mb-0 text-center <?php echo $section_class; ?>">
                            <strong><?php echo $title; ?> (<?php echo htmlspecialchars($position_name); ?>)</strong>
                            <span class="badge bg-secondary rounded-pill ms-2"><?php echo $party_count; ?></span>
                        </h5>
                    </div>
                    
                    <ul class="list-group list-group-flush party-list" id="group-<?php echo $group_key; ?>-list">
                        <?php foreach ($group_parties as $index => $party): ?>
                            <?php
                            // Party details
                            $is_company = ($party['opponent_member_type'] === 'company' || $party['opponent_member_type'] === 'firm');
                            $type_label = $is_company ? '<i class="fa fa-building purple_color"></i>' : '<i class="fa fa-user purple_color"></i>';
                            $link_url = $is_company
                                ? site_url("companies/tab_company/{$party['opponent_member_id']}")
                                : site_url("contacts/edit/{$party['opponent_member_id']}");
                            
                            // The position label now represents the ordinal number within the group
                            $position_label = strtoupper($party['position_name'] ?? 'Party');
                            
                            // Apply ordinal numbers based on index within the group
                            $ordinal = get_ordinal_number($index + 1);
                            $display_role = $ordinal . ' ' . $position_label;
                            ?>
                            
                            <li class="list-group-item list-group-item-no-border d-flex justify-content-between align-items-center py-1" 
                                data-id="<?php echo $party['opponent_id'] ?? 0; ?>">
                                <div class="flex-grow-1 me-3">
                                    <div class="legal-line">
                                        <span class="party-name text-nowrap fw-bold me-2">
                                            <?php echo $type_label; ?>
                                            <?php echo htmlspecialchars($party['opponentName'] ?? 'N/A'); ?>
                                            <?php if (!empty($party['opponentForeignName'])): ?>
                                                <br><small class="text-muted fst-italic"><?php echo htmlspecialchars($party['opponentForeignName']); ?></small>
                                            <?php endif; ?>
                                            <a href="<?php echo $link_url; ?>" target="_blank" class="text-decoration-none ms-1 text-muted">
                                                <i class="fa fa-external-link"></i>
                                            </a>
                                        </span>
                                        <span class="dots flex-grow-1 mx-2"></span>
                                        <span class="party-role text-nowrap fst-italic text-muted">
                                            <?php echo $display_role; ?>
                                        </span>
                                    </div>
                                </div>
                                <div class="party-actions d-flex flex-shrink-0 align-items-center">
                                    <a href="javascript:;" class="edit-btn me-2" 
                                        onclick="open_party_form(
                                            <?php echo $case_id ?? 0; ?>,
                                            <?php echo $party['opponent_id'] ?? 0; ?>
                                        )">
                                        <i class="icon-alignment fa fa-pencil purple_color for-editcursor-pointer-click"></i>
                                    </a>
                                    <a href="javascript:;" class="delete-btn" 
                                        onclick="partyDelete(
                                            <?php echo $party['opponent_id'] ?? 0; ?>,
                                            '<?php echo htmlspecialchars($party['opponentName'] ?? ''); ?>',
                                            event
                                        )">
                                        <i class="icon-alignment fa fa-trash light_red-color"></i>
                                    </a>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endforeach; ?>
            
            <?php if (empty($dynamic_groups)): ?>
                <div class="card-body text-center text-muted">
                    No parties have been added to this case yet.
                </div>
            <?php endif; ?>
        </div>
        
      
    </div>
 

<style>
 
.legal-line {
    display: flex;
    align-items: baseline;
    width: 100%; /* Ensure it spans available width */
}

.dots {
    border-bottom: 1px dotted #6c757d;
    height: 0.8em;
    position: relative;
    top: -2px;
}

.list-group-item-no-border {
    border: none !important;
    padding-top: 0.25rem !important;
    padding-bottom: 0.25rem !important;
}

.party-side-section {
    padding: 1rem; 
}

.party-side-section + .party-side-section {
    /* Add a subtle line between sections (except before the first one) */
    border-top: 1px solid #dee2e6 !important; 
}

.icon-alignment {
    vertical-align: middle;
}

.party-actions a {
    text-decoration: none;
}
</style>
<script>
function partyDelete(opponent_id, party_name, event) {
    event.preventDefault();
    
    if (confirm('Are you sure you want to remove ' + party_name + ' from this case?')) {
        // AJAX call to remove party
        $.ajax({
            url: '<?php echo site_url("cases/remove_party"); ?>',
            type: 'POST',
            data: { 
                case_id: '<?php echo $case_id ?? ""; ?>',
                opponent_id: opponent_id 
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    showAlert('success', response.message);
                    // Reload the page or specific section
                    location.reload();
                } else {
                    showAlert('error', response.message);
                }
            },
            error: function() {
                showAlert('error', 'An error occurred while removing the party.');
            }
        });
    }
}

function showAlert(type, message) {
    var alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
    var alertHtml = '<div class="alert ' + alertClass + ' alert-dismissible fade show" role="alert">' +
                    message +
                    '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
                    '<span aria-hidden="true">&times;</span></button></div>';
    
    $('.container.my-4').prepend(alertHtml);
    
    setTimeout(function() {
        $('.alert').alert('close');
    }, 5000);
}

// Helper function to get ordinal numbers
function get_ordinal_number(n) {
    var s = ["th", "st", "nd", "rd"],
        v = n % 100;
    return n + (s[(v - 20) % 10] || s[v] || s[0]);
}
</script>