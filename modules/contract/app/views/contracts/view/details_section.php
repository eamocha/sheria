<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div id="view-container">
    <h4><?php echo $this->lang->line('contract_details'); ?></h4>
    <ul class="clearfix">
        <li><b><?php echo $this->lang->line('type'); ?>: </b><span><?php echo htmlspecialchars($contract['type']); ?></span></li>
        <li><b><?php echo $this->lang->line('workflow_status'); ?>: </b>
            <span class="colored-status white-text" style="background-color: <?php echo htmlspecialchars($contract['status_color'] ?? ''); ?>"><?php echo htmlspecialchars($contract['status_name']); ?></span> 
            <?php if ($contract['workflow_id'] && $contract['workflow_name']) { ?>
                <span>(<a href="modules/contract/contract_workflows/index#<?php echo htmlspecialchars($contract['workflow_id']); ?>"><?php echo htmlspecialchars($contract['workflow_name']); ?></a>)</span>
            <?php } ?>
        </li>
       <!-- <li><b><?php echo $this->lang->line('sub_type'); ?>: </b><span><?php echo htmlspecialchars($contract['sub_type']); ?></span></li>-->
        <li><b><?php echo $this->lang->line('priority'); ?>: </b><span class="priority-<?php echo htmlspecialchars($contract['priority']); ?>"></span> <span><?php echo $this->lang->line($contract['priority']); ?></span></li>
        <li><b><?php echo strtolower($contract['category']) == 'mou' ? $this->lang->line('mou_value') : $this->lang->line('contract_value'); ?>: </b>
            <span><?php echo $contract['value'] ? number_format($contract['value'], 2, '.', ',') . ($contract['currency'] ? ' (' . htmlspecialchars($contract['currency']) . ')' : '') : $this->lang->line('none'); ?></span>
        </li>
        <li><b><?php echo $this->lang->line('country'); ?>: </b><span><?php echo htmlspecialchars($contract['country']); ?></span></li>
        <li><b><?php echo $this->lang->line('applicable_law'); ?>: </b><span><?php echo htmlspecialchars($contract['applicable_law']); ?></span></li>
        <li><b><?php echo $this->lang->line('department'); ?>: </b><span><?php echo htmlspecialchars($contract['department_name']); ?></span></li>
        <li><b><?php echo $this->lang->line('advance_payment_guarantee'); ?>: </b><span><?php echo htmlspecialchars($contract['advance_payment_guarantee'] ?? $this->lang->line('none')); ?></span></li>
        <li class="full-width"><b><?php echo $this->lang->line('letter_of_credit_details'); ?>: </b><span><?php echo nl2br(htmlspecialchars($contract['letter_of_credit_details'] ?? $this->lang->line('none'))); ?></span></li>
        <li class="full-width"><b><?php echo $this->lang->line('amendment_of'); ?>: </b>
            <span><?php echo $contract['amendment_of'] ? '<a href="modules/contract/contracts/view/' . htmlspecialchars($contract['amendment_of']) . '">' . htmlspecialchars($model_code . $contract['amendment_of']) . ': ' . htmlspecialchars($contract['amendment_of_name']) . '</a>' : $this->lang->line('none'); ?></span>
        </li>
        <?php if (!empty($parties)) {
            $count = 1;
            foreach ($parties as $data) {
                $link = $data['party_member_type'] === 'company' ? 'companies/tab_company/' : 'contacts/edit/';
                ?>
                <li><b><?php echo $this->lang->line('party') . ' (' . $count . ')'; ?>: </b><span><a href="<?php echo app_url($link . htmlspecialchars($data['party_member_id'])); ?>"><?php echo htmlspecialchars($data['party_name']); ?></a></span></li>
                <li><b><?php echo $this->lang->line('party_category') . ' (' . $count . ')'; ?>: </b><span><?php echo htmlspecialchars($data['party_category_name'] ?? $this->lang->line('none')); ?></span></li>
                <?php $count++;
            }
        } else { ?>
            <li><b><?php echo $this->lang->line('party'); ?>: </b><span><?php echo $this->lang->line('none'); ?></span></li>
            <li><b><?php echo $this->lang->line('party_category'); ?>: </b><span><?php echo $this->lang->line('none'); ?></span></li>
        <?php } ?>
        <?php if (!empty($custom_fields['main'])) { ?>
            <div id="custom-fields" class="custom-field-container"></div>
            <div id="collapsable-custom-fields" class="custom-field-container" style="display:none;"></div>
        <?php } ?>
    </ul>
    <div id="collapsable-button" style="display:block;"></div>
    <hr>
    <div class="box-shadow-container-contract p-0">
        <div onclick="collapse('comment-section-heading', 'comment-section-content', false, 'fast', 'fa-solid fa-angle-down', 'fa-solid fa-angle-right', true)">
            <h4 role="button" class="box-title left-border p-3 m-0" id="comment-section-heading"><i class="fa-solid fa-pen mr-2 font-18 purple_color"></i><?php echo $this->lang->line('description'); ?>
                <a href="javascript:;" class="float-right"><i class="fa fa-angle-right icon font-18 purple_color"></i></a>
            </h4>
        </div>
        <div class="d-none px-3 pb-3" id="comment-section-content">
            <hr class="hr-separator p-0 m-0">
            <p class="mt-4"><?php echo nl2br(htmlspecialchars($contract['description'] ?? $this->lang->line('none'))); ?></p>
        </div>
    </div>
