<div style="font-family: Arial, sans-serif; line-height: 1.6;">
    <h2 style="color: #2c3e50;">New Task Assigned</h2>

    <p>Hello,</p>

    <p>You have been assigned a new task related to contract <?php echo $contract_code . $contract_id; ?>:</p>

    <div style="background: #f9f9f9; padding: 15px; border-left: 4px solid #3498db; margin: 15px 0;">
        <h3 style="margin-top: 0;"><?php echo $task_title; ?></h3>
        <p><?php echo $task_description; ?></p>
        <p><strong>Due Date:</strong> <?php echo date('F j, Y', strtotime($due_date)); ?></p>
        <p><strong>Priority:</strong> <?php echo $priority; ?></p>
    </div>

    <p>This task was assigned by <?php echo $assigner_name; ?>.</p>

    <p>
        <a href="<?php echo $task_url; ?>" style="background: #3498db; color: white; padding: 10px 15px; text-decoration: none; border-radius: 4px; display: inline-block;">
            View Task Details
        </a>
    </p>

    <p>Best regards,<br>Sheroa360 Team</p>
</div>