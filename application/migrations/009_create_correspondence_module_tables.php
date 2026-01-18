<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_Correspondence_Module_Tables extends CI_Migration {

	public function up()
	{
		$this->db->trans_start(); // Start a transaction for atomicity

		// Drop all tables if they exist (for clean migration)
		$this->dropAllTables();

		// --- 1. Independent tables (no foreign key dependencies on other new tables) ---

		// correspondence_document_types
		$this->db->query("
            CREATE TABLE [dbo].[correspondence_document_types] (
                [id] BIGINT IDENTITY(1,1) NOT NULL,
                [name] NVARCHAR(255) NOT NULL,
                [description] NVARCHAR(MAX) NULL,
                [createdOn] DATETIME NOT NULL DEFAULT GETDATE(),
                [modifiedOn] DATETIME NULL,
                [createdBy] BIGINT NULL,
                [modifiedBy] BIGINT NULL,
                CONSTRAINT [pk_correspondence_document_types] PRIMARY KEY ([id])
            )
        ");

		// correspondence_statuses
		$this->db->query("
            CREATE TABLE [dbo].[correspondence_statuses] (
                [id] BIGINT IDENTITY(1,1) NOT NULL,
                [name] NVARCHAR(255) NOT NULL,
                [createdOn] DATETIME NOT NULL DEFAULT GETDATE(),
                [modifiedOn] DATETIME NULL,
                [createdBy] BIGINT NULL,
                [modifiedBy] BIGINT NULL,
                CONSTRAINT [pk_correspondence_statuses] PRIMARY KEY ([id])
            )
        ");

		// correspondence_types
		$this->db->query("
            CREATE TABLE [dbo].[correspondence_types] (
                [id] BIGINT IDENTITY(1,1) NOT NULL,
                [name] NVARCHAR(255) NOT NULL,
                [createdOn] DATETIME NOT NULL DEFAULT GETDATE(),
                [modifiedOn] DATETIME NULL,
                [createdBy] BIGINT NULL,
                [modifiedBy] BIGINT NULL,
                CONSTRAINT [pk_correspondence_types] PRIMARY KEY ([id])
            )
        ");

		// --- 2. Reference tables (depend only on independent tables or users) ---

		// correspondence_workflow_steps (depends on correspondence_types, users)
		$this->db->query("
            CREATE TABLE [dbo].[correspondence_workflow_steps] (
                [id] BIGINT IDENTITY(1,1) NOT NULL,
                [name] NVARCHAR(255) NOT NULL,
                [correspondence_type_id] BIGINT NOT NULL,
                [sequence_order] INT NOT NULL,
                [comment] NVARCHAR(MAX) NULL,
                [createdOn] DATETIME NOT NULL DEFAULT GETDATE(),
                [modifiedOn] DATETIME NULL,
                [createdBy] BIGINT NULL,
                [modifiedBy] BIGINT NULL,
                [category] NVARCHAR(50) NULL,
                CONSTRAINT [pk_correspondence_workflow_steps] PRIMARY KEY ([id])
            )
        ");

		// --- 3. Main transactional tables ---

		// correspondences (depends on correspondence_types, correspondence_statuses, users, correspondence_document_types)
		$this->db->query("
            CREATE TABLE [dbo].[correspondences] (
                [id] BIGINT IDENTITY(1,1) NOT NULL,
                [correspondence_type_id] BIGINT NOT NULL,
                [sender] BIGINT NULL,
                [recipient] BIGINT NULL,
                [subject] NVARCHAR(255) NOT NULL,
                [body] NVARCHAR(MAX) NULL,
                [date_received] DATETIME NULL,
                [document_date] DATETIME NULL,
                [reference_number] NVARCHAR(255) NULL,
                [status_id] BIGINT NOT NULL,
                [assigned_to] BIGINT NULL,
                [filename] NVARCHAR(255) NULL,
                [comments] NVARCHAR(MAX) NULL,
                [createdOn] DATETIME NOT NULL DEFAULT GETDATE(),
                [modifiedOn] DATETIME NULL,
                [createdBy] BIGINT NULL,
                [modifiedBy] BIGINT NULL,
                [document_id] BIGINT NULL,
                [document_type_id] BIGINT NULL,
                [action_required] NVARCHAR(50) NULL,
                [priority] NVARCHAR(20) NULL,
                [requires_signature] NVARCHAR(3) NOT NULL DEFAULT 'No',
                [mode_of_dispatch] NVARCHAR(50) NULL,
                [assignee_team_id] BIGINT NULL,
                [category] NVARCHAR(50) NULL,
                [related_to_object] NVARCHAR(50) NULL,
                [related_to_object_id] BIGINT NULL,
                [sender_contact_type] NVARCHAR(10) NULL,
                [recipient_contact_type] NVARCHAR(10) NULL,
                [date_dispatched] DATETIME NULL,
                [due_date] DATETIME NULL,
                [mode_of_receipt] NVARCHAR(50) NULL,
                CONSTRAINT [pk_correspondences] PRIMARY KEY ([id]),
                CONSTRAINT [UQ_correspondences_reference_number] UNIQUE ([reference_number])
            )
        ");

		// correspondence_document (depends on correspondences, correspondence_document_types, correspondence_statuses, users)
		$this->db->query("
            CREATE TABLE [dbo].[correspondence_document] (
                [id] BIGINT IDENTITY(1,1) NOT NULL,
                [name] NVARCHAR(255) NOT NULL,
                [size] BIGINT NULL,
                [extension] NVARCHAR(20) NULL,
                [correspondence_id] BIGINT NOT NULL,
                [document_type_id] BIGINT NULL,
                [document_status_id] BIGINT NULL,
                [comments] NVARCHAR(MAX) NULL,
                [createdOn] DATETIME NOT NULL DEFAULT GETDATE(),
                [modifiedOn] DATETIME NULL,
                [createdBy] BIGINT NULL,
                [modifiedBy] BIGINT NULL,
                CONSTRAINT [pk_correspondence_document] PRIMARY KEY ([id])
            )
        ");

		// correspondence_workflow (depends on correspondences, correspondence_workflow_steps, users)
		$this->db->query("
            CREATE TABLE [dbo].[correspondence_workflow] (
                [id] BIGINT IDENTITY(1,1) NOT NULL,
                [correspondence_id] BIGINT NOT NULL,
                [workflow_step_id] BIGINT NOT NULL,
                [status] NVARCHAR(255) NOT NULL,
                [createdOn] DATETIME NOT NULL DEFAULT GETDATE(),
                [modifiedOn] DATETIME NULL,
                [createdBy] BIGINT NULL,
                [modifiedBy] BIGINT NULL,
                [comments] NVARCHAR(MAX) NULL,
                [completion_date] DATETIME NULL,
                CONSTRAINT [pk_correspondence_workflow] PRIMARY KEY ([id])
            )
        ");

		// correspondence_activity_log (depends on correspondences, users)
		$this->db->query("
            CREATE TABLE [dbo].[correspondence_activity_log] (
                [id] BIGINT IDENTITY(1,1) NOT NULL,
                [correspondence_id] BIGINT NOT NULL,
                [user_id] BIGINT NOT NULL,
                [action] NVARCHAR(255) NOT NULL,
                [details] NVARCHAR(MAX) NULL,
                [createdOn] DATETIME NOT NULL DEFAULT GETDATE(),
                [modifiedOn] DATETIME NULL,
                [createdBy] BIGINT NULL,
                [modifiedBy] BIGINT NULL,
                CONSTRAINT [pk_correspondence_activity_log] PRIMARY KEY ([id])
            )
        ");

		// correspondence_relationships (depends on correspondences, users)
		$this->db->query("
            CREATE TABLE [dbo].[correspondence_relationships] (
                [id] BIGINT IDENTITY(1,1) NOT NULL,
                [correspondence_id1] BIGINT NOT NULL,
                [correspondence_id2] BIGINT NOT NULL,
                [comments] NVARCHAR(255) NULL,
                [createdBy] BIGINT NULL,
                [createdOn] DATETIME NOT NULL DEFAULT GETDATE(),
                CONSTRAINT [pk_correspondence_relationships] PRIMARY KEY ([id])
            )
        ");

		// --- 4. Add All Foreign Key Constraints (using safe methods) ---

		// For correspondence_workflow_steps
		$this->addForeignKeySafely(
			'correspondence_workflow_steps',
			'FK_correspondence_workflow_steps_type',
			'correspondence_type_id',
			'correspondence_types',
			'id'
		);

		$this->addForeignKeySafely(
			'correspondence_workflow_steps',
			'FK_correspondence_workflow_steps_createdBy',
			'createdBy',
			'users',
			'id'
		);

		$this->addForeignKeySafely(
			'correspondence_workflow_steps',
			'FK_correspondence_workflow_steps_modifiedBy',
			'modifiedBy',
			'users',
			'id'
		);

		// For correspondences
		$this->addForeignKeySafely(
			'correspondences',
			'FK_correspondences_status',
			'status_id',
			'correspondence_statuses',
			'id'
		);

		$this->addForeignKeySafely(
			'correspondences',
			'FK_correspondences_type',
			'correspondence_type_id',
			'correspondence_types',
			'id'
		);

		$this->addForeignKeySafely(
			'correspondences',
			'FK_correspondences_createdBy',
			'createdBy',
			'users',
			'id'
		);

		$this->addForeignKeySafely(
			'correspondences',
			'FK_correspondences_modifiedBy',
			'modifiedBy',
			'users',
			'id'
		);

		$this->addForeignKeySafely(
			'correspondences',
			'FK_correspondences_document_type',
			'document_type_id',
			'correspondence_document_types',
			'id'
		);

		// Additional foreign keys for sender, recipient, assigned_to if they reference users table
		$this->addForeignKeySafely(
			'correspondences',
			'FK_correspondences_sender',
			'sender',
			'users',
			'id'
		);

		$this->addForeignKeySafely(
			'correspondences',
			'FK_correspondences_recipient',
			'recipient',
			'users',
			'id'
		);

		$this->addForeignKeySafely(
			'correspondences',
			'FK_correspondences_assigned_to',
			'assigned_to',
			'users',
			'id'
		);

		// For correspondence_document
		$this->addForeignKeySafely(
			'correspondence_document',
			'FK_correspondence_document_correspondence',
			'correspondence_id',
			'correspondences',
			'id'
		);

		$this->addForeignKeySafely(
			'correspondence_document',
			'FK_correspondence_document_type',
			'document_type_id',
			'correspondence_document_types',
			'id'
		);

		$this->addForeignKeySafely(
			'correspondence_document',
			'FK_correspondence_document_status',
			'document_status_id',
			'correspondence_statuses',
			'id'
		);

		$this->addForeignKeySafely(
			'correspondence_document',
			'FK_correspondence_document_createdBy',
			'createdBy',
			'users',
			'id'
		);

		$this->addForeignKeySafely(
			'correspondence_document',
			'FK_correspondence_document_modifiedBy',
			'modifiedBy',
			'users',
			'id'
		);

		// For correspondence_workflow
		$this->addForeignKeySafely(
			'correspondence_workflow',
			'FK_correspondence_workflow_correspondence',
			'correspondence_id',
			'correspondences',
			'id'
		);

		$this->addForeignKeySafely(
			'correspondence_workflow',
			'FK_correspondence_workflow_step',
			'workflow_step_id',
			'correspondence_workflow_steps',
			'id'
		);

		$this->addForeignKeySafely(
			'correspondence_workflow',
			'FK_correspondence_workflow_createdBy',
			'createdBy',
			'users',
			'id'
		);

		$this->addForeignKeySafely(
			'correspondence_workflow',
			'FK_correspondence_workflow_modifiedBy',
			'modifiedBy',
			'users',
			'id'
		);

		// For correspondence_activity_log
		$this->addForeignKeySafely(
			'correspondence_activity_log',
			'FK_correspondence_activity_log_correspondence',
			'correspondence_id',
			'correspondences',
			'id'
		);

		$this->addForeignKeySafely(
			'correspondence_activity_log',
			'FK_correspondence_activity_log_user',
			'user_id',
			'users',
			'id'
		);

		$this->addForeignKeySafely(
			'correspondence_activity_log',
			'FK_correspondence_activity_log_createdBy',
			'createdBy',
			'users',
			'id'
		);

		$this->addForeignKeySafely(
			'correspondence_activity_log',
			'FK_correspondence_activity_log_modifiedBy',
			'modifiedBy',
			'users',
			'id'
		);

		// For correspondence_relationships
		$this->addForeignKeySafely(
			'correspondence_relationships',
			'FK_correspondence_relationships_correspondence1',
			'correspondence_id1',
			'correspondences',
			'id'
		);

		$this->addForeignKeySafely(
			'correspondence_relationships',
			'FK_correspondence_relationships_correspondence2',
			'correspondence_id2',
			'correspondences',
			'id'
		);

		$this->addForeignKeySafely(
			'correspondence_relationships',
			'FK_correspondence_relationships_createdBy',
			'createdBy',
			'users',
			'id'
		);

		// --- 5. Add Check Constraints ---
		$this->addCheckConstraintSafely('correspondences', 'CHK_correspondence_dates',
			"[date_received] IS NULL OR [document_date] IS NULL OR [date_received] >= [document_date]"
		);

		$this->addCheckConstraintSafely('correspondences', 'CHK_correspondence_priority',
			"[priority] IS NULL OR [priority] IN ('Low', 'Medium', 'High', 'Critical')"
		);

		$this->addCheckConstraintSafely('correspondences', 'CHK_correspondence_requires_signature',
			"[requires_signature] IN ('Yes', 'No')"
		);

		$this->addCheckConstraintSafely('correspondence_workflow', 'CHK_correspondence_workflow_dates',
			"[createdOn] <= ISNULL([completion_date], '9999-12-31')"
		);

		// --- 6. Create Indexes for Performance ---
		$this->addIndexSafely('correspondences', 'IX_correspondences_type', '[correspondence_type_id]');
		$this->addIndexSafely('correspondences', 'IX_correspondences_status', '[status_id]');
		$this->addIndexSafely('correspondences', 'IX_correspondences_createdOn', '[createdOn]');
		$this->addIndexSafely('correspondences', 'IX_correspondences_reference', '[reference_number]');

		$this->addIndexSafely('correspondence_document', 'IX_correspondence_document_correspondence', '[correspondence_id]');
		$this->addIndexSafely('correspondence_document', 'IX_correspondence_document_type', '[document_type_id]');

		$this->addIndexSafely('correspondence_workflow', 'IX_correspondence_workflow_correspondence', '[correspondence_id]');
		$this->addIndexSafely('correspondence_workflow', 'IX_correspondence_workflow_step', '[workflow_step_id]');

		$this->addIndexSafely('correspondence_activity_log', 'IX_correspondence_activity_log_correspondence', '[correspondence_id]');
		$this->addIndexSafely('correspondence_activity_log', 'IX_correspondence_activity_log_user', '[user_id]');

		$this->addIndexSafely('correspondence_relationships', 'IX_correspondence_relationships_correspondence1', '[correspondence_id1]');
		$this->addIndexSafely('correspondence_relationships', 'IX_correspondence_relationships_correspondence2', '[correspondence_id2]');

		// --- 7. Insert Initial Data ---
		$this->insertInitialData();

		$this->db->trans_complete(); // Complete the transaction
	}

	public function down()
	{
		$this->db->trans_start(); // Start a transaction for atomicity

		// Drop all tables safely
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
			'correspondence_relationships',
			'correspondence_activity_log',
			'correspondence_workflow',
			'correspondence_document',
			'correspondences',
			'correspondence_workflow_steps',
			'correspondence_types',
			'correspondence_statuses',
			'correspondence_document_types'
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
		// Check if tables are empty before inserting
		$checkQuery = $this->db->query("SELECT COUNT(*) as count FROM [dbo].[correspondence_types]");
		$count = $checkQuery->row()->count;

		if ($count == 0) {
			// Insert correspondence types
			$correspondenceTypes = [
				['name' => 'Letter'],
				['name' => 'Email'],
				['name' => 'Memo'],
				['name' => 'Report'],
				['name' => 'Contract'],
				['name' => 'Agreement'],
				['name' => 'Notice'],
				['name' => 'Invoice'],
				['name' => 'Request'],
				['name' => 'Response']
			];

			foreach ($correspondenceTypes as $type) {
				$this->db->query("
                    INSERT INTO [dbo].[correspondence_types] ([name]) 
                    VALUES (?)",
					[$type['name']]
				);
			}

			// Insert correspondence statuses
			$statuses = [
				['name' => 'Draft'],
				['name' => 'Pending Review'],
				['name' => 'Under Review'],
				['name' => 'Approved'],
				['name' => 'Rejected'],
				['name' => 'Sent'],
				['name' => 'Received'],
				['name' => 'Archived'],
				['name' => 'Completed']
			];

			foreach ($statuses as $status) {
				$this->db->query("
                    INSERT INTO [dbo].[correspondence_statuses] ([name]) 
                    VALUES (?)",
					[$status['name']]
				);
			}

			// Insert document types
			$documentTypes = [
				['name' => 'PDF Document', 'description' => 'Portable Document Format'],
				['name' => 'Word Document', 'description' => 'Microsoft Word Document'],
				['name' => 'Excel Spreadsheet', 'description' => 'Microsoft Excel Spreadsheet'],
				['name' => 'Image File', 'description' => 'Image files (JPG, PNG, etc.)'],
				['name' => 'Scanned Document', 'description' => 'Scanned PDF or image'],
				['name' => 'Presentation', 'description' => 'PowerPoint or other presentation'],
				['name' => 'Text File', 'description' => 'Plain text document'],
				['name' => 'Signed Copy', 'description' => 'Signed document copy']
			];

			foreach ($documentTypes as $type) {
				$this->db->query("
                    INSERT INTO [dbo].[correspondence_document_types] ([name], [description]) 
                    VALUES (?, ?)",
					[$type['name'], $type['description']]
				);
			}

			// Insert workflow steps for Letter type (get the ID of Letter type)
			$letterTypeQuery = $this->db->query("SELECT [id] FROM [dbo].[correspondence_types] WHERE [name] = 'Letter'");
			if ($letterTypeQuery->num_rows() > 0) {
				$letterTypeId = $letterTypeQuery->row()->id;

				$workflowSteps = [
					['name' => 'Draft Creation', 'sequence_order' => 1, 'comment' => 'Initial draft creation'],
					['name' => 'Review', 'sequence_order' => 2, 'comment' => 'Internal review process'],
					['name' => 'Approval', 'sequence_order' => 3, 'comment' => 'Manager approval'],
					['name' => 'Finalization', 'sequence_order' => 4, 'comment' => 'Final edits and formatting'],
					['name' => 'Dispatch', 'sequence_order' => 5, 'comment' => 'Sending to recipient']
				];

				foreach ($workflowSteps as $step) {
					$this->db->query("
                        INSERT INTO [dbo].[correspondence_workflow_steps] 
                        ([name], [correspondence_type_id], [sequence_order], [comment]) 
                        VALUES (?, ?, ?, ?)",
						[$step['name'], $letterTypeId, $step['sequence_order'], $step['comment']]
					);
				}
			}
		}
	}
}