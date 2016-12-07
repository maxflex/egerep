<!-- форма поиска -->
<div class="modal fade" id="searchModal" tabindex="-1" ng-controller="SearchCtrl">
    <div class="modal-dialog">
        <div class="modal-content">
            <input type="text" ng-model="query" ng-keyup="key($event)" ng-keydown="stoper($event)" placeholder="искать" id="searchQueryInput">
            <div id="searchResult">
                <div class="notFound" v-bind:class="{hide: (results > 0 || results < 0)}">cовпадений нет</div>

                <div v-for="(index, row) in lists" class="resultRow" v-bind:class="{ active: ((index+1) ==  active), hide: (results == 0)}">
                    <div v-if="row.type == 'clients'">
                        <a href="#">@{{row.name}}</a>  - ученик
                    </div>
                    <div v-else>
                        <a href="#">@{{row.last_name}} @{{row.first_name}} @{{row.middle_name}}</a>  - репетитор
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
<!-- конец формы поиска -->