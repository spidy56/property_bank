<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Property;
use App\Models\Assign;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class EmployeeController extends Controller
{
    public function index()
    {
        $data['properties'] = Property::select("id", "name")->where("is_active", "Active")->where("user_role", "admin")->get();
    	return view('admin.employee.employees', $data);
    }

    public function newEmployee()
    {
        return view('admin.employee.new_employee');
    }

    public function submitEmployeeData(Request $req)
    {
        $profile_pic = Null;
        $pan_card_pic = Null;
        $aadhar_card_front = Null;
        $aadhar_card_back = Null;

        // Query to upload image if selected
        if ($req->hasfile('profile_pic')) {
            $profile_pic_file = $req->file('profile_pic');
            // $profile_pic = $req->file('profile_pic')->getClientOriginalName();
            $ext = $req->file('profile_pic')->getClientOriginalExtension();
            $profile_pic = rand(111111, 999999).'_profile.'.$ext;
            $profile_pic_file->storeAs('public/profile_pics', $profile_pic);
        }

         // Query to upload image if selected
        if ($req->hasfile('pan_card_pic')) {
            $pan_card_pic_file = $req->file('pan_card_pic');
            // $pan_card_pic = $req->file('pan_card_pic')->getClientOriginalName();
            $ext = $req->file('pan_card_pic')->getClientOriginalExtension();
            $pan_card_pic = rand(111111, 999999).'_pan_card.'.$ext;
            $pan_card_pic_file->storeAs('public/pan_card_pics', $pan_card_pic);
        }

        // Query to upload image if selected
        if ($req->hasfile('aadhar_card_front')) {
            $aadhar_card_front_file = $req->file('aadhar_card_front');
            // $aadhar_card_front = $req->file('aadhar_card_front')->getClientOriginalName();
            $ext = $req->file('aadhar_card_front')->getClientOriginalExtension();
            $aadhar_card_front = rand(111111, 999999).'_aadhar_front.'.$ext;
            $aadhar_card_front_file->storeAs('public/aadhar_cards', $aadhar_card_front);
        }

         // Query to upload image if selected
        if ($req->hasfile('aadhar_card_back')) {
            $aadhar_card_back_file = $req->file('aadhar_card_back');
            // $aadhar_card_back = $req->file('aadhar_card_back')->getClientOriginalName();
            $ext = $req->file('aadhar_card_back')->getClientOriginalExtension();
            $aadhar_card_back = rand(111111, 999999).'_aadhar_back.'.$ext;
            $aadhar_card_back_file->storeAs('public/aadhar_cards', $aadhar_card_back);
        }

        if(!empty($req->experience_category))
        {
            $experience_category = implode(", ", $req->experience_category);
        }
        else
        {
            $experience_category = NULL;
        }

    	$user = new User;

        $user->name = $req->name;
    	$user->username = $req->username;
        $user->profile_pic = $profile_pic;
    	$user->email = $req->email;
        $user->aadhar_card_no = $req->aadhar_card_no;
        $user->aadhar_card_front = $aadhar_card_front;
        $user->aadhar_card_back = $aadhar_card_back;
        $user->pan_card_no = $req->pan_card_no;
        $user->pan_card_pic = $pan_card_pic;
        $user->experience_year = $req->experience_year;
        $user->experience_category = $experience_category;
        $user->is_rera_registered = $req->is_rera_registered;
        $user->rera_reg_no = $req->rera_reg_no;
        $user->contact_no = $req->contact_no;
    	$user->password = Hash::make($req->password);

    	$user->save();

        $associate_id = $user->id;

        $associateID = "PBAS".str_pad($associate_id, 4, '0', STR_PAD_LEFT);

        $updateUser = User::find($associate_id);
        $updateUser->associate_id = $associateID;
        $query = $updateUser->save();

    	if ($query)
        {
            return response()->json([
                "status" => true,
                'message' => 'Associate registered successfully !'
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

    public function employeeServerSideTable(Request $request)
    {
        if($request->ajax())
        {
            $query = User::query();
            
            $query->where("is_active", "Active");
            $query->orderBy("id", "DESC");

            $rows = $query->get();

            return datatables()->of($rows)->addIndexColumn()
            ->addColumn('id', function($data){
                return $data->associate_id;
            })
            ->addColumn('name', function($data){
                return $data->name;
            })
            ->addColumn('username', function($data){
                return $data->username;
            })
            // ->addColumn('profile_pic', function($data){
            //     $profile_pic = "NA";
            //     if (!empty($data->profile_pic)) {
            //         $profile_pic_url = asset("storage/profile_pics/".$data->profile_pic);
            //         $profile_pic = '<a href="'.$profile_pic_url.'" target="_BLANK"><img src="'.$profile_pic_url.'" width="70" height="auto"></a>';
            //     }
            //     return $profile_pic;
            // })
            ->addColumn('email', function($data){
            	return $data->email;
            })
            ->addColumn('contact_no', function($data){
                return $data->contact_no;
            })
            // ->addColumn('aadhar_card_no', function($data){
            //     return $data->aadhar_card_no;
            // })
            // ->addColumn('aadhar_card_front', function($data){
            //     $aadhar_card_front = "NA";
            //     if (!empty($data->aadhar_card_front)) {
            //         $aadhar_card_front_url = asset("storage/aadhar_cards/".$data->aadhar_card_front);
            //         $aadhar_card_front = '<a href="'.$aadhar_card_front_url.'" target="_BLANK"><img src="'.$aadhar_card_front_url.'" width="70" height="auto"></a>';
            //     }
            //     return $aadhar_card_front;
            // })
            // ->addColumn('aadhar_card_back', function($data){
            //     $aadhar_card_back = "NA";
            //     if (!empty($data->aadhar_card_back)) {
            //         $aadhar_card_back_url = asset("storage/aadhar_cards/".$data->aadhar_card_back);
            //         $aadhar_card_back = '<a href="'.$aadhar_card_back_url.'" target="_BLANK"><img src="'.$aadhar_card_back_url.'" width="70" height="auto"></a>';
            //     }
            //     return $aadhar_card_back;
            // })
            // ->addColumn('pan_card_no', function($data){
            //     return $data->pan_card_no;
            // }) 
            // ->addColumn('pan_card_pic', function($data){
            //     $pan_card_pic = "NA";
            //     if (!empty($data->pan_card_pic)) {
            //         $pan_card_pic_url = asset("storage/pan_card_pics/".$data->pan_card_pic);
            //         $pan_card_pic = '<a href="'.$pan_card_pic_url.'" target="_BLANK"><img src="'.$pan_card_pic_url.'" width="70" height="auto"></a>';
            //     }
            //     return $pan_card_pic;
            // })          
            ->addColumn('experience_year', function($data){
                return $data->experience_year;
            })
            ->addColumn('experience_category', function($data){
                return $data->experience_category;
            })
            ->addColumn('is_rera_registered', function($data){
                return $data->is_rera_registered;
            })
            ->addColumn('rera_reg_no', function($data){
                return $data->rera_reg_no;
            })
            ->addColumn('created_at', function($data){
                return date("d-m-Y h:i A", strtotime($data->created_at));
            })
            ->addColumn('status', function($data){
                if ($data->status=="Unblocked")
                {
                    $badge = '<div class="badge badge-light-success">Active</div>';
                }
                else
                {
                    $badge = '<div class="badge badge-light-danger">Blocked</div>';
                }
                return $badge;
            })
            
            ->addColumn('actions', function($data){

                $button = '<div class="ui" style="width: 150px !important;">';
                $button .= ' <a href="javascript:void(0);" data-id="'.$data->id.'" class="btn btn-sm btn-info shown-tooltip reset-associate-password-btn" data-toggle="tooltip" data-placement="top" title="Reset Password"><i class="bx bx-key" aria-hidden="true"></i></a>';

                $button .= ' <a href="edit-associate/'. $data->id .'" class="btn btn-sm btn-success" data-toggle="tooltip" data-placement="top" title="Edit associate"><i class="bx bx-edit" aria-hidden="true"></i></a>';

                if ($data->status=="Unblocked")
                {
                    $button .= ' <button data-id="'. $data->id .'" class="btn btn-sm btn-danger block-employee-btn" data-toggle="tooltip" data-placement="top" title="Block associate"><i class="bx bx-block" aria-hidden="true"></i></button>';
                }
                else
                {
                    $button .= ' <button data-id="'. $data->id .'" class="btn btn-sm btn-success unblock-employee-btn" data-toggle="tooltip" data-placement="top" title="Unblock associate"><i class="bx bx-check" aria-hidden="true"></i></button>';
                }

                $button .= ' <button data-id="'. $data->id .'" class="btn btn-sm btn-danger delete-employee-btn" data-toggle="tooltip" data-placement="top" title="Delete associate"><i class="bx bx-trash"></i></button>';

                $button .= ' <button data-id="'. $data->id .'" class="btn btn-sm btn-primary assign-property-btn" data-toggle="tooltip" data-placement="top" title="Assign properties"><i class="bx bxs-send"></i></button>';
                $button .= '</div>';
                return $button;
            })
            
            ->rawColumns(['id', 'name', 'username', 'email', 'contact_no', 'experience_year', 'experience_category', 'is_rera_registered', 'rera_reg_no', 'created_at', 'status', 'actions'])
            // ->rawColumns(['id', 'name', 'username', 'profile_pic', 'email', 'contact_no', 'aadhar_card_no', 'aadhar_card_front', 'aadhar_card_back', 'pan_card_no', 'pan_card_pic', 'experience_year', 'experience_category', 'is_rera_registered', 'rera_reg_no', 'status', 'actions'])
                ->make(true);
        }
    }  


    public function deleteEmployee(Request $request)
    {
        $id = $request->id;

        $user = User::find($id);
        $user->is_active = "Inactive";
        $query = $user->save();
        
        if ($query)
        {
            return response()->json([
                "status" => true,
                'message' => 'Employee deleted successfully !'
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

    public function editEmployee($id)
    {
        $data['user'] = User::where('id', $id)->first();
        return view('admin.employee.edit_employee', $data);
    }

    public function editUserProfile()
    {
        $id = Auth::user()->id;
        $data['user'] = User::where('id', $id)->first();
        return view('admin.employee.edit_employee', $data);
    }

    public function updateEmployeeData(Request $req)
    {

        $profile_pic = "";
        $pan_card_pic = "";
        $aadhar_card_front = "";
        $aadhar_card_back = "";

        // Query to upload image if selected
        if ($req->hasfile('profile_pic')) {
            $profile_pic_file = $req->file('profile_pic');
            // $profile_pic = $req->file('profile_pic')->getClientOriginalName();
            $ext = $req->file('profile_pic')->getClientOriginalExtension();
            $profile_pic = rand(111111, 999999).'_profile.'.$ext;
            $profile_pic_file->storeAs('public/profile_pics', $profile_pic);
        }

         // Query to upload image if selected
        if ($req->hasfile('pan_card_pic')) {
            $pan_card_pic_file = $req->file('pan_card_pic');
            // $pan_card_pic = $req->file('pan_card_pic')->getClientOriginalName();
            $ext = $req->file('pan_card_pic')->getClientOriginalExtension();
            $pan_card_pic = rand(111111, 999999).'_pan_card.'.$ext;
            $pan_card_pic_file->storeAs('public/pan_card_pics', $pan_card_pic);
        }

        // Query to upload image if selected
        if ($req->hasfile('aadhar_card_front')) {
            $aadhar_card_front_file = $req->file('aadhar_card_front');
            // $aadhar_card_front = $req->file('aadhar_card_front')->getClientOriginalName();
            $ext = $req->file('aadhar_card_front')->getClientOriginalExtension();
            $aadhar_card_front = rand(111111, 999999).'_aadhar_front.'.$ext;
            $aadhar_card_front_file->storeAs('public/aadhar_cards', $aadhar_card_front);
        }

         // Query to upload image if selected
        if ($req->hasfile('aadhar_card_back')) {
            $aadhar_card_back_file = $req->file('aadhar_card_back');
            // $aadhar_card_back = $req->file('aadhar_card_back')->getClientOriginalName();
            $ext = $req->file('aadhar_card_back')->getClientOriginalExtension();
            $aadhar_card_back = rand(111111, 999999).'_aadhar_back.'.$ext;
            $aadhar_card_back_file->storeAs('public/aadhar_cards', $aadhar_card_back);
        }

        $user = User::find($req->id);

        $user->name = $req->name;
        $user->username = $req->username;

        if (!empty($profile_pic)) {
            $user->profile_pic = $profile_pic;
        }

        $user->email = $req->email;
        $user->aadhar_card_no = $req->aadhar_card_no;

        if (!empty($aadhar_card_front)) {
            $user->aadhar_card_front = $aadhar_card_front;
        }

        if (!empty($aadhar_card_back)) {
            $user->aadhar_card_back = $aadhar_card_back;
        }

        $user->pan_card_no = $req->pan_card_no;

        if (!empty($pan_card_pic)) {
            $user->pan_card_pic = $pan_card_pic;
        }


        if(!empty($req->experience_category))
        {
            $experience_category = implode(", ", $req->experience_category);
        }
        else
        {
            $experience_category = NULL;
        }

        $user->experience_year = $req->experience_year;
        $user->experience_category = $experience_category;
        $user->is_rera_registered = $req->is_rera_registered;
        $user->rera_reg_no = $req->rera_reg_no;
        $user->contact_no = $req->contact_no;

        $query = $user->save();

        if ($query)
        {
            return response()->json([
                "status" => true,
                'message' => 'Associate details updated successfully !'
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

    public function resetEmployeePassword($id)
    {
        $data['user'] = User::select("id")->where("id", $id)->first();
        return view('admin.employee.reset_password', $data);
    }

    public function submitEmployeePassword(Request $req)
    {
        $user = User::find($req->id);

        $user->password = Hash::make($req->password);

        $query = $user->save();

        if ($query)
        {
            return response()->json([
                "status" => true,
                'message' => 'Employee password reset successfully !'
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


    public function changeEmployeePassword(Request $req)
    {
        $user = User::find($req->id);

        $user->password = Hash::make($req->password);

        $query = $user->save();

        if ($query)
        {
            return response()->json([
                "status" => true,
                'message' => 'Employee password reset successfully !'
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

    public function blockEmployee(Request $request)
    {
        $id = $request->id;

        $user = User::find($id);
        $user->status = "Blocked";
        $query = $user->save();
        
        if ($query)
        {
            return response()->json([
                "status" => true,
                'message' => 'Employee blocked successfully !'
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


    public function unblockEmployee(Request $request)
    {
        $id = $request->id;

        $user = User::find($id);
        $user->status = "Unblocked";
        $query = $user->save();
        
        if ($query)
        {
            return response()->json([
                "status" => true,
                'message' => 'Employee unblocked successfully !'
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

    public function savePermissionData(Request $request)
    {

        if (!empty($request->assign_id)) {
            $assign = Assign::find($request->assign_id);
        }
        else
        {
            $assign = new Assign;
        }

        $assign->user_id = $request->user_id;
        $assign->property_id = $request->property_id;
        $assign->module_ids = $request->module_ids;
        $query = $assign->save();
        
        if ($query)
        {
            return response()->json([
                "status" => true,
                'message' => 'Property assigned successfully !'
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

    public function loadAssignedProperties(Request $request)
    {
        $ids = [];
        $user_id = $request->input("userid");
        $data['properties'] = Assign::select("p.id", "p.name", "assigns.property_id", "assigns.id as assign_id")
            ->join("properties as p", "assigns.property_id", "=", "p.id", "left")
            ->where("assigns.user_id", $user_id)
            ->where("assigns.is_active", "Active")
            ->where("p.is_active", "Active")
            ->get();

        foreach ($data['properties'] as $value)
        {
            $ids[] = $value->property_id;
        }
        
        $data['options'] = Property::select("id", "name")
            ->whereNotIn("id", $ids)
            ->where("is_active", "Active")
            ->where("user_role", "admin")
            ->get();

        // $data['ids'] = $ids;

        return response()->json($data);
    }

    public function checkAssignedPropertiesAvailability(Request $request)
    {
        $propertyId = $request->id;
        $userId = $request->userid;
        $assign = Assign::select("assigns.id", "assigns.user_id", "assigns.property_id", "assigns.module_ids", "p.name")
            ->join("properties as p", "assigns.property_id", "=", "p.id", "left")
            ->where("assigns.property_id", $propertyId)
            ->where("assigns.user_id", $userId)
            ->where("assigns.is_active", "Active")
            ->first();
        return response()->json($assign);
    }

    public function removeAssignedProperty(Request $request)
    {
        $id = $request->id;

        $assign = Assign::find($id);
        $assign->is_active = "Inactive";
        $query = $assign->save();
        
        if ($query)
        {
            return response()->json([
                "status" => true,
                'message' => 'Property permission removed successfully !'
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

    public function checkEmailAvailability(Request $request)
    {
        $emailId = $request->email;
        $username = $request->username;

        $checkEmail = User::where("email", $emailId)
            ->where("is_active", "Active")
            ->where("status", "Unblocked")
            ->get()->count();

        $checkUsername = User::where("username", $username)
            ->where("is_active", "Active")
            ->where("status", "Unblocked")
            ->get()->count();

        $data = [
            "email" => $checkEmail,
            "username" => $checkUsername
        ];

        return response()->json($data);
    }

    public function checkEditEmailAvailability(Request $request)
    {
        $id = $request->empid;
        $emailId = $request->email;
        $username = $request->username;

        $checkEmail = User::where("email", $emailId)
            ->where("id", "!=", $id)
            ->where("is_active", "Active")
            ->where("status", "Unblocked")
            ->get()->count();

        $checkUsername = User::where("username", $username)
            ->where("id", "!=", $id)
            ->where("is_active", "Active")
            ->where("status", "Unblocked")
            ->get()->count();

        $data = [
            "email" => $checkEmail,
            "username" => $checkUsername
        ];

        return response()->json($data);
    }

    public function verifyAdminPassword(Request $request)
    {
        $adminId = Auth::user()->id;

        $adminPass = Admin::where("id", $adminId)->where("role", "admin")->where("status", "Unblocked")->where("is_active", "Active")->value("password");

        if (Hash::check($request->verify_password, $adminPass))
        {
            return response()->json(["status" => true]);
        }
        else
        {
            return response()->json(["status" => false, 'message' => 'Permission denied !']);
        }
    }

    public function checkCurrentEmployeePassword(Request $request)
    {
        $userId = Auth::user()->id;

        $userPass = User::where("id", $userId)->where("role", "user")->where("status", "Unblocked")->where("is_active", "Active")->value("password");

        if (Hash::check($request->old_password, $userPass))
        {
            return response()->json(["status" => 'true']);
        }
        else
        {
            return response()->json(["status" => 'false']);
        }
    }

}
