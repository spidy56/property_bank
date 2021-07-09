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

class PropertyController extends Controller
{
    public function index()
    {
    	return view('admin.property.new_property');
    }

    public function uploadImage(Request $req)
    {

    	if ($file = $req->file('file')) {
    		$fileType = $req->input('upload_file_type');

            $imgOriginalName = $req->file('file')->getClientOriginalName();
            $imgName = rand(100000000, 999999999)."_".$imgOriginalName;
    		if ($fileType=="b_one_upload")
    		{
            	$imgPath = 'b_one/'.$imgName;
            	$file = $req->file->storeAs('public/b_one', $imgName);
    		}
    		else if ($fileType=="p_two_upload")
    		{
            	$imgPath = 'p_two/'.$imgName;
            	$file = $req->file->storeAs('public/p_two', $imgName);
    		}
    		else if ($fileType=="misal_upload")
    		{
            	$imgPath = 'misal/'.$imgName;
            	$file = $req->file->storeAs('public/misal', $imgName);
    		}
    		else if ($fileType=="abdikar_abhhilekh_upload")
    		{
            	$imgPath = 'abdikar_abhhilekh/'.$imgName;
            	$file = $req->file->storeAs('public/abdikar_abhhilekh', $imgName);
    		}
    		else if ($fileType=="bhu_upyogita_upload")
    		{
            	$imgPath = 'bhu_upyogita/'.$imgName;
            	$file = $req->file->storeAs('public/bhu_upyogita', $imgName);
    		}
    		else if ($fileType=="namantran_panji_upload")
    		{
            	$imgPath = 'namantran_panji/'.$imgName;
            	$file = $req->file->storeAs('public/namantran_panji', $imgName);
    		}
    		else if ($fileType=="nistar_patrak_upload")
    		{
            	$imgPath = 'nistar_patrak/'.$imgName;
            	$file = $req->file->storeAs('public/nistar_patrak', $imgName);
    		}
    		else if ($fileType=="rin_pustika_upload")
    		{
            	$imgPath = 'rin_pustika/'.$imgName;
            	$file = $req->file->storeAs('public/rin_pustika', $imgName);
    		}
            else if ($fileType=="mutation_upload")
            {
                $imgPath = 'mutations/'.$imgName;
                $file = $req->file->storeAs('public/mutations', $imgName);
            }

            return response()->json([
                "success" => true,
                "file" => $imgPath,
                "fileOriginalName" => $imgOriginalName,
                "filetype" => $fileType
            ]);
        }
        else
        {
        	return response()->json([
                "success" => false
            ]);
        }
    }

    public function uploadMultiplePhotos(Request $req)
    {
        // return response()->json($req->file_type);
    	$data = [];

        if($req->hasfile('file')) {
            if ($req->file_type=="patwari_naksha") {
                foreach($req->file('file') as $file)
                {
                    $originalImg = $file->getClientOriginalName();
                    $imgName = rand(10000000, 99999999)."_".$originalImg;
                    $file->storeAs('public/patwari_naksha/', $imgName);  
                    $data[] = $imgName;
                }

                return response()->json([
                    "success" => true,
                    'files' => $data,
                    'filetype' => $req->file_type
                ]);
            } 
            else if ($req->file_type=="google_naksha") {
                foreach($req->file('file') as $file)
                {
                    $originalImg = $file->getClientOriginalName();
                    $imgName = rand(10000000, 99999999)."_".$originalImg;
                    $file->storeAs('public/google_naksha/', $imgName);  
                    $data[] = $imgName;  
                }

                return response()->json([
                    "success" => true,
                    'files' => $data,
                    'filetype' => $req->file_type
                ]);
            }
            else if ($req->file_type=="photos") {
                foreach($req->file('file') as $file)
                {
                    $originalImg = $file->getClientOriginalName();
                    $imgName = rand(10000000, 99999999)."_".$originalImg;
                    $file->storeAs('public/photos/', $imgName);  
                    $data[] = $imgName;  
                }

                return response()->json([
                    "success" => true,
                    'files' => $data,
                    'filetype' => $req->file_type
                ]);
            }
            else if ($req->file_type=="chauhaddi") {
                foreach($req->file('file') as $file)
                {
                    $originalImg = $file->getClientOriginalName();
                    $imgName = rand(10000000, 99999999)."_".$originalImg;
                    $file->storeAs('public/chauhaddi/', $imgName);  
                    $data[] = $imgName;  
                }

                return response()->json([
                    "success" => true,
                    'files' => $data,
                    'filetype' => $req->file_type
                ]);
            } 
        }
             
    }

    public function uploadExtraDocument(Request $req)
    {
        $data = [];

        if($req->hasfile('file')) {
            foreach($req->file('file') as $file)
            {
                $originalImg = $file->getClientOriginalName();
                $imgName = rand(10000000, 99999999)."_".$originalImg;
                $file->storeAs('public/extra_documents/', $imgName);  
                $data[] = $imgName;
            }

            return response()->json([
                "success" => true,
                'files' => $data
            ]); 
        }      
    }

    public function uploadRegistryPaper(Request $req)
    {
        $data = [];

        if($req->hasfile('file')) {
            foreach($req->file('file') as $file)
            {
                $originalImg = $file->getClientOriginalName();
                $imgName = rand(10000000, 99999999)."_".$originalImg;
                $file->storeAs('public/registry_papers/', $imgName);  
                $data[] = $imgName;
            }

            return response()->json([
                "success" => true,
                'files' => $data
            ]); 
        }      
    }

