<pagination style="margin-top: 30px"
    ng-hide='data.last_page <= 1'
    ng-model="current_page"
    ng-change="pageChanged()"
    total-items="data.total"
    max-size="10"
    items-per-page="data.per_page"
    first-text="«"
    last-text="»"
    previous-text="«"
    next-text="»"
></pagination>
