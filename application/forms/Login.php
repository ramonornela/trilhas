<?php
/**
 * Trilhas - Learning Management System
 * Copyright (C) 2005-2010  Preceptor Educação a Distância Ltda. <http://www.preceptoead.com.br>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * @category   Application
 * @package    Application_Form
 * @copyright  Copyright (C) 2005-2010  Preceptor Educação a Distância Ltda. <http://www.preceptoead.com.br>
 * @license    http://www.gnu.org/licenses/  GNU GPL
 */
class Application_Form_Login extends Zend_Form
{
    /**
     * (non-PHPdoc)
     * @see Zend_Form#init()
     */
    public function init()
    {
		$session = new Zend_Session_Namespace('data');
		
        $this->setAction('user/login')
             ->setMethod('post');

        $email = new Zend_Form_Element_Text('email');
        $email->setRequired()
              ->setLabel('Email')
              ->addFilters(array('StringTrim', 'StripTags'));

        $password = new Zend_Form_Element_Password('password');
        $password->setRequired()
                 ->setLabel('Password')
                 ->addFilters(array('StringTrim', 'StripTags'));
		
		if (isset($session->attempt) && $session->attempt >= ATTEMPTS) {
			$captcha = new Zend_Form_Element_Captcha('foo', array(
			    'captcha' => array(
			        'captcha' => 'Figlet',
			        'wordLen' => 4,
			        'timeout' => 300,
			    ),
			));
			/**
			 * @TODO Implementar translate
			 */
			//$captcha->setLabel('enter the code');
			$captcha->setLabel('Informe o código');
		}
		
        $this->addElement($email)
             ->addElement($password);
		if  (isset($session->attempt) && $session->attempt >= ATTEMPTS) {
			$this->addElement($captcha);
		}
        $this->addElement('submit', 'Login');
   }
}
