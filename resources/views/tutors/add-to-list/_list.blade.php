<div ng-show="mode == 'list'" class="row">
    <div class="col-sm-12" ng-show='intersectingTutors().length'>
        <h6 class='bold' style='margin: 30px 0'>РЕПЕТИТОРЫ, КОТОРЫЕ МОГУТ ВЫЕЗЖАТЬ К ЭТОМУ КЛИЕНТУ</h6>
        <table class='table reverse-borders' style='margin-bottom: 0'>
          <tr ng-repeat="tutor in sortedIntersectingTutors()">
            @include('tutors.add-to-list._tutor_list')
          </tr>
        </table>
    </div>
    <div class="col-sm-12" ng-show='notIntersectingTutors().length'>
    <h6 class='bold' style='margin: 30px 0'>
        <span ng-show="search.destination == 'r_k'">РЕПЕТИТОРЫ, КОТОРЫЕ ВЫЕЗЖАЮТ, НО ЭТОТ КЛИЕНТ МОЖЕТ ОКАЗАТЬСЯ ДЛЯ НИХ СЛИШКОМ ДАЛЕКО</span>
        <span ng-show="search.destination == 'k_r'">РЕПЕТИТОРЫ, У КОТОРЫХ ВОЗМОЖНО ЗАНЯТИЕ НА ИХ ТЕРРИТОРИИ</span>
    </h6>
      <table class='table reverse-borders' style='margin-bottom: 0'>
        <tr ng-repeat="tutor in notIntersectingTutors() | orderBy:'minutes'">
          @include('tutors.add-to-list._tutor_list')
        </tr>
      </table>
    </div>
</div>
