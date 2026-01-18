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
                <li class="breadcrumb-item active"><?php echo $this->lang->line("add"); ?></li>
                <li class="breadcrumb-item">
                    <a href="<?php echo site_url("conveyancing_transactions_types/add"); ?>">
                        <?php echo $this->lang->line("add"); ?>
                    </a>
                </li>
            </ul>
        </div>
        
        <div class="col-md-12 no-padding">
            <div class="col-md-12 form-group row" id="pagination">
                <div class="col-md-6 no-padding col-xs-12">
                    <h4>
                        <?php echo $this->lang->line("total_records"); ?>: 
                        <?php echo $this->conveyancing_transaction_types->get("paginationTotalRows"); ?>
                    </h4>
                </div>
                <?php
                $links = $this->conveyancing_transaction_types->get("paginationLinks");
                if (!empty($links)) {
                ?>
                    <div class="col-md-6 no-padding col-xs-12" id="pagination">
                        <ul class="pagination pull-right">
                            <?php echo $links; ?>
                        </ul>
                    </div>
                <?php
                }
                unset($links);
                ?>
            </div>
            
            <?php if (count($records) > 0) { ?>
                <div class="col-md-12 form-group">
                    <div class="dropdown more pull-right margin-right10">
                        <a href="" data-toggle="dropdown" class="dropdown-toggle btn btn-default btn-xs">
                            <i class="icon fa fa-cog"></i> <span class="caret no-margin"></span>
                        </a>
                        <div aria-labelledby="dLabel" role="menu" class="dropdown-menu dropdown-menu-right">
                            <a class="dropdown-item" href="<?php echo site_url("export/conveyancing_transactions_types"); ?>">
                                <?php echo $this->lang->line("export_to_excel"); ?>
                            </a>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-12 table-responsive">
                    <table class="table table-bordered table-striped table-hover">
                        <tr>
                            <th><?php echo $this->lang->line("name"); ?>&nbsp;</th>
                            <th><?php echo $this->lang->line("edit"); ?>&nbsp;</th>
                            <th><?php echo $this->lang->line("delete"); ?>&nbsp;</th>
                        </tr>
                        
                        <?php foreach ($records as $record) { ?>
                            <tr>
                                <td><?php echo $record["name"]; ?>&nbsp;</td>
                                <td>
                                    <a href="<?php echo site_url("conveyancing_transactions_types/edit/" . $record["id"]); ?>">
                                        <i class="fa fa-edit fa-lg"></i>
                                    </a>&nbsp;
                                </td>
                                <td>
                                    <a href="<?php echo site_url("conveyancing_transactions_types/delete/" . $record["id"]); ?>"
                                        onclick="return confirm('<?php echo $this->lang->line('confirmationDeleteSelectedRecord'); ?>');">
                                         <i class="fa fa-trash fa-lg"></i>
                                    </a>&nbsp;
                                </td>
                            </tr>
                        <?php } ?>
                    </table>
                </div>
            <?php } ?>
        </div>
    </div>
</div>
<?php
$this->load->view("partial/footer");