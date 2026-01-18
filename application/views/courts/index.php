<?php $this->load->view("partial/header"); ?>


<div class="col-md-12">
    <div class="row">
        <div class="col-md-12" style="padding-left: 0; padding-right: 0;">
            <ul class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="<?php echo site_url('dashboard/admin'); ?>">
                        <?php echo $this->lang->line("administration"); ?>
                    </a>
                </li>
                <li class="breadcrumb-item active">
                    <?php echo $this->lang->line("court"); ?>
                </li>
                <li class="breadcrumb-item">
                    <a href="<?php echo site_url('courts/add'); ?>">
                        <?php echo $this->lang->line("add_court"); ?>
                    </a>
                </li>
            </ul>
        </div>
        <form method="get" action="<?php echo site_url('courts'); ?>" class="form-inline mb-3">
            <div class="form-group mr-2">
                <input type="text" name="name" class="form-control" placeholder="Court Name" value="<?php echo $this->input->get('name'); ?>">
            </div>
            <div class="form-group mr-2">
                <select name="type" class="form-control">
                    <option value="">All Types</option>
                    <?php foreach ($court_types as $key => $label) { ?>
                        <option value="<?php echo $key; ?>" <?php if ($this->input->get('type') == $key) echo 'selected'; ?>>
                            <?php echo $label; ?>
                        </option>
                    <?php } ?>
                </select>
            </div>
            <div class="form-group mr-2">
                <select name="rank" class="form-control">
                    <option value="">All Ranks</option>
                    <?php foreach ($court_ranks as $key => $label) { ?>
                        <option value="<?php echo $key; ?>" <?php if ($this->input->get('rank') == $key) echo 'selected'; ?>>
                            <?php echo $label; ?>
                        </option>
                    <?php } ?>
                </select>
            </div>
            <div class="form-group mr-2">
                <select name="region" class="form-control">
                    <option value="">All Regions</option>
                    <?php foreach ($court_regions as $key => $label) { ?>
                        <option value="<?php echo $key; ?>" <?php if ($this->input->get('region') == $key) echo 'selected'; ?>>
                            <?php echo $label; ?>
                        </option>
                    <?php } ?>
                </select>
            </div>
            <div class="form-group mr-2">
                <input type="number" name="hierarchy" class="form-control" placeholder="Hierarchy" value="<?php echo $this->input->get('hierarchy'); ?>">
            </div>
            <button type="submit" class="btn btn-primary">Filter</button>
        </form>

        <div class="col-md-12 no-padding">
            <div class="col-md-12 form-group row" id="pagination">
                <div class="col-md-6 no-padding col-xs-12">
                    <h4>
                        <?php echo $this->lang->line("total_records"); ?>:
                        <?php echo $records["totalRows"]; ?>
                    </h4>
                </div>

                <?php if (!empty($this->court->get("paginationLinks"))) { ?>
                    <div class="col-md-6 no-padding col-xs-12" id="pagination">
                        <ul class="pagination pull-right">
                            <?php echo $this->court->get("paginationLinks"); ?>
                        </ul>
                    </div>
                <?php } ?>
            </div>

            <?php if ($records["totalRows"] > 0) { ?>
                <div class="col-md-12 form-group">
                    <div class="dropdown more pull-right margin-right10">
                        <a href="#" data-toggle="dropdown" class="dropdown-toggle btn btn-default btn-xs">
                            <i class="icon fa fa-cog"></i> <span class="caret no-margin"></span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right">
                            <a class="dropdown-item" href="<?php echo site_url("export/courts"); ?>">
                                <?php echo $this->lang->line("export_to_excel"); ?>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="col-md-12 table-responsive">
                    <table id="courtsTable" class="table table-bordered table-striped table-hover">
                        <thead>
                        <tr>
                            <th><?php echo $this->lang->line("name"); ?></th>
                            <th><?php echo $this->lang->line("court_type"); ?></th>
                            <th><?php echo $this->lang->line("court_degree"); ?></th>
                            <th><?php echo $this->lang->line("court_region"); ?></th>
                            <th><?php echo $this->lang->line("court_hierarchy"); ?></th>
                            <th><?php echo $this->lang->line("edit"); ?></th>
                            <th><?php echo $this->lang->line("delete"); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($records["data"] as $record) { ?>
                            <tr>
                                <td><?php echo trim($record["name"]); ?></td>
                                <td><?php echo trim($record["type_name"]); ?></td>
                                <td><?php echo trim($record["rank_name"]); ?></td>
                                <td><?php echo trim($record["region_name"]); ?></td>
                                <td><?php echo $record["court_hierarchy"]; ?></td>
                                <td>
                                    <a href="<?php echo site_url("courts/edit/" . $record["id"]); ?>">
                                        <i class="fa fa-edit fa-lg"></i>
                                    </a>
                                </td>
                                <td>
                                    <a href="javascript:;" onclick="return confirm(_lang.confirmationDeleteSelectedRecord) ? document.location = '<?php echo site_url("courts/delete/" . $record["id"]); ?>' : false;">
                                        <i class="fa fa-trash fa-lg"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </div>
            <?php } ?>
        </div>

    </div>
</div>




<?php $this->load->view("partial/footer"); ?>

