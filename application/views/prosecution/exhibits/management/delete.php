<!-- Delete Exhibit Modal -->
<div class="modal fade" tabindex="-1" >
    <div class="modal-dialog">
        <div class="modal-content">
            <?php echo form_open('', ['id' => 'delete']); ?>
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteExhibitModalLabel">Confirm Deletion</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this exhibit? This action cannot be undone.</p>
                    <input type="hidden" name="id" id="deleteId" value="<?php echo $id?>">
                    <?php echo form_error('exhibit_id', '<div class="alert alert-danger">', '</div>'); ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary rounded" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger rounded">Delete</button>
                </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>