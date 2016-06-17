<?php
namespace App\Controller;

class App_release extends \App\LoginController
{
    public $if_filter = false;

    public function app_list()
    {
        $query_params = [
            'page' => intval(array_get($_GET, 'page', 1)),
            'pagesize' => 15,
            'order' => 'id desc',
            'where' => '`enable` = ' . APP_STATUS_ENABLED,
        ];
        $data = table('app', 'platform')->gets($query_params, $pager);

        $os_list = model('App')->getOSList();

        $app_id_list = [];
        foreach ($data as &$row)
        {
            if (isset($os_list[$row['os']]))
            {
                $row['os_name'] = $os_list[$row['os']];
            }
            else
            {
                $row['os_name'] = \Swoole::$php->config['setting']['app_os_name'][APP_OS_UNKNOWN];
                $row['os'] = APP_OS_UNKNOWN;
            }

            $row['os'] = intval($row['os']);
            $app_id_list[] = intval($row['id']);
        }
        unset($row);

        $query_params = [
            'select' => 'app_id, max(version_int) max_version, count(*) num_version',
            'where' => sprintf('app_id IN (%s)', implode(',', $app_id_list)),
            'group' => 'app_id',
        ];
        $release_info_list = table('app_release', 'platform')->gets($query_params);
        $release_info_list = array_rebuild($release_info_list, 'app_id');

        $this->assign('page_title', 'APP列表');
        $this->assign('pager', $pager);
        $this->assign('data', $data);
        $this->assign('release_info_list', $release_info_list);
        $this->display();
    }

    public function release_list()
    {
        $app_id = !empty($_GET['app_id']) ? intval($_GET['app_id']) : null;
        if (!is_null($app_id))
        {
            $app = table('app', 'platform')->get($app_id)->get();
        }
        if (!empty($app))
        {
            $app['os_name'] = \Swoole::$php->config['setting']['app_os'][$app['os']];
        }
        else
        {
            return $this->error('APP不存在！');
        }

        $query_params = [
            'where' => sprintf("app_id = %d AND status != %d", $app_id, DB_STATUS_DELETED),
            'page' => intval(array_get($_GET, 'page', 1)),
            'pagesize' => 15,
            'order' => 'version_int DESC, id DESC',
        ];
        $data = table('app_release', 'platform')->gets($query_params, $pager);

        $this->assign('pager', $pager);
        $this->assign('app', $app);
        $this->assign('data', $data);
        $this->display();
    }

    public function new_release()
    {
        $app_id = !empty($_GET['app_id']) ? intval($_GET['app_id']) : null;
        if (!is_null($app_id))
        {
            $app = table('app', 'platform')->get($app_id)->get();
        }
        if (empty($app))
        {
            return $this->error('APP不存在！');
        }

        if (!empty($_POST))
        {
            $form_data = $_POST;
            $form_data['app_id'] = $app_id;
            $db_data = $this->validate($form_data, [$this, 'editReleaseCheck'], $errors);
            if (empty($errors))
            {
                $db_data['app_id'] = $app_id;
                $db_data['create_time'] = $db_data['update_time'] = date('Y-m-d H:i:s');
                $insert_id = table('app_release', 'platform')->put($db_data);
                if ($insert_id)
                {
                    \App\Session::flash('msg', '添加APP版本成功！');
                    return $this->redirect("/app_release/edit_release?id={$insert_id}");
                }
                else
                {
                    $errors[] = '添加失败，请联系管理员！';
                }
            }
        }
        else
        {
            $form_data['force_upgrade_strategy'] = APP_FORCE_UPGRADE_STRATEGY_OPTIONAL;
        }

        $this->assign('app', $app);
        $this->assign('form_data', !empty($form_data) ? $form_data : []);
        $this->assign('errors', !empty($errors) ? $errors : []);
        $this->display('app_release/edit_release.php');
    }

