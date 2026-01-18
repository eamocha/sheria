<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
require(APPPATH . "controllers/Top_controller.php");

class V7_0 extends Top_controller
{
    public function __construct()
    {
        parent::__construct();
        ini_set('max_execution_time', 0);
        $this->load->database();
        $this->index();
    }

    public function index()
    {
        $this->update_number_in_accounts();
        $this->log('Account number step is done', 'info');
        $this->dms_migration();
    }

    public function dms_migration()
    {
        $this->dms_data_migration();
        $this->dms_permissions_migration();
        $this->dms_config_migration();
    }

    public function dms_data_migration()
    {
        $this->load->library('dms');
        $this->remove_corrupted_files();
        $models = array(
            array(
                'fetch_query' => array(
                    'where' => array(
                        array('module', 'company')
                    )
                ),
                'module' => 'company',
                'module_record_id_table' => 'companies',
                'container' => 'companies'
            ),
            array(
                'fetch_query' => array(
                    'where' => array(
                        array('module', 'contact')
                    )
                ),
                'module' => 'contact',
                'module_record_id_table' => 'contacts',
                'container' => 'contacts'
            ),
            array(
                'fetch_query' => array(
                    'where' => array(
                        array('module', 'case')
                    )
                ),
                'module' => 'case',
                'module_record_id_table' => 'legal_cases',
                'container' => 'cases'
            ),
            array(
                'fetch_query' => array(
                    'where' => array(
                        array('module', 'document'),
                        array('module_record_id', 1)
                    )
                ),
                'module' => 'doc',
                'container' => 'docs'
            ),
            array(
                'fetch_query' => array(
                    'where' => array(
                        array('module', 'BI-DOCS')
                    )
                ),
                'module' => 'BI',
                'module_record_id_table' => 'voucher_headers',
                'container' => 'money' . DIRECTORY_SEPARATOR . 'bills'
            ),
            array(
                'fetch_query' => array(
                    'like' => array(
                        array('module', 'BI-PY_', 'after')
                    )
                ),
                'module' => 'BI-PY',
                'module_record_id_table' => 'bill_payments',
                'container' => 'money' . DIRECTORY_SEPARATOR . 'bills_payments'
            ),
            array(
                'fetch_query' => array(
                    'where' => array(
                        array('module', 'EXP-DOCS')
                    )
                ),
                'module' => 'EXP',
                'module_record_id_table' => 'voucher_headers',
                'container' => 'money' . DIRECTORY_SEPARATOR . 'expenses'
            ),
            array(
                'fetch_query' => array(
                    'where' => array(
                        array('module', 'INV-DOCS')
                    )
                ),
                'module' => 'INV',
                'module_record_id_table' => 'voucher_headers',
                'container' => 'money' . DIRECTORY_SEPARATOR . 'invoices'
            ),
            array(
                'fetch_query' => array(
                    'like' => array(
                        array('module', 'INV-PY_', 'after')
                    )
                ),
                'module' => 'INV-PY',
                'module_record_id_table' => 'invoice_payments',
                'container' => 'money' . DIRECTORY_SEPARATOR . 'invoices_payments'
            )
        );
        foreach ($models as $model) {
            $this->model_data_migration($model);
            $this->log('Model data migration for '.$model['module'] . ' is done', 'info');
        }
        $this->db->query("DELETE FROM {$this->dms->model->_table} WHERE lineage = '/1' OR lineage like '/1/%'");
        $this->dms->delete_tree(FCPATH . 'webdav' . DIRECTORY_SEPARATOR . 'data');
        $this->log('Deleting web dav tree is done', 'info');
        $this->versioning_data_migration();
    }

