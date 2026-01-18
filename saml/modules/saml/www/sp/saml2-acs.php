<?php

if(!array_key_exists("PATH_INFO", $_SERVER)) {
    throw new SimpleSAML\Error\BadRequest("Missing authentication source ID in assertion consumer service URL");
}
$sourceId = substr($_SERVER["PATH_INFO"], 1);
$source = SimpleSAML\Auth\Source::getById($sourceId, "\\SimpleSAML\\Module\\saml\\Auth\\Source\\SP");
$spMetadata = $source->getMetadata();
try {
    $b = SAML2\Binding::getCurrentBinding();
} catch (Exception $e) {
    if($e->getMessage() === "Unable to find the current binding.") {
        throw new SimpleSAML\Error\Error("ACSPARAMS", $e, 400);
    }
    throw $e;
}
if($b instanceof SAML2\HTTPArtifact) {
    $b->setSPMetadata($spMetadata);
}
$response = $b->receive();
if(!$response instanceof SAML2\Response) {
    throw new SimpleSAML\Error\BadRequest("Invalid message received to AssertionConsumerService endpoint.");
}
$issuer = $response->getIssuer();
if($issuer === NULL) {
    foreach ($response->getAssertions() as $a) {
        if($a instanceof SAML2\Assertion) {
            $issuer = $a->getIssuer();
            if($issuer === NULL) {
                throw new Exception("Missing <saml:Issuer> in message delivered to AssertionConsumerService.");
            }
        }
    }
}
$idp = $issuer;
if($issuer instanceof SAML2\XML\saml\Issuer) {
    $idp = $idp->getValue();
}
$session = SimpleSAML\Session::getSessionFromRequest();
$prevAuth = $session->getAuthData($sourceId, "saml:sp:prevAuth");
if($prevAuth !== NULL && $prevAuth["id"] === $response->getId() && $prevAuth["issuer"] === $idp) {
    SimpleSAML\Logger::info("Duplicate SAML 2 response detected - ignoring the response and redirecting the user to the correct page.");
    if(isset($prevAuth["redirect"])) {
        SimpleSAML\Utils\HTTP::redirectTrustedURL($prevAuth["redirect"]);
    }
    SimpleSAML\Logger::info("No RelayState or ReturnURL available, cannot redirect.");
    throw new SimpleSAML\Error\Exception("Duplicate assertion received.");
}
$idpMetadata = [];
$state = NULL;
$stateId = $response->getInResponseTo();
if(!empty($stateId)) {
    try {
        $state = SimpleSAML\Auth\State::loadState($stateId, "saml:sp:sso");
    } catch (Exception $e) {
        SimpleSAML\Logger::warning("Could not load state specified by InResponseTo: " . $e->getMessage() . " Processing response as unsolicited.");
    }
}
if($state) {
    assert(array_key_exists("saml:sp:AuthId", $state), "assert(array_key_exists('saml:sp:AuthId', \$state))");
    if($state["saml:sp:AuthId"] !== $sourceId) {
        throw new SimpleSAML\Error\Exception("The authentication source id in the URL does not match the authentication source which sent the request.");
    }
    assert(array_key_exists("ExpectedIssuer", $state), "assert(array_key_exists('ExpectedIssuer', \$state))");
    if($state["ExpectedIssuer"] !== $idp) {
        $idpMetadata = $source->getIdPMetadata($idp);
        $idplist = $idpMetadata->getArrayize("IDPList", []);
        if(!in_array($state["ExpectedIssuer"], $idplist, true)) {
            SimpleSAML\Logger::warning("The issuer of the response not match to the identity provider we sent the request to.");
        }
    }
} else {
    $state = ["saml:sp:isUnsolicited" => true, "saml:sp:AuthId" => $sourceId, "saml:sp:RelayState" => SimpleSAML\Utils\HTTP::checkURLAllowed($spMetadata->getString("RelayState", $response->getRelayState()))];
}
SimpleSAML\Logger::debug("Received SAML2 Response from " . var_export($idp, true) . ".");
if(empty($idpMetadata)) {
    $idpMetadata = $source->getIdPmetadata($idp);
}
try {
    $assertions = SimpleSAML\Module\saml\Message::processResponse($spMetadata, $idpMetadata, $response);
} catch (SimpleSAML\Module\saml\Error $e) {
    $e = $e->toException();
    SimpleSAML\Auth\State::throwException($state, $e);
}
$authenticatingAuthority = NULL;
$nameId = NULL;
$sessionIndex = NULL;
$expire = NULL;
$attributes = [];
$foundAuthnStatement = false;
foreach ($assertions as $assertion) {
    $store = SimpleSAML\Store::getInstance();
    if($store !== false) {
        $aID = $assertion->getId();
        if($store->get("saml.AssertionReceived", $aID) !== NULL) {
            $e = new SimpleSAML\Error\Exception("Received duplicate assertion.");
            SimpleSAML\Auth\State::throwException($state, $e);
        }
        $notOnOrAfter = $assertion->getNotOnOrAfter();
        if($notOnOrAfter === NULL) {
            $notOnOrAfter = time() + 86400;
        } else {
            $notOnOrAfter += 60;
        }
        $store->set("saml.AssertionReceived", $aID, true, $notOnOrAfter);
    }
    if($authenticatingAuthority === NULL) {
        $authenticatingAuthority = $assertion->getAuthenticatingAuthority();
    }
    if($nameId === NULL) {
        $nameId = $assertion->getNameId();
    }
    if($sessionIndex === NULL) {
        $sessionIndex = $assertion->getSessionIndex();
    }
    if($expire === NULL) {
        $expire = $assertion->getSessionNotOnOrAfter();
    }
    $attributes = array_merge($attributes, $assertion->getAttributes());
    if($assertion->getAuthnInstant() !== NULL) {
        $foundAuthnStatement = true;
    }
}
if(!$foundAuthnStatement) {
    $e = new SimpleSAML\Error\Exception("No AuthnStatement found in assertion(s).");
    SimpleSAML\Auth\State::throwException($state, $e);
}
if($expire !== NULL) {
    $logoutExpire = $expire;
} else {
    $logoutExpire = time() + 86400;
}
if(!empty($nameId)) {
    SimpleSAML\Module\saml\SP\LogoutStore::addSession($sourceId, $nameId, $sessionIndex, $logoutExpire);
    $logoutState = ["saml:logout:Type" => "saml2", "saml:logout:IdP" => $idp, "saml:logout:NameID" => $nameId, "saml:logout:SessionIndex" => $sessionIndex];
    $state["saml:sp:NameID"] = $nameId;
} else {
    $logoutState = ["saml:logout:Type" => "saml1"];
}
$state["LogoutState"] = $logoutState;
$state["saml:AuthenticatingAuthority"] = $authenticatingAuthority;
$state["saml:AuthenticatingAuthority"][] = $idp;
$state["PersistentAuthData"][] = "saml:AuthenticatingAuthority";
$state["saml:AuthnInstant"] = $assertion->getAuthnInstant();
$state["PersistentAuthData"][] = "saml:AuthnInstant";
$state["saml:sp:SessionIndex"] = $sessionIndex;
$state["PersistentAuthData"][] = "saml:sp:SessionIndex";
$state["saml:sp:AuthnContext"] = $assertion->getAuthnContextClassRef();
$state["PersistentAuthData"][] = "saml:sp:AuthnContext";
if($expire !== NULL) {
    $state["Expire"] = $expire;
}
$state["saml:sp:prevAuth"] = ["id" => $response->getId(), "issuer" => $idp];
if(isset($state["\\SimpleSAML\\Auth\\Source.ReturnURL"])) {
    $state["saml:sp:prevAuth"]["redirect"] = $state["\\SimpleSAML\\Auth\\Source.ReturnURL"];
} elseif(isset($state["saml:sp:RelayState"])) {
    $state["saml:sp:prevAuth"]["redirect"] = $state["saml:sp:RelayState"];
}
$state["PersistentAuthData"][] = "saml:sp:prevAuth";
$source->handleResponse($state, $idp, $attributes);
assert(false, "assert(false)");

?>