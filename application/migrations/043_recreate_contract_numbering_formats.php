<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Recreate_Contract_Numbering_Formats extends CI_Migration {

    private $table = 'contract_numbering_formats';
    private $sp_name = 'sp_get_new_contract_ref_number';

    public function up()
    {
        $this->db->trans_start(); // Start a transaction for atomicity

        // --- 1. Drop the table first if it exists ---
        $this->dbforge->drop_table($this->table, TRUE);

        // --- 2. Create the 'contract_numbering_formats' table using raw SQL ---
        $create_table_sql = "
            CREATE TABLE {$this->table} (
                id INT IDENTITY(1,1) PRIMARY KEY,
                name VARCHAR(100) NOT NULL,
                description VARCHAR(255) NULL,
                pattern VARCHAR(100) NOT NULL,
                example VARCHAR(100) NOT NULL,
                prefix VARCHAR(20) DEFAULT 'CT',
                suffix VARCHAR(20) NULL,
                fixed_code VARCHAR(20) NULL,
                sequence_reset VARCHAR(20) NOT NULL CHECK (sequence_reset IN ('never', 'monthly', 'yearly', 'daily')),
                sequence_length INT NOT NULL DEFAULT 3,
                is_active BIT NOT NULL DEFAULT 1,
                last_sequence INT NOT NULL DEFAULT 0,
                last_reset_date DATE NULL,
                created_at DATETIME2 DEFAULT SYSDATETIME()
            )
        ";
        $this->db->query($create_table_sql);

        // --- 3. Create UNIQUE INDEX for is_active = 1 (Filtered Index) ---
        $index_sql = "
            IF NOT EXISTS (SELECT name FROM sys.indexes WHERE name = 'ux_one_active_format' AND object_id = OBJECT_ID('{$this->table}'))
            BEGIN
                CREATE UNIQUE INDEX ux_one_active_format
                ON {$this->table} (is_active)
                WHERE is_active = 1;
            END
        ";
        $this->db->query($index_sql);

        // --- 4. Insert Default Formats ---
        $data = [
            [
                'name' => 'Sheria360 Standard Format',
                'description' => 'Your current format with fixed prefix',
                'pattern' => 'PREFIXSEQ/MM/YYYY',
                'example' => 'S360/SCM/070/001/08/2023',
                'prefix' => 'CA/SCM/070/',
                'sequence_reset' => 'never',
                'sequence_length' => 3,
                'is_active' => 1,
                'last_sequence' => 0,
            ],
            [
                'name' => 'Monthly Reset',
                'description' => 'Resets sequence each month',
                'pattern' => 'PREFIX/YYYY/MM/SEQ',
                'example' => 'CT/2023/08/001',
                'prefix' => 'CT',
                'sequence_reset' => 'monthly',
                'sequence_length' => 3,
                'is_active' => 0,
                'last_sequence' => 0,
            ],
            [
                'name' => 'Department Monthly',
                'description' => 'Includes department code',
                'pattern' => 'PREFIX/DEPT/YYYY/MM/SEQ',
                'example' => 'CT/FIN/2023/08/001',
                'prefix' => 'CT',
                'sequence_reset' => 'monthly',
                'sequence_length' => 3,
                'is_active' => 0,
                'last_sequence' => 0,
            ],
            [
                'name' => 'Yearly Reset',
                'description' => 'Resets sequence each year',
                'pattern' => 'PREFIX/YYYY/SEQ',
                'example' => 'CT/2023/001',
                'prefix' => 'CT',
                'sequence_reset' => 'yearly',
                'sequence_length' => 4,
                'is_active' => 0,
                'last_sequence' => 0,
            ],
            [
                'name' => 'Continuous',
                'description' => 'Never resets sequence',
                'pattern' => 'PREFIX/SEQ',
                'example' => 'CT/0001',
                'prefix' => 'CT',
                'sequence_reset' => 'never',
                'sequence_length' => 5,
                'is_active' => 0,
                'last_sequence' => 0,
            ]
        ];

        // Insert data - let the database handle created_at default
        foreach ($data as $row) {
            $this->db->insert($this->table, $row);
        }

        // --- 5. Create or Alter Stored Procedure ---
        $sp_sql = "
            CREATE OR ALTER PROCEDURE dbo.{$this->sp_name}
                @deptCode NVARCHAR(20) = NULL,
                @newRefNumber NVARCHAR(200) OUTPUT
            AS
            BEGIN
                SET NOCOUNT ON;

                DECLARE @formatId INT,
                        @pattern NVARCHAR(100),
                        @prefix NVARCHAR(20),
                        @suffix NVARCHAR(20),
                        @fixed_code NVARCHAR(20),
                        @sequence_reset NVARCHAR(20),
                        @sequence_length INT,
                        @last_sequence INT,
                        @last_reset_date DATE,
                        @next_sequence INT,
                        @today DATE = CAST(GETDATE() AS DATE),
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
                FROM {$this->table} WITH (UPDLOCK, ROWLOCK)
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
                    AND (@last_reset_date IS NULL OR @last_reset_date <> @today)
                    SET @resetNeeded = 1;

                IF @resetNeeded = 1
                    SET @last_sequence = 0;

                -- 3. Increment sequence
                SET @next_sequence = @last_sequence + 1;

                -- 4. Update format table
                UPDATE {$this->table}
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
        ";
        $this->db->query($sp_sql);

        $this->db->trans_complete(); // Complete the transaction
    }

    public function down()
    {
        $this->db->trans_start(); // Start a transaction for atomicity

        // 1. Drop the Stored Procedure
        $this->db->query("
            IF OBJECT_ID('dbo.{$this->sp_name}', 'P') IS NOT NULL
            DROP PROCEDURE dbo.{$this->sp_name};
        ");

        // 2. Drop the Table
        $this->dbforge->drop_table($this->table, TRUE);

        $this->db->trans_complete(); // Complete the transaction
    }
}