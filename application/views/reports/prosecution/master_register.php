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
            <li class="breadcrumb-item active"><?php echo $this->lang->line("master_register");?></li>
        </ul>
    </div>
    <div class=" row col-xs-12 d-flex form-group p-0 ">
        <div class=" row form-group col-4">

            <label for="yearSelect col-5">Select Year</label>
            <select class="form-control col-6" id="yearSelect" name="year">
                <option value="2020">2020/2021</option>
                <option value="2021">2021/2022</option>
                <option value="2022">2022/2023</option>
                <option value="2023">2023/2024</option>
                <option value="2024">20242025</option>
                <option value="2025">2025/2026</option>
            </select>
        </div>

        <div class=" row form-group col-4">
            <button class="btn btn-primary align-bottom">Filter</button>
        </div>
    </div>

    <div class="container-fluid">
        <h2 class="mt-4 mb-3">Enforcement Cases Master Register</h2>

        <div class="table-responsive">
            <table class="table table-striped table-bordered table-hover">
                <thead class="thead-grayish">
                <tr>
                    <th>S/NO.</th>
                    <th>Financial Year (When Detection/Enforcement Undertaken)</th>
                    <th>Entity/Section Filing Complaint/Request (Origin of Matter)</th>
                    <th>Approval for Enforcement</th>
                    <th>Name of Accused/Suspect</th>
                    <th>Place & Date of Arrest/Occurrence</th>
                    <th>Case Reference (Police/Court)</th>
                    <th>Brief of Case, Offence Matter & Category of Operations</th>
                    <th>Case Status/Position (e.g., PBC, PUI, PAKA, etc.)</th>
                    <th>Investigating Officer (I/O)</th>
                    <th>Remarks (e.g., Observations, Challenges)</th>
                </tr>
                </thead>
                <tbody>
                <!-- Sample Row 1 -->
                <tr>
                    <td>1</td>
                    <td>2024/2025</td>
                    <td>Telecom Regulatory Authority</td>
                    <td>Yes</td>
                    <td>John Doe</td>
                    <td>Nairobi, 15-Jan-2025</td>
                    <td>CR/123/2025</td>
                    <td>Illegal SIM card distribution - Telecom Offences</td>
                    <td>PUI - Pending Under Investigation</td>
                    <td>Officer A. Smith</td>
                    <td>Delayed evidence collection</td>
                </tr>
                <!-- Sample Row 2 -->
                <tr>
                    <td>2</td>
                    <td>2024/2025</td>
                    <td>Consumer Protection Unit</td>
                    <td>Yes</td>
                    <td>Jane Roe</td>
                    <td>Mombasa, 20-Feb-2025</td>
                    <td>CR/456/2025</td>
                    <td>Fraudulent electronic transactions - Consumer Protection</td>
                    <td>PAKA - Pending Awaiting Key Action</td>
                    <td>Officer B. Jones</td>
                    <td>Awaiting court scheduling</td>
                </tr>
                <!-- Add more rows as needed -->
                </tbody>
            </table>
        </div>
    </div>