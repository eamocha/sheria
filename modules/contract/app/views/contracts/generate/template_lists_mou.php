<?php
echo form_input(["id" => "doc-name", "value" => "", "name" => "template[name]", "type" => "hidden"]);
?>
<div class="col-md-12 p-0 m-0 form-group row margin-bottom-10">
    <div class="col-md-12 p-0 m-0 orm-group row margin-bottom-10">
           <label class="col-form-label text-right required"><?php echo $this->lang->line("mou_type");?></label>
       </div>
       <div class="form-group row col-md-12 p-0 m-0">
           <?php echo form_input(["id" => "category", "name"=>"category","class" => "form-control", "value" => "Mou", "type"=>"hidden"]); ?>
           <?php echo form_input(["id" => "stage", "name"=>"stage","class" => "form-control", "value" => "Development", "type"=>"hidden"]); ?>
           <?php echo form_dropdown("type_id", $types, "", "id=\"type\" class=\"form-control select-picker\" data-live-search=\"true\" data-size=\"" . $this->session->userdata("max_drop_down_length") . "\"");?>
           <div data-field="type_id\" class="inline-error d-none"></div>
       </div>
    <div class="col-md-12 p-0 m-0 form-group row margin-bottom-10">
        <label class="col-form-label text-right"><?php echo $this->lang->line("sub_type"); ?></label>
       </div>
       <div class="form-group row col-md-12 p-0 m-0">
           <?php echo form_dropdown("sub_type_id", "", "", "id=\"sub-type\" class=\"form-control select-picker\" data-live-search=\"true\" data-size=\"" . $this->session->userdata("max_drop_down_length") . "\"");?>
           <div data-field="sub_type_id" class="inline-error d-none"></div>
       </div>
       <div class="col-md-12 p-0 m-0 form-group row margin-bottom-10">
           <label class="col-form-label text-right required"><?php echo $this->lang->line("choose_template");?></label>
       </div>
       <div class="form-group row col-md-11 p-0 m-0">
           <?php echo form_dropdown("template[id]", "", "", "id=\"templates\" class=\"form-control select-picker\" data-live-search=\"true\" data-size=\"" . $this->session->userdata("max_drop_down_length") . "\"");?>     <div data-field="templates\" class="inline-error d-none"></div>
       </div>
       <div class="col-md-1 p-0 m-0 col-xs-2">
           <a href="<?php echo app_url("modules/contract/contracts/add");?>" class="btn btn-link mt-2">
               <i class="fa-solid fa-right-long" title="<?php echo $this->lang->line("template_not_available");?>"> </i>         </a>
       </div>
</div>
<div class="col-md-12 p-0 m-0 form-group row margin-bottom-10">
    <div class="col-md-12 p-0 m-0 form-group row margin-bottom-10">
        <label class="col-form-label text-right"><?php echo $this->lang->line("doc_name");?></label>
    </div>
    <div class="input-group form-group row col-md-11 p-0 m-0">
        <?php echo form_input(["id" => "doc-name-preffix", "class" => "form-control", "value" => "", "autocomplete" => "off"]);?>
        <div class="input-group-addon align-middle ml-1"><span  id="doc-name-suffix"><?php echo date("YmdHis");?></span>.docx</div>
    </div>
</div>