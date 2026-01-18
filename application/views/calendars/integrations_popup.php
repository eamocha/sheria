<div class="modal fade modal-container modal-resizable">
    <div class="modal-dialog">
        <div id="calendar-integration-dialog" class="modal-content">
            <div class="modal-header px-4">
                <h4 class="modal-title"><?php echo $this->lang->line("calendar_integration"); ?></h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <?php $this->load->view("calendars/integrations"); ?>
            </div>
            <div class="modal-footer-empty">
            </div>
        </div>
    </div>
</div>

