<?php

use \Braintree_ClientToken as Braintree_ClientToken;

class ApiController extends BaseController {


    public function getTest() {
        return Page::generateMenu(1,1,41,1,102);
    }


    ###################################################### SPLASH ########################################################

	public function getSplash(){
		$array = array();
        $array['splash']              = URL::asset('assets/'.Settings::getValue('splash')).'?id='.rand();
        $array['powered_by']          = (int)Settings::getValue('powered_by');
        $array['powered_by_bg']       = Settings::getValue('powered_by_bg');
        $array['powered_by_color']    = Settings::getValue('powered_by_color');

        $array['power_by_has_bg']     = (int)Settings::getValue('power_by_has_bg');
        $array['power_by_text']       = Settings::getValue('power_by_text');
        $array['power_by_icon']       = (int)Settings::getValue('power_by_icon');
        $array['power_by_icon_url']   = URL::asset('assets/'.Settings::getValue('icon')).'?id='.rand();

        $array['font_family']         = Settings::getValue('font-family');


        return Response::json($array);
	}

    ###################################################### INIT ########################################################

	public function getInit() {

		$array = array();

		foreach (Languages::where('published', '=', '1')->get() as $key => $value) {
            $array['languages'][$key]['language_id'] = $value->id;
            $array['languages'][$key]['name']        = $value->name;
            $array['languages'][$key]['icon']        = URL::asset($value->thumb);
            $array['languages'][$key]['iso']         = $value->short_name;
        }

        foreach (Venue::where('published', '=', '1')->get() as $key => $value) {
            $array['venues'][$key]['id']            = $value->id;
            $array['venues'][$key]['name']          = $value->name;
            $array['venues'][$key]['image']         = Settings::appUrl().$value->thumb.'?id='.str_random(4);
        }

        $array['settings']['logo']   		          = URL::asset('assets/'.Settings::getValue('logo')).'?id='.rand();
        $array['settings']['menu_background']         = URL::asset('assets/'.Settings::getValue('menu-background')).'?id='.rand();
		$array['settings']['rate_background']         = URL::asset('assets/'.Settings::getValue('rate-background')).'?id='.rand();
        $array['settings']['spreadit_background']     = URL::asset('assets/'.Settings::getValue('spreadit-background')).'?id='.rand();
        $array['settings']['reservation_background']  = URL::asset('assets/'.Settings::getValue('reservation-background')).'?id='.rand();
        $array['settings']['login_background']        = URL::asset('assets/'.Settings::getValue('login-background')).'?id='.rand();
        $array['settings']['catalog_background']      = URL::asset('assets/'.Settings::getValue('catalog_image')).'?id='.rand();

        $array['settings']['nav_text_color']     = Settings::getValue('nav-text-color');
        $array['settings']['nav_back_color']     = Settings::getValue('nav-back-color');
        $array['settings']['menu_text_color']    = Settings::getValue('menu-text-color');

        $array['settings']['menu_color']	     = Settings::getValue('menu-color');
        $array['settings']['content_color']      = Settings::getValue('content-color');
        $array['settings']['content_text_color'] = Settings::getValue('content-text-color');
        $array['settings']['font_family']        = Settings::getValue('font-family');
        $array['settings']['font_url']           = URL::asset('/assets/app_fonts/'.Settings::getValue('font-family'));

        $array['settings']['header_color']       = Settings::getValue('header_color');
        $array['settings']['header_text_color']  = Settings::getValue('header_text_color');
        $array['settings']['progress_color']     = Settings::getValue('progress_color');
        $array['settings']['weather_key']        = Settings::getValue('weather_key');
        $array['settings']['youtube_key']        = Settings::getValue('youtube_key');
        $array['settings']['ios_back_color']     = Settings::getValue('ios_back_color');

		$array['settings']['bar_icons_color']    = Settings::getValue('bar_icons_color');

        //$append_to_function = ($width != null && $height != null? "&width=".$width."&height=".$height: "");
        //$array['back_image']        = URL::action('JsonController@getFixImageOnWhite')."?red=255&green=255&blue=255&blur=100&url=".URL::asset('assets/splash.png').$append_to_function;
        $array['settings']['template']          = (Settings::hasValue('template')? Settings::getValue('template') : "grid");

        $array['settings']['app_status']        = (int)Settings::getValue('app_status');
		$array['settings']['app_color']         = Settings::getValue('app_color');
		$array['settings']['app_text_color']    = Settings::getValue('app_text_color');
        $array['settings']['alert_color_text']  = Settings::getValue('alert_color_text');
        $array['settings']['alert_color_bg']    = Settings::getValue('alert_color_bg');

		$array['settings']['share_bg_color']        = Settings::getValue('share_bg_color');
		$array['settings']['share_icon_color']      = Settings::getValue('share_icon_color');
		$array['settings']['share_progress_color']  = Settings::getValue('share_progress_color');


        $array['settings']['offline_image']          = URL::asset('assets/'.Settings::getValue('offline_image')).'?id='.rand();
        $array['settings']['privacy_url']            = Settings::appUrl().'terms';
        $array['settings']['catalog_status']         = (int)Settings::getValue('catalog_status');
        $array['settings']['catalog_type']           = Settings::getValue('catalog_type');
        $array['settings']['catalog_payment_type']   = Settings::getValue('catalog_payment_type');
        $array['settings']['order_range']            = Settings::getValue('order_range');
        $array['settings']['min_order_cost']         = Settings::getValue('min_order_cost');
        $array['settings']['delivery_time']          = Settings::getValue('delivery_time');
        $array['settings']['first_order_descount']   = Settings::getValue('first_order_descount');
        $array['settings']['has_register']           = (int)Settings::getValue('has_register');
        $array['settings']['has_reresvation_person'] = (int)Settings::getValue('has_reresvation_person');
        $array['settings']['lang_title']             = 'Επιλογή γλώσσας';
        $array['settings']['lang_descr']             = 'Το περιεχόμενο της εφαρμογής θα εμφανίζεται στη γλώσσα που επιλέγετε';

        $array['settings']['splash_banner']          = Banner::details(0);

        return Response::json($array);
	}

    ###################################################### MENU ########################################################

