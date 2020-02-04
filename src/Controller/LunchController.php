<?php

namespace App\Controller;

use stdClass;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\KernelInterface;

class LunchController extends AbstractController
{
    private $appKernel;

    public function __construct(KernelInterface $appKernel)
    {
        $this->appKernel = $appKernel;

        $ingredientFinder = new Finder;
        $ingredientFinder->files()->in($this->appKernel->getProjectDir() . '/dist')->name('ingredient.json');
        $ingredient_obj = new stdClass;
        foreach ($ingredientFinder as $file) {
            $ingredient_content = $file->getContents();
            $ingredient_obj = \json_decode($ingredient_content);
        }
        $this->ingredients = $ingredient_obj;
    }

    private function getIngredient($use_by)
    {
        $use_by = $use_by == '' ? date('Y-m-d') : $use_by;        
        $ingredients = [];
        $ingredient_obj = $this->ingredients;
        $ingredients = array_values(array_filter($ingredient_obj->ingredients, function($ingredient) use ($use_by) {
            return $use_by <= $ingredient->{"use-by"};
        }));           
        $ingredients = array_column($ingredients, 'title');      

        return $ingredients;
    }

    private function isFreshIngredient($ingredients_in_recipe, $date_order)
    {
        $ingredient_obj = $this->ingredients;
        $ingredients = array_filter($ingredient_obj->ingredients, function($ingredient) use ($ingredients_in_recipe, $date_order) {
            $isGreat = strtotime($date_order) - strtotime($ingredient->{"best-before"});
            return in_array($ingredient->title, $ingredients_in_recipe) && $isGreat <= 0;
        });

        return count($ingredients) == count($ingredients_in_recipe);
    }

    private function getRecipe($ingredients, $use_by)
    {
        $recipeFilder = new Finder;
        $recipeFilder->files()->in($this->appKernel->getProjectDir() . '/dist')->name('recipe.json');
        $recipes = [];
        foreach ($recipeFilder as $file) {
            $recipe_content = $file->getContents();
            $recipe_obj = \json_decode($recipe_content);
            
            $recipes['freshest'] = array_values(array_filter($recipe_obj->recipes, function($recipe) use($ingredients, $use_by) {
                $diff = array_diff($recipe->ingredients, $ingredients);
                return count($diff) == 0 && $this->isFreshIngredient($recipe->ingredients, $use_by);
            }));
            $recipes['freshest'] = array_map(function($recipe) {
                return $recipe->title;
            }, $recipes['freshest']);            
            
            $recipes['oldest'] = array_values(array_filter($recipe_obj->recipes, function($recipe) use($ingredients, $use_by) {
                $diff = array_diff($recipe->ingredients, $ingredients);
                return count($diff) == 0 && !$this->isFreshIngredient($recipe->ingredients, $use_by);
            }));
            $recipes['oldest'] = array_map(function($recipe) {
                return $recipe->title;
            }, $recipes['oldest']);            
        }

        return $recipes['freshest'] || $recipes['oldest'] ? $recipes : [];
    }
    
    /**
     * @Route("/lunch/{use_by}", name="lunch")
     */
    public function index(String $use_by = '')
    {        
        // Ingredients
        $ingredients = $this->getIngredient($use_by);          
        // Recipes
        $recipes = $this->getRecipe($ingredients, $use_by);        

        return $this->json([
            'message' => 'Success',
            'data' => $recipes
        ]);
    }
}
