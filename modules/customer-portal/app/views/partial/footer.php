<div class="col-md-12 row no-margin footer cpFooter" id="footer">
    <div class="col-md-12 col-xs-12 text-center copyright margin-top-15">
        <span><?= $this->lang->line("footer_all_rights_reserved") ?> &copy; <?= date("Y") ?> - Sheria360</span>
    </div>
</div>
</div><!-- /.cp-login-page -->
</div><!-- /.cp-container -->

<?php if ($this->config->item("csrf_protection")): ?>
    <script type="text/javascript">
        var csrfName = '<?= $this->security->get_csrf_token_name() ?>';
        var csrfValue = '<?= $this->security->get_csrf_hash() ?>';
        jQuery.ajaxSetup({
            data: {
                '<?= $this->security->get_csrf_token_name() ?>': '<?= $this->security->get_csrf_hash() ?>'
            }
        });
    </script>
<?php endif; ?>

<a href="#" class="scrollToTop"></a>
</body>
</html>