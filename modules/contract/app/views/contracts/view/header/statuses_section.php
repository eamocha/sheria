<?php
$first_statuses = array_slice($available_statuses, 0, 3, true);
$other_statuses = array_slice($available_statuses, 3, null, true);

function renderStatusLink($contract, $status_id, $status_name, $step, $isDropdown = false)
{
    $stepId   = !empty($step['id']) ? $step['id'] : 'false';
    $stepName = !empty($step['name']) ? $step['name'] : $status_name;
    $title    = !empty($step['comments']) ? $step['comments'] : $stepName;

    $classes = $isDropdown ? 'dropdown-item' : 'btn btn-light';

    if (empty($step['id'])) {
        $classes .= ' submit-with-loader';
    }
    ?>
    <a
        href="<?php echo  site_url('contracts/view/' . $contract['id']) ?>"
        class="<?php echo  $classes ?>"
        title="<?php echo  htmlspecialchars($title) ?>"
        onclick="moveStatus('<?php echo  $contract['id'] ?>',     '<?php echo  $status_id ?>', <?php echo  $stepId === 'false' ? 'false' : "'" . $stepId . "'" ?>,
            event
        );"
    >
        <?php echo  htmlspecialchars($stepName) ?>
    </a>
    <?php
}
?>

<?php foreach ($first_statuses as $status_id => $status_name){ ?>
    <?php $step = $status_transitions[$status_id] ?? []; ?>
    <li>
        <?php renderStatusLink($contract, $status_id, $status_name, $step); ?>
    </li>
<?php }; ?>

<?php if (!empty($other_statuses)): ?>
    <li>
        <div class="btn-group">
            <button
                class="btn btn-light btn-sm dropdown-toggle"
                type="button"
                data-toggle="dropdown"
                aria-haspopup="true"
                aria-expanded="false"
            >
                <?php echo  $this->lang->line('more') ?>
                <span class="caret"></span>
            </button>

            <div class="dropdown-menu">
                <?php foreach ($other_statuses as $status_id => $status_name): ?>
                    <?php $step = $status_transitions[$status_id] ?? []; ?>
                    <?php renderStatusLink($contract, $status_id, $status_name, $step, true); ?>
                <?php endforeach; ?>
            </div>
        </div>
    </li>
<?php endif; ?>
