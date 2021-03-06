<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\CookerBook;
use App\Models\Recipe;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;

class CookerBookController extends Controller
{

    public $limit;
    public $offset;

    public function __construct()
    {
        $this->limit = config('constants.limit');
        $this->offset = config('constants.offset');
    }


    public function addRecipe(Request $request, $idRecipe){
        $user = Auth::user();



        if($user && $user->can("addRecipe", CookerBook::class)) {

            $cookerBook = CookerBook::where('idUser', $user->id)->first();
//dd($cookerBook);
            if (!$cookerBook) {
                $newCookerBook = CookerBook::create([
                    'idUser' => $user->id
                ]);

                //dd($newCookerBook);
                if (!$newCookerBook) {
                    return redirect()->back();
                } else {
                    $cookerBook = CookerBook::findOrFail($newCookerBook->idCookerBook);

                }
            }

                if ($cookerBook->hasRecipe($idRecipe)) {
                    $request->session()->flash('flash_message', 'Данный рецепт уже есть в Вашей кулинарной книге!');
                    return redirect()->back();
                } else {
                    $recipe = Recipe::findOrFail($idRecipe);
                    $cookerBook->recipes()->attach($recipe);
                    return redirect('/show');
                }
        }//if
        else{
            if($user && $user->hasStatus(2)){
                $request->session()->flash('flash_message', 'Ваша учетная запись заблокирована! Обратитесь к администратору! ');
                return redirect()->back();
            }
            return view('auth.login');
        }


    }//addRecipe
    public function show(Request $request){
        $user = Auth::user();
        if($user && $user->can("show", CookerBook::class)) {
            $cookerBook = CookerBook::where('idUser', $user->id)->first();
            $recipes = $cookerBook->recipes()->get();
            $category = [];

            foreach ($recipes as $recipe) {
                //dd($recipe);
                $cat = Category::findOrFail($recipe->idCategory);
                if (array_search($cat, $category) === false) {
                    $category[] = Category::where('idCategory', $recipe->idCategory)->first();
                }

            }

            $context = [
                'categories' => $category
            ];
            return view('public.cookerBook.show', $context);
        }
        else{
            if($user && $user->hasStatus(2)){
                $request->session()->flash('flash_message', 'Ваша учетная запись заблокирована! Обратитесь к администратору! ');
                return redirect()->back();
            }
            return view('auth.login');
        }
    }//show

    public function getRecipesByCategory($idCategory){
        $user = Auth::user();
        if($user ){
            $category = Category::findOrFail($idCategory);
            $cookerBook = CookerBook::where('idUser', $user->id)->first();
            $recipes = $cookerBook->recipes()->where('idCategory', $idCategory)->skip($this->offset)->take($this->limit)->get();
            $recipeInfo=[];
            foreach($recipes as $recipe){
                $recipeUser = User::findOrFail($recipe->idUser)->toJson();
                $user = json_decode($recipeUser);
                $likes = $recipe->usersWhoLike()->count();
                $dislike = $recipe->usersWhoDislike()->count();
                $app = app();
                $obj = $app->make('stdClass');
                $obj->recipe = $recipe;
                $obj->likes = $likes;
                $obj->dislikes = $dislike;
                $obj->user = $user ;
                $recipeInfo[]=$obj;
            }
            $context = [
                'recipes'=>$recipeInfo,
                'category'=>$category

            ];
            return view('public.cookerBook.showRecipesByCategory', $context);
        }
        else{
            return view('auth.login');
        }

    }//getRecipesByCategory

    public function showMoreRecipes(Request $request){
        $offset = $request->input('offset');
        $limit = $request->input('limit');
        $idCategory = $request->input('idCategory');
        $user = Auth::user();

        //dd($user);
        if($user){
            $category = Category::findOrFail($idCategory);
//return $category;
            $cookerBook = CookerBook::where('idUser', $user->id)->first();
            $recipes = $cookerBook->recipes()->where('idCategory', $idCategory)->skip($offset)->take($limit)->get();
           if(count($recipes) > 0 ) {
                $recipeInfo = [];
                foreach ($recipes as $recipe) {
                    $recipeUser = User::findOrFail($recipe->idUser)->toJson();
                    $user = json_decode($recipeUser);
                    $app = app();
                    $obj = $app->make('stdClass');
                    $obj->recipe = $recipe;
                    $obj->user = $user;
                    $recipeInfo[] = $obj;
                }
                $context = [
                    'recipes' => $recipeInfo,
                    'category' => $category

                ];
                return $context;
            }
            else{
                return 0;
            }

        }
        else{
            return view('auth.login');
       }
    }

    public function deleteRecipeFromCookerBook(Request $request, $idRecipe){
        //dd($idRecipe);

        $user = Auth::user();
        $cookerBook = CookerBook::where('idUser', $user->id)->first();

        if($user){
            if ($cookerBook->hasRecipe($idRecipe)) {
                $recipe = Recipe::findOrFail($idRecipe);
                $cookerBook->recipes()->detach($recipe);
                $request->session()->flash('flash_message', 'Рецепт удален из Вашей кулинарной книги!');
                return redirect()->back();
            }
            else{
                $request->session()->flash('flash_message', 'Данного рецепта нет в Вашей кулинарной книге!');
                return redirect()->back();
            }
        }
        else{
            return view('auth.login');
        }
    }
}
