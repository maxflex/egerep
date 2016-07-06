<div class="row controls-line" ng-if="selected_request.id">
    <div class="col-sm-12">
        <span
            ng-repeat="list in selected_request.lists"
            ng-class="{'link-like': selected_list !== list}"
            ng-click="setList(list)"
        >список по <sbj ng-repeat='subject_id in list.subjects'>@{{Subjects.dative[subject_id]}}@{{$last ? '' : ' и '}}</sbj></span>
        <span class="link-like link-gray" ng-click="addList()">добавить список</span>
        <div class="teacher-remove-droppable drop-delete">удалить из списка</div>
        <span class="link-like text-danger show-on-hover" ng-show='selected_list' ng-click="removeList()">удалить список</span>
    </div>
</div>

<div class="row">
    <div class="col-sm-12">
        <div class="mbs">
            <table class="table reverse-borders">
                <tbody ui-sortable='sortableOptions' ng-model="selected_list.tutor_ids" ng-if="selected_list">
                    <tr ng-repeat="tutor in getTutorList()" data-id='@{{tutor.id}}'>
                        <td style='width: 25%'>
                            <a href="tutors/@{{ tutor.id }}/edit">@{{ tutor.full_name }}</a>
                        </td>
                        <td style='width: 10%'>
                            @include('modules.subjects-list', ['subjects' => 'tutor.subjects', 'type' => 'three_letters'])
                        </td>
                        <td style='width: 7%'>
                            <plural count='tutor.age' type='age'></plural>
                        </td>
                        <td  style='width: 3%'>
                            @{{ tutor.lk }}
                        </td>
                        <td  style='width: 3%'>
                            @{{ tutor.tb }}
                        </td>
                        <td  style='width: 3%'>
                            @{{ tutor.js }}
                        </td>
                        <td style='width: 16%' ng-init='recommendation = RecommendationService.get(tutor, client.grade)'>
                            <span aria-label='@{{ recommendation.text }}' class='hint--bottom-right cursor-default' ng-class="{
                                'text-success': recommendation.type == 0,
                                'text-warning': recommendation.type == 1,
                                'text-danger': recommendation.type == 2,
                            }">
                                @{{ recommendation.type_text }}
                            </span>
                        </td>
                        <td style='width: 10%'>
                            <plural count='tutor.clients_count' type='client' none-text='клиентов нет' hide-zero></plural>
                        </td>
                        <td style='width: 15%'>
                            <span ng-hide="attachmentExists(tutor.id)"
                            class="link-like link-gray" style="margin-left: 10px"
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
