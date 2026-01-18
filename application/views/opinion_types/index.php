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
                <li class="breadcrumb-item active">
                    <?php echo $this->lang->line("opinion_type"); ?>
                </li>
                <li class="breadcrumb-item">
                    <a href="<?php echo site_url("opinion_types/add"); ?>">
                        <?php echo $this->lang->line("add_opinion_type"); ?>
                    </a>
                </li>
            </ul>
        </div>

        <div class="col-md-12 no-padding">
            <div class="col-md-12 form-group row" id="pagination">
                <div class="col-md-6 no-padding col-xs-12">
                    <h4>
                        <?php echo $this->lang->line("total_records"); ?>: <?php echo sizeof($records); ?>
                    </h4>
                </div>
            </div>

            <?php  if (count($records) > 0){ ?>
                <div class="col-md-12 form-group">
                    <div class="dropdown more pull-right margin-right10">
                        <a href="#" data-toggle="dropdown" class="dropdown-toggle btn btn-default btn-xs">
                            <i class="icon fa fa-cog"></i> <span class="caret no-margin"></span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right" role="menu" aria-labelledby="dLabel">
                            <a class="dropdown-item" href="<?php echo site_url("export/opinion_types"); ?>">
                                <?php echo $this->lang->line("export_to_excel"); ?>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="col-md-12 table-responsive">
                    <table class="table table-bordered table-striped table-hover">
                        <tr>
                            <?php  foreach ($languages as $key => $value){ ?>
                                <th>
                                    <?php echo $this->lang->line("opinion_type_language_" . $value["name"]); ?>&nbsp;
                                </th>
                            <?php } ?>
                            <th><?php echo $this->lang->line("applies_to"); ?>&nbsp;</th>
                            <th><?php echo $this->lang->line("edit"); ?>&nbsp;</th>
                            <th><?php echo $this->lang->line("delete"); ?>&nbsp;</th>
                        </tr>

                        <?php foreach ($records as $record){ ?>
                            <tr>
                                <?php foreach ($languages as $key => $value){ ?>
                                    <td>
                                        <?php
                                        $field = "name_" . $value["name"];
                                        echo isset($record[$field]) ? $record[$field] : "";
                                        ?>&nbsp;
                                    </td>
                                <?php }; ?>
                                <td><?php echo $record['applies_to'];?></td>
                                <td>
                                    <a href="<?php echo site_url("opinion_types/edit/" . $record["id"]); ?>">
                                        <i class="fa fa-edit fa-lg"></i>
                                    </a>&nbsp;
                                </td>
                                <td>
                                    <a href="javascript:;" onclick="return confirm(_lang.confirmationDeleteSelectedRecord) ? document.location = '<?php echo site_url("opinion_types/delete/" . $record["id"]); ?>' : false;">
                                        <i class="fa fa-trash fa-lg"></i>
                                    </a>&nbsp;
                                </td>
                            </tr>
                        <?php }; ?>
                    </table>
                </div>
            <?php }; ?>
        </div>
    </div>
</div>

<?php $this->load->view("partial/footer"); ?>
