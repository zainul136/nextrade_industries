<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\StoreRequest;
use App\Http\Requests\Customer\UpdateRequest;
use App\Http\Requests\SampleRequest\StoreRequest as storeSampleRequest;
use App\Http\Requests\Order\UpdateRequest as updateOrderRequest;
use App\Mail\CustomerEmail;
use App\Models\Customer;
use App\Models\OrderFiles;
use App\Models\OrderStatus;
use App\Models\SampleRequest;
use App\Models\ScanInLog;
use App\Models\ScanOutInventory;
use App\Models\ScanOutLog;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use DataTables;
use Illuminate\Support\Facades\Mail;

class CustomerController extends Controller
{
    public Customer $customer;
    public SampleRequest $sampleRequest;

    const PRELOADED = 'preloaded';
    const LOADED = 'loaded';
    const SHIPPED = 'shipped';
    const SCANOUT = 1;

    public function __construct(Customer $customer, SampleRequest $sampleRequest)
    {
        $this->middleware('check_customer_permission');
        $this->customer = $customer;
        $this->sampleRequest = $sampleRequest;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $customers = $this->customer->getAllCustomers();
            return Datatables::of($customers)
                ->addIndexColumn()
                ->addColumn('action', function ($data) {

                    $btn = '';
                    $btn .= '<span class="action-icon-spacing">';
                    $btn .= '<a href="' . route('admin:customers.edit', encrypt($data->id)) . '" title="Edit">
                                          <i class="fa fa-edit text-primary"></i>
                                      </a>';
                    $btn .= '<a href="javascript:void(0);" class="add_sample" customer-id="' . $data->id . '" title="Add Sample">
                                      <i class="fa fa-plus text-info"></i>
                                  </a>';
                    $btn .= '<a  href="' . route('admin:customers.samples', $data->id) . '" title="Samples List">
                                      <i class="fa fa-file text-info"></i>
                                  </a>';
                    $btn .= '<a  href="' . route('admin:orders', $data->id) . '" title="Orders List">
                                    <i class="fa fa-shopping-cart" aria-hidden="true"></i>
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
        return view('admin.modules.customers.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.modules.customers.create');
    }

    /**v
     * Store a newly created resource in storage.
     */
    public function store(StoreRequest $request)
    {
        $customerDetails = $request->validated();
        $result = $this->customer->createCustomer($customerDetails);

        if (!$result) {
            return back()->with('error', 'Something went wrong, try again!');
        }
        return redirect()->route('admin:customers')->with('success', 'Customer created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $id = decrypt($id);
        $customer = $this->customer->getCustomer($id);
        return view('admin.modules.customers.edit', compact('customer'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRequest $request, $id)
    {
        $id = decrypt($id);
        $customerDetails = $request->validated();
        $result = $this->customer->updateCustomer($id, $customerDetails);

        if (!$result) {
            return back()->with('error', 'Something went wrong, try again!');
        }
        return redirect()->route('admin:customers')->with('success', 'Customer updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $id = $request->customer_id;
        $result = $this->customer->destroyCustomer($id);
        if (!isset($result)) {
            return response()->json(['status' => false, 'message' => 'Something went wrong! try again'], 404);
        }
        return response()->json(['status' => true, 'message' => 'Customer deleted successfully'], 200);
    }

    public function addSample(storeSampleRequest $request)
    {
        $sampleDetails = $request->validated();
        $result = $this->sampleRequest->createSampleRequest($sampleDetails);
        if (!isset($result)) {
            return response()->json(['status' => false, 'message' => 'Something went wrong! try again'], 404);
        }
        return response()->json(['status' => true, 'message' => 'Sample Request added successfully'], 200);
    }

    public function samples($id)
    {
        $customer = Customer::find($id);
        $samples = SampleRequest::where('customer_id', $id)->orderBy('id', 'desc')->get();
        return view('admin.modules.customers.samples', compact('samples', 'customer'));
    }

    public function sendEmail(Request $request)
    {
        // Validate the request data (email addresses and email content)
        $request->validate([
            'emails' => 'required|string', // Change to a single string
            'emailContent' => 'required|string',
        ]);

        // Extract the emails from the string and convert to an array
        $emails = explode(',', $request->input('emails'));
        $emails = array_map('trim', $emails);

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imagePath = public_path('assets/images');
            $imageFileName = time() . '_' . $image->getClientOriginalName();
            $image->move($imagePath, $imageFileName);
            $imageUrl = asset('assets/images/' . $imageFileName);
        } else {
            $imageUrl = null;
        }

        $emailContent = $request->input('emailContent');
        foreach ($emails as $email) {
            Mail::to($email)->send(new CustomerEmail($emailContent, $imageUrl));
        }
        return response()->json(['status' => true, 'message' => 'Emails sent successfully']);
    }



}
