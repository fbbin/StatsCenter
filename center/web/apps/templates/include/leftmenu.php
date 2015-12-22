<nav>
<ul>
    <li <?php if ($this->isActiveMenu('stats', 'home')){ ?>class="active"<?php } ?>>
        <a href="/stats/home/" title="Dashboard"><i class="fa fa-lg fa-fw fa-home"></i> <span class="menu-item-parent">系统首页</span></a>
    </li>
    <?php if ($this->isAllow('stats')) : ?>
    <li <?php if ($this->isActiveMenu('stats')){ ?>class="active"<?php } ?>>
        <a href="/stats/index/" id="stats_index_link"><i class="fa fa-lg fa-fw fa-th"></i> <span class="menu-item-parent">统计数据</span></a>
    </li>
    <li>
        <a href="#"><i class="fa fa-lg fa-fw fa-bell"></i> <span class="menu-item-parent">模调管理</span></a>
        <ul>
            <li <?php if ($this->isActiveMenu('setting', 'add_interface')){ ?>class="active"<?php } ?>>
                <a href="/setting/add_interface/"><i class="fa fa-lg fa-fw fa-pencil"></i> <span class="menu-item-parent">新增接口</span></a>
            </li>
            <li <?php if ($this->isActiveMenu('setting', 'interface_list')){ ?>class="active"<?php } ?>>
                <a href="/setting/interface_list/"><i class="fa fa-lg fa-fw fa-reorder"></i> <span class="menu-item-parent">接口列表</span></a>
            </li>
            <li <?php if ($this->isActiveMenu('setting', 'add_module')){ ?>class="active"<?php } ?>>
                <a href="/setting/add_module/"><i class="fa fa-lg fa-fw fa-plus-circle"></i> <span class="menu-item-parent">新增模块</span></a>
            </li>
            <li <?php if ($this->isActiveMenu('setting', 'module_list')){ ?>class="active"<?php } ?>>
                <a href="/setting/module_list/"><i class="fa fa-lg fa-fw fa-reorder"></i> <span class="menu-item-parent">模块列表</span></a>
            </li>
        </ul>
    </li>
    <?php endif; ?>
    <?php
    //只有超级管理员可以修改项目
    if ($this->userinfo['usertype'] == 0):
    ?>
        <li>
        <a href="#"><i class="fa fa-lg fa-fw fa-cog"></i> <span class="menu-item-parent">系统管理</span></a>
        <ul>
            <li
                <?php if ($this->isActiveMenu('setting', 'add_user')){ ?>class="active"
                <?php } ?>>
                <a href="/setting/add_user/"
            ><i class="fa fa-lg fa-fw fa-user"></i> <span
                        class="menu-item-parent">新增用户</span></a>
            </li>
            <li <?php if ($this->isActiveMenu('setting', 'user_list')){ ?>
                class="active" <?php } ?>>
                <a href="/setting/user_list/"><i class="fa fa-lg fa-fw fa-reorder"></i> <span
                        class="menu-item-parent">用户列表</span></a>
            </li>
            <li <?php if ($this->isActiveMenu('setting', 'add_project')){ ?>
                class="active" <?php } ?>>
                <a href="/setting/add_project/"><i class="fa fa-lg fa-fw fa-pencil"></i> <span
                        class="menu-item-parent">新增项目</span></a>
            </li>
            <li <?php if ($this->isActiveMenu('setting', 'project_list')){ ?>
                class="active" <?php } ?>>
                <a href="/setting/project_list/"><i class="fa fa-lg fa-fw fa-reorder"></i>
                    <span class="menu-item-parent">项目列表</span></a>
            </li>
        </ul>
        </li>
    <?php
    endif;
    //短信查看的权限
    if ($this->isAllow('sms')) : ?>
        <li>
            <a href="#"><i class="fa fa-lg fa-fw fa-envelope"></i> <span class="menu-item-parent">短信管理</span></a>
            <ul>
                <li <?php if ($this->isActiveMenu('msg', 'msg_stats')){ ?>class="active"<?php } ?>>
                    <a href="/msg/msg_stats/"><i class="fa fa-lg fa-fw fa-envelope-o"></i> <span
                            class="menu-item-parent">短信统计</span></a>
                </li>
                <li <?php if ($this->isActiveMenu('msg', 'smslog')){ ?> class="active" <?php } ?>>
                    <a href="/msg/smslog/"><i class="fa fa-lg fa-fw fa-reorder"></i>
                        <span class="menu-item-parent">短信记录</span></a>
                </li>
                <li <?php if ($this->isActiveMenu('msg', 'captcha_stats')){ ?>class="active" <?php } ?>>
                    <a href="/msg/captcha_stats/"><i class="fa fa-lg fa-fw fa-folder-open"></i> <span
                            class="menu-item-parent">验证码统计</span></a>
                </li>
                <?php if ($this->userinfo['usertype'] == 0):?>
                <li <?php if ($this->isActiveMenu('msg', 'weight')){ ?>class="active"<?php } ?>>
                    <a href="/msg/weight/"><i class="fa fa-lg fa-fw fa-random"></i> <span
                            class="menu-item-parent">权重设置</span></a>
                </li>
                <?php endif; ?>
            </ul>
        </li>
    <?php endif;?>

    <?php if ($this->userinfo['usertype'] == 0 || $this->userinfo['usertype'] == 1) : ?>
    <li <?php if ($this->isActiveMenu('logs2', 'index')){ ?>class="active"<?php } ?>>
        <a href="/logs2/index/" id="logs2_index_link"><i class="fa fa-lg fa-fw fa-list-alt"></i> <span class="menu-item-parent">日志系统</span></a>
    </li>
    <?php endif; ?>
    <li <?php if ($this->isActiveMenu('user', 'passwd')){ ?>class="active"<?php } ?>>
        <a href="/user/passwd/"><i class="fa fa-lg fa-fw fa-key"></i> <span class="menu-item-parent">修改密码</span></a>
    </li>
    <?php if ($this->isAllow('app')) : ?>
    <li>
        <a href="#">
            <i class="fa fa-lg fa-fw fa-mobile"></i>
            <span class="menu-item-parent">APP云端控制</span>
        </a>
        <ul>
            <!-- <li <?php if ($this->isActiveMenu('app_host', 'add_project')){ ?>
            class="active" <?php } ?>>
            <a href="/app_host/add_project/"><i class="fa fa-lg fa-fw fa-pencil"></i> <span class="menu-item-parent">新增项目</span></a>
            </li>
            <li <?php if ($this->isActiveMenu('app_host', 'project_list')){ ?>
            class="active" <?php } ?>>
            <a href="/app_host/project_list/"><i class="fa fa-lg fa-fw fa-reorder"></i> <span class="menu-item-parent">项目列表</span></a>
            </li> -->
            <li <?php if ($this->isActiveMenu('app_host', 'add_host')){ ?>
                class="active" <?php } ?>>
                <a href="/app_host/add_host/"><i class="fa fa-lg fa-fw fa-pencil"></i> <span class="menu-item-parent">新增App接口</span></a>
            </li>
            <li <?php if ($this->isActiveMenu('app_host', 'host_list')){ ?>
                class="active" <?php } ?>>
                <a href="/app_host/host_list/"><i class="fa fa-lg fa-fw fa-reorder"></i> <span class="menu-item-parent">App接口列表</span></a>
            </li>
            <li <?php if ($this->isActiveMenu('app_host', 'add_rule')){ ?>
                class="active" <?php } ?>>
                <a href="/app_host/add_rule/"><i class="fa fa-lg fa-fw fa-pencil"></i> <span class="menu-item-parent">指定设备接口</span></a>
            </li>
            <li <?php if ($this->isActiveMenu('app_host', 'rule_list')){ ?>
                class="active" <?php } ?>>
                <a href="/app_host/rule_list/"><i class="fa fa-lg fa-fw fa-reorder"></i> <span class="menu-item-parent">指定列表</span></a>
            </li>
            <li <?php if ($this->isActiveMenu('app_script', 'index')){ ?>
                class="active" <?php } ?>>
                <a href="/app_script/index/"><i class="fa fa-lg fa-fw fa-reorder"></i> <span class="menu-item-parent">JS脚本下发</span></a>
            </li>
        </ul>
    </li>
    <?php endif; ?>
    <li>
        <a href="http://tinyurl.chelun.com/page/login/?token=<?=$_SESSION['login_token']?>" target="_blank">
            <i class="fa fa-lg fa-fw fa-link"></i>
            <span class="menu-item-parent">短网址管理</span>
        </a>
    </li>
</ul>
</nav>
