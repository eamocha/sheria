<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require "Top_controller.php";
class Migrations extends Top_controller {

    public function __construct()
    {
        parent::__construct();

        // Only allow CLI in production
        if (ENVIRONMENT === 'production' && !$this->input->is_cli_request()) {
            show_error('Migrations can only be run via CLI in production', 403); //Added 403
        }

        $this->load->library('migration'); // Use CodeIgniter's migration library
        $this->load->database();
        //   $this->load->library('logger'); // Load the logger library
    }

    public function index()
    {
        if (!$this->migration->latest()) {
            $error_message = $this->migration->error_string();
            //     $this->logger->error('Migration failed: ' . $error_message); // Log the error
            echo 'Migration failed: ' . $error_message . PHP_EOL;
            exit(1);
        }

        //    $this->logger->info('Migrations completed successfully.');
        echo 'Migrations completed successfully.' . PHP_EOL;
        exit(0);
    }

    public function version($version)
    {
        if (!$this->migration->version($version)) {
            $error_message = $this->migration->error_string();
            //     $this->logger->error("Migration to version $version failed: " . $error_message);
            echo "Migration to version $version failed: " . $error_message . PHP_EOL;
            exit(1);
        }

        //   $this->logger->info("Migrated to version $version successfully.");
        echo "Migrated to version $version successfully." . PHP_EOL;
        exit(0);
    }

    public function current()
    {
        $version = $this->migration->current();
        if ($version === FALSE) {
            //  $this->logger->error("Could not get current migration version.");
            echo "Could not get current migration version." . PHP_EOL;
            exit(1);
        }
        echo "Current migration version: $version" . PHP_EOL;
        exit(0);
    }

    public function reset()
    {
        if (ENVIRONMENT !== 'development') {
            show_error('Reset is only allowed in development environment', 403); //Added 403
        }

        if (!$this->migration->version(0)) { // Migrate to version 0 to reset
            $error_message = $this->migration->error_string();
            //  $this->logger->error('Migration reset failed: ' . $error_message);
            echo 'Migration reset failed: ' . $error_message . PHP_EOL;
            exit(1);
        }

        // $this->logger->info('Migrations reset successfully.');
        echo 'Migrations reset successfully.' . PHP_EOL;
        exit(0);
    }
}
