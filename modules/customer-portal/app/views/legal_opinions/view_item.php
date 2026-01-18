<div class="modal fade" id="requestDetailsModal" tabindex="-1" role="dialog" aria-labelledby="requestDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="requestDetailsModalLabel">Request Details - LO-<?php echo $request["OpinionId"]?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-8">
                        <!-- Progress Tracker -->
                        <div class="card mb-4 d-none">
                            <div class="card-header">
                                <h6>Request Progress</h6>
                            </div>
                            <div class="card-body">
                                <div class="progress-tracker">
                                    <div class="progress-line"></div>
                                    <div class="progress-line-fill" style="width: 60%;"></div>
                                    <div class="progress-step completed">1</div>

                                    <div class="progress-step active" style="left: 50%;">3</div>

                                    <div class="progress-step" style="left: 100%;">5</div>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <small>Received</small>

                                    <small>In Progress</small>

                                    <small>Completed</small>
                                </div>
                            </div>
                        </div>
                        <!-- Request Details -->
                        <div class="card mb-4">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h6>Request Information</h6>
                                <button class="btn btn-sm btn-primary" id="editRequestBtn">
                                    <i class="fas fa-eye"></i> 
                                </button>
                            </div>
                            <div class="card-body">
                                <h5 id="detailSubject"><?php echo $request["title"]?></h5>
                                <p><strong>Reference No.:</strong> LO<?php echo $request["OpinionId"]?></p>
                                <p><strong>Request Date:</strong> <?php echo date("F j, Y",strtotime($request["createdOn"]))?></p>
                                <p><strong>Due Date:</strong> <?php echo date("F j, Y",strtotime($request["due_date"]))?></p>
                                
                                <p><strong>Status:</strong> <span class="badge badge-info"><?php echo $request["status"]?></span></p>

                                <div class="mt-4">
                                    <h6>Background</h6>
                                    <p id="detailBackground"><?php echo $request["background_info"]?></p>

                                    <h6 class="mt-3">Detailed Information</h6>
                                    <p id="detailDetails"><?php echo $request["detailed_info"]?></p>

                                    <h6 class="mt-3">Legal Question</h6>
                                    <p id="detailQuestion"><?php echo $request["legal_question"]?></p>
                                </div>
                            </div>
                        </div>
                                             
                        <!-- Comments Section -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6>Comments & Notes</h6>
                            </div>
                            <div class="card-body">
                                <?php $comments = $comments['records'] ?? [];?>
                                
                                <h4 class="comments-header">Comments (<?= $comments[0]['total_rows'] ?? 0 ?>)</h4>
                                <?php if (!empty( $comments[0]['total_rows'])){ ?>
                                    <?php foreach ($comments as $comment) { ?>
                                <div class="comment-box p-3 mb-3">
                                    <div class="d-flex justify-content-between">
                                        <strong><?php echo  $comment['created_by_name']?></strong>
                                        <small class="text-muted"><?php echo  date('M j, Y \a\t g:i A', strtotime($comment['createdOn']));?></small>
                                    </div>
                                    <p class="mt-2"><?php echo $comment['comment']?></p>
                                </div>
                                <?php }
                                } ?>
                                <div id="commentsSection">
                                    <!-- Additional comments will be dynamically added here -->
                                </div>
                                <form class="mt-4">
                                    <div class="form-group">
                                        <label for="newComment">Add Comment</label>
                                        <textarea class="form-control" id="newComment" rows="3"></textarea>
                                    </div>
                                    <a href="javascript:void(0);" class="btn btn-primary" onclick="addCommentFromCP(<?php echo $request['id']?>)" >Post Comment</a>
                                </form>
                            </div>
                        </div>
                    </div>
                 

              <div class="col-md-4">
                        <!-- Assignment & Workflow -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6>Assignment & Workflow</h6>
                            </div>
                            <div class="card-body">
                                <p><strong>Request Source:</strong> <?php echo $request['channel']?></p>
                                <p><strong>Requested By:</strong> <?php echo $request['requestedBy']??""?></p>
                                <p><strong>Created By:</strong><?php echo $request['createdBy']?></p>
                                <p><strong>Current Assignee:</strong> <?php echo $request['assignee_fullname']?> (LO)</p>
                               

                                <hr>

                                <div class="form-group d-none">
                                    <label for="assignTo">Assign/Reassign To</label>
                                    <select class="form-control" id="assignTo">
                                        <option value="">Select...</option>
                                        <option value="add">Assistant Deputy Director</option>
                                        <option value="plo">Legal Officer</option>
                                        <option value="slo">Senior Legal Officer</option>
                                        <option value="lo" selected>Other</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="statusUpdate">Update Status</label>
                                    <select class="form-control" id="statusUpdate">
                                        <option value="new">New</option>
                                        <option value="assigned">Received</option>
                                        <option value="in-progress">Downloaded</option>
                                        <option value="review" selected>Acknowledges</option>
                                        <option value="completed">Completed</option>
                                    </select>
                                </div>

                                <button class="btn btn-primary btn-block mt-3">Update Assignment</button>
                            </div>
                        </div>

                        <!-- Attachments -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6>Attachments</h6>
                            </div>
                            <div class="card-body">
                                <div class="attachment-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <i class="fas fa-file-pdf mr-2 text-danger"></i>
                                        <span>Signed advisory Opinion.pdf</span>
                                    </div>
                                    <div>
                                        <button class="btn btn-sm btn-outline-primary" onclick="download.php?file=signed_advisory_opinion.pdf">
                                            <i class="fas fa-download"></i>
                                        </button>
                                    </div>
                                </div>

                               <!-- <div class="attachment-item d-flex justify-content-between align-items-center">-->
