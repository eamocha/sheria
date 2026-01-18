<?php $this->load->view("partial/header"); ?>

<div class="col-md-12">
    <div class="row">
        <div class="col-md-12">
            <ul class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="dashboard/admin"><?= html_escape($this->lang->line("administration")) ?></a>
                </li>
                <li class="breadcrumb-item active">
                    <?= html_escape($this->lang->line("departments")) ?>
                </li>
            </ul>
        </div>

        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4><?= html_escape($this->lang->line("departments")) ?></h4>
                    <a href="<?= site_url("departments/add") ?>" class="btn btn-primary">
                        <i class="fas fa-plus"></i> <?= html_escape($this->lang->line("add_department")) ?>
                    </a>
                </div>
                <div class="card-body">
                    <?php if (!empty($fb)): ?>
                        <div class="alert alert-<?= html_escape($fb["type"]) ?> alert-dismissible fade show" role="alert">
                            <?= html_escape($fb["message"]) ?>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    <?php endif; ?>

                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="thead-light">
                            <tr>
                                <th><?= html_escape($this->lang->line("id")) ?></th>
                                <th><?= html_escape($this->lang->line("name")) ?></th>
                                <th><?= html_escape($this->lang->line("actions")) ?></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php if (isset($records) && !empty($records)): ?>
                                <?php foreach ($records as $record): ?>
                                    <tr>
                                        <td><?= html_escape($record['id']) ?></td>
                                        <td><?= html_escape($record['name']) ?></td>
                                        <td>
                                            <a href="<?= site_url("departments/edit/" . $record['id']) ?>" class="btn btn-sm btn-info" title="<?= html_escape($this->lang->line("edit")) ?>">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="<?= site_url("departments/delete/" . $record['id']) ?>" class="btn btn-sm btn-danger delete-record"
                                               data-record-id="<?= html_escape($record['id']) ?>"
                                               data-record-name="<?= html_escape($record['name'] )?>"
                                               title="<?= html_escape($this->lang->line("delete")) ?>"
                                               onclick="return confirm('<?= html_escape(sprintf($this->lang->line("confirm_delete_record"), $record['name'])) ?>');">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="3" class="text-center">
                                        <?= html_escape($this->lang->line("no_departments_found")) ?>
                                    </td>
                                </tr>
                            <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<?php $this->load->view("partial/footer"); ?>