    public function edit_release()
    {
        $release = null;
        if (!empty($_GET['id']))
        {
            $release_id = intval($_GET['id']);
            $release = table('app_release', 'platform')->get($release_id)->get();
        }
        else
        {
            if (isset($_GET['version']))
            {
                $version_number = trim(array_get($_GET, 'version'));
                $release = table('app_release', 'platform')->get($version_number, 'version_number')->get();
            }
        }

        if (empty($release))
        {
            return $this->error('APP版本不存在！');
        }
        $app_id = intval($release['app_id']);
        $release_id = intval($release['id']);
        $app = table('app', 'platform')->get($app_id)->get();
        if (empty($app))
        {
            return $this->error('APP不存在，请联系管理员！');
        }

        if (!empty($_POST))
        {
            $form_data = $_POST;
            $form_data['app_id'] = $app_id;
            $form_data['release_id'] = $release_id;
            $db_data = $this->validate($form_data, [$this, 'editReleaseCheck'], $errors);
            if (empty($errors))
            {
                $db_data['update_time'] = date('Y-m-d H:i:s');
                $result = table('app_release', 'platform')->set($release_id, $db_data);
                if ($result)
                {
                    \App\Session::flash('msg', '编辑APP版本成功！');
                    return $this->redirect("/app_release/edit_release?id={$release_id}");
                }
                else
                {
                    $errors[] = '编辑失败，请联系管理员！';
                }
            }
        }
        else
        {
            $form_data = $release;
            if ($form_data['force_upgrade'])
            {
                $form_data['force_upgrade_strategy'] = APP_FORCE_UPGRADE_STRATEGY_ALL;
            }
            else
            {
                if (trim($form_data['force_upgrade_version']) !== '')
                {
                    $form_data['force_upgrade_strategy'] = APP_FORCE_UPGRADE_STRATEGY_SPECIFIC;
                }
                else
                {
                    $form_data['force_upgrade_strategy'] = APP_FORCE_UPGRADE_STRATEGY_OPTIONAL;
                }
            }
        }

        $this->assign('app', $app);
        $this->assign('form_data', $form_data);
        $this->assign('msg', \App\Session::get('msg'));
        $this->assign('errors', !empty($errors) ? $errors : []);
        $this->display('app_release/edit_release.php');
    }

    public function delete_release()
    {
        $release_id = intval(array_get($_GET, 'id'));
        if (!empty($release_id))
        {
            $release = table('app_release', 'platform')->get($release_id)->get();
        }
        if (empty($release))
        {
            return $this->error('APP版本不存在！');
        }

        $query_params = [
            'where' => sprintf('release_id = %d', $release_id),
        ];
        $num_link = table('app_release_link', 'platform')->count($query_params);
        if ($num_link)
        {
            return $this->error('该版本的下载包/补丁包不为空，请先清空该版本的下载包/补丁包！');
        }

        $result = table('app_release', 'platform')->del($release_id);
        if (!$result)
        {
            return $this->error('DB错误，请联系管理员！');
        }

        return $this->success('操作成功！', '/app_release/release_list?app_id=' . intval($release['app_id']));
    }

    public function enable_release()
    {
        $release_id = intval(array_get($_GET, 'id'));
        if (!empty($release_id))
        {
            $release = table('app_release', 'platform')->get($release_id)->get();
        }
        if (empty($release))
        {
            return $this->error('APP版本不存在！');
        }

        $query_params = [
            'where' => sprintf('release_id = %d AND package_type = %d', $release_id, PACKAGE_TYPE_INSTALL),
        ];
        $num_release_link = table('app_release_link', 'platform')->count($query_params);
        if (!$num_release_link)
        {
            return $this->error('请确保渠道下载包数量不为0，再发布！');
        }

        $result = table('app_release', 'platform')->set($release_id, ['status' => DB_STATUS_ENABLED]);
        if (!$result)
        {
            return $this->error('DB错误，请联系管理员！');
        }

        \Swoole\JS::js_back('APP发布成功！');
    }

    public function disable_release()
    {
        $release_id = intval(array_get($_GET, 'id'));
        if (!empty($release_id))
        {
            $release = table('app_release', 'platform')->get($release_id)->get();
        }
        if (empty($release))
        {
            return $this->error('APP版本不存在！');
        }

        $result = table('app_release', 'platform')->set($release_id, ['status' => DB_STATUS_DISABLED]);
        if (!$result)
        {
            return $this->error('DB错误，请联系管理员！');
        }

        \Swoole\JS::js_back('APP下架成功！');
    }

