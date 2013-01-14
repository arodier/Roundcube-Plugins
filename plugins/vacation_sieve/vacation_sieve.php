<?php

/*
 +-----------------------------------------------------------------------+
 | Vacation Module for RoundCube                                         |
 |                                                                       |
 | Copyright (C) 2011 André Rodier <andre.rodier@gmail.com>              |
 | Licensed under the GNU GPL                                            |
 +-----------------------------------------------------------------------+
 */

define ('PLUGIN_SUCCESS', 0);
define ('PLUGIN_ERROR_DEFAULT', 1);
define ('PLUGIN_ERROR_CONNECT', 2);
define ('PLUGIN_ERROR_PROCESS', 3);

class vacation_sieve extends rcube_plugin
{
    public $task = 'settings';
    private $app;
    private $obj;
    private $config;

    /*
     * Initializes the plugin.
     */
    public function init()
    {
        try
        {
            write_log('vacation_sieve', 'Initialising');

            $this->app = rcmail::get_instance();
            $this->add_texts('localization/', true);

            $this->app->output->add_label('vacation');
            $this->register_action('plugin.vacation_sieve', array($this, 'vacation_sieve_init'));
            $this->register_action('plugin.vacation_sieve-save', array($this, 'vacation_sieve_save'));
            $this->include_script('vacation_sieve.js');
            $this->include_stylesheet('styles.css');

            # Load default config, and merge with users' settings
            $this->load_config('config-default.inc.php');
            $this->load_config('config.inc.php');

            $this->config = $this->app->config->get('vacation_sieve');


            require $this->home . '/model.php';
            
            $this->obj = new model();

            write_log('vacation_sieve', 'Initialised');
        }
        catch ( Exception $exc )
        {
            write_log('vacation_sieve', 'Fail to initialise: '.$exc->getMessage());
        }
    }

    /*
     * Plugin initialization function.
     */
    public function vacation_sieve_init()
    {
        try
        {
            write_log('vacation_sieve', 'Loading data');
            $this->read_data();

            $this->register_handler('plugin.body', array($this, 'vacation_sieve_form'));
            $this->app->output->set_pagetitle($this->gettext('vacation'));
            $this->app->output->send('plugin');
        }
        catch (Exception $exc)
        {
            write_log('vacation_sieve', 'Fail to Loaded: '.$exc->getMessage());
        }
    }

	/*
	 * Reads plugin data.
	 */
	public function read_data()
	{
        return true;
    }

    /*
     * Plugin save function.
     */
    public function vacation_sieve_save()
    {
        try
        {
            write_log('vacation_sieve', 'Saving data');
            $this->write_data();

            $this->register_handler('plugin.body', array($this, 'vacation_sieve_form'));
            $this->app->output->set_pagetitle($this->gettext('vacation'));
            rcmail_overwrite_action('plugin.vacation_sieve');
            $this->app->output->send('plugin');
        }
        catch ( Exception $exc)
        {
            write_log('vacation_sieve', 'Fail to save: '.$exc->getMessage());
        }
    }

	/*
	 * Reads plugin data.
	 */
	public function write_data()
	{
        $params = array();
        $params['enable'] = get_input_value('_vacation_enable', RCUBE_INPUT_POST, true);
        $params['start'] = get_input_value('_vacation_start', RCUBE_INPUT_POST);
        $params['end'] = get_input_value('_vacation_end', RCUBE_INPUT_POST);
        $params['every'] = intval(get_input_value('_every', RCUBE_INPUT_POST));

        $params['subject'] = get_input_value('_vacation_subject', RCUBE_INPUT_POST);
        $params['appendSubject'] = (get_input_value('_append_subject', RCUBE_INPUT_POST) == 'on');

        $params['addresses'] = get_input_value('_addressed_to', RCUBE_INPUT_POST, true);
        $params['sendFrom'] = get_input_value('_send_from', RCUBE_INPUT_POST, true);
        $params['message'] = get_input_value('_vacation_message', RCUBE_INPUT_POST, true);

        require 'scriptmanager.php';
        $scriptManager = new ScriptManager();
        $script = $scriptManager->BuildScriptFromParams($params);

        # Open the config file, and save the script
        $transferParams = $this->config['transfer'];
        $path = $transferParams['path'];
        $userData = ($this->app->user->data);
        $userName = $userData['alias'];
        list($logon,$domain) = preg_split('/@/', $userName);
        $path = str_replace('<domain>', $domain, $path);
        $path = str_replace('<logon>', $logon, $path);
        $transferParams['path'] = $path;

        # load the transfer class 
        require 'transfer/factory.php';
        $mode = $transferParams['mode'];
        $transfer = GetTransferClass($mode, $transferParams);

        $success = $transfer->SaveScript($path,$script);
        
        if ( !$success )
        {
            $msg = sprintf("Cannot save the script in '%s'", $path);
            write_log('vacation_sieve', $msg);
        }
    }

