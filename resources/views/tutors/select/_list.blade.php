<div ng-show="mode == 'list'" class="row">
    <div class="col-sm-12">
        <table class='table reverse-borders' style='margin-bottom: 0'>
          <tr ng-repeat="tutor in tutors">
            @include('tutors.select._tutor_list')
          </tr>
        </table>
    </div>
</div>
