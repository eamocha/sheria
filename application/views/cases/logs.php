<?php if ($id){ ?>

    <?php
    echo form_input(["id" => "case-id", "value" => $id, "type"  => "hidden" ]);
    echo form_input([ "id" => "controller", "value" => "cases", "type"  => "hidden" ]);
    echo form_input([ "id" => "case-client-id", "value" => $legalCase["client_id"] ?? 0, "type"  => "hidden" ]);
    ?>

    <?php if ($this->is_auth->check_uri_permissions("/cases/", "/cases/load_client_widgets/", "core", true, true)){ ?>
        <?php $this->load->view("cases/client_widgets"); ?>
    <?php }; ?>

<?php }; ?>

<script>
    jQuery(document).ready(function () {
        if (jQuery(this).width() < 800) {
            jQuery('#case_info').addClass('d-none');
            jQuery('#case_toggle i')
                .removeClass('fa-solid fa-arrow-down')
                .addClass('fa-solid fa-arrow-right');
        }

        <?php if ($this->is_auth->check_uri_permissions("/cases/", "/cases/load_client_widgets/", "core", true, true)){ ?>
        clientStatusesEvents(jQuery('#client-account-status'));
        clientTransactionsBalance(
            {
                case: '<?php echo $legalCase["id"] ?? 0; ?>',
                client: '<?php echo $legalCase["client_id"] ?? 0; ?>'
            },
            jQuery('#client-account-status'),
            'cases',
            true
        );
        <?php }; ?>
    });
</script>
