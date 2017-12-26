<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
use App\Http\Models\Category;
use App\Http\Models\Show;
use App\Http\Models\Band;

/**
 * Manage TicketsTypes
 *
 * @author ivan
 */
class CategoryController extends Controller{
    
    /**
     * List all categories and return default view.
     *
     * @return view
     */
    public function index()
    {
        try {
            //init
            $input = Input::all(); 
            if(isset($input) && isset($input['id']))
            {
                //get selected record
                $category = Category::find($input['id']);
                if($category)
                    return ['success'=>true,'category'=>$category];
                return ['success'=>false,'msg'=>'There was an error getting the category.<br>Maybe it is not longer in the system.'];
            }
            else
            {
                $categories = Category::get_categories('-&emsp;&emsp;');
                //return view
                return view('admin.categories.index',compact('categories'));
            }
        } catch (Exception $ex) {
            throw new Exception('Error Category Index: '.$ex->getMessage());
        }
    }
    /**
     * Save new or updated category.
     *
     * @void
     */
    public function save()
    {
        try {   
            //init
            $input = Input::all(); 
            //active/inactive      
            if($input && (isset($input['category']) && isset($input['active'])))
            {
                $category = Category::find($input['category']);
                if($category)
                {
                    $category->disabled = ($input['active'] == 'true')? 0 : 1;
                    $category->save();
                    return ['success'=>true,'msg'=>'Category updated successfully!'];
                }
                return ['success'=>false,'msg'=>'There was an error getting the category.<br>Maybe it is not longer in the system.'];
            }
            //save all record      
            else if($input)
            {
                $current = date('Y-m-d H:i:s');
                if(isset($input['id']) && $input['id'])
                    $category = Category::find($input['id']);
                else
                    $category = new Category;
                //save category
                $category->name = strip_tags($input['name']);
                $category->id_parent = $input['id_parent'];
                $category->save();
                //return
                return ['success'=>true,'msg'=>'Category saved successfully!'];
            }
            else return ['success'=>false,'msg'=>'There was an error saving the Category.<br>The server could not retrieve the data.'];
        } catch (Exception $ex) {
            throw new Exception('Error Category Save: '.$ex->getMessage());
        }
    }
    /**
     * Remove Categories.
     *
     * @void
     */
    public function remove()
    {
        try {
            //init
            $input = Input::all();
            $msg = ''; 
            $allcategories = [];
            function search_dependences($category)
            {
                $shows = Show::where('category_id',$category->id)->count();
                $bands = Band::where('category_id',$category->id)->count();
                return ($shows>0 || $bands>0);                    
            }
            function add_msg($category_name)
            {
                if($msg=='')
                    $msg = 'The following category have dependences (shows/bands) and the system cannot delete them:<br><br><ol style="max-height:200px;overflow:auto;text-align:left;">';
                $msg .= '<li style="color:red;">'.$category_name.'</li>';
            }
            //delete all records   
            foreach ($input['id'] as $id)
            {
                //get category
                $category = Category::find($id);
                $allCat = [];
                if($category)
                {
                    $dependences = search_dependences($category);
                    if($dependences)
                    {
                        add_msg($category->name);
                        break;
                    }
                    else 
                    {
                        $allCat[] = $category->id;
                        foreach ($category->children() as $child)
                        {
                            $dependences = search_dependences($child);
                            if($dependences)
                            {
                                add_msg($child->name);
                                break;
                            }
                            else 
                            {
                                $allCat[] = $child->id;
                                foreach ($child->children() as $niece)
                                {
                                    $dependences = search_dependences($niece);
                                    if($dependences)
                                    {
                                        add_msg($niece->name);
                                        break;
                                    }
                                    else
                                        $allCat[] = $niece->id;
                                }
                            } 
                            if($dependences) break;
                        }
                    }  
                    if(!$dependences)
                        $allcategories = array_merge ($allcategories,$allCat);
                }
            }
            if(count($allcategories) && Category::destroy($allcategories))
            {
                if($msg!='')
                    return ['success'=>false,'msg'=>$msg];
                return ['success'=>true,'msg'=>'All records deleted successfully!'];
            }  
            return ['success'=>false,'msg'=>'There was an error deleting the category(ies)!<br>They might have some dependences.'];
        } catch (Exception $ex) {
            throw new Exception('Error Category Remove: '.$ex->getMessage());
        }
    }   
    
}
