<?php

namespace App\Http\Controllers\Employee;

use Illuminate\Http\Request;
use App\Models\Property;
use App\Models\Image;
use App\Models\Admin;
use App\Models\User;
use App\Models\Video;
use App\Models\Assign;
use App\Models\LeadAssign;
use Auth;

class PropertyController extends Controller
{
    public function Properties()
    {
        $user_id = Auth::id();

        $assignedProperties = Assign::join("properties as p", "p.id", "=", "assigns.property_id", "left")->select("p.propertyid", "p.name", "p.khasra_no", "p.khasra_name", "p.no_of_registry", "p.area", "p.front", "p.deep", "p.source_of_property", "p.plot_facing", "p.location", "p.area_in_sqft")->where("assigns.user_id", $user_id)->where("assigns.is_active", "Active")->where("p.user_role", "admin")->where("p.is_active", "Active")->where("p.status", "Available")->get();

        $properties = Property::select("propertyid", "name", "khasra_no", "khasra_name", "no_of_registry", "area", "front", "deep", "source_of_property", "plot_facing", "location", "area_in_sqft")->where("created_by", $user_id)->where("user_role", "user")->where("is_active", "Active")->where("status", "Available")->get();

        foreach($assignedProperties as $assignedProperty) {
            $properties->add($assignedProperty);
        }

        $data['properties'] = $properties;

        $assignedPrices = Assign::join("properties as p", "p.id", "=", "assigns.property_id", "left")->select("p.from_price", "p.from_price as from_price_key")->where("assigns.user_id", $user_id)->where("assigns.is_active", "Active")->where("p.user_role", "admin")->where("p.is_active", "Active")->where("p.status", "Available")->whereNotNull("p.from_price")->orderByRaw('CONVERT(p.from_price, SIGNED) ASC')->groupBy("p.from_price")->get();

        $prices = Property::select("from_price", "from_price as from_price_key")->where("is_active", "Active")->where("status", "Available")->whereNotNull("from_price")->orderByRaw('CONVERT(from_price, SIGNED) ASC')->where("created_by", $user_id)->where("user_role", "user")->groupBy("from_price")->get();

         foreach($assignedPrices as $assignedPrice) {
            $prices->add($assignedPrice);
        }

        foreach($prices as $price)
        {
            $price->from_price_key = $this->no_to_words($price->from_price_key);
        }

        $data['prices'] = $prices;

    	return view('employee.property.properties', $data);
    }

