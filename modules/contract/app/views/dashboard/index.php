<div id="contract-dashboard" class="dashboard">
    <div class="row no-margin">

        <!-- Received Contracts This Month -->
        <div class="col-lg-4 col-md-6">
            <div class="col-md-12 widget card">
                <div class="col-md-12 widget-header">
                    <div class="widget-header-title">
                        <i class="fa fa-inbox p-5-important"></i>
                        <span><?= $this->lang->line("contract_received_this_month") ?></span>
                    </div>
                    <div id="received-contract-count" class="widget-total-data float-right"></div>
                </div>
                <div class="col-md-12 list-widget p-0">
                    <ul class="p-0 no-margin" id="received-contract-list"></ul>
                    <a class="col-md-12 btn widget-see-all-btn" href="<?= app_url("modules/contract/") ?>">
                        <p class="float-left"><?= $this->lang->line("view_all_records") ?></p>
                        <i class="float-right fa fa-angle-double-<?= $this->session->userdata("AUTH_language") == "english" ? "right" : "left" ?>"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Expiring This Quarter -->
        <div class="col-lg-4 col-md-6">
            <div class="col-md-12 widget card">
                <div class="col-md-12 widget-header">
                    <div class="widget-header-title">
                        <i class="fa fa-exclamation-triangle p-5-important"></i>
                        <span><?= $this->lang->line("expiring_contract_this_quarter") ?></span>
                    </div>
                    <div id="quarter-expired-contract-count" class="widget-total-data float-right"></div>
                </div>
                <div class="col-md-12 list-widget p-0">
                    <ul class="p-0 no-margin" id="quarter-expired-contract-list"></ul>
                    <a class="col-md-12 btn widget-see-all-btn" href="<?= app_url("modules/contract/") ?>">
                        <p class="float-left"><?= $this->lang->line("view_all_records") ?></p>
                        <i class="float-right fa fa-angle-double-<?= $this->session->userdata("AUTH_language") == "english" ? "right" : "left" ?>"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Expiring next Quarter -->
        <div class="col-lg-4 col-md-6">
            <div class="col-md-12 widget card">
                <div class="col-md-12 widget-header">
                    <div class="widget-header-title">
                        <i class="fa fa-exclamation-triangle p-5-important"></i>
                        <span><?= $this->lang->line("expiring_contract_next_quarter") ?></span>
                    </div>
                    <div id="next-quarter-expired-contract-count" class="widget-total-data float-right"></div>
                </div>
                <div class="col-md-12 list-widget p-0">
                    <ul class="p-0 no-margin" id="next-quarter-expired-contract-list"></ul>
                    <a class="col-md-12 btn widget-see-all-btn" href="<?= app_url("modules/contract/") ?>">
                        <p class="float-left"><?= $this->lang->line("view_all_records") ?></p>
                        <i class="float-right fa fa-angle-double-<?= $this->session->userdata("AUTH_language") == "english" ? "right" : "left" ?>"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row no-margin">

        <!-- Contracts Per Status -->
        <div class="col-lg-4 col-md-6">
            <div class="col-md-12 widget card object-per-status-widget">
                <div class="widget-top row">
                    <div class="col-md-12">
                        <h5><?= $this->lang->line("contract_per_status") ?></h5>
                    </div>
                    <div class="row col-md-12 no-margin">
                        <div class="col-md-6 pr-0 text-center">
                            <label><?= $this->lang->line("contract_type") ?></label>
                            <?= form_dropdown("type", $contract_types, "", "class='form-control select-picker' id='contract-type' onchange='return contractsPerStatusPieCharts();' data-size='" . $this->session->userdata("max_drop_down_length") . "'") ?>
                        </div>
                        <div class="col-md-3 pr-0 text-center">
                            <label><?= $this->lang->line("year") ?></label>
                            <select id="contract-year" name="year" class="form-control select-picker" onchange="return contractsPerStatusPieCharts();" data-size="<?= $this->session->userdata("max_drop_down_length") ?>">
                                <option value="0"><?= $this->lang->line("all") ?></option>
                                <?php
                                $year = 2005;
                                for ($count = 0; $count <= 40; $count++, $year++) {
                                    $selected = ($year == date("Y")) ? "selected" : "";
                                    echo "<option value=\"$year\" $selected>$year</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row-fluid text-center">
                    <span class="loader-submit"></span>
                </div>
                <div class="row-fluid">
                    <div id="pie-chart1"></div>
                </div>
            </div>
        </div>

        <!-- Contracts Per Party -->
        <div class="col-lg-4 col-md-6">
            <div class="col-md-12 widget card object-per-party-widget">
                <div class="widget-top row">
                    <div class="col-md-12">
                        <h5><?= $this->lang->line("contract_by_supplier_consultants") ?></h5>
                    </div>
                    <div class="row col-md-12 no-margin">
                        <div class="col-md-6 pr-0 text-center">
                            <label><?= $this->lang->line("contract_type") ?></label>
                            <?= form_dropdown("type", $contract_types, "", "class='form-control select-picker' id='contract-type' onchange='return contractsPerPartyPieCharts();' data-size='" . $this->session->userdata("max_drop_down_length") . "'") ?>
                        </div>
                        <div class="col-md-3 pr-0 text-center">
                            <label><?= $this->lang->line("year") ?></label>
                            <select id="contract-year" name="year" class="form-control select-picker" onchange="return contractsPerPartyPieCharts();" data-size="<?= $this->session->userdata("max_drop_down_length") ?>">
                                <option value="0"><?= $this->lang->line("all") ?></option>
                                <?php
                                $year = 2005;
                                for ($count = 0; $count <= 40; $count++, $year++) {
                                    $selected = ($year == date("Y")) ? "selected" : "";
                                    echo "<option value=\"$year\" $selected>$year</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row-fluid text-center">
                    <span class="loader-submit"></span>
                </div>
                <div class="row-fluid">
                    <div id="pie-chart2"></div>
                </div>
            </div>
        </div>

        <!-- Contracts Per Department -->
        <div class="col-lg-4 col-md-6">
            <div class="col-md-12 widget card object-per-department-widget">
                <div class="widget-top row">
                    <div class="col-md-12">
                        <h5><?= $this->lang->line("contracts_per_department") ?></h5>
                    </div>
                    <div class="row col-md-12 no-margin">
                        <div class="col-md-6 pr-0 text-center">
                            <label><?= $this->lang->line("contract_type") ?></label>
                            <?= form_dropdown("type", $contract_types, "", "class='form-control select-picker' id='contract-type' onchange='return contractsPerDepartmentPieCharts();' data-size='" . $this->session->userdata("max_drop_down_length") . "'") ?>
                        </div>
                        <div class="col-md-3 pr-0 text-center">
                            <label><?= $this->lang->line("year") ?></label>
                            <select id="contract-year" name="year" class="form-control select-picker" onchange="return contractsPerDepartmentPieCharts();" data-size="<?= $this->session->userdata("max_drop_down_length") ?>">
                                <option value="0"><?= $this->lang->line("all") ?></option>
                                <?php
                                $year = 2005;
                                for ($count = 0; $count <= 40; $count++, $year++) {
                                    $selected = ($year == date("Y")) ? "selected" : "";
                                    echo "<option value=\"$year\" $selected>$year</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row-fluid text-center">
                    <span class="loader-submit"></span>
                </div>
                <div class="row-fluid">
                    <div id="pie-chart3"></div>
                </div>
            </div>
        </div>

    </div>

    <div class="row no-margin">
        <!-- Contracts Per Value -->
        <div class="col-lg-6 col-md-6">
            <div class="col-md-12 widget card">
                <div class="widget-top">
                    <div class="col-md-12">
                        <h5><?= $this->lang->line("contract_per_value") ?></h5>
                    </div>
                    <div id="contracts-per-value"></div>
                </div>
            </div>
        </div>
    </div>
</div>
