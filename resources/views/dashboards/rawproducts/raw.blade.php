<!DOCTYPE html>
<html>

<head>
    <title>Product Form</title>
    <style>
        .material-row {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }

        .material-row select {
            margin-right: 10px;
        }

        .material-row i {
            cursor: pointer;
            margin-left: 5px;
            color: green;
        }

        .remove-icon {
            color: red;
        }
    </style>
</head>

<body>


    <h3>Product Form</h3>

    <form method="POST" action="/raw-product">
        @csrf
        <!-- Product Dropdown -->
        <label>Product Name:</label>
        <select name="product_id">
            <option value="">-- Select Product --</option>
            @foreach ($products as $product)
                <option value="{{ $product->id }}">{{ $product->name }}</option>
            @endforeach
        </select>
        <br><br>

        <!-- Raw Material Field Group -->
        <div id="rawMaterialGroup">
            <div class="material-row" id="rawMaterialRow-1">
                <select name="raw_material_ids[]" id="rawMaterial-1">
                    <option value="">-- Select Raw Material --</option>
                    @foreach ($rawMaterials as $material)
                        <option value="{{ $material->id }}">{{ $material->name }}</option>
                    @endforeach
                </select>
                <i class="add-icon" onclick="addRawMaterial()">➕</i>
            </div>
        </div>


        <br>
        <button type="submit">Submit</button>
    </form>



    <script>
        let count = 1;

        // Use Blade to pass the raw materials data into the JavaScript
        const rawMaterialOptions = @json(
            $rawMaterials->map(function ($material) {
                return '<option value="' + $material->id + '">' + $material->name + '</option>';
            })) - > implode('');

        function addRawMaterial() {
            count++;
            const div = document.createElement("div");
            div.className = "material-row";
            div.id = "rawMaterialRow-" + count;

            div.innerHTML = `
                <select name="raw_material_ids[]" id="rawMaterial-${count}">
                    <option value="">-- Select Raw Material --</option>
                    ${rawMaterialOptions}
                </select>
                <i class="add-icon" onclick="addRawMaterial()">➕</i>
                <i class="remove-icon" onclick="removeRawMaterial(${count})">➖</i>
            `;

            document.getElementById("rawMaterialGroup").appendChild(div);
        }

        function removeRawMaterial(id) {
            const row = document.getElementById("rawMaterialRow-" + id);
            if (row) {
                row.remove();
            }
        }
    </script>



</body>

</html>
