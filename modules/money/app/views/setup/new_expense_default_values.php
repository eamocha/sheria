<div class="container mt-4">
    <div class="card">
        <div class="card-header ">
            <h4 class="mb-0">Default Values Configuration</h4>
        </div>
        <div class="card-body">
            <form id="defaultValuesForm" method="post" action="<?php echo site_url('money_preferences/save_defaults'); ?>">
                <div class="form-group row">
                    <label for="default_organization_id" class="col-sm-4 col-form-label">Default Organization</label>
                    <div class="col-sm-8">
                        <select class="form-control" id="default_organization_id" name="default_organization_id" required>
                            <option value="">-- Select Organization --</option>
                            <?php foreach ($organizations as $org): ?>
                                <option value="<?php echo $org['id']; ?>" <?php echo ($default_organization_id == $org['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($org['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="default_expense_status" class="col-sm-4 col-form-label">Default Expense Status</label>
                    <div class="col-sm-8">
                        <select class="form-control" id="default_expense_status" name="default_expense_status" required>
                            <option value="">-- Select Status --</option>
                            <option value="pending" <?php echo ($default_expense_status == 'pending') ? 'selected' : ''; ?>>Pending</option>
                            <option value="approved" <?php echo ($default_expense_status == 'approved') ? 'selected' : ''; ?>>Approved</option>
                            <option value="rejected" <?php echo ($default_expense_status == 'rejected') ? 'selected' : ''; ?>>Rejected</option>
                            <option value="paid" <?php echo ($default_expense_status == 'paid') ? 'selected' : ''; ?>>Paid</option>
                        </select>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="default_expense_account" class="col-sm-4 col-form-label">Default Expense Account</label>
                    <div class="col-sm-8">
                        <select class="form-control" id="default_expense_account" name="default_expense_account" required>
                            <option value="">-- Select Account --</option>
                            <?php foreach ($accounts as $account): ?>
                                <option value="<?php echo $account['id']; ?>" <?php echo ($default_expense_account == $account['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($account['name']); ?> (<?php echo htmlspecialchars($account['code']); ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="default_expense_category_id" class="col-sm-4 col-form-label">Default Expense Category</label>
                    <div class="col-sm-8">
                        <select class="form-control" id="default_expense_category_id" name="default_expense_category_id" required>
                            <option value="">-- Select Category --</option>
                            <?php foreach ($expense_categories as $category): ?>
                                <option value="<?php echo $category['id']; ?>" <?php echo ($default_expense_category_id == $category['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($category['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="default_paymentMethod" class="col-sm-4 col-form-label">Default Payment Method</label>
                    <div class="col-sm-8">
                        <select class="form-control" id="default_paymentMethod" name="default_paymentMethod" required>
                            <option value="">-- Select Method --</option>
                            <option value="cash" <?php echo ($default_paymentMethod == 'cash') ? 'selected' : ''; ?>>Cash</option>
                            <option value="check" <?php echo ($default_paymentMethod == 'check') ? 'selected' : ''; ?>>Check</option>
                            <option value="bank_transfer" <?php echo ($default_paymentMethod == 'bank_transfer') ? 'selected' : ''; ?>>Bank Transfer</option>
                            <option value="credit_card" <?php echo ($default_paymentMethod == 'credit_card') ? 'selected' : ''; ?>>Credit Card</option>
                            <option value="online" <?php echo ($default_paymentMethod == 'online') ? 'selected' : ''; ?>>Online Payment</option>
                        </select>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="default_paid_through" class="col-sm-4 col-form-label">Default Paid Through</label>
                    <div class="col-sm-8">
                        <select class="form-control" id="default_paid_through" name="default_paid_through" required>
                            <option value="">-- Select Option --</option>
                            <option value="petty_cash" <?php echo ($default_paid_through == 'petty_cash') ? 'selected' : ''; ?>>Petty Cash</option>
                            <option value="company_account" <?php echo ($default_paid_through == 'company_account') ? 'selected' : ''; ?>>Company Account</option>
                            <option value="client_account" <?php echo ($default_paid_through == 'client_account') ? 'selected' : ''; ?>>Client Account</option>
                        </select>
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-sm-8 offset-sm-4">
                        <button type="submit" class="btn btn-primary">Save Defaults</button>
                        <a href="<?php echo site_url('money_preferences'); ?>" class="btn btn-secondary">Cancel</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        // Initialize form validation
        $('#defaultValuesForm').validate({
            errorClass: 'is-invalid',
            validClass: 'is-valid',
            errorElement: 'div',
            errorPlacement: function(error, element) {
                error.addClass('invalid-feedback');
                element.closest('.form-group').append(error);
            },
            highlight: function(element, errorClass, validClass) {
                $(element).addClass(errorClass).removeClass(validClass);
            },
            unhighlight: function(element, errorClass, validClass) {
                $(element).removeClass(errorClass).addClass(validClass);
            }
        });
    });
</script>