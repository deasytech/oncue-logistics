<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'customer_id',
        'category_id',
        'subcategory_id',
        'custom_subcategory',
        'location',
        'estimated_number_of_guest',
        'event_date',
        'aso_ebi_color',
        'logo',
        'description',
        'notes',
        'slug',
        'is_active',
    ];

    protected $casts = [
        'event_date' => 'datetime',
        'is_active' => 'boolean',
    ];

    protected $appends = ['display_subcategory'];

    public function getDisplaySubcategoryAttribute()
    {
        if ($this->category && $this->category->name === 'Custom') {
            return $this->custom_subcategory;
        }
        return $this->subcategory?->name;
    }

    protected static function booted()
    {
        static::creating(function ($event) {
            if (empty($event->slug)) {
                $event->slug = Str::slug($event->name . '-' . Str::random(6));
            }
        });
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function subCategory()
    {
        return $this->belongsTo(Category::class, 'subcategory_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function guests()
    {
        return $this->belongsToMany(Guest::class)
            ->withPivot([
                'attendance_status',
                'plus_one',
                'rsvp_token',
                'reminder_attempts',
                'rsvp_sent_at',
                'rsvp_expires_at',
                'rsvp_responded_at',
                'last_reminder_sent_at',
            ])
            ->withTimestamps();
    }

    public function delivery()
    {
        return $this->hasOne(Delivery::class);
    }

    public function asoEbiSubscriptions()
    {
        return $this->hasMany(AsoEbiSubscription::class);
    }

    public function guestFabricSelections()
    {
        return $this->hasMany(GuestFabricSelection::class);
    }

    public function packageCustomizations()
    {
        return $this->belongsToMany(PackageCustomization::class, 'package_customization_event');
    }

    public function fabricTypes()
    {
        return $this->belongsToMany(FabricType::class)
            ->withPivot('custom_price')
            ->withTimestamps();
    }

    public function scopeIsActive($query)
    {
        return $query->where('is_active', true);
    }
}
