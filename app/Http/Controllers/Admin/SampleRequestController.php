<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SampleRequest\StoreRequest;
use App\Http\Requests\Customer\StoreRequest as CustomerRequest;
use App\Http\Requests\SampleRequest\UpdateRequest;
use App\Models\Customer;
use App\Models\SampleRequest;
use Illuminate\Http\Request;
use DataTables;


class SampleRequestController extends Controller
{

    public SampleRequest $sampleRequest;
    public Customer $customer;

    public function __construct(SampleRequest $sampleRequest, Customer $customer)
    {
        $this->sampleRequest = $sampleRequest;
        $this->customer = $customer;
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $sampleRequest = $this->sampleRequest->getSamplerequests();
            return Datatables::of($sampleRequest)
                ->addIndexColumn()
                ->addColumn('action', function ($data) {

                    $btn = '';
                    $btn .= '<span class="action-icon-spacing">';
                    $btn .= '<a href="' . route('admin:sampleRequests.edit', $data->id) . '" title="Edit">
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
        return view('admin.modules.sample-requests.index');
    }

    public function create()
    {
        $customers = Customer::all();
        return view('admin.modules.sample-requests.create', compact('customers'));
    }

    public function store(StoreRequest $request)
    {
        $sampleDetails = $request->validated();
        $result = $this->sampleRequest->createSampleRequest($sampleDetails);

        if (!$result) {
            return back()->with('error', 'Something went wrong! try again.');
        }
        return redirect()->route('admin:sampleRequests')->with('success', 'Sample Request created successfully.');
    }

    public function edit($id)
    {
        $customers = Customer::all();
        $sample = $this->sampleRequest->getSample($id);
        return view('admin.modules.sample-requests.edit', compact('sample', 'customers'));
    }

    public function update(UpdateRequest $request, $id)
    {
        $sampleDetails = $request->validated();
        $result = $this->sampleRequest->updateSampleRequest($id, $sampleDetails);
        if (!$result) {
            return back()->with('error', 'Something went wrong! try again.');
        }
        return redirect()->route('admin:sampleRequests')->with('success', 'Sample updated successfully.');
    }

    public function destroy(Request $request)
    {
        $id = $request->sample_id;
        $result = $this->sampleRequest->destroySample($id);
        if (!$result) {
            return response()->json(['status' => false, 'message' => 'Something went wrong! try again'], 404);
        }
        return response()->json(['status' => true, 'message' => 'Sample deleted successfully'], 200);
    }

    public function addCustomer(CustomerRequest $request)
    {
        $customerDetails = $request->validated();
        $result = $this->customer->createCustomer($customerDetails);
        if ($result) {
            return response()->json(['status' => true, 'data' => ['id' => $result->id, 'name' => $result->name]]);
        } else {
            $errors = $request->errors();
            return response()->json(['status' => false, 'errors' => $errors]);
        }
    }
}
