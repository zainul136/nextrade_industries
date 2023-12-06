<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Warehouse\StoreRequest;
use App\Http\Requests\Warehouse\UpdateRequest;
use Illuminate\Http\Request;
use App\Models\Warehouse;
use DataTables;

class WarehouseController extends Controller
{
    public Warehouse $warehouse;

    public function __construct(Warehouse $warehouse)
    {
        $this->middleware('check_warehouse_permission');

        $this->warehouse = $warehouse;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        if ($request->ajax()) {
            $warehouses = $this->warehouse->getAllWarehouses();
            return Datatables::of($warehouses)
                ->addIndexColumn()
                ->addColumn('action', function ($data) {

                    $btn = '';
                    $btn .= '<span class="action-icon-spacing">';
                    $btn .= '<a href="' . route('admin:warehouses.edit', encrypt($data->id)) . '" title="Edit">
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
        return view('admin.modules.warehouses.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.modules.warehouses.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequest $request)
    {
        $result = $this->warehouse->createWarehouse($request->validated());

        if (!$result) {
            return back()->with('error', 'Something went wrong, try again!');
        }
        return redirect()->route('admin:warehouses')->with('success', 'Warehouse created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $id = decrypt($id);
        $warehouse = $this->warehouse->getWarehouse($id);
        return view('admin.modules.warehouses.edit', compact('warehouse'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRequest $request, $id)
    {
        $id = decrypt($id);
        $warehouseDetails = $request->validated();
        $result = $this->warehouse->updateWarehouse($id, $warehouseDetails);

        if (!$result) {
            return back()->with('error', 'Something went wrong, try again!');
        }
        return redirect()->route('admin:warehouses')->with('success', 'Warehouse updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $id = $request->warehouse_id;
        $result = $this->warehouse->destroyWarehouse($id);

        if (!$result) {
            return response()->json(['status' => false, 'message' => 'Something went wrong, try again!', 404]);
        }
        return response()->json(['status' => true, 'message' => 'Warehouse deleted successfully.'], 200);
    }
}
