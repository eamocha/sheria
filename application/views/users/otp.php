<?php exit("please check the otp.php view")?><div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <?php
            echo form_open("", 'name="verifyOtpForm" id="verifyOtpForm" method="post" class="form-horizontal"');
            echo form_input(["name" => "type", "value" => "submit", "type" => "hidden"]);
            ?>
            <div class="row">
                <div class="col-md-12"><h4><?php echo $this->lang->line("verify_otp_form");?></h4></div>
                <div class="col-md-7 no-padding">
                    <?php echo form_input(["name" => "userId", "value" => $userId, "id" => "userId", "type" => "hidden"]);?>
                    <div class="form-group row">
                        <label class="control-label col-md-4 required"><?php echo $this->lang->line("otp_code");?></label>
                        <div class="col-md-5">
                            <?php echo form_input(["name" => "otpCode", "id" => "otpCode", "autocomplete" => "stop", "class" => "form-control", "placeholder" => $this->lang->line("otp_code"), "data-validation-engine" => "validate[required]", "type" => "tel"]);
                             echo form_error("otpCode", '<span class="help-inline">', '</span>');
                             
                            ?>
                            <div class="inline-text"><?php  echo $this->lang->line("please_input_otp");?>  </div>
                            <span onClick="refreshOTP()"  class="btn btn-default btn-info" ><?php echo $this->lang->line("resendOtp");?></span>
                        </div>
                    <div class="actions" id="actionsHeader">
                        <input type="submit" name="btnSubmit" value="<?php echo $this->lang->line("submit");?>" class="btn btn-default btn-info" />
                        <input type="reset" name="btnReset" value="<?php echo $this->lang->line("reset");?>" class="btn btn-default btn-link" />
                    </div>
                </div>
            </div>
            <?php echo form_close();?>
        </div>
    </div>
</div>
<script>
    jQuery(document).ready(function () {
        jQuery("#verifyOtpForm").validationEngine({
            autoPositionUpdate: true,
            promptPosition: 'bottomRight',
            scroll: false
        });
    });
    function refreshOTP() {
        $(function() {
              $.ajax({
                  
                    url: "users/otp",
                    type: 'POST',
                    data: {refreshOtp: "refreshOtp"},
                  })
                    .done(function(data) { alert("New OTP generated successifully");
                           // $('.targeted').text(data);
                          });
                        });
    }
</script>