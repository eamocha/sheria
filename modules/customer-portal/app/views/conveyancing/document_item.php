<?php
foreach($data as $doc)
{?>
    <div class="document-item">
        <span><?php  echo $doc['name']?></span>
        <p class="small text-muted">Uploaded: <?php echo  $doc['display_created_on']?> by <?php echo $doc['display_creator_full_name']?></p>
   <a href="javascript:;" onclick="downloadFile(<?=$doc['module_record_id']?>,false,'customer-portal','conveyancing')" class="btn btn-sm ">
            <i class="fas fa-download"></i> Download
        </a>

    <a href="javascript:;" onclick="deleteDocument(<?=$doc['id']?>,<?=$doc['module_record_id']?>);" class="btn btn-sm ">
        <i class="fas fa-trash"></i> Delete
    </a>
    </div><?php
} ?>

<script type="text/javascript">

    function deleteDocument(documentId, conveyancingId) {
        if (!documentId || !conveyancingId) {
            pinesMessage({ty: 'error', m: 'Error: Document or Conveyancing ID is missing for deletion.'});
            return;
        }

        if (confirm("Are you sure you want to delete this document? This action cannot be undone.")) {
            jQuery.ajax({
                url: getBaseURL("customer-portal")+"conveyancing/delete_document",
                type: 'POST',
                dataType: 'json',
                data: {
                    document_id: documentId,
                    module_record_id: conveyancingId,
                    module: "conveyancing"
                },
                beforeSend: function () {
                    jQuery('#loader-global').show();
                },
                success: function (response) {
                    jQuery('#loader-global').hide();
                    if (response.success) {
                        pinesMessage({ty: 'success', m: response.message || 'Document deleted successfully.'});
                        // Reload the documents after successful deletion
                        loadDocuments();
                    } else {
                        pinesMessage({ty: 'error', m: response.message || 'Failed to delete document.'});
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    jQuery('#loader-global').hide();
                    console.error("AJAX Error (Delete Document): " + textStatus, errorThrown, jqXHR.responseText);
                    pinesMessage({ty: 'error', m: 'An error occurred during document deletion.'});

                    // defaultAjaxJSONErrorsHandler(jqXHR, textStatus, errorThrown);
                }
            });
        }
    }

</script>