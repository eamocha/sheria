<div class="col-md-12">
   <div class="row">
           <div class="col-md-12">
               <nav aria-label="breadcrumb">
                   <ol class="breadcrumb">
                       <li class="breadcrumb-item" aria-current="page"><a   href="dashboard/admin"><?php echo $this->lang->line("administration");?></a></li>
                       <li class="breadcrumb-item active" aria-current="page"><?php echo $this->lang->line("contract_type");?></li>
                       <li class="breadcrumb-item" aria-current="page"><a href="javascript:;"  onclick="administrationForm('contract_types', false, false, 'contract');"><?php echo $this->lang->line("add");?></a>
                       </li>
                   </ol>
               </nav>
           </div><?php if (0 < count($records)) {?>
       <div class="col-md-12">
           <div class="dropdown more float-right">
               <button class="btn" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                   <i class="spr spr-gear"></i>
               </button>
               <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dLabel">
                   <a class="dropdown-item" href="<?php echo site_url("export/contract_types");?>"><?php  echo $this->lang->line("export_to_excel");?></a>
               </div>
           </div>
       </div>
       <?php }
       $this->load->view("administration/index");?>
   </div>
   </div>