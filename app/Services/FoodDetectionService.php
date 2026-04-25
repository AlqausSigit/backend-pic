<?php

namespace App\Services;

use Google\Cloud\Vision\V1\ImageAnnotatorClient;

class FoodDetectionService
{
    public function detect($imagePath)
    {
        try {
            $client = new ImageAnnotatorClient();

            $image = file_get_contents(storage_path('app/public/'.$imagePath));

            $response = $client->labelDetection($image);
            $labels = $response->getLabelAnnotations();

            if (count($labels) == 0) return 'makanan';

            return strtolower($labels[0]->getDescription());
        } catch (\Exception $e) {
            // Fallback for dummy if credentials not set yet
            return 'ayam goreng';
        }
    }
}
