<?php
namespace App\Controller;
use Swoole;
use App\CdnMgr;

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
            if (!is_array($url_list))
            {
                exit($this->json('', 1, '上传失败'));
            }

            $url_list_success = array();
            foreach ($url_list as $row)
            {
                $url = json_decode($row);

                if (empty($url->file) || empty($url->md5))
                {
                    continue;
                }

                $url_list_success[] = $url;
            }

            $res = model('Files')->addFiles($url_list_success);
            if ($res === false)
            {
                exit($this->json('', 1, 'Add files failed.'));
            }

            exit($this->json('', 0, 'Success'));
        }
    }

    function delete()
    {
        if (isset($_GET['id']))
        {
            $id = (int) $_GET['id'];
            $res = model('Files')->del($id);

            $msg = $res ? '操作成功' : '操作失败';
            \Swoole\JS::js_goto($msg, '/upload/file_list');
        }
        else
        {
            $this->http->status(302);
            $this->http->header('Location', '/upload/file_list');
        }
    }

    function file_list()
    {
        $data = array();

        $data = model('Files')->gets(
            array('where' => array('1 = 1'), 'order' => 'add_time DESC'),
            $pager
        );

        if ($data === false)
        {
            $data = array();
        }

        $this->assign('data', $data);
        $this->display('upload/file_list.php');
    }

    function ucdn_prefetch_domain_cache()
    {
        if (!isset($_GET['id']) || !isset($_GET['file']) || !isset($_GET['md5']))
        {
            exit('Forbidden.');
        }
        $id = (int) $_GET['id'];
        $file = $_GET['file'];
        $md5 = $_GET['md5'];
        if (strlen($md5) != 32)
        {
            return \Swoole\JS::js_goto('md5不存在', '/upload/file_list');
        }

        $cdn = new CdnMgr();
        $params = array();
        $params['UrlList.0'] = $file;
        $params['Md5'] = $md5;
        $res = $cdn->exec('PrefetchDomainCache', $params, 3);

        if (empty($res['TaskId']))
        {
            return \Swoole\JS::js_goto('同步到CDN失败', '/upload/file_list');
        }

        $res = table('files')->set($id, array(
            'status' => 1,
            'ucdn_task_id' => $res['TaskId']
        ));

        if ($res)
        {
            return \Swoole\JS::js_goto('同步到CDN成功', '/upload/file_list');
        }
        else
        {
            return \Swoole\JS::js_goto('同步到CDN失败', '/upload/file_list');
        }
    }

    function ucdn_query_prefetch_cache_status()
    {
        if (!isset($_GET['id']) || !isset($_GET['task_id']))
        {
            exit('Forbidden.');
        }

        $id = (int) $_GET['id'];
        $taskId = $_GET['task_id'];

        $cdn = new CdnMgr();
        $params = array();
        $params['TaskId'] = $taskId;
        $res = $cdn->exec('DescribePrefetchCacheTask', $params, 3);

        if (!empty($res['TaskSet'][0]['Status']))
        {
            $status = $res['TaskSet'][0]['Status'];
        }
        else
        {
            $status = 'unknown';
        }

        if ($status == 'failure')
        {
            $res = table('files')->set($id, array(
                'status' => 0,
            ));

            if ($res)
            {
                return \Swoole\JS::js_goto('CDN同步失败，请重新同步', '/upload/file_list');
            }
        }

        if ($status == 'success')
        {
            $res = table('files')->set($id, array(
                'status' => 2,
            ));

            if ($res)
            {
                return \Swoole\JS::js_goto('CDN同步成功', '/upload/file_list');
            }
        }

        return \Swoole\JS::js_goto('CDN同步中，请耐心等候', '/upload/file_list');
    }

    function ucdn_describe_prefetch_cache_task()
    {
        if (!isset($_GET['task_id']))
        {
            exit('Forbidden.');
        }
        $task_id =$_GET['task_id'];

        $cdn = new CdnMgr();
        $params = array();
        $params['TaskId'] = $task_id;
        $res = $cdn->exec('DescribePrefetchCacheTask', $params, 3);

        var_dump($res);
    }
}
