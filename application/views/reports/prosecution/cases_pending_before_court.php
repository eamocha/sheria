<style>
    .table-responsive {
        margin: 20px 0;
    }
    th, td {
        text-align: center;
    }
    .thead-grayish {
        background-color: #6c757d;
        color: white;
    }
    .thead-grayish th {
        border-color: #5a6268;
    }
</style>
<div class="container-fluid">
    <div class="col-12">
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><?php echo $this->lang->line("reports");?> / <a href="<?php  echo site_url("reports/case_other_reports/");?>"><?php echo $this->lang->line("other_reports");?></a></li>
            <li class="breadcrumb-item active"><?php echo $this->lang->line("cases_pending_before_court");?></li>
        </ul>
    </div>
    <div class="container mt-4">
        <form>
            <div class="form-row align-items-end">
                <!-- Month Dropdown -->
                <div class="col-md-4">
                    <label for="monthSelect">Select Month</label>
                    <select class="form-control" id="monthSelect" name="month">
                        <option value="1">January</option>
                        <option value="2">February</option>
                        <option value="3">March</option>
                        <option value="4">April</option>
                        <option value="5">May</option>
                        <option value="6">June</option>
                        <option value="7">July</option>
                        <option value="8">August</option>
                        <option value="9">September</option>
                        <option value="10">October</option>
                        <option value="11">November</option>
                        <option value="12">December</option>
                    </select>
                </div>

                <!-- Year Dropdown -->
                <div class="col-md-4">
                    <label for="yearSelect">Select Year</label>
                    <select class="form-control" id="yearSelect" name="year">
                        <option value="2020">2020</option>
                        <option value="2021">2021</option>
                        <option value="2022">2022</option>
                        <option value="2023">2023</option>
                        <option value="2024">2024</option>
                        <option value="2025">2025</option>
                    </select>

                </div>

                <!-- Filter Button -->
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary btn-block">Filter</button>
                </div>
            </div>
        </form>
    </div>
    <h2 class="mt-4 mb-3">Current Cases Pending Before Court (Monthly Case Status Report)</h2>

    <div class="table-responsive">
        <table class="table table-striped table-bordered table-hover">
            <thead class="thead-grayish">
            <tr>
                <th>S/NO.</th>
                <th>Case Reference</th>
                <th>Accused</th>
                <th>Offence</th>
                <th>Court</th>
                <th>Status</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>1</td>
                <td>KISUMU LAW COURTS<br>CR627/181/2022<br>CF E517/2022</td>
                <td>AUSTIN ACTION JOHN</td>
                <td>Establishing FM Station without a valid license</td>
                <td>Kisumu Law Courts</td>
                <td>Next Hearing 05/02/2025</td>
            </tr>
            <tr>
                <td>2</td>
                <td>NAIROBI LAW COURTS<br>CR123/456/2023<br>CF E789/2023</td>
                <td>MARY JANE KIMANI</td>
                <td>Illegal SIM card distribution</td>
                <td>Nairobi Law Courts</td>
                <td>Pending Judgment 15/03/2025</td>
            </tr>
            <tr>
                <td>3</td>
                <td>MOMBASA LAW COURTS<br>CR987/654/2024<br>CF E321/2024</td>
                <td>PETER OTIENO</td>
                <td>Unauthorized use of radio frequency</td>
                <td>Mombasa Law Courts</td>
                <td>Next Hearing 20/04/2025</td>
            </tr>
            <tr>
                <td>4</td>
                <td>ELDORET LAW COURTS<br>CR456/789/2023<br>CF E654/2023</td>
                <td>SARAH WANJIKU</td>
                <td>Fraudulent electronic transactions</td>
                <td>Eldoret Law Courts</td>
                <td>Mention 10/02/2025</td>
            </tr>
            <tr>
                <td>5</td>
                <td>NAKURU LAW COURTS<br>CR321/123/2024<br>CF E987/2024</td>
                <td>JAMES KIPKORIR</td>
                <td>Operating unlicensed courier service</td>
                <td>Nakuru Law Courts</td>
                <td>Pending Under Investigation 28/02/2025</td>
            </tr>
            <tr>
                <td>6</td>
                <td>KISII LAW COURTS<br>CR654/321/2023<br>CF E147/2023</td>
                <td>ANNE MUTHONI</td>
                <td>Broadcasting without approval</td>
                <td>Kisii Law Courts</td>
                <td>Next Hearing 12/03/2025</td>
            </tr>
            <tr>
                <td>7</td>
                <td>NYERI LAW COURTS<br>CR789/456/2024<br>CF E258/2024</td>
                <td>DAVID MWANGI</td>
                <td>Sale of unapproved telecom equipment</td>
                <td>Nyeri Law Courts</td>
                <td>Judgment Delivered 01/04/2025</td>
            </tr>
            <tr>
                <td>8</td>
                <td>THIKA LAW COURTS<br>CR147/258/2023<br>CF E369/2023</td>
                <td>ESTHER NJERI</td>
                <td>Consumer fraud via postal services</td>
                <td>Thika Law Courts</td>
                <td>Next Hearing 25/02/2025</td>
            </tr>
            <tr>
                <td>9</td>
                <td>MERU LAW COURTS<br>CR258/369/2024<br>CF E741/2024</td>
                <td>PAUL KAMAU</td>
                <td>Interference with telecom systems</td>
                <td>Meru Law Courts</td>
                <td>Pending Awaiting Key Action 18/03/2025</td>
            </tr>
            <tr>
                <td>10</td>
                <td>GARISSA LAW COURTS<br>CR369/741/2023<br>CF E852/2023</td>
                <td>FATIMA HASSAN</td>
                <td>Illegal satellite broadcasting</td>
                <td>Garissa Law Courts</td>
                <td>Next Hearing 30/03/2025</td>
            </tr>
            </tbody>
        </table>
    </div>
</div>

