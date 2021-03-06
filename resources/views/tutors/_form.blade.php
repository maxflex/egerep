@include('modules.svgmap')
@include('modules.gmap')
@include('tutors._modals')

<sms number='sms_number'></sms>
<input name="file" type="file" id="fileupload2" data-url="upload/tutorfile" style='display: none'>

<div class="row">
    <div class="col-sm-1" style="width: 157px">
        {{-- <div class="form-group img-container">
             <img id='image' ng-src="img/photo/@{{ tutor.has_photo ? tutor.photo : 'no-profile-img.gif' }}">
        </div> --}}
        <div class="form-group">
            <div class="tutor-img" ng-class="{'border-transparent': tutor.has_photo_cropped}" ng-click="showPhotoEditor()">
                <div>
                    изменить фото
                </div>
                <span class="btn-file">
                    {{-- <input name="tutor_photo" type="file" id="fileupload" data-url="upload/tutor/" accept="image/jpg"> --}}
                </span>
                <img ng-src="@{{ tutor.photo_url }}?ver=@{{ picture_version }}">
            </div>
        </div>
        <div class="form-group">
            <div class="input-group custom tiny">
              <span class="input-group-addon">ТБ –</span>
              <input type="text" class="form-control digits-only" ng-model="tutor.tb">
            </div>
        </div>
        <div class="form-group">
            <div class="input-group custom tiny">
              <span class="input-group-addon">ЛК –</span>
              <input type="text" class="form-control digits-only" ng-model="tutor.lk">
            </div>
        </div>
        <div class="form-group">
            <div class="input-group custom tiny">
              <span class="input-group-addon">ЖС –</span>
              <input type="text" class="form-control digits-only" ng-model="tutor.js">
            </div>
        </div>
        <div class="form-group">
            <div class="tutor-state tutor-state-@{{ tutor.state }}" ng-click="toggleEnum(tutor, 'state', TutorStates, [3, 5], {{ allowed(\Shared\Rights::ER_TUTOR_STATUSES, true) }})">
                @{{ TutorStates[tutor.state] }}
            </div>
        </div>
    </div>
    <div class="col-sm-3" style='width: 19.5%'>
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
                ng-options="gender as label for (gender, label) in Genders" placeholder="пол"></select>
        </div>

        <div class="form-group">
            <input type="text" placeholder="год рождения" maxlength='4'
                class="form-control" ng-model="tutor.birth_year">
            <span class="inside-input" ng-show="tutor.birth_year > 999">– @{{ yearDifference(tutor.birth_year) }}
                <ng-pluralize count="yearDifference(tutor.birth_year)" when="{
                    'one': 'год',
                    'few': 'года',
                    'many': 'лет',
                }"></ng-pluralize>
            </span>
        </div>

        <div class="form-group">
            <input type="text" class="form-control digits-year"  maxlength='4' ng-model="tutor.start_career_year" placeholder="год начала карьеры">
            <span class="inside-input" ng-show="tutor.start_career_year > 999">– стаж @{{ yearDifference(tutor.start_career_year) }}
                <ng-pluralize count="yearDifference(tutor.start_career_year)" when="{
                    'one': 'год',
                    'few': 'года',
                    'many': 'лет',
                }"></ng-pluralize>
            </span>
        </div>

        <div class="form-group">
            <select class="form-control" multiple id='sp-tutor-subjects'
                ng-model="tutor.subjects"
                ng-options="subject_id as subject for (subject_id, subject) in Subjects.three_letters">
            </select>
        </div>

        <div class="form-group">
            <select class="form-control" multiple id='sp-tutor-grades' ng-model='tutor.grades'
                ng-options="grade_id as label for (grade_id, label) in Grades">
            </select>
        </div>
    </div>

    <div class="col-sm-8">
        <div class="col-sm-8">
            <div class="row">
                <div class="col-sm-12">
                    <div class="form-group">
                        <phones entity='tutor' sms-number='sms_number' entity-types='репетитор'></phones>
                    </div>
                    <div class="form-group">
                        <email entity='tutor'></email>
                    </div>
                </div>
            </div>
            <div class='row'>
                <div class='col-sm-12'>
                    <span ng-show='!tutor.file' class='link-like' onclick="$('#fileupload2').click()">загрузить файл</span>
                    <span ng-show='tutor.file'>
                        <a href='/tutor-files/@{{ tutor.file }}' target='_blank'>скачать файл</a>
                        {{-- <a class='pointer' onclick="$('#fileupload2').click()" style='margin: 0 10px'>загрузить новый</a> --}}
                        <a class='link-like red' ng-click='tutor.file = null' style='margin: 0 10px'>удалить</a>
                    </span>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <span class="link-like" ng-click="SvgMap.show(tutor.svg_map)">выезд @{{ tutor.svg_map.length ? 'возможен' : 'невозможен' }}</span>
                    <span ng-show="tutor.svg_map.length">
                        (@{{ tutor.svg_map.length }} <ng-pluralize count="tutor.svg_map.length" when="{
                            'one': 'станция',
                            'few': 'станции',
                            'many': 'станций',
                        }"></ng-pluralize>)
                    </span>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <span class="link-like" ng-click="showMap()">метки</span> (@{{ tutor.markers.length }})
                    <div style="margin-top: 5px">
                        <metro-list markers='tutor.markers'></metro-list>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                <div class="tutor-state tutor-inegecentr-@{{ tutor.in_egecentr }}" ng-click="toggleEnum(tutor, 'in_egecentr', Workplaces)" style='width: 100%'>
                    @{{ Workplaces[tutor.in_egecentr] }}
                </div>
            </div>
            <div class="form-group">
                <select class="form-control" ng-model="tutor.source" ng-options="+(id) as label for (id, label) in TutorSources">
                    {{-- <option ng-repeat="(id, label) in TutorSources" value='@{{ id }}' ng-selected="id == tutor.source">@{{ label }}</option> --}}
                </select>
            </div>
            <div ng-show="tutor.in_egecentr">
                <div class="form-group">
                    <select class="form-control" multiple id='sp-tutor-branches' ng-model='tutor.branches'>
                        <option ng-repeat='(branch_id, branch) in Branches'
                                value='@{{branch_id}}'
                                data-content='@{{ BranchService.getNameWithColor(branch_id) }}'>@{{ branch.full }}</option>
                    </select>
                </div>
                <div class="form-group">
                    <select class="form-control" multiple id='sp-tutor-subjects-ec'
                        ng-model="tutor.subjects_ec"
                        ng-options="subject_id as subject for (subject_id, subject) in Subjects.three_letters">
                    </select>
                </div>
                 <div class="form-group">
                    <div class="input-group custom">
                    <span class="input-group-addon">СО –</span>
                    <input type="text" class="form-control digits-only" ng-model="tutor.so">
                    </div>
                </div>
                <div class="form-group">
                    <div>
                         <md-checkbox ng-model="tutor.auto_publish_disabled">
                           запретить автопубликацию
                         </md-checkbox>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row" style="margin-top: 10px;margin-bottom: 10px">
    <div class="col-sm-12">
        <h4>САМОЕ ВАЖНОЕ</h4>
        <md-input-container class="md-block" style="margin-top: 20px">
            <label>Контакты, места для занятий</label>
            <textarea class="md-input" ng-model="tutor.contacts"></textarea>
        </md-input-container>
        <md-input-container class="md-block" style="margin-top: 20px">
            <label>Стоимость</label>
            <textarea class="md-input" ng-model="tutor.price"></textarea>
        </md-input-container>

        <section>
            <div class="row">
                <div class="col-sm-6">
                    <div class="row">
                        <div class="col-sm-5">
                            <md-input-container class="md-block" style="margin-top: 20px">
                                <label>Опубликованная цена</label>
                                <textarea class="md-input digits-only" ng-model="tutor.public_price"></textarea>
                            </md-input-container>
                        </div>
                        <div class="col-sm-2" style="text-align:right;">
                            <span class="ng-grey" style="position: relative; top: 27px" ng-show='tutor.public_price > 0'>
                                <ng-pluralize count='tutor.public_price' when="{
                                                                             'one': 'рубль',
                                                                             'few': 'рубля',
                                                                             'many': 'рублей'
                                                                        }"></ng-pluralize> за
                            </span>
                        </div>
                        <section ng-show='tutor.public_price > 0'>
                            <div class="col-sm-2">
                                <md-input-container class="md-block">
                                    <textarea class="md-input digits-only" ng-model="tutor.lesson_duration"></textarea>
                                </md-input-container>
                            </div>
                            <div class="col-sm-3">
                                <span class="ng-grey" style="position: relative; top: 27px">
                                    <plural count='tutor.lesson_duration' type='minute' text-only></plural>
                                </span>
                            </div>
                        </section>
                    </div>
                </div>
                <div class="col-sm-6" ng-show='tutor.svg_map.length'>
                    <div class="row">
                        <div class="col-sm-6">
                            <md-input-container class="md-block" style="margin-top: 20px">
                                <label>Минимальная стоимость выезда</label>
                                <textarea class="md-input digits-only" ng-model="tutor.departure_price"></textarea>
                            </md-input-container>
                        </div>
                        <div class="col-sm-6">
                            <span class="ng-grey" style="position: relative; top: 27px" ng-show='tutor.public_price > 0'>
                                <ng-pluralize count='tutor.departure_price' when="{
                                    'one': 'рубль',
                                    'few': 'рубля',
                                    'many': 'рублей'
                                }"></ng-pluralize>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <md-input-container class="md-block" style="margin: 20px 0 0">
                <label>Опубликованное описание <published-field></label>
                <textarea class="md-input" ng-model="tutor.public_desc"></textarea>
            </md-input-container>
        </section>

        <h4>ОБЩИЕ СВЕДЕНИЯ</h4>
        <md-input-container class="md-block" style="margin-top: 20px">
            <label>Образование (вуз, факультет, аспирантура, с годами) <published-field></label>
            <textarea class="md-input" ng-model="tutor.education"></textarea>
        </md-input-container>
        <md-input-container class="md-block" style="margin-top: 20px">
            <label>Степени, разряды, заслуги <published-field></label>
            <textarea class="md-input" ng-model="tutor.achievements"></textarea>
        </md-input-container>
        <md-input-container class="md-block" style="margin-top: 20px">
            <label>Опыт работы преподавателем в учебных заведениях, настоящее место работы <published-field></label>
            <textarea class="md-input" ng-model="tutor.experience"></textarea>
        </md-input-container>
        {{--<md-input-container class="md-block" style="margin-top: 20px">--}}
            {{--<label>Настоящее место работы</label>--}}
            {{--<textarea class="md-input" ng-model="tutor.current_work"></textarea>--}}
        {{--</md-input-container>--}}
        <md-input-container class="md-block" style="margin-top: 20px">
            <label>Опыт работы репетитором <published-field></label>
            <textarea class="md-input" ng-model="tutor.tutoring_experience"></textarea>
        </md-input-container>
        <md-input-container class="md-block" style="margin-top: 20px">
            <label>Категории учеников, предпочтения по уровню и классам</label>
            <textarea class="md-input" ng-model="tutor.students_category"></textarea>
        </md-input-container>
        <md-input-container class="md-block" style="margin-top: 20px">
            <label>Предпочтения по предметам, опыт подготовки к ЕГЭ и ОГЭ <published-field></label>
            <textarea class="md-input" ng-model="tutor.preferences"></textarea>
        </md-input-container>
        <md-input-container class="md-block" style="margin-top: 20px">
            <label>Общее впечатление как о преподавателе и о человеке</label>
            <textarea class="md-input" ng-model="tutor.impression"></textarea>
        </md-input-container>
        <md-input-container class="md-block" style="margin-top: 20px">
            <label>Расписание и загрузка в течение года</label>
            <textarea class="md-input" ng-model="tutor.schedule"></textarea>
        </md-input-container>
        <md-input-container class="md-block" style="margin-top: 20px">
            <label>Готовность работать в ЕГЭ-Центре</label>
            <textarea class="md-input" ng-model="tutor.ready_to_work"></textarea>
        </md-input-container>

        <div ng-show="tutor.in_egecentr" class="egecentrInformation">
            <h4>ИНФОРМАЦИЯ ПО ЕГЭ-ЦЕНТРУ</h4>
            <md-input-container class="md-block" style="margin-top: 20px">
                <label>Описание</label>
                <textarea class="md-input" ng-model="tutor.comment"></textarea>
            </md-input-container>
            <section>
                <div class="row">
                    <div class="col-sm-12">
                        <md-input-container class="md-block" style="margin-top: 20px; margin-bottom: 0">
                            <label>Опубликованное описание на сайте ЕГЭ-Центра <published-field in-ege-centr="1"></label>
                            <textarea class="md-input" ng-model="tutor.description"></textarea>
                        </md-input-container>
                    </div>
                </div>
            </section>

            <md-input-container class="md-block" style="margin-top: 20px">
                <label>Подпись под фото на сайте ЕГЭ-Центра</label>
                <textarea class="md-input" ng-model="tutor.photo_desc"></textarea>
            </md-input-container>
        </div>
        <div class="mb-xl">
            <h4>ПАСПОРТ</h4>
            <div style='display: flex'>
                <md-input-container class="md-block" style="margin-top: 20px; margin-right: 10px">
                    <label>Серия</label>
                    <textarea class="md-input" ng-model="tutor.passport_series"></textarea>
                </md-input-container>
                <md-input-container class="md-block" style="margin-top: 20px; margin-right: 10px">
                    <label>Номер</label>
                    <textarea class="md-input" ng-model="tutor.passport_number"></textarea>
                </md-input-container>
                <md-input-container class="md-block" style="margin-top: 20px; margin-right: 10px">
                    <label>Код подразделения</label>
                    <textarea class="md-input" ng-model="tutor.passport_code"></textarea>
                </md-input-container>
            </div>
            <md-input-container class="md-block" style="margin-top: 20px">
                <label>Выдан</label>
                <textarea class="md-input" ng-model="tutor.passport_issue_place"></textarea>
            </md-input-container>
            <md-input-container class="md-block" style="margin-top: 20px">
                <label>Зарегистрирован по адресу</label>
                <textarea class="md-input" ng-model="tutor.passport_address"></textarea>
            </md-input-container>
        </div>
        <div class="mb-xl">
            <h4>СТАТИСТИКА</h4>
            <div>Количество клиентов: @{{ tutor.clients_count }}</div>
            <div>Группа маржинальности: @{{ tutor.margin }}</div>
            <div>Cредняя оценка: @{{ tutor.statistics.er_review_avg | number : 1 }} (на основе @{{ tutor.statistics.er_review_count }} <ng-pluralize count='tutor.statistics.er_review_count' when="{
                'one': 'оценки',
                'few': 'оценок',
                'many': 'оценок'
            }"></ng-pluralize> от учеников и нашей оценки)</div>
            <a href="reviews/@{{ tutor.id }}">читать все отзывы</a>
        </div>
        <div ng-if="tutor.id">
            <h4>КОММЕНТАРИИ</h4>
            <comments entity-type='tutor' entity-id='{{ isset($id) ? $id : 0 }}' user='{{ $user }}'></comments>
        </div>
        <div ng-if="tutor.id" class="tutor-created">
            профиль создан @{{ formatDateTime(tutor.created_at) }}
        </div>
    </div>
</div>
