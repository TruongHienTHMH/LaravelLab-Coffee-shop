<?php

namespace App\Models;

use Database\Factories\ProductFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    /** @use HasFactory<ProductFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'price',
        'currency',
        'display_image_url',
        'category_id',
    ];


    public const DEFAULT_CURRENCY = 'VNĐ';

    public const DEFAULT_IMAGE = 'https://images.unsplash.com/photo-1509042239860-f550ce710b93';

    /**
     * @return BelongsTo<Category, $this>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function getFormattedPriceAttribute(): string
    {
        return number_format((float) $this->price).' '.$this->currency;
    }

    public function getFormattedTotalAmount(int $quantity = 1): string
    {
        return number_format((float) ($this->price * $quantity)).' '.$this->currency;
    }
}
