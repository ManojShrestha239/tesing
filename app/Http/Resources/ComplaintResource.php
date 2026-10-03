<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ComplaintResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        // return parent::toArray($request);
        return [
            'id' => $this->id,
            'issuetype_id' => $this->issuetype_id,
            'issue_desc' => $this->issue_desc,
            'user_type' => $this->user_type,
            'status' => $this->status,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
            'issuetype' => $this->issuetype,
            'dealer name' => $this->dealeruser->dealeruser->name,
            'dealer code' => $this->dealeruser->dealeruser->code,
            'dealer phone' => $this->dealeruser->dealeruser->phone,
            'complaintimage' => ComplaintImageResource::collection($this->complaintimage),
        ];
    }
}
