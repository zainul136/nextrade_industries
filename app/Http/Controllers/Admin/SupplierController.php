<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Supplier\StoreRequest;
use App\Http\Requests\Supplier\UpdateRequest;
use App\Models\Supplier;
use Illuminate\Http\Request;
use DataTables;

class SupplierController extends Controller
{
    public Supplier $supplier;

    public function __construct(Supplier $supplier)
    {
        $this->middleware('check_supplier_permission');
        $this->supplier = $supplier;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $suppliers = $this->supplier->getAllSuppliers();
            return Datatables::of($suppliers)
                ->addIndexColumn()
                ->addColumn('action', function ($data) {

                    $btn = '';
                    $btn .= '<span class="action-icon-spacing">';
                    $btn .= '<a href="' . route('admin:suppliers.edit', encrypt($data->id)) . '" title="Edit">
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
        return view('admin.modules.suppliers.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.modules.suppliers.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequest $request)
    {
        $supplierDetails = $request->validated();
        $result = $this->supplier->createSupplier($supplierDetails);

        if (!$result) {
            return back()->with('error', 'Something went wrong, try again!');
        }
        return redirect()->route('admin:suppliers')->with('success', 'Supplier created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $id = decrypt($id);
        $supplier = $this->supplier->getSupplier($id);
        return view('admin.modules.suppliers.edit', compact('supplier'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRequest $request, $id)
    {
        $id = decrypt($id);
        $supplierDetails = $request->validated();
        $result = $this->supplier->updateSupplier($id, $supplierDetails);

        if (!$result) {
            return back()->with('error', 'Something went wrong, try again!');
        }
        return redirect()->route('admin:suppliers')->with('success', 'Supplier updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $id = $request->supplier_id;
        $result = $this->supplier->destroySupplier($id);

        if (!$result) {
            return response()->json(['status' => false, 'message' => 'Something went wrong, try again!', 404]);
        }
        return response()->json(['status' => true, 'message' => 'Supplier deleted successfully.'], 200);
    }
}