	public function getMenu($venue_id = null, $lang_id = null) {

        if ($lang_id == null || $venue_id == null) {
            $array['error'] = "Please enter venue_id and language_id";
        } else {

        $masterpages = Page::where('master_id', '=', 0)->where('lang_id', '=', $lang_id)->where('venue_id', '=', $venue_id)->where('published', '=', '1')->orderBy('order')->get();
        
        $details  = array();
        $array    = array();

        foreach ($masterpages as $key => $value) {

        		$array['menu'][$key]['id']              = $value->id;
                $array['menu'][$key]['page_id']         = $value->page_id;
                $array['menu'][$key]['page_type_id']    = $value->page_type_id;
                $array['menu'][$key]['page_type']       = Pagetype::getName($value->page_type_id);
                $array['menu'][$key]['name']            = $value->name;
                $array['menu'][$key]['position']        = strlen($value->position) == 0 ? 'left' : $value->position;
                $array['menu'][$key]['icon']            = URL::asset('/assets/appicons/'.Settings::getValue('icons').'/'.$value->page_icon);

                
                $details  = Page::generateMenu($lang_id, $venue_id, $value->page_type_id, $value->page_id, $value->id);


                //return $array;

                //$details  = Page::generateMenu($lang_id, $venue_id, $value->page_type_id, $value->page_id, $value->id);
                //$array['menu'][$key]['details']   = $details;
                
                $array['menu'][$key]['is_module']  = $details['is_module'];
                $array['menu'][$key]['has_subs']   = $details['has_subs'];
                $array['menu'][$key]['subs']       = $details['subs'];

                /*if (strlen($value->image) > 0) {
                    $myfile = public_path().'/assets/page_images/'.$value->image;
                    
                    if (File::exists($myfile)){ 
                        $array['menu'][$key]['image']   = URL::asset('/assets/page_images/'.$value->image);
                    } else {
                        $array['menu'][$key]['image']   = '';
                    }
                } else {
                    $array['menu'][$key]['image']   = '';
                }
                
                
                if (Pagetype::getName($value->page_type_id) == 'sub') {


                    $array['menu'][$key]['is_module'] = 0;

                    if (sizeof (Page::mysubs($lang_id, $venue_id, $value->page_id)) > 0 ) {
                        $array['menu'][$key]['has_subs']    = 1;
                        $array['menu'][$key]['subs']        = Settings::appUrl().'api/subs/'.$lang_id.'/'.$venue_id.'/'.$value->page_id;
                    } else {
                        $array['menu'][$key]['has_subs']    = 0;
                        $array['menu'][$key]['subs']        = Settings::appUrl().'api/article/'.$lang_id.'/'.$venue_id.'/'.$value->id;
                    }

                } elseif (Pagetype::getName($value->page_type_id) == 'Article') {

                    $array['menu'][$key]['is_module'] = 0;

                    if (sizeof (Page::mysubs($lang_id, $venue_id, $value->page_id)) > 0 ) {
                        $array['menu'][$key]['has_subs']    = 1;
                        $array['menu'][$key]['subs']        = Settings::appUrl().'api/subs/'.$lang_id.'/'.$venue_id.'/'.$value->page_id;
                    } else {
                        $array['menu'][$key]['has_subs']    = 0;
                        $array['menu'][$key]['subs']        = Settings::appUrl().'api/article/'.$lang_id.'/'.$venue_id.'/'.$value->id;
                    }


                } elseif (Pagetype::getName($value->page_type_id) == 'gallery') {

                    $array['menu'][$key]['is_module'] = 0;

                    if (sizeof (Page::mysubs($lang_id, $venue_id, $value->page_id)) > 0 ) {
                        $array['menu'][$key]['has_subs']    = 1;
                        $array['menu'][$key]['subs']        = Settings::appUrl().'api/subs/'.$lang_id.'/'.$venue_id.'/'.$value->page_id;
                    } else {
                        $array['menu'][$key]['has_subs']    = 0;
                        $array['menu'][$key]['subs']        = Settings::appUrl().'api/gallery-list/'.$value->id;
                    }

                } elseif (Pagetype::getName($value->page_type_id) == 'video') {

                    $array['menu'][$key]['is_module'] = 0;

                    if (sizeof (Page::mysubs($lang_id, $venue_id, $value->page_id)) > 0 ) {
                        $array['menu'][$key]['has_subs']    = 1;
                        $array['menu'][$key]['subs']        = Settings::appUrl().'api/subs/'.$lang_id.'/'.$venue_id.'/'.$value->page_id;
                    } else {
                        $array['menu'][$key]['has_subs']    = 0;
                        $array['menu'][$key]['subs']        = Settings::appUrl().'api/video/'.$value->id;
                    }

                ###################################################### MODULES ########################################################

				} elseif (Pagetype::getName($value->page_type_id) == 'homegrid') {

					$array['menu'][$key]['is_module']   = 1;
					$array['menu'][$key]['has_subs']    = 1;
					$array['menu'][$key]['subs']        = Settings::appUrl().'api/home-grid/'.$lang_id.'/'.$venue_id;


                } elseif (Pagetype::getName($value->page_type_id) == 'Hotel') {

                    $array['menu'][$key]['is_module'] = 1;

                    if (sizeof (Roomcategory::mysubs($lang_id, $venue_id, 0)) > 0 ) {
                        $array['menu'][$key]['has_subs']    = 1;
                        $array['menu'][$key]['subs']        = Settings::appUrl().'api/room-subs/'.$lang_id.'/'.$venue_id;
                    } else {
                        $array['menu'][$key]['has_subs']    = 0;
                        $array['menu'][$key]['subs']        = Settings::appUrl().'api/rooms/'.$lang_id.'/'.$venue_id.'/'.$value->id;
                    }

                } elseif (Pagetype::getName($value->page_type_id) == 'Catalog') {

                    $array['menu'][$key]['is_module'] = 1;

                    if (sizeof (Foodcategory::mysubs($lang_id, $venue_id, 0)) > 0 ) {
                        $array['menu'][$key]['has_subs']    = 1;
                        $array['menu'][$key]['subs']        = Settings::appUrl().'api/catalog-subs/'.$lang_id.'/'.$venue_id;
                    } else {
                        $array['menu'][$key]['has_subs']    = 0;
                        $array['menu'][$key]['subs']        = Settings::appUrl().'api/catalogs/'.$lang_id.'/'.$venue_id.'/'.$value->id;
                    }
                } elseif (Pagetype::getName($value->page_type_id) == 'Service') {

                    $array['menu'][$key]['is_module'] = 1;

                    if (sizeof (Servicecategory::mysubs($lang_id, $venue_id, 0)) > 0 ) {
                        $array['menu'][$key]['has_subs']    = 1;
                        $array['menu'][$key]['subs']        = Settings::appUrl().'api/service-subs/'.$lang_id.'/'.$venue_id;
                    } else {
                        $array['menu'][$key]['has_subs']    = 0;
                        $array['menu'][$key]['subs']        = Settings::appUrl().'api/services/'.$lang_id.'/'.$venue_id.'/'.$value->id;
                    }
                } elseif (Pagetype::getName($value->page_type_id) == 'Blog') {

                    $array['menu'][$key]['is_module'] = 1;

                    if (sizeof (Blogcategory::mysubs($lang_id, $venue_id, 0)) > 0 ) {
                        $array['menu'][$key]['has_subs']    = 1;
                        $array['menu'][$key]['subs']        = Settings::appUrl().'api/blog-subs/'.$lang_id.'/'.$venue_id;
                    } else {
                        $array['menu'][$key]['has_subs']    = 0;
                        $array['menu'][$key]['subs']        = Settings::appUrl().'api/blog-articles/'.$lang_id.'/'.$venue_id.'/'.$value->id;
                    }
                } elseif (Pagetype::getName($value->page_type_id) == 'pois') {

                    $array['menu'][$key]['is_module'] = 1;
                    $array['menu'][$key]['has_subs']  = 0;
                    $array['menu'][$key]['subs']      = Settings::appUrl().'api/pois/'.$lang_id.'/'.$venue_id;

                } elseif (Pagetype::getName($value->page_type_id) == 'Rate') {

                    $array['menu'][$key]['is_module'] = 1;
                    $array['menu'][$key]['has_subs']  = 0;
                    $array['menu'][$key]['subs']      = Settings::appUrl().'api/rates/'.$venue_id;

                } elseif (Pagetype::getName($value->page_type_id) == 'Testimonial') {

                    $array['menu'][$key]['is_module'] = 1;
                    $array['menu'][$key]['has_subs']  = 0;
                    $array['menu'][$key]['subs']      = Settings::appUrl().'api/testimonials/'.$venue_id.'/'.$lang_id;

                } elseif (Pagetype::getName($value->page_type_id) == 'weather') {

                    $array['menu'][$key]['is_module'] = 1;
                    $array['menu'][$key]['has_subs']  = 0;
                    $array['menu'][$key]['subs']      = 'This is call by mobile';

                } elseif (Pagetype::getName($value->page_type_id) == 'contact') {

                    $array['menu'][$key]['is_module'] = 1;
                    $array['menu'][$key]['has_subs']  = 0;
                    $array['menu'][$key]['subs']      = 'This is call by mobile';
                }*/

        }
            return Response::json($array);
    	}

    }


    public function postBannerClick(){
        
        $message = array();
        $banner_id = Input::get('banner_id');
        $user_id   = Input::get('user_id');

        if (strlen($banner_id) > 0) {
            Banner::NewClick($banner_id, $user_id);
            $message['status']  = 'success';
            $message['message'] = "";
        } else {
            $message['status']  = 'error';
            $message['reason'] = "";
        }

        return Response::json($message);
    }


    public function getGalleryList($page_id){
        return Gallery::getGalleryList($page_id);
    }


    public function getGallery($gallery_id){
        return Gallery::getImages($gallery_id);
    }


    public function getVideo($page_id){
        return Cmsvideo::getVideo($page_id);
    }

    public function getPdf($page_id){
        return Pdfreader::getPdf($page_id);
    }

    public function getRss(){
        $array = array();

        $array["url"] = Settings::getValue('rss_url');

        return $array;
    }


    public function getWebContent($lang_id, $venue_id, $page_id) {
        //return Cms::data($lang_id, $venue_id, $id);

        $data = array();
        
         $cms = Cms::where('lang_id', '=', $lang_id)->where('venue_id', '=', $venue_id)->where('page_id','=',$page_id)->first();

         if ($cms) {

            $text = str_ireplace(array("\r","\n",'\r','\n'),'', $cms->descr);

            $data['url'] = strip_tags($text);
        }
        return $data;
    }


    public function getTelephone($lang_id, $venue_id, $page_id) {
        //return Cms::data($lang_id, $venue_id, $id);

        $data = array();
        
         $cms = Cms::where('lang_id', '=', $lang_id)->where('venue_id', '=', $venue_id)->where('page_id','=',$page_id)->first();

         if ($cms) {

            $text = str_ireplace(array("\r","\n",'\r','\n'),'', $cms->descr);

            $data['telephone'] = strip_tags($text);
        }
        return $data;
    }


    ############################################## HOME GRID ####################################################

    public function getHomeGrid($lang_id = null, $venue_id = null) {
        $data  = array();
        $array = array();

        $details = array();

        foreach (Homegrid::where('lang_id','=',$lang_id)->where('venue_id','=',$venue_id)->where('isactive','=',1)->get() as $key => $value) {

            $page = Page::where('page_id','=',$value->menu_page_id)->where('lang_id','=',$lang_id)->where('venue_id','=',$venue_id)->first();

            $data[$key]['id']            = $page->id;
            $data[$key]['page_id']       = $value->menu_page_id;
            $data[$key]['page_type_id']  = $page->page_type_id;
            $data[$key]['page_type']     = Pagetype::getName($page->page_type_id);
            $data[$key]['title']         = $value->title;
            $data[$key]['image']         = URL::asset('assets/homegrid_images').'/'.$value->image;
            $data[$key]['bg_color']      = $value->bg_color;
            $data[$key]['text_color']    = $value->text_color;
            
            $details  = Page::generateMenu($lang_id,$venue_id,Page::find($page->id)->page_type_id,$page->page_id,$page->id);

            $data[$key]['ismodule'] = $details['is_module'];
            $data[$key]['has_subs'] = $details['has_subs'];
            $data[$key]['subs']     = $details['subs'];
            
            //$data[$key]['details']  = $details;
        
        }


        $array['hasheader']      = (int)Settings::getValue('home_grid_header');
        $array['home_grid_rows'] = (int)Settings::getValue('home_grid_rows');
        $array['home_grid_col']  = (int)Settings::getValue('home_grid_col');
        $array['grid'] = $data;

        return Response::json($array);
    }


