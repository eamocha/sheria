<div class="card shadow-sm mb-3">
    
    <div class="card-body p-0">
        <div id="risksAccordion">

            <?php if (!empty($risks)) : ?>
                <?php foreach ($risks as $index => $risk) : ?>
                    <div class="card mb-0">
                        <div class="card-header" id="heading<?php echo $index; ?>">
                            <h6 class="mb-0">
                                <button class="btn btn-link d-flex justify-content-between align-items-center w-100" 
                                        data-toggle="collapse" 
                                        data-target="#collapse<?php echo $index; ?>" 
                                        aria-expanded="<?php echo $index === 0 ? 'true' : 'false'; ?>" 
                                        aria-controls="collapse<?php echo $index; ?>">

                                    <span>
                                        <strong><?php echo htmlspecialchars($risk['risk_category']); ?></strong> 
                                        (<?php echo htmlspecialchars($risk['riskLevel']); ?>)
                                    </span>

                                    <span class="badge 
                                        <?php 
                                            switch (strtolower($risk['status'])) {
                                                case 'open': echo 'badge-danger'; break;
                                                case 'in progress': echo 'badge-warning'; break;
                                                case 'resolved': echo 'badge-success'; break;
                                                case 'closed': echo 'badge-secondary'; break;
                                                default: echo 'badge-light'; 
                                            }
                                        ?>">
                                        <?php echo htmlspecialchars($risk['status']); ?>
                                    </span>
                                </button>
                            </h6>
                        </div>

                        <div id="collapse<?php echo $index; ?>" 
                             class="collapse <?php echo $index === 0 ? 'show' : ''; ?>" 
                             aria-labelledby="heading<?php echo $index; ?>" 
                             data-parent="#risksAccordion">
                            <div class="card-body">
                                <p><strong>Risk Type:</strong> <?php echo htmlspecialchars($risk['risk_type']); ?></p>
                                <p><strong>Description:</strong> <?php echo nl2br(htmlspecialchars($risk['details'] ?? '')); ?></p>
                                <p><strong>Mitigation:</strong> <?php echo nl2br(htmlspecialchars($risk['mitigation'] ?? '')); ?></p>
                                <p><strong>Responsible:</strong> <?php echo htmlspecialchars($risk['responsible_name'] ?? 'Unassigned'); ?></p>
                                <p><strong>Created On:</strong> <?php echo htmlspecialchars($risk['createdOn']); ?></p>
                            </div>
                            <div class="m-2 text-right">
                                <a href="javascript:;" onclick="addMatterRisk(<?php echo $risk['id']; ?>)" class="text-primary mr-3">
                                    <i class="fa fa-edit"></i> Update
                                </a>
                                <a href="javascript:;" 
                                   class="text-danger" 
                                   onclick="deleteRelatedRisk(<?php echo $risk['id']; ?>)">
                                    <i class="fa fa-trash"></i> Delete
                                </a>
                          
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else : ?>
                <div class="p-3 text-center text-muted">
                    <i class="fa fa-info-circle"></i> No risks recorded for this case.
                </div>
            <?php endif; ?>

        </div>
    </div>
</div>
