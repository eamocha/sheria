<?php

echo "<head>\r\n    <meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />\r\n</head>\r\n<body class=\"license-error-container\">\r\n    <br>\r\n    <br>\r\n    <div>\r\n        <h2 class=\"license-error-header\">";
echo $this->lang->line("App4Legal");
echo "</h2>\r\n        <h3 class=\"license-error-message\">\r\n            ";
echo $error;
echo "        </h3>\r\n        <p><a href=\"";
echo base_url();
echo "\" class=\"button\">";
echo $this->lang->line("try_again");
echo "</a></p>\r\n    </div>\r\n    <br>\r\n    <div class=\"footer\">\r\n        <div>\r\n            <span>";
echo $this->lang->line("footer_all_rights_reserved");
echo " &copy; " . date("Y") . " - Sheria360";
echo "</span>\r\n        </div>\r\n    </div>\r\n</body>\r\n<style>\r\n.button {\r\n    background-color: #4CAF50;\r\n    border: none;\r\n    color: white;\r\n    padding: 15px 32px;\r\n    text-align: center;\r\n    text-decoration: none;\r\n    display: inline-block;\r\n    font-size: 20px;\r\n    margin: 4px 2px;\r\n    cursor: pointer;\r\n}\r\n.button:hover {\r\n    background-color: #68DB6D;\r\n}\r\n.license-error-container {\r\n    text-align: center;\r\n    margin: 0;\r\n}\r\n.license-error-header{\r\n    font-size: 40px;\r\n}\r\n.license-error-message {\r\n    color:#CE2D2D;\r\n    font-size: 25px;\r\n}\r\n.footer {\r\n    font-size: 13px;\r\n}\r\nul {\r\n    list-style-type: none;\r\n    margin: 0;\r\n    padding: 0;\r\n    overflow: hidden;\r\n    background-color: white;\r\n    border: 1px solid #e7e7e7;\r\n}\r\nli {\r\n    float: left;\r\n}\r\nli a {\r\n    font-size: 18px;\r\n    display: block;\r\n    color: #777;\r\n    text-align: center;\r\n    padding: 14px 16px;\r\n    text-decoration: none;\r\n}\r\nli a:hover, li:hover {\r\n    cursor: default;\r\n}\r\n.go-back-link {\r\n    color: #777;\r\n    text-decoration: none;\r\n}\r\n.go-back-link:hover {\r\n    color: #c0bebe;\r\n}\r\n</style>\r\n";

?>