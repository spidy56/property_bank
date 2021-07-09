<?php

namespace App\Http\Controllers\Employee;

use Illuminate\Http\Request;
use App\Models\Property;
use App\Models\Image;
use App\Models\Video;
use App\Models\Assign;
use App\Models\User;
use App\Models\Admin;
use App\Models\Lead;
use App\Models\LeadAssign;
use Illuminate\Support\Facades\Auth;

class LeadController extends Controller
{
    public function index($property_id)
    {
        $property_name = Property::where("id", $property_id)->value("name");
    	return view('employee.leads.leads')->with(["property_id" => $property_id, "property_name" => $property_name]);
    }

    public function submitLead(Request $req)
    {
        $lead = new Lead;
        
        $user_id = Auth::user()->id;
        $creator_role = Auth::user()->role;

        $lead->lead_name = $req->lead_name;
        $lead->mobile_no = $req->mobile_no;
        $lead->address = $req->address;
        $lead->comment = $req->comment;
        $lead->added_from = 'property';
        $lead->added_by = $user_id;
        $lead->user_role = $creator_role;
        $lead->propertyid = $req->property_id;

        $query = $lead->save();

        $leadAssign = new LeadAssign;
        $leadAssign->lead_id = $lead->id;
        $leadAssign->user_id = $user_id;
        $leadAssign->property_id = $req->property_id;
        $leadAssign->creator = $user_id;
        $leadAssign->creator_role = $creator_role;
        $leadAssign->assignee_role = $creator_role;
        $leadAssign->save();

        if ($query)
        {
            return response()->json([
                "status" => true,
                'message' => 'Lead created successfully !'
            ]);
        }
        else
        {
            return response()->json([
                "status" => false,
                'message' => 'Something went wrong !'
            ]);
        }
    }

    public function leadServerSideTable(Request $request)
    {
        if($request->ajax()){

            $urlvalue = $request->urlvalue;

            $query = Lead::query();

            $leadAssign = LeadAssign::where(function ($q) use($urlvalue){
                $q->whereRaw("find_in_set($urlvalue, property_id)");
                $q->where("is_active", "Yes");
                $q->where("creator_role", "admin");
                $q->where("assignee_role", "user");
                $q->where("user_id", Auth::user()->id);
            })
            ->orWhere(function ($q2) use($urlvalue){
                $q2->whereRaw("find_in_set($urlvalue, property_id)");
                $q2->where("is_active", "Yes");
                $q2->where("creator_role", "user");
                $q2->where("user_id", Auth::user()->id);
            })
            ->groupBy("lead_id")
            ->pluck("lead_id");

            $query->whereIn("id", $leadAssign);

            if($request->viewfiler != 'All'){
                if($request->viewfiler == 'ListedByMe'){
                    $query->where("user_role", "user");
                    $query->where("added_by", Auth::user()->id);
                }
                else
                {
                    $query->where("user_role", "admin");
                }
            }

            $query->where("is_active", "Yes")->orderBy("id", "DESC");
            $rows = $query->get();

            foreach($rows as $data)
            {
                if ($data->added_by==Auth::user()->id && $data->user_role=="user")
                {

                    $editButton = '<button style="margin-left: 5px;" class="btn btn-sm d-none d-sm-block float-left btn-light-success edit-lead-btn" data-toggle="tooltip" data-placement="top" title="Edit Lead" data-id="'. $data->id .'"><i class="cursor-pointer bx bx-edit font-small-3 mr-50"></i><span>Edit</span></button>';
                    $editButton .= '<button class="btn btn-sm d-block d-sm-none btn-block text-center btn-light-success edit-lead-btn" data-toggle="tooltip" data-placement="top" title="Edit Lead" data-id="'. $data->id .'"> <i class="cursor-pointer bx bx-edit font-small-3 mr-25"></i><span>Edit</span></button></div>';

                    // $editButton .= '<button class="btn btn-sm d-block d-sm-none btn-block text-center btn-light-success edit-lead-btn" data-toggle="tooltip" data-placement="top" title="Edit Lead" data-id="'. $data->id .'"> <i class="cursor-pointer bx bx-edit font-small-3 mr-25 d-block d-sm-none"></i><span class="d-none d-sm-block">Edit</span></button></div>';

                    $deleteButton = '<button class="btn btn-sm d-none d-sm-block float-right btn-light-danger delete-lead-btn" data-toggle="tooltip" data-placement="top" title="Delete lead" data-id="'. $data->id .'"><i class="cursor-pointer bx bx-trash font-small-3 mr-50"></i><span>Delete</span></button>';
                    $deleteButton .= '<button class="btn btn-sm d-block d-sm-none btn-block text-center btn-light-danger delete-lead-btn" style="margin-top: 33px !important;" data-toggle="tooltip" data-placement="top" title="Delete lead" data-id="'. $data->id .'"> <i class="cursor-pointer bx bx-trash font-small-3 mr-25"></i><span>Delete</span></button></div>';
                    // $deleteButton .= '<button class="btn btn-sm d-block d-sm-none btn-block text-center btn-light-danger delete-lead-btn" style="margin-top: 33px !important;" data-toggle="tooltip" data-placement="top" title="Delete lead" data-id="'. $data->id .'"> <i class="cursor-pointer bx bx-trash font-small-3 mr-25 d-block d-sm-none"></i><span class="d-none d-sm-block">Delete</span></button></div>';
                }
                else
                {
                    $editButton = 'NA';
                    $deleteButton = 'NA';
                }

                $data->editbtn = $editButton;
                $data->deletebtn = $deleteButton;
            }

            return response()->json($rows);
        }
    }
    
