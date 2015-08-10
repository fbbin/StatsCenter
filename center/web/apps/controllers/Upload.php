<?php
namespace App\Controller;
use Swoole;

class Upload extends \App\LoginController
{
    function add()
    {
        if (empty($_POST))
        {
            $this->display('upload/edit.php');
        }
        else
        {
            if (!isset($_POST['url_list']) || !is_array($_POST['url_list']))
            {
                exit($this->json('', 1, 'Param url_list is needed.'));
            }
            $url_list = $_POST['url_list'];

            $res = model('Files')->add_if_not_exists($url_list);
            if ($res === false)
            {
                exit($this->json('', 1, 'Add files failed.'));
            }

            exit($this->json('', 0, 'Success'));
        }
    }
}
