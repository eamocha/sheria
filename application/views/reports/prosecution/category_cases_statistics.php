
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
            <li class="breadcrumb-item active"><?php echo $this->lang->line("category_cases_statistics");?></li>
        </ul>
    </div>
    <div class=" row col-xs-12 d-flex form-group p-0 ">
        <div class=" row form-group col-4">

            <label for="yearSelect col-5">Select Year</label>
            <select class="form-control col-6" id="yearSelect" name="year">
                <option value="2020">2020</option>
                <option value="2021">2021</option>
                <option value="2022">2022</option>
                <option value="2023">2023</option>
                <option value="2024">2024</option>
                <option value="2025">2025</option>
            </select>
        </div>
        <div class=" row form-group col-4">

            <label for="QSelect col-5 ">Quarter</label>
            <select class=" form-control col-6" id="qSelect" name="quarter">
                <option value="2020">Q1</option>
                <option value="2021">Q2</option>
                <option value="2022">Q3</option>
                <option value="2023">Q4</option>

            </select>
        </div>
        <div class=" row form-group col-4">
            <button class="btn btn-primary align-bottom">Filter</button>
        </div>
    </div>

        

    <h2 class="mt-4 mb-3">Statistics of Cases Handled - Quarter 1</h2>
    <div class="table-responsive">
        <table class="table table-striped table-bordered table-hover">
            <thead class="thead-grayish">
            <tr>
                <th rowspan="2">Broad Nature of Cases/Category</th>
                <th rowspan="2">No. of cases reported/detected</th>
                <th colspan="3">Cases in Progress</th>
                <th colspan="4">Cases Closed</th>
                <th rowspan="2">Totals</th>
            </tr>
            <tr>
                <th>PBC</th>
                <th>PUI</th>
                <th>PAKA</th>
                <th>Withdrawn</th>
                <th>Acquitted</th>
                <th>Convicted</th>
                <th>Total Closed</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>Telecommunications offences</td>
                <td>10</td>
                <td>2</td>
                <td>3</td>
                <td>1</td>
                <td>1</td>
                <td>2</td>
                <td>1</td>
                <td>4</td>
                <td>10</td>
            </tr>
            <tr>
                <td>SIM Card offences</td>
                <td>8</td>
                <td>1</td>
                <td>2</td>
                <td>1</td>
                <td>2</td>
                <td>1</td>
                <td>1</td>
                <td>4</td>
                <td>8</td>
            </tr>
            <tr>
                <td>Type Approval/Equipment offences</td>
                <td>5</td>
                <td>1</td>
                <td>1</td>
                <td>0</td>
                <td>1</td>
                <td>1</td>
                <td>1</td>
                <td>3</td>
                <td>5</td>
            </tr>
            <tr>
                <td>Electronic Transactions/Systems</td>
                <td>12</td>
                <td>3</td>
                <td>2</td>
                <td>2</td>
                <td>2</td>
                <td>1</td>
                <td>2</td>
                <td>5</td>
                <td>12</td>
            </tr>
            <tr>
                <td>Radio Communication/Frequency</td>
                <td>7</td>
                <td>2</td>
                <td>1</td>
                <td>1</td>
                <td>1</td>
                <td>1</td>
                <td>1</td>
                <td>3</td>
                <td>7</td>
            </tr>
            <tr>
                <td>Broadcasting offences</td>
                <td>6</td>
                <td>1</td>
                <td>1</td>
                <td>1</td>
                <td>1</td>
                <td>0</td>
                <td>2</td>
                <td>3</td>
                <td>6</td>
            </tr>
            <tr>
                <td>Postal/Courier offences</td>
                <td>4</td>
                <td>0</td>
                <td>1</td>
                <td>1</td>
                <td>1</td>
                <td>0</td>
                <td>1</td>
                <td>2</td>
                <td>4</td>
            </tr>
            <tr>
                <td>Consumer Protection Offences</td>
                <td>9</td>
                <td>2</td>
                <td>2</td>
                <td>1</td>
                <td>1</td>
                <td>1</td>
                <td>2</td>
                <td>4</td>
                <td>9</td>
            </tr>
            <tr>
                <td>Others</td>
                <td>3</td>
                <td>1</td>
                <td>0</td>
                <td>0</td>
                <td>1</td>
                <td>0</td>
                <td>1</td>
                <td>2</td>
                <td>3</td>
            </tr>
            <tr class="font-weight-bold">
                <td>Totals at the end of Quarter 1</td>
                <td>64</td>
                <td>13</td>
                <td>13</td>
                <td>8</td>
                <td>11</td>
                <td>7</td>
                <td>12</td>
                <td>30</td>
                <td>64</td>
            </tr>
            </tbody>
        </table>
    </div>
</div>