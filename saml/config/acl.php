<?php

$config = ["adminlist" => [], "example-simple" => [["allow", "equals", "mail", "admin1@example.org"],
    ["allow", "equals", "mail", "admin2@example.org"]],
    "example-deny-some" => [["deny", "equals", "mail", "eviluser@example.org"],
        ["allow"]], "example-maildomain" => [["allow", "equals-preg", "mail", "/@example\\.org\$/"]],
    "example-allow-employees" => [["allow", "has", "eduPersonAffiliation", "employee"]],
    "example-allow-employees-not-students" => [["deny", "has", "eduPersonAffiliation", "student"],
        ["allow", "has", "eduPersonAffiliation", "employee"]],
    "example-deny-student-except-one" => [["deny", "and", ["has", "eduPersonAffiliation", "student"], ["not", "equals", "mail", "user@example.org"]],
        ["allow"]], "example-allow-or" => [["allow", "or", ["equals", "eduPersonAffiliation", "student", "member"], ["equals", "mail", "someuser@example2.org"]]], "example-allow-all" => [["allow"]]];

?>