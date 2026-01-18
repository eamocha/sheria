<?php


$metadata['app4legal-azure_ad'] = [

    'entityID' => 'spn:ed59ef7c-f34c-4d48-81bd-30edbda453a8',


    'AssertionConsumerService' => 'https://lexnet.ca.go.ke/saml/module.php/saml/sp/saml2-acs.php/app4legal-azure_ad',


    'SingleLogoutService' => 'https://lexnet.ca.go.ke/saml/module.php/saml/sp/saml2-logout.php/app4legal-azure_ad',


    'NameIDFormat' => 'urn:oasis:names:tc:SAML:2.0:nameid-format:persistent',


    'privatekey' => 'sp-key.pem',

    'certificate' => 'sp-cert.pem',


    // Optional: sign all outgoing requests

    'sign.authnrequest' => true,

];

