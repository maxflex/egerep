<div class="modal modal-fullscreen" tabindex="-1" id='change-photo'>
    <div class="modal-dialog" style="width: 80%; height: 90%; margin: 3% auto">
        <div class="modal-content" style="height: 100%">
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-10 image-col-left">
                        <div ng-if='tutor.has_photo_original'>
                            <img src="img/tutors/@{{ tutor.id + '_original.' + tutor.photo_extension }}?ver=@{{ picture_version }}" id='photo-edit'>
                        </div>
                    </div>
                    <div class="col-sm-2 center image-col-right">
                      <div id="image-preview" ng-show='tutor.has_photo_original'>
                          <div class="form-group img-preview-container">
                              <div class="img-preview"></div>
                          </div>
                      </div>

                      <div class='photo-sizes'>
                          <div ng-show='tutor.photo_original_size'>
                              @{{ formatBytes(tutor.photo_original_size) }}
                          </div>
                          <div ng-show='tutor.photo_cropped_size'>
                              @{{ formatBytes(tutor.photo_cropped_size) }}
                          </div>
                      </div>

                        <div class="form-group">
                            <button class="btn btn-primary full-width">Загрузить
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
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