<!--                                    <div>-->
<!--                                        <i class="fas fa-file-word mr-2 text-primary"></i>-->
<!--                                        <span>Project_Scope.docx</span>-->
<!--                                    </div>-->
<!--                                    <div>-->
<!--                                        <button class="btn btn-sm btn-outline-primary">-->
<!--                                            <i class="fas fa-download"></i>-->
<!--                                        </button>-->
<!--                                    </div>-->
<!--                                </div> -->

                                <div class="mt-3">
                                    <button class="btn btn-sm btn-outline-secondary btn-block" data-toggle="collapse" data-target="#uploadAttachment">
                                        <i class="fas fa-plus"></i> Add Attachment
                                    </button>
                                    <div class="collapse mt-2" id="uploadAttachment">
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input" id="additionalAttachments">
                                            <label class="custom-file-label" for="additionalAttachments">Choose file</label>
                                        </div>
                                        <button class="btn btn-sm btn-primary mt-2">Upload</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Digital Signature -->
<!--                        <div class="card">-->
<!--                            <div class="card-header">-->
<!--                                <h6>Legal Advisory Signing</h6>-->
<!--                            </div>-->
<!--                            <div class="card-body">-->
<!--                                <p>Once the legal opinion is finalized, it can be signed digitally here.</p>-->
<!---->
<!--                                <div class="form-group">-->
<!--                                    <label for="signatureType">Signature Type</label>-->
<!--                                    <select class="form-control" id="signatureType">-->
<!--                                        <option value="">Select...</option>-->
<!--                                        <option value="electronic">Electronic Signature</option>-->
<!--                                        <option value="digital">Digital Certificate</option>-->
<!--                                    </select>-->
<!--                                </div>-->
<!---->
<!--                                <button class="btn btn-success btn-block" disabled id="signDocumentBtn">-->
<!--                                    <i class="fas fa-signature"></i> Sign Document-->
<!--                                </button>-->
<!---->
<!--                                <small class="text-muted d-block mt-2">Note: Document must be in 'Completed' status before signing.</small>-->
<!--                            </div>-->
<!--                        </div>-->
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>