    ############################################## PAGES ####################################################

    public function getSubs($lang_id = null, $venue_id = null, $master_id = null) {
        return Page::mysubs($lang_id, $venue_id, $master_id);
    }

    public function getArticle($lang_id, $venue_id, $id) {
        return Cms::data($lang_id, $venue_id, $id);
    }


    ############################################## HOTEL ####################################################

    public function getRoomSubs($lang_id = null, $venue_id = null, $master_id = null) {
        return Roomcategory::mysubs($lang_id, $venue_id, $master_id);
    }

    public function getRooms($lang_id, $venue_id, $id) {
        return Room::data($lang_id, $venue_id, $id);
    }

    public function getRoomDetails($id) {
        return Room::detail($id);
    }



    ############################################## CATALOG ####################################################


    public function getCatalogSubs($lang_id = null, $venue_id = null, $master_id = null) {
        return Foodcategory::mysubs($lang_id, $venue_id, $master_id);
    }

    public function getCatalogs($lang_id, $venue_id, $id) {
        return Food::data($lang_id, $venue_id, $id);
    }

    public function getCatalogDetails($id) {
        return Food::detail($id);
    }


    ############################################## SERVICE ####################################################


    public function getServiceSubs($lang_id = null, $venue_id = null, $master_id = null) {
        return Servicecategory::mysubs($lang_id, $venue_id, $master_id);
    }

    public function getServices($lang_id, $venue_id, $id) {
        return Service::data($lang_id, $venue_id, $id);
    }

    public function getServiceDetails($id) {
        return Service::detail($id);
    }


    ############################################## BLOG ####################################################


    public function getBlogSubs($lang_id = null, $venue_id = null, $master_id = null) {
        return Blogcategory::mysubs($lang_id, $venue_id, $master_id);
    }

    public function getBlogArticles($lang_id, $venue_id, $id) {
        return Blog::data($lang_id, $venue_id, $id);
    }

    public function getBlogArticleDetails($id) {
        return Blog::detail($id);
    }


    ############################################## POIS ####################################################

    public function getPois($lang_id, $venue_id) {

        $pois = Poi::where('lang_id', '=', $lang_id)->where('venue_id', '=', $venue_id)->get();
        $returnme = array();

        foreach ($pois as $key => $value) {
            $returnme['pois'][$key]['id']           = $value->id;
            $returnme['pois'][$key]['name']         = $value->name;
            $returnme['pois'][$key]['descr']        = $value->descr;
            $returnme['pois'][$key]['geox']         = floatval($value->geox);
            $returnme['pois'][$key]['geoy']         = floatval($value->geoy);
            $returnme['pois'][$key]['icon']         = $value->icon;
            $returnme['pois'][$key]['icon_url']     = URL::asset('assets/poi').'/'.$value->icon;
						$returnme['pois'][$key]['distance_num'] = floatval(Settings::distance( $value->geoy , $value->geox, Venue::find($venue_id)->geoX, Venue::find($venue_id)->geoY, "K"));
						$returnme['pois'][$key]['distance']     = number_format(Settings::distance( $value->geoy , $value->geox, Venue::find($venue_id)->geoX, Venue::find($venue_id)->geoY, "K"),2);

						//$returnme['pois'][$key]['venue_geox'] = Venue::find($venue_id)->geoX;
						//$returnme['pois'][$key]['venue_geoy'] = Venue::find($venue_id)->geoY;

						//$width  = 100;
            //$height = 100;
            //$append_to_function = ($width != null && $height != null? "&width=".$width."&height=".$height: "");
            //$returnme['pois'][$key]['back_image'] = URL::action('JsonController@getFixImageOnWhite')."?red=0&green=0&blue=0&blur=100&url=".URL::asset('assets/poi').'/'.$value->icon.$append_to_function;
        }



				$po = Poi::Sorting($returnme['pois']);

				$myarray = array();

				$myarray['pois'] = $po;

        return Response::json($myarray);
    }

    ############################################## RATES ####################################################

    public function getRates($venue_id) {

        $rates = Rate::where('venue_id', '=', $venue_id)->where('isactive', '=', 1)->orderby('created_at','desc')->get();
        $array = array();

        $array['average'] = floatval(Rate::average($venue_id));
        $array['rates'] = array();

        foreach ($rates as $key => $value) {
            $array['rates'][$key]['id']       = $value->id;
            $array['rates'][$key]['name']     = ''.$value->name;

            if ($value->gender == 1){
              $array['rates'][$key]['image']  = Settings::appUrl().'assets/img/icon_man.png';
            } else if ($value->gender == 2){
              $array['rates'][$key]['image']  = Settings::appUrl().'assets/img/icon_woman.png';
            } else {
              $array['rates'][$key]['image']  = Settings::appUrl().'assets/img/icon_man.png';
            }

            $array['rates'][$key]['descr']     = $value->descr;
            $array['rates'][$key]['score']     = $value->score;
            $array['rates'][$key]['timestamp'] = Settings::totimestamp( $value->created_at );
        }



        return Response::json($array);

    }


    public function postRate() {

        try {

                $email    = Input::get('email');
                $name     = Input::get('name');
                $descr    = Input::get('descr');
                $score    = Input::get('score');
                $venue_id = Input::get('venue_id');
                $gender   = Input::get('gender_id'); //1-male , 2-female
                $lang_id  = Input::get('lang_id');

                $rate = new Rate;
                $rate->email    = $email;
                $rate->name     = $name;
                $rate->descr    = $descr;
                $rate->venue_id = $venue_id;
                $rate->score    = $score;
                $rate->gender   = $gender;
                $rate->isactive = 0;
                $rate->save();

                $m['status']  = 'success';
                $m['message'] = Translation::field('rate_submit_success',$lang_id);
                return Response::json($m);

            } catch(Exception $e) {

                //return $e;

                $m['status'] = 'error';
                $m['reason'] =  Translation::field('rate_submit_error',$lang_id);
                return Response::json($m);
            }
    }



    ############################################## TESTIMONIAL ####################################################

    public function getTestimonials($venue_id, $lang_id) {

        $testimonials = Testimonial::where('isactive', '=', 1)->where('venue_id', '=', $venue_id)->where('lang_id', '=', $lang_id)->get();
        $array = array();

        foreach ($testimonials as $key => $value) {
            $array['testimonials'][$key]['id']       = $value->id;
            $array['testimonials'][$key]['name']     = $value->title;
            $array['testimonials'][$key]['descr']    = html_entity_decode(strip_tags($value->descr));
            $array['testimonials'][$key]['position'] = $value->descr2;
        }

        return Response::json($array);

    }




    ############################################## VENUES ####################################################

    public function getVenue($lang_id, $venue_id) {

        $array   = array();
        $venues  = array();
        $contact = array();
        $social  = array();
        $stores  = array();
        

        foreach (Venue::where('id','=',$venue_id)->get() as $key => $value) {
            $venues['id']    = $value->id;
            $venues['name']  = $value->name;
            $venues['image'] = Settings::appUrl().$value->thumb.'?id='.str_random(4);
            //$venues['geox']  = floatval($value->geoX);
            //$venues['geoy']  = floatval($value->geoY);
        }


        /*
        foreach (Venueinfo::where('venue_id','=',$venue_id)->where('lang_id', '=', $lang_id)->get() as $key => $value) {
            $contact['address'] = $value->address;
            $contact['email']   = strlen($value->email) > 0 ? $value->email : '';
            $contact['phone']   = strlen($value->phone) > 0 ? $value->phone : '';
            $contact['phone2']  = strlen($value->phone2) > 0 ? $value->phone2 : '';
            $contact['website'] = strlen($value->website) > 0 ? $value->website : '';
            $contact['descr']   = $value->area;
        }
        */


        /*
        foreach (Linktype::where('venue_id','=',$venue_id)->get() as $key => $value) {
            $social[$key]['name']       	= $value->name;
            $social[$key]['type']           = strlen($value->social_type_id) > 0 ? $value->social_type_id : '';
            $social[$key]['type_text'] 	    = strlen($value->social_type_id) > 0 ? Socialtype::find($value->social_type_id)->name : '';
            $social[$key]['image']          = Settings::appUrl().$value->thumb.'?id='.str_random(4);
            $social[$key]['url']        	= $value->url;
			$social[$key]['social_app_id']  = $value->social_app_id;
        }
        */



        foreach (Store::where('venue_id','=',$venue_id)->where('lang_id', '=', $lang_id)->where('isactive','=',1)->get() as $key => $value) {

            $social_new  = array();
            
            
            foreach (Linktype::where('store_id','=', $value->store_id)->get() as $s => $v) {
                $social_new[$s]['name']           = $v->name;
                $social_new[$s]['type']           = strlen($v->social_type_id) > 0 ? $v->social_type_id : '';
                $social_new[$s]['type_text']      = strlen($v->social_type_id) > 0 ? Socialtype::find($v->social_type_id)->name : '';
                $social_new[$s]['image']          = Settings::appUrl().$v->thumb.'?id='.str_random(4);
                $social_new[$s]['url']            = $v->url;
                $social_new[$s]['path']           = $v->path;
                $social_new[$s]['social_app_id']  = $v->social_app_id;
            }
            
            


            $stores[$key]['id']        = $value->id;
            $stores[$key]['store_id']  = $value->store_id;
            $stores[$key]['name']      = $value->name;
            $stores[$key]['descr']     = $value->descr;
            $stores[$key]['address']   = $value->address;
            $stores[$key]['phone']     = strlen($value->phone) > 0 ? $value->phone : '';
            $stores[$key]['phone2']    = strlen($value->phone2) > 0 ? $value->phone2 : '';
            $stores[$key]['email']     = strlen($value->email) > 0 ? $value->email : '';
            $stores[$key]['website']   = strlen($value->website) > 0 ? $value->website : '';
            //$stores[$key]['image']     = Settings::appUrl().'assets/stores/'.$value->icon.'?id='.str_random(4);
            $stores[$key]['geox']      = floatval($value->geox);
            $stores[$key]['geoy']      = floatval($value->geoy);
            $stores[$key]['social']    = $social_new;
        }



        

        $array['info']    = $venues;
        //$array['contact'] = $contact;
        $array['banner']  = Banner::details($venue_id, $lang_id);
        //$array['social']  = $social;
        $array['stores']  = $stores;

        return Response::json($array);
    }



