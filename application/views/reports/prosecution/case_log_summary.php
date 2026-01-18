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
            <li class="breadcrumb-item active"><?php echo $this->lang->line("case_log_summary");?></li>
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
    <h2 class="mt-4 mb-3">Case Log/Inventory of Cases</h2>

    <div class="table-responsive">
        <table class="table table-striped table-bordered table-hover">
            <thead class="thead-grayish">
            <tr>
                <th>Serial No.</th>
                <th>Case/Inquiry No. allocated and/or OB No</th>
                <th>Nature of case/complaint (brief)</th>
                <th>Case Reference (Court & Police Station - CF & PCR No.)</th>
                <th>Name of Accused & with address/contacts</th>
                <th>Offence</th>
                <th>Case Status (e.g., Ongoing, Closed, etc.)</th>
                <th>Investigating Officer</th>
                <th>Remarks</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>1</td>
                <td>OB/123/2025</td>
                <td>Illegal FM station setup</td>
                <td>Kisumu Law Courts CF E517/2022<br>PCR 627/181/2022</td>
                <td>Austin Action John, Kisumu, 0712-345-678</td>
                <td>Establishing FM Station without a valid license</td>
                <td>Next Hg 05/02/2025 - Mention</td>
                <td>Off. J. Okoth</td>
                <td>Awaiting license verification</td>
            </tr>
            <tr>
                <td>2</td>
                <td>OB/456/2025</td>
                <td>SIM card fraud</td>
                <td>Nairobi Law Courts CF E789/2023<br>PCR 123/456/2023</td>
                <td>Mary Jane Kimani, Nairobi, 0723-456-789</td>
                <td>Illegal SIM card distribution</td>
                <td>Jdgmnt 15/03/2025 - Final arguments heard</td>
                <td>Off. P. Mwangi</td>
                <td>Evidence submitted</td>
            </tr>
            <tr>
                <td>3</td>
                <td>OB/789/2025</td>
                <td>Radio interference</td>
                <td>Mombasa Law Courts CF E321/2024<br>PCR 987/654/2024</td>
                <td>Peter Otieno, Mombasa, 0734-567-890</td>
                <td>Unauthorized use of radio frequency</td>
                <td>Next Hg 20/04/2025 - Witness testimony</td>
                <td>Off. A. Hassan</td>
                <td>Technical report pending</td>
            </tr>
            <tr>
                <td>4</td>
                <td>OB/101/2025</td>
                <td>Online fraud</td>
                <td>Eldoret Law Courts CF E654/2023<br>PCR 456/789/2023</td>
                <td>Sarah Wanjiku, Eldoret, 0745-678-901</td>
                <td>Fraudulent electronic transactions</td>
                <td>Mn 10/02/2025 - Case mention</td>
                <td>Off. L. Chebet</td>
                <td>Bank records requested</td>
            </tr>
            <tr>
                <td>5</td>
                <td>OB/202/2025</td>
                <td>Unlicensed courier</td>
                <td>Nakuru Law Courts CF E987/2024<br>PCR 321/123/2024</td>
                <td>James Kipkorir, Nakuru, 0756-789-012</td>
                <td>Operating unlicensed courier service</td>
                <td>PUI 28/02/2025 - Investigation ongoing</td>
                <td>Off. K. Rotich</td>
                <td>Site inspection scheduled</td>
            </tr>
            <tr>
                <td>6</td>
                <td>OB/303/2025</td>
                <td>Unapproved broadcast</td>
                <td>Kisii Law Courts CF E147/2023<br>PCR 654/321/2023</td>
                <td>Anne Muthoni, Kisii, 0767-890-123</td>
                <td>Broadcasting without approval</td>
                <td>Next Hg 12/03/2025 - Plea taking</td>
                <td>Off. S. Onyango</td>
                <td>Broadcast equipment seized</td>
            </tr>
            <tr>
                <td>7</td>
                <td>OB/404/2025</td>
                <td>Equipment violation</td>
                <td>Nyeri Law Courts CF E258/2024<br>PCR 789/456/2024</td>
                <td>David Mwangi, Nyeri, 0778-901-234</td>
                <td>Sale of unapproved telecom equipment</td>
                <td>Closed 01/04/2025 - Convicted</td>
                <td>Off. M. Kariuki</td>
                <td>Fine imposed</td>
            </tr>
            <tr>
                <td>8</td>
                <td>OB/505/2025</td>
                <td>Postal scam</td>
                <td>Thika Law Courts CF E369/2023<br>PCR 147/258/2023</td>
                <td>Esther Njeri, Thika, 0789-012-345</td>
                <td>Consumer fraud via postal services</td>
                <td>Next Hg 25/02/2025 - Evidence presentation</td>
                <td>Off. R. Njoroge</td>
                <td>Multiple complainants</td>
            </tr>
            <tr>
                <td>9</td>
                <td>OB/606/2025</td>
                <td>System interference</td>
                <td>Meru Law Courts CF E741/2024<br>PCR 258/369/2024</td>
                <td>Paul Kamau, Meru, 0790-123-456</td>
                <td>Interference with telecom systems</td>
                <td>PAKA 18/03/2025 - Awaiting prosecution</td>
                <td>Off. T. Mutua</td>
                <td>Technical analysis ongoing</td>
            </tr>
            <tr>
                <td>10</td>
                <td>OB/707/2025</td>
                <td>Satellite misuse</td>
                <td>Garissa Law Courts CF E852/2023<br>PCR 369/741/2023</td>
                <td>Fatima Hassan, Garissa, 0701-234-567</td>
                <td>Illegal satellite broadcasting</td>
                <td>Next Hg 30/03/2025 - Defense hearing</td>
                <td>Off. H. Ali</td>
                <td>Cross-border implications</td>
            </tr>
            </tbody>
        </table>
    </div>
</div>