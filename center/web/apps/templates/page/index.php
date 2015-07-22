<!DOCTYPE html>
<html lang="en-us">
<head>
    <meta charset="utf-8">
    <title><?= Swoole::$php->config['common']['site_name'] ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <?php include __DIR__ . '/../include/css.php'; ?>
</head>
<body>
<header id="header">
    <div id="logo-group">
        <h1>&nbsp;&nbsp;<?= Swoole::$php->config['common']['site_name'] ?></h1>
    </div>
</header>
<!-- MAIN PANEL -->
<div id="main" role="main">
    <div id="content">
        <!-- row -->
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-5 col-lg-4">
                <div class="well no-padding">
                    <form action="/page/login/" method="post" id="login-form" class="smart-form client-form" novalidate="novalidate">
                        <header>
                            登录
                        </header>
                        <fieldset>
                            <section>
                                <label class="label">帐号</label>
                                <label class="input"> <i class="icon-append fa fa-user"></i>
                                    <input type="text" name="username">
                                    <b class="tooltip tooltip-top-right"><i class="fa fa-user txt-color-teal"></i>
                                        请输入用户名</b></label>
                            </section>
                            <section>
                                <label class="label">密码</label>
                                <label class="input"> <i class="icon-append fa fa-lock"></i>
                                    <input type="password" name="password">
                                    <b class="tooltip tooltip-top-right"><i class="fa fa-lock txt-color-teal"></i> 请输入密码</b>
                                </label>
                            </section>

                            <section>
                                <label class="checkbox">
                                    <input type="checkbox" name="remember" checked="">
                                    <i></i>保持登录状态</label>
                            </section>
                        </fieldset>
                        <footer>
                            <button type="submit" class="btn btn-primary">
                                登录
                            </button>
                        </footer>
                    </form>
                </div>
            </div>
            <!-- END MAIN CONTENT -->
        </div>
    </div>
</div>
<!-- END MAIN PANEL -->
<?php include dirname(__DIR__) . '/include/javascript.php'; ?>
<script>
    runAllForms();
    $(function () {
        $("#login-form").validate({
            // Rules for form validation
            rules: {
                username: {
                    required: true
                },
                password: {
                    required: true,
                    minlength: 3,
                    maxlength: 20
                }
            },

            // Messages for form validation
            messages: {
                username: {
                    required: '用户名不能为空'
                },
                password: {
                    required: '密码不能为空'
                }
            },

            // Do not change code below
            errorPlacement: function (error, element) {
                error.insertAfter(element.parent());
            }
        });
    });
</script>
</body>
</html>
