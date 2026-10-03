<!DOCTYPE html>
    <!-- BEGIN HEAD -->
    <head>
        @include('dashboards.partials.head')
        <link href="../assets/pages/css/login-2.css" rel="stylesheet" type="text/css" />
    </head>
    <!-- END HEAD -->

    <body class="login">
    <!-- BEGIN LOGO -->
        <div class="logo">
            <a>
                <img src="../assets/pages/img/jas.png" style="height: 17px;" alt="" /> </a>
        </div>
        <!-- END LOGO -->
        <!-- BEGIN LOGIN -->
        <div class="content">
            <form action="index.blade.php" method="post">
                <div class="form-title">
                    <span class="form-title">Forget Password ?</span>
                    <span class="form-subtitle">Enter your e-mail to reset it.</span>
                </div>
                <div class="form-group">
                    <input class="form-control placeholder-no-fix" type="text" autocomplete="off" placeholder="Email" name="email" /> </div>
                <div class="form-actions">
                    <button type="button" id="back-btn" class="btn btn-default">Back</button>
                    <button type="submit" class="btn btn-primary uppercase pull-right">Submit</button>
                </div>
            </form>

        </div>
        <div class="copyright hide"> 2018 © BihaniTech </div>
        @include('dashboards.partials.js')
        <!-- END THEME GLOBAL SCRIPTS -->
        <!-- BEGIN PAGE LEVEL SCRIPTS -->
        <script src="../assets/global/plugins/jquery-validation/js/jquery.validate.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/jquery-validation/js/additional-methods.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/select2/js/select2.full.min.js" type="text/javascript"></script>
        <script src="../assets/pages/scripts/login.min.js" type="text/javascript"></script>

        <!-- END PAGE LEVEL SCRIPTS -->
    </body>

</html>
