<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductType\StoreRequest;
use App\Http\Requests\ProductType\UpdateRequest;
use App\Models\ProductType;
use Illuminate\Http\Request;
use DataTables;

class ProductTypeController extends Controller
{
    public ProductType $productType;

    public function __construct(ProductType $productType)
    {
        $this->middleware('check_product_type_permission');
        $this->productType = $productType;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $productTypes = $this->productType->getAllProductTypes();
            return Datatables::of($productTypes)
                ->addIndexColumn()
                ->addColumn('action', function ($data) {

                    $btn = '';
                    $btn .= '<span class="action-icon-spacing">';
                    $btn .= '<a href="' . route('admin:product.types.edit', encrypt($data->id)) . '" title="Edit">
                                          <i class="fa fa-edit text-primary"></i>
                                      </a>';

                    $btn .= '<a href="javascript:void(0);" class="delete" data-id="' . $data->id . '" title="Delete">
                                       <i class="fa fa-trash text-danger"></i>
                                   </a>';

                    $btn .= '</span>';

                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('admin.modules.product-types.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.modules.product-types.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequest $request)
    {
        $productTypeDetails = $request->validated();
        $result = $this->productType->createProductType($productTypeDetails);

        if (!$result) {
            return back()->with('error', 'Something went wrong, try again!');
        }
        return redirect()->route('admin:product.types')->with('success', 'The product type created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $id = decrypt($id);
        $productType = $this->productType->getProductType($id);
        return view('admin.modules.product-types.edit', compact('productType'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRequest $request, $id)
    {
        $id = decrypt($id);
        $productTypeDetails = $request->validated();
        $result = $this->productType->updateProductType($id, $productTypeDetails);

        if (!$result) {
            return back()->with('error', 'Something went wrong, try again!');
        }
        return redirect()->route('admin:product.types')->with('success', 'The product type updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $id = $request->product_id;
        $result = $this->productType->destroyProductType($id);

        if (!$result) {
            return response()->json(['status' => false, 'message' => 'Something went wrong, try again!', 404]);
        }
        return response()->json(['status' => true, 'message' => 'The product type deleted successfully.'], 200);
    }
}
