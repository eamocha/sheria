<div class="primary-style">
    <div class="modal fade modal-container modal-resizable">
          <div class="modal-dialog">
              <div class="modal-content">
                  <div class="modal-header">
                      <h4 id="title" class="modal-title"><?php echo $title;?></h4>
                      <button type="button" class="close" data-dismiss="modal" >×</button>
                  </div><!-- /.modal-header -->
                  <div class="modal-body"><!-- /.modal-body -->
                      <?php
                      echo form_open("", 'id="administration-form" name="administration_form" method="post" class="form-horizontal"');
                      if (isset($extra_html)) {
                          echo $extra_html;
                      }
                      ?>
                      <div class="col-md-12 form-group row p-0">
                          <label class="col-form-label text-right pr-0 col-md-4 col-xs-12 required"><?php echo $field["label"];?></label>
                          <div class="col-md-8  col-xs-10"><?php echo form_input(["name" => $field["name"] . "_" . $system_lang, "value" => $records[$field["name"]. "_" . $system_lang]??"", "class" => "form-control first-input"]);?>
                              <div data-field="<?php echo $field["name"];?>_<?php echo $system_lang;?>" class="inline-error d-none padding-5"></div>
                          </div>
                      </div>
                      <div class="col-md-12 form-group row p-0">
                          <label class="col-form-label text-right pr-0 col-md-4 col-xs-12 required"><?php echo $applies_to_field["label"];?></label>
                          <div class="col-md-8  col-xs-10"><?php echo form_dropdown($applies_to_field["name"], ["contract"=>"Contract","both"=>"Both","mou"=>"Mou"],"Contract", 'class="form-control"'); ?>

                              <div data-field="<?php echo $applies_to_field["name"];?>_<?php echo $system_lang;?>" class="inline-error d-none padding-5"></div>
                          </div>
                      </div>
                      <div> <?php echo $extra_footer_html ?? "";?> </div>
                      <div class="col-md-12 p-0 show-rest-fields">
                          <div class="form-group row col-md-12 p-0">
                              <div class="col-md-4 pr-0">&nbsp;</div>
                              <div class="col-md-8 pr-0">
                                  <a href="javascript:;"  onclick="showMoreFields(jQuery('#administration-form'),jQuery('.hide-rest-fields','#administration-form'));"><?php echo $this->lang->line("more_fields");?></a>
                              </div>
                          </div>
                      </div>
                      <div id="other-lang-container" class="container-hidden-fields d-none">
                          <?php foreach ($languages as $language) {
                              if ($language["name"] !== $system_lang) { ?>
                          <div class="col-md-12 form-group row p-0">
                              <label class="col-form-label text-right pr-0 col-md-4 col-xs-12"><?php echo $field["label"];?>(<?php echo $this->lang->line($language["fullName"]);?>)</label>
                              <div class="col-md-8 col-xs-10"><?php
                                      echo form_input(["name" => $field["name"] . "_" . $language["name"], "value" => $records[$field["name"] . "_" . $language["name"]]??"", "class" => "form-control"]);?>
                                  <div data-field="<?php echo $field["name"];?>_<?php echo $language["name"];?>" class="inline-error d-none padding-5"></div>
                              </div>
                          </div><?php }
                          }?>
                      </div>
                      <div class="col-md-12 p-0 d-none-rest-fields d-none">
                          <div class="form-group row col-md-12 p-0 ">
                              <div class="col-md-4 pr-0">&nbsp;</div>
                              <div class="col-md-8 pr-0">
                                  <a href="javascript:;" onclick="showLessFields(jQuery('#administration-form'));"><i class="fa fa-angle-double-up"></i>&nbsp;<?php echo $this->lang->line("less_fields");?>
                                  </a>
                              </div>
                          </div>
                      </div><?php form_close();?>
                  </div><!-- /.modal-body -->
                  <div class="modal-footer"><!-- /.modal-footer -->
                      <span class="loader-submit"></span>
                      <div class="btn-group">
                          <button id="form-submit" type="button"
                                  class="btn btn-save btn-add-dropdown modal-save-btn"><?php echo $this->lang->line("save");?></button>
                      </div>
                      <button type="button" class="btn btn-link" data-dismiss="modal"><?php echo $this->lang->line("cancel");?></button>
                  </div><!-- /.modal-footer -->
              </div><!-- /.modal-content -->
          </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
</div>