<?php

namespace App\Http\Controllers\Admin;

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
        $adminid = Auth::user()->id;
        $users = User::select("id", "name")->where("is_active", "Active")->where("status", 'Unblocked')->get();
        $property_name = Property::where("id", $property_id)->value("name");
        $associates = User::select("id", "name", "contact_no")->where("status", "Unblocked")->where("is_active", "Active")->get();

        $properties = Property::select("id", "name")->where("status", "Available")->where("is_active", "Active")->where("created_by", $adminid)->where("user_role", "admin")->get();

    	return view('admin.leads.leads')->with(["property_id" => $property_id, "associates" => $associates, "property_name" => $property_name, "users" => $users, "properties" => $properties]);
    }

    public function leads()
    {
        $adminid = Auth::user()->id;
        $users = User::select("id", "name")->where("is_active", "Active")->where("status", 'Unblocked')->get();
        $associates = User::select("id", "name", "contact_no")->where("status", "Unblocked")->where("is_active", "Active")->get();
        $properties = Property::select("id", "name")->where("status", "Available")->where("is_active", "Active")->where("created_by", $adminid)->where("user_role", "admin")->get();
    	return view('admin.leads.leads')->with(["associates" => $associates, "users" => $users, "properties" => $properties]);
    }

    public function submitLead(Request $req)
    {
        $lead = new Lead;
        
        $user_id = Auth::user()->id;
        $creator_role = Auth::user()->role;

        if($req->property_add_type=='property')
        {
            $added_from = 'property';
            $propertyIIIID = $req->propertyiid;
        }
        else
        {
            $added_from = 'global';
            $propertyIIIID = $req->property_id;
        }

        $lead->lead_name = $req->lead_name;
        $lead->mobile_no = $req->mobile_no;
        $lead->address = $req->address;
        $lead->comment = $req->comment;
        $lead->added_from = $added_from;
        $lead->added_by = $user_id;
        $lead->user_role = $creator_role;
        $lead->propertyid = $propertyIIIID;

        $query = $lead->save();

        $leadAssign = new LeadAssign;
        $leadAssign->lead_id = $lead->id;
        $leadAssign->user_id = $user_id;
        $leadAssign->property_id = $propertyIIIID;
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

            $query = Lead::query();          

            if(!empty($request->urlvalue))
            {
                $leadAssign = LeadAssign::whereRaw("find_in_set($request->urlvalue, property_id)")
                ->where("is_active", "Yes")
                ->groupBy("lead_id")
                ->pluck("lead_id");

                $query->where("propertyid", "!=", NULL);
                $query->whereIn("id", $leadAssign);
            }

            $viewfiler = $request->viewfiler;
            $userfiler = $request->userfiler;  

            if($viewfiler != 'All'){
                if($viewfiler == 'ListedByMe'){
                    $query->where("user_role", "admin");
                    $query->where("added_by", Auth::user()->id);
                }
                else
                {
                    $query->where("user_role", "user");
                }
            }

            if( !is_null($userfiler)){
                $query->where("added_by", $userfiler);
            // dd("userfiler", $userfiler);
            }

            $query->where("is_active", "Yes");

            $query->orderBy("id", "DESC");
            $rows = $query->get();

            return datatables()->of($rows)->addIndexColumn()
            ->addColumn('lead_id', function($data){
                return $data->id;
            })
            ->addColumn('lead_name', function($data){
                return $data->lead_name;
            })
            ->addColumn('mobile_no', function($data){
                $mobile = '<div style="width: 200px;"><a href="tel:'.$data->mobile_no.'" data-toggle="tooltip" data-placement="top" title="Call Now">'.$data->mobile_no.'</a> <span><a href="tel:'.$data->mobile_no.'" class="btn" data-toggle="tooltip" data-placement="top" title="Call Now"><img src="'.asset("assets/img/call_now.png").'" height="30" alt="Call Now"></span></div>';
                return $mobile;
            })
            ->addColumn('address', function($data){
                return $data->address;
            })
            ->addColumn('comment', function($data){
                return $data->comment;
            })
            ->addColumn('property', function($data){
                if (!is_null($data->propertyid)) {
                    $propertyName = Property::where("id", $data->propertyid)->where("is_active", "Active")->value("name");
                    $property = '<a href="leads/'.$data->propertyid.'">'.$propertyName.'</a>';
                }
                else
                {
                    $property = "Globally Added";
                }
                return $property;
            })
            ->addColumn('creator', function($data){
                // return $data->added_by;
                if ($data->user_role=="admin") {
                    if ($data->added_by==Auth::user()->id) {
                        $user = "Added By Me";
                    }
                    else
                    {
                        $user = Admin::where("id", $data->added_by)->where("status", "Unblocked")->where("is_active", "Active")->value("name");
                    }
                }
                else
                {
                    $user = User::where("id", $data->added_by)->where("status", "Unblocked")->where("is_active", "Active")->value("name");
                }
                return $user;
            })
            ->addColumn('actions', function($data){
                if($data->user_role=="admin")
                {
                    $button = '<div class="ui" style="width: 150px;">';
                    $button .= ' <button class="btn btn-sm btn-success edit-lead-btn" data-toggle="tooltip" data-placement="top" title="Edit Lead" data-id="'. $data->id .'"><i class="bx bx-edit" aria-hidden="true"></i></button>';
                    $button .= ' <button data-id="'. $data->id .'" class="btn btn-sm btn-danger delete-lead-btn" data-toggle="tooltip" data-placement="top" title="Delete lead"><i class="bx bx-trash"></i></button>';
                    $button .= ' <button data-id="'. $data->id .'" class="btn btn-sm btn-primary assign-lead-btn" data-toggle="tooltip" data-placement="top" title="Assign Lead"><i class="bx bxs-send" aria-hidden="true"></i></button>';
                    $button .= '</div>';
                }
                else
                {
                    $button = "NA";
                }
                return $button;
            })
            
            ->rawColumns(['lead_id', 'lead_name', 'mobile_no', 'address', 'comment', 'property', 'creator', 'actions'])
                ->make(true);
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

        if($req->property_add_type=='property')
        {
            $lead->propertyid = $req->propertyiid;
        }
        else
        {
            $lead->propertyid = $req->property_id;

        }
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

    public function loadAssignedLeads(Request $request)
    {
        $ids = [];
        $user_id = Auth::user()->id;
        $user_role = Auth::user()->role;
        $lead_id = $request->input("leadid");
        $data['users'] = LeadAssign::select("lead_assigns.lead_id", "lead_assigns.user_id", "lead_assigns.creator", "lead_assigns.id as assign_id", "u.id as userid", "u.name as username")
            ->join("users as u", "lead_assigns.user_id", "=", "u.id", "left")
            ->where("lead_assigns.lead_id", $lead_id)
            ->where("lead_assigns.is_active", "Yes")
            ->where("lead_assigns.creator", $user_id)
            ->where("lead_assigns.creator_role", $user_role)
            ->where("lead_assigns.assignee_role", "!=", $user_role)
            ->where("u.is_active", "Active")
            ->where("u.status", "Unblocked")
            ->get();

        // dd($data['users']);

        foreach ($data['users'] as $value)
        {
            $ids[] = $value->user_id;
        }

        $detail = LeadAssign::select("lead_id", "user_id", "creator", "id as assign_id", "creator_role")
            ->where("lead_id", $lead_id)
            ->where("is_active", "Yes")
            ->where("user_id", $user_id)
            ->where("creator", $user_id)
            ->where("creator_role", $user_role)
            ->first();

        $data['detail'] = $detail;
        
        $data['options'] = User::select("id", "name", "contact_no")
            ->whereNotIn("id", $ids)
            ->where("is_active", "Active")
            ->where("status", "Unblocked")
            ->get();

        return response()->json($data);
    }

    public function getAssociateProperty(Request $request)
    {

        $user_id = $request->id;

        $assignedProperties = Assign::join("properties as p", "p.id", "=", "assigns.property_id", "left")
        ->select("p.id", "p.name", "p.location")
        ->where(function($query) use($user_id){
            $query->where('p.created_by', $user_id);
            $query->where('p.user_role', 'user');
        })
        ->orWhere(function($query2) use($user_id){
            $query2->where("assigns.user_id", $user_id);
            $query2->where("assigns.is_active", "Active");
            $query2->where("p.user_role", "admin");
            $query2->where("p.is_active", "Active");
            $query2->where("p.status", "Available");
        })
        ->get();


        $myproperties = Property::select("id", "name", "location")->where('created_by', $user_id)->where('user_role', 'user')->where("is_active", "Active")->where("status", "Available")->get();

        foreach($assignedProperties as $assignedProperty) {
            $myproperties->add($assignedProperty);
        }

        $properties = $myproperties;

        foreach($properties as $property)
        {
            $property->name = ucfirst($property->name);
            $property->location = ucfirst($property->location);
        }

        $role = User::where('id', $user_id)->value("role");

        $data['properties'] = $properties;
        $data['role'] = $role;

        return response()->json($data);
    }

    public function saveAssignedProperty(Request $request)
    {
        if (!empty($request->assign_id)) {
            $assign = LeadAssign::find($request->assign_id);
        }
        else
        {
            $assign = new LeadAssign;
        }

        $assign->lead_id = $request->lead_id;
        $assign->user_id = $request->user_id;
        $assign->property_id = $request->property_ids;
        $assign->creator = $request->creator_id;
        $assign->creator_role = $request->creator_role;
        $assign->assignee_role = $request->assignee_role;
        $query = $assign->save();
        
        if ($query)
        {
            return response()->json([
                "status" => true,
                'message' => 'Lead assigned successfully !'
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

    public function checkAssignedUsersAvailability(Request $request)
    {
        $id = $request->id;
        $assign = LeadAssign::select("lead_id", "user_id", "property_id", "creator", "id as assign_id", "creator_role", "assignee_role")
            ->where("id", $id)
            ->where("is_active", "Yes")
            ->first();

        if($assign)
        {
            // $properties = Property::select("id", "name", "location")->where('created_by', $assign->user_id)->where('user_role', 'user')->get();
            $assignedProperties = Assign::join("properties as p", "p.id", "=", "assigns.property_id", "left")->select("p.id", "p.name", "p.location")->where("assigns.user_id", $assign->user_id)->where("assigns.is_active", "Active")->where("p.user_role", "admin")->where("p.is_active", "Active")->where("p.status", "Available")->get();

            $myproperties = Property::select("id", "name", "location")->where('created_by', $assign->user_id)->where('user_role', 'user')->where("is_active", "Active")->where("status", "Available")->get();

            foreach($assignedProperties as $assignedProperty) {
                $myproperties->add($assignedProperty);
            }

            $properties = $myproperties;
            // dd($properties);

            foreach($properties as $property)
            {
                $property->name = ucfirst($property->name);
                $property->location = ucfirst($property->location);
            }
            $data["properties"] = $properties;
        }

        $data["assign"] = $assign;
            
        return response()->json($data);
    }

}
