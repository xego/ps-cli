<?php

/*
 * 2016 Xego
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
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
 * @author Matteo Perego <matteo dot perego AT xego dot it>
 * @copyright 2016 Xego
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
*/

#
#
#	Customer functions
#	  - list_customer
#

class PS_CLI_Customer extends PS_CLI_Plugin
{
    protected function __construct()
    {
        $command = new PS_CLI_Command('customer', 'Manage PrestaShop customers');
        $command->addOpt('list', 'List customers', false, 'boolean');
        $command->addOpt('anonimize', 'anonimize customers', false, 'boolean');

        $this->register_command($command);
    }

    public function run()
    {
        $arguments = PS_CLI_Arguments::getArgumentsInstance();
        $interface = PS_CLI_Interface::getInterface();

        $status = null;

        if ($arguments->getOpt('list', false)) {
            $status = $this->list_customers();
				}
        elseif ($arguments->getOpt('anonimize', false)) {
            $status = $this->anonimize_customers();
        } else {
            $arguments->show_command_usage('customer');
            exit(1);
        }

        if ($status === false) {
            exit(1);
        }

        exit(0);
    }

    public static function list_customers($lang = null)
    {

        // TODO: check if lang exists before using it
        if ($lang === null) {
            $lang = Configuration::get('PS_LANG_DEFAULT');
        }

        $customers = Customer::getCustomers();

        $table = new cli\Table();
        $table->setHeaders(array(
            'ID',
            'email',
            'First name',
            'Last name',
            )
        );

        foreach ($customers as $customer) {

            // print_r($customer);

                $table->addRow(array(
                    $customer['id_customer'],
                    $customer['email'],
                    $customer['firstname'],
                    $customer['lastname'],
                    )
                );
        }

        $table->display();
    }

    public static function anonimize_customers()
    {
        $customers = Customer::getCustomers();

        foreach ($customers as $customer) {
						// print_r($customer);
						$anon_customer = new Customer($customer["id_customer"]);
            $anon_customer->email = md5($customer['email']).'@example.com';
            $res = $anon_customer->update();

            if ($res) {
                echo "Successfully updated user " . $anon_customer->id . "\n";
            } else {
                echo "Error, could not update user " . $customer['id_customer'] . "\n";
                return false;
            }
        }

    }
}

PS_CLI_Configure::register_plugin('PS_CLI_Customer');
