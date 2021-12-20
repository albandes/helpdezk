<?php
 
namespace App\modules\admin\models\mysql;

final class featureModel
{
    /**
     * @var array
     */
    private $globalSettingsList;

    /**
     * Get the value of globalSettingsList
     *
     * @return  array
     */ 
    public function getGlobalSettingsList(): array
    {
        return $this->globalSettingsList;
    }

    /**
     * Set the value of globalSettingsList
     *
     * @param  array  $globalSettingsList
     *
     * @return  self
     */ 
    public function setGlobalSettingsList(array $globalSettingsList): self
    {
        $this->globalSettingsList = $globalSettingsList;

        return $this;
    }
}