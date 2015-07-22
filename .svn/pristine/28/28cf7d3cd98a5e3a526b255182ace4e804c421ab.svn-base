<?php
namespace App\Model;
use Swoole;
use App\ShortUrl;

class Url_shortener extends Swoole\Model
{
    function get_category_list()
    {
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

        return $category_options;
    }

    function get_tiny_url_by_id($id)
    {
        $gets = array('where' => array());
        $gets['where'][] = "id = {$id}";
        $gets['where'][] = 'status = 1';

        $tiny_url_list = table('tiny_url')->gets($gets);

        if (!empty($tiny_url_list[0]))
        {
            $tiny_url = $tiny_url_list[0];
            $symbol = $tiny_url['prefix'] . ShortUrl::encode($id);
            $tiny_url['url'] = $this->swoole->redis('cluster')->hGet('tiny-url:url', $symbol);
        }
        else
        {
            $tiny_url = null;
        }

        return $tiny_url;
    }

    function update_tiny_url_by_id($id, $category_id, $name, $url)
    {
        $tiny_url = table('tiny_url')->get($id);
        if (!$tiny_url)
        {
            return false;
        }

        $sets = array();
        $sets['category_id'] = $category_id;
        $sets['name'] = $name;
        $res = table('tiny_url')->set($id, $sets);

        if ($res)
        {
            $symbol = $tiny_url['prefix'] . ShortUrl::encode($id);
            $this->swoole->redis('cluster')->hSet('tiny-url:url', $symbol, $url);
        }

        return (bool) $res;
    }

    function delete_tiny_url($id)
    {
        $tiny_url = table('tiny_url')->get($id);
        if (!$tiny_url)
        {
            return false;
        }

        $res = table('tiny_url')->set($id, array('status' => 2));

        if ($res)
        {
            $symbol = $tiny_url['prefix'] . ShortUrl::encode($id);
            $this->swoole->redis('cluster')->hDel('tiny-url:url', $symbol);
        }

        return (bool) $res;
    }
}
