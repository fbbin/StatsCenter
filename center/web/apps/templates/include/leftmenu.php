<nav>
<ul>
    <li class="active">
        <a href="/stats/home/" title="Dashboard"><i class="fa fa-lg fa-fw fa-home"></i> <span class="menu-item-parent">系统首页</span></a>
    </li>
    <li>
        <a href="/stats/index/" id="stats_index_link"><i class="fa fa-lg fa-fw fa-th"></i> <span class="menu-item-parent">统计数据</span></a>
    </li>
    <li>
    </li>
    <li>
        <a href="/setting/add_interface/"><i class="fa fa-lg fa-fw fa-pencil"></i> <span class="menu-item-parent">新增接口</span></a>
    </li>
    <li>
        <a href="/setting/interface_list/"><i class="fa fa-lg fa-fw fa-reorder"></i> <span class="menu-item-parent">接口列表</span></a>
    </li>
    <li>
        <a href="/setting/add_module/"><i class="fa fa-lg fa-fw fa-plus-circle"></i> <span class="menu-item-parent">新增模块</span></a>
    </li>
    <li>
        <a href="/setting/module_list/"><i class="fa fa-lg fa-fw fa-reorder"></i> <span class="menu-item-parent">模块列表</span></a>
    </li>
    <?php
    //只有超级管理员可以修改项目
    if ($this->userinfo['usertype'] == 0):
    ?>
        <li>
        <a href="#"><i class="fa fa-lg fa-fw fa-cog"></i> <span class="menu-item-parent">系统管理</span></a>
        <ul>
            <li
                <?php if ($this->isActiveMenu('user', 'add')){ ?>class="active"
                <?php } ?>>
                <a href="/user/add/"
            ><i class="fa fa-lg fa-fw fa-user"></i> <span
                        class="menu-item-parent">新增用户</span></a>
            </li>
            <li <?php if ($this->isActiveMenu('user', 'ulist')){ ?>
                class="active" <?php } ?>>
                <a href="/user/ulist/"><i class="fa fa-lg fa-fw fa-reorder"></i> <span
                        class="menu-item-parent">用户列表</span></a>
            </li>
            <li <?php if ($this->isActiveMenu('project', 'edit')){ ?>
                class="active" <?php } ?>>
                <a href="/project/edit/"><i class="fa fa-lg fa-fw fa-pencil"></i> <span
                        class="menu-item-parent">新增项目</span></a>
            </li>
            <li <?php if ($this->isActiveMenu('project', 'plist')){ ?>
                class="active" <?php } ?>>
                <a href="/project/plist/"><i class="fa fa-lg fa-fw fa-reorder"></i>
                    <span class="menu-item-parent">项目列表</span></a>
            </li>
        </ul>
        </li>
    <?php
    endif;
    ?>
    <li>
        <a href="/user/passwd/"><i class="fa fa-lg fa-fw fa-key"></i> <span class="menu-item-parent">修改密码</span></a>
    </li>
</ul>
</nav>