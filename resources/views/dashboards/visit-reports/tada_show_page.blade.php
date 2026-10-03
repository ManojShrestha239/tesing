@extends('dashboards.sessions.layout')

@section('content')
<div class="page-content">
    <div class="breadcrumbs">
        <h1>Daily Report Details - {{ $report->entry_date_bs ?? $report->entry_date }}</h1>
        <!-- Sidebar Toggle Button -->
        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".page-sidebar">
            <span class="sr-only">Toggle navigation</span>
            <span class="toggle-icon">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </span>
        </button>
        <!-- Sidebar Toggle Button -->
    </div>

    <div class="page-content-container">
        <div class="page-content-row">
            <div class="col-md-12">
                <div class="portlet light portlet-fit bordered">
                    <div class="portlet-title">

                        <div class="actions">
                            <a href="{{ route('tada-report.index') }}" class="btn btn-default btn-sm"><i class="fa fa-arrow-left"></i> Back</a>
                        </div>
                    </div>
                    <div class="portlet-body">
                        @include('dashboards.visit-reports.tada_show_modal', ['report' => $report, 'visitList' => $visitList, 'from' => $from, 'to' => $to])
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Expense Images Modal -->
    <div class="modal" id="tadaExpenseImagesModal" tabindex="-1" role="dialog" aria-hidden="true" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.75); z-index: 11050; overflow: auto;">
        <div class="modal-dialog modal-lg" role="document" style="z-index:11060; margin: 60px auto;">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="tadaExpenseImagesModalLabel">Expense Files</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="tadaExpenseImagesCarousel" class="carousel slide" data-ride="carousel" data-interval="false">
                        <div class="carousel-inner"></div>
                        <a class="left carousel-control carousel-control-prev" href="#tadaExpenseImagesCarousel" role="button" data-slide="prev" data-bs-slide="prev">
                            <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="sr-only">Previous</span>
                        </a>
                        <a class="right carousel-control carousel-control-next" href="#tadaExpenseImagesCarousel" role="button" data-slide="next" data-bs-slide="next">
                            <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="sr-only">Next</span>
                        </a>
                    </div>
                    <div class="no-files-message text-center text-muted" style="display:none; padding: 40px 0;">
                        No files available.
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        $(document).ready(function() {
            $('#tadaExpenseImagesModal').appendTo('body');

            function isImageUrl(url) {
                return /\.(png|jpe?g|gif|webp|bmp|svg)(\?|$)/i.test(url);
            }

            function extractFileName(url) {
                var parts = url.split('/');
                var name = parts[parts.length - 1] || url;
                return decodeURIComponent(name.split('?')[0]);
            }

            function buildExpenseImageCarousel(images) {
                var inner = $('#tadaExpenseImagesCarousel .carousel-inner');
                var prevControl = $('#tadaExpenseImagesCarousel .carousel-control-prev');
                var nextControl = $('#tadaExpenseImagesCarousel .carousel-control-next');
                var noFilesMessage = $('#tadaExpenseImagesModal .no-files-message');
                inner.empty();
                noFilesMessage.hide();

                var imageUrls = images.filter(function(url) {
                    return isImageUrl(url);
                });

                if (!imageUrls.length) {
                    $('#tadaExpenseImagesCarousel').hide();
                    prevControl.hide();
                    nextControl.hide();
                    noFilesMessage.show();
                    return false;
                }

                $('#tadaExpenseImagesCarousel').show();
                noFilesMessage.hide();

                imageUrls.forEach(function(url, index) {
                    var itemClass = 'item carousel-item' + (index === 0 ? ' active' : '');
                    var item = $('<div>').addClass(itemClass + ' text-center');
                    item.append(
                        $('<img>').attr('src', url).addClass('expense-preview-img')
                    );
                    item.append($('<div>').addClass('text-center mt-3 small text-muted').text(extractFileName(url)));
                    inner.append(item);
                });

                if (imageUrls.length > 1) {
                    prevControl.show();
                    nextControl.show();
                } else {
                    prevControl.hide();
                    nextControl.hide();
                }

                $('#tadaExpenseImagesCarousel').carousel({
                    interval: false,
                    wrap: true
                });
                $('#tadaExpenseImagesCarousel').carousel(0);
                return true;
            }

            function showModal(modal) {
                if (!$('.modal-backdrop').length) {
                    $('<div class="modal-backdrop fade in"></div>').appendTo('body');
                }
                $('.modal-backdrop').addClass('show in');
                modal.css({
                    'display': 'block',
                    'visibility': 'visible',
                    'opacity': '1',
                    'pointer-events': 'auto'
                }).addClass('show in');
                modal.attr('aria-hidden', 'false');
                $('body').addClass('modal-open');
            }

            function hideModal(modal) {
                modal.css({
                    'display': 'none',
                    'visibility': 'hidden',
                    'opacity': '0',
                    'pointer-events': 'none'
                }).removeClass('show in');
                modal.attr('aria-hidden', 'true');
                $('body').removeClass('modal-open');
                $('.modal-backdrop').remove();
            }

            $(document).on('click', '.view-expense-images-btn', function() {
                var imagesRaw = $(this).attr('data-images') || '[]';
                var images = [];
                try {
                    images = JSON.parse(imagesRaw);
                } catch (e) {
                    console.error('Invalid image list:', imagesRaw, e);
                    images = [];
                }
                buildExpenseImageCarousel(images);
                showModal($('#tadaExpenseImagesModal'));
            });

            $(document).on('click', '#tadaExpenseImagesModal [data-dismiss="modal"], #tadaExpenseImagesModal .close', function() {
                hideModal($('#tadaExpenseImagesModal'));
            });
        });
    </script>
    <style>
        .expense-preview-img {
            max-height: 75vh;
            max-width: 100%;
            object-fit: contain;
            display: block;
            margin: 0 auto;
        }

        .pdf-placeholder {
            display: none;
        }

        .carousel-inner .item,
        .carousel-inner .carousel-item {
            min-height: 60vh;
            align-items: center;
            justify-content: center;
        }

        .carousel-inner .item.active,
        .carousel-inner .carousel-item.active {
            display: flex !important;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        #tadaExpenseImagesModal .modal-body {
            background-color: #ffffff !important;
        }

        #tadaExpenseImagesCarousel {
            background-color: #ffffff !important;
        }

        #tadaExpenseImagesCarousel .carousel-inner {
            background-color: #ffffff !important;
        }

        #tadaExpenseImagesCarousel .carousel-item,
        #tadaExpenseImagesCarousel .item {
            background-color: #ffffff !important;
        }

        /* Carousel Navigation Controls Styles */
        #tadaExpenseImagesModal .carousel-control {
            background: transparent !important;
            background-image: none !important;
            opacity: 0.5;
            transition: opacity 0.2s ease-in-out;
            width: 8% !important;
        }

        #tadaExpenseImagesModal .carousel-control:hover,
        #tadaExpenseImagesModal .carousel-control:focus {
            opacity: 0.9;
            background: transparent !important;
            background-image: none !important;
        }

        #tadaExpenseImagesModal .carousel-control .glyphicon,
        #tadaExpenseImagesModal .carousel-control .carousel-control-prev-icon,
        #tadaExpenseImagesModal .carousel-control .carousel-control-next-icon {
            color: #333333 !important;
            text-shadow: none !important;
        }

        #tadaExpenseImagesModal .carousel-control-prev-icon,
        #tadaExpenseImagesModal .carousel-control-next-icon {
            filter: invert(1) brightness(0.2) !important;
        }

        .modal-backdrop {
            z-index: 11040 !important;
        }

        #tadaExpenseImagesModal .modal-content {
            background-color: #ffffff !important;
        }
    </style>
    @endpush
</div>
@endsection