    ############################################### TRANSLATIONS ################################################################################
    
    public function getTranslations($lang_id) {

        $array = array();

        foreach (Translation::where('lang_id', '=', $lang_id)->get() as $key => $value) {
            $array['translation'][$value->name]  = $value->value;
        }

        return Response::json($array);
    } 


    public function postTranslationsApp() {

        $array = array();

        $lang_id = Input::get('lang_id');
        $is_app  = Input::get('is_app');

        foreach (Translation::where('lang_id', '=', $lang_id)->where('is_app','=',$is_app)->get() as $key => $value) {
            $array['translation'][$value->name]  = $value->value;
        }

        return Response::json($array);
    } 
    


    public function postContact(){

        try {

            $email      = strip_tags(Input::get('e'));
            $firstname  = strip_tags(Input::get('f'));
            $lastname   = strip_tags(Input::get('l'));
            $subject    = strip_tags(Input::get('s'));
            $body       = strip_tags(Input::get('m'));
            $lang_id    = Input::get('lang_id');

            $data['email']     = strlen($email) > 0 ? $email : '';
            $data['firstname'] = strlen($firstname) > 0 ? $firstname : '';
            $data['lastname']  = strlen($lastname) > 0 ? $lastname : '';
            $data['subject']   = strlen($subject) > 0 ? $subject : '';
            $data['body']      = strlen($body) > 0 ? $body : '';
            $data['lang_id']   = $lang_id;

            Mail::send('emails.contact', $data, function($message) use ($data) {
                $message->from(Settings::sender(), Settings::sender_name());
                $message->to( Settings::Adminmail() )->subject(Settings::getValue('app_name').' | '.Translation::field('contact_mail_subject',$data['lang_id']));
                $message->bcc( 'project@focus-on.gr' )->subject(Settings::getValue('app_name').' | '.Translation::field('contact_mail_subject',$data['lang_id']));
            });


            $contact = new Contact;
            $contact->email      = $email;
            $contact->firstname  = $firstname;
            $contact->lastname   = $lastname;
            $contact->subject    = $subject;
            $contact->body       = $body;
            $contact->ip_address = $_SERVER['REMOTE_ADDR'];
            $contact->has_seen   = 0;
            $contact->save();

           $m['status']  = 'success';
           $m['message'] = Translation::field('error_message_sent',$lang_id);
           return Response::json($m);

        } catch (Exception $e) {

            $m['status'] = 'error';
            $m['reason'] = Translation::field('error_general',$lang_id);
            return Response::json($m);
        }
    }



    #generate a token to send to client
    public function anyClientToken() {
        //check top of page use \Braintree_ClientToken;
        //config app/config/packages/mschinis/braintree/config.php
        try {

            $store_id  = Input::get('store_id');

            $method      = Store::Payment('method',$store_id);
            $merchand_id = Store::Payment('merchand_id',$store_id);
            $public_key  = Store::Payment('public_key',$store_id);
            $private_key = Store::Payment('private_key',$store_id);

            Braintree_Configuration::environment($method);
            Braintree_Configuration::merchantId($merchand_id);
            Braintree_Configuration::publicKey($public_key );
            Braintree_Configuration::privateKey( $private_key );

            $clientToken = Braintree_clientToken::generate();

            $message['status'] = 'success';
            $message['token']  =  $clientToken;
            return Response::json($message); 

        } catch (Exception $e) {

            $message['status'] = 'error';
            $message['reason'] = 'General error';
            return Response::json($message); 

        }

    }


    public function postOrder() {

        //  try { 

            $order_type = Settings::getValue('catalog_type');

            $firstname        =  Input::json('firstname');
            $lastname         =  Input::json('lastname');
            $email            =  Input::json('email');
            $phone            =  Input::json('phone');
            $address          =  Input::json('address');
            $room_number      =  Input::json('room_number');
            $floor            =  Input::json('floor');
            $bell             =  Input::json('bell');
            $venue_id         =  Input::json('venue_id');
            $comment          =  Input::json('comment');
            $total_cost       =  Input::json('total_cost');
            $payment_type     =  Input::json('payment_type');
            $lang_id          =  Input::json('lang_id');
            $user_lat         =  Input::json('user_lat');
            $user_lng         =  Input::json('user_lng');
            $user_id          =  Input::json('user_id');
            $store_id         =  Input::json('store_id');
            $user_address_id  =  Input::json('user_address_id');
            $coupon           =  Input::json('coupon');
            $amount           =  Input::json('amount');
            $discount         =  Input::json('discount');
            


            // 1:email, 2:braintree , 3:sto katastima
            if ($payment_type == 2) {
            //if (Settings::getValue('catalog_payment_type') == 'Braintree') {
                
                $client_token  =  Input::json('client_token');
                $paymentNonce  =  Input::json('payment_method_nonce');

                $method = Store::Payment('method',$store_id);
                $merchand_id = Store::Payment('merchand_id',$store_id);
                $public_key = Store::Payment('public_key',$store_id);
                $private_key = Store::Payment('private_key',$store_id);

                Braintree_Configuration::environment($method);
                Braintree_Configuration::merchantId($merchand_id);
                Braintree_Configuration::publicKey($public_key );
                Braintree_Configuration::privateKey( $private_key );


                $result = Braintree_Transaction::sale(array(
                'amount' => $total_cost,
                //'amount' => '9.00',
                'paymentMethodNonce' => $paymentNonce,
                'customer' => array(
                                    'firstName' => $firstname,
                                    'lastName'  => $lastname,
                                    'phone'     => $phone,
                                    'email'     => $email
                                    ),
                    'options' => [
                        'submitForSettlement'   => true,
                        'storeInVaultOnSuccess' => true,
                    ]
                ));

                //return $result;

                if (!$result->success) {
                    $message['status']  = 'error';
                    $message['message'] = $result->message;
                    return Response::json($message); 
                } 
            
            }

            //get id of nearest store
            $get_store_id  = Store::find_nearest($user_lng,$user_lat) >= 0 ? Store::find_nearest($user_lng, $user_lat) : 0; 
            //return $get_store_id;


            $record = new Foodorder;
            
            $record->name            = $firstname.' '.$lastname;
            $record->email           = $email;
            $record->phone           = $phone;
            //$record->order_num     = Foodorder::generateNum();
            $record->order_num       = '#'.Foodorder::max('id') + 1;
            $record->room_number     = $room_number;
            $record->total_cost      = $total_cost;
            $record->venue_id        = $venue_id;
            $record->comment         = $comment;
            $record->address         = $address;
            $record->store_id        = $store_id;
            $record->payment_type    = $payment_type;
            $record->coupon          = $coupon;
            $record->lang_id         = $lang_id;
            $record->user_id         = $user_id;
            $record->user_address_id = $user_address_id;

            if ($user_address_id > 0) { //otan einai login pernei ti dieuthinsei apo tis dieuthinseis
                $record->floor           = Useraddress::details('floor',$user_address_id);
                $record->bell            = Useraddress::details('bell',$user_address_id);
            } else { //allios oti m steilei
                $record->floor           = $floor;
                $record->bell            = $bell;
            }
            
            $record->responded       = 0;

            if ($payment_type == 2) {
              $record->status     = 1;  //accepted
              $record->responded  = 1;  //accepted
            } else {
              $record->status     = 0;  //onprogress
            }


            
            $record->amount         = $amount;
            $record->discount       = $discount;
            
            
            $record->save();

            

            $order_id = $record->id;

            $details  =  Input::json('details');

            $a = array();

            foreach ($details as $key => $value) {
             
               DB::table('food_orders_detail')->insert(
                    array(
                          'food_order_id'      => $order_id, 
                          'food_id'            => $value['id'], 
                          //'food_feature_id'    => $value['feature_id'], 
                          //'food_attribute_id'  => $value['attribute_id'], 
                          'title'              => $value['title'], 
                          'comment'            => $value['comment'],
                          'price'              => $value['price'],
                          'qty'                => $value['qty']
                        )
              );

               $line = DB::table('food_orders_detail')->max('id');

               $k = 1;
               foreach ($value['features'] as $key => $val) {
                    DB::table('food_order_feature')->insert(
                        array('food_order_id' => $order_id,'food_id' => $value['id'], 'food_feature_id' => $val['id'], 'food_attribute_id' => serialize($val['extras']), 'line' => $k )
                    );
                    $k = $k + 1;
               }

            } //end foreach

            //find store email
            $data['email']  = Store::find_email($get_store_id);

            $data['header']  = Foodorder::find($record->id);
            $data['details'] = Foodorderdetail::where('food_order_id','=',$record->id)->get();

            $data['order_id'] = $order_id;

            Mail::send('emails.new.order', $data, function($message) use ($data) {
              $message->from( $data['email'] , Settings::getValue('app_name'));
              $message->to( $data['email'] )->subject(Settings::getValue('app_name').' - ΝΕΑ ΠΑΡΑΓΓΕΛΙΑ ΚΩΔ. #'.$data['order_id']);
              $message->bcc( 'project@focus-on.gr' )->subject(Settings::getValue('app_name').' - ΝΕΑ ΠΑΡΑΓΓΕΛΙΑ ΚΩΔ. #'.$data['order_id']);
            }); 

            if ($payment_type == 2) {
                //Usermessage::message_and_push($order_id,1,'');
                //send email to user - only from braintree
                Foodorder::email_for_braintree($order_id, $lang_id);
            }
            

            $message['status']  = 'success';
            //$message['message'] = 'Order submited';
            $message['message'] = Translation::field('error_order_completed',$lang_id);
            $message['header']  = $data['header'];

        return Response::json($message); 

        /*} catch (Exception $e) {

            $message['status'] = 'error';
            return Response::json($message); 

        }*/

    }



