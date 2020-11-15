<?php


namespace AppBundle\Helpers;


class DashboardGridBuilder
{

    private $menuItems = [];



    /**
     * @return mixed
     */
    public function getMenuItems()
    {
        return $this->menuItems;
    }

    /**
     * @param $icon
     * @param $label
     * @param $path
     * @param $description
     */
    public function setMenuItems($icon,$label,$path,$description)
    {
        array_push($this->menuItems, [
            'icon'=>$icon,
            'label'=>$label,
            'path'=>$path,
            'description'=>$description
        ]);
    }


}