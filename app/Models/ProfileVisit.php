<?php

namespace App\Models;

use Database\Factories\ProfileVisitFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['profile_user_id', 'visitor_id'])]
class ProfileVisit extends Model
{
    /** @use HasFactory<ProfileVisitFactory> */
    use HasFactory;

    /**
     * @return BelongsTo<User, $this>
     */
    public function profileUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'profile_user_id');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function visitor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'visitor_id');
    }
}