    public function remove_corrupted_files()
    {
        $corrupted_files = $this->dms->model->load_all(array('where' => array(array('type', 'file'), array('name', 'FileRenamedTemporarly_forbiddenLetters'))));
        $this->log('removing corrupted files step started', 'info');
        foreach ($corrupted_files as $file) {
            $this->db->delete('documents_management_system', array('id' => $file['id']));
            if (file_exists(FCPATH . 'webdav' . DIRECTORY_SEPARATOR . 'data' . $file['lineage'])) {
                if (@!unlink(FCPATH . 'webdav' . DIRECTORY_SEPARATOR . 'data' . $file['lineage'])) {
                    $this->log("failed to delete '" . FCPATH . 'webdav' . DIRECTORY_SEPARATOR . 'data' . $file['lineage'] . "'");
                }
            }
        }
        $this->log('removing corrupted files step is done', 'info');
    }

    public function model_data_migration($model)
    {
        $model_containers = $this->dms->model->load_all($model['fetch_query']);
        foreach ($model_containers as $model_container) {
            if (file_exists(FCPATH . 'webdav' . DIRECTORY_SEPARATOR . 'data' . $model_container['lineage'])) {
                $migrate = true;
                if (isset($model['module_record_id_table'])) {
                    $query_result = $this->db->query("SELECT id FROM {$model['module_record_id_table']} WHERE id = {$model_container['module_record_id']}");
                    $query_data = $query_result->result_array();
                    if (!isset($query_data[0])) {
                        $migrate = false;
                    }
                }
                if ($migrate) {
                    $model_container_lineage_explode = explode('/', $model_container['lineage']);
                    array_pop($model_container_lineage_explode);
                    $model_container_parent_lineage = implode('/', $model_container_lineage_explode);
                    $query['like'] = array(
                        array('lineage', "{$model_container['lineage']}/", 'after')
                    );
                    $model_documents = $this->dms->model->load_all($query);
                    if (strpos($model_container['module'], 'BI-PY') !== false || strpos($model_container['module'], 'INV-PY') !== false) {
                        $payment_model_document_key = array_search(max(array_column($model_documents, 'id')), array_column($model_documents, 'id'));
                        $payment_model_document = $model_documents[$payment_model_document_key];
                        $this->update_document_data($model, $model_container_parent_lineage, $model_container, $payment_model_document);
                        unset($model_documents[$payment_model_document_key]);
                        foreach ($model_documents as $model_document) {
                            if (file_exists(FCPATH . 'webdav' . DIRECTORY_SEPARATOR . 'data' . $model_document['lineage'])) {
                                if (@!unlink(FCPATH . 'webdav' . DIRECTORY_SEPARATOR . 'data' . $model_document['lineage'])) {
                                    $this->log("failed to delete '" . FCPATH . 'webdav' . DIRECTORY_SEPARATOR . 'data' . $model_document['lineage'] . "'");
                                }
                            }
                            $this->db->query("DELETE FROM {$this->dms->model->_table} WHERE id = {$model_document['id']};");
                        }
                    } else {
                        foreach ($model_documents as $model_document) {
                            $this->update_document_data($model, $model_container_parent_lineage, $model_container, $model_document);
                        }
                    }
                    $this->update_document_data($model, $model_container_parent_lineage, $model_container);
                    $model_container_path = FCPATH . 'files' . DIRECTORY_SEPARATOR . 'attachments' . DIRECTORY_SEPARATOR . $model['container'] . DIRECTORY_SEPARATOR . $model_container['id'];
                    if (@!rename(FCPATH . 'webdav' . DIRECTORY_SEPARATOR . 'data' . $model_container['lineage'], $model_container_path)) {
                        $this->log("failed to move '" . FCPATH . 'webdav' . DIRECTORY_SEPARATOR . 'data' . $model_container['lineage'] . "' to '" . $model_container_path . "'");
                    }
                } else {
                    $this->db->query("DELETE FROM {$this->dms->model->_table} WHERE lineage = '{$model_container['lineage']}' OR lineage like '{$model_container['lineage']}%'");
                    $this->dms->delete_tree(FCPATH . 'webdav' . DIRECTORY_SEPARATOR . 'data' . $model_container['lineage']);
                    $this->log('Deleting records for '.$model_container['lineage'] . ' from webdav data folder is done', 'info');
                }
            } else {
                $this->db->query("DELETE FROM {$this->dms->model->_table} WHERE lineage = '{$model_container['lineage']}' OR lineage like '{$model_container['lineage']}%'");
                $this->log('Deleting records for '.$model_container['lineage'] . ' from document managmenet system table is done', 'info');
            }
        }
    }

