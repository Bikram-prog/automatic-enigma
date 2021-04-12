<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UsersMeta;
use App\Models\UserPhotos;
use App\Models\Connectionsfinal;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Crypt;
use DateTime;
use Mail;

class ApiController extends Controller
{
    public $users = [];
    public $usersMatch = [];
	
    public function viewBrowse($id){
        // /(select * from user_photos where user_id = '".$id."')
        //LEFT JOIN `user_photos` ON `user_photos`.`user_id` = `users`.`id` 
    	$data = $data = DB::select("SELECT * FROM `users` LEFT JOIN `users_metas` ON `users_metas`.`user_id` = `users`.`id` LEFT JOIN `users_match` ON `users_match`.`users_match_id` = `users`.`id` WHERE `users`.`id` != '".$id."' AND `users`.`id` NOT IN ((SELECT b_who_id FROM block_users WHERE b_whom_id = '".$id."')) ORDER BY `users`.`id` DESC");

          

        foreach($data as $d) {
            //get 1 photo

            $data2 = $data = DB::select("select * from user_photos where user_id = '".$d->id."' and user_photo_path != '' limit 1");
            if(count($data2) > 0) {
                $arr[] = array(
                'id' => $d->id,
                'name' => $d->name,
                'age' => $d->age,
                'sex' => $d->sex,
                'prfl_photo_path' => ($d->prfl_photo_path == '' ? 'https://www.globallove.online/images/male-user-placeholder.png' : 'https://www.globallove.online/images/' . $d->prfl_photo_path),
                'user_photo_path' => (count($data2) > 0 ? 'https://www.globallove.online/images/' . $data2[0]->user_photo_path : 'https://dummyimage.com/600x400/777/fff.jpg&text=Globalworld'),
            );
            }
            
        }


	   if (isset($_REQUEST['distance'])){
			$data = DB::table('users')
                ->leftJoin('users_metas', 'users_metas.user_id', '=', 'users.id')
                ->leftJoin('users_match', 'users_match.users_match_id', '=', 'users.id')
                ->leftJoin('user_photos', 'user_photos.user_id', '=', 'users.id')
                ->where('users.id', '!=', $id)
                ->where('users_metas.sex', '=', $request->input('distance'))
                ->whereBetween('users_metas.age', [(int)$request->input('fromAge'), (int)$request->input('toAge')])
                ->whereNotIn('users.id', DB::table('block_users')->select('b_who_id')->where('b_whom_id','=', $id))
                ->orderBy('users.created_at', 'desc')
                ->get();
	   }
	   return json_encode($arr);
    }

