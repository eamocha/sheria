<style>
    body { background-color: #f8f9fa; }
    .card { margin-bottom: 20px; }
    .payments-table td, .payments-table th { font-size: 0.9rem; }
    .badge-paid { background: #28a745; color: #fff; }
    .badge-pending { background: #ffc107; color: #000; }
    .pagination { justify-content: center; }
</style>

<div class="container-fluid">
    <h3 class="mb-4"><i class="fa fa-file-invoice-dollar"></i> Fee Notes</h3>

    <div class="input-group mb-3">
        <input type="text" id="searchBox" class="form-control" placeholder="Search by invoice, vendor or description...">
        <div class="input-group-append">
            <button class="btn btn-primary" id="searchBtn"><i class="fa fa-search"></i> Search</button>
        </div>
    </div>

    <div id="invoices-container"></div>
    <nav><ul class="pagination mt-4" id="pagination"></ul></nav>
</div>

<script>
    let currentPage = 1;
    const limit = 10;

    function fetchInvoices(page = 1, search = '') {
        showLoader(true);
        $('#invoices-container').html('');
        $('#pagination').html('');

        $.getJSON('<?= base_url("feenotes/fetch") ?>', { page, limit, search }, function(res) {
            showLoader(false);

            if (res.error) {
                $('#invoices-container').html('<div class="alert alert-danger">' + res.error + '</div>');
                return;
            }

            if (!res.data.length) {
                $('#invoices-container').html('<div class="alert alert-warning">No fee notes found.</div>');
                return;
            }

            renderInvoices(res.data);
            renderPagination(res.total, res.page, res.limit, search);
        }).fail(function() {
            showLoader(false);
            $('#invoices-container').html('<div class="alert alert-danger">Failed to load invoices.</div>');
        });
    }

    function renderInvoices(invoices) {
        invoices.forEach(inv => {
            const paidBadge = inv.PendingBalance == 0
                ? '<span class="badge badge-paid">Paid</span>'
                : '<span class="badge badge-pending">Pending</span>';

            let paymentsHtml = '';
            if (inv.Payments && inv.Payments.length > 0) {
                paymentsHtml = `
        <table class="table table-sm table-bordered payments-table mt-3">
          <thead class="thead-light">
            <tr>
              <th>Payment No</th><th>Date</th><th>Description</th><th>Paid By</th><th class="text-right">Amount</th>
            </tr>
          </thead>
          <tbody>
            ${inv.Payments.map(p => `
              <tr>
                <td>${p.PaymentNo}</td>
                <td>${p.PaymentDate}</td>
                <td>${p.Description}</td>
                <td>${p.PaidBy}</td>
                <td class="text-right">${Number(p.PaymentAmount).toLocaleString()}</td>
              </tr>
            `).join('')}
          </tbody>
        </table>`;
            } else {
                paymentsHtml = `<p class="text-muted mt-2">No payments recorded yet.</p>`;
            }

            const cardHtml = `
      <div class="card shadow-sm">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Invoice: <strong>${inv.InvoiceNumber}</strong></h5>
            ${paidBadge}
          </div>
          <p class="text-muted mb-1">${inv.InvoiceDescription}</p>

          <div class="row mt-2">
            <div class="col-md-3"><strong>Vendor No:</strong> ${inv.LemisVendorNumber}</div>
            <div class="col-md-3"><strong>Sage No:</strong> ${inv.SageVendorNumber}</div>
            <div class="col-md-3"><strong>Invoice Amount:</strong> ${Number(inv.InvoiceAmount).toLocaleString()}</div>
            <div class="col-md-3"><strong>Paid:</strong> ${Number(inv.PaidAmount).toLocaleString()}</div>
          </div>
          <div class="row">
            <div class="col-md-3"><strong>Pending:</strong> ${Number(inv.PendingBalance).toLocaleString()}</div>
          </div>

          ${paymentsHtml}
        </div>
      </div>`;
            $('#invoices-container').append(cardHtml);
        });
    }

    function renderPagination(total, current, limit, search) {
        const totalPages = Math.ceil(total / limit);
        if (totalPages <= 1) return;

        let html = '';
        for (let i = 1; i <= totalPages; i++) {
            html += `<li class="page-item ${i === current ? 'active' : ''}">
               <a href="#" class="page-link" data-page="${i}">${i}</a>
             </li>`;
        }
        $('#pagination').html(html);
        $('#pagination .page-link').click(function(e) {
            e.preventDefault();
            fetchInvoices(parseInt($(this).data('page')), $('#searchBox').val());
        });
    }

    $('#searchBtn').click(() => fetchInvoices(1, $('#searchBox').val()));
    $('#searchBox').keypress(e => { if (e.which === 13) fetchInvoices(1, $('#searchBox').val()); });

    $(document).ready(() => fetchInvoices());
</script>
