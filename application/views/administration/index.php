<div class="col-md-12 no-padding" id="administration-type-container">
          <?php echo form_input(["value" => $field_type, "id" => "field-type", "type" => "hidden"]);?>
    <div class="col-md-12 form-group" id="pagination"><?php
    echo form_input(["value" => sizeof($records), "id" => "rows-number", "type" => "hidden"]);?>
        <div class="col-md-6 no-padding col-xs-12">
            <h4><?php echo $this->lang->line("total_records");?>: <span id="total-records"><?php echo sizeof($records);?></span></h4>
        </div>
    </div>
    <div class="col-md-12 table-responsive">
        <table id="administration-type-table" class="table table-bordered table-striped table-hover <?php echo 0 < count($records) ? "" : "d-none";?>">
            <thead><tr> <?php foreach ($languages as $key => $value) { ?>
                <th id="lang-<?php echo $key; ?>"><?php  echo $field_name;?> (<?php echo $this->lang->line($value["fullName"]);?>)&nbsp;</th>
                <?php }
                if (isset($extra_columns) && is_array($extra_columns)) {
                    foreach ($extra_columns as $key => $extra_column) {?>
                <th id="extra-columns-<?php echo $extra_column; ?>"><?php   echo $this->lang->line($extra_column); ?></th>
                <?php   }
                }?>
                <th><?php echo $this->lang->line("applies_to");?>&nbsp;</th>
                <th><?php echo $this->lang->line("edit");?>&nbsp;</th>
                <th><?php echo $this->lang->line("delete");?>&nbsp;</th>
            </tr>
            </thead>
            <tbody>  <?php
            if (0 < count($records)) {
                foreach ($records as $id => $record) {  ?>
                    <tr id="administration-type-record-<?php  echo $id;  ?>">  <?php
                        foreach ($languages as $value) {  ?>
                            <td><?php  echo $record["name_" . $value["name"]]; ?>&nbsp;</td>
                            <?php  }

                        if (isset($extra_columns) && is_array($extra_columns)) {
                            foreach ($extra_columns as $extra_column) {  ?>
                                <td><?php echo $record[$extra_column];?></td>
                                <?php   }
                        }?>
                        <td><?php echo $record['apply_to']?></td>
                        <td><a href="javascript:;" onclick="administrationForm('<?php echo $field_type;?>', '<?php echo $id;?>', false, '<?php echo $module ?? false;?>')"><i class="fa fa-edit fa-lg"></i></a>&nbsp;</td>
                        <td><a href="javascript:;" onclick="confirmationDialog('confirm_delete_record', {resultHandler: deleteAdministrationRecord, parm: '<?php echo $id;?>', module: '<?php echo $module ?? false;?>'});"><i class="fa fa-trash fa-lg"></i></a>&nbsp;</td>
                    </tr>
                    <?php
                }
            }  ?>
            </tbody>
        </table>
    </div>
</div>