<!-- Add Bill Modal -->
<div class="modal fade" id="addBillModal" tabindex="-1" role="dialog" aria-labelledby="addBillModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form method="post" action="<?php echo site_url('modules/money/vouchers/bill_add'); ?>">
                <div class="modal-header">
                    <h5 class="modal-title" id="addBillModalLabel">Add Bill</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <!-- Redirect to (dynamic) -->
                    <input type="hidden" name="redirect_to" value="<?php echo isset($case_id) ? site_url('cases/edit/'.$case_id) : ''; ?>">

                    <!-- Hidden IDs -->
                    <input type="hidden" name="rate" value="1.0000">
                    <input type="hidden" name="organization_id" value="1">
                    <input type="hidden" name="case_id" value="<?php echo isset($case_id) ? $case_id : ''; ?>">
                    <input type="hidden" name="client_id" value="<?php echo isset($client_id) ? $client_id : ''; ?>">

                    <!-- Supplier -->
                    <div class="form-group row d-none">
                        <label for="supplier_id" class="col-sm-3 col-form-label">Supplier</label>
                        <div class="col-sm-9">
                            <select class="form-control" id="supplier_id" name="supplier_id" required>

                                <option value="<?php echo $supplier_id?>" selected>Matrix Vision Systems Ltd</option>
                            </select>
                        </div>
                    </div>

                    <!-- Reference Number -->
                    <div class="form-group row">
                        <label for="referenceNum" class="col-sm-3 col-form-label">Reference #</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="referenceNum" name="referenceNum" value="CA50009" required>
                        </div>
                    </div>

                    <!-- Dates -->
                    <div class="form-group row">
                        <label for="dated" class="col-sm-3 col-form-label">Dated</label>
                        <div class="col-sm-9">
                            <input type="date" class="form-control" id="dated" name="dated" value="<?php echo date('Y-m-d'); ?>" required>
                            <input type="hidden" name="dated_Hidden" value="<?php echo date('Y-m-d'); ?>">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="dueDate" class="col-sm-3 col-form-label">Due Date</label>
                        <div class="col-sm-9">
                            <input type="date" class="form-control" id="dueDate" name="dueDate" value="<?php echo date('Y-m-d'); ?>" required>
                            <input type="hidden" name="dueDate_Hidden" value="<?php echo date('Y-m-d'); ?>">
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="form-group row">
                        <label for="description" class="col-sm-3 col-form-label">Description</label>
                        <div class="col-sm-9">
                            <textarea class="form-control" id="description" name="description"></textarea>
                        </div>
                    </div>

                    <!-- Accounts, Quantity, Price -->
                    <div class="form-group row ">
                        <label class="col-sm-3 col-form-label">Amount</label>
                        <div class="col-sm-9">
                            <input type="number" step="0.01" name="price[]" class="form-control" value="10">
                            <input type="hidden" name="basePrice[]" value="20">
                            <table class="table table-sm table-bordered d-none">
                                <thead>
                                <tr>
                                    <th>Account</th>
                                    <th>Description</th>
                                    <th>Qty</th>
                                    <th>Price</th>
                                    <th>Total</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td>
                                        <select name="accounts[]" class="form-control">
                                            <option value="<?php echo $account ?>" selected>39</option>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="text" name="desc[]" class="form-control" value="">
                                    </td>
                                    <td>
                                        <input type="number" step="1" name="quantity[]" class="form-control" value="1">
                                    </td>
                                    <td>

                                    </td>
                                    <td>
                                        <input type="text" class="form-control" name="line_total[]" value="300" readonly>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Total -->
                    <div class="form-group row d-none">
                        <label for="total" class="col-sm-3 col-form-label">Total</label>
                        <div class="col-sm-9">
                            <input type="number" step="0.01" class="form-control" id="total" name="total" value="30000" readonly>
                        </div>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Save Bill</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    jQuery(document).ready(function() {

        var priceInput = jQuery("#addBillModal input[name='price[]']");
        var quantityInput = jQuery("#addBillModal input[name='quantity[]']");
        var basePrice = jQuery("#addBillModal input[name='basePrice[]']");
        var line_total = jQuery("#addBillModal input[name='line_total[]']");
        var totalDisplay = jQuery("#addBillModal #total");

        function updateTotal() {
            const quantity = parseFloat(quantityInput.val()) || 1;
            const price = parseFloat(priceInput.val()) || 0;

            const total = quantity * price;

            basePrice.val(total.toFixed(2));
            line_total.val(total.toFixed(2));
            totalDisplay.val(total.toFixed(2));
        }


        quantityInput.on('input', updateTotal);
        priceInput.on('input', updateTotal);


        updateTotal();
    });
</script>