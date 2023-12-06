<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CGTGrade\StoreRequest;
use App\Http\Requests\CGTGrade\UpdateRequest;
use App\Models\CGTGrade;
use Illuminate\Http\Request;
use DataTables;
use Illuminate\Support\Facades\Validator;

class CGTGradeController extends Controller
{
    public CGTGrade $cgtGrade;

    public function __construct(CGTGrade $cgtGrade)
    {
        $this->middleware('check_cgt_permission');
        $this->cgtGrade = $cgtGrade;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $cgtGrades = $this->cgtGrade->getAllCgtGrades();
            return Datatables::of($cgtGrades)
                ->addIndexColumn()
                ->addColumn('action', function ($data) {

                    $btn = '';
                    $btn .= '<span class="action-icon-spacing">';
                    $btn .= '<a href="' . route('admin:cgt_grades.edit', encrypt($data->id)) . '" title="Edit">
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
        return view('admin.modules.cgt-grades.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.modules.cgt-grades.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequest $request)
    {
        $cgtGradeDetails = $request->validated();
        $result = $this->cgtGrade->createCgtGrade($cgtGradeDetails);

        if (!$result) {
            return back()->with('error', 'Something went wrong, try again!');
        }
        return redirect()->route('admin:cgt_grades')->with('success', 'The CGT Grade created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $id = decrypt($id);
        $cgtGrade = $this->cgtGrade->getCgtGrade($id);
        return view('admin.modules.cgt-grades.edit', compact('cgtGrade'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRequest $request, $id)
    {
        $id = decrypt($id);
        $cgtGradeDetails = $request->validated();
        $result = $this->cgtGrade->updateCgtGrade($id, $cgtGradeDetails);

        if (!$result) {
            return back()->with('error', 'Something went wrong, try again!');
        }
        return redirect()->route('admin:cgt_grades')->with('success', 'The CGT Grade updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $id = $request->cgt_id;
        $result = $this->cgtGrade->destroyCgtGrade($id);

        if (!$result) {
            return response()->json(['status' => false, 'message' => 'Something went wrong, try again!', 404]);
        }
        return response()->json(['status' => true, 'message' => 'CGT Grade deleted successfully.'], 200);
    }
}
