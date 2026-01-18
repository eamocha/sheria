<div id="attachments-module-heading" class="d-flex" onclick="collapse('attachments-module-heading', 'attachments-module-body');">
    <a href="javascript:;" class="toggle-title p-1"> <i class="fa fa-angle-down black_color font-18">&nbsp;</i>  </a>
    <h4 class="toggle-title px-2"><?php echo $opinion_data["type"]=="Conveyancing"?$this->lang->line("legal_instruments"):$this->lang->line("attachments");?></h4>
</div>
<div class="mod-content attachments-drop-zone dragAndDrop" id="attachments-module-body">
    <?php echo form_open("", 'id="attachments-form" method="post" class="form-horizontal" role="form" accept-charset="utf-8"');
echo form_input(["id" => "module", "name" => "module", "value" => "opinion", "type" => "hidden"]);
echo form_input(["id" => "module-controller", "name" => "module", "value" => "legal_opinions", "type" => "hidden"]);
echo form_input(["id" => "module-record-id", "name" => "module_record_id", "value" => $opinion_data["id"], "type" => "hidden"]);
echo form_input(["id" => "lineage", "name" => "lineage", "type" => "hidden"]);
echo form_input(["id" => "term", "name" => "term", "type" => "hidden"]);?>
    <div class="zone-div">
        <span class="zone-text">  <i class="zone-drop-icon fa-solid fa-upload"></i>&nbsp;
            <?php echo $this->lang->line("drop_files");     echo $this->lang->line("or");?>
            <button type="button" class="zone-button"><?php echo $this->lang->line("browse");?>.</button>
        </span>
    </div><?php echo form_close();?>
    <ol id="attachment_thumbnails" class="item-attachments"></ol>
</div>