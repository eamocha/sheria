<select name="model_type[]" class="model-type-selectize" placeholder="<?php echo $this->lang->line("select"); ?>" id="model-type" multiple="multiple" tabindex="-1">
    <?php if (isset($cf_model_types)): ?>
        <?php foreach ($model_types as $type): ?>
            <?php if (in_array($type["id"], $cf_model_types)): ?>
                <option selected="selected" value="<?php echo $type["id"]; ?>">
                    <?php echo $type["name"]; ?>
                </option>
            <?php endif; ?>
        <?php endforeach; ?>
    <?php endif; ?>
</select>

<div data-field="type_id" class="inline-error hide padding-5"></div>

<script>
    var availableTypes = <?php echo json_encode($model_types); ?>;
    initializeSelectizeFields(jQuery('.model-type-selectize', '#custom-field-container'), availableTypes);
</script>