    /*
     * Plugin UI form function.
     */
    public function vacation_sieve_form()
    {
        try
        {
            $table = new html_table(array('cols' => 2, 'class' => 'propform'));

            $format = $this->app->config->get('date_format');

            # Options
            $table->add(array('colspan' => 2, 'class' => 'section-first'),Q($this->gettext('options')));
            $table->add_row();
            $field_id = 'vacation_enable';
            $input_vacationenable = new html_checkbox(array('name' => '_vacation_enable', 'id' => $field_id, 'value' => 1));
            $table->add('title', html::label($field_id, Q($this->gettext('vacationenable'))));
            $table->add(null, $input_vacationenable->show($this->obj->is_vacation_enable() ? 1 : 0));

            $field_id = 'vacation_start';
            $input_vacationstart = new html_inputfield(array('name' => '_vacation_start', 'id' => $field_id, 'size' => 10));
            $table->add('title', html::label($field_id, Q($this->gettext('period'))));
            $vacStart = $this->obj->get_vacation_start();

            $field_id = 'vacation_end';
            $input_vacationend = new html_inputfield(array('name' => '_vacation_end', 'id' => $field_id, 'size' => 10));
            $vacEnd = $this->obj->get_vacation_end();

            $periodFields = 'From '.$input_vacationstart->show(date($format, $vacStart)) . ' To ' . $input_vacationend->show(date($format, $vacEnd));
            $table->add(null, $periodFields);

            $table->add_row();
            $field_id = 'every';
            $input_every = new html_inputfield(array('name' => '_every', 'id' => $field_id, 'size' => 5));
            $table->add('title', html::label($field_id, Q($this->gettext('frequency'))));
            $table->add(null, 'Answer no more than every ' . $input_every->show($this->obj->get_every()) . ' days');
            
            $table->add_row();
            $identities = $this->get_identities();
            $field_id = 'addressed_to';
            $input_addressed_to = new html_select(array('name' => '_addressed_to[]', 'multiple' => 'multiple', 'id' => 'identities'));
            $input_addressed_to->add($identities);
            $table->add('top title', html::label($field_id, Q($this->gettext('addressed_to'))));
            $table->add(null, $input_addressed_to->show());

            # Subject field
            $table->add(array('colspan' => 2, 'class' => 'section'),Q($this->gettext('subject')));
            $table->add_row();
            $field_id = 'vacation_subject';
            $input_vacationsubject = new html_inputfield(array('name' => '_vacation_subject', 'id' => $field_id, 'size' => 40));
            $table->add('title', html::label($field_id, Q($this->gettext('vacationsubject'))));
            $table->add(null, $input_vacationsubject->show($this->obj->get_vacation_subject()));

            $table->add_row();
            $field_id = '_append_subject';
            $input_appendsubject = new html_checkbox(array('name' => '_append_subject', 'id' => $field_id));
            $table->add('title', html::label($field_id, Q($this->gettext('append_subject'))));
            $table->add(null, $input_appendsubject->show($this->obj->get_append_subject()));

            # Message
            $table->add(array('colspan' => 2, 'class' => 'section'),Q($this->gettext('vacationmessage')));
            $table->add_row();
            $field_id = 'send_from';
            $input_sendfrom = new html_select(array('name' => '_send_from'));
            $input_sendfrom->add($identities);
            $table->add('title', html::label($field_id, Q($this->gettext('send_from'))));
            $table->add(null, $input_sendfrom->show());

            # Add the HTML Row
            $table->add_row();
            $field_id = 'vacation_message';
            if ($this->config['msg_format'] == 'html')
            {
                $this->app->output->add_label('converting', 'editorwarning');
                rcube_html_editor('identity');

                $text_vacationmessage =
                new html_textarea(array('name' => '_vacation_message', 'id' => $field_id, 'spellcheck' => 1, 'rows' => 6, 'cols' => 40, 'class' => 'mce_editor'));
            }
            else
            {
                $text_vacationmessage = new html_textarea(array('name' => '_vacation_message', 'id' => $field_id, 'spellcheck' => 1, 'rows' => 6, 'cols' => 40));
            }

            # 
            $table->add('top title', html::label($field_id, Q($this->gettext('vacationmessage'))));
            $table->add(null, $text_vacationmessage->show($this->obj->get_vacation_message()));

            # Get the HTML
            $tableHtml = $table->show();
            $submitLine = html::p(null,
                $this->app->output->button(array('command' => 'plugin.vacation_sieve-save',
                'type' => 'input', 'class' => 'button mainaction', 'label' => 'save')));

            # First line
            $boxTitle = html::div(array('id' => "prefs-title", 'class' => 'boxtitle'), $this->gettext('vacation'));

            //*/
            $out = html::div(
                array('class' => 'box'),
                $boxTitle . html::div(array('class' => "boxcontent"), $tableHtml . $submitLine));

            $this->app->output->add_gui_object('vacationsieveform', 'vacation_sieve_form');

            return $this->app->output->form_tag(
                array(
                'id' => 'vacation_sieve_form',
                'name' => 'vacation_sieve_form',
                'method' => 'post',
                'action' => './?_task=settings&_action=plugin.vacation_sieve-save'),
                $out);
        }
        catch(Exception $exc)
        {
            write_log('vacation_sieve', 'Fail to build form: '.$exc->getMessage());
        }
    }

    private function get_identities()
    {
        $select = array();
        $identities = $this->app->user->list_identities();

        foreach ( $identities as $identity )
        {
            $email = $identity['email'];
            $name = $identity['name'];
            $text = sprintf("%s <%s>", $name, $email);
            $select[$text] = $text;
        }

        return $select;
    }

}
