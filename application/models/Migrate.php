<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migrate extends My_Model_Factory {}

class mysqli_Migrate extends My_Model {

    public $_table = 'migrations';
    public $primary_key = 'version';
    protected $modelName = "migration";

    protected $_listFieldName = "version";
    protected $_fieldsNames = ["version"];


    public function __construct() {
        parent::__construct();
        $this->ci =& get_instance();

        $this->ci->load->library('migration');
        $this->ci->load->dbforge(); // Ensure dbforge is loaded
    }

    /**
     * Initialize migrations table
     */
    public function install_migrations() {
        if (!$this->ci->db->table_exists($this->_table)) {
            $this->ci->dbforge->add_field([
                $this->primary_key => [
                    'type' => 'BIGINT',
                    'constraint' => 20,
                ]
            ]);
            $this->ci->dbforge->add_key($this->primary_key, TRUE);
            $this->ci->dbforge->create_table($this->_table, TRUE);

            // Set initial version
            $this->ci->db->insert($this->_table, [$this->primary_key => 0]);
            return true;
        }
        return false;
    }

    /**
     * Run all pending migrations
     */
    public function run_migrations() {
        $this->install_migrations();

        if ($this->ci->migration->latest() === FALSE) {
            log_message('error', $this->ci->migration->error_string());
            return [
                'success' => false,
                'message' => $this->ci->migration->error_string()
            ];
        }

        return [
            'success' => true,
            'message' => 'Migrations completed successfully'
        ];
    }

    /**
     * Run specific migration version
     */
    public function run_migration_to($version) {
        $this->install_migrations();

        if ($this->ci->migration->version($version)) {
            return [
                'success' => true,
                'message' => "Migrated to version $version successfully"
            ];
        } else {
            log_message('error', $this->ci->migration->error_string());
            return [
                'success' => false,
                'message' => $this->ci->migration->error_string()
            ];
        }
    }

    /**
     * Get current migration version
     */
    public function get_current_version() {
        $row = $this->ci->db->get($this->_table)->row();
        return $row ? $row->{$this->primary_key} : 0;
    }

    /**
     * Reset migrations (for development)
     */
    public function reset_migrations() {
        $this->ci->migration->version(0);
        return $this->run_migrations();
    }
}

class mysql_Migrate extends mysqli_Migrate {}
class sqlsrv_Migrate extends mysqli_Migrate {
    public function latest() {
        return $this->ci->migration->latest();
    }
}
