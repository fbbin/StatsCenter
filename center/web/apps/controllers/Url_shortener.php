<?php
namespace App\Controller;
use Swoole;
use App\ShortUrl;

class Url_shortener extends \App\LoginController
{
    public $if_filter = false;

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
            $prefix = ShortUrl::gen_prefix_str();
            $inserts['prefix'] = $prefix;

            $tiny_url_id = table('tiny_url')->put($inserts);

            if ($tiny_url_id)
            {
                $msg = '添加成功';

                $symbol = $prefix . ShortUrl::encode($tiny_url_id);
                $res = $this->redis('cluster')->hSet('tiny-url:url', $symbol, $url);
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

    function stats()
    {
        if (isset($_GET['id']))
        {
            $has_prev = $has_next = false;
            $tiny_url_id = (int) $_GET['id'];
            $today = new \DateTime('today');
            $from_date = isset($_GET['from']) ? new \DateTime($_GET['from']) : clone $today;
            $next_from_date = clone $from_date;
            $next_from_date_str = $next_from_date->modify('-20 days')->format('Y-m-d');
            $prev_from_date = clone $from_date;
            $prev_from_date_str = $prev_from_date->modify('+20 days')->format('Y-m-d');

            $tiny_url_info = table('tiny_url')->get($tiny_url_id)->get();
            if (!$tiny_url_info)
            {
                \Swoole\JS::js_goto('短网址不存在！', '/url_shortener/tiny_url_list');
            }
            $symbol = $tiny_url_info['prefix'] . ShortUrl::encode($tiny_url_id);
            $tiny_url = "http://chelun.com/url/{$symbol}";

            $data = array();
            $start_date = new \DateTime('2015-07-16 00:00:00');

            if ($from_date < $today)
            {
                $has_prev = true;
            }

            $interval = $start_date->diff($from_date);
            $row_count = $interval->days + 1;
            if ($row_count > 20)
            {
                $row_count = 20;
                $has_next = true;
            }

            for ($i = 0, $date = clone $from_date; $i < $row_count; $i++, $date->modify('-1 day'))
            {
                $date_str = $date->format('Y-m-d');

                $visits = $this->redis('cluster')->zScore("tiny-url:visits:{$date_str}", $symbol);
                $visits = $visits !== false ? $visits : 0;

                $data[] = array(
                    'date' => $date_str,
                    'visits' => $visits,
                );
            }

            $this->assign('tiny_url_id', $tiny_url_id);
            $this->assign('tiny_url', $tiny_url);
            $this->assign('data', $data);
            $this->assign('next_from_date_str', $next_from_date_str);
            $this->assign('prev_from_date_str', $prev_from_date_str);
            $this->assign('has_prev', $has_prev);
            $this->assign('has_next', $has_next);
            $this->display();
        }
        else
        {
            $this->http->status(302);
            $this->http->header('Location', '/url_shortener/tiny_url_list');
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
            'placeholder' => '短网址名称'
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

        $symbol_list = array();
        foreach ($data as &$row)
        {
            $tiny_url_id = (int) $row['id'];

            $symbol = $row['prefix'] . ShortUrl::encode($tiny_url_id);
            $symbol_list[$tiny_url_id] = $symbol;

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
        }
        unset($row);

        $this->assign('form', $form);
        $this->assign('data', $data);
        $this->assign('pager', array('render' => $pager->render()));

        $this->display();
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
