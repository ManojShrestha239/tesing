// followup form steps js start
document.addEventListener("DOMContentLoaded", function () {
    let currentStep = 1;
    const totalSteps = 3;

    const steps = document.querySelectorAll(".msjk-step-item");
    const pages = document.querySelectorAll(".msjk-step-content");
    const dots = document.querySelectorAll(".msjk-pagination-dots span");

    const nextBtn = document.querySelector(".msjk-next-btn");
    const prevBtn = document.querySelector(".msjk-prev-btn");

    function showStep(step) {
        currentStep = step;

        pages.forEach(function (page) {
            page.classList.remove("msjk-step-content-active");
        });

        steps.forEach(function (item) {
            item.classList.remove("msjk-step-item-active");
        });

        dots.forEach(function (dot) {
            dot.classList.remove("msjk-pagination-dots-active");
        });

        document
            .getElementById("msjk-page-" + step)
            .classList.add("msjk-step-content-active");

        document
            .querySelector('.msjk-step-item[data-step="' + step + '"]')
            .classList.add("msjk-step-item-active");

        dots[step - 1].classList.add("msjk-pagination-dots-active");

        prevBtn.style.visibility = step === 1 ? "hidden" : "visible";

        nextBtn.innerHTML =
            step === totalSteps ? "Complete Follow-up" : "Next Step";
    }

    nextBtn.addEventListener("click", function () {
        if (currentStep < totalSteps) {
            showStep(currentStep + 1);
        }
    });

    prevBtn.addEventListener("click", function () {
        if (currentStep > 1) {
            showStep(currentStep - 1);
        }
    });

    steps.forEach(function (step) {
        step.addEventListener("click", function () {
            showStep(parseInt(this.dataset.step));
        });
    });
});
// followup form steps js ends
