<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\NTGrade\StoreRequest;
use App\Http\Requests\NTGrade\UpdateRequest;
use App\Models\NTGrade;
use Illuminate\Http\Request;
use DataTables;

class NTGradeController extends Controller
{
    public NTGrade $ntGrade;

    public function __construct(NTGrade $ntGrade)
    {
        $this->middleware('check_nt_permission');
        $this->ntGrade = $ntGrade;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $ntGrades = $this->ntGrade->getAllNtGrades();
            return Datatables::of($ntGrades)
                ->addIndexColumn()
                ->addColumn('action', function ($data) {
                    $btn = '';
                    $btn .= '<span class="action-icon-spacing">';
                    $btn .= '<a href="' . route('admin:nt_grades.edit', encrypt($data->id)) . '" title="Edit">
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
        return view('admin.modules.nt-grades.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.modules.nt-grades.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequest $request)
    {
        $ntGradeDetails = $request->validated();
        $result = $this->ntGrade->createNtGrade($ntGradeDetails);

        if (!$result) {
            return back()->with('error', 'Something went wrong, try again!');
        }
        return redirect()->route('admin:nt_grades')->with('success', 'The NT Grade created successfully.');
    }
    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $id = decrypt($id);
        $ntGrade = $this->ntGrade->getNtGrade($id);
        return view('admin.modules.nt-grades.edit', compact('ntGrade'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRequest $request, $id)
    {
        $id = decrypt($id);
        $ntGradeDetails = $request->validated();
        $result = $this->ntGrade->updateNtGrade($id, $ntGradeDetails);

        if (!$result) {
            return back()->with('error', 'Something went wrong, try again!');
        }
        return redirect()->route('admin:nt_grades')->with('success', 'The NT Grade updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $id = $request->nt_id;
        $result = $this->ntGrade->destroyNtGrade($id);

        if (!$result) {
            return response()->json(['status' => false, 'message' => 'Something went wrong, try again!', 404]);
        }
        return response()->json(['status' => true, 'message' => 'NT Grade deleted successfully.'], 200);
    }
}
