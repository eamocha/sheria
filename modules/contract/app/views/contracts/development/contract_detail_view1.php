<style>
    .timeline {
        list-style: none;
        padding: 10px 0 10px;
        position: relative;
    }

    .timeline:before {
        top: 0;
        bottom: 0;
        position: absolute;
        content: " ";
        width: 3px;
        background-color: #eeeeee;
        left: 50%;
        margin-left: -1.5px;
    }

    .timeline > li {
        margin-bottom: 0x;
        position: relative;
    }

    .timeline > li:before,
    .timeline > li:after {
        content: " ";
        display: table;
    }

    .timeline > li:after {
        clear: both;
    }

    .timeline > li > .timeline-panel {
        width: 46%;
        float: left;
        border: 1px solid #d4d4d4;
        border-radius: 2px;
        padding: 10px;
        position: relative;
        -webkit-box-shadow: 0 1px 6px rgba(0, 0, 0, 0.175);
        box-shadow: 0 1px 6px rgba(0, 0, 0, 0.175);
    }

    .timeline > li > .timeline-panel:before {
        position: absolute;
        top: 26px;
        right: -15px;
        display: inline-block;
        border-top: 15px solid transparent;
        border-left: 15px solid #ccc;
        border-right: 0 solid #ccc;
        border-bottom: 15px solid transparent;
        content: " ";
    }

    .timeline > li > .timeline-panel:after {
        position: absolute;
        top: 27px;
        right: -14px;
        display: inline-block;
        border-top: 14px solid transparent;
        border-left: 14px solid #fff;
        border-right: 0 solid #fff;
        border-bottom: 14px solid transparent;
        content: " ";
    }

    .timeline > li > .timeline-badge {
        color: #fff;
        width: 50px;
        height: 50px;
        line-height: 50px;
        font-size: 1.4em;
        text-align: center;
        position: absolute;
        top: 16px;
        left: 50%;
        margin-left: -25px;
        background-color: #999999;
        z-index: 100;
        border-top-right-radius: 50%;
        border-top-left-radius: 50%;
        border-bottom-right-radius: 50%;
        border-bottom-left-radius: 50%;
    }

    .timeline > li.timeline-inverted > .timeline-panel {
        float: right;
    }

    .timeline > li.timeline-inverted > .timeline-panel:before {
        border-left-width: 0;
        border-right-width: 15px;
        left: -15px;
        right: auto;
    }

    .timeline > li.timeline-inverted > .timeline-panel:after {
        border-left-width: 0;
        border-right-width: 14px;
        left: -14px;
        right: auto;
    }

    .timeline-badge.primary {
        background-color: #008CBA !important;
    }

    .timeline-badge.success {
        background-color: #43AC6A !important;
    }

    .timeline-badge.warning {
        background-color: #E99002 !important;
    }

    .timeline-badge.danger {
        background-color: #F04124 !important;
    }

    .timeline-badge.info {
        background-color: #5BC0DE !important;
    }

    .timeline-title {
        margin-top: 0;
        color: inherit;
    }

    .timeline-body > p,
    .timeline-body > ul {
        margin-bottom: 0;
    }

    .timeline-body > p + p {
        margin-top: 5px;
    }
</style>

<div class="container">
    <div class="page-header">
        <h3 id="timeline" class="text-primary">The Plumbing Company [**-****6541]</h3>
        <small>
            1200 Ridgecrest Blvd.<br/>
            Fayetteville, AR 72704
        </small>
    </div>
    <ul class="timeline">
        <li>
            <div class="timeline-badge primary">
                <i class="fa fa-check"></i>
            </div>
            <div class="timeline-panel">
                <div class="timeline-heading">
                    <h5 class="timeline-title">Office Added</h5>
                    <p>
                        <small class="text-muted">
                            <i class="fa fa-clock-o"></i>
                            11 hours ago via The Nature Boy
                        </small>
                    </p>
                </div>
                <div class="timeline-body">
                    <p>Permissions to perform work for the Fort Smith (FOR) office were added by Ric Flair.</p>
                    <a href="http:ubh.com"></a>
                </div>
            </div>
        </li>
        <li class="timeline-inverted">
            <div class="timeline-badge success">
                <i class="fa fa-check"></i>
            </div>
            <div class="timeline-panel">
                <div class="timeline-heading">
                    <h5 class="timeline-title">Document Approved</h5>
                    <p>
                        <small class="text-muted">
                            <i class="fa fa-clock-o"></i>
                            10 hours ago via Roddy Piper</small>
                    </p>
                </div>
                <div class="timeline-body">
                    <p>A new version of the W-9 document was approved.</p>
                </div>
            </div>
        </li>
        <li>
            <div class="timeline-badge danger">
                <i class="fa fa-cog"></i>
            </div>
            <div class="timeline-panel">
                <div class="timeline-heading">
                    <h5 class="timeline-title">Data Change Rejected</h5>
                    <p>
                        <small class="text-muted">
                            <i class="fa fa-clock-o"></i></i> 2 days ago via Rick Martel</small>
                    </p>
                </div>
                <div class="timeline-body">
                    <p>The address data change was rejected.</p>
                </div>
            </div>
        </li>
        <li class="timeline-inverted">
            <div class="timeline-badge warning">
                <i class="fa fa-calendar"></i>
            </div>
            <div class="timeline-panel">
                <div class="timeline-heading">
                    <h5 class="timeline-title">Data Change Requested</h5>
                    <p>
                        <small class="text-muted">
                            <i class="glyphicon glyphicon-time"></i> 3 days ago via Adrian Adonis</small>
                    </p>
                </div>
                <div class="timeline-body">
                    <p>A change to the vendor TIN was requested.</p>
                </div>
            </div>
        </li>
        <li>
            <div class="timeline-badge info">
                <i class="fa fa-file-pdf-o"></i></div>
            <div class="timeline-panel">
                <div class="timeline-heading">
                    <h5 class="timeline-title">W-9 Document Uploaded for Approval</h5>
                    <p>
                        <small class="text-muted">
                            <i class="glyphicon glyphicon-time"></i> 4 days ago via Bob Orton</small>
                    </p>
                </div>
                <div class="timeline-body">
                    <p>Bob uploaded a new version of the W-9 document. A vendor workflow for approval was initiated.</p>
                </div>
            </div>
        </li>
        <li class="timeline-inverted">
            <div class="timeline-badge success">
                <i class="fa fa-thumbs-o-up"></i></div>
            <div class="timeline-panel">
                <div class="timeline-heading">
                    <h5 class="timeline-title">Vendor Approved</h5>
                    <p>
                        <small class="text-muted">
                            <i class="glyphicon glyphicon-time"></i> 5 months ago via Randy Savage</small>
                    </p>
                </div>
                <div class="timeline-body">
                    <p>The vendor was approved and added to the system.</p>
                </div>
            </div>
        </li>
    </ul>
</div>