<?php



if (!empty($custom_fields["main"])) {
    echo "<div id=\"custom-fields-section-1\">\r\n    <ul class=\"property-list\">\r\n        ";
    foreach ($custom_fields["main"] as $field) {
        echo "        <li class=\"item\">\r\n            <div class=\"wrap\">\r\n                <strong class=\"name\">";
        echo $field["customName"];
        echo ":</strong>\r\n                <span class=\"value\">\r\n                    ";
        if ($field["type"] == "long_text" && $field["text_value"]) {
            echo "                    ";
            echo str_replace("\\r\\n", "<br/>", $field["text_value"]);
            echo "                    ";
        } else {
            echo "                    ";
            echo $field["text_value"] ?? $this->lang->line("none");
            echo "                    ";
        }
        echo "                </span>\r\n            </div>\r\n        </li>  \r\n        ";
    }
    echo "    </ul>\r\n</div>\r\n";
}

?>