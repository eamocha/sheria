<?php
if (!strcmp($module_controller, "case_containers")) {
?>
<div class="row no-margin d-none" id="caseContainerContainer">
    <?php echo form_input(["name" => "id", "value" => $id, "id" => "id", "type" => "hidden"]); ?>
    <div class="col-md-12 form-group" id="container-display-fields"></div>
    <div class="col-md-12 no-padding">
        <?php $this->load->view("partial/tabs_subnav", $tabsNLogs); ?>
        <div class="col-md-12 dms-grid-container dms-grid-matter-container">
            <?php
            } else {
            if (isset($tabsNLogs) && is_array($tabsNLogs)) {
            ?>
            <div class="row primary-style" id="edit-legal-case-documents">
                <?php
                if ($legalCase["category"] == "IP") {
                    $this->load->view("intellectual_properties/top_nav", ["is_edit" => 1, "main_tab" => false]);
                } else {
                    $this->load->view("cases/top_nav", ["is_edit" => 1, "main_tab" => false]);
                }
                ?>
                <div class="main-offcanvas main-offcanvas-left">
                    <?php $this->load->view("partial/tabs_subnav_vertical", $tabsNLogs); ?>
                    <div class="resp-main-body-width-70 no-padding flex-scroll-auto" id="main-content-side">
                        <div class="main-content-section main-grid-container">
                            <?php
                            if ($legalCase["category"] == "IP") {
                                $this->load->view("intellectual_properties/object_header");
                            } else {
                                $this->load->view("cases/object_header");
                            }
                            ?>
                            <?php
                            }
                            }

                            $integrations = $integrations ?? [];
                            $module_record_id = isset($module_record_id) ? $module_record_id : NULL;
                            if ($module === "doc") {
                                $module_record_id = $root_folder_id;
                            } else {
                                $module_record_id = (int) $module_record_id;
                            }

                            if (strcmp($module_controller, "case_containers")) {
                            ?>
                            <div class="col-md-12 dms-grid-container">
                                <?php
                                }
                                ?>
                                <?php
                                echo form_open("", "name=\"documentsForm\" id=\"documentsForm\" method=\"post\" class=\"form-horizontal\" role=\"form\"");
                                echo form_input(["id" => "module", "name" => "module", "value" => $module, "type" => "hidden"]);
                                echo form_input(["id" => "module-record-id", "name" => "module_record_id", "value" => $module_record_id, "type" => "hidden"]);
                                echo form_input(["id" => "lineage", "name" => "lineage", "type" => "hidden"]);
                                echo form_input(["id" => "term", "name" => "term", "type" => "hidden"]);

                                $visibleToCP = false;
                                if (isset($isCustomerPortal) || isset($visibleToCustomerPortal)) {
                                    $visibleToCP = ($isCustomerPortal == "yes" || $visibleToCustomerPortal == "yes");
                                }
                                echo form_input([
                                        "name" => "visibleToCP",
                                        "id" => "visibleToCP",
                                        "value" => $visibleToCP,
                                        "type" => "hidden"
                                ]);
                                echo form_close();

                                if (!strcmp($module_controller, "cases")) {
                                    $this->load->view($module_controller . "/hard_copies");
                                } else {
                                    if (!strcmp($module_controller, "contacts")) {
                                        $tabsNLogs["contact_full_info"] = false;
                                        $this->load->view("contacts/logs", $tabsNLogs);
                                    }
                                }
                                ?>
                                <div class="col-12">
                                    <div id="documentsTabs">
                                        <ul>
                                            <?php
                                            foreach ($integration_settings["tabs_list"] as $tab_k => $tab_v) {
                                                $activeClass = ($tab_k == 1) ? "k-state-active" : "";
                                                if ($tab_v === "A4L") {
                                                    $tabName = isset($A4L_doc_tab_name) ? $A4L_doc_tab_name : $this->lang->line("attachments");
                                                    echo "<li class=\"{$activeClass}\">{$tabName}</li>";
                                                } else {
                                                    $tabName = $this->lang->line($tab_v);
                                                    echo "<li class=\"{$activeClass}\">{$tabName}</li>";
                                                }
                                            }

                                            $urlClass = $urlGrid ? "" : "d-none";
                                            echo "<li class=\"{$urlClass}\">" . $this->lang->line("urls") . "</li>";

                                            foreach ($integrations as $integration) {
                                                echo "<li class=\"integration-tab\" data-integration-code=\"{$integration["code"]}\">{$integration["name"]}</li>";
                                            }
                                            ?>
                                        </ul>

                                        <?php
                                        foreach ($integration_settings["tabs_list"] as $tab_k => $tab_v) {
                                            if ($tab_v === "A4L") {
                                                $rtlClass = ($this->session->userdata("AUTH_language") == "arabic") ? "k-rtl" : "";
                                                $crumbText = ($crumbParent == "Documents") ? $this->lang->line("documents") : $crumbParent;
                                                ?>
                                                <div class="col-md-12 attachments">
                                                    <div id="documentsContainer">
                                                        <div id="dropzone-container">
                                                            <ul class="breadcrumb no-margin-bottom" id="BreadcrumbContainer">
                                                                <li class="breadcrumb-item active bold fixed">
                                                                    <a href="javascript:;" onclick="openFolder('');"><?php echo $crumbText; ?></a>
                                                                </li>
                                                            </ul>
                                                            <div id="dragAndDrop" class="container dragAndDrop d-none" style="margin: 6em 0 0;"></div>
                                                            <div class="<?php echo $rtlClass; ?>">
                                                                <div id="documentsGrid"></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <?php
                                            } else {
                                                echo form_open("", "name=\"integrationDocumentForm_{$tab_v}\" id=\"integrationDocumentsForm_{$tab_v}\" method=\"post\" class=\"form-horizontal\" role=\"form\"");
                                                echo form_input(["id" => "module", "name" => "module", "value" => $module, "type" => "hidden"]);
                                                echo form_input(["id" => "module-record-id", "name" => "module_record_id", "value" => "", "type" => "hidden"]);
                                                $lineageValue = isset($integration_settings["model_lineage"]) ? $integration_settings["model_lineage"] : "";
                                                echo form_input(["id" => "lineage", "name" => "lineage", "value" => $lineageValue, "type" => "hidden"]);
                                                echo form_input(["id" => "term", "name" => "term", "type" => "hidden"]);
                                                echo form_input(["id" => "provider", "name" => "provider", "type" => "hidden"]);
                                                echo form_close();

                                                $rtlClass = ($this->session->userdata("AUTH_language") == "arabic") ? "k-rtl" : "";
                                                ?>
                                                <div class="col-md-12 attachments" id="integrationDocumentsContainer_<?php echo $tab_v; ?>">
                                                    <ul class="breadcrumb no-margin-bottom" id="integrationBreadcrumbContainer_<?php echo $tab_v; ?>">
                                                        <?php if ($integration_settings["model"] != "doc") { ?>
                                                            <li class="active bold fixed">Sheria360</li>
                                                        <?php } else { ?>
                                                            <li class="active bold fixed">
                                                                <a href="javascript:;" onclick="integrationLoadDirectoryContent('<?php echo $lineageValue; ?>', '<?php echo $tab_v; ?>');">Sheria360</a>
                                                            </li>
                                                        <?php } ?>
                                                    </ul>
                                                    <div id="dragAndDrop" class="container dragAndDrop d-none" style="margin: 6em 0 0;"></div>
                                                    <div class="<?php echo $rtlClass; ?>">
                                                        <div id="integrationDocumentsGrid_<?php echo $tab_v; ?>"></div>
                                                    </div>
                                                </div>
                                                <?php
                                            }
                                        }
                                        ?>

                                        <div class="col-md-12 urls">
                                            <?php $this->load->view($module_controller . "/urls_hidden_form", ["modelId" => $module_record_id]); ?>
                                            <div class="<?php echo ($this->session->userdata("AUTH_language") == "arabic") ? "k-rtl" : ""; ?>">
                                                <div id="urlGrid"></div>
                                            </div>
                                        </div>

                                        <?php
                                        foreach ($integrations as $integration) {
                                            ?>
                                            <div class="col-md-12">
                                                <div class="iframe-container">
                                                    <iframe id="app4legal-<?php echo $integration["code"]; ?>-iframe"
                                                            name="app4legal-<?php echo $integration["code"]; ?>-iframe"
                                                            allow="clipboard-write"
                                                            frameborder="0"
                                                            width="100%"
                                                            height="100%">
                                                    </iframe>
                                                </div>
                                            </div>
                                            <?php
                                        }
                                        ?>
                                    </div>
                                </div>

                                <div class="d-none" id="documentFolderContainer">
                                    <?php $this->load->view("documents_management_system/folder_form"); ?>
                                </div>

                                <?php
                                if (isset($this->instance_data_array["installationType"]) && $this->instance_data_array["installationType"] == "on-server") {
                                    ?>
                                    <div class="d-none" id="directoryDialog">
                                        <?php $this->load->view("documents_management_system/directory_add", compact("data")); ?>
                                    </div>
                                    <?php
                                }
                                ?>

                                <div class="d-none" id="attachmentDialog">
                                    <?php $this->load->view("documents_management_system/attachment_add", compact("data")); ?>
                                </div>

                                <div class="d-none" id="integrationAttachmentDialog">
                                    <?php $this->load->view("integrations/document_attachment_add", compact("data")); ?>
                                </div>

                                <div class="d-none" id="pathForm">
                                    <?php $this->load->view("documents_management_system/path_form", compact("data")); ?>
                                </div>

                                <div class="d-none" id="documentDialog">
                                    <?php $this->load->view("documents_management_system/document_add", compact("data")); ?>
                                </div>

                                <div class="d-none" id="sharedWithDialog"></div>

                                <iframe id="hidden_upload" name="hidden_upload" src="" width="0" height="0" class="d-none"></iframe>
                                <object id="winFirefoxPlugin" type="application/x-sharepoint" width="0" height="0" style="visibility: hidden;"></object>
                                <div class="clear clearfix clearfloat">&nbsp;</div>
                                <div id="versions-list-container" class="container col-md-12 d-none"></div>

                                <?php
                                $this->load->view("documents_management_system/document_editor_modal", []);
                                $this->load->view("documents_management_system/document_editor_installation_modal", []);
                                ?>

                                <script type="text/javascript">
                                    <?php
                                    if (!empty($documentTypes)) {
                                        $docTypes = [];
                                        foreach ($documentTypes as $value => $text) {
                                            $docTypes[] = "{value: " . (empty($value) ? "''" : $value) . ", text: '" . addslashes($text) . "'}";
                                        }
                                        echo "var moduleDocumentTypeValues = [" . implode(", ", $docTypes) . "];";
                                    } else {
                                        echo "var moduleDocumentTypeValues = \"\";";
                                    }

                                    if (!empty($documentStatuses)) {
                                        $docStatuses = [];
                                        foreach ($documentStatuses as $value => $text) {
                                            $docStatuses[] = "{value: " . (empty($value) ? "''" : $value) . ", text: '" . addslashes($text) . "'}";
                                        }
                                        echo "var moduleDocumentStatusValues = [" . implode(", ", $docStatuses) . "];";
                                    } else {
                                        echo "var moduleDocumentStatusValues = \"\";";
                                    }

                                    if (isset($pathTypes)) {
                                        $pType = [];
                                        foreach ($pathTypes as $value => $text) {
                                            $pType[] = "{value:'" . addslashes($value) . "', text:'" . addslashes($text) . "'}";
                                        }
                                        echo "var moduleDocumentPathTypeValues= [" . implode(", ", $pType) . "];";
                                    }
                                    ?>
                                    var allowedUploadSizeMegabite = '<?php echo $this->config->item("allowed_upload_size_megabite"); ?>';
                                    var moduleController = '<?php echo $module_controller; ?>';
                                    var installationType = '<?php echo $this->instance_data_array["installationType"]; ?>';
                                    var module = '<?php echo $module; ?>';
                                    var loadedIntegrationProviders = [<?php echo "\"" . implode("\",\"", $integration_settings["tabs_list"]) . "\""; ?>];
                                    var modelName = '<?php echo $integration_settings["model"]; ?>';
                                    var displayIntegrationButton = '<?php echo count($integration_settings["tabs_list"]) == 1 && $integration_settings["model"]; ?>';
                                    var modelLineage = '<?php echo isset($integration_settings["model_lineage"]) ? $integration_settings["model_lineage"] : ""; ?>';
                                    var moduleRecordId = '<?php echo $module_record_id; ?>';
                                    var viewableExtensions = <?php echo json_encode($this->document_management_system->viewable_documents_extensions); ?>;
                                    var isCloudInstance = <?php echo $this->db->dbdriver === "sqlsrv" ? "false" : "true"; ?>;
                                    var documentEditorDownloadURL = '<?php echo !empty($this->document_editor_download_url) ? $this->document_editor_download_url : ""; ?>';
                                    var showDocumentEditorInstallationModal = <?php echo isset($show_document_editor_installation_modal) ? "true" : "false"; ?>;
                                </script>

                                <?php
                                if (!strcmp($module_controller, "case_containers")) {
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
                } else {
                ?>
            </div>
        </div>
    </div>
</div>
    </div>
<?php
}
?>

<script>
    var disableMatter = '<?php echo isset($legalCase["archived"]) && $legalCase["archived"] == "yes" && isset($systemPreferences["disableArchivedMatters"]) && $systemPreferences["disableArchivedMatters"] ? true : false; ?>';
</script>