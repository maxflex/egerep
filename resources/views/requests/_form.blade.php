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
            <select class="form-control" ng-model="client.grade"
                ng-options="grade as grade + ' класс' for grade in range(9, 11)"></select>
        </div>
    </div>
    <div class="col-sm-3">
        <div class="form-group">
            <div class="input-group">
                <input type="text" class="form-control phone-masked" ng-model="client.phone" placeholder="телефон">
                <div class="input-group-btn">
                    <button class="btn btn-default">
                        <span class="glyphicon glyphicon-earphone no-margin-right"></span>
                    </button>
                    <button class="btn btn-default">
                        <span class="glyphicon glyphicon-envelope no-margin-right"></span>
                    </button>
                    <button class="btn btn-default">
                        <span class="glyphicon glyphicon-plus no-margin-right"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-4"></div>
</div>

<div class="row controls-line">
    <div class="col-sm-12">
        <span>заявка 1</span>
        <a href="#">заявка 2</a>
        <a class="link-gray" href="#">добавить</a>
        <a class="text-danger" href="#">удалить заявку</a>
    </div>
</div>

<div class="row">
    <div class="col-sm-6">
        <textarea class="form-control" rows="6" cols="40" placeholder="комментарий"></textarea>
    </div>
    <div class="col-sm-6">
        <p>
            <b>Заявку создал:</b> system 15.09.15 в 15:06
        </p>
        <p>
            <b>Ответственный:</b> maxflex
        </p>
        <p>
            <b>Статус заявки:</b> <a href="#">не выполнена</a>
        </p>
        <p>
            <b>Отмеченные репетиторы в заявке:</b>
        </p>
    </div>
</div>

<div class="row">
    <div class="col-sm-12">
        <span class="pointer no-margin-right comment-add" style="font-size: 12px">комментировать</span>
    </div>
</div>


<div class="row controls-line">
    <div class="col-sm-12">
        <span>список по обществознанию</span>
        <a href="#">список по русскому языку</a>
        <a class="link-gray" href="#">добавить список</a>
        <a class="text-danger" href="#">удалить список</a>
    </div>
</div>

<div class="row">
    <div class="col-sm-12">
        <p>
            <span class="line-number">961</span>
            <a href="#">Васильева Ольга Владимировна</a>
            <a href="#" class="gray-link" style="margin-left: 10px">начать процесс</a>
        </p>
        <p>
            <span class="line-number">10576</span>
            <a href="#">Тарасюк Ирина Вячеславовна</a>
        </p>
    </div>
</div>

<div class="row controls-line">
    <div class="col-sm-12">
        <span>стыковка с Тарасюк Ирина Вячеславовна</span>
        <a href="#">стыковка с Васильева Ольга Владимировна</a>
        <a class="text-danger" href="#">удалить стыковку</a>
    </div>
</div>

<div class="row">
    <div class="col-sm-3">
        <div class="form-group">
            <input type="text" placeholder="дата стыковки" class="form-control bs-date">
        </div>
        <div class="form-group">
            <select class="form-control">
                <option ng-repeat="grade in [9, 10, 11]">@{{ grade }} класс</option>
            </select>
        </div>
        <div class="form-group">
            <input type="text" placeholder="предметы" class="form-control">
        </div>
    </div>
    <div class="col-sm-3">
        <div class="form-group">
            <textarea style="height: 75px" cols="40" class="form-control"></textarea>
        </div>
        <div class="form-group"><input type="text" class="form-control digits-only" placeholder="прогноз в неделю"></div>
    </div>
    <div class="col-sm-6">
        <p>
            <b>Стыковку создал:</b> root 15.09.16 в 16:15
        </p>
        <p>
            <b>Статус стыковки:</b> новая
        </p>
    </div>
</div>

<div class="row mb">
    <div class="col-sm-12">
        <span class="pointer no-margin-right comment-add" style="font-size: 12px">комментировать</span>
    </div>
</div>

<div class="row">
    <div class="col-sm-12">
        <div class="form-group">
            <b>архивация</b>
        </div>
    </div>
</div>

<div class="row mb">
    <div class="col-sm-3">
        <div class="form-group"><input type="text" class="form-control bs-date" placeholder="дата архивации"></div>
        <div class="form-group"><input type="text" class="form-control digits-only" placeholder="всего занятий не проведено"></div>
    </div>
    <div class="col-sm-3">
        <div class="form-group">
            <textarea style="height: 75px" cols="40" class="form-control"></textarea>
        </div>
    </div>
    <div class="col-sm-6">

    </div>
</div>


<div class="row">
    <div class="col-sm-12">
        <div class="form-group">
            <b>отзыв</b>
        </div>
    </div>
</div>

<div class="row mb">
    <div class="col-sm-3">
        <div class="form-group"><input type="text" class="form-control bs-date" placeholder="дата архивации"></div>
        <div class="form-group"><input type="text" class="form-control digits-only" placeholder="всего занятий не проведено"></div>
    </div>
    <div class="col-sm-3">
        <div class="form-group">
            <textarea style="height: 75px" cols="40" class="form-control"></textarea>
        </div>
    </div>
    <div class="col-sm-6">

    </div>
</div>