    public function update_document_data($model, $model_container_parent_lineage, $model_container, $model_document = null)
    {
        $document = empty($model_document) ? $model_container : $model_document;
        if (file_exists(FCPATH . 'webdav' . DIRECTORY_SEPARATOR . 'data' . $document['lineage']) && ($document['type'] == 'folder' || filesize(FCPATH . 'webdav' . DIRECTORY_SEPARATOR . 'data' . $document['lineage']) > 0)) {
            $sliced_lineage = substr($document['lineage'], strlen($model_container_parent_lineage), strlen($document['lineage']));
            $exploded_lineage = explode('/', $sliced_lineage);
            $parent_document = count($exploded_lineage) > 2 ? $exploded_lineage[count($exploded_lineage) - 2] : 'NULL';
            $new_lineage = $this->db->escape(implode(DIRECTORY_SEPARATOR, $exploded_lineage));
            if (in_array($model['module'], array('BI-PY', 'INV-PY'))) {
                $query_result = $this->db->query("SELECT voucher_header_id FROM {$model['module_record_id_table']} WHERE id = {$model_container['module_record_id']}");
                $query_data = $query_result->result_array();
                $model_container['module_record_id'] = $document['module_record_id'] = $query_data[0]['voucher_header_id'];
            }
            $fields = "lineage = {$new_lineage}, parent = {$parent_document}, module = '{$model['module']}', module_record_id = " . ($model['module'] == 'doc' ? 'NULL' : $model_container['module_record_id']);
            if ($document['type'] == 'file') {
                $file_name_exploded = explode('.', $document['name']);
                $file_extension = $file_name_exploded[count($file_name_exploded) - 1];
                array_pop($file_name_exploded);
                $file_raw_name = addslashes(implode('.', $file_name_exploded));
                $fields .= ", name = '{$file_raw_name}', extension = '{$file_extension}', size = " . filesize(FCPATH . 'webdav' . DIRECTORY_SEPARATOR . 'data' . $document['lineage']);
            }
            if (empty($model_document) || !empty($model_document['module'])) {
                foreach (array('company', 'contact', 'case', 'document', 'BI-DOCS', 'BI-PY_', 'EXP-DOCS', 'INV-DOCS', 'INV-PY_') as $module) {
                    if (strpos($document['module'], $module) !== false) {
                        $model_container_name = $model['module'] == 'doc' ? "{$model['module']}_container" : "{$model['module']}_{$document['module_record_id']}";
                        $document['name'] != $model_container_name && $document['module'] != 'case-notes' ? $fields .= ", name = '{$model_container_name}'" : null;
                        $fields .= ", system_document = 1, visible = "  . ($document['module'] == 'case-notes' ? "1" : "0");
                        break;
                    }
                }
            }
            $this->db->query("UPDATE {$this->dms->model->_table} SET {$fields} WHERE id = {$document['id']};");
        } else {
            if (file_exists(FCPATH . 'webdav' . DIRECTORY_SEPARATOR . 'data' . $document['lineage'])) {
                if (@!unlink(FCPATH . 'webdav' . DIRECTORY_SEPARATOR . 'data' . $document['lineage'])) {
                    $this->log("failed to delete '" . FCPATH . 'webdav' . DIRECTORY_SEPARATOR . 'data' . $document['lineage'] . "'");
                }
            }
            $this->db->query("DELETE FROM {$this->dms->model->_table} WHERE id = {$document['id']};");
        }
    }

