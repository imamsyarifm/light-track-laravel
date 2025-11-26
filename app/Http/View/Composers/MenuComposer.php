<?php 
namespace App\Http\View\Composers;

use Illuminate\View\View;

class MenuComposer
{
    /**
     * Bind data to the view.
     *
     * @param  \Illuminate\View\View  $view
     * @return void
     */
    public function compose(View $view)
    {
        $menuData = config('menu.verticalMenu'); 

        $view->with('menuData', $menuData);
        
    }
}