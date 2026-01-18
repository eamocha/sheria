
    <div class="col-md-12 no-padding row no-margin card">
        
        <div class="card-body p-0">
            <?php
        
            $grouped_parties = [
                '1' => [], // Claimant/Plaintiff
                '2' => [], // Defence Parties
                '3' => []  // Other Parties
            ];
            
            if (!empty($parties)) {
                foreach ($parties as $party) {
                    // CRITICAL CHECK: Only include parties with a valid ID
                    if (isset($party['opponent_id']) && $party['opponent_id'] > 0) {
                        $side = $party['where_displayed'] ?? '1';
                        if (isset($grouped_parties[$side])) {
                            $grouped_parties[$side][] = $party;
                        } else {
                            $grouped_parties['3'][] = $party;
                        }
                    }
                }
            }
            
            // Define section titles and styling  
            $sections = [
                '1' => [
                    'title' => 'Between', 
                    'header_icon' => '',//fa-gavel',
                    'class' => ' ',
                    'side_name' => 'Claimant/Plaintiff Party'
                ],
                '2' => [
                    'title' => '...AND...', 
                    'header_icon' => 'fa-shield-halved',
                    'class' => ' ',
                    'side_name' => 'Defence Parties'
                ],
                '3' => [
                    'title' => '...AND...',
                    'header_icon' => '',//fa-users',
                    'class' => '',
                    'side_name' => 'Other Parties'
                ]
            ];
            
            function get_ordinal_number($n) {
                $s = ["th", "st", "nd", "rd"];
                $v = $n % 100;
                return $n . ($s[($v - 20) % 10] ?? $s[$v] ?? $s[0]);
            }
            ?>

            <?php foreach ($sections as $side => $section): 
                $party_count = count($grouped_parties[$side]);
                $is_empty = empty($grouped_parties[$side]);
            ?>
                <div class="party-side-section p-3 <?php echo ($side !== '1' ? 'border-top' : ''); ?>">
                    
                    <div class="d-flex justify-content-between align-items-center mb-2 pb-1 border-bottom">
                        
                        <h5 class="mb-0 flex-grow-1 text-center <?php echo $section['class']; ?>">
                            <i class="fa <?php echo $section['header_icon']; ?> me-2"></i>
                            <strong><?php echo $section['title']; ?></strong>
                            
                        </h5>
                        
                        <a class="btn btn-sm btn-primary flex-shrink-0" onclick="open_party_form(<?php echo $case_id ?? 0; ?>,'<?php echo $side; ?>')" title="Add New Party">
                            <i class="fas fa-plus-square"></i> Add
                        </a>
                    </div>
                    
                    <ul class="list-group list-group-flush party-list" id="side-<?php echo $side; ?>-list">
                        <?php if (!$is_empty): ?>
                            <?php foreach ($grouped_parties[$side] as $index => $party): ?>
                                <?php
                                // Party details
                                $is_company = ($party['opponent_member_type'] === 'company');
                                $type_label = $is_company ? '<i class="fa fa-building purple_color"></i>' : '<i class="fa fa-user purple_color"></i>';
                                $link_url = $is_company
                                    ? site_url("companies/tab_company/{$party['opponent_member_id']}")
                                    : site_url("contacts/edit/{$party['opponent_member_id']}");
                                
                                $position_label = strtoupper($party['position_name'] ?? 'Party');
                                if (in_array($side, ['1', '2']) && ($index > 0 || $party_count > 1)) {
                                    $ordinal = get_ordinal_number($index + 1);
                                    $position_label = $ordinal . ' ' . $position_label;
                                }
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
                                                <?php echo $position_label; ?>
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
                        <?php else: ?>
                            <li class="list-group-item text-center text-muted py-2 border-0">
                                No <strong><?php echo $section['side_name']; ?></strong> added yet.
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
            <?php endforeach; ?>
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