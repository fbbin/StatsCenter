<!DOCTYPE html>
<html lang="en-us">
<head>
    <meta charset="utf-8">
    <title><?=Swoole::$php->config['common']['site_name']?></title>
    <meta name="description" content="">
    <meta name="author" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <?php include __DIR__.'/../include/css.php'; ?>
</head>
<body class="">
<header style="background: #E4E4E4;color: #22201F" id="header">
    <div id="logo-group">
    <span><img style="vertical-align:top;padding: 8px" width="80"
               src="<?= Swoole::$php->config['common']['logo_url'] ?>"/></span>
    <span id="logo" style="margin-left: 0px"><strong
            style="font-size: 18px;"><?= Swoole::$php->config['common']['site_name'] ?></strong></span>
    </div>

    <div id="project-context">
        <span class="label">当前项目：</span>
        <span id="project-selector" class="popover-trigger-element dropdown-toggle" data-toggle="dropdown">
            <?=$_project_info['name']?> <i class="fa fa-angle-down"></i></span>
        <!-- Suggestion: populate this list with fetch and push technique -->
        <ul class="dropdown-menu">
            <?php foreach ($_projects as $p): ?>
                <li <?php if ($p['id'] == $_project_id) echo "class='active'"; ?>>
                    <a href="/stats/index/?project=<?= $p['id'] ?>"><?= $p['name'] ?></a>
                </li>
            <?php endforeach; ?>
<!--            <li class="divider"></li>-->
<!--            <li>-->
<!--                <a href="javascript:void(0);"><i class="fa fa-power-off"></i> Clear</a>-->
<!--            </li>-->
        </ul>
        <!-- end dropdown-menu-->
    </div>

    <div class="pull-right">
            <span style="padding: 15px 5px;font-weight: bolder">
        <span style="text-transform: none;">
                    <a style="text-decoration: none" href="/user/edit">用户：<?= $_COOKIE['username'] ?>
        </span>
        <span style="text-transform: none;padding: 15px 5px;">
                    <a style="text-decoration: none;font-weight: bolder" href="/page/logout/">退出</a>
        </span>
    </span>
    </div>
</header>
<aside id="left-panel">
    <!--            --><?php //include __DIR__.'/../include/login_info.php'; ?>
    <?php include __DIR__ . '/../include/leftmenu.php'; ?>
    <span class="minifyme"> <i class="fa fa-arrow-circle-left hit"></i> </span>
</aside>