    public function getFeatures($food_id,$lang_id) {

        $features   = array();
        $attributes = array();
        $array      = array();
        

        $feat       = DB::table('food_product_attributes')->select('food_feature_id')->where('food_id','=',$food_id)->distinct()->get();

        foreach ( $feat as $key => $value ) {
            
            $array['features'][$key]['id']          = $value->food_feature_id;
            $array['features'][$key]['title']       = Foodfeature::translation('title', $value->food_feature_id, $lang_id);
            $array['features'][$key]['is_required'] = Foodfeature::translation('required', $value->food_feature_id, $lang_id);
            
                /*$required = Foodproductattribute::where('food_id','=',$food_id)->where('required','=',1)->where('food_feature_id','=',$value->food_feature_id)->get();

                foreach ( $required as $k => $val ) {
                    $attributes[$k]['attribute_id'] = $val->food_attribute_id;
                    $attributes[$k]['name']         = Foodattribute::translation('title', $val->food_attribute_id, $lang_id);;
                    $attributes[$k]['cost']         = $val->extra_cost;
                }*/

                $ext    = Foodproductattribute::where('food_id','=',$food_id)->where('food_feature_id','=',$value->food_feature_id)->get();
                $extras = array();
                foreach ( $ext as $k => $val ) {
                    $extras[$k]['feature_id']    = $value->food_feature_id;
                    $extras[$k]['attribute_id']  = $val->food_attribute_id;
                    $extras[$k]['name']          = Foodattribute::translation('title', $val->food_attribute_id, $lang_id);;
                    $extras[$k]['cost']          = $val->extra_cost;
                }
            /*$array['features'][$key]['required'] = $attributes;*/
            $array['features'][$key]['extras']   = $extras;
        }

      return Response::json($array);
    }




    //test//
    public function getOrdermail($id) {
        
            $data['email']  = Settings::getValue('catalog_email');

            $data['header']  = Foodorder::find($id);
            $data['details'] = Foodorderdetail::where('food_order_id','=',$id)->get();

            Mail::send('emails.new.order', $data, function($message) use ($data) {
              $message->from( $data['email'] , Settings::getValue('app_name'));
              $message->to( $data['email'] )->subject('New Order from '.Settings::getValue('app_name'));
              $message->bcc( $data['email'] )->subject('New Order from '.Settings::getValue('app_name'));
            }); 

    }


    public function getEmail() {

        $data['email'] = 'project@focus-on.gr';

        Mail::send('emails.new.new_order_user', $data, function($message) use ($data) {
          $message->from('project@focus-on.gr', Settings::getValue('app_name'));
          $message->to( $data['email'] )->subject('New Order from '.Settings::getValue('app_name'));
        });      
    }



    public function getCartDetails() {

    }


    public function postCartPrices() {
        
        $products =  Input::json('products');  
        $lang_id  =  Input::json('lang_id'); 

        $array  = array();
        $result = array();
        
        $i = 0;
        foreach ($products as $key => $value) {
            //$array[$key]['myid'] = $value['id'];
            $food_id = $value['id'];

            $food_record = Food::where('food_id','=',$food_id)->where('lang_id','=',$lang_id)->first();

            if ($food_record) {
                $array[$i]['id']       = $value['id'];
                $array[$i]['title']    = $food_record->title;
                $array[$i]['price']    = floatval($food_record->price);
                $array[$i]['features'] = Foodfeature::product_feature($food_id, $lang_id);
                $i++;
            }

        }

        $result['products'] = $array;
        return Response::json($result);
    }



    public function postReservation(){

        try {

            $email      = strip_tags(Input::get('e'));
            $firstname  = strip_tags(Input::get('f'));
            $lastname   = strip_tags(Input::get('l'));
            $subject    = strip_tags(Input::get('s'));
            $body       = strip_tags(Input::get('m'));
            $phone      = strip_tags(Input::get('p'));
            $date       = strip_tags(Input::get('tm'));
            $people     = strip_tags(Input::get('people'));
            $lang_id    = Input::get('lang_id');
            $user_id    = Input::get('user_id');

            $data['email']     = strlen($email) > 0 ? $email : '';
            $data['firstname'] = strlen($firstname) > 0 ? $firstname : '';
            $data['lastname']  = strlen($lastname) > 0 ? $lastname : '';
            $data['subject']   = strlen($subject) > 0 ? $subject : '';
            $data['body']      = strlen($body) > 0 ? $body : '';
            $data['user_id']   = strlen($user_id) > 0 ? $user_id : '';
            $data['phone']     = strlen($phone) > 0 ? $phone : '';
            $data['people']    = strlen($people) > 0 ? $people : '';
            $data['date']      = strlen($date) > 0 ? date("d M Y H:i:s", $date/1000 ) : '';


            //return $data['date'];

            


            
            $record = new Reservation;
            $record->email            = $email;
            $record->firstname        = $firstname;
            $record->lastname         = $lastname;
            $record->subject          = $subject;
            $record->phone            = $phone;
            $record->user_id          = $user_id;
            $record->reservation_date = $data['date'];
            $record->body             = $body;
            $record->people           = $people;
            $record->lang_id          = $lang_id;
            $record->ip_address       = $_SERVER['REMOTE_ADDR'];
            $record->has_seen         = 0;
            $record->responded        = 0;
            $record->save();

            Mail::send('emails.reservation', $data, function($message) use ($data)
            {
                $message->from(Settings::sender(), Settings::sender_name());
                $message->to( Settings::Adminmail() )->subject('Reservation Form');
                $message->bcc('projects@focus-on.gr')->subject('Reservation Form bcc');
            });
            

           $m['status']  = 'success';
           $m['message'] = Translation::field('reservation_success_message',$lang_id);
           return Response::json($m);

        } catch (Exception $e) {

            $m['status'] = 'error';
            $m['reason'] = Translation::field('error_general',$lang_id);
            return Response::json($m);
        }
    }



