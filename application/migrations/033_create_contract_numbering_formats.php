<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_Contract_Numbering_Formats extends CI_Migration {

    public function up()
    {
        $this->db->trans_start(); // Start a transaction for atomicity

        // --- 1. Drop the table first if it exists to ensure a clean state ---
        $this->dbforge->drop_table('contract_numbering_formats', TRUE);

        // --- 2. Create the contract_numbering_formats table ---
        $this->dbforge->add_field([
            'id' => [
                'type' => 'BIGINT',
                'constraint' => 20,
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ],
            'name' => [
                'type' => 'NVARCHAR',
                'constraint' => '100',
                'null' => FALSE,
            ],
            'description' => [
                'type' => 'NVARCHAR',
                'constraint' => '255',
                'null' => TRUE,
            ],
            'pattern' => [
                'type' => 'NVARCHAR',
                'constraint' => '100',
                'null' => FALSE,
            ],
            'example' => [
                'type' => 'NVARCHAR',
                'constraint' => '100',
                'null' => FALSE,
            ],
            'prefix' => [
                'type' => 'NVARCHAR',
                'constraint' => '20',
                'null' => TRUE,
                'default' => 'CT',
            ],
            'suffix' => [
                'type' => 'NVARCHAR',
                'constraint' => '20',
                'null' => TRUE,
                'default' => null,
            ],
            'fixed_code' => [
                'type' => 'NVARCHAR',
                'constraint' => '20',
                'null' => TRUE,
                'default' => null,
            ],
            'sequence_reset' => [
                'type' => 'NVARCHAR',
                'constraint' => '20',
                'null' => FALSE,
            ],
            'sequence_length' => [
                'type' => 'BIGINT',
                'constraint' => 20,
                'null' => FALSE,
                'default' => 3,
            ],
            'is_active' => [
                'type' => 'BIT',
                'null' => FALSE,
                'default' => 1,
            ],
            'last_sequence' => [
                'type' => 'BIGINT',
                'constraint' => 20,
                'null' => FALSE,
                'default' => 0,
            ],
            'last_reset_date' => [
                'type' => 'DATETIME2',
                'null' => TRUE,
            ],
            'CONSTRAINT pk_contract_numbering_formats PRIMARY KEY (id)',
            'CONSTRAINT chk_sequence_reset CHECK (sequence_reset IN (\'never\', \'monthly\', \'yearly\', \'daily\'))'
        ]);
        $this->dbforge->create_table('contract_numbering_formats', TRUE);

        // Add the unique index for is_active
        $this->db->query("CREATE UNIQUE INDEX ux_one_active_format ON contract_numbering_formats (is_active) WHERE is_active = 1;");


        // --- 3. Insert your formats ---
        $data = [
            [
                'name' => 'Sheria360 Standard Format',
                'description' => 'Your current format with fixed prefix',
                'pattern' => 'PREFIX/SEQ/MM/YYYY',
                'example' => 'CA/SCM/070/001/08/2023',
                'prefix' => 'CA/SCM/070',
                'suffix' => null,
                'fixed_code' => null,
                'sequence_reset' => 'never',
                'sequence_length' => 3,
                'is_active' => 1,
                'last_sequence' => 0,
                'last_reset_date' => null,
            ],
            [
                'name' => 'Monthly Reset',
                'description' => 'Resets sequence each month',
                'pattern' => 'PREFIX/YYYY/MM/SEQ',
                'example' => 'CT/2023/08/001',
                'prefix' => 'CT',
                'suffix' => null,
                'fixed_code' => null,
                'sequence_reset' => 'monthly',
                'sequence_length' => 3,
                'is_active' => 0,
                'last_sequence' => 0,
                'last_reset_date' => null,
            ],
            [
                'name' => 'Department Monthly',
                'description' => 'Includes department code',
                'pattern' => 'PREFIX/DEPT/YYYY/MM/SEQ',
                'example' => 'CT/FIN/2023/08/001',
                'prefix' => 'CT',
                'suffix' => null,
                'fixed_code' => null,
                'sequence_reset' => 'monthly',
                'sequence_length' => 3,
                'is_active' => 0,
                'last_sequence' => 0,
                'last_reset_date' => null,
            ],
            [
                'name' => 'Yearly Reset',
                'description' => 'Resets sequence each year',
                'pattern' => 'PREFIX/YYYY/SEQ',
                'example' => 'CT/2023/001',
                'prefix' => 'CT',
                'suffix' => null,
                'fixed_code' => null,
                'sequence_reset' => 'yearly',
                'sequence_length' => 4,
                'is_active' => 0,
                'last_sequence' => 0,
                'last_reset_date' => null,
            ],
            [
                'name' => 'Continuous',
                'description' => 'Never resets sequence',
                'pattern' => 'PREFIX/SEQ',
                'example' => 'CT/0001',
                'prefix' => 'CT',
                'suffix' => null,
                'fixed_code' => null,
                'sequence_reset' => 'never',
                'sequence_length' => 5,
                'is_active' => 0,
                'last_sequence' => 0,
                'last_reset_date' => null,
            ]
        ];

        foreach ($data as $row) {
            $exists = $this->db->get_where('contract_numbering_formats', ['name' => $row['name']])->num_rows();
            if ($exists == 0) {
                // Directly insert the row. The CodeIgniter DB driver will now
                // correctly handle the missing date fields by letting the database
                // use its specified defaults.
                $this->db->insert('contract_numbering_formats', $row);
            }
        }

        // --- 4. Create or Alter the stored procedure ---
        $this->db->query("
            CREATE OR ALTER PROCEDURE sp_get_new_contract_ref_number
                @deptCode NVARCHAR(20) = NULL,
                @newRefNumber NVARCHAR(200) OUTPUT
            AS
            BEGIN
                SET NOCOUNT ON;

                DECLARE @formatId BIGINT,
                        @pattern NVARCHAR(100),
                        @prefix NVARCHAR(20),
                        @suffix NVARCHAR(20),
                        @fixed_code NVARCHAR(20),
                        @sequence_reset NVARCHAR(20),
                        @sequence_length BIGINT,
                        @last_sequence BIGINT,
                        @last_reset_date DATETIME2,
                        @next_sequence BIGINT,
                        @today DATETIME2 = GETDATE(),
                        @year CHAR(4) = FORMAT(GETDATE(), 'yyyy'),
                        @month CHAR(2) = FORMAT(GETDATE(), 'MM'),
                        @day CHAR(2) = FORMAT(GETDATE(), 'dd');

                -- 1. Get active format (lock it for update)
                SELECT TOP 1
                    @formatId = id,
                    @pattern = pattern,
                    @prefix = prefix,
                    @suffix = suffix,
                    @fixed_code = fixed_code,
                    @sequence_reset = sequence_reset,
                    @sequence_length = sequence_length,
                    @last_sequence = last_sequence,
                    @last_reset_date = last_reset_date
                FROM contract_numbering_formats WITH (UPDLOCK, ROWLOCK)
                WHERE is_active = 1;

                IF @formatId IS NULL
                BEGIN
                    RAISERROR('No active contract numbering format found.', 16, 1);
                    RETURN;
                END

                -- 2. Determine if reset is required
                DECLARE @resetNeeded BIT = 0;

                IF @sequence_reset = 'yearly'
                    AND (@last_reset_date IS NULL OR YEAR(@last_reset_date) <> YEAR(@today))
                    SET @resetNeeded = 1;
                ELSE IF @sequence_reset = 'monthly'
                    AND (@last_reset_date IS NULL OR FORMAT(@last_reset_date, 'yyyyMM') <> FORMAT(@today, 'yyyyMM'))
                    SET @resetNeeded = 1;
                ELSE IF @sequence_reset = 'daily'
                    AND (@last_reset_date IS NULL OR CAST(@last_reset_date AS DATE) <> CAST(@today AS DATE))
                    SET @resetNeeded = 1;

                IF @resetNeeded = 1
                    SET @last_sequence = 0;

                -- 3. Increment sequence
                SET @next_sequence = @last_sequence + 1;

                -- 4. Update format table
                UPDATE contract_numbering_formats
                SET last_sequence = @next_sequence,
                    last_reset_date = @today
                WHERE id = @formatId;

                -- 5. Pad sequence
                DECLARE @seqStr NVARCHAR(20) = RIGHT(REPLICATE('0', @sequence_length) + CAST(@next_sequence AS NVARCHAR), @sequence_length);

                -- 6. Replace tokens in pattern
                SET @newRefNumber = REPLACE(@pattern, 'PREFIX', ISNULL(@prefix,''));
                SET @newRefNumber = REPLACE(@newRefNumber, 'SEQ', @seqStr);
                SET @newRefNumber = REPLACE(@newRefNumber, 'YYYY', @year);
                SET @newRefNumber = REPLACE(@newRefNumber, 'MM', @month);
                SET @newRefNumber = REPLACE(@newRefNumber, 'DD', @day);
                SET @newRefNumber = REPLACE(@newRefNumber, 'DEPT', ISNULL(@deptCode,'GEN'));
                SET @newRefNumber = REPLACE(@newRefNumber, 'SUFFIX', ISNULL(@suffix,''));
                SET @newRefNumber = REPLACE(@newRefNumber, 'FIXED', ISNULL(@fixed_code,''));
            END;
        ");

        $this->db->trans_complete(); // Complete the transaction
    }

    public function down()
    {
        $this->db->trans_start(); // Start a transaction for atomicity

        // --- 1. Drop the stored procedure ---
        $drop_procedure_sql = "
            IF OBJECT_ID('sp_get_new_contract_ref_number', 'P') IS NOT NULL
            BEGIN
                DROP PROCEDURE sp_get_new_contract_ref_number;
            END
        ";
        $this->db->query($drop_procedure_sql);

        // --- 2. Drop the table and all its dependents (index) ---
        if ($this->db->table_exists('contract_numbering_formats')) {
            $this->dbforge->drop_table('contract_numbering_formats', TRUE);
        }

        $this->db->trans_complete(); // Complete the transaction
    }
}
