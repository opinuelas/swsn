<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Gender;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'userid', 'email', 'password', 'cell_number', 'gender', 'country', 'state', 'city', 'physical_description', 'finding_preference', 'profile_picture_id'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'gender' => Gender::class,
        ];
    }

    /**
     * @return HasMany<Picture, $this>
     */
    public function pictures(): HasMany
    {
        return $this->hasMany(Picture::class);
    }

    /**
     * @return BelongsTo<Picture, $this>
     */
    public function profilePicture(): BelongsTo
    {
        return $this->belongsTo(Picture::class, 'profile_picture_id');
    }

    /**
     * @return HasMany<Like, $this>
     */
    public function likes(): HasMany
    {
        return $this->hasMany(Like::class);
    }

    /**
     * @return HasMany<Comment, $this>
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * Visits other users made to this user's public profile.
     *
     * @return HasMany<ProfileVisit, $this>
     */
    public function profileVisits(): HasMany
    {
        return $this->hasMany(ProfileVisit::class, 'profile_user_id');
    }

    /**
     * Get the location as a single "city, state, country" string.
     */
    public function location(): string
    {
        return collect([$this->city, $this->state, $this->country])->filter()->implode(', ');
    }

    /**
     * Get the URL of the user's profile picture, or the default avatar.
     */
    public function avatarUrl(): string
    {
        return $this->profilePicture?->url() ?? asset('images/default-avatar.svg');
    }

    /**
     * @return HasMany<Message, $this>
     */
    public function sentMessages(): HasMany
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    /**
     * @return HasMany<Message, $this>
     */
    public function receivedMessages(): HasMany
    {
        return $this->hasMany(Message::class, 'recipient_id');
    }
}
