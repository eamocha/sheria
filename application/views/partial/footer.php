</div>
</div>
</div>
<?php

// Display the footer if the user is logged in.
if ($this->is_auth->is_logged_in()) {
    ?>
    <div class="footer-bg"></div>
    <div class="footer" id="footer">
        <div class="container">
            <div class="footer-hidden-background d-none"></div>
            <div class="row no-margin col-md-12">
                <div class="col-md-12 text-center">
                    <div class="footer1">
                        <img class="footer-logo" src='<?= $this->instance_data_array["app_footer_logo"] . "?v=" . $this->instance_data_array["app_theme_version"] ?>'/>
                    </div>
                    <p>
                        <span><?= sprintf("Sheria360 Legal Management Services Software", $this->is_auth->product, $this->productVersion) ?></span>&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;
                        <a href="home"><?= sprintf("About sheria360", $this->is_auth->product) ?></a>&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;
                        <a target="_blank" href="https://convenepoint.sheria360.com/"><?= $this->lang->line("support") ?></a>&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;
                        <a target="_blank" href="<?= $this->config->item("help_url") ?>"><?= $this->lang->line("documentation_center") ?></a>
                    </p>
                </div>
            </div>
        </div>
    </div>
    <?php
}
?>

<a href="#" class="scrollToTop"></a>
</body>

<?php
// Print CSS and JS from arrays.
print_css($this->css_footer);
print_js($this->js_footer);

// Add the footer script.
$client = isset($this->licensor) ? $this->licensor->get("clientName") : "";
?>
<script>
    jQuery(document).ready(function(e) {
        if ('<?= $this->currentTopNavItem ?>' == 'pages') {
            var rSegment_2 = '<?= $this->uri->rsegment(2) ?>';
            jQuery('.' + rSegment_2 + 'Tab').addClass('external-portal-active-nav');
        } else {
            jQuery('.<?= $this->currentTopNavItem ?>', '#top-nav-item-list').addClass('active');
        }
    });
</script>

</html>