    public function getLead(Request $request)
    {
        $id = $request->id;
        $lead = Lead::where('id', $id)->first();
        return response()->json($lead);
    }

    public function updateLead(Request $req)
    {
        $lead = Lead::find($req->id);

        $lead->lead_name = $req->lead_name;
        $lead->mobile_no = $req->mobile_no;
        $lead->address = $req->address;
        $lead->comment = $req->comment;
        $query = $lead->save();

        if ($query)
        {
            return response()->json([
                "status" => true,
                'message' => 'Lead updated successfully !'
            ]);
        }
        else
        {
            return response()->json([
                "status" => false,
                'message' => 'Something went wrong !'
            ]);
        }
    }

    public function deleteLead(Request $request)
    {
        $id = $request->id;

        $lead = Lead::find($id);
        $lead->is_active = "No";
        $query = $lead->save();
        
        if ($query)
        {
            return response()->json([
                "status" => true,
                'message' => 'Lead deleted successfully !'
            ]);
        }
        else
        {
            return response()->json([
                "status" => false,
                'message' => 'Something went wrong !'
            ]);
        }
    }

    public function globalLeads(Request $request)
    {
        $user_id = Auth::user()->id;
        // $assignedProperties = Assign::join("properties as p", "p.id", "=", "assigns.property_id", "left")->select("p.id", "p.name")->where("assigns.user_id", $user_id)->where("assigns.is_active", "Active")->where("p.user_role", "admin")->where("p.is_active", "Active")->where("p.status", "Available")->get();

        $properties = Property::select("id", "name")->where("created_by", $user_id)->where("user_role", "user")->where("is_active", "Active")->where("status", "Available")->get();

        // foreach($assignedProperties as $assignedProperty) {
        //     $properties->add($assignedProperty);
        // }
        return view('employee.leads.global_leads')->with(["properties" => $properties]);
    }

