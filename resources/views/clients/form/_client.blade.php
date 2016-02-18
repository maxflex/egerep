<div class="row mb">
    <div class="col-sm-3">
        <div class="form-group">
            <textarea name="name" rows="4" cols="40" class="form-control" placeholder="адрес" ng-model="client.address"></textarea>
        </div>
        <div class="form-group">
            <div class="form-gorup">
                <input type="text" class="form-control" placeholder="имя ученика" ng-model="client.name">
            </div>
        </div>
        <div class="form-group">
            <select class="form-control" multiple id='sp-client-grades' ng-model='client.grades'
                ng-options="grade_id as label for (grade_id, label) in Grades">
            </select>
        </div>
    </div>
    <div class="col-sm-3">
        <div class="form-group">
            <div ng-if='client !== undefined'>
                <phones entity="client"></phones>
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="form-group">
            <span class="link-like" ng-click="showMap()">метки</span> (@{{ client.markers.length }})
        </div>
    </div>
</div>
