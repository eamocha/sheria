<div id="money-settings" class="mt-2 container-fluid p-2">
    <div class="row">
        <div class="col-md-4">
            <div class="home-block card">
                <div class="settings-title">
                    <i class="fa fa-cubes"></i>
                    <h5><?php echo $this->lang->line("organizations"); ?></h5>
                </div>
                <ul>
                    <li><a href="<?php echo site_url("organizations"); ?>"><?php echo $this->lang->line("setup_organizations"); ?></a></li>
                    <li><a href="<?php echo site_url("import_entity_settings"); ?>"><?php echo $this->lang->line("import_entity_settings"); ?></a></li>
                </ul>
            </div>
            <div class="home-block no-height">
                <div class="settings-title">
                    <i class="fa fa-file-text"></i>
                    <h5><?php echo $this->lang->line("setup_invoices"); ?></h5>
                </div>
                <ul>
                    <?php if ($activateTax) { ?>
                        <li><a href="<?php echo site_url("taxes"); ?>"><?php echo $this->lang->line("taxes"); ?></a></li>
                    <?php } ?>
                    <?php if ($activateDiscount !== "no") { ?>
                        <li><a href="<?php echo site_url("discounts"); ?>"><?php echo $this->lang->line("discounts"); ?></a></li>
                    <?php } ?>
                    <li><a href="<?php echo site_url("terms"); ?>"><?php echo $this->lang->line("terms"); ?></a></li>
                    <li><a href="<?php echo site_url("items"); ?>"><?php echo $this->lang->line("services"); ?></a></li>
                    <li><a href="<?php echo site_url("organization_invoice_templates"); ?>"><?php echo $this->lang->line("invoice_templates"); ?></a></li>
                    <li><a href="<?php echo site_url("invoice_notes"); ?>"><?php echo $this->lang->line("invoice_notes"); ?></a></li>
                    <?php if ($activateInvoiceDetailsFormat == "yes") { ?>
                        <li><a href="<?php echo site_url("invoice_detail_format/index"); ?>"><?php echo $this->lang->line("invoice_detail_format"); ?></a></li>
                    <?php } ?>
                    <li><a href="javascript:;" onclick="configureInvoiceDiscount()"><?php echo $this->lang->line("discount_in_invoices"); ?></a></li>
                    <li><a href="<?php echo site_url("credit_note_reasons"); ?>"><?php echo $this->lang->line("credit_note_reasons"); ?></a></li>
                    <li><a href="<?php echo site_url("debit_note_reasons"); ?>"><?php echo $this->lang->line("debit_note_reasons"); ?></a></li>
                </ul>
            </div>
        </div>
        <div class="col-md-4">
            <div class="home-block no-height">
                <div class="settings-title">
                    <i class="fa fa-money"></i>
                    <h5><?php echo $this->lang->line("setup_expenses"); ?></h5>
                </div>
                <ul>
                    <li><a href="<?php echo site_url("expense_categories"); ?>"><?php echo $this->lang->line("expense_categories"); ?></a></li>
                    <li><a href="<?php echo site_url("vouchers/new_expense_default_values"); ?>"><?php echo $this->lang->line("new_expense_default_values"); ?></a></li>
                    <li><a href="<?php echo site_url("vouchers/petty_cash_user_mapping"); ?>"><?php echo $this->lang->line("petty_cash_user_mapping"); ?></a></li>

                </ul>
            </div>
            <div class="home-block no-height">
                <div class="settings-title">
                    <i class="fa fa-cogs"></i>
                    <h5><?php echo $this->lang->line("money_setup"); ?></h5>
                </div>
                <ul>
                    <li><a href="<?php echo site_url("money_preferences"); ?>"><?php echo $this->lang->line("default_values"); ?></a></li>
                    <li><a href="<?php echo site_url("users_rate"); ?>"><?php echo $this->lang->line("users_rate_per_hour"); ?></a></li>
                    <li><a href="<?php echo site_url("setup/rate_between_money_currencies"); ?>"><?php echo $this->lang->line("rate_between_money_currencies"); ?></a></li>
                    <li><a href="<?php echo site_url("setup/time_tracking_sales_account"); ?>"><?php echo $this->lang->line("timeTrackingSalesAccount"); ?></a></li>
                    <li><a href="<?php echo site_url("setup/invoice_number_prefix"); ?>"><?php echo $this->lang->line("invoiceNumberPrefix"); ?></a></li>
                    <li><a href="<?php echo site_url("setup/credit_note_number_prefix"); ?>"><?php echo $this->lang->line("credit_note_number_prefix"); ?></a></li>
                    <li><a href="<?php echo site_url("setup/debit_note_number_prefix"); ?>"><?php echo $this->lang->line("debit_note_number_prefix"); ?></a></li>
                    <li><a href="javascript:;" onclick="setAccountNumberPrefix();"><?php echo $this->lang->line("set_prefix_for_account_type"); ?></a></li>
                    <li><a href="javascript:;" onclick="setTrustAccount();"><?php echo $this->lang->line("trust_account"); ?></a></li>
                </ul>
            </div>
        </div>
        <div class="col-md-4">
            <div class="home-block no-height">
                <div class="settings-title">
                    <i class="fa fa-users"></i>
                    <h5><?php echo $this->lang->line("partners"); ?></h5>
                </div>
                <ul>
                    <li><a href="<?php echo site_url("setup/partners_commissions"); ?>"><?php echo $this->lang->line("activate_partners_commissions_tools"); ?></a></li>
                    <li><a href="<?php echo site_url("setup/global_partner_shares_account"); ?>"><?php echo $this->lang->line("partner_expenses_account"); ?></a></li>
                    <li><a href="<?php echo site_url("organization_invoice_templates/partner_statement"); ?>"><?php echo $this->lang->line("partner_invoice_templates"); ?></a></li>
                </ul>
            </div>
            <div class="home-block no-height">
                <div class="settings-title">
                    <i class="fa fa-credit-card"></i>
                    <h5><?php echo $this->lang->line("setup_bills"); ?></h5>
                </div>
                <ul>
                    <?php if ($activateTax) { ?>
                        <li><a href="<?php echo site_url("supplier_taxes"); ?>"><?php echo $this->lang->line("taxes"); ?></a></li>
                    <?php } ?>
                    <li><a href="<?php echo site_url("organization_invoice_templates/bills_templates"); ?>"><?php echo $this->lang->line("bills_templates"); ?></a></li>
                </ul>
            </div>
        </div>
        <div class="col-md-4">
            <div class="home-block no-height">
                <div class="settings-title">
                    <i class="fa fa-line-chart"></i>
                    <h5><?php echo $this->lang->line("manage_boards"); ?></h5>
                </div>
                <ul>
                    <li><a href="<?php echo site_url("money_dashboards/money_dashboard_config/1"); ?>"><?php echo $this->lang->line("money_dashboard"); ?></a></li>
                </ul>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        <?php if (!empty($isOpenDiscountPopup) && $isOpenDiscountPopup == 1) { ?>
        jQuery(document).ready(function(){
            configureInvoiceDiscount();
        });
        <?php } ?>
    </script>
</div>