</div>
<script>
    <?php if (isset($custom_fields['main'])) {
        $count_array = count($custom_fields['main']);
        if ($count_array > 6) { ?>
            jQuery('#collapsable-button').append('<a href="javascript:;" id="expand-collapse-link" class="collapsible margin-bottom" onclick="expandCollapseCustomFields();" title="Show More"><img id="expand-collapse-icons" src="assets/images/contract/expand.svg" class="btn filter-color" width="40px"/><span id="expand-collapse-text"><?php echo $this->lang->line('expand_all_fields'); ?></span></a>');
        <?php }
        $count = 1;
        $count_inputs = 1;
        foreach ($custom_fields['main'] as $i => $field) {
            if ($field['type'] !== 'long_text') {
                if ($count < 8) { ?>
                    jQuery('#custom-fields').append('<li><b><?php echo htmlspecialchars($field['customName']); ?>: </b><?php echo htmlspecialchars($field['text_value'] ?? $this->lang->line('none')); ?></li>');
                <?php } else { ?>
                    jQuery('#collapsable-custom-fields').append('<li><b><?php echo htmlspecialchars($field['customName']); ?>: </b><?php echo htmlspecialchars($field['text_value'] ?? $this->lang->line('none')); ?></li>');
                <?php }
                unset($custom_fields['main'][$i]);
            }
            $count++;
        }
        if (!empty($custom_fields['main'])) {
            foreach ($custom_fields['main'] as $field) { ?>
                var textValue = <?php echo $field['text_value'] == '' ? "'" . $this->lang->line('none') . "'" : str_replace('rn', '<br/>', json_encode($field['text_value'])); ?>;
                <?php if ($count < 7) { ?>
                    jQuery('#custom-fields').append('<li class="col-md-12"><b><?php echo htmlspecialchars($field['customName']); ?>: </b>' + textValue + '</li>');
                <?php } else { ?>
                    jQuery('#collapsable-custom-fields').append('<li class="col-md-12"><b><?php echo htmlspecialchars($field['customName']); ?>: </b>' + textValue + '</li>');
                <?php }
                $count_inputs++;
            }
        }
    } ?>
    function expandCollapseCustomFields() {
        if (document.getElementById('expand-collapse-link').title === 'Show More') {
            document.getElementById('expand-collapse-icons').src = 'assets/images/contract/collapse.svg';
            document.getElementById('expand-collapse-link').title = 'Show Less';
            document.getElementById('collapsable-custom-fields').style.display = 'block';
            document.getElementById('expand-collapse-text').textContent = '<?php echo $this->lang->line('collapse'); ?>';
        } else {
            document.getElementById('expand-collapse-icons').src = 'assets/images/contract/expand.svg';
            document.getElementById('expand-collapse-link').title = 'Show More';
            document.getElementById('collapsable-custom-fields').style.display = 'none';
            document.getElementById('expand-collapse-text').textContent = '<?php echo $this->lang->line('expand_all_fields'); ?>';
        }
    }
</script>