    public function versioning_data_migration()
    {
        $query = "
        SELECT d1.id, d1.full_name, d1.version, d1.parent, d1.parent_lineage, d1.module, d1.module_record_id, d1.createdOn, d1.createdBy, d1.createdByChannel
        FROM documents_management_system_full_details d1
        WHERE d1.type = 'file' AND d1.version = (
            SELECT max(d2.version)
            FROM documents_management_system_full_details d2
            WHERE d2.module = d1.module
                AND d2.parent = d1.parent
        		AND d2.full_name = d1.full_name
        		AND d2.version > 1
            GROUP BY d2.parent, d2.full_name
        )";
        $query_result = $this->db->query($query);
        $documents_with_versioning = $query_result->result_array();
        $this->log('versioning migration for data will start', 'info');
        foreach ($documents_with_versioning as $document) {
            if ($this->dms->get_module_properties($document['module'], 'versioning')) {
                $this->log('versioning migration with version structure for '.$document['module'].' document '.$document['id'] . ' will start', 'info');
                $this->dms->model->reset_fields();
                $this->dms->model->set_fields(
                    array(
                        'name' => "{$document['id']}_versions",
                        'type' => 'folder',
                        'parent' => $document['parent'],
                        'module' => $document['module'],
                        'module_record_id' => $document['module_record_id'],
                        'system_document' => 1,
                        'visible' => 0,
                        'createdOn' => $document['createdOn'],
                        'createdBy' => $document['createdBy'],
                        'createdByChannel' => $document['createdByChannel'],
                        'modifiedOn' => $document['createdOn'],
                        'modifiedBy' => $document['createdBy'],
                        'modifiedByChannel' => $document['createdByChannel']
                    )
                );
                $this->dms->model->insert();
                $this->log('Inserting version folder for '.$document['module'].' document '.$document['id'] . ' is done', 'info');
                $versions_container_lineage = empty($document['parent_lineage']) ? DIRECTORY_SEPARATOR . $document['parent'] : $document['parent_lineage'];
                $this->dms->model->set_field('lineage', $versions_container_lineage . DIRECTORY_SEPARATOR . $this->dms->model->get_field('id'));
                $this->dms->model->update();
                $this->log('Updating version folder for  '.$document['module'].' document '.$document['id'] . ' is done', 'info');
                if (@mkdir($this->dms->get_module_container_path($document['module']) . $this->dms->model->get_field('lineage'))) {
                    $versions_container = $this->dms->model->get_fields();
                    $query = "
                    SELECT v.id, v.lineage, v.version, v.module, v.createdOn, v.createdBy, v.createdByChannel
                    FROM documents_management_system_full_details v
                    WHERE v.id != {$document['id']} AND v.type = 'file' AND v.parent = {$document['parent']} AND v.full_name = '" . addslashes($document['full_name']) . "'";
                    $query_result = $this->db->query($query);
                    $document_versions = $query_result->result_array();
                    if (!empty($document_versions)) {
                        $document_initial_version = $document_versions[array_search(min(array_column($document_versions, 'version')), array_column($document_versions, 'version'))];
                        foreach ($document_versions as $version) {
                            $this->dms->model->reset_fields();
                            $this->dms->model->fetch($version['id']);
                            $versioned_file_lineage = $versions_container['lineage'] . DIRECTORY_SEPARATOR . $this->dms->model->get_field('id');
                            $this->dms->model->set_fields(
                                array(
                                    'parent' => $versions_container['id'],
                                    'lineage' => $versioned_file_lineage,
                                    'initial_version_created_on' => $document_initial_version['createdOn'],
                                    'initial_version_created_by' => $document_initial_version['createdBy'],
                                    'initial_version_created_by_channel' => $document_initial_version['createdByChannel'],
                                    'visible' => 0
                                )
                            );
                            $this->dms->model->update();
                            $this->log('Updating version data for '.$version['id'] . ' is done', 'info');
                            if (@!rename($this->dms->get_module_container_path($version['module']) . $version['lineage'], $this->dms->get_module_container_path($version['module']) . $versioned_file_lineage)) {
                                $this->log("failed to move '" . $this->dms->get_module_container_path($version['module']) . $version['lineage']. "' to '" . $this->dms->get_module_container_path($version['module']) . $versioned_file_lineage . "'");
                            }
                        }
                        $this->dms->model->reset_fields();
                        $this->dms->model->fetch($document['id']);
                        $this->dms->model->set_fields(
                            array(
                                'initial_version_created_on' => $document_initial_version['createdOn'],
                                'initial_version_created_by' => $document_initial_version['createdBy'],
                                'initial_version_created_by_channel' => $document_initial_version['createdByChannel']
                            )
                        );
                        $this->dms->model->update();
                        $this->log('Updating initial versions data for document id '.$document['id'] . ' is done', 'info');
                    }
                } else {
                    $this->log("failed to create directory '" . $this->dms->get_module_container_path($document['module']) . $this->dms->model->get_field('lineage') . "'");
                }
            } else {
                $this->log('versioning migration with version column set to 1 for '.$document['module'].' document '.$document['id'] . ' will start', 'info');
                $query = "
                SELECT v.id, v.name, v.version
                FROM documents_management_system_full_details v
                WHERE v.type = 'file' AND v.version > 1 AND v.parent = {$document['parent']} AND v.full_name = '" . addslashes($document['full_name']) . "'";
                $query_result = $this->db->query($query);
                $document_versions = $query_result->result_array();
                foreach ($document_versions as $version) {
                    $this->db->query("UPDATE {$this->dms->model->_table} SET name = '{$version['name']} ({$version['version']})', version = 1 WHERE id = {$version['id']};");
                    $this->log('Updating document to version 1 for document id '.$version['id'] . ' is done', 'info');
                }
            }
        }
    }

