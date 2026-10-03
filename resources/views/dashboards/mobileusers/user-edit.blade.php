@extends('dashboards.blank')

@section('content')
    <style>
        #goback:hover {
            border: 2px solid #f48220;
            cursor: pointer;
            transition: 0.4s;
        }

        #goback {
            border: 2px solid #0d2444;
            width: 100px;
            border-radius: 32px;
        }

        #goback img {
            margin-top: 6px;
            margin-left: 4px;
            margin-right: 4px;
        }

        #goback h3 {
            margin-top: 6px;
            font-size: 20px;
            font-weight: 600;
        }

        .select-area {
            max-height: 150px !important;
            overflow: auto;
            width: 49.1%;
        }

        /* ---- Senior Team search UI ---- */
        .senior-team-toolbar {
            display: flex;
            flex-direction: column;
            gap: 6px;
            margin-top: 8px;
            margin-bottom: 8px;
        }

        .senior-team-actions {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 6px;
        }

        .senior-team-count {
            margin-left: auto;
            font-size: 12px;
            color: #555;
            font-weight: 600;
        }

        #senior-team-list li {
            transition: background-color .12s ease;
        }

        #senior-team-list li:hover {
            background-color: #f5f7fa;
        }

        /* Visual hint: selected rows slightly highlighted */
        #senior-team-list li.is-selected {
            background-color: #f0f7ff;
        }

        @media (max-width: 575px) {
            .senior-team-actions {
                justify-content: flex-start;
            }

            .senior-team-count {
                margin-left: 0;
                width: 100%;
            }
        }
    </style>

    <div class="page-content">

        <div id="goback" onclick="goBack()">
            <img style="float:left;width:25px; height:25px;" onclick="goBack()"
                src={{ asset('assets/global/img/left-arrow-key.svg') }}>
            <h3> Back</h3>
        </div>
        <script>
            function goBack() {
                window.history.back();
            }
        </script>
        <div class="breadcrumbs">

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
            <div class="page-content-row" id="replecable">

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
                    <div class="row">
                        <div class="col-md-12">
                            <div class="portlet light portlet-fit bordered">
                                <div class="portlet-title">
                                    <div class="caption">
                                        <i class=" fa fa-user font-red"></i>
                                        <span class="caption-subject font-red bold uppercase">Edit User</span>
                                    </div>

                                </div>

                                @if (count($errors) > 0)
                                    @foreach ($errors->all() as $error)
                                        <p class="alert alert-danger">{{ $error }}</p>
                                    @endforeach
                                @endif

                                <form method="POST" action="/user/{{ $mobileusers->id }}">
                                    {{ method_field('PATCH') }}
                                    @csrf

                                    <div class="form-group row">
                                        <label for="name"
                                            class="col-md-3 col-form-label text-right">{{ __('Name') }}</label>

                                        <div class="col-md-6">
                                            <input type="text" class="form-control" value="{{ $mobileusers->name }}"
                                                name="name" placeholder="Full Name" required autofocus>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="email"
                                            class="col-md-3 col-form-label text-right">{{ __('E-Mail Address') }}</label>

                                        <div class="col-md-6">
                                            <input id="email" type="email" class="form-control" name="email"
                                                value="{{ $mobileusers->email }}" placeholder="E-mail Address" required
                                                autofocus>

                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="phone"
                                            class="col-md-3 col-form-label text-right">{{ __('Phone') }}</label>

                                        <div class="col-md-6">
                                            <input type="text" class="form-control" value="{{ $mobileusers->phone }}"
                                                name="phone" placeholder="98XXXXXXXX" autofocus
                                                pattern="^(98|97)[0-9]{8}$"
                                                title="Enter a valid phone number starting with 97 or 98. Phone number must be exactly 10 digits"
                                                minlength="10" maxlength="10"
                                                oninput="this.value = this.value.replace(/[^0-9]/g, '')" />
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-md-3 col-form-label text-right">Area</label>
                                        <div class="col-md-6 select-area">
                                            <input type="text" id="area-search" onkeyup="searchArea()"
                                                placeholder="Search for area.." class="form-control">
                                            <ul id="myUL-area" style="list-style-type: none;overflow-y: auto;">
                                                @foreach ($areas as $area)
                                                    <li>
                                                        <a style="text-decoration: none;color: black;">
                                                            <input type="checkbox" class="checkbox-area" name="areas[]"
                                                                id="area-data" value="{{ $area->areacode }}"
                                                                @foreach ($selectedarea as $select) @if ($select->areacode == $area->areacode) checked @endif @endforeach>
                                                            <label>{{ $area->area }}</label> <br>

                                                        </a>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="form-group row subarea-div">
                                        <label class="col-md-3 col-form-label text-right">Sub Area</label>
                                        <div class="col-md-6" style="max-height:150px;overflow:auto">
                                            <input type="text" id="sub-area-search" onkeyup="searchSubArea()"
                                                placeholder="Search for subarea.." class="form-control">
                                            <ul id="myUL-subarea" class="select-subarea"
                                                style="list-style-type: none;overflow: auto;">
                                                @foreach ($subarea as $area)
                                                    <li>
                                                        <a style="text-decoration: none;color: black;">
                                                            <input type="checkbox" class="new-subarea" name="subarea[]"
                                                                @foreach ($selectedsub as $select) @if ($select->subarea == $area->subarea) checked @endif @endforeach
                                                                value="{{ $area->subarea }}"> <label
                                                                class="new-subarea">{{ $area->subarea }}</label> <br>

                                                        </a>
                                                    </li>
                                                @endforeach
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-md-3 col-form-label text-right">Is Senior?</label>
                                        <div class="col-md-6">
                                            @php
                                                if ($mobileusers->is_super_senior === true) {
                                                    $defaultIsSenior = '2';
                                                } elseif ($mobileusers->is_senior === true) {
                                                    $defaultIsSenior = '1';
                                                } else {
                                                    $defaultIsSenior = '0';
                                                }
                                                $selected = old('is_senior', $defaultIsSenior);
                                            @endphp

                                            <label>
                                                <input type="radio" name="is_senior" value="1"
                                                    onchange="toggleSeniorSection()"
                                                    {{ $selected == '1' ? 'checked' : '' }}>
                                                Yes
                                            </label>

                                            <label style="margin-left: 15px;">
                                                <input type="radio" name="is_senior" value="0"
                                                    onchange="toggleSeniorSection()"
                                                    {{ $selected == '0' ? 'checked' : '' }}>
                                                No
                                            </label>

                                            <label style="margin-left: 15px;">
                                                <input type="radio" name="is_senior" value="2"
                                                    onchange="toggleSeniorSection()"
                                                    {{ $selected == '2' ? 'checked' : '' }}>
                                                Admin
                                            </label>
                                        </div>
                                    </div>

                                    {{-- ============================================================ --}}
                                    {{-- Senior section with searchable team member list            --}}
                                    {{-- ============================================================ --}}
                                    <div class="form-group row senior-section" style="display:none;">
                                        <label class="col-md-3 col-form-label text-right">Team Members</label>
                                        <div class="col-md-6">
                                            <small class="text-muted">
                                                Select employees to add to this senior's team. An employee can be under
                                                multiple seniors.
                                            </small>

                                            {{-- Search + actions toolbar --}}
                                            <div class="senior-team-toolbar">
                                                <input type="text" id="senior-team-search" class="form-control"
                                                    placeholder="Search by name, email, or phone..." autocomplete="off"
                                                    onkeyup="searchSeniorTeam()">

                                                <div class="senior-team-actions">
                                                    <button type="button" class="btn btn-xs btn-warning"
                                                        onclick="clearSeniorTeam()">
                                                        Clear All
                                                    </button>
                                                    <span class="senior-team-count" id="senior-team-count">
                                                        0 selected
                                                    </span>
                                                </div>
                                            </div>

                                            {{-- Scrollable list --}}
                                            <div
                                                style="max-height:200px; overflow-y:auto; border:1px solid #ddd; padding:5px;">
                                                <ul id="senior-team-list"
                                                    style="list-style-type: none; padding-left: 0; margin: 0;">
                                                    @if (count($allMobileusers) > 0)
                                                        @foreach ($allMobileusers as $mu)
                                                            @php
                                                                $isSelected = in_array($mu->id, $selectedTeamMembers);
                                                            @endphp
                                                            <li data-name="{{ strtolower($mu->name) }}"
                                                                data-email="{{ strtolower($mu->email ?? '') }}"
                                                                data-phone="{{ $mu->phone ?? '' }}"
                                                                class="{{ $isSelected ? 'is-selected' : '' }}">
                                                                <label
                                                                    style="display:block; padding:4px 0; cursor:pointer; margin:0;">
                                                                    <input type="checkbox" name="team_members[]"
                                                                        value="{{ $mu->id }}"
                                                                        class="senior-team-checkbox"
                                                                        onchange="onSeniorTeamChange(this)"
                                                                        {{ $isSelected ? 'checked' : '' }}>
                                                                    {{ $mu->name }}
                                                                    @if (!empty($mu->email))
                                                                        <small
                                                                            class="text-muted">({{ $mu->email }})</small>
                                                                    @endif
                                                                    @if (!empty($mu->phone))
                                                                        <small class="text-muted">·
                                                                            {{ $mu->phone }}</small>
                                                                    @endif
                                                                </label>
                                                            </li>
                                                        @endforeach
                                                    @else
                                                        <li style="padding:10px;color:#999;">
                                                            No other mobile users found.
                                                        </li>
                                                    @endif
                                                </ul>
                                            </div>

                                            <small class="text-muted" id="senior-team-no-match"
                                                style="display:none; color:#dc3545 !important; margin-top:5px;">
                                                No team members match your search.
                                            </small>
                                        </div>
                                    </div>

                                    <div class="form-group row super-senior-section" style="display:none;">
                                        <label class="col-md-3 col-form-label text-right">Team Auto-Assignment</label>
                                        <div class="col-md-6">
                                            <div class="alert alert-info" style="margin-bottom:0;">
                                                <i class="fa fa-info-circle"></i>
                                                <strong>Admin mode:</strong> All current mobile users will be
                                                automatically assigned under this admin. Any new mobile users created
                                                in the future will also be auto-assigned.
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="role_id"
                                            class="col-md-3 col-form-label text-right">{{ __('User Role') }}</label>
                                        <div class="col-md-6">
                                            <select name="role_id" id="role_id" class="form-control" required>
                                                <option value="">-- Select Role --</option>
                                                @foreach ($roles as $role)
                                                    <option value="{{ $role->id }}"
                                                        {{ old('role_id', $mobileusers->role_id) == $role->id ? 'selected' : '' }}>
                                                        {{ $role->role }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>


                                    <div class="form-group row mb-0">
                                        <div class="col-md-6 col-md-offset-3">
                                            <button type="submit" class="btn btn-primary" style="width: 200px;">
                                                {{ __('Update') }}
                                            </button>
                                        </div>
                                    </div>
                                </form>
                                <script src="{{ URL::asset('assets/global/scripts/bootstrap-select.js') }}"></script>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        /* ============================================================
                                                                                                             |  Area / Sub-Area search
                                                                                                             * ============================================================ */
        function searchArea() {
            var input, filter, ul, li, a, i, txtValue;
            input = document.getElementById("area-search");
            filter = input.value.toUpperCase();
            ul = document.getElementById("myUL-area");
            li = ul.getElementsByTagName("li");
            for (i = 0; i < li.length; i++) {
                a = li[i].getElementsByTagName("a")[0];
                txtValue = a.textContent || a.innerText;
                if (txtValue.toUpperCase().indexOf(filter) > -1) {
                    li[i].style.display = "";
                } else {
                    li[i].style.display = "none";
                }
            }
        }

        function searchSubArea() {
            var input, filter, ul, li, a, i, txtValue;
            input = document.getElementById("sub-area-search");
            filter = input.value.toUpperCase();
            ul = document.getElementById("myUL-subarea");
            li = ul.getElementsByTagName("li");
            for (i = 0; i < li.length; i++) {
                a = li[i].getElementsByTagName("a")[0];
                txtValue = a.textContent || a.innerText;
                if (txtValue.toUpperCase().indexOf(filter) > -1) {
                    li[i].style.display = "";
                } else {
                    li[i].style.display = "none";
                }
            }
        }

        /* ============================================================
         |  Senior / Admin section toggle
         * ============================================================ */
        function toggleSeniorSection() {
            var seniorRadio = document.querySelector('input[name="is_senior"]:checked');
            var seniorSection = document.querySelector('.senior-section');
            var superSeniorSection = document.querySelector('.super-senior-section');

            if (!seniorRadio) return;

            if (seniorRadio.value === '1') {
                seniorSection.style.display = 'block';
                superSeniorSection.style.display = 'none';
            } else if (seniorRadio.value === '2') {
                superSeniorSection.style.display = 'block';
                seniorSection.style.display = 'none';
            } else {
                seniorSection.style.display = 'none';
                superSeniorSection.style.display = 'none';
            }
        }

        /* ============================================================
         |  Senior Team — search + bulk actions
         * ============================================================ */
        function searchSeniorTeam() {
            var input = document.getElementById('senior-team-search');
            if (!input) return;

            var filter = input.value.trim().toLowerCase();
            var list = document.getElementById('senior-team-list');
            if (!list) return;

            var items = list.getElementsByTagName('li');
            var visibleCount = 0;

            for (var i = 0; i < items.length; i++) {
                var item = items[i];

                if (!item.hasAttribute('data-name')) {
                    continue;
                }

                var name = item.getAttribute('data-name') || '';
                var email = item.getAttribute('data-email') || '';
                var phone = item.getAttribute('data-phone') || '';

                var haystack = name + ' ' + email + ' ' + phone;
                var matches = filter === '' || haystack.indexOf(filter) > -1;

                item.style.display = matches ? '' : 'none';
                if (matches) visibleCount++;
            }

            var noMatch = document.getElementById('senior-team-no-match');
            if (noMatch) {
                noMatch.style.display = (visibleCount === 0 && filter !== '') ? 'block' : 'none';
            }
        }

        function clearSeniorTeam() {
            document.querySelectorAll('.senior-team-checkbox').forEach(function(cb) {
                cb.checked = false;
            });
            onSeniorTeamChange();
        }

        function updateSeniorTeamCount() {
            var count = document.querySelectorAll('.senior-team-checkbox:checked').length;
            var el = document.getElementById('senior-team-count');
            if (el) {
                el.textContent = count + ' selected';
            }
        }

        /**
         * Fires whenever a single team-member checkbox changes.
         * Updates the counter, highlights the row, and re-sorts the list
         * so the newly checked member moves to the top.
         */
        function onSeniorTeamChange() {
            updateSeniorTeamCount();
            highlightSelectedRows();
            sortSeniorTeamList();
        }

        /**
         * Adds / removes the `.is-selected` class on each row
         * so checked rows get a subtle highlight.
         */
        function highlightSelectedRows() {
            document.querySelectorAll('#senior-team-list li[data-name]').forEach(function(li) {
                var cb = li.querySelector('.senior-team-checkbox');
                if (cb && cb.checked) {
                    li.classList.add('is-selected');
                } else {
                    li.classList.remove('is-selected');
                }
            });
        }

        /**
         * Move all checked rows to the top of the list.
         * Preserves the original relative order within checked and unchecked groups.
         * Runs once on page load and again whenever a checkbox changes.
         */
        function sortSeniorTeamList() {
            var list = document.getElementById('senior-team-list');
            if (!list) return;

            var items = Array.from(list.children).filter(function(li) {
                return li.hasAttribute('data-name');
            });

            if (items.length === 0) return;

            var checked = [];
            var unchecked = [];

            items.forEach(function(li) {
                var cb = li.querySelector('.senior-team-checkbox');
                if (cb && cb.checked) {
                    checked.push(li);
                } else {
                    unchecked.push(li);
                }
            });

            // Re-append in order: checked first, then unchecked
            checked.forEach(function(li) {
                list.appendChild(li);
            });
            unchecked.forEach(function(li) {
                list.appendChild(li);
            });
        }

        /* ============================================================
         |  Area → Sub-Area AJAX (preserves checked subareas on reload)
         * ============================================================ */
        $(document).ready(function() {
            toggleSeniorSection();

            // ---- Initialise senior team state ----
            updateSeniorTeamCount();
            highlightSelectedRows();
            sortSeniorTeamList(); // ← moves selected members to the top on load

            function loadSubareas() {
                var selectedAreas = $('.checkbox-area:checked').map(function() {
                    return $(this).val();
                }).get();

                var selectedSubareas = $('.new-subarea:checked').map(function() {
                    return $(this).val();
                }).get();

                if (!selectedAreas.length) {
                    $('.select-subarea').empty();
                    $('.subarea-div').hide();
                    return;
                }

                $.ajax({
                    url: "/getsubarea",
                    type: "get",
                    data: {
                        data: selectedAreas,
                    },
                    success: function(response) {
                        $('.subarea-div').css('display', 'block');

                        var checkedMap = {};
                        selectedSubareas.forEach(function(subarea) {
                            checkedMap[subarea] = true;
                        });

                        var renderedSubareas = {};
                        var html = '';

                        response.forEach(function(item) {
                            if (renderedSubareas[item.subarea]) {
                                return;
                            }

                            renderedSubareas[item.subarea] = true;

                            var checked = checkedMap[item.subarea] ? ' checked' : '';

                            html += '<li><a style="text-decoration: none;color: black;">' +
                                '<input type="checkbox" class="new-subarea" name="subarea[]" value="' +
                                item.subarea + '"' + checked + '>' +
                                '<label style="margin-left:5px;" class="new-subarea">' +
                                item.subarea + '</label></a></li>';
                        });

                        $('.select-subarea').html(html);
                    },
                });
            }

            $('.checkbox-area').on('change', loadSubareas);

            loadSubareas();
        });
    </script>
@endsection
