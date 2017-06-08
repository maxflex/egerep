{{-- ПЕРЕМЕЩЕНИЕ ЗАЯВКИ --}}
<div id="log-graph" class="modal" role="dialog" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      {{-- <div class="modal-header">
        <h4 class="modal-title">График</h4>
      </div> --}}
      <div class="modal-body" style='box-sizing: border-box'>
          <div class="frontend-loading animate-fadeIn" ng-show='graph_loading'>
                <span>загрузка...</span>
            </div>
            <div class='graph-wrapper'>
                <div class='canvas-wrapper' ng-class="{'zero-opacity': graph_loading}" ng-style="{'width': width}">
                    <canvas id='graph'></canvas>
                </div>
            </div>
      </div>
    </div>
  </div>
</div>