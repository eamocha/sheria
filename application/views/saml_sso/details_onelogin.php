<div class="primary-style">
    <div class="modal fade modal-container modal-resizable">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 id="title" class="modal-title">
                        <?php echo sprintf($this->lang->line("configure_as_idp"), $this->lang->line($idp)); ?>
                    </h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body no-padding-top">
                    <ul>
                        <li><h4>Configure OneLogin</h4></li>
                        <ol>
                            <li>Log in to the OneLogin Dashboard, and click Apps > Add Apps.</li>
                            <li>Search for SAML, and select SAML Test Connector (IdP w/attr).</li>
                            <li>When prompted, change the Display Name of your app.</li>
                            <li>Click SAVE.</li>
                            <li>Go to More Actions > <i class="fa fa-fw fa-arrow-circle-o-down"></i> SAML Metadata. <strong>(to be imported as Metadata File at Sheria360)</strong></li>
                            <li>Go to Configuration and setup the below</li>
                        </ol>
                        <div class="col-md-12">
                            <table class="table mt-10">
                                <tr><td><b>Login URL</b></td><td><code>https://YOUR_DOMAIN</code></td></tr>
                                <tr><td><b>ACS (Consumer) URL*</b></td><td><code>https://YOUR_DOMAIN/saml/www/module.php/saml/sp/saml2-acs.php/sheria360-onelogin</code></td></tr>
                                <tr><td><b>SAML Recipient</b></td><td><code>https://YOUR_DOMAIN/saml/www/module.php/saml/sp/saml2-acs.php/sheria360-onelogin</code></td></tr>
                                <tr><td><b>SAML Single Logout URL:</b></td><td><code>https://YOUR_DOMAIN/saml/www/module.php/saml/sp/saml2-logout.php/sheria360-onelogin</code></td></tr>
                                <tr><td><b>ACS (Consumer) URL Validator*</b></td><td><code>https://YOUR_DOMAIN/saml/www/module.php/saml/sp/saml2-acs.php/sheria360-onelogin</code></td></tr>
                                <tr><td><b>Audience</b></td><td><code>https://YOUR_DOMAIN/saml/www/module.php/saml/sp/metadata.php/sheria360-onelogin</code></td></tr>
                            </table>
                        </div>
                        <li><h4>API Credentails</h4></li>
                        <ol>
                            <li>Go to Developers > API Credentails </li>
                            <li>Create new API credential </li>
                            <li>Copy client id & client secret</li>
                        </ol>
                        <li><h4>Sheria360 Configuration</h4></li>
                        <ol>
                            <li>Go to Settings > Single Sign On > setup </li>
                            <li>Select OneLogin </li>
                            <li>Paste client id & client secret </li>
                            <li>Upload Saml Metadata File</li>
                            <li>Click Save.</li>
                        </ol>
                    </ul>
                </div>
                <!-- /.modal-body -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-link close_model no_bg_button pull-right text-align-right" data-dismiss="modal">
                        <?php echo $this->lang->line("cancel"); ?>
                    </button>
                </div><!-- /.modal-footer -->
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
</div>