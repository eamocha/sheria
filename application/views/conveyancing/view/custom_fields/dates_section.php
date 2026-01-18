<?php



if (!empty($custom_fields["date"])) {
    echo "<div id=\"custom-fields-section-3\">\r\n    <ul class=\"section-details\">\r\n        ";
    foreach ($custom_fields["date"] as $field) {
        echo "          <li>\r\n                <dl class=\"dates\">\r\n                    <dt>";
        echo $field["customName"];
        echo ":</dt>\r\n                    <dd class=\"date\">\r\n                        <span>\r\n                            ";
        echo $field["date_value"] ? $field["date_value"] . ($field["type"] === "date_time" && $field["time_value"] ? " - " . $field["time_value"] : "") : $this->lang->line("none");
        echo "                                                                                  \r\n                        </span>\r\n                    </dd>\r\n                </dl>\r\n            </li>\r\n        ";
    }
    echo "    </ul>\r\n</div>\r\n";
}

?>