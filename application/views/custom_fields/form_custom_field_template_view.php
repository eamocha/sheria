<div class="row m-0 col-md-12 box-shadow_container padding-15 main-grid-container custom-fields-container-view">
    <?php foreach ($custom_fields as $field) { ?>
        <div id="<?php echo $field["id"]; ?>-container" class="form-group col-md-6 no-padding">
            <label title="<?php echo $field["customName"]; ?>" class="custom-field-label control-label">
                <?php echo $field["customName"]; ?>
            </label>
            <div>
                <?php
                echo $field["hidden_custom_field_id"];
                echo $field["hidden_value_id"];
                echo $field["hidden_record_id"];
                echo $field["custom_field"];
                ?>
            </div>
        </div>
    <?php } ?>
</div>