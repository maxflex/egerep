<div ng-show="mode == 'list'" class="row">
    <div class="col-sm-12" style="margin-bottom: 40px" ng-show='intersectingTutors().length'>
        <table class='table table-divlike'>
          <tr ng-repeat="tutor in intersectingTutors() | orderBy:'minutes'">
            @include('tutors.add-to-list._tutor_list')
          </tr>
        </table>
    </div>
    <div class="col-sm-12">
      <table class='table table-divlike'>
        <tr ng-repeat="tutor in notIntersectingTutors() | orderBy:'minutes'">
          @include('tutors.add-to-list._tutor_list')
        </tr>
      </table>
    </div>
</div>
