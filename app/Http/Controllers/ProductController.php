<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Http\Controllers\NotificationController;
use App\Models\Productimage;
use Illuminate\Support\Facades\Session;
use Intervention\Image\Facades\Image;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ProductImport;
use App\Imports\UpdateProductExcel;
use App\Traits\FCM;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    use FCM;
    public function index()
    {
        $data['products'] = Product::orderBy('id', 'DESC')->get();
        return view('dashboards.products.list', $data);
    }

    public function create()
    {
        $data['categories'] = Category::where('status', 'Active')->get();
        return view('dashboards.products.new', $data);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|unique:products',
            'category_id' => 'required|integer',
            'image' => 'nullable|mimes:jpeg,png,jpg|max:2048',
        ]);
        $product = new Product;
        if (!empty($request->image)) {
            $filecontent = File::get($request->image);
            $imageName = time() . '_' . $request->image->getClientOriginalName();
            Storage::disk('public')->put("/productImage/$imageName", $filecontent);

            $product->image = $imageName;
        }

        $product->name = $request['name'];
        $product->category_id = $request['category_id'];

        $product->save();

        return redirect('/product')->with('Status', 'New Product Added.');
    }

    public function edit($id)
    {
        $data['product'] = Product::findorFail($id);
        $data['categories'] = Category::all();
        return view('dashboards.products.edit', $data);
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required|unique:products,id',
            'category_id' => 'required|integer',
            'image' => 'nullable|mimes:jpeg,png,jpg|max:2048',
        ]);

        $product = Product::findorFail($id);

        if (!empty($request->image)) {
            $filecontent = File::get($request->image);
            $imageName = time() . '_' . $request->image->getClientOriginalName();
            Storage::disk('public')->put("/productImage/$imageName", $filecontent);

            $product->image = $imageName;
        }

        $product->name = $request->input('name');
        $product->category_id = $request->input('category_id');

        $product->save();
        return redirect('/product')->with('Status', 'Product Updated');
    }

    public function destroy($id)
    {
        $product = Product::with('category')->findorFail($id);
        if ($product->category->status == 'Active') {
            if ($product->status == "Active") {
                $product->status = "Inactive";
                Session::flash('Status', 'product Disabled Successfully.');
            } else {
                $product->status = "Active";
                Session::flash('Status', 'product Enabled Successfully.');
            }
            $product->save();

            return back();
        } else {
            Session::flash('Status', "Product Cannot be update cause it's category is Disabled.");
            return back();
        }
    }

    public function excelProduct(Request $request)
    {
        $this->validate($request, [
            'excel_file' => 'required|file|mimes:xlsx',
        ]);

        $file = $request->file('excel_file');
        Excel::import(new ProductImport, $file);

        $title = "New Product Added";
        $body  = "New products have been added to the company.";
        $topic = strtolower(config('app.env')) === 'production' ? config('app.prod-topic') : config('app.dev-topic');

        $data = [
            'route_channel' => 'product',
        ];

        $this->runFirebaseNotification($fcmid = null, $topic, $title, $body, $data);

        return redirect('product')->with('Status', 'New Product Added.');
    }

    public function viewImage($id)
    {
        $product = Product::findorFail($id);
        return view('dashboards.products.imageAddView', ['product' => $product]);
    }

    public function imageSave(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'image.*' => 'required|mimes:jpeg,jpg,png|max:5048',
        ]);

        if ($validate->fails()) {
            return back()->withErrors($validate);
        }

        if (!$request->hasFile('image')) {
            return back()->withErrors(['image' => 'No images provided']);
        }

        $destinationPath = public_path('thumbnail');

        if (!File::isDirectory($destinationPath)) {
            File::makeDirectory($destinationPath, 0755, true);
        }

        foreach ($request->file('image') as $file) {
            $name = time() . '_' . rand(1000, 9999) . '_' . $file->getClientOriginalName();

            Storage::disk('public')->put("productImage/{$name}", File::get($file));

            Image::make($file)
                ->resize(100, 100, fn($constraint) => $constraint->aspectRatio())
                ->save("{$destinationPath}/{$name}");

            Productimage::create([
                'product_id' => $request->product_id,
                'image' => $name,
                'thumbnail' => $name,
            ]);
        }

        return redirect('/product')->with('Status', 'Image upload successful.');
    }

    public function imgView($id)
    {
        Product::findorFail($id);
        $images = Productimage::where('product_id', $id)->get();
        return view('dashboards.products.viewImg', ['images' => $images]);
    }

    public function unlink($id)
    {
        $img = Productimage::findorFail($id);
        $image_path = public_path() . '/productImage/' . $img->image;
        if (Storage::disk('public')->exists("productImage/$img->image")) {
            Storage::disk('public')->delete("productImage/$img->image");
        }
        $thmb = public_path() . '/thumbnail/' . $img->image;
        if (file_exists($thmb)) {
            @unlink($thmb);
        }
        $img->delete();
        return  redirect('/product/images/' . $img->product_id . '/all')->with('Status', 'Image Deleted Successfully!!');
    }

    public function excelUpdate(Request $request)
    {
        $this->validate($request, [
            'excel_file' => 'required|file|mimes:xlsx',
        ]);
        $file = $request->file('excel_file');
        Excel::import(new UpdateProductExcel, $file);


        $title = 'Product Price Updated';
        $flag = 'product';
        $content = array('Title' => 'Product Price Updated', 'Message' => 'Product Price Updated.');

        $notice = new NotificationController();
        // $notice->sendGroupNotification($flag, $title, $content);

        return redirect('product')->with('Status', 'Product Price Updated.');
    }
}
