@include('modules.svgmap')
@include('modules.gmap')

<div class="row">
    <div class="col-sm-12">
    </div>
</div>
<div class="row">
    <div class="col-sm-1" style="width: 157px">
        <div class="form-group">
            <div class="tutor-img" ng-class="{'border-transparent': tutor.has_photo}">
                <div>
                    загрузить фото
                </div>
                <span class="btn-file">
                    {{-- <input name="tutor_photo" type="file" id="fileupload" data-url="upload/tutor/" accept="image/jpg"> --}}
                </span>
                <img src="img/tutors/@{{ tutor.has_photo ? tutor.id + '_2x.jpg' : 'no-profile-img.gif' }}">
            </div>
        </div>
        <div class="form-group">
            <div class="input-group custom">
              <span class="input-group-addon">ТБ –</span>
              <input type="text" class="form-control digits-only" ng-model="tutor.tb">
            </div>
        </div>
        <div class="form-group">
            <div class="input-group custom">
              <span class="input-group-addon">ЛК –</span>
              <input type="text" class="form-control digits-only" ng-model="tutor.lk">
            </div>
        </div>
        <div class="form-group">
            <div class="input-group custom">
              <span class="input-group-addon">ЖС –</span>
              <input type="text" class="form-control digits-only" ng-model="tutor.js">
            </div>
        </div>
        <div class="form-group">
            <div class="tutor-approved" ng-class="{'not-approved': !tutor.approved}" ng-click="tutor.approved = !tutor.approved">
                @{{ tutor.approved ? 'одобрено' : 'не одобрено' }}
            </div>
        </div>
    </div>
    <div class="col-sm-3">
        <div class="form-group">
            <input type="text" class="form-control" ng-model="tutor.last_name" placeholder="фамилия">
        </div>

        <div class="form-group">
            <input type="text" class="form-control" ng-model="tutor.first_name" placeholder="имя">
        </div>

        <div class="form-group">
            <input type="text" class="form-control" ng-model="tutor.middle_name" placeholder="отчество">
        </div>

        <div class="form-group">
            <select class="form-control"
                ng-model="tutor.gender"
                ng-options="gender as label for (gender, label) in genders" placeholder="пол"></select>
        </div>

        <div class="form-group">
            <input type="text" class="form-control digits-year" ng-model="tutor.birth_year" placeholder="год рождения">
            <span class="inside-input" ng-show="tutor.birth_year > 999">– возраст @{{ yearDifference(tutor.birth_year) }}
                <ng-pluralize count="yearDifference(tutor.birth_year)" when="{
                    'one': 'год',
                    'few': 'года',
                    'many': 'лет',
                }"></ng-pluralize>
            </span>
        </div>

        <div class="form-group">
            <input type="text" class="form-control digits-year" ng-model="tutor.start_career_year" placeholder="начало карьеры">
            <span class="inside-input" ng-show="tutor.start_career_year > 999">– педагогический стаж @{{ yearDifference(tutor.start_career_year) }}
                <ng-pluralize count="yearDifference(tutor.start_career_year)" when="{
                    'one': 'год',
                    'few': 'года',
                    'many': 'лет',
                }"></ng-pluralize>
            </span>
        </div>

        {{-- <div layout-gt-sm="row">
          <md-input-container class="md-block flex-gt-sm">
            <label>Имя</label>
            <input ng-model="tutor.first_name">
          </md-input-container>

          <md-input-container class="md-block flex-gt-sm">
            <label>Фамилия</label>
            <input ng-model="tutor.last_name">
          </md-input-container>
        </div> --}}

    </div>

    <div class="col-sm-3">
        <div class="form-group">
            <div class="input-group">
                <input type="text" class="form-control phone-masked" ng-model="tutor.phone" placeholder="телефон">
                <div class="input-group-btn">
                    <button class="btn btn-default">
                        <span class="glyphicon glyphicon-earphone no-margin-right"></span>
                    </button>
                    <button class="btn btn-default">
                        <span class="glyphicon glyphicon-envelope no-margin-right"></span>
                    </button>
                    <button class="btn btn-default">
                        <span class="glyphicon glyphicon-plus no-margin-right"></span>
                    </button>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="input-group">
                <input type="text" class="form-control" ng-model="tutor.email" placeholder="email">
                <div class="input-group-btn">
                    <button class="btn btn-default">
                        <span class="glyphicon glyphicon-envelope no-margin-right"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-3">
        <p>
            <span class="link-like" ng-click="SvgMap.show(tutor.svg_map)">выезд @{{ tutor.svg_map.length ? 'возможен' : 'невозможен' }}</span>
            <span ng-show="tutor.svg_map.length">
                (@{{ tutor.svg_map.length }} <ng-pluralize count="tutor.svg_map.length" when="{
                    'one': 'станция',
                    'few': 'станции',
                    'many': 'станций',
                }"></ng-pluralize>)
            </span>
        </p>
        <p>
            <span class="link-like" ng-click="showMap()">метки</span> (@{{ tutor.markers.length }})
        </p>
        <div>
            {{-- @{{ tutor.svg_map }} --}}
        </div>
    </div>
