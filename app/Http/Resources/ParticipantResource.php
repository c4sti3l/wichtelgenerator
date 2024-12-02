<?php

namespace App\Http\Resources;

use App\Services\Participant\ParticipantService;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class UserResource
 */
class ParticipantResource extends JsonResource {
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     */
    public function toArray($request): array {

        $p = new ParticipantService();

        $data = $this->resource->toArray();
        $data['chosen_by_decrypt'] = $p->decryptString($data['chosen_by']);
//        $data['count'] = $data['count'] === 0 ? 0 : ($data['count'] / 2);

        return $data;
    }
}
