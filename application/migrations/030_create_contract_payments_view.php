<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_Contract_Payments_View extends CI_Migration {

    private $view_name = 'contract_payments_view';

    public function up()
    {
        $this->db->trans_start(); // Start a transaction for atomicity

        $view_sql = "
            CREATE OR ALTER VIEW dbo.{$this->view_name} AS
            SELECT
                c.id AS contract_id,
                CAST(ROUND(
                    COALESCE(SUM(cm.amount), 0),
                    2
                ) AS DECIMAL(19,2)) AS amount_paid_so_far,
                
                CAST(ROUND(
                    CASE
                        WHEN c.value IS NULL THEN 0
                        WHEN c.value = 0 THEN 0
                        ELSE c.value - COALESCE(SUM(cm.amount), 0)
                    END,
                    2
                ) AS DECIMAL(19,2)) AS balance_due
            FROM
                dbo.contract c
            LEFT JOIN
                dbo.contract_milestone cm
                    ON cm.contract_id = c.id
                    AND cm.financial_status IN ('paid', 'partially_paid')
            GROUP BY
                c.id, c.value;
        ";

        // Execute the CREATE OR ALTER VIEW statement
        $this->db->query($view_sql);

        $this->db->trans_complete(); // Complete the transaction
    }

    public function down()
    {
        $this->db->trans_start(); // Start a transaction for atomicity

        // Safe way to drop a view in SQL Server
        $drop_sql = "
            IF OBJECT_ID('dbo.{$this->view_name}', 'V') IS NOT NULL
            DROP VIEW dbo.{$this->view_name};
        ";
        $this->db->query($drop_sql);

        $this->db->trans_complete(); // Complete the transaction
    }
}
