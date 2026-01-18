<div class="container-fluid mt-4">
    <h3 class="mb-3 ">Opinion Version Control</h3>




    <!-- Version History Table -->
    <h5 class="mt-3">Version History Log</h5>
    <table class="table table-bordered table-hover">
        <thead class="">
        <tr>
            <th>Version</th>
            <th>Date</th>
            <th>Author</th>
            <th>Summary of Changes</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td>v1.0</td>
            <td>2025-06-12</td>
            <td>Attorney A</td>
            <td>Initial opinion issued</td>
            <td><button class="btn btn-sm btn-outline-primary">View</button></td>
        </tr>
        <tr>
            <td>v1.1</td>
            <td>2025-07-05</td>
            <td>Attorney B</td>
            <td>Case references added</td>
            <td><button class="btn btn-sm btn-outline-primary">View</button></td>
        </tr>
        <tr>
            <td>v2.0</td>
            <td>2025-08-20</td>
            <td>Attorney C</td>
            <td>Conclusion revised, expanded scope</td>
            <td><button class="btn btn-sm btn-success">Compare</button></td>
        </tr>
        </tbody>
    </table>

    <!-- Comparison Tool -->
    <h5 class="mt-4">Comparison View: v1.1 → v2.0</h5>
    <div class="card shadow-sm mb-3">
        <div class="card-body">
            <p>
                <strong>Before:</strong> The matter falls under
                <span class="text-danger">Data Protection Act only.</span>
            </p>
            <p>
                <strong>After:</strong> The matter falls under
                <span class="text-success">Data Protection Act and Constitutional Law provisions.</span>
            </p>
            <p>
                <strong>References:</strong>
                Opinion #120
                <span class="text-success">, Opinion #123</span>
            </p>
        </div>
    </div>

    <!-- Legend -->
    <div class="alert alert-info">
        <strong>Legend:</strong>
        <span class="text-success">Green = Additions</span> |
        <span class="text-danger">Red = Deletions</span> |
        <span class="text-warning">Orange = Modified text</span>
    </div>
</div>
