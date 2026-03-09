<!DOCTYPE html>
<html class="loading" lang="en" data-textdirection="ltr">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1.0,user-scalable=0,minimal-ui">
    <meta name="description" content="monsieur-wifi cloud controller dashboard for managing and monitoring WiFi networks.">
    <meta name="keywords" content="wifi, cloud controller, network management, monsieur-wifi">
    <meta name="author" content="monsieur-wifi">
    <title>Location Details - monsieur-wifi Controller</title>
    <link rel="apple-touch-icon" href="/app-assets/images/ico/apple-icon-120.png">
    <link rel="shortcut icon" type="image/x-icon" href="/app-assets/mrwifi-assets/MrWifi.png">

    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,300;0,400;0,500;0,600;1,400;1,500;1,600" rel="stylesheet">

    <!-- BEGIN: Vendor CSS-->
    <link rel="stylesheet" type="text/css" href="/app-assets/vendors/css/vendors.min.css">
    <link rel="stylesheet" type="text/css" href="/app-assets/vendors/css/charts/apexcharts.css">
    <link rel="stylesheet" type="text/css" href="/app-assets/vendors/css/extensions/toastr.min.css">
    <link rel="stylesheet" type="text/css" href="/app-assets/vendors/css/forms/select/select2.min.css">
    <link rel="stylesheet" type="text/css" href="/app-assets/vendors/css/pickers/flatpickr/flatpickr.min.css">
    <!-- END: Vendor CSS-->
    <!-- BEGIN: Theme CSS-->
    <link rel="stylesheet" type="text/css" href="/app-assets/css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="/app-assets/css/bootstrap-extended.css">
    <link rel="stylesheet" type="text/css" href="/app-assets/css/colors.css">
    <link rel="stylesheet" type="text/css" href="/app-assets/css/components.css">
    <link rel="stylesheet" type="text/css" href="/app-assets/css/themes/dark-layout.css">
    <link rel="stylesheet" type="text/css" href="/app-assets/css/themes/bordered-layout.css">
    <link rel="stylesheet" type="text/css" href="/app-assets/css/themes/semi-dark-layout.css">

    <!-- BEGIN: Page CSS-->
    <link rel="stylesheet" type="text/css" href="/app-assets/css/core/menu/menu-types/vertical-menu.css">
    <link rel="stylesheet" type="text/css" href="/app-assets/css/plugins/charts/chart-apex.css">
    <link rel="stylesheet" type="text/css" href="/app-assets/css/plugins/extensions/ext-component-toastr.css">
    <link rel="stylesheet" type="text/css" href="/app-assets/vendors/css/maps/leaflet.min.css">
    <link rel="stylesheet" type="text/css" href="/app-assets/css/plugins/maps/map-leaflet.css">
    <link rel="stylesheet" type="text/css" href="/app-assets/css/plugins/forms/pickers/form-flat-pickr.css">
    <!-- END: Page CSS-->

    <!-- BEGIN: Custom CSS-->
    <link rel="stylesheet" type="text/css" href="/assets/css/style.css">
    <!-- END: Custom CSS-->

    <style>
        /* ==============================================
           MODERN STATUS BADGES
        ============================================== */
        .status-badge {
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
        }

        .custom-btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
            color: white !important;
            box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3) !important;
        }
        
        .status-online {
            background: linear-gradient(45deg, #28c76f, #48da89);
            color: white;
            box-shadow: 0 2px 8px rgba(40, 199, 111, 0.3);
        }
        
        .status-offline {
            background: linear-gradient(45deg, #ea5455, #ff6b6b);
            color: white;
            box-shadow: 0 2px 8px rgba(234, 84, 85, 0.3);
        }
        
        .status-warning {
            background: linear-gradient(45deg, #ff9f43, #ffb976);
            color: white;
            box-shadow: 0 2px 8px rgba(255, 159, 67, 0.3);
        }
        
        /* ==============================================
           ENHANCED CARDS
        ============================================== */
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 2px 20px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
            background: #fff;
            margin-bottom: 1.5rem;
        }
        
        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(0,0,0,0.12);
        }

        /* ==============================================
           INTERACTIVE SCHEDULE STYLING
        ============================================== */
        .schedule-container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1),
                0 2px 4px -1px rgba(0, 0, 0, 0.06);
            overflow: hidden;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto,
                sans-serif;
            padding: 0 1rem;
            box-sizing: border-box;
        }

        .schedule-header {
            padding: 1.5rem;
            background: white;
            color: #333;
            border-bottom: 1px solid #dee2e6;
        }

        .schedule-header h5 {
            color: #333;
            font-weight: 600;
            margin-bottom: 0.25rem;
        }

        .schedule-header .text-muted {
            color: #6c757d !important;
        }

        .quick-actions .btn {
            border-color: #dee2e6;
            color: #495057;
            font-size: clamp(0.4rem, 1.2vw, 0.6rem);
            padding: 0 4px;
            margin-left: 0.5rem;
            transition: all 0.2s ease;
        }

        .quick-actions .btn:hover {
            background-color: #f8f9fa;
            border-color: #adb5bd;
            color: #495057;
            transform: translateY(-1px);
        }

        .schedule-wrapper {
            overflow-x: auto;
            overflow-y: visible;
            width: 100%;
            max-width: 100%;
            scrollbar-width: thin;
            scrollbar-color: #6c757d #f8f9fa;
            box-sizing: border-box;
        }

        .schedule-wrapper::-webkit-scrollbar {
            height: 8px;
        }

        .schedule-wrapper::-webkit-scrollbar-track {
            background: #f8f9fa;
        }

        .schedule-wrapper::-webkit-scrollbar-thumb {
            background: #6c757d;
            border-radius: 4px;
        }

        .schedule-wrapper::-webkit-scrollbar-thumb:hover {
            background: #495057;
        }

        .schedule-grid {
            display: grid;
            grid-template-columns: 120px repeat(24, 60px);
            min-height: 400px;
            min-width: 1592px;
            gap: 1px;
            background-color: #e5e7eb;
            margin: 0;
            overflow: hidden;
            position: relative;
        }

        .time-header {
            display: contents;
        }

        .time-label {
            background: #f8fafc;
            padding: 0.75rem 0.5rem;
            text-align: center;
            font-size: 0.75rem;
            font-weight: 500;
            color: #64748b;
            border-right: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .time-label:first-child {
            background: #f1f5f9;
            border-right: 2px solid #cbd5e1;
        }

        .day-row {
            display: contents;
        }

        .day-label {
            background: #f8fafc;
            padding: 1rem 0.75rem;
            font-weight: 600;
            color: #374151;
            border-right: 2px solid #cbd5e1;
            display: flex;
            align-items: center;
            text-transform: capitalize;
        }

        .time-cell {
            background: white;
            min-height: 60px;
            position: relative;
            cursor: pointer;
            transition: all 0.2s ease;
            border-right: 1px solid #f1f5f9;
        }

        .time-cell:hover {
            background-color: #f0f9ff;
            box-shadow: inset 0 0 0 2px #3b82f6;
        }

        .time-cell.drop-zone {
            background-color: #dcfce7;
            box-shadow: inset 0 0 0 2px #22c55e;
        }

        .time-cell.invalid-drop {
            background-color: #fef2f2;
            box-shadow: inset 0 0 0 2px #ef4444;
        }

        /* Time Slot Styles */
        .time-slot {
            position: absolute;
            top: 8px;
            bottom: 8px;
            left: 2px;
            container-type: inline-size;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            border-radius: 6px;
            color: white;
            font-weight: 600;
            font-size: clamp(0.5rem, calc(0.8rem + 0.5vw), 0.875rem);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: move;
            user-select: none;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            transition: all 0.2s ease;
            z-index: 10;
            min-width: calc(100% - 4px);
            box-sizing: border-box;
            padding: 0px 8px;
            white-space: nowrap;
            text-overflow: ellipsis;
        }

        .time-slot:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        }

        .time-slot.dragging {
            z-index: 1000;
            transform: rotate(2deg) scale(1.05);
            box-shadow: 0 8px 25px rgba(16, 185, 129, 0.4);
        }

        .resize-handle {
            position: absolute;
            top: 0;
            bottom: 0;
            width: 8px;
            background: rgba(255, 255, 255, 0.3);
            cursor: ew-resize;
            opacity: 0;
            transition: opacity 0.2s ease;
        }

        .resize-handle.left {
            left: 0;
            border-radius: 6px 0 0 6px;
        }

        .resize-handle.right {
            right: 0;
            border-radius: 0 6px 6px 0;
        }

        .time-slot:hover .resize-handle {
            opacity: 1;
        }

        @container (min-width: 60px) {
            .time-slot {
                font-size: 0.65rem;
            }
        }

        @container (min-width: 120px) {
            .time-slot {
                font-size: 0.75rem;
            }
        }

        @container (min-width: 180px) {
            .time-slot {
                font-size: 0.875rem;
            }
        }

        .resize-handle:hover {
            background: rgba(255, 255, 255, 0.5);
        }

        .delete-btn {
            position: absolute;
            top: -8px;
            right: -8px;
            width: 20px;
            height: 20px;
            background: #ef4444;
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: bold;
            cursor: pointer;
            opacity: 0;
            transition: all 0.2s ease;
            z-index: 20;
            line-height: 1;
        }

        .time-slot:hover .delete-btn {
            opacity: 1;
            transform: scale(1.1);
        }

        .delete-btn:hover {
            background: #dc2626;
            transform: scale(1.2);
        }

        .schedule-container .border-top {
            border-color: #e5e7eb !important;
        }

        .schedule-container .bg-light {
            background-color: #f8fafc !important;
        }

        .schedule-container .btn-success {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            border: none;
            font-weight: 600;
            padding: 0.5rem 1.5rem;
            transition: all 0.2s ease;
        }

        .schedule-container .btn-success:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        }

        @media (max-width: 768px) {
            .schedule-grid {
                grid-template-columns: 100px repeat(24, minmax(30px, 1fr));
                margin: 0.5rem;
            }

            .schedule-header {
                padding: 1rem;
            }

            .schedule-header .d-flex {
                flex-direction: column;
                gap: 1rem;
            }

            .quick-actions {
                text-align: center;
            }

            .time-label,
            .day-label {
                font-size: 0.75rem;
                padding: 0.5rem 0.25rem;
            }

            .time-cell {
                min-height: 50px;
            }

            .time-slot {
                font-size: 0.75rem;
                top: 4px;
                bottom: 4px;
            }
        }

        @media (max-width: 480px) {
            .schedule-grid {
                grid-template-columns: 80px repeat(24, minmax(25px, 1fr));
            }

            .time-label {
                font-size: 0.625rem;
                padding: 0.25rem;
            }

            .day-label {
                font-size: 0.75rem;
                padding: 0.5rem 0.25rem;
            }

            .time-cell {
                min-height: 40px;
            }

            .time-slot {
                font-size: 0.625rem;
                min-width: 40px;
            }
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        .time-slot {
            animation: slideIn 0.3s ease;
        }

        .schedule-container {
            animation: fadeIn 0.5s ease;
        }

        .schedule-container::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }

        .schedule-container::-webkit-scrollbar-track {
            background: #f1f5f9;
        }

        .schedule-container::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 3px;
        }

        .schedule-container::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        .time-cell:focus,
        .time-slot:focus,
        .delete-btn:focus {
            outline: 2px solid #3b82f6;
            outline-offset: 2px;
        }

        @media (prefers-contrast: high) {
            .time-slot {
                border: 2px solid #000;
            }

            .time-cell:hover {
                border: 2px solid #000;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            .time-slot,
            .schedule-container,
            .delete-btn,
            .resize-handle,
            .quick-actions .btn {
                animation: none;
                transition: none;
            }

            .time-slot:hover {
                transform: none;
            }

            .time-slot.dragging {
                transform: scale(1.05);
            }
        }

        @media print {
            .schedule-container {
                box-shadow: none;
                border: 1px solid #000;
            }

            .quick-actions,
            .delete-btn,
            .resize-handle {
                display: none;
            }

            .schedule-header {
                background: #f5f5f5 !important;
                color: #000 !important;
            }

            .time-slot {
                background: #e5e5e5 !important;
                color: #000 !important;
            }
        }
        
        .card-header {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-bottom: 1px solid rgba(0,0,0,0.05);
            border-radius: 12px 12px 0 0 !important;
            padding: 1.5rem;
        }

        /* ==============================================
           ANALYTICS CHART CARD
        ============================================== */
        .analytics-chart-card {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 20px;
            padding: 0;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            position: relative;
            overflow: hidden;
            height: 100%;
            border: 1px solid rgba(0, 0, 0, 0.05);
        }

        .analytics-chart-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, rgba(255,255,255,0.5) 0%, transparent 50%);
            pointer-events: none;
        }

        .chart-header {
            padding: 25px 25px 20px;
            position: relative;
            z-index: 2;
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .chart-title-wrapper {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .chart-icon {
            width: 50px;
            height: 50px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.2);
        }

        .chart-icon i {
            font-size: 22px;
            color: white;
        }

        .chart-title {
            color: #2c3e50;
            font-size: 1.4rem;
            font-weight: 600;
            margin: 0;
        }

        .chart-subtitle {
            color: #6c757d;
            font-size: 0.9rem;
            margin: 0;
        }

        .period-selector {
            display: flex;
            background: rgba(0, 0, 0, 0.05);
            border-radius: 10px;
            padding: 4px;
            border: 1px solid rgba(0, 0, 0, 0.1);
        }

        .period-btn {
            padding: 8px 16px;
            border: none;
            background: transparent;
            color: #6c757d;
            border-radius: 8px;
            font-size: 0.9rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .period-btn:hover {
            background: rgba(102, 126, 234, 0.1);
            color: #667eea;
        }

        .period-btn.active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3);
        }

        .chart-stats {
            display: flex;
            gap: 20px;
            padding: 0 25px 20px;
            position: relative;
            z-index: 2;
        }

        .stat-item {
            display: flex;
            align-items: center;
            gap: 12px;
            background: white;
            padding: 15px;
            border-radius: 15px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            border: 1px solid rgba(0, 0, 0, 0.08);
            flex: 1;
        }

        .stat-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
        }

        .stat-users {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            color: white;
        }

        .stat-sessions {
            background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
            color: white;
        }

        .stat-avg {
            background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
            color: white;
        }

        .stat-content {
            display: flex;
            flex-direction: column;
        }

        .stat-value {
            font-size: 1.2rem;
            font-weight: 700;
            color: #2c3e50;
            line-height: 1;
        }

        .stat-label {
            font-size: 0.75rem;
            color: #6c757d;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .chart-container {
            background: white;
            margin: 0 25px 25px;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            border: 1px solid rgba(0, 0, 0, 0.08);
            position: relative;
            z-index: 2;
        }

        #daily-usage-chart {
            height: 300px;
        }

        /* ==============================================
           ONLINE USERS CARD
        ============================================== */
        .online-users-card {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 20px;
            padding: 0;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            position: relative;
            overflow: hidden;
            height: 100%;
            border: 1px solid rgba(0, 0, 0, 0.05);
        }

        .online-users-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, rgba(255,255,255,0.5) 0%, transparent 50%);
            pointer-events: none;
        }

        .users-header {
            padding: 25px 25px 20px;
            position: relative;
            z-index: 2;
        }

        .users-title-wrapper {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .users-icon {
            width: 50px;
            height: 50px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.2);
        }

        .users-icon i {
            font-size: 22px;
            color: white;
        }

        .users-title {
            color: #2c3e50;
            font-size: 1.4rem;
            font-weight: 600;
            margin: 0;
        }

        .users-subtitle {
            color: #6c757d;
            font-size: 0.9rem;
            margin: 0;
        }

        .refresh-btn {
            width: 40px;
            height: 40px;
            border: 1px solid rgba(0, 0, 0, 0.1);
            background: white;
            border-radius: 10px;
            color: #667eea;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .refresh-btn:hover {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            /*transform: rotate(180deg);    */
        }

        .users-count {
            display: flex;
            align-items: center;
            gap: 8px;
            background: white;
            padding: 15px;
            border-radius: 15px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            border: 1px solid rgba(0, 0, 0, 0.08);
            margin-top: 15px;
        }

        .count-number {
            font-size: 2rem;
            font-weight: 700;
            color: #667eea;
            line-height: 1;
        }

        .count-label {
            color: #6c757d;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .count-range {
            display: block;
            color: #6c757d;
            font-size: 0.75rem;
            margin-top: 4px;
        }

        .users-container {
            background: white;
            margin: 20px 25px 25px;
            border-radius: 15px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            border: 1px solid rgba(0, 0, 0, 0.08);
            position: relative;
            z-index: 2;
            display: flex;
            flex-direction: column;
        }

        #online-users-list {
            max-height: 350px;
            overflow-y: auto;
            flex: 1;
        }

        .pagination-container {
            padding: 15px 20px;
            border-top: 1px solid rgba(0, 0, 0, 0.05);
            background: #f8f9fa;
            border-radius: 0 0 15px 15px;
        }

        .pagination-controls {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
        }

        .pagination-info-row {
            display: flex;
            justify-content: center;
            margin-top: 8px;
        }

        .pagination-btn {
            width: 32px;
            height: 32px;
            border: 1px solid rgba(0, 0, 0, 0.1);
            background: white;
            border-radius: 8px;
            color: #667eea;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .pagination-btn:hover:not(:disabled) {
            background: #667eea;
            color: white;
            transform: translateY(-1px);
        }

        .pagination-btn:disabled {
            opacity: 0.4;
            cursor: not-allowed;
        }

        .pagination-btn i {
            width: 16px !important;
            height: 16px !important;
        }

        .pagination-info {
            font-size: 0.85rem;
            color: #6c757d;
            font-weight: 500;
        }

        .page-numbers {
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .page-number-btn {
            width: 32px;
            height: 32px;
            border: 1px solid rgba(0, 0, 0, 0.1);
            background: white;
            border-radius: 8px;
            color: #6c757d;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 0.85rem;
            font-weight: 500;
        }

        .page-number-btn:hover {
            background: #667eea;
            color: white;
            transform: translateY(-1px);
            box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3);
        }

        .page-number-btn.active {
            background: #667eea;
            color: white;
            border-color: #667eea;
            box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3);
        }

        .page-ellipsis {
            padding: 0 4px;
            color: #6c757d;
            font-size: 0.85rem;
            display: flex;
            align-items: center;
        }

        .users-container::-webkit-scrollbar {
            width: 6px;
        }

        .users-container::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .users-container::-webkit-scrollbar-thumb {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 10px;
        }

        .users-container::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(135deg, #5a67d8 0%, #6b5b95 100%);
        }

        .user-item {
            padding: 15px 20px;
            border-bottom: 1px solid rgba(0,0,0,0.05);
            transition: all 0.3s ease;
        }

        .user-item:hover {
            background: rgba(102, 126, 234, 0.03);
        }

        .user-item:last-child {
            border-bottom: none;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 14px;
        }

        .user-details h6 {
            margin: 0;
            font-size: 1rem;
            font-weight: 600;
            color: #2c3e50;
        }

        .user-details small {
            color: #7f8c8d;
            font-size: 0.8rem;
        }

        .user-details small i {
            margin-right: 4px;
            color: #667eea;
        }

        .user-status {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 4px;
        }

        .network-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.7rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .badge-light-info {
            background: rgba(102, 126, 234, 0.1);
            color: #667eea;
        }

        .badge-light-success {
            background: rgba(40, 199, 111, 0.1);
            color: #28c76f;
        }

        .connection-time {
            font-size: 0.75rem;
            color: #7f8c8d;
        }

        /* ==============================================
           LOADING & EMPTY STATES
        ============================================== */
        .loading-state {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
            text-align: center;
        }

        .loading-icon {
            width: 40px !important;
            height: 40px !important;
            color: #667eea;
            animation: spin 1s linear infinite;
            margin-bottom: 15px;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .empty-state {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
            text-align: center;
            color: #7f8c8d;
        }

        .empty-state i {
            font-size: 3rem;
            margin-bottom: 15px;
            color: #bdc3c7;
        }

        .error-state {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
            text-align: center;
            color: #e74c3c;
        }

        .error-state i {
            font-size: 3rem;
            margin-bottom: 15px;
            color: #e74c3c;
        }

                 /* ==============================================
            CHART TOOLTIP
         ============================================== */
         .custom-tooltip {
             background: white;
             border: 1px solid #e9ecef;
             border-radius: 8px;
             padding: 10px;
             box-shadow: 0 4px 12px rgba(0,0,0,0.1);
         }

         .tooltip-title {
             font-size: 0.8rem;
             color: #6c757d;
             margin-bottom: 4px;
         }

         .tooltip-value {
             font-size: 1rem;
             font-weight: 600;
             color: #667eea;
         }

         /* ==============================================
            RESPONSIVE DESIGN
         ============================================== */
         @media (max-width: 768px) {
             .chart-stats {
                 flex-direction: column;
                 gap: 10px;
             }
             
             .stat-item {
                 flex-direction: row;
             }
             
             .header-content {
                 flex-direction: column;
                 gap: 15px;
                 align-items: flex-start;
             }
             
             .chart-title-wrapper {
                 flex-direction: column;
                 align-items: flex-start;
                 gap: 10px;
             }
             
             .page-numbers {
                 flex-wrap: wrap;
                 gap: 2px;
             }
             
             .page-number-btn, .pagination-btn {
                 width: 28px;
                 height: 28px;
                 font-size: 0.8rem;
             }
             
             .pagination-container {
                 padding: 12px 15px;
             }
         }
        
        .card-title {
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 0;
            font-size: 1.1rem;
        }
        
        .card-body {
            padding: 1.5rem;
        }
        
        /* ==============================================
           MODERN NAVIGATION TABS
        ============================================== */
        .nav-tabs {
            border: none;
            background: #f8f9fa;
            border-radius: 12px;
            padding: 8px;
            margin-bottom: 2rem;
        }
        
        .nav-tabs .nav-item {
            margin-bottom: 0;
        }
        
        .nav-tabs .nav-link {
            border: none;
            color: #6c757d;
            font-weight: 500;
            padding: 12px 20px;
            border-radius: 8px;
            transition: all 0.3s ease;
            margin-right: 4px;
            display: flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
        }
        
        .nav-tabs .nav-link:hover {
            background: rgba(115, 103, 240, 0.1);
            color: #7367f0;
            transform: translateY(-1px);
            text-decoration: none;
        }
        
        .nav-tabs .nav-link.active {
            background: linear-gradient(135deg, #7367f0 0%, #9c88ff 100%);
            color: white;
            box-shadow: 0 4px 15px rgba(115, 103, 240, 0.3);
        }
        
        /* Enhanced Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .stat-card {
            background: linear-gradient(135deg, #fff 0%, #f8f9fa 100%);
            border-radius: 12px;
            padding: 1.5rem;
            border-left: 4px solid #7367f0;
            transition: all 0.3s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        }
        
        .stat-value {
            font-size: 2rem;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 0.5rem;
        }
        
        .stat-label {
            color: #6c757d;
            font-size: 0.9rem;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        /* ==============================================
           FORM IMPROVEMENTS
        ============================================== */
        .form-control {
            border: 2px solid #e9ecef;
            border-radius: 8px;
            padding: 12px 16px;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            line-height: 1.5;
            background-color: #fff;
        }
        
        /* Select specific improvements for vertical alignment */
        select.form-control {
            padding: 12px 16px;
            height: 50px;
            line-height: 1.5;
            vertical-align: middle;
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
            background-position: right 12px center;
            background-repeat: no-repeat;
            background-size: 16px 12px;
            padding-right: 40px;
            display: flex;
            align-items: center;
        }
        
        select.form-control option {
            padding: 8px 16px;
            line-height: 1.5;
            vertical-align: middle;
        }
        
        /* Textarea specific to avoid flex layout issues */
        textarea.form-control {
            display: block;
            resize: vertical;
            min-height: 80px;
        }
        
        .form-control:focus {
            border-color: #7367f0;
            box-shadow: 0 0 0 0.2rem rgba(115, 103, 240, 0.15);
            outline: none;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-group label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 8px;
            font-size: 0.9rem;
            display: block;
        }
        
        .form-group .form-text {
            font-size: 0.8rem;
            color: #6c757d;
            margin-top: 0.5rem;
        }
        
        /* Form validation states */
        .form-control.is-invalid {
            border-color: #dc3545;
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
        }
        
        .form-control.is-valid {
            border-color: #28a745;
            box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
        }
        
        .invalid-feedback {
            display: none;
            width: 100%;
            margin-top: 0.25rem;
            font-size: 0.875rem;
            color: #dc3545;
        }
        
        .form-control.is-invalid ~ .invalid-feedback {
            display: block;
        }
        
        .valid-feedback {
            display: none;
            width: 100%;
            margin-top: 0.25rem;
            font-size: 0.875rem;
            color: #28a745;
        }
        
        .form-control.is-valid ~ .valid-feedback {
            display: block;
        }
        
        /* Required field indicator */
        .required::after {
            content: " *";
            color: #dc3545;
        }
        
        /* Form actions */
        .form-actions {
            padding-top: 1rem;
            border-top: 1px solid #e9ecef;
            margin-top: 1rem;
        }
        
        /* Button Enhancements */
        .btn {
            border-radius: 8px;
            padding: 10px 20px;
            font-weight: 500;
            transition: all 0.3s ease;
            border: none;
        }
        
        .custom-btn {
            background: linear-gradient(135deg, #7367f0 0%, #9c88ff 100%);
            box-shadow: 0 4px 15px rgba(115, 103, 240, 0.3);
        }
        
        .custom-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(115, 103, 240, 0.4);
        }
        
        .btn-outline-primary {
            border: 2px solid #7367f0;
            color: #7367f0;
            background: transparent;
        }
        
        .btn-outline-primary:hover {
            background: #7367f0;
            color: white;
            transform: translateY(-1px);
        }
        
        /* Network Interface Cards */
        .network-interface-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .interface-card {
            background: #fff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 20px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
        }
        
        .interface-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(0,0,0,0.12);
        }
        
        .interface-header {
            background: linear-gradient(135deg, #7367f0 0%, #9c88ff 100%);
            color: white;
            padding: 1rem 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .interface-body {
            padding: 1.5rem;
        }
        
        .interface-detail {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 0;
            border-bottom: 1px solid #f1f3f4;
        }
        
        .interface-detail:last-child {
            border-bottom: none;
        }
        
        .interface-label {
            color: #6c757d;
            font-size: 0.9rem;
            font-weight: 500;
        }
        
        .interface-value {
            color: #2c3e50;
            font-weight: 600;
        }
        
        /* Content Sections */
        .content-section {
            background: #fff;
            border-radius: 12px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 2px 20px rgba(0,0,0,0.08);
        }
        
        .section-header {
            display: flex;
            justify-content: between;
            align-items: center;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #f1f3f4;
        }
        
        .section-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: #2c3e50;
            margin: 0;
        }
        
        /* Location Map */
        .location-map {
            height: 280px;
            width: 100%;
            border-radius: 12px;
            overflow: hidden;
        }
        
        /* ==============================================
           RESPONSIVE GRID SYSTEM
        ============================================== */
        .responsive-grid {
            display: grid;
            gap: 1.5rem;
            grid-template-columns: 1fr;
        }
        
        @media (min-width: 576px) {
            .responsive-grid {
                grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            }
        }
        
        @media (min-width: 768px) {
            .responsive-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        
        @media (min-width: 1200px) {
            .responsive-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }
        
        /* Mobile-specific adjustments */
        @media (max-width: 575.98px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            .card-body {
                padding: 1rem;
            }
            
            .nav-tabs .nav-link {
                padding: 8px 12px;
                font-size: 0.9rem;
            }
        }
        
        /* Modal Improvements */
        .modal-content {
            border: none;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.15);
        }
        
        .modal-header {
            background: linear-gradient(135deg, #7367f0 0%, #9c88ff 100%);
            color: white;
            border-radius: 12px 12px 0 0;
            padding: 1.5rem 2rem;
        }
        
        .modal-body {
            padding: 2rem;
        }
        
        .modal-footer {
            border-top: 1px solid #e9ecef;
            padding: 1.5rem 2rem;
        }
        
        /* Progress Indicators */
        .progress {
            height: 8px;
            border-radius: 4px;
            background: #f1f3f4;
        }
        
        .progress-bar {
            border-radius: 4px;
        }
        
        /* Timeline Improvements */
        .timeline {
            position: relative;
        }
        
        .timeline::before {
            content: '';
            position: absolute;
            left: 6px;
            top: 0;
            bottom: 0;
            width: 2px;
            background: #e9ecef;
        }
        
        .timeline-item {
            position: relative;
            padding-left: 2rem;
            margin-bottom: 1.5rem;
        }
        
        .timeline-point-indicator {
            position: absolute;
            left: -6px;
            top: 0;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: #e9ecef;
            border: 2px solid #fff;
            z-index: 1;
        }
        
        .timeline-point-primary {
            background: #7367f0 !important;
        }
        
        /* Alert Improvements */
        .alert {
            border: none;
            border-radius: 8px;
            padding: 1rem 1.5rem;
        }
        
        .alert-info {
            background: linear-gradient(135deg, rgba(115, 103, 240, 0.1) 0%, rgba(156, 136, 255, 0.1) 100%);
            color: #7367f0;
        }
        
        .alert-warning {
            background: linear-gradient(135deg, rgba(255, 159, 67, 0.1) 0%, rgba(255, 185, 118, 0.1) 100%);
            color: #ff9f43;
        }
        
        .alert-success {
            background: linear-gradient(135deg, rgba(40, 199, 111, 0.1) 0%, rgba(72, 218, 137, 0.1) 100%);
            color: #28c76f;
        }
        
        /* Utility Classes */
        .text-gradient {
            background: linear-gradient(135deg, #7367f0 0%, #9c88ff 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .shadow-soft {
            box-shadow: 0 2px 20px rgba(0,0,0,0.08) !important;
        }
        
        .border-radius-lg {
            border-radius: 12px !important;
        }
        
        /* Animation Classes */
        .fade-in {
            animation: fadeIn 0.5s ease-in;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .slide-in {
            animation: slideIn 0.3s ease-out;
        }
        
        @keyframes slideIn {
            from { transform: translateX(-10px); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
    </style>

    <!-- Add this right after the existing styles and before closing the head tag -->
    <style>
        /* Collapsible section styles */
        .collapsible-header {
            cursor: pointer;
            padding: 1rem;
            background-color: #f8f8f8;
            border-radius: 5px;
            margin-bottom: 0.5rem;
            transition: all 0.2s ease;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .collapsible-header:hover {
            background-color: #eee;
        }

        .collapsible-header h5 {
            margin: 0;
            font-size: 1rem;
            font-weight: 600;
        }

        .collapsible-content {
            display: none;
            padding: 1rem;
            border-left: 3px solid #7367f0;
            margin-left: 0.5rem;
            margin-bottom: 1.5rem;
            background-color: #fcfcfc;
            border-radius: 0 5px 5px 0;
        }

        /* Card content grouping */
        .card-group-title {
            font-weight: 600;
            color: #5e5873;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid #f0f0f0;
        }

        /* Tab organization improvements */
        .config-section {
            margin-bottom: 1.5rem;
        }

        /* Compact form elements */
        .form-compact .form-group {
            margin-bottom: 0.75rem;
        }

        /* Improved navigation */
        .tab-navigation {
            display: flex;
            flex-wrap: wrap;
            margin-bottom: 1rem;
        }

        .nav-tab-action {
            flex: 1;
            padding: 0.75rem;
            text-align: center;
            background-color: #f8f8f8;
            border-radius: 5px;
            margin: 0 0.5rem 0.5rem 0;
            cursor: pointer;
            transition: all 0.2s ease;
            font-weight: 500;
        }

        .nav-tab-action:hover, .nav-tab-action.active {
            background-color: #7367f0;
            color: white;
        }

        /* Better switch status indicators */
        .switch-label {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        /* Summary views */
        .summary-view {
            padding: 1rem;
            background-color: #f8f8f8;
            border-radius: 5px;
            margin-bottom: 1rem;
        }

        .summary-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.5rem;
        }

        .summary-label {
            color: #6e6b7b;
        }

        .summary-value {
            font-weight: 500;
        }

        /* Improved social media options */
        #social-settings {
            background-color: #f8f8f8;
            border-radius: 5px;
            padding: 1rem;
            margin-top: 0.5rem;
            margin-bottom: 1rem;
            border-left: 3px solid #7367f0;
        }
    </style>

    <!-- Add this CSS right after the existing styles and before closing the head tag -->
    <style>
        /* Enhanced Channel Scanning Modal Styles */
        .scan-pulse-container {
            position: relative;
            width: 80px;
            height: 80px;
            margin: 0 auto;
        }
        
        .scan-pulse-dot {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 20px;
            height: 20px;
            background-color: #7367f0;
            border-radius: 50%;
            z-index: 2;
        }
        
        .scan-pulse-ring {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 40px;
            height: 40px;
            border: 3px solid #7367f0;
            border-radius: 50%;
            opacity: 1;
            z-index: 1;
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0% {
                width: 30px;
                height: 30px;
                opacity: 1;
            }
            100% {
                width: 80px;
                height: 80px;
                opacity: 0;
            }
        }
        
        /* Timeline styling for scan steps */
        .timeline {
            padding-left: 0;
            list-style: none;
            margin-bottom: 0;
        }
        
        .timeline-item {
            position: relative;
            padding-left: 2rem;
            margin-bottom: 0.85rem;
        }
        
        .timeline-item:last-child {
            margin-bottom: 0;
        }
        
        .timeline-point {
            position: absolute;
            left: 0;
            top: 0;
        }
        
        .timeline-point-indicator {
            display: inline-block;
            height: 12px;
            width: 12px;
            border-radius: 50%;
            background-color: #ebe9f1;
        }
        
        .timeline-point-primary {
            background-color: #7367f0 !important;
        }
        
        .timeline-point-secondary {
            background-color: #82868b !important;
        }
        
        .timeline-point-success {
            background-color: #28c76f !important;
        }
        
        /* Channel recommendation cards */
        .channel-recommendation {
            padding: 1rem;
            background-color: #f8f8f8;
            border-radius: 0.5rem;
            margin-bottom: 1rem;
            border-left: 4px solid #7367f0;
        }
        
        .channel-value {
            font-size: 2rem;
            font-weight: 600;
            color: #5e5873;
        }
        
        .interference-meter {
            height: 6px;
            background-color: #eee;
            border-radius: 3px;
            overflow: hidden;
            margin-top: 4px;
        }
        
        .interference-level {
            height: 100%;
            border-radius: 3px;
            background-color: #28c76f;
        }
        
        .interference-low {
            background-color: #28c76f;
            width: 20%;
        }
        
        .interference-medium {
            background-color: #ff9f43;
            width: 50%;
        }
        
        .interference-high {
            background-color: #ea5455;
            width: 80%;
        }

        .pppoe_display {
            display: none;
        }

        .static_ip_display {
            display: none;
        }

        /* Dark mode fixes for wizard/stepper text */
        .dark-layout .text-muted {
            color: #b4b7bd !important;
        }

        .dark-layout .timeline-event h6 {
            color: #d0d2d6 !important;
        }

        .dark-layout .timeline-event p {
            color: #b4b7bd !important;
        }

        .dark-layout .channel-value {
            color: #d0d2d6 !important;
        }

        .dark-layout .card .card-title {
            color: #d0d2d6 !important;
        }

        .dark-layout .card .card-text {
            color: #b4b7bd !important;
        }

        .dark-layout .channel-recommendation {
            background-color: #2c2c2c !important;
            color: #d0d2d6 !important;
        }

        .semi-dark-layout .text-muted {
            color: #b4b7bd !important;
        }

        .semi-dark-layout .timeline-event h6 {
            color: #d0d2d6 !important;
        }

        .semi-dark-layout .timeline-event p {
            color: #b4b7bd !important;
        }

        .semi-dark-layout .channel-value {
            color: #d0d2d6 !important;
        }

        .semi-dark-layout .card .card-title {
            color: #d0d2d6 !important;
        }

        .semi-dark-layout .card .card-text {
            color: #b4b7bd !important;
        }

        .semi-dark-layout .channel-recommendation {
            background-color: #2c2c2c !important;
            color: #d0d2d6 !important;
        }

        /* Dark mode fixes for tab navigation and form text */
        .dark-layout .nav-tabs {
            background-color: #283046 !important;
        }

        .dark-layout .nav-tabs .nav-link {
            color: #b4b7bd !important;
        }

        .dark-layout .nav-tabs .nav-link:hover {
            background-color: rgba(115, 103, 240, 0.2) !important;
            color: #d0d2d6 !important;
        }

        .dark-layout .nav-tabs .nav-link.active {
            color: #ffffff !important;
        }

        .dark-layout .form-group label {
            color: #d0d2d6 !important;
        }

        .dark-layout .interface-label {
            color: #b4b7bd !important;
        }

        .dark-layout .interface-value {
            color: #d0d2d6 !important;
        }

        .dark-layout .section-title {
            color: #d0d2d6 !important;
        }

        .dark-layout .card-title {
            color: #d0d2d6 !important;
        }

        .dark-layout h4, .dark-layout h5, .dark-layout h6 {
            color: #d0d2d6 !important;
        }

        .semi-dark-layout .nav-tabs {
            background-color: #283046 !important;
        }

        .semi-dark-layout .nav-tabs .nav-link {
            color: #b4b7bd !important;
        }

        .semi-dark-layout .nav-tabs .nav-link:hover {
            background-color: rgba(115, 103, 240, 0.2) !important;
            color: #d0d2d6 !important;
        }

        .semi-dark-layout .nav-tabs .nav-link.active {
            color: #ffffff !important;
        }

        .semi-dark-layout .form-group label {
            color: #d0d2d6 !important;
        }

        .semi-dark-layout .interface-label {
            color: #b4b7bd !important;
        }

        .semi-dark-layout .interface-value {
            color: #d0d2d6 !important;
        }

        .semi-dark-layout .section-title {
            color: #d0d2d6 !important;
        }

        .semi-dark-layout .card-title {
            color: #d0d2d6 !important;
        }

        .semi-dark-layout h4, .semi-dark-layout h5, .semi-dark-layout h6 {
            color: #d0d2d6 !important;
        }

        /* Dark mode fixes for card headers */
        .dark-layout .card-header {
            background: linear-gradient(135deg, #283046 0%, #2c2c2c 100%) !important;
            border-bottom: 1px solid rgba(180, 183, 189, 0.3) !important;
        }

        .dark-layout .card-header h4, 
        .dark-layout .card-header h5, 
        .dark-layout .card-header h6,
        .dark-layout .card-header .card-title {
            color: #d0d2d6 !important;
        }

        .dark-layout .card-header .btn {
            color: #b4b7bd !important;
            border-color: #b4b7bd !important;
        }

        .dark-layout .card-header .btn:hover {
            color: #ffffff !important;
            background-color: #7367f0 !important;
            border-color: #7367f0 !important;
        }

        .semi-dark-layout .card-header {
            background: linear-gradient(135deg, #283046 0%, #2c2c2c 100%) !important;
            border-bottom: 1px solid rgba(180, 183, 189, 0.3) !important;
        }

        .semi-dark-layout .card-header h4, 
        .semi-dark-layout .card-header h5, 
        .semi-dark-layout .card-header h6,
        .semi-dark-layout .card-header .card-title {
            color: #d0d2d6 !important;
        }

        .semi-dark-layout .card-header .btn {
            color: #b4b7bd !important;
            border-color: #b4b7bd !important;
        }

        .semi-dark-layout .card-header .btn:hover {
            color: #ffffff !important;
            background-color: #7367f0 !important;
            border-color: #7367f0 !important;
        }

        /* Dark mode fixes for Router Settings tab specific elements */
        .dark-layout .stat-label {
            color: #b4b7bd !important;
        }

        .dark-layout .stat-value {
            color: #d0d2d6 !important;
        }

        .dark-layout .content-section {
            background-color: #283046 !important;
            border: 1px solid #3b4253 !important;
        }

        .dark-layout .section-header {
            border-bottom-color: #3b4253 !important;
        }

        .dark-layout .alert {
            background-color: #2c2c2c !important;
            border-color: #3b4253 !important;
            color: #d0d2d6 !important;
        }

        .dark-layout .alert .alert-body {
            color: #d0d2d6 !important;
        }

        .dark-layout .custom-control-label {
            color: #d0d2d6 !important;
        }

        .dark-layout .custom-control-label::before {
            background-color: #3b4253 !important;
            border-color: #3b4253 !important;
        }

        .dark-layout small, .dark-layout .small {
            color: #b4b7bd !important;
        }

        .dark-layout .form-control {
            background-color: #3b4253 !important;
            border-color: #3b4253 !important;
            color: #d0d2d6 !important;
        }

        .dark-layout .form-control:focus {
            background-color: #3b4253 !important;
            border-color: #7367f0 !important;
            color: #d0d2d6 !important;
        }

        .dark-layout .form-control option {
            background-color: #3b4253 !important;
            color: #d0d2d6 !important;
        }

        .semi-dark-layout .stat-label {
            color: #b4b7bd !important;
        }

        .semi-dark-layout .stat-value {
            color: #d0d2d6 !important;
        }

        .semi-dark-layout .content-section {
            background-color: #283046 !important;
            border: 1px solid #3b4253 !important;
        }

        .semi-dark-layout .section-header {
            border-bottom-color: #3b4253 !important;
        }

        .semi-dark-layout .alert {
            background-color: #2c2c2c !important;
            border-color: #3b4253 !important;
            color: #d0d2d6 !important;
        }

        .semi-dark-layout .alert .alert-body {
            color: #d0d2d6 !important;
        }

        .semi-dark-layout .custom-control-label {
            color: #d0d2d6 !important;
        }

        .semi-dark-layout .custom-control-label::before {
            background-color: #3b4253 !important;
            border-color: #3b4253 !important;
        }

        .semi-dark-layout small, .semi-dark-layout .small {
            color: #b4b7bd !important;
        }

        .semi-dark-layout .form-control {
            background-color: #3b4253 !important;
            border-color: #3b4253 !important;
            color: #d0d2d6 !important;
        }

        .semi-dark-layout .form-control:focus {
            background-color: #3b4253 !important;
            border-color: #7367f0 !important;
            color: #d0d2d6 !important;
        }

        .semi-dark-layout .form-control option {
            background-color: #3b4253 !important;
            color: #d0d2d6 !important;
        }
    </style>

    <!-- Add this right before the closing body tag -->
    <style>
        /* Fix for oscillating progress bar */
        #channel-scan-modal .progress-bar {
            transition: width 0.5s linear;
        }
        
        /* Hourly Schedule Styles */
        .hourly-schedule-container {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 1rem;
            margin-top: 1rem;
        }

        .hourly-schedule-container .table {
            margin-bottom: 0;
            background: white;
            border-radius: 6px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .hour-header {
            font-size: 0.75rem;
            padding: 0.25rem !important;
            text-align: center;
            background: #e9ecef;
            font-weight: 600;
            min-width: 25px;
        }

        .day-label {
            font-weight: 600;
            text-transform: capitalize;
            background: #f8f9fa;
            padding: 0.5rem !important;
            min-width: 80px;
            border-right: 2px solid #dee2e6;
        }

        .hour-cell {
            padding: 0.25rem !important;
            text-align: center;
            border: 1px solid #dee2e6;
            cursor: pointer;
            transition: all 0.2s ease;
            min-width: 25px;
            height: 35px;
            position: relative;
        }

        .hour-cell.enabled {
            background: #28a745;
            color: white;
        }

        .hour-cell.disabled {
            background: #6c757d;
            color: white;
        }

        .hour-cell:hover {
            transform: scale(1.1);
            z-index: 2;
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
        }

        .day-actions {
            padding: 0.5rem !important;
            min-width: 100px;
        }

        .day-actions .btn {
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
            margin: 0 0.125rem;
        }

        /* Current hour highlighting */
        .current-hour {
            box-shadow: 0 0 0 2px #007bff !important;
        }

        /* Responsive design for hourly schedule */
        @media (max-width: 1200px) {
            .hour-header, .hour-cell {
                font-size: 0.65rem;
                min-width: 20px;
                padding: 0.15rem !important;
            }
        }

        @media (max-width: 768px) {
            .hourly-schedule-container {
                overflow-x: auto;
            }
            
            .hour-header, .hour-cell {
                font-size: 0.6rem;
                min-width: 18px;
                padding: 0.1rem !important;
            }
            
            .day-label {
                min-width: 60px;
                font-size: 0.8rem;
            }
        }

        /* Dark mode styles */
        .dark-layout .hourly-schedule-container {
            background: #283046;
        }

        .dark-layout .hourly-schedule-container .table {
            background: #2f3349;
            color: #d0d2d6;
        }

        .dark-layout .hour-header {
            background: #3b4253;
            color: #d0d2d6;
        }

        .dark-layout .day-label {
            background: #3b4253;
            color: #d0d2d6;
        }

        .dark-layout .hour-cell {
            border-color: #404656;
        }
        
        /* Fix for modal close icon */
        #channel-scan-modal .modal-header .close {
            color: #fff !important;
            text-shadow: none !important;
            opacity: 1 !important;
            padding: 0.75rem !important;
            margin: -0.75rem -1rem -0.75rem auto !important;
            background-color: rgba(255, 255, 255, 0.1) !important;
            border-radius: 0.357rem !important;
            box-shadow: none !important;
            transform: none !important;
            position: relative !important;
            font-size: 1.5rem !important;
            line-height: 1 !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            width: 32px !important;
            height: 32px !important;
            border: none !important;
            transition: all 0.2s ease !important;
        }
        
        #channel-scan-modal .modal-header .close:hover {
            opacity: 1 !important;
            background-color: rgba(255, 255, 255, 0.2) !important;
            transform: none !important;
            box-shadow: none !important;
        }
        
        #channel-scan-modal .modal-header .close:focus {
            outline: none !important;
            box-shadow: none !important;
        }
        
        /* Ensure feather icon in close button is visible */
        #channel-scan-modal .modal-header .close span {
            font-size: 1.5rem !important;
            display: block !important;
            line-height: 1 !important;
            color: #fff !important;
        }
        
        /* Improve modal header icon alignment */
        #channel-scan-modal .modal-title i {
            vertical-align: middle;
            margin-top: -3px;
        }
    </style>
</head>
<!-- END: Head-->

<!-- BEGIN: Body-->
<body class="vertical-layout vertical-menu-modern navbar-floating footer-static menu-collapsed" data-open="click" data-menu="vertical-menu-modern" data-col="">
    <!-- BEGIN: Header-->
    <nav class="header-navbar navbar navbar-expand-lg align-items-center floating-nav navbar-light navbar-shadow">
        <div class="navbar-container d-flex content">
            <div class="bookmark-wrapper d-flex align-items-center">
                <ul class="nav navbar-nav d-xl-none">
                    <li class="nav-item"><a class="nav-link menu-toggle" href="javascript:void(0);"><i data-feather="menu"></i></a></li>
                </ul>
            </div>
            <ul class="nav navbar-nav align-items-center ml-auto">
                <!-- Language dropdown -->
                <li class="nav-item dropdown dropdown-language">
                    <a class="nav-link dropdown-toggle" id="dropdown-flag" href="javascript:void(0);" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="flag-icon flag-icon-us"></i>
                        <span class="selected-language">English</span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdown-flag">
                        <a class="dropdown-item" href="/en/locations" data-language="en">
                            <i class="flag-icon flag-icon-us"></i> English
                        </a>
                        <a class="dropdown-item" href="/fr/locations" data-language="fr">
                            <i class="flag-icon flag-icon-fr"></i> Français
                        </a>
                    </div>
                </li>
                <!-- Dark mode toggle -->
                <li class="nav-item d-none d-lg-block">
                    <a class="nav-link nav-link-style">
                        <i class="ficon" data-feather="moon"></i>
                    </a>
                </li>
                <!-- Notifications -->
                <!-- <li class="nav-item dropdown dropdown-notification mr-25">
                    <a class="nav-link" href="javascript:void(0);" data-toggle="dropdown">
                        <i class="ficon" data-feather="bell"></i>
                        <span class="badge badge-pill badge-primary badge-up">5</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-media dropdown-menu-right">
                        </ul>
                </li> -->
                
                <!-- User dropdown -->
                <li class="nav-item dropdown dropdown-user"><a class="nav-link dropdown-toggle dropdown-user-link" id="dropdown-user" href="javascript:void(0);" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <div class="user-nav d-sm-flex d-none"><span class="user-name font-weight-bolder"></span><span class="user-status"></span></div><span class="avatar"><img class="round user-profile-picture" src="/assets/avatar-default.jpg" alt="avatar" height="40" width="40"><span class="avatar-status-online"></span></span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdown-user">
                        <a class="dropdown-item" href="/en/profile"><i class="mr-50" data-feather="user"></i> Profile</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="/logout"><i class="mr-50" data-feather="power"></i> Logout</a>
                    </div>
                </li>
            </ul>
        </div>
    </nav>
    <!-- END: Header-->

    <!-- BEGIN: Main Menu-->
    <div class="main-menu menu-fixed menu-light menu-accordion menu-shadow" data-scroll-to-active="true">
        <div class="navbar-header">
            <ul class="nav navbar-nav flex-row">
                <li class="nav-item mr-auto">
                    <a class="navbar-brand" href="/en/dashboard">
                        <span class="brand-logo"><img src="/app-assets/mrwifi-assets/Mr-Wifi.PNG" alt="logo"></span>
                        <h2 class="brand-text">monsieur-wifi</h2>
                    </a>
                </li>
                <li class="nav-item nav-toggle">
                    <a class="nav-link modern-nav-toggle pr-0" data-toggle="collapse">
                        <i class="d-block d-xl-none text-primary toggle-icon font-medium-4" data-feather="x"></i>
                        <i class="d-none d-xl-block collapse-toggle-icon font-medium-4 text-primary" data-feather="disc"></i>
                    </a>
                </li>
            </ul>
        </div>
        <div class="shadow-bottom"></div>
        <div class="main-menu-content">
            <ul class="navigation navigation-main" id="main-menu-navigation" data-menu="menu-navigation">
                <li class="navigation-header"><span>Management</span></li>
                <li class="nav-item">
                    <a class="d-flex align-items-center" href="/en/dashboard"><i data-feather="home"></i><span class="menu-title text-truncate">Dashboard</span></a>
                </li>
                <li class="nav-item">
                    <a class="d-flex align-items-center" href="/en/zones"><i data-feather="layers"></i><span class="menu-title text-truncate">Zones</span></a>
                </li>
                <li class="nav-item">
                    <a class="d-flex align-items-center" href="/en/devices"><i data-feather="hard-drive"></i><span class="menu-title text-truncate">Devices</span></a>
                </li>
                <li class="nav-item active">
                    <a class="d-flex align-items-center" href="/en/locations"><i data-feather="map-pin"></i><span class="menu-title text-truncate">Locations</span></a>
                </li>
                <li class="nav-item">
                    <a class="d-flex align-items-center" href="/en/captive-portals"><i data-feather="layout"></i><span class="menu-title text-truncate">Captive Portals</span></a>
                </li>
                <li class="nav-item">
                    <a class="d-flex align-items-center" href="/en/shop"><i data-feather="shopping-bag"></i><span class="menu-title text-truncate">Shop</span></a>
                </li>
                
                
                <li class="navigation-header admin_and_above hidden"><span>For Admin</span></li>
                <li class="nav-item admin_and_above hidden">
                    <a class="d-flex align-items-center" href="/en/accounts"><i data-feather="users"></i><span class="menu-title text-truncate">Accounts</span></a>
                </li>
                <li class="nav-item admin_and_above hidden">
                    <a class="d-flex align-items-center" href="/en/domain-blocking"><i data-feather="slash"></i><span class="menu-title text-truncate">Domain Blocking</span></a>
                </li>
                <li class="nav-item admin_and_above hidden">
                    <a class="d-flex align-items-center" href="/en/admin/models"><i data-feather="cpu"></i><span class="menu-title text-truncate">Manage Models</span></a>
                </li>
                <li class="nav-item admin_and_above hidden">
                    <a class="d-flex align-items-center" href="/en/admin/inventory"><i data-feather="box"></i><span class="menu-title text-truncate">Manage Inventory</span></a>
                </li>
                <li class="nav-item admin_and_above hidden">
                    <a class="d-flex align-items-center" href="/en/admin/orders"><i data-feather="package"></i><span class="menu-title text-truncate">Manage Orders</span></a>
                </li>
                
                <li class="navigation-header only_superadmin hidden"><span>Super Admin</span></li>
                <li class="nav-item only_superadmin hidden">
                    <a class="d-flex align-items-center" href="/en/firmware"><i data-feather="download"></i><span class="menu-title text-truncate">Firmware</span></a>
                </li>
                <li class="nav-item only_superadmin hidden">
                    <a class="d-flex align-items-center" href="/en/system-settings"><i data-feather="settings"></i><span class="menu-title text-truncate">System Settings</span></a>
                </li>
                
                <li class="navigation-header"><span>Account</span></li>
                <li class="nav-item">
                     <a class="d-flex align-items-center" href="/en/profile">
                         <i data-feather="user"></i>
                         <span class="menu-title text-truncate">Profile</span>
                     </a>
                </li>
                <li class="nav-item">
                     <a class="d-flex align-items-center" href="/en/orders">
                         <i data-feather="list"></i>
                         <span class="menu-title text-truncate">My Orders</span>
                     </a>
                </li>
                <li class="nav-item">
                     <a class="d-flex align-items-center logout-button" href="/logout">
                         <i data-feather="power"></i>
                         <span class="menu-title text-truncate">Logout</span>
                     </a>
                </li> 
            </ul>
        </div>
    </div>
    <!-- END: Main Menu-->

    <!-- BEGIN: Content-->
    <div class="app-content content">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper">
            <div class="content-header row">
                <div class="content-header-left col-md-9 col-12 mb-2">
                    <div class="row breadcrumbs-top">
                        <div class="col-12">
                            <h2 class="content-header-title float-left mb-0">Location Details</h2>
                            <div class="breadcrumb-wrapper">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item">
                                        <a href="/en/dashboard">Home</a>
                                    </li>
                                    <li class="breadcrumb-item">
                                        <a href="/en/locations">Locations</a>
                                    </li>
                                    <li class="breadcrumb-item active">
                                        <span class="location_name">Loading...</span>
                                    </li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="content-header-right text-md-right col-md-3 col-12 mb-1">
                    <div class="form-group breadcrumb-right d-flex align-items-center justify-content-end mb-0">
                        <div class="btn-group" role="group">
                            <a href="#network-configuration-tabs" class="btn custom-btn btn-analytics">
                                <i data-feather="settings" class="mr-50"></i>
                                <span class="d-none d-sm-inline">Settings</span>
                            </a>
                            <a href="#" class="btn btn-outline-primary" id="guest-users-link">
                                <i data-feather="user-check" class="mr-50"></i>
                                <span class="d-none d-sm-inline">Guest Users</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="content-body">
                <!-- Location Overview -->
                <div class="stats-grid">
                    <!-- Location Info Card -->
                    <div class="stat-card">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <h4 class="text-gradient mb-1"><span class="location_name"></span></h4>
                                <p class="text-muted mb-0"><span class="location_address"></span></p>
                                <div class="d-flex align-items-center mt-1">
                                    <small class="text-muted mr-2">MAC: <span class="router_mac_address_header font-weight-bold">Loading...</span></small>
                                    <button class="btn btn-sm btn-outline-secondary p-1" id="edit-mac-btn" style="font-size: 0.7rem; line-height: 1;">
                                        <i data-feather="edit" class="mr-1" style="width: 12px; height: 12px;"></i>Edit
                                    </button>
                                </div>
                            </div>
                            <span class="status-badge status-offline">Offline</span>
                        </div>

                        <div class="row">
                            <div class="col-6">
                                <div class="interface-detail">
                                    <span class="interface-label">Router Model</span>
                                    <span class="interface-value router_model_updated"></span>
                                </div>
                                <div class="interface-detail">
                                    <span class="interface-label">MAC Address</span>
                                    <span class="interface-value router_mac_address"></span>
                                </div>
                                <div class="interface-detail">
                                    <span class="interface-label">Firmware</span>
                                    <span class="interface-value router_firmware"></span>
                                </div>
                                <div class="interface-detail">
                                    <span class="interface-label">Total Users</span>
                                    <span class="interface-value connected_users"></span>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="interface-detail">
                                    <span class="interface-label">Daily Usage</span>
                                    <span class="interface-value daily_usage"></span>
                                </div>
                                <div class="interface-detail">
                                    <span class="interface-label">Uptime</span>
                                    <span class="interface-value uptime"></span>
                                </div>
                                <!-- <div class="interface-detail">
                                    <span class="interface-label">Reboot Count</span>
                                    <span class="interface-value reboot_count">0</span>
                                </div> -->
                            </div>
                        </div>
                        
                        <div class="d-flex gap-2 mt-3">
                            <button class="btn custom-btn btn-sm flex-fill" id="device-restart-btn">
                                <i data-feather="refresh-cw" class="mr-1"></i>
                                Restart
                            </button>
                            <button class="btn btn-outline-primary btn-sm flex-fill" id="update-firmware-btn">
                                <i data-feather="download" class="mr-1"></i>
                                Update
                            </button>
                        </div>
                    </div>

                    <!-- Usage Stats Grid -->
                    <div class="stat-card">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="card-title mb-0">Current Usage</h5>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-toggle="dropdown" id="usage-period-btn">
                                    Today
                                </button>
                                <div class="dropdown-menu dropdown-menu-right" id="usage-period-dropdown">
                                    <a class="dropdown-item" href="javascript:void(0);" data-period="today">Today</a>
                                    <a class="dropdown-item" href="javascript:void(0);" data-period="7days">Last 7 Days</a>
                                    <a class="dropdown-item" href="javascript:void(0);" data-period="30days">Last 30 Days</a>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Loading Indicator -->
                        <div id="usage-loading" class="text-center py-3" style="display: none;">
                            <div class="spinner-border spinner-border-sm text-primary" role="status">
                                <span class="sr-only">Loading...</span>
                            </div>
                            <small class="d-block mt-2 text-muted">Loading usage data...</small>
                        </div>
                        
                        <!-- Usage Data -->
                        <div class="row text-center" id="usage-data">
                            <div class="col-6">
                                <div class="mb-3">
                                    <div class="stat-value text-primary" id="download-usage">
                                        <i class="fas fa-spinner fa-spin" style="font-size: 1rem;"></i>
                                    </div>
                                    <div class="stat-label">Download</div>
                                </div>
                                <div>
                                    <div class="stat-value text-info" id="users-sessions-count">
                                        <i class="fas fa-spinner fa-spin" style="font-size: 1rem;"></i>
                                    </div>
                                    <div class="stat-label">Users / Sessions</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="mb-3">
                                    <div class="stat-value text-success" id="upload-usage">
                                        <i class="fas fa-spinner fa-spin" style="font-size: 1rem;"></i>
                                    </div>
                                    <div class="stat-label">Upload</div>
                                </div>
                                <div>
                                    <div class="stat-value text-warning" id="avg-session-time">
                                        <i class="fas fa-spinner fa-spin" style="font-size: 1rem;"></i>
                                    </div>
                                    <div class="stat-label">Avg. Session</div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Last Updated -->
                        <div class="text-center mt-3">
                            <small class="text-muted" id="usage-last-updated">
                                Loading data...
                            </small>
                        </div>
                    </div>

                    <!-- Location Map Card -->
                    <div class="stat-card">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="card-title mb-0">Location</h5>
                            <small class="text-muted" id="map-coordinates" style="display: none;"></small>
                        </div>
                        <div id="location-map" class="location-map"></div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Analytics</h4>
                            </div>
                            <div class="card-body p-4">
                                <div class="row g-4">
                                    <!-- Daily Usage Chart -->
                                    <div class="col-lg-8">
                                        <div class="analytics-chart-card">
                                            <div class="chart-header">
                                                <div class="header-content">
                                                    <div class="chart-title-wrapper">
                                                        <div class="chart-icon">
                                                            <i data-feather="bar-chart-2"></i>
                                                        </div>
                                                        <div>
                                                            <h5 class="chart-title">Daily Usage Analytics</h5>
                                                            <p class="chart-subtitle">Captive Portal User Activity</p>
                                                        </div>
                                                    </div>
                                                    <div class="chart-controls">
                                                        <div class="period-selector">
                                                            <button class="period-btn active" data-period="7">7D</button>
                                                            <button class="period-btn" data-period="30">30D</button>
                                                            <button class="period-btn" data-period="90">90D</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="chart-stats">
                                                <div class="stat-item">
                                                    <div class="stat-icon stat-users">
                                                        <i data-feather="users"></i>
                                                    </div>
                                                    <div class="stat-content">
                                                        <span class="stat-value" id="total-users">-</span>
                                                        <span class="stat-label">Total Users</span>
                                                    </div>
                                                </div>
                                                <div class="stat-item">
                                                    <div class="stat-icon stat-sessions">
                                                        <i data-feather="activity"></i>
                                                    </div>
                                                    <div class="stat-content">
                                                        <span class="stat-value" id="total-sessions">-</span>
                                                        <span class="stat-label">Sessions</span>
                                                    </div>
                                                </div>
                                                <div class="stat-item">
                                                    <div class="stat-icon stat-avg">
                                                        <i data-feather="trending-up"></i>
                                                    </div>
                                                    <div class="stat-content">
                                                        <span class="stat-value" id="avg-daily">-</span>
                                                        <span class="stat-label">Daily Avg</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="chart-container">
                                                <div id="daily-usage-chart"></div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Online Users List -->
                                    <div class="col-lg-4">
                                        <div class="online-users-card">
                                            <div class="users-header">
                                                <div class="header-content">
                                                                                                         <div class="users-title-wrapper">
                                                         <div class="users-icon">
                                                             <i data-feather="wifi"></i>
                                                         </div>
                                                        <div>
                                                            <h5 class="users-title">Live Users</h5>
                                                            <p class="users-subtitle">Currently Connected</p>
                                                        </div>
                                                    </div>
                                                    <button class="refresh-btn" id="refresh-online-users">
                                                        <i data-feather="refresh-cw"></i>
                                                    </button>
                                                </div>
                                                <div class="users-count">
                                                    <span class="count-number" id="online-count">0</span>
                                                    <span class="count-label">Online</span>
                                                    <span class="count-range" id="count-range" style="display: none;"></span>
                                                </div>
                                            </div>
                                            <div class="users-container">
                                                <div id="online-users-list">
                                                    <div class="loading-state">
                                                        <i data-feather="loader" class="loading-icon"></i>
                                                        <p>Loading online users...</p>
                                                    </div>
                                                </div>
                                                <div class="pagination-container" id="users-pagination" style="display: none;">
                                                    <div class="pagination-controls">
                                                        <button class="pagination-btn" id="prev-page" disabled>
                                                            <i data-feather="chevron-left"></i>
                                                        </button>
                                                        <div class="page-numbers" id="page-numbers">
                                                            <!-- Page numbers will be dynamically generated -->
                                                        </div>
                                                        <button class="pagination-btn" id="next-page" disabled>
                                                            <i data-feather="chevron-right"></i>
                                                        </button>
                                                    </div>
                                                    <div class="pagination-info-row">
                                                        <span class="pagination-info" id="page-info">1 / 1</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                                        


            <!-- Network Configuration Tabs -->
            <div class="row" id="network-configuration-tabs">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Network Configuration</h4>
                        </div>
                        <div class="card-body">
                            <ul class="nav nav-tabs" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="location-settings-tab" data-toggle="tab" href="#location-settings" role="tab" aria-controls="location-settings" aria-selected="false">
                                        <i class="fas fa-building mr-2"></i>Location Details
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link " id="router-tab" data-toggle="tab" href="#router" aria-controls="router" role="tab" aria-selected="true">
                                        <i data-feather="hard-drive" class="mr-50"></i>Router Settings
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="captive-portal-tab" data-toggle="tab" href="#captive-portal" aria-controls="captive-portal" role="tab" aria-selected="false">
                                        <i data-feather="layout" class="mr-50"></i>Captive Portal WiFi
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="secured-wifi-tab" data-toggle="tab" href="#secured-wifi" aria-controls="secured-wifi" role="tab" aria-selected="false">
                                        <i data-feather="lock" class="mr-50"></i>Password WiFi
                                    </a>
                                </li>


                                <!-- Add to your tab navigation -->
                               
                            </ul>
                            <div class="tab-content">
                                <!-- Location Details Tab -->
                                <div class="tab-pane active show" id="location-settings" role="tabpanel" aria-labelledby="location-settings-tab">
                                    <div class="card">
                                        <div class="card-header">
                                            <h4 class="card-title">Location Information</h4>
                                        </div>
                                        <div class="card-body">
                                            <form id="location-info-form" novalidate>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="location-name" class="required">Location Name</label>
                                                            <input type="text" class="form-control" id="location-name" placeholder="Enter location name" required aria-describedby="location-name-help">
                                                            <div class="invalid-feedback"></div>
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="location-address">Address</label>
                                                            <input type="text" class="form-control" id="location-address" placeholder="Street address">
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="location-city">City</label>
                                                            <input type="text" class="form-control" id="location-city" placeholder="City">
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="location-state">State/Province</label>
                                                            <input type="text" class="form-control" id="location-state" placeholder="State/Province">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="location-postal-code">Postal Code</label>
                                                            <input type="text" class="form-control" id="location-postal-code" placeholder="Postal code">
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="location-country">Country</label>
                                                            <input type="text" class="form-control" id="location-country" placeholder="Country">
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="router-model-select">Router Model</label>
                                                            <select class="form-control" id="router-model-select" aria-describedby="router-model-help">
                                                                <option value="">Select Router Model</option>
                                                                <option value="820AX">820AX</option>
                                                                <option value="835AX">835AX</option>
                                                            </select>
                                                            <small id="router-model-help" class="form-text text-muted">Choose the router model installed at this location.</small>
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="location-manager">Manager Name</label>
                                                            <input type="text" class="form-control" id="location-manager" placeholder="Manager name">
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="location-contact-email">Contact Email</label>
                                                            <input type="email" class="form-control" id="location-contact-email" placeholder="Contact email">
                                                            <div class="invalid-feedback"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="location-contact-phone">Contact Phone</label>
                                                            <input type="tel" class="form-control" id="location-contact-phone" placeholder="Contact phone">
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="location-status">Status</label>
                                                            <select class="form-control" id="location-status">
                                                                <option value="active">Active</option>
                                                                <option value="inactive">Inactive</option>
                                                                <option value="maintenance">Maintenance</option>
                                                            </select>
                                                        </div>
                                                        <div class="form-group" id="location-owner-group" data-admin-only="true">
                                                            <label for="location-owner">Location Owner</label>
                                                            <select class="form-control" id="location-owner">
                                                                <option value="">Select Owner</option>
                                                                <!-- Options will be populated via JavaScript -->
                                                            </select>
                                                            <small class="form-text text-muted">Only administrators can assign location owners</small>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="location-description">Description</label>
                                                            <textarea class="form-control" id="location-description" rows="3" placeholder="Location description" maxlength="500"></textarea>
                                                            <small class="form-text text-muted">
                                                                <span id="description-counter">0</span>/500 characters
                                                            </small>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-actions">
                                                    <button type="button" id="save-location-info" class="btn custom-btn">
                                                        <i data-feather="save" class="mr-1"></i>
                                                        Save Location Information
                                                    </button>
                                                    <button type="button" class="btn btn-outline-secondary ml-2" onclick="resetLocationForm()">
                                                        <i data-feather="refresh-ccw" class="mr-1"></i>
                                                        Reset
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <!-- Router Settings Tab -->
                                <div class="tab-pane fade" id="router" aria-labelledby="router-tab" role="tabpanel">
                                    <!-- WAN Configuration Section -->
                                    <div class="content-section">
                                        <div class="section-header d-flex justify-content-between align-items-center">
                                            <h5 class="section-title">WAN Connection</h5>
                                            <button class="btn btn-outline-primary btn-sm" data-toggle="modal" data-target="#wan-settings-modal">
                                                <i data-feather="edit" class="mr-1"></i>Edit WAN Settings
                                            </button>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="interface-detail">
                                                    <span class="interface-label">Connection Type</span>
                                                    <span class="interface-value" id="wan-type-display">DHCP</span>
                                                </div>
                                            </div>
                                            <div class="col-md-9 wan-static-ip-display_div hidden">
                                                <div class="row">
                                                    <div class="col-md-3">
                                                        <div class="interface-detail">
                                                            <span class="interface-label">IP Address</span>
                                                            <span class="interface-value" id="wan-ip-display">-</span>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="interface-detail">
                                                            <span class="interface-label">Subnet Mask</span>
                                                            <span class="interface-value" id="wan-subnet-display">-</span>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="interface-detail">
                                                            <span class="interface-label">Gateway</span>
                                                            <span class="interface-value" id="wan-gateway-display">-</span>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="interface-detail">
                                                            <span class="interface-label">Primary DNS</span>
                                                            <span class="interface-value" id="wan-dns1-display">-</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-9 wan-pppoe-display_div hidden">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="interface-detail">
                                                            <span class="interface-label">Username</span>
                                                            <span class="interface-value" id="wan-pppoe-username">-</span>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="interface-detail">
                                                            <span class="interface-label">Service Name</span>
                                                            <span class="interface-value" id="wan-pppoe-service-name">-</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                                        <!-- Network Interfaces Section -->
                    <div class="content-section">
                        <div class="section-header">
                            <h5 class="section-title">Local Network Interfaces</h5>
                        </div>
                        
                        <!-- VLAN Global Settings -->
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="vlan-enabled">
                                        <label class="custom-control-label" for="vlan-enabled">Enable VLAN Support</label>
                                    </div>
                                    <small class="text-muted">Master switch to enable/disable VLAN functionality for this location.</small>
                                </div>
                            </div>
                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="card">
                                                    <div class="card-header d-flex justify-content-between align-items-center">
                                                        <h6 class="mb-0">Captive Portal Network</h6>
                                                        <button class="btn btn-sm btn-outline-primary" data-toggle="modal" data-target="#captive-portal-modal">
                                                            <i data-feather="edit" class="mr-1"></i>Edit
                                                        </button>
                                                    </div>
                                                    <div class="card-body">
                                                        <div class="interface-detail">
                                                            <span class="interface-label">IP Address</span>
                                                            <span class="interface-value" id="captive-ip-display">-</span>
                                                        </div>
                                                        <div class="interface-detail">
                                                            <span class="interface-label">Subnet Mask</span>
                                                            <span class="interface-value" id="captive-netmask-display">-</span>
                                                        </div>
                                                        <!-- <div class="interface-detail">
                                                            <span class="interface-label">Gateway</span>
                                                            <span class="interface-value" id="captive-gateway-display">-</span>
                                                        </div> -->
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-6">
                                                <div class="card">
                                                    <div class="card-header d-flex justify-content-between align-items-center">
                                                        <h6 class="mb-0">Password WiFi Network</h6>
                                                        <button class="btn btn-sm btn-outline-primary" data-toggle="modal" data-target="#password-network-modal">
                                                            <i data-feather="edit" class="mr-1"></i>Edit
                                                        </button>
                                                    </div>
                                                    <div class="card-body">
                                                        <div class="interface-detail">
                                                            <span class="interface-label">Connection Type</span>
                                                            <span class="interface-value" id="password-wifi-ip-type-display">Static IP</span>
                                                        </div>
                                                        <div class="hidden password-ip-assignment-display_div">
                                                            <div class="interface-detail">
                                                                <span class="interface-label">IP Address</span>
                                                                <span class="interface-value" id="password-ip-display">-</span>
                                                            </div>
                                                            <div class="interface-detail">
                                                                <span class="interface-label">Subnet Mask</span>
                                                                <span class="interface-value" id="password-netmask-display">-</span>
                                                            </div>
                                                            <div class="interface-detail">
                                                                <span class="interface-label">Gateway</span>
                                                                <span class="interface-value" id="password-gateway-display">-</span>
                                                            </div>
                                                            <div class="interface-detail">
                                                                <span class="interface-label">DHCP Server</span>
                                                                <span class="interface-value" id="password-dhcp-status-display">-</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- WiFi Radio & Channel Configuration -->
                                    <div class="content-section">
                                        <div class="section-header">
                                            <h5 class="section-title">WiFi Radio & Channel Configuration</h5>
                                        </div>
                                        <div class="row">
                                            <!-- Radio & Power Settings -->
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="wifi-country">Country/Region</label>
                                                    <select class="form-control" id="wifi-country">
                                                        <option value="US" selected>United States (US)</option>
                                                        <option value="CA">Canada (CA)</option>
                                                        <option value="GB">United Kingdom (GB)</option>
                                                        <option value="FR">France (FR)</option>
                                                        <option value="DE">Germany (DE)</option>
                                                        <option value="IT">Italy (IT)</option>
                                                        <option value="ES">Spain (ES)</option>
                                                        <option value="AU">Australia (AU)</option>
                                                        <option value="JP">Japan (JP)</option>
                                                        <option value="CN">China (CN)</option>
                                                        <option value="IN">India (IN)</option>
                                                        <option value="BR">Brazil (BR)</option>
                                                        <option value="ZA">South Africa (ZA)</option>
                                                        <option value="AE">United Arab Emirates (AE)</option>
                                                        <option value="SG">Singapore (SG)</option>
                                                    </select>
                                                </div>
                                                
                                                <div class="form-group">
                                                    <label for="power-level-2g">2.4 GHz Power</label>
                                                    <select class="form-control" id="power-level-2g">
                                                        <option value="20">Maximum (20 dBm)</option>
                                                        <option value="17">High (17 dBm)</option>
                                                        <option value="15" selected>Medium (15 dBm)</option>
                                                        <option value="12">Low (12 dBm)</option>
                                                        <option value="10">Minimum (10 dBm)</option>
                                                    </select>
                                                </div>
                                                
                                                <div class="form-group">
                                                    <label for="power-level-5g">5 GHz Power</label>
                                                    <select class="form-control" id="power-level-5g">
                                                        <option value="23">Maximum (23 dBm)</option>
                                                        <option value="20">High (20 dBm)</option>
                                                        <option value="17" selected>Medium (17 dBm)</option>
                                                        <option value="14">Low (14 dBm)</option>
                                                        <option value="10">Minimum (10 dBm)</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <!-- Channel Settings -->
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="channel-width-2g">2.4 GHz Channel Width</label>
                                                    <select class="form-control" id="channel-width-2g">
                                                        <option value="20">20 MHz</option>
                                                        <option value="40" selected>40 MHz</option>
                                                    </select>
                                                </div>
                                                
                                                <div class="form-group">
                                                    <label for="channel-width-5g">5 GHz Channel Width</label>
                                                    <select class="form-control" id="channel-width-5g">
                                                        <option value="20">20 MHz</option>
                                                        <option value="40">40 MHz</option>
                                                        <option value="80" selected>80 MHz</option>
                                                        <option value="160">160 MHz</option>
                                                    </select>
                                                </div>
                                                
                                                <div class="form-group">
                                                    <label for="channel-2g">2.4 GHz Channel</label>
                                                    <select class="form-control" id="channel-2g">
                                                        <option value="1">Channel 1 (2412 MHz)</option>
                                                        <option value="2">Channel 2 (2417 MHz)</option>
                                                        <option value="3">Channel 3 (2422 MHz)</option>
                                                        <option value="4">Channel 4 (2427 MHz)</option>
                                                        <option value="5">Channel 5 (2432 MHz)</option>
                                                        <option value="6" selected>Channel 6 (2437 MHz)</option>
                                                        <option value="7">Channel 7 (2442 MHz)</option>
                                                        <option value="8">Channel 8 (2447 MHz)</option>
                                                        <option value="9">Channel 9 (2452 MHz)</option>
                                                        <option value="10">Channel 10 (2457 MHz)</option>
                                                        <option value="11">Channel 11 (2462 MHz)</option>
                                                        <option value="12">Channel 12 (2467 MHz)</option>
                                                        <option value="13">Channel 13 (2472 MHz)</option>
                                                        <option value="14">Channel 14 (2484 MHz)</option>
                                                    </select>
                                                </div>
                                                
                                                <div class="form-group">
                                                    <label for="channel-5g">5 GHz Channel</label>
                                                    <select class="form-control" id="channel-5g">
                                                        <option value="36" selected>Channel 36 (5180 MHz)</option>
                                                        <option value="40">Channel 40 (5200 MHz)</option>
                                                        <option value="44">Channel 44 (5220 MHz)</option>
                                                        <option value="48">Channel 48 (5240 MHz)</option>
                                                        <option value="52">Channel 52 (5260 MHz)</option>
                                                        <option value="56">Channel 56 (5280 MHz)</option>
                                                        <option value="60">Channel 60 (5300 MHz)</option>
                                                        <option value="64">Channel 64 (5320 MHz)</option>
                                                        <option value="100">Channel 100 (5500 MHz)</option>
                                                        <option value="104">Channel 104 (5520 MHz)</option>
                                                        <option value="108">Channel 108 (5540 MHz)</option>
                                                        <option value="112">Channel 112 (5560 MHz)</option>
                                                        <option value="116">Channel 116 (5580 MHz)</option>
                                                        <option value="120">Channel 120 (5600 MHz)</option>
                                                        <option value="124">Channel 124 (5620 MHz)</option>
                                                        <option value="128">Channel 128 (5640 MHz)</option>
                                                        <option value="132">Channel 132 (5660 MHz)</option>
                                                        <option value="136">Channel 136 (5680 MHz)</option>
                                                        <option value="140">Channel 140 (5700 MHz)</option>
                                                        <option value="149">Channel 149 (5745 MHz)</option>
                                                        <option value="153">Channel 153 (5765 MHz)</option>
                                                        <option value="157">Channel 157 (5785 MHz)</option>
                                                        <option value="161">Channel 161 (5805 MHz)</option>
                                                        <option value="165">Channel 165 (5825 MHz)</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <!-- Channel Optimization -->
                                            <div class="col-md-4">
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <label class="mb-0">Channel Optimization</label>
                                                    <button class="btn btn-outline-primary btn-sm" id="scan-channels-btn">
                                                        <i data-feather="wifi" class="mr-1"></i>Scan
                                                    </button>
                                                </div>
                                                
                                                <div class="alert alert-info mb-3" id="scan-status-alert">
                                                    <div class="alert-body">
                                                        <i data-feather="info" class="mr-2"></i>
                                                        <span id="scan-status-text">Click Scan to analyze optimal channels.</span>
                                                    </div>
                                                </div>
                                                
                                                <div class="row text-center mb-3">
                                                    <div class="col-6">
                                                        <div class="stat-value text-primary" id="last-optimal-2g">--</div>
                                                        <div class="stat-label">Best 2.4G</div>
                                                    </div>
                                                    <div class="col-6">
                                                        <div class="stat-value text-success" id="last-optimal-5g">--</div>
                                                        <div class="stat-label">Best 5G</div>
                                                    </div>
                                                </div>
                                                
                                                <div class="text-center mb-2">
                                                    <small class="text-muted" id="last-scan-timestamp">No scan performed yet</small>
                                                </div>
                                                
                                                <button class="btn btn-success btn-block btn-sm" id="save-channels-btn" disabled>
                                                    <i data-feather="check" class="mr-1"></i>Apply Optimal
                                                </button>
                                            </div>
                                        </div>
                                        
                                        <div class="text-center mt-3">
                                            <button class="btn custom-btn" id="save-radio-settings">
                                                <i data-feather="save" class="mr-2"></i>Save All Radio Settings
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Content Filtering Section -->
                                    <div class="content-section">
                                        <div class="section-header d-flex justify-content-between align-items-center">
                                            <h5 class="section-title">Web Content Filtering</h5>
                                            <button class="btn custom-btn" id="save-web-filter-settings">
                                                <i data-feather="save" class="mr-2"></i>Save Web Filter Settings
                                            </button>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                                        <label class="mb-0">Enable Content Filtering</label>
                                                        <div class="custom-control custom-switch custom-control-primary">
                                                            <input type="checkbox" class="custom-control-input" id="global-web-filter">
                                                            <label class="custom-control-label" for="global-web-filter"></label>
                                                        </div>
                                                    </div>
                                                    <small class="text-muted">Apply content filtering to all WiFi networks.</small>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="global-filter-categories">Block Categories</label>
                                                    <select class="select2 form-control" id="global-filter-categories" multiple="multiple">
                                                        <!-- Categories will be loaded dynamically from API -->
                                                    </select>
                                                    <small class="text-muted">Select content categories to block across all networks.</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                    <!-- Simplified Captive Portal WiFi Tab Content -->
                                    <div class="tab-pane fade" id="captive-portal" role="tabpanel" aria-labelledby="captive-portal-tab">
                                            <div class="card">
                                                <div class="card-header d-flex justify-content-between align-items-center">
                                                    <h4 class="card-title mb-0">Captive Portal WiFi</h4>
                                                    <button class="btn custom-btn save-captive-portal" id="save-captive-portal-1">
                                                        <i data-feather="save" class="mr-1"></i> Save Settings
                                                    </button>

                                                    <!-- <button class="btn custom-btn" id="save-web-filter-settings">
                                                        <i data-feather="save" class="mr-2"></i>Save Web Filter Settings
                                                    </button> -->
                                                </div>

                                                <div class="card-body">
                                                    <!-- Basic Settings Section -->
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="portal-ssid">Network Name (SSID)</label>
                                                                <div class="input-group">
                                                                    <input type="text" class="form-control" id="captive-portal-ssid" placeholder="Guest WiFi">
                                                                    <div class="input-group-append">
                                                                        <button class="btn btn-outline-primary" type="button" data-toggle="modal" data-target="#ssid-qr-modal" title="Show QR Code">
                                                                            <i data-feather="code"></i> QR Code
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="portal-visibility">Network Visibility</label>
                                                                <select class="form-control" id="captive-portal-visible">
                                                                    <option value="1" selected>Visible (Broadcast SSID)</option>
                                                                    <option value="0">Hidden (Don't Broadcast SSID)</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Authentication Section -->
                                                    <h5 class="border-bottom pb-1">Authentication</h5>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="captive-auth-method">Authentication Method</label>
                                                                <select class="form-control" id="captive-auth-method">
                                                                    <option value="click-through" selected>Click-through (No Authentication)</option>
                                                                    <option value="password">Password-based</option>
                                                                    <option value="sms">SMS Verification</option>
                                                                    <option value="email">Email Verification</option>
                                                                    <option value="social">Social Media Login</option>
                                                                </select>
                                                            </div>

                                                            <div class="form-group hidden" id="password-auth-options">
                                                                <label for="captive_portal_password">Password</label>
                                                                <div class="input-group">
                                                                    <input type="password" class="form-control form-control-sm" id="captive_portal_password" value="">
                                                                    <div class="input-group-append">
                                                                        <button class="btn btn-outline-secondary btn-sm" type="button" id="toggle-captive-password">
                                                                            <i data-feather="eye"></i>
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="form-group hidden" id="social-auth-options">
                                                                <label for="captive-social-auth-method">Social Media Logins</label>
                                                                <select class="form-control" id="captive-social-auth-method">
                                                                    <option value="facebook">Facebook</option>
                                                                    <option value="google">Google</option>
                                                                </select>
                                                            </div>
                                                    
                                                            <!-- Session Settings -->
                                                            <div class="row">
                                                                <div class="col-6">
                                                                <div class="form-group">
                                                                        <label for="captive-session-timeout">Session (mins)</label>
                                                                        <select class="form-control" id="captive-session-timeout">
                                                                            <option value="60">1 Hr</option>
                                                                            <option value="120">2 Hrs</option>
                                                                            <option value="180">3 Hrs</option>
                                                                            <option value="240">4 Hrs</option>
                                                                            <option value="300">5 Hrs</option>
                                                                            <option value="360">6 Hrs</option>
                                                                            <option value="720">12 Hrs</option>
                                                                            <option value="1440">1 Day</option>
                                                                            <option value="2880">2 Days</option>
                                                                            <option value="4320">3 Days</option>
                                                                            <option value="5760">4 Days</option>
                                                                            <option value="7200">5 Days</option>
                                                                            <option value="8640">6 Days</option>
                                                                            <option value="10080">1 Week</option>
                                                                            <option value="11520">2 Weeks</option>
                                                                            <option value="12960">3 Weeks</option>
                                                                            <option value="14400">1 Month</option>
                                                                            <option value="28800">2 Months</option>
                                                                            <option value="43200">3 Months</option>
                                                                            <option value="57600">4 Months</option>
                                                                            <option value="72000">5 Months</option>
                                                                            <option value="86400">6 Months</option>
                                                                            <option value="100800">7 Months</option>
                                                                            <option value="115200">8 Months</option>
                                                                            <option value="129600">9 Months</option>
                                                                            <option value="144000">10 Months</option>
                                                                            <option value="158400">11 Months</option>
                                                                            <option value="172800">1 Year</option>
                                                                            <option value="345600">2 Years</option>
                                                                            <option value="604800">3 Years</option>
                                                                            <option value="1209600">4 Years</option>
                                                                        </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-6">
                                                            <div class="form-group">
                                                                <label for="captive-idle-timeout">Idle (mins)</label>
                                                                <select class="form-control" id="captive-idle-timeout">
                                                                    <option value="15">15 Mins</option>
                                                                    <option value="30">30 Mins</option>
                                                                    <option value="45">45 Mins</option>
                                                                    <option value="60">1 Hr</option>
                                                                    <option value="120">2 Hrs</option>
                                                                    <option value="180">3 Hrs</option>
                                                                    <option value="240">4 Hrs</option>
                                                                    <option value="360">6 Hrs</option>
                                                                    <option value="720">12 Hrs</option>
                                                                    <option value="1440">1 Day</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-12">
                                                            <div class="form-group">
                                                                <label for="captive-portal-redirect">Custom Redirect URL (Optional)</label>
                                                                <input type="url" class="form-control" id="captive-portal-redirect" placeholder="https://example.com/welcome">
                                                                <small class="text-muted">Redirect users to this URL after successful authentication. Leave empty for default behavior.</small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <!-- Password Auth Options -->
                                                    <div id="password-auth-options" class="auth-options-section" style="display: none;">
                                                        <div class="form-group">
                                                            <label for="portal-shared-password">Shared Password</label>
                                                            <div class="input-group">
                                                                <input type="password" id="portal-shared-password" class="form-control" placeholder="Enter password">
                                                                <div class="input-group-append">
                                                                    <button class="btn btn-outline-secondary" type="button" id="toggle-portal-password">
                                                                        <i data-feather="eye"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- SMS/Email Auth Options -->
                                            <div id="sms-auth-options" class="auth-options-section" style="display: none;">
                                                <div class="alert alert-info mb-0 p-2">SMS verification will be used to authenticate guests.</div>
                                            </div>

                                            <div id="email-auth-options" class="auth-options-section" style="display: none;">
                                                <div class="alert alert-info mb-0 p-2">Email verification will be used to authenticate guests.</div>
                                            </div>

                                            <!-- Social Login Options -->
                                            <div id="social-auth-options" class="auth-options-section" style="display: none;">
                                                <label>Enable Social Login Options</label>
                                                <div class="d-flex flex-wrap">
                                                    <div class="custom-control custom-switch custom-control-primary mr-2 mb-1">
                                                        <input type="checkbox" class="custom-control-input" id="social-facebook" checked>
                                                        <label class="custom-control-label" for="social-facebook">Facebook</label>
                                                    </div>
                                                    <div class="custom-control custom-switch custom-control-primary mr-2 mb-1">
                                                        <input type="checkbox" class="custom-control-input" id="social-google" checked>
                                                         <label class="custom-control-label" for="social-google">Google</label>
                                                    </div>
                                                    <div class="custom-control custom-switch custom-control-primary mr-2 mb-1">
                                                        <input type="checkbox" class="custom-control-input" id="social-twitter">
                                                        <label class="custom-control-label" for="social-twitter">Twitter</label>
                                                     </div>
                                                    <div class="custom-control custom-switch custom-control-primary mb-1">
                                                        <input type="checkbox" class="custom-control-input" id="social-apple">
                                                        <label class="custom-control-label" for="social-apple">Apple</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                            
                                            <!-- Network Settings Section -->
                                            <!-- <h5 class="border-bottom pb-1 mt-3">Network Settings</h5>
                                                    <div class="row">
                                                <div class="col-md-3">
                                                            <div class="form-group">
                                                        <label for="captive-ip-address">IP Address</label>
                                                        <input type="text" class="form-control" id="captive-ip-address" placeholder="192.168.2.1">
                                                </div>
                                                </div>
                                                <div class="col-md-3">
                                                            <div class="form-group">
                                                        <label for="captive-netmask">Netmask</label>
                                                        <input type="text" class="form-control" id="captive-netmask" placeholder="255.255.255.0">
                                            </div>
                                                </div>
                                                <div class="col-md-6 d-flex align-items-end mb-1">
                                                    <button class="btn btn-outline-primary btn-sm" data-toggle="modal" data-target="#captive-network-modal">
                                                        <i data-feather="edit" class="mr-1"></i> Advanced Network Settings
                                                    </button>
                                                </div>
                                            </div> -->
                                        <!-- Bandwidth Section -->
                                        <h5 class="border-bottom pb-1 mt-3">Bandwidth & Portal</h5>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                                        <label class="mb-0">Bandwidth Limits</label>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-6">
                                                             <label for="captive-download-limit">Download (Mbps)</label>
                                                            <select class="form-control form-control-sm" id="captive-download-limit">
                                                                <option value="">Select Download Limit</option>
                                                                <option value="1">1 Mbps</option>
                                                                <option value="2">2 Mbps</option>
                                                                <option value="5">5 Mbps</option>
                                                                <option value="10">10 Mbps</option>
                                                                <option value="15">15 Mbps</option>
                                                                <option value="20">20 Mbps</option>
                                                                <option value="25">25 Mbps</option>
                                                                <option value="30">30 Mbps</option>
                                                                <option value="35">35 Mbps</option>
                                                                <option value="40">40 Mbps</option>
                                                                <option value="45">45 Mbps</option>
                                                                <option value="50">50 Mbps</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-6">
                                                            <label for="captive-upload-limit">Upload (Mbps)</label>
                                                            <select class="form-control form-control-sm" id="captive-upload-limit">
                                                                <option value="0">Select Upload Limit</option>
                                                                <option value="1">1 Mbps</option>
                                                                <option value="2">2 Mbps</option>
                                                                <option value="5">5 Mbps</option>
                                                                <option value="10">10 Mbps</option>
                                                                <option value="15">15 Mbps</option>
                                                                <option value="20">20 Mbps</option>
                                                                <option value="25">25 Mbps</option>
                                                                <option value="30">30 Mbps</option>
                                                                <option value="35">35 Mbps</option>
                                                                <option value="40">40 Mbps</option>
                                                                <option value="45">45 Mbps</option>
                                                                <option value="50">50 Mbps</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="mb-2">Portal Configuration</label>
                                                    <select class="form-control form-control-sm" id="captive-portal-design">
                                                        <!-- Options will be populated dynamically -->
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                            
                                        <!-- Network Settings Section -->
                                        <h5 class="border-bottom pb-1 mt-3">Network Settings</h5>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <button class="btn btn-outline-primary btn-sm" data-toggle="modal" data-target="#captive-portal-modal">
                                                        <i data-feather="settings" class="mr-1"></i> Configure IP & VLAN Settings
                                                    </button>
                                                    <small class="text-muted d-block mt-1">Configure IP address, gateway, and VLAN settings for the captive portal network.</small>
                                                </div>
                                            </div>
                                        </div>
                                            <!-- Access Control Section -->
                                        <h5 class="border-bottom pb-1 mt-3">Access Control</h5>
                                        <div class="row">
                                            <div class="col-lg-6">
                                                    <!-- MAC Filtering -->
                                                 <div class="form-group">
                                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                                        <label class="mb-0">MAC Address Filtering</label>
                                                        <div class="d-flex align-items-center">
                                                            <label class="mr-2 mb-0 text-muted" style="font-size: 0.8rem;">Filter View:</label>
                                                            <select class="form-control form-control-sm" id="captive-mac-view-filter" style="width: auto;">
                                                                <option value="all">Show All</option>
                                                                <option value="blacklisted">Show Blacklisted</option>
                                                                <option value="whitelisted">Show Whitelisted</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                        
                                                        <!-- Add MAC Address Controls -->
                                                    <div class="row mb-2">
                                                        <div class="col-7">
                                                            <input type="text" class="form-control form-control-sm" id="captive-mac-address" placeholder="00:11:22:33:44:55">
                                                        </div>
                                                        <div class="col-3">
                                                            <select class="form-control form-control-sm" id="captive-mac-type">
                                                                <option value="blacklist">Blacklist</option>
                                                                <option value="whitelist">Whitelist</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-2">
                                                            <button class="btn btn-sm custom-btn w-100" id="captive-add-mac">Add</button>
                                                        </div>
                                                </div>
                                                        
                                                <!-- MAC Address List -->
                                                <div class="mac-address-container">
                                                    <div class="mac-filter-status text-muted mb-1" id="captive-mac-status">
                                                        <small>No MAC addresses added yet</small>                                                            </div>
                                                        <div class="filtered-mac-list border rounded" style="min-height: 60px;">
                                                            <div class="text-center text-muted p-3" id="captive-mac-empty">
                                                                <i data-feather="shield" class="mb-2"></i>
                                                                <div><small>No MAC addresses configured</small></div>
                                                                <div><small class="text-muted">Add MAC addresses above to control access</small></div>
                                                            </div>
                                                        </div>
                                                        <!-- Pagination Controls -->
                                                        <div class="mac-pagination-container mt-2" id="captive-mac-pagination" style="display: none;">
                                                            <nav aria-label="MAC address pagination">
                                                                <ul class="pagination pagination-sm justify-content-center mb-0" id="captive-mac-pagination-list">
                                                                </ul>
                                                            </nav>
                                                        </div>
                                                    </div>
                                                        
                                                    <div class="mt-2">
                                                        <button class="btn btn-sm btn-success" id="save-captive-mac-filter">
                                                            <i data-feather="save" class="mr-1"></i> Save MAC Filter Settings
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Working Hours Section -->
                                        <h5 class="border-bottom pb-1 mt-3">Working Hours</h5>
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <div class="py-4 px-2">
                                                        <div class="schedule-container" id="schedule-container"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Hourly Schedule Section (Now integrated into Working Hours above) 
                                        <h5 class="border-bottom pb-1 mt-3">
                                            Hourly Schedule 
                                            <small class="text-muted">(Advanced hour-by-hour control)</small>
                                        </h5>
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="alert alert-info">
                                                    <i data-feather="info" class="mr-2"></i>
                                                    Configure enable/disable settings for each hour of the day. This provides more granular control than the basic working hours above.
                                                </div>
                                                
                                                <div class="d-flex justify-content-between align-items-center mb-3">
                                                    <div>
                                                        <button class="btn btn-sm btn-primary" id="load-hourly-schedule">
                                                            <i data-feather="refresh-cw" class="mr-1"></i> Load Schedule
                                                        </button>
                                                        <button class="btn btn-sm btn-success" id="save-hourly-schedule">
                                                            <i data-feather="save" class="mr-1"></i> Save Schedule
                                                        </button>
                                                        <button class="btn btn-sm btn-warning" id="initialize-from-working-hours">
                                                            <i data-feather="copy" class="mr-1"></i> Initialize from Working Hours
                                                        </button>
                                                        <button class="btn btn-sm btn-info" onclick="loadHourlySchedule()">
                                                            <i data-feather="play" class="mr-1"></i> Debug Load
                                                        </button>
                                                    </div>
                                                    <div>
                                                        <div class="legend d-flex align-items-center">
                                                            <span class="badge badge-success mr-2">Enabled</span>
                                                            <span class="badge badge-secondary">Disabled</span>
                                                        </div>
                                                    </div>
                                                </div>

                                                
                                            </div>
                                        </div>
                                        -->
                                        
                                    </div>
                                </div>

                                <!-- Simplified Password WiFi Tab Content -->
                                <div class="tab-pane fade" id="secured-wifi" role="tabpanel" aria-labelledby="secured-wifi-tab">
                                    <div class="card">
                                        <div class="card-header d-flex justify-content-between align-items-center">
                                            <h4 class="card-title mb-0">Password WiFi</h4>
                                            <button class="btn custom-btn save-password-network" id="save-secured-wifi">
                                                <i data-feather="save" class="mr-1"></i> Save Settings
                                            </button>
                                            </div>
                                        
                                            <div class="card-body">
                                                <!-- Basic Settings Section -->
                                                <div class="row">
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label for="wifi-ssid">Network Name (SSID)</label>
                                                            <div class="input-group">
                                                                <input type="text" class="form-control" id="password-wifi-ssid" placeholder="Home WiFi">
                                                                <div class="input-group-append">
                                                                    <button class="btn btn-outline-primary" type="button" data-toggle="modal" data-target="#password-ssid-qr-modal" title="Show QR Code">
                                                                        <i data-feather="code"></i> QR Code
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label for="password-wifi-password">WiFi Password</label>
                                                            <div class="input-group">
                                                                <input type="password" class="form-control" id="password-wifi-password" placeholder="Password">
                                                                <div class="input-group-append">
                                                                    <button class="btn btn-outline-secondary" type="button" id="toggle-password">
                                                                        <i data-feather="eye"></i>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <div class="form-group">
                                                            <label for="password-wifi-visible">WiFi Visible</label>
                                                            <div class="input-group">
                                                                <select class="form-control" id="password-wifi-visible">
                                                                    <option value="1">Visible</option>
                                                                    <option value="0">Hidden</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <div class="form-group">
                                                            <label for="wifi-security">Security Type</label>
                                                            <select class="form-control" id="password-wifi-security">
                                                                <option value="wpa2-psk" selected>WPA2-PSK (Recommended)</option>
                                                                <option value="wpa-wpa2-psk">WPA/WPA2-PSK Mixed</option>
                                                                <option value="wpa3-psk">WPA3-PSK (Most Secure)</option>
                                                                <option value="wep">WEP (Legacy, Not Recommended)</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <div class="form-group">
                                                            <label for="password_wifi_cipher_suites">Cipher Suites</label>
                                                            <select class="form-control" id="password_wifi_cipher_suites">
                                                                <option value="CCMP" selected>CCMP</option>
                                                                <option value="TKIP">TKIP</option>
                                                                <option value="TKIP+CCMP">TKIP+CCMP</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            
                                            <!-- Access Control Section -->
                                            <h5 class="border-bottom pb-1 mt-3">Access Control</h5>
                                            <div class="row">
                                                <div class="col-lg-6">
                                                    <!-- MAC Filtering -->
                                                    <div class="form-group">
                                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                                            <label class="mb-0">MAC Address Filtering</label>
                                                            <div class="d-flex align-items-center">
                                                                <label class="mr-2 mb-0 text-muted" style="font-size: 0.8rem;">Filter View:</label>
                                                                <select class="form-control form-control-sm" id="secured-mac-view-filter" style="width: auto;">
                                                                    <option value="all">Show All</option>
                                                                    <option value="blacklisted">Show Blacklisted</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        
                                                        <!-- Add MAC Address Controls -->
                                                        <div class="row mb-2">
                                                            <div class="col-7">
                                                                <input type="text" class="form-control form-control-sm" id="secured-mac-address" placeholder="00:11:22:33:44:55">
                                                            </div>
                                                            <div class="col-3">
                                                                <select class="form-control form-control-sm" id="secured-mac-type">
                                                                    <option value="blacklist">Blacklist</option>
                                                                </select>
                                                            </div>
                                                            <div class="col-2">
                                                                <button class="btn btn-sm custom-btn w-100" id="secured-add-mac">Add</button>
                                                            </div>
                                                        </div>
                                                        
                                                        <!-- MAC Address List -->
                                                        <div class="mac-address-container">
                                                            <div class="mac-filter-status text-muted mb-1" id="secured-mac-status">
                                                                <small>No MAC addresses added yet</small>
                                                            </div>
                                                            <div class="filtered-mac-list border rounded" style="min-height: 60px;">
                                                                <div class="text-center text-muted p-3" id="secured-mac-empty">
                                                                    <i data-feather="shield" class="mb-2"></i>
                                                                    <div><small>No MAC addresses configured</small></div>
                                                                    <div><small class="text-muted">Add MAC addresses above to control access</small></div>
                                                                </div>
                                                            </div>
                                                            <!-- Pagination Controls -->
                                                            <div class="mac-pagination-container mt-2" id="secured-mac-pagination" style="display: none;">
                                                                <nav aria-label="MAC address pagination">
                                                                    <ul class="pagination pagination-sm justify-content-center mb-0" id="secured-mac-pagination-list">
                                                                    </ul>
                                                                </nav>
                                                            </div>
                                                        </div>
                                                        
                                                        <div class="mt-2">
                                                            <button class="btn btn-sm btn-success" id="save-secured-mac-filter">
                                                                <i data-feather="save" class="mr-1"></i> Save MAC Filter Settings
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-lg-6">
                                                    <!-- Network Security Settings -->
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                </div><!-- End .tab-content -->
                            </div><!-- End .card-body -->
                        </div><!-- End .card -->
                    </div><!-- End .col-12 -->
                </div><!-- End .row -->
            </div><!-- End .content-body -->
        </div><!-- End .content-wrapper -->
    </div><!-- End .content -->
    <!-- END: Content -->

    <!-- BEGIN: Modals -->
    <!-- Enhanced Channel Scan Modal with Results View -->
    <div class="modal fade" id="channel-scan-modal" tabindex="-1" role="dialog" aria-labelledby="channel-scan-modal-title" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="channel-scan-modal-title">Channel Scan</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12">
                            <!-- Initial view before scan starts -->
                            <div id="pre-scan-view">
                                <div class="alert alert-info">
                                    <div class="alert-body">
                                        <i data-feather="info" class="mr-1 align-middle"></i>
                                        <span>Scanning will analyze nearby WiFi networks and interference to determine optimal channel settings.</span>
                                    </div>
                                </div>
                                
                                <!-- Device and Scan Counter Info -->
                                <div class="card bg-light-primary mb-3">
                                    <div class="card-body p-2">
                                        <div class="row">
                                            <div class="col-6">
                                                <h6 class="mb-1">Device Info</h6>
                                                <p class="mb-0"><strong>Location ID:</strong> <span id="modal-location-id">-</span></p>
                                                <p class="mb-0"><strong>Device ID:</strong> <span id="modal-device-id">-</span></p>
                                            </div>
                                            <div class="col-6">
                                                <h6 class="mb-1">Scan Counter</h6>
                                                <p class="mb-0"><strong>Current Counter:</strong> <span id="modal-scan-counter">-</span></p>
                                                <p class="mb-0"><strong>Next Scan ID:</strong> <span id="modal-next-scan-id">-</span></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 col-12">
                                        <h6>Last Scan Results:</h6>
                                        <ul class="list-group mb-2">
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <span>2.4 GHz Best Channel</span>
                                                <span class="badge badge-primary" id="last-best-channel-2g">Channel 6</span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <span>5 GHz Best Channel</span>
                                                <span class="badge badge-primary" id="last-best-channel-5g">Channel 36</span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <span>Scan Time</span>
                                                <span id="last-scan-time">2023-11-05 14:22</span>
                                            </li>
                                        </ul>
                                    </div>
                                    
                                    <div class="col-md-6 col-12">
                                        <h6>Nearby Networks:</h6>
                                        <ul class="list-group">
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <span>2.4 GHz</span>
                                                <span class="badge badge-light" id="nearby-networks-2g">8 networks</span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <span>5 GHz</span>
                                                <span class="badge badge-light" id="nearby-networks-5g">4 networks</span>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                
                                <div class="d-flex justify-content-center mt-2">
                                    <button class="btn custom-btn hidden" id="start-scan-btn">
                                        <i data-feather="refresh-cw" class="mr-1"></i> Start New Channel Scan
                                    </button>
                                </div>
                                <p class="text-muted text-center" id="scan-info-text">
                                    <small>
                                        <i data-feather="info" class="mr-1"></i>
                                        <span>The AP needs to be online to start a channel scan.</span>
                                    </small>
                                </p>
                            </div>
                            
                            <!-- Scan in progress view -->
                            <div id="scan-in-progress-view" style="display: none;">
                                <div class="alert alert-warning">
                                    <div class="alert-body">
                                        <i data-feather="clock" class="mr-1 align-middle"></i>
                                        <span>Scanning for available WiFi channels and detecting interference. This may take a minute...</span>
                                    </div>
                                </div>
                                
                                <!-- Current Scan Info -->
                                <!-- <div class="card bg-light-warning mb-2">
                                    <div class="card-body p-2">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="mb-0">Current Scan</h6>
                                                <small class="text-muted">Use this scan ID for curl testing</small>
                                            </div>
                                            <div class="text-right">
                                                <h4 class="mb-0 text-warning">ID: <span id="current-scan-id">-</span></h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                 -->
                                <div class="progress progress-bar-primary mb-2">
                                    <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%"></div>
                                </div>
                                
                                <div class="timeline">
                                    <div class="timeline-item">
                                        <div class="timeline-point-indicator" id="step-initiated-indicator"></div>
                                        <div class="timeline-event">
                                            <div class="d-flex justify-content-between">
                                                <h6>Scan Initiated</h6>
                                                <span class="text-muted">Step 1/4</span>
                                            </div>
                                            <p>Preparing device for channel scanning</p>
                                        </div>
                                    </div>
                                    <div class="timeline-item">
                                        <div class="timeline-point-indicator" id="step-started-indicator"></div>
                                        <div class="timeline-event">
                                            <div class="d-flex justify-content-between">
                                                <h6>Scan Started</h6>
                                                <span class="text-muted">Step 2/4</span>
                                            </div>
                                            <p>Device is ready and beginning frequency analysis</p>
                                        </div>
                                    </div>
                                    <div class="timeline-item">
                                        <div class="timeline-point-indicator" id="step-2g-indicator"></div>
                                        <div class="timeline-event">
                                            <div class="d-flex justify-content-between">
                                                <h6>Scanning 2.4 GHz Band</h6>
                                                <span class="text-muted">Step 3/4</span>
                                            </div>
                                            <p>Checking channels 1-11 for signal strength and interference</p>
                                        </div>
                                    </div>
                                    <div class="timeline-item">
                                        <div class="timeline-point-indicator" id="step-5g-indicator"></div>
                                        <div class="timeline-event">
                                            <div class="d-flex justify-content-between">
                                                <h6>Scanning 5 GHz Band</h6>
                                                <span class="text-muted">Step 4/4</span>
                                            </div>
                                            <p>Checking channels 36-165 for signal strength and interference</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Scan results view -->
                            <div id="scan-results-view" style="display: none;">
                                <div class="alert alert-success mb-2">
                                    <div class="alert-body">
                                        <i data-feather="check-circle" class="mr-1 align-middle"></i>
                                        <span>Scan complete! Optimal channels have been determined based on current RF environment.</span>
                                    </div>
                                </div>
                                
                                <div class="row mb-2">
                                    <div class="col-md-6 col-12">
                                        <div class="card bg-light-primary mb-0">
                                            <div class="card-body">
                                                <h5 class="card-title">2.4 GHz Results</h5>
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span>Recommended Channel:</span>
                                                    <h3 class="mb-0" id="result-channel-2g">6</h3>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6 col-12">
                                        <div class="card bg-light-primary mb-0">
                                            <div class="card-body">
                                                <h5 class="card-title">5 GHz Results</h5>
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span>Recommended Channel:</span>
                                                    <h3 class="mb-0" id="result-channel-5g">36</h3>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">Nearby Networks</h5>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-hover" id="nearby-networks-table">
                                    <thead>
                                        <tr>
                                            <th>Band</th>
                                            <th>Channel</th>
                                            <th>Networks</th>
                                            <th>Signal Strength</th>
                                            <th>Interference</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody id="nearby-networks-tbody">
                                        <!-- Dynamic content will be populated here -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                                
                                <div class="row">
                                    <div class="col-12 d-flex justify-content-between">
                                        <button class="btn custom-btn" id="apply-scan-results">
                                            <i data-feather="check" class="mr-1"></i> Apply These Settings
                                        </button>
                                        <button class="btn btn-outline-primary" id="back-to-scan-btn">
                                            <i data-feather="refresh-cw" class="mr-1"></i> Run Another Scan
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Portal Management Modal -->
    <div class="modal fade" id="portal-management-modal" tabindex="-1" role="dialog" aria-labelledby="portal-management-modal-title" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="portal-management-modal-title">Captive Portal Management</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <ul class="nav nav-pills mb-2">
                        <li class="nav-item">
                            <a class="nav-link active" id="html-pill" data-toggle="pill" href="#html-editor" aria-expanded="true">HTML</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="css-pill" data-toggle="pill" href="#css-editor" aria-expanded="false">CSS</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="js-pill" data-toggle="pill" href="#js-editor" aria-expanded="false">JavaScript</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="social-pill" data-toggle="pill" href="#social-settings" aria-expanded="false">Social Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="preview-pill" data-toggle="pill" href="#preview" aria-expanded="false">Preview</a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active" id="html-editor" role="tabpanel" aria-labelledby="html-pill">
                            <textarea class="form-control code-editor" rows="15" style="font-family: monospace;"><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><span class="location_name"></span> WiFi</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="login-container">
        <div class="logo">
            <img src="logo.png" alt="ogo">
        </div>
        <h1>Welcome to <span class="location_name"></span> WiFi</h1>
        <p>Please login to access the internet</p>
        
        <form class="login-form">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" required>
            </div>
            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" id="name" required>
            </div>
            <div class="checkbox">
                <input type="checkbox" id="terms" required>
                <label for="terms">I agree to the terms and conditions</label>
            </div>
            <button type="submit">Connect</button>
        </form>
        
        <div class="social-login">
            <p>Or connect with</p>
            <div class="social-buttons">
                <button class="facebook">Facebook</button>
                <button class="google">Google</button>
            </div>
        </div>
    </div>
</body>
</html></textarea>
                        </div>
                                        <div class="tab-pane" id="js-editor" role="tabpanel" aria-labelledby="js-pill">
                                            <textarea class="form-control code-editor" rows="15" style="font-family: monospace;">// Optional JavaScript for enhanced functionality
document.addEventListener('DOMContentLoaded', function() {
   // Form submission
   const loginForm = document.querySelector('.login-form');
   if (loginForm) {
       loginForm.addEventListener('submit', function(e) {
           e.preventDefault();
           // Add your form submission logic here
           console.log('Form submitted');
           // You can add AJAX request or other form handling code
       });
   }
});</textarea>
                                           </div>
                          <div class="tab-pane" id="preview" role="tabpanel" aria-labelledby="preview-pill">
                              <div style="border: 1px solid #ddd; border-radius: 4px; padding: 1rem; background-color: #f9f9f9; height: 400px; overflow: auto;">
                                  <h5 class="text-center">Preview will be rendered here</h5>
                                  <p class="text-center text-muted">This is a placeholder for the live preview of your portal page.</p>
                                  <!-- Preview content will be rendered here -->
                                      </div>
                                  </div>
                              </div>
                          </div>
                 </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn custom-btn">Save Changes</button>
                    </div>
                </div>
            </div>
        </div>
    
    <!-- Captive Portal Network Settings Modal -->
    <div class="modal fade" id="captive-network-modal" tabindex="-1" role="dialog" aria-labelledby="captive-network-modal-title" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="captive-network-modal-title">Edit Captive Portal Network Settings</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info mb-2">
                        <div class="alert-body">
                            <i data-feather="info" class="mr-50"></i>
                            <span>Captive Portal requires Static IP configuration.</span>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>IP Address</label>
                        <input type="text" class="form-control" placeholder="192.168.10.1" id="captive-portal-ip-modal" value="">
                    </div>
                    <div class="form-group">
                        <label>Netmask</label>
                        <input type="text" class="form-control" placeholder="255.255.255.0" id="captive-portal-netmask-modal" value="">
                    </div>
                    <div class="form-group">
                        <label>Gateway</label>
                        <input type="text" class="form-control" placeholder="192.168.10.1" id="captive-portal-gateway-modal" value="">
                    </div>
                    <div class="form-group">
                        <label>Primary DNS</label>
                        <input type="text" class="form-control" placeholder="8.8.8.8" id="captive-portal-dns1-modal" value="">
                    </div>
                    <div class="form-group">
                        <label>Secondary DNS</label>
                        <input type="text" class="form-control" placeholder="1.1.1.1" id="captive-portal-dns2-modal" value="">
                    </div>
                    
                    <div class="form-group vlan-setting">
                        <label>VLAN ID (Optional)</label>
                        <input type="number" class="form-control captive_portal_vlan_id" placeholder="20" id="captive-portal-vlan-modal_alt" value="" min="1" max="4094" disabled>
                        <small class="text-muted">Specify2 VLAN ID for captive portal network segmentation (1-4094). Enable VLAN support in Router Settings to use this feature.</small>
                    </div>
                    
                    <div class="form-group vlan-setting">
                        <label>VLAN Tagging</label>
                        <select class="form-control" id="captive-portal-vlan-tagging-modal" disabled>
                            <option value="disabled">Disabled</option>
                            <option value="tagged">Tagged</option>
                            <option value="untagged">Untagged</option>
                        </select>
                        <small class="text-muted">Configure VLAN tagging mode for captive portal network. Enable VLAN support in Router Settings to use this feature.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn custom-btn save-captive-portal">Save Changes1</button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Password WiFi Network Settings Modal -->
    <div class="modal fade" id="password-network-modal" tabindex="-1" role="dialog" aria-labelledby="password-network-modal-title" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="password-network-modal-title">Edit Password WiFi Network</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>IP Assignment</label>
                        <select class="form-control" id="password-ip-assignment">
                            <option value="STATIC" selected>Static IP</option>
                            <option value="DHCP">DHCP Client</option>
                        </select>
                        <small class="text-muted">When using DHCP Client, DHCP Server will be automatically disabled.</small>
                    </div>
                    
                    <div id="password-static-fields" class="hidden">
                        <div class="form-group">
                            <label>IP Address</label>
                            <input type="text" class="form-control" placeholder="192.168.1.1" id="password-ip" value="">
                        </div>
                        <div class="form-group">
                            <label>Netmask</label>
                            <input type="text" class="form-control" placeholder="255.255.255.0" id="password-netmask" value="">
                        </div>
                        <div class="form-group">
                            <label>Gateway</label>
                            <input type="text" class="form-control" placeholder="192.168.1.1" id="password-gateway" value="" readonly>
                            <small class="text-muted">Gateway is automatically set to the same as IP address.</small>
                        </div>
                        <div class="form-group">
                            <label>Primary DNS</label>
                            <input type="text" class="form-control" placeholder="8.8.8.8" id="password-primary-dns" value="">
                        </div>
                        <div class="form-group">
                            <label>Secondary DNS</label>
                            <input type="text" class="form-control" placeholder="1.1.1.1" id="password-secondary-dns" value="">
                        </div>
                        
                        <div class="form-group vlan-setting">
                            <label>VLAN ID (Optional)</label>
                            <input type="number" class="form-control" placeholder="10" id="password-wifi-vlan" value="" min="1" max="4094" disabled>
                            <small class="text-muted">Specify VLAN ID for network segmentation (1-4094). Enable VLAN support in Router Settings to use this feature.</small>
                        </div>
                        
                        <div class="form-group vlan-setting">
                            <label for="password-wifi-vlan-tagging-modal">VLAN Tagging</label>
                            <select class="form-control" id="password-wifi-vlan-tagging-modal" >
                                <option value="disabled">Disabled</option>
                                <option value="tagged">Tagged</option>
                                <option value="untagged">Untagged</option>
                            </select>
                            <small class="text-muted">Configure VLAN tagging mode for password WiFi network. Enable VLAN support in Router Settings to use this feature.</small>
                        </div>

                        <div class="form-group mt-3 pt-2 border-top">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="password-dhcp-server-toggle" checked>
                                <label class="custom-control-label" for="password-dhcp-server-toggle">Enable DHCP Server</label>
                            </div>
                            <small class="text-muted">Provides automatic IP addressing for connected clients.</small>
                        </div>
                        
                        <div id="password-dhcp-server-fields" class="hidden">
                            <div class="form-group">
                                <label>DHCP Range Start</label>
                                <input type="text" class="form-control" placeholder="192.168.1.100" id="password-dhcp-start" value="192.168.1.100">
                            </div>
                            <div class="form-group">
                                <label>DHCP Range End</label>
                                <input type="text" class="form-control" placeholder="192.168.1.200" id="password-dhcp-end" value="192.168.1.200">
                            </div>
                            <div class="form-group">
                                <label>Lease Time (hours)</label>
                                <input type="number" class="form-control" placeholder="24" id="password-lease-time" value="24">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn custom-btn save-password-network">Save Changes</button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- WAN Settings Modal -->
    <div class="modal fade" id="wan-settings-modal" tabindex="-1" role="dialog" aria-labelledby="wan-settings-modal-title" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="wan-settings-modal-title">Edit WAN Interface Settings</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Connection Type</label>
                        <select class="form-control" id="wan-connection-type">
                            <option value="DHCP">DHCP</option>
                            <option value="STATIC">Static IP</option>
                            <option value="PPPOE">PPPoE</option>
                        </select>
                    </div>
                    
                    <div id="wan-static-fields" class="hidden">
                        <div class="form-group">
                            <label>IP Address</label>
                            <input type="text" class="form-control" id="wan-ip-address" placeholder="203.0.113.10" value="203.0.113.10">
                        </div>
                        <div class="form-group">
                            <label>Netmask</label>
                            <input type="text" class="form-control" id="wan-netmask" placeholder="255.255.255.0" value="255.255.255.0">
                        </div>
                        <div class="form-group">
                            <label>Gateway</label>
                            <input type="text" class="form-control" id="wan-gateway" placeholder="203.0.113.1" value="203.0.113.1">
                        </div>
                        <div class="form-group">
                            <label>Primary DNS</label>
                            <input type="text" class="form-control" id="wan-primary-dns" placeholder="8.8.8.8" value="8.8.8.8">
                        </div>
                        <div class="form-group">
                            <label>Secondary DNS</label>
                            <input type="text" class="form-control" id="wan-secondary-dns" placeholder="1.1.1.1" value="1.1.1.1">
                        </div>
                    </div>
                    
                    <div id="wan-pppoe-fields" style="display: none;">
                        <div class="form-group">
                            <label>Username</label>
                            <input type="text" class="form-control" id="wan-pppoe-username" placeholder="Username">
                        </div>
                        <div class="form-group">
                            <label>Password</label>
                            <input type="password" class="form-control" id="wan-pppoe-password" placeholder="Password">
                        </div>
                        <div class="form-group">
                            <label>Service Name (Optional)</label>
                            <input type="text" class="form-control" id="wan-pppoe-service-name" placeholder="Service Name">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn custom-btn save-wan-settings">Save Changes</button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- LAN Settings Modal -->
    <div class="modal fade" id="lan-settings-modal" tabindex="-1" role="dialog" aria-labelledby="lan-settings-modal-title" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="lan-settings-modal-title">Edit LAN Settings</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>IP Address</label>
                        <input type="text" class="form-control" placeholder="192.168.1.1" value="192.168.1.1">
                    </div>
                    <div class="form-group">
                        <label>Netmask</label>
                        <input type="text" class="form-control" placeholder="255.255.255.0" value="255.255.255.0">
                    </div>
                    
                    <div class="form-group">
                        <div class="custom-control custom-switch mb-1">
                            <input type="checkbox" class="custom-control-input" id="dhcp-server-toggle" checked>
                            <label class="custom-control-label" for="dhcp-server-toggle">Enable DHCP Server</label>
                        </div>
                    </div>
                    
                    <div id="dhcp-server-fields">
                        <div class="form-group">
                            <label>DHCP Start Address</label>
                            <input type="text" class="form-control" placeholder="192.168.1.100" value="192.168.1.100">
                        </div>
                        <div class="form-group">
                            <label>DHCP End Address</label>
                            <input type="text" class="form-control" placeholder="192.168.1.200" value="192.168.1.200">
                        </div>
                        <div class="form-group">
                            <label>Lease Time (hours)</label>
                            <input type="number" class="form-control" placeholder="24" value="24">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn custom-btn save-lan-settings">Save Changes</button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Captive Portal Settings Modal -->
    <div class="modal fade" id="captive-portal-modal" tabindex="-1" role="dialog" aria-labelledby="captive-portal-modal-title" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="captive-portal-modal-title">Captive Portal IP Management</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Static IP Settings -->
                    <div class="form-group">
                        <label>IP Assignment</label>
                        <select class="form-control" id="captive-ip-assignment" disabled>
                            <option value="static" selected>Static IP</option>
                        </select>
                        <small class="text-muted">Only Static IP configuration is available for Captive Portal</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="captive-portal-ip">IP Address</label>
                        <input type="text" class="form-control" id="captive-portal-ip" placeholder="192.168.2.1">
                    </div>
                    
                    <div class="form-group">
                        <label for="captive-portal-netmask">Netmask</label>
                        <input type="text" class="form-control" id="captive-portal-netmask" placeholder="255.255.255.0">
                    </div>
<!--                     
                    <div class="form-group">
                        <label for="captive-portal-gateway">Gateway</label>
                        <input type="text" class="form-control" id="captive-portal-gateway" placeholder="192.168.2.1">
                    </div> -->
                    
                    <div class="form-group vlan-setting">
                        <label for="captive-portal-vlan-modal">VLAN ID (Optional)</label>
                        <input type="number" class="form-control" id="captive-portal-vlan-modal" placeholder="20" min="1" max="4094" disabled>
                        <small class="text-muted">Specify1 VLAN ID for captive portal network segmentation (1-4094). Enable VLAN support in Router Settings to use this feature.</small>
                    </div>
                    
                    <div class="form-group vlan-setting">
                        <label for="captive-portal-vlan-tagging-modal_alt">VLAN Tagging</label>
                        <select class="form-control" id="captive-portal-vlan-tagging-modal_alt" >
                            <option value="disabled">Disabled</option>
                            <option value="tagged">Tagged</option>
                            <option value="untagged">Untagged</option>
                        </select>
                        <small class="text-muted">Configure VLAN tagging mode for captive portal network. Enable VLAN support in Router Settings to use this feature.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn custom-btn save-captive-portal" id="save-captive-portal-settings">Save Changes</button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Device Restart Confirmation Modal -->
    <div class="modal fade" id="restart-confirmation-modal" tabindex="-1" role="dialog" aria-labelledby="restart-confirmation-modal-title" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="restart-confirmation-modal-title">
                        <i data-feather="refresh-cw" class="mr-2"></i>Restart Device
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning mb-3">
                        <div class="alert-body">
                            <i data-feather="alert-triangle" class="mr-2"></i>
                            <strong>Warning:</strong> This action will restart the device and temporarily interrupt internet access.
                        </div>
                    </div>
                    
                    <div class="d-flex align-items-center mb-3">
                        <div class="avatar bg-light-primary p-50 mr-3">
                            <div class="avatar-content">
                                <i data-feather="hard-drive" class="font-medium-4"></i>
                            </div>
                        </div>
                        <div>
                            <h6 class="mb-0">Device Information</h6>
                            <p class="card-text text-muted mb-0">Location: <span class="location_name font-weight-bold"></span></p>
                            <p class="card-text text-muted mb-0">Model: <span class="router_model font-weight-bold"></span></p>
                            <p class="card-text text-muted mb-0">MAC Address: <span class="router_mac_address font-weight-bold"></span></p>
                        </div>
                    </div>
                    
                    <div class="bg-light-secondary p-2 rounded mb-3">
                        <h6 class="mb-2">What happens during restart:</h6>
                        <ul class="mb-0 pl-3">
                            <li>WiFi networks will be temporarily unavailable (2-3 minutes)</li>
                            <li>Connected users will be disconnected</li>
    
                        </ul>
                    </div>
                    
                    <p class="text-muted">Are you sure you want to restart this device?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirm-restart-btn">
                        <i data-feather="refresh-cw" class="mr-1"></i>
                        <span>Restart Device</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- SSID QR Code Modal -->
    <div class="modal fade" id="ssid-qr-modal" tabindex="-1" role="dialog" aria-labelledby="ssid-qr-modal-title" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="ssid-qr-modal-title">
                        <i data-feather="wifi" class="mr-2"></i>WiFi Network QR Code
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <div class="alert alert-info mb-3">
                        <div class="alert-body">
                            <i data-feather="info" class="mr-2"></i>
                            <strong>Scan this QR code</strong> to connect to the WiFi network automatically.
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <h6 class="mb-2">Network Name (SSID):</h6>
                        <h4 class="text-primary font-weight-bold" id="qr-ssid-display">Guest WiFi</h4>
                    </div>
                    
                    <!-- QR Code Container -->
                    <div class="d-flex justify-content-center mb-3">
                        <div id="qr-code-container" class="p-3 bg-white border rounded"></div>
                    </div>
                    
                    <p class="text-muted small">Point your device's camera at the QR code to connect</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="download-qr-btn">
                        <i data-feather="download" class="mr-1"></i>
                        Download QR Code
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Password WiFi SSID QR Code Modal -->
    <div class="modal fade" id="password-ssid-qr-modal" tabindex="-1" role="dialog" aria-labelledby="password-ssid-qr-modal-title" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="password-ssid-qr-modal-title">
                        <i data-feather="wifi" class="mr-2"></i>WiFi Network QR Code
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <div class="alert alert-info mb-3">
                        <div class="alert-body">
                            <i data-feather="info" class="mr-2"></i>
                            <strong>Scan this QR code</strong> to connect to the WiFi network automatically.
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <h6 class="mb-2">Network Name (SSID):</h6>
                        <h4 class="text-primary font-weight-bold" id="password-qr-ssid-display">Home WiFi</h4>
                    </div>
                    
                    <div class="mb-3">
                        <h6 class="mb-2">Security:</h6>
                        <span class="badge badge-light-success">WPA/WPA2 Protected</span>
                    </div>
                    
                    <!-- QR Code Container -->
                    <div class="d-flex justify-content-center mb-3">
                        <div id="password-qr-code-container" class="p-3 bg-white border rounded"></div>
                    </div>
                    
                    <p class="text-muted small">Point your device's camera at the QR code to connect</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="download-password-qr-btn">
                        <i data-feather="download" class="mr-1"></i>
                        Download QR Code
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Firmware Update Modal -->
    <div class="modal fade" id="firmware-update-modal" tabindex="-1" role="dialog" aria-labelledby="firmware-update-modal-title" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="firmware-update-modal-title">
                        <i data-feather="download" class="mr-2"></i>Update Firmware
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info mb-3">
                        <div class="alert-body">
                            <i data-feather="info" class="mr-2"></i>
                            <strong>Important:</strong> Firmware update will restart the device and may take 5-10 minutes to complete.
                        </div>
                    </div>
                    
                    <div class="d-flex align-items-center mb-3">
                        <div class="avatar bg-light-primary p-50 mr-3">
                            <div class="avatar-content">
                                <i data-feather="hard-drive" class="font-medium-4"></i>
                            </div>
                        </div>
                        <div>
                            <h6 class="mb-0">Current Device Information</h6>
                            <p class="card-text text-muted mb-0">Model: <span class="router_model font-weight-bold"></span></p>
                            <p class="card-text text-muted mb-0">Current Firmware: <span class="router_firmware font-weight-bold"></span></p>
                            <p class="card-text text-muted mb-0">MAC Address: <span class="router_mac_address font-weight-bold"></span></p>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="firmware-version-select">Available Firmware Versions</label>
                        <select class="form-control" id="firmware-version-select">
                            <option value="">Loading firmware versions...</option>
                        </select>
                        <small class="text-muted">Select a firmware version compatible with your device model.</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="firmware-description">Firmware Description</label>
                        <div class="card">
                            <div class="card-body p-2">
                                <div id="firmware-description">
                                    <p class="text-muted mb-0">Select a firmware version to view details.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-light-warning p-2 rounded mb-3">
                        <h6 class="mb-2">During firmware update:</h6>
                        <ul class="mb-0 pl-3">
                            <li>Device will reboot automatically</li>
                            <li>WiFi networks will be unavailable for 5-10 minutes</li>
                            <li>All connected users will be disconnected</li>
                            <li>Do not power off the device during update</li>
                        </ul>
                    </div>
                    
                    
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn custom-btn" id="start-firmware-update-btn" disabled>
                        <i data-feather="download" class="mr-1"></i>
                        <span>Update Firmware</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Firmware Update Progress Modal -->
    <div class="modal fade" id="firmware-progress-modal" tabindex="-1" role="dialog" aria-labelledby="firmware-progress-modal-title" aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="firmware-progress-modal-title">
                        <i data-feather="download" class="mr-2"></i>Updating Firmware
                    </h5>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning mb-3">
                        <div class="alert-body">
                            <i data-feather="alert-triangle" class="mr-2"></i>
                            <strong>Do not close this window or power off the device during update.</strong>
                        </div>
                    </div>
                    
                    <div class="text-center mb-3">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                    </div>
                    
                    <div class="progress progress-bar-primary mb-2">
                        <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%" id="firmware-progress-bar"></div>
                    </div>
                    
                    <div class="text-center">
                        <h6 id="firmware-progress-status">Preparing firmware update...</h6>
                        <p class="text-muted mb-0" id="firmware-progress-description">This may take several minutes to complete.</p>
                    </div>
                    
                    <div class="timeline mt-3">
                        <div class="timeline-item">
                            <div class="timeline-point-indicator timeline-point-primary" id="step-1-indicator"></div>
                            <div class="timeline-event">
                                <h6>Uploading Firmware</h6>
                                <p class="text-muted mb-0">Transferring firmware to device</p>
                            </div>
                        </div>
                        <div class="timeline-item">
                            <div class="timeline-point-indicator" id="step-2-indicator"></div>
                            <div class="timeline-event">
                                <h6>Installing Update</h6>
                                <p class="text-muted mb-0">Writing firmware to device memory</p>
                            </div>
                        </div>
                        <div class="timeline-item">
                            <div class="timeline-point-indicator" id="step-3-indicator"></div>
                            <div class="timeline-event">
                                <h6>Rebooting Device</h6>
                                <p class="text-muted mb-0">Device will restart with new firmware</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- MAC Address Edit Modal -->
    <div class="modal fade" id="mac-address-edit-modal" tabindex="-1" role="dialog" aria-labelledby="mac-address-edit-modal-title" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="mac-address-edit-modal-title">
                        <i data-feather="edit" class="mr-2"></i>Edit MAC Address
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info mb-3">
                        <div class="alert-body">
                            <i data-feather="info" class="mr-2"></i>
                            <strong>Note:</strong> This will update the MAC address for the device associated with this location.
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="mac-address-input">MAC Address</label>
                        <input type="text" class="form-control" id="mac-address-input" placeholder="XX-XX-XX-XX-XX-XX" maxlength="17">
                        <small class="text-muted">Enter the MAC address in format XX-XX-XX-XX-XX-XX</small>
                    </div>
                    
                    <div class="form-group">
                        <label>Current MAC Address</label>
                        <div class="form-control-plaintext bg-light p-2 rounded">
                            <span id="current-mac-display">-</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn custom-btn" id="save-mac-address-btn">
                        <i data-feather="save" class="mr-1"></i>
                        <span>Save Changes</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- MAC Address Radio Selection Modal for Captive Portal -->
    <div class="modal fade" id="captive-mac-radio-modal" tabindex="-1" role="dialog" aria-labelledby="captive-mac-radio-modal-title" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="captive-mac-radio-modal-title">Select Radio Frequency</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info mb-3">
                        <div class="alert-body">
                            <i data-feather="info" class="mr-2"></i>
                            <strong>Select which radio frequency to block this MAC address on:</strong>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>MAC Address</label>
                        <div class="form-control-plaintext bg-light p-2 rounded font-weight-bold" id="captive-mac-radio-display">
                            -
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Radio Frequency</label>
                        <div class="custom-control custom-radio mb-2">
                            <input type="radio" id="captive-radio-24" name="captive-radio-selection" class="custom-control-input" value="2.4GHz">
                            <label class="custom-control-label" for="captive-radio-24">2.4GHz Radio</label>
                        </div>
                        <div class="custom-control custom-radio mb-2">
                            <input type="radio" id="captive-radio-5" name="captive-radio-selection" class="custom-control-input" value="5GHz">
                            <label class="custom-control-label" for="captive-radio-5">5GHz Radio</label>
                        </div>
                        <div class="custom-control custom-radio">
                            <input type="radio" id="captive-radio-both" name="captive-radio-selection" class="custom-control-input" value="both" checked>
                            <label class="custom-control-label" for="captive-radio-both">All</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn custom-btn" id="captive-confirm-add-mac">
                        <i data-feather="check" class="mr-1"></i>
                        Confirm
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- MAC Address Radio Selection Modal for Secured WiFi -->
    <div class="modal fade" id="secured-mac-radio-modal" tabindex="-1" role="dialog" aria-labelledby="secured-mac-radio-modal-title" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="secured-mac-radio-modal-title">Select Radio Frequency</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info mb-3">
                        <div class="alert-body">
                            <i data-feather="info" class="mr-2"></i>
                            <strong>Select which radio frequency to block this MAC address on:</strong>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>MAC Address</label>
                        <div class="form-control-plaintext bg-light p-2 rounded font-weight-bold" id="secured-mac-radio-display">
                            -
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Radio Frequency</label>
                        <div class="custom-control custom-radio mb-2">
                            <input type="radio" id="secured-radio-24" name="secured-radio-selection" class="custom-control-input" value="2.4GHz">
                            <label class="custom-control-label" for="secured-radio-24">2.4GHz Radio</label>
                        </div>
                        <div class="custom-control custom-radio mb-2">
                            <input type="radio" id="secured-radio-5" name="secured-radio-selection" class="custom-control-input" value="5GHz">
                            <label class="custom-control-label" for="secured-radio-5">5GHz Radio</label>
                        </div>
                        <div class="custom-control custom-radio">
                            <input type="radio" id="secured-radio-both" name="secured-radio-selection" class="custom-control-input" value="both" checked>
                            <label class="custom-control-label" for="secured-radio-both">All</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn custom-btn" id="secured-confirm-add-mac">
                        <i data-feather="check" class="mr-1"></i>
                        Confirm
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- END: Modals -->

    <!-- BEGIN: Vendor JS-->
    <script src="/app-assets/vendors/js/vendors.min.js"></script>
    <!-- BEGIN Vendor JS-->

    <!-- BEGIN: Page Vendor JS-->
    <script src="/app-assets/vendors/js/charts/apexcharts.min.js"></script>
    <script src="/app-assets/vendors/js/extensions/toastr.min.js"></script>
    <script src="/app-assets/vendors/js/forms/select/select2.full.min.js"></script>
    <script src="/app-assets/vendors/js/pickers/flatpickr/flatpickr.min.js"></script>
    <script src="/app-assets/vendors/js/maps/leaflet.min.js"></script>
    <!-- Interact.js for interactive schedule -->
    <script src="https://cdn.jsdelivr.net/npm/interactjs/dist/interact.min.js"></script>
    <!-- QRCode.js for QR code generation -->
    <script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
    <!-- END: Page Vendor JS-->

    <!-- BEGIN: Theme JS-->
    <script src="/app-assets/js/core/app-menu.js"></script>
    <script src="/app-assets/js/core/app.js"></script>
    <!-- END: Theme JS-->

    <!-- BEGIN: Page JS-->
    <script src="/app-assets/js/scripts/charts/chart-apex.js"></script>
    <script src="/app-assets/js/scripts/extensions/ext-component-toastr.js"></script>
    <script src="/app-assets/js/scripts/maps/map-leaflet.js"></script>
    <!-- END: Page JS-->
    <script src="/assets/js/config.js?v=1"></script>
    <script src="/assets/js/location-details.js?v=7"></script>
    <script>
        // ==============================================
        // FORM VALIDATION UTILITIES
        // ==============================================
        
        // Form validation helper functions
        function validateEmail(email) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return emailRegex.test(email);
        }
        // alert("V3   ");
        function validatePhone(phone) {
            const phoneRegex = /^[\+]?[1-9][\d]{0,15}$/;
            return phoneRegex.test(phone.replace(/[\s\-\(\)]/g, ''));
        }
        
        function showFieldError(fieldId, message) {
            const field = $('#' + fieldId);
            field.addClass('is-invalid').removeClass('is-valid');
            field.siblings('.invalid-feedback').text(message);
        }
        
        function showFieldSuccess(fieldId) {
            const field = $('#' + fieldId);
            field.addClass('is-valid').removeClass('is-invalid');
            field.siblings('.invalid-feedback').text('');
        }
        
        function clearFieldValidation(fieldId) {
            const field = $('#' + fieldId);
            field.removeClass('is-invalid is-valid');
            field.siblings('.invalid-feedback').text('');
        }
        
        // Reset location form
        function resetLocationForm() {
            $('#location-info-form')[0].reset();
            $('#location-info-form .form-control').removeClass('is-invalid is-valid');
            $('#location-info-form .invalid-feedback').text('');
            updateDescriptionCounter();
        }
        
        // Update description character counter
        function updateDescriptionCounter() {
            const description = $('#location-description').val();
            const counter = $('#description-counter');
            counter.text(description.length);
            
            if (description.length > 450) {
                counter.addClass('text-warning').removeClass('text-danger');
            } else if (description.length > 500) {
                counter.addClass('text-danger').removeClass('text-warning');
            } else {
                counter.removeClass('text-warning text-danger');
            }
        }

        const user = UserManager.getUser();
        const token = UserManager.getToken();
        console.log("user: ", user);
        console.log("token: ", token);
        // alert("v2 page");
        
        if (!token || !user) {
            // No token or user found, redirect to login page
            window.location.href = '/';
            // return;
        }
        $('.user-name').text(user.name);
        $('.user-status').text(user.role);
        var profile_picture = localStorage.getItem('profile_picture');
        // alert("profile_picture: " + profile_picture);
        $('.user-profile-picture').attr('src', '/uploads/profile_pictures/' + profile_picture);
        
        var button_default_color = '#007bff';
        var button_default_text_color = '#fff';
        // var button_default_text = 'Save Changes';

        // ==============================================
        // DOCUMENT READY FUNCTIONS
        // ==============================================
        
        // Firmware Update Modal functionality
        $(document).ready(function() {
            // ==============================================
            // FORM EVENT HANDLERS
            // ==============================================
            
            // Description character counter
            $('#location-description').on('input', function() {
                updateDescriptionCounter();
            });
            
            // Email validation
            $('#location-contact-email').on('blur', function() {
                const email = $(this).val();
                if (email && !validateEmail(email)) {
                    showFieldError('location-contact-email', 'Please enter a valid email address');
                } else if (email) {
                    showFieldSuccess('location-contact-email');
                } else {
                    clearFieldValidation('location-contact-email');
                }
            });
            
            // Phone validation
            $('#location-contact-phone').on('blur', function() {
                const phone = $(this).val();
                if (phone && !validatePhone(phone)) {
                    showFieldError('location-contact-phone', 'Please enter a valid phone number');
                } else if (phone) {
                    showFieldSuccess('location-contact-phone');
                } else {
                    clearFieldValidation('location-contact-phone');
                }
            });
            
            // Required field validation
            $('#location-name').on('blur', function() {
                const value = $(this).val().trim();
                if (!value) {
                    showFieldError('location-name', 'Location name is required');
                } else {
                    showFieldSuccess('location-name');
                }
            });
            
            // Initialize character counter
            updateDescriptionCounter();
            
            // ==============================================
            // ANALYTICS FUNCTIONALITY
            // ==============================================
            
            // Initialize Analytics components
            initializeAnalytics();
            
            // ==============================================
            // EXISTING FUNCTIONALITY
            // ==============================================
            
            // Show firmware update modal when button is clicked
            $('#update-firmware-btn').on('click', function() {
                // Check if router model is set
                const routerModel = $('.router_model_updated').text().trim();
                if (!routerModel) {
                    toastr.error('Please set the router model first in Location Details before updating firmware.');
                    return;
                }
                
                $('#firmware-update-modal').modal('show');
                loadFirmwareVersions();
            });

            // Load firmware versions based on router model
            function loadFirmwareVersions() {
                // Get router model from the dropdown selection or current device
                const routerModel = $('#router-model-select').val() || $('.router_model_updated').text();
                const $select = $('#firmware-version-select');
                
                console.log('Loading firmware for model:', routerModel);
                
                // Clear existing options
                $select.html('<option value="">Loading firmware versions...</option>');
                
                // Make API call to get firmware versions based on model
                getFirmwareByModel(routerModel)
                
                    .then(function(firmwareVersions) {
                        console.log('Received firmware versions:', firmwareVersions);
                        
                        $select.empty();
                        if (firmwareVersions.length === 0) {
                            $select.html('<option value="">No firmware versions available for this model</option>');
                            $('#firmware-description').html('<div class="alert alert-warning mb-0"><i data-feather="alert-triangle" class="mr-1"></i>This device model (' + routerModel + ') is not supported for firmware updates. Only 820AX and 835AX models are supported.</div>');
                            return;
                        }
                        
                        $select.append('<option value="">Select firmware version...</option>');
                        
                        firmwareVersions.forEach(function(firmware) {
                            console.log('Processing firmware:', firmware);
                            const option = `<option value="${firmware.id}" 
                                            data-name="${firmware.name}"
                                            data-version="${firmware.version}"
                                            data-description="${firmware.description}"
                                            data-release-date="${firmware.release_date}"
                                            data-file-size="${firmware.file_size}"
                                            data-changelog="${firmware.changelog}"
                                            data-model="${firmware.model}"
                                            data-file-name="${firmware.file_name}"
                                            data-md5sum="${firmware.md5sum}">
                                            ${firmware.name}
                                            ${firmware.is_latest ? ' (Latest)' : ''}
                                            ${firmware.is_current ? ' (Current)' : ''}
                                        </option>`;
                            $select.append(option);
                        });
                        
                        // Pre-select current firmware if device data is available
                        if (window.currentDeviceData && window.currentDeviceData.firmware_id) {
                            console.log('Pre-selecting current firmware ID:', window.currentDeviceData.firmware_id);
                            $select.val(window.currentDeviceData.firmware_id);
                            $select.trigger('change'); // Trigger change event to show firmware details
                        } else if (window.currentDeviceData && window.currentDeviceData.firmware_version) {
                            // If no firmware_id but we have firmware_version, try to match by name
                            console.log('Trying to pre-select by firmware version name:', window.currentDeviceData.firmware_version);
                            $select.find('option').each(function() {
                                if ($(this).data('name') === window.currentDeviceData.firmware_version) {
                                    $select.val($(this).val());
                                    $select.trigger('change');
                                    return false; // Break the loop
                                }
                            });
                        }
                    })
                    .catch(function(error) {
                        console.error('Error loading firmware versions:', error);
                        $select.html('<option value="">Error loading firmware versions</option>');
                        $('#firmware-description').html('<div class="alert alert-danger mb-0"><i data-feather="alert-circle" class="mr-1"></i>Failed to load firmware versions. Please try again later.</div>');
                        toastr.error('Failed to load firmware versions');
                    });
            }

            // API call to get firmware by model (make it globally accessible)
            window.getFirmwareByModel = function(model) {

                console.log('Getting firmware by model:', model);
                return new Promise(function(resolve, reject) {
                    // Check if model is supported
                    if (!model || (model !== '820AX' && model !== '835AX')) {
                        resolve([]);
                        return;
                    }

                    $.ajax({
                        url: '/api/firmware/model/' + model,
                        method: 'GET',
                        headers: {
                            'Authorization': 'Bearer ' + UserManager.getToken(),
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        success: function(response) {
                            console.log("firmware response:::::", response);
                            // Transform API response to match expected format
                            let firmwareList = [];
                            
                            if (response.data && Array.isArray(response.data)) {
                                firmwareList = response.data.map(function(firmware) {
                                    return {
                                        id: firmware.id,
                                        name: firmware.name || 'Unnamed Firmware',
                                        version: firmware.version || firmware.name || 'Unknown Version',
                                        description: firmware.description || 'No description available',
                                        release_date: firmware.created_at ? firmware.created_at.split('T')[0] : 'Unknown',
                                        file_size: firmware.file_size ? (firmware.file_size + ' bytes') : 'Unknown',
                                        changelog: firmware.description || 'No changelog available',
                                        is_latest: false, // You may need to determine this logic
                                        is_current: false, // You may need to determine this logic
                                        model: firmware.model,
                                        file_name: firmware.file_name,
                                        md5sum: firmware.md5sum,
                                        is_enabled: firmware.is_enabled
                                    };
                                });
                            } else if (response && Array.isArray(response)) {
                                // Handle direct array response
                                firmwareList = response.map(function(firmware) {
                                    return {
                                        id: firmware.id,
                                        name: firmware.name || 'Unnamed Firmware',
                                        version: firmware.version || firmware.name || 'Unknown Version',
                                        description: firmware.description || 'No description available',
                                        release_date: firmware.created_at ? firmware.created_at.split('T')[0] : 'Unknown',
                                        file_size: firmware.file_size ? (firmware.file_size + ' bytes') : 'Unknown',
                                        changelog: firmware.description || 'No changelog available',
                                        is_latest: false,
                                        is_current: false,
                                        model: firmware.model,
                                        file_name: firmware.file_name,
                                        md5sum: firmware.md5sum,
                                        is_enabled: firmware.is_enabled
                                    };
                                });
                            }
                            
                            resolve(firmwareList);
                        },
                        error: function(xhr, status, error) {
                            console.error('API Error:', xhr.responseText);
                            reject(error);
                        }
                    });
                });
            };

            // Handle firmware version selection
            $('#firmware-version-select').on('change', function() {
                const selectedOption = $(this).find('option:selected');
                const $button = $('#start-firmware-update-btn');
                const $description = $('#firmware-description');

                if (selectedOption.val()) {
                    // Enable update button
                    $button.prop('disabled', false);
                    
                    // Show firmware details
                    const details = `
                        <div class="row">
                            <div class="col-6">
                                <strong>Name:</strong> ${selectedOption.data('name')}<br>
                                <strong>Version:</strong> ${selectedOption.data('version')}<br>
                                <strong>Release Date:</strong> ${selectedOption.data('release-date')}<br>
                                <strong>File Size:</strong> ${selectedOption.data('file-size')}
                            </div>
                            <div class="col-6">
                                <strong>Model:</strong> ${selectedOption.data('model')}<br>
                                <strong>File Name:</strong> ${selectedOption.data('file-name')}<br>
                                <strong>MD5 Checksum:</strong><br>
                                <small class="text-muted">${selectedOption.data('md5sum')}</small>
                            </div>
                        </div>
                        <hr class="my-2">
                        <p class="mb-0">${selectedOption.data('description')}</p>
                    `;
                    $description.html(details);
                } else {
                    // Disable update button
                    $button.prop('disabled', true);
                    $description.html('<p class="text-muted mb-0">Select a firmware version to view details.</p>');
                }
            });

            // Handle firmware update start
            $('#start-firmware-update-btn').on('click', function() {
                const selectedOption = $('#firmware-version-select option:selected');
                
                if (!selectedOption.val()) {
                    toastr.error('Please select a firmware version to update.');
                    return;
                }

                const firmwareId = selectedOption.val();
                const firmwareName = selectedOption.data('name');
                const locationId = getLocationId();
                
                console.log('Initiating firmware update:', {
                    locationId: locationId,
                    firmwareId: firmwareId,
                    firmwareName: firmwareName
                });
                
                if (!locationId) {
                    toastr.error('Location ID not found');
                    return;
                }

                // Show loading state
                const $button = $(this);
                const originalText = $button.html();
                $button.html('<i data-feather="loader" class="mr-1"></i> Initiating Update...').prop('disabled', true);

                // Make API call to update firmware
                $.ajax({
                    url: `/api/locations/${locationId}/update-firmware`,
                    method: 'POST',
                    headers: {
                        'Authorization': 'Bearer ' + UserManager.getToken(),
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    data: JSON.stringify({
                        firmware_id: firmwareId,
                        firmware_version: firmwareName
                    }),
                    success: function(response) {
                        console.log('Firmware update API response:', response);
                        
                        // Hide selection modal
                        $('#firmware-update-modal').modal('hide');
                        
                        // Show success message
                        toastr.success('Firmware update initiated successfully! The device will be upgraded in 5-10 minutes. Please do not power off the device during this time.', 'Firmware Update Started', {
                            timeOut: 8000,
                            extendedTimeOut: 3000,
                            closeButton: true,
                            progressBar: true
                        });
                        
                        // Update the displayed firmware version
                        if (response.data && response.data.device && response.data.device.firmware_version) {
                            $('.router_firmware').text(response.data.device.firmware_version);
                            // Update the stored device data
                            if (window.currentDeviceData) {
                                window.currentDeviceData.firmware_version = response.data.device.firmware_version;
                                window.currentDeviceData.firmware_id = response.data.device.firmware_id;
                            }
                        } else {
                            // If no firmware version in response, use the firmware name we sent
                            $('.router_firmware').text(firmwareName);
                            if (window.currentDeviceData) {
                                window.currentDeviceData.firmware_version = firmwareName;
                                window.currentDeviceData.firmware_id = firmwareId;
                            }
                        }
                        
                        // Reset button state
                        $button.html(originalText).prop('disabled', false);
                    },
                    error: function(xhr, status, error) {
                        console.error('Firmware update failed:', xhr.responseText);
                        
                        // Reset button state
                        $button.html(originalText).prop('disabled', false);
                        
                        // Handle API error
                        handleApiError(xhr, status, error, 'updating device firmware');
                    }
                });
            });
        });

        // Function to load web filter categories
        function loadWebFilterCategories() {
            console.log('Loading web filter categories from API');
            
            $.ajax({
                url: '/api/categories/enabled',
                method: 'GET',
                headers: {
                    'Authorization': 'Bearer ' + UserManager.getToken(),
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                success: function(response) {
                    console.log('Categories loaded successfully:', response);
                    
                    const $select = $('#global-filter-categories');
                    $select.empty();
                    
                    if (response.data && Array.isArray(response.data)) {
                        response.data.forEach(function(category) {
                            const option = `<option value="${category.id}" data-name="${category.name}" data-slug="${category.slug}">
                                ${category.name} (${category.active_blocked_domains_count || 0} domains)
                            </option>`;
                            $select.append(option);
                        });
                        
                        // Initialize Select2 if not already initialized
                        if (!$select.hasClass('select2-hidden-accessible')) {
                            $select.select2({
                                placeholder: 'Select categories to block',
                                allowClear: true,
                                width: '100%'
                            });
                        }
                        
                        // Load existing location settings for web filtering
                        loadLocationWebFilterSettings();
                    } else {
                        console.warn('No categories found in response');
                        $select.append('<option value="">No categories available</option>');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Failed to load categories:', error);
                    handleApiError(xhr, status, error, 'loading web filter categories');
                    
                    // Add fallback categories if API fails
                    const $select = $('#global-filter-categories');
                    $select.html(`
                        <option value="">Failed to load categories</option>
                        <option value="fallback-adult">Adult Content (Fallback)</option>
                        <option value="fallback-malware">Malware & Phishing (Fallback)</option>
                    `);
                }
            });
        }

        // Function to load location web filter settings
        function loadLocationWebFilterSettings() {
            const locationId = getLocationId();
            if (!locationId) {
                console.log('No location ID found - cannot load web filter settings');
                return;
            }

            console.log('Loading web filter settings for location:', locationId);
            
            $.ajax({
                url: '/api/locations/' + locationId + '/settings',
                method: 'GET',
                headers: {
                    'Authorization': 'Bearer ' + UserManager.getToken(),
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                success: function(response) {
                    console.log('Location settings loaded:', response);
                    
                    if (response.data && response.data.settings) {
                        const settings = response.data.settings;
                        
                        // Set web filter enabled status
                        if (settings.web_filter_enabled !== undefined) {
                            $('#global-web-filter').prop('checked', settings.web_filter_enabled);
                        }
                        
                        // Set selected categories
                        if (settings.web_filter_categories && Array.isArray(settings.web_filter_categories)) {
                            $('#global-filter-categories').val(settings.web_filter_categories).trigger('change');
                        }
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Failed to load location settings:', error);
                    // Don't show error to user for settings loading failure
                }
            });
        }

        // Function to save web filter settings
        function saveWebFilterSettings() {
            const locationId = getLocationId();
            if (!locationId) {
                toastr.error('Location ID not found');
                return;
            }

            const webFilterEnabled = $('#global-web-filter').is(':checked');
            const selectedCategories = $('#global-filter-categories').val() || [];

            console.log('Saving web filter settings:', {
                web_filter_enabled: webFilterEnabled,
                web_filter_categories: selectedCategories
            });

            $.ajax({
                url: '/api/locations/' + locationId + '/settings',
                method: 'PUT',
                headers: {
                    'Authorization': 'Bearer ' + UserManager.getToken(),
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                data: JSON.stringify({
                    web_filter_enabled: webFilterEnabled,
                    web_filter_categories: selectedCategories
                }),
                success: function(response) {
                    console.log('Web filter settings saved successfully:', response);
                    toastr.success('Web content filtering settings saved successfully!');
                },
                error: function(xhr, status, error) {
                    handleApiError(xhr, status, error, 'saving web filter settings');
                }
            });
        }

        // ==============================================
        // INTERACTIVE SCHEDULE FUNCTIONALITY
        // ==============================================
        
        // InteractiveScheduler class
        class InteractiveScheduler {
            constructor(options = {}) {
                this.options = {
                    container: "#schedule-container",
                    mode: "slot", // "slot" for original behavior, "hourly" for hour-by-hour
                    onSave: null,
                    onSlotCreate: null,
                    onSlotUpdate: null,
                    onSlotDelete: null,
                    onHourToggle: null,
                    initialData: [],
                    businessHours: { start: 9, end: 17 },
                    businessDays: [
                        "monday",
                        "tuesday",
                        "wednesday",
                        "thursday",
                        "friday",
                    ],
                    ...options,
                };

                this.slots = [];
                this.slotIdCounter = 0;
                this.cellWidth = 0;
                this.container = null;
                this.hourlyData = {}; // Store hourly enable/disable data

                this.init();
            }

            init() {
                this.container = document.querySelector(this.options.container);
                if (!this.container) {
                    throw new Error(`Container ${this.options.container} not found`);
                }

                this.render();
                this.calculateCellWidth();
                this.setupEventListeners();
                
                if (this.options.mode !== "hourly") {
                this.setupInteractions();
                }

                if (this.options.initialData.length > 0) {
                    this.loadData(this.options.initialData);
                }
                
                // Apply hourly mode styles if needed
                if (this.options.mode === "hourly") {
                    this.setupHourlyMode();
                }

                window.addEventListener("resize", () => this.calculateCellWidth());
            }

            render() {
                const days = [
                    "monday",
                    "tuesday",
                    "wednesday",
                    "thursday",
                    "friday",
                    "saturday",
                    "sunday",
                ];
                const hours = Array.from({ length: 24 }, (_, i) => i);

                this.container.innerHTML = `
                    <div class="schedule-container">
                        <div class="schedule-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h5 class="mb-1">Working Hours</h5>
                                    <p class="mb-0 text-muted">Captive Portal Access Schedule</p>
                                </div>
                                <div class="quick-actions">
                                    <small class="text-muted me-2">Quick Set:</small>
                                    <button class="btn btn-outline-primary btn-sm" data-action="business-hours">Business Hours</button>
                                    <button class="btn btn-outline-secondary btn-sm" data-action="clear-all">Clear All</button>
                                </div>
                            </div>
                        </div>
                        
                        <div class="position-relative d-flex flex-column">
                            <div class="schedule-wrapper d-flex">
                                <div class="schedule-grid flex-1" id="schedule-grid">
                                <!-- Time headers -->
                                <div class="time-header">
                                    <div class="time-label"></div>
                                    ${hours
                                        .map(
                                            (hour) =>
                                                `<div class="time-label">${hour}</div>`
                                        )
                                        .join("")}
                                </div>
                                
                                <!-- Days -->
                                ${days
                                    .map(
                                        (day) => `
                                    <div class="day-row" data-day="${day}">
                                        <div class="day-label">${this.capitalize(
                                            day
                                        )}</div>
                                        ${hours
                                            .map(
                                                (hour) =>
                                                    `<div class="time-cell" data-hour="${hour}"></div>`
                                            )
                                            .join("")}
                                    </div>
                                `
                                    )
                                    .join("")}
                                </div>
                            </div>
                        </div>
                        
                        <div class="p-3 border-top bg-light">
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">
                                    <i class="bi bi-info-circle me-1"></i>
                                    Click empty cells to create slots. Drag to move, resize with handles, hover for delete.
                                </small>
                                <button class="btn btn-success btn-sm" data-action="save">
                                    <i class="bi bi-check-lg me-1"></i> Save Schedule
                                </button>
                            </div>
                        </div>
                    </div>
                `;
            }

            calculateCellWidth() {
                // Use dynamic width based on screen size
                if (window.innerWidth > 1600) {
                    // On large screens, calculate actual cell width
                    const firstCell = this.container.querySelector(".time-cell");
                    if (firstCell) {
                        this.cellWidth = firstCell.offsetWidth;
                    }
                } else {
                    // On smaller screens, use fixed width for horizontal scrolling
                    this.cellWidth = 60;
                }
            }

            setupEventListeners() {
                this.container.addEventListener("click", (e) => {
                    const action = e.target.dataset.action;

                    if (action === "business-hours") {
                        this.setBusinessHours();
                    } else if (action === "clear-all") {
                        this.clearAll();
                    } else if (action === "save") {
                        this.saveSchedule();
                    } else if (e.target.classList.contains("time-cell")) {
                        if (!e.target.querySelector(".time-slot")) {
                            // Create slots in both modes (visual slots represent hourly data)
                        this.createSlot(e.target);
                        }
                    } else if (e.target.classList.contains("delete-btn")) {
                        const slotId = e.target.closest(".time-slot").dataset.slotId;
                        this.deleteSlot(slotId);
                    }
                });
            }

            createSlot(cell) {
                const dayRow = cell.closest(".day-row");
                const day = dayRow.dataset.day;
                const hour = parseInt(cell.dataset.hour);

                if (this.hasOverlap(day, hour, hour + 1)) {
                    this.showMessage(
                        "Cannot create slot: overlaps with existing slot",
                        "error"
                    );
                    return;
                }

                const slotId = `slot-${this.slotIdCounter++}`;
                const slot = {
                    id: slotId,
                    day: day,
                    startHour: hour,
                    endHour: hour + 1,
                };

                this.slots.push(slot);
                this.renderSlot(slot);

                if (this.options.onSlotCreate) {
                    this.options.onSlotCreate(slot);
                }
            }

            renderSlot(slot) {
                const dayRow = this.container.querySelector(`[data-day="${slot.day}"]`);
                if (!dayRow) return;
                
                const startCell = dayRow.querySelector(`[data-hour="${slot.startHour}"]`);
                if (!startCell) return;

                const slotElement = document.createElement("div");
                slotElement.className = "time-slot";
                slotElement.dataset.slotId = slot.id;
                slotElement.innerHTML = `
                    <div class="resize-handle left"></div>
                    <span>${slot.startHour}h - ${slot.endHour}h</span>
                    <div class="resize-handle right"></div>
                    <div class="delete-btn">&times;</div>
                `;

                const width = (slot.endHour - slot.startHour) * this.cellWidth - 4; // -4px for padding
                slotElement.style.width = `${width}px`;
                slotElement.style.transform = "translate(0px, 0px)"; // Reset transform
                slotElement.setAttribute("data-x", 0);
                slotElement.setAttribute("data-y", 0);

                this.adjustSlotFontSize(slotElement, width);

                startCell.appendChild(slotElement);
                this.setupSlotInteractions(slotElement);
            }

            setupSlotInteractions(slotElement) {
                interact(slotElement)
                    .draggable({
                        listeners: {
                            start: (event) => {
                                event.target.classList.add("dragging");
                            },
                            move: (event) => {
                                this.handleSlotMove(event);
                            },
                            end: (event) => {
                                event.target.classList.remove("dragging");
                                this.handleSlotMoveEnd(event);
                            },
                        },
                    })
                    .resizable({
                        edges: {
                            left: ".resize-handle.left",
                            right: ".resize-handle.right",
                        },
                        listeners: {
                            move: (event) => {
                                this.handleSlotResize(event);
                            },
                            end: (event) => {
                                this.handleSlotResizeEnd(event);
                            },
                        },
                    });
            }

            handleSlotMove(event) {
                const target = event.target;
                const x = (parseFloat(target.getAttribute("data-x")) || 0) + event.dx;
                const y = (parseFloat(target.getAttribute("data-y")) || 0) + event.dy;

                target.style.transform = `translate(${x}px, ${y}px)`;
                target.setAttribute("data-x", x);
                target.setAttribute("data-y", y);

                this.updateDropZones(event.clientX, event.clientY);
            }

            handleSlotMoveEnd(event) {
                const target = event.target;
                const slotId = target.dataset.slotId;
                const slot = this.slots.find((s) => s.id === slotId);

                if (!slot) return;

                const rect = target.getBoundingClientRect();
                const gridRect = this.container
                    .querySelector("#schedule-grid")
                    .getBoundingClientRect();

                const newDay = this.getDayFromY(
                    rect.top + rect.height / 2 - gridRect.top
                );
                const newHour = this.getHourFromX(rect.left - gridRect.left - 120); // 120px for day labels, use left edge not center

                if (newDay && newHour !== null) {
                    const duration = slot.endHour - slot.startHour;
                    const newEndHour = newHour + duration;

                    if (
                        newEndHour <= 24 &&
                        !this.hasOverlap(newDay, newHour, newEndHour, slotId)
                    ) {
                        const oldSlot = { ...slot };
                        slot.day = newDay;
                        slot.startHour = newHour;
                        slot.endHour = newEndHour;

                        target.remove();
                        this.renderSlot(slot);

                        if (this.options.onSlotUpdate) {
                            this.options.onSlotUpdate(slot, oldSlot);
                        }
                    } else {
                        this.resetSlotPosition(target);
                        this.showMessage(
                            "Invalid position: slot would overlap or exceed bounds",
                            "error"
                        );
                    }
                } else {
                    this.resetSlotPosition(target);
                }

                this.clearDropZones();
            }

            handleSlotResize(event) {
                const target = event.target;
                let { x, y } = target.dataset;

                x = (parseFloat(x) || 0) + event.deltaRect.left;
                y = (parseFloat(y) || 0) + event.deltaRect.top;

                target.style.width = event.rect.width + "px";
                target.style.height = event.rect.height + "px";
                target.style.transform = `translate(${x}px, ${y}px)`;

                target.setAttribute("data-x", x);
                target.setAttribute("data-y", y);
            }

            handleSlotResizeEnd(event) {
                const target = event.target;
                const slotId = target.dataset.slotId;
                const slot = this.slots.find((s) => s.id === slotId);

                if (!slot) return;

                const newWidth = event.rect.width;
                const newDuration = Math.round(newWidth / this.cellWidth);

                let newStartHour, newEndHour;

                if (event.edges && event.edges.left) {
                    // Left edge was moved - adjust start hour
                    newEndHour = slot.endHour;
                    newStartHour = newEndHour - newDuration;
                } else {
                    // Right edge was moved - adjust end hour
                    newStartHour = slot.startHour;
                    newEndHour = newStartHour + newDuration;
                }

                // Check bounds and overlaps
                if (
                    newStartHour >= 0 &&
                    newEndHour <= 24 &&
                    newEndHour > newStartHour &&
                    !this.hasOverlap(slot.day, newStartHour, newEndHour, slotId)
                ) {
                    const oldSlot = { ...slot };
                    slot.startHour = newStartHour;
                    slot.endHour = newEndHour;

                    // Re-render slot with new size
                    target.remove();
                    this.renderSlot(slot);

                    if (this.options.onSlotUpdate) {
                        this.options.onSlotUpdate(slot, oldSlot);
                    }
                } else {
                    // Invalid resize, reset
                    target.remove();
                    this.renderSlot(slot);
                    this.showMessage(
                        "Invalid resize: would overlap or exceed bounds",
                        "error"
                    );
                }
            }

            resetSlotPosition(target) {
                target.style.transform = "";
                target.removeAttribute("data-x");
                target.removeAttribute("data-y");
            }

            getDayFromY(y) {
                const dayRows = this.container.querySelectorAll(".day-row");
                for (let row of dayRows) {
                    const rect = row
                        .querySelector(".day-label")
                        .getBoundingClientRect();
                    const gridRect = this.container
                        .querySelector("#schedule-grid")
                        .getBoundingClientRect();
                    const relativeTop = rect.top - gridRect.top;
                    const relativeBottom = rect.bottom - gridRect.top;

                    if (y >= relativeTop && y <= relativeBottom) {
                        return row.dataset.day;
                    }
                }
                return null;
            }

            getHourFromX(x) {
                const hour = Math.floor(x / this.cellWidth);
                return hour >= 0 && hour < 24 ? hour : null;
            }

            updateDropZones(clientX, clientY) {
                this.clearDropZones();

                const element = document.elementFromPoint(clientX, clientY);
                if (element && element.classList.contains("time-cell")) {
                    element.classList.add("drop-zone");
                }
            }

            clearDropZones() {
                this.container
                    .querySelectorAll(".drop-zone, .invalid-drop")
                    .forEach((el) => {
                        el.classList.remove("drop-zone", "invalid-drop");
                    });
            }

            hasOverlap(day, startHour, endHour, excludeSlotId = null) {
                return this.slots.some((slot) => {
                    if (slot.id === excludeSlotId) return false;
                    if (slot.day !== day) return false;

                    return !(endHour <= slot.startHour || startHour >= slot.endHour);
                });
            }

            deleteSlot(slotId) {
                const slotIndex = this.slots.findIndex((slot) => slot.id === slotId);
                if (slotIndex === -1) return;

                const slot = this.slots[slotIndex];
                this.slots.splice(slotIndex, 1);

                const slotElement = this.container.querySelector(
                    `[data-slot-id="${slotId}"]`
                );
                if (slotElement) {
                    slotElement.remove();
                }

                if (this.options.onSlotDelete) {
                    this.options.onSlotDelete(slot);
                }
            }

            setBusinessHours() {
                if (this.options.mode === "hourly") {
                    this.setBusinessHoursHourly();
                    this.showMessage("Business hours applied", "success");
                } else {
                this.clearAll();

                this.options.businessDays.forEach((day) => {
                    const slot = {
                        id: `slot-${this.slotIdCounter++}`,
                        day: day,
                        startHour: this.options.businessHours.start,
                        endHour: this.options.businessHours.end,
                    };
                    this.slots.push(slot);
                    this.renderSlot(slot);
                });

                this.showMessage("Business hours applied", "success");
                }
            }

            clearAll() {
                // Clear all slots (works for both modes)
                this.slots = [];
                this.container
                    .querySelectorAll(".time-slot")
                    .forEach((slot) => slot.remove());
                
                if (this.options.mode === "hourly") {
                    // In hourly mode, also clear hourly data
                    this.hourlyData = {};
                    const days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
                    days.forEach(day => {
                        this.hourlyData[day] = {};
                        for (let hour = 0; hour < 24; hour++) {
                            this.hourlyData[day][hour] = false;
                        }
                    });
                    this.showMessage("All hours disabled", "info");
                } else {
                this.showMessage("All slots cleared", "info");
                }
            }

            saveSchedule() {
                let scheduleData;
                
                if (this.options.mode === "hourly") {
                    scheduleData = this.getHourlyData();
                } else {
                    scheduleData = this.getScheduleData();
                }

                if (this.options.onSave) {
                    this.options.onSave(scheduleData);
                } else {
                    console.log("Schedule data:", scheduleData);
                    this.showMessage(
                        "Schedule saved! Check console for data.",
                        "success"
                    );
                }
            }

            getScheduleData() {
                return this.slots.map((slot) => ({
                    day: slot.day,
                    startHour: slot.startHour,
                    endHour: slot.endHour,
                    startTime: `${slot.startHour.toString().padStart(2, "0")}:00`,
                    endTime: `${slot.endHour.toString().padStart(2, "0")}:00`,
                }));
            }

            loadData(data) {
                this.clearAll();

                data.forEach((item) => {
                    const slot = {
                        id: `slot-${this.slotIdCounter++}`,
                        day: item.day,
                        startHour: item.startHour || this.parseTime(item.startTime),
                        endHour: item.endHour || this.parseTime(item.endTime),
                    };

                    if (!this.hasOverlap(slot.day, slot.startHour, slot.endHour)) {
                        this.slots.push(slot);
                        this.renderSlot(slot);
                    }
                });
            }

            parseTime(timeString) {
                if (typeof timeString === "number") return timeString;
                const [hours] = timeString.split(":");
                return parseInt(hours, 10);
            }

            setupInteractions() {
                // Setup interact.js for the grid
                interact(this.container.querySelector(".schedule-grid")).dropzone({
                    accept: ".time-slot",
                    ondrop: (event) => {
                        // Handle drop events
                    },
                });
            }

            showMessage(message, type = "info") {
                console.log(`[${type.toUpperCase()}] ${message}`);

                if (type === "error") {
                    toastr.error(message);
                } else if (type === "success") {
                    toastr.success(message);
                } else if (type === "info") {
                    toastr.info(message);
                }
            }

            // ===== HOURLY MODE METHODS =====
            
            toggleHour(cell) {
                const dayRow = cell.closest(".day-row");
                const day = dayRow.dataset.day;
                const hour = parseInt(cell.dataset.hour);

                if (!this.hourlyData[day]) {
                    this.hourlyData[day] = {};
                }

                // Toggle the hour state
                const currentlyEnabled = this.hourlyData[day][hour] || false;
                this.hourlyData[day][hour] = !currentlyEnabled;

                // Update visual state
                this.updateHourVisual(cell, !currentlyEnabled);

                // Call callback if provided
                if (this.options.onHourToggle) {
                    this.options.onHourToggle(day, hour, !currentlyEnabled);
                }
            }

            updateHourVisual(cell, enabled) {
                if (enabled) {
                    cell.classList.add('hour-enabled');
                    cell.classList.remove('hour-disabled');
                    cell.style.background = 'linear-gradient(135deg, #10b981 0%, #059669 100%)';
                    cell.style.color = 'white';
                    cell.style.fontWeight = '600';
                    cell.innerHTML = '<span style="font-size: 0.75rem;">✓</span>';
                    cell.title = 'Enabled - Click to disable';
                } else {
                    cell.classList.add('hour-disabled');
                    cell.classList.remove('hour-enabled');
                    cell.style.background = '#f8fafc';
                    cell.style.color = '#64748b';
                    cell.style.fontWeight = 'normal';
                    cell.innerHTML = '';
                    cell.title = 'Disabled - Click to enable';
                }
            }

            loadHourlyData(data) {
                // Clear existing data
                this.hourlyData = {};
                
                // Load the hourly data
                Object.keys(data).forEach(day => {
                    this.hourlyData[day] = {};
                    
                    if (Array.isArray(data[day])) {
                        // Handle array format from API
                        data[day].forEach(hourData => {
                            this.hourlyData[day][hourData.hour] = hourData.enabled;
                        });
                    } else {
                        // Handle object format
                        Object.keys(data[day]).forEach(hour => {
                            this.hourlyData[day][hour] = data[day][hour].enabled;
                        });
                    }
                });

                // Convert hourly data to time slots for visual display
                this.convertHourlyDataToSlots();
            }

            convertHourlyDataToSlots() {
                // Clear existing slots (but preserve hourly data)
                this.slots = [];
                this.container
                    .querySelectorAll(".time-slot")
                    .forEach((slot) => slot.remove());
                
                // Convert hourly data to continuous time slots
                const days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
                
                days.forEach(day => {
                    if (!this.hourlyData[day]) return;
                    
                    let slotStart = null;
                    
                    for (let hour = 0; hour < 24; hour++) {
                        const isEnabled = this.hourlyData[day][hour];
                        
                        if (isEnabled && slotStart === null) {
                            // Start of a new slot
                            slotStart = hour;
                        } else if (!isEnabled && slotStart !== null) {
                            // End of current slot
                            this.createSlotFromHours(day, slotStart, hour);
                            slotStart = null;
                        }
                    }
                    
                    // Handle slot that goes to end of day
                    if (slotStart !== null) {
                        this.createSlotFromHours(day, slotStart, 24);
                    }
                });
            }

            createSlotFromHours(day, startHour, endHour) {
                const slot = {
                    id: `slot-${this.slotIdCounter++}`,
                    day: day,
                    startHour: startHour,
                    endHour: endHour,
                };
                
                this.slots.push(slot);
                this.renderSlot(slot);
            }

            renderHourlyVisuals() {
                // This method is now replaced by convertHourlyDataToSlots
                // but kept for backward compatibility
                console.log('renderHourlyVisuals called - using slot-based display instead');
            }

            getHourlyData() {
                // Convert slots back to hourly format for API
                const result = [];
                const days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
                
                // Initialize all hours as disabled
                const hourlyData = {};
                days.forEach(day => {
                    hourlyData[day] = {};
                    for (let hour = 0; hour < 24; hour++) {
                        hourlyData[day][hour] = false;
                    }
                });
                
                // Mark hours as enabled based on slots
                this.slots.forEach(slot => {
                    for (let hour = slot.startHour; hour < slot.endHour; hour++) {
                        hourlyData[slot.day][hour] = true;
                    }
                });
                
                // Convert to API format
                days.forEach(day => {
                    for (let hour = 0; hour < 24; hour++) {
                        result.push({
                            day_of_week: day,
                            hour: hour,
                            enabled: hourlyData[day][hour]
                        });
                    }
                });
                
                return result;
            }

            setBusinessHoursHourly() {
                // In hourly mode, set business hours (9-17) for business days
                const businessDays = this.options.businessDays;
                const businessStart = this.options.businessHours.start;
                const businessEnd = this.options.businessHours.end;

                this.clearAll();

                businessDays.forEach(day => {
                    if (!this.hourlyData[day]) {
                        this.hourlyData[day] = {};
                    }
                    
                    for (let hour = 0; hour < 24; hour++) {
                        this.hourlyData[day][hour] = hour >= businessStart && hour < businessEnd;
                    }
                });

                this.renderHourlyVisuals();
            }

            setupHourlyMode() {
                // Add specific styles for hourly mode
                const style = document.createElement('style');
                style.textContent = `
                    .schedule-container[data-mode="hourly"] .time-cell {
                        cursor: pointer !important;
                        transition: all 0.2s ease;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        border-radius: 4px;
                    }
                    
                    .schedule-container[data-mode="hourly"] .time-cell:hover {
                        transform: scale(1.05);
                        box-shadow: 0 2px 8px rgba(0,0,0,0.15);
                        z-index: 5;
                    }
                    
                    .schedule-container[data-mode="hourly"] .time-cell.hour-enabled {
                        background: linear-gradient(135deg, #10b981 0%, #059669 100%) !important;
                        color: white !important;
                        box-shadow: 0 2px 4px rgba(16, 185, 129, 0.3);
                    }
                    
                    .schedule-container[data-mode="hourly"] .time-cell.hour-disabled {
                        background: #f8fafc !important;
                        color: #64748b !important;
                    }
                `;
                document.head.appendChild(style);
                
                // Mark container with hourly mode
                const container = this.container.querySelector('.schedule-container');
                if (container) {
                    container.setAttribute('data-mode', 'hourly');
                }
            }

            capitalize(str) {
                return str.charAt(0).toUpperCase() + str.slice(1);
            }

            // Public API methods
            destroy() {
                if (this.container) {
                    this.container.innerHTML = "";
                }
            }

            refresh() {
                this.calculateCellWidth();
                // Re-render all slots
                const currentSlots = [...this.slots];
                this.container
                    .querySelectorAll(".time-slot")
                    .forEach((slot) => slot.remove());
                currentSlots.forEach((slot) => this.renderSlot(slot));
            }

            adjustSlotFontSize(slotElement, width) {
                let fontSize;
                if (width < 60) {
                    fontSize = "0.5rem";
                } else if (width < 120) {
                    fontSize = "0.65rem";
                } else if (width < 180) {
                    fontSize = "0.75rem";
                } else {
                    fontSize = "0.875rem";
                }

                const textSpan = slotElement.querySelector("span");
                if (textSpan) {
                    textSpan.style.fontSize = fontSize;
                }
            }
        }
        
        // Global variable to store the scheduler instance
        let workingHoursScheduler = null;

        // Initialize working hours functionality
        $(document).ready(function() {
            // Initialize the enhanced interactive scheduler with hourly granularity
            workingHoursScheduler = new InteractiveScheduler({
                container: "#schedule-container",
                mode: "hourly", // Enable hourly mode
                onSave: saveWorkingHoursFromHourlyData
            });
            
            // Add a small delay to ensure DOM is fully ready
            setTimeout(function() {
                loadWorkingHoursAsHourlyData();
            }, 500);
        });
        
        // Load working hours as hourly data from new API
        function loadWorkingHoursAsHourlyData() {
            const locationId = getLocationId();
            if (!locationId) {
                console.error('Location ID not found for working hours');
                return;
            }

            const token = UserManager.getToken();
            if (!token) {
                console.error('No authentication token found');
                return;
            }

            $.ajax({
                url: `/api/locations/${locationId}/captive-portal/hourly-schedule`,
                method: 'GET',
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                success: function(response) {
                    if (response.status === 'success' && workingHoursScheduler) {
                        workingHoursScheduler.loadHourlyData(response.data);
                    }
                },
                error: function(xhr, status, error) {
                    if (xhr.status === 404) {
                        // Initialize with default all-enabled schedule
                        initializeDefaultWorkingHours();
                    } else {
                        console.error('API Error:', xhr.responseText);
                        toastr.error('Failed to load working hours');
                    }
                }
            });
        }

        // Initialize default working hours (all enabled)
        function initializeDefaultWorkingHours() {
            const days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
            const defaultData = {};
            
            days.forEach(day => {
                defaultData[day] = [];
                for (let hour = 0; hour < 24; hour++) {
                    defaultData[day].push({
                        hour: hour,
                        enabled: true,
                        time_label: String(hour).padStart(2, '0') + ':00'
                    });
                }
            });
            
            if (workingHoursScheduler) {
                workingHoursScheduler.loadHourlyData(defaultData);
            }
        }

        // Save working hours from hourly data
        function saveWorkingHoursFromHourlyData(hourlyData) {
            const locationId = getLocationId();
            if (!locationId) {
                toastr.error('Location ID not found');
                return;
            }

            const token = UserManager.getToken();
            if (!token) {
                toastr.error('Authentication required');
                return;
            }

            console.log('Saving hourly working hours:', hourlyData);

            const saveBtn = document.querySelector('[data-action="save"]');
            if (saveBtn) {
                saveBtn.disabled = true;
                saveBtn.innerHTML = '<i class="bi bi-loader me-1 spinner-border spinner-border-sm"></i> Saving...';
            }

            $.ajax({
                url: `/api/locations/${locationId}/captive-portal/hourly-schedule`,
                method: 'POST',
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                data: JSON.stringify({
                    schedule: hourlyData
                }),
                success: function(response) {
                    console.log('Hourly working hours saved:', response);
                    toastr.success('Working hours saved successfully!');
                },
                error: function(xhr, status, error) {
                    console.error('Error saving hourly working hours:', error);
                    toastr.error('Failed to save working hours: ' + (xhr.responseJSON?.message || error));
                },
                complete: function() {
                    // Reset button state
                    if (saveBtn) {
                        saveBtn.disabled = false;
                        saveBtn.innerHTML = '<i class="bi bi-check-lg me-1"></i> Save Schedule';
                    }
                }
            });
        }

        // Load working hours from API (original function - keep for backward compatibility)
        function loadWorkingHours() {
            const locationId = getLocationId();
            if (!locationId) {
                console.error('No location ID found');
                return;
            }
            
            $.ajax({
                url: `/api/locations/${locationId}/captive-portal/working-hours`,
                method: 'GET',
                headers: {
                    'Authorization': 'Bearer ' + UserManager.getToken(),
                    'Accept': 'application/json'
                },
                success: function(response) {
                    console.log('Working hours loaded:', response);
                    if (response.status === 'success' && response.data) {
                        populateWorkingHours(response.data);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error loading working hours:', error);
                    // Don't show error toast for 404 (no working hours set yet)
                    if (xhr.status !== 404) {
                        toastr.error('Failed to load working hours settings');
                    }
                }
            });
        }
        
        // Populate working hours form with data
        function populateWorkingHours(workingHoursData) {
            console.log('Populating working hours:', workingHoursData);
            
            // Convert API data to scheduler format
            const scheduleData = workingHoursData
                .filter(dayData => dayData.enabled && dayData.start_time && dayData.end_time)
                .map(dayData => ({
                    day: dayData.day_of_week,
                    startTime: dayData.start_time,
                    endTime: dayData.end_time,
                    startHour: parseInt(dayData.start_time.split(':')[0]),
                    endHour: parseInt(dayData.end_time.split(':')[0])
                }));

            // Load data into scheduler
            if (workingHoursScheduler) {
                workingHoursScheduler.loadData(scheduleData);
            }
        }
        
        // Save working hours to API
        function saveWorkingHours(scheduleData) {
            const locationId = getLocationId();
            if (!locationId) {
                toastr.error('Location ID not found');
                return;
            }
            
            // Convert scheduler data to API format
            const workingHours = [];
            const days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
            
            days.forEach(day => {
                const daySlots = scheduleData.filter(slot => slot.day === day);
                const enabled = daySlots.length > 0;
                
                if (enabled) {
                    // For now, take the first slot for each day (can be extended for multiple slots per day)
                    const slot = daySlots[0];
                    workingHours.push({
                        day_of_week: day,
                        start_time: slot.startTime,
                        end_time: slot.endTime,
                        enabled: true
                    });
                } else {
                    workingHours.push({
                        day_of_week: day,
                        start_time: null,
                        end_time: null,
                        enabled: false
                    });
                }
            });
            
            // Show loading state
            const saveBtn = document.querySelector('[data-action="save"]');
            if (saveBtn) {
                saveBtn.disabled = true;
                saveBtn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i> Saving...';
            }
            
            $.ajax({
                url: `/api/locations/${locationId}/captive-portal/working-hours`,
                method: 'POST',
                headers: {
                    'Authorization': 'Bearer ' + UserManager.getToken(),
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                data: JSON.stringify({
                    working_hours: workingHours
                }),
                success: function(response) {
                    console.log('Working hours saved:', response);
                    toastr.success('Working hours saved successfully!');
                },
                error: function(xhr, status, error) {
                    console.error('Error saving working hours:', error);
                    handleApiError(xhr, status, error, 'saving working hours');
                },
                complete: function() {
                    // Reset button state
                    if (saveBtn) {
                        saveBtn.disabled = false;
                        saveBtn.innerHTML = '<i class="bi bi-check-lg me-1"></i> Save Schedule';
                    }
                }
            });
        }

        // ========================================
        // HOURLY SCHEDULE FUNCTIONALITY (COMMENTED OUT - INTEGRATED INTO WORKING HOURS)
        // ========================================
        
        /*
        let hourlyScheduleData = {};
        
        // Initialize hourly schedule functionality
        $(document).ready(function() {
            // Bind hourly schedule events
            $('#load-hourly-schedule').on('click', loadHourlySchedule);
            $('#save-hourly-schedule').on('click', saveHourlySchedule);
            $('#initialize-from-working-hours').on('click', initializeFromWorkingHours);
            
            // Load hourly schedule when captive portal tab is shown
            $('a[data-toggle="tab"][href="#captive-portal"]').on('shown.bs.tab', function() {
                // Small delay to ensure DOM is ready
                setTimeout(loadHourlySchedule, 500);
            });
            
            // Also load if captive portal tab is already active on page load
            if ($('#captive-portal').hasClass('active') || $('#captive-portal').hasClass('show')) {
                setTimeout(loadHourlySchedule, 1000);
            }
        });
        */

        /*
        // Load hourly schedule from API (COMMENTED OUT - FUNCTIONALITY MOVED TO WORKING HOURS)
        function loadHourlySchedule() {
            try {
                const locationId = getLocationId();
                if (!locationId) {
                    console.error('Location ID not found for hourly schedule');
                    toastr.error('Location ID not found');
                    return;
                }

                const token = UserManager.getToken();
                if (!token) {
                    console.error('No authentication token found for hourly schedule');
                    toastr.error('Authentication required');
                    return;
                }

                console.log('Loading hourly schedule for location:', locationId);

            $.ajax({
                url: `/api/locations/${locationId}/captive-portal/hourly-schedule`,
                method: 'GET',
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                success: function(response) {
                    console.log('Hourly schedule loaded successfully:', response);
                    if (response.status === 'success') {
                        hourlyScheduleData = response.data;
                        renderHourlySchedule(hourlyScheduleData);
                        console.log('Hourly schedule rendered successfully');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error loading hourly schedule:', xhr.status, error);
                    if (xhr.status === 404) {
                        console.log('No hourly schedule found, initializing with default data');
                        initializeDefaultSchedule();
                    } else {
                        console.error('API Error:', xhr.responseText);
                        toastr.error('Failed to load hourly schedule');
                    }
                }
            });
            } catch (error) {
                console.error('Error in loadHourlySchedule:', error);
                toastr.error('Failed to load hourly schedule: ' + error.message);
            }
        }

        // Initialize default schedule (all enabled)
        function initializeDefaultSchedule() {
            const days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
            hourlyScheduleData = {};
            
            days.forEach(day => {
                hourlyScheduleData[day] = {};
                for (let hour = 0; hour < 24; hour++) {
                    hourlyScheduleData[day][hour] = {
                        hour: hour,
                        enabled: true,
                        time_label: String(hour).padStart(2, '0') + ':00'
                    };
                }
            });
            
            renderHourlySchedule(hourlyScheduleData);
        }

        // Render hourly schedule grid
        function renderHourlySchedule(scheduleData) {
            try {
                console.log('Rendering hourly schedule with data:', scheduleData);
                
                const tbody = $('#hourly-schedule-body');
                console.log('Found tbody element:', tbody.length);
                
                if (tbody.length === 0) {
                    console.error('Hourly schedule tbody not found!');
                    toastr.error('Hourly schedule table not found on page');
                    return;
                }
                
                tbody.empty();
                
                // Add loading indicator
                tbody.append('<tr><td colspan="26" class="text-center">Loading hourly schedule...</td></tr>');

            const days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
            const dayNames = {
                'monday': 'Monday',
                'tuesday': 'Tuesday', 
                'wednesday': 'Wednesday',
                'thursday': 'Thursday',
                'friday': 'Friday',
                'saturday': 'Saturday',
                'sunday': 'Sunday'
            };

            const currentDay = new Date().toLocaleDateString('en-US', { weekday: 'long' }).toLowerCase();
            const currentHour = new Date().getHours();

            days.forEach(day => {
                const row = $('<tr></tr>');
                
                // Day label
                row.append(`<td class="day-label">${dayNames[day]}</td>`);
                
                // Hour cells
                for (let hour = 0; hour < 24; hour++) {
                    let hourData = { hour: hour, enabled: true }; // default
                    
                    if (scheduleData[day]) {
                        // Handle both array and object formats
                        if (Array.isArray(scheduleData[day])) {
                            // API returns array format: [{"hour":0,"enabled":false}, ...]
                            const hourEntry = scheduleData[day].find(entry => entry.hour === hour);
                            if (hourEntry) {
                                hourData = hourEntry;
                            }
                        } else {
                            // Object format: {0:{"hour":0,"enabled":false}, ...}
                            hourData = scheduleData[day][hour] || hourData;
                        }
                    }
                    
                    const isCurrentHour = day === currentDay && hour === currentHour;
                    const cellClass = `hour-cell ${hourData.enabled ? 'enabled' : 'disabled'} ${isCurrentHour ? 'current-hour' : ''}`;
                    
                    const cell = $(`<td class="${cellClass}" data-day="${day}" data-hour="${hour}" title="${dayNames[day]} ${hour}:00 - ${hourData.enabled ? 'Enabled' : 'Disabled'}"></td>`);
                    cell.text(hourData.enabled ? '✓' : '✗');
                    
                    // Add click handler for toggling
                    cell.on('click', function() {
                        toggleHour(day, hour);
                    });
                    
                    row.append(cell);
                }
                
                // Day actions
                const actionsCell = $(`<td class="day-actions">
                    <button class="btn btn-sm btn-success enable-all-btn" data-day="${day}" title="Enable All">All On</button>
                    <button class="btn btn-sm btn-secondary disable-all-btn" data-day="${day}" title="Disable All">All Off</button>
                </td>`);
                
                row.append(actionsCell);
                tbody.append(row);
            });

            // Clear loading message and show completed grid
            tbody.find('tr:first').remove();
            
            // Bind day action buttons
            $('.enable-all-btn').on('click', function() {
                const day = $(this).data('day');
                bulkUpdateDay(day, 'enable_all');
            });

            $('.disable-all-btn').on('click', function() {
                const day = $(this).data('day');
                bulkUpdateDay(day, 'disable_all');
            });
            
            console.log('Hourly schedule grid rendered successfully with', days.length, 'days');
            } catch (error) {
                console.error('Error rendering hourly schedule:', error);
                toastr.error('Failed to render hourly schedule: ' + error.message);
                const tbody = $('#hourly-schedule-body');
                tbody.html('<tr><td colspan="26" class="text-center text-danger">Error loading schedule. Check console for details.</td></tr>');
            }
        }

        // Toggle a specific hour
        function toggleHour(day, hour) {
            if (!hourlyScheduleData[day]) {
                hourlyScheduleData[day] = {};
            }
            
            if (!hourlyScheduleData[day][hour]) {
                hourlyScheduleData[day][hour] = { hour: hour, enabled: true };
            }
            
            hourlyScheduleData[day][hour].enabled = !hourlyScheduleData[day][hour].enabled;
            
            // Update the cell visually
            const cell = $(`.hour-cell[data-day="${day}"][data-hour="${hour}"]`);
            const enabled = hourlyScheduleData[day][hour].enabled;
            
            cell.removeClass('enabled disabled');
            cell.addClass(enabled ? 'enabled' : 'disabled');
            cell.text(enabled ? '✓' : '✗');
            cell.attr('title', cell.attr('title').replace(/(Enabled|Disabled)/, enabled ? 'Enabled' : 'Disabled'));
        }

        // Bulk update all hours for a day
        function bulkUpdateDay(day, action) {
            const enabled = action === 'enable_all';
            
            if (!hourlyScheduleData[day]) {
                hourlyScheduleData[day] = {};
            }
            
            for (let hour = 0; hour < 24; hour++) {
                hourlyScheduleData[day][hour] = { hour: hour, enabled: enabled };
                
                // Update cell visually
                const cell = $(`.hour-cell[data-day="${day}"][data-hour="${hour}"]`);
                cell.removeClass('enabled disabled');
                cell.addClass(enabled ? 'enabled' : 'disabled');
                cell.text(enabled ? '✓' : '✗');
                cell.attr('title', cell.attr('title').replace(/(Enabled|Disabled)/, enabled ? 'Enabled' : 'Disabled'));
            }
        }

        // Save hourly schedule to API
        function saveHourlySchedule() {
            const locationId = getLocationId();
            if (!locationId) {
                toastr.error('Location ID not found');
                return;
            }

            // Convert hourlyScheduleData to API format
            const scheduleArray = [];
            Object.keys(hourlyScheduleData).forEach(day => {
                Object.keys(hourlyScheduleData[day]).forEach(hour => {
                    scheduleArray.push({
                        day_of_week: day,
                        hour: parseInt(hour),
                        enabled: hourlyScheduleData[day][hour].enabled
                    });
                });
            });

            const saveBtn = $('#save-hourly-schedule');
            saveBtn.prop('disabled', true);
            saveBtn.html('<i data-feather="loader" class="mr-1 spin"></i> Saving...');

            $.ajax({
                url: `/api/locations/${locationId}/captive-portal/hourly-schedule`,
                method: 'POST',
                headers: {
                    'Authorization': 'Bearer ' + UserManager.getToken(),
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                data: JSON.stringify({
                    schedule: scheduleArray
                }),
                success: function(response) {
                    console.log('Hourly schedule saved:', response);
                    toastr.success('Hourly schedule saved successfully!');
                },
                error: function(xhr, status, error) {
                    console.error('Error saving hourly schedule:', error);
                    handleApiError(xhr, status, error, 'saving hourly schedule');
                },
                complete: function() {
                    saveBtn.prop('disabled', false);
                    saveBtn.html('<i data-feather="save" class="mr-1"></i> Save Schedule');
                    feather.replace(); // Re-render feather icons
                }
            });
        }

        // Initialize hourly schedule from working hours
        function initializeFromWorkingHours() {
            const locationId = getLocationId();
            if (!locationId) {
                toastr.error('Location ID not found');
                return;
            }

            const initBtn = $('#initialize-from-working-hours');
            initBtn.prop('disabled', true);
            initBtn.html('<i data-feather="loader" class="mr-1 spin"></i> Initializing...');

            $.ajax({
                url: `/api/locations/${locationId}/captive-portal/hourly-schedule/initialize`,
                method: 'POST',
                headers: {
                    'Authorization': 'Bearer ' + UserManager.getToken(),
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                success: function(response) {
                    console.log('Hourly schedule initialized:', response);
                    toastr.success('Hourly schedule initialized from working hours!');
                    // Reload the schedule to show updated data
                    loadHourlySchedule();
                },
                error: function(xhr, status, error) {
                    console.error('Error initializing hourly schedule:', error);
                    handleApiError(xhr, status, error, 'initializing hourly schedule');
                },
                complete: function() {
                    initBtn.prop('disabled', false);
                    initBtn.html('<i data-feather="copy" class="mr-1"></i> Initialize from Working Hours');
                    feather.replace(); // Re-render feather icons
                }
            });
        }
        */

        // Function to get location ID (moved outside of document ready block for global access)
        function getLocationId() {
            // Option 1: From URL path (e.g., /locations/123/details or /location-details?id=123)
            const pathParts = window.location.pathname.split('/');
            console.log('URL path parts:', pathParts);
            
            // Check for locations/ID pattern
            const locationIndex = pathParts.indexOf('locations');
            if (locationIndex !== -1 && pathParts[locationIndex + 1]) {
                const locationId = pathParts[locationIndex + 1];
                console.log('Found location ID from path:', locationId);
                return locationId;
            }
            
            // Option 2: From URL parameters
            const urlParams = new URLSearchParams(window.location.search);
            const locationId = urlParams.get('location_id') || urlParams.get('id');
            if (locationId) {
                console.log('Found location ID from URL params:', locationId);
                return locationId;
            }
            
            // Option 3: From breadcrumb text (as fallback) - "Location 14"
            const breadcrumbText = $('.breadcrumb-item.active').text();
            console.log('Breadcrumb text:', breadcrumbText);
            const locationMatch = breadcrumbText.match(/Location (\d+)/);
            if (locationMatch) {
                const locationId = locationMatch[1];
                console.log('Found location ID from breadcrumb:', locationId);
                return locationId;
            }
            
            // Option 4: From data attribute or global variable
            if (window.currentLocationId) {
                console.log('Found location ID from global variable:', window.currentLocationId);
                return window.currentLocationId;
            }
            
            console.log('No location ID found');
            return null;
        }

        // Helper function to handle API errors consistently
        function handleApiError(xhr, status, error, context) {
            console.error(`API Error in ${context}:`, error);
            console.error('Status:', status);
            console.error('Response:', xhr.responseText);
            
            if (xhr.status === 401) {
                console.error('Unauthorized - redirecting to login');
                toastr.error('Session expired. Please log in again.');
                setTimeout(() => {
                    window.location.href = '/';
                }, 2000);
                return;
            }
            
            let errorMessage = 'An error occurred';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMessage = xhr.responseJSON.message;
            } else if (xhr.responseText) {
                try {
                    const response = JSON.parse(xhr.responseText);
                    errorMessage = response.message || response.error || errorMessage;
                } catch (e) {
                    errorMessage = error || errorMessage;
                }
            }
            
            toastr.error(`${context}: ${errorMessage}`);
        }

        // ==============================================
        // ANALYTICS FUNCTIONS
        // ==============================================
        
        // Initialize Analytics components
        function initializeAnalytics() {
            initializeDailyUsageChart();
            loadOnlineUsers();
            
            // Set up refresh button
            $('#refresh-online-users').on('click', function() {
                loadOnlineUsers();
            });
            
            // Set up chart period buttons
            $('.period-btn').on('click', function(e) {
                e.preventDefault();
                const period = $(this).data('period');
                
                // Update active state
                $('.period-btn').removeClass('active');
                $(this).addClass('active');
                
                // Load new data
                loadDailyUsageData(period);
            });
            
            // Set up pagination controls
            $('#prev-page').on('click', function() {
                if (window.usersCurrentPage > 1) {
                    window.usersCurrentPage--;
                    renderCurrentPage();
                    console.log('⬅️ Previous page clicked, now on page:', window.usersCurrentPage);
                }
            });
            
            $('#next-page').on('click', function() {
                const totalPages = Math.ceil(window.allOnlineUsers.length / window.usersPerPage);
                if (window.usersCurrentPage < totalPages) {
                    window.usersCurrentPage++;
                    renderCurrentPage();
                    console.log('➡️ Next page clicked, now on page:', window.usersCurrentPage);
                }
            });
        }
        
        // Global variables for charts
        let dailyUsageChart = null;
        
        // Global variables for pagination
        window.allOnlineUsers = [];
        window.usersCurrentPage = 1;
        window.usersPerPage = 2;
        
        // Initialize Daily Usage Chart
        function initializeDailyUsageChart() {
            const chartOptions = {
                chart: {
                    type: 'area',
                    height: 300,
                    toolbar: {
                        show: false
                    },
                    animations: {
                        enabled: true,
                        easing: 'easeinout',
                        speed: 800
                    },
                    sparkline: {
                        enabled: false
                    }
                },
                series: [{
                    name: 'Users',
                    data: []
                }],
                xaxis: {
                    categories: [],
                    labels: {
                        style: {
                            colors: '#6c757d',
                            fontSize: '12px'
                        }
                    },
                    axisBorder: {
                        show: false
                    },
                    axisTicks: {
                        show: false
                    }
                },
                yaxis: {
                    labels: {
                        style: {
                            colors: '#6c757d',
                            fontSize: '12px'
                        }
                    }
                },
                fill: {
                    type: 'gradient',
                    gradient: {
                        shade: 'light',
                        type: 'vertical',
                        shadeIntensity: 0.3,
                        gradientToColors: ['#a5b4fc'],
                        inverseColors: false,
                        opacityFrom: 0.4,
                        opacityTo: 0.05,
                        stops: [0, 100]
                    }
                },
                stroke: {
                    curve: 'smooth',
                    width: 2,
                    colors: ['#667eea']
                },
                colors: ['#667eea'],
                grid: {
                    borderColor: '#f1f3f4',
                    strokeDashArray: 5,
                    xaxis: {
                        lines: {
                            show: false
                        }
                    },
                    yaxis: {
                        lines: {
                            show: true
                        }
                    }
                },
                legend: {
                    show: false
                },
                tooltip: {
                    theme: 'light',
                    custom: function({series, seriesIndex, dataPointIndex, w}) {
                        const value = series[seriesIndex][dataPointIndex];
                        const category = w.globals.categoryLabels[dataPointIndex];
                        return `
                            <div class="custom-tooltip">
                                <div class="tooltip-title">${category}</div>
                                <div class="tooltip-value">${value} users</div>
                            </div>
                        `;
                    }
                },
                dataLabels: {
                    enabled: false
                }
            };
            
            dailyUsageChart = new ApexCharts(document.querySelector('#daily-usage-chart'), chartOptions);
            dailyUsageChart.render();
            
            // Load initial data
            loadDailyUsageData(7);
        }
        
        // Load Daily Usage Data
        function loadDailyUsageData(period = 7) {
            const locationId = getLocationId();
            if (!locationId) {
                console.error('No location ID found for daily usage data');
                return;
            }
            
            $.ajax({
                url: `/api/locations/${locationId}/captive-portal/daily-usage`,
                method: 'GET',
                data: {
                    period: period
                },
                headers: {
                    'Authorization': 'Bearer ' + UserManager.getToken(),
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                success: function(response) {
                    console.log('Daily usage data loaded:', response);
                    
                    // Process the data
                    const data = response.data || [];
                    const categories = data.map(item => item.date);
                    const series = data.map(item => item.users);
                    
                    // Update chart
                    dailyUsageChart.updateOptions({
                        xaxis: {
                            categories: categories
                        }
                    });
                    
                    dailyUsageChart.updateSeries([{
                        name: 'Users',
                        data: series
                    }]);
                    
                    // Update statistics
                    const summary = response.summary || {};
                    $('#total-users').text(summary.total_unique_users || 0);
                    $('#total-sessions').text(summary.total_sessions || 0);
                    $('#avg-daily').text(summary.average_users_per_day || 0);
                },
                error: function(xhr, status, error) {
                    console.error('Error loading daily usage data:', error);
                    
                    // Show empty chart with error message
                    dailyUsageChart.updateOptions({
                        xaxis: {
                            categories: []
                        },
                        noData: {
                            text: 'Unable to load usage data',
                            align: 'center',
                            verticalAlign: 'middle',
                            style: {
                                color: '#9aa0ac',
                                fontSize: '14px'
                            }
                        }
                    });
                    
                    dailyUsageChart.updateSeries([{
                        name: 'Users',
                        data: []
                    }]);
                    
                    // Show error message
                    toastr.error('Failed to load captive portal usage data');
                }
            });
        }
        
        // Load Online Users
        function loadOnlineUsers() {
            const locationId = getLocationId();
            if (!locationId) {
                console.error('No location ID found for online users');
                return;
            }
            
            $('#online-users-list').html(`
                <div class="loading-state">
                    <i data-feather="loader" class="loading-icon"></i>
                    <p>Loading online users...</p>
                </div>
            `);
            
            // Re-render Feather icons
            if (typeof feather !== 'undefined') {
                feather.replace();
            }
            
            $.ajax({
                url: `/api/locations/${locationId}/online-users`,
                method: 'GET',
                headers: {
                    'Authorization': 'Bearer ' + UserManager.getToken(),
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                success: function(response) {
                    console.log('Online users loaded:', response);
                    // Handle the response format from the API
                    const onlineUsers = response.data && response.data.online_users ? response.data.online_users : [];
                    displayOnlineUsers(onlineUsers);
                },
                error: function(xhr, status, error) {
                    console.error('Error loading online users:', error);
                    
                    // Show error message instead of sample data
                    $('#online-users-list').html(`
                        <div class="error-state">
                            <i data-feather="alert-triangle"></i>
                            <p>Unable to load online users</p>
                            <small>Please try refreshing</small>
                        </div>
                    `);
                    
                    // Re-render Feather icons
                    if (typeof feather !== 'undefined') {
                        feather.replace();
                    }
                    
                    // Show error toast
                    toastr.error('Failed to load online users data');
                }
            });
        }
        
                // Display Online Users
        function displayOnlineUsers(users) {
            // Store all users globally for pagination
            window.allOnlineUsers = users || [];
            
            // Calculate max pages and ensure current page is valid
            const totalPages = Math.ceil(window.allOnlineUsers.length / window.usersPerPage);
            window.usersCurrentPage = Math.min(Math.max(1, window.usersCurrentPage), totalPages || 1);
            
            // Update total users count
            $('#online-count').text(window.allOnlineUsers.length);
            
            if (window.allOnlineUsers.length === 0) {
                $('#online-users-list').html(`
                    <div class="empty-state">
                        <i data-feather="users"></i>
                        <p>No users currently online</p>
                    </div>
                `);
                $('#users-pagination').hide();
                $('#count-range').hide();
                
                // Re-render Feather icons
                if (typeof feather !== 'undefined') {
                    feather.replace();
                }
                return;
            }
            
            // Show pagination if more than usersPerPage users
            if (window.allOnlineUsers.length > window.usersPerPage) {
                $('#users-pagination').show();
                $('#count-range').show();
            } else {
                $('#users-pagination').hide();
                $('#count-range').hide();
            }
            
            // Render current page
            renderCurrentPage();
        }
        
        // Render current page of users
        function renderCurrentPage() {
            const startIndex = (window.usersCurrentPage - 1) * window.usersPerPage;
            const endIndex = startIndex + window.usersPerPage;
            const currentUsers = window.allOnlineUsers.slice(startIndex, endIndex);
            const totalPages = Math.ceil(window.allOnlineUsers.length / window.usersPerPage);
            
            // Update range display
            const rangeStart = startIndex + 1;
            const rangeEnd = Math.min(endIndex, window.allOnlineUsers.length);
            $('#count-range').text(`${rangeStart}-${rangeEnd} of ${window.allOnlineUsers.length}`);
            
            // Generate HTML for current page users
            let html = '';
            currentUsers.forEach(user => {
                const connectedTime = user.connected_time || 'Unknown';
                const hostname = user.hostname || 'Unknown Device';
                const initials = hostname.substring(0, 2).toUpperCase();
                
                html += `
                    <div class="user-item">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="user-info">
                                <div class="user-avatar">${initials}</div>
                                <div class="user-details">
                                    <h6>${hostname}</h6>
                                    <small><i data-feather="cpu" style="width: 12px; height: 12px;"></i>${user.mac || 'N/A'}</small>
                                    <br>
                                    <small><i data-feather="globe" style="width: 12px; height: 12px;"></i>${user.ip || 'N/A'}</small>
                                </div>
                            </div>
                            <div class="user-status">
                                <span class="network-badge ${user.network_badge || 'badge-light-info'}">${user.network_label || 'Online'}</span>
                            </div>
                        </div>
                    </div>
                `;
            });
            
            $('#online-users-list').html(html);
            
            // Update pagination controls
            updatePaginationControls(totalPages);
            
            // Re-render Feather icons after dynamic content insertion
            if (typeof feather !== 'undefined') {
                feather.replace();
            }
        }
        
        // Update pagination controls
        function updatePaginationControls(totalPages) {
            $('#page-info').text(`${window.usersCurrentPage} / ${totalPages}`);
            
            // Update button states
            $('#prev-page').prop('disabled', window.usersCurrentPage === 1);
            $('#next-page').prop('disabled', window.usersCurrentPage === totalPages);
            
            // Generate page number buttons
            generatePageNumbers(totalPages);
            
            // Debug pagination controls
            console.log('🎛️ Pagination Controls Updated:', {
                currentPage: window.usersCurrentPage,
                totalPages: totalPages,
                prevDisabled: window.usersCurrentPage === 1,
                nextDisabled: window.usersCurrentPage === totalPages
            });
        }
        
        // Generate page number buttons
        function generatePageNumbers(totalPages) {
            const $pageNumbers = $('#page-numbers');
            $pageNumbers.empty();
            
            if (totalPages <= 1) {
                return; // No need for page numbers if only 1 page
            }
            
            const currentPage = window.usersCurrentPage;
            const maxVisible = 5; // Maximum visible page numbers
            
            let startPage = 1;
            let endPage = totalPages;
            
            // Calculate visible page range
            if (totalPages > maxVisible) {
                const half = Math.floor(maxVisible / 2);
                
                if (currentPage <= half + 1) {
                    // Near the beginning
                    startPage = 1;
                    endPage = maxVisible;
                } else if (currentPage >= totalPages - half) {
                    // Near the end
                    startPage = totalPages - maxVisible + 1;
                    endPage = totalPages;
                } else {
                    // In the middle
                    startPage = currentPage - half;
                    endPage = currentPage + half;
                }
            }
            
            // Add first page and ellipsis if needed
            if (startPage > 1) {
                addPageButton(1);
                if (startPage > 2) {
                    $pageNumbers.append('<span class="page-ellipsis">...</span>');
                }
            }
            
            // Add visible page numbers
            for (let i = startPage; i <= endPage; i++) {
                addPageButton(i);
            }
            
            // Add ellipsis and last page if needed
            if (endPage < totalPages) {
                if (endPage < totalPages - 1) {
                    $pageNumbers.append('<span class="page-ellipsis">...</span>');
                }
                addPageButton(totalPages);
            }
            
            // Re-render Feather icons if any
            if (typeof feather !== 'undefined') {
                feather.replace();
            }
        }
        
        // Add a page button
        function addPageButton(pageNumber) {
            const isActive = pageNumber === window.usersCurrentPage;
            const $button = $(`
                <button class="page-number-btn ${isActive ? 'active' : ''}" data-page="${pageNumber}">
                    ${pageNumber}
                </button>
            `);
            
            $button.on('click', function() {
                const targetPage = parseInt($(this).data('page'));
                if (targetPage !== window.usersCurrentPage) {
                    window.usersCurrentPage = targetPage;
                    renderCurrentPage();
                    console.log(`📄 Page ${targetPage} clicked`);
                }
            });
            
            $('#page-numbers').append($button);
        }
        
                         
        // Router Model Selection functionality
        $(document).ready(function() {
            // Check authentication first
            if (!UserManager.getToken()) {
                console.error('No authentication token found, redirecting to login');
                
                // Debug: Let's see what's actually in localStorage
                console.log('localStorage contents:');
                for (let i = 0; i < localStorage.length; i++) {
                    const key = localStorage.key(i);
                    console.log(`${key}: ${localStorage.getItem(key)}`);
                }
                
                console.log('UserManager.getToken():', UserManager.getToken());
                console.log('Available UserManager methods:', Object.keys(UserManager));
                
                // Don't redirect immediately for debugging
                // window.location.href = '/';
                // return;
            } else {
                console.log('Authentication token found:', UserManager.getToken().substring(0, 20) + '...');
            }
            
            // Check user role and show/hide role-based menu items
            if (UserManager.hasRole('superadmin')) {
                console.log('SuperAdmin user detected, showing all menu items');
                $('.admin_and_above').removeClass('hidden').show();
                $('.only_superadmin').removeClass('hidden').show();
                $('[data-admin-only="true"]').show();
            } else if (UserManager.hasRole('admin')) {
                console.log('Admin user detected, showing admin menu items');
                $('.admin_and_above').removeClass('hidden').show();
                $('.only_superadmin').addClass('hidden').hide();
                $('[data-admin-only="true"]').show();
            } else {
                console.log('Regular user detected, hiding admin-only elements');
                $('.admin_and_above').addClass('hidden').hide();
                $('.only_superadmin').addClass('hidden').hide();
                $('[data-admin-only="true"]').hide();
            }

            // Clear/unset router model on page load
            console.log('Clearing router model on page load');
            $('.router_model_updated').text('');
            $('.router_firmware').text('Unknown');  // Set default text instead of empty
            $('#router-model-select').val('');
            
            // Global variable to store current device data
            window.currentDeviceData = null;

            // Function to load device data from API
            // Function to load device info specifically for the modal
            function loadDeviceInfoForModal(locationId) {
                console.log('Loading device info for modal');
                
                $.ajax({
                    url: '/api/locations/' + locationId,
                    method: 'GET',
                    headers: {
                        'Authorization': 'Bearer ' + UserManager.getToken(),
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    success: function(response) {
                        console.log('Device info loaded for modal:', response);
                        
                        // Extract device data from response
                        let device = null;
                        if (response.data && response.data.device) {
                            device = response.data.device;
                        } else if (response.device) {
                            device = response.device;
                        } else if (response.data && response.data.devices && response.data.devices.length > 0) {
                            device = response.data.devices[0];
                        }
                        
                        if (device) {
                            $('#modal-device-id').text(device.id || '-');
                            $('#modal-scan-counter').text(device.scan_counter || 0);
                            $('#modal-next-scan-id').text((device.scan_counter || 0) + 1);
                        } else {
                            $('#modal-device-id').text('-');
                            $('#modal-scan-counter').text('-');
                            $('#modal-next-scan-id').text('-');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Failed to load device info for modal:', error);
                        $('#modal-device-id').text('Error');
                        $('#modal-scan-counter').text('Error');
                        $('#modal-next-scan-id').text('Error');
                    }
                });
            }

            function loadDeviceData() {
                console.log('Loading device data from API');
                
                // Get location ID from URL or data attribute
                const locationId = getLocationId();
                
                if (!locationId) {
                    console.log('No location ID found - cannot load device data');
                    return;
                }

                console.log('Making API call to /api/locations/' + locationId);

                $.ajax({
                    url: '/api/locations/' + locationId,
                    method: 'GET',
                    headers: {
                        'Authorization': 'Bearer ' + UserManager.getToken(),
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    success: function(response) {
                        console.log('API Response received:', response);
                        
                        // Extract location data from response
                        let location = null;
                        if (response.data) {
                            location = response.data;
                        } else if (response.location) {
                            location = response.location;
                        }

                        console.log('Extracted location data:', location);

                        // Store location data globally for use in other functions
                        window.currentLocationData = location;
                        
                        // Populate location form fields with API data
                        if (location) {
                            console.log('Populating location form with API data:', location);
                            $('#location-name').val(location.name || '');
                            $('#location-address').val(location.address || '');
                            $('#location-city').val(location.city || '');
                            $('#location-state').val(location.state || '');
                            $('#location-postal-code').val(location.postal_code || '');
                            $('#location-country').val(location.country || '');
                            $('#location-manager').val(location.manager_name || '');
                            $('#location-contact-email').val(location.contact_email || '');
                            $('#location-contact-phone').val(location.contact_phone || '');
                            $('#location-status').val(location.status || 'active');
                            $('#location-description').val(location.description || '');
                            
                            // Store owner_id to set after users are loaded
                            if (location.owner_id) {
                                console.log('Location has owner_id:', location.owner_id);
                                // Reload the owner dropdown with the correct selected value
                                if (UserManager.isAdminOrAbove()) {
                                    loadUsersForOwnerDropdown(location.owner_id);
                                }
                            } else {
                                console.log('Location has no owner assigned');
                                // Still reload to ensure dropdown is populated
                                if (UserManager.isAdminOrAbove()) {
                                    loadUsersForOwnerDropdown();
                                }
                            }
                            
                            // Update UI elements
                            $('.location_name').text(location.name || 'Unnamed Location');
                            $('.location_address').text((location.address || '') + ', ' + (location.city || '') + ', ' + (location.state || ''));
                        }

                        // Initialize map if location has coordinates
                        if (location && location.latitude && location.longitude) {
                            // Convert coordinates to ensure they're numbers
                            const lat = parseFloat(location.latitude);
                            const lng = parseFloat(location.longitude);
                            
                            if (!isNaN(lat) && !isNaN(lng)) {
                                // Delay map initialization to ensure DOM is ready
                                setTimeout(function() {
                                    initializeLocationMap(lat, lng, location.name, location.address);
                                }, 300);
                            } else {
                                console.error('Invalid coordinates from API:', location.latitude, location.longitude);
                                $('#location-map').html(`
                                    <div class="d-flex align-items-center justify-content-center h-100">
                                        <div class="text-center text-warning">
                                            <i data-feather="alert-circle" class="mb-2"></i>
                                            <p class="mb-0">Invalid coordinates from server</p>
                                            <small>Please check location data</small>
                                        </div>
                                    </div>
                                `);
                                if (typeof feather !== 'undefined') {
                                    feather.replace();
                                }
                            }
                        } else {
                            console.log('No coordinates found for location, map not initialized');
                            // Show a message in the map container
                            $('#location-map').html(`
                                <div class="d-flex align-items-center justify-content-center h-100">
                                    <div class="text-center text-muted">
                                        <i data-feather="map-pin" class="mb-2"></i>
                                        <p class="mb-0">No coordinates available</p>
                                        <small>Add address information to see location on map</small>
                                    </div>
                                </div>
                            `);
                            $('#map-coordinates').hide();
                            if (typeof feather !== 'undefined') {
                                feather.replace();
                            }
                        }
                        
                        // Extract device data from response
                        let device = null;
                        if (response.data && response.data.device) {
                            device = response.data.device;
                        } else if (response.device) {
                            device = response.device;
                        } else if (response.data && response.data.devices && response.data.devices.length > 0) {
                            device = response.data.devices[0]; // Take first device if multiple
                        }

                        console.log('Extracted device data:', device);

                        if (device) {
                            // Store device data globally for use in other functions
                            window.currentDeviceData = device;
                            
                            // Update router model if it exists
                            if (device.model) {
                                console.log('Setting router model to:', device.model);
                                $('.router_model_updated').text(device.model);
                                $('#router-model-select').val(device.model);
                            } else {
                                console.log('No device model found, leaving blank');
                                $('.router_model_updated').text('');
                                $('#router-model-select').val('');
                            }

                            // Update MAC address if it exists
                            if (device.mac_address) {
                                console.log('Setting MAC address to:', device.mac_address);
                                // Use hyphens as delimiter (no conversion needed)
                                $('.router_mac_address').text(device.mac_address);
                                $('.router_mac_address_header').text(device.mac_address);
                            } else {
                                console.log('No MAC address found, leaving blank');
                                $('.router_mac_address').text('Not Available');
                                $('.router_mac_address_header').text('Not Available');
                            }

                            // Update firmware version if it exists
                            if (device.firmware_version && device.firmware_version.trim() !== '') {
                                console.log('Setting firmware version to:', device.firmware_version);
                                $('.router_firmware').text(device.firmware_version);
                            } else {
                                console.log('No firmware version found, checking for latest firmware for model:', device.model);
                                $('.router_firmware').text('Not Set');
                                
                                // If device has a model but no firmware, try to get the latest firmware for this model
                                if (device.model && (device.model === '820AX' || device.model === '835AX')) {
                                    loadLatestFirmwareForModel(device.model);
                                }
                            }
                            // Update other device fields
                            if (device.reboot_count !== null && device.reboot_count !== undefined) {
                                console.log('Setting reboot count to:', device.reboot_count);
                                $('.reboot_count').text(device.reboot_count);
                            }
                            
                            // Update uptime if it exists
                            if (device.uptime !== null && device.uptime !== undefined) {
                                var uptime_text = '';
                                var actual_uptime = device.uptime;
                                
                                // If device is offline, set uptime to 0
                                if (device.is_online === false) {
                                    console.log('Device is offline, setting uptime to 0');
                                    actual_uptime = 0;
                                }
                                
                                console.log('Setting uptime to:', actual_uptime);
                                
                                // Uptime is in seconds, convert to hours, minutes, Hours Days
                                const uptime_hours = Math.floor(actual_uptime / 3600);
                                const uptime_minutes = Math.floor((actual_uptime % 3600) / 60);
                                if (uptime_hours > 24) {
                                    const uptime_days = Math.floor(uptime_hours / 24);
                                    uptime_text = uptime_days + 'd ' + (uptime_hours % 24) + 'h ' + uptime_minutes + 'm';
                                } else if (uptime_hours > 0) {
                                    uptime_text = uptime_hours + 'h ' + uptime_minutes + 'm';
                                } else {
                                    uptime_text = uptime_minutes + 'm';
                                }
                                $('.uptime').text(uptime_text);
                            } else {
                                console.log('No uptime found, leaving blank');
                                $('.uptime').text('');
                            }
                        } else {
                            console.log('No device data found in response, setting defaults');
                            window.currentDeviceData = null;
                            $('.router_model_updated').text('');
                            $('.router_firmware').text('Unknown');
                            $('.router_mac_address').text('Not Available');
                            $('.router_mac_address_header').text('Not Available');
                            $('.uptime').text('');
                        }
                    },
                    error: function(xhr, status, error) {
                        handleApiError(xhr, status, error, 'loading device data');
                    }
                });
            }

            // Function to initialize location map with coordinates
            function initializeLocationMap(latitude, longitude, locationName, locationAddress) {
                console.log('Initializing location map with coordinates:', latitude, longitude);
                console.log('Coordinate types:', typeof latitude, typeof longitude);
                
                // Convert to numbers and validate coordinates
                const lat = parseFloat(latitude);
                const lng = parseFloat(longitude);
                
                if (isNaN(lat) || isNaN(lng)) {
                    console.error('Invalid coordinates provided:', { latitude, longitude });
                    $('#location-map').html(`
                        <div class="d-flex align-items-center justify-content-center h-100">
                            <div class="text-center text-warning">
                                <i data-feather="alert-circle" class="mb-2"></i>
                                <p class="mb-0">Invalid coordinates</p>
                                <small>Latitude: ${latitude}, Longitude: ${longitude}</small>
                            </div>
                        </div>
                    `);
                    $('#map-coordinates').hide();
                    if (typeof feather !== 'undefined') {
                        feather.replace();
                    }
                    return;
                }
                
                // Check if map container exists and is visible
                const mapContainer = document.getElementById('location-map');
                if (!mapContainer) {
                    console.error('Map container not found');
                    return;
                }
                
                // Clear existing map if any
                if (window.locationMap) {
                    try {
                        window.locationMap.remove();
                        window.locationMap = null;
                    } catch (e) {
                        console.log('Error removing existing map:', e);
                    }
                }
                
                $('#location-map').empty();
                
                // Wait a bit to ensure container is properly rendered
                setTimeout(function() {
                    try {
                        // Check if Leaflet is loaded
                        if (typeof L === 'undefined') {
                            console.error('Leaflet library not loaded');
                            $('#location-map').html(`
                                <div class="d-flex align-items-center justify-content-center h-100">
                                    <div class="text-center text-warning">
                                        <i data-feather="alert-circle" class="mb-2"></i>
                                        <p class="mb-0">Map library loading...</p>
                                        <small>Please wait a moment</small>
                                    </div>
                                </div>
                            `);
                            if (typeof feather !== 'undefined') {
                                feather.replace();
                            }
                                                         // Retry after Leaflet loads
                             setTimeout(function() {
                                 initializeLocationMap(lat, lng, locationName, locationAddress);
                             }, 1000);
                            return;
                        }
                        
                        // Check again that container exists and has dimensions
                        if (!isMapContainerReady()) {
                            console.log('Map container not ready, waiting...');
                                                         // Try again after a longer delay
                             setTimeout(function() {
                                 initializeLocationMap(lat, lng, locationName, locationAddress);
                             }, 500);
                            return;
                        }
                        
                        // Initialize the map
                        const map = L.map('location-map').setView([lat, lng], 15);
                    
                    // Add OpenStreetMap tile layer
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
                        maxZoom: 19
                    }).addTo(map);
                    
                    // Create popup content
                    let popupContent = `<strong>${locationName || 'Location'}</strong>`;
                    if (locationAddress) {
                        popupContent += `<br><small>${locationAddress}</small>`;
                    }
                    popupContent += `<br><small>Lat: ${lat.toFixed(6)}, Lng: ${lng.toFixed(6)}</small>`;
                    
                    // Add marker with popup
                    const marker = L.marker([lat, lng]).addTo(map);
                    marker.bindPopup(popupContent).openPopup();
                    
                        // Store map reference globally for potential future use
                        window.locationMap = map;
                        
                        // Show coordinates in the header
                        $('#map-coordinates').text(`${lat.toFixed(6)}, ${lng.toFixed(6)}`).show();
                        
                        console.log('Location map initialized successfully');
                    } catch (error) {
                        console.error('Error initializing location map:', error);
                        $('#location-map').html(`
                            <div class="d-flex align-items-center justify-content-center h-100">
                                <div class="text-center text-danger">
                                    <i data-feather="alert-triangle" class="mb-2"></i>
                                    <p class="mb-0">Error loading map</p>
                                    <small>Please try refreshing the page</small>
                                </div>
                            </div>
                        `);
                        $('#map-coordinates').hide();
                        if (typeof feather !== 'undefined') {
                            feather.replace();
                        }
                    }
                }, 100); // End of setTimeout
            }

            // Function to check if map container is ready
            function isMapContainerReady() {
                const container = document.getElementById('location-map');
                if (!container) {
                    return false;
                }
                
                // Check if container is visible and has dimensions
                const rect = container.getBoundingClientRect();
                return rect.width > 0 && rect.height > 0;
            }

            // Function to update location map with new coordinates
            function updateLocationMap(location) {
                if (!location || !location.latitude || !location.longitude) {
                    console.log('No location coordinates to update map');
                    return;
                }
                
                // Convert coordinates to ensure they're numbers
                const lat = parseFloat(location.latitude);
                const lng = parseFloat(location.longitude);
                
                if (isNaN(lat) || isNaN(lng)) {
                    console.error('Invalid coordinates in location data:', location.latitude, location.longitude);
                    return;
                }
                
                console.log('Updating location map with new coordinates:', lat, lng);
                
                // Check if container is ready before updating
                if (isMapContainerReady()) {
                    initializeLocationMap(lat, lng, location.name, location.address);
                } else {
                    console.log('Map container not ready, waiting...');
                    setTimeout(function() {
                        updateLocationMap(location);
                    }, 500);
                }
            }

            // Function to load latest firmware for a specific model when current firmware is not set (make it globally accessible)
            window.loadLatestFirmwareForModel = function(model) {
                console.log('Loading latest firmware for model:', model);
                
                getFirmwareByModel(model)
                    .then(function(firmwareVersions) {
                        if (firmwareVersions.length > 0) {
                            // Find the latest firmware (you can modify this logic based on your needs)
                            // For now, we'll take the first one or look for one marked as latest
                            let latestFirmware = firmwareVersions.find(fw => fw.is_latest) || firmwareVersions[0];
                            
                            console.log('Found latest firmware:', latestFirmware);
                            $('.router_firmware').text(latestFirmware.version + ' (Latest Available)');
                        } else {
                            console.log('No firmware versions found for model:', model);
                            $('.router_firmware').text('No Firmware Available');
                        }
                    })
                    .catch(function(error) {
                        console.error('Error loading latest firmware for model:', error);
                        $('.router_firmware').text('Error Loading Firmware');
                    });
            };

            // Save location information including router model
            $('#save-location-info').on('click', function() {
                const locationData = {
                    name: $('#location-name').val(),
                    address: $('#location-address').val(),
                    city: $('#location-city').val(),
                    state: $('#location-state').val(),
                    postal_code: $('#location-postal-code').val(),
                    country: $('#location-country').val(),
                    router_model: $('#router-model-select').val(),
                    manager: $('#location-manager').val(),
                    contact_email: $('#location-contact-email').val(),
                    contact_phone: $('#location-contact-phone').val(),
                    status: $('#location-status').val(),
                    description: $('#location-description').val(),
                };

                // Only include owner_id if user is admin or above
                if (UserManager.isAdminOrAbove()) {
                    locationData.owner_id = $('#location-owner').val() || null;
                }

                // Validate required fields
                if (!locationData.name) {
                    toastr.error('Location name is required.');
                    return;
                }

                // Router model is optional now - removed validation
                // if (!locationData.router_model) {
                //     toastr.error('Please select a router model.');
                //     return;
                // }

                // Show loading state
                const $button = $(this);
                const originalText = $button.html();
                $button.html('<i data-feather="loader" class="mr-1"></i> Saving...').prop('disabled', true);

                // Make real API call to save location information
                const locationId = getLocationId();
                if (!locationId) {
                    toastr.error('Location ID not found');
                    $button.html(originalText).prop('disabled', false);
                    return;
                }

                console.log('Saving location data:', locationData);

                $.ajax({
                    url: '/api/locations/' + locationId + '/general',
                    method: 'PUT',
                    headers: {
                        'Authorization': 'Bearer ' + UserManager.getToken(),
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    data: JSON.stringify(locationData),
                    success: function(response) {
                        console.log('Location data saved successfully:', response);
                        
                        // Update UI elements with new data
                        $('.location_name').text(locationData.name);
                        $('.location_address').text(locationData.address + ', ' + locationData.city + ', ' + locationData.state);
                        $('.router_model_updated').text(locationData.router_model);
                        
                        // Reset button state
                        $button.html(originalText).prop('disabled', false);
                        
                        // Show success message
                        toastr.success('Location information saved successfully!');
                        
                        // Re-initialize feather icons
                        if (typeof feather !== 'undefined') {
                            feather.replace();
                        }
                        
                        // Reload device data to verify the update and refresh map with potentially new coordinates
                        setTimeout(function() {
                            loadDeviceData();
                        }, 1000);
                    },
                    error: function(xhr, status, error) {
                        // Reset button state first
                        $button.html(originalText).prop('disabled', false);
                        
                        // Then handle the API error
                        handleApiError(xhr, status, error, 'saving location data');
                    }
                });
            });

            // Load existing location data when page loads
            function loadLocationData() {
                console.log('loadLocationData called');
                
                // In a real application, this would load from your backend
                // For now, we'll populate with sample data if fields are empty
                
                if (!$('#location-name').val()) {
                    console.log('Loading sample location data');
                    // Sample data - replace with actual API call
                    const sampleData = {
                        name: 'Downtown Coffee Shop',
                        address: '123 Main Street',
                        city: 'New York',
                        state: 'NY',
                        postal_code: '10001',
                        country: 'United States',
                        router_model: '', // Always start with blank
                        manager: 'John Smith',
                        contact_email: 'john@coffeeshop.com',
                        contact_phone: '+1 (555) 123-4567',
                        status: 'active',
                        description: 'Main downtown location with high traffic'
                    };
                    
                    // Populate form fields
                    $('#location-name').val(sampleData.name);
                    $('#location-address').val(sampleData.address);
                    $('#location-city').val(sampleData.city);
                    $('#location-state').val(sampleData.state);
                    $('#location-postal-code').val(sampleData.postal_code);
                    $('#location-country').val(sampleData.country);
                    $('#router-model-select').val(sampleData.router_model);
                    $('#location-manager').val(sampleData.manager);
                    $('#location-contact-email').val(sampleData.contact_email);
                    $('#location-contact-phone').val(sampleData.contact_phone);
                    $('#location-status').val(sampleData.status);
                    $('#location-description').val(sampleData.description);
                    
                    // Update UI elements
                    $('.location_name').text(sampleData.name);
                    $('.location_address').text(sampleData.address + ', ' + sampleData.city + ', ' + sampleData.state);
                    $('.router_model_updated').text(sampleData.router_model);
                } else {
                    console.log('Location data already loaded, skipping');
                }
            }

            // Load data when the location settings tab is shown
            $('a[href="#location-settings"]').on('shown.bs.tab', function() {
                loadLocationData();
            });

            // Load data immediately if location settings tab is active
            if ($('#location-settings').hasClass('active')) {
                loadLocationData();
            }

            // Handle router model selection change
            $('#router-model-select').on('change', function() {
                const selectedModel = $(this).val();
                console.log('Router model changed to:', selectedModel);
                
                // Update UI immediately
                $('.router_model_updated').text(selectedModel);
                
                // Update device model via API
                updateDeviceModel(selectedModel);
                
                // Show success message
                if (selectedModel) {
                    toastr.success('Router model updated to ' + selectedModel);
                }
            });

            // Function to update device model via API
            function updateDeviceModel(model) {
                const locationId = getLocationId();
                if (!locationId) {
                    console.log('No location ID found, cannot update device model');
                    return;
                }

                console.log('Updating device model to:', model, 'for location:', locationId);

                $.ajax({
                    url: '/api/locations/' + locationId,
                    method: 'PUT',
                    headers: {
                        'Authorization': 'Bearer ' + UserManager.getToken(),
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    data: JSON.stringify({
                        device: {
                            model: model
                        }
                    }),
                    success: function(response) {
                        console.log('Device model updated successfully:', response);
                        toastr.success('Router model updated successfully');
                        
                        // Reload device data to verify the update
                        setTimeout(function() {
                            loadDeviceData();
                        }, 1000);
                    },
                    error: function(xhr, status, error) {
                        handleApiError(xhr, status, error, 'updating device model');
                    }
                });
            }

            // Initialize router model on page load
            setTimeout(function() {
                const currentRouterModel = $('.router_model').text();
                console.log('Timeout check - current router model:', currentRouterModel);
                
                if (!currentRouterModel || currentRouterModel === '') {
                    const savedModel = localStorage.getItem('router_model');
                    if (savedModel) {
                        console.log('Timeout setting router model to saved:', savedModel);
                        $('.router_model').text(savedModel);
                    } else {
                        console.log('No saved model, leaving blank');
                    }
                } else {
                    console.log('Timeout check - router model already properly set:', currentRouterModel);
                }
            }, 100);

            // Function to load all users for the owner dropdown
            function loadUsersForOwnerDropdown(selectedOwnerId = null) {
                console.log('Loading users for owner dropdown...');
                
                $.ajax({
                    url: '/api/accounts/users',
                    method: 'GET',
                    headers: {
                        'Authorization': 'Bearer ' + UserManager.getToken(),
                        'Accept': 'application/json'
                    },
                    success: function(response) {
                        console.log('Users loaded successfully:', response);
                        
                        const $ownerSelect = $('#location-owner');
                        $ownerSelect.empty();
                        $ownerSelect.append('<option value="">Select Owner</option>');
                        
                        if (response.status === 'success' && response.users) {
                            response.users.forEach(function(user) {
                                $ownerSelect.append(`<option value="${user.id}">${user.name} (${user.email})</option>`);
                            });
                        }
                        
                        // Set the selected owner if provided
                        if (selectedOwnerId) {
                            console.log('Setting selected owner to:', selectedOwnerId);
                            $ownerSelect.val(selectedOwnerId);
                            
                            // Verify it was set correctly
                            const actualValue = $ownerSelect.val();
                            console.log('Owner dropdown value after setting:', actualValue);
                            if (actualValue !== selectedOwnerId.toString()) {
                                console.warn('Failed to set owner dropdown value. Expected:', selectedOwnerId, 'Actual:', actualValue);
                            }
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error loading users for owner dropdown:', error);
                        // Fallback - just show empty dropdown with error message
                        const $ownerSelect = $('#location-owner');
                        $ownerSelect.empty();
                        $ownerSelect.append('<option value="">Unable to load users</option>');
                    }
                });
            }

            // Load device data when page loads - with delay to ensure DOM is ready
            $(document).ready(function() {
                setTimeout(function() {
                    loadDeviceData();
                    // Note: loadUsersForOwnerDropdown() will be called from within loadDeviceData 
                    // when location data is populated, with the correct owner selected
                }, 500);
            });

            // Handle window resize to make map responsive
            $(window).on('resize', function() {
                if (window.locationMap) {
                    setTimeout(function() {
                        window.locationMap.invalidateSize();
                    }, 100);
                }
            });

            // Handle tab switching to refresh map size
            $('a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
                if (window.locationMap) {
                    setTimeout(function() {
                        window.locationMap.invalidateSize();
                    }, 100);
                } else {
                    // If map doesn't exist but container is now visible, try to initialize
                    setTimeout(function() {
                        if (isMapContainerReady() && window.currentLocationData) {
                            const location = window.currentLocationData;
                            if (location && location.latitude && location.longitude) {
                                // Convert coordinates to ensure they're numbers
                                const lat = parseFloat(location.latitude);
                                const lng = parseFloat(location.longitude);
                                
                                if (!isNaN(lat) && !isNaN(lng)) {
                                    initializeLocationMap(lat, lng, location.name, location.address);
                                }
                            }
                        }
                    }, 200);
                }
            });

            // Initialize Bootstrap tabs properly
            $(document).ready(function() {
                // Let Bootstrap handle tabs automatically - no manual intervention needed
                console.log('Bootstrap tabs initialized');
            });

            // Load web filter categories when page loads
            loadWebFilterCategories();
            
            // Load last scan results when page loads
            loadLastScanResults();
            
            // Load all device settings when page loads
            loadDeviceSettings();



            // Function to load all device settings from API 
            function loadDeviceSettings() {
                const locationId = getLocationId();
                if (!locationId) {
                    console.log('No location ID found - cannot load device settings');
                    return;
                }

                console.log('Loading device settings for location:', locationId);
                
                // Try the direct device settings endpoint first
                if (window.currentDeviceData && window.currentDeviceData.device_token) {
                    const deviceId = window.currentDeviceData.id;
                    const deviceToken = window.currentDeviceData.device_token;
                    
                    $.ajax({
                        url: `/api/devices/${deviceId}/${deviceToken}/settings`,
                        method: 'GET',
                        headers: {
                            'Authorization': 'Bearer ' + UserManager.getToken(),
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        success: function(response) {
                            console.log('Device settings loaded from direct endpoint:', response);
                            populateAllSettings(response);
                        },
                        error: function(xhr, status, error) {
                            console.log('Failed to load from device endpoint, trying location settings:', error);
                            loadLocationSettings();
                        }
                    });
                } else {
                    // Fallback to location settings
                    loadLocationSettings();
                }
            }

            // Function to load settings from location endpoint
            function loadLocationSettings() {
                const locationId = getLocationId();
                
                $.ajax({
                    url: `/api/locations/${locationId}/settings`,
                    method: 'GET',
                    headers: {
                        'Authorization': 'Bearer ' + UserManager.getToken(),
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    success: function(response) {
                        console.log('Location settings loaded:', response);
                        populateAllSettings(response);
                    },
                    error: function(xhr, status, error) {
                        console.log('Failed to load location settings:', error);
                        // Don't show error toast as settings might not exist yet
                    }
                });
            }

            // Function to populate all form fields with settings data
            function populateAllSettings(response) {
                console.log('Populating all settings with:', response);
                
                let settings = null;
                
                // Extract settings from different response formats
                if (response.settings) {
                    settings = response.settings;
                } else if (response.data && response.data.settings) {
                    settings = response.data.settings;
                } else if (response.data) {
                    settings = response.data;
                }
                
                if (!settings) {
                    console.log('No settings found in response');
                    return;
                }

                console.log('Extracted settings:', settings);
                
                // Store settings globally for other functions to access
                window.deviceSettings = { settings: settings };
                console.log('Stored settings in window.deviceSettings:', window.deviceSettings);
                
                // **VLAN Settings**
                if (settings.vlan_enabled !== undefined) {
                    $('#vlan-enabled').prop('checked', settings.vlan_enabled);
                    console.log('Set VLAN enabled to:', settings.vlan_enabled);
                }
                
                // **Radio Settings**
                if (settings.country_code) {
                    $('#wifi-country').val(settings.country_code);
                }
                if (settings.transmit_power_2g) {
                    $('#power-level-2g').val(settings.transmit_power_2g);
                }
                if (settings.transmit_power_5g) {
                    $('#power-level-5g').val(settings.transmit_power_5g);
                }
                if (settings.channel_width_2g) {
                    $('#channel-width-2g').val(settings.channel_width_2g);
                }
                if (settings.channel_width_5g) {
                    $('#channel-width-5g').val(settings.channel_width_5g);
                }
                if (settings.channel_2g) {
                    $('#channel-2g').val(settings.channel_2g);
                }
                if (settings.channel_5g) {
                    $('#channel-5g').val(settings.channel_5g);
                }
                
                // **Captive Portal Settings**
                if (settings.captive_portal_ssid) {
                    $('#captive-portal-ssid').val(settings.captive_portal_ssid);
                }
                if (settings.captive_portal_visible !== undefined) {
                    $('#captive-portal-visible').val(settings.captive_portal_visible ? '1' : '0');
                }
                if (settings.captive_auth_method) {
                    $('#captive-auth-method').val(settings.captive_auth_method).trigger('change');
                }
                if (settings.captive_portal_password) {
                    $('#captive_portal_password').val(settings.captive_portal_password);
                }
                if (settings.session_timeout) {
                    $('#captive-session-timeout').val(settings.session_timeout);
                }
                if (settings.idle_timeout) {
                    $('#captive-idle-timeout').val(settings.idle_timeout);
                }
                // Handle bandwidth limits - set to value or empty string if null/undefined
                if (settings.download_limit !== undefined && settings.download_limit !== null) {
                    $('#captive-download-limit').val(settings.download_limit);
                } else {
                    $('#captive-download-limit').val(''); // Default to empty for no limit
                }
            
                if (settings.upload_limit !== undefined && settings.upload_limit !== null) {
                    $('#captive-upload-limit').val(settings.upload_limit);
                } else {
                    $('#captive-upload-limit').val('0'); // Default to "0" option for no limit
                }
                if (settings.captive_portal_redirect) {
                    $('#captive-portal-redirect').val(settings.captive_portal_redirect);
                }
                
                // **Captive Portal Network Settings**
                console.log('=== POPULATING CAPTIVE PORTAL NETWORK SETTINGS ===');
                console.log('Captive portal IP from settings:', settings.captive_portal_ip);
                console.log('Captive portal netmask from settings:', settings.captive_portal_netmask);
                console.log('Captive portal gateway from settings:', settings.captive_portal_gateway);
                console.log('Captive portal DNS1 from settings:', settings.captive_portal_dns1);
                console.log('Captive portal DNS2 from settings:', settings.captive_portal_dns2);
                
                if (settings.captive_portal_ip) {
                    $('#captive-portal-ip').val(settings.captive_portal_ip);
                    // Update display field for IP address
                    $('#captive-ip-display').text(settings.captive_portal_ip);
                    console.log('Set captive portal IP to:', settings.captive_portal_ip);
                }
                if (settings.captive_portal_netmask) {
                    $('#captive-portal-netmask').val(settings.captive_portal_netmask);
                    // Update display field for netmask
                    $('#captive-netmask-display').text(settings.captive_portal_netmask);
                    console.log('Set captive portal netmask to:', settings.captive_portal_netmask);
                }
                if (settings.captive_portal_gateway) {
                    $('#captive-portal-gateway').val(settings.captive_portal_gateway);
                    // Update display field for gateway
                    $('#captive-gateway-display').text(settings.captive_portal_gateway);
                    console.log('Set captive portal gateway to:', settings.captive_portal_gateway);
                }
                if (settings.captive_portal_vlan) {
                    $('#captive-portal-vlan').val(settings.captive_portal_vlan);
                    $('#captive-portal-vlan-modal').val(settings.captive_portal_vlan);
                }
                if (settings.captive_portal_vlan_tagging) {
                    $('#captive-portal-vlan-tagging').val(settings.captive_portal_vlan_tagging);
                    $('#captive-portal-vlan-tagging-modal').val(settings.captive_portal_vlan_tagging);
                }
                
                // **Password WiFi Settings**
                if (settings.password_wifi_ssid || settings.wifi_name) {
                    $('#password-wifi-ssid').val(settings.password_wifi_ssid || settings.wifi_name);
                }
                if (settings.password_wifi_password || settings.wifi_password) {
                    $('#password-wifi-password').val(settings.password_wifi_password || settings.wifi_password);
                }
                if (settings.wifi_visible) {
                    console.log('Password WiFi visible:', settings.wifi_visible);
                    if (settings.wifi_visible) {
                        $('#password-wifi-visible').val(1);
                    } 
                }else{
                    console.log('Password WiFi visible:', settings.wifi_visible);
                    $('#password-wifi-visible').val(0);
                }
                if (settings.password_wifi_security || settings.wifi_security_type) {
                    const security = settings.password_wifi_security || settings.wifi_security_type;
                    if (security === 'WPA2') {
                        $('#password-wifi-security').val('wpa2-psk');
                    } else {
                        $('#password-wifi-security').val(security.toLowerCase());
                    }
                }
                if (settings.password_wifi_cipher_suites) {
                    $('#password_wifi_cipher_suites').val(settings.password_wifi_cipher_suites);
                }
                
                // **Password WiFi Network Settings**
                if (settings.password_wifi_ip_mode || settings.password_wifi_ip_type) {
                    $('#password-ip-assignment').val((settings.password_wifi_ip_mode || settings.password_wifi_ip_type).toUpperCase()).trigger('change');
                    // Update display field for IP assignment type
                    $('#password-wifi-ip-type-display').text((settings.password_wifi_ip_mode || settings.password_wifi_ip_type).toUpperCase());
                }
                if (settings.password_wifi_ip) {
                    $('#password-ip').val(settings.password_wifi_ip);
                    // For password WiFi, gateway is typically the same as IP address
                    $('#password-gateway').val(settings.password_wifi_ip);
                    // Update the display as well
                    $('#password-gateway-display').text(settings.password_wifi_ip);
                    // Update display field for IP address
                    $('#password-ip-display').text(settings.password_wifi_ip);
                }
                if (settings.password_wifi_netmask) {
                    $('#password-netmask').val(settings.password_wifi_netmask);
                    // Update display field for netmask
                    $('#password-netmask-display').text(settings.password_wifi_netmask);
                }
                if (settings.password_wifi_dns1) {
                    $('#password-primary-dns').val(settings.password_wifi_dns1);
                }
                if (settings.password_wifi_dns2) {
                    $('#password-secondary-dns').val(settings.password_wifi_dns2);
                }
                if (settings.password_wifi_vlan) {
                    $('#password-wifi-vlan').val(settings.password_wifi_vlan);
                }
                if (settings.password_wifi_vlan_tagging) {
                    $('#password-wifi-vlan-tagging').val(settings.password_wifi_vlan_tagging);
                    $('#password-wifi-vlan-tagging-modal').val(settings.password_wifi_vlan_tagging);
                }
                if (settings.password_wifi_dhcp_enabled !== undefined) {
                    $('#password-dhcp-server-toggle').prop('checked', settings.password_wifi_dhcp_enabled).trigger('change');
                    // Update display field for DHCP server status
                    $('#password-dhcp-status-display').text(settings.password_wifi_dhcp_enabled ? 'Enabled' : 'Disabled');
                }
                if (settings.password_wifi_dhcp_start) {
                    $('#password-dhcp-start').val(settings.password_wifi_dhcp_start);
                }
                if (settings.password_wifi_dhcp_end) {
                    $('#password-dhcp-end').val(settings.password_wifi_dhcp_end);
                }
                
                // **WAN Settings**
                if (settings.wan_connection_type) {
                    $('#wan-connection-type').val(settings.wan_connection_type).trigger('change');
                }
                if (settings.wan_ip_address) {
                    $('#wan-ip-address').val(settings.wan_ip_address);
                }
                if (settings.wan_netmask) {
                    $('#wan-netmask').val(settings.wan_netmask);
                }
                if (settings.wan_gateway) {
                    $('#wan-gateway').val(settings.wan_gateway);
                }
                if (settings.wan_primary_dns) {
                    $('#wan-primary-dns').val(settings.wan_primary_dns);
                }
                if (settings.wan_secondary_dns) {
                    $('#wan-secondary-dns').val(settings.wan_secondary_dns);
                }
                if (settings.wan_pppoe_username) {
                    $('#wan-pppoe-username').val(settings.wan_pppoe_username);
                }
                if (settings.wan_pppoe_password) {
                    $('#wan-pppoe-password').val(settings.wan_pppoe_password);
                }
                if (settings.wan_pppoe_service_name) {
                    $('#wan-pppoe-service-name').val(settings.wan_pppoe_service_name);
                }
                
                // **Web Filter Settings**
                if (settings.web_filter_enabled !== undefined) {
                    $('#global-web-filter').prop('checked', settings.web_filter_enabled);
                }
                
                // **MAC Filter Settings**
                // Load MAC addresses from separate fields
                if (settings.captive_mac_filter_list && Array.isArray(settings.captive_mac_filter_list)) {
                    loadMacAddressesFromSettings(settings.captive_mac_filter_list, 'captive');
                }
                if (settings.secured_mac_filter_list && Array.isArray(settings.secured_mac_filter_list)) {
                    loadMacAddressesFromSettings(settings.secured_mac_filter_list, 'secured');
                }
                
                // Backward compatibility - if old mac_filter_list exists, load it for both contexts
                if (settings.mac_filter_list && Array.isArray(settings.mac_filter_list)) {
                    if (!settings.captive_mac_filter_list) {
                        loadMacAddressesFromSettings(settings.mac_filter_list, 'captive');
                    }
                    if (!settings.secured_mac_filter_list) {
                        loadMacAddressesFromSettings(settings.mac_filter_list, 'secured');
                    }
                }
                
                console.log('All settings populated successfully');
                
                // **Show Password WiFi Network IP fields if using static IP**
                if (settings.password_wifi_ip_mode === 'STATIC' || settings.password_wifi_ip_type === 'STATIC') {
                    $('.password-ip-assignment-display_div').removeClass('hidden').show();
                    console.log('Password WiFi network IP fields shown for STATIC mode');
                } else {
                    $('.password-ip-assignment-display_div').addClass('hidden').hide();
                    console.log('Password WiFi network IP fields hidden for non-STATIC mode');
                }
                
                // **Important: Trigger VLAN toggle after all fields are populated**
                setTimeout(function() {
                    toggleVlanFields();
                    console.log('VLAN fields toggled after settings population');
                }, 100);
            }

            // Event handlers for web filter settings
            $('#save-web-filter-settings').on('click', function() {
                saveWebFilterSettings();
            });

            // Event handler for saving radio settings including channels
            $('#save-radio-settings').on('click', function() {
                console.log('Save radio settings clicked');
                
                const locationId = getLocationId();
                if (!locationId) {
                    toastr.error('Location ID not found');
                    return;
                }
                
                // Get all radio settings from the form
                const radioSettings = {
                    wifi_country: $('#wifi-country').val(),
                    power_level_2g: $('#power-level-2g').val(),
                    power_level_5g: $('#power-level-5g').val(),
                    channel_width_2g: $('#channel-width-2g').val(),
                    channel_width_5g: $('#channel-width-5g').val(),
                    channel_2g: parseInt($('#channel-2g').val()),
                    channel_5g: parseInt($('#channel-5g').val())
                };
                
                console.log('Saving radio settings:', radioSettings);
                
                // Show loading state
                const $button = $(this);
                const originalText = $button.html();
                $button.html('<i data-feather="loader" class="mr-1"></i> Saving...').prop('disabled', true);
                
                // Get current channel settings to check if they changed
                getCurrentChannelSettings(locationId)
                    .then(function(currentSettings) {
                        console.log('Current settings for radio save:', currentSettings);
                        
                        const currentChannel2g = currentSettings.channel_2g || null;
                        const currentChannel5g = currentSettings.channel_5g || null;
                        
                        // Check if channels have changed
                        const channelsChanged = (currentChannel2g != radioSettings.channel_2g) || (currentChannel5g != radioSettings.channel_5g);
                        
                        console.log('Radio settings - channels changed:', channelsChanged, {
                            current2g: currentChannel2g,
                            new2g: radioSettings.channel_2g,
                            current5g: currentChannel5g,
                            new5g: radioSettings.channel_5g
                        });
                        
                        // Add config version increment flag to radio settings
                        radioSettings.increment_config_version = channelsChanged;
                        radioSettings.updated_from = 'radio_settings';
                        
                        return saveAllRadioSettings(locationId, radioSettings);
                    })
                    .then(function(response) {
                        console.log('Radio settings saved successfully:', response);
                        
                        // Reset button state
                        $button.html(originalText).prop('disabled', false);
                        
                        // Show success message
                        // The /api/locations/{id} endpoint doesn't return config_version_incremented flag
                        // but the backend will increment it automatically when radio settings change
                        toastr.success('Radio settings saved successfully!', 'Settings Saved', {
                            timeOut: 4000,
                            closeButton: true
                        });
                    })
                    .catch(function(error) {
                        console.error('Failed to save radio settings:', error);
                        
                        // Reset button state
                        $button.html(originalText).prop('disabled', false);
                        
                        toastr.error('Failed to save radio settings. Please try again.');
                    });
            });
            
            // Function to save all radio settings
            function saveAllRadioSettings(locationId, settings) {
                return new Promise(function(resolve, reject) {
                    console.log('Saving all radio settings:', settings);
                    
                    // Prepare the data with settings_type for the backend
                    const requestData = {
                        settings_type: 'router',
                        settings: settings
                    };
                    
                    $.ajax({
                        url: `/api/locations/${locationId}`,
                        method: 'PUT',
                        headers: {
                            'Authorization': 'Bearer ' + UserManager.getToken(),
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        data: JSON.stringify(requestData),
                        success: function(response) {
                            console.log('All radio settings saved successfully:', response);
                            resolve(response);
                        },
                        error: function(xhr, status, error) {
                            console.error('Failed to save radio settings:', xhr.responseText);
                            reject(error);
                        }
                    });
                });
            }

            // Apply optimal channels (from scan results)
            $('#save-channels-btn').on('click', function() {
                applyOptimalChannels();
            });
            
            // Function to apply optimal channels from last scan
            function applyOptimalChannels() {
                const scanData = window.lastScanResults;
                
                if (!scanData || (!scanData.optimal_channel_2g && !scanData.optimal_channel_5g)) {
                    toastr.error('No optimal channels available. Please run a scan first.', 'No Scan Data');
                    return;
                }
                
                console.log('Applying optimal channels from scan:', scanData);
                
                const optimal2g = scanData.optimal_channel_2g;
                const optimal5g = scanData.optimal_channel_5g;
                
                // Update form fields to show optimal channels
                if (optimal2g) {
                    $('#channel-2g').val(optimal2g);
                }
                if (optimal5g) {
                    $('#channel-5g').val(optimal5g);
                }
    
                // Save channels with scan data
                saveChannelSettings(optimal2g, optimal5g, true, 'channel_optimization');
            }

            // Channel scan button event handler
            $('#scan-channels-btn').on('click', function() {
                console.log('Channel scan button clicked');
                
                // Populate device info in modal
                const locationId = getLocationId();
                if (locationId) {
                    $('#modal-location-id').text(locationId);
                    
                    // Get device info and scan counter
                    loadDeviceInfoForModal(locationId);
                }
                
                $('#channel-scan-modal').modal('show');
            });

            // Channel scan modal event handlers
            $('#start-scan-btn').on('click', function() {
                console.log('Starting channel scan');
                startChannelScan();
            });

            $('#back-to-scan-btn').on('click', function() {
                console.log('Back to scan button clicked');
                showPreScanView();
            });

            $('#apply-scan-results').on('click', function() {
                console.log('Applying scan results');
                applyScanResults();
            });

            // Clean up polling when modal is closed
            $('#channel-scan-modal').on('hidden.bs.modal', function() {
                console.log('Channel scan modal closed, cleaning up polling');
                if (window.scanPollingInterval) {
                    clearInterval(window.scanPollingInterval);
                    window.scanPollingInterval = null;
                }

                // Reset to pre-scan view for next time
                showPreScanView();
            });

            // Channel scan functions
            function startChannelScan() {
                console.log('Starting real channel scan process');

                const locationId = getLocationId();
                if (!locationId) {
                    toastr.error('Location ID not found');
                    return;
                }

                // Hide pre-scan view and show progress view
                $('#pre-scan-view').hide();
                $('#scan-in-progress-view').show();
                $('#scan-results-view').hide();

                // Reset progress
                $('.progress-bar').css('width', '0%').attr('aria-valuenow', 0);
                $('.timeline-point-indicator').removeClass('timeline-point-primary timeline-point-success');

                // Initialize the first step
                $('#step-initiated-indicator').addClass('timeline-point-primary');

                // Initiate scan via API
                $.ajax({
                    url: `/api/locations/${locationId}/scan/initiate`,
                    method: 'POST',
                    headers: {
                        'Authorization': 'Bearer ' + UserManager.getToken(),
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    success: function(response) {
                        console.log('Scan initiated successfully:', response);

                        if (response.data && response.data.scan_id) {
                            // Store scan ID for polling
                            window.currentScanId = response.data.scan_id;

                            // Display scan ID prominently
                            $('#current-scan-id').text(response.data.scan_id);

                            // Update the next scan ID counter in pre-scan view for future reference
                            $('#modal-scan-counter').text(response.data.scan_counter || response.data.scan_id);
                            $('#modal-next-scan-id').text((response.data.scan_counter || response.data.scan_id) + 1);

                            // Start polling for scan status
                            pollScanStatus(locationId, response.data.scan_id);

                            toastr.success(`Channel scan initiated!`, 'Scan Started', {
                                timeOut: 5000,
                                closeButton: true
                            });
                        } else {
                            console.error('Invalid response format:', response);
                            toastr.error('Failed to initiate scan - invalid response');
                            showPreScanView();
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Failed to initiate scan:', error);
                        handleApiError(xhr, status, error, 'initiating channel scan');
                        showPreScanView();
                    }
                });
            }

            function pollScanStatus(locationId, scanId) {
                console.log('Polling scan status for scan ID:', scanId);

                // Clear any existing polling interval
                if (window.scanPollingInterval) {
                    clearInterval(window.scanPollingInterval);
                }

                window.scanPollingInterval = setInterval(function() {
                    $.ajax({
                        url: `/api/locations/${locationId}/scan/${scanId}/status`,
                        method: 'GET',
                        headers: {
                            'Authorization': 'Bearer ' + UserManager.getToken(),
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        success: function(response) {
                            console.log('Scan status:', response);

                            if (response.data) {
                                const data = response.data;

                                // Update progress bar
                                const progress = data.progress || 0;
                                $('.progress-bar').css('width', progress + '%').attr('aria-valuenow', progress);

                                // Update timeline indicators based on status
                                updateTimelineIndicators(data.status);

                                // Check if scan is completed
                                if (data.is_completed) {
                                    clearInterval(window.scanPollingInterval);
                                    console.log('Scan completed, showing results');
                                    showScanResults(data);
                                } else if (data.is_failed) {
                                    clearInterval(window.scanPollingInterval);
                                    console.log('Scan failed:', data.error_message);
                                    toastr.error('Scan failed: ' + (data.error_message || 'Unknown error'));
                                    showPreScanView();
                                }
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('Failed to get scan status:', error);
                            clearInterval(window.scanPollingInterval);
                            handleApiError(xhr, status, error, 'getting scan status');
                            showPreScanView();
                        }
                    });
                }, 2000); // Poll every 2 seconds
            }

            function updateTimelineIndicators(status) {
                // Reset all indicators
                $('.timeline-point-indicator').removeClass('timeline-point-primary timeline-point-success');

                switch (status) {
                    case 'initiated':
                        $('#step-initiated-indicator').addClass('timeline-point-primary');
                        break;
                    case 'started':
                        $('#step-initiated-indicator').addClass('timeline-point-success');
                        $('#step-started-indicator').addClass('timeline-point-primary');
                        break;
                    case 'scanning_2g':
                        $('#step-initiated-indicator').addClass('timeline-point-success');
                        $('#step-started-indicator').addClass('timeline-point-success');
                        $('#step-2g-indicator').addClass('timeline-point-primary');
                        break;
                    case 'scanning_5g':
                        $('#step-initiated-indicator').addClass('timeline-point-success');
                        $('#step-started-indicator').addClass('timeline-point-success');
                        $('#step-2g-indicator').addClass('timeline-point-success');
                        $('#step-5g-indicator').addClass('timeline-point-primary');
                        break;
                    case 'completed':
                        $('#step-initiated-indicator').addClass('timeline-point-success');
                        $('#step-started-indicator').addClass('timeline-point-success');
                        $('#step-2g-indicator').addClass('timeline-point-success');
                        $('#step-5g-indicator').addClass('timeline-point-success');
                        break;
                }
            }

            function showScanResults(scanData) {
                console.log('Showing scan results with real data:', scanData);
                
                // Hide progress view and show results view
                $('#scan-in-progress-view').hide();
                $('#scan-results-view').show();
                
                // Update result channels with real data
                if (scanData) {
                    const optimal2g = scanData.optimal_channel_2g || 6;
                    const optimal5g = scanData.optimal_channel_5g || 36;
                    
                    $('#result-channel-2g').text(optimal2g);
                    $('#result-channel-5g').text(optimal5g);
                    
                    // Update last scan info
                    $('#last-best-channel-2g').text('Channel ' + optimal2g);
                    $('#last-best-channel-5g').text('Channel ' + optimal5g);
                    
                    if (scanData.completed_at) {
                        $('#last-scan-time').text(new Date(scanData.completed_at).toLocaleString());
                    } else {
                        $('#last-scan-time').text(new Date().toLocaleString());
                    }
                    
                    // Update nearby networks count
                    if (scanData.nearby_networks_2g !== undefined) {
                        $('#nearby-networks-2g').text(scanData.nearby_networks_2g + ' networks');
                    }
                    if (scanData.nearby_networks_5g !== undefined) {
                        $('#nearby-networks-5g').text(scanData.nearby_networks_5g + ' networks');
                    }
                    
                    // Populate nearby networks table with real data
                    populateNearbyNetworksTable(scanData);
                    
                    // Update Channel Optimization display with new scan results
                    updateChannelOptimizationDisplay(scanData);
                    
                    // Store scan results for apply function
                    window.lastScanResults = scanData;
                } else {
                    // Fallback if no data provided
                    const optimal2g = 6;
                    const optimal5g = 36;
                    
                    $('#result-channel-2g').text(optimal2g);
                    $('#result-channel-5g').text(optimal5g);
                    
                    $('#last-best-channel-2g').text('Channel ' + optimal2g);
                    $('#last-best-channel-5g').text('Channel ' + optimal5g);
                    $('#last-scan-time').text(new Date().toLocaleString());
                    
                    // Show fallback data for nearby networks
                    populateNearbyNetworksTable(null);
                }
            }
            
            // Function to populate the nearby networks table with real API data
            function populateNearbyNetworksTable(scanData) {
                console.log('Populating nearby networks table with data:', scanData);
                
                const $tbody = $('#nearby-networks-tbody');
                $tbody.empty();
                
                if (scanData && (scanData.scan_results_2g || scanData.scan_results_5g)) {
                    // Process real API data structure
                    
                    // Group networks by channel from scan results
                    const channelMap = {};
                    
                    // Process 2.4GHz scan results
                    if (scanData.scan_results_2g && Array.isArray(scanData.scan_results_2g)) {
                        scanData.scan_results_2g.forEach(function(network) {
                            const channel = network.channel;
                            const key = `2.4GHz-${channel}`;
                            
                            if (!channelMap[key]) {
                                channelMap[key] = {
                                    band: '2.4 GHz',
                                    channel: channel,
                                    networks: [],
                                    count: 0,
                                    signals: []
                                };
                            }
                            
                            channelMap[key].networks.push(network);
                            channelMap[key].count++;
                            channelMap[key].signals.push(network.signal);
                        });
                    }
                    
                    // Process 5GHz scan results
                    if (scanData.scan_results_5g && Array.isArray(scanData.scan_results_5g)) {
                        scanData.scan_results_5g.forEach(function(network) {
                            const channel = network.channel;
                            const key = `5GHz-${channel}`;
                            
                            if (!channelMap[key]) {
                                channelMap[key] = {
                                    band: '5 GHz',
                                    channel: channel,
                                    networks: [],
                                    count: 0,
                                    signals: []
                                };
                            }
                            
                            channelMap[key].networks.push(network);
                            channelMap[key].count++;
                            channelMap[key].signals.push(network.signal);
                        });
                    }
                    
                    // Add common channels that might not have networks
                    const common2gChannels = [1, 6, 11];
                    const common5gChannels = [36, 40, 44, 48, 149, 153, 157, 161];
                    
                    common2gChannels.forEach(function(channel) {
                        const key = `2.4GHz-${channel}`;
                        if (!channelMap[key]) {
                            channelMap[key] = {
                                band: '2.4 GHz',
                                channel: channel,
                                networks: [],
                                count: 0,
                                signals: []
                            };
                        }
                    });
                    
                    common5gChannels.forEach(function(channel) {
                        const key = `5GHz-${channel}`;
                        if (!channelMap[key]) {
                            channelMap[key] = {
                                band: '5 GHz',
                                channel: channel,
                                networks: [],
                                count: 0,
                                signals: []
                            };
                        }
                    });
                    
                    // Sort channels and create table rows
                    const sortedChannels = Object.values(channelMap).sort(function(a, b) {
                        // Sort by band first (2.4GHz then 5GHz), then by channel number
                        if (a.band !== b.band) {
                            return a.band === '2.4 GHz' ? -1 : 1;
                        }
                        return parseInt(a.channel) - parseInt(b.channel);
                    });
                    
                    sortedChannels.forEach(function(channelData) {
                        const avgSignal = channelData.signals.length > 0 ? 
                            Math.round(channelData.signals.reduce((a, b) => a + b, 0) / channelData.signals.length) + ' dBm' : 
                            'N/A';
                            
                        // Determine interference level based on network count and signal strength
                        let interferenceLevel = 'None';
                        if (channelData.count === 0) {
                            interferenceLevel = 'None';
                        } else if (channelData.count === 1) {
                            interferenceLevel = 'Low';
                        } else if (channelData.count <= 3) {
                            interferenceLevel = 'Medium';
                        } else {
                            interferenceLevel = 'High';
                        }
                        
                        // Check if this is the optimal channel
                        const isOptimal = (channelData.band === '2.4 GHz' && channelData.channel == scanData.optimal_channel_2g) ||
                                        (channelData.band === '5 GHz' && channelData.channel == scanData.optimal_channel_5g);
                        
                        const row = createNetworkTableRow(
                            channelData.band,
                            channelData.channel,
                            channelData.count,
                            avgSignal,
                            interferenceLevel,
                            isOptimal
                        );
                        $tbody.append(row);
                    });
                    
                } else {
                    // Fallback: show sample data structure
                    console.log('No scan data available, showing sample structure');
                    
                    const fallbackData = [
                        { band: '2.4 GHz', channel: 1, networks: 3, signal: '-45 dBm', interference: 'Medium' },
                        { band: '2.4 GHz', channel: 6, networks: 1, signal: '-38 dBm', interference: 'Low' },
                        { band: '2.4 GHz', channel: 11, networks: 2, signal: '-52 dBm', interference: 'Medium' },
                        { band: '5 GHz', channel: 36, networks: 1, signal: '-41 dBm', interference: 'Low' },
                        { band: '5 GHz', channel: 44, networks: 2, signal: '-48 dBm', interference: 'Medium' }
                    ];
                    
                    fallbackData.forEach(function(data) {
                        const row = createNetworkTableRow(
                            data.band,
                            data.channel,
                            data.networks,
                            data.signal,
                            data.interference,
                            false
                        );
                        $tbody.append(row);
                    });
                }
                
                // Show message if no scan data found
                if ($tbody.children().length === 0) {
                    $tbody.append(`
                        <tr>
                            <td colspan="6" class="text-center text-muted">
                                <i data-feather="wifi-off" class="mr-2"></i>
                                No channel scan data available
                            </td>
                        </tr>
                    `);
                }
                
                // Re-initialize feather icons
                if (typeof feather !== 'undefined') {
                    feather.replace();
                }
            }
            
            // Helper function to create a table row for network data
            function createNetworkTableRow(band, channel, networkCount, signalStrength, interferenceLevel, isOptimal = false) {
                const interferenceClass = getInterferenceClass(interferenceLevel);
                const interferenceIcon = getInterferenceIcon(interferenceLevel);
                const rowClass = isOptimal ? 'table-success' : '';
                const networkBadgeClass = networkCount === 0 ? 'badge-light-success' : 'badge-light-info';
                
                let statusBadge = '';
                if (isOptimal) {
                    statusBadge = '<span class="badge badge-success"><i data-feather="star" class="mr-1" style="width: 12px; height: 12px;"></i>Optimal</span>';
                } else if (networkCount === 0) {
                    statusBadge = '<span class="badge badge-light-success"><i data-feather="check" class="mr-1" style="width: 12px; height: 12px;"></i>Available</span>';
                } else if (networkCount >= 4) {
                    statusBadge = '<span class="badge badge-light-danger"><i data-feather="wifi" class="mr-1" style="width: 12px; height: 12px;"></i>Crowded</span>';
                } else if (networkCount >= 2) {
                    statusBadge = '<span class="badge badge-light-warning"><i data-feather="radio" class="mr-1" style="width: 12px; height: 12px;"></i>Busy</span>';
                } else {
                    statusBadge = '<span class="badge badge-light-info"><i data-feather="radio" class="mr-1" style="width: 12px; height: 12px;"></i>In Use</span>';
                }
                
                return `
                    <tr class="${rowClass}">
                        <td><span class="badge badge-light-${band === '2.4 GHz' ? 'primary' : 'success'}">${band}</span></td>
                        <td><strong>${channel}</strong></td>
                        <td>
                            <span class="badge ${networkBadgeClass}">${networkCount} network${networkCount !== 1 ? 's' : ''}</span>
                        </td>
                        <td>${signalStrength}</td>
                        <td>
                            <span class="badge badge-${interferenceClass}">
                                <i data-feather="${interferenceIcon}" class="mr-1" style="width: 12px; height: 12px;"></i>
                                ${interferenceLevel}
                            </span>
                        </td>
                        <td>${statusBadge}</td>
                    </tr>
                `;
            }
            
            // Helper function to get interference badge class
            function getInterferenceClass(level) {
                switch (level.toLowerCase()) {
                    case 'none': return 'light-success';
                    case 'low': return 'light-success';
                    case 'medium': return 'light-warning';
                    case 'high': return 'light-danger';
                    default: return 'light-secondary';
                }
            }
            
            // Helper function to get interference icon
            function getInterferenceIcon(level) {
                switch (level.toLowerCase()) {
                    case 'none': return 'check-circle';
                    case 'low': return 'check-circle';
                    case 'medium': return 'alert-triangle';
                    case 'high': return 'x-circle';
                    default: return 'help-circle';
                }
            }
            
            // Helper function to calculate interference level from signal strength
            function calculateInterferenceLevel(signalStrength) {
                if (typeof signalStrength === 'string') {
                    const dbm = parseInt(signalStrength.replace(/[^\d-]/g, ''));
                    if (dbm > -40) return 'High';
                    if (dbm > -60) return 'Medium';
                    return 'Low';
                }
                return 'Unknown';
            }

            function showPreScanView() {
                console.log('Showing pre-scan view');
                
                // Stop any ongoing polling
                if (window.scanPollingInterval) {
                    clearInterval(window.scanPollingInterval);
                    window.scanPollingInterval = null;
                }
                
                // Show pre-scan view and hide others
                $('#pre-scan-view').show();
                $('#scan-in-progress-view').hide();
                $('#scan-results-view').hide();
                
                // Reset progress and timeline
                $('.progress-bar').css('width', '0%').attr('aria-valuenow', 0);
                $('.timeline-point-indicator').removeClass('timeline-point-primary timeline-point-success');
            }

            function applyScanResults() {
                console.log('Applying scan results');
                
                const newChannel2g = $('#result-channel-2g').text();
                const newChannel5g = $('#result-channel-5g').text();
                const locationId = getLocationId();
                
                if (!locationId) {
                    toastr.error('Location ID not found');
                    return;
                }
                
                // Show loading state
                const $button = $('#apply-scan-results');
                const originalText = $button.html();
                $button.html('<i data-feather="loader" class="mr-1"></i> Applying...').prop('disabled', true);
                
                // Get current channel settings first
                getCurrentChannelSettings(locationId)
                    .then(function(currentSettings) {
                        console.log('Current channel settings:', currentSettings);
                        
                        const currentChannel2g = currentSettings.channel_2g || null;
                        const currentChannel5g = currentSettings.channel_5g || null;
                        
                        // Check if channels have changed
                        const channelsChanged = (currentChannel2g != newChannel2g) || (currentChannel5g != newChannel5g);
                        
                        console.log('Channels changed:', channelsChanged, {
                            current2g: currentChannel2g,
                            new2g: newChannel2g,
                            current5g: currentChannel5g,
                            new5g: newChannel5g
                        });
                        
                        // Save the new channel settings
                        return saveChannelSettings(locationId, newChannel2g, newChannel5g, channelsChanged);
                    })
                    .then(function(response) {
                        console.log('Channel settings saved successfully:', response);
                        
                        // Update the main form with optimal channels
                        $('#channel-2g').val(newChannel2g);
                        $('#channel-5g').val(newChannel5g);
                        
                        // Reset button state
                        $button.html(originalText).prop('disabled', false);
                        
                        // Close modal
                        $('#channel-scan-modal').modal('hide');
                        
                        // Show success message
                        if (response.config_version_incremented) {
                            toastr.success(`Optimal channels applied and saved: 2.4GHz Channel ${newChannel2g}, 5GHz Channel ${newChannel5g}. Config version incremented to ${response.new_config_version}.`, 'Channels Updated', {
                                timeOut: 6000,
                                closeButton: true
                            });
                        } else {
                            toastr.success(`Optimal channels applied: 2.4GHz Channel ${newChannel2g}, 5GHz Channel ${newChannel5g}. No changes detected.`, 'Channels Applied', {
                                timeOut: 4000,
                                closeButton: true
                            });
                        }
                    })
                    .catch(function(error) {
                        console.error('Failed to apply scan results:', error);
                        
                        // Reset button state
                        $button.html(originalText).prop('disabled', false);
                        
                        toastr.error('Failed to save channel settings. Please try again.');
                    });
            }
            
            // Function to get current channel settings from location_settings
            function getCurrentChannelSettings(locationId) {
                return new Promise(function(resolve, reject) {
                    $.ajax({
                        url: `/api/locations/${locationId}/settings`,
                        method: 'GET',
                        headers: {
                            'Authorization': 'Bearer ' + UserManager.getToken(),
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        success: function(response) {
                            console.log('Current location settings response:', response);
                            
                            let settings = {};
                            if (response.data && response.data.settings) {
                                settings = response.data.settings;
                            } else if (response.settings) {
                                settings = response.settings;
                            }
                            
                            resolve({
                                channel_2g: settings.channel_2g || null,
                                channel_5g: settings.channel_5g || null,
                                config_version: settings.config_version || 1
                            });
                        },
                        error: function(xhr, status, error) {
                            console.error('Failed to get current channel settings:', error);
                            // If we can't get current settings, assume no previous channels
                            resolve({
                                channel_2g: null,
                                channel_5g: null,
                                config_version: 1
                            });
                        }
                    });
                });
            }
            
            // Function to save channel settings to location_settings
            function saveChannelSettings(locationId, channel2g, channel5g, shouldIncrementVersion) {
                return new Promise(function(resolve, reject) {
                    const settingsData = {
                        channel_2g: parseInt(channel2g),
                        channel_5g: parseInt(channel5g),
                        increment_config_version: shouldIncrementVersion,
                        updated_from: 'channel_scan'
                    };
                    
                    console.log('Saving channel settings:', settingsData);
                    
                    $.ajax({
                        url: `/api/locations/${locationId}/settings`,
                        method: 'PUT',
                        headers: {
                            'Authorization': 'Bearer ' + UserManager.getToken(),
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        data: JSON.stringify(settingsData),
                        success: function(response) {
                            console.log('Channel settings saved successfully:', response);
                            resolve(response);
                        },
                        error: function(xhr, status, error) {
                            console.error('Failed to save channel settings:', xhr.responseText);
                            reject(error);
                        }
                    });
                });
            }

            // Enable/disable category selector based on main switch
            $('#global-web-filter').on('change', function() {
                const isEnabled = $(this).is(':checked');
                $('#global-filter-categories').prop('disabled', !isEnabled);
                
                if (isEnabled) {
                    $('#global-filter-categories').select2('enable');
                } else {
                    $('#global-filter-categories').select2('disable');
                }
            });
            
            // Function to load last scan results
            function loadLastScanResults() {
                const locationId = getLocationId();
                if (!locationId) {
                    console.log('No location ID found - cannot load last scan results');
                    return;
                }

                console.log('Loading last scan results for location:', locationId);
                
                // First try to get the latest scan results directly
                $.ajax({
                    url: `/api/locations/${locationId}/scan-results/latest`,
                    method: 'GET',
                    headers: {
                        'Authorization': 'Bearer ' + UserManager.getToken(),
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    success: function(response) {
                        console.log('Last scan results loaded:', response);
                        
                        if (response.data) {
                            const scanData = response.data;
                            updateChannelOptimizationDisplay(scanData);
                        } else {
                            console.log('No previous scan results found');
                            updateChannelOptimizationDisplay(null);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.log('Failed to load last scan results from scan-results endpoint, trying alternative:', error);
                        
                        // Fallback: try to get scan history directly
                        $.ajax({
                            url: `/api/locations/${locationId}/scans`,
                            method: 'GET',
                            headers: {
                                'Authorization': 'Bearer ' + UserManager.getToken(),
                                'Content-Type': 'application/json',
                                'Accept': 'application/json'
                            },
                            success: function(scansResponse) {
                                console.log('Scans history loaded:', scansResponse);
                                
                                // Look for the most recent completed scan
                                if (scansResponse.data && scansResponse.data.length > 0) {
                                    // Find the most recent completed scan
                                    const completedScans = scansResponse.data.filter(scan => scan.is_completed && !scan.is_failed);
                                    if (completedScans.length > 0) {
                                        // Sort by completed_at or created_at descending and take the first
                                        completedScans.sort((a, b) => {
                                            const aDate = new Date(a.completed_at || a.created_at);
                                            const bDate = new Date(b.completed_at || b.created_at);
                                            return bDate - aDate;
                                        });
                                        
                                        const latestScan = completedScans[0];
                                        console.log('Found latest completed scan:', latestScan);
                                        updateChannelOptimizationDisplay(latestScan);
                                    } else {
                                        console.log('No completed scans found in scan history');
                                        updateChannelOptimizationDisplay(null);
                                    }
                                } else {
                                    console.log('No scan history found');
                                    updateChannelOptimizationDisplay(null);
                                }
                            },
                            error: function(xhr2, status2, error2) {
                                console.log('Failed to load scan history:', error2);
                                updateChannelOptimizationDisplay(null);
                            }
                        });
                    }
                });
            }
            
            // Function to update the Channel Optimization display
            function updateChannelOptimizationDisplay(scanData) {
                console.log('Updating channel optimization display with:', scanData);
                
                if (scanData && scanData.is_completed && !scanData.is_failed) {
                    // Update optimal channels
                    $('#last-optimal-2g').text(scanData.optimal_channel_2g || '--');
                    $('#last-optimal-5g').text(scanData.optimal_channel_5g || '--');
                    
                    // Update timestamp
                    if (scanData.completed_at) {
                        const scanDate = new Date(scanData.completed_at);
                        const timeAgo = getTimeAgo(scanDate);
                        $('#last-scan-timestamp').text(`Last scan: ${timeAgo}`);
                    } else {
                        $('#last-scan-timestamp').text('Scan completed recently');
                    }
                    
                    // Update status alert
                    $('#scan-status-alert').removeClass('alert-info alert-warning').addClass('alert-success');
                    $('#scan-status-text').html('Optimal channels available from last scan.');
                    
                    // Enable Apply button
                    $('#save-channels-btn').prop('disabled', false);
                    
                    // Update the main channel form fields with optimal values
                    if (scanData.optimal_channel_2g) {
                        $('#channel-2g').val(scanData.optimal_channel_2g);
                    }
                    if (scanData.optimal_channel_5g) {
                        $('#channel-5g').val(scanData.optimal_channel_5g);
                    }
                    
                    // Store scan results globally for other functions to use
                    window.lastScanResults = scanData;
                    
                } else if (scanData && scanData.is_failed) {
                    // Scan failed
                    $('#last-optimal-2g').text('--');
                    $('#last-optimal-5g').text('--');
                    $('#last-scan-timestamp').text('Last scan failed');
                    $('#scan-status-alert').removeClass('alert-info alert-success').addClass('alert-warning');
                    $('#scan-status-text').html('<i data-feather="alert-triangle" class="mr-2"></i>Previous scan failed. Try scanning again.');
                    $('#save-channels-btn').prop('disabled', true);
                    
                } else {
                    // No scan data available
                    $('#last-optimal-2g').text('--');
                    $('#last-optimal-5g').text('--');
                    $('#last-scan-timestamp').text('No scan performed yet');
                    $('#scan-status-alert').removeClass('alert-success alert-warning').addClass('alert-info');
                    $('#scan-status-text').html('<i data-feather="info" class="mr-2"></i>Click Scan to analyze optimal channels.');
                    $('#save-channels-btn').prop('disabled', true);
                }
                
                // Re-initialize feather icons
                if (typeof feather !== 'undefined') {
                    feather.replace();
                }
            }
            
            // Helper function to get time ago string
            function getTimeAgo(date) {
                const now = new Date();
                const diffMs = now - date;
                const diffMins = Math.floor(diffMs / 60000);
                const diffHours = Math.floor(diffMs / 3600000);
                const diffDays = Math.floor(diffMs / 86400000);
                
                if (diffMins < 1) return 'just now';
                if (diffMins < 60) return `${diffMins} minute${diffMins > 1 ? 's' : ''} ago`;
                if (diffHours < 24) return `${diffHours} hour${diffHours > 1 ? 's' : ''} ago`;
                if (diffDays < 7) return `${diffDays} day${diffDays > 1 ? 's' : ''} ago`;
                
                return date.toLocaleDateString() + ' at ' + date.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
            }
            
            // Test API call for debugging
            console.log('Testing API authentication...');
            $.ajax({
                url: '/api/locations',
                method: 'GET',
                headers: {
                    'Authorization': 'Bearer ' + UserManager.getToken(),
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                success: function(response) {
                    console.log('API Test Success:', response);
                },
                error: function(xhr, status, error) {
                    console.log('API Test Failed:', {
                        status: xhr.status,
                        statusText: xhr.statusText,
                        responseText: xhr.responseText,
                        error: error
                    });
                }
            });
            
            // Function to toggle VLAN fields based on VLAN enabled checkbox (make it globally accessible)
            window.toggleVlanFields = function(vlanEnabledParam) {
                const vlanEnabled = vlanEnabledParam !== undefined ? vlanEnabledParam : $('#vlan-enabled').is(':checked');
                console.log('Toggling VLAN fields, enabled:', vlanEnabled);
                
                // Specific VLAN field IDs to target (including modal fields)
                const vlanFields = [
                    '#captive-portal-vlan',
                    '#captive-portal-vlan-tagging',
                    '#password-wifi-vlan',
                    '#password-wifi-vlan-tagging',
                    // Modal specific fields with unique IDs
                    '#captive-portal-vlan-modal',
                    '#captive-portal-vlan-tagging-modal',
                    '#password-wifi-vlan-tagging-modal'
                ];
                
                // Toggle all VLAN setting fields by class and specific IDs
                $('.vlan-setting input, .vlan-setting select').prop('disabled', !vlanEnabled);
                
                // Also target specific VLAN field IDs to ensure they get toggled
                vlanFields.forEach(function(fieldId) {
                    const field = $(fieldId);
                    if (field.length > 0) {
                        field.prop('disabled', !vlanEnabled);
                        console.log(`✅ Toggled ${fieldId}: disabled = ${!vlanEnabled}`);
                    }
                });
                
                // Special handling for fields in visible modals
                $('.modal.show .vlan-setting input, .modal.show .vlan-setting select').prop('disabled', !vlanEnabled);
                
                // Update visual styling for all VLAN settings (including in modals)
                const vlanSettingContainers = $('.vlan-setting, .modal .vlan-setting');
                
                if (vlanEnabled) {
                    vlanSettingContainers.removeClass('text-muted').find('label').removeClass('text-muted');
                    vlanSettingContainers.find('small').removeClass('text-muted').addClass('text-info');
                    console.log('VLAN fields enabled - should be interactive now');
                } else {
                    vlanSettingContainers.addClass('text-muted').find('label').addClass('text-muted');
                    vlanSettingContainers.find('small').removeClass('text-info').addClass('text-muted');
                    
                    // Clear VLAN values when disabled
                    vlanSettingContainers.find('input[type="number"]').val('');
                    vlanSettingContainers.find('select').val('disabled');
                    console.log('VLAN fields disabled and cleared');
                }
                
                // Debug: Log the state of specific fields
                console.log('=== VLAN Field States ===');
                vlanFields.forEach(function(fieldId) {
                    const field = $(fieldId);
                    if (field.length > 0) {
                        console.log(`✅ ${fieldId}: disabled = ${field.prop('disabled')}, value = "${field.val()}"`);
                    } else {
                        console.log(`❌ ${fieldId}: not found in DOM`);
                    }
                });
                
                // Also check if fields are in visible modals
                $('.modal.show').each(function() {
                    const modalId = $(this).attr('id');
                    const modalVlanFields = $(this).find('.vlan-setting input, .vlan-setting select');
                    console.log(`Modal ${modalId} has ${modalVlanFields.length} VLAN fields`);
                    modalVlanFields.each(function() {
                        const fieldId = $(this).attr('id');
                        console.log(`  - Modal field #${fieldId}: disabled = ${$(this).prop('disabled')}`);
                    });
                });
                console.log('=== End VLAN Debug ===');
            };
            
            // Event handler for VLAN enabled checkbox
            $('#vlan-enabled').on('change', function() {
                console.log('VLAN checkbox changed, checked:', $(this).is(':checked'));
                toggleVlanFields();
            });
            
            // Initialize VLAN fields state on page load
            setTimeout(function() {
                console.log('Initializing VLAN fields on page load');
                console.log('VLAN checkbox exists:', $('#vlan-enabled').length > 0);
                console.log('VLAN checkbox initial state:', $('#vlan-enabled').is(':checked'));
                toggleVlanFields();
            }, 500);
            

            
            // Additional debugging: Check if VLAN fields exist and log their initial state
            $(document).ready(function() {
                setTimeout(function() {
                    console.log('=== VLAN Fields Debug ===');
                    const vlanFieldSelectors = [
                        '#captive-portal-vlan',
                        '#captive-portal-vlan-tagging', 
                        '#password-wifi-vlan',
                        '#password-wifi-vlan-tagging'
                    ];
                    
                    vlanFieldSelectors.forEach(function(selector) {
                        const field = $(selector);
                        if (field.length > 0) {
                            console.log(`${selector}: exists, disabled=${field.prop('disabled')}, value="${field.val()}"`);
                        } else {
                            console.log(`${selector}: NOT FOUND`);
                        }
                    });
                    
                    console.log('VLAN setting divs count:', $('.vlan-setting').length);
                    console.log('=== End VLAN Debug ===');
                }, 1000);
            });
            
            // // Password visibility toggle handlers
            // $('#toggle-password').on('click', function() {
            //     const passwordField = $('#password-wifi-password');
            //     const icon = $(this).find('i');
                
            //     if (passwordField.attr('type') === 'password') {
            //         passwordField.attr('type', 'text');
            //         icon.attr('data-feather', 'eye-off');
            //     } else {
            //         passwordField.attr('type', 'password');
            //         icon.attr('data-feather', 'eye');
            //     }
            //     feather.replace();
            // });
            
            $('#toggle-captive-password').on('click', function() {
                const passwordField = $('#captive_portal_password');
                const icon = $(this).find('i');
                
                if (passwordField.attr('type') === 'password') {
                    passwordField.attr('type', 'text');
                    icon.attr('data-feather', 'eye-off');
                } else {
                    passwordField.attr('type', 'password');
                    icon.attr('data-feather', 'eye');
                }
                feather.replace();
            });
            
            $('#toggle-portal-password').on('click', function() {
                const passwordField = $('#portal-shared-password');
                const icon = $(this).find('i');
                
                if (passwordField.attr('type') === 'password') {
                    passwordField.attr('type', 'text');
                    icon.attr('data-feather', 'eye-off');
                } else {
                    passwordField.attr('type', 'password');
                    icon.attr('data-feather', 'eye');
                }
                feather.replace();
            });
            
            // Authentication method change handler for captive portal
            $('#captive-auth-method').on('change', function() {
                const method = $(this).val();
                
                // Hide all auth option sections
                $('.auth-options-section').hide();
                
                // Show relevant section based on method
                switch(method) {
                    case 'password':
                        $('#password-auth-options').show();
                        break;
                    case 'sms':
                        $('#sms-auth-options').show();
                        break;
                    case 'email':
                        $('#email-auth-options').show();
                        break;
                    case 'social':
                        $('#social-auth-options').show();
                        break;
                    default:
                        // click-through - no additional options needed
                        break;
                }
            });
            
            // IP assignment change handler for password WiFi
            $('#password-ip-assignment').on('change', function() {
                const assignment = $(this).val();
                
                // Update display field
                $('#password-wifi-ip-type-display').text(assignment);
                
                if (assignment === 'STATIC') {
                    $('#password-static-fields').removeClass('hidden').show();
                    $('.password-ip-assignment-display_div').removeClass('hidden').show();
                    $('#dhcp-client-message').hide();
                    console.log('Password WiFi set to STATIC - showing IP fields');
                } else {
                    $('#password-static-fields').addClass('hidden').hide();
                    $('.password-ip-assignment-display_div').addClass('hidden').hide();
                    $('#dhcp-client-message').show();
                    console.log('Password WiFi set to ' + assignment + ' - hiding IP fields');
                }
            });
            
            // DHCP server toggle for password WiFi
            $('#password-dhcp-server-toggle').on('change', function() {
                const enabled = $(this).is(':checked');
                
                // Update display field
                $('#password-dhcp-status-display').text(enabled ? 'Enabled' : 'Disabled');
                
                if (enabled) {
                    $('#password-dhcp-server-fields').removeClass('hidden').show();
                } else {
                    $('#password-dhcp-server-fields').addClass('hidden').hide();
                }
                
                console.log('Password WiFi DHCP server status updated to:', enabled ? 'Enabled' : 'Disabled');
            });
            
            // WAN connection type change handler
            $('#wan-connection-type').on('change', function() {
                const connectionType = $(this).val();
                
                // Hide all connection type specific fields
                $('#wan-static-fields').addClass('hidden').hide();
                $('#wan-pppoe-fields').hide();
                
                // Show relevant fields based on connection type
                switch(connectionType) {
                    case 'STATIC':
                        $('#wan-static-fields').removeClass('hidden').show();
                        break;
                    case 'PPPOE':
                        $('#wan-pppoe-fields').show();
                        break;
                    default:
                        // DHCP - no additional fields needed
                        break;
                }
            });
            
            // Initialize form field visibility on page load
            $('#captive-auth-method').trigger('change');
            $('#password-ip-assignment').trigger('change');
            $('#password-dhcp-server-toggle').trigger('change');
            $('#wan-connection-type').trigger('change');
            
            // Auto-update gateway when password WiFi IP changes
            $('#password-ip').on('input', function() {
                const ipAddress = $(this).val();
                $('#password-gateway').val(ipAddress);
                $('#password-gateway-display').text(ipAddress);
                $('#password-ip-display').text(ipAddress);
                console.log('Password WiFi gateway auto-updated to:', ipAddress);
            });
            
            // Auto-update display fields when password WiFi netmask changes
            $('#password-netmask').on('input', function() {
                const netmask = $(this).val();
                $('#password-netmask-display').text(netmask);
                console.log('Password WiFi netmask display updated to:', netmask);
            });
            
            // Auto-update display fields when captive portal IP changes
            $('#captive-portal-ip').on('input', function() {
                const ipAddress = $(this).val();
                $('#captive-ip-display').text(ipAddress);
                console.log('Captive portal IP display updated to:', ipAddress);
            });
            
            // Auto-update display fields when captive portal netmask changes
            $('#captive-portal-netmask').on('input', function() {
                const netmask = $(this).val();
                $('#captive-netmask-display').text(netmask);
                console.log('Captive portal netmask display updated to:', netmask);
            });
            
            // Auto-update display fields when captive portal gateway changes
            $('#captive-portal-gateway').on('input', function() {
                const gateway = $(this).val();
                $('#captive-gateway-display').text(gateway);
                console.log('Captive portal gateway display updated to:', gateway);
            });
            
            // **Modal Event Handlers for VLAN fields**
            // When captive portal modal is shown, ensure VLAN fields are properly toggled
            $('#captive-portal-modal').on('shown.bs.modal', function() {
                console.log('Captive Portal modal opened, checking VLAN fields');
                setTimeout(function() {
                    toggleVlanFields();
                }, 100);
            });
            
            // When password network modal is shown, load current settings and ensure VLAN fields are properly toggled
            $('#password-network-modal').on('shown.bs.modal', function() {
                console.log('Password Network modal opened, loading current settings');
                
                // Load current settings into modal fields
                const locationId = getLocationId();
                if (locationId) {
                    $.ajax({
                        url: `/api/locations/${locationId}/settings`,
                        method: 'GET',
                        headers: {
                            'Authorization': 'Bearer ' + UserManager.getToken(),
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        success: function(response) {
                            console.log('Password network modal: Settings loaded:', response);
                            
                            let settings = null;
                            if (response.settings) {
                                settings = response.settings;
                            } else if (response.data && response.data.settings) {
                                settings = response.data.settings;
                            } else if (response.data) {
                                settings = response.data;
                            }
                            
                            if (settings) {
                                // Populate modal fields with current values
                                if (settings.password_wifi_ip_mode || settings.password_wifi_ip_type) {
                                    $('#password-ip-assignment').val((settings.password_wifi_ip_mode || settings.password_wifi_ip_type).toUpperCase()).trigger('change');
                                }
                                if (settings.password_wifi_ip) {
                                    $('#password-ip').val(settings.password_wifi_ip);
                                    $('#password-gateway').val(settings.password_wifi_ip);
                                }
                                if (settings.password_wifi_netmask) {
                                    $('#password-netmask').val(settings.password_wifi_netmask);
                                }
                                if (settings.password_wifi_dns1) {
                                    $('#password-primary-dns').val(settings.password_wifi_dns1);
                                }
                                if (settings.password_wifi_dns2) {
                                    $('#password-secondary-dns').val(settings.password_wifi_dns2);
                                }
                                if (settings.password_wifi_vlan) {
                                    $('#password-wifi-vlan').val(settings.password_wifi_vlan);
                                }
                                if (settings.password_wifi_vlan_tagging) {
                                    $('#password-wifi-vlan-tagging-modal').val(settings.password_wifi_vlan_tagging);
                                }
                                if (settings.password_wifi_dhcp_enabled !== undefined) {
                                    $('#password-dhcp-server-toggle').prop('checked', settings.password_wifi_dhcp_enabled).trigger('change');
                                }
                                if (settings.password_wifi_dhcp_start) {
                                    $('#password-dhcp-start').val(settings.password_wifi_dhcp_start);
                                }
                                if (settings.password_wifi_dhcp_end) {
                                    $('#password-dhcp-end').val(settings.password_wifi_dhcp_end);
                                }
                                
                                console.log('Password network modal: Fields populated with current settings');
                            }
                        },
                        error: function(xhr, status, error) {
                            console.log('Failed to load settings for password network modal:', error);
                        }
                    });
                }
                
                setTimeout(function() {
                    toggleVlanFields();
                }, 100);
            });
            
            // When any modal with VLAN fields is shown, ensure VLAN fields are properly toggled
            $('.modal').on('shown.bs.modal', function() {
                const modalId = $(this).attr('id');
                if (modalId && (modalId.includes('captive') || modalId.includes('password') || modalId.includes('network'))) {
                    console.log(`Modal ${modalId} opened, checking VLAN fields`);
                    setTimeout(function() {
                        toggleVlanFields();
                    }, 100);
                }
            });
            
            // Handle IP assignment mode changes in password network modal
            $('#password-ip-assignment').on('change', function() {
                const mode = $(this).val();
                console.log('Password IP assignment mode changed to:', mode);
                
                if (mode === 'STATIC') {
                    $('#password-static-fields').removeClass('hidden').show();
                } else {
                    $('#password-static-fields').addClass('hidden').hide();
                }
            });
            
            // Handle DHCP server toggle in password network modal
            $('#password-dhcp-server-toggle').on('change', function() {
                const enabled = $(this).is(':checked');
                console.log('Password DHCP server toggle changed to:', enabled);
                
                if (enabled) {
                    $('#password-dhcp-server-fields').removeClass('hidden').show();
                } else {
                    $('#password-dhcp-server-fields').addClass('hidden').hide();
                }
            });
            
            // Auto-update gateway when IP address changes in password network modal
            $('#password-ip').on('input', function() {
                const ipAddress = $(this).val();
                $('#password-gateway').val(ipAddress);
            });
            
            // MAC address management for captive portal
            // Enhanced MAC filtering handlers for individual blacklist/whitelist management
            
            // Store MAC addresses globally for filtering
            window.macAddresses = {
                captive: [],
                secured: []
            };
            
            // Pagination state for MAC addresses
            window.macPagination = {
                captive: {
                    currentPage: 1,
                    itemsPerPage: 5
                },
                secured: {
                    currentPage: 1,
                    itemsPerPage: 5
                }
            };
            
            // MAC address validation
            function validateMacAddress(macAddress) {
                const macRegex = /^([0-9A-Fa-f]{2}[:-]){5}([0-9A-Fa-f]{2})$/;
                return macRegex.test(macAddress);
            }
            
            // Check if MAC address already exists
            function checkDuplicateMac(macAddress, context) {
                return window.macAddresses[context].some(item => item.mac === macAddress);
            }
            
            // Map radio selection to API scope value
            function mapRadioToScope(radio) {
                switch(radio) {
                    case '2.4GHz':
                        return 'block_24';
                    case '5GHz':
                        return 'block_5';
                    case 'both':
                    default:
                        return 'all';
                }
            }
            
            // Map API scope value to display text
            function mapScopeToDisplay(scope) {
                switch(scope) {
                    case 'block_24':
                        return '2.4GHz';
                    case 'block_5':
                        return '5GHz';
                    case 'all':
                    default:
                        return 'All';
                }
            }
            
            // Add MAC address to the list
            function addMacAddress(macAddress, type, context, radio = 'both') {
                // Normalize MAC address
                macAddress = macAddress.toUpperCase();
                
                // Check if already exists
                if (checkDuplicateMac(macAddress, context)) {
                    toastr.error('This MAC address is already in the list');
                    return false;
                }
                
                // Map radio selection to API scope value
                const scope = mapRadioToScope(radio);
                
                // Add to global storage with scope information
                window.macAddresses[context].push({
                    mac: macAddress,
                    type: type,
                    scope: scope,
                    radio: radio // Keep for display purposes
                });
                
                // Refresh display
                refreshMacDisplay(context);
                
                // Auto-save
                saveMacFilterSettings(context);
                
                const radioText = radio === 'both' ? 'All' : `${radio} radio`;
                toastr.success(`MAC address ${macAddress} added to ${type} on ${radioText}`);
                return true;
            }
            
            // Remove MAC address from the list
            function removeMacAddress(macAddress, context) {
                const index = window.macAddresses[context].findIndex(item => item.mac === macAddress);
                if (index > -1) {
                    const removedItem = window.macAddresses[context].splice(index, 1)[0];
                    refreshMacDisplay(context);
                    saveMacFilterSettings(context);
                    toastr.success(`MAC address ${macAddress} removed from ${removedItem.type}`);
                }
            }
            
            // Refresh the MAC address display based on current filter with pagination
            function refreshMacDisplay(context) {
                const container = context === 'captive' ? 
                    $('#captive-portal .filtered-mac-list') : 
                    $('#secured-wifi .filtered-mac-list');
                const emptyElement = context === 'captive' ? $('#captive-mac-empty') : $('#secured-mac-empty');
                const paginationContainer = context === 'captive' ? $('#captive-mac-pagination') : $('#secured-mac-pagination');
                const paginationList = context === 'captive' ? $('#captive-mac-pagination-list') : $('#secured-mac-pagination-list');
                const viewFilter = context === 'captive' ? $('#captive-mac-view-filter').val() : $('#secured-mac-view-filter').val();
                
                // Clear current display
                container.find('.mac-address-item').remove();
                
                // Filter MAC addresses based on view filter
                let filteredMacs = window.macAddresses[context];
                if (viewFilter === 'blacklisted') {
                    filteredMacs = filteredMacs.filter(item => item.type === 'blacklist');
                } else if (viewFilter === 'whitelisted') {
                    filteredMacs = filteredMacs.filter(item => item.type === 'whitelist');
                }
                
                // Show/hide empty message
                if (filteredMacs.length === 0) {
                    emptyElement.show();
                    paginationContainer.hide();
                } else {
                    emptyElement.hide();
                    
                    // Pagination logic
                    const pagination = window.macPagination[context];
                    const totalPages = Math.ceil(filteredMacs.length / pagination.itemsPerPage);
                    
                    // Reset to page 1 if current page is out of bounds
                    if (pagination.currentPage > totalPages && totalPages > 0) {
                        pagination.currentPage = 1;
                    }
                    
                    // Calculate pagination slice
                    const startIndex = (pagination.currentPage - 1) * pagination.itemsPerPage;
                    const endIndex = startIndex + pagination.itemsPerPage;
                    const paginatedMacs = filteredMacs.slice(startIndex, endIndex);
                    
                    // Add MAC addresses to display (only current page)
                    paginatedMacs.forEach(item => {
                        const badgeClass = item.type === 'whitelist' ? 'badge-success' : 'badge-danger';
                        const iconClass = item.type === 'whitelist' ? 'check-circle' : 'x-circle';
                        
                        // Use scope if available, otherwise fall back to radio, then default to 'all'
                        const scope = item.scope || (item.radio ? mapRadioToScope(item.radio) : 'all');
                        const radioText = mapScopeToDisplay(scope);
                        
                        // Format display text based on type and scope
                        let displayText;
                        if (item.type === 'blacklist') {
                            displayText = `Blacklisted on ${radioText}`;
                        } else {
                            displayText = `Whitelisted on ${radioText}`;
                        }
                        
                        const macItem = `
                            <div class="d-flex justify-content-between align-items-center p-2 border-bottom mac-address-item" data-mac="${item.mac}" data-type="${item.type}" data-scope="${scope}">
                                <div class="d-flex align-items-center flex-wrap">
                                    <i data-feather="${iconClass}" class="mr-2" style="width: 16px; height: 16px;"></i>
                                    <span class="mac-address-text font-weight-medium mr-2">${item.mac}</span>
                                    <span class="badge ${badgeClass}" title="${displayText}">${displayText}</span>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-danger remove-mac" title="Remove MAC address">
                                    <i data-feather="x" style="width: 12px; height: 12px;"></i>
                                </button>
                            </div>
                        `;
                        container.append(macItem);
                    });
                    
                    // Render pagination controls
                    if (totalPages > 1) {
                        renderMacPagination(context, pagination.currentPage, totalPages, paginationList);
                        paginationContainer.show();
                    } else {
                        paginationContainer.hide();
                    }
                }
                
                // Update status
                updateMacStatus(context);
                
                // Re-initialize feather icons
                feather.replace();
            }
            
            // Render pagination controls
            function renderMacPagination(context, currentPage, totalPages, paginationList) {
                paginationList.empty();
                
                // Previous button
                const prevDisabled = currentPage === 1 ? 'disabled' : '';
                const prevPage = currentPage > 1 ? currentPage - 1 : 1;
                paginationList.append(`
                    <li class="page-item ${prevDisabled}">
                        <a class="page-link mac-pagination-link" href="#" data-page="${prevPage}" data-context="${context}">Previous</a>
                    </li>
                `);
                
                // Page numbers
                const maxVisiblePages = 5;
                let startPage = Math.max(1, currentPage - Math.floor(maxVisiblePages / 2));
                let endPage = Math.min(totalPages, startPage + maxVisiblePages - 1);
                
                // Adjust start page if we're near the end
                if (endPage - startPage < maxVisiblePages - 1) {
                    startPage = Math.max(1, endPage - maxVisiblePages + 1);
                }
                
                // First page and ellipsis
                if (startPage > 1) {
                    paginationList.append(`
                        <li class="page-item">
                            <a class="page-link mac-pagination-link" href="#" data-page="1" data-context="${context}">1</a>
                        </li>
                    `);
                    if (startPage > 2) {
                        paginationList.append(`
                            <li class="page-item disabled">
                                <span class="page-link">...</span>
                            </li>
                        `);
                    }
                }
                
                // Page number buttons
                for (let i = startPage; i <= endPage; i++) {
                    const activeClass = i === currentPage ? 'active' : '';
                    paginationList.append(`
                        <li class="page-item ${activeClass}">
                            <a class="page-link mac-pagination-link" href="#" data-page="${i}" data-context="${context}">${i}</a>
                        </li>
                    `);
                }
                
                // Last page and ellipsis
                if (endPage < totalPages) {
                    if (endPage < totalPages - 1) {
                        paginationList.append(`
                            <li class="page-item disabled">
                                <span class="page-link">...</span>
                            </li>
                        `);
                    }
                    paginationList.append(`
                        <li class="page-item">
                            <a class="page-link mac-pagination-link" href="#" data-page="${totalPages}" data-context="${context}">${totalPages}</a>
                        </li>
                    `);
                }
                
                // Next button
                const nextDisabled = currentPage === totalPages ? 'disabled' : '';
                const nextPage = currentPage < totalPages ? currentPage + 1 : totalPages;
                paginationList.append(`
                    <li class="page-item ${nextDisabled}">
                        <a class="page-link mac-pagination-link" href="#" data-page="${nextPage}" data-context="${context}">Next</a>
                    </li>
                `);
            }
            
            // Handle pagination clicks
            $(document).on('click', '.mac-pagination-link', function(e) {
                e.preventDefault();
                const page = parseInt($(this).data('page'));
                const context = $(this).data('context');
                
                if (page && context && !$(this).closest('.page-item').hasClass('disabled')) {
                    window.macPagination[context].currentPage = page;
                    refreshMacDisplay(context);
                }
            });
            
            // Update status display
            function updateMacStatus(context) {
                const statusElement = context === 'captive' ? $('#captive-mac-status small') : $('#secured-mac-status small');
                const total = window.macAddresses[context].length;
                const whitelisted = window.macAddresses[context].filter(item => item.type === 'whitelist').length;
                const blacklisted = window.macAddresses[context].filter(item => item.type === 'blacklist').length;
                
                if (total === 0) {
                    statusElement.text('No MAC addresses added yet');
                } else {
                    statusElement.text(`Total: ${total} | Whitelisted: ${whitelisted} | Blacklisted: ${blacklisted}`);
                }
            }
            
            // Handle view filter changes
            $('#captive-mac-view-filter').on('change', function() {
                // Reset to page 1 when filter changes
                window.macPagination.captive.currentPage = 1;
                refreshMacDisplay('captive');
            });
            
            $('#secured-mac-view-filter').on('change', function() {
                // Reset to page 1 when filter changes
                window.macPagination.secured.currentPage = 1;
                refreshMacDisplay('secured');
            });
            
            // Handle adding MAC addresses for captive portal - show modal first
            $('#captive-add-mac').on('click', function() {
                const macAddress = $('#captive-mac-address').val().trim().toUpperCase();
                const macType = $('#captive-mac-type').val();
                
                if (!macAddress) {
                    toastr.error('Please enter a MAC address');
                    return;
                }
                
                if (!validateMacAddress(macAddress)) {
                    toastr.error('Please enter a valid MAC address format (00:11:22:33:44:55)');
                    return;
                }
                
                // Store MAC address and type for later use
                $('#captive-mac-radio-display').text(macAddress);
                $('#captive-mac-radio-modal').data('mac-address', macAddress);
                $('#captive-mac-radio-modal').data('mac-type', macType);
                
                // Reset radio selection to default (both)
                $('#captive-radio-both').prop('checked', true);
                
                // Show modal
                $('#captive-mac-radio-modal').modal('show');
            });
            
            // Handle confirm button in captive MAC radio modal
            $('#captive-confirm-add-mac').on('click', function() {
                const macAddress = $('#captive-mac-radio-modal').data('mac-address');
                const macType = $('#captive-mac-radio-modal').data('mac-type');
                const radioSelection = $('input[name="captive-radio-selection"]:checked').val();
                
                if (addMacAddress(macAddress, macType, 'captive', radioSelection)) {
                    $('#captive-mac-address').val('');
                    $('#captive-mac-radio-modal').modal('hide');
                }
            });
            
            // Handle adding MAC addresses for secured WiFi - show modal first
            $('#secured-add-mac').on('click', function() {
                const macAddress = $('#secured-mac-address').val().trim().toUpperCase();
                const macType = $('#secured-mac-type').val();
                
                if (!macAddress) {
                    toastr.error('Please enter a MAC address');
                    return;
                }
                
                if (!validateMacAddress(macAddress)) {
                    toastr.error('Please enter a valid MAC address format (00:11:22:33:44:55)');
                    return;
                }
                
                // Store MAC address and type for later use
                $('#secured-mac-radio-display').text(macAddress);
                $('#secured-mac-radio-modal').data('mac-address', macAddress);
                $('#secured-mac-radio-modal').data('mac-type', macType);
                
                // Reset radio selection to default (both)
                $('#secured-radio-both').prop('checked', true);
                
                // Show modal
                $('#secured-mac-radio-modal').modal('show');
            });
            
            // Handle confirm button in secured MAC radio modal
            $('#secured-confirm-add-mac').on('click', function() {
                const macAddress = $('#secured-mac-radio-modal').data('mac-address');
                const macType = $('#secured-mac-radio-modal').data('mac-type');
                const radioSelection = $('input[name="secured-radio-selection"]:checked').val();
                
                if (addMacAddress(macAddress, macType, 'secured', radioSelection)) {
                    $('#secured-mac-address').val('');
                    $('#secured-mac-radio-modal').modal('hide');
                }
            });
            
            // Initialize feather icons when radio selection modals are shown
            $('#captive-mac-radio-modal, #secured-mac-radio-modal').on('shown.bs.modal', function() {
                feather.replace();
            });
            
            // Handle removing MAC addresses
            $(document).on('click', '.remove-mac', function() {
                const macItem = $(this).closest('.mac-address-item');
                const macAddress = macItem.data('mac');
                const context = macItem.closest('#captive-portal').length > 0 ? 'captive' : 'secured';
                
                removeMacAddress(macAddress, context);
            });
            
            // MAC address input validation (format as user types)
            $('#captive-mac-address, #secured-mac-address').on('input', function() {
                let value = $(this).val().replace(/[^0-9A-Fa-f]/g, '');
                let formatted = '';
                
                for (let i = 0; i < value.length && i < 12; i++) {
                    if (i > 0 && i % 2 === 0) {
                        formatted += ':';
                    }
                    formatted += value[i];
                }
                
                $(this).val(formatted.toUpperCase());
            });
            
            // Function to save MAC filter settings
            function saveMacFilterSettings(context) {
                const locationId = getLocationId();
                if (!locationId) {
                    console.error('Location ID not found for MAC filter settings save');
                    return;
                }
                
                // Transform MAC addresses to include scope field for API
                const macAddressesForApi = window.macAddresses[context].map(item => {
                    // Ensure scope is set - use existing scope or map from radio, default to 'all'
                    const scope = item.scope || (item.radio ? mapRadioToScope(item.radio) : 'all');
                    
                    return {
                        mac: item.mac,
                        type: item.type,
                        scope: scope
                    };
                });
                
                // Prepare the new data structure with context-specific field names
                const macFilterData = {};
                if (context === 'captive') {
                    // For captive portal - adds to radcheck, no config version increment
                    macFilterData.captive_mac_filter_list = macAddressesForApi;
                } else if (context === 'secured') {
                    // For password WiFi - adds to list, increments config version
                    macFilterData.secured_mac_filter_list = macAddressesForApi;
                }
                
                // Print the JSON being submitted to API
                const jsonString = JSON.stringify(macFilterData, null, 2);
                console.log('=== MAC Filter JSON Being Submitted to API ===');
                console.log('Context:', context);
                console.log('URL:', `/api/locations/${locationId}/settings`);
                console.log('JSON:', jsonString);
                console.log('=============================================');
                
                // Save to API
                $.ajax({
                    url: `/api/locations/${locationId}/settings`,
                    method: 'PUT',
                    headers: {
                        'Authorization': 'Bearer ' + UserManager.getToken(),
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    data: jsonString,
                    success: function(response) {
                        console.log('MAC filter settings saved successfully:', response);
                        // Don't show success toast for auto-save to avoid spam
                    },
                    error: function(xhr, status, error) {
                        console.error('Failed to save MAC filter settings:', error);
                        toastr.error('Failed to save MAC filter settings. Please try again.');
                    }
                });
            }
            
            // Function to load MAC addresses from settings
            function loadMacAddressesFromSettings(macFilterList, context) {
                if (!macFilterList || !Array.isArray(macFilterList)) {
                    console.log(`No MAC filter list found for ${context}`);
                    return;
                }
                
                // Handle both old format (array of strings) and new format (array of objects)
                const normalizedList = macFilterList.map(item => {
                    if (typeof item === 'string') {
                        // Old format - default to blacklist and all radios for backward compatibility
                        return { 
                            mac: item, 
                            type: 'blacklist', 
                            scope: 'all',
                            radio: 'both' // For display purposes
                        };
                    } else if (item && item.mac && item.type) {
                        // New format - ensure scope property exists
                        const scope = item.scope || 'all';
                        return {
                            mac: item.mac,
                            type: item.type,
                            scope: scope,
                            radio: item.radio || mapScopeToDisplay(scope) // For display purposes
                        };
                    }
                    return null;
                }).filter(item => item !== null);
                
                // Store in global variable
                window.macAddresses[context] = normalizedList;
                
                // Refresh display
                refreshMacDisplay(context);
                
                console.log(`Loaded ${normalizedList.length} MAC addresses for ${context}:`, normalizedList);
            }
            
            // Manual save button handlers for MAC filtering
            $('#save-captive-mac-filter').on('click', function() {
                const $button = $(this);
                const originalText = $button.html();
                
                // Show loading state
                $button.html('<i data-feather="loader" class="mr-1"></i> Saving...').prop('disabled', true);
                
                // Save settings
                saveMacFilterSettings('captive');
                
                // Reset button after short delay
                setTimeout(function() {
                    $button.html(originalText).prop('disabled', false);
                    feather.replace();
                    
                    // Show summary in success message
                    const total = window.macAddresses.captive.length;
                    const whitelisted = window.macAddresses.captive.filter(item => item.type === 'whitelist').length;
                    const blacklisted = window.macAddresses.captive.filter(item => item.type === 'blacklist').length;
                    toastr.success(`Captive Portal MAC filter saved! Total: ${total} (${whitelisted} whitelisted, ${blacklisted} blacklisted)`);
                }, 1000);
            });
            
            $('#save-secured-mac-filter').on('click', function() {
                const $button = $(this);
                const originalText = $button.html();
                
                // Show loading state
                $button.html('<i data-feather="loader" class="mr-1"></i> Saving...').prop('disabled', true);
                
                // Save settings
                saveMacFilterSettings('secured');
                
                // Reset button after short delay
                setTimeout(function() {
                    $button.html(originalText).prop('disabled', false);
                    feather.replace();
                    
                    // Show summary in success message
                    const total = window.macAddresses.secured.length;
                    const blacklisted = window.macAddresses.secured.filter(item => item.type === 'blacklist').length;
                    toastr.success(`Secured WiFi MAC filter saved! Total: ${total} (${blacklisted} blacklisted)`);
                }, 1000);
            });
            
            // **Fix for Password WiFi Save Handler**
            // Override the external JS handler to exclude problematic fields
            // $('.save-password-network').off('click').on('click', function(e) {
            //     e.preventDefault();
            //     console.log('Password WiFi save clicked - using fixed handler');
                
            //     const locationId = getLocationId();
            //     if (!locationId) {
            //         toastr.error('Location ID not found');
            //         return;
            //     }
                
            //     // Determine if we're in a modal context
            //     const isModal = $(e.target).closest('.modal').length > 0;
            //     const modalId = isModal ? $(e.target).closest('.modal').attr('id') : null;
                
            //     console.log('Password WiFi save context:', isModal ? 'Modal: ' + modalId : 'Main form');
                
            //     // Collect form data - EXCLUDING the problematic password_wifi_gateway field
            //     const passwordWifiData = {
            //         password_wifi_ssid: $('#password-wifi-ssid').val(),
            //         password_wifi_password: $('#password-wifi-password').val(),
            //         password_wifi_security: $('#password-wifi-security').val(),
            //         password_wifi_cipher_suites: $('#password_wifi_cipher_suites').val(),
            //         password_wifi_ip_mode: $('#password-ip-assignment').val(),
            //         password_wifi_ip: $('#password-ip').val(),
            //         password_wifi_netmask: $('#password-netmask').val(),
            //         password_wifi_dns1: $('#password-primary-dns').val(),
            //         password_wifi_dns2: $('#password-secondary-dns').val(),
            //         password_wifi_vlan: $('#password-wifi-vlan').val(),
            //         // Use the correct VLAN tagging field based on context
            //         password_wifi_vlan_tagging: isModal && modalId === 'password-network-modal' 
            //             ? $('#password-wifi-vlan-tagging-modal').val() 
            //             : $('#password-wifi-vlan-tagging').val(),
            //         password_wifi_dhcp_enabled: $('#password-dhcp-server-toggle').is(':checked'),
            //         password_wifi_dhcp_start: $('#password-dhcp-start').val(),
            //         password_wifi_dhcp_end: $('#password-dhcp-end').val()
            //         // NOTE: Intentionally NOT including password_wifi_gateway since it doesn't exist in DB
            //     };
                
            //     console.log('Saving password WiFi data (without gateway):', passwordWifiData);
                
            //     // Show loading state
            //     const $button = $(this);
            //     const originalText = $button.html();
            //     $button.html('<i data-feather="loader" class="mr-1"></i> Saving...').prop('disabled', true);
                
            //     // Save to API
            //     $.ajax({
            //         url: `/api/locations/${locationId}`,
            //         method: 'PUT',
            //         headers: {
            //             'Authorization': 'Bearer ' + UserManager.getToken(),
            //             'Content-Type': 'application/json',
            //             'Accept': 'application/json'
            //         },
            //         data: JSON.stringify({
            //             settings_type: 'password_network',
            //             settings: passwordWifiData
            //         }),
            //         success: function(response) {
            //             console.log('Password WiFi settings saved successfully:', response);
                        
            //             // Reset button state
            //             $button.html(originalText).prop('disabled', false);
                        
            //             // Close modal if this was from a modal
            //             if ($(e.target).closest('.modal').length > 0) {
            //                 $(e.target).closest('.modal').modal('hide');
            //             }
                        
            //             // Show success message
            //             toastr.success('Password WiFi settings saved successfully!');
                        
            //             // Reload device data to verify the update
            //             setTimeout(function() {
            //                 loadDeviceSettings();
            //             }, 1000);
            //         },
            //         error: function(xhr, status, error) {
            //             // Reset button state
            //             $button.html(originalText).prop('disabled', false);
                        
            //             // Handle API error
            //             handleApiError(xhr, status, error, 'saving password WiFi settings');
            //         }
            //                     });
            // });
            
            // **Fix for Captive Portal Save Handler**
            // Override any external handlers to ensure we only send valid fields and preserve button styling
            // $('.save-captive-portal').off('click').on('click.fixed', function(e) {
            // $('.save-captive-portal').on('click', function(e) {
            //     e.preventDefault();
            //     // alert('Captive portal save clicked - using fixed handler');
            //     // Get button reference but don't change appearance
            //     const $button = $(this);
                
            //     const modal = $('#captive-portal-modal');
            //     const modalContext = $(this).closest('.modal');

            //     // Use the CORRECT field IDs from the captive-portal-modal that actually opens
            //     const formData = {
            //         ip: $('#captive-portal-ip').val(),
            //         netmask: $('#captive-portal-netmask').val(),
            //         gateway: $('#captive-portal-gateway').val(),
            //         vlan: $('#captive-portal-vlan-modal').val(),
            //         vlan_tagging: $('#captive-portal-vlan-tagging-modal').val()
            //     };
                
                
            //     const alternativeData = {
            //         ip_by_modal_context: modalContext.find('#captive-portal-ip').val(),
            //         ip_by_document: $(document).find('#captive-portal-ip').val(),
            //         ip_by_attribute: $('input[id="captive-portal-ip"]').val(),
            //         // Also check the wrong IDs we were using before
            //         wrong_ip_field: $('#captive-portal-ip-modal').val()
            //     };
            //     console.log('Alternative field selection results:', alternativeData);
                
            //     const locationId = getLocationId();
            //     if (!locationId) {
            //         toastr.error('Location ID not found');
            //         return;
            //     }
                
            //     // Determine if we're in a modal context
            //     const isModal = $(e.target).closest('.modal').length > 0;
            //     const modalId = isModal ? $(e.target).closest('.modal').attr('id') : null;

            //     // Check if fields are being cleared by testing their actual DOM state
            //     console.log('All modal form fields DOM state:', {
            //         ip_element: $('#captive-portal-ip-modal')[0],
            //         netmask_element: $('#captive-portal-netmask-modal')[0],
            //         gateway_element: $('#captive-portal-gateway-modal')[0],
            //         dns1_element: $('#captive-portal-dns1-modal')[0],
            //         dns2_element: $('#captive-portal-dns2-modal')[0]
            //     });

            //     const captivePortalData = {
            //         // Network settings (from modal or form)
            //         captive_portal_ip: formData.ip,
            //         captive_portal_netmask: formData.netmask,
            //         captive_portal_gateway: formData.gateway,
            //         captive_portal_dns1: '8.8.8.8', // Default DNS since not in this modal
            //         captive_portal_dns2: '1.1.1.1', // Default DNS since not in this modal
            //         captive_portal_vlan: formData.vlan,
            //         captive_portal_vlan_tagging: formData.vlan_tagging,
                    
            //         // **BANDWIDTH LIMITS FROM MAIN TAB**
            //         download_limit: $('#captive-download-limit').val(),
            //         upload_limit: $('#captive-upload-limit').val(),
            //         captive_download_limit: $('#captive-download-limit').val(),
            //         captive_upload_limit: $('#captive-upload-limit').val(),
                    
            //         // **OTHER CAPTIVE PORTAL SETTINGS FROM MAIN TAB**
            //         captive_portal_ssid: $('#captive-portal-ssid').val(),
            //         captive_portal_visible: $('#captive-portal-visible').val(),
            //         captive_auth_method: $('#captive-auth-method').val(),
            //         captive_portal_password: $('#captive_portal_password').val(),
            //         session_timeout: $('#captive-session-timeout').val(),
            //         idle_timeout: $('#captive-idle-timeout').val(),
            //         captive_portal_redirect: $('#captive-portal-redirect').val()
            //     };

            //     // $button.html('<i data-feather="loader" class="mr-1"></i> Saving...').prop('disabled', true);
                
            //     // Save to API using the correct endpoint and format
            //     $.ajax({
            //         url: `/api/locations/${locationId}`,
            //         method: 'PUT',
            //         headers: {
            //             'Authorization': 'Bearer ' + UserManager.getToken(),
            //             'Content-Type': 'application/json',
            //             'Accept': 'application/json'
            //         },
            //         data: JSON.stringify({
            //             settings_type: 'captive_portal',
            //             settings: captivePortalData
            //         }),
            //         success: function(response) {
            //             console.log('=== CAPTIVE PORTAL SAVE SUCCESS ===');
            //             console.log('Response:', response);
            //             console.log('Response type:', typeof response);
            //             console.log('Response success:', response.success);
            //             console.log('Response message:', response.message);
            //             alert('Captive portal settings saved successfully!');

            //             // Button appearance remains unchanged
                        
            //             // Close modal if this was from a modal
            //             if ($(e.target).closest('.modal').length > 0) {
            //                 $(e.target).closest('.modal').modal('hide');
            //             }

            //             // Show success message
            //             toastr.success('Captive portal settings saved successfully!');
                        
            //             // Reload device data to verify the update
            //             setTimeout(function() {
            //                 console.log('Reloading device settings after captive portal save...');
            //                 console.log('Previous captive portal settings before reload:', window.deviceSettings?.settings ? {
            //                     captive_portal_ip: window.deviceSettings.settings.captive_portal_ip,
            //                     captive_portal_netmask: window.deviceSettings.settings.captive_portal_netmask,
            //                     captive_portal_gateway: window.deviceSettings.settings.captive_portal_gateway,
            //                     captive_portal_dns1: window.deviceSettings.settings.captive_portal_dns1,
            //                     captive_portal_dns2: window.deviceSettings.settings.captive_portal_dns2
            //                 } : 'No settings available');
            //                 loadDeviceSettings();
            //             }, 1000);
            //         },
            //         error: function(xhr, status, error) {
            //             // Button appearance remains unchanged

            //             // Handle API error
            //             handleApiError(xhr, status, error, 'saving captive portal settings');
            //         }
            //     });
            // });
            
            // **Fix for WAN Settings Save Handler**
            // Override any external handlers to ensure we only send valid fields
            $('.save-wan-settings').off('click').on('click', function(e) {
                e.preventDefault();
                console.log('WAN settings save clicked - using fixed handler');
                
                const locationId = getLocationId();
                if (!locationId) {
                    toastr.error('Location ID not found');
                    return;
                }
                
                // Collect form data - only including fields that exist in database
                const wanData = {
                    wan_connection_type: $('#wan-connection-type').val(),
                    wan_ip_address: $('#wan-ip-address').val(),
                    wan_netmask: $('#wan-netmask').val(),
                    wan_gateway: $('#wan-gateway').val(),
                    wan_primary_dns: $('#wan-primary-dns').val(),
                    wan_secondary_dns: $('#wan-secondary-dns').val(),
                    wan_pppoe_username: $('#wan-pppoe-username').val(),
                    wan_pppoe_password: $('#wan-pppoe-password').val(),
                    wan_pppoe_service_name: $('#wan-pppoe-service-name').val()
                };
                
                console.log('Saving WAN data:', wanData);
                
                // Show loading state
                const $button = $(this);
                const originalText = $button.html();
                $button.html('<i data-feather="loader" class="mr-1"></i> Saving...').prop('disabled', true);
                
                // Save to API
                $.ajax({
                    url: `/api/locations/${locationId}/settings`,
                    method: 'PUT',
                    headers: {
                        'Authorization': 'Bearer ' + UserManager.getToken(),
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    data: JSON.stringify(wanData),
                    success: function(response) {
                        console.log('WAN settings saved successfully:', response);
                        
                        // Reset button state
                        $button.html(originalText).prop('disabled', false);
                        
                        // Close modal if this was from a modal
                        if ($(e.target).closest('.modal').length > 0) {
                            $(e.target).closest('.modal').modal('hide');
                        }
                        
                        // Show success message
                        toastr.success('WAN settings saved successfully!');
                        
                        // Update display
                        $('#wan-type-display').text(wanData.wan_connection_type);
                        if (wanData.wan_connection_type === 'STATIC') {
                            $('#wan-ip-display').text(wanData.wan_ip_address);
                            $('#wan-subnet-display').text(wanData.wan_netmask);
                            $('#wan-gateway-display').text(wanData.wan_gateway);
                            $('#wan-dns1-display').text(wanData.wan_primary_dns);
                            $('.wan-static-ip-display_div').removeClass('hidden').show();
                            $('.wan-pppoe-display_div').addClass('hidden').hide();
                        } else if (wanData.wan_connection_type === 'PPPOE') {
                            $('#wan-pppoe-username').text(wanData.wan_pppoe_username);
                            $('#wan-pppoe-service-name').text(wanData.wan_pppoe_service_name);
                            $('.wan-pppoe-display_div').removeClass('hidden').show();
                            $('.wan-static-ip-display_div').addClass('hidden').hide();
                        } else {
                            $('.wan-static-ip-display_div').addClass('hidden').hide();
                            $('.wan-pppoe-display_div').addClass('hidden').hide();
                        }
                        
                        // Reload device data to verify the update
                        setTimeout(function() {
                            loadDeviceSettings();
                        }, 1000);
                    },
                    error: function(xhr, status, error) {
                        // Reset button state
                        $button.html(originalText).prop('disabled', false);
                        
                        // Handle API error
                        handleApiError(xhr, status, error, 'saving WAN settings');
                    }
                });
            });
            
            // Debug: Test all possible scan endpoints
            const locationId = getLocationId();
            if (locationId) {
                console.log('=== DEBUGGING SCAN ENDPOINTS FOR LOCATION:', locationId, '===');
                
                // Test 1: scan-results/latest
                $.ajax({
                    url: `/api/locations/${locationId}/scan-results/latest`,
                    method: 'GET',
                    headers: {
                        'Authorization': 'Bearer ' + UserManager.getToken(),
                        'Accept': 'application/json'
                    },
                    success: function(response) {
                        console.log('✅ /scan-results/latest SUCCESS:', response);
                        if (response.data) {
                            console.log('🎯 Found scan data in latest endpoint, testing update...');
                            updateChannelOptimizationDisplay(response.data);
                        }
                    },
                    error: function(xhr) {
                        console.log('❌ /scan-results/latest FAILED:', xhr.status, xhr.responseText);
                    }
                });
                
                // Test 2: scans endpoint
                $.ajax({
                    url: `/api/locations/${locationId}/scans`,
                    method: 'GET',
                    headers: {
                        'Authorization': 'Bearer ' + UserManager.getToken(),
                        'Accept': 'application/json'
                    },
                    success: function(response) {
                        console.log('✅ /scans SUCCESS:', response);
                        if (response.data && response.data.length > 0) {
                            console.log('🎯 Found scan history, testing with latest completed scan...');
                            const completedScans = response.data.filter(scan => scan.is_completed && !scan.is_failed);
                            if (completedScans.length > 0) {
                                completedScans.sort((a, b) => {
                                    const aDate = new Date(a.completed_at || a.created_at);
                                    const bDate = new Date(b.completed_at || b.created_at);
                                    return bDate - aDate;
                                });
                                console.log('📊 Testing with latest scan:', completedScans[0]);
                                updateChannelOptimizationDisplay(completedScans[0]);
                            }
                        }
                    },
                    error: function(xhr) {
                        console.log('❌ /scans FAILED:', xhr.status, xhr.responseText);
                    }
                });
                
                console.log('=== END DEBUGGING ===');
            }

            // MAC Address Edit Modal functionality
            $('#edit-mac-btn').on('click', function() {
                const currentMac = $('.router_mac_address_header').text();
                $('#current-mac-display').text(currentMac);
                $('#mac-address-input').val(currentMac !== 'Not Available' ? currentMac : '');
                $('#mac-address-edit-modal').modal('show');
            });

            // MAC Address validation and formatting
            $('#mac-address-input').on('input', function() {
                let value = $(this).val().replace(/[^0-9A-Fa-f:-]/g, '');
                
                // Remove existing delimiters and clean up
                let cleanValue = value.replace(/[:-]/g, '');
                
                // Format with hyphens
                if (cleanValue.length > 2) {
                    cleanValue = cleanValue.match(/.{1,2}/g).join('-');
                }
                
                // Limit to 17 characters (XX-XX-XX-XX-XX-XX)
                if (cleanValue.length > 17) {
                    cleanValue = cleanValue.substring(0, 17);
                }
                
                $(this).val(cleanValue.toUpperCase());
            });

            // Save MAC Address
            $('#save-mac-address-btn').on('click', function() {
                const newMacAddress = $('#mac-address-input').val().trim();
                const locationId = getLocationId();
                
                if (!newMacAddress) {
                    toastr.error('Please enter a MAC address');
                    return;
                }
                
                if (!isValidMacAddress(newMacAddress)) {
                    toastr.error('Please enter a valid MAC address in format XX-XX-XX-XX-XX-XX');
                    return;
                }
                
                if (!locationId) {
                    toastr.error('Location ID not found');
                    return;
                }
                
                // Disable button and show loading state
                const $btn = $(this);
                const originalText = $btn.html();
                $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i>Saving...');
                
                // Make API call to update MAC address
                $.ajax({
                    url: '/api/locations/' + locationId + '/update-mac-address',
                    method: 'POST',
                    headers: {
                        'Authorization': 'Bearer ' + UserManager.getToken(),
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    data: JSON.stringify({
                        mac_address: newMacAddress
                    }),
                    success: function(response) {
                        console.log('MAC address updated successfully:', response);
                        
                        // Update all MAC address displays with colon format for display
                        $('.router_mac_address_header').text(newMacAddress);
                        $('.router_mac_address').text(newMacAddress);
                        
                        // Update device data with the actual stored format (hyphens)
                        if (window.currentDeviceData && response.data && response.data.device) {
                            window.currentDeviceData.mac_address = response.data.device.mac_address;
                        }
                        
                        // Close modal
                        $('#mac-address-edit-modal').modal('hide');
                        
                        toastr.success('MAC address updated successfully');
                    },
                    error: function(xhr, status, error) {
                        console.error('Error updating MAC address:', xhr.responseText);
                        handleApiError(xhr, status, error, 'updating MAC address');
                    },
                    complete: function() {
                        // Re-enable button and restore original text
                        $btn.prop('disabled', false).html(originalText);
                        
                        // Re-render icons
                        if (typeof feather !== 'undefined') {
                            feather.replace();
                        }
                    }
                });
            });

            // MAC Address validation function
            function isValidMacAddress(mac) {
                const macRegex = /^([0-9A-Fa-f]{2}[-]){5}([0-9A-Fa-f]{2})$/;
                return macRegex.test(mac);
            }

            // Helper function to convert MAC address format for display (hyphens to colons)
            function formatMacAddressForDisplay(mac) {
                if (!mac || mac === 'Not Available') {
                    return mac;
                }
                return mac.replace(/-/g, ':');
            }

            // Reset modal when closed
            $('#mac-address-edit-modal').on('hidden.bs.modal', function() {
                $('#mac-address-input').val('');
                $('#current-mac-display').text('-');
            });

            // Handle captive portal modal open (CORRECT modal ID)
            $('#captive-portal-modal').on('shown.bs.modal', function() {
                console.log('=== CAPTIVE PORTAL MODAL OPENED ===');
                console.log('Modal open timestamp:', new Date().toLocaleTimeString());
                
                // Check if fields exist before trying to populate them (CORRECT field IDs)
                console.log('Field existence check:', {
                    ip_field: $('#captive-portal-ip').length,
                    netmask_field: $('#captive-portal-netmask').length,
                    gateway_field: $('#captive-portal-gateway').length,
                    vlan_field: $('#captive-portal-vlan-modal').length,
                    vlan_tagging_field: $('#captive-portal-vlan-tagging-modal').length
                });
                
                // Check for duplicate IDs that might be causing issues
                console.log('=== CHECKING FOR DUPLICATE IDs ===');
                const allIpFields = $('[id="captive-portal-ip"]');
                console.log('Number of elements with captive-portal-ip ID:', allIpFields.length);
                if (allIpFields.length > 1) {
                    console.warn('FOUND DUPLICATE IDs! This could be the problem.');
                    allIpFields.each(function(index) {
                        console.log(`IP field ${index}:`, this, 'value:', $(this).val());
                    });
                }
                
                // Populate modal fields with current settings or defaults
                if (window.deviceSettings && window.deviceSettings.settings) {
                    const settings = window.deviceSettings.settings;
                    
                    console.log('Available settings:', settings);
                    console.log('Captive portal specific settings:', {
                        captive_portal_ip: settings.captive_portal_ip,
                        captive_portal_netmask: settings.captive_portal_netmask,
                        captive_portal_gateway: settings.captive_portal_gateway,
                        captive_portal_dns1: settings.captive_portal_dns1,
                        captive_portal_dns2: settings.captive_portal_dns2,
                        captive_portal_vlan: settings.captive_portal_vlan,
                        captive_portal_vlan_tagging: settings.captive_portal_vlan_tagging
                    });
                    
                    // Only set values if they exist in the settings, otherwise use defaults (CORRECT field IDs)
                    console.log('Setting IP field to:', settings.captive_portal_ip || '192.168.10.1');
                    $('#captive-portal-ip').val(settings.captive_portal_ip || '192.168.10.1');
                    $('#captive-portal-netmask').val(settings.captive_portal_netmask || '255.255.255.0');
                    $('#captive-portal-gateway').val(settings.captive_portal_gateway || '192.168.10.1');
                    $('#captive-portal-vlan-modal').val(settings.captive_portal_vlan || '');
                    $('#captive-portal-vlan-tagging-modal').val(settings.captive_portal_vlan_tagging || 'disabled');
                    
                    console.log('Modal fields populated with values (CORRECT IDs):', {
                        ip: $('#captive-portal-ip').val(),
                        netmask: $('#captive-portal-netmask').val(),
                        gateway: $('#captive-portal-gateway').val(),
                        vlan: $('#captive-portal-vlan-modal').val(),
                        vlan_tagging: $('#captive-portal-vlan-tagging-modal').val()
                    });
                } else {
                    console.log('No device settings available, using defaults (CORRECT field IDs)');
                    // Use defaults when no settings available
                    $('#captive-portal-ip').val('192.168.10.1');
                    $('#captive-portal-netmask').val('255.255.255.0');
                    $('#captive-portal-gateway').val('192.168.10.1');
                    $('#captive-portal-vlan-modal').val('');
                    $('#captive-portal-vlan-tagging-modal').val('disabled');
                }

                // Add comprehensive field tracking
                const trackFieldChange = function(fieldId, fieldName) {
                    $(fieldId).off('input.debug change.debug').on('input.debug change.debug', function() {
                        console.log(`${fieldName} field changed to: "${$(this).val()}" at ${new Date().toLocaleTimeString()}`);
                    });
                    
                    // Track when field gets cleared
                    const originalVal = $(fieldId).val;
                    $(fieldId).val = function(value) {
                        if (arguments.length === 0) {
                            return originalVal.call(this);
                        } else {
                            console.log(`${fieldName} field .val() called with: "${value}" at ${new Date().toLocaleTimeString()}`);
                            const stack = new Error().stack;
                            console.log('Call stack:', stack);
                            return originalVal.call(this, value);
                        }
                    };
                };

                // Track fields using CORRECT field IDs
                trackFieldChange('#captive-portal-ip', 'IP');
                trackFieldChange('#captive-portal-netmask', 'Netmask');
                trackFieldChange('#captive-portal-gateway', 'Gateway');
                trackFieldChange('#captive-portal-vlan-modal', 'VLAN');
                trackFieldChange('#captive-portal-vlan-tagging-modal', 'VLAN_Tagging');

                // Check for any other event handlers that might be interfering
                setTimeout(function() {
                    console.log('=== CHECKING FOR OTHER EVENT HANDLERS ===');
                    const ipField = $('#captive-portal-ip')[0];
                    if (ipField) {
                        console.log('IP field event handlers:', $._data(ipField, 'events'));
                    }
                    console.log('Modal event handlers:', $._data($('#captive-portal-modal')[0], 'events'));
                }, 100);
            });

            // Handle captive portal modal close (CORRECT modal ID)
            $('#captive-portal-modal').on('hidden.bs.modal', function() {
                console.log('=== CAPTIVE PORTAL MODAL CLOSED ===');
                console.log('Modal close timestamp:', new Date().toLocaleTimeString());
                // TEMPORARILY DISABLED: Clear modal fields when closed
                // $('#captive-portal-ip-modal').val('');
                // $('#captive-portal-netmask-modal').val('');
                // $('#captive-portal-gateway-modal').val('');
                // $('#captive-portal-dns1-modal').val('');
                // $('#captive-portal-dns2-modal').val('');
                // $('#captive-portal-vlan-modal').val('');
                // $('#captive-portal-vlan-tagging-modal').val('disabled');
                console.log('Field clearing DISABLED for debugging');
            });

            // Handle Guest Users link click
            $('#guest-users-link').on('click', function(e) {
                e.preventDefault();
                const locationId = getLocationId();
                if (locationId) {
                    window.location.href = '/en/locations/' + locationId + '/guests';
                } else {
                    toastr.error('Location ID not found');
                }
            });

            // ==============================================
            // QR CODE FUNCTIONALITY
            // ==============================================
            
            let qrCode = null;
            
            // Generate QR code when modal is shown
            $('#ssid-qr-modal').on('show.bs.modal', function() {
                const ssid = $('#captive-portal-ssid').val() || 'Guest WiFi';
                
                // Update SSID display in modal
                $('#qr-ssid-display').text(ssid);
                
                // Clear previous QR code
                $('#qr-code-container').empty();
                
                // Generate WiFi QR code format: WIFI:T:WPA;S:SSID;P:password;H:false;;
                // For open network: WIFI:T:nopass;S:SSID;;
                const wifiString = `WIFI:T:nopass;S:${ssid};;`;
                
                // Create new QR code
                qrCode = new QRCode(document.getElementById('qr-code-container'), {
                    text: wifiString,
                    width: 256,
                    height: 256,
                    colorDark: '#000000',
                    colorLight: '#ffffff',
                    correctLevel: QRCode.CorrectLevel.H
                });
            });
            
            // Clean up QR code when modal is hidden
            $('#ssid-qr-modal').on('hidden.bs.modal', function() {
                $('#qr-code-container').empty();
                qrCode = null;
            });
            
            // Download QR code as image
            $('#download-qr-btn').on('click', function() {
                const canvas = $('#qr-code-container canvas')[0];
                if (canvas) {
                    const ssid = $('#captive-portal-ssid').val() || 'Guest WiFi';
                    const url = canvas.toDataURL('image/png');
                    const link = document.createElement('a');
                    link.download = `wifi-qr-${ssid.replace(/[^a-z0-9]/gi, '_').toLowerCase()}.png`;
                    link.href = url;
                    link.click();
                    toastr.success('QR Code downloaded successfully');
                } else {
                    toastr.error('Failed to download QR code');
                }
            });

            // ==============================================
            // PASSWORD WIFI QR CODE FUNCTIONALITY
            // ==============================================
            
            let passwordQrCode = null;
            
            // Generate QR code for password-protected WiFi when modal is shown
            $('#password-ssid-qr-modal').on('show.bs.modal', function() {
                const ssid = $('#password-wifi-ssid').val() || 'Home WiFi';
                const password = $('#password-wifi-password').val() || '';
                
                // Update SSID display in modal
                $('#password-qr-ssid-display').text(ssid);
                
                // Clear previous QR code
                $('#password-qr-code-container').empty();
                
                // Generate WiFi QR code format with password
                // WIFI:T:WPA;S:SSID;P:password;H:false;;
                let wifiString;
                if (password) {
                    wifiString = `WIFI:T:WPA;S:${ssid};P:${password};;`;
                } else {
                    wifiString = `WIFI:T:nopass;S:${ssid};;`;
                }
                
                // Create new QR code
                passwordQrCode = new QRCode(document.getElementById('password-qr-code-container'), {
                    text: wifiString,
                    width: 256,
                    height: 256,
                    colorDark: '#000000',
                    colorLight: '#ffffff',
                    correctLevel: QRCode.CorrectLevel.H
                });
            });
            
            // Clean up QR code when modal is hidden
            $('#password-ssid-qr-modal').on('hidden.bs.modal', function() {
                $('#password-qr-code-container').empty();
                passwordQrCode = null;
            });
            
            // Download password WiFi QR code as image
            $('#download-password-qr-btn').on('click', function() {
                const canvas = $('#password-qr-code-container canvas')[0];
                if (canvas) {
                    const ssid = $('#password-wifi-ssid').val() || 'Home WiFi';
                    const url = canvas.toDataURL('image/png');
                    const link = document.createElement('a');
                    link.download = `wifi-qr-${ssid.replace(/[^a-z0-9]/gi, '_').toLowerCase()}.png`;
                    link.href = url;
                    link.click();
                    toastr.success('QR Code downloaded successfully');
                } else {
                    toastr.error('Failed to download QR code');
                }
            });

        });
    </script>
    </body>
    <!-- END: Body-->
</html>