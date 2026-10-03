document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("siteRegistrationForm");
    const steps = document.querySelectorAll(".msjk-sitereg-step");
    const pages = document.querySelectorAll(".msjk-sitereg-page");
    const nextBtn = document.querySelector(".msjk-sitereg-next-btn");
    const prevBtn = document.querySelector(".msjk-sitereg-prev-btn");
    const addStakeholderBtn = document.getElementById("addStakeholderBtn");
    const stakeholderRows = document.getElementById("stakeholderRows");
    const addProductBtn = document.getElementById("addProductBtn");
    const productRows = document.getElementById("productRows");
    const productOptionsTemplate = document.getElementById(
        "productOptionsTemplate",
    );
    const useCurrentLocationBtn = document.getElementById(
        "useCurrentLocationBtn",
    );
    const sitePhotosInput = document.getElementById("site_photos");
    const progressPhotosInput = document.getElementById("progress_photos");
    const sitePhotosPreview = document.getElementById("sitePhotosPreview");
    const progressPhotosPreview = document.getElementById("progressPhotosPreview");
    const latInput = document.getElementById("lat");
    const lngInput = document.getElementById("lng");
    const mapElement = document.getElementById("siteLocationMap");
    const pinText = document.getElementById("siteLocationPinText");
    const submitLabel = form ? form.dataset.submitLabel || "Register Site" : "Register Site";

    if (!form || !nextBtn || !prevBtn) {
        return;
    }

    if (window.jQuery && window.jQuery.fn && window.jQuery.fn.selectpicker) {
        window.jQuery(".selectpicker").selectpicker();
    }

    const totalSteps = pages.length || 6;
    let currentStep = 1;

    const buttonTexts = {
        1: "Next: Contacts",
        2: "Next: Site Details",
        3: "Next: Products",
        4: "Next: Follow-up",
        5: "Next: Photos",
    };

    function getNextIndex(container) {
        const rows = container.querySelectorAll("[data-row-index]");
        let maxIndex = -1;

        rows.forEach(function (row) {
            const rowIndex = parseInt(row.dataset.rowIndex, 10);
            if (!Number.isNaN(rowIndex) && rowIndex > maxIndex) {
                maxIndex = rowIndex;
            }
        });

        return maxIndex + 1;
    }

    function normalizeContactNumber(value) {
        return String(value || "")
            .replace(/\D/g, "")
            .slice(0, 10);
    }

    function updatePinText(lat, lng) {
        if (!pinText) {
            return;
        }

        if (lat !== "" && lng !== "" && lat !== null && lng !== null) {
            pinText.textContent =
                "PIN: " + Number(lat).toFixed(6) + ", " + Number(lng).toFixed(6);
            return;
        }

        pinText.textContent = "";
    }

    function setCoordinates(lat, lng) {
        if (latInput) {
            latInput.value = lat !== "" && lat !== null ? Number(lat).toFixed(7) : "";
        }

        if (lngInput) {
            lngInput.value = lng !== "" && lng !== null ? Number(lng).toFixed(7) : "";
        }

        updatePinText(lat, lng);
    }

    function clearPreview(container) {
        if (container) {
            container.innerHTML = "";
        }
    }

    function renderFilePreview(input, container) {
        if (!input || !container) {
            return;
        }

        clearPreview(container);

        const files = Array.from(input.files || []);
        if (!files.length) {
            return;
        }

        files.forEach(function (file) {
            if (!file.type || !file.type.startsWith("image/")) {
                return;
            }

            const col = document.createElement("div");
            col.className = "col-xs-6 col-md-3 mb-3";

            const card = document.createElement("div");
            card.style.border = "1px solid #e5e7eb";
            card.style.borderRadius = "10px";
            card.style.overflow = "hidden";
            card.style.background = "#fff";

            const img = document.createElement("img");
            img.alt = file.name;
            img.style.width = "100%";
            img.style.height = "110px";
            img.style.objectFit = "cover";

            const label = document.createElement("div");
            label.textContent = file.name;
            label.style.padding = "8px 10px";
            label.style.fontSize = "12px";
            label.style.color = "#475569";
            label.style.whiteSpace = "nowrap";
            label.style.overflow = "hidden";
            label.style.textOverflow = "ellipsis";

            const objectUrl = URL.createObjectURL(file);
            img.src = objectUrl;
            img.onload = function () {
                URL.revokeObjectURL(objectUrl);
            };

            card.appendChild(img);
            card.appendChild(label);
            col.appendChild(card);
            container.appendChild(col);
        });
    }

    function updateStepper(step) {
        currentStep = step;

        pages.forEach(function (page) {
            page.classList.remove("msjk-sitereg-page-active");
        });

        steps.forEach(function (item) {
            item.classList.remove("msjk-sitereg-step-active");
        });

        const activePage = document.getElementById("msjk-sitereg-page-" + step);
        const activeStep = document.querySelector(
            '.msjk-sitereg-step[data-step="' + step + '"]',
        );

        if (activePage) {
            activePage.classList.add("msjk-sitereg-page-active");
        }

        if (activeStep) {
            activeStep.classList.add("msjk-sitereg-step-active");
        }

        prevBtn.style.visibility = step === 1 ? "hidden" : "visible";

        if (step === totalSteps) {
            nextBtn.type = "submit";
            nextBtn.textContent = submitLabel;
            nextBtn.classList.add("msjk-sitereg-submit-btn");
        } else {
            nextBtn.type = "button";
            nextBtn.textContent = buttonTexts[step] || "Next";
            nextBtn.classList.remove("msjk-sitereg-submit-btn");
        }
    }

    function createStakeholderRow(index) {
        return `
            <div class="row stakeholder-row mb-3" data-row-index="${index}">
                <div class="col-md-4">
                    <div class="msjk-sitereg-form-group">
                        <label>Name <span>*</span></label>
                        <input type="text" name="stakeholders[${index}][name]" class="msjk-sitereg-form-control form-control" placeholder="Stakeholder name">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="msjk-sitereg-form-group">
                        <label>Role <span>*</span></label>
                        <input type="text" name="stakeholders[${index}][role]" class="msjk-sitereg-form-control form-control" placeholder="Owner / Architect">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="msjk-sitereg-form-group">
                        <label>Contact Number <span>*</span></label>
                        <input type="text" name="stakeholders[${index}][contact_number]" class="msjk-sitereg-form-control form-control js-contact-number" placeholder="98XXXXXXXX" inputmode="numeric" maxlength="10" pattern="^(98|97)[0-9]{8}$" title="Use a 10 digit number starting with 98 or 97">
                    </div>
                </div>
                <div class="col-md-1 d-flex align-items-end">
                    <button type="button" class="btn btn-link text-danger p-0 remove-stakeholder-btn">Remove</button>
                </div>
            </div>
        `;
    }

    function addStakeholder() {
        const nextIndex = getNextIndex(stakeholderRows);
        stakeholderRows.insertAdjacentHTML(
            "beforeend",
            createStakeholderRow(nextIndex),
        );
    }

    function addProduct() {
        const nextIndex = getNextIndex(productRows);
        if (!productOptionsTemplate) {
            return;
        }

        const options = productOptionsTemplate.innerHTML.trim();

        productRows.insertAdjacentHTML(
            "beforeend",
            `
            <div class="row product-row mb-3" data-row-index="${nextIndex}">
                <div class="col-md-7">
                    <div class="msjk-sitereg-form-group">
                        <label>Product <span>*</span></label>
                        <select name="products[${nextIndex}][id]" class="msjk-sitereg-form-control form-control">
                            <option value="">Select product</option>
                            ${options}
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="msjk-sitereg-form-group">
                        <label>Quantity <span>*</span></label>
                        <input type="number" step="any" name="products[${nextIndex}][quantity]" class="msjk-sitereg-form-control form-control" placeholder="1">
                    </div>
                </div>
                <div class="col-md-1 d-flex align-items-end">
                    <button type="button" class="btn btn-link text-danger p-0 remove-product-btn">Remove</button>
                </div>
            </div>
        `,
        );
    }

    nextBtn.addEventListener("click", function (event) {
        if (currentStep < totalSteps) {
            event.preventDefault();
            updateStepper(currentStep + 1);
        }
    });

    prevBtn.addEventListener("click", function (event) {
        event.preventDefault();
        if (currentStep > 1) {
            updateStepper(currentStep - 1);
        }
    });

    steps.forEach(function (stepItem) {
        stepItem.addEventListener("click", function () {
            const step = parseInt(this.dataset.step, 10);
            if (!Number.isNaN(step)) {
                updateStepper(step);
            }
        });
    });

    if (addStakeholderBtn && stakeholderRows) {
        addStakeholderBtn.addEventListener("click", addStakeholder);

        stakeholderRows.addEventListener("click", function (event) {
            if (event.target.classList.contains("remove-stakeholder-btn")) {
                const row = event.target.closest(".stakeholder-row");
                if (row) {
                    row.remove();
                }
            }
        });
    }

    if (addProductBtn && productRows) {
        addProductBtn.addEventListener("click", addProduct);

        productRows.addEventListener("click", function (event) {
            if (event.target.classList.contains("remove-product-btn")) {
                const row = event.target.closest(".product-row");
                if (row) {
                    row.remove();
                }
            }
        });
    }

    if (useCurrentLocationBtn) {
        useCurrentLocationBtn.addEventListener("click", function () {
            if (!navigator.geolocation) {
                alert("Geolocation is not supported in this browser.");
                return;
            }

            navigator.geolocation.getCurrentPosition(
                function (position) {
                    const { latitude, longitude } = position.coords;
                    const latInput = document.getElementById("lat");
                    const lngInput = document.getElementById("lng");

                    if (latInput) {
                        latInput.value = latitude.toFixed(6);
                    }

                    if (lngInput) {
                        lngInput.value = longitude.toFixed(6);
                    }
                },
                function () {
                    alert("Unable to get your current location.");
                },
            );
        });
    }

    if (form) {
        form.addEventListener("input", function (event) {
            if (event.target && event.target.classList.contains("js-contact-number")) {
                event.target.value = normalizeContactNumber(event.target.value);
            }
        });
    }

    if (sitePhotosInput && sitePhotosPreview) {
        sitePhotosInput.addEventListener("change", function () {
            renderFilePreview(sitePhotosInput, sitePhotosPreview);
        });
    }

    if (progressPhotosInput && progressPhotosPreview) {
        progressPhotosInput.addEventListener("change", function () {
            renderFilePreview(progressPhotosInput, progressPhotosPreview);
        });
    }

    window.SiteRegistrationGoogleMaps = window.SiteRegistrationGoogleMaps || {};
    window.SiteRegistrationGoogleMaps.init = function () {
        if (!mapElement || !window.google || !google.maps) {
            return;
        }

        const defaultLat = parseFloat(mapElement.dataset.defaultLat || "27.7172");
        const defaultLng = parseFloat(mapElement.dataset.defaultLng || "85.3240");
        const currentLat = latInput ? parseFloat(latInput.value) : NaN;
        const currentLng = lngInput ? parseFloat(lngInput.value) : NaN;
        const hasCoordinates = !Number.isNaN(currentLat) && !Number.isNaN(currentLng);
        const initialLat = hasCoordinates ? currentLat : defaultLat;
        const initialLng = hasCoordinates ? currentLng : defaultLng;
        const initialPosition = { lat: initialLat, lng: initialLng };

        const map = new google.maps.Map(mapElement, {
            center: initialPosition,
            zoom: hasCoordinates ? 16 : 13,
            mapTypeControl: false,
            streetViewControl: false,
            fullscreenControl: false,
        });

        const marker = new google.maps.Marker({
            map: map,
            position: initialPosition,
            draggable: true,
        });

        function syncFromPosition(position) {
            const lat = typeof position.lat === "function" ? position.lat() : position.lat;
            const lng = typeof position.lng === "function" ? position.lng() : position.lng;

            marker.setPosition({ lat: lat, lng: lng });
            map.setCenter({ lat: lat, lng: lng });
            setCoordinates(lat, lng);
        }

        map.addListener("click", function (event) {
            syncFromPosition(event.latLng);
        });

        marker.addListener("dragend", function () {
            const position = marker.getPosition();
            setCoordinates(position.lat(), position.lng());
        });

        setCoordinates(initialLat, initialLng);
    };

    if (window.__siteLocationGoogleReady) {
        window.SiteRegistrationGoogleMaps.init();
    }

    updateStepper(1);
});