    public function propertyServerSideTable(Request $request)
    {
        if($request->ajax()){
            $userid = Auth::id();

            $ids = Assign::where("user_id", $userid)->where("is_active", "Active")->pluck("property_id");

            $query = Property::query();

            if($request->viewfiler != 'All'){
                if($request->viewfiler == 'ListedByMe'){
                    $query->where("user_role", "user");
                }
                else
                {
                    $query->where("user_role", "admin");
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


            // if( $request->from_area != ''){
            //     $query->where('area', ">=", intval($request->from_area));
            // }
            // if( $request->to_area != ''){
            //     $query->where('area', "<=", intval($request->to_area));
            // }
            // if( !empty($request->area_unit) ){
            //     $query->where("area_unit", $request->area_unit);
            // }


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

            foreach ($rows as $data)
            {
                $module = Assign::where("user_id", $userid)->where("property_id", $data->id)->where("is_active", "Active")->value("module_ids");

                $options = explode(",", $module);

                $data->option = $options;

                $ptopertyImg = "assets/img/noimage.png";
                $image = Image::where('property_id', $data->id)->where("image_type", "photos")->value("image");
                if ($image)
                {
                    $ptopertyImg = asset("storage/photos/".$image);
                }
                $data->image = $ptopertyImg;

                // verified_property
                if ($data->status=='Available')
                {
                    if ($data->verified_property=="Yes") {
                        $name = '<a href="viewProperty/'. $data->id .'">'.ucfirst($data->name).'
                            <span style="font-size: 11px;" class="text-success">
                                <i style="font-size: 11px;" class="bx bxs-checkbox-checked text-success"></i>
                                Verified
                            </span>
                        </a>';
                    }
                    else
                    {
                        $name = '<a href="viewProperty/'. $data->id .'">'.ucfirst($data->name).'</a>';
                    }
                }
                else
                {
                    if ($data->verified_property=="Yes") {
                        $name = '<a href="javascript:void(0);">'.ucfirst($data->name).'
                            <span style="font-size: 11px;" class="text-success">
                                <i style="font-size: 11px;" class="bx bxs-checkbox-checked text-success"></i>
                                Verified
                            </span>
                        </a>';
                    }
                    else
                    {
                        $name = ucfirst($data->name);
                    }
                }

                $data->name = $name;

                $data->areaval = $data->area;

                if ($data->area) {
                    $area = $data->area." ".$data->area_unit;
                }
                else
                {
                    $area = "NA";
                }
                $data->area = $area;

                // if ($data->from_price && $data->to_price) {
                //     $price = "&#x20B9; ".$data->from_price."-".$data->to_price;
                // }
                // elseif($data->from_price)
                // {
                //     $price = "&#x20B9; ".$data->from_price;
                // }
                // else
                // {
                //     $price = "NA";
                // }
                // $data->price = $price;

                if($data->from_price)
                {
                    $fromPrice = $this->no_to_words($data->from_price);
                    $price = $fromPrice." Rs./".$data->price_unit;
                }
                else
                {
                    $price = "NA";
                }
                $data->price = $price;

                if ($data->front && $data->deep) {

                    $data->front_units = $this->shortFrontUnit($data->front_units);
                    $data->deep_unit = $this->shortDeepUnit($data->deep_unit);

                    $front_deep = $data->front." ".$data->front_units." x ".$data->deep." ".$data->deep_unit;
                    $front_deep_title = "Front x Deep";
                }
                else if (!isset($data->deep) && isset($data->front)) {
                    $data->front_units = $this->shortFrontUnit($data->front_units);
                    $front_deep = $data->front." ".$data->front_units;
                    $front_deep_title = "Front";
                }
                else if (!isset($data->front) && isset($data->deep)) {
                    $data->deep_unit = $this->shortDeepUnit($data->deep_unit);
                    $front_deep = $data->deep." ".$data->deep_unit;
                    $front_deep_title = "Deep";
                }
                else
                {
                    $front_deep = "NA";
                    $front_deep_title = "Front x Deep";
                }
                $data->front_deep = $front_deep;
                $data->front_deep_title = $front_deep_title;
                

                if ($data->front) {
                    $front = $data->front." ".$data->front_units;
                }
                else
                {
                    $front = "NA";
                }
                $data->front = $front;

                if ($data->deep) {
                    $deep = $data->deep." ".$data->deep_unit;
                }
                else
                {
                    $deep = "NA";
                }
                $data->deep = $deep;

                if ($data->location) {
                    $location = ucfirst($data->location);
                }
                else
                {
                    $location = "NA";
                }
                $data->location = $location;

                if ($data->city) {
                    $city = ucfirst($data->city);
                }
                else
                {
                    $city = "NA";
                }
                $data->city = $city;

                if ($data->user_role=="admin") {
                    $data->user = "Property Bank";
                }
                else
                {
                    $data->user = "Me";
                }

                $data->date = date("d-m-Y h:i A", strtotime($data->created_at));


                $propertyid = $data->id;

                $counts = LeadAssign::where(function ($q) use($propertyid){
                    $q->whereRaw("find_in_set($propertyid, property_id)");
                    $q->where("is_active", "Yes");
                    $q->where("creator_role", "admin");
                    $q->where("assignee_role", "user");
                    $q->where("user_id", Auth::user()->id);
                })
                ->orWhere(function ($q2) use($propertyid){
                    $q2->whereRaw("find_in_set($propertyid, property_id)");
                    $q2->where("is_active", "Yes");
                    $q2->where("creator_role", "user");
                    $q2->where("user_id", Auth::user()->id);
                })
                ->groupBy("lead_id")
                ->get()
                ->count();

                // $counts = LeadAssign::whereRaw("find_in_set($propertyid , property_id)")
                // ->where("is_active", "Yes")
                // ->groupBy("lead_id")
                // ->get()
                // ->count();

                // if ($counts>0)
                // {
                    $link = '<a href="leads/'. $data->id .'" class="btn btn-primary btn-block" data-toggle="tooltip" data-placement="top" title="Click to see leads">Leads ('.$counts.')</a>';
                // }
                // else
                // {
                //     $link = '<a href="leads/'. $data->id .'" class="btn btn-primary btn-block">Leads ('.$counts.')</a>';
                // }

                $data->leads = $link;

            }


            return response()->json($rows);
        }
    } 

    public function shortFrontUnit($front_unit)
    {
        if($front_unit=="Metre")
        {
            $front_unit = "M";
        }
        else if($front_unit=="Feet")
        {
            $front_unit = "Ft";
        }
        else if($front_unit=="Yard")
        {
            $front_unit = "Yd";
        }
        else if($front_unit=="KM")
        {
            $front_unit = "Ft";
        }

        return $front_unit;
    }

    public function shortDeepUnit($deep_unit)
    {
        if($deep_unit=="Metre")
        {
            $deep_unit = "M";
        }
        else if($deep_unit=="Feet")
        {
            $deep_unit = "Ft";
        }
        else if($deep_unit=="Yard")
        {
            $deep_unit = "Yd";
        }
        else if($deep_unit=="KM")
        {
            $deep_unit = "Ft";
        }

        return $deep_unit;
    }

    public function viewProperty($id)
    {
        $userid = Auth::id();

        $data['property'] = Property::where('id', $id)->where("is_active", "Active")->first();

        if ($data['property'])
        {
            $data['assigned'] = Assign::where('user_id', $userid)->where('property_id', $data['property']->id)->where("is_active", "Active")->first();
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

            if(isset($data['property']->from_price))
            {
                $fromPrice = $this->no_to_words($data['property']->from_price);
                $price = $fromPrice;
            }
            else
            {
                $price = "NA";
            }

            $data['property']->price = $price;

            if(isset($data['property']->registry_price))
            {
                $registry_price = $this->no_to_words($data['property']->registry_price);
                // $price = $registry_price;
            }
            else
            {
                $registry_price = "NA";
            }

            $data['property']->registry_price = $registry_price;
        }

        $data['patwaris'] = Image::where('image_type', "patwarinaksha")->where('property_id', $id)->get();
        $data['chauhaddis'] = Image::where('image_type', "chauhaddi")->where('property_id', $id)->get();
        $data['extradocs'] = Image::where('image_type', "extra_documents")->where('property_id', $id)->get();
        $data['regPapers'] = Image::where('image_type', "registry_papers")->where('property_id', $id)->get();
        $data['images'] = Image::where('image_type', "photos")->where('property_id', $id)->get();
        $data['photo'] = Image::where('image_type', "photos")->where('property_id', $id)->first();
        $data['googles'] = Image::where('image_type', "googlenaksha")->where('property_id', $id)->get();
        $data['videos'] = Video::where('property_id', $id)->get();
        return view('employee.property.view_property', $data);
    }
    

    public function propertiesMapView()
    {
        $user_id = Auth::id();

        $assignedProperties = Assign::join("properties as p", "p.id", "=", "assigns.property_id", "left")->select("p.propertyid", "p.name", "p.khasra_no", "p.khasra_name", "p.no_of_registry", "p.area", "p.front", "p.deep", "p.source_of_property", "p.plot_facing", "p.location")->where("assigns.user_id", $user_id)->where("assigns.is_active", "Active")->where("p.user_role", "admin")->where("p.is_active", "Active")->where("p.status", "Available")->get();

        $properties = Property::select("propertyid", "name", "khasra_no", "khasra_name", "no_of_registry", "area", "front", "deep", "source_of_property", "plot_facing", "location")->where("created_by", $user_id)->where("user_role", "user")->where("is_active", "Active")->where("status", "Available")->get();

        foreach($assignedProperties as $assignedProperty) {
            $properties->add($assignedProperty);
        }

        $data['properties'] = $properties;
        
        return view('employee.property.properties_map_view', $data);
    }

    public function propertyServerSideMap(Request $request)
    {

        if($request->ajax())
        {
            $query = Property::query();

            $userid = Auth::id();

            $ids = Assign::where("user_id", $userid)->where("is_active", "Active")->pluck("property_id");

            $query = Property::query();

            if($request->viewfiler != 'All'){
                if($request->viewfiler == 'ListedByMe'){
                    $query->where("user_role", "user");
                }
                else
                {
                    $query->where("user_role", "admin");
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
            
            // if( $request->from_area != ''){
            //     $query->where('area', ">=", intval($request->from_area));
            // }
            // if( $request->to_area != ''){
            //     $query->where('area', "<=", intval($request->to_area));
            // }
            // if( !empty($request->area_unit) ){
            //     $query->where("area_unit", $request->area_unit);
            // }



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
            $query->where("status", "Available");

            $query->where(function($query1) use ($ids, $userid){

                $query1->where(function($query2) use ($ids){
                    $query2->whereIn("id", $ids);
                    $query2->where("user_role", "admin");
                });

                $query1->orWhere(function($query3) use ($userid){
                    $query3->where("created_by", $userid);
                    $query3->where("user_role", "user");
                });
            });
            
            // $query->whereIn("id", $ids);

            $rows = $query->get();

            $geojson = [];

            foreach($rows as $row)
            {
                $image = Image::select('image')->where("property_id", $row->id)->first();
                $row->image = $image;

                $module = Assign::where("user_id", $userid)->where("property_id", $row->id)->where("is_active", "Active")->value("module_ids");

                $options = explode(",", $module);

                $row->option = $options;

                $geojson[] = $row;
            }

            return response()->json($geojson);

        }
    }

    public function propertyServerSideTable1(Request $request)
    {
        if($request->ajax()){
            $query = Property::query();

            if( !empty($request->property_id) ){
                $query->where("id", $request->property_id);
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
            // if( $request->from_area != ''){
            //     $query->where('area', ">=", intval($request->from_area));
            // }
            // if( $request->to_area != ''){
            //     $query->where('area', "<=", intval($request->to_area));
            // }
            // if( !empty($request->area_unit) ){
            //     $query->where("area_unit", $request->area_unit);
            // }

            if( !empty($request->area_unit) ){
                if ($request->area_unit=="Acre") {
                    if( $request->from_area != ''){
                        $fromArea = intval($request->from_area)*43560;
                        $query->where('area', ">=", $fromArea);
                    }
                    if( $request->to_area != ''){
                        $toArea = intval($request->to_area)*43560;
                        $query->where('area', "<=", $toArea);
                    }
                }
                else if ($request->area_unit=="Hectare") {
                    if( $request->from_area != ''){
                        $fromArea = intval($request->from_area)*107639;
                        $query->where('area', ">=", $fromArea);
                    }
                    if( $request->to_area != ''){
                        $toArea = intval($request->to_area)*107639;
                        $query->where('area', "<=", $toArea);
                    }
                }
                else if ($request->area_unit=="Dismil") {
                    if( $request->from_area != ''){
                        $fromArea = intval($request->from_area)*436;
                        $query->where('area', ">=", $fromArea);
                    }
                    if( $request->to_area != ''){
                        $toArea = intval($request->to_area)*436;
                        $query->where('area', "<=", $toArea);
                    }
                }
                else {
                    if( $request->from_area != ''){
                        $fromArea = intval($request->from_area);
                        $query->where('area', ">=", $fromArea);
                    }
                    if( $request->to_area != ''){
                        $toArea = intval($request->to_area);
                        $query->where('area', "<=", $toArea);
                    }
                }
            }
            else
            {
                if( $request->from_area != ''){
                    $fromArea = intval($request->from_area);
                    $query->where('area', ">=", $fromArea);
                }
                if( $request->to_area != ''){
                    $toArea = intval($request->to_area);
                    $query->where('area', "<=", $toArea);
                }
            }

            if( !empty($request->from_price)){
                $query->where("from_price", ">=", intval($request->from_price));
            }
            if( !empty($request->to_price)){
                $query->where("to_price" , "<=", intval($request->to_price));
            }

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
            $query->where("is_active", "Active");
            // $query->where("status", "Available");
            $rows = $query->get();
            return datatables()->of($rows)->addIndexColumn()
            ->addColumn('id', function($data){
                return $data->id;
            })
            ->addColumn('name', function($data){
                // verified_property
                if ($data->status=='Available')
                {
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
                }
                else
                {
                    if ($data->verified_property=="Yes") {
                        $name = '<a href="javascript:void(0);">'.$data->name.'<br>
                            <span style="font-size: 11px;" class="text-success">
                                <i style="font-size: 11px;" class="bx bxs-checkbox-checked text-success"></i>
                                Verified
                            </span>
                        </a>';
                    }
                    else
                    {
                        $name = $data->name;
                    }
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
                if ($data->to_price) {
                    return $data->from_price."-".$data->to_price;
                }
                else
                {
                    return $data->from_price;
                }
            })
            ->addColumn('location', function($data){
                return $data->location;

            })
            ->addColumn('status', function($data){
                $button = '';
                if ($data->status=='Available')
                {
                    $button .= '<div class="btn btn-sm btn-success">'.ucfirst($data->status).'</div>';
                }
                else
                {
                    $button .= '<div class="btn btn-sm btn-danger">'.ucfirst($data->status).'</div>';
                }
                // $button .= '</div>';
                return $button;
            })
            
            ->rawColumns(['id', 'name', 'land_type', 'area', 'price', 'location', 'status'])
            ->make(true);
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


    public function fetchAreaAutocomplete(Request $request)
    {   

        if ($request->id=="from_area") {
            $areaClass = "from-area-item";
        } 

        if ($request->id=="to_area") {
            $areaClass = "to-area-item";
        }
        
        $user_id = Auth::user()->id;
        $assignedProperties = Assign::join("properties as p", "p.id", "=", "assigns.property_id", "left")->where("assigns.user_id", $user_id)->where("assigns.is_active", "Active")->where("p.user_role", "admin")->where("p.is_active", "Active")->where("p.status", "Available")->pluck("p.area");

        $properties = Property::where("created_by", $user_id)->where("user_role", "user")->where("is_active", "Active")->where("status", "Available")->pluck("area");

        foreach($assignedProperties as $assignedProperty) {
            $properties->add($assignedProperty);
        }

        $output = '';

        if (count($properties) > 0) {
          
            $output = '<ul class="list-group" style="display: block; position: relative; z-index: 1">';
          
            foreach ($properties as $row){
               
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
            
            $user_id = Auth::user()->id;
            $assignedProperties = Assign::join("properties as p", "p.id", "=", "assigns.property_id", "left")->where("assigns.user_id", $user_id)->where('p.area', 'LIKE', "%{$query}%")->where("assigns.is_active", "Active")->where("p.user_role", "admin")->where("p.is_active", "Active")->where("p.status", "Available")->pluck("p.area");

            $properties = Property::where("created_by", $user_id)->where("user_role", "user")->where("is_active", "Active")->where('area', 'LIKE', "%{$query}%")->where("status", "Available")->pluck("area");

            foreach($assignedProperties as $assignedProperty) {
                $properties->add($assignedProperty);
            }

            $output = '';

            if (count($properties) > 0) {
              
                $output = '<ul class="list-group" style="display: block; position: relative; z-index: 1">';
              
                foreach ($properties as $row){
                   
                    $output .= '<li class="list-group-item '.$areaClass.' mt-0 mb-0">'.$row.'</li>';
                }
              
                $output .= '</ul>';
            }

            return response()->json($output);
        }
    }

    public function fetchPriceAutocomplete(Request $request)
    {   
        $user_id = Auth::user()->id;

        if ($request->id=="from_price_val") {
            $priceClass = "from-price-item";
        } 

        if ($request->id=="to_price_val") {
            $priceClass = "to-price-item";
        }

        $assignedPrices = Assign::join("properties as p", "p.id", "=", "assigns.property_id", "left")->select("p.from_price", "p.from_price as from_price_key")->where("assigns.user_id", $user_id)->where("assigns.is_active", "Active")->where("p.user_role", "admin")->where("p.is_active", "Active")->where("p.status", "Available")->whereNotNull("p.from_price")->orderByRaw('CONVERT(p.from_price, SIGNED) ASC')->groupBy("p.from_price")->get();

        $prices = Property::select("from_price", "from_price as from_price_key")->where("is_active", "Active")->where("status", "Available")->whereNotNull("from_price")->orderByRaw('CONVERT(from_price, SIGNED) ASC')->where("created_by", $user_id)->where("user_role", "user")->groupBy("from_price")->get();

         foreach($assignedPrices as $assignedPrice) {
            $prices->add($assignedPrice);
        }

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

            $user_id = Auth::user()->id;

            if ($request->id=="from_price_val") {
                $priceClass = "from-price-item";
            } 

            if ($request->id=="to_price_val") {
                $priceClass = "to-price-item";
            }

            $assignedPrices = Assign::join("properties as p", "p.id", "=", "assigns.property_id", "left")->select("p.from_price", "p.from_price as from_price_key")->where("assigns.user_id", $user_id)->where("assigns.is_active", "Active")->where('p.from_price', 'LIKE', "%{$query}%")->where("p.user_role", "admin")->where("p.is_active", "Active")->where("p.status", "Available")->whereNotNull("p.from_price")->orderByRaw('CONVERT(p.from_price, SIGNED) ASC')->groupBy("p.from_price")->get();

            $prices = Property::select("from_price", "from_price as from_price_key")->where("is_active", "Active")->where("status", "Available")->where('from_price', 'LIKE', "%{$query}%")->whereNotNull("from_price")->orderByRaw('CONVERT(from_price, SIGNED) ASC')->where("created_by", $user_id)->where("user_role", "user")->groupBy("from_price")->get();

             foreach($assignedPrices as $assignedPrice) {
                $prices->add($assignedPrice);
            }

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

        $user_id = Auth::user()->id;
        $assignedProperties = Assign::join("properties as p", "p.id", "=", "assigns.property_id", "left")->where("assigns.user_id", $user_id)->where("assigns.is_active", "Active")->where("p.user_role", "admin")->groupBy("p.front")->groupBy("p.front_units")->where("p.is_active", "Active")->where("p.status", "Available")->pluck("p.front");

        $properties = Property::where("created_by", $user_id)->where("user_role", "user")->where("is_active", "Active")->groupBy("front")->groupBy("front_units")->where("status", "Available")->pluck("front");

        foreach($assignedProperties as $assignedProperty) {
            $properties->add($assignedProperty);
        }

        $output = '';

        if (count($properties) > 0) {
          
            $output = '<ul class="list-group" style="display: block; position: relative; z-index: 1">';
          
            foreach ($properties as $row){
               
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

            $user_id = Auth::user()->id;
            $assignedProperties = Assign::join("properties as p", "p.id", "=", "assigns.property_id", "left")->where("assigns.user_id", $user_id)->where('p.front', 'LIKE', "%{$query}%")->where("assigns.is_active", "Active")->where("p.user_role", "admin")->groupBy("p.front")->groupBy("p.front_units")->where("p.is_active", "Active")->where("p.status", "Available")->pluck("p.front");

            $properties = Property::where("created_by", $user_id)->where('front', 'LIKE', "%{$query}%")->where("user_role", "user")->where("is_active", "Active")->groupBy("front")->groupBy("front_units")->where("status", "Available")->pluck("front");

            foreach($assignedProperties as $assignedProperty) {
                $properties->add($assignedProperty);
            }

            $output = '';

            if (count($properties) > 0) {
              
                $output = '<ul class="list-group" style="display: block; position: relative; z-index: 1">';
              
                foreach ($properties as $row){
                   
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

        $user_id = Auth::user()->id;
        $assignedProperties = Assign::join("properties as p", "p.id", "=", "assigns.property_id", "left")->where("assigns.user_id", $user_id)->where("assigns.is_active", "Active")->where("p.user_role", "admin")->whereNotNull("deep")->where("p.is_active", "Active")->where("p.status", "Available")->groupBy("p.deep")->groupBy("p.deep_unit")->pluck("p.deep");

        $properties = Property::where("created_by", $user_id)->where("user_role", "user")->where("is_active", "Active")->whereNotNull("deep")->where("status", "Available")->groupBy("deep")->groupBy("deep_unit")->pluck("deep");

        foreach($assignedProperties as $assignedProperty) {
            $properties->add($assignedProperty);
        }

        $output = '';

        if (count($properties) > 0) {
          
            $output = '<ul class="list-group" style="display: block; position: relative; z-index: 1">';
          
            foreach ($properties as $row){
               
                $output .= '<li class="list-group-item '.$deepClass.' mt-0 mb-0">'.$row.'</li>';
            }
          
            $output .= '</ul>';
        }

        return response()->json($output);
    }

    public function fetchSearchDeepAutocomplete(Request $request)
    {   
        // if($request->get('query'))
        // {
            $query = $request->get('query');

            if ($request->id=="from_deep") {
                $deepClass = "from-deep-item";
            } 

            if ($request->id=="to_deep") {
                $deepClass = "to-deep-item";
            }

            $user_id = Auth::user()->id;
            $assignedProperties = Assign::join("properties as p", "p.id", "=", "assigns.property_id", "left")->where("assigns.user_id", $user_id)->where("assigns.is_active", "Active")->where("p.user_role", "admin")->where('p.deep', 'LIKE', "%{$query}%")->whereNotNull("deep")->where("p.is_active", "Active")->where("p.status", "Available")->groupBy("p.deep")->groupBy("p.deep_unit")->pluck("p.deep");

            $properties = Property::where("created_by", $user_id)->where("user_role", "user")->where("is_active", "Active")->where('deep', 'LIKE', "%{$query}%")->whereNotNull("deep")->where("status", "Available")->groupBy("deep")->groupBy("deep_unit")->pluck("deep");

            foreach($assignedProperties as $assignedProperty) {
                $properties->add($assignedProperty);
            }

            $output = '';

            if (count($properties) > 0) {
              
                $output = '<ul class="list-group" style="display: block; position: relative; z-index: 1">';
              
                foreach ($properties as $row){
                   
                    $output .= '<li class="list-group-item '.$deepClass.' mt-0 mb-0">'.$row.'</li>';
                }
              
                $output .= '</ul>';
            }

            return response()->json($output);
        // }
    }


}
