<?php

namespace App\Services;

class NutritionService
{
    public function get($food)
    {
        $data = [
            'fried rice' => ['kalori'=>450,'protein'=>12,'lemak'=>15,'karbohidrat'=>65],
            'chicken rice' => ['kalori'=>500,'protein'=>30,'lemak'=>20,'karbohidrat'=>60],
            'ayam goreng' => ['kalori'=>350,'protein'=>25,'lemak'=>18,'karbohidrat'=>10],
            'nasi padang' => ['kalori'=>600,'protein'=>25,'lemak'=>30,'karbohidrat'=>80],
            'burger' => ['kalori'=>400,'protein'=>15,'lemak'=>20,'karbohidrat'=>40],
        ];

        return $data[strtolower($food)] ?? [
            'kalori'=>300,'protein'=>10,'lemak'=>10,'karbohidrat'=>40
        ];
    }
}
