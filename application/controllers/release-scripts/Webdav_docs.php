<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
/* *
 * webdav_docs
 * This class Is Used To Migrate All Attachments Under All Models In The Application To Be Compatible With Webdav Structure
 * Models That Have Attachments To Be Migrated In The App Are: "Cases [including Case Notes Attachments]", "Contacts", "Companies", "iDocs [other-documents & Templates],
 * "Bills [related Documents & Bills Payments Attachments]", "Expenses [related Documents]", "Invoices [related Docuemnts & Invoices Payments Attachments]"
 * Run This On CMD: "PATH_TO_APP>php Index.php Release-scripts/webdav_docs"
 *
 * @author AAloul
 */

Class Webdav_docs extends CI_Controller
{
    public $models = null;
    public $oldDocumentsDirectory = '';
    public $webdavDocumentsDirectory = '';
    public $directorySeparator = '';
    public $cliRequest;

    public function __construct()
    {
        parent::__construct();
        ini_set('max_execution_time', 4000);
        $this->hooks->enabled = false;
        $this->load->database();
        $this->load->library(array('is_auth'));
        $this->load->helper(array('url'));
        $this->cliRequest = is_cli();
        $this->models = array(
            'case' => array('lineage' => '1/2'),
            'company' => array('lineage' => '1/3'),
            'contact' => array('lineage' => '1/4'),
            'document' => array('lineage' => '1/5'),
            'money' => array('lineage' => '1/6'),
            'BI' => array('lineage' => '1/6/7'), // bills
            'EXP' => array('lineage' => '1/6/8'), //expenses
            'INV' => array('lineage' => '1/6/9'), // invoices
            'organizations' => array('lineage' => '1/6/10'),
            'cover_pages' => array('lineage' => '1/6/11'),
        );
        $this->directorySeparator = '/';
        $this->oldDocumentsDirectory = $this->config->item('files_path').$this->directorySeparator.'attachments'.$this->directorySeparator;
        $this->webdavDocumentsDirectory = WEBDAVPATH.'data';
    }

    public function index()
    {
        $tableCreated = $this->createWebdavTable();
        if ($tableCreated === false) {
            $this->printLog('log', 'Please contact your support team to continue migration process.', '<h3 style="font-family:Arial;color:red">', '</h3>');

            return false;
        }

        $nodeCount = 12; // This variable is needed to rename each new copied node [To be matched with the inserted db record]. It is also used to reduce DB queries to get last inserted id
        $filesCount = 0;
        $foldersCount = 0;
        $this->printLog('display', 'Migration Started...', '<h1 style="font-family:Arial;">', '</h1>');
        // Cases Attachments Migration:
        $this->printLog('display', 'Migrating Cases Attachments...', '<h2 style="font-family:Arial;color:orange">', '</h2>');
        $data = array('modelC' => 'Legal_Case', 'modelS' => 'legal_case', 'modelName' => 'case', 'folder' => 'cases');
        $this->modelsAttachmentsMigration($data, $nodeCount, $filesCount, $foldersCount);

        // Companies Attachments Migration:
        $this->printLog('display', 'Migrating Companies Attachments...', '<h2 style="font-family:Arial;color:orange">', '</h2>');
        $data = array('modelC' => 'Company', 'modelS' => 'company', 'modelName' => 'company', 'folder' => 'companies');
        $this->modelsAttachmentsMigration($data, $nodeCount, $filesCount, $foldersCount);

        // Contacts Attachments Migration:
        $this->printLog('display', 'Migrating Contacts Attachments...', '<h2 style="font-family:Arial;color:orange">', '</h2>');
        $data = array('modelC' => 'Contact', 'modelS' => 'contact', 'modelName' => 'contact', 'folder' => 'contacts');
        $this->modelsAttachmentsMigration($data, $nodeCount, $filesCount, $foldersCount);

        // iDocs Attachments Migration:
        $this->printLog('display', 'Migrating iDocs Attachments...', '<h2 style="font-family:Arial;color:orange">', '</h2>');
        $this->iDocsAttachmentsMigration($nodeCount, $filesCount, $foldersCount);

        //Bills Attachments Migration:
        $error = 0;
        $this->printLog('display', 'Migrating Money Bills Attachments...', '<h2 style="font-family:Arial;color:orange">', '</h2>');
        $this->billAttachmentsMigration($nodeCount, $filesCount, $foldersCount, $error);

        // Expenses Attachments Migration:
        $this->printLog('display', 'Migrating Money Expenses Attachments...', '<h2 style="font-family:Arial;color:orange">', '</h2>');
        $this->expensesAttachmentsMigration($nodeCount, $filesCount, $foldersCount, $error);

        // Invoices Attachments Migration:
        $this->printLog('display', 'Migrating Money Invoices Attachments...', '<h2 style="font-family:Arial;color:orange">', '</h2>');
        $this->invoiceAttachmentsMigration($nodeCount, $filesCount, $foldersCount, $error);

        $this->printLog('display', '-----------------------------------------------------------------------');
        $this->printLog('display', 'Total migrated files: '.$filesCount, '<h1 style="font-family:Arial;color:red">', '</h1>');
        $this->printLog('display', 'Total migrated Directories: '.$foldersCount, '<h1 style="font-family:Arial;color:red">', '</h1>');
    }

    public function createWebdavTable()
    {
        if (!strcmp($this->db->dbdriver, 'mysqli')) {
            $webdavTable = "
            CREATE TABLE IF NOT EXISTS webdav_tree_structure (
              id int(11) NOT NULL AUTO_INCREMENT,
              parentId int(11) DEFAULT NULL,
              name text NOT NULL,
              versionNb int(10) DEFAULT NULL,
              lineage text NOT NULL,
              type enum('file','folder') NOT NULL,
              private char(3) DEFAULT NULL,
              document_status_id int(10) unsigned DEFAULT NULL,
              physicalName text NOT NULL,
              document_type_id int(10) unsigned DEFAULT NULL,
              comments text NOT NULL,
              modelName varchar(255) DEFAULT NULL,
              modelId int(11) DEFAULT NULL,
              createdOn timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
              createdBy int(11) NOT NULL,
              modifiedOn datetime DEFAULT NULL,
              modifiedBy int(11) NOT NULL,
              PRIMARY KEY (id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
        } else {
            $webdavTable = "IF OBJECT_ID('dbo.webdav_tree_structure', 'U') IS NOT NULL DROP TABLE dbo.webdav_tree_structure;                
                CREATE TABLE webdav_tree_structure (
                  id BIGINT NOT NULL PRIMARY KEY IDENTITY,
                  parentId BIGINT NULL,
                  name nvarchar(255) NOT NULL,
                  versionNb BIGINT DEFAULT NULL,
                  lineage nvarchar(255) NOT NULL,
                  type nvarchar(14) NOT NULL CHECK(type IN ('file','folder')),
                  private CHAR( 3 ) NULL DEFAULT NULL,
                  document_status_id BIGINT DEFAULT NULL,
                  physicalName text NOT NULL,
                  document_type_id BIGINT DEFAULT NULL,
                  comments text NOT NULL,
                  modelName nvarchar(35) DEFAULT NULL,
                  modelId BIGINT DEFAULT NULL,
                  createdOn smalldatetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                  createdBy BIGINT NOT NULL,
                  modifiedOn smalldatetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                  modifiedBy BIGINT NOT NULL
                );";
        }
        $webdavTableR = $this->db->query($webdavTable);
        if ($webdavTableR) {
            $this->printLog('log', 'Creating webdav table structure ...', '<p style="font-family:Arial;font-size:15px">', '</p>');
            $staticData = "INSERT INTO webdav_tree_structure (parentId, name, versionNb, lineage, type, private, document_status_id, physicalName, document_type_id, comments, modelName, modelId, createdOn, createdBy, modifiedOn, modifiedBy) VALUES
                    (NULL,	'root',	NULL,	'/1',	'folder',	NULL,	NULL,	'',	NULL,	'',	NULL,	NULL,	CURRENT_TIMESTAMP,	0,	CURRENT_TIMESTAMP,	0),
                    (1,	'case',	NULL,	'/1/2',	'folder',	NULL,	NULL,	'2',	NULL,	'',	NULL,	NULL,	CURRENT_TIMESTAMP,	1,	CURRENT_TIMESTAMP,	1),
                    (1,	'company',	NULL,	'/1/3',	'folder',	NULL,	NULL,	'3',	NULL,	'',	NULL,	NULL,	CURRENT_TIMESTAMP,	1,	CURRENT_TIMESTAMP,	1),
                    (1,	'contact',	NULL,	'/1/4',	'folder',	NULL,	NULL,	'4',	NULL,	'',	NULL,	NULL,	CURRENT_TIMESTAMP,	1,	CURRENT_TIMESTAMP,	1),
                    (1,	'document',	NULL,	'/1/5',	'folder',	NULL,	NULL,	'5',	NULL,	'',	NULL,	NULL,	CURRENT_TIMESTAMP,	1,	CURRENT_TIMESTAMP,	1),
                    (1,	'money',	NULL,	'/1/6',	'folder',	NULL,	NULL,	'6',	NULL,	'',	NULL,	NULL,	CURRENT_TIMESTAMP,	1,	CURRENT_TIMESTAMP,	1),
                    (6,	'BI',	NULL,	'/1/6/7',	'folder',	NULL,	NULL,	'7',	NULL,	'',	NULL,	NULL,	CURRENT_TIMESTAMP,	1,	CURRENT_TIMESTAMP,	1),
                    (6,	'EXP',	NULL,	'/1/6/8',	'folder',	NULL,	NULL,	'8',	NULL,	'',	NULL,	NULL,	CURRENT_TIMESTAMP,	1,	CURRENT_TIMESTAMP,	1),
                    (6,	'INV',	NULL,	'/1/6/9',	'folder',	NULL,	NULL,	'9',	NULL,	'',	NULL,	NULL,	CURRENT_TIMESTAMP,	1,	CURRENT_TIMESTAMP,	1),
                    (6,	'organizations',	NULL,	'/1/6/10',	'folder',	NULL,	NULL,	'10',	NULL,	'',	NULL,	NULL,	CURRENT_TIMESTAMP,	1,	CURRENT_TIMESTAMP,	1),
                    (6,	'cover_pages',	NULL,	'/1/6/11',	'folder',	NULL,	NULL,	'11',	NULL,	'',	NULL,	NULL,	CURRENT_TIMESTAMP,	1,	CURRENT_TIMESTAMP,	1);";
            $checkModelR = $this->db->query($staticData);
            if ($checkModelR) {
                $this->printLog('log', 'Initial values was inserted successfully to webdav table', '<p style="font-family:Arial;font-size:15px">', '</p>');
            } else {
                $this->printLog('log', '*** Error ***  Initial data was not inserted succeffully', '<h3 style="font-family:Arial;color:red">', '</h3>');

                return false;
            }
        } else {
            $this->printLog('log', '*** Error ***  Webdav table is not created successfully', '<h3 style="font-family:Arial;color:red">', '</h3>');

            return false;
        }

        return true;
    }

    public function modelsAttachmentsMigration($data, &$nodeCount, &$filesCount, &$foldersCount)
    {
        $errorCount = 0;
        $legalCaseAttError = 0;
        extract($data);
        $modelFactory = $modelC.'factory';
        $this->load->model($modelC, $modelFactory);
        $this->$modelC = $this->$modelFactory->get_instance();
        $model_ModelCode = $this->$modelC->get('modelCode');

        $modelAttachment = $modelC.'_attachment_version';
        $modelAttachmentFactory = $modelC.'_attachment_versionfactory';
        $this->load->model($modelAttachment, $modelAttachmentFactory);
        $this->$modelAttachment = $this->$modelAttachmentFactory->get_instance();
        $versionPrefix = $this->$modelAttachment->get('folderVersionsSuffixTerm');
        $model_FetchedById = array();
        $mappingArray = array(); // This is used to map old attachment IDs with the webdav attachment IDs to easily get the newly inserted parent IDs
        $model_Query = 'SELECT '.$modelS.'_attachment_tree_view.*, '.$modelS.'_attachment_versions.name AS versionName FROM '.$modelS.'_attachment_tree_view '
            .'LEFT JOIN '.$modelS.'_attachment_versions ON '.$modelS.'_attachment_tree_view.clearId = '.$modelS.'_attachment_versions.'.$modelS.'_attachment_id '
            .'ORDER BY '.$modelS.'_attachment_tree_view.clearId ASC ';
        $model_Results = $this->db->query($model_Query);
        $model_Attachments = $model_Results->result();

        $modelParentLineage = $this->directorySeparator.$this->models[$modelName]['lineage'];
        $modelParentId = $this->_getIdFromPath($modelParentLineage);
        foreach ($model_Attachments as $record) {
            // Check if the parent directory wasn't be added to webdav physical directory & DB:
            if (!array_key_exists($record->{$modelS.'_id'}, $model_FetchedById)) {
                $this->printLog('log', "Creating parent directory for {$modelName} ".$model_ModelCode.$record->{$modelS.'_id'}.' ...', '<p style="font-family:Arial;font-size:15px">', '</p>');
                $modelId = (int) $record->{$modelS.'_id'};
                $dirLineage = $modelParentLineage.$this->directorySeparator.$nodeCount;
                $dirName = $modelName.'_'.(int) $record->{$modelS.'_id'};
                $checkModelQ = "SELECT webdav_tree_structure.lineage FROM webdav_tree_structure WHERE name = '{$dirName}'";
                $checkModelR = $this->db->query($checkModelQ);
                $modelPDirectory = $checkModelR->result();
                if (!empty($modelPDirectory)) {
                    $firstRecord = $modelPDirectory[0];
                    $model_FetchedById[$record->{$modelS.'_id'}]['modelParent'] = $firstRecord->lineage;
                } else {
                    $webdavQuery = 'INSERT INTO webdav_tree_structure (parentId, name, versionNb, lineage, type, private, document_status_id, physicalName, document_type_id, comments, modelName, modelId, createdOn, createdBy, modifiedOn, modifiedBy) '
                        ."VALUES ({$modelParentId}, '{$dirName}',	NULL, '{$dirLineage}', 'folder', NULL, NULL, '', NULL, '', '{$modelName}', {$modelId}, CURRENT_TIMESTAMP,	0, CURRENT_TIMESTAMP, 0)";
                    $insertQ = $this->db->query($webdavQuery);
                    if ($insertQ) {
                        $dirPath = $this->webdavDocumentsDirectory.$dirLineage;
                        if (!is_dir($dirPath) && !@mkdir($dirPath, 0755, true)) {
                            $this->printLog('log', '*** Error ***  Parent Directory creation failed ['.$model_ModelCode.$record->{$modelS.'_id'}.']', '<h3 style="font-family:Arial;color:red">', '</h3>');
                            $deleteQ = 'DELETE FROM webdav_tree_structure WHERE id = '.$nodeCount;
                            $this->db->query($deleteQ);
                            ++$errorCount;
                        } else {
                            $model_FetchedById[$record->{$modelS.'_id'}]['modelParent'] = $dirLineage;
                        }
                        ++$nodeCount;
                    } else {
                        $this->printLog('log', 'Parent Directory creation failed ['.$model_ModelCode.$record->{$modelS.'_id'}.']', '<h3 style="font-family:Arial;color:red">', '</h3>');
                        ++$errorCount;
                        continue;
                    }
                }
            }
            if (is_null($record->attachmentClearId) && isset($model_FetchedById[$record->{$modelS.'_id'}]['modelParent'])) { // This is assumed to be at tree level "1"
                $webdavLineage = $model_FetchedById[$record->{$modelS.'_id'}]['modelParent'];
                $nodeParentId = $this->_getIdFromPath($webdavLineage);
            } elseif (isset($mappingArray[$record->attachmentClearId])) {
                $webdavLineage = $mappingArray[$record->attachmentClearId]['lineage']; // Get lineage from parent id of old attachment record
                $nodeParentId = $mappingArray[$record->attachmentClearId]['id'];
            } else {
                $this->printLog('log', '*** Error ***  Case attachment <u>'.$record->name.'</u> with ID = '.$record->clearId.' is not migrated !', '<h3 style="font-family:Arial;color:red">', '</h3>');
                ++$errorCount;
                continue;
            }
            $nodeLineage = $webdavLineage.$this->directorySeparator.$nodeCount;
            if (!strcmp($record->type, 'folder')) {
                $versionNb = 'NULL';
            } else {
                $versionNb = 1;
                if (strcmp(trim($record->versionName), '')) { // Check for versioning
                    $versionArr = explode('_', $record->versionName);
                    $versionNb = (int) $versionArr[1];
                }
            }
            $statusId = is_null($record->{$modelS.'_document_status_id'}) ? 0 : $record->{$modelS.'_document_status_id'};
            $typeId = is_null($record->{$modelS.'_document_type_id'}) ? 0 : $record->{$modelS.'_document_type_id'};
            $comments = str_replace("'", "''", $record->comments);
            $name = str_replace("'", "''", $record->name);
            $webdavQuery = 'INSERT INTO webdav_tree_structure (parentId, name, versionNb, lineage, type, private, document_status_id, physicalName, document_type_id, comments, modelName, modelId, createdOn, createdBy, modifiedOn, modifiedBy) '
                ."VALUES ({$nodeParentId}, '{$name}',	{$versionNb}, '{$nodeLineage}', '{$record->type}', '{$record->private}', {$statusId}, '{$nodeCount}', {$typeId}, '{$comments}', '', '', '{$record->createdOn}', {$record->createdBy}, '{$record->modifiedOn}', {$record->modifiedById})";
            $insertQ = $this->db->query($webdavQuery);
            if ($insertQ) {
                // First update privacy if exists with the new record id
                if (!strcmp($record->private, 'yes')) {
                    $privacyQ = "UPDATE document_managment_users SET recordId = {$nodeCount} WHERE recordId = {$record->clearId} AND model ='{$folder}';";
                    $privacyR = $this->db->query($privacyQ);
                    if (!$privacyR) {
                        $this->printLog('log', '*** Error ***  Privacy over folder "'.$record->name.'" [ID = '.$record->clearId.'] is not updated!', '<h3 style="font-family:Arial;color:red">', '</h3>');
                        ++$errorCount;
                    }
                }
                // Create Directories / Move Files from old physical structure:
                $nodePath = $this->webdavDocumentsDirectory.$nodeLineage;
                if (!strcmp($record->type, 'folder')) {
                    if (!is_dir($nodePath) && !@mkdir($nodePath, 0755, true)) {
                        $this->printLog('log', '*** Error ***  Creating directory failed '.$record->name.' [ID = '.$record->clearId.']', '<h3 style="font-family:Arial;color:red">', '</h3>');
                        $deleteQ = 'DELETE FROM webdav_tree_structure WHERE id = '.$nodeCount;
                        $this->db->query($deleteQ);
                        ++$errorCount;
                    } else {
                        ++$foldersCount;
                        $this->printLog('log', ' -- Migrating "'.$record->name.'" [ID = '.$record->clearId.']... ', '<p style="font-family:Arial;font-size:14px">', '<font color="green"><b>Done!</b></font></p>');
                        $mappingArray[$record->clearId]['id'] = $nodeCount;
                        $mappingArray[$record->clearId]['lineage'] = $nodeLineage;
                    }
                } else {
                    if (!strcmp($modelS, 'company')) {
                        $physicalModelId = $model_ModelCode.str_pad($record->{$modelS.'_id'}, 8, '0', STR_PAD_LEFT);
                    } else {
                        $physicalModelId = $model_ModelCode.$record->{$modelS.'_id'};
                    }
                    $oldFileSubPath = substr($record->fullPath, 1).$this->directorySeparator;
                    $oldfilePath = $this->oldDocumentsDirectory.$folder.$this->directorySeparator.$physicalModelId.$this->directorySeparator.$oldFileSubPath;
                    $oldfilePath .= strcmp(trim($record->versionName), '') ? $record->clearId.$versionPrefix.$this->directorySeparator.$record->versionName : $record->physicalName;
                    $result = @copy($oldfilePath, $nodePath);
                    if (!$result) {
                        $this->printLog('log', '*** Error ***  The following file is not moved physically: '.$record->name.' [ID = '.$record->clearId.']', '<h3 style="font-family:Arial;color:red">', '</h3>');
                        $deleteQ = 'DELETE FROM webdav_tree_structure WHERE id = '.$nodeCount;
                        $this->db->query($deleteQ);
                        ++$errorCount;
                    } else {
                        ++$filesCount;
                        $this->printLog('log', ' -- Migrating "'.$record->name.'" [ID = '.$record->clearId.']... ', '<p style="font-family:Arial;font-size:14px">', '<font color="green"><b>Done!</b></font></p>');
                        $mappingArray[$record->clearId]['id'] = $nodeCount;
                        $mappingArray[$record->clearId]['lineage'] = $nodeLineage;
                    }
                }
                ++$nodeCount; // In all cases this should be incremented
            }
        }
        // Now migrate Case notes:
        if (!strcmp($modelS, 'legal_case')) {
            $commentQ = 'SELECT case_comment_attachments.* , case_comments.* FROM case_comment_attachments '
                ."LEFT JOIN case_comments ON case_comment_attachments.case_comment_id = case_comments.id WHERE case_comment_attachments.uploaded = 'Yes'";
            $commentR = $this->db->query($commentQ);
            $model_CommentAttachments = $commentR->result();
            foreach ($model_CommentAttachments as $attach) {
                if (!array_key_exists($attach->case_id, $model_FetchedById)) {
                    $this->printLog('log', "Creating parent directory for {$modelName} ".$model_ModelCode.$attach->case_id.' ...', '<p style="font-family:Arial;font-size:15px">', '</p>');
                    $modelId = (int) $attach->case_id;
                    $dirLineage = $modelParentLineage.$this->directorySeparator.$nodeCount;
                    $dirName = $modelName.'_'.(int) $attach->case_id;
                    $checkModelQ = "SELECT webdav_tree_structure.lineage FROM webdav_tree_structure WHERE name = '{$dirName}'";
                    $checkModelR = $this->db->query($checkModelQ);
                    $modelPDirectory = $checkModelR->result();
                    if (!empty($modelPDirectory)) {
                        $firstRecord = $modelPDirectory[0];
                        $model_FetchedById[$attach->case_id]['modelParent'] = $firstRecord->lineage;
                    } else {
                        $webdavQuery = 'INSERT INTO webdav_tree_structure (parentId, name, versionNb, lineage, type, private, document_status_id, physicalName, document_type_id, comments, modelName, modelId, createdOn, createdBy, modifiedOn, modifiedBy) '
                            ."VALUES ({$modelParentId}, '{$dirName}',	NULL, '{$dirLineage}', 'folder', NULL, NULL, '', NULL, '', '{$modelName}', {$modelId}, CURRENT_TIMESTAMP,	0, CURRENT_TIMESTAMP, 0)";
                        $insertQ = $this->db->query($webdavQuery);
                        if ($insertQ) {
                            $dirPath = $this->webdavDocumentsDirectory.$dirLineage;
                            if (!is_dir($dirPath) && !@mkdir($dirPath, 0755, true)) {
                                $this->printLog('log', '*** Error ***  Parent Directory creation failed ['.$model_ModelCode.$attach->case_id.']', '<h3 style="font-family:Arial;color:red">', '</h3>');
                                $deleteQ = 'DELETE FROM webdav_tree_structure WHERE id = '.$nodeCount;
                                $this->db->query($deleteQ);
                                ++$legalCaseAttError;
                                ++$errorCount;
                            } else {
                                $model_FetchedById[$attach->case_id]['modelParent'] = $dirLineage;
                            }
                            ++$nodeCount;
                        } else {
                            $this->printLog('log', 'Parent Directory creation failed ['.$model_ModelCode.$attach->case_id.']', '<h3 style="font-family:Arial;color:red">', '</h3>');
                            ++$errorCount;
                            ++$legalCaseAttError;
                            continue;
                        }
                    }
                }
                if (!isset($model_FetchedById[$attach->case_id]['modelNotes'])) {
                    $this->printLog('log', 'Creating Notes directory for case '.$model_ModelCode.$attach->case_id.' ...', '<p style="font-family:Arial;font-size:15px">', '</p>');
                    $modelName = 'case-notes';
                    $modelId = 1;
                    $dirLineage = $model_FetchedById[$attach->case_id]['modelParent'].$this->directorySeparator.$nodeCount;
                    $notesParentId = $this->_getIdFromPath($model_FetchedById[$attach->case_id]['modelParent']);
                    $dirName = 'Case Notes Attachments';
                    $webdavQuery = 'INSERT INTO webdav_tree_structure (parentId, name, versionNb, lineage, type, private, document_status_id, physicalName, document_type_id, comments, modelName, modelId, createdOn, createdBy, modifiedOn, modifiedBy) '
                        ."VALUES ({$notesParentId}, '{$dirName}',	NULL, '{$dirLineage}', 'folder', NULL, NULL, '', NULL, '', '{$modelName}', {$modelId}, CURRENT_TIMESTAMP,	{$attach->user_id}, CURRENT_TIMESTAMP, {$attach->modifiedBy})";
                    $insertQ = $this->db->query($webdavQuery);
                    if ($insertQ) {
                        $dirPath = $this->webdavDocumentsDirectory.$dirLineage;
                        if (!is_dir($dirPath) && !@mkdir($dirPath, 0755, true)) {
                            $this->printLog('log', '*** Error ***  Notes directory for ['.$model_ModelCode.$attach->case_id.'] is not created!', '<h3 style="font-family:Arial;color:red">', '</h3>');
                            $deleteQ = 'DELETE FROM webdav_tree_structure WHERE id = '.$nodeCount;
                            $this->db->query($deleteQ);
                            ++$errorCount;
                            ++$legalCaseAttError;
                        } else {
                            $model_FetchedById[$attach->case_id]['modelNotes'] = $dirLineage;
                        }
                        ++$nodeCount;
                    } else {
                        $this->printLog('log', 'Parent Directory creation failed ['.$model_ModelCode.$attach->case_id.']', '<h3 style="font-family:Arial;color:red">', '</h3>');
                        ++$errorCount;
                        ++$legalCaseAttError;
                        continue;
                    }
                }
                $parentLineage = $model_FetchedById[$attach->case_id]['modelNotes'];
                $parentId = $this->_getIdFromPath($parentLineage);
                $attachLineage = $parentLineage.$this->directorySeparator.$nodeCount;
                // Now insert the attachment and copy it to the new location:
                $comment = str_replace("'", "''", mb_substr($attach->comment, 0, 20));
                $name = str_replace("'", "''", $attach->name);
                $webdavQuery = 'INSERT INTO webdav_tree_structure (parentId, name, versionNb, lineage, type, private, document_status_id, physicalName, document_type_id, comments, modelName, modelId, createdOn, createdBy, modifiedOn, modifiedBy) '
                    ."VALUES ({$parentId}, '{$name}',	1, '{$attachLineage}', 'file', NULL, NULL, '', NULL, '{$comment}', NULL, NULL, '{$attach->createdOn}', {$attach->user_id}, CURRENT_TIMESTAMP, {$attach->modifiedBy})";
                $insertQ = $this->db->query($webdavQuery);
                if ($insertQ) {
                    $newPath = $this->webdavDocumentsDirectory.$attachLineage;
                    $oldfilePath = $this->oldDocumentsDirectory.$folder.$this->directorySeparator.'__notes'.$this->directorySeparator.$model_ModelCode.$attach->case_id.$this->directorySeparator.$attach->path;
                    $result = @copy($oldfilePath, $newPath);
                    if (!$result) {
                        $this->printLog('log', '*** Error ***  The following case comment file is not moved physically: '.$attach->name.' [ID = '.$attach->id.']', '<h3 style="font-family:Arial;color:red">', '</h3>');
                        $deleteQ = 'DELETE FROM webdav_tree_structure WHERE id = '.$nodeCount;
                        $this->db->query($deleteQ);
                        ++$errorCount;
                        ++$legalCaseAttError;
                    } else {
                        ++$filesCount;
                        $this->printLog('log', ' -- Migrating Case Comment Attachment "'.$attach->name.'" [ID = '.$attach->id.']... ', '<p style="font-family:Arial;font-size:15px">', '<font color="green"><b>Done!</b></font></p>');
                    }
                    ++$nodeCount;
                }
            }
        }
        if ($legalCaseAttError == 0) {
            $query = "DELETE FROM case_comment_attachments WHERE uploaded = 'Yes'";
            $this->db->query($query);
        }
        if ($errorCount > 0) {
            $this->printLog('display', '--- Warning --- some folders/files in "'.$modelName.'" model were not migrated successfully. Please contact your support team', '<h3 style="font-family:Arial;color:red">', '</h3>');
        } else {
            $this->printLog('display', "--- Done --- All folders/files in \"{$modelName}\" model are successfully migrated", '<p style="font-family:Arial;font-size:15px">', '</p>');
            $this->printLog('display', "Deleteing old attachments for {$modelName}...", '<p style="font-family:Arial;font-size:15px">', '</p>');
            $this->_deleteDir($this->oldDocumentsDirectory.$folder.'/');
            $tablesToDelete = array($modelS.'_attachment_versions', $modelS.'_attachment_tree_levels', $modelS.'_attachments');
            $viewsToDelete = array($modelS.'_attachments_accessibility', $modelS.'_attachments_users', $modelS.'_attachment_tree_view');
            $this->_cleanDB($folder, $tablesToDelete, $viewsToDelete);
        }

        return true;
    }

    public function iDocsAttachmentsMigration(&$nodeCount, &$filesCount, &$foldersCount)
    {
        $errorCount = 0;
        // Load required models:
        $this->load->model('document_version', 'Document_Versionfactory');
        $this->document_version = $this->document_versionfactory->get_instance();
        $versionPrefix = $this->document_version->get('folderVersionsSuffixTerm');
        $modelParentLineage = $this->directorySeparator.$this->models['document']['lineage'];
        $modelParentId = $this->_getIdFromPath($modelParentLineage);
        // Create Parent directory for documents:
        $model_FetchedById = array();
        $mappingArray = array();
        $checkModelQ = "SELECT webdav_tree_structure.lineage FROM webdav_tree_structure WHERE name = 'document_1'";
        $checkModelR = $this->db->query($checkModelQ);
        $modelPDirectory = $checkModelR->result();
        if (!empty($modelPDirectory)) {
            $firstRecord = $modelPDirectory[0];
            $docLineage = $firstRecord->lineage;
        } else {
            $this->printLog('log', 'Creating parent directory for "Documents" ...', '<p style="font-family:Arial;font-size:15px">', '</p>');
            $docLineage = $modelParentLineage.$this->directorySeparator.$nodeCount;
            $webdavQuery = 'INSERT INTO webdav_tree_structure (parentId, name, versionNb, lineage, type, private, document_status_id, physicalName, document_type_id, comments, modelName, modelId, createdOn, createdBy, modifiedOn, modifiedBy) '
                ."VALUES ({$modelParentId}, 'document_1',	NULL, '{$docLineage}', 'folder', NULL, NULL, '{$nodeCount}', NULL, '', 'document', 1, CURRENT_TIMESTAMP, 0, CURRENT_TIMESTAMP, 0)";
            $insertQ = $this->db->query($webdavQuery);
            if ($insertQ) {
                $dirPath = $this->webdavDocumentsDirectory.$docLineage;
                if (!is_dir($dirPath) && !@mkdir($dirPath, 0755, true)) {
                    $this->printLog('log', '*** Error ***  Parent Directory creation failed [Templates for iDocs] [ERROR #1]', '<h3 style="font-family:Arial;color:red">', '</h3>');
                    $deleteQ = 'DELETE FROM webdav_tree_structure WHERE id = '.$nodeCount;
                    $this->db->query($deleteQ);
                    ++$errorCount;

                    return false; // Should omit the iDocs migration because there's no parent directory was been created
                }
                ++$nodeCount;
            } else {
                $this->printLog('log', '*** Error ***  Parent Directory creation failed [Templates for iDocs] [ERROR #2]', '<h3 style="font-family:Arial;color:red">', '</h3>');
                ++$errorCount;

                return false;
            }
        }
        $model_Query = 'SELECT document_tree_view.*, document_versions.name AS versionName FROM document_tree_view LEFT JOIN document_versions ON document_tree_view.clearId = document_versions.document_id ORDER BY document_tree_view.clearId ASC ';
        $model_Results = $this->db->query($model_Query);
        $model_Attachments = $model_Results->result();
        $templatesLineage = '';
        $docDirId = $this->_getIdFromPath($docLineage);
        $templateUserUpdated = false;
        foreach ($model_Attachments as $record) {
            if (!$templateUserUpdated && !strcmp($record->documentType, 'templates')) {
                $checkModelQ = "SELECT webdav_tree_structure.lineage FROM webdav_tree_structure WHERE name = 'Templates'";
                $checkModelR = $this->db->query($checkModelQ);
                $modelPDirectory = $checkModelR->result();
                if (!empty($modelPDirectory)) {
                    $firstRecord = $modelPDirectory[0];
                    $templatesLineage = $firstRecord->lineage;
                    $templateUserUpdated = true;
                } else {
                    // Create Parent directory for "Templates":
                    $this->printLog('log', 'Creating parent directory for "Templates" ...', '<p style="font-family:Arial;font-size:15px">', '</p>');
                    $templatesLineage = $docLineage.$this->directorySeparator.$nodeCount;
                    $webdavQuery = 'INSERT INTO webdav_tree_structure (parentId, name, versionNb, lineage, type, private, document_status_id, physicalName, document_type_id, comments, modelName, modelId, createdOn, createdBy, modifiedOn, modifiedBy) '
                        ."VALUES ({$docDirId}, 'Templates',	NULL, '{$templatesLineage}', 'folder', NULL, NULL, '{$nodeCount}', NULL, '', 'iDocs-Templates', 1, CURRENT_TIMESTAMP, {$record->createdBy}, CURRENT_TIMESTAMP, {$record->modifiedById})";
                    $insertQ = $this->db->query($webdavQuery);
                    if ($insertQ) {
                        $dirPath = $this->webdavDocumentsDirectory.$templatesLineage;
                        if (!is_dir($dirPath) && !@mkdir($dirPath, 0755, true)) {
                            $this->printLog('log', '*** Error ***  Parent Directory creation failed [Templates for iDocs] [ERROR #1]', '<h3 style="font-family:Arial;color:red">', '</h3>');
                            $deleteQ = 'DELETE FROM webdav_tree_structure WHERE id = '.$nodeCount;
                            $this->db->query($deleteQ);
                            ++$errorCount;
                        }
                        $templateUserUpdated = true;
                        ++$nodeCount;
                    } else {
                        $this->printLog('log', '*** Error ***  Parent Directory creation failed [Templates for iDocs] [ERROR #2]', '<h3 style="font-family:Arial;color:red">', '</h3>');
                        ++$errorCount;
                    }
                }
            }
            if (is_null($record->documentClearId)) { // This is assumed to be at tree level "1"
                if (!strcmp($record->documentType, 'templates')) {
                    $webdavLineage = $templatesLineage;
                    $nodeParentId = $this->_getIdFromPath($templatesLineage);
                } else {
                    $webdavLineage = $docLineage;
                    $nodeParentId = $docDirId;
                }
            } elseif (isset($mappingArray[$record->documentClearId])) {
                $webdavLineage = $mappingArray[$record->documentClearId]['lineage']; // Get lineage from parent id of old attachment record
                $nodeParentId = $mappingArray[$record->documentClearId]['id'];
            } else {
                $this->printLog('log', '*** Error ***  Case attachment <u>'.$record->name.'</u> with ID = '.$record->clearId.' is not migrated !', '<h3 style="font-family:Arial;color:red">', '</h3>');
                ++$errorCount;
                continue;
            }
            $nodeLineage = $webdavLineage.$this->directorySeparator.$nodeCount;
            if (!strcmp($record->type, 'folder')) {
                $versionNb = 'NULL';
            } else {
                $versionNb = 1;
                if (strcmp(trim($record->versionName), '')) { // Check for versioning
                    $versionArr = explode('_', $record->versionName);
                    $versionNb = (int) $versionArr[1];
                }
            }
            $statusId = is_null($record->document_status_id) ? 0 : $record->document_status_id;
            $typeId = is_null($record->document_type_id) ? 0 : $record->document_type_id;
            $comments = str_replace("'", "''", $record->comments);
            $name = str_replace("'", "''", $record->name);
            $webdavQuery = 'INSERT INTO webdav_tree_structure (parentId, name, versionNb, lineage, type, private, document_status_id, physicalName, document_type_id, comments, modelName, modelId, createdOn, createdBy, modifiedOn, modifiedBy) '
                ."VALUES ({$nodeParentId}, '{$name}',	{$versionNb}, '{$nodeLineage}', '{$record->type}', '{$record->private}', {$statusId}, '{$nodeCount}', {$typeId}, '{$comments}', '', '', '{$record->createdOn}', {$record->createdBy}, '{$record->modifiedOn}', {$record->modifiedById})";
            $insertQ = $this->db->query($webdavQuery);
            if ($insertQ) {
                // First update privacy if exists
                if (!strcmp($record->private, 'yes')) {
                    $folder = !strcmp($record->documentType, 'templates') ? 'idocs_templates' : 'idocs_others';
                    $privacyQ = "UPDATE document_managment_users SET recordId = {$nodeCount} WHERE recordId = {$record->clearId} and model ='{$folder}';";
                    $privacyR = $this->db->query($privacyQ);
                    if (!$privacyR) {
                        $this->printLog('log', '*** Error ***  Privacy over folder "'.$record->name.'" [ID = '.$record->clearId.'] is not updated!', '<h3 style="font-family:Arial;color:red">', '</h3>');
                        ++$errorCount;
                    }
                }
                // Create Directories / Move Files from old physical structure:
                $nodePath = $this->webdavDocumentsDirectory.$nodeLineage;
                if (!strcmp($record->type, 'folder')) {
                    if (!is_dir($nodePath) && !@mkdir($nodePath, 0755, true)) {
                        $this->printLog('log', '*** Error ***  Creating directory failed '.$record->name.' [ID = '.$record->clearId.']', '<h3 style="font-family:Arial;color:red">', '</h3>');
                        $deleteQ = 'DELETE FROM webdav_tree_structure WHERE id = '.$nodeCount;
                        $this->db->query($deleteQ);
                        ++$errorCount;
                    } else {
                        ++$foldersCount;
                        $this->printLog('log', ' -- Migrating "'.$record->name.'" [ID = '.$record->clearId.']... ', '<p style="font-family:Arial;font-size:15px">', '<font color="green"><b>Done!</b></font></p>');
                        $mappingArray[$record->clearId]['id'] = $nodeCount;
                        $mappingArray[$record->clearId]['lineage'] = $nodeLineage;
                    }
                } else {
                    $oldFileSubPath = substr($record->fullPath, 1).$this->directorySeparator;
                    $oldfilePath = $this->oldDocumentsDirectory.'documents'.$this->directorySeparator.$record->documentType.$this->directorySeparator.$oldFileSubPath;
                    $oldfilePath .= strcmp(trim($record->versionName), '') ? $record->clearId.$versionPrefix.$this->directorySeparator.$record->versionName : $record->physicalName;
                    $result = @copy($oldfilePath, $nodePath);
                    if (!$result) {
                        $this->printLog('log', '*** Error ***  The following file is not moved physically: '.$record->name.' [ID = '.$record->clearId.']', '<h3 style="font-family:Arial;color:red">', '</h3>');
                        $deleteQ = 'DELETE FROM webdav_tree_structure WHERE id = '.$nodeCount;
                        $this->db->query($deleteQ);
                        ++$errorCount;
                    } else {
                        ++$filesCount;
                        $this->printLog('log', ' -- Migrating "'.$record->name.'" [ID = '.$record->clearId.']... ', '<p style="font-family:Arial;font-size:15px">', '<font color="green"><b>Done!</b></font></p>');
                        $mappingArray[$record->clearId]['id'] = $nodeCount;
                        $mappingArray[$record->clearId]['lineage'] = $nodeLineage;
                    }
                }
                ++$nodeCount;
            } else {
                $this->printLog('log', '*** Error ***  The following file is not migrated : '.$record->name.' [ID = '.$record->clearId.']', '<h3 style="font-family:Arial;color:red">', '</h3>');
                ++$errorCount;
            }
        }
        if ($errorCount > 0) {
            $this->printLog('display', '--- Warning --- some folders/files in "iDocs" were not migrated successfully. Please contact your support team', '<h3 style="font-family:Arial;color:red">', '</h3>');
        } else {
            $this->printLog('display', '--- Done --- All folders/files in "iDocs" are successfully migrated', '<p style="font-family:Arial;font-size:15px">', '</p>');
            $this->printLog('display', 'Deleteing old attachments for documents...', '<p style="font-family:Arial;font-size:15px">', '</p>');
            $this->_deleteDir($this->oldDocumentsDirectory.'documents/');
            $tablesToDelete = array('document_tree_levels', 'document_versions', 'documents');
            $viewsToDelete = array('documents_accessibility', 'document_tree_view', 'documents_users');
            $this->_cleanDB('iDocs', $tablesToDelete, $viewsToDelete);
        }

        return true;
    }

    public function billAttachmentsMigration(&$nodeCount, &$filesCount, &$foldersCount, &$errorCount)
    {
        $billsErrorCount = 0;
        $globalArr = array(
            /*
             * "modelID" => array(
             * 					"ModelLineage" => "1/6/7/..."
             * 					"BI-PY_1" => "1/6/7/...",
             * 					"BI-DOCS_1", "1/6/7/...",
             * 					"BI-PY_2" => "1/6/7/...",
             * 					...
             * 				) --> This should contain all parent directories under a certain BILL
             */
        );
        // Bill Payments:
        $billPaymentsQ = 'SELECT voucher_attachments.*, bh.voucher_header_id AS modelId, bp.id AS subModelId FROM voucher_attachments '
            .'LEFT JOIN bill_payments bp ON bp.voucher_header_id = voucher_attachments.voucher_header_id '
            .'LEFT JOIN bill_payment_bills bpb ON bpb.bill_payment_id = bp.id '
            .'LEFT JOIN bill_headers bh ON bh.id = bpb.bill_header_id '
            ."WHERE voucher_attachments.voucherType = 'BI-PY' "
            .'ORDER BY voucher_attachments.id ASC ';

        $billPaymentR = $this->db->query($billPaymentsQ);
        $billPaymentAttachments = $billPaymentR->result();
        $modelParentLineage = $this->directorySeparator.$this->models['BI']['lineage'];
        $modelParentId = $this->_getIdFromPath($modelParentLineage);
        $this->printLog('display', 'Bill Payments Attachments:', '<h4 style="font-family:Arial;color:blue">', '</h4>');
        foreach ($billPaymentAttachments as $record) {
            $modelId = (int) $record->modelId;
            if (!array_key_exists($modelId, $globalArr)) { // Should create a parent directory for all bill payments/bill docs attachments under each bill EX: BI_20
                $dirName = 'BI_'.$modelId;
                $checkModelQ = "SELECT webdav_tree_structure.lineage FROM webdav_tree_structure WHERE name = '{$dirName}'";
                $checkModelR = $this->db->query($checkModelQ);
                $modelPDirectory = $checkModelR->result();
                if (!empty($modelPDirectory)) {
                    $firstRecord = $modelPDirectory[0];
                    $globalArr[$modelId]['ModelLineage'] = $firstRecord->lineage;
                    $templateUserUpdated = true;
                } else {
                    $this->printLog('log', 'Creating parent directory for Bill Attachments  [ID = '.$record->modelId.'] ...', '<p style="font-family:Arial;font-size:15px">', '</p>');
                    $dirLineage = $modelParentLineage.$this->directorySeparator.$nodeCount;
                    $webdavQuery = 'INSERT INTO webdav_tree_structure (parentId, name, versionNb, lineage, type, private, document_status_id, physicalName, document_type_id, comments, modelName, modelId, createdOn, createdBy, modifiedOn, modifiedBy) '
                        ."VALUES ({$modelParentId}, '{$dirName}',	NULL, '{$dirLineage}', 'folder', NULL, NULL, '', NULL, '', 'BI', {$modelId}, CURRENT_TIMESTAMP,	0, CURRENT_TIMESTAMP, 0)";
                    $insertQ = $this->db->query($webdavQuery);
                    if ($insertQ) {
                        $dirPath = $this->webdavDocumentsDirectory.$dirLineage;
                        if (!is_dir($dirPath) && !@mkdir($dirPath, 0755, true)) {
                            $this->printLog('log', '*** Error ***  Parent Directory creation failed ['.$record->modelId.']', '<h3 style="font-family:Arial;color:red">', '</h3>');
                            $deleteQ = 'DELETE FROM webdav_tree_structure WHERE id = '.$nodeCount;
                            $this->db->query($deleteQ);
                            ++$billsErrorCount;
                        } else {
                            $globalArr[$modelId]['ModelLineage'] = $dirLineage;
                        }
                        ++$nodeCount;
                    } else {
                        $this->printLog('log', 'Parent Directory creation failed ['.$record->modelId.']', '<h3 style="font-family:Arial;color:red">', '</h3>');
                        ++$billsErrorCount;
                        continue;
                    }
                }
            }
            if (!array_key_exists('BI-PY_'.$modelId, $globalArr[$modelId])) {
                $dirName = 'BI-PY_'.$modelId;
                $checkModelQ = "SELECT webdav_tree_structure.lineage FROM webdav_tree_structure WHERE name = '{$dirName}'";
                $checkModelR = $this->db->query($checkModelQ);
                $modelPDirectory = $checkModelR->result();
                if (!empty($modelPDirectory)) {
                    $firstRecord = $modelPDirectory[0];
                    $globalArr[$modelId]['BI-PY_'.$modelId] = $firstRecord->lineage;
                    $templateUserUpdated = true;
                } else {
                    $this->printLog('log', 'Creating parent directory for Bill payments Attachments  [ID = '.$record->modelId.'] ...', '<p style="font-family:Arial;font-size:15px">', '</p>');
                    $dirLineage = $globalArr[$modelId]['ModelLineage'].$this->directorySeparator.$nodeCount;
                    $parentID = $this->_getIdFromPath($globalArr[$modelId]['ModelLineage']);
                    $webdavQuery = 'INSERT INTO webdav_tree_structure (parentId, name, versionNb, lineage, type, private, document_status_id, physicalName, document_type_id, comments, modelName, modelId, createdOn, createdBy, modifiedOn, modifiedBy) '
                        ."VALUES ({$parentID}, '{$dirName}',	NULL, '{$dirLineage}', 'folder', NULL, NULL, '{$nodeCount}', NULL, '', 'BI-PY', {$modelId}, CURRENT_TIMESTAMP,	0, CURRENT_TIMESTAMP, 0)";
                    $insertQ = $this->db->query($webdavQuery);
                    if ($insertQ) {
                        $dirPath = $this->webdavDocumentsDirectory.$dirLineage;
                        if (!is_dir($dirPath) && !@mkdir($dirPath, 0755, true)) {
                            $this->printLog('log', '*** Error ***  Parent Directory creation failed ['.$record->modelId.']', '<h3 style="font-family:Arial;color:red">', '</h3>');
                            $deleteQ = 'DELETE FROM webdav_tree_structure WHERE id = '.$nodeCount;
                            $this->db->query($deleteQ);
                            ++$billsErrorCount;
                        } else {
                            $globalArr[$modelId]['BI-PY_'.$modelId] = $dirLineage;
                        }
                        ++$nodeCount;
                    } else {
                        $this->printLog('log', 'Parent Directory creation failed ['.$record->modelId.']', '<h3 style="font-family:Arial;color:red">', '</h3>');
                        ++$billsErrorCount;
                        continue;
                    }
                }
            }
            $subModelId = (int) $record->subModelId;
            $dirName = 'BI-PY_'.$modelId.'_'.$subModelId;
            $checkModelQ = "SELECT webdav_tree_structure.lineage FROM webdav_tree_structure WHERE name = '{$dirName}'";
            $checkModelR = $this->db->query($checkModelQ);
            $modelPDirectory = $checkModelR->result();
            if (!empty($modelPDirectory)) {
                $firstRecord = $modelPDirectory[0];
                $dirLineage = $firstRecord->lineage;
                $fileParentIld = $this->_getIdFromPath($dirLineage);
                $name = str_replace("'", "''", $record->name);
                $fileLineage = $dirLineage.$this->directorySeparator.$nodeCount;
                $webdavQuery = 'INSERT INTO webdav_tree_structure (parentId, name, versionNb, lineage, type, private, document_status_id, physicalName, document_type_id, comments, modelName, modelId, createdOn, createdBy, modifiedOn, modifiedBy) '
                    ."VALUES ({$fileParentIld}, '{$name}', 1, '{$fileLineage}', 'file', NULL, NULL, '{$nodeCount}', NULL, '', NULL, NULL, '{$record->createdOn}', {$record->createdBy}, '{$record->modifiedOn}', {$record->modifiedBy})";
                $fileInserted = $this->db->query($webdavQuery);
                if ($fileInserted) { // So we should copy the physical file to the new location
                    $nodePath = $this->webdavDocumentsDirectory.$fileLineage;
                    $oldfilePath = $this->oldDocumentsDirectory.'money'.$this->directorySeparator.'bill_payments'.$this->directorySeparator.$record->organization_id.$this->directorySeparator.$record->voucher_header_id.$this->directorySeparator.$record->physicalName;
                    $result = @copy($oldfilePath, $nodePath);
                    if (!$result) {
                        $this->printLog('log', '*** Error ***  The following file is not moved physically: '.$record->name.' [ID = '.$record->id.']', '<h3 style="font-family:Arial;color:red">', '</h3>');
                        $deleteQ = 'DELETE FROM webdav_tree_structure WHERE id = '.$nodeCount;
                        $this->db->query($deleteQ);
                        ++$billsErrorCount;
                    } else {
                        ++$filesCount;
                        $this->printLog('log', ' -- Migrating "'.$record->name.'" [ID = '.$record->id.']... ', '<p style="font-family:Arial;font-size:15px">', '<font color="green"><b>Done!</b></font></p>');
                    }
                    ++$nodeCount;
                }
            } else {
                $this->printLog('log', 'Creating parent directory for Bill payment [ID = '.$record->subModelId.'] ...', '<p style="font-family:Arial;font-size:15px">', '</p>');
                $dirLineage = $globalArr[$modelId]['BI-PY_'.$modelId].$this->directorySeparator.$nodeCount;
                $parentID = $this->_getIdFromPath($globalArr[$modelId]['BI-PY_'.$modelId]);
                $webdavQuery = 'INSERT INTO webdav_tree_structure (parentId, name, versionNb, lineage, type, private, document_status_id, physicalName, document_type_id, comments, modelName, modelId, createdOn, createdBy, modifiedOn, modifiedBy) '
                    ."VALUES ({$parentID}, '{$dirName}', 1, '{$dirLineage}', 'folder', NULL, NULL, '{$nodeCount}', NULL, '', 'BI-PY_{$modelId}' , {$subModelId}, '{$record->createdOn}', {$record->createdBy}, '{$record->modifiedOn}', {$record->modifiedBy})";
                $insertQ = $this->db->query($webdavQuery);
                if ($insertQ) {
                    $dirPath = $this->webdavDocumentsDirectory.$dirLineage;
                    if (!is_dir($dirPath) && !@mkdir($dirPath, 0755, true)) {
                        $this->printLog('log', '*** Error ***  Parent Directory creation failed ['.$record->modelId.']', '<h3 style="font-family:Arial;color:red">', '</h3>');
                        $deleteQ = 'DELETE FROM webdav_tree_structure WHERE id = '.$nodeCount;
                        $this->db->query($deleteQ);
                        ++$billsErrorCount;
                        ++$nodeCount;
                    } else {
                        // Now add attachment record to DB and copy physical file to the new location
                        // Since we only have one attachment per bill payment so we can directly copy the physical path and add a new DB record without storing parent's data in the array
                        $fileParentIld = $nodeCount;
                        ++$nodeCount;
                        $name = str_replace("'", "''", $record->name);
                        $fileLineage = $dirLineage.$this->directorySeparator.$nodeCount;
                        $webdavQuery = 'INSERT INTO webdav_tree_structure (parentId, name, versionNb, lineage, type, private, document_status_id, physicalName, document_type_id, comments, modelName, modelId, createdOn, createdBy, modifiedOn, modifiedBy) '
                            ."VALUES ({$fileParentIld}, '{$name}', NULL, '{$fileLineage}', 'file', NULL, NULL, '{$nodeCount}', NULL, '', NULL, NULL, '{$record->createdOn}', {$record->createdBy}, '{$record->modifiedOn}', {$record->modifiedBy})";
                        $fileInserted = $this->db->query($webdavQuery);
                        if ($fileInserted) { // So we should copy the physical file to the new location
                            $nodePath = $this->webdavDocumentsDirectory.$fileLineage;
                            $oldfilePath = $this->oldDocumentsDirectory.'money'.$this->directorySeparator.'bill_payments'.$this->directorySeparator.$record->organization_id.$this->directorySeparator.$record->voucher_header_id.$this->directorySeparator.$record->physicalName;
                            $result = @copy($oldfilePath, $nodePath);
                            if (!$result) {
                                $this->printLog('log', '*** Error ***  The following file is not moved physically: '.$record->name.' [ID = '.$record->id.']', '<h3 style="font-family:Arial;color:red">', '</h3>');
                                $deleteQ = 'DELETE FROM webdav_tree_structure WHERE id = '.$nodeCount;
                                $this->db->query($deleteQ);
                                ++$billsErrorCount;
                            } else {
                                ++$filesCount;
                                $this->printLog('log', ' -- Migrating "'.$record->name.'" [ID = '.$record->id.']... ', '<p style="font-family:Arial;font-size:15px">', '<font color="green"><b>Done!</b></font></p>');
                            }
                            ++$nodeCount;
                        }
                    }
                } else {
                    $this->printLog('log', 'Parent Directory creation failed [Bill Payment ID = '.$record->subModelId.']', '<h3 style="font-family:Arial;color:red">', '</h3>');
                    ++$billsErrorCount;
                    continue;
                }
            }
        }
        // Bill Docs:
        $billDocsQ = 'SELECT voucher_attachments.*, voucher_attachments.voucher_header_id AS modelId FROM voucher_attachments '
            ."WHERE voucher_attachments.voucherType = 'BI' "
            .'ORDER BY voucher_attachments.id ASC ';

        $billDocs = $this->db->query($billDocsQ);
        $billDocsResult = $billDocs->result();
        $this->printLog('display', 'Bill Related Docs:', '<h4 style="font-family:Arial;color:blue">', '</h4>');
        foreach ($billDocsResult as $record) {
            $modelId = (int) $record->modelId;
            if (!array_key_exists($modelId, $globalArr)) { // Should create a parent directory for all bill payments/bill docs attachments under each bill EX: BI_20
                $dirName = 'BI_'.$modelId;
                $checkModelQ = "SELECT webdav_tree_structure.lineage FROM webdav_tree_structure WHERE name = '{$dirName}'";
                $checkModelR = $this->db->query($checkModelQ);
                $modelPDirectory = $checkModelR->result();
                if (!empty($modelPDirectory)) {
                    $firstRecord = $modelPDirectory[0];
                    $globalArr[$modelId]['ModelLineage'] = $firstRecord->lineage;
                } else {
                    $this->printLog('log', 'Creating parent directory for Bill Attachments  [ID = '.$record->modelId.'] ...', '<p style="font-family:Arial;font-size:15px">', '</p>');
                    $dirLineage = $modelParentLineage.$this->directorySeparator.$nodeCount;
                    $webdavQuery = 'INSERT INTO webdav_tree_structure (parentId, name, versionNb, lineage, type, private, document_status_id, physicalName, document_type_id, comments, modelName, modelId, createdOn, createdBy, modifiedOn, modifiedBy) '
                        ."VALUES ({$modelParentId}, '{$dirName}',	NULL, '{$dirLineage}', 'folder', NULL, NULL, '', NULL, '', 'BI', {$modelId}, CURRENT_TIMESTAMP,	0, CURRENT_TIMESTAMP, 0)";
                    $insertQ = $this->db->query($webdavQuery);
                    if ($insertQ) {
                        $dirPath = $this->webdavDocumentsDirectory.$dirLineage;
                        if (!is_dir($dirPath) && !@mkdir($dirPath, 0755, true)) {
                            $this->printLog('log', '*** Error ***  Parent Directory creation failed ['.$record->modelId.']', '<h3 style="font-family:Arial;color:red">', '</h3>');
                            $deleteQ = 'DELETE FROM webdav_tree_structure WHERE id = '.$nodeCount;
                            $this->db->query($deleteQ);
                            ++$billsErrorCount;
                        } else {
                            $globalArr[$modelId]['ModelLineage'] = $dirLineage;
                        }
                        ++$nodeCount;
                    } else {
                        $this->printLog('log', 'Parent Directory creation failed ['.$record->modelId.']', '<h3 style="font-family:Arial;color:red">', '</h3>');
                        ++$billsErrorCount;
                        continue;
                    }
                }
            }
            if (!array_key_exists('BI-DOCS_'.$modelId, $globalArr[$modelId])) {
                $dirName = 'BI-DOCS_'.$modelId;
                $checkModelQ = "SELECT webdav_tree_structure.lineage FROM webdav_tree_structure WHERE name = '{$dirName}'";
                $checkModelR = $this->db->query($checkModelQ);
                $modelPDirectory = $checkModelR->result();
                if (!empty($modelPDirectory)) {
                    $firstRecord = $modelPDirectory[0];
                    $globalArr[$modelId]['BI-DOCS_'.$modelId] = $firstRecord->lineage;
                } else {
                    $this->printLog('log', 'Creating parent directory for Bill Documents [ID = '.$record->modelId.'] ...', '<p style="font-family:Arial;font-size:15px">', '</p>');
                    $dirLineage = $globalArr[$modelId]['ModelLineage'].$this->directorySeparator.$nodeCount;
                    $parentID = $this->_getIdFromPath($globalArr[$modelId]['ModelLineage']);
                    $webdavQuery = 'INSERT INTO webdav_tree_structure (parentId, name, versionNb, lineage, type, private, document_status_id, physicalName, document_type_id, comments, modelName, modelId, createdOn, createdBy, modifiedOn, modifiedBy) '
                        ."VALUES ({$parentID}, '{$dirName}',	NULL, '{$dirLineage}', 'folder', NULL, NULL, '{$nodeCount}', NULL, '', 'BI-DOCS', {$modelId}, '{$record->createdOn}', {$record->createdBy}, '{$record->modifiedOn}', {$record->modifiedBy})";
                    $insertQ = $this->db->query($webdavQuery);
                    if ($insertQ) {
                        $dirPath = $this->webdavDocumentsDirectory.$dirLineage;
                        if (!is_dir($dirPath) && !@mkdir($dirPath, 0755, true)) {
                            $this->printLog('log', '*** Error ***  Parent Directory creation failed ['.$record->modelId.']', '<h3 style="font-family:Arial;color:red">', '</h3>');
                            $deleteQ = 'DELETE FROM webdav_tree_structure WHERE id = '.$nodeCount;
                            $this->db->query($deleteQ);
                            ++$billsErrorCount;
                        } else {
                            $globalArr[$modelId]['BI-DOCS_'.$modelId] = $dirLineage;
                        }
                        ++$nodeCount;
                    } else {
                        $this->printLog('log', 'Parent Directory creation failed ['.$record->modelId.']', '<h3 style="font-family:Arial;color:red">', '</h3>');
                        ++$billsErrorCount;
                        continue;
                    }
                }
            }
            $parentLineage = $globalArr[$modelId]['BI-DOCS_'.$modelId];
            $fileParentIld = $this->_getIdFromPath($parentLineage);
            $fileLineage = $parentLineage.$this->directorySeparator.$nodeCount;
            $name = str_replace("'", "''", $record->name);
            $webdavQuery = 'INSERT INTO webdav_tree_structure (parentId, name, versionNb, lineage, type, private, document_status_id, physicalName, document_type_id, comments, modelName, modelId, createdOn, createdBy, modifiedOn, modifiedBy) '
                ."VALUES ({$fileParentIld}, '{$name}', 1, '{$fileLineage}', 'file', NULL, NULL, '{$nodeCount}', NULL, '', NULL, NULL, '{$record->createdOn}', {$record->createdBy}, '{$record->modifiedOn}', {$record->modifiedBy})";
            $fileInserted = $this->db->query($webdavQuery);
            if ($fileInserted) {
                $nodePath = $this->webdavDocumentsDirectory.$fileLineage;
                $oldfilePath = $this->oldDocumentsDirectory.'money'.$this->directorySeparator.'bills_related_documents'.$this->directorySeparator.$record->organization_id.$this->directorySeparator.$record->voucher_header_id.$this->directorySeparator.$record->physicalName;
                $result = @copy($oldfilePath, $nodePath);
                if (!$result) {
                    $this->printLog('log', '*** Error ***  The following file is not moved physically: '.$record->name.' [ID = '.$record->id.']', '<h3 style="font-family:Arial;color:red">', '</h3>');
                    $deleteQ = 'DELETE FROM webdav_tree_structure WHERE id = '.$nodeCount;
                    $this->db->query($deleteQ);
                    ++$billsErrorCount;
                } else {
                    ++$filesCount;
                    $this->printLog('log', ' -- Migrating Bill Docs "'.$record->name.'" [ID = '.$record->id.']... ', '<p style="font-family:Arial;font-size:15px">', '<font color="green"><b>Done!</b></font></p>');
                }
                ++$nodeCount;
            }
        }
        if ($billsErrorCount > 0) {
            $this->printLog('display', '--- Warning --- some files for "Bills" were not migrated successfully. Please contact your support team', '<h3 style="font-family:Arial;color:red">', '</h3>');
        } else {
            $this->printLog('display', '--- Done --- All files in "Bills" model are migrated successfully', '<p style="font-family:Arial;font-size:15px">', '</p>');
            $this->printLog('display', 'Deleteing old attachments for Bills...', '<p style="font-family:Arial;font-size:15px">', '</p>');
            $this->_deleteDir($this->oldDocumentsDirectory.'money'.$this->directorySeparator.'bill_payments/');
            $this->_deleteDir($this->oldDocumentsDirectory.'money'.$this->directorySeparator.'bills_related_documents/');
        }
        $errorCount += $billsErrorCount;

        return true;
    }

    public function expensesAttachmentsMigration(&$nodeCount, &$filesCount, &$foldersCount, &$errorCount)
    {
        $expensesErrorCount = 0;
        $expenseAttQ = "SELECT voucher_attachments.*, voucher_attachments.voucher_header_id AS modelId FROM voucher_attachments WHERE voucherType = 'EXP' ORDER BY voucher_attachments.id ASC ";
        $expenseAttR = $this->db->query($expenseAttQ);
        $expenseAttachments = $expenseAttR->result();

        $modelParentLineage = $this->directorySeparator.$this->models['EXP']['lineage'];
        $modelParentId = $this->_getIdFromPath($modelParentLineage);

        $expenseArr = array();
        foreach ($expenseAttachments as $record) {
            $modelId = (int) $record->voucher_header_id;
            if (!array_key_exists($modelId, $expenseArr)) {
                // Create main directory [EX: EXP_2]
                $this->printLog('log', 'Creating parent directory for Expense Attachments  [ID = '.$record->voucher_header_id.'] ...', '<p style="font-family:Arial;font-size:15px">', '</p>');
                $dirLineage = $modelParentLineage.$this->directorySeparator.$nodeCount;
                $dirName = 'EXP_'.$modelId;
                $webdavQuery = 'INSERT INTO webdav_tree_structure (parentId, name, versionNb, lineage, type, private, document_status_id, physicalName, document_type_id, comments, modelName, modelId, createdOn, createdBy, modifiedOn, modifiedBy) '
                    ."VALUES ({$modelParentId}, '{$dirName}',	NULL, '{$dirLineage}', 'folder', NULL, NULL, '', NULL, '', 'EXP', {$modelId}, CURRENT_TIMESTAMP,	0, CURRENT_TIMESTAMP, 0)";
                $insertQ = $this->db->query($webdavQuery);
                if ($insertQ) {
                    $dirPath = $this->webdavDocumentsDirectory.$dirLineage;
                    if (!is_dir($dirPath) && !@mkdir($dirPath, 0755, true)) {
                        $this->printLog('log', '*** Error ***  Parent Directory creation failed [EXP_'.$record->voucher_header_id.']', '<h3 style="font-family:Arial;color:red">', '</h3>');
                        $deleteQ = 'DELETE FROM webdav_tree_structure WHERE id = '.$nodeCount;
                        $this->db->query($deleteQ);
                        ++$nodeCount;
                        ++$expensesErrorCount;
                    } else {
                        // Create sub-directory [Ex: EXP-DOCS_2]
                        $this->printLog('log', 'Creating directory for Expense documents  [ID = '.$record->voucher_header_id.'] ...', '<p style="font-family:Arial;font-size:15px">', '</p>');
                        $mainModelId = $nodeCount;
                        ++$nodeCount;
                        $dName = 'EXP-DOCS_'.$modelId;
                        $dLineage = $dirLineage.$this->directorySeparator.$nodeCount;
                        $webdavQuery = 'INSERT INTO webdav_tree_structure (parentId, name, versionNb, lineage, type, private, document_status_id, physicalName, document_type_id, comments, modelName, modelId, createdOn, createdBy, modifiedOn, modifiedBy) '
                            ."VALUES ({$mainModelId}, '{$dName}',	NULL, '{$dLineage}', 'folder', NULL, NULL, '', NULL, '', 'EXP-DOCS', {$modelId}, CURRENT_TIMESTAMP,	0, CURRENT_TIMESTAMP, 0)";
                        $subFolderR = $this->db->query($webdavQuery);
                        if ($subFolderR) {
                            $dirPath = $this->webdavDocumentsDirectory.$dLineage;
                            if (!is_dir($dirPath) && !@mkdir($dirPath, 0755, true)) {
                                $this->printLog('log', '*** Error ***  Parent Directory creation failed [EXP-DOCS-'.$record->voucher_header_id.']', '<h3 style="font-family:Arial;color:red">', '</h3>');
                                $deleteQ = 'DELETE FROM webdav_tree_structure WHERE id = '.$nodeCount;
                                $this->db->query($deleteQ);
                                ++$expensesErrorCount;
                            } else {
                                $expenseArr[$modelId] = $dLineage;
                            }
                            ++$nodeCount;
                        } else {
                            ++$expensesErrorCount;
                            $this->printLog('log', '*** Error ***  Parent Directory creation failed [EXP-DOCS-'.$record->voucher_header_id.']', '<h3 style="font-family:Arial;color:red">', '</h3>');
                        }
                    }
                } else {
                    ++$expensesErrorCount;
                    $this->printLog('log', '*** Error ***  Parent Directory creation failed [EXP_'.$record->voucher_header_id.']', '<h3 style="font-family:Arial;color:red">', '</h3>');
                    continue;
                }
            }

            $parentLineage = $expenseArr[$modelId];
            $fileParentIld = $this->_getIdFromPath($parentLineage);
            $fileLineage = $parentLineage.$this->directorySeparator.$nodeCount;
            $name = str_replace("'", "''", $record->name);
            $createdBy = strcmp($record->createdBy, '') ? $record->createdBy : $record->modifiedBy;
            $createdOn = strcmp($record->createdOn, '') ? $record->createdOn : '';
            $webdavQuery = 'INSERT INTO webdav_tree_structure (parentId, name, versionNb, lineage, type, private, document_status_id, physicalName, document_type_id, comments, modelName, modelId, createdOn, createdBy, modifiedOn, modifiedBy) '
                ."VALUES ({$fileParentIld}, '{$name}', 1, '{$fileLineage}', 'file', NULL, NULL, '{$nodeCount}', NULL, '', NULL, NULL, '{$createdOn}', {$createdBy}, '{$record->modifiedOn}', {$record->modifiedBy})";
            $fileInserted = $this->db->query($webdavQuery);
            if ($fileInserted) {
                $nodePath = $this->webdavDocumentsDirectory.$fileLineage;
                $oldfilePath = $this->oldDocumentsDirectory.'money'.$this->directorySeparator.'expenses_related_documents'.$this->directorySeparator.$record->organization_id.$this->directorySeparator.$record->voucher_header_id.$this->directorySeparator.$record->physicalName;
                $result = @copy($oldfilePath, $nodePath);
                if (!$result) {
                    ++$expensesErrorCount;
                    $this->printLog('log', '*** Error ***  The following file is not moved physically: '.$record->name.' [ID = '.$record->id.']', '<h3 style="font-family:Arial;color:red">', '</h3>');
                    $deleteQ = 'DELETE FROM webdav_tree_structure WHERE id = '.$nodeCount;
                    $this->db->query($deleteQ);
                } else {
                    ++$filesCount;
                    $this->printLog('log', ' -- Migrating Expense Document "'.$record->name.'" [ID = '.$record->id.']... ', '<p style="font-family:Arial;font-size:15px">', '<font color="green"><b>Done!</b></font></p>');
                }
                ++$nodeCount;
            } else {
                ++$expensesErrorCount;
                $this->printLog('log', '*** Error ***  The following file is not migrated: '.$record->name.' [ID = '.$record->id.']', '<h3 style="font-family:Arial;color:red">', '</h3>');
            }
        }
        if ($expensesErrorCount > 0) {
            $this->printLog('display', '--- Warning --- some files in "Expenses" were not migrated successfully. Please contact your support team', '<h3 style="font-family:Arial;color:red">', '</h3>');
        } else {
            $this->printLog('display', '--- Done --- All files in "Expenses" model are successfully migrated', '<p style="font-family:Arial;font-size:15px">', '</p>');
            $this->printLog('display', 'Deleteing old attachments for Expenses...', '<p style="font-family:Arial;font-size:15px">', '</p>');
            $this->_deleteDir($this->oldDocumentsDirectory.'money'.$this->directorySeparator.'expenses_related_documents/');
        }
        $errorCount += $expensesErrorCount;

        return true;
    }

    public function invoiceAttachmentsMigration(&$nodeCount, &$filesCount, &$foldersCount, &$errorCount)
    {
        $invoicesErrorCount = 0;
        $globalArr = array();
        // Invoice Payments:
        $invoicePaymentsQ = "SELECT voucher_attachments.*, bh.voucher_header_id AS modelId, ip.id AS subModelId FROM voucher_attachments LEFT JOIN invoice_payments ip ON ip.voucher_header_id = voucher_attachments.voucher_header_id LEFT JOIN invoice_payment_invoices ipi ON ipi.invoice_payment_id = ip.id LEFT JOIN invoice_headers bh ON bh.id = ipi.invoice_header_id WHERE voucher_attachments.voucherType = 'INV-PY' ORDER BY voucher_attachments.id ASC ";
        $invoicePaymentR = $this->db->query($invoicePaymentsQ);
        $invoicePaymentAttachments = $invoicePaymentR->result();
        $modelParentLineage = $this->directorySeparator.$this->models['INV']['lineage'];
        $modelParentId = $this->_getIdFromPath($modelParentLineage);
        $this->printLog('display', 'Invoice Payments Attachments:', '<h4 style="font-family:Arial;color:blue">', '</h4>');
        foreach ($invoicePaymentAttachments as $record) {
            $modelId = (int) $record->modelId;
            if (!array_key_exists($modelId, $globalArr)) {
                $this->printLog('log', 'Creating parent directory for Invoice Attachments  [ID = '.$record->modelId.'] ...', '<p style="font-family:Arial;font-size:15px">', '</p>');
                $dirLineage = $modelParentLineage.$this->directorySeparator.$nodeCount;
                $dirName = 'INV_'.$modelId;
                $webdavQuery = 'INSERT INTO webdav_tree_structure (parentId, name, versionNb, lineage, type, private, document_status_id, physicalName, document_type_id, comments, modelName, modelId, createdOn, createdBy, modifiedOn, modifiedBy) '
                    ."VALUES ({$modelParentId}, '{$dirName}',	NULL, '{$dirLineage}', 'folder', NULL, NULL, '', NULL, '', 'INV', {$modelId}, CURRENT_TIMESTAMP,	0, CURRENT_TIMESTAMP, 0)";
                $insertQ = $this->db->query($webdavQuery);
                if ($insertQ) {
                    $dirPath = $this->webdavDocumentsDirectory.$dirLineage;
                    if (!is_dir($dirPath) && !@mkdir($dirPath, 0755, true)) {
                        ++$invoicesErrorCount;
                        $this->printLog('log', '*** Error ***  Parent Directory creation failed ['.$record->modelId.']', '<h3 style="font-family:Arial;color:red">', '</h3>');
                        $deleteQ = 'DELETE FROM webdav_tree_structure WHERE id = '.$nodeCount;
                        $this->db->query($deleteQ);
                    } else {
                        $globalArr[$modelId]['ModelLineage'] = $dirLineage;
                    }
                    ++$nodeCount;
                } else {
                    ++$invoicesErrorCount;
                    $this->printLog('log', 'Parent Directory creation failed ['.$record->modelId.']', '<h3 style="font-family:Arial;color:red">', '</h3>');
                    continue;
                }
            }
            if (!array_key_exists('INV-PY_'.$modelId, $globalArr[$modelId])) {
                $this->printLog('log', 'Creating parent directory for Invoice payments Attachments  [ID = '.$record->modelId.'] ...', '<p style="font-family:Arial;font-size:15px">', '</p>');
                $dirLineage = $globalArr[$modelId]['ModelLineage'].$this->directorySeparator.$nodeCount;
                $parentID = $this->_getIdFromPath($globalArr[$modelId]['ModelLineage']);
                $dirName = 'INV-PY_'.$modelId;
                $webdavQuery = 'INSERT INTO webdav_tree_structure (parentId, name, versionNb, lineage, type, private, document_status_id, physicalName, document_type_id, comments, modelName, modelId, createdOn, createdBy, modifiedOn, modifiedBy) '
                    ."VALUES ({$parentID}, '{$dirName}',	NULL, '{$dirLineage}', 'folder', NULL, NULL, '{$nodeCount}', NULL, '', 'INV-PY', {$modelId}, CURRENT_TIMESTAMP,	0, CURRENT_TIMESTAMP, 0)";
                $insertQ = $this->db->query($webdavQuery);
                if ($insertQ) {
                    $dirPath = $this->webdavDocumentsDirectory.$dirLineage;
                    if (!is_dir($dirPath) && !@mkdir($dirPath, 0755, true)) {
                        ++$invoicesErrorCount;
                        $this->printLog('log', '*** Error ***  Parent Directory creation failed ['.$record->modelId.']', '<h3 style="font-family:Arial;color:red">', '</h3>');
                        $deleteQ = 'DELETE FROM webdav_tree_structure WHERE id = '.$nodeCount;
                        $this->db->query($deleteQ);
                    } else {
                        $globalArr[$modelId]['INV-PY_'.$modelId] = $dirLineage;
                    }
                    ++$nodeCount;
                } else {
                    ++$invoicesErrorCount;
                    $this->printLog('log', 'Parent Directory creation failed ['.$record->modelId.']', '<h3 style="font-family:Arial;color:red">', '</h3>');
                    continue;
                }
            }
            $this->printLog('log', 'Creating parent directory for Invoice payment [ID = '.$record->subModelId.'] ...', '<p style="font-family:Arial;font-size:15px">', '</p>');
            $dirLineage = $globalArr[$modelId]['INV-PY_'.$modelId].$this->directorySeparator.$nodeCount;
            $parentID = $this->_getIdFromPath($globalArr[$modelId]['INV-PY_'.$modelId]);
            $subModelId = (int) $record->subModelId;
            $dirName = 'INV-PY_'.$modelId.'_'.$subModelId;
            $webdavQuery = 'INSERT INTO webdav_tree_structure (parentId, name, versionNb, lineage, type, private, document_status_id, physicalName, document_type_id, comments, modelName, modelId, createdOn, createdBy, modifiedOn, modifiedBy) '
                ."VALUES ({$parentID}, '{$dirName}',	NULL, '{$dirLineage}', 'folder', NULL, NULL, '{$nodeCount}', NULL, '', 'INV-PY_{$modelId}' , {$subModelId}, '{$record->createdOn}', {$record->createdBy}, '{$record->modifiedOn}', {$record->modifiedBy})";
            $insertQ = $this->db->query($webdavQuery);
            if ($insertQ) {
                $dirPath = $this->webdavDocumentsDirectory.$dirLineage;
                if (!is_dir($dirPath) && !@mkdir($dirPath, 0755, true)) {
                    ++$invoicesErrorCount;
                    $this->printLog('log', '*** Error ***  Parent Directory creation failed ['.$record->modelId.']', '<h3 style="font-family:Arial;color:red">', '</h3>');
                    $deleteQ = 'DELETE FROM webdav_tree_structure WHERE id = '.$nodeCount;
                    $this->db->query($deleteQ);
                    ++$nodeCount;
                } else {
                    $fileParentIld = $nodeCount;
                    ++$nodeCount;
                    $fileLineage = $dirLineage.$this->directorySeparator.$nodeCount;
                    $name = str_replace("'", "''", $record->name);
                    $webdavQuery = 'INSERT INTO webdav_tree_structure (parentId, name, versionNb, lineage, type, private, document_status_id, physicalName, document_type_id, comments, modelName, modelId, createdOn, createdBy, modifiedOn, modifiedBy) '
                        ."VALUES ({$fileParentIld}, '{$name}', 1, '{$fileLineage}', 'file', NULL, NULL, '{$nodeCount}', NULL, '', NULL, NULL, '{$record->createdOn}', {$record->createdBy}, '{$record->modifiedOn}', {$record->modifiedBy})";
                    $fileInserted = $this->db->query($webdavQuery);
                    if ($fileInserted) {
                        $nodePath = $this->webdavDocumentsDirectory.$fileLineage;
                        $oldfilePath = $this->oldDocumentsDirectory.'money'.$this->directorySeparator.'invoice_payments'.$this->directorySeparator.$record->organization_id.$this->directorySeparator.$record->voucher_header_id.$this->directorySeparator.$record->physicalName;
                        $result = @copy($oldfilePath, $nodePath);
                        if (!$result) {
                            ++$invoicesErrorCount;
                            $this->printLog('log', '*** Error ***  The following file is not moved physically: '.$record->name.' [ID = '.$record->id.']', '<h3 style="font-family:Arial;color:red">', '</h3>');
                            $deleteQ = 'DELETE FROM webdav_tree_structure WHERE id = '.$nodeCount;
                            $this->db->query($deleteQ);
                        } else {
                            ++$filesCount;
                            $this->printLog('log', ' -- Migrating "'.$record->name.'" [ID = '.$record->id.']... ', '<p style="font-family:Arial;font-size:15px">', '<font color="green"><b>Done!</b></font></p>');
                        }
                        ++$nodeCount;
                    }
                }
            } else {
                ++$invoicesErrorCount;
                $this->printLog('log', 'Parent Directory creation failed [Invoice Payment ID = '.$record->subModelId.']', '<h3 style="font-family:Arial;color:red">', '</h3>');
                continue;
            }
        }
        // Invoice Docs:
        $invoiceDocsQ = "SELECT voucher_attachments.*, voucher_attachments.voucher_header_id AS modelId FROM voucher_attachments WHERE voucher_attachments.voucherType = 'INV' ORDER BY voucher_attachments.id ASC ";
        $invoiceDocs = $this->db->query($invoiceDocsQ);
        $invoiceDocsResult = $invoiceDocs->result();
        $this->printLog('log', 'Invoice Related Docs:', '<h4 style="font-family:Arial;color:blue">', '</h4>');
        foreach ($invoiceDocsResult as $record) {
            $modelId = (int) $record->modelId;
            if (!array_key_exists($modelId, $globalArr)) {
                $this->printLog('log', 'Creating parent directory for Invoice Attachments  [ID = '.$record->modelId.'] ...', '<p style="font-family:Arial;font-size:15px">', '</p>');
                $dirLineage = $modelParentLineage.$this->directorySeparator.$nodeCount;
                $dirName = 'INV_'.$modelId;
                $webdavQuery = 'INSERT INTO webdav_tree_structure (parentId, name, versionNb, lineage, type, private, document_status_id, physicalName, document_type_id, comments, modelName, modelId, createdOn, createdBy, modifiedOn, modifiedBy) '
                    ."VALUES ({$modelParentId}, '{$dirName}',	NULL, '{$dirLineage}', 'folder', NULL, NULL, '', NULL, '', 'INV', {$modelId}, CURRENT_TIMESTAMP,	0, CURRENT_TIMESTAMP, 0)";
                $insertQ = $this->db->query($webdavQuery);
                if ($insertQ) {
                    $dirPath = $this->webdavDocumentsDirectory.$dirLineage;
                    if (!is_dir($dirPath) && !@mkdir($dirPath, 0755, true)) {
                        ++$invoicesErrorCount;
                        $this->printLog('log', '*** Error ***  Parent Directory creation failed ['.$record->modelId.']', '<h3 style="font-family:Arial;color:red">', '</h3>');
                        $deleteQ = 'DELETE FROM webdav_tree_structure WHERE id = '.$nodeCount;
                        $this->db->query($deleteQ);
                    } else {
                        $globalArr[$modelId]['ModelLineage'] = $dirLineage;
                    }
                    ++$nodeCount;
                } else {
                    ++$invoicesErrorCount;
                    $this->printLog('log', 'Parent Directory creation failed ['.$record->modelId.']', '<h3 style="font-family:Arial;color:red">', '</h3>');
                    continue;
                }
            }
            if (!array_key_exists('INV-DOCS_'.$modelId, $globalArr[$modelId])) {
                $this->printLog('log', 'Creating parent directory for Invoice Documents [ID = '.$record->modelId.'] ...', '<p style="font-family:Arial;font-size:15px">', '</p>');
                $dirLineage = $globalArr[$modelId]['ModelLineage'].$this->directorySeparator.$nodeCount;
                $parentID = $this->_getIdFromPath($globalArr[$modelId]['ModelLineage']);
                $dirName = 'INV-DOCS_'.$modelId;
                $webdavQuery = 'INSERT INTO webdav_tree_structure (parentId, name, versionNb, lineage, type, private, document_status_id, physicalName, document_type_id, comments, modelName, modelId, createdOn, createdBy, modifiedOn, modifiedBy) '
                    ."VALUES ({$parentID}, '{$dirName}',	NULL, '{$dirLineage}', 'folder', NULL, NULL, '{$nodeCount}', NULL, '', 'INV-DOCS', {$modelId}, '{$record->createdOn}', {$record->createdBy}, '{$record->modifiedOn}', {$record->modifiedBy})";
                $insertQ = $this->db->query($webdavQuery);
                if ($insertQ) {
                    $dirPath = $this->webdavDocumentsDirectory.$dirLineage;
                    if (!is_dir($dirPath) && !@mkdir($dirPath, 0755, true)) {
                        ++$invoicesErrorCount;
                        $this->printLog('log', '*** Error ***  Parent Directory creation failed ['.$record->modelId.']', '<h3 style="font-family:Arial;color:red">', '</h3>');
                        $deleteQ = 'DELETE FROM webdav_tree_structure WHERE id = '.$nodeCount;
                        $this->db->query($deleteQ);
                    } else {
                        $globalArr[$modelId]['INV-DOCS_'.$modelId] = $dirLineage;
                    }
                    ++$nodeCount;
                } else {
                    ++$invoicesErrorCount;
                    $this->printLog('log', 'Parent Directory creation failed ['.$record->modelId.']', '<h3 style="font-family:Arial;color:red">', '</h3>');
                    continue;
                }
            }
            $parentLineage = $globalArr[$modelId]['INV-DOCS_'.$modelId];
            $fileParentIld = $this->_getIdFromPath($parentLineage);
            $fileLineage = $parentLineage.$this->directorySeparator.$nodeCount;
            $name = str_replace("'", "''", $record->name);
            $webdavQuery = 'INSERT INTO webdav_tree_structure (parentId, name, versionNb, lineage, type, private, document_status_id, physicalName, document_type_id, comments, modelName, modelId, createdOn, createdBy, modifiedOn, modifiedBy) '
                ."VALUES ({$fileParentIld}, '{$name}', 1, '{$fileLineage}', 'file', NULL, NULL, '{$nodeCount}', NULL, '', NULL, NULL, '{$record->createdOn}', {$record->createdBy}, '{$record->modifiedOn}', {$record->modifiedBy})";
            $fileInserted = $this->db->query($webdavQuery);
            if ($fileInserted) {
                $nodePath = $this->webdavDocumentsDirectory.$fileLineage;
                $oldfilePath = $this->oldDocumentsDirectory.'money'.$this->directorySeparator.'invoices_related_documents'.$this->directorySeparator.$record->organization_id.$this->directorySeparator.$record->voucher_header_id.$this->directorySeparator.$record->physicalName;
                $result = @copy($oldfilePath, $nodePath);
                if (!$result) {
                    ++$invoicesErrorCount;
                    $this->printLog('log', '*** Error ***  The following file is not moved physically: '.$record->name.' [ID = '.$record->id.']', '<h3 style="font-family:Arial;color:red">', '</h3>');
                    $deleteQ = 'DELETE FROM webdav_tree_structure WHERE id = '.$nodeCount;
                    $this->db->query($deleteQ);
                } else {
                    ++$filesCount;
                    $this->printLog('log', ' -- Migrating Invoice Document "'.$record->name.'" [ID = '.$record->id.']... ', '<p style="font-family:Arial;font-size:15px">', '<font color="green"><b>Done!</b></font></p>');
                }
                ++$nodeCount;
            }
        }
        if ($invoicesErrorCount > 0) {
            $this->printLog('display', '--- Warning --- some files in "Invoices" were not migrated successfully. Please contact your support team', '<h3 style="font-family:Arial;color:red">', '</h3>');
        } else {
            $this->printLog('display', '--- Done --- All files in "Invoices" model are migrated successfully', '<p style="font-family:Arial;font-size:15px">', '</p>');
            $this->printLog('display', 'Deleteing old attachments for Invoices...', '<p style="font-family:Arial;font-size:15px">', '</p>');
            $this->_deleteDir($this->oldDocumentsDirectory.'money'.$this->directorySeparator.'invoice_payments/');
            $this->_deleteDir($this->oldDocumentsDirectory.'money'.$this->directorySeparator.'invoices_related_documents/');
        }
        $errorCount += $invoicesErrorCount;
        if ($errorCount == 0) {
            $tablesToDelete = array('voucher_attachments');
            $this->_cleanDB('Bills, Expenses and Invoices', $tablesToDelete);
        }
        // Drop column model from document_management_users table:
        switch ($this->db->dbdriver) {
            case 'mysqli':
                $query = 'ALTER TABLE document_managment_users DROP COLUMN model';
                break;
            case 'sqlsrv':
                $query = "DECLARE @defaultname VARCHAR(255)
                DECLARE @executesql VARCHAR(1000)

                SELECT @defaultname = dc.name
                        FROM sys.check_constraints dc
                            INNER JOIN sys.columns sc
                            ON dc.parent_object_id = sc.object_id
                            AND dc.parent_column_id = sc.column_id
                            WHERE OBJECT_NAME (parent_object_id) = 'document_managment_users'
                            AND sc.name ='model'
                SET @executesql =  'ALTER TABLE document_managment_users DROP CONSTRAINT ' + @defaultname
                EXEC(@executesql)
                DROP INDEX document_managment_users.recordId_user_id;
                ALTER TABLE document_managment_users DROP COLUMN model;";
                break;
        }

        if (!$this->db->query($query)) {
            $this->printLog('log', 'Error dropping model field from document_managment_users table: ');
        } else {
            $this->printLog('log', 'model field in document_managment_users table was successfully dropped..');
        }

        return true;
    }

    public function _getIdFromPath($path)
    {
        $pathArr = explode($this->directorySeparator, $path);

        return $pathArr[count($pathArr) - 1];
    }

    public function printLog($type, $log, $sTag = '', $eTag = '')
    {
        $logFilePath = 'files/logs/files-migration-'.date('Y-m-d').'.php';
        if (!$fp = @fopen($logFilePath, FOPEN_WRITE_CREATE)) {
            return false;
        }
        flock($fp, LOCK_EX);
        fwrite($fp, $log."\n");
        flock($fp, LOCK_UN);
        fclose($fp);
        @chmod($logFilePath, FILE_WRITE_MODE);
        if (!strcmp($type, 'display')) {
            if ($this->cliRequest) {
                echo $log."\n";
            } else {
                echo $sTag.$log.$eTag;
            }
        }
    }

    private function _cleanDB($model, $tablesToDelete = array(), $viewsToDelete = array())
    {
        $this->printLog('log', "Cleaning up old database data for {$model}", '<p style="font-family:Arial;font-size:15px">', '</p>');
        if (!empty($tablesToDelete)) {
            foreach ($tablesToDelete as $table) {
                $query = 'DROP TABLE '.$table;
                if (!$this->db->query($query)) {
                    $this->printLog('log', 'Error dropping DB table: '.$table);
                } else {
                    $this->printLog('log', 'DB table ['.$table.'] was successfully dropped..');
                }
            }
        }
        if (!empty($viewsToDelete)) {
            foreach ($viewsToDelete as $view) {
                $query = 'DROP VIEW '.$view;
                if (!$this->db->query($query)) {
                    $this->printLog('log', 'Error dropping DB View: '.$view);
                } else {
                    $this->printLog('log', 'DB view ['.$view.'] was successfully dropped..');
                }
            }
        }
    }
    private function _deleteDir($dir)
    {
        if ($handle = opendir($dir)) {
            $array = array();
            while (false !== ($file = readdir($handle))) {
                if ($file != '.' && $file != '..') {
                    if (is_dir($dir.$file) && !@rmdir($dir.$file)) {
                        $this->_deleteDir($dir.$file.'/');
                    } elseif (file_exists($dir.$file)) {
                        unlink($dir.$file);
                    }
                }
            }
            closedir($handle);
            rmdir($dir);
        }
    }
}