    public function dms_permissions_migration()
    {
        $this->log('dms permissions migrations started', 'info');
        $this->load->model('user_group', 'user_groupfactory');
        $this->user_group = $this->user_groupfactory->get_instance();
        $this->load->model('user_group_permission');
        $modules = array(
            'company' => array(
                'permission_module' => 'core',
                'controller' => 'companies',
                'list_document_permission' => '/companies/documents/'
            ),
            'contact' => array(
                'permission_module' => 'core',
                'controller' => 'contacts',
                'list_document_permission' => '/contacts/documents/'
            ),
            'case' => array(
                'permission_module' => 'core',
                'controller' => 'cases',
                'list_document_permission' => '/cases/documents/'
            ),
            'intellectual_property' => array(
                'permission_module' => 'core',
                'controller' => 'intellectual_properties',
                'list_document_permission' => '/intellectual_properties/documents/'
            ),
            'document' => array(
                'permission_module' => 'core',
                'controller' => 'docs',
                'list_document_permission' => '/documents/index/',
                'list_document_permission_update' => '/docs/index/'
            ),
            'bill' => array(
                'permission_module' => 'money',
                'controller' => 'vouchers',
            ),
            'bill_payment' => array(
                'permission_module' => 'money',
                'controller' => 'vouchers',
                'payment_module' => true
            ),
            'expense' => array(
                'permission_module' => 'money',
                'controller' => 'vouchers',
            ),
            'invoice' => array(
                'permission_module' => 'money',
                'controller' => 'vouchers',
            ),
            'invoice_payment' => array(
                'permission_module' => 'money',
                'controller' => 'vouchers',
                'payment_module' => true
            )
        );
        $actions = array(
            'file_add' => 'upload_file',
            'folder_add' => 'create_folder',
            'attachment_shared_with' => 'share_folder',
            'file_rename' => 'rename_file',
            'folder_rename' => 'rename_folder',
            'attachment_edit' => 'edit_documents',
            'file_download' => 'download_file',
            'file_delete' => 'delete_document'
        );
        $user_groups = $this->user_group->load_all(array('select' => array('id, name')));
        foreach ($user_groups as $group) {
            $user_group_permissions = $this->user_group_permission->get_permissions($group['id'], false);
            if (isset($user_group_permissions['core'])) {
                foreach ($modules as $module_key => $module_properties) {
                    foreach ($actions as $action_old_name => $action_new_name) {
                        $action_key = array_search("/webdav_documents/{$module_key}_{$action_old_name}/", $user_group_permissions['core']);
                        if ($action_key !== false) {
                            if ($module_properties['permission_module'] == 'core') {
                                $user_group_permissions['core'][$action_key] = "/{$module_properties['controller']}/{$action_new_name}/";
                            } else {
                                unset($user_group_permissions['core'][$action_key]);
                                if (!isset($module_properties['payment_module']) || $action_old_name == 'file_download') {
                                    $user_group_permissions['money'][] = "/{$module_properties['controller']}/{$module_key}_{$action_new_name}/";
                                }
                            }
                        }
                    }
                    if (isset($module_properties['list_document_permission']) && in_array($module_properties['list_document_permission'], $user_group_permissions['core'])) {
                        $user_group_permissions['core'][] = "/{$module_properties['controller']}/list_file_versions/";
                        if (isset($module_properties['list_document_permission_update'])) {
                            $action_key = array_search($module_properties['list_document_permission'], $user_group_permissions['core']);
                            $user_group_permissions['core'][$action_key] = $module_properties['list_document_permission_update'];
                        }
                    }
                }
                $webdav_permission_key = array_search('/webdav_documents/', $user_group_permissions['core']);
                if ($webdav_permission_key !== false) {
                    unset($user_group_permissions['core'][$webdav_permission_key]);
                }
                $this->user_group_permission->set_permission_data($group['id'], $user_group_permissions);
            }
        }
        $this->log('dms permissions migrations is done', 'info');
    }

