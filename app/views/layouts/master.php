<!DOCTYPE html>
<html>
<head>
    <title><?= isset($data->page_title)?$data->page_title:'EstefCasting'; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="Generator" content="Cuna Framework">
    <meta name="Author" content="Jon Cuna">
    @header_includes@
    <link href='https://fonts.googleapis.com/css?family=Dancing+Script' rel='stylesheet' type='text/css'>
</head>
<body>
<div class="content-wrapper">
    <nav class="navbar navbar-default">
        <div class="container-fluid">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="/">EstefCasting</a>
            </div>
            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <?php if (self::isLoggedIn()) : ?>
                    <ul class="nav navbar-nav">
                        <li><a href="/profiles/create">Create Profile</a></li>
                        <li><a href="/profiles">Profiles</a></li>
                        <?php if (self::is_user_role(['Super Admin', 'Admin'])) : ?>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Manage Users <span class="caret"></span></a>
                            <ul class="dropdown-menu" role="menu">
                                <li><a href="/users/all">All Users</a></li>
                                <li><a href="/users/create">Create New User</a></li>
                            </ul>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Admin<span class="caret"></span></a>
                            <ul class="dropdown-menu" role="menu">
                                <li><a href="/admin">Files</a></li>
                                <li><a href="/admin/logs">Logs</a></li>
                                <li><a href="/admin/statusReport">Status Report</a></li>
                                <li><a href="/admin/showRoutes" target="_blank">Routes</a></li>
                                <li><a href="/admin/info" target="_blank">System Info</a></li>
                            </ul>
                            <?php endif;?>
                        </li>
                    </ul>
                <?php endif; ?>
                <form class="navbar-form navbar-left" method="GET" action="/profiles/search" role="search">
                    <div class="form-group">
                        <input type="text" name="query" class="form-control" placeholder="Search">
                    </div>
                    <?php $disable = !self::isLoggedIn() ? ' disabled="disabled"' : '' ; ?>
                    <button type="submit" class="btn btn-default"<?=$disable?>> Buscar</button>
                </form>
                <ul class="nav navbar-nav navbar-right">
                    <?php if (self::isLoggedIn()) : ?>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><?php echo self::getUser('username'); ?> <span class="caret"></span></a>
                            <ul class="dropdown-menu" role="menu">
                                <li><a href="/users">Change Password</a></li>
                                <li class="divider"></li>
                                <li><a href="/users/logout">Logout</a></li>
                            </ul>
                        </li>
                    <?php endif; ?>
                    <?php if (!self::isLoggedIn()) : ?>
                        <li><a href="/login">Login</a></li>
                    <?php endif; ?>
                </ul>
            </div>
            <!-- /.navbar-collapse -->
        </div>
        <!-- /.container-fluid -->
    </nav>
    <div class="container">
        @render_feedback@
        @yield
    </div>
    <!-- end container -->
</div>
<!-- end content-wrapper -->
<div id="footer">
    <footer class="panel-footer">
        <div class="container">
            <p>Created by Jon Garcia</p>
            <p><a href="mailto:garciajon@me.com">garciajon@me.com</a></p>
            <p>
                <script src="//platform.linkedin.com/in.js" type="text/javascript"></script>
                <script type="IN/MemberProfile" data-id="https://www.linkedin.com/in/jonag" data-format="hover" data-text="Jon Garcia"></script>
            </p>
        </div>
    </footer>
</div>
<script src="/themes/lightbox/js/lightbox.js"></script>
</body>
</html>