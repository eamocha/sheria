<?php $this->load->view("partial/header"); ?>

<div class="col-md-12">
    <div class="row">
        <div class="col-md-12">
            <ul class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="dashboard/admin"><?php echo $this->lang->line("administration"); ?></a>
                </li>
                <li class="breadcrumb-item active"><?php echo $this->lang->line("location"); ?></li>
                <li class="breadcrumb-item">
                    <a href="<?php echo site_url("exhibit_locations/add"); ?>">
                        <?php echo $this->lang->line("add_location"); ?>
                    </a>
                </li>
            </ul>
        </div>

        <div class="col-md-12 no-padding">
            <div class="col-md-12 form-group row" id="pagination">
                <div class="col-md-6 no-padding col-xs-12">
                    <h4><?php echo $this->lang->line("total_records"); ?>: <?php echo $this->exhibit_location->get("paginationTotalRows"); ?></h4>
                </div>
                
                <?php if (!empty($links = $this->exhibit_location->get("paginationLinks"))) : ?>
                    <div class="col-md-6 no-padding col-xs-12" id="pagination">
                        <ul class="pagination pull-right">
                            <?php echo $links; ?>
                        </ul>
                    </div>
                <?php endif; ?>
            </div>

            <?php if (count($records) > 0) : ?>
                <div class="col-md-12 form-group">
                    <div class="dropdown more pull-right margin-right10">
                        <a href="" data-toggle="dropdown" class="dropdown-toggle btn btn-default btn-xs">
                            <i class="icon fa fa-cog"></i> <span class="caret no-margin"></span>
                        </a>
                        <div aria-labelledby="dLabel" role="menu" class="dropdown-menu dropdown-menu-right">
                            <a class="dropdown-item" href="<?php echo site_url("export/exhibit_locations"); ?>">
                                <?php echo $this->lang->line("export_to_excel"); ?>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="col-md-12 table-responsive">
                    <table class="table table-bordered table-striped table-hover">
                        <tr>
                            <th><?php echo $this->lang->line("name"); ?></th>
                            <th><?php echo $this->lang->line("latitude"); ?></th>
                            <th><?php echo $this->lang->line("longitude"); ?></th>
							<th><?php echo $this->lang->line("description"); ?></th>
                            <th><?php echo $this->lang->line("edit"); ?></th>
                            <th><?php echo $this->lang->line("delete"); ?></th>
                        </tr>

                        <?php foreach ($records as $record) : ?>
                            <tr id="tl_<?php echo $record["id"]; ?>">
                                <td><?php echo $record["name"]; ?></td>
                                <td><?php echo $record["latitude"] ?? 'N/A'; ?></td>
                                <td><?php echo $record["longitude"] ?? 'N/A'; ?></td>
								 <td><?php echo $record["description"] ?? 'N/A'; ?></td>
                                <td>
                                    <a href="<?php echo site_url("exhibit_locations/edit/" . $record["id"]); ?>">
                                        <i class="fa fa-edit fa-lg"></i>
                                    </a>
                                </td>
                                <td>
                                    <a href="javascript:;" onclick="deleteTaskLocationSelectedRow(<?php echo $record["id"]; ?>)">
                                        <i class="fa fa-trash fa-lg"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php $this->load->view("partial/footer"); ?>