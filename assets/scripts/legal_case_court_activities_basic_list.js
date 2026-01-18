// This script fetches and displays basic court activity records for a specific case ID
function fetchBasicRecords(id){
    $.ajax({
        url: getBaseURL()+"cases/readBasicCourtActivityList/",
        type: "POST",
        dataType: "JSON",
        data: {
            case_id: id,
            action: "getBasicCourtActivityList", 
            limit: 10, // or whatever limit value you need
            take: 5,
            skip: 0,
            page: 1,
            pageSize: 5,
            legal_case_id: id,
            returnData: 1,
        },
        beforeSend: function() {
            jQuery('#loader-global').show();
        },
        success: function(response) {
            if(response && response.data) {
                renderCActivityData(response.data);
            } else {
                $('#hearing-data').html('<tr><td colspan="5" class="text-center text-danger">Invalid data format received</td></tr>');
            }
        },
        error: function(xhr, status, error) {
            $('#hearing-data').html('<tr><td colspan="5" class="text-center text-danger">Failed to load hearing data: ' + error + '</td></tr>');
        },
        complete: function() {
            jQuery('#loader-global').hide();
        }
    });

    // Function to render hearing data
    function renderCActivityData(hearings) {
        const tbody = $('#hearing-data');
        tbody.empty();
        
        hearings.forEach(hearing => {
            const scheduledDateTime = hearing.startDate ? 
                `${formatDate(hearing.startDate)} ${hearing.startTime ? hearing.startTime.substring(0,5) : ''}` : 'N/A';
            
           
            
            const createdOn = hearing.createdOn ? new Date(hearing.createdOn).toLocaleString() : 'N/A';
            
            tbody.append(`
                <tr>
                    <td>
                        <a href="javascript:;" onclick="legalCaseHearingForm('${hearing.id}','', '${hearing.legal_case_id}'); return false;">
                            ${hearing.hearingID || 'N/A'}
                        </a>
                    </td>
                    <td>${hearing.type_name || 'Unknown'}</td>
                    <td>${scheduledDateTime}</td>
                     <td>${hearing.comments}</td>
                 
                    <td>${createdOn}</td>
                </tr>
            `);
        });
        
        if(hearings.length === 0) {
            tbody.html('<tr><td colspan="5" class="text-center">No hearings found</td></tr>');
        }
    }

    // Helper function to format date (YYYY-MM-DD to more readable format)
    function formatDate(dateString) {
        if (!dateString) return 'N/A';
        const options = { year: 'numeric', month: 'short', day: 'numeric' };
        return new Date(dateString).toLocaleDateString(undefined, options);
    }
};