    public function viewMatch($id){

         $this->usersMatch = DB::Select("SELECT * FROM users_match WHERE users_match_id = '".$id."'");

        $sex = $this->usersMatch[0]->match_seeking;
        $age_min = $this->usersMatch[0]->match_min_age;
        $age_max = $this->usersMatch[0]->match_max_age;
        $country = $this->usersMatch[0]->match_country;
        $drink = explode(',', $this->usersMatch[0]->match_drink);
        $drink = implode("','", $drink);
        $smoke = explode(',', $this->usersMatch[0]->match_smoke);
        $smoke = implode("','", $smoke);
        $height_min = $this->usersMatch[0]->match_min_height;
        $height_max = $this->usersMatch[0]->match_max_height;
        $weight_min = $this->usersMatch[0]->match_min_weight;
        $weight_max = $this->usersMatch[0]->match_max_weight;
        $body_type = explode(',', $this->usersMatch[0]->match_body_type);
        $body_type = implode("','", $body_type);
        $match_ethnicity = explode(',', $this->usersMatch[0]->match_ethnicity);
        $match_ethnicity = implode("','", $match_ethnicity);
        $match_appearance = explode(',', $this->usersMatch[0]->match_appearance);
        $match_appearance = implode("','", $match_appearance);
        $match_body_art = explode(',', $this->usersMatch[0]->match_body_art);
        $match_body_art = implode("','", $match_body_art);
        $match_marital_status = explode(',', $this->usersMatch[0]->match_marital_status);
        $match_marital_status = implode("','", $match_marital_status);
        $match_occupation = explode(',', $this->usersMatch[0]->match_occupation);
        $match_occupation = implode("','", $match_occupation);
        $match_emp_status = explode(',', $this->usersMatch[0]->match_emp_status);
        $match_emp_status = implode("','", $match_emp_status);
        $match_education = explode(',', $this->usersMatch[0]->match_education);
        $match_education = implode("','", $match_education);
        $match_eng_ability = explode(',', $this->usersMatch[0]->match_eng_ability);
        $match_eng_ability = implode("','", $match_eng_ability);
        $match_religion = $this->usersMatch[0]->match_religion;
        $match_religious_values = explode(',', $this->usersMatch[0]->match_religious_values);
        $match_religious_values = implode("','", $match_religious_values);
        $match_star_sign = explode(',', $this->usersMatch[0]->match_star_sign);
        $match_star_sign = implode("','", $match_star_sign);

        $query = '';
        $query .= "SELECT t1.*,t2.* FROM users t1 LEFT JOIN users_metas t2 ON (t1.id=t2.user_id) WHERE t2.age BETWEEN ".$age_min." AND ".$age_max." AND t2.height BETWEEN ".$height_min." AND ".$height_max." AND t2.weight BETWEEN ".$weight_min." AND ".$weight_max . " AND t1.id NOT IN (SELECT b_who_id from block_users where b_whom_id = '".$id."')";
        
        if($sex != 'All') {
            $query .= " AND t2.sex = '".$sex."'";
        }
        if($country != 'All') {
            $query .= " AND t2.country = '".$country."'";
        }
        if($drink != 'All') {
            $query .= " AND t2.drink IN ( '".$drink."' )";
        }
        if($smoke != 'All') {
            $query .= " AND t2.smoke IN ( '".$smoke."' )";
        }
        if($body_type != 'All') {
            $query .= " AND t2.body_type IN ( '".$body_type."' )";
        }
        if($match_ethnicity != 'All') {
            $query .= " AND t2.ethnicity IN ( '".$match_ethnicity."' )";
        }
        if($match_appearance != 'All') {
            $query .= " AND t2.appearance IN ( '".$match_appearance."' )";
        }
        if($match_body_art != 'All') {
            $query .= " AND t2.body_art IN ( '".$match_body_art."' )";
        }
        if($match_marital_status != 'All') {
            $query .= " AND t2.relationship IN ( '".$match_marital_status."' )";
        }
        if($match_occupation != 'All') {
            $query .= " AND t2.occupation IN ( '".$match_occupation."' )";
        }
        if($match_emp_status != 'All') {
            $query .= " AND t2.emplyment_status IN ( '".$match_emp_status."' )";
        }
        if($match_education != 'All') {
            $query .= " AND t2.education IN ( '".$match_education."' )";
        }
        if($match_eng_ability != 'All') {
            $query .= " AND t2.eng_lang_ability IN ( '".$match_eng_ability."' )";
        }
        if($match_religious_values != 'All') {
            $query .= " AND t2.religious_view IN ( '".$match_religious_values."' )";
        }
        if($match_star_sign != 'All') {
            $query .= " AND t2.star_sign IN ( '".addslashes($match_star_sign)."' )";
        }

        
        
        
        $this->users = DB::Select($query);

        foreach($this->users as $d) {
            //get 1 photo

            $data2 = $data = DB::select("select * from user_photos where user_id = '".$d->id."' and user_photo_path != '' limit 1");
            if(count($data2) > 0) {
                $arr[] = array(
                'id' => $d->id,
                'name' => $d->name,
                'age' => $d->age,
                'sex' => $d->sex,
                'prfl_photo_path' => ($d->prfl_photo_path == '' ? 'https://www.globallove.online/images/male-user-placeholder.png' : 'https://www.globallove.online/images/' . $d->prfl_photo_path),
                'user_photo_path' => (count($data2) > 0 ? 'https://www.globallove.online/images/' . $data2[0]->user_photo_path : 'https://dummyimage.com/600x400/777/fff.jpg&text=Globalworld'),
            );
            }
            
        }
       
       return json_encode($arr);
    }

    public function viewMyLikes($id){

        $mylike = DB::table('connectionsfinals')
                ->leftJoin('users', 'users.id', '=', 'connectionsfinals.you_like')
                ->leftJoin('users_metas', 'users_metas.user_id', '=', 'users.id')
                ->where('connectionsfinals.user_id', '=', $id)
                ->whereNotIn('users.id', DB::table('block_users')->select('b_who_id')->where('b_whom_id','=', $id))
                ->orderBy('connectionsfinals.created_at', 'desc')
                ->get();

                foreach($mylike as $d) {
            //get 1 photo

            $data2 = $data = DB::select("select * from user_photos where user_id = '".$d->id."' and user_photo_path != '' limit 1");
            if(count($data2) > 0) {
                $arr[] = array(
                'id' => $d->id,
                'name' => $d->name,
                'age' => $d->age,
                'sex' => $d->sex,
                'prfl_photo_path' => ($d->prfl_photo_path == '' ? 'https://www.globallove.online/images/male-user-placeholder.png' : 'https://www.globallove.online/images/' . $d->prfl_photo_path),
                'user_photo_path' => 'https://www.globallove.online/images/' . $data2[0]->user_photo_path,
            );
            }
            
        }

        return json_encode($arr);
    }

