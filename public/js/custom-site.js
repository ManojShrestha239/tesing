/**
 * Site Registration — filter + AJAX pagination.
 *
 * Replaces full-page form submits with in-place table updates.
 * Requires:
 *   - form id="siteFiltersForm"
 *   - table class="msjk-sites-table"
 *   - container class="pagination-controls"
 *   - span class="pagination-status" (optional, for status text)
 *   - meta[name="csrf-token"]
 *
 * Features:
 *   - Aborts stale requests (rapid typing/filter changes)
 *   - XSS-safe rendering
 *   - Skeleton loading state
 *   - URL sync (back/forward, refresh preserve filters)
 *   - Session/CSRF expiry handling
 *   - Scroll-to-top on page change
 */
(function () {
    "use strict";

    document.addEventListener("DOMContentLoaded", init);

    function init() {
        const form = document.getElementById("siteFiltersForm");
        const tableBody = document.querySelector(".msjk-sites-table tbody");
        const paginationBox = document.querySelector(".pagination-controls");
        const paginationInfo = document.querySelector(".pagination-status");

        if (!form || !tableBody) return;

        const baseUrl =
            form.dataset.baseUrl ||
            form.getAttribute("action") ||
            window.location.pathname;
        const csrfToken =
            document.querySelector('meta[name="csrf-token"]')?.content || "";

        /** Currently in-flight request — cancelled when a new one starts. */
        let inflight = null;

        /* ==========================================================
         |  Helpers
         * ========================================================== */
        function escapeHtml(value) {
            if (value === null || value === undefined) return "";
            return String(value)
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/"/g, "&quot;")
                .replace(/'/g, "&#39;");
        }

        function debounce(fn, wait) {
            let t;
            return function (...args) {
                clearTimeout(t);
                t = setTimeout(() => fn.apply(this, args), wait);
            };
        }

        function buildParams(page) {
            const params = new URLSearchParams();
            const data = new FormData(form);

            data.forEach((value, key) => {
                if (value !== null && value !== "") {
                    params.append(key, value);
                }
            });

            if (page && page > 1) {
                params.set("page", page);
            }

            return params;
        }

        /* ==========================================================
         |  Loading / message states
         * ========================================================== */
        function showSkeleton() {
            const cols =
                document.querySelectorAll(".msjk-sites-table thead th")
                    .length || 9;
            tableBody.innerHTML = Array.from({ length: 5 })
                .map(
                    () => `
                    <tr class="msjk-skeleton-row">
                        ${Array.from({ length: cols })
                            .map(
                                () =>
                                    '<td><span class="msjk-skeleton-bar"></span></td>',
                            )
                            .join("")}
                    </tr>
                `,
                )
                .join("");
        }

        function showMessage(text) {
            const cols =
                document.querySelectorAll(".msjk-sites-table thead th")
                    .length || 9;
            tableBody.innerHTML = `<tr><td colspan="${cols}" class="text-center">${escapeHtml(text)}</td></tr>`;
        }

        /* ==========================================================
         |  Row rendering
         * ========================================================== */
        function renderRows(sites) {
            if (!sites || sites.length === 0) {
                showMessage("No sites found.");
                return;
            }
            tableBody.innerHTML = sites.map(renderSite).join("");
        }

        function renderSite(site) {
            const products =
                Array.isArray(site.products) && site.products.length > 0
                    ? site.products
                    : [null];
            const rowspan = products.length;
            const photoPath =
                site.photos?.[0]?.file_path || "/images/default-site.png";
            const showUrl = `/site-registration-v2/${encodeURIComponent(site.id)}`;
            const dealerName = site.dealer?.name || "-";
            const dealerCode = site.dealer?.code || "-";
            const siteType = escapeHtml(
                relationName(site.siteType ?? site.site_type),
            );
            const stageName = escapeHtml(
                relationName(site.currentStage ?? site.current_stage),
            );
            const statusText = site.status
                ? site.status
                      .replace(/_/g, " ")
                      .replace(/\b\w/g, (c) => c.toUpperCase())
                : "-";

            return products
                .map((product, index) => {
                    const first = index === 0;

                    const productCell = product
                        ? `${escapeHtml(product.name || "-")} <small>(${escapeHtml(product.types || "-")})</small>`
                        : "-";
                    const qtyCell = product?.pivot?.quantity ?? "-";

                    return `
                    <tr>
                        ${
                            first
                                ? `
                            <td class="msjk-site-reg" rowspan="${rowspan}">${escapeHtml(site.site_registration_no || "-")}</td>
                            <td rowspan="${rowspan}">
                                <a href="${showUrl}" style="display:block;color:inherit;text-decoration:none;">
                                    <div class="msjk-site-info">
                                        <div class="msjk-site-thumb">
                                            <img src="${escapeHtml(photoPath)}"
                                                alt=""
                                                onerror="this.onerror=null; this.src='/images/default-site.png';"/>
                                        </div>
                                        <div>
                                            <div class="msjk-site-name">${escapeHtml(site.name || "-")}</div>
                                        </div>
                                    </div>
                                </a>
                            </td>
                            <td rowspan="${rowspan}">${siteType}</td>
                            <td rowspan="${rowspan}">
                                <span class="msjk-stage-dot"></span>${stageName}
                            </td>
                            <td rowspan="${rowspan}">
                                <div>${escapeHtml(dealerName)}</div>
                                <small>(${escapeHtml(dealerCode)})</small>
                            </td>
                        `
                                : ""
                        }
                        <td>${productCell}</td>
                        <td>${escapeHtml(qtyCell)}</td>
                        ${
                            first
                                ? `
                            <td rowspan="${rowspan}">${escapeHtml(site.next_followup_at || "-")}</td>
                            <td rowspan="${rowspan}">
                                <span class="msjk-status-badge msjk-status-${escapeHtml(site.status || "unknown")}">${statusText}</span>
                            </td>
                        `
                                : ""
                        }
                    </tr>
                `;
                })
                .join("");
        }

        /* ==========================================================
         |  Pagination rendering — full rebuild each render
         * ========================================================== */
        function buildPageList(current, last) {
            if (last <= 7) {
                return Array.from({ length: last }, (_, i) => i + 1);
            }

            const pages = [1];

            if (current > 4) pages.push("…");

            const start = Math.max(2, current - 1);
            const end = Math.min(last - 1, current + 1);

            for (let p = start; p <= end; p++) pages.push(p);

            if (current < last - 3) pages.push("…");

            pages.push(last);

            return pages;
        }

        function renderPagination(meta, links) {
            if (!paginationBox) return;

            // ---- Status text ----
            if (paginationInfo) {
                paginationInfo.textContent =
                    (meta.total ?? 0) === 0
                        ? "No entries"
                        : `Showing ${meta.from}-${meta.to} of ${meta.total} entries`;
            }

            const current = Number(meta.current_page) || 1;
            const last = Number(meta.last_page) || 1;
            const pages = buildPageList(current, last);

            const prevDisabled = !links.prev;
            const nextDisabled = !links.next;

            // ---- Build entire pagination HTML ----
            let html = `
                <a href="#"
                   class="btn btn-default msjk-nav-btn${prevDisabled ? " disabled" : ""}"
                   data-page="${Math.max(1, current - 1)}"
                   data-disabled="${prevDisabled ? "1" : "0"}"
                   aria-disabled="${prevDisabled ? "true" : "false"}"
                   data-nav="prev">
                    <span class="glyphicon glyphicon-chevron-left"></span>
                </a>
            `;

            html += '<div class="page-numbers">';
            pages.forEach((item) => {
                if (item === "…") {
                    html += `<span class="pagination-ellipsis" style="padding:6px 12px;color:#6c757d;">…</span>`;
                    return;
                }
                const active =
                    item === current ? " btn-primary active" : " btn-default";
                html += `<a href="#"
                           class="btn page-num-btn${active}"
                           data-page="${item}"
                           data-disabled="0">${item}</a>`;
            });
            html += "</div>";

            html += `
                <a href="#"
                   class="btn btn-default msjk-nav-btn${nextDisabled ? " disabled" : ""}"
                   data-page="${Math.min(last, current + 1)}"
                   data-disabled="${nextDisabled ? "1" : "0"}"
                   aria-disabled="${nextDisabled ? "true" : "false"}"
                   data-nav="next">
                    <span class="glyphicon glyphicon-chevron-right"></span>
                </a>
            `;

            // ---- Swap the whole container ----
            paginationBox.innerHTML = html;

            // ---- Single delegated handler ----
            paginationBox.querySelectorAll("[data-page]").forEach((el) => {
                el.addEventListener("click", (e) => {
                    e.preventDefault();
                    if (el.dataset.disabled === "1") return;
                    const page = parseInt(el.dataset.page, 10);
                    if (!isNaN(page)) loadPage(page, { scroll: true });
                });
            });
        }

        /* ==========================================================
         |  Initial pagination binding (first page load)
         * ========================================================== */
        function attachInitialPagination() {
            if (!paginationBox) return;

            const navs = paginationBox.querySelectorAll(".msjk-nav-btn");
            const prev = navs[0] ?? null;
            const next = navs[navs.length - 1] ?? null;

            if (prev) prev.dataset.nav = "prev";
            if (next && next !== prev) next.dataset.nav = "next";

            // Page number buttons
            paginationBox.querySelectorAll(".page-num-btn").forEach((a) => {
                const text = (a.textContent || "").trim();
                if (!/^\d+$/.test(text)) return;

                a.addEventListener("click", (e) => {
                    e.preventDefault();
                    const page = parseInt(text, 10);
                    if (!isNaN(page)) loadPage(page, { scroll: true });
                });
            });

            // Prev / Next
            if (prev && !prev.classList.contains("disabled")) {
                prev.addEventListener("click", (e) => {
                    e.preventDefault();
                    loadPage(Math.max(1, readCurrentPage() - 1), {
                        scroll: true,
                    });
                });
            }
            if (next && next !== prev && !next.classList.contains("disabled")) {
                next.addEventListener("click", (e) => {
                    e.preventDefault();
                    loadPage(readCurrentPage() + 1, { scroll: true });
                });
            }
        }

        function readCurrentPage() {
            const active = paginationBox?.querySelector(".page-num-btn.active");
            const n = parseInt(active?.textContent ?? "1", 10);
            return isNaN(n) ? 1 : n;
        }

        /* ==========================================================
         |  Network
         * ========================================================== */
        async function loadPage(page = 1, options = {}) {
            const { scroll = false } = options;

            if (inflight) inflight.abort();
            inflight = new AbortController();

            showSkeleton();

            const params = buildParams(page);
            const url = `${baseUrl}?${params.toString()}`;

            try {
                const response = await fetch(url, {
                    method: "GET",
                    headers: {
                        "X-Requested-With": "XMLHttpRequest",
                        "X-CSRF-TOKEN": csrfToken,
                        Accept: "application/json",
                    },
                    signal: inflight.signal,
                });

                if (response.status === 419 || response.status === 401) {
                    window.location.reload();
                    return;
                }

                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}`);
                }

                const payload = await response.json();

                renderRows(payload.data || []);
                renderPagination(payload.meta || {}, payload.links || {});
                syncUrl(params);

                if (scroll) {
                    document
                        .querySelector(".msjk-sites-table-section")
                        ?.scrollIntoView({
                            behavior: "smooth",
                            block: "start",
                        });
                }
            } catch (err) {
                if (err.name === "AbortError") return;
                console.error("Filter request failed:", err);
                showMessage("Failed to load data. Please try again.");
            } finally {
                inflight = null;
            }
        }

        function syncUrl(params) {
            if (!window.history?.replaceState) return;
            const qs = params.toString();
            const next = qs ? `${baseUrl}?${qs}` : baseUrl;
            window.history.replaceState(null, "", next);
        }

        /* ==========================================================
         |  Filter bindings
         * ========================================================== */
        const debouncedSearch = debounce(() => loadPage(1), 400);

        // Selects — instant
        form.querySelectorAll("select").forEach((select) => {
            select.addEventListener("change", () => loadPage(1));
        });

        // Text/search inputs — debounced
        form.querySelectorAll(
            'input[type="text"], input[type="search"]',
        ).forEach((input) => {
            input.addEventListener("input", debouncedSearch);
            input.addEventListener("keydown", (e) => {
                if (e.key === "Enter") {
                    e.preventDefault();
                    loadPage(1);
                }
            });
        });

        // Prevent native form submit — AJAX handles it
        form.addEventListener("submit", (e) => {
            e.preventDefault();
            loadPage(1);
        });

        // Reset — hard reload without filters
        form.querySelector(".msjk-filter-reset")?.addEventListener(
            "click",
            (e) => {
                e.preventDefault();
                window.location.href = baseUrl;
            },
        );

        /* ==========================================================
         |  Initial state — bind to server-rendered controls
         * ========================================================== */
        attachInitialPagination();
    }

    /**
     * Resolve a related model's display name from either a nested object
     * (camelCase or snake_case key) or a raw string.
     */
    function relationName(value) {
        if (value === null || value === undefined) return "-";
        if (typeof value === "string") return value;
        if (typeof value === "object") return value.name ?? "-";
        return "-";
    }
})();
