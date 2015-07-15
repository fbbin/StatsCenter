<?php
namespace App\Controller;
use Swoole;

class Url_shortener extends \App\LoginController
{
    private function display_edit_page($category_id = null, $name = null, $url = null, $error = null)
    {
        $form['name'] = \Swoole\Form::input('name', $name);
        $form['url'] = \Swoole\Form::input('url', $url);

        $projects = table('project')->gets(array(
            'select' => 'id, name',
            'order' => 'id desc'
        ));
        $projects = array_rebuild($projects, 'id', 'name');

        $categories = table('tiny_url_category')->gets(array(
            'select' => 'id, name, project_id',
            'order' => 'project_id asc',
            'where' => 'status = 1',
        ));
        $category_options = array();
        foreach ($categories as $category)
        {
            $option = '';

            if (!isset($projects[$category['project_id']])) {
                continue;
            }

            $option .= $projects[$category['project_id']];
            $option .= ' - ';
            $option .= $category['name'];
            $category_options[$category['id']] = $option;
        }
        $form['category_id'] = \Swoole\Form::select(
            'category_id',
            $category_options,
            $category_id,
            null,
            array('class' => 'select2 select2-offscreen', 'style' => 'width:100%')
        );

        $this->assign('error', $error);
        $this->assign('form', $form);
        $this->display('url_shortener/edit.php');
    }

    function add()
    {
        if (empty($_POST))
        {
            return $this->display_edit_page();
        }
        else
        {
            $error = '';

            $category_id = isset($_POST['category_id'])
                ? (int) $_POST['category_id']
                : '';
            $name = isset($_POST['name']) ? trim($_POST['name']) : '';
            $url = isset($_POST['url']) ? trim($_POST['url']) : '';

            if (empty($category_id))
            {
                $error = '所属分类是必需的！';
            }
            elseif ($name === '')
            {
                $error = '名称是必需的！';
            }
            elseif ($url === '')
            {
                $error = '网址是必需的！';
            }
            elseif (!is_valid_url($url))
            {
                $error = '网址格式不正确！';
            }

            if (!empty($error)) {
                return $this->display_edit_page($category_id, $name, $url, $error);
            }

            $inserts['name'] = $name;
            $inserts['category_id'] = $category_id;

            $tiny_url_id = table('tiny_url')->put($inserts);

            if ($tiny_url_id)
            {
                $msg = '添加成功';

                $res = $this->redis->hSet('tiny-url:url', ShortUrl::encode($tiny_url_id), $url);
                if (!$res) {
                    // log
                }
            }
            else
            {
                $msg = '添加失败';
            }

            return \Swoole\JS::js_goto($msg, '/url_shortener/tiny_url_list');
        }
    }

    function edit()
    {
        $form['name'] = \Swoole\Form::input('name');
        $form['url'] = \Swoole\Form::input('url');
        $form['project_id'] = \Swoole\Form::select('project', array(), '', null, array('class' => 'select2 select2-offscreen', 'style' => 'width:100%'));

        $this->assign('form', $form);
        $this->display();
    }

    function tiny_url_list()
    {
        $this->display();
    }

    private function display_edit_category_page($title = '', $project_id = null, $name = null)
    {
        $projects = table('project')->gets(array(
            'select' => 'id, name',
            'order' => 'id desc'
        ));
        $projects = array_rebuild($projects, 'id', 'name');

        $form['project_id'] = \Swoole\Form::select(
            'project_id',
            $projects,
            htmlspecialchars($project_id),
            null,
            array('class' => 'select2 select2-offscreen', 'style' => 'width:100%')
        );

        $form['name'] = \Swoole\Form::input('name', htmlspecialchars($name));

        $this->assign('title', '编辑短链接分类');
        $this->assign('form', $form);
        $this->display('url_shortener/edit_category.php');
    }

    // 增加短链接分类
    function add_category()
    {
        if (empty($_POST))
        {
            $this->display_edit_category_page('新增短链接分类');
        }
        else
        {
            $inserts['name'] = trim($_POST['name']);
            $inserts['project_id'] = (int) $_POST['project_id'];

            $res = table('tiny_url_category')->put($inserts);
            $msg = $res ? '添加成功' : '添加失败';
            \Swoole\JS::js_goto($msg, '/url_shortener/category_list');
        }
    }

