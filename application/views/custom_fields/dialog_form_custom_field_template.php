<div id="custom-fieds-container" class="col-md-12 no-padding">
    <?php foreach ($custom_fields as $field) {?>
    <div id="<?php echo $field["id"];?>-container" class="row m-0 form-group col-md-12 no-padding mb-10">
        <label class="control-label col-md-3 col-xs-7 no-padding-right"><?php  echo $field["customName"];?></label>
        <div class="col-md-9 no-padding-right col-xs-10"><?php    echo $field["hidden_custom_field_id"];
        echo $field["hidden_value_id"];
        echo $field["hidden_record_id"];
        switch ($field["type"]) {
            case "date": ?>
                <div class="input-group date col-md-12 no-padding"><?php     echo $field["custom_field"];?></div>
                <div class="input-group date col-md-12 col-xs-12 no-padding"> <div data-field="date_value_<?php echo $field["id"];?>" class="inline-error d-none"></div>        </div><?php
                break;
            case "date_time": ?>
                <div class="input-group date col-md-12 no-padding"><?php       echo $field["custom_field"];?>  </div>
                <div class="input-group date col-md-5 col-xs-6 no-padding date-picker">
                    <div data-field="date_value_<?php  echo $field["id"];?>" class="inline-error d-none"></div>
                </div>
                <div class="col-md-3 col-xs-4 time-container">
                <div data-field="time_value_<?php  echo $field["id"];?>" class="inline-error d-none"></div>
                </div>
                <?php
                break;
            case "long_text": ?>
                <textarea name=" <?php echo customFields[$field["id"]][text_value]?>" cols="10" rows="3"  dir="auto" id="custom-field-<?php echo $field["id"] ?>" class="form-control" field-type="<?php $field["type"] ?>><?php
                echo str_replace("\\r\\n", "<br/>", $field["text_value"]);?></textarea>
                <?php
                break;
            default: echo $field["custom_field"]
                ?>
                                            </div>
         </div>
<?php
    }
}
?></div>
