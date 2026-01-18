<?php
if (!empty($custom_fields["people"])) {
    echo "    <div id=\"custom-fields-section-2\">\r\n        <ul class=\"section-details\">\r\n            ";
    foreach ($custom_fields["people"] as $field) {
        echo "                <li class=\"people-details\">\r\n                    <dl>\r\n                        <dt>";
        echo $field["customName"];
        echo ":</dt>\r\n                        <dd>\r\n                            <span id=\"assignee-val\">\r\n                                <span class=\"user-hover\">\r\n                                    ";
        echo $field["value"] ? implode(",", $field["value"]) : $this->lang->line("none");
        echo "                                </span>                                    \r\n                            </span>\r\n                        </dd>\r\n                    </dl>   \r\n                </li>\r\n            ";
    }
    echo "        </ul>\r\n    </div>\r\n";
}

?>