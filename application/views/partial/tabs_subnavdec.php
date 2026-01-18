<?php
if (1 < count($subNavItems)) {  ?>
    <div class="col-md-12 margin-bottom">
        <ul class="nav nav-tabs my-3 p-0" id="tabsSubnavItems">
            <?php foreach ($subNavItems as $subNavItemHref => $subNavItem) {
                ?>
                <li class="nav-item " <?php echo ($subNavItemHref == $activeSubNavItem ? "active" : "") ?>">
                <a href="<?php echo $subNavItemHref . "/" . $id;?>" class="nav-link active<?php  echo isset($subNavItem["class_a_href"]) ? $subNavItem["class_a_href"] . "/" . $id : "";?>"><?php echo $subNavItem["label"];?></a></li>
                <?php  if (is_array($subNavItem)) {?>
                    <i class="<?php echo $subNavItem["icon"] ?>"></i> <span class="sub-menu-collapse in hidden-s"><?php echo $subNavItem["name"];?></span></a></li>
                    <?php
                } else {
                    ?>
                    <span class="sub-menu-collapse in hidden-s"><?php echo $subNavItem;?></span></a></li>
                <?php    }
            }?>
        </ul>
    </div>
<?php }?>
<script type="text/javascript">
    jQuery(function () {
        jQuery(".scrollable").scrollable({keyboard: false});
    });
</script>