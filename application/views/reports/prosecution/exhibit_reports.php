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
            <li class="breadcrumb-item active"><?php echo $this->lang->line("exhibit_reports");?></li>
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
    <h2 class="mt-4 mb-3">Exhibit Management</h2>

    <div class="table-responsive">
        <table class="table table-striped table-bordered table-hover">
            <thead class="thead-grayish">
            <tr>
                <th>S/NO.</th>
                <th>Case Reference (File No)</th>
                <th>Court (& Court No)</th>
                <th>Parties</th>
                <th>Description of Exhibit and Identifying Markings</th>
                <th>Date Received</th>
                <th>Temporary Removals (Reason and Date)</th>
                <th>Manner of Disposal (including dates)</th>
                <th>Signature of Recipient and Date</th>
                <th>Signature of Officer Disposing of Exhibit</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>1</td>
                <td>CF E517/2022</td>
                <td>Kisumu Law Courts (Court 3)</td>
                <td>State vs. Austin Action John</td>
                <td>FM Transmitter, Serial No. FMT-1234</td>
                <td>10/01/2022</td>
                <td>Forensic Analysis, 15/02/2025</td>
                <td>Returned to owner, 20/04/2025</td>
                <td>Signed by recipient, 20/04/2025</td>
                <td>Signed by officer</td>
            </tr>
            <tr>
                <td>2</td>
                <td>CF E789/2023</td>
                <td>Nairobi Law Courts (Court 1)</td>
                <td>State vs. Mary Jane Kimani</td>
                <td>Box of SIM Cards, Batch No. SIM-4567</td>
                <td>05/03/2023</td>
                <td>Court presentation, 10/03/2025</td>
                <td>Destroyed, 25/03/2025</td>
                <td></td>
                <td>Signed by officer</td>
            </tr>
            <tr>
                <td>3</td>
                <td>CF E321/2024</td>
                <td>Mombasa Law Courts (Court 2)</td>
                <td>State vs. Peter Otieno</td>
                <td>Radio Antenna, ID: RA-7890</td>
                <td>12/05/2024</td>
                <td>Technical testing, 18/02/2025</td>
                <td>Pending disposal</td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td>4</td>
                <td>CF E654/2023</td>
                <td>Eldoret Law Courts (Court 4)</td>
                <td>State vs. Sarah Wanjiku</td>
                <td>Laptop, Serial No. LP-2345</td>
                <td>20/07/2023</td>
                <td>Data extraction, 05/02/2025</td>
                <td>Retained as evidence</td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td>5</td>
                <td>CF E987/2024</td>
                <td>Nakuru Law Courts (Court 5)</td>
                <td>State vs. James Kipkorir</td>
                <td>Courier Van, Reg. No. KBC 123X</td>
                <td>15/09/2024</td>
                <td>Inspection, 28/02/2025</td>
                <td>Auctioned, 10/04/2025</td>
                <td>Signed by recipient, 10/04/2025</td>
                <td>Signed by officer</td>
            </tr>
            <tr>
                <td>6</td>
                <td>CF E147/2023</td>
                <td>Kisii Law Courts (Court 1)</td>
                <td>State vs. Anne Muthoni</td>
                <td>Broadcast Mixer, ID: BM-5678</td>
                <td>03/11/2023</td>
                <td>Court display, 12/03/2025</td>
                <td>Pending disposal</td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td>7</td>
                <td>CF E258/2024</td>
                <td>Nyeri Law Courts (Court 3)</td>
                <td>State vs. David Mwangi</td>
                <td>Telecom Router, Serial No. TR-9012</td>
                <td>25/01/2024</td>
                <td>N/A</td>
                <td>Destroyed, 01/04/2025</td>
                <td></td>
                <td>Signed by officer</td>
            </tr>
            <tr>
                <td>8</td>
                <td>CF E369/2023</td>
                <td>Thika Law Courts (Court 2)</td>
                <td>State vs. Esther Njeri</td>
                <td>Postal Parcels, Batch No. PP-3456</td>
                <td>10/04/2023</td>
                <td>Fraud analysis, 20/02/2025</td>
                <td>Retained as evidence</td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td>9</td>
                <td>CF E741/2024</td>
                <td>Meru Law Courts (Court 4)</td>
                <td>State vs. Paul Kamau</td>
                <td>Signal Jammer, ID: SJ-6789</td>
                <td>30/06/2024</td>
                <td>Technical review, 15/03/2025</td>
                <td>Pending disposal</td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td>10</td>
                <td>CF E852/2023</td>
                <td>Garissa Law Courts (Court 1)</td>
                <td>State vs. Fatima Hassan</td>
                <td>Satellite Dish, Serial No. SD-0123</td>
                <td>18/08/2023</td>
                <td>Court evidence, 25/03/2025</td>
                <td>Returned to owner, 05/04/2025</td>
                <td>Signed by recipient, 05/04/2025</td>
                <td>Signed by officer</td>
            </tr>
            </tbody>
        </table>
    </div>
</div>