<?php
$this->load->view("partial/header");
?>

    <div class="col-md-12">
        <div class="row">
            <div class="col-md-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="dashboard/admin"><?= $this->lang->line("administration") ?></a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="<?= site_url("departments/index") ?>"><?= $this->lang->line("departments") ?></a>
                    </li>
                    <li class="breadcrumb-item active">
                        <?php
                        echo empty($id) ? $this->lang->line("add_department") : $this->lang->line("edit");
                        ?>
                    </li>
                </ul>
            </div>

            <div class="col-md-12">
                <?= form_open(current_url(), "novalidate") ?>

                <div class="col-md-12 no-padding form-group">
                    <h4><?= $this->lang->line("departments") ?></h4>
                </div>

                <?= form_input([
                    "name" => "id",
                    "id" => "id",
                    "value" => $this->department->get_field("id"),
                    "type" => "hidden"
                ]) ?>

                <div class="form-group col-md-6 no-padding row">
                    <label class="control-label col-md-1 no-padding">
                        <?= $this->lang->line("name") ?>
                    </label>
                    <div class="col-md-4 no-padding">
                        <?= form_input([
                            "name" => "name",
                            "id" => "name",
                            "placeholder" => $this->lang->line("name"),
                            "class" => "form-control",
                            "maxlength" => "100",
                            "required" => "",
                            "value" => $this->department->get_field("name")
                        ]) ?>
                        <div class="margin-top">

                            <?= $this->department->get_error("name", "<div class=\"help-inline error\">", "</div>") ?>
                        </div>
                    </div>
                </div>

                <div class="form-group col-md-12 no-padding row">
                    <?= form_submit(
                        "submitBtn",
                        $this->lang->line("save"),
                        "class=\"btn btn-default btn-info\""
                    ) ?>
                    <?= form_reset(
                        "reset",
                        $this->lang->line("reset"),
                        "class=\"btn btn-default btn-link\""
                    ) ?>
                </div>

                <?= form_close() ?>
            </div>
        </div>
    </div>

<?php
$this->load->view("partial/footer");
?>