<?php
namespace App;

use Swoole\Client\CURL;

class CrowdUser
{
    const BASE_URL = 'http://10.10.1.30:8095/crowd/rest/';

    static function setPassword($username, $password)
    {
        $curl = new CURL(true);
        $curl->setHeader('Content-Type', 'application/json');
        $curl->setHeader('Accept', 'application/json');
        $curl->setCredentials('eclicks_confluence', 'eclicks0716');
        $curl->setMethod('PUT');
        $curl->setHeaderOut();
        $res = $curl->post(self::BASE_URL . 'usermanagement/1/user/password?username=' . $username, json_encode([
            'value' => $password,
        ]));
        if ($curl->httpCode != 204)
        {
            var_dump($curl->info);
            return false;
        }
        return true;
    }
}