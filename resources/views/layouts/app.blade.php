<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap">

        <!-- Styles -->
        <!-- BEGIN: Vendor CSS-->
        <link rel="stylesheet" type="text/css" href="{{asset('css/vendors.min.css')}}">
        <link rel="stylesheet" type="text/css" href="{{asset('css/forms/select/select2.min.css')}}">
        <link rel="stylesheet" type="text/css" href="{{asset('css/tables/datatable/dataTables.bootstrap5.min.css')}}">
        <link rel="stylesheet" type="text/css" href="{{asset('css/tables/datatable/responsive.bootstrap5.min.css')}}">
        <link rel="stylesheet" type="text/css" href="{{asset('css/tables/datatable/buttons.bootstrap5.min.css')}}">
        <link rel="stylesheet" type="text/css" href="{{asset('css/tables/datatable/rowGroup.bootstrap5.min.css')}}">
        <link rel="stylesheet" type="text/css" href="{{asset('/')}}css/toastr.min.css">
        <!-- END: Vendor CSS-->

        <!-- BEGIN: Theme CSS-->
        <link rel="stylesheet" type="text/css" href="{{asset('css/bootstrap.min.css')}}">
        <link rel="stylesheet" type="text/css" href="{{asset('css/bootstrap-extended.min.css')}}">
        <link rel="stylesheet" type="text/css" href="{{asset('css/colors.min.css')}}">
        <link rel="stylesheet" type="text/css" href="{{asset('css/components.min.css')}}">
        <link rel="stylesheet" type="text/css" href="{{asset('css/themes/dark-layout.min.css')}}">
        <link rel="stylesheet" type="text/css" href="{{asset('css/themes/bordered-layout.min.css')}}">
        <link rel="stylesheet" type="text/css" href="{{asset('css/themes/semi-dark-layout.min.css')}}">

        <!-- BEGIN: Page CSS-->
        <link rel="stylesheet" type="text/css" href="{{asset('css/vertical-menu.min.css')}}">
        <link rel="stylesheet" type="text/css" href="{{asset('css/forms/form-validation.css')}}">
        <link rel="stylesheet" type="text/css" href="{{asset('css/authentication.css')}}">
        <!-- END: Page CSS-->

        <!-- BEGIN: Custom CSS-->
        <link rel="stylesheet" type="text/css" href="{{asset('css/style.css')}}">
        <!-- END: Custom CSS-->
        <!-- BEGIN: Vendor JS-->
        <script src="{{asset('js/vendors.min.js')}}"></script>
        <!-- BEGIN Vendor JS-->

        <!-- BEGIN: Page Vendor JS-->
        <script src="{{asset('js/forms/select/select2.full.min.js')}}"></script>
        <script src="{{asset('js/tables/datatable/jquery.dataTables.min.js')}}"></script>
        <script src="{{asset('js/tables/datatable/dataTables.bootstrap5.min.js')}}"></script>
        <script src="{{asset('js/tables/datatable/dataTables.responsive.min.js')}}"></script>
        <script src="{{asset('js/tables/datatable/responsive.bootstrap5.js')}}"></script>
        <script src="{{asset('js/tables/datatable/datatables.buttons.min.js')}}"></script>
        <script src="{{asset('js/tables/datatable/jszip.min.js')}}"></script>
        <script src="{{asset('js/tables/datatable/pdfmake.min.js')}}"></script>
        <script src="{{asset('js/tables/datatable/vfs_fonts.js')}}"></script>
        <script src="{{asset('js/tables/datatable/buttons.html5.min.js')}}"></script>
        <script src="{{asset('js/tables/datatable/buttons.print.min.js')}}"></script>
        <script src="{{asset('js/tables/datatable/dataTables.rowGroup.min.js')}}"></script>
        <script src="{{asset('js/forms/validation/jquery.validate.min.js')}}"></script>
        <script src="{{asset('js/forms/cleave/cleave.min.js')}}"></script>
        <script src="{{asset('js/forms/cleave/addons/cleave-phone.us.js')}}"></script>
        <script src="{{asset('/')}}js/toastr.min.js"></script>
        <!-- END: Page Vendor JS-->

        <!-- BEGIN: Theme JS-->
        <script src="{{asset('js/core/app-menu.min.js')}}"></script>
        <script src="{{asset('js/core/app.min.js')}}"></script>
        <script src="{{asset('js/customizer.min.js')}}"></script>
        <!-- END: Theme JS-->
        <script>
            let token = '{{csrf_token()}}';
            $(window).on('load', function () {
                if (feather) {
                    feather.replace({width: 14, height: 14});
                }
            })
        </script>
    </head>
    <body class="vertical-layout vertical-menu-modern  navbar-floating footer-static  " data-open="click" data-menu="vertical-menu-modern" data-col="">
        @include('layouts.navigation')
        @include('layouts.menu')

        <!-- BEGIN: Content-->
        <div class="app-content content ">
            <div class="content-overlay"></div>
            <div class="header-navbar-shadow"></div>
            <div class="content-wrapper container-xxl p-0">
                {{ $slot }}
            </div>
        </div>
        <!-- END: Content-->

    </body>
</html>
