<?php

class Setup
{
    // reference to core
    private $_referenceCore = null;

    // response to ajax request
    private $_response;

    function __construct($reference_core)
    {
        $this->_referenceCore = $reference_core;

        $this->_response = $this->_init();
    }

    private function _init()
    {
        $action = strip_tags($_POST['action']);

        switch ($action) {
            case 'checkstate':
                return $this->_checkstate();
                break;
            case 'savecredentials':
                return $this->_savecredentials();
                break;
            case 'login':
                return $this->_doLogin();
                break;
        }
    }

    private function _doLogin()
    {
        $username = strip_tags($_POST['username']);
        $password = strip_tags($_POST['password']);

        $error = '';

        $coreConfig = $this->_referenceCore->getPathBase().'Data/Config/Config.ini.php';

        $ini = $this->_referenceCore->getModuleHandle('ini');
        $phpass = $this->_referenceCore->getModuleHandle('phpass');

        if (file_exists($coreConfig)) {
            $parsedConfig = $ini->read($coreConfig);

            // check for correct password
            $check_username = $phpass->CheckPassword($username, $parsedConfig['ADMIN']['CONF_ADMIN_USERNAME']);
            $check_password = $phpass->CheckPassword($password, $parsedConfig['ADMIN']['CONF_ADMIN_PASSWORD']);

            if (!$check_username || !$check_password) {
                $error = 'Login credentials incorrect! Please try again...';
            } else {

                $auth = $this->_referenceCore->getModuleHandle('auth');

                $auth_array = array(
                    'store'     => 'file',
                    'action'    => 'create',
                    'user'      => $username,
                    'logintime' => time(),
                    'lifetime'  => '1800'
                );

                $ok = $auth->dispatch($auth_array);

                if (!$ok) {
                    $error = 'Authorization creation failed: Error::'.$auth->getLastError();
                }
            }
        } else {
            $error = 'config file does not exist!';
        }

        // return result of processing
        return $error ? '{success:false, error:'.json_encode($error).'}' : '{success:true}';
    }

    private function _savecredentials()
    {
        $error = '';

        $coreConfig = $this->_referenceCore->getPathBase().'Data/Config/Config.ini.php';

        // core config should not exist
        if (!file_exists($coreConfig)) {
            // try editing core config and save
            $ini = $this->_referenceCore->getModuleHandle('ini');
            $phpass = $this->_referenceCore->getModuleHandle('phpass');

            $parsedConfig = $ini->read('core_config.ini.default.php');

            $parsedConfig['ADMIN']['CONF_ADMIN_USERNAME'] =  $phpass->HashPassword(strip_tags($_POST['username']));
            $parsedConfig['ADMIN']['CONF_ADMIN_PASSWORD'] = $phpass->HashPassword(strip_tags($_POST['password']));
            $parsedConfig['ADMIN']['CONF_ADMIN_EMAIL'] = strip_tags($_POST['email']);
            $parsedConfig['PRIVATE_KEY']['CONF_PRIVATE_KEYPHRASE'] = strip_tags($_POST['privatekey']);

            $ini->write($this->_referenceCore->getPathBase().'Data/Config/Config.ini.php', $parsedConfig);

        } else {
            $error = 'config file allready exists! delete core_config.ini.php and try again!';
        }

        // return result of processing
        return $error ? '{success:false, error:'.json_encode($error).'}' : '{success:true}';
    }

    private function _checkstate()
    {
        $coreConfig = $this->_referenceCore->getPathBase().'Data/Config/Config.ini.php';

        if (file_exists($coreConfig)) {

            // get handle on ini parser
            $ini = $this->_referenceCore->getModuleHandle('ini');

            $parsedConfig = $ini->read($coreConfig);

            if (empty($parsedConfig)) {
                return 0;
            } else {
                return 1;
            }
        } else {
            return 0;
        }
    }

    public function dispatch()
    {
        return $this->_response;
    }
}

?>