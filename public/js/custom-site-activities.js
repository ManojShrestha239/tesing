document.addEventListener("DOMContentLoaded", function () {
    const loadMoreBtn = document.getElementById("load-more-activity");
    const timelineWrapper = document.querySelector(".msjk-timeline-wrapper");
    const siteId = document.getElementById("site-id").value;

    // Get the ID of the last currently displayed log
    let existingLogs = document.querySelectorAll(".msjk-site-timeline-item");
    let lastId = null;

    if (existingLogs.length > 0) {
        // Get the last log's ID for cursor pagination
        const lastLog = existingLogs[existingLogs.length - 1];
        lastId = lastLog.dataset.id;
    }

    let loading = false;

    // Hide button if not present or less than 5 initial logs
    if (!loadMoreBtn || existingLogs.length < 5) {
        if (loadMoreBtn) {
            loadMoreBtn.style.display = "none";
        }
        return;
    }

    loadMoreBtn.addEventListener("click", function () {
        if (loading) return;
        loading = true;

        // Show loading state
        loadMoreBtn.textContent = "Loading...";
        loadMoreBtn.disabled = true;

        // Build URL with cursor parameter
        const url = `/site-registration/${siteId}/activitylog${lastId ? `?last_id=${lastId}` : ""}`;

        fetch(url, {
            method: "GET",
            headers: {
                "Content-Type": "application/json",
                "X-Requested-With": "XMLHttpRequest",
                "X-CSRF-TOKEN": document
                    .querySelector('meta[name="csrf-token"]')
                    ?.getAttribute("content"),
            },
        })
            .then((response) => response.json())
            .then((data) => {
                const logs = data.data;

                if (logs.length === 0) {
                    loadMoreBtn.style.display = "none";
                    return;
                }

                // Generate HTML for new logs
                let html = "";
                logs.forEach((log) => {
                    html += `
                        <div class="msjk-site-timeline-item" data-id="${log.id}">
                            <div class="msjk-site-timeline-left">
                                <div class="msjk-site-timeline-icon">🕧</div>
                            </div>
                            <div class="msjk-site-timeline-card">
                                <div class="msjk-site-timeline-top">
                                    <span class="msjk-site-timeline-date">${timeAgo(new Date(log.created_at))}</span>
                                </div>
                                <h4 class="msjk-site-timeline-title">${escapeHtml(log.event || "")}</h4>
                                <p class="msjk-site-timeline-desc">${escapeHtml(log.description || "")}</p>
                                <div class="msjk-site-timeline-user">
                                    <svg width="20px" height="20px" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg" fill="#000000" style="margin-right: 8px;">
                                        <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                        <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                                        <g id="SVGRepo_iconCarrier">
                                            <title>profile_round [#667085]</title>
                                            <desc>Created with Sketch.</desc>
                                            <defs></defs>
                                            <g id="Page-1" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                <g id="Dribbble-Light-Preview" transform="translate(-140.000000, -2159.000000)" fill="#667085">
                                                    <g id="icons" transform="translate(56.000000, 160.000000)">
                                                        <path d="M100.562548,2016.99998 L87.4381713,2016.99998 C86.7317804,2016.99998 86.2101535,2016.30298 86.4765813,2015.66198 C87.7127655,2012.69798 90.6169306,2010.99998 93.9998492,2010.99998 C97.3837885,2010.99998 100.287954,2012.69798 101.524138,2015.66198 C101.790566,2016.30298 101.268939,2016.99998 100.562548,2016.99998 M89.9166645,2004.99998 C89.9166645,2002.79398 91.7489936,2000.99998 93.9998492,2000.99998 C96.2517256,2000.99998 98.0830339,2002.79398 98.0830339,2004.99998 C98.0830339,2007.20598 96.2517256,2008.99998 93.9998492,2008.99998 C91.7489936,2008.99998 89.9166645,2007.20598 89.9166645,2004.99998 M103.955674,2016.63598 C103.213556,2013.27698 100.892265,2010.79798 97.837022,2009.67298 C99.4560048,2008.39598 100.400241,2006.33098 100.053171,2004.06998 C99.6509769,2001.44698 97.4235996,1999.34798 94.7348224,1999.04198 C91.0232075,1998.61898 87.8750721,2001.44898 87.8750721,2004.99998 C87.8750721,2006.88998 88.7692896,2008.57398 90.1636971,2009.67298 C87.1074334,2010.79798 84.7871636,2013.27698 84.044024,2016.63598 C83.7745338,2017.85698 84.7789973,2018.99998 86.0539717,2018.99998 L101.945727,2018.99998 C103.221722,2018.99998 104.226185,2017.85698 103.955674,2016.63598" id="profile_round-[#667085]"></path>
                                                    </g>
                                                </g>
                                            </g>
                                        </g>
                                    </svg>
                                    <span>Logged by: ${escapeHtml(log.causer?.name || "System")}</span>
                                </div>
                            </div>
                        </div>
                    `;
                });

                // Append new logs to timeline
                timelineWrapper.insertAdjacentHTML("beforeend", html);

                // Update lastId for next request (use the last item's ID)
                lastId = logs[logs.length - 1].id;

                // Hide button if no more data
                if (!data.hasMore) {
                    loadMoreBtn.style.display = "none";
                }

                // Reset button state
                loadMoreBtn.textContent = "Load More";
                loadMoreBtn.disabled = false;
                loading = false;
            })
            .catch((error) => {
                console.error("Error loading activity logs:", error);
                loadMoreBtn.textContent = "Load More";
                loadMoreBtn.disabled = false;
                loading = false;
                alert("Failed to load more activities.");
            });
    });

    // Helper function for time ago
    function timeAgo(date) {
        if (!date) return "";
        const seconds = Math.floor((new Date() - new Date(date)) / 1000);
        let interval = Math.floor(seconds / 31536000);
        if (interval > 1) return interval + " years ago";
        interval = Math.floor(seconds / 2592000);
        if (interval > 1) return interval + " months ago";
        interval = Math.floor(seconds / 86400);
        if (interval > 1) return interval + " days ago";
        interval = Math.floor(seconds / 3600);
        if (interval > 1) return interval + " hours ago";
        interval = Math.floor(seconds / 60);
        if (interval > 1) return interval + " minutes ago";
        return Math.floor(seconds) + " seconds ago";
    }

    // Helper function to escape HTML
    function escapeHtml(text) {
        if (!text) return "";
        const map = {
            "&": "&amp;",
            "<": "&lt;",
            ">": "&gt;",
            '"': "&quot;",
            "'": "&#039;",
        };
        return text.toString().replace(/[&<>"']/g, function (m) {
            return map[m];
        });
    }
});
