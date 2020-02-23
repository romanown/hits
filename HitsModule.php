<?php

class HitsModule extends ApiWebModule
{
    public function isMenuItem()
    {
        return true;
    }

    protected function getDefaultController()
    {
        return '';
    }

    protected function getDefaultRole()
    {
        return 'catalog';
    }

    protected function getMenuName()
    {
        return 'Логистика';
    }

    protected function getMenuIcon()
    {
        return '/images/icons/catalog.png';
    }

    protected function getMenuLabel()
    {
        return '<img src="/images/icons/logystic.png" alt="" /><span>'.$this->getMenuName().'</span>';
    }

    protected function getDefaultUrl()
    {
        return '/api/Hits/Hits';
    }

    public function getChildrenItems()
    {
        /** @var \WebUser $user */
        $user = Yii::app()->user;

        return [
            [
                'label' => 'Hits',
                'url' => ['/api/Hits/Hits/hits'],
            ],
        ];
    }


    protected $bIblockElementFields;
}
