<div class="row controls-line" ng-if="selected_request.id">
    <div class="col-sm-12">
        <span
            ng-repeat="list in selected_request.lists"
            ng-class="{'link-like': selected_list !== list}"
            ng-click="setList(list)"
        >список по <sbj ng-repeat='subject_id in list.subjects'>@{{Subjects.dative[subject_id]}}@{{$last ? '' : ' и '}}</sbj></span>
        <span class="link-like link-gray" ng-click="addList()">добавить</span>
        <div class="teacher-remove-droppable drop-delete" ng-show='is_dragging_teacher'>удалить репетитора из списка</div>
        @if($user->allowed(\Shared\Rights::ER_DELETE_LISTS))
            <div class='controls-right'>
                <span ng-show='selected_list && !selected_list.attachments.length' ng-click="removeList()">удалить</span>
            </div>
        @endif
    </div>
</div>

<div class="row">
    <div class="col-sm-12">
        <div class="mbs">
            <table class="table reverse-borders">
                <tbody ui-sortable='sortableOptions' ng-model="selected_list.tutor_ids" ng-if="selected_list">
                    <tr ng-repeat="tutor in getTutorList() track by $index" data-id='@{{tutor.id}}'>
                        <td style='width: 20%'>
                            <a href="tutors/@{{ tutor.id }}/edit">@{{ tutor.full_name }}</a>
                        </td>
                        <td style='width: 17%'>
                            @{{ tutor.public_price }} руб.
                            <span ng-show='tutor.departure_possible'>
                                +
                                <span ng-show='tutor.departure_price'>выезд от @{{ tutor.departure_price }} руб.</span>
                                <span ng-show='!tutor.departure_price'>бесплатный выезд</span>
                            </span>
                            <span ng-show='!tutor.departure_possible'>(выезд невозможен)</span>
                        </td>
                        <td style='width: 8%'>
                            @include('modules.subjects-list', ['subjects' => 'tutor.subjects', 'type' => 'three_letters'])
                        </td>
                        <td style='width: 8%'>
                            <plural count='tutor.age' type='age'></plural>
                        </td>
                        <td  style='width: 2%'>
                            @{{ tutor.review_avg | number:1 }}
                        </td>
                        <td style='width: 12%'>
                            <plural count='tutor.clients_count' type='client' none-text='клиентов нет' hide-zero></plural>
                            @{{ tutor.margin }}
                        </td>
                        <td style='width: 15%' data-init="@{{ recommendation = RecommendationService.get(tutor, getRealGrade()) }}">
                            <span aria-label='@{{ recommendation.text }}' class='hint--bottom-right cursor-default' ng-class="{
                                'text-success': recommendation.type == 0,
                                'text-warning': recommendation.type == 1,
                                'text-danger': recommendation.type == 2,
                            }">
                                @{{ recommendation.type_text }}
                            </span>
                        </td>
                        <td style='width: 10%'>
                            <span ng-hide="attachmentExists(tutor.id)"
                            class="link-like link-gray"
                            ng-click="newAttachment(tutor.id)">создать стыковку</span>
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class='mbs' >


            </div>
        </div>
        <p ng-show='selected_list'>
            <a class="link-gray" href='tutors/add/@{{ selected_list.id }}' style='margin-right: 15px'>добавить репетитора</a>
            <span ng-class="{'link-like': !list_map}" ng-click='listMap()'>посмотреть список на карте</a>
        </p>
    </div>
</div>

@include('clients.form._gmap')
