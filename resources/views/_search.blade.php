<!-- форма поиска -->
<div class="modal" id="searchModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <input type="text" placeholder="искать" id="searchQueryInput" v-on:keyup="keyup" v-on:keydown.up.prevent v-model="query">
            <!--<input type="text" ng-model="query" ng-keyup="key($event)" ng-keydown="stoper($event)" placeholder="искать" id="searchQueryInput">-->
            <div id="searchResult">
                <div class="searchResultWraper" v-if="query!='' && !loading && results == 0">
                    <div class="notFound" v-if="!error">cовпадений нет</div>
                </div>


                <div v-if="results > 0" v-for="(index, row) in lists" class="resultRow" v-bind:class="{ active: ((index+1) ==  active)}">
                    <div v-if="row.type == 'clients'">
                        <a :href="row.link" v-html="'клиент ' + row.id" target="_blank">клиент @{{ row.id }}</a>
                    </div>
                    <div v-else>
                        <a :href="row.link" target="_blank" v-html="'репетитор ' + row.last_name + ' ' + row.first_name + ' ' + row.middle_name">репетитор
                            <span v-if="row.first_name != ''">@{{ row.last_name + ' ' + row.first_name + ' ' + row.middle_name }}</span>
                            <span v-else>имя не указано</span>
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
<!-- конец формы поиска -->