    public function postLogin(){

        $message       = array();
        $group_files   = array();
        $user_files    = array();
        $user_messages = array();
        $result        = array();
        $user_data     = array();

        $credentials = array(
            'username' => Input::get('username'),
            'password' => Input::get('password')
        );

        $lang_id = Input::get('lang_id');
    

        if (Auth::attempt($credentials)) {

            $group_id  = Auth::user()->role_id;
            $email     = Auth::user()->email;
            $user_id   = Auth::user()->id;
            $username  = Auth::user()->username;

            $device_id = strlen(Input::get('device_id')) > 0 ? Input::get('device_id')  : '' ;

            $devices = Userdevice::where('device_id','=', $device_id )->count();
            if ($devices == 0) {
               
                    $div = new Userdevice;
                    $div->user_id       = $user_id;
                    $div->device_id     = $device_id;
                    $div->save();

            } else {

                $usr = Userdevice::where('device_id','=', $device_id )->first();
                
                if (sizeof($usr) > 0 ) {
                    if ($usr['user_id'] <> $user_id) {
                        Userdevice::where('device_id','=',$device_id)->update(array('user_id' => $user_id));
                    }
                }
            }


            //$phone = Auth::user()->mobile;
            //$cnt = Foodorder::where('phone','=',$phone)->count();

            if (Settings::getValue('catalog_type') != 'business' ) {
                 
                //email send
                if (Auth::user()->first_order_push == 0) {       
                 
                 Foodorder::FirstOrderEmail($email,$lang_id,$username);
                 
                 $title = Translation::field('coupon_first_push_start',$lang_id);
                 $descr = Translation::field('coupon_first_push_mid',$lang_id)." ". Settings::getValue('first_order_discount_ammount'). "% " .Translation::field('coupon_first_push_closure',$lang_id);

                     if ( Settings::getValue('first_order_discount') == 1 || Settings::getValue('first_order_discount') == '1' ) {
                        Usermessage::PushToUser($user_id,$title,$descr);
                        Usermessage::firstOrder($user_id,$title,$descr);
                     }
                    
                    User::where('id','=',$user_id)->update(array('first_order_push' => 1));

                }
            }


            $groupfiles = Usergroup::find($group_id);

            $i = 0;
            foreach ($groupfiles->files()->get() as $file) {
                if ($file->published == 1) {
                    $group_files[$i]['title']    = $file->name;
                    $group_files[$i]['image']    = '';
                    $group_files[$i]['filename'] = $file->filename;
                    $i++;
                }
            }

            
            $myfiles = User::find($user_id);

            $i = 0;
            foreach ($myfiles->files()->get() as $file) {
                if ($file->published == 1) {
                    $user_files[$i]['title']    = $file->name;
                    $user_files[$i]['image']    = '';
                    $user_files[$i]['filename'] = $file->filename;
                    $i++;
                }
            }

            
            foreach (Usermessage::where('user_id','=',$user_id)->orderby('created_at','desc')->get() as $key => $value) {
                $user_messages[$key]['id']         = $value->id;
                $user_messages[$key]['title']      = $value->title;
                $user_messages[$key]['descr']      = $value->descr;
                //$user_messages[$key]['date']       = Settings::todate($value->created_at);
                //$user_messages[$key]['time']       = Settings::totime($value->created_at);
                $user_messages[$key]['timestamp']  = Settings::totimestamp($value->created_at);
            }
            
            
            $user = User::find($user_id);

            $user_data['id'] = $user_id;
            $user_data['username']     = $user->username;
            $user_data['firstname']    = $user->fname;
            $user_data['lastname']     = $user->lname;
            $user_data['email']        = $user->email;
            $user_data['phone']        = $user->phone;
            $user_data['mobile']       = $user->mobile;
            $user_data['room_number']  = strlen($user->room_number) > 0 ? $user->room_number : "0";
            $user_data['check_in']     = strlen($user->check_in)    > 0 ? strtotime($user->check_in) : 0;
            $user_data['check_out']    = strlen($user->check_out)   > 0 ? strtotime($user->check_out) : 0;

            //check if is hotel
            if (Settings::getValue('catalog_type') == 'hotel' ){
                //check ean exei liksei to checkout
                if ( strtotime("now") > strtotime($user->check_out) ) {
                    $message['status']     = 'error';
                    $message['isExpired']  = 1;
                    $message['Message']    = Translation::field('error_expired',$lang_id);
                    return Response::json($message);
                }
            }

            if( File::exists( 'assets/users/'.$user->id.'.jpg')) {

                $user_data['gender_image'] = URL::asset('assets/users/'.$user->id.'.jpg?id='.str_random(5));

            } else {

                if ($user->gender == 1){
                  $user_data['gender_image']  = Settings::appUrl().'assets/img/icon_man.png';
                } else if ($value->gender == 2){
                  $user_data['gender_image']  = Settings::appUrl().'assets/img/icon_woman.png';
                } else {
                  $user_data['gender_image']  = Settings::appUrl().'assets/img/icon_man.png';
                }

            }



            $user_data['gender']    = $user->gender;


            $result['user_data']   = $user_data;
            $result['group_files'] = $group_files;
            $result['user_files']  = $user_files;
            $result['messages']    = $user_messages;

            return Response::json($result);
            //return Redirect::to('pages/view');
        } else {
        
            $message['status']     = 'error';
            $message['isExpired']  = 0;
            $message['reason']     = Translation::field('error_login_invalid_credentials',$lang_id);
            return Response::json($message);
             
        }

    }


    public function postLogout() {

        try {
            
            $user_id   = Input::get('user_id');
            $device_id = Input::get('device_id');
            $lang_id   = Input::get('lang_id');

            Userdevice::where('user_id','=',$user_id)->where('device_id','=',$device_id)->delete();

            $message['status']   = 'success';
            $message['Message']  = Translation::field('error_logout_success',$lang_id);

            } catch (Exception $e) {

                $message['status'] = 'error';
                $message['reason'] = Translation::field('error_cannot_find_user_or_device',$lang_id);
            }
            
            return Response::json($message);
    }


    public function postUserMessages() {

        $user_id       = Input::get('user_id');
        $user_messages = array();
        $messages      = array();

        foreach (Usermessage::where('user_id','=',$user_id)->orderby('created_at','desc')->get() as $key => $value) {
            $user_messages[$key]['id']         = $value->id;
            $user_messages[$key]['title']      = $value->title;
            $user_messages[$key]['descr']      = $value->descr;
            $user_messages[$key]['timestamp']  = Settings::totimestamp($value->created_at);
        }

        $messages["messages"] = $user_messages;

        return Response::json($messages);
    }


    public function postDeleteMessage(){

        $message  = array();

        $user_id    = Input::get('user_id');
        $message_id = Input::get('message_id');
        $lang_id    = Input::get('lang_id');

        $cnt  = Usermessage::where('user_id','=',$user_id)->where('id','=',$message_id)->count();
        //return $cnt;

        if ($cnt > 0) {

            $del = Usermessage::where('user_id','=',$user_id)->where('id','=',$message_id)->delete();

            $message['status']  = 'success';
            $message['message'] = Translation::field('error_message_deleted',$lang_id);
        } else {
            $message['status'] = 'error';
            $message['reason'] = Translation::field('error_general',$lang_id);
        }

        return Response::json($message);

    }


    //register user for business app or coffee app
    public function postRegister() {

         try {

              $message    = array();
              
              $first_name = Input::json('first_name');
              $last_name  = Input::json('last_name');
              $pass       = Input::json('pass');
              $email      = Input::json('email');
              $phone      = Input::json('phone');
              $mobile     = Input::json('mobile');
              $username   = Input::json('username');
              $lang_id    = Input::json('lang_id');

              //return $username;

              $check_username = User::where('username','=',$username)->count();
              $check_email    = User::where('email','=',$email)->count();
              

              if ($check_username > 0) {

                    $message['status'] = 'error';
                    $message['reason'] = Translation::field('error_username_exists',$lang_id); 
                    return Response::json($message);
              }

              if ($check_email > 0) {

                    $message['status'] = 'error';
                    $message['reason'] = Translation::field('error_email_exists',$lang_id); 
                    return Response::json($message);
              }

              /*
              $check_mobile   = User::where('mobile','=',$mobile)->count();
              if ($check_mobile > 0) {

                    $message['status'] = 'error';
                    $message['reason'] = Translation::field('error_mobile_exists',$lang_id); 
                    return Response::json($message);
              }
              */


              if ( strlen($first_name) > 0 && strlen($last_name) > 0 && strlen($pass) > 0 && strlen($email) > 0) {
                 
                 $user = new User;
                 //$user->username = 'r'.strtotime("now");
                 $user->username = $username;
                 $user->email    = $email;
                 $user->fname    = $first_name;
                 $user->lname    = $last_name;
                 $user->phone    = $phone;
                 $user->mobile   = $mobile;
                 $user->role_id  = 3;
                 $user->gender   = 1;
                 $user->first_order_push = 0;
                 $user->password = Hash::make((string)$pass);
                 $user->save();


                 $message['status'] = 'success';
                 //$message['message'] = Translation::field('error_user_updated',$lang_id); 
                 $message['message'] = ""; 


                 

              } else {

                 $message['status'] = 'error';
                 $message['reason'] = Translation::field('error_general',$lang_id); 
              
              }

              return Response::json($message);
             
            } catch (Exception $e) {

                    $message['status'] = 'error';
                    $message['reason'] = Translation::field('error_general',$lang_id);
                    return Response::json($message);
            }
    }


    public function postForgot(){

        /******* TYPE **********
        1.Pass
        2.Username
        */

        $message    = array();

        $email   = Input::get('email');
        $type    = Input::get('type');
        $lang_id = Input::get('lang_id');


        if (strlen($email) == 0) {
           $message['status'] = 'error';
           $message['reason'] = Translation::field('error_email_not_valid',$lang_id);
           return Response::json($message);  
        }


        $check_email = User::where('email','=',$email)->count();

        if ($check_email == 0) {
            $message['status'] = 'error';
            $message['reason'] = Translation::field('error_email_not_exist',$lang_id);
            return Response::json($message);
        } else {


            $info = '';
            $user = User::where('email','=',$email)->first();

            if ($type == 1) {

                if ($user) {

                    $newPass = str_random(6);

                    $record = User::find($user->id);
                    $record->password = Hash::make($newPass);
                    $record->save();

                    $data['info']   = $newPass;
                    $data['email']  = strlen($email) > 0 ? $email : '';
                    $data['sender'] = Settings::getValue('admin_email');
                    $data['lang_id'] = $lang_id;

                    Mail::send('emails.new.forgot_pass', $data, function($message) use ($data) {
                      $message->from( $data['sender'] , Settings::getValue('app_name'));
                      $message->to( $data['email'] )->subject(Settings::getValue('app_name').' | '.Translation::field('forgot_pass_subject',$data['lang_id']));
                      $message->bcc( 'project@focus-on.gr' )->subject(Settings::getValue('app_name').' | '.Translation::field('forgot_pass_subject',$data['lang_id']));
                    }); 


                    $message['status']  = 'success';
                    $message['message'] = Translation::field('error_email_sent',$lang_id);

                    return Response::json($message);

                }
           
            } elseif ($type == 2) {

                if ($user) {
                    
                    $info = $user->username;

                    $data['info']   = $info;
                    $data['email']  = strlen($email) > 0 ? $email : '';
                    $data['sender'] = Settings::getValue('admin_email');
                    $data['lang_id'] = $lang_id;

                    Mail::send('emails.new.forgot_username', $data, function($message) use ($data) {
                      $message->from( $data['sender'] , Settings::getValue('app_name'));
                      $message->to( $data['email'] )->subject(Settings::getValue('app_name').' | '.Translation::field('forgot_username_subject',$data['lang_id']));
                      $message->bcc( 'project@focus-on.gr' )->subject(Settings::getValue('app_name').' | '.Translation::field('forgot_username_subject',$data['lang_id']));
                    }); 

                    $message['status']  = 'success';
                    $message['message'] = Translation::field('error_email_sent',$lang_id);

                    return Response::json($message);
                }

            }

        }


    }


