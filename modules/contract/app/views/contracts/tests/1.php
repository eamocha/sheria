<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Workflow Configuration</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .step-card {
            border: 1px solid #dee2e6;
            border-radius: 5px;
            margin-bottom: 15px;
            overflow: hidden;
        }
        .step-header {
            padding: 12px 15px;
            background-color: #f8f9fa;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .step-content {
            padding: 15px;
        }
        .rotate-icon {
            transition: transform 0.2s;
        }
        .collapsed .rotate-icon {
            transform: rotate(-90deg);
        }
        .checklist-item {
            display: flex;
            align-items: center;
            padding: 5px 0;
        }
    </style>
</head>
<body>
<div class="container py-4">
    <h1>Workflow Steps</h1>
    <div id="configuredWorkflowDisplay"></div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Your workflow data
    const workflowData = {
        steps: [
            {
                id: "5",
                start_point: 1,
                step_name: "Cancelled",
                responsible_user_roles: "Admin",
                activity: "Review documents",
                step_input: "Contract files",
                step_output: "Approval decision",
                is_global: 0,
                checklist: [
                    {
                        item_text: "Are all documents uploaded",
                        input_type: "yesno",
                        is_required: 1
                    }
                ],
                functions: [
                    {
                        label: "Add reminder",
                        icon_class: "fa-bell"
                    }
                ],
                transitions: [
                    {
                        to_step: "7",
                        name: "Move to review"
                    }
                ]
            },
            {
                id: "7",
                step_name: "Closed",
                responsible_user_roles: "Manager",
                activity: "Final approval",
                checklist: [
                    {
                        item_text: "Verify all signatures",
                        input_type: "yesno",
                        is_required: 1
                    }
                ]
            }
        ]
    };

    function renderWorkflow() {
        const container = document.getElementById('configuredWorkflowDisplay');
        container.innerHTML = '';

        if (!workflowData.steps || workflowData.steps.length === 0) {
            container.innerHTML = '<p class="text-center text-muted">No steps configured</p>';
            return;
        }

        workflowData.steps.forEach(step => {
            const stepId = `step-${step.id}`;
            const stepHTML = `
        <div class="step-card">
            <div class="step-header collapsed"
                 data-bs-toggle="collapse"
                 data-bs-target="#${stepId}">
                <h5 class="mb-0">
                    <i class="fas fa-circle me-2"></i>
                    Step ${step.id}: ${step.step_name}
                    ${step.start_point ? '<span class="badge bg-success ms-2">Start</span>' : ''}
                </h5>
                <i class="fas fa-chevron-down rotate-icon"></i>
            </div>
            <div class="collapse" id="${stepId}">
                <div class="step-content">
                    <p><strong>Responsible:</strong> ${step.responsible_user_roles || 'None'}</p>
                    <p><strong>Activity:</strong> ${step.activity || 'None'}</p>
                    <p><strong>Input:</strong> ${step.step_input || 'None'}</p>
                    <p><strong>Output:</strong> ${step.step_output || 'None'}</p>

                    ${step.checklist && step.checklist.length ? `
                    <div class="mt-3">
                        <h6>Checklist:</h6>
                        ${step.checklist.map(item => `
                            <div class="checklist-item">
                                <i class="fas fa-check-circle text-success me-2"></i>
                                ${item.item_text}
                                ${item.is_required ? '<span class="badge bg-danger ms-2">Required</span>' : ''}
                            </div>
                        `).join('')}
                    </div>
                    ` : ''}
                </div>
            </div>
        </div>
        `;
            container.insertAdjacentHTML('beforeend', stepHTML);
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        console.log("Rendering workflow...");
        renderWorkflow();
    });
</script>
</body>
</html>