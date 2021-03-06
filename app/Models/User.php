<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';

    protected $primaryKey = 'id';

    protected $table = 'users';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'email', 'password', 'name', 'userPhoto', 'rating', 'idStatus',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function roles(){
        return $this->belongsToMany(Role::class, 'roles_user', 'idUser', 'idRole');
    }
    public function services(){
        return $this->belongsToMany(Service::class, 'services_users', 'idUser', 'idService')->withPivot('sum');
    }

    public function likeRecipes(){
        return $this->belongsToMany(Recipe::class, 'user_recipe_like', 'idUser', 'idRecipe');
    }

    public function dislikeRecipes(){
        return $this->belongsToMany(Recipe::class, 'user_recipe_dislike', 'idUser', 'idRecipe');
    }
    public function recipies(){
        return $this->hasMany(Recipe::class, 'idUser', 'id');
    }

    public function hasRole($idRole) : bool{
        //dd($this->roles()->where('roles.idRole', $idRole)->count() == 1);
        return $this->roles()->where('roles.idRole', $idRole)->count() == 1;
    }



    public function isLikeRecipe($idRecipe) : bool{
        return $this->likeRecipes()->where('recipes.idRecipe', $idRecipe)->count() == 1;
    }

    public function isDislikeRecipe($idRecipe) : bool{
        return $this->dislikeRecipes()->where('recipes.idRecipe', $idRecipe)->count() == 1;
    }


    public function isLikeDoctor($idDoctor) : bool{
        return $this->likeDoctors()->where('doctor_info.idDoctorInfo', $idDoctor)->count() > 0;
    }

    public function isDislikeDoctor($idDoctor) : bool{
        return $this->dislikeDoctors()->where('doctor_info.idDoctorInfo', $idDoctor)->count() > 0;
    }


    public function status(){
        return $this->belongsTo(Status::class, 'idStatus', 'idStatus');
    }

    public function hasStatus($idStatus) : bool{
        //dd($this->status()->where('idStatus', $idStatus)->count()==1);
        return $this->status()->where('idStatus', $idStatus)->count()==1;
    }

    public function comments(){
        return $this->hasMany(Comment::class, 'idUser', 'id');
    }
    public function cookerBook(){
        return $this->hasOne(CookerBook::class, 'idUser', 'id');
    }

    public function sendMessages(){
        return $this->hasMany(Message::class, 'idSender', 'id');
    }
    public function takeMessages(){
        return $this->hasMany(Message::class, 'idTaker', 'id');
    }
    public function specialties(){
        return $this->hasMany(Specialty::class, 'idUser', 'id');
    }

    public function doctorInfo()
    {
        return $this->hasOne(DoctorInfo::class, 'idUser', 'id');
    }

    public function likeDoctors(){
        return $this->belongsToMany(DoctorInfo::class, 'user_doctor_like', 'idUser', 'idDoctorInfo');
    }

    public function dislikeDoctors(){
        return $this->belongsToMany(DoctorInfo::class, 'user_doctor_dislike', 'idUser', 'idDoctorInfo');
    }
    public function takeNoReadMessagesFromAdmin(){

        $roleAdmin = Role::where('idRole', 3)->first();
        $admin = $roleAdmin->users()->first();
        $messages = $this->hasMany(Message::class, 'idTaker', 'id')->get();
        $notReadMessages = [];
        foreach($messages as $message){
            if($message->idSender === $admin->id && $message->isRead===0){
                $notReadMessages[]=$message;
            }
        }

        return $notReadMessages;
    }

    public function takeNoReadMessagesToAdmin(){

        $roleAdmin = Role::where('idRole', 3)->first();
        $admin = $roleAdmin->users()->first();
        $messages = $this->hasMany(Message::class, 'idSender', 'id')->get();
        $notReadMessages = [];
        foreach($messages as $message){
            if($message->idTaker === $admin->id && $message->isRead===0){
                $notReadMessages[]=$message;
            }
        }

        return $notReadMessages;
    }

    public function adminTakeNoReadMessages(){

        $roleAdmin = Role::where('idRole', 3)->first();
        $admin = $roleAdmin->users()->first();
        $notReadMessages = [];
        if($this->id === $admin->id){
            $messages = $this->hasMany(Message::class, 'idTaker', 'id')->get();
            foreach($messages as $message){
                if($message->idTaker === $admin->id && $message->isRead===0){
                    $notReadMessages[]=$message;
                }
            }
        }
        else{
            return $notReadMessages;
        }


        return $notReadMessages;
    }
}
