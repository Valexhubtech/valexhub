<?php

namespace Wave;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'features' => 'array',
        'images' => 'array',
        'is_active' => 'boolean',
        'low_price' => 'decimal:2',
        'high_price' => 'decimal:2',
    ];

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($product) {
            if (empty($product->slug)) {
                $product->slug = Str::slug($product->name);
            }
        });

        static::updating(function ($product) {
            if ($product->isDirty('name') && empty($product->slug)) {
                $product->slug = Str::slug($product->name);
            }
        });

        static::saved(function ($product) {
            static::clearCache();
        });

        static::deleted(function ($product) {
            static::clearCache();
        });
    }

    /**
     * Get the category that owns the product
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the user products for this product
     */
    public function userProducts(): HasMany
    {
        return $this->hasMany(UserProduct::class);
    }

    /**
     * Get the subscriptions for this product
     */
    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    /**
     * Get the changelogs for this product
     */
    public function changelogs(): MorphMany
    {
        return $this->morphMany(Changelog::class, 'loggable');
    }

    /**
     * Get all active products with caching
     */
    public static function getActiveProducts()
    {
        if (app()->bound('cache')) {
            try {
                return Cache::remember('wave_active_products', 1800, function () {
                    return self::where('is_active', 1)
                        ->orderBy('sort_order')
                        ->orderBy('id')
                        ->with(['category'])
                        ->get();
                });
            } catch (Exception $e) {
                // Fallback to direct query if cache fails
            }
        }

        return self::where('is_active', 1)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->with(['category'])
            ->get();
    }

    /**
     * Get products by category with caching
     */
    public static function getByCategory($categoryId)
    {
        if (app()->bound('cache')) {
            try {
                return Cache::remember("wave_products_category_{$categoryId}", 1800, function () use ($categoryId) {
                    return self::where('category_id', $categoryId)
                        ->where('is_active', 1)
                        ->orderBy('sort_order')
                        ->orderBy('id')
                        ->with(['category'])
                        ->get();
                });
            } catch (Exception $e) {
                // Fallback to direct query if cache fails
            }
        }

        return self::where('category_id', $categoryId)
            ->where('is_active', 1)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->with(['category'])
            ->get();
    }

    /**
     * Get product by slug with caching
     */
    public static function getBySlug($slug)
    {
        if (app()->bound('cache')) {
            try {
                return Cache::remember("wave_product_slug_{$slug}", 1800, function () use ($slug) {
                    return self::where('slug', $slug)->with(['category'])->first();
                });
            } catch (Exception $e) {
                // Fallback to direct query if cache fails
            }
        }

        return self::where('slug', $slug)->with(['category'])->first();
    }

    /**
     * Clear product cache
     */
    public static function clearCache()
    {
        if (app()->bound('cache')) {
            try {
                Cache::forget('wave_active_products');
                
                // Clear category-specific caches
                $categories = Category::pluck('id');
                foreach ($categories as $categoryId) {
                    Cache::forget("wave_products_category_{$categoryId}");
                }
                
                // Clear slug-specific caches
                $slugs = self::pluck('slug');
                foreach ($slugs as $slug) {
                    Cache::forget("wave_product_slug_{$slug}");
                }
            } catch (Exception $e) {
                // Silently handle cache clearing failures
            }
        }
    }

    /**
     * Get the price range display
     */
    public function getPriceRangeAttribute()
    {
        if ($this->low_price && $this->high_price) {
            if ($this->low_price == $this->high_price) {
                return '$' . number_format($this->low_price, 2);
            }
            return '$' . number_format($this->low_price, 2) . ' - $' . number_format($this->high_price, 2);
        } elseif ($this->low_price) {
            return 'From $' . number_format($this->low_price, 2);
        } elseif ($this->high_price) {
            return 'Up to $' . number_format($this->high_price, 2);
        }
        
        return 'Contact for pricing';
    }

    /**
     * Get the first image from images array
     */
    public function getFirstImageAttribute()
    {
        $images = $this->images ?? [];
        return !empty($images) ? $images[0] : null;
    }

    /**
     * Scope for active products
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', 1);
    }

    /**
     * Scope for products by category
     */
    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }
}