<?php
/*
* 2007-2014 PrestaShop 
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
*         DISCLAIMER   *
* *************************************** */
/* Do not edit or add to this file if you wish to upgrade Prestashop to newer
* versions in the future.
* ****************************************************
* @category   Opensum
* @package    popupbox
* @author    vivek kumar tripathi <vivek@opensum.com>
* @site    http://www.opensum.com
* @copyright  Copyright (c) 2010 - 2012 Opensum.com (http://www.opensum.com)
*/

if (!defined('_PS_VERSION_')){
	exit;
}

class os_popupbox extends Module
{
	private $_html;
	public function __construct(){
		$this->name = 'os_popupbox';
		$this->tab = 'front_office_features';
		$this->version = '1.0.0';
		$this->author = 'vivek kumar tripathi';
		$this->need_instance = 0;
                $this->bootstrap = true;

		parent::__construct();
		
		$this->displayName = $this->l('home page popupbox');
		$this->description = $this->l('Display popup box/ banner for home page');
	}

	public function install(){
		if (!parent::install() OR
			!$this->registerHook('displayHome') OR
			!$this->registerHook('header')
		) {
			return false;
		}
		$this->updatePosition(Hook::get('home'), 0, 1);
		return true;
	}
	
	public function uninstall(){
		if (!parent::uninstall())
			return false;
		return true;
	}
	
	function hookDisplayHome($params){  
		global $smarty;
                 $this->context->controller->addJS(__PS_BASE_URI__ . 'modules/os_popupbox/js/jquery.uilock.js');
		$os_popupbox_data = Configuration::get('os_popupbox_data') ;
		$os_popupbox_reopentime = Configuration::get('os_popupbox_reopentime')!=''?Configuration::get('os_popupbox_reopentime'):1 ;
		$smarty->assign(array(
			'os_popupbox_reopentime' => $os_popupbox_reopentime	,		
			'os_popupbox_data' => $os_popupbox_data			
		));
            return $this->display(__FILE__, '/views/templates/front/os_popupbox.tpl');		
	}
	
	public function getContent(){
		global $smarty, $cookie;
		if(Tools::isSubmit('submitUpdate')) {
			Configuration::updateValue('os_popupbox_reopentime', Tools::getValue('os_popupbox_reopentime'));
			Configuration::updateValue('os_popupbox_data', Tools::getValue('os_popupbox_data'),true);
			
			$smarty->assign(array(
				'save_ok' => true
			));
		}
		$this->_html .= $this->_displayForm();
		return $this->_html;
	}
        public function getConfigFieldsValues()
	{
         return array(
               'os_popupbox_reopentime' => Tools::getValue('os_popupbox_reopentime',  Configuration::get('os_popupbox_reopentime')),
               'os_popupbox_data' => Tools::getValue('os_popupbox_data', Configuration::get('os_popupbox_data'))
                );
	}
        private function _displayForm()
        {
            $fields_form = array(
			'form' => array(
				'legend' => array(
					'title' => $this->l('Settings'),
					'icon' => 'icon-cogs'
				),
				'input' => array(
				    array(
                                            'type' => 'text',
                                            'label' => $this->l('Reopen time in(Hour)'),
                                            'name' => 'os_popupbox_reopentime',
                                            'class'=>'fixed-width-xs',
                                            'desc' => $this->l('Put time in hour after what time you want to appear that popup box on the page')
					),
                                       array(
                                            'type' => 'textarea',
                                            'label' => $this->l('Popup box code'),
                                            'name' => 'os_popupbox_data',
                                            'desc' => $this->l(''),'class' => 'rte',
                                            'autoload_rte' => true
                                        )
				),
				'submit' => array(
					'title' => $this->l('Save')
				)
			),
		);
		
		$helper = new HelperForm();
		$helper->show_toolbar = false;
		$helper->table =  $this->table;
		$lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
		$helper->default_form_language = $lang->id;
		$helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
		$this->fields_form = array();

		$helper->identifier = $this->identifier;
		$helper->submit_action = 'submitUpdate';
		$helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
		$helper->token = Tools::getAdminTokenLite('AdminModules');
		$helper->tpl_vars = array(
			'fields_value' => $this->getConfigFieldsValues(),
			'languages' => $this->context->controller->getLanguages(),
			'id_language' => $this->context->language->id
		);
		$this->_html .=  $helper->generateForm(array($fields_form));
        }
	public function checkMobileDevice(){
		if (preg_match('/(alcatel|amoi|android|avantgo|blackberry|benq|cell|cricket|docomo|elaine|htc|iemobile|iphone|ipaq|ipod|j2me|java|midp|mini|mmp|mobi\s|motorola|nec-|nokia|palm|panasonic|philips|phone|sagem|sharp|sie-|smartphone|sony|symbian|t-mobile|telus|up\.browser|up\.link|vodafone|wap|webos|wireless|xda|zte)/i', $_SERVER['HTTP_USER_AGENT'], $out))
			return true;
	}
}
