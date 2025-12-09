<?php

namespace App\Http\Resources\Api\V1\Admin\UserListing;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserListingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'email' => $this->whenLoaded('user', $this->user->email),
            ],
            'model' => [
                'id' => $this->model->id,
                'name' => $this->model->name,
                'brand' => $this->when($this->relationLoaded('model') && $this->model->relationLoaded('brand'), [
                    'id' => $this->model->brand->id,
                    'name' => $this->model->brand->name,
                    'image_url' => $this->model->brand->image_url,
                ]),
            ],
            'year' => $this->year,
            'condition' => $this->condition,
            'condition_label' => $this->getConditionLabel(),
            'price' => $this->price,
            'price_formatted' => '$' . number_format($this->price, 2),
            'description' => $this->description,
            'status' => $this->status,
            'status_label' => $this->getStatusLabel(),
            'status_badge_color' => $this->getStatusBadgeColor(),
            'images' => UserListingImageResource::collection($this->whenLoaded('images')),
            'primary_image' => $this->when(
                $this->relationLoaded('images'),
                function () {
                    $primaryImage = $this->images->firstWhere('is_primary', true);
                    return $primaryImage ? new UserListingImageResource($primaryImage) : null;
                }
            ),
            'images_count' => $this->when(isset($this->images_count), $this->images_count),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
            'created_at_human' => $this->created_at?->diffForHumans(),
            'can_edit' => $this->canEdit(),
            'can_delete' => $this->canDelete(),
            'can_mark_sold' => $this->canMarkAsSold(),
            'can_reactivate' => $this->canReactivate(),
        ];
    }

    /**
     * Get human-readable condition label.
     */
    private function getConditionLabel(): string
    {
        return match ($this->condition) {
            'new' => 'New',
            'like-new' => 'Like New',
            'good' => 'Good',
            'fair' => 'Fair',
            'for-parts' => 'For Parts',
            default => ucfirst($this->condition),
        };
    }

    /**
     * Get human-readable status label.
     */
    private function getStatusLabel(): string
    {
        return match ($this->status) {
            'pending' => 'Pending Approval',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
            'sold' => 'Sold',
            'expired' => 'Expired',
            default => ucfirst($this->status),
        };
    }

    /**
     * Get status badge color for frontend.
     */
    private function getStatusBadgeColor(): string
    {
        return match ($this->status) {
            'pending' => 'warning',
            'approved' => 'success',
            'rejected' => 'danger',
            'sold' => 'info',
            'expired' => 'secondary',
            default => 'primary',
        };
    }

    /**
     * Check if user can edit this listing.
     */
    private function canEdit(): bool
    {
        return !in_array($this->status, ['approved', 'sold']);
    }

    /**
     * Check if user can delete this listing.
     */
    private function canDelete(): bool
    {
        return true;
    }

    /**
     * Check if user can mark this listing as sold.
     */
    private function canMarkAsSold(): bool
    {
        return $this->status === 'approved';
    }

    /**
     * Check if user can request reactivation.
     */
    private function canReactivate(): bool
    {
        return in_array($this->status, ['rejected', 'expired']);
    }
}