    public function release_link_list()
    {
        $app_id = !empty($_GET['app_id']) ? intval($_GET['app_id']) : null;
        if (!is_null($app_id))
        {
            $app = table('app', 'platform')->get($app_id)->get();
        }
        if (!empty($app))
        {
            $app['os_name'] = \Swoole::$php->config['setting']['app_os'][$app['os']];
        }
        else
        {
            return $this->error('APP不存在！');
        }

        $package_type = $this->value($_GET, 'package_type', PACKAGE_TYPE_INSTALL, true);
        $this->assign('package_type', $package_type);

        $query_params = [
            'where' => sprintf("app_id = %d AND status != %d", $app_id, DB_STATUS_DELETED),
            'page' => intval(array_get($_GET, 'page', 1)),
            'pagesize' => 15,
            'order' => 'version_int DESC, id DESC',
        ];
        $data = table('app_release', 'platform')->gets($query_params, $pager);
        $release_id_list = [];

        if (!empty($data))
        {
            foreach ($data as &$row)
            {
                $release_id_list[] = intval($row['id']);
            }
            unset($row);

            $query_params = [
                'select' => 'app_release_link.*, app_channel.name AS channel_name, app_channel.channel_key AS channel_key',
                'order' => 'fallback_link DESC, app_release_link.channel_id DESC',
                'where' => "app_release_link.app_id = {$app_id}
                    AND app_release_link.release_id IN (" . implode(',', $release_id_list) . ')'
                . ' AND app_release_link.package_type = ' . $package_type,
                'leftjoin' => ['app_channel', 'app_release_link.channel_id = app_channel.id'],
            ];
            $link_list = table('app_release_link', 'platform')->gets($query_params);

            if (!empty($link_list))
            {
                $temp_link_list = [];
                foreach ($link_list as &$row)
                {
                    $row['release_id'] = intval($row['release_id']);
                    if (!isset($temp_link_list[$row['release_id']]))
                    {
                        $temp_link_list[$row['release_id']] = [];
                    }
                    $temp_link_list[$row['release_id']][] = $row;
                }
                unset($row);
                $link_list = $temp_link_list;

                foreach ($data as &$row)
                {
                    if (isset($link_list[$row['id']]))
                    {
                        $row['release_link_list'] = $link_list[$row['id']];
                    }
                }
                unset($row);
            }
        }

        $this->assign('pager', $pager);
        $this->assign('app', $app);
        $this->assign('data', $data);
        $this->display();
    }

    public function add_channel_release_link()
    {
        $release_id = !empty($_GET['release_id']) ? intval($_GET['release_id']) : null;
        if (!is_null($release_id))
        {
            $release = table('app_release', 'platform')->get($release_id)->get();
        }
        if (empty($release))
        {
            return $this->error('APP版本不存在！');
        }
        $app_id = intval($release['app_id']);
        $app = table('app', 'platform')->get($app_id)->get();
        if (empty($app))
        {
            return $this->error('APP不存在，请联系管理员！');
        }

        $package_type = $this->value($_GET, 'package_type', PACKAGE_TYPE_INSTALL, true);
        $this->assign('package_type', $package_type);

        $query_params = [
            'page' => intval(array_get($_GET, 'page', 1)),
            'pagesize' => 15,
            'order' => 'id desc',
        ];
        $channel_list = table('app_channel', 'platform')->gets($query_params, $pager);
        if (empty($channel_list))
        {
            return $this->error('APP渠道为空，<a href="/app_release/add_channel">点这里新增APP渠道</a>！');
        }

        // 找出已有下载包/补丁包的渠道
        $query_params = [
            'where' => sprintf(
                'release_id = %d AND app_id = %d AND package_type = %d',
                $release_id,
                $app_id,
                $package_type
            ),
        ];
        $has_fallback_link = false;
        $released_channel_id_list = [];
        $link_list = table('app_release_link', 'platform')->gets($query_params);
        foreach ($link_list as $link)
        {
            $released_channel_id_list[$link['channel_id']] = intval($link['channel_id']);
            // 有缺省下载地址
            if ($link['fallback_link'])
            {
                $has_fallback_link = true;
            }
        }

        $form_data['channel_list'] = [];
        foreach ($channel_list as $channel)
        {
            // 只记录没有下载包/补丁包的渠道
            if (!in_array($channel['id'], $released_channel_id_list))
            {
                $form_data['channel_list'][$channel['id']] = $channel['name'];
            }
        }

        if (empty($form_data['channel_list']))
        {
            return $this->error('所有渠道都有下载包了！');
        }

        if (!empty($_POST))
        {
            $form_data = array_merge($form_data, $_POST);
            $form_data['app_id'] = $app_id;
            $form_data['release_id'] = $release_id;

            $data = $this->validate($form_data, [$this, 'editChannelReleaseLinkCheck'], $errors);
            if (empty($errors))
            {
                $data['create_time'] = $data['update_time'] = date('Y-m-d H:i:s');
                $data['app_id'] = $app_id;
                $data['release_id'] = $release_id;
                $insert_id = table('app_release_link', 'platform')->put($data);
                if ($insert_id)
                {
                    \App\Session::flash('msg', '添加渠道下载包成功！');
                    return $this->redirect("/app_release/edit_channel_release_link?id={$insert_id}");
                }
                else
                {
                    $errors[] = '添加失败，请联系管理员！';
                }
            }
        }

        $this->assign('app', $app);
        $this->assign('release', $release);
        $this->assign('has_fallback_link', $has_fallback_link);
        $this->assign('form_data', !empty($form_data) ? $form_data : []);
        $this->assign('errors', !empty($errors) ? $errors : []);
        $this->display('app_release/edit_channel_release_link.php');
    }

    public function edit_channel_release_link()
    {
        $release_link_id = !empty($_GET['id']) ? intval($_GET['id']) : null;
        if (!is_null($release_link_id))
        {
            $release_link = table('app_release_link', 'platform')->get($release_link_id)->get();
        }
        if (empty($release_link))
        {
            return $this->error('APP渠道下载包不存在！');
        }
        $release_id = intval($release_link['release_id']);
        $release = table('app_release', 'platform')->get($release_id)->get();
        if (empty($release))
        {
            return $this->error('APP版本不存在！');
        }
        $app_id = intval($release_link['app_id']);
        $app = table('app', 'platform')->get($app_id)->get();

        if (empty($app))
        {
            return $this->error('APP不存在，请联系管理员！');
        }

        $package_type = $this->value($release_link, 'package_type', PACKAGE_TYPE_INSTALL, true);
        $this->assign('package_type', $package_type);

        // 是否已有缺省下载地址
        $query_params = [
            'where' => sprintf('release_id = %d AND app_id = %d AND fallback_link = 1', $release_id, $app_id),
        ];
        $has_fallback_link = table('app_release_link', 'platform')->count($query_params) ? true : false;
        // 当前下载地址是否缺省下载地址
        $is_fallback_link = (bool) $release_link['fallback_link'];

        if (!empty($_POST))
        {
            $form_data = $_POST;
            $form_data['app_channel'] = intval($release_link['channel_id']);
            $form_data['app_id'] = $app_id;
            $form_data['release_id'] = $release_id;
            $form_data['release_link_id'] = $release_link_id;

            $data = $this->validate($form_data, [$this, 'editChannelReleaseLinkCheck'], $errors);
            if (empty($errors))
            {
                $data['update_time'] = date('Y-m-d H:i:s');
                $result = table('app_release_link', 'platform')->set($release_link_id, $data);
                if ($result)
                {
                    \App\Session::flash('msg', '编辑APP渠道下载包成功！');
                    return $this->redirect("/app_release/edit_channel_release_link?id={$release_link_id}");
                }
                else
                {
                    $errors[] = '编辑失败，请联系管理员！';
                }
            }
        }
        else
        {
            $form_data = $release_link;
        }

        $this->assign('app', $app);
        $this->assign('release', $release);
        $this->assign('has_fallback_link', $has_fallback_link);
        $this->assign('is_fallback_link', $is_fallback_link);
        $this->assign('form_data', $form_data);
        $this->assign('msg', \App\Session::get('msg'));
        $this->assign('errors', !empty($errors) ? $errors : []);
        $this->display('app_release/edit_channel_release_link.php');
    }

    public function delete_channel_release_link()
    {
        $release_link_id = intval(array_get($_GET, 'id'));
        if (!empty($release_link_id))
        {
            $release_link = table('app_release_link', 'platform')->get($release_link_id)->get();
        }
        if (empty($release_link))
        {
            return $this->error('APP渠道下载包不存在！');
        }

        $query_params = [
            'where' => sprintf('app_id = %d AND release_id = %d', $release_link['app_id'], $release_link['release_id']),
        ];
        $num_release_link = table('app_release_link', 'platform')->count($query_params);
        // 下载包数量小于等于1的时候，需要APP版本下架才能删除下载包
        if ($num_release_link <= 1)
        {
            $release = table('app_release', 'platform')->get($release_link['release_id']);
            if (empty($release))
            {
                return $this->error('下载包对应的APP版本不存在，请联系管理员！');
            }
            if (intval($release['status']) === DB_STATUS_ENABLED)
            {
                return $this->error('只剩最后一个下载包，请先让APP下架再删除！');
            }
        }

        $result = table('app_release_link', 'platform')->del($release_link_id);
        if (!$result)
        {
            return $this->error('DB错误，请联系管理员！');
        }

        return $this->success('操作成功', '/app_release/release_link_list?app_id=' . intval($release_link['app_id']) . '&package_type=' . intval($release_link['package_type']));
    }

    public function channel_list()
    {
        $query_params = [
            'page' => intval(array_get($_GET, 'page', 1)),
            'pagesize' => 15,
            'order' => 'id desc',
        ];
        $data = table('app_channel', 'platform')->gets($query_params, $pager);

        $release_link_info = [];
        if (!empty($data))
        {
            $channel_id_list = array_map('intval', array_rebuild($data, 'id', 'id'));

            $query_params = [
                'select' => 'channel_id, count(*) num_release_link',
                'where' => sprintf('channel_id IN (%s)', implode(', ', $channel_id_list)),
                'group' => 'channel_id',
            ];
            $release_link_data = table('app_release_link', 'platform')->gets($query_params);
            if (!empty($release_link_data))
            {
                $release_link_info = array_rebuild($release_link_data, 'channel_id');
            }
        }

        $this->assign('pager', $pager);
        $this->assign('data', $data);
        $this->assign('release_link_info', $release_link_info);
        $this->display();
    }

    public function add_channel()
    {
        if (!empty($_POST))
        {
            $form_data = $_POST;
            $db_data = $this->validate($form_data, [$this, 'editChannelCheck'], $errors);
            if (empty($errors))
            {
                $db_data['create_time'] = $db_data['update_time'] = date('Y-m-d H:i:s');
                $insert_id = table('app_channel', 'platform')->put($db_data);
                if ($insert_id)
                {
                    \App\Session::flash('msg', '添加渠道成功！');
                    return $this->redirect("/app_release/edit_channel?id={$insert_id}");
                }
                else
                {
                    $errors[] = '添加失败，请联系管理员！';
                }
            }
        }

        $this->assign('page_title', '新增渠道');
        $this->assign('form_data', !empty($form_data) ? $form_data : []);
        $this->assign('errors', !empty($errors) ? $errors : []);
        $this->display('app_release/edit_channel.php');
    }

    public function edit_channel()
    {
        $id = !empty($_GET['id']) ? intval($_GET['id']) : null;
        if (!is_null($id))
        {
            $channel = table('app_channel', 'platform')->get($id);
        }
        if (empty($channel))
        {
            return $this->error('APP渠道不存在！');
        }

        if (!empty($_POST))
        {
            $form_data = $_POST;
            $form_data['channel_id'] = $id;
            $db_data = $this->validate($form_data, [$this, 'editChannelCheck'], $errors);
            if (empty($errors))
            {
                $db_data['update_time'] = date('Y-m-d H:i:s');
                $result = table('app_channel', 'platform')->set($id, $db_data);
                if ($result)
                {
                    \App\Session::flash('msg', '编辑APP渠道成功！');
                    return $this->redirect("/app_release/edit_channel?id={$id}");
                }
                else
                {
                    $errors[] = '编辑失败，请联系管理员！';
                }
            }
        }
        else
        {
            $form_data = $channel;
        }

        $this->assign('page_title', '编辑渠道');
        $this->assign('form_data', $form_data);
        $this->assign('msg', \App\Session::get('msg'));
        $this->assign('errors', !empty($errors) ? $errors : []);
        $this->display('app_release/edit_channel.php');
    }

    public function delete_channel()
    {
        $channel_id = intval(array_get($_GET, 'id'));
        if (!empty($channel_id))
        {
            $query_params = [
                'where' => sprintf('channel_id = %d', $channel_id),
            ];
            $num_link = table('app_release_link', 'platform')->count($query_params);
            if ($num_link)
            {
                return $this->error('该渠道的下载包不为空，请先清空改渠道的下载包/渠道包。');
            }

            $result = table('app_channel', 'platform')->del($channel_id);
            if (!$result)
            {
                return $this->error('DB错误，请联系管理员！');
            }
        }

        return $this->success('操作成功！', '/app_release/channel_list');
    }

    public function editReleaseCheck($data, &$errors)
    {
        $db = \Swoole::$php->db('platform');
        $version_number = trim(array_get($data, 'version_number'));
        if ($this->isValidVersionFormat($version_number, $matches))
        {
            $version_high = (int) $matches[1];
            $version_middle = (int) $matches[2];
            $version_low = (int) $matches[3];

            if (($version_high < 0 || $version_high > 255)
                || ($version_middle < 0 || $version_middle > 255)
                || ($version_low < 0 || $version_low > 255))
            {
                $errors[] = '各位版本号取值范围0-255！';
            }
            else
            {
                $db_data['version_number'] = sprintf('%d.%d.%d', $version_high, $version_middle, $version_low);
                $db_data['version_int'] = version_string_to_int($db_data['version_number']);

                if (isset($data['release_id']))
                {
                    $query_params = [
                        'where' => sprintf("app_id = %d AND version_number = '%s' AND id != %d", $data['app_id'], $db_data['version_number'], $data['release_id']),
                    ];
                    $num_releases = table('app_release', 'platform')->count($query_params);
                }
                else
                {
                    $query_params = [
                        'where' => sprintf("app_id = %d AND version_number = '%s'", $data['app_id'], $db_data['version_number']),
                    ];
                    $num_releases = table('app_release', 'platform')->gets($query_params);
                }

                if ($num_releases)
                {
                    $errors[] = "欲增加的版本号({$db_data['version_number']})已存在！";
                }
            }
        }
        else
        {
            $errors[] = 'APP版本号格式不正确！';
        }
        $db_data['version_code'] = trim(array_get($data, 'version_code'));
        if ($db_data['version_code'] !== '')
        {
            if (isset($data['release_id']))
            {
                $query_params = [
                    'where' => sprintf(
                        "app_id = %d AND version_code = '%s' AND id != %d",
                        $data['app_id'],
                        $db->quote($db_data['version_code']),
                        $data['release_id']
                    ),
                ];
            }
            else
            {
                $query_params = [
                    'where' => sprintf(
                        "app_id = %d AND version_code = '%s'",
                        $data['app_id'],
                        $db->quote($db_data['version_code'])
                    ),
                ];
            }

            $num_releases = table('app_release', 'platform')->count($query_params);

            if ($num_releases)
            {
                $errors[] = "Android版本Code({$db_data['version_code']})已存在！";
            }
        }

        $db_data['prompt_title'] = trim(array_get($data, 'prompt_title'));
        if ($db_data['prompt_title'] === '')
        {
            $errors[] = '弹框标题不能为空！';
        }
        $db_data['prompt_content'] = trim(array_get($data, 'prompt_content'));
        if ($db_data['prompt_content'] === '')
        {
            $errors[] = '弹框内容不能为空！';
        }
        $db_data['prompt_interval'] = trim(array_get($data, 'prompt_interval'));
        if ((!is_numeric($db_data['prompt_interval'])) || ($db_data['prompt_interval'] < 0))
        {
            $errors[] = '弹框提示周期必须为非负数';
        }
        $db_data['prompt_interval'] = intval($db_data['prompt_interval']);
        $db_data['prompt_confirm_button_text'] = trim(array_get($data, 'prompt_confirm_button_text'));
        if ($db_data['prompt_confirm_button_text'] === '')
        {
            $errors[] = '弹框确定按钮文字不能为空！';
        }
        $db_data['prompt_cancel_button_text'] = trim(array_get($data, 'prompt_cancel_button_text'));
        if ($db_data['prompt_cancel_button_text'] === '')
        {
            $errors[] = '弹框取消按钮文字不能为空！';
        }
        $force_upgrade_strategy = array_get($data, 'force_upgrade_strategy');
        if (($force_upgrade_strategy === '')
            || (!in_array($force_upgrade_strategy, [APP_FORCE_UPGRADE_STRATEGY_ALL, APP_FORCE_UPGRADE_STRATEGY_PREVIOUS, APP_FORCE_UPGRADE_STRATEGY_OPTIONAL, APP_FORCE_UPGRADE_STRATEGY_SPECIFIC])))
        {
            $errors[] = '必须指定强制更新策略！';
        }
        $force_upgrade_strategy = intval($force_upgrade_strategy);
        if ($force_upgrade_strategy === APP_FORCE_UPGRADE_STRATEGY_ALL)
        {
            $db_data['force_upgrade'] = APP_FORCE_UPGRADE_ENABLED;
            // 强制更新的话，提示升级弹窗周期改为0
            $db_data['prompt_interval'] = 0;
        }
        elseif ($force_upgrade_strategy === APP_FORCE_UPGRADE_STRATEGY_PREVIOUS)
        {
            $db_data['force_upgrade'] = APP_FORCE_UPGRADE_DISABLED;

            if (empty($errors))
            {
                $data['app_id'] = intval($data['app_id']);
                $query_params = [
                    'where' => "`version_int` < {$db_data['version_int']} AND `app_id` = {$data['app_id']}",
                    'order' => '`version_int` DESC',
                ];
                $release_list = table('app_release', 'platform')->gets($query_params, $pager);
                if (!empty($release_list))
                {
                    $release = reset($release_list);
                    $db_data['force_upgrade_version'] = $release['version_number'];
                }
                else
                {
                    $errors[] = sprintf('找不到APP (%s) 的上个版本！', version_int_to_string($db_data['version_int']));
                }
            }
        }
        elseif ($force_upgrade_strategy === APP_FORCE_UPGRADE_STRATEGY_SPECIFIC)
        {
            $force_upgrade_version = trim(array_get($data, 'force_upgrade_version'));
            if ($force_upgrade_version !== '')
            {
                $version_list = array_filter(explode(',', $force_upgrade_version));
                $valid_version_list = [];
                foreach ($version_list as $version)
                {
                    if ($this->isValidVersionFormat($version, $matches))
                    {
                        if (version_string_to_int($version) < $db_data['version_int'])
                        {
                            $valid_version_list[] = $version;
                        }
                        else
                        {
                            $errors[] = "指定强制更新的({$version})版本号不应大于或等于新增的APP版本号({$db_data['version_number']})";
                        }
                    }
                    else
                    {
                        $errors[] = "指定强制更新的版本中，{$version}不是合法的版本号格式";
                    }
                }

                if (!empty($valid_version_list))
                {
                    $db_data['force_upgrade_version'] = implode(',', $version_list);
                }
            }
            else
            {
                $errors[] = '指定强制更新的版本不能为空！';
            }
        }
        else
        {
            $db_data['force_upgrade'] = APP_FORCE_UPGRADE_DISABLED;
            // 不强制更新，需要清空设置的“强制更新的版本”
            $db_data['force_upgrade_version'] = '';
        }
        return $db_data;
    }

    public function editChannelCheck($data, &$errors)
    {
        $db_data['name'] = trim(array_get($data, 'name'));
        if ($db_data['name'] === '')
        {
            $errors[] = '渠道名称不能为空！';
        }
        $db_data['channel_key'] = trim(array_get($data, 'channel_key'));
        if ($db_data['channel_key'] === '')
        {
            $errors[] = '渠道标识符不能为空！';
        }
        $db_data['channel_key_lowercase'] = strtolower($db_data['channel_key']);
        if (!preg_match('/^[a-zA-Z0-9]+$/', $db_data['channel_key']))
        {
            $errors[] = '渠道标识符只能是英文数字字母组合！';
        }
        if (empty($errors))
        {
            $db = \Swoole::$php->db('platform');
            if (isset($data['channel_id']))
            {
                $query_params = [
                    'where' => sprintf("(`name` = '%s' OR `channel_key_lowercase` = '%s') AND `id` != %d", $db->quote($db_data['name']), $db->quote($db_data['channel_key_lowercase']), intval($data['channel_id'])),
                ];
            }
            else
            {
                $query_params = [
                    'where' => sprintf("`name` = '%s' OR `channel_key_lowercase` = '%s'", $db->quote($db_data['name']), $db->quote($db_data['channel_key_lowercase'])),
                ];
            }

            $channel_list = table('app_channel', 'platform')->gets($query_params);
            $name_exists = false;
            $key_exists = false;
            foreach ($channel_list as $channel)
            {
                if ($channel['name'] === $db_data['name'])
                {
                    $name_exists = true;
                }
                if ($channel['channel_key_lowercase'] === $db_data['channel_key_lowercase'])
                {
                    $key_exists = true;
                }
            }
            if ($name_exists)
            {
                $errors[] = '已存在同名的渠道名称！';
            }
            if ($key_exists)
            {
                $errors[] = '已存在同名的渠道标识符！';
            }
        }
        return $db_data;
    }

    public function editChannelReleaseLinkCheck($input, &$errors)
    {
        $output['channel_id'] = trim(array_get($input, 'app_channel'));
        if ($output['channel_id'] !== '')
        {
            $query_params = [
                'where' => "id = '{$output['channel_id']}'",
            ];
            $count = table('app_channel', 'platform')->count($query_params);
            if (!$count)
            {
                $errors[] = 'APP渠道不存在！';
            }
        }
        else
        {
            $errors[] = 'APP渠道不能为空！';
        }
        $output['release_link'] = trim(array_get($input, 'release_link'));
        if ($output['release_link'] === '')
        {
            $errors[] = '下载地址不能为空！';
        }
        if (!is_valid_url($output['release_link']))
        {
            $errors[] = '请填写正确的下载地址！';
        }

        $output['md5'] = trim(array_get($input, 'md5'));
        if ($output['md5'] === '')
        {
            $errors[] = 'MD5不能为空！';
        }
        if (strlen($output['md5']) !== 32)
        {
            $errors[] = 'MD5长度不正确！';
        }

        $output['remarks'] = trim(array_get($input, 'remarks'));

        $output['fallback_link'] = !empty($input['fallback_link']) ? 1 : 0;

        $package_type = intval($input['package_type']) === PACKAGE_TYPE_INSTALL
            ? PACKAGE_TYPE_INSTALL
            : PACKAGE_TYPE_PATCH;
        $output['package_type'] = $package_type;

        if (empty($errors) && $output['fallback_link'])
        {
            $release_link_id = intval(array_get($input, 'release_link_id'));
            if (empty($release_link_id))
            {
                $query_params = [
                    'where' => sprintf(
                        'app_id = %d AND release_id = %d AND fallback_link = 1 AND package_type = %d',
                        $input['app_id'],
                        $input['release_id'],
                        $package_type
                    ),
                ];
            }
            else
            {
                $query_params = [
                    'where' => sprintf(
                        'app_id = %d AND release_id = %d AND fallback_link = 1 AND package_type = %d AND id != %d',
                        $input['app_id'],
                        $input['release_id'],
                        $package_type,
                        $release_link_id
                    ),
                ];
            }
            $num_link = table('app_release_link', 'platform')->count($query_params);
            if ($num_link)
            {
                $errors[] = '只有一个渠道能设置缺省下载地址';
            }
        }

        return $output;
    }

    public function isValidVersionFormat($version_number, &$matches)
    {
        return preg_match('/^(\d+)\.(\d+).(\d+)$/', $version_number, $matches);
    }
}
