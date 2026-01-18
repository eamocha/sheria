<?php
$this->load->view("partial/header");
?>
<div class="col-md-12">
    <div class="row">
        <div class="col-md-12">
            <ul class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="dashboard/admin"><?php echo $this->lang->line("administration"); ?></a>
                </li>
                <li class="breadcrumb-item">
                    <a href="<?php echo site_url("correspondence_types/index"); ?>">
                        <?php echo $this->lang->line("correspondence_type"); ?>
                    </a>
                </li>
                <li class="breadcrumb-item active"><?php echo $this->lang->line("add_correspondence_type"); ?></li>
            </ul>
        </div>
        
        <div class="col-md-12">
            <?php echo form_open(current_url(), "novalidate"); ?>
            
            <div class="col-md-12 no-padding form-group">
                <h4><?php echo $this->lang->line("correspondence_type"); ?></h4>
            </div>
            
            <?php echo form_input([
                "name" => "id", 
                "value" => $this->correspondence_type->get_field("id"), 
                "id" => "id", 
                "type" => "hidden"
            ]); ?>
            
            <div class="form-group col-md-6 no-padding row">
                <label class="control-label col-md-1 no-padding">
                    <?php echo $this->lang->line("type"); ?>
                </label>
                <div class="col-md-4 no-padding">
                    <?php echo form_input([
                        "name" => "name",
                        "value" => $this->correspondence_type->get_field("name"),
                        "id" => "name",
                        "class" => "form-control",
                        "placeholder" => $this->lang->line("type"),
                        "maxlength" => "255",
                        "required" => true
                    ]); ?>
                    <div class="margin-top">
                        <?php echo $this->correspondence_type->get_error("name", "<div class=\"help-inline error\">", "</div>"); ?>
                    </div>
                </div>
            </div>
            
            <div class="form-group col-md-12 no-padding">
                <?php echo form_submit("submitBtn", $this->lang->line("save"), "class=\"btn btn-default btn-info\""); ?>
                <?php echo form_reset("reset", $this->lang->line("reset"), "class=\"btn btn-default btn-link\""); ?>
            </div>
            
            <?php echo form_close(); ?>
        </div>
    </div>
</div>
<?php
$this->load->view("partial/footer");