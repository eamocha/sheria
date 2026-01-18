<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_Conveyancing_Tables extends CI_Migration {

	public function up()
	{
		$this->db->trans_start(); // Start a transaction for atomicity

		// --- 1. Create Tables with No Direct Foreign Key Dependencies ---
		// Using direct SQL for table creation to ensure proper SQL Server syntax

		// Drop tables if they exist (for clean migration)
		$this->dropAllTables();

		// conveyancing_document_status
		$this->db->query("
            CREATE TABLE [dbo].[conveyancing_document_status] (
                [id] INT IDENTITY(1,1) NOT NULL,
                [name] NVARCHAR(255) NOT NULL,
                [addedon] DATETIME NOT NULL DEFAULT GETDATE(),
                CONSTRAINT [pk_conveyancing_document_status] PRIMARY KEY ([id]),
                CONSTRAINT [UQ_document_status_name] UNIQUE ([name])
            )
        ");

		// conveyancing_document_type
		$this->db->query("
            CREATE TABLE [dbo].[conveyancing_document_type] (
                [id] INT IDENTITY(1,1) NOT NULL,
                [name] NVARCHAR(255) NOT NULL,
                [addedOn] DATETIME NOT NULL DEFAULT GETDATE(),
                CONSTRAINT [pk_conveyancing_document_type] PRIMARY KEY ([id]),
                CONSTRAINT [UQ_document_type_name] UNIQUE ([name])
            )
        ");

		// conveyancing_process_stages
		$this->db->query("
            CREATE TABLE [dbo].[conveyancing_process_stages] (
                [id] BIGINT IDENTITY(1,1) NOT NULL,
                [name] NVARCHAR(100) NOT NULL,
                [description] NVARCHAR(500) NULL,
                [sequence_order] INT NOT NULL,
                [is_active] BIT NOT NULL DEFAULT 1,
                [created_at] DATETIME NOT NULL DEFAULT GETDATE(),
                [updated_at] DATETIME NOT NULL DEFAULT GETDATE(),
                CONSTRAINT [pk_conveyancing_process_stages] PRIMARY KEY ([id])
            )
        ");

		// conveyancing_activity_type
		$this->db->query("
            CREATE TABLE [dbo].[conveyancing_activity_type] (
                [id] BIGINT IDENTITY(1,1) NOT NULL,
                [name] NVARCHAR(255) NOT NULL,
                [description] NVARCHAR(MAX) NULL,
                [createdOn] DATETIME NOT NULL DEFAULT GETDATE(),
                [createdBy] BIGINT NULL,
                [modifiedOn] DATETIME NULL,
                [modifiedBy] BIGINT NULL,
                CONSTRAINT [pk_conveyancing_activity_type] PRIMARY KEY ([id]),
                CONSTRAINT [UQ_activity_type_name] UNIQUE ([name])
            )
        ");

		// conveyancing_transaction_types
		$this->db->query("
            CREATE TABLE [dbo].[conveyancing_transaction_types] (
                [id] BIGINT IDENTITY(1,1) NOT NULL,
                [name] NVARCHAR(255) NOT NULL,
                [applies_to] NVARCHAR(15) NULL,
                [createdOn] DATETIME NOT NULL DEFAULT GETDATE(),
                [modifiedOn] DATETIME NULL,
                [createdBy] BIGINT NULL,
                [modifiedBy] BIGINT NULL,
                CONSTRAINT [pk_conveyancing_transaction_types] PRIMARY KEY ([id]),
                CONSTRAINT [UQ_transaction_name_applies] UNIQUE ([name], [applies_to])
            )
        ");

		// conveyancing_instrument_types
		$this->db->query("
            CREATE TABLE [dbo].[conveyancing_instrument_types] (
                [id] BIGINT IDENTITY(1,1) NOT NULL,
                [name] NVARCHAR(255) NOT NULL,
                [applies_to] NVARCHAR(15) NULL,
                [createdOn] DATETIME NOT NULL DEFAULT GETDATE(),
                [modifiedOn] DATETIME NULL,
                [createdBy] BIGINT NULL,
                [modifiedBy] BIGINT NULL,
                CONSTRAINT [pk_conveyancing_instrument_types] PRIMARY KEY ([id]),
                CONSTRAINT [UQ_instrument_name_applies] UNIQUE ([name], [applies_to])
            )
        ");

		// --- 2. Create Main Transactional Tables ---

		// conveyancing_instruments
		$this->db->query("
            CREATE TABLE [dbo].[conveyancing_instruments] (
                [id] BIGINT IDENTITY(1,1) NOT NULL,
                [title] NVARCHAR(255) NOT NULL,
                [instrument_type_id] BIGINT NOT NULL,
                [transaction_type_id] BIGINT NULL,
                [reference_number] NVARCHAR(50) NULL,
                [parties] NVARCHAR(500) NOT NULL,
                [initiated_by] BIGINT NULL,
                [assignee_id] BIGINT NULL,
                [staff_pf_no] NVARCHAR(30) NULL,
                [date_initiated] DATE NOT NULL,
                [due_date] DATE NULL,
                [description] NVARCHAR(MAX) NOT NULL,
                [external_counsel_id] BIGINT NULL,
                [property_value] DECIMAL(22,2) NULL,
                [amount_requested] DECIMAL(22,2) NULL,
                [amount_approved] DECIMAL(22,2) NULL,
                [createdOn] DATETIME NOT NULL DEFAULT GETDATE(),
                [createdBy] BIGINT NULL,
                [modifiedOn] DATETIME NULL,
                [modifiedBy] BIGINT NULL,
                [archived] BIT NOT NULL DEFAULT 0,
                [channel] NVARCHAR(10) NULL,
                [visible_to_CP] BIT NOT NULL DEFAULT 0,
                [date_received] DATE NULL,
                [status_id] BIGINT NULL,
                [assignee_team_id] BIGINT NULL,
                [current_stage_id] BIGINT NULL,
                [priority] TINYINT NULL DEFAULT 3,
                [completion_date] DATE NULL,
                CONSTRAINT [pk_conveyancing_instruments] PRIMARY KEY ([id]),
                CONSTRAINT [UQ_conveyancing_reference] UNIQUE ([reference_number])
            )
        ");

		// conveyancing_stage_progress
		$this->db->query("
            CREATE TABLE [dbo].[conveyancing_stage_progress] (
                [id] BIGINT IDENTITY(1,1) NOT NULL,
                [instrument_id] BIGINT NOT NULL,
                [stage_id] BIGINT NOT NULL,
                [status] NVARCHAR(20) NOT NULL,
                [start_date] DATETIME NULL DEFAULT GETDATE(),
                [completion_date] DATETIME NULL,
                [updated_by] BIGINT NOT NULL,
                [updated_on] DATETIME NOT NULL DEFAULT GETDATE(),
                [comments] NVARCHAR(MAX) NULL,
                CONSTRAINT [pk_conveyancing_stage_progress] PRIMARY KEY ([id])
            )
        ");

		// conveyancing_activity
		$this->db->query("
            CREATE TABLE [dbo].[conveyancing_activity] (
                [id] BIGINT IDENTITY(1,1) NOT NULL,
                [conveyancing_instrument_id] BIGINT NULL,
                [activity_type_id] BIGINT NULL,
                [action] NVARCHAR(50) NOT NULL,
                [activity_details] NVARCHAR(MAX) NULL,
                [activity_status] NVARCHAR(50) NULL,
                [modifiedOn] DATETIME NULL,
                [modifiedBy] BIGINT NULL,
                [createdOn] DATETIME NOT NULL DEFAULT GETDATE(),
                [createdBy] BIGINT NOT NULL,
                [createdByChannel] NVARCHAR(3) NULL,
                CONSTRAINT [pk_conveyancing_activity] PRIMARY KEY ([id])
            )
        ");

		// --- 3. Add Foreign Key Constraints ---
		// Using the safe helper methods from previous version

		// For conveyancing_activity_type
		$this->addForeignKeySafely(
			'conveyancing_activity_type',
			'FK_conveyancing_activity_type_createdBy',
			'createdBy',
			'users',
			'id'
		);

		$this->addForeignKeySafely(
			'conveyancing_activity_type',
			'FK_conveyancing_activity_type_modifiedBy',
			'modifiedBy',
			'users',
			'id'
		);

		// For conveyancing_transaction_types
		$this->addForeignKeySafely(
			'conveyancing_transaction_types',
			'FK_conveyancing_transaction_types_createdBy',
			'createdBy',
			'users',
			'id'
		);

		$this->addForeignKeySafely(
			'conveyancing_transaction_types',
			'FK_conveyancing_transaction_types_modifiedBy',
			'modifiedBy',
			'users',
			'id'
		);

		// For conveyancing_instrument_types
		$this->addForeignKeySafely(
			'conveyancing_instrument_types',
			'FK_conveyancing_instrument_types_createdBy',
			'createdBy',
			'users',
			'id'
		);

		$this->addForeignKeySafely(
			'conveyancing_instrument_types',
			'FK_conveyancing_instrument_types_modifiedBy',
			'modifiedBy',
			'users',
			'id'
		);

		// For conveyancing_instruments
		$this->addForeignKeySafely(
			'conveyancing_instruments',
			'FK_conveyancing_instruments_instrument_type',
			'instrument_type_id',
			'conveyancing_instrument_types',
			'id'
		);

		$this->addForeignKeySafely(
			'conveyancing_instruments',
			'FK_conveyancing_instruments_transaction_type',
			'transaction_type_id',
			'conveyancing_transaction_types',
			'id'
		);

		$this->addForeignKeySafely(
			'conveyancing_instruments',
			'FK_conveyancing_instruments_assignee',
			'assignee_id',
			'users',
			'id'
		);

		$this->addForeignKeySafely(
			'conveyancing_instruments',
			'FK_conveyancing_instruments_initiated_by',
			'initiated_by',
			'users',
			'id'
		);

		$this->addForeignKeySafely(
			'conveyancing_instruments',
			'FK_conveyancing_instruments_createdBy',
			'createdBy',
			'users',
			'id'
		);

		$this->addForeignKeySafely(
			'conveyancing_instruments',
			'FK_conveyancing_instruments_modifiedBy',
			'modifiedBy',
			'users',
			'id'
		);

		$this->addForeignKeySafely(
			'conveyancing_instruments',
			'FK_conveyancing_instruments_current_stage',
			'current_stage_id',
			'conveyancing_process_stages',
			'id'
		);

		// For conveyancing_stage_progress
		$this->addForeignKeySafely(
			'conveyancing_stage_progress',
			'FK_conveyancing_stage_progress_instrument',
			'instrument_id',
			'conveyancing_instruments',
			'id'
		);

		$this->addForeignKeySafely(
			'conveyancing_stage_progress',
			'FK_conveyancing_stage_progress_stage',
			'stage_id',
			'conveyancing_process_stages',
			'id'
		);

		$this->addForeignKeySafely(
			'conveyancing_stage_progress',
			'FK_conveyancing_stage_progress_updated_by',
			'updated_by',
			'users',
			'id'
		);

		// For conveyancing_activity
		$this->addForeignKeySafely(
			'conveyancing_activity',
			'FK_conveyancing_activity_instrument',
			'conveyancing_instrument_id',
			'conveyancing_instruments',
			'id'
		);

		$this->addForeignKeySafely(
			'conveyancing_activity',
			'FK_conveyancing_activity_type',
			'activity_type_id',
			'conveyancing_activity_type',
			'id'
		);

		$this->addForeignKeySafely(
			'conveyancing_activity',
			'FK_conveyancing_activity_createdBy',
			'createdBy',
			'users',
			'id'
		);

		$this->addForeignKeySafely(
			'conveyancing_activity',
			'FK_conveyancing_activity_modifiedBy',
			'modifiedBy',
			'users',
			'id'
		);

		// --- 4. Add CHECK Constraints ---
		$this->addCheckConstraintSafely('conveyancing_instruments', 'CHK_conveyancing_instruments_property_value', '[property_value] >= 0');
		$this->addCheckConstraintSafely('conveyancing_instruments', 'CHK_conveyancing_instruments_amount_requested', '[amount_requested] >= 0');
		$this->addCheckConstraintSafely('conveyancing_instruments', 'CHK_conveyancing_instruments_amount_approved', '[amount_approved] >= 0');
		$this->addCheckConstraintSafely('conveyancing_instruments', 'CHK_conveyancing_instruments_priority', '[priority] BETWEEN 1 AND 5');
		$this->addCheckConstraintSafely('conveyancing_instruments', 'CHK_conveyancing_instruments_dates',
			"[date_initiated] <= ISNULL([due_date], '9999-12-31') AND
             [date_initiated] <= ISNULL([completion_date], '9999-12-31') AND
             (ISNULL([due_date], '9999-12-31') >= ISNULL([completion_date], '1900-01-01'))"
		);
		$this->addCheckConstraintSafely('conveyancing_instruments', 'CHK_conveyancing_instruments_amounts',
			"([amount_approved] IS NULL OR [amount_requested] IS NULL OR [amount_approved] <= [amount_requested])"
		);

		// For conveyancing_stage_progress
		$this->addCheckConstraintSafely('conveyancing_stage_progress', 'CK_conveyancing_stage_progress_date_logic',
			'([start_date] IS NULL OR [completion_date] IS NULL OR [start_date] <= [completion_date])'
		);

		// --- 5. Create Indexes for Performance ---
		$this->addIndexSafely('conveyancing_instruments', 'IX_conveyancing_instruments_type', '[instrument_type_id]');
		$this->addIndexSafely('conveyancing_instruments', 'IX_conveyancing_instruments_assignee', '[assignee_id]');
		$this->addIndexSafely('conveyancing_instruments', 'IX_conveyancing_instruments_dates', '[date_initiated], [due_date]');
		$this->addIndexSafely('conveyancing_instruments', 'IX_conveyancing_instruments_status', '[status_id]');
		$this->addIndexSafely('conveyancing_stage_progress', 'IX_conveyancing_stage_progress_instrument', '[instrument_id]');
		$this->addIndexSafely('conveyancing_stage_progress', 'IX_conveyancing_stage_progress_stage', '[stage_id]');
		$this->addIndexSafely('conveyancing_activity', 'IX_conveyancing_activity_instrument', '[conveyancing_instrument_id]');
		$this->addIndexSafely('conveyancing_activity', 'IX_conveyancing_activity_type', '[activity_type_id]');

		// --- 6. Insert Initial Data (Optional) ---
		// Only if needed, insert default data
		$this->insertInitialData();

		$this->db->trans_complete(); // Complete the transaction
	}

	public function down()
	{
		$this->db->trans_start(); // Start a transaction for atomicity

		// Drop all tables (they will cascade)
		$this->dropAllTables();

		$this->db->trans_complete(); // Complete the transaction
	}

	/**
	 * Helper method to safely add foreign key constraints with existence check
	 */
	private function addForeignKeySafely($tableName, $constraintName, $column, $referenceTable, $referenceColumn)
	{
		$sql = "
            IF NOT EXISTS (
                SELECT 1 
                FROM sys.foreign_keys 
                WHERE name = '{$constraintName}'
            )
            BEGIN
                ALTER TABLE [dbo].[{$tableName}] 
                WITH CHECK ADD CONSTRAINT [{$constraintName}] 
                FOREIGN KEY([{$column}]) 
                REFERENCES [dbo].[{$referenceTable}] ([{$referenceColumn}])
            END
        ";
		$this->db->query($sql);
	}

	/**
	 * Helper method to safely add check constraints with existence check
	 */
	private function addCheckConstraintSafely($tableName, $constraintName, $checkCondition)
	{
		$sql = "
            IF NOT EXISTS (
                SELECT 1 
                FROM sys.check_constraints 
                WHERE name = '{$constraintName}'
            )
            BEGIN
                ALTER TABLE [dbo].[{$tableName}] 
                ADD CONSTRAINT [{$constraintName}] CHECK ({$checkCondition})
            END
        ";
		$this->db->query($sql);
	}

	/**
	 * Helper method to safely add indexes with existence check
	 */
	private function addIndexSafely($tableName, $indexName, $columns)
	{
		$sql = "
            IF NOT EXISTS (
                SELECT 1 
                FROM sys.indexes 
                WHERE name = '{$indexName}' 
                AND object_id = OBJECT_ID('dbo.{$tableName}')
            )
            BEGIN
                CREATE INDEX [{$indexName}] ON [dbo].[{$tableName}] ({$columns})
            END
        ";
		$this->db->query($sql);
	}

	/**
	 * Helper method to drop all tables safely
	 */
	private function dropAllTables()
	{
		// Drop tables in reverse dependency order
		$tables = [
			'conveyancing_activity',
			'conveyancing_stage_progress',
			'conveyancing_instruments',
			'conveyancing_instrument_types',
			'conveyancing_transaction_types',
			'conveyancing_activity_type',
			'conveyancing_process_stages',
			'conveyancing_document_type',
			'conveyancing_document_status'
		];

		foreach ($tables as $table) {
			$this->db->query("
                IF OBJECT_ID('dbo.{$table}', 'U') IS NOT NULL
                BEGIN
                    DROP TABLE [dbo].[{$table}]
                END
            ");
		}
	}

	/**
	 * Insert initial data into tables
	 */
	private function insertInitialData()
	{
		// Only insert if tables are empty
		$checkQuery = $this->db->query("SELECT COUNT(*) as count FROM [dbo].[conveyancing_document_type]");
		$count = $checkQuery->row()->count;

		if ($count == 0) {
			// Insert document types
			$documentTypes = [
				['name' => 'Sale Agreement'],
				['name' => 'Lease Agreement'],
				['name' => 'Transfer Deed'],
				['name' => 'Charge Document'],
				['name' => 'Discharge of Charge'],
				['name' => 'Consent to Transfer'],
				['name' => 'Letter of Offer'],
				['name' => 'Title Deed'],
				['name' => 'Search Certificate'],
				['name' => 'Valuation Report']
			];

			foreach ($documentTypes as $type) {
				$this->db->query("
                    INSERT INTO [dbo].[conveyancing_document_type] ([name]) 
                    VALUES (?)",
					[$type['name']]
				);
			}

			// Insert document statuses
			$statuses = [
				['name' => 'Draft'],
				['name' => 'Under Review'],
				['name' => 'Approved'],
				['name' => 'Rejected'],
				['name' => 'Executed'],
				['name' => 'Filed'],
				['name' => 'Archived']
			];

			foreach ($statuses as $status) {
				$this->db->query("
                    INSERT INTO [dbo].[conveyancing_document_status] ([name]) 
                    VALUES (?)",
					[$status['name']]
				);
			}

			// Insert process stages
			$stages = [
				['name' => 'Initiation', 'description' => 'Case initiation and assignment', 'sequence_order' => 1],
				['name' => 'Document Collection', 'description' => 'Gathering required documents', 'sequence_order' => 2],
				['name' => 'Review', 'description' => 'Legal review of documents', 'sequence_order' => 3],
				['name' => 'Drafting', 'description' => 'Drafting legal instruments', 'sequence_order' => 4],
				['name' => 'Approval', 'description' => 'Internal approval process', 'sequence_order' => 5],
				['name' => 'Execution', 'description' => 'Signing and execution', 'sequence_order' => 6],
				['name' => 'Registration', 'description' => 'Registration with authorities', 'sequence_order' => 7],
				['name' => 'Completion', 'description' => 'Case completion and filing', 'sequence_order' => 8]
			];

			foreach ($stages as $stage) {
				$this->db->query("
                    INSERT INTO [dbo].[conveyancing_process_stages] ([name], [description], [sequence_order]) 
                    VALUES (?, ?, ?)",
					[$stage['name'], $stage['description'], $stage['sequence_order']]
				);
			}
		}
	}
}