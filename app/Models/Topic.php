<?php

namespace App\Models;

use App\Observers\TopicObserver;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 *
 *
 * @property int $id
 * @property string $title 标题
 * @property string $body 内容
 * @property int $user_id 用户ID
 * @property int $category_id 分类ID
 * @property int $view_count 浏览次数
 * @property int $reply_count 回复次数
 * @property int|null $last_reply_user_id 最后回复用户ID
 * @property int $order 排序
 * @property string|null $excerpt 摘要
 * @property string|null $slug 别名
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Category|null $category
 * @property-read \App\Models\User|null $user
 * @method static \Database\Factories\TopicFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Topic newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Topic newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Topic query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Topic whereBody($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Topic whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Topic whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Topic whereExcerpt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Topic whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Topic whereLastReplyUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Topic whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Topic whereReplyCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Topic whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Topic whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Topic whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Topic whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Topic whereViewCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Topic recent()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Topic recentReplied()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Topic withOrder(?string $order)
 * @mixin \Eloquent
 */

#[ObservedBy(TopicObserver::class)]

class Topic extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'body', 'category_id', 'excerpt', 'slug'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    /**
     * Scope a query to order topics based on the specified order.
     *
     * @param $query
     * @param string|null $order
     * @return void
     */
    public function scopeWithOrder($query, ?string $order): void
    {
        switch ($order) {
            case 'recent':
                $query->recent($query);
                break;
            default:
                $query->recentReplied($query);
                break;
        }
    }

    /**
     * Scope a query to order topics by creation date.
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeRecent(Builder $query): Builder
    {
        return $query->orderBy('created_at', 'desc');
    }

    /**
     * Scope a query to order topics by the most recent reply.
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeRecentReplied(Builder $query): Builder
    {
        // 当话题有新回复时，我们将编写逻辑来更新话题模型的 reply_count 属性，
        // 此时会自动触发框架对数据模型 updated_at 时间戳的更新
        return $query->orderBy('updated_at', 'desc');
    }
}
