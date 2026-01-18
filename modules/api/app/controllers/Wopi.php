
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Wopi extends CI_Controller
{
    // GET /wopi/files/{id}
    public function file_metadata($id)
    {
        // Authenticate and authorize the request using the access_token
        // Fetch file info from your database
        $file = $this->db->get_where('documents_management_system', ['id' => $id])->row_array();
        if (!$file) {
            show_404();
        }

        $response = [
            'BaseFileName' => $file['name'] . '.' . $file['extension'],
            'Size' => (int)$file['size'],
            'OwnerId' => (string)$file['createdBy'],
            'Version' => (string)$file['version'],
            'UserId' => (string)$this->session->userdata('user_id'),
            'UserCanWrite' => true, // Set according to your permissions
            'SupportsLocks' => true,
            'SupportsUpdate' => true,
            'UserFriendlyName' => $this->session->userdata('user_name'),
        ];
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($response));
    }

    // GET/POST /wopi/files/{id}/contents
    public function file_contents($id)
    {
        // Authenticate and authorize the request using the access_token
        $file = $this->db->get_where('documents_management_system', ['id' => $id])->row_array();
        if (!$file) {
            show_404();
        }
        $file_path = '/path/to/your/files/' . $file['name'] . '.' . $file['extension'];

        if ($this->input->method() === 'get') {
            // Return the file contents
            $this->output
                ->set_content_type(mime_content_type($file_path))
                ->set_output(file_get_contents($file_path));
        } elseif ($this->input->method() === 'post') {
            // Save the uploaded file content
            file_put_contents($file_path, file_get_contents('php://input'));
            $this->output->set_status_header(200);
        }
    }


    // Add WOPI Lock support (required for editing in Office for the web)
    // POST /wopi/files/{id}
    public function handle_post($id)
    {
        // Parse WOPI override header
        $wopi_override = $this->input->get_request_header('X-WOPI-Override', true);

        switch (strtoupper($wopi_override)) {
            case 'LOCK':
                // Handle lock request (store lock info in your DB or cache)
                // Respond with 200 OK and X-WOPI-Lock header
                $lock_id = $this->input->get_request_header('X-WOPI-Lock', true);
                // TODO: Save lock_id for $id in your storage
                $this->output
                    ->set_header('X-WOPI-Lock: ' . $lock_id)
                    ->set_status_header(200);
                break;
            case 'UNLOCK':
                // Handle unlock request (remove lock info)
                // TODO: Remove lock for $id in your storage
                $this->output->set_status_header(200);
                break;
            case 'REFRESH_LOCK':
                // Handle refresh lock (update lock timestamp)
                // TODO: Refresh lock for $id in your storage
                $this->output->set_status_header(200);
                break;
            case 'GET_LOCK':
                // Return current lock value
                // TODO: Retrieve lock for $id from your storage
                $lock_id = ''; // Set to current lock value or empty
                $this->output
                    ->set_header('X-WOPI-Lock: ' . $lock_id)
                    ->set_status_header(200);
                break;
            case 'PUT':
                // Save file content (already handled in file_contents)
                $this->output->set_status_header(200);
                break;
            default:
                // Unsupported operation
                $this->output->set_status_header(400);
        }
    }

}