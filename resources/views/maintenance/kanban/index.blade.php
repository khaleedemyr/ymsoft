@extends('layouts.master')
@section('title')
    Maintenance Order Kanban
@endsection
@section('css')
    <link href="{{ URL::asset('build/libs/dragula/dragula.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('build/css/maintenance-comment.css') }}" rel="stylesheet" type="text/css" />
    <style>
        .member-options .member-item {
            padding: 8px 16px;
            border-radius: 20px;
            background-color: #f8f9fa;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .member-options .member-item:hover {
            background-color: #e9ecef;
        }

        .member-options .member-item.selected {
            background-color: #e9ecef;
            color: #0d6efd;
        }

        .member-options .member-item img {
            width: 24px;
            height: 24px;
            border-radius: 50%;
        }

        .capture-preview .media-item,
        .media-preview .media-item,
        .doc-preview .doc-item {
            position: relative;
            width: 60px;
            height: 60px;
            border-radius: 8px;
            overflow: hidden;
        }

        .capture-preview .media-item img,
        .media-preview .media-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .capture-preview .media-item video,
        .media-preview .media-item video {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .doc-preview .doc-item {
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #f8f9fa;
        }

        .delete-media {
            position: absolute;
            top: 2px;
            right: 2px;
            background: rgba(255,255,255,0.8);
            border-radius: 50%;
            padding: 2px;
            cursor: pointer;
            font-size: 12px;
        }

        /* Untuk memperlebar dan memposisikan modal SweetAlert di tengah */
        .swal-wide {
            width: 850px !important;
            margin-left: auto !important;
            margin-right: auto !important;
        }

        /* Memastikan container SweetAlert berada di tengah */
        .swal2-container {
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
        }

        /* Styling untuk list dalam SweetAlert */
        .swal2-html-container ul {
            padding-left: 20px;
            margin-bottom: 0;
        }

        .swal2-html-container li {
            margin-bottom: 8px;
        }

        /* Memastikan modal content tidak lebih lebar dari viewport */
        @media (max-width: 900px) {
            .swal-wide {
                width: 90% !important;
            }
        }

        /* Tambahan style untuk wisdom container */
        .wisdom-container {
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }

        .wisdom-container ul {
            list-style-type: none;
            padding-left: 0;
        }

        .wisdom-container li {
            position: relative;
            padding-left: 20px;
            margin-bottom: 8px;
        }

        .wisdom-container li:before {
            content: "•";
            position: absolute;
            left: 0;
            color: currentColor;
        }

        .wisdom-container li:last-child {
            margin-bottom: 0;
        }

        /* Warna background soft */
        .bg-soft-warning {
            background-color: rgba(255, 199, 0, 0.1) !important;
        }

        .bg-soft-info {
            background-color: rgba(0, 143, 251, 0.1) !important;
        }

        .bg-soft-success {
            background-color: rgba(10, 179, 156, 0.1) !important;
        }

        /* Style untuk member selection */
        .selected-member {
            position: relative;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: #0d6efd;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .selected-member:hover {
            background-color: #0b5ed7;
        }

        .selected-member:hover .delete-member {
            opacity: 1;
        }

        .delete-member {
            position: absolute;
            top: -5px;
            right: -5px;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background-color: #dc3545;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            cursor: pointer;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .delete-member:hover {
            background-color: #bb2d3b;
        }

        /* Styling untuk camera container */
        .camera-container {
            position: relative;
            width: 100%;
        }

        .media-item {
            position: relative;
            width: 100px;
            height: 100px;
            border-radius: 8px;
            overflow: hidden;
            margin: 5px;
        }

        .media-item img,
        .media-item video {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .delete-media {
            position: absolute;
            top: 5px;
            right: 5px;
            background: rgba(255, 0, 0, 0.7);
            color: white;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: background 0.3s;
        }

        .delete-media:hover {
            background: rgba(255, 0, 0, 0.9);
        }

        .camera-controls {
            padding: 10px;
            background: rgba(0,0,0,0.1);
            border-radius: 8px;
        }

        #cameraPreview {
            border-radius: 8px;
            background: #000;
        }

        .video-container {
            width: 150px !important;
            height: 150px !important;
            position: relative;
            overflow: hidden;
        }

        .video-container video {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .video-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(0,0,0,0.3);
            opacity: 0;
            transition: opacity 0.3s;
            pointer-events: auto;
        }

        .video-container:hover .video-overlay {
            opacity: 1;
        }

        .play-btn {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(255,255,255,0.9) !important;
            pointer-events: auto;
            cursor: pointer;
            z-index: 1000;
        }

        .play-btn:hover {
            background: rgba(255,255,255,1) !important;
        }

        .video-preview {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .recording-indicator {
            position: absolute;
            top: 10px;
            left: 10px;
            background: rgba(0,0,0,0.5);
            color: white;
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 5px;
            z-index: 2;
        }

        .blink {
            animation: blink 1s infinite;
        }

        @keyframes blink {
            0% { opacity: 1; }
            50% { opacity: 0; }
            100% { opacity: 1; }
        }

        .recording-time {
            font-family: monospace;
        }

        .photo-container {
            width: 150px !important;
            height: 150px !important;
            position: relative;
            margin: 5px;
            display: inline-block;
        }

        .photo-container img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .photo-container .delete-media {
            position: absolute;
            top: 5px;
            right: 5px;
            background: rgba(255, 0, 0, 0.7);
            color: white;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: background 0.3s;
            z-index: 2;
        }

        .photo-container .delete-media:hover {
            background: rgba(255, 0, 0, 0.9);
            color: white;
            text-decoration: none;
        }

        .media-wrapper {
            display: inline-block;
            position: relative;
            margin: 5px;
            pointer-events: auto;
        }

        .media-item {
            width: 150px !important;
            height: 150px !important;
            position: relative;
        }

        .video-container video,
        .photo-container img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .delete-media {
            position: absolute;
            top: -10px;
            right: -10px;
            background: #dc3545;
            color: white;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: background 0.3s;
            z-index: 999;
            pointer-events: auto;
        }

        .delete-media:hover {
            background: #c82333;
            color: white;
            text-decoration: none;
        }

        .video-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(0,0,0,0.3);
            opacity: 0;
            transition: opacity 0.3s;
        }

        .video-container:hover .video-overlay {
            opacity: 1;
        }

        .play-btn {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(255,255,255,0.9) !important;
        }

        .capture-preview {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            padding: 10px;
        }

        /* Pastikan semua container tidak menghalangi events */
        .media-item,
        .video-container,
        .photo-container {
            pointer-events: auto;
        }

        .upload-preview {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            padding: 10px;
            border: 1px dashed #ccc;
            border-radius: 4px;
            min-height: 100px;
            margin-top: 10px;
        }

        /* Gunakan style yang sama dengan capture-preview untuk konsistensi */
        .media-wrapper,
        .media-item,
        .video-container,
        .photo-container,
        .delete-media,
        .video-overlay,
        .play-btn {
            /* Style yang sudah ada tetap sama */
        }

        /* Styling khusus untuk upload preview */
        .delete-upload {
            position: absolute;
            top: -10px;
            right: -10px;
            background: #dc3545;
            color: white;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: background 0.3s;
            z-index: 999;
        }

        .delete-upload:hover {
            background: #c82333;
            color: white;
            text-decoration: none;
        }

        .play-upload-btn {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(255,255,255,0.9) !important;
            pointer-events: auto;
            cursor: pointer;
            z-index: 1000;
        }

        .play-upload-btn:hover {
            background: rgba(255,255,255,1) !important;
        }

        .document-preview {
            border: 1px dashed #ccc;
            border-radius: 4px;
            padding: 10px;
            min-height: 50px;
        }

        .document-list {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .doc-item {
            display: flex;
            align-items: center;
            padding: 8px 12px;
            background: #f8f9fa;
            border-radius: 4px;
            position: relative;
            transition: all 0.3s ease;
        }

        .doc-item:hover {
            background: #e9ecef;
        }

        .doc-icon {
            margin-right: 12px;
            display: flex;
            align-items: center;
        }

        .doc-info {
            flex: 1;
            min-width: 0; /* Penting untuk text truncate */
        }

        .doc-name {
            display: block;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 200px;
            font-size: 14px;
        }

        .delete-doc {
            color: #dc3545;
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            transition: all 0.3s ease;
            margin-left: 8px;
        }

        .delete-doc:hover {
            background: rgba(220, 53, 69, 0.1);
            color: #dc3545;
            text-decoration: none;
        }

        /* Animasi untuk delete */
        .doc-item.removing {
            opacity: 0;
            transform: translateX(-10px);
        }

        /* Style untuk tasks board */
        .tasks-board {
            display: flex;
            gap: 1rem;
            overflow-x: auto;
            padding: 1rem;
        }

        .tasks-list {
            flex: 1;
            min-width: 300px;
            background-color: #f8f9fa;
            border-radius: 0.5rem;
            padding: 1rem;
        }

        /* Media query untuk smartphone */
        @media (max-width: 768px) {
            .tasks-board {
                flex-direction: column;
                overflow-x: hidden;
                overflow-y: auto;
                padding: 1rem 0.5rem;
            }

            .tasks-list {
                min-width: 100%;
                margin-bottom: 1rem;
            }

            /* Pastikan scroll horizontal tidak muncul */
            .tasks-wrapper {
                overflow-x: hidden !important;
            }
        }

        /* Styling untuk photo preview */
        .task-media-preview {
            margin-top: 12px;
        }

        .photo-preview-item {
            cursor: pointer;
            transition: transform 0.2s;
            position: relative;
        }

        .photo-preview-item:hover {
            transform: scale(1.1);
        }

        .photo-preview-more {
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Styling untuk modal gallery */
        .media-modal-container {
            position: relative;
        }

        .media-modal-image {
            max-width: 100%;
            max-height: 70vh;
            object-fit: contain;
        }

        .media-modal-nav {
            position: absolute;
            top: 50%;
            width: 100%;
            display: flex;
            justify-content: space-between;
            padding: 0 10px;
            transform: translateY(-50%);
            z-index: 10;
        }

        .media-modal-nav button {
            background: rgba(0,0,0,0.5);
            color: white;
            border: none;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: background 0.3s;
        }

        .media-modal-nav button:hover {
            background: rgba(0,0,0,0.7);
        }

        .media-modal-counter {
            position: absolute;
            bottom: 10px;
            left: 0;
            width: 100%;
            text-align: center;
            color: white;
            background: rgba(0,0,0,0.5);
            padding: 5px;
            font-size: 14px;
        }

        /* Styling untuk tombol download */
        .media-modal-download {
            position: absolute;
            bottom: 10px;
            right: 10px;
            z-index: 10;
        }

        .download-media {
            background-color: rgba(13, 110, 253, 0.85);
            border: none;
            padding: 5px 12px;
            border-radius: 4px;
            color: white;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .download-media:hover {
            background-color: rgba(13, 110, 253, 1);
            color: white;
        }

        /* Styling untuk video preview */
        .task-video-preview {
            margin-top: 8px;
        }

        .video-preview-item {
            cursor: pointer;
            transition: transform 0.2s;
            position: relative;
        }

        .video-preview-item:hover {
            transform: scale(1.1);
        }

        .video-thumbnail {
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            background-color: #000;
        }

        .video-preview-more {
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Modal video player */
        .video-modal-player {
            width: 100%;
            max-height: 80vh;
        }

        /* Styling untuk preview section */
        .preview-section {
            margin-bottom: 8px;
        }

        .preview-title {
            display: flex;
            align-items: center;
        }

        .fs-sm {
            font-size: 0.85rem;
        }

        /* Styling untuk document preview */
        .task-doc-preview {
            margin-top: 8px;
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .doc-preview-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            width: 70px;
            height: 70px;
            background-color: #f8f9fa;
            border-radius: 6px;
            padding: 5px;
            cursor: pointer;
            transition: all 0.2s;
            border: 1px solid #e9e9e9;
            text-align: center;
        }

        .doc-preview-item:hover {
            background-color: #e9ecef;
            transform: translateY(-2px);
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .doc-icon {
            margin-bottom: 4px;
        }

        .doc-type-icon {
            font-size: 24px;
        }

        /* Warna berdasarkan tipe file */
        .doc-type-pdf {
            color: #dc3545; /* Merah untuk PDF */
        }

        .doc-type-word {
            color: #0d6efd; /* Biru untuk Word */
        }

        .doc-type-excel {
            color: #198754; /* Hijau untuk Excel */
        }

        .doc-type-ppt {
            color: #fd7e14; /* Orange untuk PowerPoint */
        }

        .doc-type-zip {
            color: #6f42c1; /* Ungu untuk file kompresi */
        }

        .doc-type-default {
            color: #6c757d; /* Abu-abu untuk tipe lainnya */
        }

        .doc-name {
            font-size: 10px;
            max-width: 100%;
            max-height: 28px;
            overflow: hidden;
            text-overflow: ellipsis;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
        }

        .doc-preview-more {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 70px;
            height: 70px;
            border-radius: 6px;
            background-color: #f8f9fa;
            border: 1px solid #e9e9e9;
            cursor: pointer;
        }

        .doc-preview-more:hover {
            background-color: #e9ecef;
        }

        /* Styling untuk attachment section */
        .attachment-toggle-btn,
        .evidence-toggle-btn {
            text-align: left;
            transition: all 0.2s;
            border-radius: 4px;
            background-color: #f8f9fa;
            border: 1px solid #e9e9e9;
        }

        .attachment-toggle-btn:hover,
        .evidence-toggle-btn:hover {
            background-color: #e9ecef;
        }

        .attachment-toggle-icon,
        .evidence-toggle-icon {
            transition: transform 0.2s ease-in-out;
        }

        .attachment-toggle-btn.expanded .attachment-toggle-icon,
        .evidence-toggle-btn.expanded .evidence-toggle-icon {
            transform: rotate(180deg);
        }

        .attachment-content,
        .evidence-content {
            padding-top: 10px;
            transition: max-height 0.3s ease-out, opacity 0.3s ease-out;
            overflow: hidden;
        }

        /* Styling untuk card footer dan tombol comment */
        .card-footer {
            border-top: 1px solid rgba(0,0,0,0.05);
        }

        .task-actions {
            display: flex;
            gap: 8px;
        }

        .task-comment-btn {
            font-size: 12px;
            display: flex;
            align-items: center;
            transition: all 0.2s;
            border-radius: 4px;
        }

        .task-comment-btn:hover {
            background-color: #e9ecef;
        }

        .task-comment-btn .badge {
            font-size: 10px;
            padding: 3px 6px;
            border-radius: 10px;
        }

        /* Styling untuk member avatars dengan hover effect */
        .task-members {
            margin-bottom: 8px;
        }

        .members-wrapper {
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .member-avatar {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            font-weight: 600;
            color: white;
            cursor: pointer;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            transition: transform 0.2s;
            position: relative; /* Untuk popup nama */
        }

        .member-avatar:hover {
            transform: scale(1.15);
            z-index: 10;
        }

        /* Popup nama dengan CSS murni */
        .member-avatar::after {
            content: attr(data-name);
            position: absolute;
            top: -30px;
            left: 50%;
            transform: translateX(-50%);
            background-color: rgba(0,0,0,0.8);
            color: white;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 10px;
            white-space: nowrap;
            pointer-events: none;
            opacity: 0;
            transition: opacity 0.2s ease;
            z-index: 100;
        }

        .member-avatar:hover::after {
            opacity: 1;
        }

        .member-more {
            background-color: #6c757d;
            font-size: 10px;
        }

        /* Tambahkan untuk member selection di modal create */
        .selected-member {
            position: relative;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: #0d6efd;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .selected-member::after {
            content: attr(data-name);
            position: absolute;
            top: -35px;
            left: 50%;
            transform: translateX(-50%);
            background-color: rgba(0,0,0,0.8);
            color: white;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 11px;
            white-space: nowrap;
            pointer-events: none;
            opacity: 0;
            transition: opacity 0.2s ease;
            z-index: 100;
        }

        .selected-member:hover::after {
            opacity: 1;
        }

        /* Atur posisi tooltip */
        .tooltip {
            font-size: 12px;
        }

        /* Styling untuk comment media */
        .comment-media-wrapper {
            position: relative;
            margin-right: 10px;
            margin-bottom: 10px;
        }

        .comment-media-item {
            position: relative;
            width: 100px;
            height: 100px;
            border-radius: 8px;
            overflow: hidden;
            border: 1px solid #e9e9e9;
        }

        .comment-media-item img,
        .comment-media-item video {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .delete-comment-media {
            position: absolute;
            top: -8px;
            right: -8px;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background-color: #dc3545;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            cursor: pointer;
            z-index: 1;
        }

        .comment-video-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: rgba(0, 0, 0, 0.2);
        }

        .comment-play-btn {
            padding: 3px 8px;
            font-size: 12px;
        }

        .comment-attachments-gallery {
            margin-top: 10px;
        }

        .comment-attachment-item-sm {
            margin-right: 5px;
            margin-bottom: 5px;
            cursor: pointer;
        }

        /* Tambahkan di bagian <style> */
        .comments-list {
            max-height: 350px;
            overflow-y: auto;
            padding-right: 5px;
        }

        .comments-list::-webkit-scrollbar {
            width: 6px;
        }

        .comments-list::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .comments-list::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 10px;
        }

        .comments-list::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }

        /* Timeline styles - BARU, LEBIH SEDERHANA & RESPONSIF */
        .timeline-container {
            width: 100%;
            margin: 0 auto;
        }
        
        .timeline {
            position: relative;
            width: 100%;
            max-width: 1400px;
            margin: 0 auto;
        }
        
        .timeline::after {
            content: "";
            position: absolute;
            width: 2px;
            background-color: #e9e9ef;
            top: 0;
            bottom: 0;
            left: 50%;
            margin-left: -1px;
        }
        
        .timeline-item {
            padding: 30px 40px;
            position: relative;
            background: inherit;
            width: 50%;
        }
        
        .timeline-item .icon {
            position: absolute;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            z-index: 2;
        }
        
        .timeline-item .timeline-content {
            padding: 15px;
            background: #fff;
            border-radius: 4px;
            box-shadow: 0 0 5px rgba(0,0,0,0.1);
        }
        
        .timeline-item .date {
            font-size: 12px;
            color: #888;
            margin-top: 5px;
        }
        
        .timeline-item.left {
            left: 0;
        }
        
        .timeline-item.left .icon {
            right: -20px;
            top: 30px;
        }
        
        .timeline-item.right {
            left: 50%;
        }
        
        .timeline-item.right .icon {
            left: -20px;
            top: 30px;
        }
        
        @media screen and (max-width: 767px) {
            .timeline::after {
                left: 40px;
            }
            
            .timeline-item {
                width: 100%;
                padding-left: 80px;
                padding-right: 10px;
            }
            
            .timeline-item.left {
                left: 0;
            }
            
            .timeline-item.right {
                left: 0;
            }
            
            .timeline-item.left .icon,
            .timeline-item.right .icon {
                left: 20px;
                right: auto;
                top: 30px;
            }
        }

        /* Timeline icon tanpa background */
        .timeline-item .icon {
            position: absolute;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            border: 2px solid #e9e9ef;
            background-color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 2;
        }

        .timeline-item .icon i {
            font-size: 18px;
        }

        /* Warna icon berdasarkan tipe aktivitas */
        .timeline-item .icon.icon-created i { color: #0ab39c; }
        .timeline-item .icon.icon-status i { color: #f7b84b; }
        .timeline-item .icon.icon-priority i { color: #f06548; }
        .timeline-item .icon.icon-member-add i { color: #0ab39c; }
        .timeline-item .icon.icon-member-remove i { color: #f06548; }
        .timeline-item .icon.icon-comment i { color: #405189; }
        .timeline-item .icon.icon-comment-delete i { color: #f06548; }
        .timeline-item .icon.icon-document i { color: #3577f1; }
        .timeline-item .icon.icon-media i { color: #7b3ff3; }
        .timeline-item .icon.icon-date i { color: #f7b84b; }
        .timeline-item .icon.icon-complete i { color: #0ab39c; }
        .timeline-item .icon.icon-team i { color: #405189; }
        .timeline-item .icon.icon-info i { color: #3577f1; }

        /* Toast container */
        #toastContainer {
            max-width: 350px;
        }
        
        /* Animasi toast */
        .toast {
            transition: opacity 0.3s ease-in-out, transform 0.3s ease-in-out;
            transform: translateX(100%);
            opacity: 0;
        }
        
        .toast.show {
            transform: translateX(0);
            opacity: 1;
        }
        
        /* Styling untuk toast */
        .toast .toast-header {
            padding: 0.5rem 0.75rem;
        }
        
        .toast .toast-body {
            padding: 0.75rem;
        }
        
        /* Media query untuk device kecil */
        @media (max-width: 576px) {
            #toastContainer {
                max-width: 100%;
                padding: 0;
                right: 0;
                left: 0;
                bottom: 0;
            }
            
            .toast {
                width: 100%;
                border-radius: 0;
                margin-bottom: 0;
            }
        }

        /* Ubah CSS style untuk animasi */
        @keyframes notificationPulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.3); }
            100% { transform: scale(1); }
        }
        
        .notification-pulse {
            animation: notificationPulse 0.5s ease-in-out 2;
        }
        
        #toastContainer {
            max-width: 350px;
        }
        
        .toast {
            margin-bottom: 0.5rem;
            box-shadow: 0 0.25rem 0.75rem rgba(0, 0, 0, 0.1);
            opacity: 0;
            transform: translateY(100%); /* Ubah dari translateX menjadi translateY */
            transition: all 0.3s ease-in-out;
        }
        
        .toast.show {
            opacity: 1;
            transform: translateY(0);
        }

        /* CSS untuk statistik PR */
        .pr-stats-section {
            margin-top: 0.75rem;
            margin-bottom: 0.75rem;
            border-top: 1px dashed #e9ecef;
            padding-top: 0.5rem;
        }
        
        .pr-stats-container {
            background-color: #f8f9fa;
            border-radius: 6px;
            font-size: 0.85rem;
        }
        
        .pr-stat-label {
            font-size: 0.7rem;
            color: #6c757d;
        }
        
        .pr-stat-value {
            font-size: 1rem;
            font-weight: 600;
        }
        
        /* Pastikan ukuran font tidak terlalu besar di mobile */
        @media (max-width: 576px) {
            .pr-stat-value {
                font-size: 0.9rem;
            }
        }
        
        /* Untuk loading spinner */
        .pr-stats-loader {
            width: 0.8rem;
            height: 0.8rem;
        }

        /* Existing styles */
        .view-po-btn {
            font-size: 12px;
            padding: 4px 8px;
        }

        .view-po-btn i {
            font-size: 14px;
            margin-right: 4px;
        }

        .add-new-po-btn i {
            font-size: 14px;
            margin-right: 4px;
        }

        /* Styling untuk photo preview di task card */
        .photo-preview-item {
            width: 40px !important;
            height: 40px !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            margin: 0 !important;
            padding: 0 !important;
            background: none !important;
            border: none !important;
            overflow: hidden !important;
        }

        .photo-preview-item img {
            width: 40px !important;
            height: 40px !important;
            object-fit: cover !important;
            border-radius: 50% !important;
            border: 2px solid #fff !important;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1) !important;
            display: block !important;
        }

        .task-media-preview {
            display: flex !important;
            align-items: center !important;
            gap: 8px !important;
            background: none !important;
            padding: 0 !important;
            margin: 0 !important;
        }

        .photo-preview-more {
            width: 40px !important;
            height: 40px !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            margin: 0 !important;
            padding: 0 !important;
        }

        .preview-section {
            margin-top: 8px;
        }

        .preview-title {
            margin-bottom: 4px;
        }

        .preview-title .fs-sm {
            font-size: 0.85rem;
        }
    </style>
@endsection
@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            Maintenance
        @endslot
        @slot('title')
            Maintenance Order Kanban
        @endslot
    @endcomponent

    <div class="card">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-lg-4">
                    <label for="outletId" class="form-label">Outlet</label>
                    @if($canSelectOutlet)
                        <select class="form-select" id="outletId" name="outlet_id" required>
                            <option value="">Pilih Outlet</option>
                            @foreach($outlets as $outlet)
                                <option value="{{ $outlet->id_outlet }}">{{ $outlet->nama_outlet }}</option>
                            @endforeach
                        </select>
                    @else
                        <input type="text" class="form-control" value="{{ $selectedOutlet->nama_outlet }}" readonly>
                        <input type="hidden" id="outletId" name="outlet_id" value="{{ $selectedOutlet->id_outlet }}">
                    @endif
                </div>
                <div class="col-lg-4" id="rukoContainer" style="display: none;">
                    <label for="rukoId" class="form-label">Ruko</label>
                    <select class="form-select" id="rukoId" name="ruko_id">
                        <option value="">Pilih Ruko</option>
                    </select>
                </div>
                <!-- Tambahkan tombol reload di sini -->
                <div class="col-auto d-flex align-items-end">
                    <button type="button" class="btn btn-primary" id="reloadTasks">
                        <i class="ri-refresh-line"></i> Load Tasks
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Tambahkan di bagian atas halaman, setelah filter outlet/ruko -->
<!--    <button id="debugReloadBtn" class="btn btn-outline-primary btn-sm ms-2">
        <i class="ri-refresh-line"></i> Reload
    </button>-->

    <script>
    $(document).ready(function() {
        $('#debugReloadBtn').on('click', function() {
            location.reload();
        });
    });
    </script>

    <div class="tasks-board mb-3" id="kanbanboard">
        <!-- Task Board (To Do) -->
        <div class="tasks-list">
            <div class="d-flex mb-3">
                <div class="flex-grow-1">
                    <h6 class="fs-14 text-uppercase fw-semibold mb-0">To Do <small class="badge bg-secondary align-bottom ms-1 totaltask-badge">0</small></h6>
                </div>
                <div class="flex-shrink-0">
                    <button class="btn btn-sm btn-soft-primary" data-bs-toggle="modal" data-bs-target="#createMaintenanceModal">
                        <i class="ri-add-line align-bottom"></i>
                    </button>
                </div>
            </div>
            <div data-simplebar class="tasks-wrapper px-3 mx-n3">
                <div id="task-list" class="tasks">
                    <!-- Tasks will be loaded here -->
                </div>
            </div>
        </div>

        <!-- PR Board -->
        <div class="tasks-list">
            <div class="d-flex mb-3">
                <div class="flex-grow-1">
                    <h6 class="fs-14 text-uppercase fw-semibold mb-0">PR <small class="badge bg-info align-bottom ms-1 totaltask-badge">0</small></h6>
                </div>
            </div>
            <div data-simplebar class="tasks-wrapper px-3 mx-n3">
                <div id="pr-list" class="tasks">
                    <!-- PR items will be loaded here -->
                </div>
            </div>
        </div>

        <!-- PO Board -->
        <div class="tasks-list">
            <div class="d-flex mb-3">
                <div class="flex-grow-1">
                    <h6 class="fs-14 text-uppercase fw-semibold mb-0">PO <small class="badge bg-primary align-bottom ms-1 totaltask-badge">0</small></h6>
                </div>
            </div>
            <div data-simplebar class="tasks-wrapper px-3 mx-n3">
                <div id="po-list" class="tasks">
                    <!-- PO items will be loaded here -->
                </div>
            </div>
        </div>

        <!-- In Progress Board -->
        <div class="tasks-list">
            <div class="d-flex mb-3">
                <div class="flex-grow-1">
                    <h6 class="fs-14 text-uppercase fw-semibold mb-0">In Progress <small class="badge bg-warning align-bottom ms-1 totaltask-badge">0</small></h6>
                </div>
            </div>
            <div data-simplebar class="tasks-wrapper px-3 mx-n3">
                <div id="inprogress-list" class="tasks">
                    <!-- In Progress items will be loaded here -->
                </div>
            </div>
        </div>

        <!-- In Review Board -->
        <div class="tasks-list">
            <div class="d-flex mb-3">
                <div class="flex-grow-1">
                    <h6 class="fs-14 text-uppercase fw-semibold mb-0">In Review <small class="badge bg-info align-bottom ms-1 totaltask-badge">0</small></h6>
                </div>
            </div>
            <div data-simplebar class="tasks-wrapper px-3 mx-n3">
                <div id="review-list" class="tasks">
                    <!-- Review items will be loaded here -->
                </div>
            </div>
        </div>

        <!-- Done Board -->
        <div class="tasks-list">
            <div class="d-flex mb-3">
                <div class="flex-grow-1">
                    <h6 class="fs-14 text-uppercase fw-semibold mb-0">Done <small class="badge bg-success align-bottom ms-1 totaltask-badge">0</small></h6>
                </div>
            </div>
            <div data-simplebar class="tasks-wrapper px-3 mx-n3">
                <div id="done-list" class="tasks">
                    <!-- Done items will be loaded here -->
                </div>
            </div>
        </div>
    </div>

    <!-- Create Maintenance Modal -->
    <div class="modal fade" id="createMaintenanceModal" tabindex="-1" aria-labelledby="createMaintenanceModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0">
                <div class="modal-header p-3 bg-soft-info">
                    <h5 class="modal-title" id="createMaintenanceModalLabel">Buat Maintenance Order</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="#" id="maintenanceForm">
                        <div class="row g-3">
                            <div class="col-lg-12">
                                <label for="maintenanceTitle" class="form-label">Task</label>
                                <input type="text" class="form-control" id="maintenanceTitle" placeholder="Masukkan judul task">
                            </div>

                            <div class="col-lg-12">
                                <label for="description" class="form-label">Deskripsi</label>
                                <textarea class="form-control" id="description" rows="3" placeholder="Masukkan deskripsi task"></textarea>
                            </div>

                            <div class="col-lg-6">
                                <label for="label" class="form-label">Label</label>
                                <select class="form-select" id="label" name="label">
                                    <option value="">Pilih Label</option>
                                    <option value="Heater">Heater</option>
                                    <option value="Refrigeration">Refrigeration</option>
                                    <option value="Civil">Civil</option>
                                    <option value="Machinery">Machinery</option>
                                    <option value="Electricity">Electricity</option>
                                    <option value="Gas">Gas</option>
                                    <option value="Others">Others</option>
                                </select>
                            </div>

                            <div class="col-lg-6">
                                <label for="priority" class="form-label">Priority</label>
                                <select class="form-select" id="priority" name="priority">
                                    <option value="">Pilih Priority</option>
                                    <option value="IMPORTANT VS URGENT">IMPORTANT VS URGENT</option>
                                    <option value="IMPORTANT VS NOT URGENT">IMPORTANT VS NOT URGENT</option>
                                    <option value="NOT IMPORTANT VS URGENT">NOT IMPORTANT VS URGENT</option>
                                </select>
                            </div>

                            <div class="col-lg-6">
                                <label for="dueDate" class="form-label">Due Date</label>
                                <input type="text" class="form-control" id="dueDate" data-provider="flatpickr" placeholder="Pilih tanggal" readonly>
                            </div>

                            <!-- Menyembunyikan bagian Member Selection di CreateMaintenanceModal -->
                            <div class="col-lg-12" style="display: none;">
                                <label class="form-label">Members</label>
                                <div class="member-selection">
                                    <div class="member-list mb-3 d-flex flex-wrap gap-2" id="selectedMembers">
                                        <!-- Selected members will appear here -->
                                    </div>
                                    <div class="member-options p-3 border rounded-3">
                                        <div class="d-flex flex-wrap gap-2" id="memberOptions">
                                            <!-- Member options will be loaded here -->
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-12">
                                <label class="form-label">Capture Foto/Video</label>
                                <div class="capture-controls">
                                    <button type="button" class="btn btn-primary me-2" id="capturePhoto">
                                        <i class="ri-camera-line"></i> Foto
                                    </button>
                                    <button type="button" class="btn btn-info" id="captureVideo">
                                        <i class="ri-video-line"></i> Video
                                    </button>
                                </div>
                                <div class="capture-preview mt-2 d-flex flex-wrap gap-2">
                                    <!-- Captured media will be shown here -->
                                </div>
                            </div>

                            <div class="col-lg-12">
                                <label class="form-label">Upload Foto/Video</label>
                                <input type="file" class="form-control" id="mediaUpload" multiple accept="image/*,video/*">
                                <div class="upload-preview mt-3"></div>
                            </div>

                            <div class="col-lg-12">
                                <label class="form-label">Upload Dokumen</label>
                                <input type="file" class="form-control" id="documentUpload" multiple 
                                    accept=".doc,.docx,.xls,.xlsx,.pdf">
                                <div class="document-preview mt-3">
                                    <div class="document-list"></div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <div class="hstack gap-2 justify-content-end">
                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Tutup</button>
                                <button type="submit" class="btn btn-success">Simpan</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Preview Modal for Media -->
    <div class="modal fade" id="mediaPreviewModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body p-0">
                    <button type="button" class="btn-close position-absolute end-0 top-0 m-2 z-3" data-bs-dismiss="modal"></button>
                    <div id="mediaPreviewContent" class="text-center">
                        <!-- Camera preview will be inserted here -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal untuk tampilan gallery -->
    <div class="modal fade" id="mediaGalleryModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Photo Gallery</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                    <div class="media-modal-container">
                        <img src="" alt="" class="media-modal-image w-100">
                        <div class="media-modal-nav">
                            <button class="prev-media">
                                <i class="ri-arrow-left-line"></i>
                            </button>
                            <button class="next-media">
                                <i class="ri-arrow-right-line"></i>
                            </button>
                        </div>
                        <div class="media-modal-counter">
                            <span class="current-index">1</span> / <span class="total-count">1</span>
                        </div>
                        
                        <!-- Tambahkan tombol download di sini -->
                        <div class="media-modal-download">
                            <a href="#" class="download-media btn btn-sm btn-primary" download>
                                <i class="ri-download-line me-1"></i> Download
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal untuk video player -->
    <div class="modal fade" id="videoPlayerModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Video Player</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                    <div class="video-modal-container">
                        <video src="" controls class="video-modal-player w-100"></video>
                        <div class="media-modal-counter">
                            <span class="current-index">1</span> / <span class="total-count">1</span>
                        </div>
                        <div class="video-modal-nav">
                            <button class="prev-video">
                                <i class="ri-arrow-left-line"></i>
                            </button>
                            <button class="next-video">
                                <i class="ri-arrow-right-line"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal untuk document list -->
    <div class="modal fade" id="documentListModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Documents</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="doc-list-container">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th width="5%">#</th>
                                    <th width="50%">File Name</th>
                                    <th width="20%">Type</th>
                                    <th width="25%">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="docListTableBody">
                                <!-- Document list will be populated here -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal untuk comments -->
    <div class="modal fade" id="taskCommentsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Task Comments</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="comments-container">
                        <!-- Tambahkan input hidden untuk menyimpan ID user saat ini -->
                        <input type="hidden" id="current-user-id" value="{{ Auth::id() }}">
                        
                        <div class="comments-list mb-3">
                            <!-- Comments will be loaded here -->
                        </div>
                        
                        <div class="comment-form">
                            <form id="commentForm" enctype="multipart/form-data" method="post">
                                <input type="hidden" id="commentTaskId" name="task_id">
                                <div class="mb-3">
                                    <textarea class="form-control" id="commentText" name="comment" rows="3" placeholder="Write your comment here..."></textarea>
                                </div>
                                
                                <!-- Media controls for comment -->
                                <div class="comment-media-controls mb-3">
                                    <div class="d-flex gap-2 mb-2">
                                        <button type="button" class="btn btn-sm btn-primary" id="commentCapturePhoto">
                                            <i class="ri-camera-line"></i> Photo
                                        </button>
                                        <button type="button" class="btn btn-sm btn-info" id="commentCaptureVideo">
                                            <i class="ri-video-line"></i> Video
                                        </button>
                                        <button type="button" class="btn btn-sm btn-success" id="commentUploadBtn">
                                            <i class="ri-upload-line"></i> Upload
                                        </button>
                                        <input type="file" id="commentFileUpload" multiple accept="image/*,video/*,.doc,.docx,.xls,.xlsx,.pdf" style="display:none;">
                                    </div>
                                    
                                    <!-- Preview containers -->
                                    <div class="comment-media-preview">
                                        <div class="comment-capture-preview d-flex flex-wrap gap-2"></div>
                                        <div class="comment-upload-preview d-flex flex-wrap gap-2 mt-2"></div>
                                    </div>
                                </div>
                                
                                <div class="text-end">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="ri-send-plane-fill me-1"></i> Add Comment
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal untuk comment camera preview -->
    <div class="modal fade" id="commentCameraModal" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="commentCameraModalTitle">Take Photo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="commentCameraModalBody">
                    <!-- Camera preview will be inserted here -->
                </div>
            </div>
        </div>
    </div>

    <!-- Modal untuk comment camera preview -->
    <div class="modal fade" id="commentCameraModal" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="commentCameraModalTitle">Take Photo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="commentCameraModalBody">
                    <!-- Camera preview will be inserted here -->
                </div>
            </div>
        </div>
    </div>

    <!-- Tambahkan modal timeline setelah modal lainnya -->
    <div class="modal fade" id="timelineModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Task Timeline</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Task Due Date Section -->
                    <div id="timeline-due-date" class="mb-4">
                        <!-- Due date akan ditampilkan di sini -->
                    </div>
                    
                    <h6 class="text-uppercase fw-semibold mb-3">Aktivitas Task</h6>
                    
                    <div class="timeline-container">
                        <div class="timeline" id="task-timeline-content" style="max-height: 500px; overflow-y: auto; padding-right: 5px;">
                            <!-- Timeline will be loaded here -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal untuk Edit Task (hanya members) -->
    <div class="modal fade" id="editTaskModal" tabindex="-1" aria-labelledby="editTaskModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0">
                <div class="modal-header p-3 bg-soft-info">
                    <h5 class="modal-title" id="editTaskModalLabel">Edit Anggota Task</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="#" id="editTaskForm">
                        <input type="hidden" id="editTaskId" name="task_id">
                        
                        <div class="row g-3">
                            <div class="col-lg-12">
                                <label class="form-label">Members</label>
                                <div class="member-selection">
                                    <div class="member-list mb-3 d-flex flex-wrap gap-2" id="editSelectedMembers">
                                        <!-- Selected members will appear here -->
                                    </div>
                                    <div class="member-options p-3 border rounded-3">
                                        <div class="d-flex flex-wrap gap-2" id="editMemberOptions">
                                            <!-- Member options will be loaded here -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <div class="hstack gap-2 justify-content-end">
                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Tutup</button>
                                <button type="submit" class="btn btn-success">Simpan</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Toast Container -->
    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 1100" id="toastContainer"></div>

    <!-- Include PR modals -->
    @include('maintenance.purchase-requisition.modals')

    <!-- Include PO modals -->
    @include('maintenance.purchase-order.modals')


    @include('maintenance.kanban.modals.simple-evidence-modal')

@endsection

@section('script')
    <script src="{{ URL::asset('build/libs/dragula/dragula.min.js') }}"></script>
    <script src="{{ URL::asset('build/libs/dom-autoscroller/dom-autoscroller.min.js') }}"></script>

        <!-- Comment Functionality -->

<script src="{{ asset('build/js/maintenance-comment.js') }}"></script>
    
<script src="{{ asset('build/js/pages/maintenance-kanban.init.js') }}"></script>
<script src="{{ asset('build/js/maintenance-evidence.js') }}"></script>
<script src="{{ asset('build/js/simple-evidence.js') }}"></script>
    <script>
        // Tambahkan kode ini di bagian awal script.js atau setelah document ready
        // Pastikan function ini berada di scope global agar bisa diakses dari HTML
        window.toggleDescription = function(element, taskId) {
            const $element = $(element);
            // Gunakan attr() untuk mendapatkan data attribute
            const isExpanded = $element.attr('data-expanded') === 'true';
            
            // Prevent double-trigger
            $element.off('click').on('click', function(e) {
                e.stopPropagation();
            });
            
            if (isExpanded) {
                // Hide full description, show short description
                $(`.full-desc-${taskId}`).hide();
                $(`.short-desc-${taskId}`).show();
                $element.html('Show more <i class="ri-arrow-down-s-line align-middle"></i>');
                $element.attr('data-expanded', 'false');
            } else {
                // Show full description, hide short description
                $(`.short-desc-${taskId}`).hide();
                $(`.full-desc-${taskId}`).show();
                $element.html('Show less <i class="ri-arrow-up-s-line align-middle"></i>');
                $element.attr('data-expanded', 'true');
            }
            
            // Prevent further propagation
            return false;
        };

        $(document).ready(function() {
            // Disable due date input
            $('#dueDate').prop('readonly', true);

            // Priority handling
            $('#priority').change(function() {
                const priority = $(this).val();
                let title, html, maxDays;

                switch(priority) {
                    case 'IMPORTANT VS URGENT':
                        title = 'IMPORTANT VS URGENT - Maksimal 2 Hari';
                        html = `
                            <div class="text-start">
                                <p class="mb-3">Tugas atau aktivitas yang memiliki dampak signifikan terhadap sistem kerja, standar kualitas produk, pelayanan, suasana, kenyamanan, pengalaman tamu dan harus segera diselesaikan karena memiliki batas waktu mendesak.</p>
                                <div class="wisdom-container bg-soft-warning p-3 rounded-3 border border-warning">
                                    <p class="mb-2 fw-bold text-warning">
                                        <i class="ri-lightbulb-flash-line me-2"></i>Pertimbangan Bijak:
                                    </p>
                                    <ul class="text-start text-dark mb-0">
                                        <li>Apakah masalah ini benar-benar membutuhkan penanganan segera?</li>
                                        <li>Apakah ada dampak langsung terhadap operasional dan kepuasan tamu?</li>
                                        <li>Apakah penundaan penanganan akan mengakibatkan masalah yang lebih besar?</li>
                                    </ul>
                                </div>
                            </div>`;
                        maxDays = 2;
                        break;

                    case 'IMPORTANT VS NOT URGENT':
                        title = 'IMPORTANT VS NOT URGENT - Maksimal 1 Bulan';
                        html = `
                            <div class="text-start">
                                <p class="mb-3">Tugas atau aktivitas yang memiliki dampak signifikan terhadap sistem kerja, standar kualitas produk, pelayanan, suasana, kenyamanan, pengalaman tamu dalam jangka panjang tetapi tidak memiliki batas waktu mendesak.</p>
                                <div class="wisdom-container bg-soft-info p-3 rounded-3 border border-info">
                                    <p class="mb-2 fw-bold text-info">
                                        <i class="ri-lightbulb-flash-line me-2"></i>Pertimbangan Bijak:
                                    </p>
                                    <ul class="text-start text-dark mb-0">
                                        <li>Apakah kita memiliki waktu untuk perencanaan yang lebih matang?</li>
                                        <li>Bagaimana kita bisa mengoptimalkan sumber daya untuk hasil terbaik?</li>
                                        <li>Apakah ada alternatif solusi yang perlu dipertimbangkan?</li>
                                    </ul>
                                </div>
                            </div>`;
                        maxDays = 30;
                        break;

                    case 'NOT IMPORTANT VS URGENT':
                        title = 'NOT IMPORTANT VS URGENT - Maksimal 2 Minggu';
                        html = `
                            <div class="text-start">
                                <p class="mb-3">Tugas atau aktivitas yang harus segera diselesaikan tetapi tidak memiliki dampak besar terhadap sistem kerja, standar kualitas produk, pelayanan, suasana, kenyamanan, pengalaman tamu dalam tujuan jangka panjang.</p>
                                <div class="wisdom-container bg-soft-success p-3 rounded-3 border border-success">
                                    <p class="mb-2 fw-bold text-success">
                                        <i class="ri-lightbulb-flash-line me-2"></i>Pertimbangan Bijak:
                                    </p>
                                    <ul class="text-start text-dark mb-0">
                                        <li>Apakah tugas ini benar-benar perlu dikerjakan segera?</li>
                                        <li>Apakah ada tugas lain yang lebih prioritas?</li>
                                        <li>Bagaimana kita bisa mengatur waktu agar efisien?</li>
                                    </ul>
                                </div>
                            </div>`;
                        maxDays = 14;
                        break;

                    default:
                        return;
                }

                Swal.fire({
                    title: title,
                    html: html,
                    icon: 'info',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Saya Mengerti',
                    cancelButtonText: 'Tidak, Saya Akan Memilih Ulang',
                    customClass: {
                        container: 'swal-wide',
                        htmlContainer: 'text-start'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Set due date
                        const today = new Date();
                        const dueDate = new Date(today);
                        dueDate.setDate(today.getDate() + maxDays);
                        
                        // Format date to YYYY-MM-DD
                        const formattedDate = dueDate.toISOString().split('T')[0];
                        $('#dueDate').val(formattedDate);
                    } else {
                        // Reset priority selection
                        $('#priority').val('');
                        $('#dueDate').val('');
                    }
                });
            });

            // Di dalam $(document).ready, update handler untuk outlet change
            const outletId = $('#outletId').val();
            if (outletId) {
                $('#outletId').trigger('change');
            }

            // Handle outlet change
            $('#outletId').on('change', function() {
                const outletId = $(this).val();
                const rukoContainer = $('#rukoContainer');
                const rukoSelect = $('#rukoId');

                // Reset ruko select
                rukoSelect.html('<option value="">Pilih Ruko</option>');

                if (outletId == '1') {
                    // Jika outlet ID = 1, tampilkan ruko select dan ambil data ruko
                    rukoContainer.show();
                    
                    // Reset semua container task karena harus pilih ruko dulu
                    $('#task-list').empty();
                    $('#pr-list').empty();
                    $('#po-list').empty();
                    $('#inprogress-list').empty();
                    $('#review-list').empty();
                    $('#done-list').empty();
                    
                    // Update counters
                    updateTaskCounters();
                    
                    // Ambil data ruko
                    $.ajax({
                        url: "{{ route('maintenance.getRuko', '') }}/" + outletId,
                        type: 'GET',
                        success: function(response) {
                            response.forEach(function(ruko) {
                                rukoSelect.append(
                                    `<option value="${ruko.id_ruko}">${ruko.nama_ruko}</option>`
                                );
                            });
                        },
                        error: function(xhr) {
                            console.error('Error loading ruko:', xhr);
                        }
                    });
                } else {
                    // Jika bukan outlet ID = 1, sembunyikan ruko select dan langsung load tasks
                    rukoContainer.hide();
                    loadTasks(outletId, null);
                }
            });

            // Initialize dragula with drop event
            var drake = dragula([
                document.getElementById('task-list'),
                document.getElementById('pr-list'),
                document.getElementById('po-list'),
                document.getElementById('inprogress-list'),
                document.getElementById('review-list'),
                document.getElementById('done-list')
            ]).on('drop', function(el, target) {
                // Mendapatkan ID task dari data attribute atau ID elemen
                const taskId = el.getAttribute('data-task-id');
                const currentStatus = el.getAttribute('data-current-status');
                
                // Mendapatkan status baru berdasarkan ID container
                let newStatus = '';
                switch(target.id) {
                    case 'task-list':
                        newStatus = 'TASK';
                        break;
                    case 'pr-list':
                        newStatus = 'PR';
                        break;
                    case 'po-list':
                        newStatus = 'PO';
                        break;
                    case 'inprogress-list':
                        newStatus = 'IN_PROGRESS';
                        break;
                    case 'review-list':
                        newStatus = 'IN_REVIEW';
                        break;
                    case 'done-list':
                        newStatus = 'DONE';
                        break;
                }
                
                // Langsung update tanpa check requirements dulu
                if (taskId && newStatus) {
                    updateTaskStatus(taskId, newStatus);
                }
            });

            // Load members
            $.get("{{ route('maintenance.getMembers') }}", function(members) {
                const memberOptions = $('#memberOptions');
                members.forEach(member => {
                    memberOptions.append(`
                        <div class="member-item" 
                             data-id="${member.id}" 
                             data-name="${member.nama_lengkap}">
                            ${member.nama_lengkap}
                        </div>
                    `);
                });
            });

            // Handle member selection dengan data-name untuk hover
            $(document).on('click', '.member-item', function() {
                const memberId = $(this).data('id');
                const memberName = $(this).data('name');
                
                // Check if already selected
                if ($(`#selected-member-${memberId}`).length > 0) {
                    return;
                }

                // Get initials
                const initials = memberName
                    .split(' ')
                    .map(word => word[0])
                    .join('')
                    .toUpperCase();

                // Add selected member dengan data-name untuk hover
                $('#selectedMembers').append(`
                    <div class="selected-member" 
                         id="selected-member-${memberId}"
                         data-name="${memberName}">
                        ${initials}
                        <div class="delete-member">
                            <i class="ri-close-line"></i>
                        </div>
                    </div>
                `);

                // Add selected class to option
                $(this).addClass('selected');
            });

            // Handle member removal
            $(document).on('click', '.delete-member', function(e) {
                e.stopPropagation();
                const memberContainer = $(this).parent();
                const memberId = memberContainer.attr('id').replace('selected-member-', '');
                
                // Remove selected class from option
                $(`.member-item[data-id="${memberId}"]`).removeClass('selected');
                
                // Remove tooltip
                memberContainer.tooltip('dispose');
                
                // Remove selected member
                memberContainer.remove();
            });

            // Form submit handler yang benar untuk mengirim file
            $('#maintenanceForm').submit(function(e) {
                e.preventDefault();
                
                var outletId = $('#outletId').val();
                var rukoId = $('#rukoId').val();
                
                // Validasi outlet
                if (!outletId) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Silahkan pilih outlet terlebih dahulu'
                    });
                    return false;
                }
                
                // Generate nomor task
                var date = new Date();
                var year = date.getFullYear().toString().substr(-2);
                var month = ('0' + (date.getMonth() + 1)).slice(-2);
                var day = ('0' + date.getDate()).slice(-2);
                var randomNum = Math.floor(1000 + Math.random() * 9000);
                var taskNumber = 'MT' + year + month + day + randomNum;
                
                // Buat FormData untuk support file upload
                var formData = new FormData();
                
                // Tambahkan data form dasar
                formData.append('task_number', taskNumber);
                formData.append('title', $('#maintenanceTitle').val().trim());
                formData.append('description', $('#description').val().trim());
                formData.append('priority_id', $('#priority').val());
                formData.append('label_id', $('#label').val());
                formData.append('due_date', $('#dueDate').val());
                formData.append('id_outlet', outletId);
                if (rukoId) formData.append('id_ruko', rukoId);
                
                // PENTING: Tambahkan member IDs yang dipilih
                var selectedMembers = [];
                $('.selected-member').each(function() {
                    var memberId = $(this).attr('id').replace('selected-member-', '');
                    selectedMembers.push(memberId);
                });
                
                // Kirim sebagai array jika ada member yang dipilih
                if (selectedMembers.length > 0) {
                    // Kirim sebagai string untuk memastikan data dikirim dengan benar
                    formData.append('member_ids', selectedMembers.join(','));
                    console.log('Selected members:', selectedMembers);
                }
                
                // Tambahkan captured photos (base64)
                capturedPhotos.forEach(function(photo, index) {
                    // Gunakan base64 data jika blob tidak tersedia
                    if (photo.blob) {
                        formData.append(`photos[${index}]`, photo.blob, `photo_${index}.jpg`);
                    } else if (photo.url) {
                        // Konversi image URL ke base64 dan tambahkan
                        var img = new Image();
                        img.src = photo.url;
                        var canvas = document.createElement('canvas');
                        canvas.width = img.width;
                        canvas.height = img.height;
                        var ctx = canvas.getContext('2d');
                        ctx.drawImage(img, 0, 0);
                        var dataURL = canvas.toDataURL('image/jpeg');
                        formData.append(`capture[${index}]`, dataURL);
                    }
                });
                
                // Tambahkan captured videos (blob/file)
                capturedVideos.forEach(function(video, index) {
                    if (video.blob) {
                        formData.append(`videos[${index}]`, video.blob, `video_${index}.webm`);
                    }
                });
                
                // Tambahkan uploaded media files
                uploadedFiles.forEach(function(item, index) {
                    if (item.file) {
                        formData.append(`media[${index}]`, item.file);
                    }
                });
                
                // Tambahkan uploaded documents
                uploadedDocs.forEach(function(doc, index) {
                    formData.append(`documents[${index}]`, doc);
                });
                
                // Log untuk debugging
                console.log('Kirim data:', {
                    task_number: taskNumber,
                    title: $('#maintenanceTitle').val(),
                    description: $('#description').val(),
                    photos: capturedPhotos.length,
                    videos: capturedVideos.length,
                    uploaded_files: uploadedFiles.length,
                    documents: uploadedDocs.length
                });
                
                // Kirim dengan AJAX
                $.ajax({
                    url: '/maintenance/kanban/store',
                    method: 'POST',
                    data: formData,
                    processData: false,  // Penting untuk FormData
                    contentType: false,  // Penting untuk FormData
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    beforeSend: function() {
                        Swal.fire({
                            title: 'Menyimpan...',
                            text: 'Mohon tunggu',
                            allowOutsideClick: false,
                            showConfirmButton: false,
                            willOpen: () => {
                                Swal.showLoading();
                            }
                        });
                    },
                    success: function(response) {
                        console.log('Response:', response);
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: 'Task berhasil dibuat'
                        }).then(() => {
                            // Reset form
                            $('#maintenanceForm')[0].reset();
                            $('.capture-preview').empty();
                            $('.upload-preview').empty();
                            $('.document-list').empty();
                            
                            // Reset arrays
                            capturedPhotos = [];
                            capturedVideos = [];
                            uploadedFiles = [];
                            uploadedDocs = [];
                            
                            // Tutup modal
                            $('#createMaintenanceModal').modal('hide');
                            
                            // Reload tasks
                            loadTasks(outletId, rukoId);
                        });
                    },
                    error: function(xhr, status, error) {
                        console.error('Error Details:', {
                            status: status,
                            error: error,
                            response: xhr.responseJSON
                        });
                        
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: xhr.responseJSON?.message || 'Terjadi kesalahan saat menyimpan task'
                        });
                    }
                });
            });

            // Tambahkan variable untuk track active stream dan camera mode
            let activeStream = null;
            let currentFacingMode = 'environment'; // 'environment' untuk kamera belakang, 'user' untuk kamera depan

            // Update array untuk menyimpan media
            let capturedPhotos = [];
            let capturedVideos = [];

            // Update fungsi untuk menghapus media
            function deleteMedia(element, index) {
                const mediaWrapper = $(element).closest('.media-wrapper');
                const mediaItem = mediaWrapper.find('.media-item');
                const isVideo = mediaItem.hasClass('video-container');
                
                if (isVideo) {
                    URL.revokeObjectURL(capturedVideos[index].url);
                    capturedVideos.splice(index, 1);
                } else {
                    URL.revokeObjectURL(capturedPhotos[index].url);
                    capturedPhotos.splice(index, 1);
                }
                
                mediaWrapper.remove();
            }

            // Update fungsi capture foto
            function initializeCamera(isVideo = false) {
                if (activeStream) {
                    activeStream.getTracks().forEach(track => track.stop());
                }

                const constraints = {
                    video: {
                        facingMode: currentFacingMode,
                        width: { ideal: 1280 },
                        height: { ideal: 720 }
                    },
                    audio: isVideo // Aktifkan audio jika mode video
                };

                navigator.mediaDevices.getUserMedia(constraints)
                    .then(function(stream) {
                        activeStream = stream;
                        
                        // Buat modal content
                        const modalContent = `
                            <div class="camera-container">
                                <video id="cameraPreview" autoplay playsinline style="width: 100%; max-height: 60vh; object-fit: cover;"></video>
                                <div class="camera-controls mt-3 d-flex justify-content-center gap-2">
                                    <button type="button" class="btn btn-primary" id="switchCamera">
                                        <i class="ri-camera-switch-line"></i>
                                    </button>
                                    <button type="button" class="btn btn-success" id="captureButton">
                                        <i class="ri-camera-line"></i> Capture
                                    </button>
                                    ${isVideo ? `
                                        <button type="button" class="btn btn-danger d-none" id="stopRecording">
                                            <i class="ri-stop-circle-line"></i> Stop
                                        </button>
                                    ` : ''}
                                </div>
                            </div>
                        `;

                        // Update modal content
                        $('#mediaPreviewModal .modal-dialog').addClass('modal-lg');
                        $('#mediaPreviewContent').html(modalContent);

                        // Set up video preview
                        const video = document.getElementById('cameraPreview');
                        video.srcObject = stream;

                        // Show modal
                        $('#mediaPreviewModal').modal('show');

                        // Handle switch camera
                        $('#switchCamera').on('click', function() {
                            currentFacingMode = currentFacingMode === 'environment' ? 'user' : 'environment';
                            initializeCamera(isVideo);
                        });

                        // Handle capture
                        $('#captureButton').on('click', function() {
                            if (isVideo) {
                                startRecording(stream);
                            } else {
                                capturePhoto();
                            }
                        });
                    })
                    .catch(function(err) {
                        console.error('Error accessing camera:', err);
                        Swal.fire({
                            icon: 'error',
                            title: 'Camera Error',
                            text: 'Tidak dapat mengakses kamera atau mikrofon. Pastikan perangkat tersedia dan izin diberikan.'
                        });
                    });
            }

            // Update fungsi capturePhoto
            function capturePhoto() {
                const video = document.getElementById('cameraPreview');
                const canvas = document.createElement('canvas');
                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;
                canvas.getContext('2d').drawImage(video, 0, 0);
                
                canvas.toBlob(function(blob) {
                    const url = URL.createObjectURL(blob);
                    const photoIndex = capturedPhotos.length;

                    $('.capture-preview').append(`
                        <div class="media-wrapper">
                            <div class="media-item photo-container" data-index="${photoIndex}">
                                <img src="${url}" class="photo-preview">
                            </div>
                            <a href="javascript:void(0);" class="delete-media">
                                <i class="ri-delete-bin-line"></i>
                            </a>
                        </div>
                    `);

                    capturedPhotos.push({
                        blob: blob,
                        url: url
                    });
                }, 'image/jpeg', 0.8);
            }

            // Update fungsi recording video
            function startRecording(stream) {
                // Pastikan audio diaktifkan
                const mediaRecorder = new MediaRecorder(stream, {
                    mimeType: 'video/webm;codecs=vp8,opus'
                });
                const chunks = [];
                let isRecording = true;

                // Update UI untuk recording
                $('#captureButton').addClass('d-none');
                $('#stopRecording').removeClass('d-none');
                
                // Tambahkan indikator recording
                $('.camera-container').append(`
                    <div class="recording-indicator">
                        <span class="blink">🔴</span> Recording...
                        <span class="recording-time">00:00</span>
                    </div>
                `);

                // Timer untuk recording
                let seconds = 0;
                const timerInterval = setInterval(() => {
                    if (!isRecording) {
                        clearInterval(timerInterval);
                        return;
                    }
                    seconds++;
                    const minutes = Math.floor(seconds / 60);
                    const remainingSeconds = seconds % 60;
                    $('.recording-time').text(
                        `${minutes.toString().padStart(2, '0')}:${remainingSeconds.toString().padStart(2, '0')}`
                    );
                }, 1000);

                mediaRecorder.ondataavailable = function(e) {
                    chunks.push(e.data);
                };

                mediaRecorder.onstop = function() {
                    isRecording = false;
                    const blob = new Blob(chunks, { type: 'video/webm' });
                    const url = URL.createObjectURL(blob);
                    const videoIndex = capturedVideos.length;

                    $('.capture-preview').append(createVideoPreview(url, videoIndex, 'capture'));

                    capturedVideos.push({
                        blob: blob,
                        url: url
                    });

                    stopCamera();
                };

                // Start recording
                mediaRecorder.start(1000); // Record in 1 second chunks

                // Handle stop recording
                $('#stopRecording').on('click', function() {
                    if (mediaRecorder.state === 'recording') {
                        mediaRecorder.stop();
                        isRecording = false;
                    }
                });
            }

            // Update event handler untuk play/pause video
            $(document).on('click', '.play-btn', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                const videoContainer = $(this).closest('.video-container');
                const video = videoContainer.find('video')[0];
                const playBtn = $(this);
                const playIcon = playBtn.find('i');

                // Pause semua video lain terlebih dahulu
                $('.video-preview').each(function() {
                    const element = $(this)[0];
                    
                    // Pastikan element adalah video dengan memeriksa apakah memiliki metode pause
                    if (element && typeof element.pause === 'function') {
                        const otherVideo = element;
                        const otherPlayBtn = $(this).closest('.video-container').find('.play-btn i');
                        
                        if (otherVideo !== video && !otherVideo.paused) {
                            otherVideo.pause();
                            otherPlayBtn.removeClass('ri-pause-fill').addClass('ri-play-fill');
                        }
                    }
                });

                // Play/Pause video yang diklik
                if (video.paused) {
                    video.play();
                    playIcon.removeClass('ri-play-fill').addClass('ri-pause-fill');
                } else {
                    video.pause();
                    playIcon.removeClass('ri-pause-fill').addClass('ri-play-fill');
                }
            });

            // Tambahkan event listener untuk ended event
            $(document).on('ended', 'video', function() {
                const playIcon = $(this).closest('.video-container').find('.play-btn i');
                playIcon.removeClass('ri-pause-fill').addClass('ri-play-fill');
            });

            // Fungsi untuk stop camera
            function stopCamera() {
                if (activeStream) {
                    activeStream.getTracks().forEach(track => track.stop());
                    activeStream = null;
                }
                $('#mediaPreviewModal').modal('hide');
            }

            // Handle modal close
            $('#mediaPreviewModal').on('hidden.bs.modal', function() {
                stopCamera();
            });

            // Bind to capture buttons
            $('#capturePhoto').on('click', function() {
                initializeCamera(false);
            });

            $('#captureVideo').on('click', function() {
                initializeCamera(true);
            });

            // Tambahkan ini untuk mencegah form submit saat klik button di dalam form
            $('#maintenanceForm').on('click', 'button[type="button"]', function(e) {
                e.preventDefault();
                e.stopPropagation();
            });

            // Event handler untuk delete media
            $(document).on('click', '.delete-media', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                const mediaWrapper = $(this).closest('.media-wrapper');
                const mediaItem = mediaWrapper.find('.media-item');
                const index = mediaItem.data('index');
                
                if (mediaItem.hasClass('video-container')) {
                    // Hapus video
                    if (capturedVideos[index]) {
                        URL.revokeObjectURL(capturedVideos[index].url);
                        capturedVideos.splice(index, 1);
                    }
                } else {
                    // Hapus foto
                    if (capturedPhotos[index]) {
                        URL.revokeObjectURL(capturedPhotos[index].url);
                        capturedPhotos.splice(index, 1);
                    }
                }
                
                mediaWrapper.remove();
            });

            // Array untuk menyimpan file yang diupload
            let uploadedFiles = [];

            // Update template untuk video container (baik untuk upload maupun capture)
            function createVideoPreview(url, index, type = 'upload') {
                return `
                    <div class="media-wrapper">
                        <div class="media-item video-container" data-index="${index}">
                            <video src="${url}" class="video-preview" preload="metadata">
                                Your browser does not support the video tag.
                            </video>
                            <div class="video-overlay">
                                <a href="javascript:void(0);" class="btn btn-sm btn-light play-btn">
                                    <i class="ri-play-fill"></i>
                                </a>
                            </div>
                        </div>
                        <a href="javascript:void(0);" class="delete-media" data-type="${type}">
                            <i class="ri-delete-bin-line"></i>
                        </a>
                    </div>
                `;
            }

            // Fungsi terpisah untuk upload preview
            function handleFileUpload(files) {
                for (let i = 0; i < files.length; i++) {
                    const file = files[i];
                    const fileIndex = uploadedFiles.length;
                    const url = URL.createObjectURL(file);
                    
                    if (file.type.startsWith('image/')) {
                        $('.upload-preview').append(`
                            <div class="media-wrapper">
                                <div class="media-item photo-container" data-index="${fileIndex}">
                                    <img src="${url}" class="photo-preview">
                                </div>
                                <a href="javascript:void(0);" class="delete-upload">
                                    <i class="ri-delete-bin-line"></i>
                                </a>
                            </div>
                        `);
                    } else if (file.type.startsWith('video/')) {
                        $('.upload-preview').append(`
                            <div class="media-wrapper">
                                <div class="media-item video-container" data-index="${fileIndex}">
                                    <video src="${url}" class="video-preview" preload="metadata">
                                        Your browser does not support the video tag.
                                    </video>
                                    <div class="video-overlay">
                                        <a href="javascript:void(0);" class="btn btn-sm btn-light play-upload-btn">
                                            <i class="ri-play-fill"></i>
                                        </a>
                                    </div>
                                </div>
                                <a href="javascript:void(0);" class="delete-upload">
                                    <i class="ri-delete-bin-line"></i>
                                </a>
                            </div>
                        `);
                    }
                    
                    uploadedFiles.push({
                        file: file,
                        url: url
                    });
                }
            }

            // Event handlers untuk upload preview
            $(document).ready(function() {
                let uploadedFiles = [];

                // Handler untuk file upload
                $('#mediaUpload').on('change', function(e) {
                    handleFileUpload(e.target.files);
                });

                // Handler untuk delete uploaded files
                $(document).on('click', '.delete-upload', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    const mediaWrapper = $(this).closest('.media-wrapper');
                    const mediaItem = mediaWrapper.find('.media-item');
                    const index = mediaItem.data('index');
                    
                    if (uploadedFiles[index]) {
                        URL.revokeObjectURL(uploadedFiles[index].url);
                        uploadedFiles.splice(index, 1);
                    }
                    
                    mediaWrapper.remove();
                });

                // Handler untuk play/pause uploaded videos
                $(document).on('click', '.play-upload-btn', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    const videoContainer = $(this).closest('.video-container');
                    const video = videoContainer.find('video')[0];
                    const playIcon = $(this).find('i');

                    // Pause semua video upload lainnya
                    $('.upload-preview video').each(function() {
                        if (this !== video && !this.paused) {
                            this.pause();
                            $(this).closest('.video-container')
                                .find('.play-upload-btn i')
                                .removeClass('ri-pause-fill')
                                .addClass('ri-play-fill');
                        }
                    });

                    if (video.paused) {
                        video.play();
                        playIcon.removeClass('ri-play-fill').addClass('ri-pause-fill');
                    } else {
                        video.pause();
                        playIcon.removeClass('ri-pause-fill').addClass('ri-play-fill');
                    }
                });

                // Handler untuk video ended event (upload)
                $(document).on('ended', '.upload-preview video', function() {
                    $(this).closest('.video-container')
                        .find('.play-upload-btn i')
                        .removeClass('ri-pause-fill')
                        .addClass('ri-play-fill');
                });
            });

            // Tambahkan handler untuk document upload
            let uploadedDocs = [];

            function getFileIcon(fileType) {
                const icons = {
                    'pdf': { icon: 'ri-file-pdf-line', color: '#dc3545' },
                    'doc': { icon: 'ri-file-word-line', color: '#0d6efd' },
                    'docx': { icon: 'ri-file-word-line', color: '#0d6efd' },
                    'xls': { icon: 'ri-file-excel-line', color: '#198754' },
                    'xlsx': { icon: 'ri-file-excel-line', color: '#198754' }
                };

                return icons[fileType] || { icon: 'ri-file-line', color: '#6c757d' };
            }

            function createDocPreview(file, index) {
                const fileExt = file.name.split('.').pop().toLowerCase();
                const { icon, color } = getFileIcon(fileExt);
                
                return `
                    <div class="doc-item" data-index="${index}">
                        <div class="doc-icon" style="color: ${color}">
                            <i class="${icon} fs-2"></i>
                        </div>
                        <div class="doc-info">
                            <span class="doc-name" title="${file.name}">${file.name}</span>
                        </div>
                        <a href="javascript:void(0);" class="delete-doc">
                            <i class="ri-delete-bin-line"></i>
                        </a>
                    </div>
                `;
            }

            $('#documentUpload').on('change', function(e) {
                const files = e.target.files;
                const allowedTypes = [
                    'application/pdf',
                    'application/msword',
                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                    'application/vnd.ms-excel',
                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
                ];

                for (let i = 0; i < files.length; i++) {
                    const file = files[i];
                    
                    if (!allowedTypes.includes(file.type)) {
                        continue; // Skip file jika tipe tidak sesuai
                    }

                    const fileIndex = uploadedDocs.length;
                    $('.document-list').append(createDocPreview(file, fileIndex));
                    uploadedDocs.push(file);
                }
            });

            // Handler untuk delete dokumen
            $(document).on('click', '.delete-doc', function(e) {
                e.preventDefault();
                const docItem = $(this).closest('.doc-item');
                const index = docItem.data('index');
                
                uploadedDocs.splice(index, 1);
                docItem.remove();
            });

            // Pindahkan fungsi loadTasks ke sini (sejajar dengan fungsi-fungsi lain)
            function loadTasks(outletId, rukoId) {
                console.log('Loading tasks for outlet:', outletId, 'ruko:', rukoId);
                
                $.ajax({
                    url: '/maintenance/kanban/getTasks',
                    method: 'GET',
                    data: {
                        outlet_id: outletId,
                        ruko_id: rukoId
                    },
                    success: function(response) {
                        console.log('Tasks loaded:', response);
                        
                        // Debug task dengan status DONE
                        response.forEach(function(task) {
                            if (task.status === 'DONE') {
                                console.log('DONE task details in frontend:', {
                                    id: task.id,
                                    title: task.title,
                                    status: task.status,
                                    completed_at: task.completed_at
                                });
                            }
                        });
                        
                        // Reset semua container task
                        $('#task-list').empty();
                        $('#pr-list').empty();
                        $('#po-list').empty();
                        $('#inprogress-list').empty();
                        $('#review-list').empty();
                        $('#done-list').empty();
                        
                        // Kelompokkan task berdasarkan status
                        response.forEach(function(task) {
                            // Jika task dalam status DONE, periksa waktunya
                            if (task.status === 'DONE' && task.completed_at) {
                                const completedDate = new Date(task.completed_at);
                                const currentDate = new Date();
                                
                                // Reset jam ke 00:00:00 untuk perbandingan tanggal saja
                                completedDate.setHours(0, 0, 0, 0);
                                currentDate.setHours(0, 0, 0, 0);
                                
                                // Hitung selisih hari
                                const diffTime = Math.abs(currentDate - completedDate);
                                const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                                
                                // Jika lebih dari 3 hari, lewati task ini (tidak ditampilkan)
                                if (diffDays > 3) {
                                    console.log(`Task ${task.id} selesai pada ${task.completed_at}, sudah ${diffDays} hari, tidak ditampilkan`);
                                    return;
                                }
                            }
                            
                            let taskHtml = createTaskCard(task);
                            
                            // Masukkan task ke container yang sesuai
                            switch(task.status) {
                                case 'TASK':
                                    $('#task-list').append(taskHtml);
                                    break;
                                case 'PR':
                                    $('#pr-list').append(taskHtml);
                                    break;
                                case 'PO':
                                    $('#po-list').append(taskHtml);
                                    break;
                                case 'IN_PROGRESS':
                                    $('#inprogress-list').append(taskHtml);
                                    break;
                                case 'IN_REVIEW':
                                    $('#review-list').append(taskHtml);
                                    break;
                                case 'DONE':
                                    $('#done-list').append(taskHtml);
                                    break;
                            }
                        });
                        
                        // Update counter di setiap board
                        updateTaskCounters();
                        
                        // Inisialisasi tooltips untuk avatar members
                        initTooltips();
                        
                        // Debug: Periksa cards yang sudah dimuat
                        console.log('After loading tasks:');
                        console.log('- tasks-box count:', $('.tasks-box').length);
                        console.log('- task-card count:', $('.task-card').length);
                        console.log('- tasks-box dengan data-task-id:', $('.tasks-box[data-task-id]').length);
                        
                        // Check if evidence functions exist
                        if (typeof EvidenceApp !== 'undefined' && EvidenceApp.init) {
                            console.log('EvidenceApp exists, initializing after tasks loaded');
                            EvidenceApp.init();
                        } else {
                            console.log('EvidenceApp not found! Make sure maintenance-evidence.js is loaded.');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error loading tasks:', error);
                    }
                });
            }

            // Pindahkan fungsi createTaskCard ke sini juga
            function createTaskCard(task, boardId) {
                // Debug task info
                console.log(`Creating task card for task ID: ${task.id}, status: ${task.status}`);
                
                // Format created_at date untuk tampilan
                const createdDate = task.created_at ? new Date(task.created_at) : new Date();
                const formattedDate = createdDate.toLocaleDateString('id-ID', {
                    day: '2-digit',
                    month: 'short',
                    year: 'numeric'
                });
                
                // Siapkan informasi creator/created by
                const createdBy = task.created_by_name || 'System';
                
                // Format dan hitung selisih due date untuk warna
                let dueDateClass = 'text-success'; // Default hijau
                let dueDate = task.due_date ? new Date(task.due_date) : null;
                let dueDateFormatted = 'Not set';
                
                if (dueDate) {
                    // Format tanggal due date
                    dueDateFormatted = dueDate.toLocaleDateString('id-ID', {
                        day: '2-digit',
                        month: 'short',
                        year: 'numeric'
                    });
                    
                    // Hitung selisih hari dengan tanggal hari ini
                    const today = new Date();
                    today.setHours(0, 0, 0, 0); // Reset waktu ke 00:00:00
                    dueDate.setHours(0, 0, 0, 0); // Reset waktu ke 00:00:00
                    
                    const diffTime = dueDate - today;
                    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                    
                    // Set warna berdasarkan selisih hari
                    if (diffDays < 0) {
                        dueDateClass = 'text-danger fw-bold'; // Sudah lewat deadline
                    } else if (diffDays < 2) {
                        dueDateClass = 'text-danger'; // Kurang dari 2 hari
                    } else if (diffDays < 3) {
                        dueDateClass = 'text-warning'; // Kurang dari 3 hari
                    }
                }
                
                // TAMBAHKAN KODE INI: Format tanggal completed_at jika ada dan task status DONE
                let completedDateHtml = '';
                if (task.status === 'DONE' && task.completed_at) {
                    console.log("Task completed_at:", task.completed_at);
                    
                    // Format tanggal
                    const completedDate = new Date(task.completed_at);
                    const formattedCompletedDate = completedDate.toLocaleDateString('id-ID', {
                        day: '2-digit',
                        month: 'short',
                        year: 'numeric'
                    });
                    
                    // Tentukan warna
                    let completedDateClass = 'text-dark';
                    if (dueDate) {
                        // Reset waktu untuk perbandingan tanggal saja
                        const dueDateOnly = new Date(dueDate.getTime()).setHours(0,0,0,0);
                        const completedDateOnly = new Date(completedDate.getTime()).setHours(0,0,0,0);
                        
                        if (completedDateOnly < dueDateOnly) {
                            completedDateClass = 'text-success';
                        } else if (completedDateOnly > dueDateOnly) {
                            completedDateClass = 'text-danger';
                        }
                    }
                    
                    // Override warna due date jika status DONE
                    dueDateClass = 'text-dark';
                    
                    // Buat HTML untuk completed date
                    completedDateHtml = `
                        <div class="me-3 mb-1">
                            <small class="${completedDateClass}">
                                <i class="ri-check-double-line me-1"></i> Selesai: ${formattedCompletedDate}
                            </small>
                        </div>
                    `;
                }
                
                // Tentukan warna badge untuk priority berdasarkan nilai
                let priorityBadgeClass = 'bg-primary';
                if (task.priority_name) {
                    if (task.priority_name.includes('IMPORTANT VS URGENT')) {
                        priorityBadgeClass = 'bg-danger';
                    } else if (task.priority_name.includes('IMPORTANT VS NOT URGENT')) {
                        priorityBadgeClass = 'bg-warning';
                    } else if (task.priority_name.includes('NOT IMPORTANT VS URGENT')) {
                        priorityBadgeClass = 'bg-info';
                    }
                }
                
                // Buat styling label dengan warna dari database
                let labelStyle = '';
                if (task.label_color) {
                    // Gunakan nilai color langsung sebagai background dengan text putih
                    labelStyle = `background-color: ${task.label_color}; color: white;`;
                }
                
                // Tentukan icon yang sesuai untuk setiap jenis label - gunakan case insensitive matching
                let labelIcon = '<i class="ri-price-tag-3-line me-1"></i>'; // Default icon
                
                if (task.label_name) {
                    // Lowercase label name untuk mempermudah matching
                    const labelNameLower = task.label_name.toLowerCase();
                    
                    // Matching dengan includes untuk lebih fleksibel
                    if (labelNameLower.includes('heat')) {
                        labelIcon = '<i class="ri-fire-line me-1"></i>'; // Icon api untuk heater
                    } else if (labelNameLower.includes('refrig') || labelNameLower.includes('cold')) {
                        labelIcon = '<i class="ri-celsius-line me-1"></i>'; // Icon suhu untuk refrigeration
                    } else if (labelNameLower.includes('civil') || labelNameLower.includes('build')) {
                        labelIcon = '<i class="ri-building-line me-1"></i>'; // Icon bangunan untuk civil
                    } else if (labelNameLower.includes('gas')) {
                        labelIcon = '<i class="ri-gas-station-line me-1"></i>'; // Icon gas station untuk gas
                    } else if (labelNameLower.includes('mach') || labelNameLower.includes('equip')) {
                        labelIcon = '<i class="ri-tools-line me-1"></i>'; // Icon tools untuk machinery
                    } else if (labelNameLower.includes('electric')) {
                        labelIcon = '<i class="ri-thunderstorms-line me-1"></i>'; // Icon listrik untuk electricity
                    } else if (labelNameLower.includes('other')) {
                        labelIcon = '<i class="ri-more-line me-1"></i>'; // Icon lainnya untuk others
                    }
                }
                
                // Persiapkan deskripsi dengan fungsi show more/less
                let description = task.description || '';
                let shortDesc = description;
                let showMoreBtn = '';
                
                // Jika deskripsi lebih dari 100 karakter, potong dan tambahkan tombol show more
                if (description.length > 100) {
                    shortDesc = description.substring(0, 100) + '...';
                    showMoreBtn = `
                        <button class="btn btn-sm btn-link show-more-btn" 
                                onclick="toggleDescription(this, ${task.id})" 
                                data-expanded="false" 
                                data-task-id="${task.id}">
                            Show more <i class="ri-arrow-down-s-line align-middle"></i>
                        </button>
                    `;
                }
                
                // Variabel untuk menyimpan HTML foto dan video
                let mediaPreview = '';
                let videoPreview = '';
                
                // Buat preview foto dari maintenance_media
                let photoCount = 0;
                let photoItems = [];
                
                // Cek apakah task memiliki foto
                if (task.photos && task.photos.length > 0) {
                    photoCount = task.photos.length;
                    
                    if (photoCount > 0) {
                        // Buat array untuk item media (untuk lightbox/modal)
                        photoItems = task.photos.map(photo => ({
                            id: photo.id,
                            path: `/storage/app/public/${photo.file_path}`,
                            name: photo.file_name
                        }));
                        
                        // Buat HTML untuk preview (max 3 photos)
                        let previewHTML = '';
                        for (let i = 0; i < Math.min(3, photoCount); i++) {
                            previewHTML += `
                                <div class="photo-preview-item" data-media-index="${i}" data-task-id="${task.id}">
                                    <img src="/storage/app/public/${task.photos[i].file_path}" alt="${task.photos[i].file_name}" 
                                         class="rounded-circle" style="width: 40px; height: 40px; object-fit: cover; border-radius: 50% !important;">
                                </div>
                            `;
                        }
                        
                        // Jika photos lebih dari 3, tambahkan +N badge
                        if (photoCount > 3) {
                            previewHTML += `
                                <div class="photo-preview-more" data-task-id="${task.id}">
                                    <span class="badge rounded-pill bg-secondary">+${photoCount - 3}</span>
                                </div>
                            `;
                        }
                        
                        // Finalisasi media preview HTML dengan judul
                        mediaPreview = `
                            <div class="preview-section mt-2">
                                <div class="preview-title mb-1">
                                    <span class="fw-bold text-muted fs-sm"><i class="ri-image-line me-1"></i>Photos (${photoCount})</span>
                                </div>
                                <div class="task-media-preview d-flex align-items-center gap-2" 
                                     data-task-id="${task.id}" data-media-count="${photoCount}" 
                                     data-media-items='${JSON.stringify(photoItems)}'>
                                    ${previewHTML}
                                </div>
                            </div>
                        `;
                    }
                }
                
                // Buat preview video dari maintenance_media
                let videoCount = 0;
                let videoItems = [];
                
                // Cek apakah task memiliki video
                if (task.videos && task.videos.length > 0) {
                    videoCount = task.videos.length;
                    
                    if (videoCount > 0) {
                        // Buat array untuk item video (untuk modal)
                        videoItems = task.videos.map(video => ({
                            id: video.id,
                            path: `/storage/app/public/${video.file_path}`,
                            name: video.file_name
                        }));
                        
                        // Buat HTML untuk preview (max 2 videos)
                        let previewHTML = '';
                        for (let i = 0; i < Math.min(2, videoCount); i++) {
                            previewHTML += `
                                <div class="video-preview-item" data-video-index="${i}" data-task-id="${task.id}">
                                    <div class="video-thumbnail rounded" style="width: 60px; height: 40px; background: #000; position: relative;">
                                        <i class="ri-play-circle-line position-absolute" style="top: 50%; left: 50%; transform: translate(-50%, -50%); font-size: 24px; color: white;"></i>
                                    </div>
                                </div>
                            `;
                        }
                        
                        // Jika videos lebih dari 2, tambahkan +N badge
                        if (videoCount > 2) {
                            previewHTML += `
                                <div class="video-preview-more" data-task-id="${task.id}">
                                    <span class="badge rounded-pill bg-secondary">+${videoCount - 2}</span>
                                </div>
                            `;
                        }
                        
                        // Finalisasi video preview HTML dengan judul
                        videoPreview = `
                            <div class="preview-section mt-2">
                                <div class="preview-title mb-1">
                                    <span class="fw-bold text-muted fs-sm"><i class="ri-movie-line me-1"></i>Videos (${videoCount})</span>
                                </div>
                                <div class="task-video-preview d-flex align-items-center gap-2" 
                                     data-task-id="${task.id}" data-video-count="${videoCount}" 
                                     data-video-items='${JSON.stringify(videoItems)}'>
                                    ${previewHTML}
                                </div>
                            </div>
                        `;
                    }
                }
                
                // Buat preview dokumen dari maintenance_documents
                let docCount = 0;
                let docItems = [];
                
                // Cek apakah task memiliki dokumen
                if (task.documents && task.documents.length > 0) {
                    docCount = task.documents.length;
                    
                    if (docCount > 0) {
                        // Buat array untuk item dokumen
                        docItems = task.documents.map(doc => ({
                            id: doc.id,
                            path: `/storage/app/public/${doc.file_path}`,
                            name: doc.file_name,
                            type: doc.file_type
                        }));
                        
                        // Buat HTML untuk preview dokumen (max 3 docs)
                        let previewHTML = '';
                        for (let i = 0; i < Math.min(3, docCount); i++) {
                            const doc = task.documents[i];
                            const fileName = doc.file_name;
                            const fileExtension = fileName.split('.').pop().toLowerCase();
                            
                            // Tentukan ikon dan class warna berdasarkan ekstensi file
                            let fileIcon = 'ri-file-text-line';
                            let colorClass = 'doc-type-default';
                            
                            if (['pdf'].includes(fileExtension)) {
                                fileIcon = 'ri-file-pdf-line';
                                colorClass = 'doc-type-pdf';
                            } else if (['doc', 'docx'].includes(fileExtension)) {
                                fileIcon = 'ri-file-word-line';
                                colorClass = 'doc-type-word';
                            } else if (['xls', 'xlsx'].includes(fileExtension)) {
                                fileIcon = 'ri-file-excel-line';
                                colorClass = 'doc-type-excel';
                            } else if (['ppt', 'pptx'].includes(fileExtension)) {
                                fileIcon = 'ri-file-ppt-line';
                                colorClass = 'doc-type-ppt';
                            } else if (['zip', 'rar', '7z'].includes(fileExtension)) {
                                fileIcon = 'ri-file-zip-line';
                                colorClass = 'doc-type-zip';
                            }
                            
                            // Potong nama file jika terlalu panjang
                            const shortFileName = fileName.length > 15 ? fileName.substring(0, 12) + '...' : fileName;
                            
                            previewHTML += `
                                <div class="doc-preview-item" data-doc-index="${i}" data-task-id="${task.id}" title="${fileName}">
                                    <div class="doc-icon">
                                        <i class="${fileIcon} doc-type-icon ${colorClass}"></i>
                                    </div>
                                    <div class="doc-name">
                                        ${shortFileName}
                                    </div>
                                </div>
                            `;
                        }
                        
                        // Jika dokumen lebih dari 3, tambahkan +N badge
                        if (docCount > 3) {
                            previewHTML += `
                                <div class="doc-preview-more" data-task-id="${task.id}">
                                    <span class="badge rounded-pill bg-secondary">+${docCount - 3}</span>
                                </div>
                            `;
                        }
                        
                        // Finalisasi document preview HTML dengan judul
                        documentPreview = `
                            <div class="preview-section mt-2">
                                <div class="preview-title mb-1">
                                    <span class="fw-bold text-muted fs-sm"><i class="ri-file-list-line me-1"></i>Documents (${docCount})</span>
                                </div>
                                <div class="task-doc-preview" 
                                     data-task-id="${task.id}" data-doc-count="${docCount}" 
                                     data-doc-items='${JSON.stringify(docItems)}'>
                                    ${previewHTML}
                                </div>
                            </div>
                        `;
                    }
                }
                
                // Hitung total attachment
                let totalPhotos = task.photos && task.photos.length ? task.photos.length : 0;
                let totalVideos = task.videos && task.videos.length ? task.videos.length : 0;
                let totalDocs = task.documents && task.documents.length ? task.documents.length : 0;
                let totalAttachments = totalPhotos + totalVideos + totalDocs;
                
                // Buat preview foto, video, dan dokumen seperti sebelumnya
                // ...
                
                // Gabungkan semua media preview
                const mediaContentHTML = (mediaPreview || videoPreview || documentPreview) ? `
                    <div class="attachment-content" style="display: none;">
                        ${mediaPreview}
                        ${videoPreview}
                        ${documentPreview}
                    </div>
                ` : '';
                
                // Buat tombol expand/collapse jika ada attachment
                const attachmentToggleHTML = totalAttachments > 0 ? `
                    <div class="attachment-toggle mt-2">
                        <button class="btn btn-sm btn-light text-left w-100 d-flex align-items-center justify-content-between attachment-toggle-btn" 
                                data-task-id="${task.id}">
                            <span>
                                <i class="ri-attachment-2 me-1"></i>
                                Attachments (${totalAttachments})
                            </span>
                            <i class="ri-arrow-down-s-line attachment-toggle-icon"></i>
                        </button>
                    </div>
                ` : '';
                
                // Gabungkan attachment toggle dan content jika ada attachment
                const mediaSection = totalAttachments > 0 ? `
                    <div class="media-section">
                        ${attachmentToggleHTML}
                        ${mediaContentHTML}
                    </div>
                ` : '';
                
                // Dapatkan jumlah komentar, pastikan angka valid
                const commentCount = task.comment_count || 0;
                
                // Buat button untuk create PR, hanya tampilkan jika status task adalah 'PR'
                let createPrButton = task.status === 'PR' ? `
                    <button class="btn btn-sm btn-outline-primary create-pr-btn" data-task-id="${task.id}">
                        <i class="ri-file-list-line me-1"></i> PR
                    </button>
                ` : '';

                // Tombol evidence hanya muncul jika:
                // 1. Task status = IN_REVIEW
                // 2. User dengan division_id=20 dan status=A, atau
                // 3. User dengan id_role=5af56935b011a dan status=A
                // 4. Belum ada data evidence untuk task tersebut
                let captureEvidenceButton = '';
                
                // Cek apakah user memenuhi kriteria
                const userCanAddEvidence = (
                    ('{{ Auth::user()->division_id }}' == '20' && '{{ Auth::user()->status }}' == 'A') ||
                    ('{{ Auth::user()->id_role }}' == '5af56935b011a' && '{{ Auth::user()->status }}' == 'A')
                );
                
                // Cek apakah task memenuhi kriteria
                if (userCanAddEvidence && task.status === 'IN_REVIEW') {
                    // Cek apakah sudah ada evidence untuk task ini
                    $.ajax({
                        url: `/maintenance/kanban/task/${task.id}/evidence`,
                        method: 'GET',
                        async: false, // Agar code menunggu hasil ajax
                        success: function(response) {
                            // Jika tidak ada evidence, tampilkan tombol
                            if (!response.data || response.data.length === 0) {
                                captureEvidenceButton = `
                                    <button class="btn btn-sm btn-outline-success new-evidence-btn me-2" 
                                            data-task-id="${task.id}" 
                                            title="Upload Evidence">
                                        <i class="ri-camera-line me-1"></i> Evidence
                                    </button>
                                `;
                            }
                        }
                    });
                }
                
                // Buat footer card dengan tombol comment
                const cardFooter = `
                    <div class="card-footer bg-transparent p-2 d-flex justify-content-between align-items-center">
                        <div>
                            ${createPrButton}
                            ${captureEvidenceButton}
                        </div>
                        <div class="task-actions">
                            ${isTaskInPoBoard(task) ? `
                                <button class="btn btn-sm btn-info view-po-btn me-2" 
                                        data-task-id="${task.id}" 
                                        title="View PO List">
                                    <i class="ri-file-list-line"></i> PO
                                </button>
                            ` : ''}
                            <button class="btn btn-sm btn-light task-comment-btn" data-task-id="${task.id}">
                                <i class="ri-chat-1-line me-1"></i>
                                <span>Comments</span>
                                ${commentCount > 0 ? `<span class="badge bg-primary ms-1">${commentCount}</span>` : ''}
                            </button>
                        </div>
                    </div>
                `;
                
                // Tambahkan kode untuk members section
                let membersHTML = '';
                
                if (task.members && task.members.length > 0) {
                    // Urutkan members untuk menampilkan maksimal 5 avatar
                    const displayMembers = task.members.slice(0, 5);
                    const extraMembers = task.members.length > 5 ? task.members.length - 5 : 0;
                    
                    // Buat avatar untuk setiap member
                    let membersAvatarsHTML = '';
                    displayMembers.forEach(member => {
                        const initials = getInitials(member.name);
                        const avatarColor = getAvatarColor(member.id);
                        
                        membersAvatarsHTML += `
                            <div class="member-avatar" 
                                 style="background-color: ${avatarColor};"
                                 data-name="${member.name}">
                                ${initials}
                            </div>
                        `;
                    });
                    
                    // Jika ada extra members, tambahkan badge +N
                    if (extraMembers > 0) {
                        membersAvatarsHTML += `
                            <div class="member-avatar member-more"
                                 data-name="${extraMembers} more members">
                                +${extraMembers}
                            </div>
                        `;
                    }
                    
                    // Bungkus dalam container dengan judul (mirip dengan photos)
                    membersHTML = `
                        <div class="preview-section mt-2">
                            <div class="preview-title mb-1">
                                <span class="fw-bold text-muted fs-sm"><i class="ri-team-line me-1"></i>Members (${task.members.length})</span>
                            </div>
                            <div class="task-members">
                                <div class="members-wrapper">
                                    ${membersAvatarsHTML}
                                </div>
                            </div>
                        </div>
                    `;
                }
                
                // Buat HTML untuk PR stats, hanya untuk board PR
                let prStatsHTML = '';

                // Hanya tampilkan PR stats jika task berada di board PR
                if (task.status === 'PR') {
                    prStatsHTML = `
                        <div class="preview-section mt-2 pr-stats-section">
                            <div class="preview-title mb-1 d-flex justify-content-between align-items-center">
                                <span class="fw-bold text-muted fs-sm"><i class="ri-file-list-3-line me-1"></i>PR Stats</span>
                                <div class="pr-stats-loader spinner-border spinner-border-sm text-secondary d-none" id="prStatsLoader-${task.id}" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                            </div>
                            <div class="pr-stats-container p-2" style="background-color: #f8f9fa; border-radius: 6px;">
                                <div class="row gx-2">
                                    <div class="col-3">
                                        <div class="d-flex flex-column align-items-center">
                                            <span class="pr-stat-value fw-bold fs-5" id="prTotal-${task.id}">-</span>
                                            <span class="pr-stat-label fs-xs text-muted">Total</span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="d-flex flex-column align-items-center">
                                            <span class="pr-stat-value fw-bold fs-5 text-success" id="prApproved-${task.id}">-</span>
                                            <span class="pr-stat-label fs-xs text-muted">Approved</span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="d-flex flex-column align-items-center">
                                            <span class="pr-stat-value fw-bold fs-5 text-danger" id="prRejected-${task.id}">-</span>
                                            <span class="pr-stat-label fs-xs text-muted">Rejected</span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="d-flex flex-column align-items-center">
                                            <span class="pr-stat-value fw-bold fs-5 text-warning" id="prDraft-${task.id}">-</span>
                                            <span class="pr-stat-label fs-xs text-muted">Draft</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;

                    // Load PR stats after card is created
                    setTimeout(() => loadPrStats(task.id), 100);
                }
                
                // Buat HTML untuk PO stats, untuk board PO, In Progress, In Review, dan Done
                let poStatsHTML = '';
                
                // Tampilkan PO stats jika task berada di board PO, In Progress, In Review, atau Done
                if (['PO', 'IN_PROGRESS', 'IN_REVIEW', 'DONE'].includes(task.status)) {
                    poStatsHTML = `
                        <div class="preview-section mt-2 po-stats-section">
                            <div class="preview-title mb-1 d-flex justify-content-between align-items-center">
                                <span class="fw-bold text-muted fs-sm"><i class="ri-shopping-cart-2-line me-1"></i>PO Stats</span>
                                <div class="po-stats-loader spinner-border spinner-border-sm text-secondary d-none" id="poStatsLoader-${task.id}" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                            </div>
                            <div class="po-stats-container p-2" style="background-color: #f8f9fa; border-radius: 6px;">
                                <div class="row gx-2">
                                    <div class="col-4">
                                        <div class="d-flex flex-column align-items-center">
                                            <span class="po-stat-value fw-bold fs-5" id="poTotal-${task.id}">-</span>
                                            <span class="po-stat-label fs-xs text-muted">Total</span>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="d-flex flex-column align-items-center">
                                            <span class="po-stat-value fw-bold fs-5 text-success" id="poApproved-${task.id}">-</span>
                                            <span class="po-stat-label fs-xs text-muted">Approved</span>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="d-flex flex-column align-items-center">
                                            <span class="po-stat-value fw-bold fs-5 text-danger" id="poRejected-${task.id}">-</span>
                                            <span class="po-stat-label fs-xs text-muted">Rejected</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row gx-2 mt-2">
                                    <div class="col-4">
                                        <div class="d-flex flex-column align-items-center">
                                            <span class="po-stat-value fw-bold fs-5 text-info" id="poReceived-${task.id}">-</span>
                                            <span class="po-stat-label fs-xs text-muted">Received</span>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="d-flex flex-column align-items-center">
                                            <span class="po-stat-value fw-bold fs-5 text-primary" id="poPayment-${task.id}">-</span>
                                            <span class="po-stat-label fs-xs text-muted">Payment</span>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="d-flex flex-column align-items-center">
                                            <span class="po-stat-value fw-bold fs-5 text-warning" id="poDraft-${task.id}">-</span>
                                            <span class="po-stat-label fs-xs text-muted">Draft & Pending</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                    
                    // Load PO stats after card is created
                    setTimeout(() => loadPoStats(task.id), 100);
                }
                
                // Lanjutkan dengan kode yang sudah ada sebelumnya, integrasikan membersHTML di return statement
                
                return `
                    <div class="card tasks-box" data-task-id="${task.id}" data-current-status="${task.status}">
                        <div class="card-body">
                            <div class="d-flex mb-1">
                                <div class="flex-grow-1 fs-md">
                                    <a href="#!">${task.task_number}</a>
                                </div>
                                <div class="flex-shrink-0">
                                    <a href="javascript:void(0);" class="text-muted" data-bs-toggle="dropdown">
                                        <i class="ri-more-fill"></i>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item view-timeline" href="javascript:void(0);" data-task-id="${task.id}"><i class="ri-calendar-event-line align-bottom me-2 text-muted"></i> Timeline</a></li>
                                        <li><a class="dropdown-item edit-task" href="javascript:void(0);" data-task-id="${task.id}"><i class="ri-edit-2-line align-bottom me-2 text-muted"></i> Assign Members</a></li>

                                        <li><a class="dropdown-item delete-task" href="javascript:void(0);" data-task-id="${task.id}"><i class="ri-delete-bin-5-line align-bottom me-2 text-muted"></i> Delete</a></li>
                                    </ul>
                                </div>
                            </div>
                            
                            <!-- INFORMASI: created by, created at, due date -->
                            <div class="d-flex align-items-center flex-wrap mb-2">
                                <div class="me-3 mb-1">
                                    <small class="text-muted">
                                        <i class="ri-user-line me-1"></i> ${createdBy}
                                    </small>
                                </div>
                                <div class="me-3 mb-1">
                                    <small class="text-muted">
                                        <i class="ri-calendar-line me-1"></i> ${formattedDate}
                                    </small>
                                </div>
                                <div class="mb-1">
                                    <small class="${dueDateClass}">
                                        <i class="ri-time-line me-1"></i> Due: ${dueDateFormatted}
                                    </small>
                                </div>
                                ${completedDateHtml}
                            </div>
                            
                            <!-- Label dan Priority badges dengan warna dan icon -->
                            <div class="mb-3">
                                ${task.priority_name ? `<span class="badge ${priorityBadgeClass} me-1"><i class="ri-flag-line me-1"></i>${task.priority_name}</span>` : ''}
                                ${task.label_name ? `<span class="badge me-1" style="${labelStyle}">${labelIcon}${task.label_name}</span>` : ''}
                            </div>
                            
                            <!-- Title Task - UPPERCASE dan ukuran font lebih besar -->
                            <h5 class="text-truncate task-title fw-bold mb-2">
                                <a href="#!" class="text-reset text-uppercase">${task.title}</a>
                            </h5>
                            
                            <!-- Deskripsi dengan tombol show more/less -->
                            <div class="description-container">
                                <p class="text-muted fs-md mb-1 short-desc-${task.id}">${shortDesc}</p>
                                <p class="text-muted fs-md mb-1 full-desc-${task.id}" style="display: none;">${description}</p>
                                ${showMoreBtn}
                            </div>
                            
                            <!-- Members section -->
                            ${membersHTML}
                            
                            <!-- Attachment section dengan toggle -->
                            ${mediaSection}
                            
                            <!-- Tambahkan sebelum mediaSection -->
                            ${prStatsHTML}
                            ${poStatsHTML}
                        </div>
                        
                        <!-- Card footer dengan tombol comment -->
                        ${cardFooter}
                    </div>
                `;
            }

            // Pindahkan fungsi updateTaskCounters ke sini juga
            function updateTaskCounters() {
                $('.tasks-list').each(function() {
                    let taskCount = $(this).find('.tasks-box').length;
                    $(this).find('.totaltask-badge').text(taskCount);
                });
            }

            // Handler untuk ruko tetap sama
            $('#rukoId').on('change', function() {
                const outletId = $('#outletId').val();
                const rukoId = $(this).val();
                
                if (outletId == '1' && rukoId) {
                    loadTasks(outletId, rukoId);
                }
            });

            // Tambahkan event listener untuk show more/less description
            $(document).on('click', '.show-more-btn', function() {
                const taskId = $(this).data('task-id');
                const isExpanded = $(this).data('expanded') === true;
                
                if (isExpanded) {
                    // Hide full description, show short description
                    $(`.full-desc-${taskId}`).hide();
                    $(`.short-desc-${taskId}`).show();
                    $(this).html('Show more <i class="ri-arrow-down-s-line align-middle"></i>');
                    $(this).data('expanded', false);
                } else {
                    // Show full description, hide short description
                    $(`.short-desc-${taskId}`).hide();
                    $(`.full-desc-${taskId}`).show();
                    $(this).html('Show less <i class="ri-arrow-up-s-line align-middle"></i>');
                    $(this).data('expanded', true);
                }
            });

            // Variabel untuk menyimpan state gallery
            let currentGalleryTask = null;
            let currentGalleryIndex = 0;
            let galleryMediaItems = [];
            
            // Event untuk klik pada preview foto
            $(document).on('click', '.photo-preview-item', function() {
                const taskId = $(this).data('task-id');
                const mediaIndex = $(this).data('media-index');
                const mediaContainer = $(this).closest('.task-media-preview');
                const mediaItems = JSON.parse(mediaContainer.attr('data-media-items'));
                
                // Simpan state gallery
                currentGalleryTask = taskId;
                currentGalleryIndex = mediaIndex;
                galleryMediaItems = mediaItems;
                
                // Tampilkan modal dengan foto yang dipilih
                showMediaModal(mediaIndex);
            });
            
            // Event untuk klik pada badge +N more
            $(document).on('click', '.photo-preview-more', function() {
                const taskId = $(this).data('task-id');
                const mediaContainer = $(this).closest('.task-media-preview');
                const mediaItems = JSON.parse(mediaContainer.attr('data-media-items'));
                
                // Simpan state gallery
                currentGalleryTask = taskId;
                currentGalleryIndex = 3; // Mulai dari foto ke-4
                galleryMediaItems = mediaItems;
                
                // Tampilkan modal dengan foto yang dipilih
                showMediaModal(currentGalleryIndex);
            });
            
            // Event untuk tombol navigasi gallery
            $('.prev-media').on('click', function() {
                if (currentGalleryIndex > 0) {
                    currentGalleryIndex--;
                    updateMediaModal();
                }
            });
            
            $('.next-media').on('click', function() {
                if (currentGalleryIndex < galleryMediaItems.length - 1) {
                    currentGalleryIndex++;
                    updateMediaModal();
                }
            });
            
            // Function untuk menampilkan modal gallery
            function showMediaModal(index) {
                if (!galleryMediaItems || galleryMediaItems.length === 0) return;
                
                // Reset index jika melebihi batas
                if (index >= galleryMediaItems.length) {
                    index = 0;
                }
                
                // Update state
                currentGalleryIndex = index;
                
                // Update modal
                updateMediaModal();
                
                // Tampilkan modal
                $('#mediaGalleryModal').modal('show');
            }
            
            // Function untuk update tampilan modal gallery
            function updateMediaModal() {
                const media = galleryMediaItems[currentGalleryIndex];
                
                // Update gambar
                $('.media-modal-image').attr('src', media.path).attr('alt', media.name);
                
                // Update counter
                $('.current-index').text(currentGalleryIndex + 1);
                $('.total-count').text(galleryMediaItems.length);
                
                // Update tombol download
                $('.download-media').attr('href', media.path);
                $('.download-media').attr('download', media.name);
                
                // Toggle tombol prev/next
                $('.prev-media').toggleClass('d-none', currentGalleryIndex === 0);
                $('.next-media').toggleClass('d-none', currentGalleryIndex === galleryMediaItems.length - 1);
            }
            
            // Reset gallery state saat modal ditutup
            $('#mediaGalleryModal').on('hidden.bs.modal', function() {
                currentGalleryTask = null;
                currentGalleryIndex = 0;
                galleryMediaItems = [];
            });
            
            // Tambahkan event listener untuk show more/less description jika belum ada
            if (typeof toggleDescription !== 'function') {
                // Using event delegation for show more/less button
                $(document).on('click', '.show-more-btn', function() {
                    const taskId = $(this).data('task-id');
                    const isExpanded = $(this).data('expanded') === true;
                    
                    if (isExpanded) {
                        // Hide full description, show short description
                        $(`.full-desc-${taskId}`).hide();
                        $(`.short-desc-${taskId}`).show();
                        $(this).html('Show more <i class="ri-arrow-down-s-line align-middle"></i>');
                        $(this).data('expanded', false);
                    } else {
                        // Show full description, hide short description
                        $(`.short-desc-${taskId}`).hide();
                        $(`.full-desc-${taskId}`).show();
                        $(this).html('Show less <i class="ri-arrow-up-s-line align-middle"></i>');
                        $(this).data('expanded', true);
                    }
                });
            }

            // Event untuk tombol download
            $(document).on('click', '.download-media', function(e) {
                const url = $(this).attr('href');
                const filename = $(this).attr('download');
                
                // Modern browsers support the download attribute
                if ('download' in document.createElement('a')) {
                    // Do nothing, let the browser handle it
                    return true;
                }
                
                // Fallback for browsers that don't support the download attribute
                e.preventDefault();
                
                // Create a temporary link element
                const link = document.createElement('a');
                link.href = url;
                link.target = '_blank';
                link.download = filename;
                
                // Append to the body
                document.body.appendChild(link);
                
                // Trigger click
                link.click();
                
                // Clean up
                document.body.removeChild(link);
                
                return false;
            });

            // Variabel untuk state video gallery
            let currentVideoTask = null;
            let currentVideoIndex = 0;
            let videoItems = [];
            let videoPlayer = null;
            
            // Event untuk klik pada preview video
            $(document).on('click', '.video-preview-item', function() {
                const taskId = $(this).data('task-id');
                const videoIndex = $(this).data('video-index');
                const videoContainer = $(this).closest('.task-video-preview');
                const videos = JSON.parse(videoContainer.attr('data-video-items'));
                
                // Simpan state
                currentVideoTask = taskId;
                currentVideoIndex = videoIndex;
                videoItems = videos;
                
                // Tampilkan modal video
                showVideoModal(videoIndex);
            });
            
            // Event untuk klik pada badge +N more videos
            $(document).on('click', '.video-preview-more', function() {
                const taskId = $(this).data('task-id');
                const videoContainer = $(this).closest('.task-video-preview');
                const videos = JSON.parse(videoContainer.attr('data-video-items'));
                
                // Simpan state
                currentVideoTask = taskId;
                currentVideoIndex = 2; // Mulai dari video ke-3
                videoItems = videos;
                
                // Tampilkan modal video
                showVideoModal(currentVideoIndex);
            });
            
            // Event untuk tombol navigasi video
            $('.prev-video').on('click', function() {
                if (currentVideoIndex > 0) {
                    videoPlayer.pause(); // Pause current video
                    currentVideoIndex--;
                    updateVideoModal();
                }
            });
            
            $('.next-video').on('click', function() {
                if (currentVideoIndex < videoItems.length - 1) {
                    videoPlayer.pause(); // Pause current video
                    currentVideoIndex++;
                    updateVideoModal();
                }
            });
            
            // Function untuk menampilkan modal video
            function showVideoModal(index) {
                if (!videoItems || videoItems.length === 0) return;
                
                // Reset index jika melebihi batas
                if (index >= videoItems.length) {
                    index = 0;
                }
                
                // Update state
                currentVideoIndex = index;
                
                // Set reference ke video player
                videoPlayer = document.querySelector('.video-modal-player');
                
                // Update modal
                updateVideoModal();
                
                // Tampilkan modal
                $('#videoPlayerModal').modal('show');
            }
            
            // Function untuk update tampilan modal video
            function updateVideoModal() {
                const video = videoItems[currentVideoIndex];
                
                // Update video source
                videoPlayer.src = video.path;
                videoPlayer.load(); // Reload the video
                
                // Update counter
                $('.video-modal-counter .current-index').text(currentVideoIndex + 1);
                $('.video-modal-counter .total-count').text(videoItems.length);
                
                // Toggle tombol prev/next
                $('.prev-video').toggleClass('d-none', currentVideoIndex === 0);
                $('.next-video').toggleClass('d-none', currentVideoIndex === videoItems.length - 1);
            }
            
            // Reset video state dan pause video saat modal ditutup
            $('#videoPlayerModal').on('hidden.bs.modal', function() {
                if (videoPlayer) {
                    videoPlayer.pause();
                }
                currentVideoTask = null;
                currentVideoIndex = 0;
                videoItems = [];
            });

            // Event untuk klik pada preview dokumen
            $(document).on('click', '.doc-preview-item', function() {
                const taskId = $(this).data('task-id');
                const docIndex = $(this).data('doc-index');
                const docContainer = $(this).closest('.task-doc-preview');
                const docItems = JSON.parse(docContainer.attr('data-doc-items'));
                
                // Buka dokumen di tab baru
                const docUrl = docItems[docIndex].path;
                window.open(docUrl, '_blank');
            });
            
            // Event untuk klik pada badge +N more documents
            $(document).on('click', '.doc-preview-more', function() {
                const taskId = $(this).data('task-id');
                const docContainer = $(this).closest('.task-doc-preview');
                const docItems = JSON.parse(docContainer.attr('data-doc-items'));
                
                // Tampilkan modal dengan daftar semua dokumen
                showDocumentsModal(docItems);
            });
            
            // Function untuk menampilkan modal daftar dokumen
            function showDocumentsModal(documents) {
                if (!documents || documents.length === 0) return;
                
                // Bersihkan tabel
                $('#docListTableBody').empty();
                
                // Isi tabel dengan daftar dokumen
                documents.forEach((doc, index) => {
                    const fileName = doc.name;
                    const fileExtension = fileName.split('.').pop().toLowerCase();
                    
                    // Tentukan ikon dan class warna berdasarkan ekstensi file
                    let fileIcon = 'ri-file-text-line';
                    let colorClass = 'doc-type-default';
                    
                    if (['pdf'].includes(fileExtension)) {
                        fileIcon = 'ri-file-pdf-line';
                        colorClass = 'doc-type-pdf';
                    } else if (['doc', 'docx'].includes(fileExtension)) {
                        fileIcon = 'ri-file-word-line';
                        colorClass = 'doc-type-word';
                    } else if (['xls', 'xlsx'].includes(fileExtension)) {
                        fileIcon = 'ri-file-excel-line';
                        colorClass = 'doc-type-excel';
                    } else if (['ppt', 'pptx'].includes(fileExtension)) {
                        fileIcon = 'ri-file-ppt-line';
                        colorClass = 'doc-type-ppt';
                    } else if (['zip', 'rar', '7z'].includes(fileExtension)) {
                        fileIcon = 'ri-file-zip-line';
                        colorClass = 'doc-type-zip';
                    }
                    
                    const row = `
                        <tr>
                            <td>${index + 1}</td>
                            <td>
                                <i class="${fileIcon} ${colorClass} me-2"></i>
                                ${fileName}
                            </td>
                            <td>${doc.type || 'N/A'}</td>
                            <td>
                                <a href="${doc.path}" class="btn btn-sm btn-primary" target="_blank">
                                    <i class="ri-eye-line me-1"></i>View
                                </a>
                                <a href="${doc.path}" class="btn btn-sm btn-info" download="${fileName}">
                                    <i class="ri-download-line me-1"></i>Download
                                </a>
                            </td>
                        </tr>
                    `;
                    
                    $('#docListTableBody').append(row);
                });
                
                // Tampilkan modal
                $('#documentListModal').modal('show');
            }

            // Event untuk toggle attachment
            $(document).off('click', '.attachment-toggle-btn').on('click', '.attachment-toggle-btn', function(e) {
                e.preventDefault();
                e.stopPropagation(); // Hentikan event propagation
                
                const taskId = $(this).data('task-id');
                const $btn = $(this);
                const $content = $btn.closest('.media-section').find('.attachment-content');
                const $icon = $btn.find('.attachment-toggle-icon');
                
                // Toggle expanded class pada button
                $btn.toggleClass('expanded');
                
                // Toggle icon rotation
                if ($btn.hasClass('expanded')) {
                    $icon.removeClass('ri-arrow-down-s-line').addClass('ri-arrow-up-s-line');
                    $content.slideDown(200);
                } else {
                    $icon.removeClass('ri-arrow-up-s-line').addClass('ri-arrow-down-s-line');
                    $content.slideUp(200);
                }
                
                // Prevent bubbling up the event
                return false;
            });

            // Event untuk tombol comment
            $(document).on('click', '.task-comment-btn', function() {
                const taskId = $(this).data('task-id');
                
                // Tampilkan loading state
                $('.comments-list').html('<div class="text-center p-3"><div class="spinner-border text-primary" role="status"></div><p class="mt-2">Loading comments...</p></div>');
                
                // Set task ID pada form
                $('#commentTaskId').val(taskId);
                
                // Load comments
                loadTaskComments(taskId);
                
                // Tampilkan modal
                $('#taskCommentsModal').modal('show');
            });
            
            // Function untuk load comments
            function loadTaskComments(taskId) {
                $.ajax({
                    url: `/maintenance/comments/${taskId}`,
                    method: 'GET',
                    success: function(response) {
                        if (response && response.length > 0) {
                            let commentsHTML = '';
                            const currentUserId = $('#current-user-id').val();
                            
                            console.log('Current user ID from hidden input:', currentUserId);
                            
                            response.forEach(comment => {
                                const commentDate = new Date(comment.created_at);
                                const formattedDate = commentDate.toLocaleString();
                                
                                // Process attachments if exists
                                let attachmentsHTML = '';
                                if (comment.attachments && comment.attachments.length > 0) {
                                    attachmentsHTML = '<div class="comment-attachments-gallery d-flex flex-wrap gap-2 mt-2">';
                                    
                                    comment.attachments.forEach(attachment => {
                                        const mediaPath = `/storage/app/public/${attachment.file_path}`;
                                        
                                        if (attachment.file_type.startsWith('image/')) {
                                            // Image
                                            attachmentsHTML += `
                                                <div class="comment-attachment-item-sm">
                                                    <img src="${mediaPath}" alt="${attachment.file_name}" class="img-thumbnail" 
                                                         style="width: 80px; height: 80px; object-fit: cover; cursor: pointer;"
                                                         onclick="openCommentMedia('${mediaPath}', '${attachment.file_type}')">
                                                </div>
                                            `;
                                        } else if (attachment.file_type.startsWith('video/')) {
                                            // Video
                                            attachmentsHTML += `
                                                <div class="comment-attachment-item-sm position-relative">
                                                    <div style="width: 80px; height: 80px;" class="img-thumbnail d-flex align-items-center justify-content-center bg-dark">
                                                        <i class="ri-play-circle-line text-white" style="font-size: 30px;"></i>
                                                        <div class="position-absolute w-100 h-100 top-0 start-0" 
                                                             style="cursor: pointer;"
                                                             onclick="openCommentMedia('${mediaPath}', '${attachment.file_type}')"></div>
                                                    </div>
                                                </div>
                                            `;
                                        } else {
                                            // File (document)
                                            let fileIcon = 'ri-file-text-line';
                                            let colorClass = 'text-secondary';
                                            
                                            if (attachment.file_type.includes('pdf')) {
                                                fileIcon = 'ri-file-pdf-line';
                                                colorClass = 'text-danger';
                                            } else if (attachment.file_type.includes('word')) {
                                                fileIcon = 'ri-file-word-line';
                                                colorClass = 'text-primary';
                                            } else if (attachment.file_type.includes('excel')) {
                                                fileIcon = 'ri-file-excel-line';
                                                colorClass = 'text-success';
                                            }
                                            
                                            attachmentsHTML += `
                                                <div class="comment-attachment-item-sm">
                                                    <div style="width: 80px; height: 80px;" 
                                                         class="img-thumbnail d-flex align-items-center justify-content-center">
                                                        <a href="${mediaPath}" target="_blank" download="${attachment.file_name}">
                                                            <i class="${fileIcon} ${colorClass}" style="font-size: 30px;"></i>
                                                        </a>
                                                    </div>
                                                </div>
                                            `;
                                        }
                                    });
                                    
                                    attachmentsHTML += '</div>';
                                }
                                
                                // Debug logs
                                console.log('Comment user ID:', comment.user_id);
                                console.log('Current user ID:', currentUserId);
                                console.log('Is same user?', String(comment.user_id) === String(currentUserId));
                                
                                // Tambahkan tombol delete jika komentar milik user yang sedang login
                                const deleteButton = String(comment.user_id) === String(currentUserId) ? 
                                    `<a href="javascript:void(0);" class="text-danger delete-comment-btn" data-comment-id="${comment.id}">
                                        <i class="ri-delete-bin-line"></i>
                                    </a>` : '';
                                
                                commentsHTML += `
                                    <div class="comment-item" id="comment-${comment.id}">
                                        <div class="comment-header d-flex justify-content-between">
                                            <div class="comment-author">
                                                <strong>${comment.user_name || 'User'}</strong>
                                            </div>
                                            <div class="d-flex align-items-center gap-3">
                                                <small class="text-muted">${formattedDate}</small>
                                                ${deleteButton}
                                            </div>
                                        </div>
                                        <div class="comment-body mt-1">
                                            ${comment.comment || ''}
                                            ${attachmentsHTML}
                                        </div>
                                        <hr>
                                    </div>
                                `;
                            });
                            
                            $('.comments-list').html(commentsHTML);
                            
                            // Scroll ke posisi paling atas untuk melihat komentar terbaru
                            setTimeout(function() {
                                $('.comments-list').scrollTop(0);
                            }, 100);
                        } else {
                            $('.comments-list').html('<div class="text-center p-3"><p class="text-muted">No comments yet. Be the first to comment!</p></div>');
                        }
                    },
                    error: function(xhr) {
                        console.error('Error loading comments:', xhr);
                        $('.comments-list').html('<div class="text-center p-3"><p class="text-danger">Failed to load comments. Please try again.</p></div>');
                    }
                });
            }

            // Function untuk membuka media di modal
            function openCommentMedia(mediaPath, mediaType) {
                if (mediaType.startsWith('image/')) {
                    // Tampilkan gambar di modal
                    Swal.fire({
                        imageUrl: mediaPath,
                        imageAlt: 'Media',
                        imageWidth: '100%',
                        imageHeight: 'auto',
                        width: 'auto',
                        showCloseButton: true,
                        showConfirmButton: false,
                        customClass: {
                            container: 'media-viewer-container'
                        }
                    });
                } else if (mediaType.startsWith('video/')) {
                    // Tampilkan video di modal
                    Swal.fire({
                        html: `<video src="${mediaPath}" controls style="max-width: 100%; max-height: 80vh;"></video>`,
                        width: 'auto',
                        showCloseButton: true,
                        showConfirmButton: false,
                        didOpen: () => {
                            const video = Swal.getHtmlContainer().querySelector('video');
                            video.play();
                        }
                    });
                }
            }

            // Handle submit comment form
            $('#commentForm').off('submit').on('submit', function(e) {
                e.preventDefault();
                
                // Cek apakah form sudah dalam proses submit
                if ($(this).data('submitting')) {
                    return false; // Jangan submit lagi jika sedang proses
                }
                
                const taskId = $('#commentTaskId').val();
                const commentText = $('#commentText').val().trim();
                
                if (!commentText && commentCapturedPhotos.length === 0 && 
                    commentCapturedVideos.length === 0 && commentUploadedFiles.length === 0) {
                    return; // Jangan submit jika kosong dan tidak ada media
                }
                
                // Set flag submitting
                $(this).data('submitting', true);
                
                // Buat FormData untuk mengirim file
                const formData = new FormData();
                formData.append('task_id', taskId);
                formData.append('comment', commentText);
                formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
                
                // Tambahkan captured photos
                commentCapturedPhotos.forEach(function(photo, index) {
                    if (photo.blob) {
                        formData.append(`comment_photos[${index}]`, photo.blob, `comment_photo_${index}.jpg`);
                    }
                });
                
                // Tambahkan captured videos
                commentCapturedVideos.forEach(function(video, index) {
                    if (video.blob) {
                        formData.append(`comment_videos[${index}]`, video.blob, `comment_video_${index}.webm`);
                    }
                });
                
                // Tambahkan uploaded files
                commentUploadedFiles.forEach(function(item, index) {
                    if (item.file) {
                        formData.append(`comment_files[${index}]`, item.file);
                    }
                });
                
                // Disable form selama proses submit
                $('#commentForm button[type="submit"]').prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Sending...');
                
                // Submit comment via AJAX
                $.ajax({
                    url: '/maintenance/comments',
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        // Reset form
                        $('#commentText').val('');
                        $('.comment-capture-preview').empty();
                        $('.comment-upload-preview').empty();
                        
                        // Reset arrays
                        commentCapturedPhotos = [];
                        commentCapturedVideos = [];
                        commentUploadedFiles = [];
                        
                        // Reload comments
                        loadTaskComments(taskId);
                        
                        // Update comment count pada card
                        const commentCount = parseInt($('.task-comment-btn[data-task-id="' + taskId + '"] .badge').text() || '0') + 1;
                        $('.task-comment-btn[data-task-id="' + taskId + '"]').html(`
                            <i class="ri-chat-1-line me-1"></i>
                            <span>Comments</span>
                            <span class="badge bg-primary ms-1">${commentCount}</span>
                        `);
                        
                        // Enable form kembali
                        $('#commentForm button[type="submit"]').prop('disabled', false).html('<i class="ri-send-plane-fill me-1"></i> Add Comment');
                        
                        // Reset flag submitting
                        $('#commentForm').data('submitting', false);
                        
                        // Tampilkan notifikasi
                        Swal.fire({
                            icon: 'success',
                            title: 'Comment Added',
                            text: 'Your comment has been successfully added',
                            timer: 2000,
                            showConfirmButton: false
                        });
                        
                        // Tambahkan notifikasi toast
                        showToast('Komentar baru telah ditambahkan', 'comment');
                    },
                    error: function(xhr) {
                        console.error('Error adding comment:', xhr);
                        
                        // Enable form kembali
                        $('#commentForm button[type="submit"]').prop('disabled', false).html('<i class="ri-send-plane-fill me-1"></i> Add Comment');
                        
                        // Reset flag submitting
                        $('#commentForm').data('submitting', false);
                        
                        // Tampilkan error
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to add comment. Please try again.'
                        });
                        
                        // Tampilkan toast error
                        showToast('Gagal menambahkan komentar: ' + xhr.responseJSON?.message || 'Unknown error', 'error');
                    }
                });
            });

            // Fungsi untuk mendapatkan inisial dari nama
            function getInitials(name) {
                if (!name) return '?';
                
                const nameParts = name.trim().split(' ');
                if (nameParts.length === 1) {
                    return nameParts[0].charAt(0).toUpperCase();
                }
                
                return (nameParts[0].charAt(0) + nameParts[nameParts.length - 1].charAt(0)).toUpperCase();
            }

            // Fungsi untuk mendapatkan warna background avatar berdasarkan ID
            function getAvatarColor(id) {
                // Array warna pastel yang menarik
                const colors = [
                    '#FF6B6B', '#4ECDC4', '#FFD166', '#86BBD8', '#9A8C98',
                    '#F4A261', '#E76F51', '#6D9886', '#6D597A', '#1A535C',
                    '#7DCFB6', '#FFC857', '#3C91E6', '#E49AB0', '#7886A4'
                ];
                
                // Gunakan ID untuk mendapatkan indeks warna
                const colorIndex = id % colors.length;
                return colors[colorIndex];
            }

            // Variabel untuk media komentar (terpisah dari variabel task)
            let commentCapturedPhotos = [];
            let commentCapturedVideos = []; 
            let commentUploadedFiles = [];
            let commentActiveStream = null;

            // Trigger file upload untuk komentar
            $('#commentUploadBtn').on('click', function() {
                $('#commentFileUpload').click();
            });

            // Handle file upload untuk komentar
            $('#commentFileUpload').on('change', function(e) {
                handleCommentFileUpload(e.target.files);
            });

            // Capture photo untuk komentar
            $('#commentCapturePhoto').on('click', function() {
                initializeCommentCamera(false);
            });

            // Capture video untuk komentar
            $('#commentCaptureVideo').on('click', function() {
                initializeCommentCamera(true);
            });

            // Fungsi untuk handle file upload komentar
            function handleCommentFileUpload(files) {
                for (let i = 0; i < files.length; i++) {
                    const file = files[i];
                    const url = URL.createObjectURL(file);
                    
                    if (file.type.startsWith('image/')) {
                        $('.comment-upload-preview').append(`
                            <div class="comment-media-wrapper">
                                <div class="comment-media-item">
                                    <img src="${url}" class="img-fluid">
                                </div>
                                <a href="javascript:void(0);" class="delete-comment-media">
                                    <i class="ri-delete-bin-line"></i>
                                </a>
                            </div>
                        `);
                    } else if (file.type.startsWith('video/')) {
                        $('.comment-upload-preview').append(`
                            <div class="comment-media-wrapper">
                                <div class="comment-media-item">
                                    <video src="${url}" class="img-fluid" preload="metadata"></video>
                                    <div class="comment-video-overlay">
                                        <a href="javascript:void(0);" class="btn btn-sm btn-light comment-play-btn">
                                            <i class="ri-play-fill"></i>
                                        </a>
                                    </div>
                                </div>
                                <a href="javascript:void(0);" class="delete-comment-media">
                                    <i class="ri-delete-bin-line"></i>
                                </a>
                            </div>
                        `);
                    } else if (file.type.includes('pdf') || file.type.includes('word') || file.type.includes('excel')) {
                        // File dokumen (tampilkan ikon)
                        let fileIcon = 'ri-file-text-line';
                        let colorClass = 'text-secondary';
                        
                        if (file.type.includes('pdf')) {
                            fileIcon = 'ri-file-pdf-line';
                            colorClass = 'text-danger';
                        } else if (file.type.includes('word')) {
                            fileIcon = 'ri-file-word-line';
                            colorClass = 'text-primary';
                        } else if (file.type.includes('excel')) {
                            fileIcon = 'ri-file-excel-line';
                            colorClass = 'text-success';
                        }
                        
                        $('.comment-upload-preview').append(`
                            <div class="comment-media-wrapper">
                                <div class="comment-media-item d-flex align-items-center justify-content-center bg-light">
                                    <i class="${fileIcon} ${colorClass}" style="font-size: 40px;"></i>
                                </div>
                                <a href="javascript:void(0);" class="delete-comment-media">
                                    <i class="ri-delete-bin-line"></i>
                                </a>
                            </div>
                        `);
                    }
                    
                    // Tambahkan file ke array
                    commentUploadedFiles.push({
                        file: file,
                        url: url
                    });
                }
            }

            // Fungsi inisialisasi kamera untuk comment
            function initializeCommentCamera(isVideo = false) {
                if (commentActiveStream) {
                    commentActiveStream.getTracks().forEach(track => track.stop());
                }

                const constraints = {
                    video: {
                        facingMode: 'environment',
                        width: { ideal: 1280 },
                        height: { ideal: 720 }
                    },
                    audio: isVideo // Aktifkan audio jika mode video
                };

                navigator.mediaDevices.getUserMedia(constraints)
                    .then(function(stream) {
                        commentActiveStream = stream;
                        
                        // Update modal title
                        $('#commentCameraModalTitle').text(isVideo ? 'Record Video' : 'Take Photo');
                        
                        // Buat modal content
                        const modalContent = `
                            <div class="comment-camera-container">
                                <video id="commentCameraPreview" autoplay playsinline style="width: 100%; max-height: 60vh; object-fit: cover;"></video>
                                <div class="camera-controls mt-3 d-flex justify-content-center gap-2">
                                    <button type="button" class="btn btn-primary" id="commentSwitchCamera">
                                        <i class="ri-camera-switch-line"></i>
                                    </button>
                                    <button type="button" class="btn btn-success" id="commentCaptureButton">
                                        <i class="ri-camera-line"></i> Capture
                                    </button>
                                    ${isVideo ? `
                                        <button type="button" class="btn btn-danger d-none" id="commentStopRecording">
                                            <i class="ri-stop-circle-line"></i> Stop
                                        </button>
                                    ` : ''}
                                </div>
                            </div>
                        `;
                        
                        $('#commentCameraModalBody').html(modalContent);
                        
                        // Set video source
                        document.getElementById('commentCameraPreview').srcObject = stream;
                        
                        // Show modal
                        $('#commentCameraModal').modal('show');
                        
                        // Event handlers for camera controls
                        
                        // Switch camera
                        $('#commentSwitchCamera').on('click', function() {
                            // Gunakan logika yang terpisah dari kode di modal create task
                            const currentFacingMode = constraints.video.facingMode;
                            constraints.video.facingMode = currentFacingMode === 'environment' ? 'user' : 'environment';
                            
                            // Re-initialize camera
                            $('#commentCameraModal').modal('hide');
                            setTimeout(() => {
                                initializeCommentCamera(isVideo);
                            }, 300);
                        });
                        
                        // Capture button for photo/video
                        $('#commentCaptureButton').on('click', function() {
                            if (isVideo) {
                                // Start recording video
                                startCommentVideoRecording(stream);
                            } else {
                                // Capture photo
                                captureCommentPhoto();
                            }
                        });
                        
                        // Stop recording button for video
                        $(document).on('click', '#commentStopRecording', function() {
                            stopCommentVideoRecording();
                        });
                    })
                    .catch(function(err) {
                        console.error('Error accessing camera:', err);
                        Swal.fire({
                            icon: 'error',
                            title: 'Camera Error',
                            text: 'Could not access the camera. Please check permissions.'
                        });
                    });
            }

            // Fungsi untuk capture photo di comment
            function captureCommentPhoto() {
                const video = document.getElementById('commentCameraPreview');
                const canvas = document.createElement('canvas');
                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;
                const ctx = canvas.getContext('2d');
                ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
                
                // Convert to blob
                canvas.toBlob(function(blob) {
                    const url = URL.createObjectURL(blob);
                    const index = commentCapturedPhotos.length;
                    
                    // Add to captured photos array
                    commentCapturedPhotos.push({
                        blob: blob,
                        url: url
                    });
                    
                    // Add to preview
                    $('.comment-capture-preview').append(`
                        <div class="comment-media-wrapper">
                            <div class="comment-media-item" data-index="${index}" data-type="photo">
                                <img src="${url}" class="img-fluid">
                            </div>
                            <a href="javascript:void(0);" class="delete-comment-media" data-type="photo">
                                <i class="ri-delete-bin-line"></i>
                            </a>
                        </div>
                    `);
                    
                    // Close modal
                    $('#commentCameraModal').modal('hide');
                }, 'image/jpeg');
            }

            // Variables untuk recording di comment
            let commentMediaRecorder = null;
            let commentRecordedChunks = [];

            // Fungsi untuk memulai recording video di comment
            function startCommentVideoRecording(stream) {
                commentRecordedChunks = [];
                commentMediaRecorder = new MediaRecorder(stream);
                
                commentMediaRecorder.ondataavailable = function(e) {
                    if (e.data.size > 0) {
                        commentRecordedChunks.push(e.data);
                    }
                };
                
                commentMediaRecorder.onstop = function() {
                    // Create blob from recorded chunks
                    const blob = new Blob(commentRecordedChunks, { type: 'video/webm' });
                    const url = URL.createObjectURL(blob);
                    const index = commentCapturedVideos.length;
                    
                    // Add to captured videos array
                    commentCapturedVideos.push({
                        blob: blob,
                        url: url
                    });
                    
                    // Add to preview
                    $('.comment-capture-preview').append(`
                        <div class="comment-media-wrapper">
                            <div class="comment-media-item" data-index="${index}" data-type="video">
                                <video src="${url}" class="img-fluid"></video>
                                <div class="comment-video-overlay">
                                    <a href="javascript:void(0);" class="btn btn-sm btn-light comment-play-btn">
                                        <i class="ri-play-fill"></i>
                                    </a>
                                </div>
                            </div>
                            <a href="javascript:void(0);" class="delete-comment-media" data-type="video">
                                <i class="ri-delete-bin-line"></i>
                            </a>
                        </div>
                    `);
                    
                    // Close modal
                    $('#commentCameraModal').modal('hide');
                };
                
                // Start recording
                commentMediaRecorder.start();
                
                // Update UI
                $('#commentCaptureButton').addClass('d-none');
                $('#commentStopRecording').removeClass('d-none');
            }

            // Fungsi untuk stop recording di comment
            function stopCommentVideoRecording() {
                if (commentMediaRecorder && commentMediaRecorder.state !== 'inactive') {
                    commentMediaRecorder.stop();
                    
                    // Update UI
                    $('#commentStopRecording').addClass('d-none');
                    $('#commentCaptureButton').removeClass('d-none');
                }
            }

            // Event delegation untuk play/pause video di comment
            $(document).on('click', '.comment-play-btn', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                const videoContainer = $(this).closest('.comment-media-item');
                const video = videoContainer.find('video')[0];
                const playIcon = $(this).find('i');
                
                // Pause semua video lainnya
                $('.comment-media-item video').each(function() {
                    if (this !== video && !this.paused) {
                        this.pause();
                        $(this).closest('.comment-media-item')
                            .find('.comment-play-btn i')
                            .removeClass('ri-pause-fill')
                            .addClass('ri-play-fill');
                    }
                });
                
                if (video.paused) {
                    video.play();
                    playIcon.removeClass('ri-play-fill').addClass('ri-pause-fill');
                } else {
                    video.pause();
                    playIcon.removeClass('ri-pause-fill').addClass('ri-play-fill');
                }
            });

            // Event untuk menghapus media di comment
            $(document).on('click', '.delete-comment-media', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                const mediaWrapper = $(this).closest('.comment-media-wrapper');
                const mediaItem = mediaWrapper.find('.comment-media-item');
                const index = mediaItem.data('index');
                const type = mediaItem.data('type');
                
                if (type === 'photo') {
                    URL.revokeObjectURL(commentCapturedPhotos[index].url);
                    commentCapturedPhotos.splice(index, 1);
                } else if (type === 'video') {
                    URL.revokeObjectURL(commentCapturedVideos[index].url);
                    commentCapturedVideos.splice(index, 1);
                } else {
                    // Uploaded file
                    URL.revokeObjectURL(commentUploadedFiles[index].url);
                    commentUploadedFiles.splice(index, 1);
                }
                
                mediaWrapper.remove();
            });

            // Tutup camera stream saat modal ditutup
            $('#commentCameraModal').on('hidden.bs.modal', function() {
                if (commentActiveStream) {
                    commentActiveStream.getTracks().forEach(track => track.stop());
                    commentActiveStream = null;
                }
            });

            // Reset media saat modal comment ditutup
            $('#taskCommentsModal').on('hidden.bs.modal', function() {
                // Reset form
                $('#commentForm')[0].reset();
                $('#commentForm').data('submitting', false);
                
                // Reset captured and uploaded media
                commentCapturedPhotos = [];
                commentCapturedVideos = [];
                commentUploadedFiles = [];
                
                // Clear preview
                $('.comment-capture-preview').empty();
                $('.comment-upload-preview').empty();
            });

            // Pastikan semua event handler hanya terpasang sekali
            setupCommentEvents();

            // Tambahkan event handler untuk edit task
            $(document).on('click', '.edit-task', function() {
                const taskId = $(this).data('task-id');
                
                // Periksa apakah user memiliki akses untuk mengedit task
                const userRole = "{{ Auth::user()->id_role }}";
                const userDivision = "{{ Auth::user()->division_id }}";
                
                // Validasi hak akses user
                if (userRole !== "5af56935b011a" && userDivision !== "20") {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Akses Dibatasi',
                        text: 'Anda tidak memiliki akses untuk mengedit task ini'
                    });
                    return;
                }
                
                // Set task ID pada form
                $('#editTaskId').val(taskId);
                
                // Reset form
                $('#editSelectedMembers').empty();
                $('#editMemberOptions').empty();
                
                // Tampilkan loading
                Swal.fire({
                    title: 'Memuat Data...',
                    text: 'Mohon tunggu sebentar',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                
                // Load task data untuk mendapatkan members yang sudah ada
                $.ajax({
                    url: `/maintenance/tasks/${taskId}`,
                    method: 'GET',
                    success: function(taskData) {
                        // Load members
                        $.ajax({
                            url: "{{ route('maintenance.getMembers') }}",
                            method: 'GET',
                            success: function(members) {
                                // Ambil data members yang sudah terpilih untuk task ini
                                $.ajax({
                                    url: `/maintenance/tasks/${taskId}/members`,
                                    method: 'GET',
                                    success: function(selectedMembers) {
                                        // Tampilkan semua members di member options
                                        const memberOptions = $('#editMemberOptions');
                                        
                                        members.forEach(member => {
                                            const isSelected = selectedMembers.some(m => m.id === member.id);
                                            const selectedClass = isSelected ? 'selected' : '';
                                            
                                            memberOptions.append(`
                                                <div class="member-item ${selectedClass}" 
                                                    data-id="${member.id}" 
                                                    data-name="${member.nama_lengkap}">
                                                    ${member.nama_lengkap}
                                                </div>
                                            `);
                                            
                                            // Jika sudah terpilih, tambahkan ke selected members
                                            if (isSelected) {
                                                addSelectedMember(member.id, member.nama_lengkap, 'edit');
                                            }
                                        });
                                        
                                        // Tutup loading dan tampilkan modal
                                        Swal.close();
                                        $('#editTaskModal').modal('show');
                                    },
                                    error: function(xhr) {
                                        console.error('Error loading task members:', xhr);
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Error',
                                            text: 'Gagal memuat data anggota task'
                                        });
                                    }
                                });
                            },
                            error: function(xhr) {
                                console.error('Error loading members:', xhr);
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: 'Gagal memuat data anggota'
                                });
                            }
                        });
                    },
                    error: function(xhr) {
                        console.error('Error loading task data:', xhr);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Gagal memuat data task'
                        });
                    }
                });
            });
            
            // Fungsi untuk menambahkan member yang dipilih
            function addSelectedMember(memberId, memberName, context = '') {
                // Tentukan container berdasarkan context
                const container = context === 'edit' ? '#editSelectedMembers' : '#selectedMembers';
                const prefix = context === 'edit' ? 'edit-selected-member-' : 'selected-member-';
                
                // Get initials
                const initials = memberName
                    .split(' ')
                    .map(word => word[0])
                    .join('')
                    .toUpperCase();
                    
                // Add selected member
                $(container).append(`
                    <div class="selected-member" 
                         id="${prefix}${memberId}"
                         data-name="${memberName}">
                        ${initials}
                        <div class="delete-member">
                            <i class="ri-close-line"></i>
                        </div>
                    </div>
                `);
            }
            
            // Handle member selection di modal edit
            $(document).on('click', '#editMemberOptions .member-item', function() {
                const memberId = $(this).data('id');
                const memberName = $(this).data('name');
                
                // Check if already selected
                if ($(`#edit-selected-member-${memberId}`).length > 0) {
                    return;
                }
                
                // Add selected member
                addSelectedMember(memberId, memberName, 'edit');
                
                // Add selected class to option
                $(this).addClass('selected');
            });
            
            // Handle member removal di modal edit
            $(document).on('click', '#editSelectedMembers .delete-member', function(e) {
                e.stopPropagation();
                const memberContainer = $(this).parent();
                const memberId = memberContainer.attr('id').replace('edit-selected-member-', '');
                
                // Remove selected class from option
                $(`#editMemberOptions .member-item[data-id="${memberId}"]`).removeClass('selected');
                
                // Remove tooltip if exists
                if (memberContainer.hasClass('tooltipped')) {
                    memberContainer.tooltip('dispose');
                }
                
                // Remove selected member
                memberContainer.remove();
            });
            
            // Form submit untuk edit task
            $('#editTaskForm').submit(function(e) {
                e.preventDefault();
                
                const taskId = $('#editTaskId').val();
                
                // Kumpulkan member IDs yang dipilih
                var selectedMembers = [];
                $('#editSelectedMembers .selected-member').each(function() {
                    var memberId = $(this).attr('id').replace('edit-selected-member-', '');
                    selectedMembers.push(memberId);
                });
                
                // Buat FormData
                var formData = new FormData();
                
                // Tambahkan task ID dan members
                formData.append('task_id', taskId);
                
                // Kirim sebagai string untuk memastikan data dikirim dengan benar
                if (selectedMembers.length > 0) {
                    formData.append('member_ids', selectedMembers.join(','));
                }
                
                // Tampilkan loading
                Swal.fire({
                    title: 'Menyimpan...',
                    text: 'Mohon tunggu',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    willOpen: () => {
                        Swal.showLoading();
                    }
                });
                
                // Kirim data ke server
                $.ajax({
                    url: `/maintenance/tasks/${taskId}/update-members`,
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: 'Anggota task berhasil diperbarui'
                        }).then(() => {
                            // Tutup modal
                            $('#editTaskModal').modal('hide');
                            
                            // Reload tasks untuk memperbarui tampilan
                            const outletId = $('#outletId').val();
                            const rukoId = $('#rukoId').val();
                            loadTasks(outletId, rukoId);
                        });
                    },
                    error: function(xhr) {
                        console.error('Error updating task members:', xhr);
                        
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: xhr.responseJSON?.message || 'Terjadi kesalahan saat memperbarui anggota task'
                        });
                    }
                });
            });
        });

        function setupCommentEvents() {
            // Hapus event binding yang mungkin sudah ada
            $('#commentCapturePhoto').off('click');
            $('#commentCaptureVideo').off('click');
            $('#commentUploadBtn').off('click');
            $('#commentFileUpload').off('change');
            $(document).off('click', '.delete-comment-media');
            $(document).off('click', '.comment-play-btn');
            
            // Tambahkan event handlers baru
            $('#commentCapturePhoto').on('click', function() {
                initializeCommentCamera(false);
            });
            
            $('#commentCaptureVideo').on('click', function() {
                initializeCommentCamera(true);
            });
            
            $('#commentUploadBtn').on('click', function() {
                $('#commentFileUpload').click();
            });
            
            $('#commentFileUpload').on('change', function(e) {
                handleCommentFileUpload(e.target.files);
            });
            
            // Lanjutkan dengan event handlers lainnya
        }

        // Alternatif: inisialisasi dengan event delegation
        $(document).on('mouseover', '[data-bs-toggle="tooltip"]', function() {
            var tooltip = new bootstrap.Tooltip(this);
        });

        // Tambahkan kode ini di bagian awal script.js atau setelah document ready
        // Pastikan function ini berada di scope global agar bisa diakses dari HTML
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl, {
                trigger: 'hover' // Pastikan tooltip hanya muncul saat hover
            });
        });

        // Untuk tooltip yang dibuat secara dinamis, gunakan ini
        function initTooltips() {
            // Hapus tooltips yang ada terlebih dahulu untuk mencegah duplikasi
            var oldTooltips = document.querySelectorAll('.tooltip');
            oldTooltips.forEach(function(tooltip) {
                tooltip.remove();
            });
            
            // Inisialisasi tooltip baru
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl, {
                    trigger: 'hover'
                });
            });
        }

        // Tambahkan event handler untuk view timeline
        $(document).on('click', '.view-timeline', function() {
                const taskId = $(this).data('task-id');
            loadTaskTimeline(taskId);
            });

        // Tambahkan event handler untuk edit task
        $(document).on('click', '.edit-task', function() {
                const taskId = $(this).data('task-id');
            // Implementasi edit task akan ditambahkan nanti
            console.log('Edit task:', taskId);
        });
        
        // Tambahkan event handler untuk delete task
        $(document).on('click', '.delete-task', function() {
            const taskId = $(this).data('task-id');
            // Tampilkan SweetAlert konfirmasi
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: 'Task dan semua data terkait akan dihapus permanen!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Tidak, batalkan!',
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
            }).then((result) => {
                if (result.isConfirmed) {
                    // Jika konfirmasi, panggil fungsi delete
                    deleteTask(taskId);
                }
            });
        });

        // Fungsi untuk menghapus task
        function deleteTask(taskId) {
            // Tampilkan loading
            Swal.fire({
                title: 'Menghapus Task...',
                text: 'Mohon tunggu sebentar',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            // Kirim request DELETE ke server
            $.ajax({
                url: `/maintenance/tasks/${taskId}`,
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    // Solusi paling sederhana: langsung reload halaman
                    // Ini akan memastikan semua data di-refresh dengan benar
                    
                    // Tampilkan notifikasi sukses dan kemudian reload
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: response.message || 'Task berhasil dihapus',
                        timer: 1500,
                        willClose: () => {
                            // Pada saat notifikasi ditutup, manual refresh halaman
                            // Ini opsi paling pasti untuk me-refresh tanpa masalah
                            location.reload();
                        }
                    });
                },
                error: function(xhr, status, error) {
                    console.error('Error deleting task:', error);
                    
                    // Tampilkan notifikasi error
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: xhr.responseJSON?.error || 'Terjadi kesalahan saat menghapus task. Silakan coba lagi nanti.'
                    });
                }
            });
        }

        // Fungsi untuk memuat timeline task
        function loadTaskTimeline(taskId) {
            // Tampilkan loading
            $('#timeline-due-date').html('');
            $('#task-timeline-content').html('<div class="text-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>');
            
            // Tampilkan modal
            $('#timelineModal').modal('show');
            
            // Ambil data task untuk mendapatkan due date
            $.ajax({
                url: `/maintenance/tasks/${taskId}`,
                method: 'GET',
                success: function(response) {
                    // Log respons mentah untuk debugging
                    console.log('Raw response:', response);
                    
                    // Cek format respons dan ekstrak data task
                    let taskData;
                    if (typeof response === 'object' && response !== null) {
                        // Jika respons adalah objek, periksa apakah ada properti task atau data
                        if (response.task) {
                            taskData = response.task;
                        } else if (response.data) {
                            taskData = response.data;
                        } else {
                            // Jika tidak ada properti task atau data, gunakan respons langsung
                            taskData = response;
                        }
                    } else {
                        // Jika respons bukan objek, gunakan sebagai fallback
                        taskData = { title: 'Error', task_number: 'Unknown', due_date: null };
                        console.error('Unexpected response format:', response);
                    }
                    
                    console.log('Processed task data:', taskData);
                    
                    // Format due date untuk ditampilkan
                    let dueDateHtml = '';
                    if (taskData.due_date) {
                        const dueDate = new Date(taskData.due_date);
                        const formattedDueDate = dueDate.toLocaleDateString('id-ID', {
                            day: '2-digit',
                            month: 'long',
                            year: 'numeric'
                        });
                        
                        // Hitung selisih hari dengan tanggal hari ini
                        const today = new Date();
                        today.setHours(0, 0, 0, 0); // Reset waktu ke 00:00:00
                        dueDate.setHours(0, 0, 0, 0); // Reset waktu ke 00:00:00
                        
                        const diffTime = dueDate - today;
                        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                        
                        // Set warna berdasarkan selisih hari
                        let dueDateColorClass = 'text-success';
                        let daysLeftText = '';
                        
                        if (diffDays < 0) {
                            dueDateColorClass = 'text-danger';
                            daysLeftText = `<div class="text-danger">Lewat ${Math.abs(diffDays)} hari</div>`;
                        } else if (diffDays === 0) {
                            dueDateColorClass = 'text-warning';
                            daysLeftText = `<div class="text-warning">Jatuh tempo hari ini</div>`;
                        } else if (diffDays === 1) {
                            dueDateColorClass = 'text-warning';
                            daysLeftText = `<div class="text-warning">Besok jatuh tempo</div>`;
                        } else if (diffDays <= 3) {
                            dueDateColorClass = 'text-warning';
                            daysLeftText = `<div class="text-warning">${diffDays} hari lagi</div>`;
                        } else {
                            daysLeftText = `<div class="text-success">${diffDays} hari lagi</div>`;
                        }
                        
                        dueDateHtml = `
                            <div class="card border border-light mb-0">
                                <div class="card-body p-3">
                                    <h5 class="card-title">${taskData.title || 'Untitled Task'}</h5>
                                    <div class="d-flex align-items-center flex-wrap mb-2">
                                        <div class="me-4">
                                            <span class="text-muted fs-13">Task Number:</span>
                                            <span class="fw-medium">${taskData.task_number || 'N/A'}</span>
                                        </div>
                                        <div>
                                            <span class="text-muted fs-13">Due Date:</span>
                                            <span class="fw-medium ${dueDateColorClass}">${formattedDueDate}</span>
                                            ${daysLeftText}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                    } else {
                        dueDateHtml = `
                            <div class="card border border-light mb-0">
                                <div class="card-body p-3">
                                    <h5 class="card-title">${taskData.title || 'Untitled Task'}</h5>
                                    <div class="d-flex align-items-center">
                                        <div class="me-4">
                                            <span class="text-muted fs-13">Task Number:</span>
                                            <span class="fw-medium">${taskData.task_number || 'N/A'}</span>
                                        </div>
                                        <div>
                                            <span class="text-muted fs-13">Due Date:</span>
                                            <span class="fw-medium text-muted">Tidak ada</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                    }
                    
                    // Update due date section
                    $('#timeline-due-date').html(dueDateHtml);
                    
                    // Ambil data timeline dari server
                    loadTaskTimelineData(taskId);
                },
                error: function(xhr, status, error) {
                    console.error('Error loading task data:', error);
                    console.log('Response:', xhr.responseText);
                    
                    try {
                        // Coba parse respons error (bisa berisi data task)
                        const errorResponse = JSON.parse(xhr.responseText);
                        console.log('Parsed error response:', errorResponse);
                        
                        // Jika respons error berisi data yang bisa digunakan, tampilkan
                        if (errorResponse && errorResponse.data) {
                            const taskData = errorResponse.data;
                            // Gunakan logika yang sama seperti di success handler
                            // ...
                        }
                    } catch(e) {
                        console.error('Error parsing error response:', e);
                    }
                    
                    $('#timeline-due-date').html(`
                        <div class="alert alert-danger">
                            Gagal memuat data task. Error: ${error}
                        </div>
                    `);
                    loadTaskTimelineData(taskId);
                }
            });
        }

        // Fungsi untuk memuat data timeline - buat ini menggantikan fungsi lama
        function loadTaskTimelineData(taskId) {
            $.ajax({
                url: `/maintenance/tasks/${taskId}/timeline`,
                method: 'GET',
                success: function(response) {
                    let timelineHtml = '';
                    
                    if (response.length === 0) {
                        timelineHtml = '<div class="text-center p-3">Tidak ada aktivitas yang tersedia</div>';
                    } else {
                        // Loop melalui aktivitas dan buat HTML timeline
                        response.forEach(function(activity, index) {
                            // Format tanggal
                            const activityDate = new Date(activity.created_at);
                            const formattedDate = activityDate.toLocaleDateString('id-ID', {
                                day: '2-digit',
                                month: 'short',
                                year: 'numeric'
                            }) + ' ' + activityDate.toLocaleTimeString('id-ID', {
                                hour: '2-digit',
                                minute: '2-digit'
                            });
                            
                            // Set icon berdasarkan tipe aktivitas
                            let iconClass = 'ri-information-line';
                            let iconType = 'icon-info';
                            
                            // Pilih icon dan tipe berdasarkan activity_type
                            switch(activity.activity_type) {
                                case 'CREATED':
                                    iconClass = 'ri-add-circle-line';
                                    iconType = 'icon-created';
                                    break;
                                case 'STATUS_CHANGED':
                                    iconClass = 'ri-exchange-line';
                                    iconType = 'icon-status';
                                    break;
                                case 'PRIORITY_CHANGED':
                                    iconClass = 'ri-flag-line';
                                    iconType = 'icon-priority';
                                    break;
                                case 'MEMBER_ADDED':
                                    iconClass = 'ri-user-add-line';
                                    iconType = 'icon-member-add';
                                    break;
                                case 'MEMBER_REMOVED':
                                    iconClass = 'ri-user-unfollow-line';
                                    iconType = 'icon-member-remove';
                                    break;
                                case 'COMMENT_ADDED':
                                case 'COMMENT':
                                    iconClass = 'ri-chat-1-line';
                                    iconType = 'icon-comment';
                                    break;
                                case 'COMMENT_DELETED':
                                    iconClass = 'ri-chat-delete-line';
                                    iconType = 'icon-comment-delete';
                                    break;
                                case 'DOCUMENT_UPLOADED':
                                    iconClass = 'ri-file-upload-line';
                                    iconType = 'icon-document';
                                    break;
                                case 'MEDIA_UPLOADED':
                                    iconClass = 'ri-image-add-line';
                                    iconType = 'icon-media';
                                    break;
                                case 'DUE_DATE_CHANGED':
                                    iconClass = 'ri-calendar-line';
                                    iconType = 'icon-date';
                                    break;
                                case 'COMPLETED':
                                    iconClass = 'ri-check-double-line';
                                    iconType = 'icon-complete';
                                    break;
                                case 'MEMBERS_UPDATED':
                                    iconClass = 'ri-team-line';
                                    iconType = 'icon-team';
                                    break;
                                default:
                                    iconClass = 'ri-information-line';
                                    iconType = 'icon-info';
                            }
                            
                            // Tambahkan kelas left/right berdasarkan indeks ganjil/genap
                            const position = index % 2 === 0 ? 'left' : 'right';
                            
                            // Buat item timeline (format kanan-kiri dengan icon tanpa background)
                            timelineHtml += `
                                <div class="timeline-item ${position}" id="timeline-item-${index}">
                                    <div class="icon ${iconType}">
                                        <i class="${iconClass}"></i>
                                    </div>
                                    <div class="timeline-content">
                                        <h6 class="mb-1">${activity.description}</h6>
                                        <p class="mb-1">oleh ${activity.user ? activity.user.nama_lengkap : 'System'}</p>
                                        <div class="date">${formattedDate}</div>
                                    </div>
                                </div>
                            `;
                        });
                    }
                    
                    // Update konten modal
                    $('#task-timeline-content').html(timelineHtml);
                    
                    // Auto scroll ke item timeline terbaru (item pertama)
                    setTimeout(function() {
                        const timelineContainer = document.getElementById('task-timeline-content');
                        timelineContainer.scrollTop = 0;
                    }, 100);
                },
                error: function(xhr, status, error) {
                    console.error('Error loading timeline:', error);
                    $('#task-timeline-content').html('<div class="alert alert-danger">Gagal memuat data timeline. Silakan coba lagi nanti.</div>');
                }
            });
        }

        // Tempatkan kode ini di dalam <script> setelah document ready
        // Fungsi untuk menampilkan toast notification
        function showToast(message, title = 'Notifikasi', type = 'info', duration = 5000) {
            // Generate ID unik untuk toast
            const toastId = 'toast-' + Date.now();
            
            // Tentukan class berdasarkan tipe
            let bgClass = 'bg-info';
            let iconClass = 'ti ti-info-circle';
            
            switch(type) {
                case 'success':
                    bgClass = 'bg-success';
                    iconClass = 'ti ti-check';
                    break;
                case 'error':
                case 'danger':
                    bgClass = 'bg-danger';
                    iconClass = 'ti ti-alert-circle';
                    break;
                case 'warning':
                    bgClass = 'bg-warning';
                    iconClass = 'ti ti-alert-triangle';
                    break;
                case 'notification':
                    bgClass = 'bg-primary';
                    iconClass = 'ti ti-bell';
                    break;
            }
            
            // Buat HTML toast
            const toastHtml = `
                <div id="${toastId}" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
                    <div class="toast-header ${bgClass} text-white">
                        <i class="${iconClass} me-2"></i>
                        <strong class="me-auto">${title}</strong>
                        <small>${new Date().toLocaleTimeString()}</small>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
                    </div>
                    <div class="toast-body">
                        ${message}
                    </div>
                </div>
            `;
            
            // Tambahkan toast ke container
            $('#toastContainer').append(toastHtml);
            
            // Inisialisasi toast
            const toastElement = document.getElementById(toastId);
            const toast = new bootstrap.Toast(toastElement, {
                delay: duration
            });
            
            // Tampilkan toast
            toast.show();
            
            // Hapus toast dari DOM setelah hilang
            toastElement.addEventListener('hidden.bs.toast', function() {
                $(this).remove();
            });
            
            // Tambahkan suara notifikasi (opsional)
            playNotificationSound();
        }

        // Fungsi untuk memainkan suara notifikasi (opsional)
        function playNotificationSound() {
            const audio = new Audio('/build/sounds/notification.mp3');
            audio.volume = 0.5;
            audio.play().catch(error => {
                // Browser mungkin memblokir autoplay
                console.log('Tidak dapat memainkan suara notifikasi:', error);
            });
        }

        // Panggil fungsi ketika halaman sudah tidak aktif/di background
        let isPageVisible = true;

        // Periksa apakah halaman visible atau di background
        document.addEventListener('visibilitychange', function() {
            isPageVisible = !document.hidden;
        });

        // Contoh penggunaan untuk notifikasi komentar baru
        function notifyNewComment(user, taskTitle) {
            // Tampilkan toast
            showToast(`${user} menambahkan komentar baru pada task "${taskTitle}"`, 'comment');
            
            // Jika halaman di background, tampilkan notifikasi desktop
            if (!isPageVisible) {
                showDesktopNotification('Komentar Baru', `${user} menambahkan komentar baru pada task "${taskTitle}"`);
            }
        }

        // Variabel untuk menyimpan ID notifikasi terakhir
        let lastNotificationId = 0;

        // Fungsi untuk memeriksa notifikasi baru
        function checkNewNotifications() {
            $.ajax({
                url: '/notifications/check-new',
                type: 'GET',
                data: { last_id: lastNotificationId },
                success: function(response) {
                    if (response.success && response.new_notifications && response.new_notifications.length > 0) {
                        // Update jumlah notifikasi
                        updateNotificationBadge(response.unread_count);
                        
                        // Tampilkan toast untuk setiap notifikasi baru
                        response.new_notifications.forEach(notification => {
                            showToast(
                                notification.message,
                                'Notifikasi Baru',
                                'notification',
                                8000
                            );
                            
                            // Update ID notifikasi terakhir
                            if (notification.id > lastNotificationId) {
                                lastNotificationId = notification.id;
                            }
                        });
                        
                        // Jika dropdown notifikasi sedang terbuka, refresh daftar
                        if ($('#notificationDropdown').hasClass('show')) {
                            loadNotifications();
                        }
                        
                        // Animasi badge
                        animateNotificationBadge();
                    }
                },
                error: function(xhr) {
                    console.error('Error checking for new notifications:', xhr);
                }
            });
        }

        // Fungsi untuk animasi badge notifikasi
        function animateNotificationBadge() {
            $('.notification-badge')
                .addClass('notification-pulse')
                .on('animationend', function() {
                    $(this).removeClass('notification-pulse');
                });
        }

        // Inisialisasi polling pada document ready
        $(document).ready(function() {
            // Dapatkan ID notifikasi terakhir saat pertama kali load
            $.ajax({
                url: '/notifications/last-id',
                type: 'GET',
                success: function(response) {
                    if (response.success && response.last_id) {
                        lastNotificationId = response.last_id;
                    }
                    
                    // Mulai polling setiap 30 detik
                    setInterval(checkNewNotifications, 30000);
                },
                error: function(xhr) {
                    console.error('Error getting last notification ID:', xhr);
                    // Mulai polling meskipun terjadi error
                    setInterval(checkNewNotifications, 30000);
                }
            });
        });

        // Fungsi untuk memuat statistik PR
        function loadPrStats(taskId) {
            // Tampilkan loading spinner
            $(`#prStatsLoader-${taskId}`).removeClass('d-none');
            
            $.ajax({
                url: `/maintenance/task/${taskId}/pr/stats`,
                method: 'GET',
                success: function(response) {
                    if (response.success) {
                        const stats = response.stats;
                        
                        // Update nilai statistik
                        $(`#prTotal-${taskId}`).text(stats.total);
                        $(`#prApproved-${taskId}`).text(stats.approved);
                        $(`#prRejected-${taskId}`).text(stats.rejected);
                        $(`#prDraft-${taskId}`).text(stats.draft);
                        
                        // Tampilkan atau sembunyikan section PR stats berdasarkan apakah ada PR atau tidak
                        if (stats.total > 0) {
                            $(`#task-${taskId} .pr-stats-section`).removeClass('d-none');
                        } else {
                            // Jika tidak ada PR dan status task bukan PR, sembunyikan stats
                            if (!$(`[data-task-id="${taskId}"]`).hasClass('status-PR')) {
                                $(`[data-task-id="${taskId}"] .pr-stats-section`).addClass('d-none');
                            }
                        }
                    }
                },
                error: function(xhr) {
                    console.error('Error loading PR stats:', xhr);
                },
                complete: function() {
                    // Sembunyikan loading spinner
                    $(`#prStatsLoader-${taskId}`).addClass('d-none');
                }
            });
        }
        
        // Fungsi untuk memuat statistik PO
        function loadPoStats(taskId) {
            // Tampilkan loading spinner
            $(`#poStatsLoader-${taskId}`).removeClass('d-none');
            
            $.ajax({
                url: `/maintenance/task/${taskId}/po/stats`,
                method: 'GET',
                success: function(response) {
                    // Update nilai statistik
                    $(`#poTotal-${taskId}`).text(response.total || 0);
                    $(`#poApproved-${taskId}`).text(response.approved || 0);
                    $(`#poRejected-${taskId}`).text(response.rejected || 0);
                    $(`#poReceived-${taskId}`).text(response.received || 0);
                    $(`#poPayment-${taskId}`).text(response.payment || 0);
                    $(`#poDraft-${taskId}`).text(response.draft || 0);
                    
                    // Tampilkan atau sembunyikan section PO stats berdasarkan apakah ada PO atau tidak
                    if (response.total > 0) {
                        $(`#task-${taskId} .po-stats-section`).removeClass('d-none');
                    } else {
                        // Jika tidak ada PO dan status task bukan PO, In Progress, In Review, atau Done, sembunyikan stats
                        if (!['PO', 'IN_PROGRESS', 'IN_REVIEW', 'DONE'].includes($(`[data-task-id="${taskId}"]`).attr('data-status'))) {
                            $(`[data-task-id="${taskId}"] .po-stats-section`).addClass('d-none');
                        }
                    }
                },
                error: function(xhr) {
                    console.error('Error loading PO stats:', xhr);
                },
                complete: function() {
                    // Sembunyikan loading spinner
                    $(`#poStatsLoader-${taskId}`).addClass('d-none');
                }
            });
        }

        // Memisahkan fungsi updateTaskStatus agar dapat dipanggil setelah validasi
        function updateTaskStatus(taskId, newStatus) {
            // Tampilkan loading state
            Swal.fire({
                title: 'Memperbarui...',
                text: 'Memperbarui status task',
                allowOutsideClick: false,
                showConfirmButton: false,
                willOpen: () => {
                    Swal.showLoading();
                }
            });
            
            // Kirim update ke server
            $.ajax({
                url: '/maintenance/kanban/updateStatus',
                method: 'POST',
                data: {
                    taskId: taskId,
                    status: newStatus
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    // Update berhasil
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Status task berhasil diperbarui',
                        timer: 1500,
                        showConfirmButton: false
                    });
                    
                    // Update PR Stats visibility berdasarkan status baru
                    const taskCard = $(`[data-task-id="${taskId}"]`);
                    const prStatsSection = taskCard.find('.pr-stats-section');
                    
                    if (newStatus === 'PR') {
                        // Jika dipindah ke PR board, tampilkan PR Stats
                        prStatsSection.removeClass('d-none');
                        // Load PR stats
                        loadPrStats(taskId);
                    } else {
                        // Jika dipindah ke board lain, sembunyikan PR Stats
                        prStatsSection.addClass('d-none');
                    }
                    
                    // Update PO Stats visibility berdasarkan status baru
                    const poStatsSection = taskCard.find('.po-stats-section');
                    
                    if (['PO', 'IN_PROGRESS', 'IN_REVIEW', 'DONE'].includes(newStatus)) {
                        // Jika dipindah ke board yang relevan, tampilkan PO Stats
                        poStatsSection.removeClass('d-none');
                        // Load PO stats
                        loadPoStats(taskId);
                    } else {
                        // Jika dipindah ke board lain, sembunyikan PO Stats
                        poStatsSection.addClass('d-none');
                    }
                    
                    // Update counter di UI
                    updateTaskCounters();
                    
                    // Jika pindah ke DONE, perbarui tampilan dengan tanggal selesai
                    if (newStatus === 'DONE') {
                        const taskCard = $(`[data-task-id="${taskId}"]`);
                        const dueDate = taskCard.find('.due-date');
                        const completedDate = new Date();
                        const formattedCompletedDate = completedDate.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
                        
                        // Perbarui warna due date menjadi hitam
                        dueDate.find('span').removeClass('text-danger text-muted').addClass('text-dark');
                        
                        // Tambahkan tanggal selesai di bawah due date
                        if (dueDate.find('.completed-date').length === 0) {
                            dueDate.append(`
                                <div class="completed-date mt-1">
                                    <span class="text-success font-size-12">
                                        <i class="ri-check-double-line align-middle"></i> Selesai: ${formattedCompletedDate}
                                    </span>
                                </div>
                            `);
                        }
                        
                        // Set timeout untuk menghilangkan card setelah 3 hari
                        // Ini hanya berlaku jika task dipindahkan ke DONE selama sesi browser aktif
                        setTimeout(() => {
                            taskCard.fadeOut('slow', function() {
                                $(this).remove();
                                updateTaskCounters();
                            });
                        }, 3 * 24 * 60 * 60 * 1000); // 3 hari dalam milidetik
                    }
                },
                error: function(xhr) {
                    // Jika error, kembalikan card ke posisi awal
                    console.error('Error updating task status:', xhr);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Gagal memperbarui status task. Silahkan coba lagi.'
                    });
                    
                    // Reload halaman untuk mengembalikan state asli
                    setTimeout(() => {
                        window.location.reload();
                    }, 2000);
                }
            });
        }

        // Fungsi untuk memperbarui status task di UI
        function updateTaskStatusUI(taskId, newStatus) {
            // Perbarui status di atribut data
            $(`[data-task-id="${taskId}"]`).attr('data-current-status', newStatus);
            
            // Perbarui warna dan ikon status jika perlu
            // ...
        }

        // Tambahkan fungsi ini di bagian atas JavaScript
        function isTaskInPoBoard(task) {
            return task.status === 'PO'; // Sesuaikan dengan status yang menandakan board PO
        }

        // Tambahkan event handler ini
        $(document).on('click', '.create-po-btn', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const taskId = $(this).data('task-id');
            
            // Redirect ke halaman create PO dengan parameter task_id
            window.location.href = `/purchasing/purchase-orders/create?task_id=${taskId}`;
        });

        // Tambahkan event handler untuk tombol reload
        $('#reloadTasks').on('click', function() {
            // Ambil nilai outlet dan ruko yang terpilih
            const outletId = $('#outletId').val();
            const rukoId = $('#rukoId').val();

            // Validasi outlet harus dipilih
            if (!outletId) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Silahkan pilih outlet terlebih dahulu'
                });
                return;
            }

            // Tampilkan loading state
            Swal.fire({
                title: 'Loading...',
                text: 'Memuat task',
                allowOutsideClick: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Trigger change event pada outlet untuk memuat ulang task
            $('#outletId').trigger('change');

            // Tutup loading setelah 1 detik
            setTimeout(() => {
                Swal.close();
            }, 1000);
        });

        // Pastikan fungsi loadTasks menampilkan loading state
        function loadTasks(outletId, rukoId) {
            console.log('Loading tasks for outlet:', outletId, 'ruko:', rukoId);
            
            // Tampilkan loading state
            Swal.fire({
                title: 'Loading...',
                text: 'Memuat task',
                allowOutsideClick: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            $.ajax({
                url: '/maintenance/kanban/getTasks',
                method: 'GET',
                data: {
                    outlet_id: outletId,
                    ruko_id: rukoId
                },
                success: function(response) {
                    console.log('Tasks loaded:', response);
                    
                    // Reset semua container task
                    $('#task-list').empty();
                    $('#pr-list').empty();
                    $('#po-list').empty();
                    $('#inprogress-list').empty();
                    $('#review-list').empty();
                    $('#done-list').empty();
                    
                    // Kelompokkan task berdasarkan status
                    response.forEach(function(task) {
                        // Jika task dalam status DONE, periksa waktunya
                        if (task.status === 'DONE' && task.completed_at) {
                            const completedDate = new Date(task.completed_at);
                            const currentDate = new Date();
                            
                            // Reset jam ke 00:00:00 untuk perbandingan tanggal saja
                            completedDate.setHours(0, 0, 0, 0);
                            currentDate.setHours(0, 0, 0, 0);
                            
                            // Hitung selisih hari
                            const diffTime = Math.abs(currentDate - completedDate);
                            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                            
                            // Jika lebih dari 3 hari, lewati task ini (tidak ditampilkan)
                            if (diffDays > 3) {
                                console.log(`Task ${task.id} selesai pada ${task.completed_at}, sudah ${diffDays} hari, tidak ditampilkan`);
                                return;
                            }
                        }
                        
                        let taskHtml = createTaskCard(task);
                        
                        // Masukkan task ke container yang sesuai
                        switch(task.status) {
                            case 'TASK':
                                $('#task-list').append(taskHtml);
                                break;
                            case 'PR':
                                $('#pr-list').append(taskHtml);
                                break;
                            case 'PO':
                                $('#po-list').append(taskHtml);
                                break;
                            case 'IN_PROGRESS':
                                $('#inprogress-list').append(taskHtml);
                                break;
                            case 'IN_REVIEW':
                                $('#review-list').append(taskHtml);
                                break;
                            case 'DONE':
                                $('#done-list').append(taskHtml);
                                break;
                        }
                    });
                    
                    // Update counter di setiap board
                    updateTaskCounters();
                    
                    // Inisialisasi tooltips untuk avatar members
                    initTooltips();

                    // Tutup loading state
                    Swal.close();
                },
                error: function(xhr) {
                    console.error('Error loading tasks:', xhr);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Gagal memuat task. Silahkan coba lagi.'
                    });
                },
                complete: function() {
                    // Pastikan loading state ditutup
                    if (Swal.isLoading()) {
                        Swal.close();
                    }
                }
            });
        }

        // ... existing code ...
        // Event handler untuk capture evidence
        $(document).on('click', '.capture-evidence', function() {
            const taskId = $(this).data('task-id');
            $('#captureEvidenceModal').data('task-id', taskId).modal('show');
        });

        // Event handler untuk tombol capture foto
        $('#capturePhotoBtn').on('click', function() {
            $('#cameraModal').modal('show');
            // Gunakan fungsi dari evidence.js
            if (typeof window.initializeCamera === 'function') {
                window.initializeCamera(false);
            }
        });

        // Event handler untuk tombol capture video
        $('#captureVideoBtn').on('click', function() {
            $('#cameraModal').modal('show');
            // Gunakan fungsi dari evidence.js
            if (typeof window.initializeCamera === 'function') {
                window.initializeCamera(true);
            }
        });

        // Event handler untuk tombol capture di modal kamera
        $('#captureBtn').on('click', function() {
            // Gunakan fungsi dari evidence.js
            if (typeof window.capturePhoto === 'function') {
                window.capturePhoto();
            }
        });

        // Event handler untuk tombol stop video
        $('#stopVideoBtn').on('click', function() {
            // Gunakan fungsi dari evidence.js
            if (typeof window.stopVideoRecording === 'function') {
                window.stopVideoRecording();
            }
        });

        // ... existing code ...

        // ... existing code ...
        // Event handler untuk tombol capture evidence di footer task card
        $(document).on('click', '.capture-evidence-btn', function() {
            const taskId = $(this).data('task-id');
            $('#captureEvidenceModal').data('task-id', taskId).modal('show');
        });
        // ... existing code ...

        // Fungsi untuk mengambil foto dengan pendekatan yang lebih sederhana dan mirip dengan video
        function captureEvidencePhoto() {
            console.log('Attempting to capture photo with simplified approach');
            
            if (!evidenceStream) {
                console.error('No media stream available');
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Tidak ada akses kamera. Pastikan kamera sudah aktif.'
                });
                return;
            }
            
            // Pemeriksaan dasar sederhana
            const videoElement = document.getElementById('cameraView');
            if (!videoElement) {
                console.error('Video element not found');
                return;
            }
            
            try {
                // Buat canvas dengan ukuran tetap
                const canvas = document.createElement('canvas');
                canvas.width = 640;
                canvas.height = 480;
                
                const context = canvas.getContext('2d');
                if (!context) {
                    console.error('Failed to get canvas context');
                    return;
                }
                
                // Gambar video ke canvas dengan ukuran tetap
                context.drawImage(videoElement, 0, 0, canvas.width, canvas.height);
                
                // Konversi ke blob langsung
                canvas.toBlob(function(blob) {
                    if (!blob) {
                        console.error('Failed to create blob from canvas');
                        return;
                    }
                    
                    console.log('Photo captured successfully, size:', blob.size);
                    
                    // Cara yang sama seperti video
                    const objectUrl = URL.createObjectURL(blob);
                    
                    // Buat photo object dengan format yang sama dengan video
                    const photo = {
                        id: Date.now(),
                        blob: blob,
                        url: objectUrl
                    };
                    
                    // Tambahkan ke array
                    evidencePhotos.push(photo);
                    
                    // Update preview
                    updateEvidencePreview();
                    
                    // Notification
                    Swal.fire({
                        icon: 'success',
                        title: 'Foto Diambil',
                        text: 'Foto berhasil ditangkap',
                        timer: 1500,
                        showConfirmButton: false
                    });
                }, 'image/jpeg', 0.85);
                
            } catch (error) {
                console.error('Error capturing photo:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Gagal mengambil foto: ' + error.message
                });
            }
        }
        
        // Menggunakan function yang sama untuk preview foto dan video
        function updateEvidencePreview() {
            console.log('Updating preview with photos:', evidencePhotos.length, 'and videos:', evidenceVideos.length);
            
            const container = document.getElementById('evidencePreviewContainer');
            container.innerHTML = '';
            
            // Generate HTML untuk foto
            evidencePhotos.forEach(function(photo, index) {
                const photoHtml = `
                    <div class="position-relative" style="width: 120px; height: 120px; margin-right: 8px; margin-bottom: 8px;">
                        <img src="${photo.url || photo.dataUrl}" class="img-thumbnail" style="width: 100%; height: 100%; object-fit: cover;">
                        <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 delete-media-btn" 
                                data-type="photo" data-index="${index}">
                            <i class="ri-delete-bin-line"></i>
                        </button>
                    </div>
                `;
                
                container.innerHTML += photoHtml;
            });
            
            // Generate HTML untuk video
            evidenceVideos.forEach(function(video, index) {
                if (!video.url) {
                    console.error('Video URL not available for index', index);
                    return;
                }
                
                const videoHtml = `
                    <div class="position-relative border" style="width: 150px; margin-right: 10px; margin-bottom: 10px; border-radius: 5px; overflow: hidden;">
                        <div style="height: 100px; background: #000; display: flex; align-items: center; justify-content: center; overflow: hidden;">
                            <i class="ri-video-line text-white" style="font-size: 40px;"></i>
                        </div>
                        <div class="p-2 d-flex justify-content-between" style="background-color: #f8f9fa;">
                            <button class="btn btn-sm btn-primary me-2" style="min-width: 40px;" onclick="bukaPutarVideo(${index})">
                                <i class="ri-play-fill"></i> Play
                            </button>
                            <button type="button" class="btn btn-sm btn-danger delete-media-btn" 
                                    data-type="video" data-index="${index}">
                                <i class="ri-delete-bin-line"></i>
                            </button>
                        </div>
                    </div>
                `;
                
                container.innerHTML += videoHtml;
            });
            
            // Tampilkan pesan jika tidak ada media
            if (evidencePhotos.length === 0 && evidenceVideos.length === 0) {
                container.innerHTML = '<div class="text-muted" id="evidenceNoMediaMessage">Belum ada media yang ditangkap</div>';
            }
        }

        // Event delegasi untuk tombol hapus media
        document.getElementById('evidencePreviewContainer').addEventListener('click', function(e) {
            const deleteBtn = e.target.closest('.delete-media-btn');
            if (deleteBtn) {
                const mediaType = deleteBtn.getAttribute('data-type');
                const mediaIndex = parseInt(deleteBtn.getAttribute('data-index'));
                
                console.log('Delete media clicked:', mediaType, mediaIndex);
                
                if (mediaType === 'photo') {
                    // Revoke URL objek untuk menghindari memory leak
                    if (evidencePhotos[mediaIndex] && evidencePhotos[mediaIndex].url) {
                        URL.revokeObjectURL(evidencePhotos[mediaIndex].url);
                    }
                    // Hapus dari array
                    evidencePhotos.splice(mediaIndex, 1);
                } else if (mediaType === 'video') {
                    // Revoke URL objek untuk menghindari memory leak
                    if (evidenceVideos[mediaIndex] && evidenceVideos[mediaIndex].url) {
                        URL.revokeObjectURL(evidenceVideos[mediaIndex].url);
                    }
                    // Hapus dari array
                    evidenceVideos.splice(mediaIndex, 1);
                }
                
                // Rebuild semua preview untuk memastikan index tetap benar
                rebuildEvidencePreview();
            }
        });
        
        // Fungsi untuk membangun ulang semua preview setelah penghapusan
        function rebuildEvidencePreview() {
            console.log('Rebuilding all previews with photos:', evidencePhotos.length, 'and videos:', evidenceVideos.length);
            
            const container = document.getElementById('evidencePreviewContainer');
            if (!container) {
                console.error('Preview container not found');
                return;
            }
            
            container.innerHTML = '';
            
            // Generate HTML untuk foto
            evidencePhotos.forEach(function(photo, index) {
                const photoHtml = `
                    <div class="position-relative" style="width: 120px; height: 120px; margin-right: 8px; margin-bottom: 8px;">
                        <img src="${photo.url || photo.dataUrl}" class="img-thumbnail" style="width: 100%; height: 100%; object-fit: cover;">
                        <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 delete-media-btn" 
                                data-type="photo" data-index="${index}">
                            <i class="ri-delete-bin-line"></i>
                        </button>
                    </div>
                `;
                
                container.innerHTML += photoHtml;
            });
            
            // Generate HTML untuk video
            evidenceVideos.forEach(function(video, index) {
                if (!video.url) {
                    console.error('Video URL not available for index', index);
                    return;
                }
                
                const videoHtml = `
                    <div class="position-relative border" style="width: 150px; margin-right: 10px; margin-bottom: 10px; border-radius: 5px; overflow: hidden;">
                        <div style="height: 100px; background: #000; display: flex; align-items: center; justify-content: center; overflow: hidden;">
                            <i class="ri-video-line text-white" style="font-size: 40px;"></i>
                        </div>
                        <div class="p-2 d-flex justify-content-between" style="background-color: #f8f9fa;">
                            <button class="btn btn-sm btn-primary me-2" style="min-width: 40px;" onclick="bukaPutarVideo(${index})">
                                <i class="ri-play-fill"></i> Play
                            </button>
                            <button type="button" class="btn btn-sm btn-danger delete-media-btn" 
                                    data-type="video" data-index="${index}">
                                <i class="ri-delete-bin-line"></i>
                            </button>
                        </div>
                    </div>
                `;
                
                container.innerHTML += videoHtml;
            });
            
            // Tampilkan pesan jika tidak ada media
            if (evidencePhotos.length === 0 && evidenceVideos.length === 0) {
                container.innerHTML = '<div class="text-muted" id="evidenceNoMediaMessage">Belum ada media yang ditangkap</div>';
            }
        }
        
        // Setup event handler untuk preview video
        function setupVideoPreviewHandlers() {
            // Ambil semua tombol play video
            const playButtons = document.querySelectorAll('.play-video-btn');
            
            playButtons.forEach(function(btn) {
                btn.addEventListener('click', function() {
                    const videoIndex = parseInt(this.getAttribute('data-index'));
                    showVideoPlayer(videoIndex);
                });
            });
            
            // Tambahkan event untuk thumbnail video
            const videoThumbnails = document.querySelectorAll('.video-preview');
            
            videoThumbnails.forEach(function(thumbnail) {
                thumbnail.addEventListener('click', function() {
                    const videoIndex = parseInt(this.getAttribute('data-index'));
                    showVideoPlayer(videoIndex);
                });
            });
        }
        
        // Fungsi untuk menampilkan video player modal
        function showVideoPlayer(videoIndex) {
            if (!evidenceVideos[videoIndex] || !evidenceVideos[videoIndex].url) {
                console.error('Video tidak tersedia');
                return;
            }
            
            // Buat HTML untuk modal video player
            const videoPlayerHtml = `
                <div class="modal fade" id="videoPlayerModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-lg">
                        <div class="modal-content bg-dark">
                            <div class="modal-header border-0">
                                <h5 class="modal-title text-white">Preview Video</h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body p-0">
                                <video src="${evidenceVideos[videoIndex].url}" controls autoplay class="w-100 rounded"></video>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            // Tambahkan modal ke body
            const modalContainer = document.createElement('div');
            modalContainer.innerHTML = videoPlayerHtml;
            document.body.appendChild(modalContainer);
            
            // Tampilkan modal
            const videoPlayerModal = new bootstrap.Modal(document.getElementById('videoPlayerModal'));
            videoPlayerModal.show();
            
            // Bersihkan DOM setelah modal ditutup
            document.getElementById('videoPlayerModal').addEventListener('hidden.bs.modal', function() {
                document.body.removeChild(modalContainer);
            });
        }

        // Menggunakan delegasi event untuk menangani semua interaksi dengan video
        document.addEventListener('DOMContentLoaded', function() {
            // Setup event handlers
            setupEvidenceEventHandlers();
            
            // Setup event delegasi untuk video play
            setupVideoPlayDelegation();
        });
        
        // Setup delegasi event untuk tombol play video
        function setupVideoPlayDelegation() {
            console.log('Setting up video play delegation');
            
            // Gunakan event delegasi untuk container
            const previewContainer = document.getElementById('evidencePreviewContainer');
            
            previewContainer.addEventListener('click', function(e) {
                // Cek jika click pada tombol play atau video preview
                const playButton = e.target.closest('.play-video-btn');
                const videoPreview = e.target.closest('.video-preview');
                
                if (playButton || videoPreview) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    // Ambil index dari element yang diklik
                    const clickedElement = playButton || videoPreview;
                    const videoIndex = parseInt(clickedElement.getAttribute('data-index'));
                    
                    console.log('Video play clicked, index:', videoIndex);
                    
                    if (!isNaN(videoIndex)) {
                        showVideoPlayer(videoIndex);
                    } else {
                        console.error('Invalid video index');
                    }
                }
            });
        }

        // Fungsi untuk memutar video - versi sederhana langsung
        function putarVideo(index) {
            console.log('Memutar video dengan index:', index);
            
            try {
                // Periksa apakah video tersedia
                if (!evidenceVideos[index] || !evidenceVideos[index].url) {
                    alert('Video tidak tersedia');
                    return;
                }
                
                // Buat overlay sederhana
                const overlay = document.createElement('div');
                overlay.style.cssText = `
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    background: rgba(0, 0, 0, 0.9);
                    z-index: 9999;
                    display: flex;
                    flex-direction: column;
                    justify-content: center;
                    align-items: center;
                `;
                
                // Buat tombol tutup
                const closeButton = document.createElement('button');
                closeButton.innerHTML = 'Tutup Video';
                closeButton.style.cssText = `
                    position: absolute;
                    top: 20px;
                    right: 20px;
                    background: #dc3545;
                    color: white;
                    border: none;
                    padding: 10px 20px;
                    border-radius: 5px;
                    cursor: pointer;
                    font-weight: bold;
                `;
                
                // Buat video player
                const videoPlayer = document.createElement('video');
                videoPlayer.src = evidenceVideos[index].url;
                videoPlayer.controls = true;
                videoPlayer.autoplay = true;
                videoPlayer.style.cssText = `
                    max-width: 90%;
                    max-height: 80vh;
                    border-radius: 4px;
                `;
                
                // Tambahkan ke overlay
                overlay.appendChild(closeButton);
                overlay.appendChild(videoPlayer);
                
                // Tambahkan ke body
                document.body.appendChild(overlay);
                
                // Tambahkan event listener untuk menutup
                closeButton.addEventListener('click', function() {
                    document.body.removeChild(overlay);
                });
                
                // Fungsi untuk menutup dengan escape key
                function handleKeyDown(e) {
                    if (e.key === 'Escape') {
                        document.body.removeChild(overlay);
                        document.removeEventListener('keydown', handleKeyDown);
                    }
                }
                
                // Tambahkan event listener keyboard
                document.addEventListener('keydown', handleKeyDown);
                
                // Log untuk debugging
                console.log('Video player dibuat dan ditampilkan');
            } catch (error) {
                console.error('Error saat memainkan video:', error);
                alert('Terjadi kesalahan saat memainkan video. Silakan coba lagi.');
            }
        }

        // Fungsi untuk membuka dan memutar video dalam modal
        function bukaPutarVideo(index) {
            console.log('Memutar video:', index);
            
            // Pastikan video tersedia
            if (!evidenceVideos[index] || !evidenceVideos[index].url) {
                alert('Video tidak ditemukan');
                return;
            }
            
            // Tambahkan div modal ke body
            const modalElem = document.createElement('div');
            modalElem.id = 'videoModal';
            modalElem.className = 'modal fade';
            modalElem.tabIndex = '-1';
            modalElem.innerHTML = `
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Preview Video</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body p-0">
                            <video src="${evidenceVideos[index].url}" controls autoplay style="width: 100%;"></video>
                        </div>
                    </div>
                </div>
            `;
            
            document.body.appendChild(modalElem);
            
            // Tampilkan modal
            const modalInstance = new bootstrap.Modal(modalElem);
            modalInstance.show();
            
            // Hapus modal dari DOM setelah ditutup
            modalElem.addEventListener('hidden.bs.modal', function() {
                document.body.removeChild(modalElem);
            });
        }

        // Render kanban pada saat dokumen siap
        $(document).ready(function() {                    
            // Inisialisasi tooltips untuk semua elemen dengan data-bs-toggle="tooltip"
            initTooltips();
            
            // Debug: Periksa apakah tasks-box sudah dimuat
            console.log('Document ready: tasks-box count:', $('.tasks-box').length);
            console.log('Document ready: task-card count:', $('.task-card').length);
            
            // Evidence dikelola oleh maintenance-evidence.js
            // loadTaskEvidence();
            
            // Manual init EvidenceApp
            if(typeof EvidenceApp !== 'undefined') {
                console.log('Manual initializing EvidenceApp');
                setTimeout(function() {
                    EvidenceApp.init();
                }, 500); // Delay 500ms untuk memastikan semua elemen sudah siap
            } else {
                console.error('EvidenceApp is not defined!');
            }
        });
        
        // Fungsi untuk mengambil data evidence
        /* 
        function loadTaskEvidence() {
            // Ambil semua task ID yang ada di halaman
            const taskIds = [];
            $('.task-card').each(function() {
                const taskId = $(this).data('task-id');
                if (taskId) taskIds.push(taskId);
            });
            
            if (taskIds.length === 0) return;
            
            console.log(`Loading evidence data for ${taskIds.length} tasks`);
            
            // Ambil data evidence untuk setiap task
            taskIds.forEach(taskId => {
                $.ajax({
                    url: `/maintenance/kanban/task/${taskId}/evidence`,
                    method: 'GET',
                    success: function(response) {
                        if (response.success && response.data && response.data.length > 0) {
                            console.log(`Found ${response.data.length} evidence items for task ${taskId}`);
                            // Tambahkan evidence ke task
                            addEvidenceToTask(taskId, response.data);
                        }
                    },
                    error: function(xhr) {
                        console.error(`Error loading evidence for task ${taskId}:`, xhr);
                    }
                });
            });
        }
        */
        
        // Fungsi untuk menambahkan evidence ke task card
        /*
        function addEvidenceToTask(taskId, evidenceData) {
            const $taskCard = $(`.task-card[data-task-id="${taskId}"]`);
            if (!$taskCard.length) return;
            
            // Hanya tampilkan evidence jika ada data dan status board adalah IN_REVIEW atau DONE
            const boardId = $taskCard.closest('.kanban-board').attr('id');
            if (evidenceData.length === 0 || (boardId !== 'review-list' && boardId !== 'done-list')) {
                return;
            }
            
            // Buat tombol toggle untuk evidence
            const evidenceToggleHTML = `
                <div class="evidence-toggle mt-2">
                    <button class="btn btn-sm btn-light text-left w-100 d-flex align-items-center justify-content-between evidence-toggle-btn" 
                            data-task-id="${taskId}">
                        <span>
                            <i class="ri-camera-line me-1"></i>
                            Evidence (${evidenceData.length})
                        </span>
                        <i class="ri-arrow-down-s-line evidence-toggle-icon"></i>
                    </button>
                </div>
            `;
            
            // Buat HTML untuk preview evidence
            let evidencePreviewHTML = '';
            
            evidenceData.forEach((evidence, index) => {
                // Buat section untuk notes evidence
                const notesHTML = evidence.notes ? `
                    <div class="evidence-notes p-2 mb-2 bg-light rounded">
                        <div class="preview-title mb-1">
                            <span class="fw-bold text-muted fs-sm"><i class="ri-sticky-note-line me-1"></i>Notes</span>
                        </div>
                        <p class="mb-0 small">${evidence.notes}</p>
                    </div>
                ` : '';
                
                // Ambil semua foto evidence jika ada
                let photosPreviewHTML = '';
                if (evidence.photos && evidence.photos.length > 0) {
                    // Maksimal tampil 3 foto
                    const maxPhotoDisplay = Math.min(3, evidence.photos.length);
                    let photosThumbnailHTML = '';
                    
                    // Buat HTML thumbnail
                    for (let i = 0; i < maxPhotoDisplay; i++) {
                        const photo = evidence.photos[i];
                        photosThumbnailHTML += `
                            <div class="photo-preview-item" data-media-path="${photo.full_url || '/storage/' + photo.path}" data-media-type="image">
                                <img src="${photo.full_url || '/storage/' + photo.path}" 
                                     alt="${photo.file_name}" 
                                     class="rounded-circle" 
                                     style="width: 40px; height: 40px; object-fit: cover; border-radius: 50% !important; cursor: pointer;">
                            </div>
                        `;
                    }
                    
                    // Jika ada lebih dari 3 foto, tambahkan badge +N
                    if (evidence.photos.length > 3) {
                        photosThumbnailHTML += `
                            <div class="photo-preview-more">
                                <span class="badge rounded-pill bg-secondary">+${evidence.photos.length - 3}</span>
                            </div>
                        `;
                    }
                    
                    // Tambahkan judul dan container
                    photosPreviewHTML = `
                        <div class="preview-section mt-2">
                            <div class="preview-title mb-1">
                                <span class="fw-bold text-muted fs-sm"><i class="ri-image-line me-1"></i>Photos (${evidence.photos.length})</span>
                            </div>
                            <div class="task-media-preview d-flex align-items-center gap-2">
                                ${photosThumbnailHTML}
                            </div>
                        </div>
                    `;
                }
                
                // Ambil semua video evidence jika ada
                let videosPreviewHTML = '';
                if (evidence.videos && evidence.videos.length > 0) {
                    // Maksimal tampil 3 video
                    const maxVideoDisplay = Math.min(3, evidence.videos.length);
                    let videosThumbnailHTML = '';
                    
                    // Buat HTML thumbnail
                    for (let i = 0; i < maxVideoDisplay; i++) {
                        const video = evidence.videos[i];
                        videosThumbnailHTML += `
                            <div class="video-preview-item" data-media-path="${video.full_url || '/storage/' + video.path}" data-media-type="video">
                                <div class="video-thumbnail position-relative" style="width: 40px; height: 40px;">
                                    <div class="bg-dark rounded-circle" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; cursor: pointer;">
                                        <i class="ri-play-fill text-white"></i>
                                    </div>
                                </div>
                            </div>
                        `;
                    }
                    
                    // Jika ada lebih dari 3 video, tambahkan badge +N
                    if (evidence.videos.length > 3) {
                        videosThumbnailHTML += `
                            <div class="video-preview-more">
                                <span class="badge rounded-pill bg-secondary">+${evidence.videos.length - 3}</span>
                            </div>
                        `;
                    }
                    
                    // Tambahkan judul dan container
                    videosPreviewHTML = `
                        <div class="preview-section mt-2">
                            <div class="preview-title mb-1">
                                <span class="fw-bold text-muted fs-sm"><i class="ri-movie-line me-1"></i>Videos (${evidence.videos.length})</span>
                            </div>
                            <div class="task-video-preview d-flex align-items-center gap-2">
                                ${videosThumbnailHTML}
                            </div>
                        </div>
                    `;
                }
                
                // Info pembuat evidence
                const createdInfo = `
                    <div class="evidence-info d-flex justify-content-between align-items-center mt-2">
                        <small class="text-muted">${evidence.created_at_formatted || formatDate(evidence.created_at)}</small>
                        <small class="text-muted">by ${evidence.creator_name || 'Unknown'}</small>
                    </div>
                `;
                
                // Gabungkan semua bagian evidence
                evidencePreviewHTML += `
                    <div class="evidence-item p-2 mb-2 border-bottom" data-evidence-id="${evidence.id}">
                        ${notesHTML}
                        ${photosPreviewHTML}
                        ${videosPreviewHTML}
                        ${createdInfo}
                    </div>
                `;
            });
            
            // Gabungkan menjadi evidence content
            const evidenceContentHTML = `
                <div class="evidence-content" style="display: none;">
                    <div class="evidence-preview mt-2">
                        ${evidencePreviewHTML}
                    </div>
                </div>
            `;
            
            // Gabungkan toggle dan content evidence
            const evidenceSection = `
                <div class="evidence-section">
                    ${evidenceToggleHTML}
                    ${evidenceContentHTML}
                </div>
            `;
            
            // Tambahkan evidence section setelah media section
            const $mediaSection = $taskCard.find('.media-section');
            if ($mediaSection.length > 0) {
                $mediaSection.after(evidenceSection);
            } else {
                // Jika tidak ada media section, cari tempat yang tepat untuk menambahkan
                const $cardBody = $taskCard.find('.card-body');
                $cardBody.append(evidenceSection);
            }
            
            // Sembunyikan tombol "Capture Evidence" jika ada di task card
            $taskCard.find('.simple-evidence-btn').hide();
            
            // Tambahkan event handler untuk evidence toggle
            $taskCard.find('.evidence-toggle-btn').on('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                const $btn = $(this);
                const $content = $btn.closest('.evidence-section').find('.evidence-content');
                const $icon = $btn.find('.evidence-toggle-icon');
                
                // Toggle expanded class pada button
                $btn.toggleClass('expanded');
                
                // Toggle icon rotation
                if ($btn.hasClass('expanded')) {
                    $icon.removeClass('ri-arrow-down-s-line').addClass('ri-arrow-up-s-line');
                    $content.slideDown(200);
                } else {
                    $icon.removeClass('ri-arrow-up-s-line').addClass('ri-arrow-down-s-line');
                    $content.slideUp(200);
                }
                
                // Prevent bubbling up the event
                return false;
            });
        }
        */
        
        // Helper: Format tanggal
        function formatDate(dateString) {
            if (!dateString) return '';
            const date = new Date(dateString);
            return date.toLocaleDateString('id-ID', {
                day: '2-digit',
                month: 'short',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        }

        // Fungsi untuk menambahkan tanggal completed ke task di board Done
        function addCompletedDateToDoneCards() {
            // Ambil semua task card di board done
            $('#done-list .tasks-box').each(function() {
                const taskId = $(this).data('task-id');
                
                // Periksa apakah sudah ada completed date
                if ($(this).find('.completed-date').length === 0) {
                    // Ambil data task completed
                    $.ajax({
                        url: `/maintenance/kanban/task/${taskId}`,
                        method: 'GET',
                        success: function(response) {
                            if (response.success && response.task && response.task.completed_at) {
                                // Format tanggal
                                const completedDate = new Date(response.task.completed_at);
                                const formattedDate = completedDate.toLocaleDateString('id-ID', {
                                    day: '2-digit',
                                    month: 'short',
                                    year: 'numeric'
                                });
                                
                                // Ambil due date untuk perbandingan warna
                                const dueDate = response.task.due_date ? new Date(response.task.due_date) : null;
                                
                                // Tentukan warna
                                let textColor = 'text-dark'; // Default untuk sama hari
                                
                                if (dueDate) {
                                    // Reset jam untuk perbandingan tanggal saja
                                    const dueDateNoTime = new Date(dueDate).setHours(0,0,0,0);
                                    const completedDateNoTime = new Date(completedDate).setHours(0,0,0,0);
                                    
                                    if (completedDateNoTime < dueDateNoTime) {
                                        textColor = 'text-success'; // Sebelum due date
                                    } else if (completedDateNoTime > dueDateNoTime) {
                                        textColor = 'text-danger'; // Setelah due date
                                    }
                                }
                                
                                // Tambahkan elemen completed date setelah due date
                                const dueElem = $(`.tasks-box[data-task-id="${taskId}"] .card-body .text-muted, .tasks-box[data-task-id="${taskId}"] .card-body .text-danger, .tasks-box[data-task-id="${taskId}"] .card-body .text-warning`).filter(function() {
                                    return $(this).text().includes('Due:');
                                });
                                
                                if (dueElem.length > 0) {
                                    dueElem.after(`
                                        <div class="mb-2 completed-date">
                                            <small class="${textColor}">
                                                <i class="ri-check-double-line me-1"></i> Selesai: ${formattedDate}
                                            </small>
                                        </div>
                                    `);
                                    
                                    // Ubah warna due date menjadi hitam
                                    dueElem.removeClass('text-danger text-warning text-muted').addClass('text-dark');
                                }
                            }
                        }
                    });
                }
            });
        }

        // Panggil fungsi ini setelah tasks dimuat
        $(document).ready(function() {
            // Observer untuk memonitor perubahan di kanban board
            const observer = new MutationObserver(function(mutations) {
                mutations.forEach(function(mutation) {
                    if (mutation.type === 'childList' && mutation.addedNodes.length > 0) {
                        // Cek apakah ada task card ditambahkan ke done-list
                        if ($(mutation.target).is('#done-list') || $(mutation.target).parents('#done-list').length > 0) {
                            // Tambahkan delay kecil untuk memastikan DOM sudah terupdate
                            setTimeout(addCompletedDateToDoneCards, 500);
                        }
                    }
                });
            });
            
            // Mulai observasi
            observer.observe(document.getElementById('done-list'), { childList: true, subtree: true });
            
            // Juga panggil fungsi saat halaman dimuat awal
            setTimeout(addCompletedDateToDoneCards, 1000);
        });

        // Fungsi untuk menyembunyikan task yang sudah lebih dari 3 hari di done board
        function hideOldDoneTasks() {
            $('#done-list .tasks-box').each(function() {
                const taskId = $(this).data('task-id');
                
                $.ajax({
                    url: `/maintenance/kanban/task/${taskId}`,
                    method: 'GET',
                    success: function(response) {
                        if (response.success && response.task && response.task.completed_at) {
                            // Hitung selisih hari
                            const completedDate = new Date(response.task.completed_at);
                            const currentDate = new Date();
                            
                            // Reset jam untuk perbandingan tanggal saja
                            completedDate.setHours(0, 0, 0, 0);
                            currentDate.setHours(0, 0, 0, 0);
                            
                            // Hitung selisih hari
                            const diffTime = Math.abs(currentDate - completedDate);
                            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                            
                            // Jika lebih dari 3 hari, sembunyikan task
                            if (diffDays > 3) {
                                $(`.tasks-box[data-task-id="${taskId}"]`).fadeOut('slow', function() {
                                    $(this).remove();
                                    updateTaskCounters();
                                });
                            }
                        }
                    }
                });
            });
        }

        // Panggil fungsi ini setelah task dimuat dan setiap kali ada perubahan di board done
        $(document).ready(function() {
            // Tambahkan ke observer yang sudah ada
            setTimeout(hideOldDoneTasks, 1500);
            
            // Jalankan pengecekan setiap 1 jam (untuk kasus user membuka halaman lama)
            setInterval(hideOldDoneTasks, 60 * 60 * 1000);
        });

        // Tambahkan fungsi ini di bawah fungsi updateTaskStatus
        // Fungsi ini akan berjalan sekali setelah halaman dimuat

        $(document).ready(function() {
            // Tunggu 2 detik untuk memastikan halaman dimuat sempurna
            setTimeout(function() {
                console.log("Menambahkan tanggal completed ke task di board Done");
                
                // Ambil semua task card di board done
                $('#done-list .tasks-box').each(function() {
                    const taskId = $(this).attr('data-task-id');
                    console.log("Processing task ID:", taskId);
                    
                    // AJAX untuk mengambil detail task
                    $.ajax({
                        url: `/maintenance/task/${taskId}`,
                        method: 'GET',
                        success: function(response) {
                            console.log("Response for task:", response);
                            
                            // Pastikan respons sukses dan ada completed_at
                            if (response.success && response.task && response.task.completed_at) {
                                console.log("Task has completed_at:", response.task.completed_at);
                                
                                // Format tanggal completed
                                const completedDate = new Date(response.task.completed_at);
                                const formattedDate = completedDate.toLocaleDateString('id-ID', {
                                    day: '2-digit',
                                    month: 'short', 
                                    year: 'numeric'
                                });
                                
                                // Cek apakah due date ada
                                let dueDate = null;
                                if (response.task.due_date) {
                                    dueDate = new Date(response.task.due_date);
                                }
                                
                                // Tentukan warna berdasarkan perbandingan dengan due date
                                let textColor = 'text-dark'; // Default warna hitam
                                
                                if (dueDate) {
                                    // Reset jam untuk perbandingan tanggal saja
                                    const dueDateOnly = new Date(dueDate.setHours(0,0,0,0));
                                    const completedDateOnly = new Date(completedDate.setHours(0,0,0,0));
                                    
                                    // Bandingkan tanggal
                                    if (completedDateOnly.getTime() < dueDateOnly.getTime()) {
                                        textColor = 'text-success'; // Lebih cepat dari due date = hijau
                                    } else if (completedDateOnly.getTime() > dueDateOnly.getTime()) {
                                        textColor = 'text-danger'; // Lebih lambat dari due date = merah
                                    }
                                }
                                
                                // Cari elemen due date di card
                                const card = $(`.tasks-box[data-task-id="${taskId}"]`);
                                
                                // Cari elemen small di card yang memiliki teks "Due:"
                                const dueElement = card.find('small').filter(function() {
                                    return $(this).text().indexOf('Due:') !== -1;
                                });
                                
                                // Ubah warna due date menjadi hitam
                                dueElement.removeClass('text-muted text-danger text-warning').addClass('text-dark');
                                
                                // Jika elemen due date ditemukan dan belum ada completed date
                                if (dueElement.length && !card.find('.completed-date-text').length) {
                                    console.log("Adding completed date after due date");
                                    
                                    // Tambahkan completed date setelah due date
                                    dueElement.after(`
                                        <br>
                                        <small class="${textColor} completed-date-text">
                                            <i class="ri-check-double-line align-middle me-1"></i> Selesai: ${formattedDate}
                                        </small>
                                    `);
                                }
                            }
                        },
                        error: function(xhr) {
                            console.error("Error getting task details:", xhr);
                        }
                    });
                });
            }, 2000);
        });

        document.addEventListener('DOMContentLoaded', function() {
            const generateBtn = document.getElementById('generateReportBtn');
            
            if (generateBtn) {
                generateBtn.addEventListener('click', function() {
                    const originalText = this.innerHTML;
                    this.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Generating...';
                    this.disabled = true;
                    
                    // Create XHR request for download
                    const xhr = new XMLHttpRequest();
                    xhr.open('GET', reportForm.action + '?period=' + document.querySelector('input[name="period"]').value, true);
                    xhr.responseType = 'blob';
                    
                    xhr.onload = function() {
                        // Reset button after response received
                        generateBtn.innerHTML = originalText;
                        generateBtn.disabled = false;
                        
                        if (this.status === 200) {
                            // Create blob URL and trigger download
                            const blob = new Blob([this.response], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });
                            const link = document.createElement('a');
                            link.href = URL.createObjectURL(blob);
                            link.download = 'task_status_report_' + new Date().toISOString().replace(/[:.]/g, '_') + '.xlsx';
                            document.body.appendChild(link);
                            link.click();
                            document.body.removeChild(link);
                        } else {
                            console.error('Download failed');
                            alert('Download failed. Please try again.');
                        }
                    };
                    
                    xhr.onerror = function() {
                        generateBtn.innerHTML = originalText;
                        generateBtn.disabled = false;
                        console.error('XHR error');
                        alert('Download failed. Please try again.');
                    };
                    
                    xhr.send();
                });
            }
        });
    </script>

    <script src="{{ URL::asset('build/js/app.js') }}"></script>

    <!-- Include PR modals -->
    @include('maintenance.purchase-requisition.modals')

    <!-- Purchase Requisition JS -->
    <script src="{{ asset('build/js/maintenance-pr.js') }}"></script>

    <!-- Purchase Order JS -->
    <script src="{{ URL::asset('build/js/maintenance-po.js') }}"></script>

    <!-- Evidence JS -->
    <script src="{{ asset('build/js/maintenance-evidence.js') }}"></script>

    <!-- Modal Evidence HTML -->
    <div class="modal fade" id="evidenceModal" tabindex="-1" aria-labelledby="evidenceModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="evidenceModalLabel">Capture Evidence</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-6 mb-2">
                            <button type="button" class="btn btn-primary w-100" id="evidenceTakePictureBtn">
                                <i class="ri-camera-line me-1"></i> Ambil Foto
                            </button>
                        </div>
                        <div class="col-md-6 mb-2">
                            <button type="button" class="btn btn-info w-100" id="evidenceRecordVideoBtn">
                                <i class="ri-video-line me-1"></i> Rekam Video
                            </button>
                        </div>
                    </div>
                    
                    <!-- Camera view -->
                    <div id="evidenceCameraContainer" class="d-none mb-3">
                        <div class="position-relative bg-black rounded overflow-hidden" style="min-height: 300px;">
                            <video id="evidenceCameraView" class="w-100 rounded" autoplay playsinline style="max-height: 60vh;"></video>
                            <div class="position-absolute bottom-0 start-0 p-2 text-white bg-dark bg-opacity-50 rounded-3 m-2 d-none" id="evidenceRecordingIndicator">
                                <span id="evidenceRecordingTime">00:00</span>
                            </div>
                            <div class="position-absolute top-0 end-0 p-2">
                                <button type="button" id="evidenceSwitchCameraBtn" class="btn btn-sm btn-light">
                                    <i class="ri-camera-switch-line"></i>
                                </button>
                            </div>
                        </div>
                        <div class="d-flex justify-content-center mt-2">
                            <button type="button" id="evidenceCaptureBtn" class="btn btn-success mx-1">
                                <i class="ri-camera-line me-1"></i> Capture
                            </button>
                            <button type="button" id="evidenceStartRecordingBtn" class="btn btn-danger mx-1 d-none">
                                <i class="ri-record-circle-line me-1"></i> Mulai Rekam
                            </button>
                            <button type="button" id="evidenceStopRecordingBtn" class="btn btn-warning mx-1 d-none">
                                <i class="ri-stop-circle-line me-1"></i> Stop
                            </button>
                            <button type="button" id="evidenceCloseCameraBtn" class="btn btn-secondary mx-1">
                                <i class="ri-close-line me-1"></i> Tutup Kamera
                            </button>
                        </div>
                    </div>
                    
                    <!-- Captured media preview -->
                    <div class="mb-3">
                        <h6>Media yang Ditangkap</h6>
                        <div id="evidencePreviewContainer" class="d-flex flex-wrap gap-2">
                            <!-- Preview items will be added here -->
                            <div class="text-muted" id="evidenceNoMediaMessage">Belum ada media yang ditangkap</div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="evidenceNotes" class="form-label">Catatan</label>
                        <textarea class="form-control" id="evidenceNotes" rows="3" placeholder="Tambahkan catatan untuk evidence ini..."></textarea>
                    </div>

                    <div class="alert alert-warning d-none" id="evidenceBrowserWarning">
                        <strong>Peringatan!</strong> Browser Anda tidak mendukung akses kamera atau tidak mengizinkan akses. 
                        Silakan gunakan browser modern seperti Chrome, Firefox, Edge, atau Safari terbaru.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" id="evidenceSaveBtn">Simpan Evidence</button>
                </div>
            </div>
        </div>
    </div>

    @push('script')

   
@endpush
   
@endsection 