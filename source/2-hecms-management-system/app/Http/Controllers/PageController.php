<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class PageController extends Controller
{
    public function ViewIndexPageFunction(){

        return view('index'); 

    }

    public function ViewHomePageFunction(string $search_by_month){
        
        $login_access_session = Session::get('LoginAccess');

        if($login_access_session == '[SUPER_ADMIN]'){


            $month_picker_display = "";

            if($search_by_month == '[FALSE]'){

                $search_data = $current_year = date('Y')."-".$current_month = date('m')."-".$current_month = date('d');
                $month_picker_display = date('Y')."-".date('m');
            }else{

                $month_picker_display = $search_by_month;
                $search_data = $search_by_month."-"."02";
               
            }
            $traffic_data = DB::select("SELECT DAY(date) AS day, COUNT(*) AS count FROM data WHERE MONTH(date) = MONTH('$search_data') AND YEAR(date) = YEAR('$search_data') GROUP BY DAY(date) ORDER BY day ASC;");            
            $total_devices = DB::select("SELECT COUNT(*) as total_count FROM device");
            $total_incidents = DB::select("SELECT COUNT(*) as total_count FROM data");
            $total_elephants_detected = DB::select("SELECT SUM(number_of_elephant) as total_count FROM data");
            
            return view('home-page',['PageName' => 'Home Page', "YearMonth" => $month_picker_display , 'TrafficData' => $traffic_data, 'TotalDevices' => $total_devices, 'TotalIncidents' => $total_incidents, 'TotalElephantsDetected' => $total_elephants_detected]); 

            
        }else if($login_access_session == '[DEVICE_ADMIN]'){

            // Device Admin Logic Come Here

        }else{

            return redirect()->route('IndexPageLink');
            
        }
        
    }


    public function ViewDataManagementFunction(string $search_by_date){

        $login_access_session = Session::get('LoginAccess');
    
        if($login_access_session == '[SUPER_ADMIN]'){
    
            if($search_by_date == '[FALSE]'){
        
                $whole_data_management_data = DB::table('data')->paginate(15);
                return view('data-management',['PageName' => 'Data Management', "type_of_search" => "[WHOLE_SEARCH]", "DataManagementData"=>$whole_data_management_data]); 
        
            }else{
        
                $date_wise_data_management_data = DB::table('data')->where('date', '=', $search_by_date)->paginate(15);
        
                return view('data-management',['PageName' => 'Data Management', "type_of_search" => "[DATE_WISE_SEARCH]", "DataManagementData"=>$date_wise_vision_data]); 
            }

        }else if($login_access_session == '[DEVICE_ADMIN]'){

            // Device Admin Logic Come Here

        }else{
            
            return redirect()->route('IndexPageLink');
       
        }
    }

    public function ShowImageFunctionInNewPage($id){

        $login_access_session = Session::get('LoginAccess');
        
        if($login_access_session == '[SUPER_ADMIN]' || $login_access_session == '[DEVICE_ADMIN]'){

            $image_data_db = DB::select("SELECT elephant_image from data WHERE auto_id = '$id'");
            $imageData = base64_encode($image_data_db[0]->elephant_image);
            $imageData = base64_decode($imageData);
            return response($imageData)->header('Content-Type', 'image/jpeg');
            
        }
        
    }

    public function ViewAudioDataFunction(string $search_by_date){
    
        $login_access_session = Session::get('LoginAccess');
    
        if($login_access_session == '[TRUE]'){
    
            if($search_by_date == '[FALSE]'){
    
                $whole_audio_data = DB::table('audio_data')->paginate(25);
                return view('audio-data',['PageName' => 'Audio Data',"type_of_search" => "[WHOLE_SEARCH]", "audio_data"=>$whole_audio_data]); 
    
            }else{
    
                $date_wise_audio_data = DB::table('audio_data')->where('date', $search_by_date)->paginate(25);
                return view('audio-data',['PageName' => 'Audio Data',"type_of_search" => "[DATE_WISE_SEARCH]", "audio_data"=>$date_wise_audio_data]); 
                
            }
            
        }else{
    
            return redirect()->route('IndexPageLink');
            
        }
        
    }
    

    public function ViewSettingsFunction(){
        
        $login_access_session = Session::get('LoginAccess');

        if($login_access_session == '[TRUE]'){

            $current_language = DB::table('setting')->where('_key', 'voice_lang')->value('_value');


            return view('settings', [
                'PageName' => 'Settings',
                'CurrentLanguage' => $current_language
            ]);
            
        }else{

            return redirect()->route('IndexPageLink');
            
        }
        
    }
}