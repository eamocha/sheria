<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
require(APPPATH . "controllers/Top_controller.php");

class V6_8 extends Top_controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->model('language');
        $this->languages = $this->language->loadAvailableLanguages(true);
        $this->index();
    }

    public function index()
    {
        $this->grid_saved_filter_migration();
        $this->add_defualt_event_type();
        $this->merge_legal_case_event_subject_date_fields();
        $this->add_case_event_types_spanish_values_for_sqlsrv_db();
    }

    public function grid_saved_filter_migration()
    {
        $this->load->model('grid_saved_filter', 'grid_saved_filterfactory');
        $this->grid_saved_filter = $this->grid_saved_filterfactory->get_instance();
        $this->load->model('grid_saved_column');
        $grid_saved_filters = $this->grid_saved_filter->load_all(array('select' => array('id, model, user_id, formData')));
        foreach ($grid_saved_filters as $grid_saved_filter) {
            $this->grid_saved_column->reset_fields();
            $this->grid_saved_column->fetch(array('model' => $grid_saved_filter['model'], 'user_id' => $grid_saved_filter['user_id']));
            $sort = $selected_columns = '';
            if (!empty($this->grid_saved_column->get_field('grid_details'))) {
                $saved_filter_user_grid_properties = unserialize($this->grid_saved_column->get_field('grid_details'));
                $sort = $saved_filter_user_grid_properties['sort'];
                $selected_columns = $saved_filter_user_grid_properties['selected_columns'];
            }
            $grid_saved_filter['formData'] = unserialize($grid_saved_filter['formData']);
            $grid_saved_column_data = array(
                'model' => $grid_saved_filter['model'],
                'user_id' => $grid_saved_filter['user_id'],
                'grid_details' => serialize(array('pageSize' => $grid_saved_filter['formData']['gridPageSize'], 'sort' => $sort, 'selected_columns' => $selected_columns)),
                'grid_saved_filter_id' => $grid_saved_filter['id']
            );
            $this->grid_saved_column->reset_fields();
            $this->grid_saved_column->set_fields($grid_saved_column_data);
            $this->grid_saved_column->insert();
            unset($grid_saved_filter['formData']['gridPageSize']);
            $grid_saved_filter['formData'] = serialize($grid_saved_filter['formData']);
            $query = "UPDATE {$this->grid_saved_filter->_table} SET formData = '{$grid_saved_filter['formData']}' WHERE id = {$grid_saved_filter['id']}";
            $this->db->query($query);
        }
    }

    public function merge_legal_case_event_subject_date_fields()
    {
        $this->load->model('legal_case_event_type');
        $this->load->model('legal_case_event_type_form');
        $this->load->model('legal_case_event_type_form_language');
        $this->load->model('legal_case_event', 'legal_case_eventfactory');
        $this->legal_case_event = $this->legal_case_eventfactory->get_instance();
        $event_types = $this->legal_case_event_type->load_all();
        foreach ($event_types as $event_type_id) {
            $order = $this->legal_case_event_type_form->load(array('select' => 'field_order', 'where' => array('event_type', $event_type_id['id']), 'order_by' => 'id DESC', 'limit' => 1));
            $this->insert_subject_date_fields($event_type_id['id'], $order ? ($order['field_order'] + 1) : 0);
        }
        $list_type_fields = $this->legal_case_event_type_form->load_all(array('select' => 'id,event_type', 'where' => array("field_type = '5'")));
        $events = $this->legal_case_event->load_all();
        foreach ($events as $event) {
            $ids = $this->legal_case_event_type_form->load_all(array('select' => 'id,field_key', 'where' => array("event_type = {$event['event_type']} AND (field_key = 'subject' OR field_key = 'date')")));
            $fields = unserialize($event['fields']);
            if ($ids) {
                foreach ($ids as $id) {
                    if ($id['field_key'] === 'subject') {
                        $fields[$id['id']] = $event['subject'];
                    }
                    if ($id['field_key'] === 'date') {
                        $fields[$id['id']]['date'] = $event['start_date'];
                        $fields[$id['id']]['time'] = date('H:i', strtotime($event['start_time']));
                    }
                }
            }
            if ($list_type_fields) {
                foreach ($list_type_fields as $list) {
                    if ($event['event_type'] === $list['event_type'] && isset($fields[$list['id']])) {
                        $this->legal_case_event_type_form_language->fetch(array('field' => $list['id'], 'language_id' => '1'));
                        $options = explode(',', $this->legal_case_event_type_form_language->get_field('field_type_details'));
                        $values = array();
                        foreach ($options as $index => $option) {
                            if (in_array($option, $fields[$list['id']])) {
                                $key = array_keys($fields[$list['id']], $option);
                                $values[$key[0]] = $index;
                            }
                        }
                        if (!empty($values)) {
                            $fields[$list['id']] = $values;
                        }
                    }
                }
            }
            $new_fields = serialize($fields);
            $this->legal_case_event->fetch(array('id' => $event['id']));
            $this->legal_case_event->set_field('fields', $new_fields);
            $this->legal_case_event->update();
            $this->legal_case_event->reset_fields();
        }
        if ($this->db->dbdriver === 'sqlsrv') {
            $query = "DECLARE @constraint_name as NVARCHAR(255);
                    DECLARE @constraint_cursor as CURSOR;
                    DECLARE @columns_name TABLE (name varchar(1000));
                    DECLARE @table_name as NVARCHAR(255);
                    SET @table_name = 'legal_case_events';
                    INSERT INTO @columns_name VALUES ('start_time');
                    SET @constraint_cursor = CURSOR FOR
                    (SELECT fk.name AS constraint_name
                    FROM sys.foreign_keys fk
                        INNER JOIN sys.foreign_key_columns fkcol on fkcol.constraint_object_id = fk.object_id
                        INNER JOIN sys.columns col on col.column_id = fkcol.parent_column_id and fk.parent_object_id = col.object_id
                    WHERE fk.parent_object_id = OBJECT_ID(@table_name)
                        AND col.name IN (SELECT name FROM @columns_name)
                    UNION
                    SELECT chk.name AS constraint_name
                    FROM sys.check_constraints chk
                        INNER JOIN sys.columns col on col.column_id = chk.parent_column_id  and chk.parent_object_id = col.object_id
                    WHERE chk.parent_object_id = OBJECT_ID(@table_name)
                        AND col.name IN (SELECT name FROM @columns_name)
                    UNION
                    SELECT dc.name AS constraint_name
                    FROM sys.default_constraints dc
                        INNER JOIN sys.columns col ON col.default_object_id = dc.object_id and dc.parent_object_id = col.object_id
                    WHERE dc.parent_object_id = OBJECT_ID(@table_name)
                        AND col.name IN (SELECT name FROM @columns_name));
                    OPEN @constraint_cursor;
                    FETCH NEXT FROM @constraint_cursor INTO @constraint_name;
                    WHILE @@FETCH_STATUS = 0
                    BEGIN
                     EXEC(N'alter table ' + @table_name + ' drop constraint  [' + @constraint_name + N']');
                     FETCH NEXT FROM @constraint_cursor INTO @constraint_name;
                    END
                    CLOSE @constraint_cursor;
                    DEALLOCATE @constraint_cursor;
                    ALTER TABLE legal_case_events DROP COLUMN start_time;
                    ALTER TABLE legal_case_events DROP COLUMN subject,start_date;";
        } else {
            $query = "ALTER TABLE legal_case_events DROP subject,DROP start_date,DROP start_time;";
        }
        $this->db->query($query);
    }

    public function add_defualt_event_type()
    {
        $this->load->model('legal_case_event_type');
        $this->load->model('legal_case_event_type_language');
        $this->load->model('legal_case_event', 'legal_case_eventfactory');
        $this->legal_case_event = $this->legal_case_eventfactory->get_instance();
        $this->legal_case_event_type->set_field('sub_event', 0);
        $this->legal_case_event_type->insert();
        $event_type_id = $this->legal_case_event_type->get_field('id');
        $translation = array('1' => 'Other', '2' => 'أخرى', '3' => 'Autre', '4' => 'Otro');
        $validate = array(
            'name' => array(
                'required' => array(
                    'required' => true,
                    'allowEmpty' => false,
                    'rule' => array('minLength', 1),
                    'message' => $this->lang->line('cannot_be_blank_rule'),
                ),
                'maxLength' => array(
                    'rule' => array('maxLength', 255),
                    'message' => sprintf($this->lang->line('max_characters'), 255),
                )
            )
        );
        $this->legal_case_event_type_language->set('validate', $validate);
        foreach ($this->languages as $lang) {
            $this->legal_case_event_type_language->set_fields([
                'event_type' => $event_type_id,
                'language_id' => $lang['id'],
                'name' => $translation[$lang['id']]
            ]);
            $this->legal_case_event_type_language->insert();
            $this->legal_case_event_type_language->reset_fields();
        }
        $query = "UPDATE {$this->legal_case_event->_table} SET event_type = '{$event_type_id}' WHERE event_type = '' OR  event_type IS NULL";
        $this->db->query($query);
    }

    private function insert_subject_date_fields($event_type_id, $order = 0)
    {
        $this->load->model('legal_case_event_type_form');
        $default_fields = array(
            'subject' => array('field_type' => '1', 'field_order' => $order, 'field_name' => array('1' => 'Subject', '2' => 'الموضوع', '3' => 'Objet', '4' => 'Tema')),
            'date' => array('field_type' => '4', 'field_order' => $order + 1, 'field_name' => array('1' => 'Date', '2' => 'التاريخ', '3' => 'Date', '4' => 'Fecha')),
        );
        foreach ($default_fields as $key => $field) {
            $this->legal_case_event_type_form->set_fields([
                'event_type' => $event_type_id,
                'field_type' => $field['field_type'],
                'field_required' => '1',
                'field_order' => $field['field_order'],
                'field_key' => $key
            ]);
            $this->legal_case_event_type_form->insert();
            $field_id = $this->legal_case_event_type_form->get_field('id');
            $this->legal_case_event_type_form->reset_fields();

            foreach ($this->languages as $lang) {
                $this->legal_case_event_type_form_language->set_fields([
                    'field' => $field_id,
                    'language_id' => $lang['id'],
                    'field_name' => $field['field_name'][$lang['id']],
                    'field_description' => ''
                ]);
                $this->legal_case_event_type_form_language->insert();
                $this->legal_case_event_type_form_language->reset_fields();
            }
        }
    }

    public function add_case_event_types_spanish_values_for_sqlsrv_db()
    {
        if ($this->db->dbdriver === 'sqlsrv') {
            $event_types = $this->legal_case_event_type->load_all();
            $fields = $this->legal_case_event_type_form->load_all();
            foreach ($event_types as $event_type_id) {
                $this->legal_case_event_type_language->fetch(array('event_type' => $event_type_id['id'], 'language_id' => 1));
                $name = $this->legal_case_event_type_language->get_field('name');
                $this->legal_case_event_type_language->set_fields([
                    'event_type' => $event_type_id['id'],
                    'language_id' => 4,
                    'name' => $name,
                    'id' => ''
                ]);
                $this->legal_case_event_type_language->insert();
                $this->legal_case_event_type_language->reset_fields();
            }
            foreach ($fields as $field_id) {
                $this->legal_case_event_type_form_language->fetch(array('field' => $field_id['id'], 'language_id' => 1));
                $this->legal_case_event_type_form_language->set_fields([
                    'field' => $field_id['id'],
                    'language_id' => 4,
                    'field_name' => $this->legal_case_event_type_form_language->get_field('field_name'),
                    'field_type_details' => $this->legal_case_event_type_form_language->get_field('field_type_details'),
                    'field_description' => $this->legal_case_event_type_form_language->get_field('field_description'),
                    'id' => ''
                ]);
                $this->legal_case_event_type_form_language->insert();
                $this->legal_case_event_type_form_language->reset_fields();
            }
        }
    }
}
