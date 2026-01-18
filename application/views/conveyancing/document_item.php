<?php foreach($data as $doc)
{?><div class="document-item">

                        <h6><?php  echo $doc['name']?></h6>
                        <p class="small text-muted">Uploaded: <?php echo  $doc['display_created_on']?> by <?php echo $doc['display_creator_full_name']?></p>
                        <a href="#" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-download"></i> Download
                        </a>
                        <a href="#" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-eye"></i> View
                        </a>
                    </div>
                    <?php } ?>