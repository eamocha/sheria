<div id="add-screen-form">
    <div class="">
        <div align="center">
            <h3><?php echo $this->contract_cp_screen->get_field("name"); ?></h3>
        </div>
        <div class="col-md-12 m-0 p-0 padding-10 no-padding-top">
            <?php
            $attachment_count = 1;
            foreach ($formHtml as $fieldKey => $fieldData) {
                $fieldName = $screenFields[$fieldKey]["related_field"];
                $hideField = false;
                if ($screenFields[$fieldKey]["isRequired"] == 1 && $screenFields[$fieldKey]["visible"] == 0) {
                    $hideField = true;
                    if ($screenFields[$fieldKey]["requiredDefaultValue"] === "") {
                        $hideField = false;
                    }
                }
                $lookup_multiselect_class = $predefinedFields[$fieldName]["formType"] == "lookup_multiselect" ? $fieldName . "-lookup-container" : "";
                ?>

                <div class="row form-group p-0 mb-10 <?php echo ($hideField ? " d-none " : "") . $lookup_multiselect_class; ?>">
                    <?php if ($fieldName === "provider_group_id" && $screenFields[$fieldKey]["requiredDefaultValue"]) { ?>
                        <?php echo form_input(["type" => "hidden", "class" => "related-assigned-team", "value" => $screenFields[$fieldKey]["requiredDefaultValue"]]); ?>
                    <?php } ?>

                    <?php if ($predefinedFields[$fieldName]["formType"] == "lookup_per_type") { ?>
                        <div id="parties-container" class="col-md-12">
                            <?php echo form_input(["name" => "contract_parties_count", "id" => "parties-count", "value" => "2", "type" => "hidden"]); ?>

                            <!-- Party 1 -->
                            <div class="parties-div" id="parties-1">
                                [Previous party 1 content...]
                            </div>

                            <!-- Party 2 -->
                            <div class="parties-div" id="parties-2">
                                [Previous party 2 content...]
                            </div>

                            <div class="col-md-12 no-padding form-group margin-bottom-5">
                                <div class="col-md-8 p-0 col-xs-10 offset-md-3 add-more-link">
                                    <a href="javascript:;" onclick="objectContainerClone('parties', '#add-screen-form', event);">
                                        <?php echo $this->lang->line("add_more"); ?>
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php } ?>

                    <?php if ($fieldName != "party") { ?>
                        <label class="control-label col-md-3 <?php echo $screenFields[$fieldKey]["visible"] == 1 && $screenFields[$fieldKey]["isRequired"] == 1 ? "required" : ""; ?>">
                            <?php echo $screenFields[$fieldKey]["labelName"]; ?>
                        </label>
                        <div class="no-padding col-md-8" requiredField="<?php echo $screenFields[$fieldKey]["visible"] == 1 && $screenFields[$fieldKey]["isRequired"] == 1 ? "yes" : "no"; ?>">
                            <?php echo $fieldData; ?>
                        </div>
                    <?php } ?>

                    <div data-field="<?php echo $fieldName; ?>" class="inline-error d-none offset-md-3 pb-3"></div>

                    <?php if (in_array($predefinedFields[$fieldName]["formType"], ["lookup", "lookup_multiselect"])) { ?>
                        <div class="no-padding col-md-12 autocomplete-helper">
                            <div class="no-padding-right offset-md-3">
                                <div class="inline-text"><?php echo $this->lang->line("helper_autocomplete"); ?></div>
                            </div>
                        </div>
                    <?php } ?>

                    <div class="p-0 col-md-12">
                        <div class="text-muted pr-0 offset-md-3">
                            <?php echo $screenFields[$fieldKey]["fieldDescription"]; ?>
                        </div>
                    </div>

                    <?php if ($fieldName == "attachment") { ?>
                        <?php echo form_input(["type" => "hidden", "name" => "requiredAttachments[]", "value" => $screenFields[$fieldKey]["isRequired"] ? "attachment_main_" . $attachment_count : ""]); ?>
                        <?php echo form_input(["type" => "hidden", "name" => "attachment[]", "value" => "attachment_main_" . $attachment_count]); ?>
                        <?php $attachment_count++; ?>
                        <div id="more-uploads" class="offset-md-3"></div>
                        <div class="offset-md-3 p-0 col-md-12">
                            <button class="btn btn-link p-0 add-more-link cp-customizable-link" type="button" onclick="addFileInput(event, 'contract')">
                                <?php echo $this->lang->line("add_more"); ?>
                            </button>
                        </div>
                    <?php } ?>
                </div>
            <?php } ?>
            <div class="clear clearfix clearfloat">&nbsp;</div>
        </div>
    </div>
</div>

<script type="text/javascript">
    <?php
    foreach ($formHtml as $fieldKey => $fieldData) {
        $fieldName = $screenFields[$fieldKey]["related_field"];
        switch ($predefinedFields[$fieldName]["formType"]) {
            case "lookup_per_type":
                echo "objectInitialization('parties', jQuery('#add-screen-form'));\n";
                break;
            case "lookup":
                echo "var lookupDetails = {\n";
                echo "    'lookupField': jQuery('#lookup-".$fieldName."', jQuery('#add-screen-form')),\n";
                echo "    'errorDiv': '".$fieldName."',\n";
                echo "    'hiddenId': '#".$fieldName."'\n";
                echo "};\n";
                echo "lookUpCustomerPortalUsers(lookupDetails, jQuery('#add-screen-form'), false, 'contracts');\n";
                break;
            case "lookup_multiselect":
                echo "var lookupDetails = {\n";
                echo "    'lookupField': jQuery('#lookup-".$fieldName."', jQuery('#add-screen-form')),\n";
                echo "    'lookupContainer': '".$fieldName."-lookup-container',\n";
                echo "    'errorDiv': 'lookup".$fieldName."',\n";
                echo "    'boxName': '".$fieldName."',\n";
                echo "    'boxId': '#selected-".$fieldName."',\n";
                echo "    'onSelect': 'handleRequiredMultiselect',\n";
                echo "    'onSelectParameters': {\n";
                echo "        \"fieldName\": '#lookup-".$fieldName."',\n";
                echo "        \"isRequired\": ".$screenFields[$fieldKey]["isRequired"].",\n";
                echo "        \"selectedItemContainer\": '#selected-".$fieldName."'\n";
                echo "    }\n";
                echo "};\n";
                if ($predefinedFields[$fieldName]["customField"]) {
                    echo "lookUpCustomFields(lookupDetails, jQuery('#add-screen-form'), true);\n";
                } else {
                    if ($predefinedFields[$fieldName]["type_data"] == "users") {
                        echo "lookUpA4LUser(lookupDetails, jQuery('#add-screen-form'), true);\n";
                    } else {
                        echo "lookUpCustomerPortalUsers(lookupDetails, jQuery('#add-screen-form'), true, 'contracts');\n";
                    }
                }
                break;
        }
    }
    ?>
</script>