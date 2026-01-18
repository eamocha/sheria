<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
require(APPPATH . "controllers/Top_controller.php");

class V6_7 extends Top_controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->index();
    }

    public function index()
    {
        $this->update_user_preference_integration_data();
    }

    public function update_user_preference_integration_data()
    {
        $this->load->model('user', 'Userfactory');
        $this->user = $this->userfactory->get_instance();
        $users_id = array_column($this->user->load_all(array('select' => 'id')), 'id');
        $this->load->model('user_preference');
        foreach ($users_id as $id) {
            $this->user_preference->fetch(array('user_id' => $id, 'keyName' => 'integration'));
            $old_settings = unserialize($this->user_preference->get_field('keyValue'));
            if (!empty($old_settings['google_calendar'])) {
                $new_settings = array(
                    'calendar' => array(
                        'enabled' => $old_settings['google_calendar']['enabled'],
                        'provider' => 'google',
                        'selected_calendar' => $old_settings['google_calendar']['selected_calendar'],
                        'calendar_name' => $old_settings['google_calendar']['calendar_name'],
                        'token' => $old_settings['google_calendar']['token'],
                        'integration_popup_displayed' => true
                    )
                );
                $this->user_preference->set_value('integration', serialize($new_settings), true, $id);
            }
            $this->user_preference->reset_fields();
        }
    }
}
