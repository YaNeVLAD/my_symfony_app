<?php

namespace App\Constants;

class AppConstants
{
    public const BASE_CATEGORY = 'pizza';

    public const EXISTING_CATEGORIES = [
        'pizza' => 'Пиццы',
        'salad' => 'Салаты',
        'drink' => 'Напитки',
        'snack' => 'Горячие закуски',
        'dessert' => 'Десерты',
    ];

    //Test Authentification
    public const USER_SESSION_NAME = 'userId';

    public const UNAUTHORIZED_ID = 0;
}