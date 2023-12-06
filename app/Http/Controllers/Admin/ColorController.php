<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Color\StoreRequest;
use App\Http\Requests\Color\UpdateRequest;
use App\Models\Color;
use Illuminate\Http\Request;
use DataTables;

class ColorController extends Controller
{
    public Color $color;

    public function __construct(Color $color)
    {
        $this->middleware('check_color_permission');
        $this->color = $color;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $colors = $this->color->getAllColors();
            return Datatables::of($colors)
                ->addIndexColumn()
                ->addColumn('action', function ($data) {

                    $btn = '';
                    $btn .= '<span class="action-icon-spacing">';
                    $btn .= '<a href="' . route('admin:colors.edit', encrypt($data->id)) . '" title="Edit">
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
        return view('admin.modules.colors.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.modules.colors.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequest $request)
    {

        $colorDetails = $request->validated();
        $result = $this->color->createColor($colorDetails);

        if (!$result) {
            return back()->with('error', 'Something went wrong! try again.');
        }
        return redirect()->route('admin:colors')->with('success', 'Color created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $id = decrypt($id);
        $color = $this->color->getColor($id);
        return view('admin.modules.colors.edit', compact('color'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRequest $request, $id)
    {
        $id = decrypt($id);
        $colorDetails = $request->validated();
        $result = $this->color->updateColor($id, $colorDetails);
        if (!$result) {
            return back()->with('error', 'Something went wrong! try again.');
        }
        return redirect()->route('admin:colors')->with('success', 'Color updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $id = $request->color_id;
        $result = $this->color->destroyColor($id);
        if (!$result) {
            return response()->json(['status' => false, 'message' => 'Something went wrong! try again'], 404);
        }
        return response()->json(['status' => true, 'message' => 'Color deleted successfully'], 200);
    }
}
