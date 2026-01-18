<table class="table table-bordered table-striped table-hover custom-fields-table">
    <thead>
    <tr>
        <?php foreach ($languages as $key => $value){ ?>
            <th class="custom-fields-table-cell"><?php echo $this->lang->line("custom_field_language_" . $value["name"]); ?></th>
        <?php }; ?>
        <th class="custom-fields-table-cell"><?php echo $this->lang->line("type"); ?></th>
        <th class="custom-fields-table-cell"><?php echo $this->lang->line("actions"); ?></th>
    </tr>
    </thead>
    <tbody id="custom-field-table-body">
    <?php if (count($records) > 0){ ?>
        <?php foreach ($records as $record){ ?>
            <tr id="custom-field-order-<?php echo $record["id"]; ?>">
                <?php foreach ($languages as $key => $value){  ?>
                    <td title="<?php echo $record["name_" . $value["name"]]; ?>" class="custom-fields-table-cell">
                        <?php echo $record["name_" . $value["name"]]; ?>
                    </td>
                <?php }; ?>
                <td class="custom-fields-table-cell">
                    <?php echo $record["type"] === "lookup" ? $this->lang->line("lookup_" . $record["type_data"]) : $this->lang->line($record["type"]); ?>
                </td>
                <td class="custom-fields-table-cell">
                    <div class="row">
                        <div class="col-md-4">
                            <span title="<?php echo $this->lang->line("helper_order_fields"); ?>" class="ui-icon ui-icon-arrowthick-2-n-s"></span>
                        </div>
                        <div class="col-md-4">
                            <a href="javascript:;" onClick="customFieldForm('<?php echo $model; ?>', '<?php echo $record["id"]; ?>');">
                                <i class="fa fa-edit fa-lg"></i>
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a href="javascript:;" onclick="deleteCustomField('<?php echo $model; ?>', '<?php echo $record["id"]; ?>');">
                                <i class="fa fa-trash fa-lg"></i>
                            </a>
                        </div>
                    </div>
                </td>
            </tr>
        <?php }; ?>
    <?php }; ?>
    </tbody>
</table>