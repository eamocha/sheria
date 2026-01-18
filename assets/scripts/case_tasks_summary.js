// Description: This script initializes a Kendo UI Grid with sample data and custom templates for document status and type.
function getCaseTasksSummary(legalCaseId) {
    $.ajax({
       
        url:  getBaseURL()+"cases/getCaseTasksSummary/",
        type: "POST",
        dataType: "JSON",
        data: {
            legal_case_id: legalCaseId, 
           
        },
        beforeSend: function () {
            $("#loader-global").show(); 
        },
        success: function (response) {
            if (response && response.length > 0) {
                renderTaskData(response); 
            } else if (response && response.length === 0) {
                $('#task-data').html('<tr><td colspan="8" class="text-center text-danger">No tasks found for this case.</td></tr>'); // Clear old data
            }
             else {
                $("#task-data").html(
                    '<tr><td colspan="8" class="text-center text-danger">Invalid data format received from server.</td></tr>'
                ); // handle error
            }
        },
        error: function (xhr, status, error) {
            $("#task-data").html(
                '<tr><td colspan="8" class="text-center text-danger">Error loading task data: ' +
                error +
                "</td></tr>"
            ); // show the error
        },
        complete: function () {
            $("#loader-global").hide(); // Hide the loader
        },
    });

    // Function to render the task data into the table.
    function renderTaskData(tasks) {
        const tbody = $("#task-data"); 
        tbody.empty(); // Clear any existing table rows.

        tasks.forEach((task) => {
            const row = `
          <tr>
            <td>${task.taskType || "N/A"}</td>
            <td>
              <a href="${getBaseURL()}/tasks/view/${task.id}" target="_blank">
                ${task.title || "N/A"}
              </a>
            </td>            
            <td>${task.assigned_to || "N/A"}</td>        
            <td>${task.taskStatus || "N/A"}</td>
            <td>${task.due_date || "N/A"}</td>
          </tr>
        `;
            tbody.append(row); // Append the row to the table body.
        });
    }
}


