<style >
    #main-container {
         margin: 0;
         padding: 0 15px;
     }
     .background-style {
         padding-bottom: 0px !important;
     }
     body {
         margin-bottom: 0px !important;
     }
 </style>
 <a id="s-menu-toggle" href="javascript:void(0);" class="s-menu-toggle">
     <i class="menu-icon-circle sub-menu-arrow fa-solid fa-angles-<?php echo $this->session->userdata("AUTH_language") == "arabic" ? "right" : "left";?> "></i>
 </a>
<div class="d-flex m-0 ">
    <div class="resp-main-body-width-10 no-margin no-padding sidebar-offcanvas" id="sub-menu-sidebar">
        <ul id="menu" role="tablist" class="left-menu nav nav-tabs  navbar-nav no-margin no-padding-top">
            <?php //  $subNavItems[site_url("legal_opinions/")]=["icon" => "spr spr-matter", "name" => $this->lang->line("legal_opinions"), "class_a_href" => "related-opinions-tab-case"];
          //$subNavItems[site_url("legal_opinions/")]=["icon" => "spr spr-matter", "name" => $this->lang->line("legal_opinions"), "class_a_href" => "related-opinions-tab-case"];
         // $subNavItems[site_url("legal_opinions/")]=["icon" => "spr spr-matter", "name" => $this->lang->line("legal_opinions"), "class_a_href" => "related-opinions-tab-case"];
          /// array_splice($subNavItems, count($subNavItems)-1, 0, $newValue);
         
           
            foreach ($subNavItems as $subNavItemHref => $subNavItem) {
                if (is_array($subNavItem)) {
                    if (isset($subNavItem["sub-menu"]) && 0 < count($subNavItem["sub-menu"])) {?>
                        <li class="sub-menu <?php  echo $subNavItemHref == $activeSubNavItem ? "dropdown-submenu" : "";?>">
                            <a class="<?php  echo $subNavItemHref == $activeSubNavItem ? "active" : "";  echo " ";   echo $subNavItem["class_a_href"] ?? "";  ?>" tabindex="-1"  href="<?php   echo $subNavItemHref == $activeSubNavItem ? "javascript:void(0);" : $subNavItemHref . "/" . $id;?>" <?php echo $subNavItemHref == $activeSubNavItem ? "onclick='go_to_section(\"top-section-div\")'" : "";?>>
                                <i class="<?php   echo $subNavItem["icon"];?>"></i>
                                <span class="sub-menu-collapse in hidden-s"><?php echo $subNavItem["name"];?></span>
                            </a>
                            <?php 
                            $case_category = isset($this->legal_case) ? strtolower($this->legal_case->get_field("category")) : "";
                            if ($subNavItemHref == $activeSubNavItem) {?>
                                <ul class="dropdown-menu sub-menu-container">
                                    <?php
                                    foreach ($subNavItem["sub-menu"] as $item => $item_name) {      
                                        $submenu_item_title ="";           
                                        switch ($item_name) {
                                            case "custom_fields":
                                                $submenu_item_title = $this->lang->line("cases_custom_field_helper");
                                                break;
                                            case "outsourcing_to_lawyers":
                                                $submenu_item_title = $this->lang->line($case_category . "_outsourcing_to_helper");
                                                break;
                                            case "related_contributors":
                                                $submenu_item_title = $this->lang->line($case_category . "_related_contributors_helper");
                                                break;
                                            case "discharge_of_social_security":
                                                $submenu_item_title = $this->lang->line("licenses_and_waiver_helper");
                                                break;
                                            default:
                                                $submenu_item_title = NULL;
                                            }
                                            ?>
                                            <li>
                                                <a class="no-style <?php echo isset($submenu_item_title) ? "tooltip-title" : "";?>" tabindex="-1" href="javascript:void(0);" onclick="go_to_section('<?php echo $item;?>')"  title="<?php  echo $submenu_item_title;?>">
                                                    <?php  echo $this->lang->line($item_name);?>
                                                </a>
                                            </li>
                                            <?php } ?>
        </ul>
        <?php } ?>
    </li>
                        <?php
                    } else {
                        ?>
                        <li class="sub-menu <?php echo $subNavItem["class"] ?? "";?>">
                            <a class=" <?php echo $subNavItemHref == $activeSubNavItem ? "active" : "";  echo " ";    echo $subNavItem["class_a_href"] ?? "";?>" href="<?php echo $subNavItemHref . "/" . $id;?>">
                                <i class="<?php echo $subNavItem["icon"];?>"></i> <span class="sub-menu-collapse in hidden-s"><?php    echo $subNavItem["name"];?>   </span>
                            </a>
                        </li>
                        <?php
                    }
                } else {
                    ?>
                    <li class="sub-menu">
                        <a class="<?php echo $subNavItemHref == $activeSubNavItem ? "active" : ""; echo " "; echo $subNavItem["class_a_href"] ?? "";  ?>"   href="<?php  echo $subNavItemHref . "/" . $id;     echo $subNavItem["class_a_href"] ?? "";?>">
                            <i class="fa fa-fw fa-circle-o"></i> <span class="sub-menu-collapse in hidden-s"><?php echo $subNavItem;?> </span>
                        </a>
                    </li>
                    <?php
                }
            }
            ?>
        </ul>
    </div>
    <script type="text/javascript">
        function go_to_section($section_id) {
            if (jQuery("#" + $section_id).length > 0) {
                var elmnt = document.getElementById($section_id);
                elmnt.scrollIntoView({behavior: 'smooth'});
            }
        }
        jQuery(function () {
            jQuery(".scrollable").scrollable({keyboard: false});
            /* off-canvas sidebar toggle */
            jQuery('#s-menu-toggle').click(function (e) {
                e.preventDefault();
                if (jQuery('.left-menu').hasClass("small-menu")) {
                    jQuery('.left-menu').removeClass('small-menu');
                    jQuery('.sub-menu-collapse').removeClass('font-size-0');
                    jQuery('.sub-menu a').removeClass('no-padding-bottom flex-center-menu');
                    jQuery('.sub-menu-container').removeClass('d-none');
                    jQuery('#s-menu-toggle').removeClass('s-menu-toggle-small-menu');
                } else {
                    jQuery('.left-menu').addClass('small-menu');
                    jQuery('.sub-menu-collapse').addClass('font-size-0');
                    jQuery('.sub-menu a').addClass('no-padding-bottom flex-center-menu');
                    jQuery('.sub-menu-container').addClass('d-none');
                    jQuery('#s-menu-toggle').addClass('s-menu-toggle-small-menu');
                }
            });
        });
    </script>