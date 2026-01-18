<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!-- Acts Affected View: standalone fragment to plug into the LDIMS module layout -->
<div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="mb-0">Existing Acts / Regulations Affected/To update</h3>
    <div>
        <button class="btn btn-sm btn-primary" id="btnAddAct">+ Link New Act</button>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table id="actsTable" class="table table-striped table-bordered" style="width:100%">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Reference</th>
                    <th>Title</th>
                    <th>Jurisdiction</th>
                    <th>Type</th>
                    <th>Affected Sections</th>
                    <th>Impact</th>
                    <th>Last Reviewed</th>
                    <th>Notes</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $acts = [
                    [1,'CAP 123','Income Tax Act','National','Act','s.12, s.18','High','2024-11-10','Tax computation definitions changed'],
                    [2,'ENV-45','Environmental Management and Coordination Act','National','Act','Part IV, s.59','Medium','2024-06-21','Additions to waste disposal rules'],
                    [3,'HLTH-77','Public Health Act','National','Act','s.3, s.12A','High','2025-01-08','Quarantine powers to be clarified'],
                    [4,'EDU-12','Education Act','National','Act','s.45,s.46','Low','2023-12-05','Minor wording updates'],
                    [5,'TRA-09','Road Traffic Act','National','Act','s.7, s.9','Medium','2024-09-15','Speed limits and enforcement changes'],
                    [6,'ICT-02','Data Protection Regulation','National','Regulation','Reg.4, Reg.8','High','2025-03-02','Alignment with new privacy rules'],
                    [7,'WAT-11','Water Services Act','County-level','Act','s.20,s.21','Medium','2024-05-30','Water resource allocation updates'],
                    [8,'ENG-31','Energy Regulation','National','Regulation','cl.3, cl.7','Medium','2024-08-19','Efficiency standards amended']
                ];

                foreach($acts as $a){
                    // short display for affected sections
                    $sections = htmlspecialchars($a[5]);
                    $notes = htmlspecialchars($a[8]);
                    echo "<tr>
              <td>{$a[0]}</td>
              <td>{$a[1]}</td>
              <td>{$a[2]}</td>
              <td>{$a[3]}</td>
              <td>{$a[4]}</td>
              <td>{$sections}</td>
              <td>{$a[6]}</td>
              <td>{$a[7]}</td>
              <td class='text-truncate' style='max-width:200px'>{$notes}</td>
              <td>
                <button class='btn btn-sm btn-info btn-view-act' data-id='{$a[0]}'>View</button>
                <button class='btn btn-sm btn-outline-secondary btn-map-draft' data-id='{$a[0]}'>Map to Draft</button>
                <button class='btn btn-sm btn-outline-primary btn-add-note' data-id='{$a[0]}'>Note</button>
              </td>
            </tr>";
                }
                ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- View Act Modal -->
<div class="modal fade" id="viewActModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Act Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <div id="actDetails"></div>
                <hr />
                <h6>Affected Sections</h6>
                <ul id="actSections"></ul>
                <hr />
                <h6>Related Drafts</h6>
                <div id="relatedDrafts">(none linked)</div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Map to Draft Modal -->
<div class="modal fade" id="mapDraftModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="mapDraftForm">
                <div class="modal-header">
                    <h5 class="modal-title">Map Act to Draft</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="map_act_id" name="act_id" />
                    <div class="form-group">
                        <label for="map_draft">Select Draft</label>
                        <select id="map_draft" name="draft_id" class="form-control">
                            <option value="1">Tax Reform Bill (REF-2025-001)</option>
                            <option value="2">Environmental Protection Act Amendment (REF-2025-002)</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="map_sections">Sections / Clauses affected (comma-separated)</label>
                        <input id="map_sections" name="sections" class="form-control" />
                    </div>
                    <div class="form-group">
                        <label for="map_notes">Notes</label>
                        <textarea id="map_notes" name="notes" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button class="btn btn-primary" type="submit">Save Mapping</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Note Modal -->
<div class="modal fade" id="addNoteModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <form id="addNoteForm">
                <div class="modal-header">
                    <h5 class="modal-title">Add Note</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="note_act_id" name="act_id" />
                    <div class="form-group">
                        <label for="note_text">Note</label>
                        <textarea id="note_text" name="note_text" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button class="btn btn-primary" type="submit">Save Note</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .text-truncate { white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
</style>

<script>
    $(function(){
        $('#actsTable').DataTable({ pageLength: 10 });

        // View Act
        $('.btn-view-act').on('click', function(){
            var id = $(this).data('id');
            // In production, fetch via AJAX. Here we read from DOM table row for demo.
            var $tr = $(this).closest('tr');
            var ref = $tr.find('td').eq(1).text();
            var title = $tr.find('td').eq(2).text();
            var jurisdiction = $tr.find('td').eq(3).text();
            var type = $tr.find('td').eq(4).text();
            var sections = $tr.find('td').eq(5).text().split(',').map(function(s){return s.trim();});
            var notes = $tr.find('td').eq(8).text();
            var html = '<p><strong>'+ref+' — '+title+'</strong><br/><small>'+type+' &middot; '+jurisdiction+'</small></p><p class="small text-muted">'+notes+'</p>';
            $('#actDetails').html(html);
            var list = '';
            sections.forEach(function(s){ list += '<li>'+s+'</li>'; });
            $('#actSections').html(list);
            $('#viewActModal').modal('show');
        });

        // Map to Draft
        $('.btn-map-draft').on('click', function(){
            var id = $(this).data('id');
            $('#map_act_id').val(id);
            $('#mapDraftModal').modal('show');
        });

        $('#mapDraftForm').on('submit', function(e){
            e.preventDefault();
            // submit mapping via AJAX in production
            $('#mapDraftModal').modal('hide');
            alert('Mapping saved (demo)');
        });

        // Add note
        $('.btn-add-note').on('click', function(){
            var id = $(this).data('id');
            $('#note_act_id').val(id);
            $('#addNoteModal').modal('show');
        });

        $('#addNoteForm').on('submit', function(e){
            e.preventDefault();
            $('#addNoteModal').modal('hide');
            alert('Note saved (demo)');
        });

        // Add new act (stub)
        $('#btnAddAct').on('click', function(){
            alert('Open a form to link a new act (implementation pending)');
        });
    });
</script>
