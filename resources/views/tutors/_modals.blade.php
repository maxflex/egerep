<div class="modal modal-fullscreen" tabindex="-1" id='change-photo'>
    <div class="modal-dialog" style="width: 80%; height: 90%; margin: 3% auto">
        <div class="modal-content" style="height: 100%">
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-9" style="height: 694px">
                        <div ng-if='tutor.has_photo_original'>
                            <img src="img/tutors/@{{ tutor.id + '_original.' + tutor.photo_extension }}?ver=@{{ picture_version }}" id='photo-edit'>
                        </div>
                        {{-- <img src="img/tutors/22_original.jpg" id='photo-edit'> --}}
                    </div>
                    <div class="col-sm-3 center">
                        <div class="form-group">
                            <button class="btn btn-primary full-width">Загрузить новое
                                <span class="btn-file">
                                    <input name="photo" type="file" id="fileupload" data-url="upload/tutor">
                                </span>
                            </button>
                        </div>
                        <div class="form-group">
                            <button class="btn btn-primary full-width" ng-click='saveCropped()' ng-disabled='!tutor.has_photo_original'>Сохранить</button>
                        </div>
                        <div class="form-group">
                            <button class="btn btn-danger full-width" ng-click='deletePhoto()' ng-disabled='!tutor.has_photo_original'>Удалить</button>
                        </div>
                        <div id="image-preview" ng-show='tutor.has_photo_original'>
                            <div class="form-group img-preview-container">
                                <div class="img-preview"></div>
                            </div>
                            <div class="form-group">
                                <div class="input-group custom tiny">
                                  <span class="input-group-addon">ТБ –</span>
                                  <input type="text" class="form-control digits-only" ng-model="tutor.tb" readonly>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="input-group custom tiny">
                                  <span class="input-group-addon">ЛК –</span>
                                  <input type="text" class="form-control digits-only" ng-model="tutor.lk" readonly>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="input-group custom tiny">
                                  <span class="input-group-addon">ЖС –</span>
                                  <input type="text" class="form-control digits-only" ng-model="tutor.js" readonly>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="tutor-state tutor-state-@{{ tutor.state }}">
                                    @{{ TutorStates[tutor.state] }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
