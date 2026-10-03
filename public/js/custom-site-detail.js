$(".msjk-view-toggle .btn").click(function () {
    $(".msjk-view-toggle .btn").removeClass("active");
    $(this).addClass("active");
});
// toggle js ends

// filter js starts
$(".msjk-filter-btn").on("click", function () {
    $(".msjk-filter-btn").removeClass("active");
    $(this).addClass("active");
});
// filter js ends
$(".msjk-action-btn").on("click", function (e) {
    e.stopPropagation();

    $(".msjk-action-menu")
        .not($(this).siblings(".msjk-action-menu"))
        .removeClass("show");

    $(this).siblings(".msjk-action-menu").toggleClass("show");
});

$(document).on("click", function () {
    $(".msjk-action-menu").removeClass("show");
});

// tab js starts
$(document).ready(function () {
    $(document).on("click", ".msjk-site-tab-btn", function () {
        var target = $(this).data("target");

        $(".msjk-site-tab-btn").removeClass("msjk-site-tab-active");
        $(this).addClass("msjk-site-tab-active");

        $(".msjk-site-tab-pane").removeClass("msjk-site-tab-pane-active");
        $(target).addClass("msjk-site-tab-pane-active");
    });
});
// tab js ends

// photos tab js starts
$(document).on("click", ".msjk-gallery-filter", function () {
    var filter = $(this).data("filter");

    $(".msjk-gallery-filter").removeClass("msjk-gallery-filter-active");
    $(this).addClass("msjk-gallery-filter-active");

    if (filter === "all") {
        $(".msjk-contact-section-col").show();
    } else {
        $(".msjk-contact-section-col").hide();
        $('.msjk-contact-section-col[data-category="' + filter + '"]').show();
    }
});
// photos tab js ends