    public function globalLeadServerSideTable(Request $request)
    {
        if($request->ajax()){

            // $urlvalue = $request->urlvalue;

            $query = Lead::query();

            $leadAssign = LeadAssign::where(function ($q){
                // $q->whereRaw("find_in_set($urlvalue, property_id)");
                $q->where("is_active", "Yes");
                $q->where("creator_role", "admin");
                $q->where("assignee_role", "user");
                $q->where("user_id", Auth::user()->id);
            })
            ->orWhere(function ($q2){
                // $q2->whereRaw("find_in_set($urlvalue, property_id)");
                $q2->where("is_active", "Yes");
                $q2->where("creator_role", "user");
                $q2->where("user_id", Auth::user()->id);
            })
            ->groupBy("lead_id")
            ->pluck("lead_id");

            $query->whereIn("id", $leadAssign);

            if($request->viewfiler != 'All'){
                if($request->viewfiler == 'ListedByMe'){
                    $query->where("user_role", "user");
                    $query->where("added_by", Auth::user()->id);
                }
                else
                {
                    $query->where("user_role", "admin");
                }
            }

            $query->where("is_active", "Yes")->orderBy("id", "DESC");
            $rows = $query->get();

            foreach($rows as $data)
            {
                if(isset($data->propertyid))
                {
                    $assignedProperty = LeadAssign::where("lead_id", $data->id)->where("user_id", Auth::user()->id)->where("is_active", "Yes")->where("assignee_role", "user")->value("property_id");
                    $propertyIds = explode(",", $assignedProperty);
                    $property = Property::select("id", "name")->where("is_active", "Active")->whereIn("id", $propertyIds)->get();
                }
                else
                {
                    $property = "NA";
                }
                $data->property = $property;

                if ($data->added_by==Auth::user()->id && $data->user_role=="user")
                {

                    $editButton = '<button style="margin-left: 5px;" class="btn btn-sm d-none d-sm-block float-left btn-light-success edit-lead-btn" data-toggle="tooltip" data-placement="top" title="Edit Lead" data-id="'. $data->id .'"><i class="cursor-pointer bx bx-edit font-small-3 mr-50"></i><span>Edit</span></button>';
                    $editButton .= '<button class="btn btn-sm d-block d-sm-none btn-block text-center btn-light-success edit-lead-btn" data-toggle="tooltip" data-placement="top" title="Edit Lead" data-id="'. $data->id .'"> <i class="cursor-pointer bx bx-edit font-small-3 mr-25"></i><span>Edit</span></button></div>';

                    $deleteButton = '<button class="btn btn-sm d-none d-sm-block float-right btn-light-danger delete-lead-btn" data-toggle="tooltip" data-placement="top" title="Delete lead" data-id="'. $data->id .'"><i class="cursor-pointer bx bx-trash font-small-3 mr-50"></i><span>Delete</span></button>';
                    $deleteButton .= '<button class="btn btn-sm d-block d-sm-none btn-block text-center btn-light-danger delete-lead-btn" style="margin-top: 33px !important;" data-toggle="tooltip" data-placement="top" title="Delete lead" data-id="'. $data->id .'"> <i class="cursor-pointer bx bx-trash font-small-3 mr-25"></i><span>Delete</span></button></div>';
                }
                else
                {
                    $editButton = 'NA';
                    $deleteButton = 'NA';
                }

                $data->editbtn = $editButton;
                $data->deletebtn = $deleteButton;
            }

            return response()->json($rows);
        }
    }

    public function submitGlobalLead(Request $req)
    {
        $lead = new Lead;
        
        $user_id = Auth::user()->id;
        $creator_role = Auth::user()->role;

        $lead->lead_name = $req->lead_name;
        $lead->mobile_no = $req->mobile_no;
        $lead->address = $req->address;
        $lead->comment = $req->comment;
        $lead->added_from = 'global';
        $lead->added_by = $user_id;
        $lead->user_role = $creator_role;
        $lead->propertyid = $req->property_id;

        $query = $lead->save();

        $leadAssign = new LeadAssign;
        $leadAssign->lead_id = $lead->id;
        $leadAssign->user_id = $user_id;
        $leadAssign->property_id = $req->property_id;
        $leadAssign->creator = $user_id;
        $leadAssign->creator_role = $creator_role;
        $leadAssign->assignee_role = $creator_role;
        $leadAssign->save();

        if ($query)
        {
            return response()->json([
                "status" => true,
                'message' => 'Lead created successfully !'
            ]);
        }
        else
        {
            return response()->json([
                "status" => false,
                'message' => 'Something went wrong !'
            ]);
        }
    }

    public function updateGlobalLead(Request $req)
    {
        $lead = Lead::find($req->id);

        $lead->lead_name = $req->lead_name;
        $lead->mobile_no = $req->mobile_no;
        $lead->address = $req->address;
        $lead->comment = $req->comment;
        $lead->propertyid = $req->property_id;
        $query = $lead->save();

        if ($query)
        {
            return response()->json([
                "status" => true,
                'message' => 'Lead updated successfully !'
            ]);
        }
        else
        {
            return response()->json([
                "status" => false,
                'message' => 'Something went wrong !'
            ]);
        }
    }

}
