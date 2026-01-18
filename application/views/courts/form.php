<?php $this->load->view("partial/header"); ?>

<div class="col-md-12">
    <div class="row">
        <div class="col-md-12">
            <ul class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="<?php echo site_url('dashboard/admin'); ?>">
                        <?php echo $this->lang->line("administration"); ?>
                    </a>
                </li>
                <li class="breadcrumb-item">
                    <a href="<?php echo site_url("courts/index"); ?>">
                        <?php echo $this->lang->line("court"); ?>
                    </a>
                </li>
                <li class="breadcrumb-item active">
                    <?php echo $this->lang->line("add_court"); ?>
                </li>
            </ul>
        </div>

        <div class="col-md-12">
            <?php echo form_open(current_url(), "novalidate"); ?>

            <?php echo form_input([
                "name" => "id",
                "value" => $this->court->get_field("id"),
                "id" => "id",
                "type" => "hidden"
            ]); ?>

            <div class="form-group row">
                <label class="control-label col-md-2"><?php echo $this->lang->line("name"); ?></label>
                <div class="col-md-4">
                    <?php echo form_input([
                        "name" => "name",
                        "value" => $this->court->get_field("name"),
                        "id" => "name",
                        "class" => "form-control",
                        "placeholder" => $this->lang->line("name"),
                        "maxlength" => "255",
                        "required" => true
                    ]); ?>
                    <?php echo $this->court->get_error("name", "<div class=\"help-inline error\">", "</div>"); ?>
                </div>
            </div>

            <div class="form-group row">
                <label class="control-label col-md-2"><?php echo $this->lang->line("court_type"); ?></label>
                <div class="col-md-4">
                    <?php echo form_dropdown(
                        "court_type_id",
                        $court_types,
                        $this->court->get_field("court_type_id"),
                        "class='form-control'"
                    ); ?>
                    <?php echo $this->court->get_error("court_type_id", "<div class=\"help-inline error\">", "</div>"); ?>
                </div>
            </div>

            <div class="form-group row">
                <label class="control-label col-md-2"><?php echo $this->lang->line("court_rank"); ?></label>
                <div class="col-md-4">
                    <?php echo form_dropdown(
                        "court_rank_id",
                        $court_ranks,
                        $this->court->get_field("court_rank_id"),
                        "class='form-control'"
                    ); ?>
                    <?php echo $this->court->get_error("court_rank_id", "<div class=\"help-inline error\">", "</div>"); ?>
                </div>
            </div>

            <div class="form-group row">
                <label class="control-label col-md-2"><?php echo $this->lang->line("court_region"); ?></label>
                <div class="col-md-4">
                    <?php echo form_dropdown(
                        "court_region_id",
                        $court_regions,
                        $this->court->get_field("court_region_id"),
                        "class='form-control'"
                    ); ?>
                    <?php echo $this->court->get_error("court_region_id", "<div class=\"help-inline error\">", "</div>"); ?>
                </div>
            </div>

            <div class="form-group row">
                <label class="control-label col-md-2"><?php echo $this->lang->line("court_hierarchy"); ?></label>
                <div class="col-md-4">
                    <?php
                    $hierarchy_options = [
                        1 => "1 (Highest)",
                        2 => "2",
                        3 => "3",
                        4 => "4 (Lowest)"
                    ];
                    echo form_dropdown(
                        "court_hierarchy",
                        $hierarchy_options,
                        $this->court->get_field("court_hierarchy"),
                        "class='form-control'"
                    );
                    ?>
                    <?php echo $this->court->get_error("court_hierarchy", "<div class=\"help-inline error\">", "</div>"); ?>
                </div>
            </div>

            <div class="form-group col-md-12 no-padding">
                <?php echo form_submit("submitBtn", $this->lang->line("save"), "class='btn btn-info'"); ?>
                <?php echo form_reset("reset", $this->lang->line("reset"), "class='btn btn-link'"); ?>
            </div>

            <?php echo form_close(); ?>
        </div>
    </div>
</div>

<?php $this->load->view("partial/footer"); ?>
