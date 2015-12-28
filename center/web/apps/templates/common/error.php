<?php include __DIR__ . '/../include/header.php' ?>

<!-- MAIN PANEL -->
<div id="main" role="main">

    <!-- RIBBON -->
    <div id="ribbon">

        <span class="ribbon-button-alignment"> <span id="refresh" class="btn btn-ribbon" data-title="refresh"  rel="tooltip" data-placement="bottom" data-original-title="<i class='text-warning fa fa-warning'></i> Warning! This will reset all your widget settings." data-html="true"><i class="fa fa-refresh"></i></span> </span>

        <!-- breadcrumb -->
        <ol class="breadcrumb">
            <li>
                <a href="/">首页</a>
            </li>
            <li>
                出错了！
            </li>
        </ol>
        <!-- end breadcrumb -->

        <!-- You can also add more buttons to the
             ribbon for further usability

             Example below:

             <span class="ribbon-button-alignment pull-right">
             <span id="search" class="btn btn-ribbon hidden-xs" data-title="search"><i class="fa-grid"></i> Change Grid</span>
             <span id="add" class="btn btn-ribbon hidden-xs" data-title="add"><i class="fa-plus"></i> Add</span>
             <span id="search" class="btn btn-ribbon" data-title="search"><i class="fa-search"></i> <span class="hidden-mobile">Search</span></span>
             </span> -->

    </div>
    <!-- END RIBBON -->

    <!-- MAIN CONTENT -->
    <div id="content">

        <!-- row -->
        <div class="row">

            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">

                <div class="row">
                    <div class="col-sm-12">
                        <div class="text-center error-box">
                            <h1 class="error-text tada animated"><i class="fa fa-times-circle text-danger error-icon-shadow"></i> 出错了！</h1>
                            <p class="lead semi-bold">
                                <strong><?=$msg?></strong><br><br>
                            </p>
                            <ul class="error-search text-left font-md">
                                <li><a href="/"><small>返回首页 <i class="fa fa-arrow-right"></i></small></a></li>
                                <li><a href="javascript:history.go(-1);"><small>返回原页面 <i class="fa fa-undo"></i></small></a></li>
                            </ul>
                        </div>

                    </div>

                </div>

            </div>

        </div>
        <!-- end row -->

    </div>
    <!-- END MAIN CONTENT -->

</div>
<!-- END MAIN PANEL -->

<?php include dirname(__DIR__).'/include/javascript.php'; ?>
</body>
</html>