    public function viewLikesMe($id){

        $likeme = DB::table('connectionsfinals')
                ->leftJoin('users', 'users.id', '=', 'connectionsfinals.user_id')
                ->leftJoin('users_metas', 'users_metas.user_id', '=', 'users.id')
                ->where('connectionsfinals.you_like', '=', $id)
                ->whereNotIn('users.id', DB::table('block_users')->select('b_who_id')->where('b_whom_id','=', $id))
                ->orderBy('connectionsfinals.created_at', 'desc')
                ->get();

                foreach($likeme as $d) {
            //get 1 photo

            $data2 = $data = DB::select("select * from user_photos where user_id = '".$d->id."' and user_photo_path != '' limit 1");
            if(count($data2) > 0) {
                $arr[] = array(
                'id' => $d->id,
                'name' => $d->name,
                'age' => $d->age,
                'sex' => $d->sex,
                'prfl_photo_path' => ($d->prfl_photo_path == '' ? 'https://www.globallove.online/images/male-user-placeholder.png' : 'https://www.globallove.online/images/' . $d->prfl_photo_path),
                'user_photo_path' => (count($data2) > 0 ? 'https://www.globallove.online/images/' . $data2[0]->user_photo_path : 'https://dummyimage.com/600x400/777/fff.jpg&text=Globalworld'),
            );
            }
            
        }

        return json_encode($arr);
    }

    public function viewProfileUsersMob($id){

        $viewpro = DB::table('users')
                ->leftJoin('users_metas', 'users_metas.user_id', '=', 'users.id')
                ->leftJoin('view_profile', 'view_profile.pro_user_id', '=', 'users.id')
                ->where('view_profile.view_user_id', '=', $id)
                ->whereNotIn('users.id', DB::table('block_users')->select('b_who_id')->where('b_whom_id','=', $id))
                ->orderBy('view_profile.created_at', 'DESC')
                ->get();

                foreach($viewpro as $d) {
            //get 1 photo

            $data2 = $data = DB::select("select * from user_photos where user_id = '".$d->id."' and user_photo_path != '' limit 1");
            //if(count($data2) > 0) {
                $arr[] = array(
                'id' => $d->id,
                'name' => $d->name,
                'age' => $d->age,
                'sex' => $d->sex,
                'prfl_photo_path' => ($d->prfl_photo_path == '' ? 'https://www.globallove.online/images/male-user-placeholder.png' : 'https://www.globallove.online/images/' . $d->prfl_photo_path),
                'user_photo_path' => (count($data2) > 0 ? 'https://www.globallove.online/images/' . $data2[0]->user_photo_path : 'https://dummyimage.com/600x400/777/fff.jpg&text=Globalworld'),
            );
            //}
            
        }

        return json_encode($arr);
    }

    public function viewProfile($id){

        $profile = DB::table('users')
                ->leftJoin('users_metas', 'users_metas.user_id', '=', 'users.id')
                ->leftJoin('users_prsnl_qstn', 'users_prsnl_qstn.prsnl_user_id', '=', 'users.id')
                ->leftJoin('users_match', 'users_match.users_match_id', '=', 'users.id')
                ->where('users.id', '=', $id)
                ->get();

        return json_encode($profile);
    }

    public function viewUsersPhotos($id){

        $data = DB::table('user_photos')
                ->where('user_id', '=', $id)
                ->get();

                foreach ($data as $value) {
                    $photos[] = "https://www.globallove.online/images/" . $value->user_photo_path;
                }

        return json_encode($photos);
    }

    public function viewUsersDetails($id){
        // /(select * from user_photos where user_id = '".$id."')
        //LEFT JOIN `user_photos` ON `user_photos`.`user_id` = `users`.`id` 
         $data = DB::table('users')
                ->leftJoin('users_metas', 'users_metas.user_id', '=', 'users.id')
                ->where('users.id', '=', $id)
                ->get();

                $arr[] = array(
                'name' => $data[0]->name,
                'sex' => $data[0]->sex,
                'email' => $data[0]->email,
                'prfl_photo_path' => ($data[0]->prfl_approve_status == '0' ? 'https://www.globallove.online/images/male-user-placeholder.png' : $data[0]->prfl_photo_path),
            );
            
       return json_encode($arr);
    }

    
}
