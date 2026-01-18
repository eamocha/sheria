<div class="container-fluid">
    <div class="row">
           <div class="col-md-12">
               <ul class="breadcrumb">
                   <li class="breadcrumb-item"><a href="dashboard/admin"><?php echo $this->lang->line("administration");?></a></li>
                   <li class="breadcrumb-item active"><?php echo $this->lang->line($model . "_custom_fields");?></li>
                   <li class="breadcrumb-item"><a href="javascript:;" onClick="customFieldForm('<?php echo $model;?>');"><?php echo $this->lang->line("add_custom_field");?></a></li>
               </ul>
           </div>
        <div class="col-md-12 no-padding">
            <div class="col-md-12 form-group row" id="pagination">
                <div class="col-md-6 no-padding col-xs-12"><h4><?php echo $this->lang->line("total_records");?>: <span id="total-records"><?php echo sizeof($records);?></span></h4></div>
            </div>
            <div id="custom-fields" class="<?php echo count($records) <= 0 ? "d-none" : "";?>">
                <div class="col-md-12 form-group">
                    <div class="dropdown more pull-right margin-right10">
                        <button type="button" data-toggle="dropdown" class="btn" aria-haspopup="true" aria-expanded="false">
                            <i class="spr spr-gear"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dLabel">
                            <a class="dropdown-item" href="<?php echo site_url("export/custom_fields/" . $model);?>"><?php echo $this->lang->line("export_to_excel");?></a>
                        </div>
                    </div>
                </div>
                <div class="clearfix"></div>
                <div class="col-md-12"><?php $this->load->view("custom_fields/table_html");?> </div>
            </div>
        </div>
    </div>
</div>