    public function dms_config_migration()
    {
        $this->log('dms config migration started', 'info');
        $config_vars_keys = array(
            'company_attachments' => 'company',
            'contact_attachments' => 'contact',
            'case_attachments' => 'case',
            'idocs' => 'doc',
            'bills_attachments' => 'BI',
            'expenses_attachments' => 'EXP',
            'invoices_attachments' => 'INV'
        );
        if (@$file_content = file_get_contents(FCPATH . 'application' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'allowed_file_uploads.php')) {
            foreach ($config_vars_keys as $old_key => $new_key) {
                $file_content = str_replace($old_key, $new_key, $file_content);
            }
            $file_content .= "\n\$config['BI-PY'] = 'doc|docx|xls|xlsx|pps|ppt|pptx|pdf|tif|jpg|png|gif|jpeg|bmp|html|htm|txt|msg|eml|vcf|zip|rar|mpg|mp3|mp4|flv';\n\n\$config['INV-PY'] = 'doc|docx|xls|xlsx|pps|ppt|pptx|pdf|tif|jpg|png|gif|jpeg|bmp|html|htm|txt|msg|eml|vcf|zip|rar|mpg|mp3|mp4|flv';";
            if (@!file_put_contents(FCPATH . 'application' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'allowed_file_uploads.php', $file_content)) {
                $this->log("failed to put content to file '" . FCPATH . 'application' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . "allowed_file_uploads.php'");
            }
        } else {
            $this->log("failed to get content of file '" . FCPATH . 'application' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . "allowed_file_uploads.php'");
        }
        $this->log('dms config migration is done', 'info');
    }

    public function log($message, $type = 'error')
    {
        $pr = fopen(FCPATH . 'files' . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR . 'v7_0_release_scripts_log.log', 'a');
        fwrite($pr, "[" . date('Y-m-d H:i:s') . "] - {$message} \n");
        fclose($pr);
        if ($type=='error') {
            echo $type.': '.$message .' check log file ' . FCPATH . 'files' . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR . "v7_0_release_scripts_log.log </br>";
            exit;
        }
    }
    public function update_number_in_accounts()
    {
        $query = 'SELECT id, organization_id, account_type_id from accounts';
        $results = $this->db->query($query)->result_array();
        $data = array();
        foreach ($results as $row) {
            $data[$row['organization_id']][$row['account_type_id']][] = $row['id'];
        }
        $i = 1;
        foreach ($data as $val) {
            foreach ($val as $ids) {
                foreach ($ids as $id) {
                    $update_query = "Update accounts set number={$i} where id = {$id} ";
                    $this->db->query($update_query);
                    $i++;
                }
                $i=1;
            }
        }
    }
}