    public function postCreateAddress() {

        $message  = array();
        $lang_id  = Input::json('lang_id');

        if ( strlen(Input::json('name')) == 0 || strlen(Input::json('street')) == 0 || strlen(Input::json('lat')) == 0 || strlen(Input::json('lot')) == 0 ) {
            $message['status'] = 'error';
            $message['reason'] = 'Fields error';
            
        } else {

            $record = new Useraddress;
            $record->name     = Input::json('name');
            $record->user_id  = Input::json('user_id');
            $record->street   = Input::json('street');
            $record->lat      = Input::json('lat');
            $record->lot      = Input::json('lot');
            $record->comment  = Input::json('comment');
            $record->floor    = Input::json('floor');
            $record->bell     = Input::json('bell');
            $record->save();

            $message['status']  = 'success';
            $message['message'] = Translation::field('error_addres_created',$lang_id);

        }

        return Response::json($message);

    }


    public function postAddressList() {

        $message  = array();
        $array    = array();

        $user_id = Input::get('user_id');
        $lang_id = Input::get('lang_id');

        $records  = Useraddress::where('user_id','=',$user_id)->get();

        if ( sizeof($records) > 0 ) {

            foreach ($records as $key => $value) {
                $array['address'][$key]['address_id']= $value->id;
                $array['address'][$key]['name']      = $value->name;
                $array['address'][$key]['street']    = $value->street;
                $array['address'][$key]['lat']       = $value->lat;
                $array['address'][$key]['lot']       = $value->lot;
                $array['address'][$key]['floor']     = $value->floor;
                $array['address'][$key]['bell']      = $value->bell;
                $array['address'][$key]['comment']   = (string)$value->comment;
            }

            return Response::json($array);
            
        } else {

            $message['status']  = 'error';
            $message['reason']  = Translation::field('error_not_found',$lang_id);
            return Response::json($message);
        }
    }



    public function postDeleteAddress(){

        $message  = array();

        $user_id    = Input::get('user_id');
        $address_id = Input::get('address_id');
        $lang_id    = Input::get('lang_id');

        $cnt  = Useraddress::where('user_id','=',$user_id)->where('id','=',$address_id)->count();
        //return $cnt;

        if ($cnt > 0) {

            $del = Useraddress::where('user_id','=',$user_id)->where('id','=',$address_id)->delete();

            $message['status']  = 'success';
            $message['message'] = Translation::field('error_address_deleted',$lang_id);
        } else {
            $message['status'] = 'error';
            $message['reason'] = Translation::field('error_general',$lang_id);
        }

        return Response::json($message);
    }


    public function postEditProfile() {

        /*
        if (empty(Input::json('new_pass'))) {
            return "keno";
        } else {
            return "oxi keno";
        }
        */

        $message  = array();

        $credentials = array(
            'username' => Input::json('username'),
            'password' => Input::json('pass')
        );

        $lang_id = Input::json('lang_id');


        if ( strlen(Input::json('first_name')) == 0 || strlen(Input::json('last_name')) == 0 || strlen(Input::json('pass')) == 0 ) {
            $message['status'] = 'error';
            $message['reason'] = 'Fields error';
            return Response::json($message);
        }


        if (Auth::attempt($credentials)) {
            
            $user = User::find( Auth::user()->id );
            $user->fname      = Input::json('first_name');
            $user->lname      = Input::json('last_name');
            $user->mobile     = Input::json('mobile');
            $user->phone      = Input::json('phone');

            $new_pass = Input::json('new_pass');
           
            //if (strlen( Input::json('new_pass') > 0 )) {
            if (!empty($new_pass)) {
                $user->password   = Hash::make( (string)Input::json('new_pass') );
            }            
            
            $user->save();

            $message['status']  = 'success';
            $message['message'] = Translation::field('error_user_updated',$lang_id);


        } else {
            $message['status'] = 'error';
            $message['reason'] = Translation::field('error_general',$lang_id);
        }

        return Response::json($message);
    }


    public function postUploadImageProfile() {

        //return "xaxa";

        $message  = array();

        $credentials = array(
            'username' => Input::get('username'),
            'password' => Input::get('pass')
        );

        $lang_id = Input::get('lang_id');

        if (Auth::attempt($credentials)) {

            $user_id = Auth::user()->id;

            $file  = Input::get('file');
            $image = base64_decode($file);

                if (base64_decode($file)) {
                   
                   $image_name = $user_id.'.jpg';
                   $path = public_path() . "/assets/users/".$image_name; 
                   
                   file_put_contents($path, $image); 

                    //full image
                    Image::make($path)->resize(300,300,function($c) {
                            $c->aspectRatio();
                            $c->upsize();
                    })->orientate()->save('assets/users/'.$image_name);

                    $message['status']  = 'success';
                    $message['message'] = 'Image OK';
                    return Response::json($message);
                   
               } else {
                 $message['status'] = 'error';
                 $message['reason'] = 'Image error';
                 return Response::json($message);
               }

        } else {
            $message['status'] = 'error';
            $message['reason'] = Translation::field('error_general',$lang_id);
        }

    }


    public function postOrderHistory() {

        $lang_id = strlen(Input::get('lang_id') > 0) ?  Input::get('lang_id') : 1;

        $message  = array();
        $history  = array();
        $lines    = array();

        $credentials = array(
            'email'    => Input::get('email'),
            'password' => Input::get('pass')
        );

        $page  = Input::get('page', 1);

        if (Auth::attempt($credentials)) {

            $orders = Foodorder::where('email','=',Input::get('email'))->orderby('created_at','desc')->get();


            if (sizeof($orders) > 0) {
                foreach ($orders as $key => $value) {
                    $history['history'][$key]['date']           = Settings::totimestamp($value->created_at);
                    $history['history'][$key]['reference']      = '#'.$value->id;
                    $history['history'][$key]['order_num']      = $value->order_num;
                    $history['history'][$key]['cost']           = $value->total_cost;
                    $history['history'][$key]['address']        = (string)$value->address;
                    $history['history'][$key]['comment']        = $value->comment;
                    $history['history'][$key]['status']         = Foodorder::statusApi($value->id,$lang_id);
                    $history['history'][$key]['status_id']      = $value->status;

                    if ((string)$value->payment_type == 1) {
                        $history['history'][$key]['payment_type']   = "email"; 
                    } elseif ((string)$value->payment_type == 2) {
                        $history['history'][$key]['payment_type']   = "braintree"; 
                    } elseif ((string)$value->payment_type == 3) {
                        $history['history'][$key]['payment_type']   = "store"; 
                    } else {
                        $history['history'][$key]['payment_type']   = ""; 
                    }
                    
                    //$history['history'][$key]['details']   = Foodorderdetail::where('food_order_id','=',$value->id)->get();

                    $details  = Foodorderdetail::where('food_order_id','=',$value->id)->get();

                    foreach ($details as $k => $value) {
                        $lines[$k]['id']           = $value->food_order_id;
                        $lines[$k]['title']        = Food::translation('title', $value->food_id, $lang_id);//$value->title;
                        $lines[$k]['food_id']      = $value->food_id;//Food::translation('title', $value->food_id, $lang_id);
                        //$lines[$k]['title__']      = Food::translation('title', $value->food_id, $lang_id);
                        $lines[$k]['features']     = ltrim(strip_tags(Foodorderfeature::orderlines($value->food_order_id,$value->food_id, $lang_id)));
                        $lines[$k]['comment']      = $value->comment;
                        $lines[$k]['qty']          = $value->qty;
                        $lines[$k]['price']        = $value->price;
                    }

                    $history['history'][$key]['details'] = $lines;
                }
                //return Response::json($history); 

                $perPage    = 20;
                $totalItems = count($history['history']);
                $totalPages = ceil($totalItems / $perPage);

                if ($page > $totalPages or $page < 1) {
                    $page = 1;
                }

                $offset  = ($page * $perPage) - $perPage;

                $records = array_slice($history['history'], $offset, $perPage);
            
                $result  = Paginator::make($records, $totalItems, $perPage);

                return Response::json($result);


            } else {

                $history['history'] = array();

                $perPage    = 20;
                $totalItems = 0;
                $totalPages = ceil($totalItems / $perPage);

                if ($page > $totalPages or $page < 1) {
                    $page = 1;
                }

                $offset  = ($page * $perPage) - $perPage;

                $records = array_slice($history['history'], $offset, $perPage);
            
                $result  = Paginator::make($records, $totalItems, $perPage);

               //return "xaxa";
               return Response::json($result); 
            }

        } else {
            $message['status'] = 'error';
            $message['reason'] = Translation::field('error_general',$lang_id);
        }

        return Response::json($message);

    }



