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
            $tiny_url['url'] = $this->swoole->redis('cluster')->hGet('tiny-url:url', ShortUrl::encode($id));
        }
        else
        {
            $tiny_url = null;
        }

        return $tiny_url;
    }

    function update_tiny_url_by_id($id, $category_id, $name, $url)
    {
        $sets = array();
        $sets['category_id'] = $category_id;
        $sets['name'] = $name;
        $res = table('tiny_url')->set($id, $sets);

        if ($res)
        {
            $this->swoole->redis('cluster')->hSet('tiny-url:url', ShortUrl::encode($id), $url);
        }

        return (bool) $res;
    }

    function delete_tiny_url($id)
    {
        $res = table('tiny_url')->set($id, array('status' => 2));

        if ($res)
        {
            $this->swoole->redis('cluster')->hDel('tiny-url:url', ShortUrl::encode($id));
        }

        return (bool) $res;
    }

    function get_symbol_list_by_id_list(array $tiny_url_id_list)
    {
        $symbol_list = array();
        foreach ($tiny_url_id_list as $id)
        {
            $symbol_list[$id] = ShortUrl::encode($id);
        }

        return $symbol_list;
    }

    function get_visits_list_by_id_list(array $tiny_url_id_list)
    {
        $visits_list = array();

        foreach ($tiny_url_id_list as $id)
        {
            $visits_list[$id] = (int) $this->swoole->redis('cluster')->zScore(
                'tiny-url:visits',
                ShortUrl::encode($id)
            );
        }

        return $visits_list;
    }
}
