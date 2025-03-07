<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MessageReceiversResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'avatar' => $this->avatar->fileBySize('thumb')->url,
            'account_type' => ucwords($this->account_type),
            'is_admin' => $this->isAdminUser(), 
            'is_verified' => !empty($this->approved_at)
        ];
    }
}
