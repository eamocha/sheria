<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_Opinion_Lang_Workflow_Tables extends CI_Migration {

	public function up()
	{
		$this->db->trans_start(); // Start a transaction for atomicity

		// Drop tables if they exist (for clean migration)
		$this->dropAllTables();

		// --- 1. Create Language-Dependent Tables ---

		// opinion_document_status_language
		$this->db->query("
            CREATE TABLE [dbo].[opinion_document_status_language] (
                [id] BIGINT IDENTITY(1,1) NOT NULL,
                [status_id] BIGINT NOT NULL,
                [language_id] BIGINT NOT NULL,
                [name] NVARCHAR(255) NOT NULL,
                CONSTRAINT [pk_opinion_document_status_language] PRIMARY KEY ([id])
            )
        ");

		// opinion_document_type_language
		$this->db->query("
            CREATE TABLE [dbo].[opinion_document_type_language] (
                [id] BIGINT IDENTITY(1,1) NOT NULL,
                [type_id] BIGINT NOT NULL,
                [language_id] BIGINT NOT NULL,
                [name] NVARCHAR(255) NOT NULL,
                [applies_to] NVARCHAR(15) NULL DEFAULT 'opinions',
                CONSTRAINT [pk_opinion_document_type_language] PRIMARY KEY ([id])
            )
        ");

		// opinion_types_languages
		$this->db->query("
            CREATE TABLE [dbo].[opinion_types_languages] (
                [id] BIGINT IDENTITY(1,1) NOT NULL,
                [opinion_type_id] BIGINT NOT NULL,
                [language_id] BIGINT NOT NULL,
                [name] NVARCHAR(255) NOT NULL,
                [applies_to] NVARCHAR(15) NULL DEFAULT 'Opinions',
                CONSTRAINT [pk_opinion_types_languages] PRIMARY KEY ([id])
            )
        ");

		// --- 2. Create Workflow Definition Tables ---

		// opinion_workflows
		$this->db->query("
            CREATE TABLE [dbo].[opinion_workflows] (
                [id] BIGINT IDENTITY(1,1) NOT NULL,
                [name] NVARCHAR(255) NOT NULL,
                [type] NVARCHAR(255) NOT NULL,
                [createdBy] BIGINT NULL,
                [createdOn] DATETIME NULL,
                [modifiedBy] BIGINT NULL,
                [modifiedOn] DATETIME NULL,
                CONSTRAINT [pk_opinion_workflows] PRIMARY KEY ([id])
            )
        ");

		// opinion_workflow_status_relation
		$this->db->query("
            CREATE TABLE [dbo].[opinion_workflow_status_relation] (
                [id] BIGINT IDENTITY(1,1) NOT NULL,
                [workflow_id] BIGINT NOT NULL,
                [status_id] BIGINT NOT NULL,
                [start_point] TINYINT NOT NULL DEFAULT 0,
                CONSTRAINT [pk_opinion_workflow_status_relation] PRIMARY KEY ([id])
            )
        ");

		// opinion_workflow_status_transition
		$this->db->query("
            CREATE TABLE [dbo].[opinion_workflow_status_transition] (
                [id] BIGINT IDENTITY(1,1) NOT NULL,
                [workflow_id] BIGINT NOT NULL,
                [from_step] BIGINT NOT NULL,
                [to_step] BIGINT NOT NULL,
                [name] NVARCHAR(255) NOT NULL,
                [comments] NVARCHAR(MAX) NULL,
                CONSTRAINT [pk_opinion_workflow_status_transition] PRIMARY KEY ([id])
            )
        ");

		// --- 3. Workflow Extension Tables ---

		// opinion_workflow_status_transition_permissions
		$this->db->query("
            CREATE TABLE [dbo].[opinion_workflow_status_transition_permissions] (
                [id] BIGINT IDENTITY(1,1) NOT NULL,
                [transition] BIGINT NOT NULL,
                [users] NVARCHAR(MAX) NULL,
                [user_groups] NVARCHAR(MAX) NULL,
                CONSTRAINT [pk_opinion_workflow_status_transition_permissions] PRIMARY KEY ([id])
            )
        ");

		// opinion_workflow_status_transition_screen_fields
		$this->db->query("
            CREATE TABLE [dbo].[opinion_workflow_status_transition_screen_fields] (
                [id] BIGINT IDENTITY(1,1) NOT NULL,
                [transition] BIGINT NOT NULL,
                [data] NVARCHAR(MAX) NULL,
                CONSTRAINT [pk_opinion_workflow_status_transition_screen_fields] PRIMARY KEY ([id])
            )
        ");

		// opinion_workflow_types
		$this->db->query("
            CREATE TABLE [dbo].[opinion_workflow_types] (
                [id] BIGINT IDENTITY(1,1) NOT NULL,
                [workflow_id] BIGINT NOT NULL,
                [type_id] BIGINT NOT NULL,
                CONSTRAINT [pk_opinion_workflow_types] PRIMARY KEY ([id])
            )
        ");

		// --- 4. Opinion Relationship Tables ---

		// opinion_comments
		$this->db->query("
            CREATE TABLE [dbo].[opinion_comments] (
                [id] BIGINT IDENTITY(1,1) NOT NULL,
                [opinion_id] BIGINT NOT NULL,
                [comment] NVARCHAR(MAX) NOT NULL,
                [edited] TINYINT NOT NULL DEFAULT 0,
                [createdOn] DATETIME NOT NULL DEFAULT GETDATE(),
                [createdBy] BIGINT NOT NULL,
                [modifiedOn] DATETIME NOT NULL DEFAULT GETDATE(),
                [modifiedBy] BIGINT NOT NULL,
                [added_from_channel] NVARCHAR(5) NULL,
                CONSTRAINT [pk_opinion_comments] PRIMARY KEY ([id])
            )
        ");

		// opinion_contributors
		$this->db->query("
            CREATE TABLE [dbo].[opinion_contributors] (
                [id] BIGINT IDENTITY(1,1) NOT NULL,
                [opinion_id] BIGINT NOT NULL,
                [user_id] BIGINT NOT NULL,
                CONSTRAINT [pk_opinion_contributors] PRIMARY KEY ([id])
            )
        ");

		// --- 5. Remaining Relationship Tables ---

		// opinion_url
		$this->db->query("
            CREATE TABLE [dbo].[opinion_url] (
                [id] BIGINT IDENTITY(1,1) NOT NULL,
                [opinion_id] BIGINT NOT NULL,
                [document_type_id] BIGINT NOT NULL,
                [document_status_id] BIGINT NOT NULL,
                [name] NVARCHAR(255) NULL,
                [path] NVARCHAR(255) NULL,
                [path_type] NVARCHAR(255) NULL,
                [comments] NVARCHAR(MAX) NULL,
                [createdOn] DATETIME NULL,
                [createdBy] BIGINT NULL,
                [modifiedOn] DATETIME NULL,
                [modifiedBy] BIGINT NULL,
                CONSTRAINT [pk_opinion_url] PRIMARY KEY ([id])
            )
        ");

		// opinion_users
		$this->db->query("
            CREATE TABLE [dbo].[opinion_users] (
                [opinion_id] BIGINT NOT NULL,
                [user_id] BIGINT NOT NULL,
                CONSTRAINT [pk_opinion_users] PRIMARY KEY ([opinion_id], [user_id])
            )
        ");

		// opinions_documents
		$this->db->query("
            CREATE TABLE [dbo].[opinions_documents] (
                [id] BIGINT IDENTITY(1,1) NOT NULL,
                [opinion_id] BIGINT NOT NULL,
                [document_id] BIGINT NOT NULL,
                CONSTRAINT [pk_opinions_documents] PRIMARY KEY ([id])
            )
        ");

		// opinion_workflow_status_transition_history
		$this->db->query("
            CREATE TABLE [dbo].[opinion_workflow_status_transition_history] (
                [id] BIGINT IDENTITY(1,1) NOT NULL,
                [opinion_id] BIGINT NOT NULL,
                [from_step] BIGINT NULL,
                [to_step] BIGINT NOT NULL,
                [user_id] BIGINT NULL,
                [changed_on] DATETIME NOT NULL DEFAULT GETDATE(),
                CONSTRAINT [pk_opinion_workflow_status_transition_history] PRIMARY KEY ([id])
            )
        ");

		// --- 6. Add All Foreign Key Constraints (using safe methods) ---

		// Language-dependent tables
		$this->addForeignKeySafely(
			'opinion_document_status_language',
			'fk_opinion_document_status_language_status',
			'status_id',
			'opinion_document_status',
			'id',
			'CASCADE',
			'CASCADE'
		);

		$this->addForeignKeySafely(
			'opinion_document_status_language',
			'fk_opinion_document_status_language_language',
			'language_id',
			'languages',
			'id',
			'CASCADE',
			'CASCADE'
		);

		$this->addForeignKeySafely(
			'opinion_document_type_language',
			'fk_opinion_document_type_language_type',
			'type_id',
			'opinion_document_type',
			'id',
			'CASCADE',
			'CASCADE'
		);

		$this->addForeignKeySafely(
			'opinion_document_type_language',
			'fk_opinion_document_type_language_language',
			'language_id',
			'languages',
			'id',
			'CASCADE',
			'CASCADE'
		);

		$this->addForeignKeySafely(
			'opinion_types_languages',
			'fk_opinion_types_languages_language',
			'language_id',
			'languages',
			'id'
		);

		$this->addForeignKeySafely(
			'opinion_types_languages',
			'fk_opinion_types_languages_type',
			'opinion_type_id',
			'opinion_types',
			'id'
		);

		// Workflow tables
		$this->addForeignKeySafely(
			'opinion_workflows',
			'fk_opinion_workflows_createdBy',
			'createdBy',
			'users',
			'id'
		);

		$this->addForeignKeySafely(
			'opinion_workflows',
			'fk_opinion_workflows_modifiedBy',
			'modifiedBy',
			'users',
			'id'
		);

		$this->addForeignKeySafely(
			'opinion_workflow_status_relation',
			'fk_opinion_workflow_status_relation_workflow',
			'workflow_id',
			'opinion_workflows',
			'id'
		);

		$this->addForeignKeySafely(
			'opinion_workflow_status_relation',
			'fk_opinion_workflow_status_relation_status',
			'status_id',
			'opinion_statuses',
			'id'
		);

		$this->addForeignKeySafely(
			'opinion_workflow_status_transition',
			'fk_opinion_workflow_status_transition_workflow',
			'workflow_id',
			'opinion_workflows',
			'id'
		);

		$this->addForeignKeySafely(
			'opinion_workflow_status_transition',
			'fk_opinion_workflow_status_transition_from',
			'from_step',
			'opinion_statuses',
			'id'
		);

		$this->addForeignKeySafely(
			'opinion_workflow_status_transition',
			'fk_opinion_workflow_status_transition_to',
			'to_step',
			'opinion_statuses',
			'id'
		);

		$this->addForeignKeySafely(
			'opinion_workflow_status_transition_permissions',
			'fk_opinion_workflow_status_transition_permissions_transition',
			'transition',
			'opinion_workflow_status_transition',
			'id'
		);

		$this->addForeignKeySafely(
			'opinion_workflow_status_transition_screen_fields',
			'fk_opinion_workflow_status_transition_screen_fields_transition',
			'transition',
			'opinion_workflow_status_transition',
			'id'
		);

		$this->addForeignKeySafely(
			'opinion_workflow_types',
			'fk_opinion_workflow_types_workflow',
			'workflow_id',
			'opinion_workflows',
			'id'
		);

		$this->addForeignKeySafely(
			'opinion_workflow_types',
			'fk_opinion_workflow_types_type',
			'type_id',
			'opinion_types',
			'id'
		);

		// Main opinion table (if it exists)
		$this->addForeignKeySafelyIfTableExists('opinions', 'fk_opinions_legal_case', 'legal_case_id', 'legal_cases', 'id');
		$this->addForeignKeySafelyIfTableExists('opinions', 'fk_opinions_status', 'opinion_status_id', 'opinion_statuses', 'id');
		$this->addForeignKeySafelyIfTableExists('opinions', 'fk_opinions_type', 'opinion_type_id', 'opinion_types', 'id');
		$this->addForeignKeySafelyIfTableExists('opinions', 'fk_opinions_user', 'user_id', 'users', 'id');
		$this->addForeignKeySafelyIfTableExists('opinions', 'fk_opinions_assigned_to', 'assigned_to', 'users', 'id');
		$this->addForeignKeySafelyIfTableExists('opinions', 'fk_opinions_reporter', 'reporter', 'users', 'id');
		$this->addForeignKeySafelyIfTableExists('opinions', 'fk_opinions_createdBy', 'createdBy', 'users', 'id');
		$this->addForeignKeySafelyIfTableExists('opinions', 'fk_opinions_modifiedBy', 'modifiedBy', 'users', 'id');
		$this->addForeignKeySafelyIfTableExists('opinions', 'fk_opinions_workflow', 'workflow', 'opinion_workflows', 'id');
		$this->addForeignKeySafelyIfTableExists('opinions', 'fk_opinions_stage', 'stage', 'legal_case_litigation_details', 'id');
		$this->addForeignKeySafelyIfTableExists('opinions', 'fk_opinions_contract', 'contract_id', 'contract', 'id', 'CASCADE', 'CASCADE');

		// Opinion relationship tables
		$this->addForeignKeySafely(
			'opinion_comments',
			'fk_opinion_comments_opinion',
			'opinion_id',
			'opinions',
			'id'
		);

		$this->addForeignKeySafely(
			'opinion_comments',
			'fk_opinion_comments_createdBy',
			'createdBy',
			'users',
			'id'
		);

		$this->addForeignKeySafely(
			'opinion_comments',
			'fk_opinion_comments_modifiedBy',
			'modifiedBy',
			'users',
			'id'
		);

		$this->addForeignKeySafely(
			'opinion_contributors',
			'fk_opinion_contributors_opinion',
			'opinion_id',
			'opinions',
			'id',
			'CASCADE',
			'CASCADE'
		);

		$this->addForeignKeySafely(
			'opinion_contributors',
			'fk_opinion_contributors_user',
			'user_id',
			'users',
			'id',
			'CASCADE',
			'CASCADE'
		);

		$this->addForeignKeySafely(
			'opinion_url',
			'fk_opinion_url_opinion',
			'opinion_id',
			'opinions',
			'id',
			'CASCADE',
			'CASCADE'
		);

		$this->addForeignKeySafely(
			'opinion_url',
			'fk_opinion_url_document_type',
			'document_type_id',
			'opinion_document_type',
			'id',
			'CASCADE',
			'CASCADE'
		);

		$this->addForeignKeySafely(
			'opinion_url',
			'fk_opinion_url_document_status',
			'document_status_id',
			'opinion_document_status',
			'id',
			'CASCADE',
			'CASCADE'
		);

		$this->addForeignKeySafely(
			'opinion_url',
			'fk_opinion_url_createdBy',
			'createdBy',
			'users',
			'id'
		);

		$this->addForeignKeySafely(
			'opinion_url',
			'fk_opinion_url_modifiedBy',
			'modifiedBy',
			'users',
			'id'
		);

		$this->addForeignKeySafely(
			'opinion_users',
			'fk_opinion_users_opinion',
			'opinion_id',
			'opinions',
			'id',
			'CASCADE',
			'CASCADE'
		);

		$this->addForeignKeySafely(
			'opinion_users',
			'fk_opinion_users_user',
			'user_id',
			'users',
			'id',
			'CASCADE',
			'CASCADE'
		);

		$this->addForeignKeySafely(
			'opinions_documents',
			'fk_opinions_documents_opinion',
			'opinion_id',
			'opinions',
			'id',
			'CASCADE',
			'CASCADE'
		);

		$this->addForeignKeySafely(
			'opinions_documents',
			'fk_opinions_documents_document',
			'document_id',
			'documents_management_system',
			'id',
			'CASCADE',
			'CASCADE'
		);

		$this->addForeignKeySafely(
			'opinion_workflow_status_transition_history',
			'fk_opinion_workflow_status_transition_history_opinion',
			'opinion_id',
			'opinions',
			'id'
		);

		$this->addForeignKeySafely(
			'opinion_workflow_status_transition_history',
			'fk_opinion_workflow_status_transition_history_from',
			'from_step',
			'opinion_statuses',
			'id'
		);

		$this->addForeignKeySafely(
			'opinion_workflow_status_transition_history',
			'fk_opinion_workflow_status_transition_history_to',
			'to_step',
			'opinion_statuses',
			'id'
		);

		$this->addForeignKeySafely(
			'opinion_workflow_status_transition_history',
			'fk_opinion_workflow_status_transition_history_user',
			'user_id',
			'users',
			'id'
		);

		// --- 7. Create Indexes for Performance ---

		$this->addIndexSafely('opinions', 'IX_opinions_status', '[opinion_status_id]');
		$this->addIndexSafely('opinions', 'IX_opinions_type', '[opinion_type_id]');
		$this->addIndexSafely('opinions', 'IX_opinions_workflow', '[workflow]');
		$this->addIndexSafely('opinions', 'IX_opinions_assigned_to', '[assigned_to]');
		$this->addIndexSafely('opinions', 'IX_opinions_createdOn', '[createdOn]');

		$this->addIndexSafely('opinion_workflow_status_relation', 'IX_workflow_status_relation_workflow', '[workflow_id]');
		$this->addIndexSafely('opinion_workflow_status_relation', 'IX_workflow_status_relation_status', '[status_id]');
		$this->addIndexSafely('opinion_workflow_status_transition', 'IX_workflow_transition_workflow', '[workflow_id]');
		$this->addIndexSafely('opinion_workflow_status_transition', 'IX_workflow_transition_from', '[from_step]');
		$this->addIndexSafely('opinion_workflow_status_transition', 'IX_workflow_transition_to', '[to_step]');
		$this->addIndexSafely('opinion_workflow_status_transition_screen_fields', 'IX_workflow_transition_screen_fields', '[transition]');

		$this->addIndexSafely('opinion_comments', 'IX_opinion_comments_opinion', '[opinion_id]');
		$this->addIndexSafely('opinion_contributors', 'IX_opinion_contributors_opinion', '[opinion_id]');
		$this->addIndexSafely('opinion_contributors', 'IX_opinion_contributors_user', '[user_id]');
		$this->addIndexSafely('opinion_url', 'IX_opinion_url_opinion', '[opinion_id]');
		$this->addIndexSafely('opinion_users', 'IX_opinion_users_opinion', '[opinion_id]');
		$this->addIndexSafely('opinion_users', 'IX_opinion_users_user', '[user_id]');
		$this->addIndexSafely('opinions_documents', 'IX_opinions_documents_opinion', '[opinion_id]');
		$this->addIndexSafely('opinion_workflow_status_transition_history', 'IX_opinion_transition_history_opinion', '[opinion_id]');

		// --- 8. Insert Initial Data ---
		$this->insertInitialData();

		$this->db->trans_complete();
	}

	public function down()
	{
		$this->db->trans_start();

		// Drop all tables safely
		$this->dropAllTables();

		$this->db->trans_complete();
	}

	/**
	 * Helper method to safely add foreign key constraints with existence check
	 */
	/**
	 * Helper method to safely add foreign key constraints with existence check
	 * Updated to handle cascade conflicts
	 */
	private function addForeignKeySafely($tableName, $constraintName, $column, $referenceTable, $referenceColumn, $onUpdate = null, $onDelete = null)
	{
		// First check if reference table exists
		$checkRefTable = $this->db->query("
        SELECT COUNT(*) as cnt 
        FROM sys.tables 
        WHERE name = '{$referenceTable}' AND type = 'U'
    ")->row();

		if ($checkRefTable->cnt == 0) {
			log_message('info', "Reference table '{$referenceTable}' doesn't exist. Skipping FK: {$constraintName}");
			return;
		}

		// Check if constraint already exists
		$checkSql = "SELECT COUNT(*) as cnt FROM sys.foreign_keys WHERE name = '{$constraintName}'";
		$result = $this->db->query($checkSql)->row();

		if ($result->cnt == 0) {
			// For contract-related FKs, avoid cascade to prevent cycles
			if (strpos($constraintName, '_contract') !== false || $referenceTable === 'contract') {
				// Use safer options for contract references
				$onUpdate = 'NO ACTION';
				$onDelete = 'SET NULL';
				log_message('info', "Using safe cascade options for contract FK: {$constraintName}");
			}

			// Build the SQL
			$sql = "ALTER TABLE [dbo].[{$tableName}] 
                WITH CHECK ADD CONSTRAINT [{$constraintName}] 
                FOREIGN KEY([{$column}]) 
                REFERENCES [dbo].[{$referenceTable}] ([{$referenceColumn}])";

			// Add ON UPDATE if specified
			if ($onUpdate) {
				$sql .= " ON UPDATE {$onUpdate}";
			}

			// Add ON DELETE if specified
			if ($onDelete) {
				$sql .= " ON DELETE {$onDelete}";
			}

			try {
				$this->db->query($sql);
				log_message('info', "Created constraint: {$constraintName}");
			} catch (Exception $e) {
				// If cascade fails, try without cascade
				if (strpos($e->getMessage(), 'multiple cascade paths') !== false ||
				    strpos($e->getMessage(), 'cycles') !== false) {
					log_message('warning', "Cascade conflict for {$constraintName}, trying without cascade...");

					$sql = "ALTER TABLE [dbo].[{$tableName}] 
                        WITH CHECK ADD CONSTRAINT [{$constraintName}] 
                        FOREIGN KEY([{$column}]) 
                        REFERENCES [dbo].[{$referenceTable}] ([{$referenceColumn}])";

					try {
						$this->db->query($sql);
						log_message('info', "Created constraint without cascade: {$constraintName}");
					} catch (Exception $e2) {
						log_message('error', "Failed to create constraint {$constraintName} even without cascade: " . $e2->getMessage());
					}
				} else {
					log_message('error', "Failed to create constraint {$constraintName}: " . $e->getMessage());
				}
			}
		} else {
			log_message('info', "Constraint already exists: {$constraintName}");
		}
	}
	/**
	 * Helper method to add foreign key only if parent table exists
	 */
	private function addForeignKeySafelyIfTableExists($tableName, $constraintName, $column, $referenceTable, $referenceColumn, $onUpdate = null, $onDelete = null)
	{
		// Check if both tables exist
		$checkTables = $this->db->query("
            SELECT 
                (SELECT COUNT(*) FROM sys.tables WHERE name = '{$tableName}' AND type = 'U') as table_exists,
                (SELECT COUNT(*) FROM sys.tables WHERE name = '{$referenceTable}' AND type = 'U') as ref_table_exists
        ")->row();

		if ($checkTables->table_exists > 0 && $checkTables->ref_table_exists > 0) {
			$this->addForeignKeySafely($tableName, $constraintName, $column, $referenceTable, $referenceColumn, $onUpdate, $onDelete);
		} else {
			log_message('info', "Skipping FK {$constraintName}: Tables not found ({$tableName}, {$referenceTable})");
		}
	}

	/**
	 * Helper method to safely add indexes with existence check
	 */
	private function addIndexSafely($tableName, $indexName, $columns)
	{
		// Check if table exists
		$checkTable = $this->db->query("
            SELECT COUNT(*) as cnt 
            FROM sys.tables 
            WHERE name = '{$tableName}' AND type = 'U'
        ")->row();

		if ($checkTable->cnt == 0) {
			log_message('info', "Table '{$tableName}' doesn't exist. Skipping index: {$indexName}");
			return;
		}

		// Check if index already exists
		$checkSql = "
            SELECT COUNT(*) as cnt 
            FROM sys.indexes 
            WHERE name = '{$indexName}' 
            AND object_id = OBJECT_ID('dbo.{$tableName}')
        ";
		$result = $this->db->query($checkSql)->row();

		if ($result->cnt == 0) {
			$sql = "CREATE INDEX [{$indexName}] ON [dbo].[{$tableName}] ({$columns})";
			try {
				$this->db->query($sql);
				log_message('info', "Created index: {$indexName}");
			} catch (Exception $e) {
				log_message('error', "Failed to create index {$indexName}: " . $e->getMessage());
			}
		} else {
			log_message('info', "Index already exists: {$indexName}");
		}
	}

	/**
	 * Helper method to drop all tables safely
	 */
	private function dropAllTables()
	{
		// First, drop all foreign key constraints in the database that reference our tables
		$tables = [
			'opinion_workflow_status_transition_history',
			'opinions_documents',
			'opinion_users',
			'opinion_url',
			'opinion_comments',
			'opinion_contributors',
			'opinion_workflow_status_transition_permissions',
			'opinion_workflow_status_transition_screen_fields',
			'opinion_workflow_types',
			'opinion_workflow_status_transition',
			'opinion_workflow_status_relation',
			'opinion_workflows',
			'opinion_types_languages',
			'opinion_document_type_language',
			'opinion_document_status_language'
		];

		// Drop all foreign key constraints that reference these tables
		foreach ($tables as $table) {
			$this->db->query("
            DECLARE @sql NVARCHAR(MAX) = '';
            
            -- Generate SQL to drop all foreign keys that reference this table
            SELECT @sql = @sql + 'ALTER TABLE ' + QUOTENAME(OBJECT_SCHEMA_NAME(parent_object_id)) 
                + '.' + QUOTENAME(OBJECT_NAME(parent_object_id)) 
                + ' DROP CONSTRAINT ' + QUOTENAME(name) + ';'
            FROM sys.foreign_keys 
            WHERE referenced_object_id = OBJECT_ID('dbo.{$table}');
            
            -- Execute the generated SQL
            IF @sql <> ''
            BEGIN
                EXEC sp_executesql @sql;
            END
        ");
		}

		// Now drop all the tables
		foreach ($tables as $table) {
			$this->db->query("IF OBJECT_ID('dbo.{$table}', 'U') IS NOT NULL DROP TABLE [dbo].[{$table}]");
		}
	}

	/**
	 * Insert initial data into tables
	 */
	private function insertInitialData()
	{
		// Check if we're running in a fresh installation
		$checkData = $this->db->query("SELECT COUNT(*) as cnt FROM [dbo].[opinion_workflows]")->row();

		if ($checkData->cnt > 0) {
			log_message('info', 'Initial data already exists. Skipping data insertion.');
			return;
		}

		try {
			// --- 1. Get default English language ID ---
			$englishLang = $this->db->query("SELECT [id] FROM [dbo].[languages] WHERE [code] = 'en' OR [name] = 'English' LIMIT 1")->row();
			$englishLangId = $englishLang ? $englishLang->id : 1; // Default to ID 1 if not found

			// --- 2. Create Default Workflow ---
			$this->db->query("
                INSERT INTO [dbo].[opinion_workflows] ([name], [type], [createdOn]) 
                VALUES ('Standard Opinion Workflow', 'standard', GETDATE())
            ");
			$workflowId = $this->getLastInsertId();

			// --- 3. Insert Language Data for Opinion Types (assuming opinion_types table exists) ---
			// Check if opinion_types has data
			$opinionTypesCount = $this->db->query("SELECT COUNT(*) as cnt FROM [dbo].[opinion_types]")->row()->cnt;

			if ($opinionTypesCount > 0) {
				// Get existing opinion types
				$opinionTypes = $this->db->query("SELECT [id], [name] FROM [dbo].[opinion_types]")->result_array();

				foreach ($opinionTypes as $type) {
					$this->db->query("
                        INSERT INTO [dbo].[opinion_types_languages] 
                        ([opinion_type_id], [language_id], [name], [applies_to]) 
                        VALUES (?, ?, ?, ?)",
						[$type['id'], $englishLangId, $type['name'], 'Opinions']
					);
				}
				log_message('info', 'Inserted ' . count($opinionTypes) . ' opinion type language records');
			}

			// --- 4. Insert Language Data for Document Statuses (assuming opinion_document_status table exists) ---
			$docStatusCount = $this->db->query("SELECT COUNT(*) as cnt FROM [dbo].[opinion_document_status]")->row()->cnt;

			if ($docStatusCount > 0) {
				$docStatuses = $this->db->query("SELECT [id], [name] FROM [dbo].[opinion_document_status]")->result_array();

				foreach ($docStatuses as $status) {
					$this->db->query("
                        INSERT INTO [dbo].[opinion_document_status_language] 
                        ([status_id], [language_id], [name]) 
                        VALUES (?, ?, ?)",
						[$status['id'], $englishLangId, $status['name']]
					);
				}
				log_message('info', 'Inserted ' . count($docStatuses) . ' document status language records');
			}

			// --- 5. Insert Language Data for Document Types (assuming opinion_document_type table exists) ---
			$docTypeCount = $this->db->query("SELECT COUNT(*) as cnt FROM [dbo].[opinion_document_type]")->row()->cnt;

			if ($docTypeCount > 0) {
				$docTypes = $this->db->query("SELECT [id], [name] FROM [dbo].[opinion_document_type]")->result_array();

				foreach ($docTypes as $type) {
					$this->db->query("
                        INSERT INTO [dbo].[opinion_document_type_language] 
                        ([type_id], [language_id], [name], [applies_to]) 
                        VALUES (?, ?, ?, ?)",
						[$type['id'], $englishLangId, $type['name'], 'opinions']
					);
				}
				log_message('info', 'Inserted ' . count($docTypes) . ' document type language records');
			}

			// --- 6. Associate workflow with opinion types ---
			if ($opinionTypesCount > 0) {
				foreach ($opinionTypes as $type) {
					$this->db->query("
                        INSERT INTO [dbo].[opinion_workflow_types] 
                        ([workflow_id], [type_id]) 
                        VALUES (?, ?)",
						[$workflowId, $type['id']]
					);
				}
			}

			// --- 7. Create default workflow status relations (assuming opinion_statuses table exists) ---
			$statusCount = $this->db->query("SELECT COUNT(*) as cnt FROM [dbo].[opinion_statuses]")->row()->cnt;

			if ($statusCount > 0) {
				$statuses = $this->db->query("SELECT [id], [name] FROM [dbo].[opinion_statuses] ORDER BY [id]")->result_array();

				$startPointSet = false;
				foreach ($statuses as $index => $status) {
					$startPoint = (!$startPointSet) ? 1 : 0;
					if ($startPoint) $startPointSet = true;

					$this->db->query("
                        INSERT INTO [dbo].[opinion_workflow_status_relation] 
                        ([workflow_id], [status_id], [start_point]) 
                        VALUES (?, ?, ?)",
						[$workflowId, $status['id'], $startPoint]
					);

					// Create transitions between consecutive statuses
					if ($index > 0) {
						$prevStatus = $statuses[$index - 1];
						$transitionName = "Move from {$prevStatus['name']} to {$status['name']}";

						$this->db->query("
                            INSERT INTO [dbo].[opinion_workflow_status_transition] 
                            ([workflow_id], [from_step], [to_step], [name]) 
                            VALUES (?, ?, ?, ?)",
							[$workflowId, $prevStatus['id'], $status['id'], $transitionName]
						);
					}
				}
				log_message('info', 'Inserted workflow status relations and transitions');
			}

			// --- 8. Insert sample data for demonstration ---
			$this->insertSampleData($workflowId, $englishLangId);

			log_message('info', 'Successfully inserted initial data for opinion module');

		} catch (Exception $e) {
			log_message('error', 'Failed to insert initial data: ' . $e->getMessage());
			// Don't throw exception to allow migration to continue
		}
	}

	/**
	 * Insert sample data for demonstration
	 */
	private function insertSampleData($workflowId, $englishLangId)
	{
		// Only insert sample data if tables are completely empty
		$checkSample = $this->db->query("SELECT COUNT(*) as cnt FROM [dbo].[opinion_comments]")->row()->cnt;

		if ($checkSample > 0) {
			return; // Sample data already exists
		}

		// Insert sample comments (assuming there are some opinions)
		$opinionCount = $this->db->query("SELECT COUNT(*) as cnt FROM [dbo].[opinions]")->row()->cnt;

		if ($opinionCount > 0) {
			// Get first opinion and first user
			$firstOpinion = $this->db->query("SELECT TOP 1 [id] FROM [dbo].[opinions] ORDER BY [id]")->row();
			$firstUser = $this->db->query("SELECT TOP 1 [id] FROM [dbo].[users] WHERE [status] = 'Active' ORDER BY [id]")->row();

			if ($firstOpinion && $firstUser) {
				// Sample comment
				$this->db->query("
                    INSERT INTO [dbo].[opinion_comments] 
                    ([opinion_id], [comment], [createdBy], [modifiedBy], [added_from_channel]) 
                    VALUES (?, ?, ?, ?, ?)",
					[$firstOpinion->id, 'Initial review completed. Please proceed with next steps.', $firstUser->id, $firstUser->id, 'CP']
				);

				// Sample contributor
				$this->db->query("
                    INSERT INTO [dbo].[opinion_contributors] 
                    ([opinion_id], [user_id]) 
                    VALUES (?, ?)",
					[$firstOpinion->id, $firstUser->id]
				);

				// Sample opinion_user relation
				$this->db->query("
                    INSERT INTO [dbo].[opinion_users] 
                    ([opinion_id], [user_id]) 
                    VALUES (?, ?)",
					[$firstOpinion->id, $firstUser->id]
				);

				log_message('info', 'Inserted sample opinion relationship data');
			}
		}
	}

	/**
	 * Get the last inserted ID
	 */
	private function getLastInsertId()
	{
		$result = $this->db->query("SELECT SCOPE_IDENTITY() as id")->row();
		return $result ? $result->id : 0;
	}
}