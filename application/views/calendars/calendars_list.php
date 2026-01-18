<?php

$check = true;
$add_a4l_calendar_option = true;

?>

<section class="service-integration-container">
    <h1><?php echo $this->lang->line("calendar_integration"); ?></h1>
    <p>
        <?php echo $this->lang->line("available_calendars_to_choose"); ?>
    </p>
    <fieldset class="container-fieldset">
        <legend><?php echo $this->lang->line("pick_your_calendar"); ?></legend>
        <?php echo form_open(current_url(), "class=\"form-horizontal\" novalidate id=\"calendar-list\""); ?>

        <?php
        foreach ($my_calendars as $calendar_id => $calendar) {
            if (strcasecmp("Sheria360", $calendar) === 0) {
                $add_a4l_calendar_option = false;
            }
            // Check if this is the previously selected calendar and we can use it
            if (isset($old_integration["calendar"]["selected_calendar"]) && $old_integration["calendar"]["selected_calendar"] === $calendar_id && !$add_a4l_calendar_option) {
                $check = false;
                unset($my_calendars[$calendar_id]); // Remove from the generic list iteration later
                ?>
                <input type="radio" name="selected_calendar" value="<?php echo $calendar_id; ?>" checked=checked>
                <label for="<?php echo $calendar; ?>"><?php echo $calendar . $this->lang->line("existing"); ?></label><br>
                <?php
            }
        }
        if ($add_a4l_calendar_option) {
            $check = false;
            ?>
            <input type="radio" name="selected_calendar" checked="checked" value="sheria360">
            <label for="<?php echo $this->lang->line("sheria360"); ?>"><?php echo $this->lang->line("create_a4l_calendar_in_acc"); ?></label><br>
            <?php
        }

        $count = 0;
        foreach ($my_calendars as $calendar_id => $calendar) {
            ?>
            <input type="radio" name="selected_calendar" value="<?php echo $calendar_id; ?>" <?php echo $count == 0 && $check ? "checked=\"checked\"" : ""; ?>>
            <label for="<?php echo $calendar; ?>"><?php echo $calendar . $this->lang->line("existing"); ?></label><br>
            <?php
            $count++;
        }

        form_close();
        ?>

        <div class="continue">
            <button class="btn btn-default btn-info"><?php echo $this->lang->line("continue"); ?></button>
            <span class="loader-submit"></span>
        </div>
    </fieldset>
</section>
