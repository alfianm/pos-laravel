<?php

namespace App\Models;

use App\Traits\Auditable;
use App\Traits\TenantAware;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Product extends Model implements HasMedia
{
    use Auditable, HasFactory, HasUuids, InteractsWithMedia, TenantAware;

    protected $guarded = [];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('main_image')
            ->singleFile()
            ->useFallbackUrl('https://ui-avatars.com/api/?name='.urlencode($this->name).'&background=f1f5f9&color=64748b&size=512');
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(200)
            ->height(200)
            ->sharpen(10);
    }

    public function getImageUrlAttribute()
    {
        return $this->getFirstMediaUrl('main_image') ?: $this->fallback_image_url;
    }

    public function getFallbackImageUrlAttribute()
    {
        return 'https://ui-avatars.com/api/?name='.urlencode($this->name).'&background=f1f5f9&color=64748b&size=512';
    }

    public function category()
    {
        return $this->belongsTo(ProductCategory::class, 'category_id');
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function inventories()
    {
        return $this->hasMany(Inventory::class);
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function branchPrices()
    {
        return $this->hasMany(ProductBranchPrice::class);
    }

    public function activeBranchPrice()
    {
        return $this->hasOne(ProductBranchPrice::class)
            ->where('branch_id', auth()->user()?->active_branch_id);
    }
}