</div>

<div class="row" style="margin-top: 10px">
    <div class="col-sm-12">
        <h4>САМОЕ ВАЖНОЕ</h4>
        <md-input-container class="md-block" style="margin-top: 30px">
            <label>Контакты, места для занятий</label>
            <textarea class="md-input" ng-model="tutor.contacts"></textarea>
        </md-input-container>
        <md-input-container class="md-block" style="margin-top: 20px">
            <label>Стоимость</label>
            <textarea class="md-input" ng-model="tutor.price"></textarea>
        </md-input-container>

        <h4>ОБЩИЕ СВЕДЕНИЯ</h4>
        <md-input-container class="md-block" style="margin-top: 30px">
            <label>Образование (вуз, факультет, аспирантура, с годами)</label>
            <textarea class="md-input" ng-model="tutor.education"></textarea>
        </md-input-container>
        <md-input-container class="md-block" style="margin-top: 20px">
            <label>Степени, разряды, заслуги</label>
            <textarea class="md-input" ng-model="tutor.achievements"></textarea>
        </md-input-container>
        <md-input-container class="md-block" style="margin-top: 20px">
            <label>Предпочтения по предметам, опыт подготовки к ЕГЭ и ОГЭ</label>
            <textarea class="md-input" ng-model="tutor.preferences"></textarea>
        </md-input-container>
        <md-input-container class="md-block" style="margin-top: 20px">
            <label>Опыт работы преподавателем в учебных заведениях</label>
            <textarea class="md-input" ng-model="tutor.experience"></textarea>
        </md-input-container>
        <md-input-container class="md-block" style="margin-top: 20px">
            <label>Настоящее место работы</label>
            <textarea class="md-input" ng-model="tutor.current_work"></textarea>
        </md-input-container>
        <md-input-container class="md-block" style="margin-top: 20px">
            <label>Опыт работы репетитором</label>
            <textarea class="md-input" ng-model="tutor.tutoring_experience"></textarea>
        </md-input-container>
        <md-input-container class="md-block" style="margin-top: 20px">
            <label>Категории учеников</label>
            <textarea class="md-input" ng-model="tutor.students_category"></textarea>
        </md-input-container>
        <md-input-container class="md-block" style="margin-top: 20px">
            <label>Общее впечатление как о преподавателе и о человеке</label>
            <textarea class="md-input" ng-model="tutor.impression"></textarea>
        </md-input-container>
        <md-input-container class="md-block" style="margin-top: 20px">
            <label>Расписание</label>
            <textarea class="md-input" ng-model="tutor.schedule"></textarea>
        </md-input-container>

        <h4>ИНФОРМАЦИЯ НА САЙТЕ</h4>
        <md-input-container class="md-block" style="margin-top: 30px">
            <label>Опубликованное описание</label>
            <textarea class="md-input" ng-model="tutor.public_desc"></textarea>
        </md-input-container>
        <md-input-container class="md-block" style="margin-top: 20px">
            <label>Опубликованная цена</label>
            <textarea class="md-input" ng-model="tutor.public_price"></textarea>
        </md-input-container>

        <h4>КОММЕНТАРИИ</h4>
        <comments entity-type='tutor' entity-id='{{ $id }}' user='{{ $user }}'></comments>
    </div>
</div>
