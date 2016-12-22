{{-- ПЕРЕМЕЩЕНИЕ ЗАЯВКИ --}}
<div id="merge-tutor" class="modal" role="dialog" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Склеить преподавателя</h4>
      </div>
      <div class="modal-body">
          <input placeholder='номер преподавателя' type="text" ng-model='new_tutor_id' class="form-control digits-only">
      </div>
      <div class="modal-footer center">
        <button type="button" class="btn btn-primary" ng-click="mergeTutorGo()"
            ng-disabled="!new_tutor_id">Склеить</button>
      </div>
    </div>
  </div>
</div>


<div class="modal modal-fullscreen" tabindex="-1" id='change-photo'>
    <div class="modal-dialog" style="width: 80%; height: 90%; margin: 3% auto">
        <div class="modal-content" style="height: 100%">
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-10 image-col-left">
                        <div class="div-loading" ng-show='!cropper_built'>
                            <span>загрузка...</span>
                        </div>
                        <div ng-show='tutor.has_photo_original' style="height: 100%">
                            <img ng-src="img/tutors/@{{ tutor.id + '_original.' + tutor.photo_extension }}?ver=@{{ picture_version }}" id='photo-edit'>
                        </div>
                    </div>
                    <div class="col-sm-2 center image-col-right">
                      <div id="image-preview" ng-show='tutor.has_photo_original'>
                          <div class="form-group img-preview-container">
                              <div class="img-preview"></div>
                          </div>
                      </div>

                      <div class='photo-sizes'>
                          <div ng-show='quality'>
                              @{{ quality }}%
                          </div>
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
