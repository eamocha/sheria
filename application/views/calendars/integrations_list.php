<div class="modal fade modal-container modal-resizable">
    <!-- /.modal -->
    <div class="modal-dialog">
        <!-- /.modal-dialog -->
        <div id="calendar-integration-dialog" class="modal-content">
            <!-- /.modal-content -->
            <div class="modal-header px-4">
                <!-- /.modal-header -->
                <h4 class="modal-title"><?php echo $this->lang->line("calendar_integration");?></h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <!-- /.modal-header -->
            <div class="modal-body">
                <!-- /.modal-body -->
               <?php $this->load->view("calendars/integrations");?>
            </div>
            <!-- /.modal-body -->
            <div class="modal-footer-empty">
            </div>
            <!-- /.modal-footer -->
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->

