<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="amendment-history">
    <?php if (!empty($amendment_history)): ?>
        <div class="table-responsive">
      
            <table class="table table-borderless">
                <thead>
                <tr>
                    <th scope="col"><?php echo $this->lang->line('by'); ?></th>
                    <th scope="col"><?php echo $this->lang->line('on'); ?></th>
                    <th scope="col"><?php echo $this->lang->line('previous_end_date'); ?></th>
                    <th scope="col"><?php echo $this->lang->line('amended_contract'); ?></th>
                    <th scope="col"><?php echo $this->lang->line('new_contract_date'); ?></th>
                    <th scope="col"><?php echo $this->lang->line('new_end_date'); ?></th>
                    <th scope="col"><?php echo $this->lang->line('comment'); ?></th>
                    <th scope="col"><?php echo $this->lang->line('status'); ?></th>
                   

                </tr>
                </thead>
                <tbody>
                <?php foreach ($amendment_history as $history): ?>
                    <tr onclick="viewAmendment(<?php echo htmlspecialchars($history['id']); ?>)">       

                        <td><?php echo htmlspecialchars($history['amended_by']); ?></td>
                        <td><?php echo htmlspecialchars(date("y-m-d",strtotime($history['amended_on']))); ?></td>
                        <td><?php echo htmlspecialchars($history['previous_end_date']); ?></td>
                        <td>
                            <a href="<?php echo base_url('modules/contract/contracts/view/' . $history['amended_id']); ?>">
                                <?php echo htmlspecialchars($model_code . $history['amended_id']); ?>
                            </a>
                        </td>
                        <td><?php echo htmlspecialchars($history['new_contract_date']); ?></td>
                        <td><?php echo htmlspecialchars($history['new_end_date']); ?></td>
                        <td><?php echo htmlspecialchars($history['comment']); ?></td>
                        <td><?php echo htmlspecialchars($history['amendment_approval_status']); ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <p id="no-history"><?php echo $this->lang->line('no_history'); ?></p>
    <?php endif; ?>
</div>
<script>
    function viewAmendment(amendmentId) {
        //send ajax request to fetch amendment details and show them on modal
       jQuery.ajax({
            url: getBaseURL("contract") +'contracts/get_amendment_history_details/'+ amendmentId,
            type: 'GET',
            //before send load the global loader  jQuery("#loader-global").show(); and after, hide it jQuery("#loader-global").hide();
            beforeSend: function () {
                jQuery("#loader-global").show();
            },
            complete: function () {
                jQuery("#loader-global").hide();
            },
           
            success: function (response) {
               //load the response into the #amendmentDetailsModal modal
                if (response.result) {
                    // If response.data contains the whole modal, replace the modal in DOM
                    if (response.html) {
                        // Remove any existing modal with the same ID
                        jQuery("#amendmentDetailsModal").remove();
                        // Append the new modal HTML to the body
                        jQuery("body").append(response.html);
                        // Show the modal
                        jQuery("#amendmentDetailsModal").modal("show");
                    } else if (response.data) {
                        // If only modal body content is returned
                        jQuery("#amendmentDetailsModal .modal-body").html(response.data);
                        jQuery("#amendmentDetailsModal").modal("show");
                    }
                } else {
                    defaultAjaxJSONErrorsHandler(response);
                }
            },
            error: function () {
                 error: defaultAjaxJSONErrorsHandler
            }
        });
    }
</script>