    function edit_category()
    {
        if (!isset($_GET['id']))
        {
            $this->http->status(404);
            return;
        }

        $id = (int) $_GET['id'];
        $project_id = null;
        $name = null;

        if (!empty($_POST) && isset($_POST['project_id']) && isset($_POST['name']))
        {
            $sets = array();

            $project_id = (int) $_POST['project_id'];
            $sets['project_id'] = $project_id;

            $name = trim($_POST['name']);
            $sets['name'] = $name;

            $res = table('tiny_url_category')->set($id, $sets);
            $msg = $res ? '操作成功' : '操作失败';
            \Swoole\JS::js_goto($msg, "/url_shortener/edit_category?id={$id}");
        }

        $gets = array('where' => array());
        $gets['where'][] = "id = {$id}";
        $gets['where'][] = 'status = 1';

        $category_list = table('tiny_url_category')->gets($gets);
        $category = !empty($category_list[0]) ? $category_list[0] : null;

        if (empty($category))
        {
            $this->http->status(404);
            return;
        }
        $project_id = $category['project_id'];
        $name = $category['name'];

        $this->display_edit_category_page('编辑短链接分类', $project_id, $name);
    }

    function delete_category()
    {
        if (isset($_GET['id']))
        {
            $id = (int) $_GET['id'];
            $res = table('tiny_url_category')->set($id, array('status' => 2));
            $msg = $res ? '操作成功' : '操作失败';
            \Swoole\JS::js_goto($msg, '/url_shortener/category_list');
        } else {
            $this->http->status(302);
            $this->http->header('Location', '/url_shortener/category_list');
        }
    }

    function category_list()
    {
        $gets = array();

        $id = trim(get_post('id'));
        if ($id !== '')
        {
            $id = (int) $id;
            $gets['where'][] = "id={$id}";
            $_GET['id'] = $id;
        }
        else
        {
            $id = null;
        }

        $name = trim(get_post('name'));
        if ($name !== '')
        {
            $name = trim($name);
            $gets['where'][] = "name like '%{$name}%'";
            $_GET['name'] = $name;
        }
        else
        {
            $name = null;
        }

        $project_id = trim(get_post('project_id'));
        if ($project_id !== '')
        {
            $project_id = (int) $project_id;
            $gets['where'][] = "project_id = {$project_id}";
            $_GET['project_id'] = $project_id;
        }
        else
        {
            $project_id = null;
        }

        $projects = table('project')->gets(array(
            'select' => 'id, name',
            'order' => 'id desc'
        ));
        $projects = array_rebuild($projects, 'id', 'name');

        // 搜索表单
        $form['id'] = \Swoole\Form::input('id', htmlspecialchars($id), array(
            'id' => 'id',
            'placeholder' => 'ID',
        ));
        $form['name'] = \Swoole\Form::input('name', htmlspecialchars($name), array(
            'id' => 'name',
            'placeholder' => '分类名称'
        ));
        $form['project_id'] = \Swoole\Form::select(
            'project_id',
            $projects,
            htmlspecialchars($project_id),
            null,
            array('class' => 'select2', 'style' => 'width:100%')
        );

        $gets['order'] = 'id desc';
        $gets['page'] = !empty($_GET['page']) ? (int) $_GET['page'] : 1;
        $gets['pagesize'] = 20;
        $gets['where'] = 'status = 1';
        $data = table('tiny_url_category')->gets($gets, $pager);

        foreach ($data as $k => $v)
        {
            $data[$k]['project_name'] = !empty($projects[$v['project_id']])
                ? $projects[$v['project_id']]
                : null;
        }

        $this->assign('form', $form);
        $this->assign('data', $data);
        $this->assign('pager', array('render' => $pager->render()));

        $this->display();
    }
}

class ShortUrl
{
    // 53 个字母数字乱序
    const ALPHABET = '4sF8y2KPuzRHixUtfGX3gcCTLhnASMe65NjpBw9YWDqbamEkQrd7J';
    const BASE = 53; // strlen(self::ALPHABET)
    const OFFSET = 10000;

    public static function encode($num)
    {
        $num = $num + self::OFFSET;
        $str = '';
        while ($num > 0)
        {
            $str = substr(self::ALPHABET, ($num % self::BASE), 1) . $str;
            $num = floor($num / self::BASE);
        }
        return $str;
    }
    public static function decode($str)
    {
        $num = 0;
        $len = strlen($str);
        for ($i = 0; $i < $len; $i++)
        {
            $num = $num * self::BASE + strpos(self::ALPHABET, $str[$i]);
        }
        $num = $num - self::OFFSET;
        return $num;
    }
}

function array_rebuild($array, $key, $value = '')
{
    $r = array();

    foreach ($array as $k => $v)
    {
        $r[$v[$key]] = $value ? $v[$value] : $v;
    }

    return $r;
}

function get_post($key)
{
    if (isset($_POST[$key]))
    {
        return $_POST[$key];
    }
    elseif (isset($_GET[$key]))
    {
        return $_GET[$key];
    }
    else
    {
        return false;
    }
}

function is_valid_url($url)
{
    return (bool) preg_match('@^(https?|ftp)://[^\s/$.?#].[^\s]*$@iS', $url);
}

