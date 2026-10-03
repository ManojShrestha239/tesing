<div class="page-content2">
    <div class="breadcrumbs">
        <img style="float:right;width:25px; height:25px;"
            onclick="document.getElementById('order-popup').style.display='none'"
            src={{ asset('assets/pages/img/quit.svg') }}>
        <h1>Edit Assigned Product With Rawmaterial</h1>
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
                                    <span class="caption-subject font-red bold uppercase">Edit Assigned Product With
                                        Rawmaterial</span>
                                </div>

                            </div>

                            @if (count($errors) > 0)
                                @foreach ($errors->all() as $error)
                                    <p class="alert alert-danger">{{ $error }}</p>
                                @endforeach
                            @endif
                            <form method="POST" action="/raw-product/{{ $rawProduct->id }}">
                                @method('patch')
                                @csrf

                                <div class="form-group row">
                                    <label for="product_name"
                                        class="col-md-3 col-form-label text-right">{{ __('Product Name ') }}</label>

                                    <div class="col-md-6">
                                        <select name="product_id" class="form-control" data-live-search="true" required>
                                            <option value="">Select Product</option>
                                            @foreach ($products as $product)
                                                <option value="{{ $product->id }}"
                                                    @if ($rawProduct->product_id == $product->id) selected @endif>
                                                    {{ $product->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-md-3 col-form-label text-right">RawMaterials</label>
                                    <div class="col-md-6 select-rawmaterials">
                                        <input type="text" id="rawmaterials-search" onkeyup="searchRawmaterials()"
                                            placeholder="Search for rawmaterials.." class="form-control">
                                        <ul id="myUL-rawmaterials" style="list-style-type: none; overflow-y: auto;">
                                            @foreach ($rawMaterials as $rawmaterial)
                                                <li>
                                                    <a style="text-decoration:none; color:black;">
                                                        <input type="checkbox" name="raw_material_ids[]"
                                                            id="rawmaterial-id-{{ $rawmaterial->id }}"
                                                            class="checkbox-rawmaterial" value="{{ $rawmaterial->id }}"
                                                            @if (in_array($rawmaterial->id, $rawMaterialIds)) checked @endif>
                                                        <label
                                                            for="rawmaterial-id-{{ $rawmaterial->id }}">{{ $rawmaterial->name }}</label>
                                                        <br>
                                                    </a>
                                                </li>
                                            @endforeach
                                        </ul>
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
    function searchRawmaterials() {
        var input, filter, ul, li, label, i, txtValue;
        input = document.getElementById("rawmaterials-search");
        filter = input.value.toUpperCase();
        ul = document.getElementById("myUL-rawmaterials");
        li = ul.getElementsByTagName("li");

        for (i = 0; i < li.length; i++) {
            label = li[i].getElementsByTagName("label")[0];
            txtValue = label.textContent || label.innerText;
            if (txtValue.toUpperCase().indexOf(filter) > -1) {
                li[i].style.display = "";
            } else {
                li[i].style.display = "none";
            }
        }
    }
</script>
{{-- @endsection --}}
