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
            'nasi kuning' => ['kalori'=>450,'protein'=>15,'lemak'=>12,'karbohidrat'=>70],
            'mie goreng' => ['kalori'=>400,'protein'=>10,'lemak'=>15,'karbohidrat'=>55],
            'telur balado' => ['kalori'=>200,'protein'=>14,'lemak'=>12,'karbohidrat'=>5],
            'sop sayur' => ['kalori'=>150,'protein'=>5,'lemak'=>3,'karbohidrat'=>25],
            'ikan bakar' => ['kalori'=>250,'protein'=>28,'lemak'=>8,'karbohidrat'=>0],
            'bubur kacang hijau' => ['kalori'=>250,'protein'=>8,'lemak'=>5,'karbohidrat'=>45],
            'susu kotak' => ['kalori'=>120,'protein'=>7,'lemak'=>4,'karbohidrat'=>12],
            'pisang' => ['kalori'=>90,'protein'=>1,'lemak'=>0,'karbohidrat'=>23],
            'sayur lodeh' => ['kalori'=>200,'protein'=>6,'lemak'=>15,'karbohidrat'=>10],
            'tempe orek' => ['kalori'=>180,'protein'=>12,'lemak'=>10,'karbohidrat'=>15],
        ];

        return $data[strtolower($food)] ?? [
            'kalori'=>300,'protein'=>10,'lemak'=>10,'karbohidrat'=>40
        ];
    }
}
