<div class="modal fade modal-container modal-resizable">
 <div class="modal-dialog modal-lg">
         <div class="modal-content">
             <div class="modal-header d-flex flex-wrap">
                 <div class="row col-md-12">
                     <h4 class="modal-title"><?php  echo htmlspecialchars($title);?></h4>
                     <button type="button" class="close pt-0" data-dismiss="modal">×</button>
                 </div>
                 <div class="row col-md-12 no-margin p-0 d-none" id="progress-bar">
                     <div class="col-md-10 no-margin pl-0 mt-1">
                         <div class="progress">
                             <div class="progress-bar" role="progressbar" progress="">
                             </div>
                         </div>
                     </div>
                     <div class="col-md-2">
                         <span><?php echo $this->lang->line("page");?></span>
                         <span id="current-page">1</span>
                         <span><?php echo $this->lang->line("of");?></span>
                         <span id="pages-count"></span>
                     </div>
                 </div>
             </div>
             <div class="modal-body">
                 <div id="contract-generate-form" class="col-md-12 m-0 p-0">
                     <fieldset id="fieldset1"><?php
                         echo form_open("", "novalidate id='form1'");
                         echo form_input(["name" => "option", "id" => "option", "value" => $option, "type" => "hidden"]);
                         if ($option == "choose") {
                             if ($commercial_service_category=='contract'){
                                 $this->load->view("contracts/generate/template_lists");
                             }else {
                                 $this->load->view("contracts/generate/template_lists_mou");
                             }
                         } else {
                             $this->load->view("contracts/generate/upload_contract");
                         }
                         echo form_close();?>
                     </fieldset>
                     <fieldset id="fieldset2"><?php
                         echo form_open("", "novalidate id='form2'");
                         if ($option == "add") {
                             $this->load->view("contracts/generate/add");
                         }
                         echo form_close();                         ?>
                     </fieldset>
                     <fieldset id="fieldset3"><?php
                         echo form_open("", "novalidate id='form3'");
                         echo form_close(); ?>
                     </fieldset>
                 </div>
             </div><!-- /.modal-body -->
             <div class="modal-footer justify-content-between" data-field="1">
                 <div><span class="loader-submit"></span>
                     <button type="button" class="save-button btn-info previous d-none"> <img src="assets/images/contract/next.svg" width="14" height="14"> <?php echo $this->lang->line("previous");?></button>
                     <button type="button"  class="save-button btn-info next margin-left-btn-save"><?php echo $this->lang->line("next");?><img src="assets/images/contract/next.svg" width="14" height="14"></button>
                     <button type="button"  class="save-button btn-info next-page d-none"><?php echo $this->lang->line("next");?><img src="assets/images/contract/next.svg" width="14" height="14"></button>
                     <button type="button" class="save-button btn-info d-none  margin-left-btn-save " id="form-submit"><?php echo $this->lang->line("submit");?></button>
                     <?php if ($show_notification) {?>
                         <span class="label-notification-checkbox pt-10 d-inline-block v-al-n-5 d-none" id="notification-div"><?php    $this->load->view("notifications/wrapper", ["hide_show_notification" => $hide_show_notification, "container" => "'#contract-generate-container'", "hide_label" => false]); ?></span>
                     <?php }?>
                 </div>
                 <button type="button" class="close_model no_bg_button float-right text-right"    data-dismiss="modal"><?php echo $this->lang->line("cancel");?></button>
             </div>
         </div><!-- /.modal-content -->
 </div><!-- /.modal-dialog -->
</div><!-- /.modal -->