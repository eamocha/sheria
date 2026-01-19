<div class="table-responsive">
    <table class="table table-striped table-hover custom-table">
        <thead>
            <tr>
                <th style="width: 15%"><?php echo $this->lang->line("module"); ?></th>
                <th style="width: 45%"><?php echo $this->lang->line("record_details"); ?></th>
                <th style="width: 20%"><?php echo $this->lang->line("link_type"); ?></th>
                <th style="width: 20%" class="text-right"><?php echo $this->lang->line("actions"); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($relationships)): ?>
                <?php foreach ($relationships as $rel): ?>
                    <tr>
                        <td>
                            <?php 
                                // Dynamic icons based on module type
                                $icon = 'fa-file-text-o';
                                $label_class = 'label-default';
                                
                                switch(strtolower($rel['module'])) {
                                    case 'correspondence': $icon = 'fa-envelope-o'; $label_class = 'label-info'; break;
                                    case 'cases':          $icon = 'fa-gavel'; $label_class = 'label-danger'; break;
                                    case 'contracts':      $icon = 'fa-briefcase'; $label_class = 'label-success'; break;
                                    case 'legal_opinions': $icon = 'fa-balance-scale'; $label_class = 'label-warning'; break;
                                }
                            ?>
                            <span class="label <?php echo $label_class; ?> padding-5">
                                <i class="fa <?php echo $icon; ?>"></i> <?php echo $rel['module']; ?>
                            </span>
                        </td>
                        <td>
                            <a href="<?php echo $rel['link_url']; ?>" class="text-bold text-primary">
                                <?php echo $rel['display_text']; ?>
                            </a>
                            <?php if(!empty($rel['comments'])): ?>
                                <div class="small text-muted m-t-5">
                                    <i class="fa fa-comment-o"></i> <?php echo $rel['comments']; ?>
                                </div>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="text-capitalize italic"><?php echo str_replace('_', ' ', $rel['relationship_type']); ?></span>
                            <div class="small text-silver"><?php echo date('d M Y', strtotime($rel['createdOn'])); ?></div>
                        </td>
                        <td class="text-right">
                            <div class="btn-group">
                                <a href="<?php echo $rel['link_url']; ?>" class="btn btn-default btn-xs" title="View Record">
                                    <i class="fa fa-external-link"></i>
                                </a>
                                <button type="button" class="btn btn-danger btn-xs" 
                                        onclick="unlinkRecord(<?php echo $rel['rel_id']; ?>)" title="Remove Link">
                                    <i class="fa fa-unlink"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4" class="text-center padding-20">
                        <i class="fa fa-info-circle fa-2x text-silver"></i>
                        <p class="m-t-10 text-muted">No related records found for this correspondence.</p>
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>