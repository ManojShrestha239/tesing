@extends('dashboards.sessions.layout')
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="stylesheet" href="{{ asset('css/custom-site-registration.css') }}">
@section('content')
    <div class="page-content">
        <div class="breadcrumbs">
            <h1>Site Registration</h1>
        </div>

        <div class="page-content-container">
            <div class="page-content-row" id="replecable">
                <div class="page-sidebar">
                    <div class="portlet light portlet-fit">
                        <div class="portlet-title">
                            <div class="caption">
                                <i class=" icon-bar font-red"></i>
                                <span class="caption-subject font-red bold uppercase">Navigation</span>
                            </div>

                        </div>
                        <nav class="navbar" role="navigation">
                            <!-- Brand and toggle get grouped for better mobile display -->
                            <!-- Collect the nav links, forms, and other content for toggling -->
                            @include('dashboards.dynamicNav.siteRegistration')
                        </nav>
                    </div>
                </div>

                <div class="page-content-col">
                    @if (session('Status'))
                        <div class="m-heading-1 border-green m-bordered">
                            <div class="page-content-col">
                                <div class="alert alert-success alert-dismissible" role="alert">
                                    <b>{{ session('Status') }}</b>
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Main Content Here --}}
                    <div class="msjk-map-wrapper">
                        <!-- Google Maps Container -->
                        <div id="map" style="width: 100%; height: 100%;"></div>

                        <!-- Discovery Section with Toggle -->
                        <div id="discovery-section" class="msjk-site-discovery">
                            <div class="msjk-discovery-header">
                                <h2 class="msjk-discovery-title">Site Discovery</h2>
                                <button id="toggle-discovery" class="msjk-toggle-btn" title="Hide Discovery">←</button>
                            </div>

                            <div class="msjk-discovery-content">
                                <div class="msjk-discovery-search">
                                    <input type="text" id="search-input" placeholder="Search by name, location..."
                                        value="{{ $search ?? '' }}" />
                                </div>

                                <div class="msjk-discovery-filters">
                                    <button
                                        class="msjk-filter-btn {{ !$activeStage || $activeStage === 'All Sites' ? 'active' : '' }}"
                                        data-stage="All Sites">All Sites</button>
                                    @if (!empty($stages))
                                        @foreach ($stages as $stage)
                                            <button
                                                class="msjk-filter-btn {{ $activeStage === $stage->name ? 'active' : '' }}"
                                                data-stage="{{ $stage->id }}">{{ strtoupper($stage->name) }}</button>
                                        @endforeach
                                    @endif
                                </div>

                                <div class="msjk-site-list" id="site-list-container">
                                    @include('dashboards.new-site.map-partials.site-discovery', [
                                        'data' => $data,
                                    ])
                                </div>

                                <!-- Loading indicator -->
                                <div id="loading-more" class="text-center" style="display: none; padding: 10px;">
                                    <div class="spinner-border" role="status">
                                        <span class="sr-only">Loading...</span>
                                    </div>
                                </div>

                                <!-- No more data message -->
                                <div id="no-more-data" class="text-center"
                                    style="display: none; padding: 10px; color: #6c757d;">
                                    No more sites to load
                                </div>
                            </div>
                        </div>

                        <!-- Project Card - Hidden by default -->
                        <div id="project-card" class="msjk-map-project-card" style="display: none;">
                            <div class="msjk-card-header">
                                <button id="close-project-card" class="msjk-close-btn" title="Close">×</button>
                            </div>

                            <div class="msjk-map-project-image">
                                <img id="project-image" src="{{ asset('images/default-site.jpg') }}" alt="Project" />
                                {{-- <span class="msjk-match-badge">85% Match</span> --}}
                            </div>

                            <div class="msjk-map-project-body">
                                <div class="msjk-map-project-header">
                                    <h4 id="project-name">Project Name</h4>
                                    {{-- <button class="msjk-share-btn">↗</button> --}}
                                </div>

                                {{-- <p class="msjk-map-location" id="project-location">Location</p> --}}

                                <div class="msjk-map-stage-box">
                                    <div class="clearfix">
                                        <span class="pull-left" id="project-stage">Plastering Stage</span>
                                        {{-- <span class="pull-right">In 5 Days</span> --}}
                                    </div>

                                    {{-- <div class="msjk-stage-progress">
                                        <div class="msjk-stage-progress-fill"></div>
                                    </div>

                                    <div class="msjk-stage-steps">
                                        <span>Foundation</span>
                                        <span>Structure</span>
                                        <span class="active">Plastering</span>
                                        <span>Putty</span>
                                    </div> --}}
                                </div>

                                <div class="row msjk-project-footer">
                                    <div class="col-xs-6">
                                        <small>Estimated Paint</small>
                                        <h4 id="project-demand">0 Ltrs</h4>
                                    </div>
                                    <div class="col-xs-6 text-right">
                                        <small>Next Follow-up</small>
                                        <h4 id="project-followup">Date</h4>
                                    </div>
                                </div>

                                <div class="row" style="margin-top: 15px">
                                    {{-- <div class="col-xs-6">
                                        <button class="btn btn-default btn-block">Navigate</button>
                                    </div> --}}
                                    <div class="col-xs-6">
                                        <button class="btn btn-primary btn-block">View Details</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- End Main Content Here --}}

                </div>
            </div>
        </div>

        <!-- END SIDEBAR CONTENT LAYOUT -->
    </div>

    <script>
        let map;
        let markers = []; // Array to store marker objects with site IDs
        let activeMarkerObj = null; // Track the currently highlighted marker
        let isDiscoveryVisible = true;
        let currentSearch = '{{ $search ?? '' }}';
        let currentStage = '{{ $activeStage ?? 'All Sites' }}';
        let lastId = {{ $lastId ?? 'null' }};
        let hasMore = {{ $hasMore ? 'true' : 'false' }};
        let isLoading = false;
        let siteDataMap = new Map(); // Store site data for quick lookup

        // Site type icons mapping
        const siteTypeIcons = {
            'Residential': '🏠',
            'Commercial': '🏢',
            'Apartment': '🏘️',
            'Renovation': '🔨',
            'default': '📍'
        };

        // Initialize the map and load initial data
        function initMap() {
            // Map options
            map = new google.maps.Map(document.getElementById("map"), {
                zoom: 12,
                center: {
                    lat: 27.7172,
                    lng: 85.3240
                }, // Center of Nepal
            });

            // Initialize with data from Laravel
            initializeSiteData();

            // Initialize event listeners
            initializeEventListeners();

            // Setup infinite scroll
            setupInfiniteScroll();
        }

        // Initialize site data from Laravel data
        function initializeSiteData() {
            const sites = @json($data);

            // Add markers for each site and store data
            sites.forEach((site, index) => {
                // Store site data for quick access
                siteDataMap.set(site.id, site);

                // Create marker for the site
                createSiteMarker(site, index);
            });

            // Add click listeners to initial site items
            addSiteItemClickListeners();
        }

        // Function to get icon based on site type
        function getSiteIcon(site) {
            // Try to get from site type name
            if (site.siteType && site.siteType.name) {
                const siteTypeName = site.siteType.name;
                if (siteTypeIcons[siteTypeName]) {
                    return siteTypeIcons[siteTypeName];
                }
            }

            // Fallback to default alternating icons
            return site.id % 2 === 0 ? '🏢' : '🏠';
        }

        // Function to create marker for a site
        function createSiteMarker(site, index) {
            // Skip if lat/lng not available
            if (!site.lat || !site.lng) return;

            const markerIcon = getSiteIcon(site);

            const marker = new google.maps.Marker({
                position: {
                    lat: parseFloat(site.lat),
                    lng: parseFloat(site.lng)
                },
                map: map,
                title: site.name,
                icon: {
                    url: getMarkerIcon(markerIcon),
                    scaledSize: new google.maps.Size(30, 30)
                }
            });

            // Store marker reference with site ID
            markers.push({
                siteId: site.id,
                marker: marker,
                site: site
            });

            // Add click event to marker
            marker.addListener("click", () => {
                showProjectCard(site);
                highlightSiteItem(site.id);
                focusMapOnSite(site.id); // Focus and highlight marker
            });
        }

        // Function to create marker icon from emoji
        function getMarkerIcon(emoji) {
            return `data:image/svg+xml;charset=UTF-8,<svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24"><text x="12" y="18" font-size="20" text-anchor="middle">${emoji}</text></svg>`;
        }

        // Function to center map on selected site and highlight its marker
        function focusMapOnSite(siteId) {
            // Find the marker object corresponding to this siteId
            const targetMarkerObj = markers.find(m => m.siteId === siteId);

            if (targetMarkerObj && targetMarkerObj.marker) {
                // 1. Reset the previous active marker back to its original state
                if (activeMarkerObj && activeMarkerObj.marker) {
                    // Determine its original icon to restore
                    const originalSite = activeMarkerObj.site;
                    const originalEmoji = getSiteIcon(originalSite);
                    activeMarkerObj.marker.setIcon({
                        url: getMarkerIcon(originalEmoji),
                        scaledSize: new google.maps.Size(30, 30)
                    });
                    activeMarkerObj.marker.setZIndex(1); // Reset depth layering
                }

                // 2. Set the new active marker object
                activeMarkerObj = targetMarkerObj;

                // 3. Update the selected marker with a unique icon (📍) and make it appear on top
                activeMarkerObj.marker.setIcon({
                    url: getMarkerIcon('📍'), // Unique marker icon for selection
                    scaledSize: new google.maps.Size(40, 40) // Slightly larger to stand out
                });
                activeMarkerObj.marker.setZIndex(google.maps.Marker.MAX_ZINDEX + 1);

                // 4. Zoom and Pan smoothly to the site's location
                const position = activeMarkerObj.marker.getPosition();
                map.panTo(position);
                map.setZoom(16); // High zoom level for a detailed site view
            }
        }

        // Function to show project card with site data
        function showProjectCard(site) {
            // Set up a placeholder image URL in case a path is broken
            const fallbackImage = 'https://via.placeholder.com/400x300?text=No+Image+Available';
            let targetPhotoUrl = fallbackImage;

            // Drill down carefully into the verified JSON structure
            if (site && site.photos && Array.isArray(site.photos) && site.photos.length > 0) {
                if (site.photos[0] && site.photos[0].file_path) {
                    targetPhotoUrl = site.photos[0].file_path;
                }
            }

            // Safely apply to your DOM Element
            const imgEl = document.getElementById('project-image');
            if (imgEl) {
                imgEl.src = targetPhotoUrl;
            } else {
                console.error("DOM element with ID 'project-image' was not found on this page.");
            }

            document.getElementById('project-name').textContent = site.name;
            document.getElementById('project-stage').textContent = site.currentStage?.name || 'Unknown Stage';
            document.getElementById('project-demand').textContent =
                `${(site.estimated_paint_litres * 1 || 0).toLocaleString()} Ltrs`;
            document.getElementById('project-followup').textContent = site.next_followup_at || 'N/A';
            document.getElementById('project-card').style.display = 'block';

            // Set up View Details button with route
            const viewDetailsBtn = document.querySelector('#project-card .btn-primary');
            if (viewDetailsBtn) {
                viewDetailsBtn.onclick = function() {
                    window.location.href = `{{ url('site-registration-v2') }}/${site.id}`;
                    // Alternative if you have a named route:
                    // window.location.href = `{{ route('site-registration-v2.show', '') }}/${site.id}`;
                };
            }
        }

        // Function to hide project card
        function hideProjectCard() {
            document.getElementById('project-card').style.display = 'none';
        }

        // Function to toggle discovery section
        function toggleDiscovery() {
            const discoverySection = document.getElementById('discovery-section');
            const toggleBtn = document.getElementById('toggle-discovery');

            if (isDiscoveryVisible) {
                discoverySection.style.width = '50px';
                discoverySection.querySelector('.msjk-discovery-content').style.display = 'none';
                discoverySection.querySelector('.msjk-discovery-title').style.display = 'none';
                toggleBtn.textContent = '→';
                toggleBtn.title = 'Show Discovery';
            } else {
                discoverySection.style.width = '350px';
                discoverySection.querySelector('.msjk-discovery-content').style.display = 'block';
                discoverySection.querySelector('.msjk-discovery-title').style.display = 'block';
                toggleBtn.textContent = '←';
                toggleBtn.title = 'Hide Discovery';
            }

            isDiscoveryVisible = !isDiscoveryVisible;
        }

        // Function to highlight selected site item
        function highlightSiteItem(siteId) {
            // Remove active class from all items
            document.querySelectorAll('.msjk-site-item').forEach(item => {
                item.classList.remove('active');
            });

            // Add active class to selected item
            const selectedItem = document.querySelector(`.msjk-site-item[data-site-id="${siteId}"]`);
            if (selectedItem) {
                selectedItem.classList.add('active');
                selectedItem.scrollIntoView({
                    behavior: 'smooth',
                    block: 'nearest'
                });
            }
        }

        // Add click listeners to site items
        function addSiteItemClickListeners() {
            // Get all site items that don't have listeners yet
            const siteItems = document.querySelectorAll('.msjk-site-item:not([data-listener-attached])');

            siteItems.forEach(item => {
                // Add click event
                item.addEventListener('click', function(e) {
                    // Prevent event bubbling
                    e.stopPropagation();

                    const siteId = parseInt(this.dataset.siteId);

                    // Try to get data from data attribute first (for dynamically loaded items)
                    const siteDataJson = this.dataset.siteData;
                    if (siteDataJson) {
                        try {
                            const siteData = JSON.parse(siteDataJson.replace(/&quot;/g, '"'));
                            // Store in map for future use
                            siteDataMap.set(siteId, siteData);
                            showProjectCard(siteData);
                            highlightSiteItem(siteId);
                            focusMapOnSite(siteId); // Focus and highlight marker
                            return;
                        } catch (error) {
                            console.error('Error parsing site data from attribute:', error);
                        }
                    }

                    // Fallback to map lookup
                    const siteData = siteDataMap.get(siteId);
                    if (siteData) {
                        showProjectCard(siteData);
                        highlightSiteItem(siteId);
                        focusMapOnSite(siteId); // Focus and highlight marker
                    } else {
                        console.log('Site data not found for ID:', siteId);
                    }
                });

                // Mark as having listener to prevent duplicate listeners
                item.setAttribute('data-listener-attached', 'true');
            });
        }

        // Function to fetch filtered data via AJAX
        async function fetchData(search = '', stage = 'All Sites', cursor = null, append = false) {
            try {
                isLoading = true;

                const params = new URLSearchParams();
                if (search) params.append('search', search);
                if (stage && stage !== 'All Sites') params.append('stage', stage);
                if (cursor) params.append('cursor', cursor);

                const response = await fetch(`{{ route('site.registration.v2.map') }}?${params.toString()}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });

                const result = await response.json();

                if (append) {
                    // Append to existing list
                    document.querySelector('#site-list-container').insertAdjacentHTML('beforeend', result.html);

                    // Parse the new HTML to extract site data and create markers
                    const tempDiv = document.createElement('div');
                    tempDiv.innerHTML = result.html;
                    const newSiteItems = tempDiv.querySelectorAll('.msjk-site-item');

                    newSiteItems.forEach((item, index) => {
                        const siteDataJson = item.dataset.siteData;
                        if (siteDataJson) {
                            try {
                                const siteData = JSON.parse(siteDataJson.replace(/&quot;/g, '"'));
                                const siteId = siteData.id;

                                // Store in map
                                siteDataMap.set(siteId, siteData);

                                // Create marker if not already exists
                                if (!markers.some(m => m.siteId === siteId)) {
                                    createSiteMarker(siteData, markers.length + index);
                                }
                            } catch (error) {
                                console.error('Error parsing site data:', error);
                            }
                        }
                    });
                } else {
                    // Replace entire list
                    document.querySelector('#site-list-container').innerHTML = result.html;

                    // Clear the data map and markers when doing a fresh search/filter
                    siteDataMap.clear();

                    // Remove all existing markers
                    markers.forEach(markerObj => {
                        markerObj.marker.setMap(null);
                    });
                    markers = [];

                    // Parse the new HTML and create markers
                    const tempDiv = document.createElement('div');
                    tempDiv.innerHTML = result.html;
                    const newSiteItems = tempDiv.querySelectorAll('.msjk-site-item');

                    newSiteItems.forEach((item, index) => {
                        const siteDataJson = item.dataset.siteData;
                        if (siteDataJson) {
                            try {
                                const siteData = JSON.parse(siteDataJson.replace(/&quot;/g, '"'));
                                const siteId = siteData.id;

                                // Store in map
                                siteDataMap.set(siteId, siteData);

                                // Create marker
                                createSiteMarker(siteData, index);
                            } catch (error) {
                                console.error('Error parsing site data:', error);
                            }
                        }
                    });
                }

                // Update pagination variables
                hasMore = result.hasMore;
                lastId = result.lastId;

                // Update filter buttons active state
                updateFilterButtons(stage);

                // Add click listeners to new items
                addSiteItemClickListeners();

                // Hide loading indicator
                document.getElementById('loading-more').style.display = 'none';
                document.getElementById('no-more-data').style.display = hasMore ? 'none' : 'block';

                return result;

            } catch (error) {
                console.error('Error fetching data:', error);
                document.getElementById('loading-more').style.display = 'none';
                return null;
            } finally {
                isLoading = false;
            }
        }

        // Setup infinite scroll
        function setupInfiniteScroll() {
            const siteListContainer = document.querySelector('.msjk-site-list');

            if (siteListContainer) {
                siteListContainer.addEventListener('scroll', function() {
                    if (isLoading || !hasMore) return;

                    const {
                        scrollTop,
                        scrollHeight,
                        clientHeight
                    } = this;

                    // Check if we're near the bottom (within 100px)
                    if (scrollTop + clientHeight >= scrollHeight - 100) {
                        loadMoreItems();
                    }
                });
            }
        }

        // Load more items
        async function loadMoreItems() {
            if (isLoading || !hasMore || !lastId) return;

            isLoading = true;
            document.getElementById('loading-more').style.display = 'block';
            document.getElementById('no-more-data').style.display = 'none';

            try {
                await fetchData(currentSearch, currentStage, lastId, true);
            } catch (error) {
                console.error('Error loading more items:', error);
            } finally {
                isLoading = false;
                document.getElementById('loading-more').style.display = 'none';
                document.getElementById('no-more-data').style.display = hasMore ? 'none' : 'block';
            }
        }

        // Update filter buttons active state
        function updateFilterButtons(activeStage) {
            document.querySelectorAll('.msjk-filter-btn').forEach(button => {
                if (button.dataset.stage === activeStage) {
                    button.classList.add('active');
                } else {
                    button.classList.remove('active');
                }
            });
        }

        // Initialize event listeners
        function initializeEventListeners() {
            // Toggle discovery button
            document.getElementById('toggle-discovery').addEventListener('click', toggleDiscovery);

            // Close project card button
            document.getElementById('close-project-card').addEventListener('click', hideProjectCard);

            // Search input with debounce
            let searchTimeout;
            const searchInput = document.getElementById('search-input');
            if (searchInput) {
                searchInput.addEventListener('input', function(e) {
                    clearTimeout(searchTimeout);
                    searchTimeout = setTimeout(() => {
                        const searchValue = e.target.value;
                        currentSearch = searchValue;
                        const activeFilter = document.querySelector('.msjk-filter-btn.active')?.dataset
                            .stage || 'All Sites';
                        currentStage = activeFilter;
                        fetchData(searchValue, activeFilter, null, false);
                    }, 300); // 300ms debounce
                });
            }

            // Filter buttons
            document.querySelectorAll('.msjk-filter-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const stage = this.dataset.stage;
                    currentStage = stage;
                    const searchValue = document.getElementById('search-input')?.value || '';
                    currentSearch = searchValue;
                    fetchData(searchValue, stage, null, false);
                });
            });

            // Hide project card when clicking on map
            google.maps.event.addListener(map, 'click', function() {
                hideProjectCard();

                // Reset active marker icon if one exists
                if (activeMarkerObj && activeMarkerObj.marker) {
                    const originalSite = activeMarkerObj.site;
                    const originalEmoji = getSiteIcon(originalSite);
                    activeMarkerObj.marker.setIcon({
                        url: getMarkerIcon(originalEmoji),
                        scaledSize: new google.maps.Size(30, 30)
                    });
                    activeMarkerObj.marker.setZIndex(1);
                    activeMarkerObj = null; // Clear active reference
                }

                // Remove active class from all site items
                document.querySelectorAll('.msjk-site-item').forEach(item => {
                    item.classList.remove('active');
                });
            });
        }
    </script>

    <!-- Load Google Maps API -->
    <script async defer
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyA3dbB5PCrN-1AW1ipmdM6RFrsdNdks3cU&v=3&callback=initMap">
    </script>


    <script src="{{ URL::asset('js/custom-site.js') }}"></script>
@endsection
