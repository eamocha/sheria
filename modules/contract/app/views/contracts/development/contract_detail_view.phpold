<style>
    .timeline {
        position: relative;
        margin-left: 20px;
        border-left: 2px solid #dee2e6;
        padding-left: 20px;
    }

    .timeline-item {
        position: relative;
        padding: 15px 0 15px 20px;
        margin-bottom: 15px;
    }

    .timeline-item::before {
        content: "\f111";
        font-family: "Font Awesome 5 Free";
        font-weight: 900;
        position: absolute;
        left: -12px;
        top: 18px;
        font-size: 0.7rem;
        color: #6c757d;
    }

    .timeline-item.completed::before {
        color: #28a745;
    }

    .timeline-item.pending::before {
        color: #6c757d;
    }

    .timeline-item.in-progress::before {
        color: #007bff;
    }

    .timeline-item h6 {
        font-weight: bold;
    }

    .timeline-date {
        font-size: 0.8rem;
        color: #6c757d;
    }
</style>

<div class="container mt-4">
    <h3 class="mb-3">Contract Drafting Timeline</h3>

    <div class="card">
        <div class="card-header bg-primary text-white">Workflow Timeline</div>
        <div class="card-body">
            <div class="timeline">

                <?php
                $workflow = [
                    [
                        'stage' => 'Drafted by SCM',
                        'actor' => 'Senior Officer SCM',
                        'date' => '2024-06-21',
                        'status' => 'completed',
                        'note' => 'Initial contract terms prepared.'
                    ],
                    [
                        'stage' => 'Reviewed by HOD SCM',
                        'actor' => 'Jane K.',
                        'date' => '2024-06-22',
                        'status' => 'completed',
                        'note' => 'Confirmed compliance with SCM policies.'
                    ],
                    [
                        'stage' => 'User Department Review',
                        'actor' => 'Lucy Otieno',
                        'date' => '2024-06-23',
                        'status' => 'completed',
                        'note' => 'Scope of work validated by department.'
                    ],
                    [
                        'stage' => 'Legal Services Review',
                        'actor' => 'David W.',
                        'date' => '',
                        'status' => 'in-progress',
                        'note' => 'Under legal clause verification.'
                    ],
                    [
                        'stage' => 'Supplier Feedback',
                        'actor' => 'Law Partners LLP',
                        'date' => '',
                        'status' => 'pending',
                        'note' => 'Pending supplier comments.'
                    ],
                    [
                        'stage' => 'Final Approval & Signing',
                        'actor' => 'DG & LS',
                        'date' => '',
                        'status' => 'pending',
                        'note' => 'To be scheduled after all reviews.'
                    ],
                ];
                ?>

                <?php foreach ($workflow as $step): ?>
                    <div class="timeline-item <?= $step['status'] ?>">
                        <h6><?= $step['stage'] ?></h6>
                        <p class="timeline-date">
                            <?= $step['date'] ? date('d M Y', strtotime($step['date'])) : '<em>Pending</em>' ?>
                            | <?= $step['actor'] ?>
                        </p>
                        <p class="mb-1"><?= $step['note'] ?></p>
                        <span class="badge badge-<?= $step['status'] === 'completed' ? 'success' : ($step['status'] === 'in-progress' ? 'info' : 'secondary') ?>">
                            <?= ucfirst(str_replace('-', ' ', $step['status'])) ?>
                        </span>
                    </div>
                <?php endforeach; ?>

            </div>
        </div>
    </div>
</div>
