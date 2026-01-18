<?php $this->load->view("partial/header"); ?>

<div class="col-md-12">
    <div class="row">
        <div class="col-md-12">
            <ul class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="<?php echo base_url('dashboard/admin') ?>">
                        <?php echo $this->lang->line("administration") ?>
                    </a>
                </li>
                <li class="breadcrumb-item">
                    <a href="<?php echo site_url("exhibit_locations/index") ?>">
                        <?php echo $this->lang->line("location") ?>
                    </a>
                </li>
                <li class="breadcrumb-item active">
                    <?php echo $this->lang->line("add_location") ?>
                </li>
            </ul>
        </div>

        <div class="col-md-12">
            <?php echo form_open(current_url(), "novalidate") ?>
            
            <div class="col-md-12 no-padding form-group">
                <h4><?php echo $this->lang->line("location") ?></h4>
            </div>

            <?php echo form_input([
                "name" => "id",
                "id" => "id",
                "value" => $this->exhibit_location->get_field("id"),
                "type" => "hidden"
            ]) ?>

            <div class="form-group col-md-6 no-padding row">
                <label class="control-label col-md-1 no-padding">
                    <?php echo $this->lang->line("name") ?>
                </label>
                <div class="col-md-4 no-padding">
                    <?php echo form_input([
                        "name" => "name",
                        "id" => "name",
                        "placeholder" => $this->lang->line("name"),
                        "class" => "form-control",
                        "maxlength" => "255",
                        "required" => "",
                        "value" => $this->exhibit_location->get_field("name")
                    ]) ?>
                    <div class="margin-top">
                        <?php echo $this->exhibit_location->get_error("name", "<div class='help-inline error'>", "</div>") ?>
                    </div>
                </div>
            </div>

            <div class="form-group col-md-6 no-padding row">
                <label class="control-label col-md-1 no-padding">
                    <?php echo $this->lang->line("latitude") ?>
                </label>
                <div class="col-md-4 no-padding">
                    <?php echo form_input([
                        "name" => "latitude",
                        "id" => "latitude",
                        "placeholder" => $this->lang->line("latitude"),
                        "class" => "form-control",
                        "maxlength" => "50",
                        "value" => $this->exhibit_location->get_field("latitude")
                    ]) ?>
                    <div class="margin-top">
                        <?php echo $this->exhibit_location->get_error("latitude", "<div class='help-inline error'>", "</div>") ?>
                    </div>
                </div>
            </div>

            <div class="form-group col-md-6 no-padding row">
                <label class="control-label col-md-1 no-padding">
                    <?php echo $this->lang->line("longitude") ?>
                </label>
                <div class="col-md-4 no-padding">
                    <?php echo form_input([
                        "name" => "longitude",
                        "id" => "longitude",
                        "placeholder" => $this->lang->line("longitude"),
                        "class" => "form-control",
                        "maxlength" => "50",
                        "value" => $this->exhibit_location->get_field("longitude")
                    ]) ?>
                    <div class="margin-top">
                        <?php echo $this->exhibit_location->get_error("longitude", "<div class='help-inline error'>", "</div>") ?>
                    </div>
                </div>
            </div>

            <div class="form-group col-md-12 no-padding">
                <label class="control-label col-md-1 no-padding">
                    <?php echo $this->lang->line("description") ?>
                </label>
                <div class="col-md-8 no-padding">
                    <?php echo form_textarea([
                        "name" => "description",
                        "id" => "description",
                        "placeholder" => $this->lang->line("description"),
                        "class" => "form-control",
                        "rows" => "3",
                        "value" => $this->exhibit_location->get_field("description")
                    ]) ?>
                </div>
            </div>

            <div class="form-group col-md-12 no-padding">
                <?php echo form_submit("submitBtn", $this->lang->line("save"), "class='btn btn-default btn-info'") ?>
                <?php echo form_reset("reset", $this->lang->line("reset"), "class='btn btn-default btn-link'") ?>
            </div>

            <?php echo form_close() ?>
        </div>
    </div>
</div>

<?php $this->load->view("partial/footer"); ?>