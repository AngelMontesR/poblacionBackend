<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PoblacionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = parent::toArray($request);

        if (isset($data['links'])) {
            $data['links'] = array_map(function ($link) {
                if ($link['label'] == 'Next &raquo;') {
                    $link['label'] = 'Siguiente';
                } elseif ($link['label'] == '&laquo; Previous') {
                    $link['label'] = 'Anterior';
                }
                return $link;
            }, $data['links']);
        }

        return $data;
    }
}
