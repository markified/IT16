<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">

    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <meta name="description" content="">

    <meta name="author" content="">
    <!-- Custom fonts for this template-->

    <link href="{{ asset('admin_assets/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">

    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <!-- Custom styles for this template-->

    <link href="{{ asset('admin_assets/css/sb-admin-2.min.css') }}" rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link href="{{ asset('admin_assets/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">

    <!-- Custom styles for sticky sidebar -->
    <style>
        .sidebar {
            position: sticky !important;
            top: 0;
            height: 100vh;
            overflow-y: auto;
            overflow-x: hidden;
        }
        .sidebar.toggled {
            overflow-y: auto !important;
            overflow-x: hidden !important;
        }
        .sidebar::-webkit-scrollbar {
            width: 6px;
        }
        .sidebar::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.1);
        }
        .sidebar::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.3);
            border-radius: 3px;
        }
        .sidebar::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.5);
        }
        .sidebar.toggled::-webkit-scrollbar {
            width: 4px;
        }
        /* Logo responsiveness in sidebar */
        .sidebar-brand {
            padding: 1.5rem 1rem !important;
            min-height: 80px;
        }
        .sidebar-brand img {
            max-width: 100%;
            width: 180px;
            height: auto;
            transition: all 0.3s ease;
            filter: brightness(1.1);
        }
        .sidebar.toggled .sidebar-brand img {
            width: 50px;
        }
        @media (max-width: 768px) {
            .sidebar-brand img {
                width: 120px;
            }
        }

        /* Fixed Topbar - aligned with sidebar */
        .sticky-topbar {
            position: fixed !important;
            top: 0;
            right: 0;
            left: 12rem; /* sidebar expanded width */
            z-index: 1030;
            margin-left: 0;
            transition: left 0.15s ease-in-out;
        }
        body.sidebar-toggled .sticky-topbar {
            left: 6.5rem; /* sidebar collapsed width */
        }
        @media (max-width: 768px) {
            .sticky-topbar {
                left: 0 !important;
            }
        }
        /* Content padding to account for fixed navbar */
        #content {
            padding-top: 80px;
        }

        /* Fixed Footer - aligned with sidebar */
        .fixed-footer {
            position: fixed !important;
            bottom: 0;
            right: 0;
            left: 12rem; /* sidebar expanded width */
            z-index: 1020;
            margin-left: 0;
            transition: left 0.15s ease-in-out;
        }
        body.sidebar-toggled .fixed-footer {
            left: 6.5rem; /* sidebar collapsed width */
        }
        @media (max-width: 768px) {
            .fixed-footer {
                left: 0 !important;
            }
        }
        /* Content padding to account for fixed footer */
        #content-wrapper {
            padding-bottom: 65px;
        }

        /* Theme Toggle Switch */
        .theme-toggle-wrapper {
            position: relative;
        }
        .theme-toggle-input {
            opacity: 0;
            position: absolute;
            width: 0;
            height: 0;
        }
        .theme-toggle-label {
            display: flex;
            align-items: center;
            justify-content: space-between;
            cursor: pointer;
            width: 55px;
            height: 28px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50px;
            position: relative;
            padding: 5px;
            transition: all 0.3s ease;
        }
        .theme-toggle-label i {
            font-size: 12px;
            z-index: 1;
        }
        .theme-toggle-label .fa-sun {
            color: #f39c12;
        }
        .theme-toggle-label .fa-moon {
            color: #f1c40f;
        }
        .toggle-ball {
            position: absolute;
            top: 3px;
            left: 3px;
            width: 22px;
            height: 22px;
            background: #fff;
            border-radius: 50%;
            transition: transform 0.3s ease;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }
        .theme-toggle-input:checked + .theme-toggle-label {
            background: linear-gradient(135deg, #2c3e50 0%, #1a252f 100%);
        }
        .theme-toggle-input:checked + .theme-toggle-label .toggle-ball {
            transform: translateX(27px);
        }

        /* Dark Mode Styles */
        body.dark-mode {
            background-color: #1a1a2e !important;
        }
        body.dark-mode #content-wrapper {
            background-color: #1a1a2e !important;
        }
        body.dark-mode #content {
            background-color: #1a1a2e !important;
        }
        body.dark-mode .topbar {
            background-color: #16213e !important;
        }
        body.dark-mode .topbar .navbar-search .form-control {
            background-color: #1a1a2e !important;
            border: 1px solid #4a4a6a !important;
            color: #e0e0e0 !important;
        }
        body.dark-mode .topbar .navbar-search .form-control::placeholder {
            color: #888 !important;
        }
        body.dark-mode .text-gray-800,
        body.dark-mode .text-gray-600,
        body.dark-mode .text-gray-400 {
            color: #e0e0e0 !important;
        }
        body.dark-mode .card {
            background-color: #16213e !important;
            border-color: #2a2a4a !important;
        }
        body.dark-mode .card-header {
            background-color: #0f3460 !important;
            border-color: #2a2a4a !important;
        }
        body.dark-mode .card-body {
            color: #e0e0e0 !important;
        }
        body.dark-mode .table {
            color: #e0e0e0 !important;
        }
        body.dark-mode .table thead th {
            background-color: #0f3460 !important;
            border-color: #2a2a4a !important;
            color: #e0e0e0 !important;
        }
        body.dark-mode .table td,
        body.dark-mode .table th {
            border-color: #2a2a4a !important;
        }
        body.dark-mode .table-striped tbody tr:nth-of-type(odd) {
            background-color: rgba(255, 255, 255, 0.03) !important;
        }
        body.dark-mode .table-hover tbody tr:hover {
            background-color: rgba(255, 255, 255, 0.08) !important;
        }
        /* Table contextual classes in dark mode */
        body.dark-mode .table-primary,
        body.dark-mode .table-primary > th,
        body.dark-mode .table-primary > td,
        body.dark-mode tr.table-primary td {
            background-color: #1a3a5c !important;
            color: #e0e0e0 !important;
        }
        body.dark-mode tr.table-primary td strong,
        body.dark-mode tr.table-primary td small,
        body.dark-mode tr.table-primary td code {
            color: #e0e0e0 !important;
        }
        body.dark-mode .table-info,
        body.dark-mode .table-info > th,
        body.dark-mode .table-info > td,
        body.dark-mode tr.table-info,
        body.dark-mode tr.table-info td {
            background-color: #1a3a4a !important;
            color: #e0e0e0 !important;
        }
        body.dark-mode tr.table-info td strong,
        body.dark-mode tr.table-info td small,
        body.dark-mode tr.table-info td code,
        body.dark-mode tr.table-info td .text-muted {
            color: #c0c0d0 !important;
        }
        body.dark-mode tr.table-info td code {
            background-color: #2a4a5a !important;
            color: #7dd3fc !important;
        }
        body.dark-mode .table-success,
        body.dark-mode .table-success > th,
        body.dark-mode .table-success > td,
        body.dark-mode tr.table-success td {
            background-color: #1a4a3a !important;
            color: #e0e0e0 !important;
        }
        body.dark-mode .table-warning,
        body.dark-mode .table-warning > th,
        body.dark-mode .table-warning > td,
        body.dark-mode tr.table-warning td {
            background-color: #4a4a2a !important;
            color: #e0e0e0 !important;
        }
        body.dark-mode .table-danger,
        body.dark-mode .table-danger > th,
        body.dark-mode .table-danger > td,
        body.dark-mode tr.table-danger td {
            background-color: #4a2a2a !important;
            color: #e0e0e0 !important;
        }
        body.dark-mode .table-secondary,
        body.dark-mode .table-secondary > th,
        body.dark-mode .table-secondary > td,
        body.dark-mode tr.table-secondary td {
            background-color: #2a2a3a !important;
            color: #e0e0e0 !important;
        }
        body.dark-mode .table-light,
        body.dark-mode .table-light > th,
        body.dark-mode .table-light > td,
        body.dark-mode tr.table-light td {
            background-color: #1a1a2e !important;
            color: #e0e0e0 !important;
        }
        body.dark-mode thead.table-primary th {
            background-color: #0f3460 !important;
            color: #e0e0e0 !important;
        }
        body.dark-mode .sticky-footer {
            background-color: #16213e !important;
        }
        body.dark-mode .sticky-footer span {
            color: #e0e0e0 !important;
        }
        body.dark-mode .dropdown-menu {
            background-color: #16213e !important;
            border-color: #2a2a4a !important;
        }
        body.dark-mode .dropdown-item {
            color: #e0e0e0 !important;
        }
        body.dark-mode .dropdown-item:hover {
            background-color: #0f3460 !important;
        }
        body.dark-mode .dropdown-divider {
            border-color: #2a2a4a !important;
        }
        body.dark-mode .bg-white {
            background-color: #16213e !important;
        }
        body.dark-mode .bg-light {
            background-color: #1a1a2e !important;
        }
        body.dark-mode code {
            background-color: #2a2a4a !important;
            color: #ff79c6 !important;
        }
        body.dark-mode .text-dark {
            color: #e0e0e0 !important;
        }
        body.dark-mode .text-muted {
            color: #9090b0 !important;
        }
        body.dark-mode strong {
            color: #e0e0e0;
        }
        body.dark-mode small {
            color: #b0b0c0;
        }
        body.dark-mode .border-left-primary {
            border-left-color: #4e73df !important;
        }
        body.dark-mode .border-left-success {
            border-left-color: #1cc88a !important;
        }
        body.dark-mode .border-left-info {
            border-left-color: #36b9cc !important;
        }
        body.dark-mode .border-left-warning {
            border-left-color: #f6c23e !important;
        }
        body.dark-mode .form-control {
            background-color: #1a1a2e !important;
            border-color: #4a4a6a !important;
            color: #e0e0e0 !important;
        }
        body.dark-mode .form-control:focus {
            background-color: #1a1a2e !important;
            border-color: #4e73df !important;
            color: #e0e0e0 !important;
        }
        body.dark-mode .input-group-text {
            background-color: #0f3460 !important;
            border-color: #4a4a6a !important;
            color: #e0e0e0 !important;
        }
        body.dark-mode label {
            color: #e0e0e0 !important;
        }
        body.dark-mode .modal-content {
            background-color: #16213e !important;
            border-color: #2a2a4a !important;
        }
        body.dark-mode .modal-header {
            border-color: #2a2a4a !important;
        }
        body.dark-mode .modal-footer {
            border-color: #2a2a4a !important;
        }
        body.dark-mode .modal-title {
            color: #e0e0e0 !important;
        }
        body.dark-mode .close {
            color: #e0e0e0 !important;
        }
        body.dark-mode h1, body.dark-mode h2, body.dark-mode h3,
        body.dark-mode h4, body.dark-mode h5, body.dark-mode h6 {
            color: #e0e0e0 !important;
        }
        body.dark-mode .breadcrumb {
            background-color: #16213e !important;
        }
        body.dark-mode .breadcrumb-item a {
            color: #4e73df !important;
        }
        body.dark-mode .page-link {
            background-color: #16213e !important;
            border-color: #2a2a4a !important;
            color: #4e73df !important;
        }
        body.dark-mode .page-item.active .page-link {
            background-color: #4e73df !important;
            border-color: #4e73df !important;
        }
        body.dark-mode .alert {
            border-color: #2a2a4a !important;
        }
        body.dark-mode .list-group-item {
            background-color: #16213e !important;
            border-color: #2a2a4a !important;
            color: #e0e0e0 !important;
        }
        body.dark-mode .dataTables_wrapper .dataTables_length,
        body.dark-mode .dataTables_wrapper .dataTables_filter,
        body.dark-mode .dataTables_wrapper .dataTables_info,
        body.dark-mode .dataTables_wrapper .dataTables_paginate {
            color: #e0e0e0 !important;
        }
        body.dark-mode select.form-control {
            background-color: #1a1a2e !important;
            color: #e0e0e0 !important;
        }
        body.dark-mode .custom-select {
            background-color: #1a1a2e !important;
            border-color: #4a4a6a !important;
            color: #e0e0e0 !important;
        }
        /* Sidebar collapse items in dark mode */
        body.dark-mode .sidebar .collapse-inner {
            background-color: #1a1a2e !important;
            border: 1px solid #2a2a4a !important;
        }
        body.dark-mode .sidebar .collapse-inner.rounded {
            background-color: #1a1a2e !important;
        }
        body.dark-mode .sidebar .bg-white {
            background-color: #1a1a2e !important;
        }
        body.dark-mode .sidebar .collapse-item {
            color: #e0e0e0 !important;
        }
        body.dark-mode .sidebar .collapse-item:hover {
            background-color: #0f3460 !important;
        }
        body.dark-mode .sidebar .collapse-item.active {
            background-color: #0f3460 !important;
            color: #4e73df !important;
        }
        body.dark-mode .sidebar .collapse-header {
            color: #aaa !important;
            background-color: transparent !important;
        }

        /* Bootstrap 5 Badge Background Support for Bootstrap 4 */
        .badge.bg-primary { background-color: #4e73df !important; color: #fff !important; }
        .badge.bg-secondary { background-color: #858796 !important; color: #fff !important; }
        .badge.bg-success { background-color: #1cc88a !important; color: #fff !important; }
        .badge.bg-danger { background-color: #e74a3b !important; color: #fff !important; }
        .badge.bg-warning { background-color: #f6c23e !important; color: #212529 !important; }
        .badge.bg-info { background-color: #36b9cc !important; color: #fff !important; }
        .badge.bg-light { background-color: #f8f9fc !important; color: #212529 !important; }
        .badge.bg-dark { background-color: #5a5c69 !important; color: #fff !important; }
        
        /* Bootstrap 5 spacing utilities for Bootstrap 4 compatibility */
        .me-1 { margin-right: 0.25rem !important; }
        .me-2 { margin-right: 0.5rem !important; }
        .me-3 { margin-right: 1rem !important; }
        .ms-1 { margin-left: 0.25rem !important; }
        .ms-2 { margin-left: 0.5rem !important; }
        .ms-3 { margin-left: 1rem !important; }
        .fs-6 { font-size: 1rem !important; }

        /* Smooth transitions */
        body, #content-wrapper, #content, .topbar, .sticky-footer, .card,
        .card-header, .card-body, .table, .dropdown-menu, .form-control,
        .modal-content {
            transition: background-color 0.3s ease, color 0.3s ease, border-color 0.3s ease;
        }
    </style>
</head>

<body id="page-top">
    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
        @include('layouts.sidebar')
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                @include('layouts.navbar')
                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                <div class="container-fluid">

                    <!-- Page Heading -->
                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <h1 class="h3 mb-0 text-gray-800">@yield('title')</h1>
                    </div>

                    @yield('contents')

                    <!-- Content Row -->


                </div>
                <!-- /.container-fluid -->

            </div>
            <!-- End of Main Content -->

            <!-- Footer -->
            @include('layouts.footer')
            <!-- End of Footer -->

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Notification Modal -->
    <div class="modal fade" id="notificationModal" tabindex="-1" role="dialog" aria-labelledby="notificationModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header" id="notificationModalHeader">
                    <h5 class="modal-title" id="notificationModalLabel">
                        <i id="notificationModalIcon" class="mr-2"></i>
                        <span id="notificationModalTitle">Notification</span>
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="notificationModalBody">
                    <!-- Message will be inserted here -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Confirmation Modal -->
    <div class="modal fade" id="confirmModal" tabindex="-1" role="dialog" aria-labelledby="confirmModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header bg-warning">
                    <h5 class="modal-title" id="confirmModalLabel">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        <span id="confirmModalTitle">Confirm Action</span>
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="confirmModalBody">
                    Are you sure you want to proceed?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmModalBtn">
                        <i class="fas fa-check mr-1"></i> Confirm
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="deleteModalLabel">
                        <i class="fas fa-trash-alt mr-2"></i>
                        <span>Confirm Delete</span>
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p id="deleteModalBody">Are you sure you want to delete this item? This action cannot be undone.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times mr-1"></i> Cancel
                    </button>
                    <button type="button" class="btn btn-danger" id="deleteModalBtn">
                        <i class="fas fa-trash-alt mr-1"></i> Delete
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Validation Error Modal -->
    <div class="modal fade" id="validationModal" tabindex="-1" role="dialog" aria-labelledby="validationModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="validationModalLabel">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        Validation Error
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="validationModalBody">
                    <ul class="mb-0 pl-3" id="validationErrorList">
                        <!-- Errors will be inserted here -->
                    </ul>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap core JavaScript-->
    <script src="{{ asset('admin_assets/vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('admin_assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <!-- Core plugin JavaScript-->
    <script src="{{ asset('admin_assets/vendor/jquery-easing/jquery.easing.min.js') }}"></script>
    <!-- Custom scripts for all pages-->
    <script src="{{ asset('admin_assets/js/sb-admin-2.min.js') }}"></script>
    <!-- Page level plugins -->
    <script src="{{ asset('admin_assets/vendor/chart.js/Chart.min.js') }}"></script>
    
    <!-- Page Specific Scripts -->
    @stack('scripts')

    <!-- Global Modal Functions -->
    <script>
        // Show notification modal
        function showNotification(type, title, message) {
            const modal = $('#notificationModal');
            const header = $('#notificationModalHeader');
            const icon = $('#notificationModalIcon');
            const titleEl = $('#notificationModalTitle');
            const body = $('#notificationModalBody');
            
            // Reset classes
            header.removeClass('bg-success bg-danger bg-warning bg-info text-white');
            icon.removeClass();
            
            // Set styling based on type
            switch(type) {
                case 'success':
                    header.addClass('bg-success text-white');
                    icon.addClass('fas fa-check-circle mr-2');
                    break;
                case 'error':
                    header.addClass('bg-danger text-white');
                    icon.addClass('fas fa-times-circle mr-2');
                    break;
                case 'warning':
                    header.addClass('bg-warning');
                    icon.addClass('fas fa-exclamation-triangle mr-2');
                    break;
                case 'info':
                    header.addClass('bg-info text-white');
                    icon.addClass('fas fa-info-circle mr-2');
                    break;
            }
            
            titleEl.text(title);
            body.html(message);
            modal.modal('show');
        }
        
        // Show success notification
        function showSuccess(message, title = 'Success') {
            showNotification('success', title, message);
        }
        
        // Show error notification
        function showError(message, title = 'Error') {
            showNotification('error', title, message);
        }
        
        // Show warning notification
        function showWarning(message, title = 'Warning') {
            showNotification('warning', title, message);
        }
        
        // Show info notification
        function showInfo(message, title = 'Information') {
            showNotification('info', title, message);
        }
        
        // Show validation errors modal
        function showValidationErrors(errors) {
            const errorList = $('#validationErrorList');
            errorList.empty();
            
            if (Array.isArray(errors)) {
                errors.forEach(function(error) {
                    errorList.append('<li>' + error + '</li>');
                });
            } else if (typeof errors === 'object') {
                Object.values(errors).forEach(function(errorArray) {
                    if (Array.isArray(errorArray)) {
                        errorArray.forEach(function(error) {
                            errorList.append('<li>' + error + '</li>');
                        });
                    } else {
                        errorList.append('<li>' + errorArray + '</li>');
                    }
                });
            } else {
                errorList.append('<li>' + errors + '</li>');
            }
            
            $('#validationModal').modal('show');
        }
        
        // Confirmation modal with callback
        function showConfirm(message, callback, title = 'Confirm Action') {
            $('#confirmModalTitle').text(title);
            $('#confirmModalBody').html(message);
            
            const confirmBtn = $('#confirmModalBtn');
            confirmBtn.off('click').on('click', function() {
                $('#confirmModal').modal('hide');
                if (typeof callback === 'function') {
                    callback();
                }
            });
            
            $('#confirmModal').modal('show');
        }
        
        // Delete confirmation modal
        function confirmDelete(formElement, message = 'Are you sure you want to delete this item? This action cannot be undone.') {
            const form = $(formElement).closest('form');
            $('#deleteModalBody').html(message);
            
            const deleteBtn = $('#deleteModalBtn');
            deleteBtn.off('click').on('click', function() {
                $('#deleteModal').modal('hide');
                form.submit();
            });
            
            $('#deleteModal').modal('show');
            return false;
        }
        
        // Handle forms with data-confirm attribute
        $(document).ready(function() {
            // Handle delete buttons with data-confirm-delete
            $(document).on('click', '[data-confirm-delete]', function(e) {
                e.preventDefault();
                const message = $(this).data('confirm-delete') || 'Are you sure you want to delete this item? This action cannot be undone.';
                confirmDelete(this, message);
            });
            
            // Handle forms with data-confirm attribute
            $(document).on('submit', 'form[data-confirm]', function(e) {
                const form = this;
                if (!$(form).data('confirmed')) {
                    e.preventDefault();
                    const message = $(form).data('confirm');
                    showConfirm(message, function() {
                        $(form).data('confirmed', true);
                        form.submit();
                    });
                }
            });
            
            // Handle buttons with data-confirm attribute
            $(document).on('click', 'button[data-confirm], a[data-confirm]', function(e) {
                const el = $(this);
                if (!el.data('confirmed')) {
                    e.preventDefault();
                    const message = el.data('confirm');
                    showConfirm(message, function() {
                        el.data('confirmed', true);
                        el[0].click();
                    });
                }
            });
        });
    </script>
    
    <!-- Handle Laravel Session Messages -->
    @if(session('success'))
    <script>
        $(document).ready(function() {
            showSuccess("{{ session('success') }}");
        });
    </script>
    @endif
    
    @if(session('error'))
    <script>
        $(document).ready(function() {
            showError("{{ session('error') }}");
        });
    </script>
    @endif
    
    @if(session('warning'))
    <script>
        $(document).ready(function() {
            showWarning("{{ session('warning') }}");
        });
    </script>
    @endif
    
    @if(session('info'))
    <script>
        $(document).ready(function() {
            showInfo("{{ session('info') }}");
        });
    </script>
    @endif
    
    @if($errors->any())
    <script>
        $(document).ready(function() {
            showValidationErrors(@json($errors->all()));
        });
    </script>
    @endif

    <!-- Sidebar Toggle Handler for Fixed Navbar/Footer -->
    <script>
        (function() {
            // Handle sidebar toggle state for fixed navbar/footer positioning
            function updateSidebarState() {
                const sidebar = document.getElementById('accordionSidebar');
                if (sidebar && sidebar.classList.contains('toggled')) {
                    document.body.classList.add('sidebar-toggled');
                } else {
                    document.body.classList.remove('sidebar-toggled');
                }
            }

            // Initialize on page load
            updateSidebarState();

            // Watch for sidebar toggle clicks
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebarToggleTop = document.getElementById('sidebarToggleTop');
            
            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', function() {
                    setTimeout(updateSidebarState, 50);
                });
            }
            if (sidebarToggleTop) {
                sidebarToggleTop.addEventListener('click', function() {
                    setTimeout(updateSidebarState, 50);
                });
            }

            // Also observe for class changes on sidebar
            const sidebar = document.getElementById('accordionSidebar');
            if (sidebar) {
                const observer = new MutationObserver(function(mutations) {
                    mutations.forEach(function(mutation) {
                        if (mutation.attributeName === 'class') {
                            updateSidebarState();
                        }
                    });
                });
                observer.observe(sidebar, { attributes: true });
            }
        })();
    </script>

    <!-- Dark Mode Toggle Script -->
    <script>
        (function() {
            const themeToggle = document.getElementById('themeToggle');
            const body = document.body;
            const THEME_KEY = 'theme-preference';

            // Load saved theme preference
            function loadTheme() {
                const savedTheme = localStorage.getItem(THEME_KEY);
                if (savedTheme === 'dark') {
                    body.classList.add('dark-mode');
                    themeToggle.checked = true;
                } else if (savedTheme === 'light') {
                    body.classList.remove('dark-mode');
                    themeToggle.checked = false;
                } else {
                    // Check system preference
                    if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
                        body.classList.add('dark-mode');
                        themeToggle.checked = true;
                    }
                }
            }

            // Toggle theme
            function toggleTheme() {
                if (themeToggle.checked) {
                    body.classList.add('dark-mode');
                    localStorage.setItem(THEME_KEY, 'dark');
                } else {
                    body.classList.remove('dark-mode');
                    localStorage.setItem(THEME_KEY, 'light');
                }
            }

            // Initialize
            loadTheme();
            themeToggle.addEventListener('change', toggleTheme);

            // Listen for system theme changes
            if (window.matchMedia) {
                window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
                    if (!localStorage.getItem(THEME_KEY)) {
                        if (e.matches) {
                            body.classList.add('dark-mode');
                            themeToggle.checked = true;
                        } else {
                            body.classList.remove('dark-mode');
                            themeToggle.checked = false;
                        }
                    }
                });
            }
        })();
    </script>
</body>

</html>