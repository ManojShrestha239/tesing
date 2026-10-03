<div class="page-content2">
    <img style="float:right;width:25px; height:25px;"
        onclick="document.getElementById('order-popup').style.display='none'"
        src={{ asset('assets/pages/img/quit.svg') }}>
    <div class="breadcrumbs">
        <h1>Edit Area</h1>
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
                    <div class="alert alert-success alert-dismissible" role="alert">
                        <b>{{ session('Status') }}</b>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif
                <div class="row">
                    <div class="col-md-12">
                        <!-- BEGIN EXAMPLE TABLE PORTLET-->
                        <div class="portlet light portlet-fit bordered">
                            <div class="portlet-title">
                                <div class="caption">
                                    <i class=" icon-map font-red"></i>
                                    <span class="caption-subject font-red bold uppercase">Edit Area</span>
                                </div>

                            </div>

                            @if (count($errors) > 0)
                                @foreach ($errors->all() as $error)
                                    <p class="alert alert-danger">{{ $error }}</p>
                                @endforeach
                            @endif

                            <form method="POST" action="/area/{{ $area->areacode }}">
                                {{ method_field('PATCH') }}
                                @csrf

                                <div class="form-group row">
                                    <label for="area"
                                        class="col-md-3 col-form-label text-right">{{ __('Area') }}</label>

                                    <div class="col-md-6">
                                        <input type="text" class="form-control" value="{{ $area->area }}"
                                            name="area" placeholder="Area" required autofocus>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="area"
                                        class="col-md-3 col-form-label text-right">{{ __('Sub Area') }}</label>
                                    <div class="col-md-6" style="max-height:200px;overflow-y:auto; padding-left:0px;">
                                        <input type="text" id="subarea-search" class="form-control"
                                            placeholder="Search sub areas.." style="margin-bottom:10px;">
                                        @foreach ($subareas as $sub)
                                            <div class="row subarea-item" style="margin-left:0px;"
                                                data-subarea-name="{{ strtolower($sub->subarea) }}">
                                                <input type="checkbox"
                                                    @foreach ($assigns as $as) @if ($as->subarea_id == $sub->id) checked @endif @endforeach
                                                    class="col-md-1 chkbox" value="{{ $sub->id }}" name="subarea[]"
                                                    autofocus>
                                                <span class="col-md-6 chk-span">{{ $sub->subarea }}</span>
                                            </div>
                                        @endforeach
                                        <div id="no-subareas-found" class="text-muted"
                                            style="display:none; padding:6px 0;">No sub areas found</div>
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
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    (function() {
        var searchInput = document.getElementById('subarea-search');
        var searchTimeout = null;

        if (!searchInput) {
            return;
        }

        function filterSubareas() {
            var term = searchInput.value.trim().toLowerCase();
            var items = document.querySelectorAll('.subarea-item');
            var visibleCount = 0;

            items.forEach(function(item) {
                var name = (item.getAttribute('data-subarea-name') || '');
                var isMatch = !term || name.indexOf(term) !== -1;

                item.style.display = isMatch ? '' : 'none';
                if (isMatch) {
                    visibleCount++;
                }
            });

            var emptyState = document.getElementById('no-subareas-found');
            if (emptyState) {
                emptyState.style.display = visibleCount === 0 ? 'block' : 'none';
            }
        }

        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(filterSubareas, 150);
        });
    })();
</script>