    public function submitPropertyData(Request $req)
    {
    	$property = new Property;

        $property->name = $req->name;

        if (!empty($req->city)) {
            $property->city = $req->city;
        }
        else
        {
            $property->city = NULL;
        }

    	$property->b_one = $req->b_one;
    	$property->p_two = $req->p_two;
    	$property->khasra_no = $req->khasra_no;
    	$property->khasra_name = $req->khasra_name;
    	$property->diverted = $req->diverted;
    	$property->land_type = $req->land_type;
    	$property->area = $req->area;
    	$property->area_unit = $req->area_unit;
    	$property->misal = $req->misal;
    	$property->adhikar_abhhilekh = $req->adhikar_abhhilekh;
    	$property->bhu_upyogita = $req->bhu_upyogita;
    	$property->namantran_panji = $req->namantran_panji;
    	$property->nistar_patrak = $req->nistar_patrak;
    	$property->depth_from_road = $req->depth_from_road;
    	$property->depth_from_road_unit = $req->depth_from_road_unit;
    	$property->front = $req->front;
    	$property->front_units = $req->front_units;
    	$property->deep = $req->deep;
    	$property->deep_unit = $req->deep_unit;
        
        if (!empty($req->extension_of_area)) {
            $property->extension_of_area = $req->extension_of_area;
        }
        else
        {
            $property->extension_of_area = NULL;
        }

        if (!empty($req->plot_facing)) {
            $property->plot_facing = $req->plot_facing;
        }
        else
        {
            $property->plot_facing = NULL;
        }

        $property->from_price = $req->from_price;
    	$property->price_unit = $req->price_unit;
    	// $property->to_price = $req->to_price;
    	$property->gmap_location_lat = $req->gmap_location_lat;
    	$property->gmap_location_long = $req->gmap_location_long;
    	$property->location = $req->location;
    	$property->boundary_wall = $req->boundary_wall;
    	$property->bore = $req->bore;
    	$property->no_of_bores = $req->no_of_bores;
    	$property->no_of_registry = $req->no_of_registry;
    	$property->adiwasi_land = $req->adiwasi_land;
    	$property->verified_property = $req->verified_property;
    	$property->source_of_property = $req->source_of_property;
    	$property->contact_no = $req->contact_no;
        $property->alternate_contact = $req->alternate_contact;
        $property->rin_pustika = $req->rin_pustika;
    	$property->mutation = $req->mutation;
        $property->registry_price = $req->registry_price;
        $property->registry_price_unit = $req->registry_price_unit;
        $property->property_remark = $req->property_remark;

        if ($req->area_unit=="Acre") {
            $areaSqft = $req->area*43560;
        }
        else if ($req->area_unit=="Sqmt") {
            $areaSqft = $req->area*11;
        }
        else if ($req->area_unit=="Hectare") {
            $areaSqft = $req->area*107639;
        }
        else if ($req->area_unit=="Dismil") {
            $areaSqft = $req->area*436;
        }
        else
        {
            $areaSqft = $req->area;
        }

        $property->area_in_sqft = intval($areaSqft);

        if ($req->price_unit=="Acre") {
            $priceSqft = $req->from_price*43560;
        }
        else if ($req->price_unit=="Sqmt") {
            $priceSqft = $req->from_price*11;
        }
        else if ($req->price_unit=="Hectare") {
            $priceSqft = $req->from_price*107639;
        }
        else if ($req->price_unit=="Dismil") {
            $priceSqft = $req->from_price*436;
        }
        else
        {
            $priceSqft = $req->from_price;
        }

        $property->price_in_sqft = intval($priceSqft);

        if ($req->front_units=="Metre") {
            $frontSqft = $req->front*4;
        }
        else if ($req->front_units=="Yard") {
            $frontSqft = $req->front*3;
        }
        else if ($req->front_units=="KM") {
            $frontSqft = $req->front*3280;
        }
        else
        {
            $frontSqft = $req->front;
        }

        $property->front_in_ft = intval($frontSqft);

        if ($req->deep_unit=="Metre") {
            $deepSqft = $req->deep*4;
        }
        else if ($req->deep_unit=="Yard") {
            $deepSqft = $req->deep*3;
        }
        else if ($req->deep_unit=="KM") {
            $deepSqft = $req->deep*3280;
        }
        else
        {
            $deepSqft = $req->deep;
        }

        $property->deep_in_ft = intval($deepSqft);

        $property->created_by = Auth::user()->id;
        $property->user_role = Auth::user()->role;

    	$query = $property->save();

    	$property_id = $property->id;

        $loc = substr($req->city, 0, 4);
        $propertyID = "PB".strtoupper($loc).str_pad($property_id, 6, '0', STR_PAD_LEFT);

        $updateProperty = Property::find($property_id);
        $updateProperty->propertyid = $propertyID;
        $updateProperty->save();

    	if (isset($req->photos)) {
    		foreach ($req->photos as $photo) {
	    		$image = new Image();
	    		$image->property_id = $property_id;
                $image->image = $photo;
	    		$image->image_type = "photos";
	    		$image->save();
	    	}
    	}

        if (isset($req->patwari_naksha)) {
            foreach ($req->patwari_naksha as $photo) {
                $image = new Image();
                $image->property_id = $property_id;
                $image->image = $photo;
                $image->image_type = "patwarinaksha";
                $image->save();
            }
        }

        if (isset($req->google_naksha)) {
            foreach ($req->google_naksha as $photo) {
                $image = new Image();
                $image->property_id = $property_id;
                $image->image = $photo;
                $image->image_type = "googlenaksha";
                $image->save();
            }
        }

        if (isset($req->chauhaddi)) {
            foreach ($req->chauhaddi as $photo) {
                $image = new Image();
                $image->property_id = $property_id;
                $image->image = $photo;
                $image->image_type = "chauhaddi";
                $image->save();
            }
        }

        if (isset($req->extra_documents)) {
            foreach ($req->extra_documents as $photo) {
                $image = new Image();
                $image->property_id = $property_id;
                $image->image = $photo;
                $image->image_type = "extra_documents";
                $image->save();
            }
        }

        if (isset($req->registry_papers)) {
            foreach ($req->registry_papers as $photo) {
                $image = new Image();
                $image->property_id = $property_id;
                $image->image = $photo;
                $image->image_type = "registry_papers";
                $image->save();
            }
        }
    	
		foreach ($req->video_links as $video_link) {
			if($video_link!=Null) {
	    		$video = new Video();
	    		$video->property_id = $property_id;
	    		$video->video_link = $video_link;
	    		$video->save();
	    	}
    	}

    	if ($query)
        {
            return response()->json([
                "status" => true,
                'message' => 'Form submitted successfully !'
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

    public function Properties()
    {
        $data['propertyIds'] = Property::where("is_active", "Active")->pluck("propertyid");
        $data['propertyNames'] = Property::where("is_active", "Active")->pluck("name");
        $data['khasraNos'] = Property::where("is_active", "Active")->pluck("khasra_no");
        $data['khasraNames'] = Property::where("is_active", "Active")->pluck("khasra_name");
        $data['noOfRegs'] = Property::where("is_active", "Active")->groupBy("no_of_registry")->pluck("no_of_registry");
        $data['areas'] = Property::where("is_active", "Active")->groupBy("area")->groupBy("area_unit")->pluck("area");
        $data['fronts'] = Property::where("is_active", "Active")->groupBy("front")->groupBy("front_units")->pluck("front");
        $data['deeps'] = Property::where("is_active", "Active")->groupBy("deep")->groupBy("deep_unit")->pluck("deep");
        $data['propertySources'] = Property::where("is_active", "Active")->groupBy("source_of_property")->pluck("source_of_property");
        $data['plotFacings'] = Property::where("is_active", "Active")->groupBy("plot_facing")->pluck("plot_facing");
        $data['locations'] = Property::where("is_active", "Active")->groupBy("location")->pluck("location");
        $prices = Property::select("from_price", "from_price as from_price_key")->where("is_active", "Active")->whereNotNull("from_price")->orderByRaw('CONVERT(from_price, SIGNED) ASC')->groupBy("from_price")->get();

        foreach($prices as $price)
        {
            $price->from_price_key = $this->no_to_words($price->from_price_key);
        }

        $data['prices'] = $prices;
        $data['users'] = User::select("id", "name")->where("is_active", "Active")->where("status", 'Unblocked')->get();
    	return view('admin.property.properties', $data);
    }

    public function propertyServerSideTable(Request $request)
    {
        if($request->ajax()){
            $query = Property::query();

            if( !empty($request->userfiler) ){
                $query->where("created_by", $request->userfiler);
            }

            if($request->viewfiler != 'All'){
                if($request->viewfiler == 'ListedByMe'){
                    $query->where("user_role", "admin");
                    $query->where("created_by", Auth::user()->id);
                }
                else
                {
                    $query->where("user_role", "user");
                }
            }

            if( !empty($request->property_id) ){
                $query->where("propertyid", $request->property_id);
            }
            if( !empty($request->property_name) ){
                $query->where(function($query) use($request){
                    $query->where('name', 'LIKE', "%{$request->property_name}%");
                });
            }
            
            if( !empty($request->khasra_no) ){
                $query->where("khasra_no", $request->khasra_no);
            }
            if( !empty($request->khasra_name) ){
                $query->where("khasra_name", $request->khasra_name);
            }
            if( !empty($request->diverted) ){
                $query->where("diverted", $request->diverted);
            }
            if( !empty($request->type_of_land) ){
                $query->where("land_type", $request->type_of_land);
            }

            if( !empty($request->area_unit) ){
                
                if ($request->area_unit=="Acre") {
                    if( $request->from_area != '' && $request->to_area == ''){
                        $fromArea = floatval($request->from_area)*43560;
                        $query->where('area_in_sqft', intval($fromArea));
                    }
                    else if( $request->from_area == '' && $request->to_area != ''){
                        $toArea = floatval($request->to_area)*43560;
                        $query->where('area_in_sqft', intval($toArea));
                    }
                    else if( $request->from_area != '' && $request->to_area != '')
                    {
                        $fromArea = floatval($request->from_area)*43560;
                        $toArea = floatval($request->to_area)*43560;
                        $query->where('area_in_sqft', ">=", intval($fromArea));
                        $query->where('area_in_sqft', "<=", intval($toArea));
                    }
                    
                }
                else if ($request->area_unit=="Sqmt") {
                    if( $request->from_area != '' && $request->to_area == ''){
                        $fromArea = floatval($request->from_area)*11;
                        $query->where('area_in_sqft', intval($fromArea));
                    }
                    else if( $request->from_area == '' && $request->to_area != ''){
                        $toArea = floatval($request->to_area)*11;
                        $query->where('area_in_sqft', intval($toArea));
                    }
                    else if( $request->from_area != '' && $request->to_area != '')
                    {
                        $fromArea = floatval($request->from_area)*11;
                        $toArea = floatval($request->to_area)*11;
                        $query->where('area_in_sqft', ">=", intval($fromArea));
                        $query->where('area_in_sqft', "<=", intval($toArea));
                    }
                }
                else if ($request->area_unit=="Hectare") {
                    if( $request->from_area != '' && $request->to_area == ''){
                        $fromArea = floatval($request->from_area)*107639;
                        $query->where('area_in_sqft', intval($fromArea));
                    }
                    else if( $request->from_area == '' && $request->to_area != ''){
                        $toArea = floatval($request->to_area)*107639;
                        $query->where('area_in_sqft', intval($toArea));
                    }
                    else if( $request->from_area != '' && $request->to_area != '')
                    {
                        $fromArea = floatval($request->from_area)*107639;
                        $toArea = floatval($request->to_area)*107639;
                        $query->where('area_in_sqft', ">=", intval($fromArea));
                        $query->where('area_in_sqft', "<=", intval($toArea));
                    }
                }
                else if ($request->area_unit=="Dismil") {
                    if( $request->from_area != '' && $request->to_area == ''){
                        $fromArea = floatval($request->from_area)*436;
                        $query->where('area_in_sqft', intval($fromArea));
                    }
                    else if( $request->from_area == '' && $request->to_area != ''){
                        $toArea = floatval($request->to_area)*436;
                        $query->where('area_in_sqft', intval($toArea));
                    }
                    else if( $request->from_area != '' && $request->to_area != '')
                    {
                        $fromArea = floatval($request->from_area)*436;
                        $toArea = floatval($request->to_area)*436;
                        $query->where('area_in_sqft', ">=", intval($fromArea));
                        $query->where('area_in_sqft', "<=", intval($toArea));
                    }
                }
                else {
                    if( $request->from_area != '' && $request->to_area == ''){
                        $fromArea = floatval($request->from_area);
                        $query->where('area_in_sqft', intval($fromArea));
                    }
                    else if( $request->from_area == '' && $request->to_area != ''){
                        $toArea = floatval($request->to_area);
                        $query->where('area_in_sqft', intval($toArea));
                    }
                    else if( $request->from_area != '' && $request->to_area != '')
                    {
                        $fromArea = floatval($request->from_area);
                        $toArea = floatval($request->to_area);
                        $query->where('area_in_sqft', ">=", intval($fromArea));
                        $query->where('area_in_sqft', "<=", intval($toArea));
                    }
                }
            }
            else
            {
                if( $request->from_area != '' && $request->to_area == ''){
                    $fromArea = floatval($request->from_area);
                    $query->where('area_in_sqft', intval($fromArea));
                }
                else if( $request->from_area == '' && $request->to_area != ''){
                    $toArea = floatval($request->to_area);
                    $query->where('area_in_sqft', intval($toArea));
                }
                else if( $request->from_area != '' && $request->to_area != '')
                {
                    $fromArea = floatval($request->from_area);
                    $toArea = floatval($request->to_area);
                    $query->where('area_in_sqft', ">=", intval($fromArea));
                    $query->where('area_in_sqft', "<=", intval($toArea));
                }
            }

            if( !empty($request->price_unit) ){
                if ($request->price_unit=="Acre") {
                    if( $request->from_price != '' && $request->to_price == ''){
                        $fromPrice = floatval($request->from_price)*43560;
                        $query->where('price_in_sqft', intval($fromPrice));
                    }
                    else if( $request->from_price == '' && $request->to_price != ''){
                        $toPrice = floatval($request->to_price)*43560;
                        $query->where('price_in_sqft', intval($toPrice));
                    }
                    else if( $request->from_price != '' && $request->to_price != '')
                    {
                        $fromPrice = floatval($request->from_price)*43560;
                        $toPrice = floatval($request->to_price)*43560;
                        $query->where('price_in_sqft', ">=", intval($fromPrice));
                        $query->where('price_in_sqft', "<=", intval($toPrice));
                    }
                }
                else if ($request->price_unit=="Sqmt") {
                    if( $request->from_price != '' && $request->to_price == ''){
                        $fromPrice = floatval($request->from_price)*11;
                        $query->where('price_in_sqft', intval($fromPrice));
                    }
                    else if( $request->from_price == '' && $request->to_price != ''){
                        $toPrice = floatval($request->to_price)*11;
                        $query->where('price_in_sqft', intval($toPrice));
                    }
                    else if( $request->from_price != '' && $request->to_price != '')
                    {
                        $fromPrice = floatval($request->from_price)*11;
                        $toPrice = floatval($request->to_price)*11;
                        $query->where('price_in_sqft', ">=", intval($fromPrice));
                        $query->where('price_in_sqft', "<=", intval($toPrice));
                    }
                }
                else if ($request->price_unit=="Hectare") {
                    if( $request->from_price != '' && $request->to_price == ''){
                        $fromPrice = floatval($request->from_price)*107639;
                        $query->where('price_in_sqft', intval($fromPrice));
                    }
                    else if( $request->from_price == '' && $request->to_price != ''){
                        $toPrice = floatval($request->to_price)*107639;
                        $query->where('price_in_sqft', intval($toPrice));
                    }
                    else if( $request->from_price != '' && $request->to_price != '')
                    {
                        $fromPrice = floatval($request->from_price)*107639;
                        $toPrice = floatval($request->to_price)*107639;
                        $query->where('price_in_sqft', ">=", intval($fromPrice));
                        $query->where('price_in_sqft', "<=", intval($toPrice));
                    }
                }
                else if ($request->price_unit=="Dismil") {
                    if( $request->from_price != '' && $request->to_price == ''){
                        $fromPrice = floatval($request->from_price)*436;
                        $query->where('price_in_sqft', intval($fromPrice));
                    }
                    else if( $request->from_price == '' && $request->to_price != ''){
                        $toPrice = floatval($request->to_price)*436;
                        $query->where('price_in_sqft', intval($toPrice));
                    }
                    else if( $request->from_price != '' && $request->to_price != '')
                    {
                        $fromPrice = floatval($request->from_price)*436;
                        $toPrice = floatval($request->to_price)*436;
                        $query->where('price_in_sqft', ">=", intval($fromPrice));
                        $query->where('price_in_sqft', "<=", intval($toPrice));
                    }
                }
                else {
                    if( $request->from_price != '' && $request->to_price == ''){
                        $fromPrice = floatval($request->from_price);
                        $query->where('price_in_sqft', intval($fromPrice));
                    }
                    else if( $request->from_price == '' && $request->to_price != ''){
                        $toPrice = floatval($request->to_price);
                        $query->where('price_in_sqft', intval($toPrice));
                    }
                    else if( $request->from_price != '' && $request->to_price != '')
                    {
                        $fromPrice = floatval($request->from_price);
                        $toPrice = floatval($request->to_price);
                        $query->where('price_in_sqft', ">=", intval($fromPrice));
                        $query->where('price_in_sqft', "<=", intval($toPrice));
                    }
                }
            }
            else
            {
                if( $request->from_price != '' && $request->to_price == ''){
                    $fromPrice = floatval($request->from_price);
                    $query->where('price_in_sqft', intval($fromPrice));
                }
                else if( $request->from_price == '' && $request->to_price != ''){
                    $toPrice = floatval($request->to_price);
                    $query->where('price_in_sqft', intval($toPrice));
                }
                else if( $request->from_price != '' && $request->to_price != '')
                {
                    $fromPrice = floatval($request->from_price);
                    $toPrice = floatval($request->to_price);
                    $query->where('price_in_sqft', ">=", intval($fromPrice));
                    $query->where('price_in_sqft', "<=", intval($toPrice));
                }
            }

            if( !empty($request->front_unit) ){
                if ($request->front_unit=="Metre") {

                    if( $request->from_front != '' && $request->to_front == ''){
                        $fromFront = floatval($request->from_front)*4;
                        $query->where('front_in_ft', intval($fromFront));
                    }
                    else if( $request->from_front == '' && $request->to_front != ''){
                        $toFront = floatval($request->to_front)*4;
                        $query->where('front_in_ft', intval($toFront));
                    }
                    else if( $request->from_front != '' && $request->to_front != '')
                    {
                        $fromFront = floatval($request->from_front)*4;
                        $toFront = floatval($request->to_front)*4;
                        $query->where('front_in_ft', ">=", intval($fromFront));
                        $query->where('front_in_ft', "<=", intval($toFront));
                    }
                }
                else if ($request->front_unit=="Yard") {
                    if( $request->from_front != '' && $request->to_front == ''){
                        $fromFront = floatval($request->from_front)*3;
                        $query->where('front_in_ft', intval($fromFront));
                    }
                    else if( $request->from_front == '' && $request->to_front != ''){
                        $toFront = floatval($request->to_front)*3;
                        $query->where('front_in_ft', intval($toFront));
                    }
                    else if( $request->from_front != '' && $request->to_front != '')
                    {    
                        $fromFront = floatval($request->from_front)*3;
                        $toFront = floatval($request->to_front)*3;
                        $query->where('front_in_ft', ">=", intval($fromFront));
                        $query->where('front_in_ft', "<=", intval($toFront));
                    }
                }
                else if ($request->front_unit=="KM") {
                    if( $request->from_front != '' && $request->to_front == ''){
                        $fromFront = floatval($request->from_front)*3280;
                        $query->where('front_in_ft', intval($fromFront));
                    }
                    else if( $request->from_front == '' && $request->to_front != ''){
                        $toFront = floatval($request->to_front)*3280;
                        $query->where('front_in_ft', intval($toFront));
                    }
                    else if( $request->from_front != '' && $request->to_front != '')
                    {
                        $fromFront = floatval($request->from_front)*3280;
                        $toFront = floatval($request->to_front)*3280;
                        $query->where('front_in_ft', ">=", intval($fromFront));
                        $query->where('front_in_ft', "<=", intval($toFront));
                    }
                }
                else {
                    if( $request->from_front != '' && $request->to_front == ''){
                        $fromFront = floatval($request->from_front);
                        $query->where('front_in_ft', intval($fromFront));
                    }
                    else if( $request->from_front == '' && $request->to_front != ''){
                        $toFront = floatval($request->to_front);
                        $query->where('front_in_ft', intval($toFront));
                    }
                    else if( $request->from_front != '' && $request->to_front != '')
                    {
                        $fromFront = floatval($request->from_front);
                        $toFront = floatval($request->to_front);
                        $query->where('front_in_ft', ">=", intval($fromFront));
                        $query->where('front_in_ft', "<=", intval($toFront));
                    }
                }
            }
            else
            {
                if( $request->from_front != '' && $request->to_front == ''){
                    $fromFront = floatval($request->from_front);
                    $query->where('front_in_ft', intval($fromFront));
                }
                else if( $request->from_front == '' && $request->to_front != ''){
                    $toFront = floatval($request->to_front);
                    $query->where('front_in_ft', intval($toFront));
                }
                else if( $request->from_front != '' && $request->to_front != '')
                {
                    $fromFront = floatval($request->from_front);
                    $toFront = floatval($request->to_front);
                    $query->where('front_in_ft', ">=", intval($fromFront));
                    $query->where('front_in_ft', "<=", intval($toFront));
                }
            }

            if( !empty($request->deep_unit) ){
                if ($request->deep_unit=="Metre") {
                    if( $request->from_deep != '' && $request->to_deep == ''){
                        $fromDeep = floatval($request->from_deep)*4;
                        $query->where('deep_in_ft', intval($fromDeep));
                    }
                    if( $request->from_deep == '' && $request->to_deep != ''){
                        $toDeep = floatval($request->to_deep)*4;
                        $query->where('deep_in_ft', intval($toDeep));
                    }
                    if( $request->from_deep != '' && $request->to_deep != '')
                    {
                        $fromDeep = floatval($request->from_deep)*4;
                        $toDeep = floatval($request->to_deep)*4;
                        $query->where('deep_in_ft', ">=", intval($fromDeep));
                        $query->where('deep_in_ft', "<=", intval($toDeep));
                    }
                }
                else if ($request->deep_unit=="Yard") {
                    if( $request->from_deep != '' && $request->to_deep == ''){
                        $fromDeep = floatval($request->from_deep)*3;
                        $query->where('deep_in_ft', intval($fromDeep));
                    }
                    if( $request->from_deep == '' && $request->to_deep != ''){
                        $toDeep = floatval($request->to_deep)*3;
                        $query->where('deep_in_ft', intval($toDeep));
                    }
                    if( $request->from_deep != '' && $request->to_deep != '')
                    {
                        $fromDeep = floatval($request->from_deep)*3;
                        $toDeep = floatval($request->to_deep)*3;
                        $query->where('deep_in_ft', ">=", intval($fromDeep));
                        $query->where('deep_in_ft', "<=", intval($toDeep));
                    }
                }
                else if ($request->deep_unit=="KM") {
                    if( $request->from_deep != '' && $request->to_deep == ''){
                        $fromDeep = floatval($request->from_deep)*3280;
                        $query->where('deep_in_ft', intval($fromDeep));
                    }
                    if( $request->from_deep == '' && $request->to_deep != ''){
                        $toDeep = floatval($request->to_deep)*3280;
                        $query->where('deep_in_ft', intval($toDeep));
                    }
                    if( $request->from_deep != '' && $request->to_deep != '')
                    {
                        $fromDeep = floatval($request->from_deep)*3280;
                        $toDeep = floatval($request->to_deep)*3280;
                        $query->where('deep_in_ft', ">=", intval($fromDeep));
                        $query->where('deep_in_ft', "<=", intval($toDeep));
                    }
                }
                else {
                    if( $request->from_deep != '' && $request->to_deep == ''){
                        $fromDeep = floatval($request->from_deep);
                        $query->where('deep_in_ft', intval($fromDeep));
                    }
                    if( $request->from_deep == '' && $request->to_deep != ''){
                        $toDeep = floatval($request->to_deep);
                        $query->where('deep_in_ft', intval($toDeep));
                    }
                    if( $request->from_deep != '' && $request->to_deep != '')
                    {
                        $fromDeep = floatval($request->from_deep);
                        $toDeep = floatval($request->to_deep);
                        $query->where('deep_in_ft', ">=", intval($fromDeep));
                        $query->where('deep_in_ft', "<=", intval($toDeep));
                    }
                }
            }
            else
            {
                if( $request->from_deep != '' && $request->to_deep == ''){
                    $fromDeep = floatval($request->from_deep);
                    $query->where('deep_in_ft', intval($fromDeep));
                }
                if( $request->from_deep == '' && $request->to_deep != ''){
                    $toDeep = floatval($request->to_deep);
                    $query->where('deep_in_ft', intval($toDeep));
                }
                if( $request->from_deep != '' && $request->to_deep != '')
                {
                    $fromDeep = floatval($request->from_deep);
                    $toDeep = floatval($request->to_deep);
                    $query->where('deep_in_ft', ">=", intval($fromDeep));
                    $query->where('deep_in_ft', "<=", intval($toDeep));
                }
            }

            if( !empty($request->from_price)){
                $query->where("from_price", ">=", intval($request->from_price));
            }
            if( !empty($request->to_price)){
                $query->where("from_price" , "<=", intval($request->to_price));
            }
            // if( !empty($request->to_price)){
            //     $query->where("to_price" , "<=", intval($request->to_price));
            // }

            if( !empty($request->location) ){
                $query->where(function($query) use($request){
                    $query->where('location', 'LIKE', "%{$request->location}%");
                });
            }

            if( !empty($request->boundary_wall)){
                $query->where("boundary_wall", $request->boundary_wall);
            }
            if( !empty($request->bore)){
                $query->where("bore", $request->bore);
            }
            if( !empty($request->no_of_registry)){
                $query->where("no_of_registry", $request->no_of_registry);
            }
            if( !empty($request->aadiwasi_land)){
                $query->where("adiwasi_land", $request->aadiwasi_land);
            }
            if( !empty($request->verified_property)){
                $query->where("verified_property", $request->verified_property);
            }
            if( !empty($request->source_of_property)){
                $query->where("source_of_property", $request->source_of_property);
            }
            if( !empty($request->plot_facing)){
                $query->where("plot_facing", $request->plot_facing);
            }
            $query->where("is_active", "Active");

            $query->where(function($query1){

                $query1->where(function($query2){
                    $query2->where("user_role", "admin");
                });

                $query1->orWhere(function($query3){
                    $query3->where("user_role", "user");
                    $query3->where("status", "Available");
                });
            });

            $query->orderBy("id", "DESC");
            $rows = $query->get();

            // $rows = $query->toSql();

            return datatables()->of($rows)->addIndexColumn()
            ->addColumn('id', function($data){
                return $data->propertyid;
            })
            ->addColumn('date', function($data){
                return date("d-m-Y h:i A", strtotime($data->created_at));
            })
            ->addColumn('name', function($data){
                if ($data->verified_property=="Yes") {
                    $name = '<a href="viewProperty/'. $data->id .'">'.$data->name.'<br>
                        <span style="font-size: 11px;" class="text-success">
                            <i style="font-size: 11px;" class="bx bxs-checkbox-checked text-success"></i>
                            Verified
                        </span>
                    </a>';
                }
                else
                {
                    $name = '<a href="viewProperty/'. $data->id .'">'.$data->name.'</a>';
                }
                return $name;
            })
            ->addColumn('land_type', function($data){
                return $data->land_type;
            })
            ->addColumn('area', function($data){
            	if ($data->area) {
                	return $data->area." ".$data->area_unit;
            	}
            })
            ->addColumn('price', function($data){
                // $price = $this->no_to_words($data->from_price);
                if (isset($data->from_price)) {
                	$price = $this->no_to_words($data->from_price)." Rs./".$data->price_unit;
                }
                else
                {
                	$price = "NA";
                }
                return $price;
            })
            ->addColumn('city', function($data){
                return $data->city;
            })
            ->addColumn('location', function($data){
                return $data->location;
            })
            ->addColumn('created_by', function($data){
                $created_by = $data->created_by;
                if ($data->user_role=="admin") {
                    $user = Admin::select("name", "role")->where("id", $created_by)->first();
                }
                else
                {
                    $user = User::select("name", "role")->where("id", $created_by)->first();
                }
                return $user->name." (".$user->role.")";
            })
            ->addColumn('status', function($data){
                if ($data->user_role=='admin')
                {
                    $button = '';
                    if ($data->status=='Available')
                    {
                        $button .= '<button type="button" data-id="'. $data->id .'" class="btn btn-sm btn-success block-property-btn" data-toggle="tooltip" data-placement="top" title="Click to unavailable">'.ucfirst($data->status).'</button>';
                    }
                    else
                    {
                        $button .= '<button type="button" data-id="'. $data->id .'" class="btn btn-sm btn-danger unblock-property-btn" data-toggle="tooltip" data-placement="top" title="Click to available">'.ucfirst($data->status).'</button>';
                    }
                    return $button;
                }
                else
                {
                    $button = '';
                    if ($data->status=='Available')
                    {
                        $button .= '<button type="button" data-id="'. $data->id .'" class="btn btn-sm btn-success">'.ucfirst($data->status).'</button>';
                    }
                    else
                    {
                        $button .= '<button type="button" data-id="'. $data->id .'" class="btn btn-sm btn-danger">'.ucfirst($data->status).'</button>';
                    }
                    return $button;
                }
                
            })
            ->addColumn('leads', function($data){
                $propertyid = $data->id;
                $counts = LeadAssign::whereRaw("find_in_set($propertyid , property_id)")
                ->where("is_active", "Yes")
                ->groupBy("lead_id")
                ->get()
                ->count();

                // if ($counts>0)
                // {
                    $link = '<a href="leads/'. $data->id .'" data-toggle="tooltip" data-placement="top" title="Click to see leads">'.$counts.'</a>';
                // }
                // else
                // {
                //     $link = $counts;
                // }

                return $link;
            })
            ->addColumn('actions', function($data){
                if ($data->user_role=='admin')
                {
                    $button = '<div class="ui" style="width: 120px;">';
                    $button .= ' <a href="viewProperty/'. $data->id .'" class="btn btn-sm btn-warning" data-toggle="tooltip" data-placement="top" title="View property details"><i class="bx bxs-user-detail" aria-hidden="true"></i></a>';
                    $button .= ' <a href="editProperty/'. $data->id .'" class="btn btn-sm btn-success" data-toggle="tooltip" data-placement="top" title="Edit property"><i class="bx bx-edit" aria-hidden="true"></i></a>';
                    $button .= ' <button data-id="'. $data->id .'" class="btn btn-sm btn-danger delete-property-btn" data-toggle="tooltip" data-placement="top" title="Delete property"><i class="bx bx-trash"></i></button>';
                    
                    $button .= '</div>';
                }
                else
                {
                    $button = '<div class="ui" style="width: 120px;">';
                    $button .= ' <a href="viewProperty/'. $data->id .'" class="btn btn-sm btn-warning" data-toggle="tooltip" data-placement="top" title="View property details"><i class="bx bxs-user-detail" aria-hidden="true"></i></a>';
                    $button .= '</div>';
                }
                return $button;
            })
            
            ->rawColumns(['id', 'date', 'name', 'land_type', 'area', 'price', 'city', 'location', 'created_by', 'status', 'leads', 'actions'])
                ->make(true);
        }
    }

    public function no_to_words($no)
    {
        if($no == 0)
        {
            return 'NA';
        }
        else
        {
            $n =  strlen($no);
            switch ($n) {
                case 1:
                    $finalval =  $no;
                    break;
                case 2:
                    $finalval =  $no;
                    break;
                case 3:
                    // $val = $no/100;
                    // $val = round($val, 2);
                    // $finalval =  $val ." hundred";
                    $finalval =  $no;
                    break;
                case 4:
                    $val = $no/1000;
                    $val = round($val, 2);
                    $finalval =  $val ." K";
                    break;
                case 5:
                    $val = $no/1000;
                    $val = round($val, 2);
                    $finalval =  $val ." K";
                    break;
                case 6:
                    $val = $no/100000;
                    $val = round($val, 2);
                    $finalval =  $val ." Lac";
                    break;
                case 7:
                    $val = $no/100000;
                    $val = round($val, 2);
                    $finalval =  $val ." Lac";
                    break;
                case 8:
                    $val = $no/10000000;
                    $val = round($val, 2);
                    $finalval =  $val ." Cr";
                    break;
                case 9:
                    $val = $no/10000000;
                    $val = round($val, 2);
                    $finalval =  $val ." Cr";
                    break;

                default:
                    echo "";
            }
            return $finalval;
        }
    }

    public function deleteProperty(Request $request)
    {
        $id = $request->id;

        $property = Property::find($id);
        $property->is_active = "Inactive";
        $query = $property->save();
        
        if ($query)
        {
            return response()->json([
                "status" => true,
                'message' => 'Property deleted successfully !'
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

    public function blockProperty(Request $request)
    {
        $id = $request->id;

        $property = Property::find($id);
        $property->status = "Unavailable";
        $query = $property->save();
        
        if ($query)
        {
            return response()->json([
                "status" => true,
                'message' => 'Property blocked successfully !'
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

    public function unblockProperty(Request $request)
    {
        $id = $request->id;

        $property = Property::find($id);
        $property->status = "Available";
        $query = $property->save();
        
        if ($query)
        {
            return response()->json([
                "status" => true,
                'message' => 'Property unblocked successfully !'
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

    public function viewProperty($id)
    {
        $data['property'] = Property::where('id', $id)->first();

        if ($data['property']) {
            // if($data['property']->from_price!="" && $data['property']->to_price !="")
            // {
            //     $price = "Rs. ".$data['property']->from_price."-".$data['property']->to_price;
            // }
            // elseif($data['property']->from_price!="" && $data['property']->to_price =="")
            // {
            //     $price = "Rs. ".$data['property']->from_price;
            // }
            // else
            // {
            //     $price = "NA";
            // }

            // $data['property']->price = $price;
            if(isset($data['property']->from_price))
            {
                $price = $this->no_to_words($data['property']->from_price);
                if ($price!="NA") {
                    $price = $price." Rs./".$data['property']->price_unit;
                }
                else
                {
                    $price = "NA";
                }
            }
            else
            {
                $price = "NA";
            }
            $data['property']->price = $price;

            if(isset($data['property']->registry_price))
            {
                $registry_price = $this->no_to_words($data['property']->registry_price);
                if ($registry_price!="NA") {
                    $registry_price = $registry_price." Rs./".$data['property']->registry_price_unit;
                }
                else
                {
                    $registry_price = "NA";
                }
            }
            else
            {
                $registry_price = "NA";
            }
            $data['property']->registry_price = $registry_price;
        }
        
        $data['images'] = Image::where('image_type', "photos")->where('property_id', $id)->get();
        $data['patwaris'] = Image::where('image_type', "patwarinaksha")->where('property_id', $id)->get();
        $data['googles'] = Image::where('image_type', "googlenaksha")->where('property_id', $id)->get();
        $data['chauhaddis'] = Image::where('image_type', "chauhaddi")->where('property_id', $id)->get();
        $data['extradocs'] = Image::where('image_type', "extra_documents")->where('property_id', $id)->get();
        $data['regPapers'] = Image::where('image_type', "registry_papers")->where('property_id', $id)->get();
        $data['photo'] = Image::where('image_type', "photos")->where('property_id', $id)->first();
        $data['videos'] = Video::where('property_id', $id)->get();

        $uids = [];
        $data['users'] = Assign::select("u.id", "u.name", "u.contact_no")
            ->join("users as u", "u.id", "=", "assigns.user_id", "left")
            ->where('assigns.property_id', $id)
            ->where('assigns.is_active', "Active")
            ->where('u.is_active', "Active")
            ->get();

        foreach ($data['users'] as $value) {
            $uids[] = $value->id;
        }

        $data['employees'] = User::select("id", "name", "contact_no")
        ->where('is_active', "Active")
            ->whereNotIn('id', $uids)
            ->get();

        return view('admin.property.view_property', $data);
    }

    public function editProperty($id)
    {
        $data['property'] = Property::where('id', $id)->first();
        $data['images'] = Image::where('image_type', "photos")->where('property_id', $id)->get();
        $data['patwaris'] = Image::where('image_type', "patwarinaksha")->where('property_id', $id)->get();
        $data['googles'] = Image::where('image_type', "googlenaksha")->where('property_id', $id)->get();
        $data['chauhaddis'] = Image::where('image_type', "chauhaddi")->where('property_id', $id)->get();
        $data['extradocs'] = Image::where('image_type', "extra_documents")->where('property_id', $id)->get();
        $data['regPapers'] = Image::where('image_type', "registry_papers")->where('property_id', $id)->get();
        $data['videos'] = Video::where('property_id', $id)->get();
        return view('admin.property.edit_property', $data);
    }

    public function updatePropertyData(Request $req)
    {
        $property = Property::find($req->id);

        $property->name = $req->name;

        if (!empty($req->city)) {
            $property->city = $req->city;
        }
        else
        {
            $property->city = NULL;
        }

        if($req->b_one!=$property->b_one) {
            $property->b_one = $req->b_one;
        }
        if($req->p_two!=$property->p_two) {
            $property->p_two = $req->p_two;
        }
        $property->khasra_no = $req->khasra_no;
        $property->khasra_name = $req->khasra_name;
        $property->diverted = $req->diverted;
        $property->land_type = $req->land_type;
        $property->area = $req->area;
        $property->area_unit = $req->area_unit;

        if($req->misal!=$property->misal) {
            $property->misal = $req->misal;
        }
        if($req->adhikar_abhhilekh!=$property->adhikar_abhhilekh) {
            $property->adhikar_abhhilekh = $req->adhikar_abhhilekh;
        }
        if($req->bhu_upyogita!=$property->bhu_upyogita) {
            $property->bhu_upyogita = $req->bhu_upyogita;
        }
        if($req->namantran_panji!=$property->namantran_panji) {
            $property->namantran_panji = $req->namantran_panji;
        }
        if($req->nistar_patrak!=$property->nistar_patrak) {
            $property->nistar_patrak = $req->nistar_patrak;
        }

        $property->depth_from_road = $req->depth_from_road;
        $property->depth_from_road_unit = $req->depth_from_road_unit;
        $property->front = $req->front;
        $property->front_units = $req->front_units;
        $property->deep = $req->deep;
        $property->deep_unit = $req->deep_unit;

        if (!empty($req->extension_of_area)) {
            $property->extension_of_area = $req->extension_of_area;
        }
        else
        {
            $property->extension_of_area = NULL;
        }

        if (!empty($req->plot_facing)) {
            $property->plot_facing = $req->plot_facing;
        }
        else
        {
            $property->plot_facing = NULL;
        }
        
        $property->from_price = $req->from_price;
        $property->price_unit = $req->price_unit;
        // $property->to_price = $req->to_price;
        $property->gmap_location_lat = $req->gmap_location_lat;
        $property->gmap_location_long = $req->gmap_location_long;
        $property->location = $req->location;
        $property->boundary_wall = $req->boundary_wall;
        $property->bore = $req->bore;
        $property->no_of_bores = $req->no_of_bores;
        $property->no_of_registry = $req->no_of_registry;
        $property->adiwasi_land = $req->adiwasi_land;
        $property->verified_property = $req->verified_property;
        $property->source_of_property = $req->source_of_property;
        $property->contact_no = $req->contact_no;
        $property->alternate_contact = $req->alternate_contact;
        if($req->rin_pustika!=$property->rin_pustika) {
            $property->rin_pustika = $req->rin_pustika;
        }
        if($req->mutation!=$property->mutation) {
            $property->mutation = $req->mutation;
        }

        $property->registry_price = $req->registry_price;
        $property->registry_price_unit = $req->registry_price_unit;
        $property->property_remark = $req->property_remark;

        if ($req->area_unit=="Acre") {
            $areaSqft = $req->area*43560;
        }
        else if ($req->area_unit=="Sqmt") {
            $areaSqft = $req->area*11;
        }
        else if ($req->area_unit=="Hectare") {
            $areaSqft = $req->area*107639;
        }
        else if ($req->area_unit=="Dismil") {
            $areaSqft = $req->area*436;
        }
        else
        {
            $areaSqft = $req->area;
        }

        $property->area_in_sqft = intval($areaSqft);

        if ($req->price_unit=="Acre") {
            $priceSqft = $req->from_price*43560;
        }
        else if ($req->price_unit=="Sqmt") {
            $priceSqft = $req->from_price*11;
        }
        else if ($req->price_unit=="Hectare") {
            $priceSqft = $req->from_price*107639;
        }
        else if ($req->price_unit=="Dismil") {
            $priceSqft = $req->from_price*436;
        }
        else
        {
            $priceSqft = $req->from_price;
        }

        $property->price_in_sqft = intval($priceSqft);

        if ($req->front_units=="Metre") {
            $frontSqft = $req->front*4;
        }
        else if ($req->front_units=="Yard") {
            $frontSqft = $req->front*3;
        }
        else if ($req->front_units=="KM") {
            $frontSqft = $req->front*3280;
        }
        else
        {
            $frontSqft = $req->front;
        }

        $property->front_in_ft = intval($frontSqft);

        if ($req->deep_unit=="Metre") {
            $deepSqft = $req->deep*4;
        }
        else if ($req->deep_unit=="Yard") {
            $deepSqft = $req->deep*3;
        }
        else if ($req->deep_unit=="KM") {
            $deepSqft = $req->deep*3280;
        }
        else
        {
            $deepSqft = $req->deep;
        }

        $property->deep_in_ft = intval($deepSqft);

        // if (Auth::user()->role=="admin") {
        //     $property->created_by = Auth::user()->id;
        //     $property->user_role = Auth::user()->role;
        // }
        
        $query = $property->save();

        if (isset($req->photos)) {
            foreach ($req->photos as $photo) {
                $image = new Image();
                $image->property_id = $req->id;
                $image->image = $photo;
                $image->image_type = "photos";
                $image->save();
            }
        }

        if (isset($req->patwari_naksha)) {
            foreach ($req->patwari_naksha as $photo) {
                $image = new Image();
                $image->property_id = $req->id;
                $image->image = $photo;
                $image->image_type = "patwarinaksha";
                $image->save();
            }
        }

        if (isset($req->google_naksha)) {
            foreach ($req->google_naksha as $photo) {
                $image = new Image();
                $image->property_id = $req->id;
                $image->image = $photo;
                $image->image_type = "googlenaksha";
                $image->save();
            }
        }

        if (isset($req->chauhaddi)) {
            foreach ($req->chauhaddi as $photo) {
                $image = new Image();
                $image->property_id = $req->id;
                $image->image = $photo;
                $image->image_type = "chauhaddi";
                $image->save();
            }
        }

        if (isset($req->extra_documents)) {
            foreach ($req->extra_documents as $photo) {
                $image = new Image();
                $image->property_id = $req->id;
                $image->image = $photo;
                $image->image_type = "extra_documents";
                $image->save();
            }
        }

        if (isset($req->registry_papers)) {
            foreach ($req->registry_papers as $photo) {
                $image = new Image();
                $image->property_id = $req->id;
                $image->image = $photo;
                $image->image_type = "registry_papers";
                $image->save();
            }
        }
        
        if (isset($req->video_links)) {
            foreach ($req->video_links as $video_link) {
                if($video_link!=Null) {
                    $video = new Video();
                    $video->property_id = $req->id;
                    $video->video_link = $video_link;
                    $video->save();
                }
            }
        }

        if ($query)
        {
            return response()->json([
                "status" => true,
                'message' => 'Form updated successfully !'
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

    public function deletePhotos(Request $request)
    {
        $id = $request->id;

        $query = Image::find($id)->delete();
        // $image = Image::find($id);
        // $image->is_active = "Inactive";
        // $query = $image->delete();
        
        if ($query)
        {
            return response()->json([
                "status" => true,
                'message' => 'Photo deleted successfully !'
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

    public function deleteVideos(Request $request)
    {
        $id = $request->id;

        $query = Video::find($id)->delete();
        // $image = Image::find($id);
        // $image->is_active = "Inactive";
        // $query = $image->delete();
        
        if ($query)
        {
            return response()->json([
                "status" => true,
                'message' => 'Video deleted successfully !'
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


    public function propertiesMapView()
    {
        $data['propertyIds'] = Property::where("is_active", "Active")->pluck("propertyid");
        $data['propertyNames'] = Property::where("is_active", "Active")->pluck("name");
        $data['khasraNos'] = Property::where("is_active", "Active")->pluck("khasra_no");
        $data['khasraNames'] = Property::where("is_active", "Active")->pluck("khasra_name");
        $data['noOfRegs'] = Property::where("is_active", "Active")->groupBy("no_of_registry")->pluck("no_of_registry");
        $data['areas'] = Property::where("is_active", "Active")->groupBy("area")->groupBy("area_unit")->pluck("area");
        $data['fronts'] = Property::where("is_active", "Active")->groupBy("front")->groupBy("front_units")->pluck("front");
        $data['deeps'] = Property::where("is_active", "Active")->groupBy("deep")->groupBy("deep_unit")->pluck("deep");
        $data['propertySources'] = Property::where("is_active", "Active")->groupBy("source_of_property")->pluck("source_of_property");
        $data['plotFacings'] = Property::where("is_active", "Active")->groupBy("plot_facing")->pluck("plot_facing");
        $data['locations'] = Property::where("is_active", "Active")->groupBy("location")->pluck("location");
        $data['users'] = User::select("id", "name")->where("is_active", "Active")->where("status", 'Unblocked')->get();
        return view('admin.property.properties_map_view', $data);
    }

    public function propertyServerSideMap(Request $request)
    {

        if($request->ajax())
        {
            $query = Property::query();

            if($request->viewfiler != 'All'){
                if($request->viewfiler == 'ListedByMe'){
                    $query->where("user_role", "admin");
                    $query->where("created_by", Auth::user()->id);
                }
                else
                {
                    $query->where("user_role", "user");

                    if( !is_null($request->userfiler) ){
                        $query->where("created_by", $request->userfiler);
                    }
                }
            }           

            if( !empty($request->property_id) ){
                $query->where("propertyid", $request->property_id);
            }
            if( !empty($request->property_name) ){
                $query->where(function($query) use($request){
                    $query->where('name', 'LIKE', "%{$request->property_name}%");
                });
            }
            if( !empty($request->khasra_no) ){
                $query->where("khasra_no", $request->khasra_no);
            }
            if( !empty($request->khasra_name) ){
                $query->where("khasra_name", $request->khasra_name);
            }
            if( !empty($request->diverted) ){
                $query->where("diverted", $request->diverted);
            }
            if( !empty($request->type_of_land) ){
                $query->where("land_type", $request->type_of_land);
            }

            if( !empty($request->area_unit) ){
                
                if ($request->area_unit=="Acre") {
                    if( $request->from_area != '' && $request->to_area == ''){
                        $fromArea = floatval($request->from_area)*43560;
                        $query->where('area_in_sqft', intval($fromArea));
                    }
                    else if( $request->from_area == '' && $request->to_area != ''){
                        $toArea = floatval($request->to_area)*43560;
                        $query->where('area_in_sqft', intval($toArea));
                    }
                    else if( $request->from_area != '' && $request->to_area != '')
                    {
                        $fromArea = floatval($request->from_area)*43560;
                        $toArea = floatval($request->to_area)*43560;
                        $query->where('area_in_sqft', ">=", intval($fromArea));
                        $query->where('area_in_sqft', "<=", intval($toArea));
                    }
                    
                }
                else if ($request->area_unit=="Sqmt") {
                    if( $request->from_area != '' && $request->to_area == ''){
                        $fromArea = floatval($request->from_area)*11;
                        $query->where('area_in_sqft', intval($fromArea));
                    }
                    else if( $request->from_area == '' && $request->to_area != ''){
                        $toArea = floatval($request->to_area)*11;
                        $query->where('area_in_sqft', intval($toArea));
                    }
                    else if( $request->from_area != '' && $request->to_area != '')
                    {
                        $fromArea = floatval($request->from_area)*11;
                        $toArea = floatval($request->to_area)*11;
                        $query->where('area_in_sqft', ">=", intval($fromArea));
                        $query->where('area_in_sqft', "<=", intval($toArea));
                    }
                }
                else if ($request->area_unit=="Hectare") {
                    if( $request->from_area != '' && $request->to_area == ''){
                        $fromArea = floatval($request->from_area)*107639;
                        $query->where('area_in_sqft', intval($fromArea));
                    }
                    else if( $request->from_area == '' && $request->to_area != ''){
                        $toArea = floatval($request->to_area)*107639;
                        $query->where('area_in_sqft', intval($toArea));
                    }
                    else if( $request->from_area != '' && $request->to_area != '')
                    {
                        $fromArea = floatval($request->from_area)*107639;
                        $toArea = floatval($request->to_area)*107639;
                        $query->where('area_in_sqft', ">=", intval($fromArea));
                        $query->where('area_in_sqft', "<=", intval($toArea));
                    }
                }
                else if ($request->area_unit=="Dismil") {
                    if( $request->from_area != '' && $request->to_area == ''){
                        $fromArea = floatval($request->from_area)*436;
                        $query->where('area_in_sqft', intval($fromArea));
                    }
                    else if( $request->from_area == '' && $request->to_area != ''){
                        $toArea = floatval($request->to_area)*436;
                        $query->where('area_in_sqft', intval($toArea));
                    }
                    else if( $request->from_area != '' && $request->to_area != '')
                    {
                        $fromArea = floatval($request->from_area)*436;
                        $toArea = floatval($request->to_area)*436;
                        $query->where('area_in_sqft', ">=", intval($fromArea));
                        $query->where('area_in_sqft', "<=", intval($toArea));
                    }
                }
                else {
                    if( $request->from_area != '' && $request->to_area == ''){
                        $fromArea = floatval($request->from_area);
                        $query->where('area_in_sqft', intval($fromArea));
                    }
                    else if( $request->from_area == '' && $request->to_area != ''){
                        $toArea = floatval($request->to_area);
                        $query->where('area_in_sqft', intval($toArea));
                    }
                    else if( $request->from_area != '' && $request->to_area != '')
                    {
                        $fromArea = floatval($request->from_area);
                        $toArea = floatval($request->to_area);
                        $query->where('area_in_sqft', ">=", intval($fromArea));
                        $query->where('area_in_sqft', "<=", intval($toArea));
                    }
                }
            }
            else
            {
                if( $request->from_area != '' && $request->to_area == ''){
                    $fromArea = floatval($request->from_area);
                    $query->where('area_in_sqft', intval($fromArea));
                }
                else if( $request->from_area == '' && $request->to_area != ''){
                    $toArea = floatval($request->to_area);
                    $query->where('area_in_sqft', intval($toArea));
                }
                else if( $request->from_area != '' && $request->to_area != '')
                {
                    $fromArea = floatval($request->from_area);
                    $toArea = floatval($request->to_area);
                    $query->where('area_in_sqft', ">=", intval($fromArea));
                    $query->where('area_in_sqft', "<=", intval($toArea));
                }
            }

            if( !empty($request->price_unit) ){
                if ($request->price_unit=="Acre") {
                    if( $request->from_price != '' && $request->to_price == ''){
                        $fromPrice = floatval($request->from_price)*43560;
                        $query->where('price_in_sqft', intval($fromPrice));
                    }
                    else if( $request->from_price == '' && $request->to_price != ''){
                        $toPrice = floatval($request->to_price)*43560;
                        $query->where('price_in_sqft', intval($toPrice));
                    }
                    else if( $request->from_price != '' && $request->to_price != '')
                    {
                        $fromPrice = floatval($request->from_price)*43560;
                        $toPrice = floatval($request->to_price)*43560;
                        $query->where('price_in_sqft', ">=", intval($fromPrice));
                        $query->where('price_in_sqft', "<=", intval($toPrice));
                    }
                }
                else if ($request->price_unit=="Sqmt") {
                    if( $request->from_price != '' && $request->to_price == ''){
                        $fromPrice = floatval($request->from_price)*11;
                        $query->where('price_in_sqft', intval($fromPrice));
                    }
                    else if( $request->from_price == '' && $request->to_price != ''){
                        $toPrice = floatval($request->to_price)*11;
                        $query->where('price_in_sqft', intval($toPrice));
                    }
                    else if( $request->from_price != '' && $request->to_price != '')
                    {
                        $fromPrice = floatval($request->from_price)*11;
                        $toPrice = floatval($request->to_price)*11;
                        $query->where('price_in_sqft', ">=", intval($fromPrice));
                        $query->where('price_in_sqft', "<=", intval($toPrice));
                    }
                }
                else if ($request->price_unit=="Hectare") {
                    if( $request->from_price != '' && $request->to_price == ''){
                        $fromPrice = floatval($request->from_price)*107639;
                        $query->where('price_in_sqft', intval($fromPrice));
                    }
                    else if( $request->from_price == '' && $request->to_price != ''){
                        $toPrice = floatval($request->to_price)*107639;
                        $query->where('price_in_sqft', intval($toPrice));
                    }
                    else if( $request->from_price != '' && $request->to_price != '')
                    {
                        $fromPrice = floatval($request->from_price)*107639;
                        $toPrice = floatval($request->to_price)*107639;
                        $query->where('price_in_sqft', ">=", intval($fromPrice));
                        $query->where('price_in_sqft', "<=", intval($toPrice));
                    }
                }
                else if ($request->price_unit=="Dismil") {
                    if( $request->from_price != '' && $request->to_price == ''){
                        $fromPrice = floatval($request->from_price)*436;
                        $query->where('price_in_sqft', intval($fromPrice));
                    }
                    else if( $request->from_price == '' && $request->to_price != ''){
                        $toPrice = floatval($request->to_price)*436;
                        $query->where('price_in_sqft', intval($toPrice));
                    }
                    else if( $request->from_price != '' && $request->to_price != '')
                    {
                        $fromPrice = floatval($request->from_price)*436;
                        $toPrice = floatval($request->to_price)*436;
                        $query->where('price_in_sqft', ">=", intval($fromPrice));
                        $query->where('price_in_sqft', "<=", intval($toPrice));
                    }
                }
                else {
                    if( $request->from_price != '' && $request->to_price == ''){
                        $fromPrice = floatval($request->from_price);
                        $query->where('price_in_sqft', intval($fromPrice));
                    }
                    else if( $request->from_price == '' && $request->to_price != ''){
                        $toPrice = floatval($request->to_price);
                        $query->where('price_in_sqft', intval($toPrice));
                    }
                    else if( $request->from_price != '' && $request->to_price != '')
                    {
                        $fromPrice = floatval($request->from_price);
                        $toPrice = floatval($request->to_price);
                        $query->where('price_in_sqft', ">=", intval($fromPrice));
                        $query->where('price_in_sqft', "<=", intval($toPrice));
                    }
                }
            }
            else
            {
                if( $request->from_price != '' && $request->to_price == ''){
                    $fromPrice = floatval($request->from_price);
                    $query->where('price_in_sqft', intval($fromPrice));
                }
                else if( $request->from_price == '' && $request->to_price != ''){
                    $toPrice = floatval($request->to_price);
                    $query->where('price_in_sqft', intval($toPrice));
                }
                else if( $request->from_price != '' && $request->to_price != '')
                {
                    $fromPrice = floatval($request->from_price);
                    $toPrice = floatval($request->to_price);
                    $query->where('price_in_sqft', ">=", intval($fromPrice));
                    $query->where('price_in_sqft', "<=", intval($toPrice));
                }
            }

            if( !empty($request->front_unit) ){
                if ($request->front_unit=="Metre") {

                    if( $request->from_front != '' && $request->to_front == ''){
                        $fromFront = floatval($request->from_front)*4;
                        $query->where('front_in_ft', intval($fromFront));
                    }
                    else if( $request->from_front == '' && $request->to_front != ''){
                        $toFront = floatval($request->to_front)*4;
                        $query->where('front_in_ft', intval($toFront));
                    }
                    else if( $request->from_front != '' && $request->to_front != '')
                    {
                        $fromFront = floatval($request->from_front)*4;
                        $toFront = floatval($request->to_front)*4;
                        $query->where('front_in_ft', ">=", intval($fromFront));
                        $query->where('front_in_ft', "<=", intval($toFront));
                    }
                }
                else if ($request->front_unit=="Yard") {
                    if( $request->from_front != '' && $request->to_front == ''){
                        $fromFront = floatval($request->from_front)*3;
                        $query->where('front_in_ft', intval($fromFront));
                    }
                    else if( $request->from_front == '' && $request->to_front != ''){
                        $toFront = floatval($request->to_front)*3;
                        $query->where('front_in_ft', intval($toFront));
                    }
                    else if( $request->from_front != '' && $request->to_front != '')
                    {    
                        $fromFront = floatval($request->from_front)*3;
                        $toFront = floatval($request->to_front)*3;
                        $query->where('front_in_ft', ">=", intval($fromFront));
                        $query->where('front_in_ft', "<=", intval($toFront));
                    }
                }
                else if ($request->front_unit=="KM") {
                    if( $request->from_front != '' && $request->to_front == ''){
                        $fromFront = floatval($request->from_front)*3280;
                        $query->where('front_in_ft', intval($fromFront));
                    }
                    else if( $request->from_front == '' && $request->to_front != ''){
                        $toFront = floatval($request->to_front)*3280;
                        $query->where('front_in_ft', intval($toFront));
                    }
                    else if( $request->from_front != '' && $request->to_front != '')
                    {
                        $fromFront = floatval($request->from_front)*3280;
                        $toFront = floatval($request->to_front)*3280;
                        $query->where('front_in_ft', ">=", intval($fromFront));
                        $query->where('front_in_ft', "<=", intval($toFront));
                    }
                }
                else {
                    if( $request->from_front != '' && $request->to_front == ''){
                        $fromFront = floatval($request->from_front);
                        $query->where('front_in_ft', intval($fromFront));
                    }
                    else if( $request->from_front == '' && $request->to_front != ''){
                        $toFront = floatval($request->to_front);
                        $query->where('front_in_ft', intval($toFront));
                    }
                    else if( $request->from_front != '' && $request->to_front != '')
                    {
                        $fromFront = floatval($request->from_front);
                        $toFront = floatval($request->to_front);
                        $query->where('front_in_ft', ">=", intval($fromFront));
                        $query->where('front_in_ft', "<=", intval($toFront));
                    }
                }
            }
            else
            {
                if( $request->from_front != '' && $request->to_front == ''){
                    $fromFront = floatval($request->from_front);
                    $query->where('front_in_ft', intval($fromFront));
                }
                else if( $request->from_front == '' && $request->to_front != ''){
                    $toFront = floatval($request->to_front);
                    $query->where('front_in_ft', intval($toFront));
                }
                else if( $request->from_front != '' && $request->to_front != '')
                {
                    $fromFront = floatval($request->from_front);
                    $toFront = floatval($request->to_front);
                    $query->where('front_in_ft', ">=", intval($fromFront));
                    $query->where('front_in_ft', "<=", intval($toFront));
                }
            }

            if( !empty($request->deep_unit) ){
                if ($request->deep_unit=="Metre") {
                    if( $request->from_deep != '' && $request->to_deep == ''){
                        $fromDeep = floatval($request->from_deep)*4;
                        $query->where('deep_in_ft', intval($fromDeep));
                    }
                    if( $request->from_deep == '' && $request->to_deep != ''){
                        $toDeep = floatval($request->to_deep)*4;
                        $query->where('deep_in_ft', intval($toDeep));
                    }
                    if( $request->from_deep != '' && $request->to_deep != '')
                    {
                        $fromDeep = floatval($request->from_deep)*4;
                        $toDeep = floatval($request->to_deep)*4;
                        $query->where('deep_in_ft', ">=", intval($fromDeep));
                        $query->where('deep_in_ft', "<=", intval($toDeep));
                    }
                }
                else if ($request->deep_unit=="Yard") {
                    if( $request->from_deep != '' && $request->to_deep == ''){
                        $fromDeep = floatval($request->from_deep)*3;
                        $query->where('deep_in_ft', intval($fromDeep));
                    }
                    if( $request->from_deep == '' && $request->to_deep != ''){
                        $toDeep = floatval($request->to_deep)*3;
                        $query->where('deep_in_ft', intval($toDeep));
                    }
                    if( $request->from_deep != '' && $request->to_deep != '')
                    {
                        $fromDeep = floatval($request->from_deep)*3;
                        $toDeep = floatval($request->to_deep)*3;
                        $query->where('deep_in_ft', ">=", intval($fromDeep));
                        $query->where('deep_in_ft', "<=", intval($toDeep));
                    }
                }
                else if ($request->deep_unit=="KM") {
                    if( $request->from_deep != '' && $request->to_deep == ''){
                        $fromDeep = floatval($request->from_deep)*3280;
                        $query->where('deep_in_ft', intval($fromDeep));
                    }
                    if( $request->from_deep == '' && $request->to_deep != ''){
                        $toDeep = floatval($request->to_deep)*3280;
                        $query->where('deep_in_ft', intval($toDeep));
                    }
                    if( $request->from_deep != '' && $request->to_deep != '')
                    {
                        $fromDeep = floatval($request->from_deep)*3280;
                        $toDeep = floatval($request->to_deep)*3280;
                        $query->where('deep_in_ft', ">=", intval($fromDeep));
                        $query->where('deep_in_ft', "<=", intval($toDeep));
                    }
                }
                else {
                    if( $request->from_deep != '' && $request->to_deep == ''){
                        $fromDeep = floatval($request->from_deep);
                        $query->where('deep_in_ft', intval($fromDeep));
                    }
                    if( $request->from_deep == '' && $request->to_deep != ''){
                        $toDeep = floatval($request->to_deep);
                        $query->where('deep_in_ft', intval($toDeep));
                    }
                    if( $request->from_deep != '' && $request->to_deep != '')
                    {
                        $fromDeep = floatval($request->from_deep);
                        $toDeep = floatval($request->to_deep);
                        $query->where('deep_in_ft', ">=", intval($fromDeep));
                        $query->where('deep_in_ft', "<=", intval($toDeep));
                    }
                }
            }
            else
            {
                if( $request->from_deep != '' && $request->to_deep == ''){
                    $fromDeep = floatval($request->from_deep);
                    $query->where('deep_in_ft', intval($fromDeep));
                }
                if( $request->from_deep == '' && $request->to_deep != ''){
                    $toDeep = floatval($request->to_deep);
                    $query->where('deep_in_ft', intval($toDeep));
                }
                if( $request->from_deep != '' && $request->to_deep != '')
                {
                    $fromDeep = floatval($request->from_deep);
                    $toDeep = floatval($request->to_deep);
                    $query->where('deep_in_ft', ">=", intval($fromDeep));
                    $query->where('deep_in_ft', "<=", intval($toDeep));
                }
            }

            
            // if( !empty($request->from_price)){
            //     $query->where("from_price", ">=", intval($request->from_price));
            // }
            // if( !empty($request->from_price)){
            //     $query->whereBetween('to_price', [intval($request->from_price), intval($request->to_price)]);
            // }
            // if( !empty($request->to_price)){
            //     $query->where("to_price" , "<=", intval($request->to_price));
            // }
            // if( !empty($request->to_price)){
            //     $query->where("from_price" , "<=", intval($request->to_price));
            // }

            if( !empty($request->location) ){
                $query->where(function($query) use($request){
                    $query->where('location', 'LIKE', "%{$request->location}%");
                });
            }

            if( !empty($request->boundary_wall)){
                $query->where("boundary_wall", $request->boundary_wall);
            }
            if( !empty($request->bore)){
                $query->where("bore", $request->bore);
            }
            if( !empty($request->no_of_registry)){
                $query->where("no_of_registry", $request->no_of_registry);
            }
            if( !empty($request->aadiwasi_land)){
                $query->where("adiwasi_land", $request->aadiwasi_land);
            }
            if( !empty($request->verified_property)){
                $query->where("verified_property", $request->verified_property);
            }
            if( !empty($request->source_of_property)){
                $query->where("source_of_property", $request->source_of_property);
            }
            if( !empty($request->plot_facing)){
                $query->where("plot_facing", $request->plot_facing);
            }

            $query->where("is_active", "Active");

            $rows = $query->get();

            $geojson = [];

            foreach($rows as $row)
            {
                $image = Image::select('image')->where("property_id", $row->id)->first();
                $row->image = $image;

                if (isset($row->from_price)) {
                    $fromPrice = $this->no_to_words($row->from_price);
                    $price = $fromPrice." Rs./".$row->price_unit;
                }
                else
                {
                    $price = "NA";
                }

                $row->from_price = $price;

                if (isset($row->location)) {
                    $location = ucfirst($row->location);
                }
                else
                {
                    $location = "NA";
                }
                $row->location = $location;

                if (isset($row->name)) {
                    $name = ucfirst($row->name);
                }
                else
                {
                    $name = "NA";
                }
                $row->name = $name;

                $geojson[] = $row;
            }

            return response()->json($geojson);

        }
    }

    public function fetchAutocomplete(Request $request)
    {
        if($request->get('query'))
        {
            $query = $request->get('query');

            $data = Property::groupBy("location")->orderBy("location", "asc")->select("location")->where('location', 'LIKE', "%{$query}%")->get();

            $output = '';
           
            if (count($data)>0) {
              
                $output = '<ul class="list-group" style="display: block; position: relative; z-index: 1">';
              
                foreach ($data as $row){
                   
                    $output .= '<li class="list-group-item mt-0 mb-0">'.$row->location.'</li>';
                }
              
                $output .= '</ul>';
            }
            // else {
             
            //     $output .= '<li class="list-group-item">'.'No results'.'</li>';
            // }
            return response()->json($output);
        }
    }

    // associateProperties

    public function associateProperties()
    {
        $data['users'] = User::select("id", "name", "contact_no")->where("is_active", "Active")->where("status", 'Unblocked')->get();
        return view('admin.property.associate_properties', $data);
    }

    public function propertyServerSideAssociateTable(Request $request)
    {
        if($request->ajax()){
            // $userid = Auth::id();
            $userid = $request->associate;

            $ids = Assign::where("user_id", $userid)->where("is_active", "Active")->pluck("property_id");

            $query = Property::query();

            if( !empty($request->userfiler) ){
                $query->where("created_by", $request->userfiler);
            }

            $query->where("is_active", "Active");

            $query->where(function($query1) use ($ids, $userid){

                $query1->where(function($query2) use ($ids){
                    $query2->whereIn("id", $ids);
                    $query2->where("user_role", "admin");
                    $query2->where("status", "Available");
                });

                $query1->orWhere(function($query3) use ($userid){
                    $query3->where("created_by", $userid);
                    $query3->where("user_role", "user");
                });
            });

            $rows = $query->get();

            // $rows = $query->get();
            return datatables()->of($rows)->addIndexColumn()
            ->addColumn('id', function($data){
                return $data->propertyid;
            })
            ->addColumn('date', function($data){
                return date("d-m-Y h:i A", strtotime($data->created_at));
            })
            ->addColumn('name', function($data){
                if ($data->verified_property=="Yes") {
                    $name = '<a href="viewProperty/'. $data->id .'">'.$data->name.'<br>
                        <span style="font-size: 11px;" class="text-success">
                            <i style="font-size: 11px;" class="bx bxs-checkbox-checked text-success"></i>
                            Verified
                        </span>
                    </a>';
                }
                else
                {
                    $name = '<a href="viewProperty/'. $data->id .'">'.$data->name.'</a>';
                }
                return $name;
            })
            ->addColumn('land_type', function($data){
                return $data->land_type;
            })
            ->addColumn('area', function($data){
                if ($data->area) {
                    return $data->area." ".$data->area_unit;
                }
            })
            ->addColumn('price', function($data){
                // $price = $this->no_to_words($data->from_price);
                if (isset($data->from_price)) {
                    $price = $this->no_to_words($data->from_price)." Rs./".$data->price_unit;
                }
                else
                {
                    $price = "NA";
                }
                return $price;
            })
            ->addColumn('city', function($data){
                return $data->city;
            })
            ->addColumn('location', function($data){
                return $data->location;
            })
            ->addColumn('created_by', function($data){
                $created_by = $data->created_by;
                if ($data->user_role=="admin") {
                    $user = Admin::select("name", "role")->where("id", $created_by)->first();
                }
                else
                {
                    $user = User::select("name", "role")->where("id", $created_by)->first();
                }
                return $user->name." (".$user->role.")";
            })
            ->addColumn('status', function($data){
                if ($data->user_role=='admin')
                {
                    $button = '';
                    if ($data->status=='Available')
                    {
                        $button .= '<button type="button" data-id="'. $data->id .'" class="btn btn-sm btn-success block-property-btn" data-toggle="tooltip" data-placement="top" title="Click to unavailable">'.ucfirst($data->status).'</button>';
                    }
                    else
                    {
                        $button .= '<button type="button" data-id="'. $data->id .'" class="btn btn-sm btn-danger unblock-property-btn" data-toggle="tooltip" data-placement="top" title="Click to available">'.ucfirst($data->status).'</button>';
                    }
                    return $button;
                }
                else
                {
                    $button = '';
                    if ($data->status=='Available')
                    {
                        $button .= '<button type="button" data-id="'. $data->id .'" class="btn btn-sm btn-success">'.ucfirst($data->status).'</button>';
                    }
                    else
                    {
                        $button .= '<button type="button" data-id="'. $data->id .'" class="btn btn-sm btn-danger">'.ucfirst($data->status).'</button>';
                    }
                    return $button;
                }
                
            })
            ->addColumn('leads', function($data){
                $propertyid = $data->id;
                $counts = LeadAssign::whereRaw("find_in_set($propertyid , property_id)")
                ->where("is_active", "Yes")
                ->groupBy("lead_id")
                ->get()
                ->count();

                // if ($counts>0)
                // {
                    $link = '<a href="leads/'. $data->id .'" data-toggle="tooltip" data-placement="top" title="Click to see leads">'.$counts.'</a>';
                // }
                // else
                // {
                //     $link = $counts;
                // }

                return $link;
            })
            ->addColumn('actions', function($data){
                if ($data->user_role=='admin')
                {
                    $button = '<div class="ui" style="width: 120px;">';
                    $button .= ' <a href="viewProperty/'. $data->id .'" class="btn btn-sm btn-warning" data-toggle="tooltip" data-placement="top" title="View property details"><i class="bx bxs-user-detail" aria-hidden="true"></i></a>';
                    $button .= ' <a href="editProperty/'. $data->id .'" class="btn btn-sm btn-success" data-toggle="tooltip" data-placement="top" title="Edit property"><i class="bx bx-edit" aria-hidden="true"></i></a>';
                    $button .= ' <button data-id="'. $data->id .'" class="btn btn-sm btn-danger delete-property-btn" data-toggle="tooltip" data-placement="top" title="Delete property"><i class="bx bx-trash"></i></button>';
                    
                    $button .= '</div>';
                }
                else
                {
                    $button = '<div class="ui" style="width: 120px;">';
                    $button .= ' <a href="viewProperty/'. $data->id .'" class="btn btn-sm btn-warning" data-toggle="tooltip" data-placement="top" title="View property details"><i class="bx bxs-user-detail" aria-hidden="true"></i></a>';
                    $button .= '</div>';
                }
                return $button;
            })
            
            ->rawColumns(['id', 'date', 'name', 'land_type', 'area', 'price', 'city', 'location', 'created_by', 'status', 'leads', 'actions'])
                ->make(true);
        }
    }


    public function fetchAreaAutocomplete(Request $request)
    {   

        if ($request->id=="from_area") {
            $areaClass = "from-area-item";
        } 

        if ($request->id=="to_area") {
            $areaClass = "to-area-item";
        }

        $data = Property::where("is_active", "Active")->groupBy("area")->groupBy("area_unit")->whereNotNull("area")->pluck("area");
        $output = '';

        if (count($data) > 0) {
          
            $output = '<ul class="list-group" style="display: block; position: relative; z-index: 1">';
          
            foreach ($data as $row){
               
                $output .= '<li class="list-group-item '.$areaClass.' mt-0 mb-0">'.$row.'</li>';
            }
          
            $output .= '</ul>';
        }

        return response()->json($output);
    }

    public function fetchSearchAreaAutocomplete(Request $request)
    {
        if($request->get('query'))
        {
            $query = $request->get('query');

            if ($request->id=="from_area") {
                $areaClass = "from-area-item";
            } 

            if ($request->id=="to_area") {
                $areaClass = "to-area-item";
            }

            $data = Property::where("is_active", "Active")->groupBy("area")->where('area', 'LIKE', "%{$query}%")->groupBy("area_unit")->whereNotNull("area")->pluck("area");

            $output = '';
           
            if (count($data)>0) {

                $output = '<ul class="list-group" style="display: block; position: relative; z-index: 1">';
          
                foreach ($data as $row){
                   
                    $output .= '<li class="list-group-item '.$areaClass.' mt-0 mb-0">'.$row.'</li>';
                }
              
                $output .= '</ul>';
            }

            return response()->json($output);
        }
    }

    public function fetchPriceAutocomplete(Request $request)
    {   

        if ($request->id=="from_price_val") {
            $priceClass = "from-price-item";
        } 

        if ($request->id=="to_price_val") {
            $priceClass = "to-price-item";
        }

        $prices = Property::select("from_price", "from_price as from_price_key")->where("is_active", "Active")->whereNotNull("from_price")->orderByRaw('CONVERT(from_price, SIGNED) ASC')->groupBy("from_price")->get();

        foreach($prices as $price)
        {
            $price->from_price_key = $this->no_to_words($price->from_price_key);
        }

        $output = '';

        if (count($prices) > 0) {
          
            $output = '<ul class="list-group" style="display: block; position: relative; z-index: 1">';
          
            foreach ($prices as $row){
               
                $output .= '<li class="list-group-item '.$priceClass.' mt-0 mb-0" data-value="'.$row->from_price.'">'.$row->from_price_key.'</li>';
            }
          
            $output .= '</ul>';
        }

        return response()->json($output);
    }

    public function fetchSearchPriceAutocomplete(Request $request)
    {
        if($request->get('query'))
        {
            $query = $request->get('query');

            if ($request->id=="from_price_val") {
                $priceClass = "from-price-item";
            } 

            if ($request->id=="to_price_val") {
                $priceClass = "to-price-item";
            }

            $prices = Property::select("from_price", "from_price as from_price_key")->where("is_active", "Active")->whereNotNull("from_price")->where('from_price', 'LIKE', "%{$query}%")->orderByRaw('CONVERT(from_price, SIGNED) ASC')->groupBy("from_price")->get();

            foreach($prices as $price)
            {
                $price->from_price_key = $this->no_to_words($price->from_price_key);
            }

            $output = '';

            if (count($prices) > 0) {
              
                $output = '<ul class="list-group" style="display: block; position: relative; z-index: 1">';
              
                foreach ($prices as $row){
                   
                    $output .= '<li class="list-group-item '.$priceClass.' mt-0 mb-0" data-value="'.$row->from_price.'">'.$row->from_price_key.'</li>';
                }
              
                $output .= '</ul>';
            }

            return response()->json($output);
        }
    }

    public function fetchFrontAutocomplete(Request $request)
    {   

        if ($request->id=="from_front") {
            $frontClass = "from-front-item";
        } 

        if ($request->id=="to_front") {
            $frontClass = "to-front-item";
        }

        $data = Property::where("is_active", "Active")->groupBy("front")->groupBy("front_units")->whereNotNull("front")->pluck("front");

        $output = '';

        if (count($data) > 0) {
          
            $output = '<ul class="list-group" style="display: block; position: relative; z-index: 1">';
          
            foreach ($data as $row){
               
                $output .= '<li class="list-group-item '.$frontClass.' mt-0 mb-0">'.$row.'</li>';
            }
          
            $output .= '</ul>';
        }

        return response()->json($output);
    }

    public function fetchSearchFrontAutocomplete(Request $request)
    { 
        if($request->get('query'))
        {
            $query = $request->get('query');

            if ($request->id=="from_front") {
                $frontClass = "from-front-item";
            } 

            if ($request->id=="to_front") {
                $frontClass = "to-front-item";
            }

            $data = Property::where("is_active", "Active")->where('front', 'LIKE', "%{$query}%")->groupBy("front")->groupBy("front_units")->whereNotNull("front")->pluck("front");

            $output = '';

            if (count($data) > 0) {
              
                $output = '<ul class="list-group" style="display: block; position: relative; z-index: 1">';
              
                foreach ($data as $row){
                   
                    $output .= '<li class="list-group-item '.$frontClass.' mt-0 mb-0">'.$row.'</li>';
                }
              
                $output .= '</ul>';
            }

            return response()->json($output);
        }
    }

    public function fetchDeepAutocomplete(Request $request)
    {   

        if ($request->id=="from_deep") {
            $deepClass = "from-deep-item";
        } 

        if ($request->id=="to_deep") {
            $deepClass = "to-deep-item";
        }

        $data = Property::where("is_active", "Active")->groupBy("deep")->groupBy("deep_unit")->whereNotNull("deep")->pluck("deep");

        $output = '';

        if (count($data) > 0) {
          
            $output = '<ul class="list-group" style="display: block; position: relative; z-index: 1">';
          
            foreach ($data as $row){
               
                $output .= '<li class="list-group-item '.$deepClass.' mt-0 mb-0">'.$row.'</li>';
            }
          
            $output .= '</ul>';
        }

        return response()->json($output);
    }

    public function fetchSearchDeepAutocomplete(Request $request)
    {   
        if($request->get('query'))
        {
            $query = $request->get('query');

            if ($request->id=="from_deep") {
                $deepClass = "from-deep-item";
            } 

            if ($request->id=="to_deep") {
                $deepClass = "to-deep-item";
            }

            $data = Property::where("is_active", "Active")->where('deep', 'LIKE', "%{$query}%")->groupBy("deep")->groupBy("deep_unit")->whereNotNull("deep")->pluck("deep");

            $output = '';

            if (count($data) > 0) {
              
                $output = '<ul class="list-group" style="display: block; position: relative; z-index: 1">';
              
                foreach ($data as $row){
                   
                    $output .= '<li class="list-group-item '.$deepClass.' mt-0 mb-0">'.$row.'</li>';
                }
              
                $output .= '</ul>';
            }

            return response()->json($output);
        }
    }

}
