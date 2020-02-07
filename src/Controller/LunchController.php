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
        $jsonData = $this->initJsonData('*.json');
        $this->ingredients = $jsonData[0] ?? '';
        $this->recipes = $jsonData[1] ?? '';
    }

    private function initJsonData($fileName)
    {
        $finder = new Finder;
        $finder->files()->in($this->appKernel->getProjectDir() . '/dist')->name($fileName);
        $obj = [];
        foreach ($finder as $file) {
            $contents = $file->getContents();
            $obj[] = \json_decode($contents);
        }

        return $obj;
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
        $recipes = [];
        $recipe_obj = $this->recipes;
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
