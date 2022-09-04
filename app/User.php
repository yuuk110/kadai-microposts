<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Http\Request;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'profile',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    public function microposts(){
        // user = micropost
        return $this->hasMany(Micropost::class);
    }

    // userがフォローしているuser
    // 見に行くモデル名, 見に行くテーブル名, 自分のid, 取得しに行くid
    public function followings(){
        return $this->belongsToMany(User::class, 'user_follow', 'user_id', 'follow_id')->withTimestamps();
    }

    // userがフォローされているuser
    public function followers(){
        return $this->belongsToMany(User::class, 'user_follow', 'follow_id', 'user_id')->withTimestamps();
        
    }

    // フォローする
    public function follow($userId){
        // 既にフォローしているかの確認
        $exist = $this->is_following($userId);

        // 相手が自分自信でないかの確認
        $its_me = $this->id == $userId;

        if ($exist || $its_me) {
            // 既にフォローしていれば何もしない
            return false;
        } else {
            // 未フォローであればフォローする
            $this->followings()->attach($userId);
            return true;
        }
    }

    // フォロー外す
    public function unfollow($userId){
        // 既にフォローしているかの確認
        $exist = $this->is_following($userId);

        // 相手が自分自身ではないかの確認
        $its_me = $this->id == $userId;
    
        if ($exist && !$its_me) {
            // 既にフォローしていればフォローを外す
            $this->followings()->detach($userId);
            return true;
        } else {
            // 未フォローであれば何もしない
            return false;
        }
    }

    public function is_following($userId){
        // follow_id = userId いたらTRUE いなかったらFALSE
        return $this->followings()->where('follow_id', $userId)->exists();
    }

    public function feed_microposts(){
        // userがフォローしている人のidを、配列で取得
        // pluck() → 指定したカラム名だけを抜き出す
        $follow_user_ids = $this->followings()->pluck('users.id')->toArray();

        // 自分の投稿も表示させたいので、自分のidも配列に追加しておく
        $follow_user_ids[] = $this->id;

        // Micropostから、follow_user_idsの中にあるユーザーidで全部取得して返す
        return Micropost::whereIn('user_id', $follow_user_ids);
    }

    // userがお気に入り登録しているmicropost
    // 見に行くモデル名, 見に行くテーブル名, 自分のid, 取得しに行くid
    public function favorites(){
        return $this->belongsToMany(Micropost::class, 'favorites', 'user_id', 'micropost_id')->withTimestamps();
    }

    // お気に入り登録する
    public function favorite($micropostId){
        // 既にお気に入り登録しているかの確認
        $exist = $this->is_favorites($micropostId);

        // 相手が自分自信でないかの確認
        // $its_me = $this->id == $micropostId;

        if ($exist) {
            // 既にお気に入り登録していれば何もしない
            return false;
        } else {
            // まだお気に入りしていなかったら登録する
            $this->favorites()->attach($micropostId);
            return true;
        }
    }

    // お気に入り外す
    public function unfavorite($micropostId){
        // 既にお気に入り登録しているかの確認
        $exist = $this->is_favorites($micropostId);

        // 相手が自分自身ではないかの確認
        // $its_me = $this->id == $userId;
    
        if ($exist) {
            // 既にお気に入り登録していればお気に入りから外す
            $this->favorites()->detach($micropostId);
            return true;
        } else {
            // まだお気に入り登録していなければ何もしない
            return false;
        }
    }

    // お気に入り登録している？
    public function is_favorites($micropostId){
        // micropost_id = micropostId いたらTRUE いなかったらFALSE
        return $this->favorites()->where('micropost_id', $micropostId)->exists();
    }
}