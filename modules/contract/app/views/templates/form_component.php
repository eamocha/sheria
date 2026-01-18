<?php
switch ($field_type) {
    case "short_text":
        echo form_input($field_name, $field_value, $field_extra);
        break;

    case "long_text":
        $data = ["name" => $field_name, "rows" => "3", "cols" => "10"];
        echo form_textarea($data, $field_value, $field_extra);
        break;

    case "list":
        echo form_dropdown($field_name, $field_options, $field_value, $field_extra);
        break;

    case "date":
        echo form_input($field_name, $field_value, $field_extra);
        break;

    case "date_time":
        // Initialize record_id with a default value if not set
        $record_id = $record_id ?? null;
        $date_class = !$record_id ? "col-md-8 col-xs-12 pl-0" : "col-md-12 p-0 mb-5";
        $time_class = !$record_id ? "col-md-4 col-xs-12 pr-0" : "col-md-12 p-0 mt-3";

        echo '<div class="input-group date date-picker '.$date_class.'">';
        echo form_input($field_name["date"], $field_value["date"] ?? "", $field_extra["date"]);
        echo '<div class="input-group-append">
                <span class="input-group-text"><i class="fa-solid fa-calendar-days"></i></span>
              </div>
            </div>
            <div class="input-group time '.$time_class.'">';
        echo form_input($field_name["time"], $field_value["time"] ?? "", $field_extra["time"]);
        echo '</div>';
        break;

    case "lookup":
        echo '<select name="'.$field_name.'" '.$field_extra.'>';
        if (!empty($field_value)) {
            foreach ($field_value as $id => $name) {
                echo '<option selected="selected" value="'.$id.'">'.$name.'</option>';
            }
        }
        echo '</select>';
        break;

    case "single_lookup":
        echo form_input([
            "name" => $field_hidden_name,
            "value" => $field_hidden_value,
            "id" => "hidden-field-".$id,
            "type" => "hidden",
            "class" => "criteria-field"
        ]);
        echo form_input($field_name, $field_value, $field_extra);
        break;

    case "lookup_per_type":
        echo form_input([
            "name" => $field_hidden_name,
            "value" => $field_hidden_value,
            "id" => "hidden-field-".$id,
            "type" => "hidden",
            "class" => "criteria-field"
        ]);
        echo form_dropdown($field_name, $field_options, $field_value[0], $field_extra[0]);
        echo form_input($field_name, $field_value[1], $field_extra[1]);
        break;

    case "multiple_lookup_per_type":
        echo form_input([
            "name" => $field_name[0],
            "value" => $field_value[0],
            $field_extra[0] => "",
            "type" => "hidden"
        ]);
        echo form_dropdown($field_name[1], $field_options, $field_value[1], $field_extra[1]);
        echo form_input($field_name[2], $field_value[2], $field_extra[2]);
        break;

    case "number":
        echo form_input([
            "name" => $field_name,
            "value" => $field_value,
            $field_extra => "",
            "type" => "number"
        ]);
        break;
}
?>