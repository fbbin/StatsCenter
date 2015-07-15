<?php
namespace App\Model;
use Swoole;

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
}
