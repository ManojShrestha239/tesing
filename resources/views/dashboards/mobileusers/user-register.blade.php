@extends('dashboards.blank')

@section('content')

    <style>
        .select-area {
            overflow: auto;
            height: 100px;
            width: 49.1%;
        }

        .select-label {
            position: sticky;
            top: 0;
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
        <div class="breadcrumbs">
            <h1>Register New User</h1>
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
                <div class="page-sidebar">
                    <div class="portlet light portlet-fit">
                        <div class="portlet-title">
                            <div class="caption">
                                <i class=" icon-bar font-red"></i>
                                <span class="caption-subject font-red bold uppercase">Quick Navigation</span>
                            </div>

                        </div>
                        <nav class="navbar" role="navigation">
                            <!-- Brand and toggle get grouped for better mobile display -->
                            <!-- Collect the nav links, forms, and other content for toggling -->
                            <ul class="nav navbar-nav margin-bottom-35 user-class">

                                @role('Super-Admin')
                                    <li><a href="/admin/create">Add New Admin </a></li>
                                    <li><a href="/admin">List Admin </a></li>
                                    <li><a href="/role-permission">Roles </a></li>
                                @endrole
                                <li class="active"><a href="/user/create">Add New User </a></li>
                                <li><a href="/user">List Users </a></li>
                            </ul>
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
                    <div class="row">
                        <div class="col-md-12">
                            <!-- BEGIN EXAMPLE TABLE PORTLET-->
                            <div class="portlet light portlet-fit bordered">
                                <div class="portlet-title">
                                    <div class="caption">
                                        <i class=" fa fa-user font-red"></i>
                                        <span class="caption-subject font-red bold uppercase">Add New User</span>
                                    </div>

                                </div>

                                @if (count($errors) > 0)
                                    @foreach ($errors->all() as $error)
                                        <p class="alert alert-danger">{{ $error }}</p>
                                    @endforeach
                                @endif

                                <form method="POST" action="/user">
                                    @csrf

                                    <div class="form-group row">
                                        <label for="name"
                                            class="col-md-3 col-form-label text-right">{{ __('Name') }}</label>

                                        <div class="col-md-6">
                                            <input type="text" class="form-control" value="{{ old('name') }}"
                                                name="name" placeholder="Full Name" required autofocus>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="email"
                                            class="col-md-3 col-form-label text-right">{{ __('E-Mail Address') }}</label>

                                        <div class="col-md-6">
                                            <input id="email" type="email" class="form-control" name="email"
                                                value="{{ old('email') }}" placeholder="E-mail Address" required autofocus>

                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="phone"
                                            class="col-md-3 col-form-label text-right">{{ __('Phone') }}</label>

                                        <div class="col-md-6">
                                            <input type="text" class="form-control" value="{{ old('phone') }}"
                                                name="phone" placeholder="98XXXXXXXX" autofocus
                                                pattern="^(98|97)[0-9]{8}$"
                                                title="Enter a valid phone number starting with 97 or 98. Phone number must be exactly 10 digits"
                                                minlength="10" maxlength="10"
                                                oninput="this.value = this.value.replace(/[^0-9]/g, '')" />
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="password"
                                            class="col-md-3 col-form-label text-right">{{ __('Password') }}</label>

                                        <div class="col-md-6">
                                            <input id="password" type="password" class="form-control" name="password"
                                                placeholder="password" required autofocus>

                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="password"
                                            class="col-md-3 col-form-label text-right">{{ __('Confirm Password') }}</label>

                                        <div class="col-md-6">
                                            <input id="password_confirmation" type="password" class="form-control"
                                                name="password_confirmation" placeholder="Confirm Password" required
                                                autofocus>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="role_id"
                                            class="col-md-3 col-form-label text-right">{{ __('User Role') }}</label>
                                        <div class="col-md-6">
                                            <select name="role_id" id="role_id" class="form-control" required>
                                                <option value="">-- Select Role --</option>
                                                @foreach ($roles as $role)
                                                    <option value="{{ $role->id }}">{{ $role->role }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-md-3 col-form-label text-right">Is Senior?</label>
                                        <div class="col-md-6">
                                            <label>
                                                <input type="radio" name="is_senior" value="1"
                                                    onchange="toggleSeniorSection()">
                                                Yes
                                            </label>
                                            <label style="margin-left: 15px;">
                                                <input type="radio" name="is_senior" value="0"
                                                    onchange="toggleSeniorSection()" checked>
                                                No
                                            </label>
                                            <label style="margin-left: 15px;">
                                                <input type="radio" name="is_senior" value="2"
                                                    onchange="toggleSeniorSection()">
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
                                                    @if (count($mobileusers) > 0)
                                                        @foreach ($mobileusers as $mu)
                                                            <li data-name="{{ strtolower($mu->name) }}"
                                                                data-email="{{ strtolower($mu->email ?? '') }}"
                                                                data-phone="{{ $mu->phone ?? '' }}">
                                                                <label
                                                                    style="display:block; padding:4px 0; cursor:pointer; margin:0;">
                                                                    <input type="checkbox" name="team_members[]"
                                                                        value="{{ $mu->id }}"
                                                                        class="senior-team-checkbox"
                                                                        onchange="updateSeniorTeamCount()">
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
                                                            No mobile users found. Create a user first.
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
                                        <label class="col-md-3 col-form-label text-right">Area</label>
                                        <div class="col-md-6 select-area">
                                            <input type="text" id="area-search" onkeyup="searchArea()"
                                                placeholder="Search for area.." class="form-control">
                                            <ul id="myUL-area" style="list-style-type: none;overflow-y: auto;">
                                                @foreach ($areas as $area)
                                                    <li>
                                                        <a style="text-decoration:none; color:black;">
                                                            <input type="checkbox" name="areas[]" id="area-id"
                                                                class="checkbox-area" value="{{ $area->areacode }}">
                                                            <label>{{ $area->area }}</label> <br>
                                                        </a>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="form-group row subarea-div" style="display:none;">
                                        <label class="col-md-3 col-form-label text-right">Sub Area</label>
                                        <div class="col-md-6 " style="height:100px;overflow:auto">
                                            <input type="text" id="sub-area-search" onkeyup="searchSubArea()"
                                                placeholder="Search for subarea.." class="form-control">
                                            <ul id="myUL-subarea" class="select-subarea"
                                                style="list-style-type: none;overflow-y: auto;">
                                            </ul>
                                        </div>
                                    </div>

                                    <div class="form-group row mb-0">
                                        <div class="col-md-6 col-md-offset-3">
                                            <button type="submit" class="btn btn-primary" style="width: 200px;">
                                                {{ __('Create') }}
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        /* ============================================================
                                                                                                             |  Area / Sub-Area search (existing)
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

                // Skip the "no users found" placeholder
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

            // Toggle "no matches" message
            var noMatch = document.getElementById('senior-team-no-match');
            if (noMatch) {
                noMatch.style.display = (visibleCount === 0 && filter !== '') ? 'block' : 'none';
            }
        }

        function clearSeniorTeam() {
            document.querySelectorAll('.senior-team-checkbox').forEach(function(cb) {
                cb.checked = false;
            });
            updateSeniorTeamCount();
        }

        function updateSeniorTeamCount() {
            var count = document.querySelectorAll('.senior-team-checkbox:checked').length;
            var el = document.getElementById('senior-team-count');
            if (el) {
                el.textContent = count + ' selected';
            }
        }

        /* ============================================================
         |  Area → Sub-Area AJAX (existing)
         * ============================================================ */
        $(document).ready(function() {
            toggleSeniorSection();
            updateSeniorTeamCount();

            $('.checkbox-area').click(function(e) {
                $('.new-subarea').remove();
                var searchIDs = [];
                var data = $("input[class='checkbox-area']:checked").map(function() {
                    searchIDs.push($(this).val());
                    $.ajax({
                        url: "/getsubarea",
                        type: "get",
                        data: {
                            data: searchIDs,
                        },
                        success: function(response) {
                            $('.subarea-div').css('display', 'block');
                            $('.new-subarea').remove();
                            $('#check-all-subarea').remove();
                            $('#uncheck-all-subarea').remove();
                            $('.select-subarea br').remove();

                            response.map(function(data) {
                                $('.select-subarea').prepend(
                                    '<li><a style="color: black;"><input type="checkbox" class="new-subarea" id="subarea-id" name="subarea[]" value="' +
                                    data.subarea +
                                    '"><label style="margin-left:5px;" class="new-subarea">"' +
                                    data.subarea + '"</label></a></li>');
                            });
                            $('.select-subarea').prepend(
                                '<input type="button" value="Check All"  class="btn btn-primary btn-sm col-md-12 check" id="check-all-subarea" autofocus>'
                            );
                            $('.select-subarea').prepend(
                                '<input type="button" value="Uncheck All" style="display:none;" class="btn col-md-12  btn-sm btn-primary uncheck" id="uncheck-all-subarea" autofocus>'
                            );
                        },
                    });
                });
            });

            $(document).on('click', '#check-all-subarea', function() {
                $('#uncheck-all-subarea').css('display', 'block');
                $('#check-all-subarea').css('display', 'none');
                $('[id=subarea-id]').attr('checked', 'checked');
            });

            $(document).on('click', '#uncheck-all-subarea', function() {
                $('#check-all-subarea').css('display', 'block');
                $('#uncheck-all-subarea').css('display', 'none');
                $('[id=subarea-id]').removeAttr('checked');
            });
        });
    </script>
@endsection
