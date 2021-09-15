<?php
/**
* 2007-2021 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2021 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

class Ps_modulebase extends Module
{
    protected $config_form = false;
    public $errors = [];
    public $warning = [];
    public $success = [];
    public $info = [];

    const PATH_LOG = _PS_ROOT_DIR_ . "/../logs/modules/ps_modulebase/log";

    public function __construct()
    {
        $this->name = 'ps_modulebase';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'Alekuoshu';
        $this->need_instance = 1;

        /**
         * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
         */
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Ps Module Base');
        $this->description = $this->l('This module is a prestashop module base');

        $this->confirmUninstall = $this->l('Are you sure want to unistall this module?');

        $this->ps_versions_compliancy = array('min' => '1.7', 'max' => _PS_VERSION_);

        $this->module_active = (int)Configuration::get('PS_MODULEBASE_LIVE_MODE');

    }


    /**
     * Don't forget to create update methods if needed:
     * http://doc.prestashop.com/display/PS16/Enabling+the+Auto-Update
     */
    public function install()
    {
        // Install default
        if (!parent::install()) {
            self::logtxt('Error en install');
            return false;
        }
        // Install SQL
        if (!$this->addTables()) {
            self::logtxt('Error en addTables');
            return false;
        }
        // Registration hook
        if (!$this->addHooks()) {
            self::logtxt('Error en addHooks');
            return false;
        }
        // Variable of configuration
        if (!$this->addConfiguration()) {
            self::logtxt('Error en addConfiguration');
            return false;
        }
       
        return true;
    }

    public function uninstall()
    {

        // Uninstall Default
        if (!parent::uninstall()) {
            self::logtxt('Error en uninstall');
            return false;
        }
        //Uninstall DataBase
        if (!$this->deleteTables()) {
            self::logtxt('Error en deleteTables');
            return false;
        }
        //Delete variables in Configuration
        if (!$this->deleteConfiguration()) {
            self::logtxt('Error en deleteConfiguration');
            return false;
        }
        //Uninstall Hooks
        if (!$this->deleteHooks()) {
            self::logtxt('Error en deleteHooks');
            return false;
        }
        
        return true;
    }

    /**
     * Create tables when the module in install
     *
     * @return boolean
     */
    private function addTables() {
        $res = true;
        include_once (dirname(__FILE__) . '/sql/install.php');
        return $res;
    }

    /**
     * Register hooks when the module is installed
     *
     * @return boolean
     */
    private function addHooks() {

        $res = true;
        $res &= $this->registerHook('header');
        $res &= $this->registerHook('backOfficeHeader');
        $res &= $this->registerHook('displayHeader');
        return $res;
    }

    /**
     * UnRegister hooks when the module is Desinstalled
     *
     * @return boolean
     */
    private function deleteHooks() {

        $res = true;
        $res &= $this->unRegisterHook('header');
        $res &= $this->unRegisterHook('backOfficeHeader');
        $res &= $this->unRegisterHook('displayHeader');
        return $res;
    }

    /**
     * Create parameters of Configuration
     * 
     * @return boolean
     */
    private function addConfiguration() {

        if (
            Configuration::updateValue('PS_MODULEBASE_LIVE_MODE', false)
        )
            return true;
        else
            return false;
    }

    //metodo para validar las variables que vienen del form "back-end"
    private function postValidation()
    {
        /*if (!Tools::getValue('PS_MODULEBASE_')) {
            $this->_errors[] = $this->l('The field category is required');
        }*/ /*elseif (!Tools::getValue('FACTURA_SECRETAPI')) {
            $this->_errors[] = $this->l('The SECRET KEY field is required');
        } */
    }

    /**
     * Delete tables when the module is uninstall
     *
     * @return boolean
     */
    private function deleteTables() {

        $res = true;
        include_once (dirname(__FILE__) . '/sql/uninstall.php');
        return $res;
    }

    /**
     * Delete configuration's variables when the module is un-install
     *
     * @return boolean
     */
    private function deleteConfiguration() {

        if (
            Configuration::deleteByName('PS_MODULEBASE_LIVE_MODE')
        )
            return true;
        else
            return false;
    }

    /**
     * Load the configuration form
     */
    public function getContent()
    {
        /**
         * If values have been submitted in the form, process.
         */
        $output = '';
        if (((bool)Tools::isSubmit('submitPrincipal')) == true) {
            
            // valida required fields
            // $this->postValidation();

            if (!count($this->_errors)) {

                $output = $this->displayConfirmation($this->l('Settings updated'));
                $this->postProcess();

                // active functionality
                $active = Tools::getValue('PS_MODULEBASE_LIVE_MODE');
                if($active){
                    self::logtxt('Modulo activado');
                    
                }else{
                    self::logtxt('Modulo desactivado');
                }

            }else{

                foreach ($this->_errors as $error) {
                    $output .= $this->displayError($error);
                }

            }

        }

        $this->context->smarty->assign('module_dir', $this->_path);

        $output .= $this->context->smarty->fetch($this->local_path.'views/templates/admin/configure.tpl');

        return $output.$this->renderForm();
    }

    /**
     * Create the form that will be displayed in the configuration of your module.
     */
    protected function renderForm()
    {
        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitPrincipal';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            .'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFormValues(), /* Add values for your inputs */
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        return $helper->generateForm(array($this->getConfigForm()));
    }

    /**
     * Create the structure of your form.
     */
    protected function getConfigForm()
    {
        return array(
            'form' => array(
                'legend' => array(
                'title' => $this->l('Settings'),
                'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Enabled/Disabled'),
                        'name' => 'PS_MODULEBASE_LIVE_MODE',
                        'is_bool' => true,
                        'desc' => $this->l('Use this module in live mode'),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),
                    // array(
                    //     'col' => 3,
                    //     'type' => 'text',
                    //     'prefix' => '<i class="icon icon-envelope"></i>',
                    //     'desc' => $this->l('Enter a valid email address'),
                    //     'name' => 'PS_MODULEBASE_ACCOUNT_EMAIL',
                    //     'label' => $this->l('Email'),
                    // ),
                    // array(
                    //     'type' => 'password',
                    //     'name' => 'PS_MODULEBASE_ACCOUNT_PASSWORD',
                    //     'label' => $this->l('Password'),
                    // ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                ),
            ),
        );
    }

    /**
     * Set values for the inputs.
     */
    protected function getConfigFormValues()
    {
        return array(
            'PS_MODULEBASE_LIVE_MODE' => Configuration::get('PS_MODULEBASE_LIVE_MODE', true),
        );
    }

    /**
     * Save form data.
     */
    protected function postProcess()
    {
        $form_values = $this->getConfigFormValues();

        foreach (array_keys($form_values) as $key) {
            Configuration::updateValue($key, Tools::getValue($key));
        }
    }

    /*HOOKS*/
    /**
    * Add the CSS & JavaScript files you want to be loaded in the BO.
    */
    public function hookBackOfficeHeader()
    {
        // if (Tools::getValue('module_name') == $this->name) {
        //     $this->context->controller->addJS($this->_path.'views/js/back.js');
        //     $this->context->controller->addCSS($this->_path.'views/css/back.css');
        // }
    }

    /**
     * Add the CSS & JavaScript files you want to be added on the FO.
     */
    // public function hookHeader()
    // {
    //     $this->context->controller->addJS($this->_path.'views/js/front.js');
    //     $this->context->controller->addCSS($this->_path.'views/css/front.css');
    // }

    public function hookDisplayHeader()
    {
        // active functionality
        if($this->module_active){
            $this->context->controller->addJS($this->_path.'views/js/front.js');
            $this->context->controller->addCSS($this->_path.'views/css/front.css');
         }   
    }

    /*GENERAL FUNCTIONS*/
    /*Function for error log*/
    public static function logtxt($text = "") {

        if (file_exists(self::PATH_LOG)) {

            $fp = fopen(self::PATH_LOG . "/errors.log", "a+");
            fwrite($fp, date('l jS \of F Y h:i:s A') . ", " . $text . "\r\n");
            fclose($fp);
            return true;
        } else {
            self::createPath(self::PATH_LOG);
        }
    }

    /*Recursively create a string of directories*/
    public static function createPath($path) {

        if (is_dir($path))
            return true;

        $prev_path = substr($path, 0, strrpos($path, '/', -2) + 1);
        $return = self::createPath($prev_path);
        return ($return && is_writable($prev_path)) ? mkdir($path) : false;
    }

    /**
     * Function for set redirections with notifications
     */
    public function redirectWithNotifications()
    {
        $notifications = json_encode(array(
            'error' => $this->errors,
            'warning' => $this->warning,
            'success' => $this->success,
            'info' => $this->info,
        ));

        if (session_status() == PHP_SESSION_ACTIVE) {
            $_SESSION['notifications'] = $notifications;
        } elseif (session_status() == PHP_SESSION_NONE) {
            session_start();
            $_SESSION['notifications'] = $notifications;
        } else {
            setcookie('notifications', $notifications);
        }

        return call_user_func_array(array('Tools', 'redirect'), func_get_args());
    }

}