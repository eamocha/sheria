<?php

echo "<div class=\"modal fade\" tabindex=\"-1\" role=\"dialog\">\r\n    <div class=\"modal-dialog\" role=\"document\">\r\n        <div class=\"modal-content\">\r\n            <div class=\"modal-header\">\r\n                <h5 class=\"modal-title\">";
echo $this->lang->line("view_transitions");
echo "</h5>\r\n                <button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-label=\"Close\">\r\n                    <span aria-hidden=\"true\">&times;</span>\r\n                </button>\r\n            </div>\r\n            <div class=\"modal-body\">\r\n                ";
if (!empty($transitions)) {
    $specialCharacters = [">", "<", "-", ":", "(", ")", "[", "]", "|"];
    $from_step_name = str_replace("{", "", $from_step_name);
    $from_step_name = str_replace("}", "", $from_step_name);
    $transitionOriginalValues = $transitions;
    foreach ($transitions as $transitionKey => $transitionVal) {
        $transitions[$transitionKey]["name"] = str_replace("{", "", $transitions[$transitionKey]["name"]);
        $transitions[$transitionKey]["to_status_name"] = str_replace("}", "", $transitions[$transitionKey]["to_status_name"]);
    }
    foreach ($specialCharacters as $char) {
        $from_step_name = str_replace($char, "{{" . $char . "}}", $from_step_name);
        foreach ($transitions as $transitionKey => $transitionVal) {
            $transitions[$transitionKey]["name"] = str_replace($char, "{{" . $char . "}}", $transitions[$transitionKey]["name"]);
            $transitions[$transitionKey]["to_status_name"] = str_replace($char, "{{" . $char . "}}", $transitions[$transitionKey]["to_status_name"]);
        }
    }
    echo "                    <pre class=\"arrows-and-boxes\">";
    echo "() (" . $from_step_name . " >";
    foreach ($transitionOriginalValues as $transitionKey => $transitionVal) {
        if (7 < strlen($transitionVal["name"])) {
            $transitionName = mb_substr($transitionVal["name"], 0, 7, "utf-8") . "...";
        } else {
            $transitionName = $transitionVal["name"];
        }
        if (count($transitions) == $transitionKey + 1) {
            echo "{{<span title='" . $transitionVal["name"] . "'>" . $transitionName . "</span>}}";
            echo " [" . $transitionVal["id"] . "]";
        } else {
            echo "{{<span title='" . $transitionVal["name"] . "'>" . $transitionName . "</span>}}";
            echo " [" . $transitionVal["id"] . "] >";
        }
    }
    echo ") || ";
    foreach ($transitions as $transition) {
        echo "(" . $transition["id"] . ":" . $transition["to_status_name"] . ") ";
    }
    echo "</pre>";
} else {
    echo "                    <p>";
    echo $this->lang->line("there_are_no_workflow_transitions");
    echo "</p>\r\n                ";
}
echo "            </div>\r\n            <div class=\"modal-footer\">\r\n                <button type=\"button\" class=\"btn btn-primary\" data-dismiss=\"modal\">";
echo $this->lang->line("close");
echo "</button>\r\n            </div>\r\n        </div>\r\n    </div>\r\n</div>";

?>