    public function postValidateDistance() {

        $message  = array();

        $user_lat   = Input::json('user_lat');
        $user_lot   = Input::json('user_lot');
        $store_lat  = Input::json('store_lat');
        $store_lot  = Input::json('store_lot');
        $lang_id    = Input::json('lang_id');
        $store_id   = Input::json('store_id');

        $distance   = floatval(Settings::distance( $user_lat , $user_lot, $store_lat, $store_lot , "K") );

        $store = Store::where('store_id','=',$store_id)->first();
        $store_distance = $store->order_range;

        if ($distance <= $store_distance ) {
           $message['status']  = 'success';
           $message['distance'] = $distance;
           $message['store_distance'] = $store_distance;
           $message['message'] =  Translation::field('error_valid_distance',$lang_id);
        } else {
            $message['status'] = 'error';
            $message['distance'] = $distance;
            $message['store_distance'] = $store_distance;
            $message['reason']   = Translation::field('error_not_valid_distance',$lang_id);
        }

        return Response::json($message);
    }



    public function postValidateCoupon() {

        $user_id      = Input::json('user_id');
        $code         = Input::json('code');
        $lang_id      = Input::json('lang_id');
        $total_cost   = Input::json('total_cost');

        $message = array();

        $coupon = Coupon::where('code','=',$code)->where('isactive','=',1)->first();

        if ($coupon) {

            $phone = User::find($user_id)->mobile;

            $cnt = Foodorder::where('phone','=',$phone)->where('coupon','=',$code)->count();
           
            //$cnt = Foodorder::where('user_id','=',$user_id)->where('coupon','=',$code)->count();
            //$cnt = Foodorder::where('phone','=',$phone)->where('coupon','=',$code)->count();

                if ($cnt == 0) {
                    $message["status"]   = "success";
                    $message["message"]  = Translation::field('coupon_is_valid',$lang_id);
                    $message["discount"] = floatval($coupon->discount);
                    $message["cost"]     = Coupon::calculatePrice($total_cost, $coupon->discount );
                } else {
                    $message["status"]  = "error";
                    $message["message"] = Translation::field('coupon_is_used',$lang_id);
                }

        } else {
            $message["status"]  = "error";
            $message["message"] = Translation::field('coupon_is_invalid',$lang_id);
        }

        return Response::json($message);
    }



    public function postStoreList() {

        $message    = array();
        $user_lat   = Input::json('user_lat');
        $user_lot   = Input::json('user_lot');
        $lang_id    = Input::json('lang_id');
        $venue_id   = Input::json('venue_id');
        $stores     = array();
        $sort       = array();
        $newList    = array();
        $finalList  = array();

        $i = 0;

        # kritiria
        # 1. energo store  | OK sto proto for
        # 2. na einai stin | sto if tou for aktina eksipiretisis
        # 3. na kaluptei to orario leitourgias | sto if tou for apo me function Store::checkStatus
        # 4. na kaluptei tin elaxisth paraggelia 
        # 5. fernei 0 i 1 an to public_key toy store exei den einai keno


        foreach (Store::where('isactive','=',1)->where('lang_id','=',$lang_id)->where('venue_id','=',$venue_id)->get() as $key => $value) {
            
            $stores[$key]['name']         = $value->name;
            $stores[$key]['distance_num'] = floatval(Settings::distance( $user_lat , $user_lot, $value->geoy, $value->geox, "K"));
            $stores[$key]['orario']       = Store::checkStatus($value->store_id,$lang_id);
            $stores[$key]['range']        = $value->order_range;
            $stores[$key]['order_cost']   = $value->min_order_cost;

            if ( $stores[$key]['orario'] == true && ($stores[$key]['range'] >= $stores[$key]['distance_num']) ) {
               
               $newList[$i]['store_id']     = $value->store_id;
               $newList[$i]['name']         = $value->name;
               $newList[$i]['distance_num'] = number_format(floatval(Settings::distance( $user_lat , $user_lot, $value->geoy, $value->geox, "K")),2);
               $newList[$i]['orario']       = Store::checkStatus($value->store_id,$lang_id);
               $newList[$i]['store_range']  = $value->order_range;
               $newList[$i]['order_cost']   = floatval($value->min_order_cost);
               $newList[$i]['lat']          = floatval($value->geoy);
               $newList[$i]['lot']          = floatval($value->geox);

               $record = Store::where('store_id','=',$value->store_id)->first();

               $newList[$i]['braintree']    = strlen($record->public_key) == 0 ? 0 : 1;

               $newList[$i]['delivery_time'] = strlen($value->delivery_time) > 0 ? $value->delivery_time : (int)Settings::getValue('delivery_time');

               $i++;
            }
        }

        if (sizeof($newList) > 0) {
            $sort = Store::Sorting($newList);
            $finalList['message'] = "";
        } else {
            $sort = $newList;
            $finalList['message'] = Translation::field('error_store_not_serve',$lang_id);
        }

        
        $finalList['stores'] = $sort;

        return Response::json($finalList);

        //to check disable line above
        $sort = Store::Sorting($stores);
        return Response::json($sort);
    }



    public function postValidateFirstOrder() {

        $user_id      = Input::json('user_id');
        $store_id     = Input::json('store_id');
        $total_cost   = Input::json('total_cost'); 
        $lang_id      = Input::json('lang_id'); 
        $message = array();


        if (Settings::getValue('first_order_discount') == 0) {
            $message['discount']   = 0;
            $message['total_cost'] = floatval($total_cost);
            return $message;
        }

        $user  = User::where('id','=',$user_id)->count();

        if ($user > 0) {
            $message['discount']   = (int)Foodorder::checkDiscount($user_id);
            $message['total_cost'] = floatval( Foodorder::validate_first_order($user_id,$store_id,$total_cost));
        } else {
            $message['status']   = 'error';
            $message['reason']   = Translation::field('error_general',$lang_id);
        } 

        return Response::json($message);

    }



    public function postValidateDate() {

        //return $x = Carbon::createFromTime(8, 40, 0, 'Europe/Athens');
        //date_default_timezone_set('Europe/Athens');
        //date_default_timezone_set('UTC');

        $lang_id = strlen(Input::get('lang_id') > 0) ?  Input::get('lang_id') : 1;
        $store_id = Input::get('store_id');

        $message  = array();

        $timestamp = Input::get('timestamp');

        date_default_timezone_set('Europe/Athens');
        date_default_timezone_set('UTC');

        $record = Working::where('id','=',Settings::to_week_number($timestamp))->first();
        //add +2hours gmt
        $converted_time = strtotime(Settings::toGmt($timestamp));
        
        $day = 0;
        $afternoon = 0;


        if ( (int)Settings::getValue('catalog_status') == 0) {
            $message['status']  = 'error';
            $message['reason']  = Translation::field('error_store_closed',$lang_id);
            return Response::json($message);
        }



        //spasto orario
        if ($record->split_time == 1) {
            
            if ($converted_time >= strtotime($record->from_time) && $converted_time <= strtotime($record->to_time) ) { 
                $day = 1;
            }

            if ($converted_time >= strtotime($record->and_from) && $converted_time <= strtotime($record->and_to) ) { 
                $afternoon = 1;
            }

            
            //return $record->and_to;
            //return $converted_time.'-'.strtotime($record->and_to);
            //return "day ".$day."-"."after ".$afternoon;

            if ($day == 1 || $afternoon == 1) {
                $message['status']  = 'success';
                $message['message'] = Translation::field('error_valid_hours',$lang_id);
            } else {
                $message['status']  = 'error';
                $message['reason']  = Translation::field('error_store_not_serve',$lang_id);
            }

        //aplo orario
        } else {

            //return strtotime($record->from_time)."<br>".$converted_time."<br>".strtotime($record->to_time);

            if ($converted_time >= strtotime($record->from_time) && $converted_time <= strtotime($record->to_time) ) { 
                $day = 1;
            }

            if ($day == 1)  {
                $message['status']  = 'success';
                $message['message'] = Translation::field('error_valid_hours',$lang_id);
            } else {
                $message['status']  = 'error';
                $message['reason']  = Translation::field('error_store_not_serve',$lang_id);
            }

        }

        
        return Response::json($message);
        //return $record;
    }



    public function postValidateUser() {
       
        $message = array();

        $email   = Input::get('email');  
        $lang_id = Input::get('lang_id');  

        $cnt = User::where('email','=',$email)->count();

        if ($cnt > 0) {
            $message['status']  = 'success';
            $message['message'] = Translation::field('validate_user_error_message',$lang_id);
        } else {
            $message['status']  = 'error';
            $message['reason']  = '';
        }

        return Response::json($message);
    }



    public function postReservationNum() {

          $data = array();

          $data['order_num']    = Reservation::max('id');
          $data['details']      = Reservation::find($data['order_num']);
          $data['count']        = Reservation::count();

          $data['time']         = Settings::to24($data['details']->created_at);

          return $data;
    }









}
