<?php
namespace App\Controller;
use Swoole;
use App\ShortUrl;

class Url_shortener extends \App\LoginController
{
    private function display_edit_page($title, $category_id = null, $name = null, $url = null, $error = null)
    {
        $form['name'] = \Swoole\Form::input('name', $name);
        $form['url'] = \Swoole\Form::input('url', $url);

        $category_options = model('Url_shortener')->get_category_list();

        $form['category_id'] = \Swoole\Form::select(
            'category_id',
            $category_options,
            $category_id,
            null,
            array('class' => 'select2 select2-offscreen', 'style' => 'width:100%')
        );

        $this->assign('title', $title);
        $this->assign('error', $error);
        $this->assign('form', $form);
        $this->display('url_shortener/edit.php');
    }

    private function edit_check(&$category_id, &$name, &$url, &$error)
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
    }

    function add()
    {
        if (empty($_POST))
        {
            return $this->display_edit_page('新增短网址');
        }
        else
        {
            $this->edit_check($category_id, $name, $url, $error);
            if (!empty($error)) {
                return $this->display_edit_page('新增短网址', $category_id, $name, $url, $error);
            }

            $inserts['name'] = $name;
            $inserts['category_id'] = $category_id;

            $tiny_url_id = table('tiny_url')->put($inserts);

            if ($tiny_url_id)
            {
                $msg = '添加成功';

                $res = $this->redis('cluster')->hSet('tiny-url:url', ShortUrl::encode($tiny_url_id), $url);
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
        if (!isset($_GET['id']))
        {
            $this->http->status(404);
            return;
        }

        $id = (int) $_GET['id'];
        $tiny_url = model('Url_shortener')->get_tiny_url_by_id($id);
        if (empty($tiny_url))
        {
            $this->http->status(404);
            return;
        }

        $category_id = $tiny_url['category_id'];
        $name = $tiny_url['name'];
        $url = $tiny_url['url'];

        if (empty($_POST))
        {
            return $this->display_edit_page('编辑短网址', $category_id, $name, $url);
        }
        else
        {
            $this->edit_check($category_id, $name, $url, $error);
            if (!empty($error))
            {
                return $this->display_edit_page('编辑短网址', $category_id, $name, $url, $error);
            }

            $res = model('Url_shortener')->update_tiny_url_by_id($id, $category_id, $name, $url);
            $msg = $res ? '操作成功' : '操作失败';
            \Swoole\JS::js_goto($msg, "/url_shortener/edit?id={$id}");
        }
    }

    function tiny_url_list()
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

        $category_id = trim(get_post('category_id'));
        if ($category_id !== '')
        {
            $category_id = (int) $category_id;
            $gets['where'][] = "category_id = {$category_id}";
            $_GET['category_id'] = $category_id;
        }
        else
        {
            $category_id = null;
        }

        $category_options = model('Url_shortener')->get_category_list();

        // 搜索表单
        $form['id'] = \Swoole\Form::input('id', htmlspecialchars($id), array(
            'id' => 'id',
            'placeholder' => 'ID',
        ));
        $form['name'] = \Swoole\Form::input('name', htmlspecialchars($name), array(
            'id' => 'name',
            'placeholder' => '分类名称'
        ));
        $form['category_id'] = \Swoole\Form::select(
            'category_id',
            $category_options,
            $category_id,
            null,
            array('class' => 'select2 select2-offscreen', 'style' => 'width:100%')
        );

        $gets['order'] = 'id desc';
        $gets['page'] = !empty($_GET['page']) ? (int) $_GET['page'] : 1;
        $gets['pagesize'] = 20;
        $gets['where'][] = 'status = 1';
        $data = table('tiny_url')->gets($gets, $pager);

        $tiny_url_id_list = array();
        foreach ($data as &$row)
        {
            $tiny_url_id_list[] = (int) $row['id'];

            if (isset($category_options[$row['category_id']]))
            {
                $row['category_name'] = $category_options[$row['category_id']];
            }
            else
            {
                $row['category_name'] = '';
            }
        }
        unset($row);

        $symbol_list = model('Url_shortener')->get_symbol_list_by_id_list($tiny_url_id_list);
        // 访问次数
        $visits_list = model('Url_shortener')->get_visits_list_by_id_list($tiny_url_id_list);
        foreach ($data as &$row)
        {
            if (!empty($symbol_list[$row['id']]))
            {
                $row['tiny_url'] = 'http://chelun.com/url/' . $symbol_list[$row['id']];
            }
            else
            {
                $row['tiny_url'] = '#';
            }

            if (!empty($visits_list[$row['id']]))
            {
                $row['visits'] = $visits_list[$row['id']];
            }
            else
            {
                $row['visits'] = 0;
            }
        }
        unset($row);

        $this->assign('form', $form);
        $this->assign('data', $data);
        $this->assign('pager', array('render' => $pager->render()));

        $this->display();
    }

    function delete()
    {
        if (isset($_GET['id']))
        {
            $id = (int) $_GET['id'];
            $res = model('Url_shortener')->delete_tiny_url($id);

            $msg = $res ? '操作成功' : '操作失败';
            \Swoole\JS::js_goto($msg, '/url_shortener/tiny_url_list');
        } else {
            $this->http->status(302);
            $this->http->header('Location', '/url_shortener/tiny_url_list');
        }
    }

    private function display_edit_category_page($title = '', $project_id = null, $name = null, $error = null)
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

        $this->assign('title', $title);
        $this->assign('error', $error);
        $this->assign('form', $form);
        $this->display('url_shortener/edit_category.php');
    }

    private function edit_category_check(&$project_id, &$name, &$error)
    {
        $error = '';

        $project_id = isset($_POST['project_id'])
            ? (int) $_POST['project_id']
            : '';
        $name = isset($_POST['name']) ? trim($_POST['name']) : '';

        if (empty($project_id))
        {
            $error = '所属项目是必需的！';
        }
        elseif ($name === '')
        {
            $error = '分类名称是必需的！';
        }
    }

    // 增加短链接分类
    function add_category()
    {
        if (empty($_POST))
        {
            return $this->display_edit_category_page('新增短网址分类');
        }
        else
        {
            $this->edit_category_check($project_id, $name, $error);
            if (!empty($error)) {
                return $this->display_edit_category_page('新增短网址分类', $project_id, $name, $error);
            }

            $inserts['name'] = $name;
            $inserts['project_id'] = $project_id;

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

        if (empty($_POST))
        {
            return $this->display_edit_category_page('编辑短链接分类', $project_id, $name);
        }
        else
        {
            $this->edit_category_check($project_id, $name, $error);
            if (!empty($error))
            {
                return $this->display_edit_category_page('新增短网址分类', $project_id, $name, $error);
            }

            $sets = array();
            $sets['project_id'] = $project_id;
            $sets['name'] = $name;

            $res = table('tiny_url_category')->set($id, $sets);
            $msg = $res ? '操作成功' : '操作失败';
            \Swoole\JS::js_goto($msg, "/url_shortener/edit_category?id={$id}");
        }
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
        $gets['where'][] = 'status = 1';
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
