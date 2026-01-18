<div class="container-fluid mt-4">
    <h3 class="mb-3">Legal Advisory Opinions Database</h3>
    <p class="text-muted">Search results can be sorted by Date, Agency, or Case Type for easier navigation.</p>

    <div class="table-responsive">
        <table class="table table-bordered table-striped" id="opinionsTable">
            <thead class="thead-dark">
            <tr>
                <th scope="col" onclick="sortTable(0)">Date <span class="sort-icon">⇅</span></th>
                <th scope="col" onclick="sortTable(1)">Agency <span class="sort-icon">⇅</span></th>
                <th scope="col" onclick="sortTable(2)">Case Type <span class="sort-icon">⇅</span></th>
                <th scope="col">Opinion Summary</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>2025-07-21</td>
                <td>Attorney General’s Office</td>
                <td>Constitutional Law</td>
                <td>Opinion on the interpretation of constitutional amendment procedures.</td>
            </tr>
            <tr>
                <td>2025-06-14</td>
                <td>Ministry of Justice</td>
                <td>Contract Law</td>
                <td>Guidance on public procurement contract compliance.</td>
            </tr>
            <tr>
                <td>2025-05-03</td>
                <td>Parliamentary Counsel</td>
                <td>Administrative Law</td>
                <td>Clarification on the scope of delegated legislation powers.</td>
            </tr>
            </tbody>
        </table>
    </div>
</div>

<script>
    function sortTable(n) {
        var table, rows, switching, i, x, y, shouldSwitch, dir, switchcount = 0;
        table = document.getElementById("opinionsTable");
        switching = true;
        dir = "asc";

        while (switching) {
            switching = false;
            rows = table.rows;
            for (i = 1; i < (rows.length - 1); i++) {
                shouldSwitch = false;
                x = rows[i].getElementsByTagName("TD")[n];
                y = rows[i + 1].getElementsByTagName("TD")[n];

                if (dir == "asc") {
                    if (x.innerHTML.toLowerCase() > y.innerHTML.toLowerCase()) {
                        shouldSwitch = true;
                        break;
                    }
                } else if (dir == "desc") {
                    if (x.innerHTML.toLowerCase() < y.innerHTML.toLowerCase()) {
                        shouldSwitch = true;
                        break;
                    }
                }
            }
            if (shouldSwitch) {
                rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
                switching = true;
                switchcount ++;
            } else {
                if (switchcount == 0 && dir == "asc") {
                    dir = "desc";
                    switching = true;
                }
            }
        }
    }
</script>
