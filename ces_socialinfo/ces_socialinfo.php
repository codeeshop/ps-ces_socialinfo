<?php
/**
* 2007-2019 PrestaShop
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
*  @copyright 2007-2019 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

class Ces_socialinfo extends Module
{
    protected $config_form = false;

    public function __construct()
    {
        $this->name = 'ces_socialinfo';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'codeeshop';
        $this->need_instance = 0;

        /**
         * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
         */
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Social Contact Information');
        $this->description = $this->l('Write your Social Contact details');

        $this->confirmUninstall = $this->l('are you sure ?');

        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
    }

    /**
     * Don't forget to create update methods if needed:
     * http://doc.prestashop.com/display/PS16/Enabling+the+Auto-Update
     */
    public function install()
    {
        Configuration::updateValue('CES_SOCIALINFO_LIVE_MODE', false);

        return parent::install() &&
            $this->registerHook('header')
            && $this->registerHook('displayNav2')
            && $this->registerHook('displayFooter')
            && $this->registerHook('backOfficeHeader');
    }

    public function uninstall()
    {
        Configuration::deleteByName('CES_SOCIALINFO_LIVE_MODE');
        Configuration::deleteByName('CES_SOCIALINFO_FACEBOOK');
        Configuration::deleteByName('CES_SOCIALINFO_LINKEDIN');
        Configuration::deleteByName('CES_SOCIALINFO_TWITTER');
        Configuration::deleteByName('CES_SOCIALINFO_YOUTUBE');
        Configuration::deleteByName('CES_SOCIALINFO_INSTA');
        Configuration::deleteByName('CES_SOCIALINFO_GITHUB');

        return parent::uninstall();
    }

    /**
     * Load the configuration form
     */
    public function getContent()
    {
        /**
         * If values have been submitted in the form, process.
         */
        if (((bool)Tools::isSubmit('submitCes_socialinfoModule')) == true) {
            $this->postProcess();
        }

        $this->context->smarty->assign('module_dir', $this->_path);

        
        return $this->renderForm();
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
        $helper->submit_action = 'submitCes_socialinfoModule';
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
                        'label' => $this->l('Live mode'),
                        'name' => 'CES_SOCIALINFO_LIVE_MODE',
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
                    array(
                        'col' => 4,
                        'type' => 'text',
                        'prefix' => '<i class="icon icon-facebook-sign"></i>',
                        'name' => 'CES_SOCIALINFO_FACEBOOK',
                        'label' => $this->l('Facebook'),
                    ),
                    array(
                        'col' => 4,
                        'type' => 'text',
                        'prefix' => '<i class="icon icon-linkedin-sign"></i>',
                        'name' => 'CES_SOCIALINFO_LINKEDIN',
                        'label' => $this->l('Linkedin'),
                    ),
                    array(
                        'col' => 4,
                        'type' => 'text',
                        'prefix' => '<i class="icon icon-twitter"></i>',
                        'name' => 'CES_SOCIALINFO_TWITTER',
                        'label' => $this->l('Twitter'),
                    ),
                    array(
                        'col' => 4,
                        'type' => 'text',
                        'prefix' => '<i class="icon icon-youtube-sign"></i>',
                        'name' => 'CES_SOCIALINFO_YOUTUBE',
                        'label' => $this->l('Youtube'),
                    ),
                    array(
                        'col' => 4,
                        'type' => 'text',
                        'prefix' => '<i class="icon icon-instagram"></i>',
                        'name' => 'CES_SOCIALINFO_INSTA',
                        'label' => $this->l('Instagram'),
                    ),
                                        array(
                        'col' => 4,
                        'type' => 'text',
                        'prefix' => '<i class="icon icon-github-sign"></i>',
                        'name' => 'CES_SOCIALINFO_GITHUB',
                        'label' => $this->l('Github'),
                    ),

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
            'CES_SOCIALINFO_LIVE_MODE' => Configuration::get('CES_SOCIALINFO_LIVE_MODE', true),
            'CES_SOCIALINFO_FACEBOOK' => Configuration::get('CES_SOCIALINFO_FACEBOOK', ''),
            'CES_SOCIALINFO_LINKEDIN' => Configuration::get('CES_SOCIALINFO_LINKEDIN', ''),
            'CES_SOCIALINFO_TWITTER' => Configuration::get('CES_SOCIALINFO_TWITTER', ''),
            'CES_SOCIALINFO_YOUTUBE' => Configuration::get('CES_SOCIALINFO_YOUTUBE', ''),
            'CES_SOCIALINFO_INSTA' => Configuration::get('CES_SOCIALINFO_INSTA', ''),
            'CES_SOCIALINFO_GITHUB' => Configuration::get('CES_SOCIALINFO_GITHUB', ''),
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

    /**
    * Add the CSS & JavaScript files you want to be loaded in the BO.
    */
    public function hookBackOfficeHeader()
    {
        if (Tools::getValue('module_name') == $this->name) {
            $this->context->controller->addJS($this->_path.'views/js/back.js');
            $this->context->controller->addCSS($this->_path.'views/css/back.css');
        }
    }

    /**
     * Add the CSS & JavaScript files you want to be added on the FO.
     */
    public function hookHeader()
    {
        $this->context->controller->addCSS($this->_path.'/views/css/front.css');
    }

    //
    public function hookDisplayNav1() 
    {
        if(!Configuration::get('CES_SOCIALINFO_LIVE_MODE')){
            return;
        }
        $social_name = array('', 'facebook', 'linkedin', 'twitter', 'youtube', 'instagram', 'github');
        $social_icons = array();
        $i=0;
        foreach ($this->getConfigFormValues() as $key => $value) {
                $social_icons[] = array('name' => $social_name[$i],
                                        'value' => $value
                                    );
            $i++;
        }   
        
        $this->context->smarty->assign(array('values' => $social_icons,
                                                'module_url' => _PS_BASE_URL_.$this->_path
                                        ));
        return $this->context->smarty->fetch($this->local_path.'views/templates/front/socialinfo.tpl');
    }
}
