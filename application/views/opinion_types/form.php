<?php
$this->load->view("partial/header");
?>

<div class="col-md-12">
    <div class="row">
        <div class="col-md-12">
            <ul class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="dashboard/admin">
                        <?php echo $this->lang->line("administration"); ?>
                    </a>
                </li>
                <li class="breadcrumb-item">
                    <a href="<?php echo site_url("opinion_types/index"); ?>">
                        <?php echo $this->lang->line("opinion_type"); ?>
                    </a>
                </li>
                <li class="breadcrumb-item active">
                    <?php echo $this->lang->line("add_opinion_type"); ?>
                </li>
            </ul>
        </div>

        <div class="col-md-12">
            <?php echo form_open(current_url(), 'id="opinionTypeForm" novalidate'); ?>

            <div class="col-md-12 no-padding form-group">
                <h4><?php echo $this->lang->line("opinion_type"); ?></h4>
            </div>

            <?php echo form_input(["name" => "id", "id" => "id", "value" => $this->opinion_type->get_field("id"), "type" => "hidden"]); ?>

            <div class="col-md-12 no-padding row">
                <?php  foreach ($languages as $key => $value){ ?>
                    <div class="col-md-3 form-group no-padding-left">
                        <label class="control-label required">
                            <?php echo $this->lang->line("opinion_type").'('. $value["display_name"].')'; ?>
                        </label>
                        <?php
                        $input_name = "name_" . $value["name"];
                        echo form_input(["name" => $input_name, "id" => $input_name, "placeholder" => $this->lang->line("name_" . $value["name"]), "class" => "form-control", "maxlength" => "255", "value" => ${$input_name}, "data-validation-engine" => "validate[required]"]);
                        ?>
                    </div>
                <?php } ?>
            </div>
            <div class="col-md-12 form-group row p-0 ml-auto">
                <div class="col-md-6 no-padding">
                    <label class="control-label required"><?php echo $applies_to_field["label"];?></label>
                <div class="col-md-4  col-xs-10"><?php echo form_dropdown($applies_to_field["name"], ["Opinions"=>"Opinions","Both"=>"Both","Conveyancing"=>"Conveyancing"],"Contract", 'class="form-control"'); ?>
                    <div data-field="<?php echo $applies_to_field["name"];?>_<?php echo $system_lang;?>" class="inline-error d-none padding-5"></div>
                </div>
               </div>
            </div>

            <div class="form-group col-md-12 no-padding">
                <?php
                echo form_submit("submitBtn", $this->lang->line("save"), 'class="btn btn-default btn-info"');
                echo form_reset("reset", $this->lang->line("reset"), 'class="btn btn-default btn-link"');
                ?>
            </div>

            <?php echo form_close(); ?>
        </div>
    </div>
</div>

<?php
$this->load->view("partial/footer");
?>

<script type="text/javascript">
    jQuery(document).ready(function () {
        jQuery('#opinionTypeForm').validationEngine({
            validationEventTrigger: "submit",
            autoPositionUpdate: true,
            promptPosition: 'bottomRight',
            scroll: false
        });
    });
</script>
