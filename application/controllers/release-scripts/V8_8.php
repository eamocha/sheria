<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

include APPPATH . 'libraries/traits/MigrationLogTrait.php';

class V8_8 extends CI_Controller
{
    use MigrationLogTrait;

    public $log_path = null;

    public function __construct()
    {
        parent::__construct();
        $this->hooks->enabled = false;
        $this->log_path = 'release-scripts' . DIRECTORY_SEPARATOR . get_class($this);
        $this->load->database();
        $this->write_log($this->log_path, 'start migration script');
    }

    public function index()
    {
        $this->fill_exchange_rates();
        $this->change_money_dashboard_widgets();
        $this->update_themes_tabs();
        $this->write_log($this->log_path, 'End migration script');
    }

    public function fill_exchange_rates()
    {
        $this->write_log($this->log_path, 'Filling Exchange Rates Started');
        $this->load->model('exchange_rate');
        $this->load->model('system_preference');
        $old_exchange_rates = $this->system_preference->get_value_by_key('exchangeRates')['keyValue'];
        if (!is_null($old_exchange_rates) && !empty($old_exchange_rates)) {
            $old_exchange_rates = unserialize($old_exchange_rates);
            $data = "";
            foreach ($old_exchange_rates as $organization_key => $organization) {
                foreach ($organization as $currency_key => $currency_rate) {
                    $data .= "({$currency_key}, {$organization_key}, {$currency_rate}),";
                }
            }
            $exchange_rates_count = $this->db->query("SELECT id FROM exchange_rates");
            if (!empty($data) && count($exchange_rates_count->result_array()) == 0) {
                $this->db->query("INSERT INTO exchange_rates (currency_id, organization_id, rate) VALUES " . substr($data, 0, -1));
                $exchange_rates_count = $this->db->query("SELECT id FROM exchange_rates");
                if (count($exchange_rates_count->result_array()) > 0) {
                    $this->db->query("DELETE FROM system_preferences WHERE keyName = 'exchangeRates'");
                } else {
                    $this->write_log($this->log_path, 'Exchange Rates Insertion Failed!');
                }
            }
            $this->write_log($this->log_path, 'Filling Exchange Rates Ended');
        } else {
            $this->write_log($this->log_path, 'No Old Exchange Rates Found!');
        }
    }

    public function change_money_dashboard_widgets()
    {
        $this->write_log($this->log_path, 'Started change_money_dashboard_widgets');

        $moneyDashboardWidgets = $this->db->query("SELECT id, filter FROM money_dashboard_widgets WHERE money_dashboard_widgets_type_id = 1")->result_array();

        foreach ($moneyDashboardWidgets as $moneyDashboardWidget) {
            $widgetId = $moneyDashboardWidget['id'];
            $filter = unserialize($moneyDashboardWidget['filter']);
            $columns = $filter['columns'];
            foreach ($columns as $key => $column) {
                switch ($column) {
                    case 'bills':
                      $columns[$key] = 'supplierBills';
                      break;
                    case 'billsPaid':
                        $columns[$key] = 'supplierPaidBills';
                      break;
                    case 'income':
                        $columns[$key] = 'collectedInvoices';
                      break;
                  }
            }
            $filter['columns'] = $columns;
            $newFilter = serialize($filter);
            $this->db->query("UPDATE money_dashboard_widgets SET filter = '{$newFilter}' WHERE id = {$widgetId};");
        }
        
        $this->write_log($this->log_path, 'Done change_money_dashboard_widgets');
    }

    public function update_themes_tabs()
    {
        $themes_dir = "assets".DIRECTORY_SEPARATOR."app_themes";
        $dirs = scandir($themes_dir);
        if (is_array($dirs) && count($dirs) > 2) {
            $this->write_log($this->log_path, "Done - Get all Dirs");
            unset($dirs[0], $dirs[1]);
            foreach ($dirs as $index => $value) {
                if(!is_dir($themes_dir.DIRECTORY_SEPARATOR.$value)){
                    unset($dirs[$index]);
                }
            }
            $this->write_log($this->log_path, "Done - Uset .. - . folders and files from array dirs");
            foreach ($dirs as $key => $value) {
                $theme_path = $themes_dir.DIRECTORY_SEPARATOR. $value.DIRECTORY_SEPARATOR;
                $this->write_log($this->log_path, "Done - Get Json files from theme ".$value);
                $theme_file = file_get_contents($theme_path.$value.'.json', FILE_USE_INCLUDE_PATH);
                $theme_json = json_decode($theme_file, true);
                $theme_scss = file($theme_path.$value.'.scss');
                $scss_text = '';
                $color_text = 'button.menu_add_new{background-color: $menu_add_new !important; &:hover{ background-color: $menu_add_new_hover !important; } &:active{ background-color: $menu_add_new_hover !important; } } button.menu_add_new{color: $menu_text_color !important;}';
                foreach ($theme_scss as $line) {
                    $scss_text.= $line;
                }
                $scss_text .= $color_text;
                $save_file_scss = file_put_contents($theme_path.$value.'.scss', $scss_text);
                if (@!$save_file_scss) {
                    $this->write_log($this->log_path, "Error - failed to put content to file ".$value.".scss");
                } else {
                    $this->write_log($this->log_path, "Done - save scss file ".$value.".scss theme ".$value);
                }
                $json_array = array("menu_add_new" => "#1ABC9C", "menu_add_new_hover" => "#148c75");
                $theme_json['menu'] = array_merge($theme_json['menu'], $json_array);
                $save_json = file_put_contents($theme_path.$value.'.json', json_encode($theme_json));
                if (@!$save_json) {
                    $this->write_log($this->log_path, "Error - failed to put content to file ".$value.".json  theme ".$value);
                } else {
                    $this->write_log($this->log_path, "Done - save json file ".$value.".json theme ".$value);
                }

                $data['theme'] = $theme_json;
                $scss_variables = $this->load->view('look_feel/style', $data, true);
                $save_css_variables = file_put_contents($theme_path.'variables.scss', $scss_variables);
                if (@!$save_css_variables) {
                    $this->write_log($this->log_path, "Error - failed to put content to file ".$value.".scss  theme ".$value);
                } else {
                    $this->write_log($this->log_path, "Done - save scss file variables.scss theme ".$value);
                }
                $this->load->library('scss_compiler');
                $scss = new Scss_compiler();
                $compile =  $scss->compile($scss_variables.$scss_text);
                $save_css = file_put_contents($theme_path.$value.'.css', $compile);
                if (@!$save_css) {
                    $this->write_log($this->log_path, "Error - failed to put content to file .css");
                } else {
                    $this->write_log($this->log_path, "Done - save css file .css theme ".$value);
                }
            }
        }
    }
}
