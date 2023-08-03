<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Entity;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Collection;


class APIController extends Controller
{ 

    public function getCategories() 
    {
        $client = new Client();
        $response = $client->get('https://api.publicapis.org/entries')->getBody();
        $entity = json_decode($response, true);
        $entries = collect($entity['entries']);
        $this->insertEntity($this->getCategoriesForInsert($entries));
        return $entries;
    }

    private function getCategoriesForInsert(Collection $entries): array 
    {
        return $entries
            ->filter(fn($entry) => $entry['Category'] === 'Animals' || $entry['Category'] === 'Security')
            ->map(fn($item) => [
                "api" => $item["API"],
                "description" => $item["Description"],
                "link" => $item["Link"],
                "category_id" => $item["Category"]
            ])
            ->toArray();
    }

    private function insertEntity(array $data): void 
    {
        $dataInsert = $this->parseCategory($data);
        Entity::query()->insert($dataInsert);
    }

    private function parseCategory(array $data): array 
    {
        return collect($data)->map(function($item) {
            $category = Category::query()
                ->where('category', $item['category_id'])
                ->first();
            
            return [
                ...$item,
                "category_id" => $category->id
            ];
        })
        ->toArray();
    }
}
