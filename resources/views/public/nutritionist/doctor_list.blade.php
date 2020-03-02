@extends('layouts.app')

<meta name="_token" content="{!! csrf_token() !!}">
@section('content')

    <div class="container">
        @include("partial.error")
        @include("partial.success")

        <div class="main-title">
            Специалисты
        </div>

        <div class="doctor-list">
            @foreach($doctors as $doctor)
                <div class="doctor">
                    <div class="doctor-info">

                            <div class="doctor-photo">
                                @if (isset($doctor->user->userPhoto))
                                    <img src={{asset('storage/'.$doctor->user->userPhoto)}}>
                                @else
                                    <img src={{asset('storage/uploads/user-default.png')}}>
                                @endif
                            </div>

                        <div class="doctor-ratio">
                            <div class="doctor-like">
                                <img src={{asset('storage/uploads/like.png')}}>
                                {{$doctor->likes}}
                            </div>
                            <div class="doctor-dislike">
                                <img src={{asset('storage/uploads/dislike.png')}}>
                                {{$doctor->dislikes}}
                            </div>
                        </div>
                    </div>

                    <div class="doctor-description">
                        <p class="recipe-title">{{$doctor->doctorInfo->surname}} {{$doctor->doctorInfo->name}} {{$doctor->doctorInfo->second_name}}</p>
                        <p class="recipe-category">Рейтинг: {{$doctor->doctorInfo->rating}}</p>
                        <label>Образование:</label>
                        @foreach($doctor->specialties as $specialty)
                            <p class="info">{{$specialty->titleSpecialty}}</p>
                        @endforeach
                        <label>Услуги:</label>
                        @foreach($doctor->services as $service)
                            <p class="info">{{$service->titleService}} - {{$service->pivot->sum}} грн.</p>
                        @endforeach

                        <label>О себе:</label>
                        <p class="info">{{Str::limit($doctor->doctorInfo->description, 90)}}</p>
                        <a href="#">Подробнее...</a>
                    </div>

                </div>
            @endforeach
        </div>
        <div class="add-recipe-button">
            <input id="showMore"  value="Больше рецептов" class="btn btn-success">
        </div>
    </div>

@endsection
