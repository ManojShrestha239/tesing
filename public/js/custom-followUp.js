document.addEventListener("DOMContentLoaded", function () {
    const baseUrl = window.location.origin + window.location.pathname;
    let searchTimeout;

    // Initialize active filter from URL
    function setActiveFilterFromUrl() {
        const urlParams = new URLSearchParams(window.location.search);
        const status = urlParams.get("status") || "all";

        document.querySelectorAll(".msjk-filter-btn").forEach((btn) => {
            btn.classList.remove("active");
        });

        if (status === "all" || !status) {
            document
                .querySelector(".msjk-filter-btn:first-child")
                .classList.add("active");
        } else {
            const targetBtn = Array.from(
                document.querySelectorAll(".msjk-filter-btn"),
            ).find((btn) => btn.textContent.toLowerCase().includes(status));
            if (targetBtn) {
                targetBtn.classList.add("active");
            }
        }
    }

    // Fetch data with parameters
    function fetchData(params = {}) {
        const url = new URL(baseUrl);
        Object.keys(params).forEach((key) =>
            url.searchParams.append(key, params[key]),
        );

        fetch(url, {
            headers: {
                "X-Requested-With": "XMLHttpRequest",
            },
        })
            .then((response) => response.json())
            .then((data) => {
                renderTable(data.followUps.data);
                renderPagination(data.followUps);
                renderCounts(data);
                updateUrl(params);
            })
            .catch((error) => console.error("Error fetching data:", error));
    }

    // Update URL without page reload
    function updateUrl(params) {
        const url = new URL(baseUrl);
        Object.keys(params).forEach((key) =>
            url.searchParams.append(key, params[key]),
        );
        window.history.pushState({}, "", url);
    }

    // Render table data
    function renderTable(followUps) {
        const tbody = document.querySelector(".msjk-followup-table tbody");
        tbody.innerHTML = "";

        if (followUps.length === 0) {
            tbody.innerHTML = `<tr><td colspan="6" class="text-center text-muted py-4">No follow-ups found.</td></tr>`;
            return;
        }

        followUps.forEach((followUp) => {
            const row = document.createElement("tr");
            row.className = "msjk-active-row";

            // Generate initials
            // Fallback to followUp.created_by_dashboard?.name if the first one is null/undefined
            const rawName =
                followUp.created_by?.name ??
                followUp.created_by_dashboard?.name;

            const namePartsCreatedBy =
                rawName
                    ?.trim()
                    .split(" ")
                    .filter((part) => part) || [];

            const initialsCreatedBy =
                namePartsCreatedBy.length > 0
                    ? `${namePartsCreatedBy[0][0]}${namePartsCreatedBy[namePartsCreatedBy.length - 1][0]}`.toUpperCase()
                    : "";

            const namePartsAssignedTo =
                followUp.assigned_to?.name
                    ?.trim()
                    .split(" ")
                    .filter((part) => part) || [];
            const initialsAssignedTo =
                namePartsAssignedTo.length > 0
                    ? `${namePartsAssignedTo[0][0]}${namePartsAssignedTo[namePartsAssignedTo.length - 1][0]}`.toUpperCase()
                    : "";

            row.innerHTML = `
                <td><strong>${followUp.site?.name || "N/A"}</strong></td>
                <td>${followUp.follow_up_type?.toUpperCase() || "N/A"}</td>
                <td class="text-muted">${followUp.follow_up_date || "N/A"}</td>
                <td>
                    <span class="msjk-status msjk-status-pending">
                        <span class="msjk-status-dot"></span>
                        ${followUp.status?.toUpperCase() || "Pending"}
                    </span>
                </td>
                 <td>
                    <div class="msjk-user">
                        <span class="msjk-avatar">${initialsAssignedTo}</span>
                        ${followUp.assigned_to?.name || "N/A"}
                    </div>
                </td>
                <td>
                    <div class="msjk-user">
                        <span class="msjk-avatar">${initialsCreatedBy}</span>
                        ${rawName || "N/A"}
                    </div>
                </td>
            `;

            tbody.appendChild(row);
        });
    }

    // Render counts
    function renderCounts(data) {
        // Clear existing counts
        document
            .querySelectorAll(".msjk-filter-count")
            .forEach((el) => el.remove());

        const filterButtons = document.querySelectorAll(".msjk-filter-btn");

        // Today count
        if (data.today > 0) {
            const todayBtn = filterButtons[1]; // Second button (Today)
            todayBtn.insertAdjacentHTML(
                "beforeend",
                `<span class="msjk-filter-count">(${data.today})</span>`,
            );
        }

        // Overdue count
        if (data.overdue > 0) {
            const overdueBtn = filterButtons[2]; // Third button (Overdue)
            overdueBtn.insertAdjacentHTML(
                "beforeend",
                `<span class="msjk-filter-count">(${data.overdue})</span>`,
            );
        }

        // Upcoming count
        if (data.upcoming > 0) {
            const upcomingBtn = filterButtons[3]; // Fourth button (Upcoming)
            upcomingBtn.insertAdjacentHTML(
                "beforeend",
                `<span class="msjk-filter-count">(${data.upcoming})</span>`,
            );
        }

        // Completed count
        if (data.completed > 0) {
            const completedBtn = filterButtons[4]; // Fifth button (Completed)
            completedBtn.insertAdjacentHTML(
                "beforeend",
                `<span class="msjk-filter-count">(${data.completed})</span>`,
            );
        }
    }

    // Render pagination
    function renderPagination(data) {
        const paginationControls = document.querySelector(
            ".pagination-controls .page-numbers",
        );
        if (!paginationControls) return;

        paginationControls.innerHTML = "";

        const currentPage = data.current_page;
        const lastPage = data.last_page;
        const sideLinks = 1;

        // First page
        const firstPageBtn =
            currentPage === 1
                ? `<button class="btn btn-primary active-page-btn" type="button">1</button>`
                : `<button class="btn btn-default page-num-btn" data-page="1">1</button>`;
        paginationControls.insertAdjacentHTML("beforeend", firstPageBtn);

        // Ellipsis before
        if (currentPage > sideLinks + 2) {
            paginationControls.insertAdjacentHTML(
                "beforeend",
                `<span class="pagination-ellipsis">...</span>`,
            );
        }

        // Page range
        for (
            let i = Math.max(2, currentPage - sideLinks);
            i <= Math.min(lastPage - 1, currentPage + sideLinks);
            i++
        ) {
            const pageBtn =
                i === currentPage
                    ? `<button class="btn btn-primary active-page-btn" type="button">${i}</button>`
                    : `<button class="btn btn-default page-num-btn" data-page="${i}">${i}</button>`;
            paginationControls.insertAdjacentHTML("beforeend", pageBtn);
        }

        // Ellipsis after
        if (currentPage < lastPage - sideLinks - 1) {
            paginationControls.insertAdjacentHTML(
                "beforeend",
                `<span class="pagination-ellipsis">...</span>`,
            );
        }

        // Last page
        if (lastPage > 1) {
            const lastPageBtn =
                currentPage === lastPage
                    ? `<button class="btn btn-primary active-page-btn" type="button">${lastPage}</button>`
                    : `<button class="btn btn-default page-num-btn" data-page="${lastPage}">${lastPage}</button>`;
            paginationControls.insertAdjacentHTML("beforeend", lastPageBtn);
        }

        // Navigation buttons
        const prevBtn = document.querySelector(".msjk-nav-btn:first-child");
        const nextBtn = document.querySelector(".msjk-nav-btn:last-child");

        if (prevBtn) {
            prevBtn.disabled = !data.prev_page_url;
            prevBtn.onclick = () => {
                if (data.prev_page_url) {
                    const url = new URL(data.prev_page_url);
                    const page = url.searchParams.get("page");
                    fetchData({ page: page });
                }
            };
        }

        if (nextBtn) {
            nextBtn.disabled = !data.next_page_url;
            nextBtn.onclick = () => {
                if (data.next_page_url) {
                    const url = new URL(data.next_page_url);
                    const page = url.searchParams.get("page");
                    fetchData({ page: page });
                }
            };
        }

        // Page number click handlers
        document.querySelectorAll(".page-num-btn").forEach((btn) => {
            btn.addEventListener("click", function () {
                const page = this.getAttribute("data-page");
                const params = getCurrentParams();
                params.page = page;
                fetchData(params);
            });
        });
    }

    // Get current filter parameters
    function getCurrentParams() {
        const params = {};
        const activeFilter = document.querySelector(".msjk-filter-btn.active");
        const searchInput = document.querySelector(".msjk-search-input");

        if (
            activeFilter &&
            activeFilter !==
                document.querySelector(".msjk-filter-btn:first-child")
        ) {
            const statusText = activeFilter.textContent.toLowerCase().trim();
            if (statusText.includes("today")) params.status = "today";
            else if (statusText.includes("overdue")) params.status = "overdue";
            else if (statusText.includes("upcoming"))
                params.status = "upcoming";
            else if (statusText.includes("completed"))
                params.status = "completed";
        }

        if (searchInput && searchInput.value.trim()) {
            params.search = searchInput.value.trim();
        }

        return params;
    }

    // Filter button click handlers
    document.querySelectorAll(".msjk-filter-btn").forEach((button, index) => {
        button.addEventListener("click", function () {
            // Remove active class from all buttons
            document.querySelectorAll(".msjk-filter-btn").forEach((btn) => {
                btn.classList.remove("active");
            });

            // Add active class to clicked button
            this.classList.add("active");

            // Get status parameter
            let params = {};
            if (index === 0) {
                // All Active - no status parameter
            } else {
                const statusText = this.textContent.toLowerCase();
                if (statusText.includes("today")) params.status = "today";
                else if (statusText.includes("overdue"))
                    params.status = "overdue";
                else if (statusText.includes("upcoming"))
                    params.status = "upcoming";
                else if (statusText.includes("completed"))
                    params.status = "completed";
            }

            // Include search term if exists
            const searchInput = document.querySelector(".msjk-search-input");
            if (searchInput && searchInput.value.trim()) {
                params.search = searchInput.value.trim();
            }

            fetchData(params);
        });
    });

    // Search input handler with debounce
    const searchInput = document.querySelector(".msjk-search-input");
    if (searchInput) {
        searchInput.addEventListener("input", function () {
            clearTimeout(searchTimeout);
            const searchTerm = this.value.trim();

            searchTimeout = setTimeout(() => {
                const params = getCurrentParams();
                if (searchTerm) {
                    params.search = searchTerm;
                } else {
                    delete params.search;
                }
                fetchData(params);
            }, 500); // 500ms debounce
        });
    }

    // Initialize active filter on page load
    setActiveFilterFromUrl();

    // Handle browser back/forward buttons
    window.addEventListener("popstate", function (event) {
        const urlParams = new URLSearchParams(window.location.search);
        const status = urlParams.get("status") || "all";
        const search = urlParams.get("search") || "";

        // Update UI based on URL
        setActiveFilterFromUrl();

        if (searchInput) {
            searchInput.value = search;
        }

        // Fetch data based on current URL parameters
        const params = {};
        if (status !== "all") params.status = status;
        if (search) params.search = search;

        fetchData(params);
    });

    // Initial load with current URL parameters
    const urlParams = new URLSearchParams(window.location.search);
    const initialParams = {};
    for (let [key, value] of urlParams) {
        initialParams[key] = value;
    }
    fetchData(initialParams);
});
