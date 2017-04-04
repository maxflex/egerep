(function() {
  var indexOf = [].indexOf || function(item) { for (var i = 0, l = this.length; i < l; i++) { if (i in this && this[i] === item) return i; } return -1; };

  angular.module("Egerep", ['ngSanitize', 'ngResource', 'ngMaterial', 'ngMap', 'ngAnimate', 'ui.sortable', 'ui.bootstrap', 'angular-ladda', 'mwl.calendar', 'svgmap']).config([
    '$compileProvider', function($compileProvider) {
      return $compileProvider.aHrefSanitizationWhitelist(/^\s*(https?|ftp|mailto|chrome-extension|sip):/);
    }
  ]).filter('cut', function() {
    return function(value, wordwise, max, nothing, tail) {
      var lastspace;
      if (nothing == null) {
        nothing = '';
      }
      if (tail == null) {
        tail = '…';
      }
      if (!value || value === '') {
        return nothing;
      }
      max = parseInt(max, 10);
      if (!max) {
        return value;
      }
      if (value.length <= max) {
        return value;
      }
      value = value.substr(0, max);
      if (wordwise) {
        lastspace = value.lastIndexOf(' ');
        if (lastspace !== -1) {
          if (value.charAt(lastspace - 1) === '.' || value.charAt(lastspace - 1) === ',') {
            lastspace = lastspace - 1;
          }
          value = value.substr(0, lastspace);
        }
      }
      return value + tail;
    };
  }).filter('hideZero', function() {
    return function(item) {
      if (item > 0) {
        return item;
      } else {
        return null;
      }
    };
  }).run(function($rootScope, $q, PusherService) {
    PusherService.bind('IncomingRequest', function(data) {
      var animate_speed, request_count, request_counter;
      request_count = $('#request-count');
      request_counter = $('#request-counter');
      animate_speed = 7000;
      request_counter.removeClass('text-success').removeClass('text-danger').css('opacity', 1);
      if (data["delete"]) {
        request_count.text(parseInt(request_count.text()) - 1);
        request_count.animate({
          'background-color': '#158E51'
        }, animate_speed / 2).animate({
          'background-color': '#777'
        }, animate_speed / 2);
        return request_counter.text('-1').addClass('text-success').animate({
          opacity: 0
        }, animate_speed);
      } else {
        request_count.text(parseInt(request_count.text()) + 1);
        request_count.animate({
          'background-color': '#A94442'
        }, animate_speed / 2).animate({
          'background-color': '#777'
        }, animate_speed / 2);
        return request_counter.text('+1').addClass('text-danger').animate({
          opacity: 0
        }, animate_speed);
      }
    });
    PusherService.bind('AttachmentCountChanged', function(data) {
      var animate_speed, attachment_count, attachment_counter;
      attachment_count = $('#attachment-count');
      attachment_counter = $('#attachment-counter');
      animate_speed = 7000;
      attachment_counter.removeClass('text-success').removeClass('text-danger').css('opacity', 1);
      if (data["delete"]) {
        attachment_count.text(parseInt(attachment_count.text()) - 1);
        attachment_count.animate({
          'background-color': '#A94442'
        }, animate_speed / 2).animate({
          'background-color': '#777'
        }, animate_speed / 2);
        return attachment_counter.text('-1').addClass('text-danger').animate({
          opacity: 0
        }, animate_speed);
      } else {
        attachment_count.text(parseInt(attachment_count.text()) + 1);
        attachment_count.animate({
          'background-color': '#158E51'
        }, animate_speed / 2).animate({
          'background-color': '#777'
        }, animate_speed / 2);
        return attachment_counter.text('+1').addClass('text-success').animate({
          opacity: 0
        }, animate_speed);
      }
    });
    $rootScope.dataLoaded = $q.defer();
    $rootScope.frontendStop = function(rebind_masks) {
      if (rebind_masks == null) {
        rebind_masks = true;
      }
      $rootScope.frontend_loading = false;
      $rootScope.dataLoaded.resolve(true);
      if (rebind_masks) {
        return rebindMasks();
      }
    };
    $rootScope.range = function(min, max, step) {
      var i, input;
      step = step || 1;
      input = [];
      i = min;
      while (i <= max) {
        input.push(i);
        i += step;
      }
      return input;
    };
    $rootScope.toggleEnum = function(ngModel, status, ngEnum, skip_values, allowed, recursion) {
      var ref, status_id, statuses;
      if (skip_values == null) {
        skip_values = [];
      }
      if (allowed == null) {
        allowed = [];
      }
      if (recursion == null) {
        recursion = false;
      }
      if (!recursion && ((ref = parseInt(ngModel[status]), indexOf.call(skip_values, ref) >= 0) || (isNaN(parseInt(ngModel[status])) && skip_values.indexOf(ngModel[status]) !== -1)) && !allowed) {
        return;
      }
      statuses = Object.keys(ngEnum);
      status_id = statuses.indexOf(ngModel[status].toString());
      status_id++;
      if (status_id > (statuses.length - 1)) {
        status_id = 0;
      }
      ngModel[status] = statuses[status_id];
      if (((isNaN(parseInt(ngModel[status])) && skip_values.indexOf(ngModel[status]) !== -1) || indexOf.call(skip_values, status_id) >= 0) && !allowed) {
        return $rootScope.toggleEnum(ngModel, status, ngEnum, skip_values, allowed, true);
      }
    };
    $rootScope.toggleEnumServer = function(ngModel, status, ngEnum, Resource, skip_values, restricted_fields, freeze_restricted) {
      var ref, status_id, statuses, update_data, value;
      if (skip_values == null) {
        skip_values = [];
      }
      if (restricted_fields == null) {
        restricted_fields = [];
      }
      if (freeze_restricted == null) {
        freeze_restricted = false;
      }
      if ((ref = ngModel[status], indexOf.call(restricted_fields, ref) >= 0) && freeze_restricted) {
        return;
      }
      statuses = Object.keys(ngEnum);
      status_id = statuses.indexOf(ngModel[status].toString());
      while (true) {
        status_id++;
        if (status_id > (statuses.length - 1)) {
          status_id = 0;
        }
        value = isNaN(parseInt(ngModel[status])) ? statuses[status_id] : status_id;
        if (!(indexOf.call(skip_values, value) >= 0 || indexOf.call(restricted_fields, value) >= 0)) {
          break;
        }
      }
      update_data = {
        id: ngModel.id
      };
      update_data[status] = value;
      return Resource.update(update_data, function() {
        return ngModel[status] = value;
      });
    };
    $rootScope.formatDateTime = function(date) {
      return moment(date).format("DD.MM.YY в HH:mm");
    };
    $rootScope.formatDate = function(date, full_year) {
      if (full_year == null) {
        full_year = false;
      }
      if (!date) {
        return '';
      }
      return moment(date).format("DD.MM.YY" + (full_year ? "YY" : ""));
    };
    $rootScope.shortenYear = function(date) {
      if (!date) {
        return '';
      }
      return date.replace(/(\d{2}[\-\.]{1}\d{2}[\-\.]{1})\d{2}(\d{2})/, '$1$2');
    };
    $rootScope.formatTimestamp = function(timestamp, full_year) {
      if (full_year == null) {
        full_year = false;
      }
      if (typeof timestamp === 'string') {
        timestamp = +(timestamp + '000');
      }
      if (!timestamp) {
        return '';
      }
      return moment(timestamp).format("DD.MM.YY в HH:mm" + (full_year ? "YY" : ""));
    };
    $rootScope.dialog = function(id) {
      $("#" + id).modal('show');
    };
    $rootScope.closeDialog = function(id) {
      $("#" + id).modal('hide');
    };
    $rootScope.ajaxStart = function() {
      ajaxStart();
      return $rootScope.saving = true;
    };
    $rootScope.ajaxEnd = function() {
      ajaxEnd();
      return $rootScope.saving = false;
    };
    $rootScope.findById = function(object, id) {
      return _.findWhere(object, {
        id: parseInt(id)
      });
    };
    $rootScope.total = function(array, prop, prop2) {
      var sum;
      if (prop2 == null) {
        prop2 = false;
      }
      sum = 0;
      $.each(array, function(index, value) {
        var v;
        v = value[prop];
        if (prop2) {
          v = v[prop2];
        }
        return sum += v;
      });
      return sum;
    };
    $rootScope.deny = function(ngModel, prop) {
      return ngModel[prop] = +(!ngModel[prop]);
    };
    return $rootScope.formatBytes = function(bytes) {
      if (bytes < 1024) {
        return bytes + ' Bytes';
      } else if (bytes < 1048576) {
        return (bytes / 1024).toFixed(1) + ' KB';
      } else if (bytes < 1073741824) {
        return (bytes / 1048576).toFixed(1) + ' MB';
      } else {
        return (bytes / 1073741824).toFixed(1) + ' GB';
      }
    };
  });

}).call(this);

(function() {
  angular.module('Egerep').factory('Model', function($resource) {
    return $resource('api/models/:id', {}, {
      update: {
        method: 'PUT'
      }
    });
  }).controller("ModelsIndex", function($scope, $timeout, Model) {
    return $scope.models = Model.query();
  }).controller("ModelsForm", function($scope, $timeout, $interval, Model) {
    return $timeout(function() {
      if ($scope.id > 0) {
        return $scope.model = Model.get({
          id: $scope.id
        });
      }
    });
  });

}).call(this);

(function() {
  var indexOf = [].indexOf || function(item) { for (var i = 0, l = this.length; i < l; i++) { if (i in this && this[i] === item) return i; } return -1; };

  angular.module('Egerep').controller('AccountsHiddenCtrl', function($scope, Grades, Attachment) {
    return bindArguments($scope, arguments);
  }).controller('AccountsCtrl', function($rootScope, $scope, $http, $timeout, Account, PaymentMethods, Archive, Grades, Attachment, AttachmentState, AttachmentStates, Weekdays, PhoneService, AttachmentVisibility, DebtTypes, YesNo, Tutor, ArchiveStates, Checked, PlannedAccount, UserService, TeacherPaymentTypes, Confirmed) {
    var getAccountEndDate, getAccountStartDate, getCalendarStartDate, getCommission, hideValue, moveCursor, renderData, updateClientCount, validatePlannedAccount;
    bindArguments($scope, arguments);
    $scope.current_scope = $scope;
    $scope.current_period = 0;
    $scope.all_displayed = false;
    $scope.updateArchive = function(field, set) {
      var archive, fillable, fillables, j, len;
      fillables = ['id', 'state', 'checked'];
      archive = {};
      for (j = 0, len = fillables.length; j < len; j++) {
        fillable = fillables[j];
        archive[fillable] = $scope.popup_attachment.archive[fillable];
      }
      $rootScope.toggleEnum(archive, field, set);
      return $scope.Archive.update(archive, function(response) {
        return _.extendOwn($scope.popup_attachment.archive, archive);
      });
    };
    angular.element(document).ready(function() {
      return $scope.loadPage();
    });
    $scope.getIndex = function(a, b) {
      var index;
      if (a === 0) {
        return b;
      }
      index = 0;
      $.each($scope.tutor.last_accounts, function(i, account) {
        if (i >= a) {
          return;
        }
        return index += $scope.getDates(i).length;
      });
      return index + b;
    };
    $scope.loadPage = function(type) {
      $rootScope.frontend_loading = true;
      return $http.get(("api/accounts/" + $scope.tutor_id) + ($scope.current_period ? "?date_limit=" + $scope.date_limit : "")).success(function(response) {
        renderData(response);
        return $scope.current_period++;
      });
    };
    renderData = function(data) {
      if (data.account === null) {
        $scope.date_limit = $scope.first_attachment_date;
        $scope.all_displayed = true;
      } else {
        if (!$scope.current_period) {
          $scope.tutor = data;
          if ($scope.tutor.planned_account && $scope.tutor.planned_account.id) {
            $scope.tutor.planned_account.user_id += '';
            $scope.tutor.planned_account.payment_method += '';
          }
        } else {
          $scope.tutor.last_accounts.unshift(data.account);
          $scope.date_limit = moment(data.account.date_end).subtract(7, 'days').format('YYYY-MM-DD');
          $scope.left = data.left;
          if (data.accounts_in_week.length) {
            $scope.tutor.last_accounts = data.accounts_in_week.concat($scope.tutor.last_accounts);
          }
        }
      }
      $rootScope.frontend_loading = false;
      $('.accounts-table').stickyTableHeaders('destroy');
      return $timeout(function() {
        $('.accounts-table').stickyTableHeaders();
        return $('.right-table-scroll').scroll(function() {
          return $(window).trigger('resize.stickyTableHeaders');
        });
      });
    };
    getAccountStartDate = function(index) {
      if (index > 0) {
        return moment($scope.tutor.last_accounts[index - 1].date_end).add(1, 'days').toDate();
      } else {
        return new Date($scope.date_limit);
      }
    };
    getAccountEndDate = function(index) {
      if ((index + 1) === $scope.tutor.last_accounts.length) {
        return '';
      } else {
        return moment($scope.tutor.last_accounts[index + 1].date_end).subtract(1, 'days').toDate();
      }
    };
    $scope.getDay = function(date) {
      var day;
      day = moment(date).day() - 1;
      if (day === -1) {
        day = 6;
      }
      return day;
    };
    $scope.accountInfo = function(client) {
      $scope.popup_attachment = null;
      $('#account-info').modal('show');
      $scope.selected_client = client;
      return Attachment.get({
        id: client.attachment_id
      }, function(response) {
        return $scope.popup_attachment = response;
      });
    };
    $scope.changeDateDialog = function(index) {
      $('#date-end-change').datepicker('destroy');
      $('#date-end-change').datepicker({
        language: 'ru',
        autoclose: true,
        orientation: 'bottom auto',
        startDate: getAccountStartDate(index),
        endDate: getAccountEndDate(index)
      });
      $scope.selected_account = $scope.tutor.last_accounts[index];
      $scope.change_date_end = $scope.formatDate($scope.selected_account.date_end, true);
      return $scope.dialog('change-account-date');
    };
    $scope.changeDate = function() {
      $scope.selected_account.date_end = convertDate($scope.change_date_end);
      Account.update({
        id: $scope.selected_account.id,
        date_end: $scope.selected_account.date_end
      });
      return $scope.closeDialog('change-account-date');
    };
    $scope.remove = function(account) {
      return bootbox.confirm('Удалить встречу?', function(result) {
        if (result === true) {
          return Account["delete"]({
            id: account.id
          }, function() {
            return $scope.tutor.last_accounts = removeById($scope.tutor.last_accounts, account.id);
          });
        }
      });
    };
    $scope.save = function() {
      ajaxStart();
      $.each($scope.tutor.last_accounts, function(index, account) {
        return Account.update(account);
      });
      return ajaxEnd();
    };
    $scope.getFakeDates = function() {
      var current_date, dates;
      dates = [];
      current_date = moment().subtract(60, 'days').format('YYYY-MM-DD');
      while (current_date <= moment().format('YYYY-MM-DD')) {
        dates.push(current_date);
        current_date = moment(current_date).add(1, 'days').format('YYYY-MM-DD');
      }
      return dates;
    };
    $scope.getDates = function(index) {
      var current_date, dates;
      dates = [];
      if (!index) {
        current_date = $scope.date_limit;
      } else {
        current_date = moment($scope.tutor.last_accounts[index - 1].date_end).add(1, 'days').format('YYYY-MM-DD');
      }
      while (current_date <= $scope.tutor.last_accounts[index].date_end) {
        dates.push(current_date);
        current_date = moment(current_date).add(1, 'days').format('YYYY-MM-DD');
      }
      return dates;
    };
    getCalendarStartDate = function() {
      var date_end;
      if ($scope.tutor.last_accounts.length > 0) {
        date_end = $scope.tutor.last_accounts[$scope.tutor.last_accounts.length - 1].date_end;
        return moment(date_end).add(1, 'days').toDate();
      } else {
        return new Date($scope.date_limit);
      }
    };
    $scope.addAccountDialog = function() {
      $scope.new_account_date_end = '';
      $('#date-end').datepicker('destroy');
      $('#date-end').datepicker({
        language: 'ru',
        startDate: getCalendarStartDate(),
        autoclose: true,
        orientation: 'bottom auto'
      });
      return $scope.dialog('add-account');
    };
    $scope.addPlannedAccountDialog = function(allowed) {
      var ref;
      if (!allowed && !$scope.tutor.planned_account) {
        return;
      }
      if (!$scope.tutor.planned_account || ((ref = !'is_planned', indexOf.call($scope.tutor.planned_account, ref) >= 0) || !$scope.tutor.planned_account.id)) {
        $scope.tutor.planned_account = {
          is_planned: '0',
          payment_method: '0',
          date: ''
        };
      } else {
        _.extend($scope.tutor.planned_account, {
          is_planned: '1',
          tutor_id: $scope.tutor.id
        });
      }
      $('#pa-date').datepicker('destroy');
      $('#pa-date').datepicker({
        language: 'ru',
        autoclose: true,
        orientation: 'bottom auto'
      });
      $timeout(function() {
        return $scope.refreshSelects();
      });
      $('#add-planned-account').modal('show');
    };
    validatePlannedAccount = function() {
      var valid;
      valid = true;
      if (!(parseInt($scope.tutor.planned_account.is_planned) === 0)) {
        if (!$scope.tutor.planned_account.user_id > 0) {
          $('#pa-user .bootstrap-select').addClass('has-error');
          valid = false;
        } else {
          $('#pa-user .bootstrap-select').removeClass('has-error');
        }
        if (!($scope.tutor.planned_account.date && moment($scope.tutor.planned_account.date, 'DD.MM.YYYY').isValid())) {
          $('#pa-date').addClass('has-error');
          valid = false;
        } else {
          $('#pa-date').removeClass('has-error');
        }
      }
      return valid;
    };
    $scope.addPlannedAccount = function() {
      if (!validatePlannedAccount()) {
        return;
      }
      $scope.tutor.planned_account['tutor_id'] = $scope.tutor.id;
      return PlannedAccount.save($scope.tutor.planned_account, function(response) {
        $scope.tutor.planned_account.id = response.id;
        $('#add-planned-account').modal('hide');
      });
    };
    $scope.updatePlannedAccount = function() {
      if (!validatePlannedAccount()) {
        return;
      }
      if (+$scope.tutor.planned_account.is_planned) {
        PlannedAccount.update({
          id: $scope.tutor.planned_account.id,
          data: $scope.tutor.planned_account
        });
      } else {
        PlannedAccount["delete"]({
          id: $scope.tutor.planned_account.id
        }, function() {
          return $scope.tutor.planned_account = null;
        });
      }
      $('#add-planned-account').modal('hide');
    };
    $scope.refreshSelects = function() {
      return $timeout(function() {
        $('#add-planned-account .selectpicker option').each(function(index, el) {
          $(el).data('subtext', $(el).attr('data-subtext'));
          return $(el).data('content', $(el).attr('data-content'));
        });
        return $('#add-planned-account .selectpicker').selectpicker('refresh');
      }, 100);
    };
    $scope.addAccount = function() {
      return Account.save({
        date_end: convertDate($scope.new_account_date_end),
        tutor_id: $scope.tutor.id
      }, function(new_account) {
        $scope.tutor.last_accounts.push(new_account);
        return $scope.closeDialog('add-account');
      });
    };
    getCommission = function(val) {
      if (val.indexOf('/') !== -1) {
        val = val.split('/')[1];
        if (val) {
          return parseInt(val);
        } else {
          return 0;
        }
      } else {
        return Math.round(parseInt(val) * .25);
      }
    };
    $scope.totalLessons = function(account, client_id) {
      var lessons_count;
      lessons_count = 0;
      $.each($scope.tutor.last_accounts, function(index, account) {
        return lessons_count += $scope.periodLessons(account, client_id);
      });
      return lessons_count || null;
    };
    $scope.periodLessons = function(account, client_id) {
      var lessons_count;
      if (!account.data[client_id]) {
        return null;
      }
      lessons_count = 0;
      $.each(account.data[client_id], function(index, value) {
        if (value) {
          return lessons_count++;
        }
      });
      return lessons_count || null;
    };
    $scope.totalCommission = function(account) {
      var total_commission;
      total_commission = 0;
      $.each(account.data, function(index, account_data) {
        return $.each(account_data, function(index, val) {
          if (val !== '') {
            return total_commission += getCommission(val);
          }
        });
      });
      return total_commission;
    };
    $scope.selectRow = function(date) {
      $('.tr-' + date).addClass('selected');
    };
    $scope.deselectRow = function(date) {
      $('.tr-' + date).removeClass('selected');
    };

    /*
    * Перевести курсор, если элемент существует
     */
    moveCursor = function(x, y, direction) {
      var el;
      switch (direction) {
        case "left":
          x--;
          break;
        case "right":
          x++;
          break;
        case "up":
          y = moment(y).subtract('days', 1).format('YYYY-MM-DD');
          break;
        case "down":
          y = moment(y).add('days', 1).format('YYYY-MM-DD');
      }
      if (x < 0 || !$('#i-' + y + '-' + x).length) {
        return;
      }
      el = $('#i-' + y + '-' + x);
      if (el.length) {
        $scope.caret = 0;
        el.focus();
      } else {
        moveCursor(x, y, direction);
      }
    };
    $scope.caret = 0;
    $scope.periodsCursor = function(y, x, event, account_data, date) {
      var d, new_element, original_element;
      original_element = $("#i-" + y + "-" + x);
      if (original_element.val() === "0" && original_element.val().length) {
        while (true) {
          d = moment(d || y).subtract('days', 1).format('YYYY-MM-DD');
          new_element = $('#i-' + d + '-' + x);
          if (!new_element.length) {
            break;
          }
          if (new_element.val()) {
            event.preventDefault();
            account_data[date] = new_element.val();
            break;
          }
        }
      }
      if (original_element.caret() !== $scope.caret) {
        $scope.caret = original_element.caret();
        return;
      }
      switch (event.which) {
        case 37:
          return moveCursor(x, y, "left");
        case 38:
          return moveCursor(x, y, "up");
        case 39:
          return moveCursor(x, y, "right");
        case 13:
        case 40:
          return moveCursor(x, y, "down");
      }
    };
    hideValue = function() {
      if ($scope.page === 'hidden') {
        return 0;
      } else {
        return 1;
      }
    };
    updateClientCount = function() {
      if ($scope.page === 'hidden') {
        return $scope.visible_clients_count++;
      } else {
        return $scope.hidden_clients_count++;
      }
    };
    return $scope.toggleConfirmed = function(account) {
      return $rootScope.toggleEnumServer(account, 'confirmed', Confirmed, Account);
    };
  });

}).call(this);

(function() {
  var indexOf = [].indexOf || function(item) { for (var i = 0, l = this.length; i < l; i++) { if (i in this && this[i] === item) return i; } return -1; };

  angular.module('Egerep').controller('AddToList', function($scope, Genders, Grades, Subjects, TutorStates, Destinations, TutorService, PhoneService, RequestList) {
    var TRANSPARENT_MARKER, bindTutorMarkerEvents, clicks, findIntersectingMetros, markerClusterer, rebindDraggable, repaintChosen, showClientOnMap, showTutorsOnMap, unsetAllMarkers;
    bindArguments($scope, arguments);
    TRANSPARENT_MARKER = 0.3;
    clicks = 0;
    markerClusterer = void 0;
    $scope.mode = 'map';
    $scope.loading = false;
    angular.element(document).ready(function() {
      $scope.list = new RequestList($scope.list);
      return $('.map-tutor-list').droppable();
    });
    $scope.getHours = function(minutes) {
      return Math.floor(minutes / 60);
    };
    $scope.getMinutes = function(minutes) {
      return minutes % 60;
    };
    $scope.find = function() {
      $scope.loading = true;
      return TutorService.getFiltered({
        search: $scope.search,
        client_marker: $scope.client.markers[0]
      }).then(function(response) {
        $scope.tutors = response.data;
        showTutorsOnMap();
        findIntersectingMetros();
        repaintChosen();
        return $scope.loading = false;
      });
    };
    $scope.added = function(tutor_id) {
      return indexOf.call($scope.list.tutor_ids.map(Number), tutor_id) >= 0;
    };
    rebindDraggable = function() {
      return $('.temporary-tutor').draggable({
        containment: 'window',
        revert: function(valid) {
          if (valid) {
            return true;
          }
          $scope.tutor_list = removeById($scope.tutor_list, $scope.dragging_tutor.id);
          return $scope.$apply();
        }
      });
    };
    $scope.startDragging = function(tutor) {
      return $scope.dragging_tutor = tutor;
    };
    showTutorsOnMap = function() {
      unsetAllMarkers();
      $scope.marker_id = 1;
      $scope.tutor_list = [];
      $scope.markers = [];
      if ($scope.tutors) {
        $scope.tutors.forEach(function(tutor) {
          return tutor.markers.forEach(function(marker) {
            var new_marker;
            new_marker = newMarker($scope.marker_id++, new google.maps.LatLng(marker.lat, marker.lng), $scope.map, marker.type);
            new_marker.metros = marker.metros;
            new_marker.tutor = tutor;
            new_marker.setMap($scope.map);
            bindTutorMarkerEvents(new_marker);
            return $scope.markers.push(new_marker);
          });
        });
      }
      return markerClusterer = new MarkerClusterer($scope.map, $scope.markers, {
        gridSize: 10,
        imagePath: 'img/maps/clusterer/m'
      });
    };
    showClientOnMap = function() {
      return $scope.client.markers.forEach(function(marker) {
        var new_marker;
        new_marker = newMarker($scope.marker_id++, new google.maps.LatLng(marker.lat, marker.lng), $scope.map, 'white');
        new_marker.metros = marker.metros;
        return new_marker.setMap($scope.map);
      });
    };
    unsetAllMarkers = function() {
      if ($scope.markers !== void 0) {
        $scope.markers.forEach(function(marker) {
          return marker.setMap(null);
        });
      }
      if (markerClusterer !== void 0) {
        return markerClusterer.clearMarkers();
      }
    };
    findIntersectingMetros = function() {
      if ($scope.search.destination === 'r_k') {
        $scope.markers.forEach(function(marker) {
          marker.intersecting = false;
          return $scope.client.markers.forEach(function(client_marker) {
            return client_marker.metros.forEach(function(client_metro) {
              var ref;
              if (ref = client_metro.station_id, indexOf.call(marker.tutor.svg_map, ref) >= 0) {
                marker.intersecting = true;
                marker.tutor.intersecting = true;
              }
            });
          });
        });
        return $scope.markers.forEach(function(marker) {
          if (!marker.intersecting) {
            return marker.setOpacity(TRANSPARENT_MARKER);
          }
        });
      }
    };
    $scope.intersectingTutors = function() {
      return _.where($scope.tutors, {
        intersecting: true
      });
    };
    $scope.notIntersectingTutors = function() {
      return _.filter($scope.tutors, function(tutor) {
        return _.isUndefined(tutor.intersecting);
      });
    };
    bindTutorMarkerEvents = function(marker) {
      google.maps.event.addListener(marker, 'click', function(event) {
        clicks++;
        if (clicks === 1) {
          return setTimeout(function() {
            var ref;
            if (clicks === 1) {
              if (ref = marker.tutor, indexOf.call($scope.tutor_list, ref) >= 0) {
                $scope.tutor_list = removeById($scope.tutor_list, marker.tutor.id);
              } else {
                $scope.hovered_tutor = null;
                $scope.tutor_list.push(marker.tutor);
              }
              $scope.$apply();
              rebindDraggable();
            } else {
              $scope.addOrRemove(marker.tutor.id);
            }
            return clicks = 0;
          }, 250);
        }
      });
      google.maps.event.addListener(marker, 'dblclick', function(event) {
        return clicks++;
      });
      google.maps.event.addListener(marker, 'mouseover', function(event) {
        var ref;
        if (ref = marker.tutor, indexOf.call($scope.tutor_list, ref) >= 0) {
          return;
        }
        $scope.hovered_tutor = marker.tutor;
        return $scope.$apply();
      });
      return google.maps.event.addListener(marker, 'mouseout', function(event) {
        $scope.hovered_tutor = null;
        return $scope.$apply();
      });
    };
    $scope.addOrRemove = function(tutor_id) {
      tutor_id = parseInt(tutor_id);
      if (indexOf.call($scope.list.tutor_ids.map(Number), tutor_id) >= 0) {
        $scope.list.tutor_ids = _.without($scope.list.tutor_ids.map(Number), tutor_id);
      } else {
        $scope.list.tutor_ids.push(tutor_id);
      }
      repaintChosen();
      return $scope.list.$update();
    };
    repaintChosen = function() {
      return $scope.markers.forEach(function(marker) {
        var ref, ref1;
        if ((ref = marker.tutor.id, indexOf.call($scope.list.tutor_ids.map(Number), ref) >= 0) && !marker.chosen) {
          marker.chosen = true;
          marker.setOpacity(1);
          marker.setIcon(ICON_BLUE);
        }
        if ((ref1 = marker.tutor.id, indexOf.call($scope.list.tutor_ids.map(Number), ref1) < 0) && marker.chosen) {
          marker.chosen = false;
          marker.setOpacity(marker.intersecting ? 1 : TRANSPARENT_MARKER);
          return marker.setIcon(getMarkerType(marker.type));
        }
      });
    };
    return $scope.$on('mapInitialized', function(event, map) {
      var INIT_COORDS;
      $scope.gmap = map;
      INIT_COORDS = {
        lat: 55.7387,
        lng: 37.6032
      };
      $scope.RECOM_BOUNDS = new google.maps.LatLngBounds(new google.maps.LatLng(INIT_COORDS.lat - 0.5, INIT_COORDS.lng - 0.5), new google.maps.LatLng(INIT_COORDS.lat + 0.5, INIT_COORDS.lng + 0.5));
      $scope.geocoder = new google.maps.Geocoder;
      $scope.gmap.setCenter(new google.maps.LatLng(55.7387, 37.6032));
      $scope.gmap.setZoom(11);
      showClientOnMap();
      $scope.tutors = $scope.list.tutors;
      showTutorsOnMap();
      return repaintChosen();
    });
  });

}).call(this);

(function() {
  angular.module('Egerep').factory('Archives', function($resource) {
    return $resource('api/archives/:id', {}, {
      update: {
        method: 'PUT'
      }
    });
  }).controller('ArchivesIndex', function($rootScope, $scope, $timeout, $http, AttachmentService, UserService, PhoneService, Subjects, Grades, Presence, YesNo, AttachmentVisibility, AttachmentErrors, ArchiveStates, Checked) {
    var loadArchives, refreshCounts;
    bindArguments($scope, arguments);
    $rootScope.frontend_loading = true;
    refreshCounts = function() {
      return $timeout(function() {
        $('.selectpicker option').each(function(index, el) {
          $(el).data('subtext', $(el).attr('data-subtext'));
          return $(el).data('content', $(el).attr('data-content'));
        });
        return $('.selectpicker').selectpicker('refresh');
      }, 100);
    };
    $scope.keyFilter = function(event) {
      if (event.keyCode === 13) {
        return $scope.filter();
      }
    };
    $scope.filter = function() {
      $.cookie('archives', JSON.stringify($scope.search), {
        expires: 365,
        path: '/'
      });
      $scope.current_page = 1;
      return $scope.pageChanged();
    };
    $scope.changeState = function(state_id) {
      $rootScope.frontend_loading = true;
      $scope.archives = [];
      $scope.current_page = 1;
      loadArchives($scope.current_page);
      return window.history.pushState(state_id, '', 'archives/' + state_id.toLowerCase());
    };
    $timeout(function() {
      $scope.search = $.cookie('archives') ? JSON.parse($.cookie('archives')) : {};
      loadArchives($scope.page);
      return $scope.current_page = $scope.page;
    });
    $scope.pageChanged = function() {
      $rootScope.frontend_loading = true;
      $rootScope.archives = [];
      loadArchives($scope.current_page);
      return paginate('archives', $scope.current_page);
    };
    return loadArchives = function(page) {
      var params;
      params = '?page=' + page;
      return $http.get("api/archives" + params).then(function(response) {
        $scope.data = response.data.data;
        $scope.archives = response.data.data.data;
        $scope.counts = response.data.counts;
        $rootScope.frontend_loading = false;
        return refreshCounts();
      });
    };
  });

}).call(this);

(function() {
  angular.module('Egerep').factory('Attachment', function($resource) {
    return $resource('api/attachments/:id', {}, {
      update: {
        method: 'PUT'
      }
    });
  }).controller('AttachmentsStats', function($scope, $rootScope, $http, $timeout, Months, UserService) {
    $scope.getYears = function() {
      var count, i, years;
      count = 4;
      i = 0;
      years = [];
      while (i < count) {
        years.push(moment().subtract('year', i).format('YYYY'));
        i++;
      }
      return years;
    };
    $scope.getUsersByYear = function(year) {
      return _.chain($scope.data).where({
        year: parseInt(year)
      }).pluck('user_id').uniq().value();
    };
    $scope.getDays = function() {
      return _.range(1, 32);
    };
    $scope.dayExtremum = function(day, year, val, mode) {
      var condition, data, extremum, max, min;
      if (!val) {
        return false;
      }
      condition = {
        year: parseInt(year)
      };
      if (day !== null) {
        condition.day = parseInt(day);
      }
      data = _.where($scope.data, condition);
      max = -999;
      min = 999;
      data.forEach(function(d) {
        if (d.count > max) {
          max = d.count;
        }
        if (d.count < min) {
          return min = d.count;
        }
      });
      extremum = mode === 'max' ? max : min;
      if (day === null) {
        console.log(extremum, val, year);
      }
      return val === extremum;
    };
    $scope.totalExtremum = function(year, val) {
      var max, user_ids;
      if (!val) {
        return false;
      }
      user_ids = $scope.getUsersByYear(year);
      if (!user_ids.length) {
        return false;
      }
      max = -9999;
      user_ids.forEach(function(user_id) {
        var v;
        v = $scope.getUserTotal(year, user_id);
        if (v > max) {
          return max = v;
        }
      });
      return val === max;
    };
    $scope.getUserTotal = function(year, user_id) {
      var data, sum;
      data = _.where($scope.data, {
        year: parseInt(year),
        user_id: parseInt(user_id)
      });
      sum = 0;
      data.forEach(function(d) {
        return sum += d.count;
      });
      return sum || '';
    };
    $scope.getDayTotal = function(year, day) {
      var condition, data, sum;
      if (day == null) {
        day = null;
      }
      condition = {
        year: parseInt(year)
      };
      if (day !== null) {
        condition.day = parseInt(day);
      }
      data = _.where($scope.data, condition);
      sum = 0;
      data.forEach(function(d) {
        return sum += d.count;
      });
      return sum || '';
    };
    $scope.getValue = function(day, year, user_id) {
      var d;
      d = _.find(scope.data, {
        day: parseInt(day),
        year: parseInt(year),
        user_id: parseInt(user_id)
      });
      if (d !== void 0) {
        return d.count;
      } else {
        return '';
      }
    };
    $scope.$watch('month', function(newVal, oldVal) {
      $rootScope.frontend_loading = true;
      return $http.post('api/attachments/stats', {
        month: newVal
      }).then(function(response) {
        $rootScope.frontend_loading = false;
        return $scope.data = response.data;
      });
    });
    return bindArguments($scope, arguments);
  }).controller('AttachmentsIndex', function($rootScope, $scope, $timeout, $http, AttachmentStates, AttachmentService, UserService, PhoneService, Subjects, Grades, Presence, YesNo, AttachmentVisibility, AttachmentErrors) {
    var loadAttachments, refreshCounts;
    bindArguments($scope, arguments);
    $rootScope.frontend_loading = true;
    $scope.recalcAttachmentErrors = function() {
      $scope.attachment_errors_updating = true;
      return $http.post('api/command/model-errors', {
        model: 'attachments'
      });
    };
    refreshCounts = function() {
      return $timeout(function() {
        $('.selectpicker option').each(function(index, el) {
          $(el).data('subtext', $(el).attr('data-subtext'));
          return $(el).data('content', $(el).attr('data-content'));
        });
        $('.selectpicker').selectpicker('refresh');
        $('.attachment-filters button').css('background', 'none');
        return $('.attachment-filters select > option[value!=""]:selected').parent('select').siblings('button').css('background', '#dceee5');
      }, 100);
    };
    $scope.filter = function() {
      $.cookie("attachments", JSON.stringify($scope.search), {
        expires: 365,
        path: '/'
      });
      $scope.current_page = 1;
      return $scope.pageChanged();
    };
    $scope.changeState = function(state_id) {
      $rootScope.frontend_loading = true;
      $scope.attachments = [];
      $scope.current_page = 1;
      loadAttachments($scope.current_page);
      return window.history.pushState(state_id, '', 'attachments/' + state_id.toLowerCase());
    };
    $timeout(function() {
      $scope.search = $.cookie("attachments") ? JSON.parse($.cookie("attachments")) : {};
      loadAttachments($scope.page);
      return $scope.current_page = $scope.page;
    });
    $scope.pageChanged = function() {
      $rootScope.frontend_loading = true;
      $rootScope.attachments = [];
      loadAttachments($scope.current_page);
      return paginate('attachments', $scope.current_page);
    };
    return loadAttachments = function(page) {
      var params;
      params = '?page=' + page;
      return $http.get("api/attachments" + params).then(function(response) {
        $scope.data = response.data.data;
        $scope.attachments = response.data.data.data;
        $scope.counts = response.data.counts;
        $rootScope.frontend_loading = false;
        return refreshCounts();
      });
    };
  }).controller('AttachmentsNew', function($rootScope, $scope, $timeout, $http, AttachmentStates, AttachmentService, UserService, PhoneService, Subjects, Grades, Presence, YesNo, AttachmentVisibility, AttachmentErrors) {
    var load;
    bindArguments($scope, arguments);
    $rootScope.frontend_loading = true;
    $rootScope.loaded_comments = 0;
    $scope.$watch(function() {
      console.log($rootScope.loaded_comments);
      return $rootScope.loaded_comments;
    }, function(val) {
      console.log(val);
      if ($scope.attachments && $scope.attachments.length === val) {
        return $rootScope.frontend_loading = false;
      }
    });
    $scope.daysAgo = function(date) {
      var now;
      now = moment(Date.now());
      date = moment(new Date(date).getTime());
      return now.diff(date, 'days');
    };
    $timeout(function() {
      load($scope.page);
      return $scope.current_page = $scope.page;
    });
    $scope.pageChanged = function() {
      $rootScope.frontend_loading = true;
      $rootScope.loaded_comments = 0;
      load($scope.current_page);
      return paginate('attachments/new', $scope.current_page);
    };
    return load = function(page) {
      var params;
      params = '?page=' + page;
      return $http.get("api/attachments/new" + params).then(function(response) {
        console.log(response);
        $scope.counts = response.data.counts;
        $scope.data = response.data;
        $scope.attachments = response.data.data;
        if (!$scope.attachments.length) {
          return $rootScope.frontend_loading = false;
        }
      });
    };
  });

}).call(this);

(function() {
  angular.module('Egerep').controller("CallsMissed", function($scope, $http, PhoneService) {
    bindArguments($scope, arguments);
    return $scope.deleteCall = function(call) {
      ajaxStart();
      return $http["delete"]("calls/" + call.entry_id, {}).then(function(response) {
        ajaxEnd();
        return $scope.calls = _.without($scope.calls, call);
      });
    };
  });

}).call(this);

(function() {
  var indexOf = [].indexOf || function(item) { for (var i = 0, l = this.length; i < l; i++) { if (i in this && this[i] === item) return i; } return -1; };

  angular.module('Egerep').controller("ClientsIndex", function($scope, $rootScope, $timeout, $http, Client, RequestStates, Request) {
    var load;
    $rootScope.frontend_loading = true;
    $scope.pageChanged = function() {
      load($scope.current_page);
      return paginate('clients', $scope.current_page);
    };
    load = function(page) {
      var params;
      $rootScope.frontend_loading = true;
      params = '?page=' + page;
      if ($scope.global_search) {
        params += "&global_search=" + $scope.global_search;
      }
      return $http.get("api/clients" + params).then(function(response) {
        $rootScope.frontendStop();
        $scope.data = response.data;
        return $scope.clients = $scope.data.data;
      });
    };
    return $timeout(function() {
      load($scope.page);
      return $scope.current_page = $scope.page;
    });
  }).controller("ClientsForm", function($scope, $rootScope, $timeout, $interval, $http, Client, Request, RequestList, User, RequestStates, Subjects, Grades, Attachment, ReviewStates, ArchiveStates, AttachmentStates, ReviewScores, Archive, Review, ApiService, UserService, RecommendationService, AttachmentService, AttachmentVisibility, Marker, YesNo, Checked) {
    var bindDroppable, bindTutorMarkerEvents, filterMarkers, rebindDraggable, repaintChosen, reselect, saveSelectedList, showClientOnMap, showTutorsOnMap, unsetAllMarkers, unsetSelected;
    bindArguments($scope, arguments);
    $rootScope.frontend_loading = true;
    $scope.is_dragging_teacher = false;
    $scope.sortableOptions = {
      tolerance: 'pointer',
      activeClass: 'drag-active',
      helper: 'clone',
      appendTo: 'body',
      start: function(e, ui) {
        $scope.is_dragging_teacher = true;
        return $scope.$apply();
      },
      stop: function(e, ui) {
        $scope.is_dragging_teacher = false;
        $scope.$apply();
        return saveSelectedList();
      }
    };
    $scope.edit = function() {
      filterMarkers();
      $scope.ajaxStart();
      return $scope.client.$update().then(function(response) {
        $scope.client = response;
        $scope.loadMarkers();
        $scope.ajaxEnd();
        return $timeout(function() {
          return reselect();
        });
      });
    };
    reselect = function() {
      if ($scope.selected_request) {
        _.each($scope.client.requests, function(request) {
          if ($scope.selected_request.id === request.id) {
            return $scope.selectRequest(request, true);
          }
        });
      }
      if ($scope.selected_list) {
        _.each($scope.selected_request.lists, function(list) {
          if ($scope.selected_list.id === list.id) {
            return $scope.setList(list, true);
          }
        });
      }
      if ($scope.selected_attachment) {
        return _.each($scope.selected_list.attachments, function(attachment) {
          if ($scope.selected_attachment.id === attachment.id) {
            return $scope.selectAttachment(attachment);
          }
        });
      }
    };
    $scope.save = function() {
      filterMarkers();
      $scope.ajaxStart();
      return $scope.Client.save($scope.client, function(response) {
        return window.location = "requests/" + response.id + "/edit";
      });
    };
    bindDroppable = function() {
      return $timeout(function() {
        return $('.teacher-remove-droppable').droppable({
          tolerance: 'pointer',
          hoverClass: 'drop-hover',
          drop: function(e, ui) {
            var tutor_id;
            tutor_id = $(ui.draggable).data('id');
            return $timeout(function() {
              $scope.selected_list.tutor_ids = _.without($scope.selected_list.tutor_ids, tutor_id.toString());
              return saveSelectedList();
            });
          }
        });
      });
    };
    $timeout(function() {
      $scope.users = User.query();
      $http.get('api/tutors/list').success(function(tutors) {
        return $scope.tutors = tutors;
      });
      if ($scope.id > 0) {
        return $scope.client = Client.get({
          id: $scope.id
        }, function(client) {
          $scope.selected_request = $scope.request_id ? _.findWhere(client.requests, {
            id: $scope.request_id
          }) : client.requests[0];
          $scope.parseHash();
          sp('list-subjects', 'выберите предмет');
          return $rootScope.frontendStop();
        });
      } else {
        $scope.client = $scope.new_client;
        $scope.client.requests = [$scope.new_request];
        $scope.selected_request = $scope.client.requests[0];
        return $rootScope.frontendStop();
      }
    });
    saveSelectedList = function() {
      return RequestList.update($scope.selected_list);
    };
    $scope.getTutorList = function() {
      var tutors;
      tutors = [];
      if ($scope.selected_list) {
        $.each($scope.selected_list.tutor_ids, function(index, tutor_id) {
          return tutors.push(findById($scope.selected_list.tutors, tutor_id));
        });
        return tutors;
      }
    };
    $scope.parseHash = function() {
      var values;
      values = window.location.hash.split('#');
      values.shift();
      if (values[0]) {
        $scope.selected_list = findById($scope.selected_request.lists, values[0]);
      }
      if (values[1] && $scope.selected_list) {
        return $scope.selected_attachment = findById($scope.selected_list.attachments, values[1]);
      }
    };
    $scope.toggleArchive = function() {
      if ($scope.selected_attachment.archive) {
        return Archive["delete"]($scope.selected_attachment.archive, function() {
          return delete $scope.selected_attachment.archive;
        });
      } else {
        return Archive.save({
          attachment_id: $scope.selected_attachment.id
        }, function(response) {
          rebindMasks();
          return $scope.selected_attachment.archive = response;
        });
      }
    };
    $scope.toggleReview = function() {
      if ($scope.selected_attachment.review) {
        return Review["delete"]($scope.selected_attachment.review, function() {
          return delete $scope.selected_attachment.review;
        });
      } else {
        return Review.save({
          attachment_id: $scope.selected_attachment.id
        }, function(response) {
          return $scope.selected_attachment.review = response;
        });
      }
    };
    $scope.attachmentExists = function(tutor_id) {
      var attachment_exists;
      attachment_exists = false;
      $.each($scope.client.requests, function(index, request) {
        if (attachment_exists) {
          return;
        }
        return $.each(request.lists, function(index, list) {
          return $.each(list.attachments, function(index, attachment) {
            if (parseInt(attachment.tutor_id) === parseInt(tutor_id)) {
              return attachment_exists = true;
            }
          });
        });
      });
      return attachment_exists;
    };
    $scope.selectAttachment = function(attachment) {
      return $scope.selected_attachment = attachment;
    };
    $scope.addList = function() {
      return $scope.dialog('add-subject');
    };
    $scope.setList = function(list, update) {
      $scope.selected_list = list;
      if ($scope.list_map) {
        $scope.showListMap();
      }
      if (!update) {
        return delete $scope.selected_attachment;
      }
    };
    $scope.listExists = function(subject_id) {
      return _.findWhere($scope.selected_request.lists, {
        subject_id: parseInt(subject_id)
      }) !== void 0;
    };
    $scope.selectRequest = function(request, update) {
      $scope.selected_request = request;
      if (!update) {
        return delete $scope.selected_list;
      }
    };
    $scope.addListSubject = function() {
      RequestList.save({
        request_id: $scope.selected_request.id,
        subjects: $scope.list_subjects
      }, function(data) {
        $scope.selected_request.lists.push(data);
        return $scope.selected_list = data;
      });
      delete $scope.list_subjects;
      spRefresh('list-subjects');
      $('#add-subject').modal('hide');
    };
    $scope.addListTutor = function() {
      $scope.selected_list.tutor_ids.push($scope.list_tutor_id);
      return RequestList.update({
        id: $scope.selected_list.id,
        tutor_ids: $scope.selected_list.tutor_ids
      }, function() {
        delete $scope.list_tutor_id;
        return $('#add-tutor').modal('hide');
      });
    };
    $scope.newAttachment = function(tutor_id) {
      ajaxStart();
      return Attachment.save({
        grade: $scope.client.grade,
        tutor_id: tutor_id,
        subjects: $scope.selected_list.subjects,
        request_list_id: $scope.selected_list.id,
        client_id: $scope.client.id
      }, function(new_attachment) {
        ajaxEnd();
        if (new_attachment.id) {
          $scope.selected_attachment = new_attachment;
          return $scope.selected_list.attachments.push(new_attachment);
        }
      });
    };
    $scope.addRequest = function() {
      var new_request;
      new_request = new Request({
        client_id: $scope.id
      });
      ajaxStart();
      return new_request.$save().then(function(data) {
        ajaxEnd();
        $scope.client.requests.push(data);
        $scope.selected_request = data;
        return unsetSelected(false, true, true);
      });
    };
    $scope.removeRequest = function() {
      return bootbox.confirm('Вы уверены, что хотите удалить заявку?', function(response) {
        if (response === true) {
          return Request["delete"]({
            id: $scope.selected_request.id
          }, function() {
            $scope.client.requests = removeById($scope.client.requests, $scope.selected_request.id);
            return unsetSelected(true, true, true);
          });
        }
      });
    };
    $scope.transferRequest = function() {
      return $('#transfer-request').modal('show');
    };
    $scope.transferRequestGo = function() {
      $('#transfer-request').modal('hide');
      ajaxStart();
      return $http.post("api/requests/transfer/" + $scope.selected_request.id, {
        client_id: $scope.transfer_client_id
      }).then(function(response) {
        ajaxEnd();
        console.log(response);
        if (response.data !== '') {
          return location.reload();
        } else {
          return bootbox.alert('Клиент не существует');
        }
      });
    };
    unsetSelected = function(request, list, attachment) {
      if (request == null) {
        request = false;
      }
      if (list == null) {
        list = false;
      }
      if (attachment == null) {
        attachment = false;
      }
      if (request) {
        $scope.selected_request = null;
      }
      if (list) {
        $scope.selected_list = null;
      }
      if (attachment) {
        return $scope.selected_attachment = null;
      }
    };
    $scope.removeList = function() {
      return bootbox.confirm('Вы уверены, что хотите удалить список?', function(response) {
        if (response === true) {
          return RequestList["delete"]({
            id: $scope.selected_list.id
          }, function() {
            $scope.selected_request.lists = removeById($scope.selected_request.lists, $scope.selected_list.id);
            delete $scope.selected_list;
            return unsetSelected(false, true, true);
          });
        }
      });
    };
    $scope.removeAttachment = function() {
      return bootbox.confirm('Вы уверены, что хотите удалить стыковку?', function(response) {
        if (response === true) {
          return Attachment["delete"]({
            id: $scope.selected_attachment.id
          }, function() {
            $scope.selected_list.attachments = removeById($scope.selected_list.attachments, $scope.selected_attachment.id);
            delete $scope.selected_attachment;
            return unsetSelected(false, false, true);
          });
        }
      });
    };
    $scope.$watch('selected_request.comment', function(newVal, oldVal) {
      var matches;
      if (newVal === void 0 && oldVal === void 0) {
        return;
      }
      if (newVal === void 0) {
        newVal = oldVal;
      }
      $scope.request_tutor_ids = [];
      matches = newVal.match(/Репетитор [\d]+/gi);
      return $.each(matches, function(index, match) {
        var tutor_id;
        tutor_id = match.match(/[\d]+/gi);
        return $scope.request_tutor_ids.push(parseInt(tutor_id));
      });
    });
    $scope.$watch('selected_attachment', function(newVal, oldVal) {
      if (newVal === void 0) {
        return;
      }
      if (oldVal === void 0) {
        sp('attachment-subjects', 'выберите предмет');
      }
      if (oldVal !== void 0) {
        spRefresh('attachment-subjects');
      }
      return rebindMasks();
    });
    $scope.$watch('selected_list', function(newVal, oldVal) {
      if (oldVal === void 0 && newVal !== void 0) {
        return bindDroppable();
      }
    });
    $scope.marker_id = 1;
    $scope.map_number = 0;
    filterMarkers = function() {
      var new_markers;
      new_markers = [];
      $.each($scope.client.markers, function(index, marker) {
        return new_markers.push(_.pick(marker, 'lat', 'lng', 'type', 'metros', 'server_id'));
      });
      return $scope.client.markers = new_markers;
    };
    $scope.$on('mapInitialized', function(event, map) {
      var INIT_COORDS;
      map.number = $scope.map_number;
      if ($scope.map_number === 0) {
        $scope.gmap = map;
        $scope.loadMarkers();
        INIT_COORDS = {
          lat: 55.7387,
          lng: 37.6032
        };
        $scope.RECOM_BOUNDS = new google.maps.LatLngBounds(new google.maps.LatLng(INIT_COORDS.lat - 0.5, INIT_COORDS.lng - 0.5), new google.maps.LatLng(INIT_COORDS.lat + 0.5, INIT_COORDS.lng + 0.5));
        $scope.geocoder = new google.maps.Geocoder;
        google.maps.event.addListener(map, 'click', function(event) {
          return $scope.gmapAddMarker(event);
        });
      } else {
        $scope.gmap2 = map;
        $scope.gmap2.setCenter(new google.maps.LatLng(55.7387, 37.6032));
        $scope.gmap2.setZoom(11);
      }
      return $scope.map_number++;
    });
    $scope.showMap = function() {
      var bounds, markers_count;
      $('#gmap-modal').modal('show');
      google.maps.event.trigger($scope.gmap, 'resize');
      $scope.gmap.setCenter(new google.maps.LatLng(55.7387, 37.6032));
      $scope.gmap.setZoom(11);
      $('#map-search').val('');
      if ($scope.search_markers && $scope.search_markers.length) {
        $.each($scope.search_markers, function(i, marker) {
          return marker.setMap(null);
        });
        $scope.search_markers = [];
      }
      if ($scope.client.markers.length) {
        bounds = new google.maps.LatLngBounds;
        markers_count = 0;
        $.each($scope.client.markers, function(index, marker) {
          markers_count++;
          return bounds.extend(marker.position);
        });
        if (markers_count > 0) {
          $scope.gmap.fitBounds(bounds);
          $scope.gmap.panToBounds(bounds);
          return $scope.gmap.setZoom(11);
        }
      }
    };
    $scope.gmapAddMarker = function(event) {
      var marker;
      marker = newMarker($scope.marker_id++, event.latLng, $scope.gmap);
      $scope.client.markers.push(marker);
      marker.setMap($scope.gmap);
      ApiService.metro('closest', {
        lat: marker.lat,
        lng: marker.lng
      }).then(function(response) {
        return marker.metros = response.data;
      });
      $scope.bindMarkerDelete(marker);
      return $scope.bindMarkerChangeType(marker);
    };
    $scope.bindMarkerDelete = function(marker) {
      return google.maps.event.addListener(marker, 'dblclick', function(event) {
        var t;
        t = this;
        t.setMap(null);
        return $.each($scope.client.markers, function(index, m) {
          if (m !== void 0 && t.id === m.id) {
            if (m.server_id !== void 0) {
              ajaxStart();
              Marker["delete"]({
                id: m.server_id
              }, function() {
                return ajaxEnd();
              });
            }
            return $scope.client.markers.splice(index, 1);
          }
        });
      });
    };
    $scope.bindMarkerChangeType = function(marker) {
      return google.maps.event.addListener(marker, 'click', function(event) {
        if (this.type === 'green') {
          this.type = 'red';
          this.setIcon(ICON_RED);
        } else if (this.type === 'red') {
          this.type = 'blue';
          this.setIcon(ICON_BLUE);
        } else {
          this.type = 'green';
          this.setIcon(ICON_GREEN);
        }
        if (marker.server_id !== void 0) {
          ajaxStart();
          return Marker.update({
            id: marker.server_id,
            type: this.type
          }, function() {
            return ajaxEnd();
          });
        }
      });
    };
    $scope.searchMap = function(address) {
      return $scope.geocoder.geocode({
        address: address + ', московская область',
        bounds: $scope.RECOM_BOUNDS,
        componentRestrictions: {
          country: 'RU',
          administrativeArea: 'Moscow'
        }
      }, function(results, status) {
        var max_results, search_result_bounds;
        if (status === google.maps.GeocoderStatus.OK) {
          max_results = 3;
          search_result_bounds = new google.maps.LatLngBounds;
          $.each(results, function(i, result) {
            var search_marker;
            if (i >= max_results) {
              return;
            }
            search_result_bounds.extend(result.geometry.location);
            search_marker = new google.maps.Marker({
              map: $scope.gmap,
              position: result.geometry.location,
              icon: ICON_SEARCH
            });
            google.maps.event.addListener(search_marker, 'click', function(event) {
              this.setMap(null);
              return $scope.gmapAddMarker(event);
            });
            $scope.search_markers = initIfNotSet($scope.search_markers);
            return $scope.search_markers.push(search_marker);
          });
          if (results.length > 0) {
            $scope.gmap.fitBounds(search_result_bounds);
            $scope.gmap.panToBounds(search_result_bounds);
            if (results.length === 1) {
              return $scope.gmap.setZoom(12);
            }
          } else {
            return $('#map-search').addClass('has-error').focus();
          }
        }
      });
    };
    $scope.gmapsSearch = function($event) {
      if ($event.keyCode === 13 || $event.type === 'click') {
        if ($('#map-search').val() === '') {
          $('#map-search').addClass('has-error').focus();
        } else {
          $('#map-search').removeClass('has-error');
        }
        return $scope.searchMap($('#map-search').val());
      }
    };
    $scope.loadMarkers = function() {
      return $rootScope.dataLoaded.promise.then(function() {
        var markers;
        markers = [];
        $.each($scope.client.markers, function(index, marker) {
          var new_marker;
          new_marker = newMarker($scope.marker_id++, new google.maps.LatLng(marker.lat, marker.lng), $scope.gmap, marker.type, marker.id);
          new_marker.metros = marker.metros;
          new_marker.setMap($scope.gmap);
          console.log('adding marker', $scope.gmap);
          $scope.bindMarkerDelete(new_marker);
          $scope.bindMarkerChangeType(new_marker);
          return markers.push(new_marker);
        });
        return $scope.client.markers = _.sortBy(markers, function(marker) {
          return marker.server_id;
        });
      });
    };
    $scope.saveMarkers = function() {
      $('#gmap-modal').modal('hide');
      filterMarkers();
    };
    $scope.listMap = function() {
      $scope.list_map = !$scope.list_map;
      return $scope.showListMap();
    };
    $scope.added = function(tutor_id) {
      return indexOf.call($scope.tutor_ids.map(Number), tutor_id) >= 0;
    };
    rebindDraggable = function() {
      return $('.temporary-tutor').draggable({
        containment: 'window',
        revert: function(valid) {
          if (valid) {
            return true;
          }
          $scope.tutor_list = removeById($scope.tutor_list, $scope.dragging_tutor.id);
          $scope.tutor_ids = _.without($scope.tutor_ids.map(Number), $scope.dragging_tutor.id);
          $scope.$apply();
          return repaintChosen();
        }
      });
    };
    $scope.startDragging = function(tutor) {
      return $scope.dragging_tutor = tutor;
    };
    showTutorsOnMap = function() {
      var bounds, markers_count;
      unsetAllMarkers();
      $scope.marker_id2 = 1;
      $scope.tutor_list = [];
      bounds = new google.maps.LatLngBounds;
      markers_count = 0;
      $scope.markers2 = [];
      google.maps.event.trigger($scope.gmap2, 'resize');
      $scope.gmap2.setCenter(new google.maps.LatLng(55.7387, 37.6032));
      $scope.gmap2.setZoom(11);
      $scope.client.markers.forEach(function(marker) {
        markers_count++;
        return bounds.extend(new google.maps.LatLng(marker.lat, marker.lng));
      });
      $scope.selected_list.tutors.forEach(function(tutor) {
        return tutor.markers.forEach(function(marker) {
          var new_marker;
          markers_count++;
          bounds.extend(new google.maps.LatLng(marker.lat, marker.lng));
          new_marker = newMarker($scope.marker_id2++, new google.maps.LatLng(marker.lat, marker.lng), $scope.gmap2, marker.type);
          new_marker.metros = marker.metros;
          new_marker.tutor = tutor;
          new_marker.setMap($scope.gmap2);
          bindTutorMarkerEvents(new_marker);
          return $scope.markers2.push(new_marker);
        });
      });
      if (markers_count > 0) {
        $scope.gmap2.fitBounds(bounds);
        $scope.gmap2.panToBounds(bounds);
        $scope.gmap2.setZoom(10);
      }
      return $scope.gmap2.panBy(150, 0);
    };
    showClientOnMap = function() {
      return $scope.client.markers.forEach(function(marker) {
        var new_marker;
        new_marker = newMarker($scope.marker_id2++, new google.maps.LatLng(marker.lat, marker.lng), $scope.gmap2, 'white');
        new_marker.metros = marker.metros;
        return new_marker.setMap($scope.gmap2);
      });
    };
    unsetAllMarkers = function() {
      if ($scope.markers2 !== void 0) {
        return $scope.markers2.forEach(function(marker) {
          return marker.setMap(null);
        });
      }
    };
    bindTutorMarkerEvents = function(marker) {
      google.maps.event.addListener(marker, 'click', function(event) {
        var ref;
        if (ref = marker.tutor, indexOf.call($scope.tutor_list, ref) >= 0) {
          $scope.tutor_list = removeById($scope.tutor_list, marker.tutor.id);
        } else {
          $scope.hovered_tutor = null;
          $scope.tutor_list.push(marker.tutor);
        }
        $scope.addOrRemove(marker.tutor.id);
        $scope.$apply();
        return rebindDraggable();
      });
      google.maps.event.addListener(marker, 'mouseover', function(event) {
        var ref;
        if (ref = marker.tutor, indexOf.call($scope.tutor_list, ref) >= 0) {
          return;
        }
        $scope.hovered_tutor = marker.tutor;
        return $scope.$apply();
      });
      return google.maps.event.addListener(marker, 'mouseout', function(event) {
        $scope.hovered_tutor = null;
        return $scope.$apply();
      });
    };
    $scope.addOrRemove = function(tutor_id) {
      tutor_id = parseInt(tutor_id);
      if ($scope.tutor_ids === void 0) {
        $scope.tutor_ids = [];
      }
      if (indexOf.call($scope.tutor_ids.map(Number), tutor_id) >= 0) {
        $scope.tutor_ids = _.without($scope.tutor_ids.map(Number), tutor_id);
      } else {
        $scope.tutor_ids.push(tutor_id);
      }
      return repaintChosen();
    };
    repaintChosen = function() {
      return $scope.markers2.forEach(function(marker) {
        var ref, ref1;
        if ((ref = marker.tutor.id, indexOf.call($scope.tutor_ids.map(Number), ref) >= 0) && !marker.chosen) {
          marker.chosen = true;
          marker.setIcon(ICON_BLUE);
        }
        if ((ref1 = marker.tutor.id, indexOf.call($scope.tutor_ids.map(Number), ref1) < 0) && marker.chosen) {
          marker.chosen = false;
          return marker.setIcon(getMarkerType(marker.type));
        }
      });
    };
    return $scope.showListMap = function() {
      return $timeout(function() {
        showTutorsOnMap();
        return showClientOnMap();
      });
    };
  });

}).call(this);

(function() {
  angular.module('Egerep').controller('ContractIndex', function($scope, $http, UserService) {
    return bindArguments($scope, arguments);
  }).controller('ContractEdit', function($scope, $http, $timeout, UserService) {
    bindArguments($scope, arguments);
    $scope.save = function() {
      ajaxStart();
      $scope.saving = true;
      $scope.contract_html = $scope.editor.getValue();
      return $http.post("contract", {
        contract_html: $scope.contract_html,
        contract_date: $scope.contract_date
      }).then(function(response) {
        ajaxEnd();
        return $scope.saving = false;
      });
    };
    return angular.element(document).ready(function() {
      return $timeout(function() {
        $scope.editor = ace.edit('editor');
        return $scope.editor.getSession().setMode('ace/mode/html');
      }, 300);
    });
  });

}).call(this);

(function() {
  var indexOf = [].indexOf || function(item) { for (var i = 0, l = this.length; i < l; i++) { if (i in this && this[i] === item) return i; } return -1; };

  angular.module('Egerep').controller('DebtMap', function($scope, $timeout, TutorService, Tutor, Subjects, YesNo) {
    var TRANSPARENT_DEFAULT, TRANSPARENT_HAS_PLANNED, bindTutorMarkerEvents, clicks, findIntersectingMetros, getOpacity, markerClusterer, rebindDraggable, repaintChosen, showClientOnMap, showTutorsOnMap, unsetAllMarkers;
    bindArguments($scope, arguments);
    TRANSPARENT_HAS_PLANNED = 0.5;
    TRANSPARENT_DEFAULT = 1;
    clicks = 0;
    markerClusterer = void 0;
    $scope.mode = 'map';
    $scope.loading = false;
    $scope.search = {};
    $scope.tutor_ids = [];
    $scope.sortType = 'debt_calc';
    $scope.sortReverse = false;
    $scope.$watch('mode', function(newVal, oldVal) {
      if (newVal === 'debtor' && $scope.debtors === void 0) {
        return TutorService.getDebtors().then(function(response) {
          return $scope.debtors = response.data;
        });
      }
    });
    $scope.totalLastDebt = function() {
      var sum, tutors;
      sum = 0;
      tutors = $scope.mode === 'list' ? $scope.tutors : $scope.debtors;
      $.each(tutors, function(index, tutor) {
        var debt;
        if (tutor.last_account_info !== null) {
          debt = tutor.last_account_info.debt;
          return sum += tutor.last_account_info.debt_type ? +debt : -debt;
        }
      });
      return {
        debt_type: sum < 0 ? 0 : 1,
        debt: Math.abs(sum)
      };
    };
    $scope.blurComment = function(tutor) {
      tutor.is_being_commented = false;
      return tutor.debt_comment = tutor.old_debt_comment;
    };
    $scope.focusComment = function(tutor) {
      tutor.is_being_commented = true;
      return tutor.old_debt_comment = tutor.debt_comment;
    };
    $scope.startComment = function(tutor) {
      tutor.is_being_commented = true;
      tutor.old_debt_comment = tutor.debt_comment;
      return $timeout(function() {
        return $("#list-comment-" + tutor.id).focus();
      });
    };
    $scope.saveComment = function(event, tutor) {
      if (event.keyCode === 13) {
        return Tutor.update({
          id: tutor.id,
          debt_comment: tutor.debt_comment
        }, function(response) {
          tutor.old_debt_comment = tutor.debt_comment;
          return $(event.target).blur();
        });
      }
    };
    angular.element(document).ready(function() {
      return $('.map-tutor-list').droppable();
    });
    $scope.find = function() {
      $scope.loading = true;
      $scope.tutor_ids = [];
      return TutorService.getDebtMap({
        search: $scope.search
      }).then(function(response) {
        $scope.tutors = response.data;
        angular.forEach($scope.tutors, function(tutor) {
          if (tutor.last_account_info) {
            return tutor.last_debt = tutor.last_account_info.debt_type ? tutor.last_account_info.debt : -tutor.last_account_info.debt;
          } else {
            return tutor.last_debt = 0;
          }
        });
        showTutorsOnMap();
        return $scope.loading = false;
      });
    };
    $scope.added = function(tutor_id) {
      return indexOf.call($scope.tutor_ids, tutor_id) >= 0;
    };
    rebindDraggable = function() {
      return $('.temporary-tutor').draggable({
        containment: 'window',
        appendTo: 'body',
        helper: 'clone',
        revert: function(valid) {
          if (valid) {
            return true;
          }
          $scope.tutor_list = removeById($scope.tutor_list, $scope.dragging_tutor.id);
          $scope.tutor_ids = _.without($scope.tutor_ids, $scope.dragging_tutor.id);
          $scope.$apply();
          return repaintChosen();
        },
        start: function() {
          $scope.isDragging = true;
          return $scope.$apply();
        },
        stop: function(event, ui) {
          ui.helper.remove();
          $scope.isDragging = false;
          return $scope.$apply();
        }
      });
    };
    $scope.startDragging = function(tutor) {
      return $scope.dragging_tutor = tutor;
    };
    showTutorsOnMap = function() {
      unsetAllMarkers();
      $scope.marker_id = 1;
      $scope.tutor_list = [];
      $scope.markers = [];
      $scope.tutors.forEach(function(tutor) {
        return tutor.markers.forEach(function(marker) {
          var new_marker;
          new_marker = newMarker($scope.marker_id++, new google.maps.LatLng(marker.lat, marker.lng), $scope.map, 'semi-black');
          new_marker.metros = marker.metros;
          new_marker.tutor = tutor;
          new_marker.setOpacity(getOpacity(new_marker));
          new_marker.setMap($scope.map);
          bindTutorMarkerEvents(new_marker);
          return $scope.markers.push(new_marker);
        });
      });
      return markerClusterer = new MarkerClusterer($scope.map, $scope.markers, {
        gridSize: 10,
        imagePath: 'img/maps/clusterer/m'
      });
    };
    showClientOnMap = function() {
      return $scope.client.markers.forEach(function(marker) {
        var new_marker;
        new_marker = newMarker($scope.marker_id++, new google.maps.LatLng(marker.lat, marker.lng), $scope.map, 'white');
        new_marker.metros = marker.metros;
        return new_marker.setMap($scope.map);
      });
    };
    unsetAllMarkers = function() {
      if ($scope.markers !== void 0) {
        $scope.markers.forEach(function(marker) {
          return marker.setMap(null);
        });
      }
      if (markerClusterer !== void 0) {
        return markerClusterer.clearMarkers();
      }
    };
    findIntersectingMetros = function() {
      if ($scope.search.destination === 'r_k') {
        $scope.markers.forEach(function(marker) {
          marker.intersecting = false;
          return $scope.client.markers.forEach(function(client_marker) {
            return client_marker.metros.forEach(function(client_metro) {
              var ref;
              if (ref = client_metro.station_id, indexOf.call(marker.tutor.svg_map, ref) >= 0) {
                marker.intersecting = true;
                marker.tutor.intersecting = true;
              }
            });
          });
        });
        return $scope.markers.forEach(function(marker) {
          if (!marker.intersecting) {
            return marker.setOpacity(TRANSPARENT_DEFAULT);
          }
        });
      }
    };
    $scope.intersectingTutors = function() {
      return _.where($scope.tutors, {
        intersecting: true
      });
    };
    $scope.notIntersectingTutors = function() {
      return _.filter($scope.tutors, function(tutor) {
        return _.isUndefined(tutor.intersecting);
      });
    };
    bindTutorMarkerEvents = function(marker) {
      google.maps.event.addListener(marker, 'click', function(event) {
        var ref;
        if (ref = marker.tutor, indexOf.call($scope.tutor_list, ref) >= 0) {
          $scope.tutor_list = removeById($scope.tutor_list, marker.tutor.id);
        } else {
          $scope.hovered_tutor = null;
          $scope.tutor_list.push(marker.tutor);
        }
        $scope.addOrRemove(marker.tutor.id);
        $scope.$apply();
        return rebindDraggable();
      });
      google.maps.event.addListener(marker, 'mouseover', function(event) {
        var ref;
        if (ref = marker.tutor, indexOf.call($scope.tutor_list, ref) >= 0) {
          return;
        }
        $scope.hovered_tutor = marker.tutor;
        return $scope.$apply();
      });
      return google.maps.event.addListener(marker, 'mouseout', function(event) {
        $scope.hovered_tutor = null;
        return $scope.$apply();
      });
    };
    $scope.addOrRemove = function(tutor_id) {
      tutor_id = parseInt(tutor_id);
      if (indexOf.call($scope.tutor_ids, tutor_id) >= 0) {
        $scope.tutor_ids = _.without($scope.tutor_ids, tutor_id);
      } else {
        $scope.tutor_ids.push(tutor_id);
      }
      return repaintChosen();
    };
    repaintChosen = function() {
      return $scope.markers.forEach(function(marker) {
        var ref, ref1;
        if ((ref = marker.tutor.id, indexOf.call($scope.tutor_ids, ref) >= 0) && !marker.chosen) {
          marker.chosen = true;
        }
        if ((ref1 = marker.tutor.id, indexOf.call($scope.tutor_ids, ref1) < 0) && marker.chosen) {
          marker.chosen = false;
        }
        if (marker.tutor.planned_account) {
          return marker.setIcon(ICON_YELLOW);
        }
      });
    };
    getOpacity = function(marker) {
      return 1;
    };
    return $scope.$on('mapInitialized', function(event, map) {
      var INIT_COORDS;
      $scope.gmap = map;
      INIT_COORDS = {
        lat: 55.7387,
        lng: 37.6032
      };
      $scope.RECOM_BOUNDS = new google.maps.LatLngBounds(new google.maps.LatLng(INIT_COORDS.lat - 0.5, INIT_COORDS.lng - 0.5), new google.maps.LatLng(INIT_COORDS.lat + 0.5, INIT_COORDS.lng + 0.5));
      $scope.geocoder = new google.maps.Geocoder;
      $scope.gmap.setCenter(new google.maps.LatLng(55.7387, 37.6032));
      return $scope.gmap.setZoom(11);
    });
  });

}).call(this);

(function() {
  angular.module('Egerep').controller('EmptyCtrl', function() {});

}).call(this);

(function() {
  angular.module('Egerep').controller('GraphController', function($scope, $timeout, $http, $rootScope) {
    var getDistance, getDistanceObject, parseId;
    bindArguments($scope, arguments);
    $scope.map_loaded = false;
    $scope.selected = [];
    parseId = function(elem) {
      return parseInt($(elem).attr('id').replace(/[^\d]/g, ''));
    };
    angular.element(document).ready(function() {
      return $timeout(function() {
        $('#stations > g > g').each(function(index, el) {
          $(el).on('mouseenter', function() {
            $scope.hovered_station_id = parseId(this);
            return $scope.$apply();
          });
          $(el).on('mouseleave', function() {
            $scope.hovered_station_id = void 0;
            return $scope.$apply();
          });
          return $(el).on('click', function() {
            if ($scope.selected.length === 2) {
              $scope.selected = [];
            }
            $scope.selected.push(parseId(this));
            if ($scope.selected.length === 2) {
              $scope.new_distance = getDistance($scope.selected[0], $scope.selected[1]);
            }
            return $scope.$apply();
          });
        });
        return $scope.map_loaded = true;
      }, 500);
    });
    $scope.$watch('hovered_station_id', function(newVal, oldVal) {
      var found_distances;
      if (newVal !== void 0) {
        found_distances = _.filter($scope.distances, function(distance) {
          return distance.from === newVal || distance.to === newVal;
        });
        $scope.found_distances = _.map(found_distances, _.clone);
        return angular.forEach($scope.found_distances, function(distance) {
          var from_buffer;
          if (distance.from !== newVal) {
            from_buffer = distance.from;
            distance.from = newVal;
            return distance.to = from_buffer;
          }
        });
      }
    });
    $scope.save = function() {
      var from, to;
      from = $scope.selected[0];
      to = $scope.selected[1];
      $rootScope.ajaxStart();
      return $http.post('graph/save', {
        from: from,
        to: to,
        distance: $scope.new_distance
      }).then(function() {
        var distance;
        distance = getDistanceObject(from, to);
        if (distance === void 0) {
          $scope.distances.push({
            from: from,
            to: to,
            distance: $scope.new_distance
          });
        } else {
          distance.distance = $scope.new_distance;
        }
        return $rootScope.ajaxEnd();
      });
    };
    $scope["delete"] = function() {
      var from, to;
      from = Math.min($scope.selected[0], $scope.selected[1]);
      to = Math.max($scope.selected[0], $scope.selected[1]);
      $rootScope.ajaxStart();
      return $http.post('graph/delete', {
        from: from,
        to: to
      }).then(function() {
        $rootScope.ajaxEnd();
        $scope.distances = _.without($scope.distances, _.findWhere($scope.distances, {
          from: from,
          to: to
        }));
        return $scope.selected = [];
      });
    };
    getDistance = function(from, to) {
      var distance;
      distance = getDistanceObject(from, to);
      if (distance === void 0) {
        return void 0;
      } else {
        return distance.distance;
      }
    };
    return getDistanceObject = function(from, to) {
      var a, b;
      a = Math.min(from, to);
      b = Math.max(from, to);
      return _.find($scope.distances, {
        from: a,
        to: b
      });
    };
  });

}).call(this);

(function() {
  angular.module('Egerep').controller('LoginCtrl', function($scope, $http) {
    angular.element(document).ready(function() {
      return $scope.l = Ladda.create(document.querySelector('#login-submit'));
    });
    $scope.enter = function($event) {
      if ($event.keyCode === 13) {
        return $scope.checkFields();
      }
    };
    return $scope.checkFields = function() {
      $scope.l.start();
      ajaxStart();
      $scope.in_process = true;
      return $http.post('login', {
        login: $scope.login,
        password: $scope.password
      }).then(function(response) {
        if (response.data === true) {
          return location.reload();
        } else {
          $scope.in_process = false;
          ajaxEnd();
          $scope.l.stop();
          return notifyError("Неправильная пара логин-пароль");
        }
      });
    };
  });

}).call(this);

(function() {
  angular.module('Egerep').controller('LogsIndex', function($rootScope, $scope, $timeout, $http, UserService, LogTypes, LogColumns) {
    var load;
    bindArguments($scope, arguments);
    $rootScope.frontend_loading = true;
    $scope.toJson = function(data) {
      return JSON.parse(data);
    };
    $scope.refreshCounts = function() {
      return $timeout(function() {
        $('.selectpicker option').each(function(index, el) {
          $(el).data('subtext', $(el).attr('data-subtext'));
          return $(el).data('content', $(el).attr('data-content'));
        });
        return $('.selectpicker').selectpicker('refresh');
      }, 100);
    };
    $scope.filter = function() {
      $.cookie("logs", JSON.stringify($scope.search), {
        expires: 365,
        path: '/'
      });
      $scope.current_page = 1;
      return $scope.pageChanged();
    };
    $scope.keyFilter = function(event) {
      if (event.keyCode === 13) {
        return $scope.filter();
      }
    };
    $timeout(function() {
      $scope.search = $.cookie("logs") ? JSON.parse($.cookie("logs")) : {};
      load($scope.page);
      return $scope.current_page = $scope.page;
    });
    $scope.pageChanged = function() {
      $rootScope.frontend_loading = true;
      load($scope.current_page);
      return paginate('logs', $scope.current_page);
    };
    return load = function(page) {
      var params;
      params = '?page=' + page;
      return $http.get("api/logs" + params).then(function(response) {
        console.log(response);
        $scope.counts = response.data.counts;
        $scope.data = response.data.data;
        $scope.logs = response.data.data.data;
        $rootScope.frontend_loading = false;
        return $scope.refreshCounts();
      });
    };
  });

}).call(this);

(function() {
  angular.module('Egerep').controller('NotificationsIndex', function($rootScope, $scope, $timeout, $http, AttachmentStates, AttachmentService, UserService, Notify) {
    var loadAttachments, refreshCounts;
    bindArguments($scope, arguments);
    $rootScope.frontend_loading = true;
    $scope.addDays = function(date, days) {
      return moment(date).add({
        day: days
      });
    };
    $scope.pastDate = function(date) {
      if (date && (Date.now() - new Date(date)) >= 0) {
        return true;
      } else {
        return false;
      }
    };
    refreshCounts = function() {
      return $timeout(function() {
        $('.selectpicker option').each(function(index, el) {
          $(el).data('subtext', $(el).attr('data-subtext'));
          return $(el).data('content', $(el).attr('data-content'));
        });
        return $('.selectpicker').selectpicker('refresh');
      }, 100);
    };
    $scope.filter = function() {
      $.cookie("notifications", JSON.stringify($scope.search), {
        expires: 365,
        path: '/'
      });
      $scope.current_page = 1;
      return $scope.pageChanged();
    };
    $scope.changeState = function(state_id) {
      $rootScope.frontend_loading = true;
      $scope.attachments = [];
      $scope.current_page = 1;
      loadAttachments($scope.current_page);
      return window.history.pushState(state_id, '', 'notifications/' + state_id.toLowerCase());
    };
    $scope.needsCall = function(attachment) {
      var today;
      if (AttachmentService.getState(attachment) !== 'new') {
        return false;
      }
      today = moment().format("YYYY-MM-DD");
      if (attachment.notification_id) {
        return attachment.notification_approved === 0 && attachment.notification_date <= today;
      } else {
        return $scope.addDays(attachment.original_date, 2).format('YYYY-MM-DD') <= today;
      }
    };
    $timeout(function() {
      $scope.search = $.cookie("notifications") ? JSON.parse($.cookie("notifications")) : {};
      loadAttachments($scope.page);
      return $scope.current_page = $scope.page;
    });
    $scope.pageChanged = function() {
      $rootScope.frontend_loading = true;
      $rootScope.attachments = [];
      loadAttachments($scope.current_page);
      return paginate('notifications', $scope.current_page);
    };
    return loadAttachments = function(page) {
      var params;
      params = '?page=' + page;
      return $http.get("api/notifications/get" + params).then(function(response) {
        $scope.data = response.data.data;
        $scope.attachments = response.data.data.data;
        $scope.counts = response.data.counts;
        $rootScope.frontend_loading = false;
        return refreshCounts();
      });
    };
  });

}).call(this);

(function() {
  angular.module('Egerep').controller('PeriodsIndex', function($scope, $timeout, $rootScope, $http, PaymentMethods, DebtTypes, TeacherPaymentTypes, UserService) {
    var getCommission, getPrefix, load;
    bindArguments($scope, arguments);
    $rootScope.frontend_loading = true;
    $timeout(function() {
      load($scope.page);
      return $scope.current_page = $scope.page;
    });
    getPrefix = function() {
      var prefix;
      return prefix = $scope.type === 'total' ? '' : "/" + $scope.type;
    };
    getCommission = function(val) {
      if (val.indexOf('/') !== -1) {
        val = val.split('/')[1];
        if (val) {
          return parseInt(val);
        } else {
          return 0;
        }
      } else {
        return Math.round(parseInt(val) * .25);
      }
    };
    $scope.totalCommission = function(account) {
      var total_commission;
      total_commission = 0;
      $.each(account.data, function(index, account_data) {
        return $.each(account_data, function(index, val) {
          if (val !== '') {
            return total_commission += getCommission(val);
          }
        });
      });
      return total_commission;
    };
    $scope.pageChanged = function() {
      ajaxStart();
      load($scope.current_page);
      return paginate('periods' + getPrefix(), $scope.current_page);
    };
    return load = function(page) {
      var params;
      params = getPrefix();
      params += '?page=' + page;
      return $http.get("api/periods" + params).then(function(response) {
        ajaxEnd();
        $rootScope.frontendStop();
        $scope.data = response.data;
        return $scope.periods = $scope.data.data;
      });
    };
  });

}).call(this);

(function() {
  angular.module('Egerep').controller('RequestsIndex', function($rootScope, $scope, $timeout, $http, Request, RequestStates, Comment, PhoneService, UserService, Grades, Subjects, PusherService) {
    var extendRequestStates, loadRequests;
    bindArguments($scope, arguments);
    _.extend(RequestStates, {
      all: 'все'
    });
    $rootScope.frontend_loading = true;
    $scope.user_id = localStorage.getItem('requests_index_user_id');
    PusherService.bind('RequestUserChanged', function(data) {
      var request;
      if (request = findById($scope.requests, data.request_id)) {
        request.user_id = data.new_user_id;
        return $scope.$apply();
      }
    });
    $scope.howLongAgo = function(created_at) {
      var days, hours, now;
      now = moment(Date.now());
      created_at = moment(new Date(created_at).getTime());
      days = now.diff(created_at, 'days');
      hours = now.diff(created_at, 'hours') - (days * 24);
      return {
        days: days,
        hours: hours
      };
    };
    $scope.changeList = function(state_id) {
      $scope.chosen_state_id = state_id;
      $scope.current_page = 1;
      $rootScope.loaded_comments = 0;
      $rootScope.frontend_loading = true;
      ajaxStart();
      loadRequests(1);
      ajaxEnd();
      return window.history.pushState('requests/' + state_id.toLowerCase(), '', 'requests/' + state_id.toLowerCase());
    };
    extendRequestStates = function() {
      $scope.RequestStatesForTabLabel = angular.copy($scope.RequestStates);
      return _.extend($scope.RequestStatesForTabLabel, {
        all: 'все'
      });
    };
    $timeout(function() {
      extendRequestStates();
      loadRequests($scope.page);
      return $scope.current_page = $scope.page;
    });
    $scope.pageChanged = function() {
      $rootScope.frontend_loading = true;
      $rootScope.loaded_comments = 0;
      loadRequests($scope.current_page);
      return paginate('requests/' + $scope.chosen_state_id, $scope.current_page);
    };
    loadRequests = function(page) {
      var params;
      if (!$scope.chosen_state_id) {
        $scope.chosen_state_id = 'all';
      }
      params = '?page=' + page;
      params += '&state=' + $scope.chosen_state_id;
      if ($scope.user_id !== '') {
        params += "&user_id=" + $scope.user_id;
      }
      $http.get("api/requests" + params).then(function(response) {
        $scope.data = response.data;
        $scope.requests = $scope.data.data;
        $scope.requests.forEach(function(request) {
          return request.client.markers.forEach(function(marker) {
            return marker.metros = _.sortBy(marker.metros, function(s) {
              return s.minutes;
            });
          });
        });
        return $rootScope.frontend_loading = false;
      });
      return $http.post("api/requests/counts", {
        state: $scope.chosen_state_id,
        user_id: $scope.user_id
      }).then(function(response) {
        $scope.request_state_counts = response.data.request_state_counts;
        $scope.user_counts = response.data.user_counts;
        console.log('counts updated');
        return $timeout(function() {
          $('#change-state option, #change-user option').each(function(index, el) {
            $(el).data('subtext', $(el).attr('data-subtext'));
            return $(el).data('content', $(el).attr('data-content'));
          });
          return $('#change-state, #change-user').selectpicker('refresh');
        });
      });
    };
    $scope.hasBannedUsers = function() {
      return _.filter(UserService.getBannedUsers(), function(u) {
        return $scope.user_counts && $scope.user_counts[u.id] !== void 0 && $scope.user_counts[u.id] > 0;
      }).length;
    };
    $scope.changeState = function() {
      localStorage.setItem('requests_index_state', $scope.state);
      return $scope.changeList($scope.state);
    };
    $scope.changeUser = function() {
      localStorage.setItem('requests_index_user_id', $scope.user_id);
      return $scope.changeList($scope.chosen_state_id);
    };
    return $scope.toggleState = function(request) {
      var request_cpy;
      request_cpy = angular.copy(request);
      $rootScope.toggleEnum(request_cpy, 'state', RequestStates);
      return $scope.Request.update({
        id: request_cpy.id,
        state: request_cpy.state
      }, function(response) {
        return $rootScope.toggleEnum(request, 'state', RequestStates);
      });
    };
  }).controller('RequestsForm', function($scope) {
    return console.log('here');
  });

}).call(this);

(function() {
  angular.module('Egerep').controller('ReviewsIndex', function($rootScope, $scope, $timeout, $http, Existance, ReviewStates, Presence, ReviewScores, UserService, ReviewErrors) {
    var load, refreshCounts;
    bindArguments($scope, arguments);
    $rootScope.frontend_loading = true;
    $scope.recalcReviewErrors = function() {
      $scope.review_errors_updating = true;
      return $http.post('api/command/model-errors', {
        model: 'reviews'
      });
    };
    refreshCounts = function() {
      return $timeout(function() {
        $('.selectpicker option').each(function(index, el) {
          $(el).data('subtext', $(el).attr('data-subtext'));
          return $(el).data('content', $(el).attr('data-content'));
        });
        return $('.selectpicker').selectpicker('refresh');
      }, 100);
    };
    $scope.filter = function() {
      $.cookie("reviews", JSON.stringify($scope.search), {
        expires: 365,
        path: '/'
      });
      $scope.current_page = 1;
      return $scope.pageChanged();
    };
    $timeout(function() {
      $scope.search = $.cookie("reviews") ? JSON.parse($.cookie("reviews")) : {};
      load($scope.page);
      return $scope.current_page = $scope.page;
    });
    $scope.pageChanged = function() {
      $rootScope.frontend_loading = true;
      load($scope.current_page);
      return paginate('reviews', $scope.current_page);
    };
    return load = function(page) {
      var params;
      params = '?page=' + page;
      return $http.get("api/reviews" + params).then(function(response) {
        console.log(response);
        $scope.counts = response.data.counts;
        $scope.data = response.data.data;
        $scope.attachments = response.data.data.data;
        $rootScope.frontend_loading = false;
        return refreshCounts();
      });
    };
  }).controller('TutorReviews', function($rootScope, $scope, $timeout, $http, Existance, ReviewStates, Presence, ReviewScores, UserService, ReviewErrors) {
    var load, refreshCounts;
    bindArguments($scope, arguments);
    $rootScope.frontend_loading = true;
    $scope.recalcReviewErrors = function() {
      $scope.review_errors_updating = true;
      return $http.post('api/command/model-errors', {
        model: 'reviews'
      });
    };
    refreshCounts = function() {
      return $timeout(function() {
        $('.selectpicker option').each(function(index, el) {
          $(el).data('subtext', $(el).attr('data-subtext'));
          return $(el).data('content', $(el).attr('data-content'));
        });
        return $('.selectpicker').selectpicker('refresh');
      }, 100);
    };
    $scope.filter = function() {
      $.cookie("tutor_reviews", JSON.stringify($scope.search), {
        path: '/'
      });
      $scope.current_page = 1;
      return $scope.pageChanged();
    };
    $scope.pageChanged = function() {
      $rootScope.frontend_loading = true;
      load($scope.current_page);
      return paginate('reviews/' + $scope.tutor_id, $scope.current_page);
    };
    load = function(page) {
      var params;
      params = '?page=' + page + '&tutor_id=' + $scope.tutor_id;
      return $http.get("api/reviews" + params).then(function(response) {
        $scope.counts = response.data.counts;
        $scope.data = response.data.data;
        $scope.attachments = response.data.data.data;
        $rootScope.frontend_loading = false;
        return refreshCounts();
      });
    };
    return angular.element(document).ready(function() {
      $scope.search = {
        tutor_id: $scope.tutor_id
      };
      $.cookie('tutor_reviews', JSON.stringify($scope.search), {
        path: '/'
      });
      load($scope.page);
      return $scope.current_page = $scope.page;
    });
  });

}).call(this);

(function() {
  Vue.config.devtools = true;

  $(document).ready(function() {
    var viewVue;
    $('#searchModalOpen').click(function() {
      var delayFunction;
      $('#searchModal').modal({
        keyboard: true
      });
      delayFunction = function() {
        return $('#searchQueryInput').focus();
      };
      setTimeout(delayFunction, 500);
      $($('body.modal-open .row')[0]).addClass('blur');
      return false;
    });
    $('#searchModal').on('hidden.bs.modal', function() {
      var delayFnc;
      delayFnc = function() {
        return $('.blur').removeClass('blur');
      };
      return setTimeout(delayFnc, 500);
    });
    return viewVue = new Vue({
      el: '#searchModal',
      data: {
        lists: [],
        links: {},
        results: -1,
        active: 0,
        query: '',
        oldquery: '',
        all: 0,
        loading: false
      },
      methods: {
        loadData: _.debounce(function() {
          return this.$http.post('api/search', {
            query: this.query
          }).then((function(_this) {
            return function(success) {
              var i, item, j, k, len, len1, ref, ref1, results;
              _this.loading = false;
              _this.active = 0;
              _this.all = 0;
              _this.lists = [];
              if (success.body.results > 0) {
                _this.results = success.body.results;
                if (success.body.clients.length > 0) {
                  ref = success.body.clients;
                  for (i = j = 0, len = ref.length; j < len; i = ++j) {
                    item = ref[i];
                    item.type = 'clients';
                    _this.all++;
                    _this.links[_this.all] = 'client/' + item.id;
                    item.link = _this.links[_this.all];
                    _this.lists.push(item);
                  }
                }
                if (success.body.tutors.length > 0) {
                  ref1 = success.body.tutors;
                  results = [];
                  for (i = k = 0, len1 = ref1.length; k < len1; i = ++k) {
                    item = ref1[i];
                    item.type = 'tutors';
                    _this.all++;
                    _this.links[_this.all] = 'tutors/' + item.id + '/edit';
                    item.link = _this.links[_this.all];
                    results.push(_this.lists.push(item));
                  }
                  return results;
                }
              } else {
                _this.active = 0;
                _this.all = 0;
                _this.lists = [];
                return _this.results = 0;
              }
            };
          })(this), (function(_this) {
            return function(error) {
              _this.active = 0;
              _this.all = 0;
              _this.lists = [];
              return _this.results = 0;
            };
          })(this));
        }, 150),
        scroll: function() {
          return $('#searchResult').scrollTop((this.active - 4) * 30);
        },
        getStateClass: function(state) {
          var obj;
          obj = {};
          obj["tutor-state-" + state] = true;
          return obj;
        },
        keyup: function(e) {
          var url;
          if (e.code === 'ArrowUp') {
            e.preventDefault();
            if (this.active > 0) {
              this.active--;
            }
            this.scroll();
          } else if (e.code === 'ArrowDown') {
            e.preventDefault();
            if (this.active < this.results) {
              this.active++;
            }
            if (this.active > 4) {
              this.scroll();
            }
          } else if (e.code === 'Enter' && this.active > 0) {
            url = this.links[this.active];
            if (url.indexOf('tutors') === 0) {
              window.open(url, '_blank');
            } else {
              window.location = url;
            }
          } else {
            if (this.query !== '') {
              if (this.oldquery !== this.query && this.query.length > 2) {
                this.loadData();
              }
              this.oldquery = this.query;
            } else {
              this.active = 0;
              this.all = 0;
              this.lists = [];
              this.results = -1;
            }
          }
          return null;
        }
      }
    });
  });

}).call(this);

(function() {
  angular.module('Egerep').controller('StreamIndex', function($rootScope, $scope, $timeout, $http, Subjects) {
    var load, refreshCounts;
    bindArguments($scope, arguments);
    $rootScope.frontend_loading = true;
    refreshCounts = function() {
      return $timeout(function() {
        $('.selectpicker option').each(function(index, el) {
          $(el).data('subtext', $(el).attr('data-subtext'));
          return $(el).data('content', $(el).attr('data-content'));
        });
        return $('.selectpicker').selectpicker('refresh');
      }, 100);
    };
    $scope.filter = function() {
      $.cookie("stream", JSON.stringify($scope.search), {
        expires: 365,
        path: '/'
      });
      $scope.current_page = 1;
      return $scope.pageChanged();
    };
    $timeout(function() {
      $scope.search = $.cookie("stream") ? JSON.parse($.cookie("stream")) : {};
      load($scope.page);
      return $scope.current_page = $scope.page;
    });
    $scope.pageChanged = function() {
      $rootScope.frontend_loading = true;
      load($scope.current_page);
      return paginate('stream', $scope.current_page);
    };
    return load = function(page) {
      var params;
      params = '?page=' + page;
      return $http.get("api/stream" + params).then(function(response) {
        console.log(response);
        $scope.counts = response.data.counts;
        $scope.data = response.data.data;
        $scope.stream = response.data.data.data;
        $rootScope.frontend_loading = false;
        return refreshCounts();
      });
    };
  }).controller('StreamConfigurations', function($rootScope, $scope, $timeout, $http, Subjects) {});

}).call(this);

(function() {
  angular.module('Egerep').controller('SummaryUsers', function($scope, $rootScope, $timeout, $http, UserService, RequestStates, AttachmentService) {
    bindArguments($scope, arguments);
    $timeout(function() {
      $scope.search = {};
      if (!$scope.allowed_all) {
        $scope.search.user_ids = [$scope.user.id.toString()];
      }
      if (!$scope.search.type) {
        $scope.search.type = 'months';
      }
      return $timeout(function() {
        return $('#change-user, #change-type').selectpicker('refresh');
      });
    }, 500);
    $scope.update = function() {
      $rootScope.frontend_loading = true;
      return $http.post('api/summary/users', $scope.search).then(function(response) {
        $rootScope.frontend_loading = false;
        return $scope.stats = response.data;
      });
    };
    $scope.getExplanation = function() {
      $rootScope.explaination_loading = true;
      return $http.post('api/summary/users/explain', $scope.search).then(function(response) {
        $rootScope.explaination_loading = false;
        return $scope.stats.efficency = response.data;
      });
    };
    $scope.monthYear = function(date) {
      date = date.split(".");
      date = date.reverse();
      date = date.join("-");
      return moment(date).format('MMMM YYYY');
    };
    $scope.sumEfficency = function() {
      var sum;
      sum = _.reduce($scope.stats.efficency, function(sum, request) {
        _.each(request.attachments, function(attachment) {
          return sum += attachment.rate;
        });
        return sum;
      }, 0);
      return sum.toFixed(2);
    };
    $scope.sumShare = function() {
      var sum;
      sum = _.reduce($scope.stats.efficency, function(sum, request) {
        if (request.attachments.length) {
          _.each(request.attachments, function(attachment) {
            return sum += attachment.share;
          });
        }
        if ($scope.isDenied(request)) {
          sum += 1;
        }
        return sum;
      }, 0);
      return sum.toFixed(2);
    };
    return $scope.isDenied = function(request) {
      var ref;
      return (ref = request.state) === 'deny';
    };
  }).controller('SummaryIndex', function($rootScope, $scope, $http, $timeout, PaymentMethods) {
    var getPrefix, loadSummary;
    bindArguments($scope, arguments);
    $rootScope.frontend_loading = true;
    $scope.debt_updating = false;
    $scope.updateDebt = function() {
      $scope.debt_updating = true;
      return $http.post('api/command/recalc-debt').then(function(response) {
        $scope.debt_updating = false;
        $scope.debt_updated = response.data.debt_updated;
        return $scope.debt_sum = response.data.debt_sum;
      });
    };
    $timeout(function() {
      loadSummary($scope.page);
      return $scope.current_page = $scope.page;
    });
    getPrefix = function() {
      var prefix;
      return prefix = $scope.type === 'total' ? '' : "/" + $scope.type;
    };
    $scope.pageChanged = function() {
      ajaxStart();
      loadSummary($scope.current_page);
      return paginate('summary' + getPrefix() + '/' + $scope.filter, $scope.current_page);
    };
    $scope.updateDebt = function() {
      $scope.debt_updating = 1;
      return $http.post('api/command/recalc-debt');
    };
    return loadSummary = function(page) {
      var params;
      params = getPrefix();
      params += '?page=' + page;
      params += '&filter=' + $scope.filter;
      return $http.post("api/summary" + params).then(function(response) {
        ajaxEnd();
        $rootScope.frontendStop();
        return $scope.summaries = response.data;
      });
    };
  });

}).call(this);

(function() {
  angular.module('Egerep').controller('TemplateIndex', function($rootScope, $scope, $http, Request) {
    $scope.form_changed = false;
    $scope.save = function() {
      return $http.post("templates", {
        allTemplates: $scope.allTemplates
      }).then(function(success) {
        return $scope.form_changed = false;
      }, function(error) {
        console.error(error);
        return $scope.form_changed = false;
      });
    };
    return angular.element(document).ready(function() {
      return $(".checkChange").on('keyup change', 'input, select, textarea', function() {
        $scope.form_changed = true;
        return $scope.$apply();
      });
    });
  });

}).call(this);

(function() {
  var indexOf = [].indexOf || function(item) { for (var i = 0, l = this.length; i < l; i++) { if (i in this && this[i] === item) return i; } return -1; };

  angular.module('Egerep').controller('TutorsSelect', function($scope, Genders, Grades, Subjects, TutorStates, Destinations, TutorService, PhoneService, RequestList, YesNo, Tutor) {
    var TRANSPARENT_MARKER, bindTutorMarkerEvents, clicks, markerClusterer, rebindDraggable, repaintChosen, showTutorsOnMap, unsetAllMarkers;
    bindArguments($scope, arguments);
    TRANSPARENT_MARKER = 0.3;
    clicks = 0;
    markerClusterer = void 0;
    $scope.mode = 'map';
    $scope.loading = false;
    $scope.tutors = [];
    $scope.list_tutor_ids = [];
    angular.element(document).ready(function() {
      $scope.list = new RequestList($scope.list);
      return $('.map-tutor-list').droppable();
    });
    $scope.blurComment = function(tutor) {
      tutor.is_being_commented = false;
      return tutor.ready_to_work = tutor.old_ready_to_work;
    };
    $scope.focusComment = function(tutor) {
      tutor.is_being_commented = true;
      return tutor.old_ready_to_work = tutor.ready_to_work;
    };
    $scope.startComment = function(tutor) {
      tutor.is_being_commented = true;
      tutor.old_ready_to_work = tutor.ready_to_work;
      return $timeout(function() {
        return $("#list-comment-" + tutor.id).focus();
      });
    };
    $scope.saveComment = function(event, tutor) {
      if (event.keyCode === 13) {
        return Tutor.update({
          id: tutor.id,
          ready_to_work: tutor.ready_to_work
        }, function(response) {
          tutor.old_ready_to_work = tutor.ready_to_work;
          return $(event.target).blur();
        });
      }
    };
    $scope.getHours = function(minutes) {
      return Math.floor(minutes / 60);
    };
    $scope.getMinutes = function(minutes) {
      return minutes % 60;
    };
    $scope.find = function() {
      $scope.loading = true;
      return TutorService.select({
        search: $scope.search
      }).then(function(response) {
        $scope.tutors = response.data;
        showTutorsOnMap();
        repaintChosen();
        return $scope.loading = false;
      });
    };
    $scope.added = function(tutor_id) {
      return indexOf.call($scope.list_tutor_ids.map(Number), tutor_id) >= 0;
    };
    rebindDraggable = function() {
      return $('.temporary-tutor').draggable({
        containment: 'window',
        revert: function(valid) {
          if (valid) {
            return true;
          }
          $scope.tutor_list = removeById($scope.tutor_list, $scope.dragging_tutor.id);
          return $scope.$apply();
        }
      });
    };
    $scope.startDragging = function(tutor) {
      return $scope.dragging_tutor = tutor;
    };
    showTutorsOnMap = function() {
      unsetAllMarkers();
      $scope.marker_id = 1;
      $scope.tutor_list = [];
      $scope.markers = [];
      $scope.tutors.forEach(function(tutor) {
        if (tutor.markers !== void 0) {
          return tutor.markers.forEach(function(marker) {
            var new_marker;
            new_marker = newMarker($scope.marker_id++, new google.maps.LatLng(marker.lat, marker.lng), $scope.map, marker.type);
            new_marker.metros = marker.metros;
            new_marker.tutor = tutor;
            new_marker.setMap($scope.map);
            bindTutorMarkerEvents(new_marker);
            return $scope.markers.push(new_marker);
          });
        }
      });
      return markerClusterer = new MarkerClusterer($scope.map, $scope.markers, {
        gridSize: 10,
        imagePath: 'img/maps/clusterer/m'
      });
    };
    unsetAllMarkers = function() {
      if ($scope.markers !== void 0) {
        $scope.markers.forEach(function(marker) {
          return marker.setMap(null);
        });
      }
      if (markerClusterer !== void 0) {
        return markerClusterer.clearMarkers();
      }
    };
    bindTutorMarkerEvents = function(marker) {
      google.maps.event.addListener(marker, 'click', function(event) {
        clicks++;
        if (clicks === 1) {
          return setTimeout(function() {
            var ref;
            if (clicks === 1) {
              if (ref = marker.tutor, indexOf.call($scope.tutor_list, ref) >= 0) {
                $scope.tutor_list = removeById($scope.tutor_list, marker.tutor.id);
              } else {
                $scope.hovered_tutor = null;
                $scope.tutor_list.push(marker.tutor);
              }
              $scope.$apply();
              rebindDraggable();
            } else {
              $scope.addOrRemove(marker.tutor.id);
            }
            return clicks = 0;
          }, 250);
        }
      });
      google.maps.event.addListener(marker, 'dblclick', function(event) {
        return clicks++;
      });
      google.maps.event.addListener(marker, 'mouseover', function(event) {
        var ref;
        if (ref = marker.tutor, indexOf.call($scope.tutor_list, ref) >= 0) {
          return;
        }
        $scope.hovered_tutor = marker.tutor;
        return $scope.$apply();
      });
      return google.maps.event.addListener(marker, 'mouseout', function(event) {
        $scope.hovered_tutor = null;
        return $scope.$apply();
      });
    };
    $scope.addOrRemove = function(tutor_id) {
      tutor_id = parseInt(tutor_id);
      if (indexOf.call($scope.list_tutor_ids.map(Number), tutor_id) >= 0) {
        $scope.list_tutor_ids = _.without($scope.list_tutor_ids.map(Number), tutor_id);
      } else {
        $scope.list_tutor_ids.push(tutor_id);
      }
      repaintChosen();
      return $scope.list.$update();
    };
    repaintChosen = function() {
      return $scope.markers.forEach(function(marker) {
        var ref, ref1;
        if ((ref = marker.tutor.id, indexOf.call($scope.list_tutor_ids.map(Number), ref) >= 0) && !marker.chosen) {
          marker.chosen = true;
          marker.setOpacity(1);
          marker.setIcon(ICON_BLUE);
        }
        if ((ref1 = marker.tutor.id, indexOf.call($scope.list_tutor_ids.map(Number), ref1) < 0) && marker.chosen) {
          marker.chosen = false;
          return marker.setIcon(getMarkerType(marker.type));
        }
      });
    };
    return $scope.$on('mapInitialized', function(event, map) {
      var INIT_COORDS;
      $scope.gmap = map;
      INIT_COORDS = {
        lat: 55.7387,
        lng: 37.6032
      };
      $scope.RECOM_BOUNDS = new google.maps.LatLngBounds(new google.maps.LatLng(INIT_COORDS.lat - 0.5, INIT_COORDS.lng - 0.5), new google.maps.LatLng(INIT_COORDS.lat + 0.5, INIT_COORDS.lng + 0.5));
      $scope.geocoder = new google.maps.Geocoder;
      $scope.gmap.setCenter(new google.maps.LatLng(55.7387, 37.6032));
      return $scope.gmap.setZoom(11);
    });
  });

}).call(this);

(function() {
  angular.module('Egerep').controller("TutorsIndex", function($scope, $rootScope, $timeout, $http, Tutor, TutorStates, UserService, PusherService, TutorPublishedStates, TutorErrors, PhoneFields) {
    var loadTutors;
    bindArguments($scope, arguments);
    $rootScope.frontend_loading = true;
    $scope.recalcTutorErrors = function() {
      $scope.tutor_errors_updating = true;
      return $http.post('api/command/model-errors', {
        model: 'tutors'
      });
    };
    $scope.state = localStorage.getItem('tutors_index_state');
    $scope.user_id = localStorage.getItem('tutors_index_user_id');
    $scope.published_state = localStorage.getItem('tutors_index_published_state');
    $scope.errors_state = localStorage.getItem('tutors_index_errors_state');
    $scope.egecentr_source = localStorage.getItem('tutors_index_egecentr_source');
    PusherService.bind('ResponsibleUserChanged', function(data) {
      var tutor;
      if (tutor = findById($scope.tutors, data.tutor_id)) {
        tutor.responsible_user_id = data.responsible_user_id;
        return $scope.$apply();
      }
    });
    $scope.duplicateClick = function(phone) {
      $scope.global_search = phone;
      return $timeout(function() {
        return $('#global-search').submit();
      });
    };
    $scope.yearDifference = function(year) {
      return moment().format("YYYY") - year;
    };
    $scope.changeState = function() {
      localStorage.setItem('tutors_index_state', $scope.state);
      return loadTutors($scope.current_page);
    };
    $scope.changeUser = function() {
      localStorage.setItem('tutors_index_user_id', $scope.user_id);
      return loadTutors($scope.current_page);
    };
    $scope.changePublishedSate = function() {
      localStorage.setItem('tutors_index_published_state', $scope.published_state);
      return loadTutors($scope.current_page);
    };
    $scope.changeErrorsState = function() {
      localStorage.setItem('tutors_index_errors_state', $scope.errors_state);
      return loadTutors($scope.current_page);
    };
    $scope.changeSource = function() {
      localStorage.setItem('tutors_index_egecentr_source', $scope.egecentr_source);
      return loadTutors($scope.current_page);
    };
    $timeout(function() {
      loadTutors($scope.page);
      return $scope.current_page = $scope.page;
    });
    $scope.pageChanged = function() {
      loadTutors($scope.current_page);
      return paginate('tutors', $scope.current_page);
    };
    loadTutors = function(page) {
      var params;
      $rootScope.frontend_loading = true;
      params = '?page=' + page;
      if ($scope.global_search) {
        params += "&global_search=" + $scope.global_search;
      }
      if ($scope.state !== null && $scope.state !== '') {
        params += "&state=" + $scope.state;
      }
      if ($scope.user_id) {
        params += "&user_id=" + $scope.user_id;
      }
      if ($scope.published_state !== null && $scope.published_state !== '') {
        params += "&published_state=" + $scope.published_state;
      }
      if ($scope.errors_state !== null && $scope.errors_state !== '') {
        params += "&errors_state=" + $scope.errors_state;
      }
      if ($scope.egecentr_source !== null && $scope.egecentr_source !== '') {
        params += "&egecentr_source=" + $scope.egecentr_source;
      }
      $http.get("api/tutors" + params).then(function(response) {
        $rootScope.frontendStop();
        $scope.data = response.data;
        return $scope.tutors = $scope.data.data;
      });
      return $http.post("api/tutors/counts", {
        state: $scope.state,
        user_id: $scope.user_id,
        published_state: $scope.published_state,
        errors_state: $scope.errors_state,
        egecentr_source: $scope.egecentr_source
      }).then(function(response) {
        $scope.state_counts = response.data.state_counts;
        $scope.user_counts = response.data.user_counts;
        $scope.published_counts = response.data.published_counts;
        $scope.errors_counts = response.data.errors_counts;
        $scope.source_counts = response.data.source_counts;
        return $timeout(function() {
          $('#change-state option, #change-user option, #change-published option, #change-errors option, #change-source option').each(function(index, el) {
            $(el).data('subtext', $(el).attr('data-subtext'));
            return $(el).data('content', $(el).attr('data-content'));
          });
          $('#change-state, #change-user, #change-published, #change-errors, #change-source').selectpicker('refresh');
          return $rootScope.frontend_loading = false;
        });
      });
    };
    $scope.blurComment = function(tutor) {
      tutor.is_being_commented = false;
      return tutor.list_comment = tutor.old_list_comment;
    };
    $scope.focusComment = function(tutor) {
      tutor.is_being_commented = true;
      return tutor.old_list_comment = tutor.list_comment;
    };
    $scope.startComment = function(tutor) {
      tutor.is_being_commented = true;
      tutor.old_list_comment = tutor.list_comment;
      return $timeout(function() {
        return $("#list-comment-" + tutor.id).focus();
      });
    };
    return $scope.saveComment = function(event, tutor) {
      if (event.keyCode === 13) {
        return Tutor.update({
          id: tutor.id,
          list_comment: tutor.list_comment
        }, function(response) {
          tutor.old_list_comment = tutor.list_comment;
          return $(event.target).blur();
        });
      }
    };
  }).controller("TutorsForm", function($scope, $rootScope, $timeout, Tutor, SvgMap, Subjects, Grades, ApiService, TutorStates, Genders, Workplaces, Branches, BranchService, TutorService, $http, Marker) {
    var bindCropper, bindFileUpload, filterMarkers;
    bindArguments($scope, arguments);
    $rootScope.frontend_loading = true;
    $scope.form_changed = false;
    $scope.fully_loaded = false;
    $scope.mergeTutor = function() {
      return $('#merge-tutor').modal('show');
    };
    $scope.mergeTutorGo = function() {
      $('#merge-tutor').modal('hide');
      return $http.post("api/tutors/merge", {
        tutor_id: $scope.tutor.id,
        new_tutor_id: $scope.new_tutor_id
      }).then(function(response) {
        if (response.data === 'false') {
          return bootbox.alert('Существуют задублированные клиенты');
        } else {
          return bootbox.alert('Информация перенесена');
        }
      });
    };
    $scope.deleteTutor = function() {
      return bootbox.confirm('Вы уверены, что хотите удалить преподавателя?', function(result) {
        if (result === true) {
          ajaxStart();
          return $scope.tutor.$delete(function() {
            return history.back();
          });
        }
      });
    };
    $scope.shortenGrades = function() {
      var a, combo_end, combo_start, i, j, limit, pairs;
      a = $scope.tutor.grades;
      if (a.length < 1) {
        return;
      }
      limit = a.length - 1;
      combo_end = -1;
      pairs = [];
      i = 0;
      while (i <= limit) {
        combo_start = parseInt(a[i]);
        if (combo_start > 11) {
          i++;
          combo_end = -1;
          pairs.push(Grades[combo_start]);
          continue;
        }
        if (combo_start <= combo_end) {
          i++;
          continue;
        }
        j = i;
        while (j <= limit) {
          combo_end = parseInt(a[j]);
          if (combo_end >= 11) {
            break;
          }
          if (parseInt(a[j + 1]) - combo_end > 1) {
            break;
          }
          j++;
        }
        if (combo_start !== combo_end) {
          pairs.push(combo_start + '–' + combo_end + ' классы');
        } else {
          pairs.push(combo_start + ' класс');
        }
        i++;
      }
      $timeout(function() {
        return $('#sp-tutor-grades').parent().find('.filter-option').html(pairs.join(', '));
      });
    };
    $scope.deletePhoto = function() {
      return bootbox.confirm('Удалить фото преподавателя?', function(result) {
        if (result === true) {
          return $scope.tutor.$deletePhoto(function() {
            $scope.tutor.has_photo_cropped = false;
            return $scope.tutor.has_photo_original = false;
          });
        }
      });
    };
    $scope.saveCropped = function() {
      return $('#photo-edit').cropper('getCroppedCanvas').toBlob(function(blob) {
        var formData;
        formData = new FormData;
        formData.append('croppedImage', blob);
        formData.append('tutor_id', $scope.tutor.id);
        ajaxStart();
        return $.ajax('upload/cropped', {
          method: 'POST',
          data: formData,
          processData: false,
          contentType: false,
          dataType: 'json',
          success: function(response) {
            ajaxEnd();
            $scope.tutor.has_photo_cropped = true;
            $scope.photo_cropped_size = response;
            $scope.picture_version++;
            $scope.$apply();
            return $scope.closeDialog('change-photo');
          }
        });
      });
    };
    bindCropper = function() {
      $('#photo-edit').cropper('destroy');
      return $('#photo-edit').cropper({
        aspectRatio: 4 / 5,
        preview: '.img-preview',
        viewMode: 1,
        zoomable: false,
        zoomOnWheel: false,
        crop: function(e) {
          var quality, width;
          width = $('#photo-edit').cropper('getCroppedCanvas').width;
          quality = Math.round(width / 240 * 100);
          $scope.quality = quality > 100 ? 100 : quality;
          return $scope.$apply();
        },
        built: function() {
          $scope.cropper_built = true;
          return $scope.$apply();
        }
      });
    };
    $scope.picture_version = 1;
    bindFileUpload = function() {
      return $('#fileupload').fileupload({
        formData: {
          tutor_id: $scope.tutor.id
        },
        maxFileSize: 10000000,
        send: function() {
          return NProgress.configure({
            showSpinner: true
          });
        },
        progress: function(e, data) {
          return NProgress.set(data.loaded / data.total);
        },
        always: function() {
          NProgress.configure({
            showSpinner: false
          });
          return ajaxEnd();
        },
        done: function(i, response) {
          $scope.tutor.photo_extension = response.result.extension;
          $scope.tutor.photo_original_size = response.result.size;
          $scope.tutor.photo_cropped_size = 0;
          $scope.tutor.has_photo_original = true;
          $scope.tutor.has_photo_cropped = false;
          $scope.picture_version++;
          $scope.$apply();
          return bindCropper();
        }
      });
    };
    $scope.showPhotoEditor = function() {
      $scope.dialog('change-photo');
      $scope.cropper_built = false;
      return bindCropper();
    };
    $scope.toggleBanned = function() {
      return $scope.tutor.banned = +(!$scope.tutor.banned);
    };
    $timeout(function() {
      if ($scope.id > 0) {
        return $scope.tutor = Tutor.get({
          id: $scope.id
        }, function() {
          $timeout(function() {
            return bindFileUpload();
          }, 1000);
          $scope.original_tutor = angular.copy($scope.tutor);
          return $rootScope.frontendStop();
        });
      } else {
        $scope.tutor = TutorService.default_tutor;
        return $rootScope.frontendStop();
      }
    });
    $scope.$watch('tutor.subjects', function(newVal, oldVal) {
      if (newVal === void 0) {
        return;
      }
      if (oldVal === void 0) {
        sp('tutor-subjects', 'предмет', '+');
      }
      if (oldVal !== void 0) {
        return spRefresh('tutor-subjects');
      }
    });
    $scope.$watch('tutor.grades', function(newVal, oldVal) {
      if (newVal === void 0) {
        return;
      }
      if (oldVal === void 0) {
        sp('tutor-grades', 'классы');
        return $timeout(function() {
          return $scope.shortenGrades();
        }, 50);
      } else {
        return $timeout(function() {
          return $scope.shortenGrades();
        });
      }
    });
    $scope.$watch('tutor.branches', function(newVal, oldVal) {
      if (newVal === void 0) {
        return;
      }
      if (oldVal === void 0) {
        sp('tutor-branches', 'филиалы', ' ');
      }
      if (oldVal !== void 0) {
        return spRefresh('tutor-branches');
      }
    });
    $scope.$watchCollection('tutor', function(newVal, oldVal) {
      if ($scope.fully_loaded) {
        return $scope.form_changed = true;
      }
    });
    $scope.$watch('tutor.in_egecentr', function(newVal, oldVal) {
      if (newVal && !$scope.tutor.login && $scope.tutor.first_name && $scope.tutor.last_name && $scope.tutor.middle_name) {
        $scope.tutor.login = TutorService.generateLogin($scope.tutor);
        return $scope.tutor.password = TutorService.generatePassword();
      }
    });
    $scope.svgSave = function() {
      $('#svg-modal').modal('hide');
    };
    $scope.yearDifference = function(year) {
      return moment().format("YYYY") - year;
    };
    $scope.add = function() {
      $scope.saving = true;
      return Tutor.save($scope.tutor, function(tutor) {
        return window.location = "tutors/" + tutor.id + "/edit";
      });
    };
    $scope.edit = function() {
      ajaxStart();
      $scope.saving = true;
      filterMarkers();
      return $scope.tutor.$update().then(function(response) {
        $scope.tutor = response;
        $scope.loadMarkers();
        $scope.saving = false;
        $scope.form_changed = false;
        return ajaxEnd();
      });
    };
    $scope.marker_id = 1;
    filterMarkers = function() {
      var new_markers;
      new_markers = [];
      $.each($scope.tutor.markers, function(index, marker) {
        return new_markers.push(_.pick(marker, 'lat', 'lng', 'type', 'metros', 'server_id'));
      });
      return $scope.tutor.markers = new_markers;
    };
    $scope.$on('mapInitialized', function(event, map) {
      var INIT_COORDS;
      $scope.gmap = map;
      $scope.loadMarkers();
      INIT_COORDS = {
        lat: 55.7387,
        lng: 37.6032
      };
      $scope.RECOM_BOUNDS = new google.maps.LatLngBounds(new google.maps.LatLng(INIT_COORDS.lat - 0.5, INIT_COORDS.lng - 0.5), new google.maps.LatLng(INIT_COORDS.lat + 0.5, INIT_COORDS.lng + 0.5));
      $scope.geocoder = new google.maps.Geocoder;
      return google.maps.event.addListener(map, 'click', function(event) {
        return $scope.gmapAddMarker(event);
      });
    });
    $scope.showMap = function() {
      var bounds, markers_count;
      $('#gmap-modal').modal('show');
      google.maps.event.trigger($scope.gmap, 'resize');
      $scope.gmap.setCenter(new google.maps.LatLng(55.7387, 37.6032));
      $scope.gmap.setZoom(11);
      $('#map-search').val('');
      if ($scope.search_markers && $scope.search_markers.length) {
        $.each($scope.search_markers, function(i, marker) {
          return marker.setMap(null);
        });
        $scope.search_markers = [];
      }
      if ($scope.tutor.markers.length) {
        bounds = new google.maps.LatLngBounds;
        markers_count = 0;
        $.each($scope.tutor.markers, function(index, marker) {
          markers_count++;
          return bounds.extend(marker.position);
        });
        if (markers_count > 0) {
          $scope.gmap.fitBounds(bounds);
          $scope.gmap.panToBounds(bounds);
          return $scope.gmap.setZoom(11);
        }
      }
    };
    $scope.gmapAddMarker = function(event) {
      var marker;
      marker = newMarker($scope.marker_id++, event.latLng, $scope.map);
      $scope.tutor.markers.push(marker);
      marker.setMap($scope.gmap);
      ApiService.metro('closest', {
        lat: marker.lat,
        lng: marker.lng
      }).then(function(response) {
        return marker.metros = response.data;
      });
      $scope.bindMarkerDelete(marker);
      return $scope.bindMarkerChangeType(marker);
    };
    $scope.bindMarkerDelete = function(marker) {
      return google.maps.event.addListener(marker, 'dblclick', function(event) {
        var t;
        t = this;
        t.setMap(null);
        return $.each($scope.tutor.markers, function(index, m) {
          console.log('id', t.id, m.id);
          if (m !== void 0 && t.id === m.id) {
            if (m.server_id !== void 0) {
              return Marker["delete"]({
                id: m.server_id
              }, function() {
                return $scope.tutor.markers.splice(index, 1);
              });
            } else {
              return $scope.tutor.markers.splice(index, 1);
            }
          }
        });
      });
    };
    $scope.bindMarkerChangeType = function(marker) {
      return google.maps.event.addListener(marker, 'click', function(event) {
        var gmap, icon_to_set;
        if (this.type === 'green') {
          this.type = 'red';
          icon_to_set = ICON_RED;
        } else {
          this.type = 'green';
          icon_to_set = ICON_GREEN;
        }
        gmap = this;
        if (marker.server_id !== void 0) {
          return Marker.update({
            id: marker.server_id,
            type: this.type
          }, function() {
            return gmap.setIcon(icon_to_set);
          });
        } else {
          return gmap.setIcon(icon_to_set);
        }
      });
    };
    $scope.searchMap = function(address) {
      return $scope.geocoder.geocode({
        address: address + ', московская область',
        bounds: $scope.RECOM_BOUNDS,
        componentRestrictions: {
          country: 'RU',
          administrativeArea: 'Moscow'
        }
      }, function(results, status) {
        var max_results, search_result_bounds;
        if (status === google.maps.GeocoderStatus.OK) {
          max_results = 3;
          search_result_bounds = new google.maps.LatLngBounds;
          $.each(results, function(i, result) {
            var search_marker;
            if (i >= max_results) {
              return;
            }
            search_result_bounds.extend(result.geometry.location);
            search_marker = new google.maps.Marker({
              map: $scope.map,
              position: result.geometry.location,
              icon: ICON_SEARCH
            });
            google.maps.event.addListener(search_marker, 'click', function(event) {
              this.setMap(null);
              return $scope.gmapAddMarker(event);
            });
            $scope.search_markers = initIfNotSet($scope.search_markers);
            return $scope.search_markers.push(search_marker);
          });
          if (results.length > 0) {
            $scope.gmap.fitBounds(search_result_bounds);
            $scope.gmap.panToBounds(search_result_bounds);
            if (results.length === 1) {
              return $scope.gmap.setZoom(12);
            }
          } else {
            return $('#map-search').addClass('has-error').focus();
          }
        }
      });
    };
    $scope.gmapsSearch = function($event) {
      if ($event.keyCode === 13 || $event.type === 'click') {
        if ($('#map-search').val() === '') {
          $('#map-search').addClass('has-error').focus();
        } else {
          $('#map-search').removeClass('has-error');
        }
        return $scope.searchMap($('#map-search').val());
      }
    };
    $scope.loadMarkers = function() {
      return $rootScope.dataLoaded.promise.then(function() {
        var markers;
        markers = [];
        $.each($scope.tutor.markers, function(index, marker) {
          var new_marker;
          new_marker = newMarker($scope.marker_id++, new google.maps.LatLng(marker.lat, marker.lng), $scope.map, marker.type, marker.id);
          new_marker.metros = marker.metros;
          new_marker.setMap($scope.map);
          $scope.bindMarkerDelete(new_marker);
          $scope.bindMarkerChangeType(new_marker);
          return markers.push(new_marker);
        });
        $scope.tutor.markers = _.sortBy(markers, function(marker) {
          return marker.server_id;
        });
        return $timeout(function() {
          return $scope.fully_loaded = true;
        });
      });
    };
    return $scope.saveMarkers = function() {
      $scope.form_changed = true;
      $('#gmap-modal').modal('hide');
      filterMarkers();
    };
  });

}).call(this);

(function() {
  angular.module('Egerep').directive('comments', function() {
    return {
      restrict: 'E',
      templateUrl: 'directives/comments',
      scope: {
        user: '=',
        entityId: '=',
        trackLoading: '=',
        entityType: '@'
      },
      controller: function($rootScope, $scope, $timeout, Comment, UserService) {
        var focusModal;
        $scope.UserService = UserService;
        $scope.show_max = 4;
        $scope.show_all_comments = false;
        $scope.showAllComments = function() {
          $scope.show_all_comments = true;
          return focusModal();
        };
        $scope.getComments = function() {
          if ($scope.show_all_comments || $scope.comments.length <= $scope.show_max) {
            return $scope.comments;
          } else {
            return _.last($scope.comments, $scope.show_max - 1);
          }
        };
        $scope.$watch('entityId', function(newVal, oldVal) {
          return $scope.comments = Comment.query({
            entity_type: $scope.entityType,
            entity_id: newVal
          }, function() {
            if ($scope.trackLoading) {
              return $rootScope.loaded_comments++;
            }
          });
        });
        $scope.formatDateTime = function(date) {
          return moment(date).format("DD.MM.YY в HH:mm");
        };
        $scope.startCommenting = function(event) {
          $scope.start_commenting = true;
          return $timeout(function() {
            return $(event.target).parent().find('input').focus();
          });
        };
        $scope.endCommenting = function() {
          $scope.comment = '';
          return $scope.start_commenting = false;
        };
        $scope.remove = function(comment_id) {
          _.find($scope.comments, {
            id: comment_id
          }).$remove();
          return $scope.comments = _.without($scope.comments, _.findWhere($scope.comments, {
            id: comment_id
          }));
        };
        $scope.edit = function(comment, event) {
          var element, old_text;
          old_text = comment.comment;
          element = $(event.target);
          comment.is_being_edited = true;
          element.unbind('keydown').unbind('blur');
          element.attr('contenteditable', 'true').focus().on('keydown', function(e) {
            console.log(old_text);
            if (e.keyCode === 13) {
              $(this).removeAttr('contenteditable').blur();
              comment.comment = $(this).text();
              comment.$update();
            }
            if (e.keyCode === 27) {
              return $(this).blur();
            }
          }).on('blur', function(e) {
            $timeout(function() {
              var ref;
              if ((ref = _.find($scope.comments, {
                id: comment.id
              })) != null) {
                ref.is_being_edited = false;
              }
              return $scope.$apply();
            }, 200);
            if (element.attr('contenteditable')) {
              console.log(old_text);
              return element.removeAttr('contenteditable').html(old_text);
            }
          });
        };
        $scope.submitComment = function(event) {
          var new_comment;
          if (event.keyCode === 13) {
            new_comment = new Comment({
              comment: $scope.comment,
              user_id: $scope.user.id,
              entity_id: $scope.entityId,
              entity_type: $scope.entityType
            });
            new_comment.$save().then(function(response) {
              console.log(response);
              new_comment.user = $scope.user;
              new_comment.id = response.id;
              return $scope.comments.push(new_comment);
            });
            $scope.endCommenting();
            focusModal();
          }
          if (event.keyCode === 27) {
            return $(event.target).blur();
          }
        };
        return focusModal = function() {
          if ($('.modal:visible').length) {
            $('.modal:visible').focus();
          }
        };
      }
    };
  });

}).call(this);

(function() {
  angular.module('Egerep').directive('email', function() {
    return {
      restrict: 'E',
      templateUrl: 'directives/email',
      scope: {
        entity: '='
      },
      controller: function($scope) {
        return $scope.send = function() {
          return $('#email-modal').modal('show');
        };
      }
    };
  });

}).call(this);

(function() {
  angular.module('Egerep').directive('ngHighlight', function() {
    return {
      restrict: 'A',
      scope: {
        ngModel: '='
      },
      controller: function($scope, $element, $attrs, $timeout) {
        var refreshInput, refreshSelect;
        if ($($element).prop('tagName') === 'INPUT') {
          $($element).on('keyup', function() {
            return refreshInput(this);
          });
          $timeout(function() {
            return refreshInput($element);
          }, 500);
        }
        if ($($element).prop('tagName') === 'SELECT') {
          $($element).on('change', function() {
            return refreshSelect(this);
          });
          $timeout(function() {
            return refreshSelect($element);
          }, 500);
        }
        refreshInput = function(el) {
          if ($(el).val()) {
            return $(el).parent().find('input, span').addClass('is-selected');
          } else {
            return $(el).parent().find('input, span').removeClass('is-selected');
          }
        };
        return refreshSelect = function(el) {
          $(el).parent().find('button').removeClass('is-selected');
          return $(el).parent().find('select > option[value!=""]:selected').parent('select').siblings('button').addClass('is-selected');
        };
      }
    };
  });

}).call(this);

(function() {
  angular.module('Egerep').directive('inputComment', function() {
    return {
      restrict: 'E',
      templateUrl: 'directives/input-comment',
      scope: {
        entity: '=',
        commentField: '@'
      },
      controller: function($scope, $timeout) {
        $scope.is_being_commented = false;
        $scope.blurComment = function() {
          return $scope.is_being_commented = false;
        };
        $scope.focusComment = function() {
          return $scope.is_being_commented = true;
        };
        $scope.startComment = function(event) {
          $scope.is_being_commented = true;
          return $timeout(function() {
            return $(event.target).parent().children('input').focus();
          });
        };
        return $scope.endComment = function(event) {
          if (event.keyCode === 13) {
            $(event.target).blur();
          }
        };
      }
    };
  });

}).call(this);

(function() {
  angular.module('Egerep').directive('metroList', function() {
    return {
      restrict: 'E',
      templateUrl: 'directives/metro-list',
      scope: {
        markers: '='
      },
      controller: function($scope, $element, $attrs) {
        $scope.inline = $attrs.hasOwnProperty('inline');
        $scope.one_station = $attrs.hasOwnProperty('oneStation');
        $scope.short = function(title) {
          return title.slice(0, 3).toUpperCase();
        };
        return $scope.minutes = function(minutes) {
          return Math.round(minutes);
        };
      }
    };
  });

}).call(this);

(function() {
  angular.module('Egerep').directive('ngMulti', function() {
    return {
      restrict: 'E',
      replace: true,
      scope: {
        object: '=',
        model: '=',
        noneText: '@'
      },
      templateUrl: 'directives/ngmulti',
      controller: function($scope, $element, $attrs, $timeout) {
        $scope.highlight = $attrs.hasOwnProperty('highlight');
        $timeout(function() {
          return $($element).selectpicker({
            noneSelectedText: $scope.noneText
          });
        });
        if ($scope.highlight) {
          return $scope.$watch('model', function(newVal, oldVal) {
            if (newVal) {
              return $timeout(function() {
                $($element).parent().find('button').removeClass('is-selected');
                return $($element).parent().find('select > option[value!=""]:selected').parent('select').siblings('button').addClass('is-selected');
              });
            }
          });
        }
      }
    };
  });

}).call(this);

(function() {
  angular.module('Egerep').directive('notifications', function() {
    return {
      restrict: 'E',
      templateUrl: 'directives/notifications',
      scope: {
        user: '=',
        entityId: '=',
        trackLoading: '=',
        entityType: '@'
      },
      controller: function($rootScope, $scope, $timeout, Notification, Notify) {
        var bindDateMask, handleDateKeycodes, notificate, saveEdit;
        $scope.show_max = 4;
        $scope.show_all_notifications = false;
        $scope.Notification = Notification;
        $scope.Notify = Notify;
        bindDateMask = function(notification_id) {
          return $("#notification-" + notification_id).find('.notification-date-add').mask('d9.y9.y9', {
            clearIfNotMatch: true
          });
        };
        $timeout(function() {
          return $scope.notifications.forEach(function(notification) {
            return bindDateMask(notification.id);
          });
        }, 2000);
        $scope.showAllNotifications = function() {
          $scope.show_all_notifications = true;
          return $timeout(function() {
            return $scope.notifications.forEach(function(notification) {
              return bindDateMask(notification.id);
            });
          });
        };
        $scope.getNotifications = function() {
          if ($scope.show_all_notifications || $scope.notifications.length <= $scope.show_max) {
            return $scope.notifications;
          } else {
            return _.last($scope.notifications, $scope.show_max - 1);
          }
        };
        $scope.hack = function(notification, event) {
          $scope.setEditing(notification);
          $(event.target).attr('contenteditable', true).focus();
        };
        $scope.setEditing = function(notification) {
          return $timeout(function() {
            return notification.is_being_edited = true;
          }, 200);
        };
        $scope.unsetEditing = function(notification) {
          return $timeout(function() {
            var ref;
            return (ref = _.find($scope.notifications, {
              id: notification.id
            })) != null ? ref.is_being_edited = false : void 0;
          }, 150);
        };
        $scope.toggle = function(notification) {
          return $rootScope.toggleEnumServer(notification, 'approved', Notify, Notification);
        };
        $scope.$watch('entityId', function(newVal, oldVal) {
          return $scope.notifications = Notification.query({
            entity_type: $scope.entityType,
            entity_id: newVal
          }, function() {
            return $timeout(function() {
              return $scope.$apply();
            });
          });
        });
        $scope.formatDateTime = function(date) {
          return moment(date).format("DD.MM.YY в HH:mm");
        };
        $scope.startNotificationing = function(event) {
          $scope.start_notificationing = true;
          return $timeout(function() {
            $(event.target).parents('div').first().find('div').focus();
            return $(event.target).parents('div').first().find('input').mask('d9.y9.y9', {
              clearIfNotMatch: true
            });
          });
        };
        $scope.endNotificationing = function(comment_element, date_element) {
          comment_element.html('');
          date_element.val('');
          return $scope.start_notificationing = false;
        };
        $scope.remove = function(notification_id) {
          _.find($scope.notifications, {
            id: notification_id
          }).$remove();
          return $scope.notifications = _.without($scope.notifications, _.findWhere($scope.notifications, {
            id: notification_id
          }));
        };
        saveEdit = function(notification, event) {
          var comment, comment_element, date, date_element, parent;
          event.preventDefault();
          parent = $(event.target).parents('div').first();
          comment_element = parent.find('div').last();
          date_element = parent.find('input');
          comment = comment_element.text();
          date = date_element.val();
          if (date === '' || date.match(/_/)) {
            console.log('no date', date, date_element);
            date_element.blur().focus();
            return;
          }
          if (comment === '') {
            console.log('no comment', comment, comment_element);
            comment_element.focus();
            return;
          }
          return Notification.update({
            id: notification.id
          }, {
            comment: comment,
            date: date
          }, function() {
            notification.comment = comment;
            return notification.date = date;
          });
        };
        $scope.editNotification = function(notification, event) {
          handleDateKeycodes(event);
          if (event.keyCode === 13) {
            event.preventDefault();
            $(event.target).blur();
            window.getSelection().removeAllRanges();
            saveEdit(notification, event);
          }
          if (event.keyCode === 27) {
            window.getSelection().removeAllRanges();
            $(event.target).blur().html(notification.comment);
            if ($(event.target).is('input')) {
              $(event.target).siblings('div.new-notification').html(notification.comment);
            }
            if ($(event.target).is('div.new-notification')) {
              $(event.target).siblings('input').val(notification.date);
            }
          }
        };
        notificate = function(event) {
          var comment, comment_element, date, date_element, new_notification, parent;
          parent = $(event.target).parents('div').first();
          comment_element = parent.find('div').last();
          date_element = parent.find('input');
          comment = comment_element.text();
          date = date_element.val();
          if (date === '' || date.match(/_/)) {
            date_element.blur().focus();
            return;
          }
          if (comment === '') {
            comment_element.focus();
            return;
          }
          new_notification = new Notification({
            comment: comment,
            user_id: $scope.user.id,
            entity_id: $scope.entityId,
            date: date,
            entity_type: $scope.entityType
          });
          new_notification.$save().then(function(response) {
            console.log(response);
            new_notification.user = $scope.user;
            new_notification.id = response.id;
            new_notification.approved = 0;
            $scope.notifications.push(new_notification);
            return $timeout(function() {
              return bindDateMask(new_notification.id);
            });
          });
          return $scope.endNotificationing(comment_element, date_element);
        };
        handleDateKeycodes = function(event) {
          var add_days, date, date_node, new_date, ref;
          if ($(event.target).prop('tagName') === 'DIV') {
            return;
          }
          if ((ref = event.keyCode) === 38 || ref === 40) {
            event.preventDefault();
            date_node = $(event.target).parents('div').first().find('input');
            date = date_node.val();
            if (date.match(/_/)) {
              return date_node.val($rootScope.formatDate(moment()));
            } else {
              add_days = event.keyCode === 38 ? 1 : -1;
              new_date = $rootScope.formatDate(moment('20' + convertDate(date)).add({
                day: add_days
              }));
              return date_node.val(new_date);
            }
          }
        };
        $scope.submitNotification = function(event) {
          handleDateKeycodes(event);
          if (event.keyCode === 13) {
            event.preventDefault();
            notificate(event);
          }
          if (event.keyCode === 27) {
            window.getSelection().removeAllRanges();
            $(event.target).blur();
            $scope.start_notificationing = false;
          }
        };
        return $scope.defaultNotification = function() {
          var new_notification;
          new_notification = new Notification({
            comment: 'стандартное напоминание',
            user_id: $scope.user.id,
            entity_id: $scope.entityId,
            entity_type: $scope.entityType,
            approved: 1,
            date: moment(convertDate($scope.$parent.selected_attachment.date)).add(2, 'days').format('DD.MM.YY')
          });
          return new_notification.$save().then(function(response) {
            new_notification.user = $scope.user;
            new_notification.id = response.id;
            new_notification.approved = 1;
            $scope.notifications.push(new_notification);
            return $timeout(function() {
              return bindDateMask(new_notification.id);
            });
          });
        };
      }
    };
  });

}).call(this);

(function() {
  angular.module('Egerep').directive('pencilInput', function() {
    return {
      restrict: 'E',
      replace: true,
      templateUrl: 'directives/pencil-input',
      scope: {
        model: '='
      },
      controller: function($scope, $timeout, $element, $controller) {
        $scope.is_being_commented = false;
        $scope.blurComment = function() {
          return $scope.is_being_commented = false;
        };
        $scope.focusComment = function() {
          return $scope.is_being_commented = true;
        };
        $scope.startComment = function(event) {
          $scope.is_being_commented = true;
          return $timeout(function() {
            return $(event.target).parent().children('div').focus();
          });
        };
        return $scope.watchEnter = function(event) {
          var ref;
          if ((ref = event.keyCode) === 13 || ref === 27) {
            if (event.keyCode === 13) {
              $scope.model = $(event.target).parent().children('div').text();
            }
            $(event.target).parent().children('div').text($scope.model);
            event.preventDefault();
            $(event.target).blur();
          }
        };
      }
    };
  });

}).call(this);

(function() {
  angular.module('Egerep').directive('phones', function() {
    return {
      restrict: 'E',
      templateUrl: 'directives/phones',
      scope: {
        entity: '='
      },
      controller: function($scope, $timeout, $rootScope, PhoneService, UserService, $interval) {
        var recodringLink;
        $scope.PhoneService = PhoneService;
        $scope.UserService = UserService;
        $scope.is_playing_stage = 'stop';
        $scope.isOpened = false;
        $rootScope.dataLoaded.promise.then(function(data) {
          return $scope.level = $scope.entity.phones && $scope.entity.phones.length ? $scope.entity.phones.length : 1;
        });
        $scope.nextLevel = function() {
          return $scope.level++;
        };
        $scope.phoneMaskControl = function(event) {
          var el, phone_id;
          el = $(event.target);
          phone_id = el.attr('ng-model').split('.')[1];
          return $scope.entity[phone_id] = $(event.target).val();
        };
        $scope.isFull = function(number) {
          if (number === void 0 || number === "") {
            return false;
          }
          return !number.match(/_/);
        };
        $scope.sms = function(number) {
          $('#sms-modal').modal('show');
          return $rootScope.sms_number = number;
        };
        $scope.info = function(number) {
          $scope.api_number = number;
          $scope.mango_info = null;
          $('#api-phone-info').modal('show');
          if ($scope.isOpened === false) {
            $('#api-phone-info').on('hidden.bs.modal', function() {
              $scope.isOpened = true;
              if ($scope.audio) {
                $scope.audio.pause();
                $scope.audio = null;
                $scope.is_playing_stage = 'stop';
                return $scope.is_playing = null;
              }
            });
          }
          return PhoneService.info(number).then(function(response) {
            return $scope.mango_info = response.data;
          });
        };
        $scope.formatDateTime = function(date) {
          return moment(date).format("DD.MM.YY в HH:mm");
        };
        $scope.time = function(seconds) {
          return moment(0).seconds(seconds).format("mm:ss");
        };
        $scope.getNumberTitle = function(number) {
          if (number === PhoneService.clean($scope.api_number)) {
            return 'текущий номер';
          }
          return number;
        };
        recodringLink = function(recording_id) {
          var api_key, api_salt, sha256, sign, timestamp;
          api_key = 'goea67jyo7i63nf4xdtjn59npnfcee5l';
          api_salt = 't9mp7vdltmhn0nhnq0x4vwha9ncdr8pa';
          timestamp = moment().add(5, 'minute').unix();
          sha256 = new jsSHA('SHA-256', 'TEXT');
          sha256.update(api_key + timestamp + recording_id + api_salt);
          sign = sha256.getHash('HEX');
          return "https://app.mango-office.ru/vpbx/queries/recording/link/" + recording_id + "/play/" + api_key + "/" + timestamp + "/" + sign;
        };
        $scope.intervalStart = function() {
          return $scope.interval = $interval(function() {
            if ($scope.audio) {
              $scope.current_time = angular.copy($scope.audio.currentTime);
              $scope.prc = (($scope.current_time * 100) / $scope.audio.duration).toFixed(2);
              if (parseInt($scope.prc) === 100) {
                return $scope.stop();
              }
            }
          }, 10);
        };
        $scope.intervalCancel = function() {
          return $interval.cancel($scope.interval);
        };
        $scope.initAudio = function(recording_id) {
          if ($scope.is_playing) {
            $scope.stop();
          }
          $scope.audio = new Audio(recodringLink(recording_id));
          $scope.current_time = 0;
          $scope.prc = 0;
          $scope.is_playing_stage = 'start';
          return $scope.is_playing = recording_id;
        };
        $scope.pause = function() {
          $scope.intervalCancel();
          if ($scope.audio) {
            $scope.audio.pause();
          }
          return $scope.is_playing_stage = 'pause';
        };
        $scope.play = function(recording_id) {
          if (!$scope.isPlaying(recording_id)) {
            $scope.initAudio(recording_id);
          }
          if ($scope.is_playing_stage === 'play') {
            return $scope.pause();
          } else {
            $scope.audio.play();
            $scope.is_playing_stage = 'play';
            return $scope.intervalStart();
          }
        };
        $scope.isPlaying = function(recording_id) {
          return $scope.is_playing === recording_id;
        };
        $scope.stop = function() {
          $scope.prc = 0;
          $scope.is_playing = null;
          $scope.audio.pause();
          $scope.audio = null;
          $scope.is_playing_stage = 'stop';
          return $scope.intervalCancel();
        };
        return $scope.setCurentTime = function(e) {
          var time, width;
          width = angular.element(e.target).width();
          $scope.prc = (e.offsetX * 100) / width;
          time = ($scope.audio.duration * $scope.prc) / 100;
          return $scope.audio.currentTime = time;
        };
      }
    };
  });

}).call(this);

(function() {
  angular.module('Egerep').directive('plural', function() {
    return {
      restrict: 'E',
      scope: {
        count: '=',
        type: '@',
        noneText: '@',
        additional: '='
      },
      templateUrl: 'directives/plural',
      controller: function($scope, $element, $attrs, $timeout) {
        $scope.textOnly = $attrs.hasOwnProperty('textOnly');
        $scope.hideZero = $attrs.hasOwnProperty('hideZero');
        return $scope.when = {
          'age': ['год', 'года', 'лет'],
          'student': ['ученик', 'ученика', 'учеников'],
          'minute': ['минуту', 'минуты', 'минут'],
          'hour': ['час', 'часа', 'часов'],
          'day': ['день', 'дня', 'дней'],
          'meeting': ['встреча', 'встречи', 'встреч'],
          'score': ['балл', 'балла', 'баллов'],
          'rubbles': ['рубль', 'рубля', 'рублей'],
          'lesson': ['занятие', 'занятия', 'занятий'],
          'client': ['клиент', 'клиента', 'клиентов'],
          'mark': ['оценки', 'оценок', 'оценок'],
          'request': ['заявка', 'заявки', 'заявок']
        };
      }
    };
  });

}).call(this);

(function() {
  angular.module('Egerep').directive('plus', function() {
    return {
      restrict: 'E',
      scope: {
        previous: '=',
        count: '='
      },
      templateUrl: 'directives/plus'
    };
  });

}).call(this);

(function() {
  angular.module('Egerep').directive('publishedField', function() {
    return {
      restrict: 'E',
      replace: true,
      templateUrl: 'directives/published-field',
      scope: {
        inEgeCentr: '@'
      }
    };
  });

}).call(this);

(function() {
  angular.module('Egerep').directive('securityNotification', function() {
    return {
      restrict: 'E',
      scope: {
        tutor: '='
      },
      templateUrl: 'directives/security-notification',
      controller: function($scope, Tutor) {
        return $scope.toggleNotification = function(index) {
          var security_notification;
          security_notification = angular.copy($scope.tutor.security_notification);
          security_notification[index] = !security_notification[index];
          return Tutor.update({
            id: $scope.tutor.id,
            security_notification: security_notification
          }, function() {
            return $scope.tutor.security_notification = angular.copy(security_notification);
          });
        };
      }
    };
  });

}).call(this);

(function() {
  angular.module('Egerep').directive('ngSelect', function() {
    return {
      restrict: 'E',
      replace: true,
      scope: {
        object: '=',
        model: '=',
        noneText: '@'
      },
      templateUrl: 'directives/ngselect',
      controller: function($scope, $element, $attrs) {
        if (!$scope.noneText) {
          return $scope.model = _.first(Object.keys($scope.object));
        }
      }
    };
  });

}).call(this);

(function() {
  angular.module('Egerep').directive('sms', function() {
    return {
      restrict: 'E',
      templateUrl: 'directives/sms',
      scope: {
        number: '='
      },
      controller: function($scope, $timeout, Sms, PusherService) {
        var scrollDown;
        PusherService.bind('SmsStatusUpdate', function(data) {
          return angular.forEach($scope.history, function(val, key) {
            if (val.id_smsru === data.id_smsru) {
              val.id_status = data.id_status;
              $scope.$apply();
            }
            return console.log(val, key);
          });
        });
        $scope.mass = false;
        $scope.smsCount = function() {
          return SmsCounter.count($scope.message || '').messages;
        };
        $scope.send = function() {
          var sms;
          if ($scope.message) {
            $scope.sms_sending = true;
            ajaxStart();
            sms = new Sms({
              message: $scope.message,
              to: $scope.number,
              mass: $scope.mass
            });
            return sms.$save().then(function(data) {
              ajaxEnd();
              $scope.sms_sending = false;
              $scope.message = '';
              $scope.history.push(data);
              return scrollDown();
            });
          }
        };
        $scope.$watch('number', function(newVal, oldVal) {
          console.log($scope.$parent.formatDateTime($scope.created_at));
          if (newVal) {
            $scope.history = Sms.query({
              number: newVal
            });
          }
          return scrollDown();
        });
        return scrollDown = function() {
          return $timeout(function() {
            return $("#sms-history").animate({
              scrollTop: $(window).height()
            }, "fast");
          });
        };
      }
    };
  });

}).call(this);

(function() {
  angular.module('Egerep').directive('tutorPhoto', function() {
    return {
      restrict: 'E',
      replace: true,
      scope: {
        tutor: '=',
        version: '='
      },
      templateUrl: 'directives/tutor-photo'
    };
  });

}).call(this);

(function() {
  angular.module('Egerep').directive('userSwitch', function() {
    return {
      restrict: 'E',
      scope: {
        entity: '=',
        resource: '=',
        userId: '@'
      },
      templateUrl: 'directives/user-switch',
      controller: function($scope) {
        return $scope.UserService = $scope.$parent.UserService;
      }
    };
  });

}).call(this);

(function() {
  angular.module('Egerep').directive('user', function() {
    return {
      restrict: 'E',
      scope: {
        model: '='
      },
      templateUrl: 'directives/user'
    };
  });

}).call(this);

(function() {
  angular.module('Egerep').value('Approved', {
    0: 'не подтвержден',
    1: 'подтвержден'
  }).value('Confirmed', {
    0: 'подтвердить',
    1: 'подтверждено'
  }).value('Months', {
    1: 'январь',
    2: 'февраль',
    3: 'март',
    4: 'апрель',
    5: 'май',
    6: 'июнь',
    7: 'июль',
    8: 'август',
    9: 'сентябрь',
    10: 'октябрь',
    11: 'ноябрь',
    12: 'декабрь'
  }).value('Notify', ['напомнить', 'не напоминать']).value('AttachmentErrors', {
    1: 'в стыковке не указан класс',
    2: 'в стыковке не указан предмет',
    3: 'не указаны условия стыковки',
    4: 'дата архивации должна быть позже даты стыковки',
    5: 'не указаны детали архивации',
    6: 'прогноз и занятия не сочетаются',
    7: 'прогноз и занятия не сочетаются',
    8: 'при наличии занятий к проводке стыковка не может быть скрыта',
    9: 'дата архивации не совпадает с датой последнего занятия',
    10: 'возможно стыковку можно скрыть',
    11: 'возможно стыковку можно скрыть',
    12: 'в скрытой стыковке дата архивации должна совпадать с датой последнего занятия',
    13: 'в скрытой стыковке без занятий между датами стыковки и архивации должно быть 7 дней',
    14: 'если архивация отсутствует, стыковка не может быть скрыта',
    15: 'стыковка, у которой дата архивации позже даты последнего расчета не может быть скрыта',
    16: 'слишком маленький прогноз',
    17: 'слишком большой прогноз'
  }).value('ReviewErrors', {
    1: 'не стоит оценка к отзыву',
    2: 'нет подписи к опубликованному отзыву',
    3: 'нет текста отзыва к опубликованному отзыву'
  }).value('TutorErrors', {
    1: 'нет оригинала фото',
    2: 'нет обрезанного фото',
    3: 'цена не установлена',
    4: 'нет меток и выезда'
  }).value('LogTypes', {
    create: 'создание',
    update: 'обновление',
    "delete": 'удаление'
  }).value('Recommendations', {
    1: {
      text: 'У этого репетитора уже было несколько расчетов, поэтому ему можно доверить длительное обучение, требующееся данному клиенту',
      type: 0
    },
    2: {
      text: 'У этого репетитора был всего 1 расчет и ему можно доверить длительное обучение, но лучше поискать более проверенные варианты',
      type: 1
    },
    3: {
      text: 'С этим репетитором не было встреч и есть клиенты, за которых он еще не рассчитался. Отдавать этого клиента категорически нельзя',
      type: 2
    },
    4: {
      text: 'С этим репетитором не было встреч и у него нет активных клиентов. Отдавать ему клиента можно, но только в крайнем случае',
      type: 1
    },
    5: {
      text: 'У этого репетитора уже было несколько расчетов, поэтому ему можно доверить данного клиента',
      type: 0
    },
    6: {
      text: 'У этого репетитора был всего 1 расчет, то есть у него средний кредитный рейтинг. Если более проверенных репетиторов нет, ему можно доверить этого клиента',
      type: 1
    },
    7: {
      text: 'С этим репетитором не было встреч и есть клиенты, за которых он еще не рассчитался. Отдавать этого репетитора можно в самом крайнем случае',
      type: 2
    },
    8: {
      text: 'С этим репетитором не было встреч и у него нет активных клиентов. Риск сотрудничества средний, поэтому работать с репетитором можно, если нет других вариантов',
      type: 1
    },
    9: {
      text: 'У этого репетитора высокий кредитный рейтинг, но конец учебного года лучше использовать для проверки неизвестных репетиторов',
      type: 1
    },
    10: {
      text: 'Этому репетитору мы не доверяем, но сейчас отличное время для его проверки. Если сотрудничество будет успешным, то мы будем рекомендовать в следующем году как проверенного. Если он не заплатит, то невыплаты будут минимальными и репетитора мы закроем навсегда, в чем великая польза.',
      type: 0
    },
    11: {
      text: 'С 10-классниками нужно быть особенно аккуратными и этот репетитор в данном случае рекомендован',
      type: 0
    },
    12: {
      text: 'С этим репетитором была всего 1 встреча, поэтому давать его ученику 10 класса будет риском. Сделайте все, чтобы избежать этого, но если не получается – давать можно',
      type: 1
    },
    13: {
      text: 'С этим репетитором не было встреч и есть клиенты, за которых он еще не рассчитался. Нужно сделать все, чтобы 10-классник его не получил, так как 10 классы всегда продолжают заниматься и в 11 классе. Давать этого репетитора категорически нельзя',
      type: 2
    },
    14: {
      text: 'Этот репетитор для компании новый. Давать 10-класснику можно, но в самом крайнем случае',
      type: 2
    }
  }).value('RecommendationTypes', ['очень рекомендован', 'средне рекомендован', 'не рекомендован']).value('DebtTypes', {
    0: 'не доплатил',
    1: 'переплатил'
  }).value('Weekdays', {
    0: 'пн',
    1: 'вт',
    2: 'ср',
    3: 'чт',
    4: 'пт',
    5: 'сб',
    6: 'вс'
  }).value('Destinations', {
    r_k: 'репетитор едет к клиенту',
    k_r: 'клиент едет к репетитору'
  }).value('Workplaces', {
    0: 'не активен в системе ЕГЭ-Центре',
    1: 'активен в системе ЕГЭ-Центра',
    2: 'ведет занятия в ЕГЭ-Центре',
    3: 'ранее работал в ЕГЭ-Центре'
  }).value('Genders', {
    male: 'мужской',
    female: 'женский'
  }).value('YesNo', {
    0: 'нет',
    1: 'да'
  }).value('TutorPublishedStates', {
    0: 'не опубликован',
    1: 'опубликован'
  }).value('PaymentMethods', {
    0: 'стандартный расчет',
    1: 'яндекс.деньги',
    2: 'перевод на карту'
  }).value('ArchiveStates', {
    impossible: 'невозможно',
    possible: 'возможно'
  }).value('ReviewStates', {
    unpublished: 'не опубликован',
    published: 'опубликован'
  }).value('Existance', ['созданные', 'требующие создания']).value('Presence', [['есть', 'отсутствует'], ['есть', 'нет']]).value('AttachmentVisibility', {
    0: 'показано',
    1: 'скрыто'
  }).value('AttachmentStates', {
    "new": 'новые',
    inprogress: 'рабочие',
    ended: 'завершенные'
  }).value('AttachmentState', {
    "new": 'новый',
    inprogress: 'рабочий',
    ended: 'завершенный'
  }).value('Checked', ['не проверено', 'проверено']).value('ReviewScores', {
    1: 1,
    2: 2,
    3: 3,
    4: 4,
    5: 5,
    6: 6,
    7: 7,
    8: 8,
    9: 9,
    10: 10,
    11: 'отзыв не собирать',
    12: 'отзыв собрать позже'
  }).value('Grades', {
    1: '1 класс',
    2: '2 класс',
    3: '3 класс',
    4: '4 класс',
    5: '5 класс',
    6: '6 класс',
    7: '7 класс',
    8: '8 класс',
    9: '9 класс',
    10: '10 класс',
    11: '11 класс',
    12: 'студенты',
    13: 'остальные'
  }).value('Subjects', {
    all: {
      1: 'математика',
      2: 'физика',
      3: 'химия',
      4: 'биология',
      5: 'информатика',
      6: 'русский',
      7: 'литература',
      8: 'обществознание',
      9: 'история',
      10: 'английский',
      11: 'география'
    },
    full: {
      1: 'Математика',
      2: 'Физика',
      3: 'Химия',
      4: 'Биология',
      5: 'Информатика',
      6: 'Русский язык',
      7: 'Литература',
      8: 'Обществознание',
      9: 'История',
      10: 'Английский язык',
      11: 'География'
    },
    dative: {
      1: 'математике',
      2: 'физике',
      3: 'химии',
      4: 'биологии',
      5: 'информатике',
      6: 'русскому языку',
      7: 'литературе',
      8: 'обществознанию',
      9: 'истории',
      10: 'английскому языку',
      11: 'географии'
    },
    short: ['М', 'Ф', 'Р', 'Л', 'А', 'Ис', 'О', 'Х', 'Б', 'Ин', 'Г'],
    three_letters: {
      1: 'МАТ',
      2: 'ФИЗ',
      3: 'ХИМ',
      4: 'БИО',
      5: 'ИНФ',
      6: 'РУС',
      7: 'ЛИТ',
      8: 'ОБЩ',
      9: 'ИСТ',
      10: 'АНГ',
      11: 'ГЕО'
    },
    short_eng: ['math', 'phys', 'rus', 'lit', 'eng', 'his', 'soc', 'chem', 'bio', 'inf', 'geo']
  }).value('Branches', {
    1: {
      code: 'TRG',
      full: 'Тургеневская',
      short: 'ТУР',
      address: 'Мясницкая 40с1',
      color: '#FBAA33'
    },
    2: {
      code: 'PVN',
      full: 'Проспект Вернадского',
      short: 'ВЕР',
      address: '',
      color: '#EF1E25'
    },
    3: {
      code: 'BGT',
      full: 'Багратионовская',
      short: 'БАГ',
      address: '',
      color: '#019EE0'
    },
    5: {
      code: 'IZM',
      full: 'Измайловская',
      short: 'ИЗМ',
      address: '',
      color: '#0252A2'
    },
    6: {
      code: 'OPL',
      full: 'Октябрьское поле',
      short: 'ОКТ',
      address: '',
      color: '#B61D8E'
    },
    7: {
      code: 'RPT',
      full: 'Рязанский Проспект',
      short: 'РЯЗ',
      address: '',
      color: '#B61D8E'
    },
    8: {
      code: 'VKS',
      full: 'Войковская',
      short: 'ВОЙ',
      address: '',
      color: '#029A55'
    },
    9: {
      code: 'ORH',
      full: 'Орехово',
      short: 'ОРЕ',
      address: '',
      color: '#029A55'
    },
    11: {
      code: 'UJN',
      full: 'Южная',
      short: 'ЮЖН',
      address: '',
      color: '#ACADAF'
    },
    12: {
      code: 'PER',
      full: 'Перово',
      short: 'ПЕР',
      address: '',
      color: '#FFD803'
    },
    13: {
      code: 'KLG',
      full: 'Калужская',
      short: 'КЛЖ',
      address: 'Научный проезд 8с1',
      color: '#C07911'
    },
    14: {
      code: 'BRT',
      full: 'Братиславская',
      short: 'БРА',
      address: '',
      color: '#B1D332'
    },
    15: {
      code: 'MLD',
      full: 'Молодежная',
      short: 'МОЛ',
      address: '',
      color: '#0252A2'
    },
    16: {
      code: 'VLD',
      full: 'Владыкино',
      short: 'ВЛА',
      address: '',
      color: '#ACADAF'
    }
  });

}).call(this);

(function() {
  var apiPath, updateMethod;

  angular.module('Egerep').factory('Marker', function($resource) {
    return $resource(apiPath('markers'), {
      id: '@id'
    }, updateMethod());
  }).factory('Notification', function($resource) {
    return $resource(apiPath('notifications'), {
      id: '@id'
    }, updateMethod());
  }).factory('Account', function($resource) {
    return $resource(apiPath('accounts'), {
      id: '@id'
    }, updateMethod());
  }).factory('PlannedAccount', function($resource) {
    return $resource(apiPath('periods/planned'), {
      id: '@id'
    }, updateMethod());
  }).factory('Review', function($resource) {
    return $resource(apiPath('reviews'), {
      id: '@id'
    }, updateMethod());
  }).factory('Archive', function($resource) {
    return $resource(apiPath('archives'), {
      id: '@id'
    }, updateMethod());
  }).factory('Attachment', function($resource) {
    return $resource(apiPath('attachments'), {
      id: '@id'
    }, updateMethod());
  }).factory('RequestList', function($resource) {
    return $resource(apiPath('lists'), {
      id: '@id'
    }, updateMethod());
  }).factory('Request', function($resource) {
    return $resource(apiPath('requests'), {
      id: '@id'
    }, {
      update: {
        method: 'PUT'
      },
      transfer: {
        method: 'POST',
        url: apiPath('requests', 'transfer')
      },
      list: {
        method: 'GET'
      }
    });
  }).factory('Sms', function($resource) {
    return $resource(apiPath('sms'), {
      id: '@id'
    }, updateMethod());
  }).factory('Comment', function($resource) {
    return $resource(apiPath('comments'), {
      id: '@id'
    }, updateMethod());
  }).factory('Client', function($resource) {
    return $resource(apiPath('clients'), {
      id: '@id'
    }, updateMethod());
  }).factory('User', function($resource) {
    return $resource(apiPath('users'), {
      id: '@id'
    }, updateMethod());
  }).factory('Tutor', function($resource) {
    return $resource(apiPath('tutors'), {
      id: '@id'
    }, {
      update: {
        method: 'PUT'
      },
      deletePhoto: {
        url: apiPath('tutors', 'photo'),
        method: 'DELETE'
      },
      list: {
        method: 'GET'
      }
    });
  });

  apiPath = function(entity, additional) {
    if (additional == null) {
      additional = '';
    }
    return ("api/" + entity + "/") + (additional ? additional + '/' : '') + ":id";
  };

  updateMethod = function() {
    return {
      update: {
        method: 'PUT'
      }
    };
  };

}).call(this);

(function() {
  angular.module('Egerep').service('ApiService', function($http) {
    this.metro = function(fun, data) {
      return $http.post("api/metro/" + fun, data);
    };
    return this;
  });

}).call(this);

(function() {
  angular.module('Egerep').service('AttachmentService', function(AttachmentStates) {
    this.AttachmentStates = AttachmentStates;
    this.getState = function(attachment) {
      if (attachment.archive) {
        return 'ended';
      } else {
        if (attachment.forecast) {
          return 'inprogress';
        } else {
          return 'new';
        }
      }
    };
    this.getStatus = function(attachment) {
      return this.AttachmentStates[this.getState(attachment)];
    };
    return this;
  });

}).call(this);

(function() {
  angular.module('Egerep').service('BranchService', function(Branches) {
    this.branches = Branches;
    this.getNameWithColor = function(branch_id) {
      var curBranch;
      curBranch = this.branches[branch_id];
      return '<svg xmlns="http://www.w3.org/2000/svg" version="1.1" class="svg-metro"><circle fill="' + curBranch.color + '" r="6" cx="7" cy="7"></circle></svg>' + curBranch.full;
    };
    return this;
  });

}).call(this);

(function() {
  angular.module('Egerep').service('PhoneService', function($rootScope, $http) {
    this.info = function(number) {
      return $http.post('api/command/mango-stats', {
        number: number
      });
    };
    this.call = function(number) {
      return location.href = "sip:" + number.replace(/[^0-9]/g, '');
    };
    this.isMobile = function(number) {
      return number && (parseInt(number[4]) === 9 || parseInt(number[1]) === 9);
    };
    this.clean = function(number) {
      return number.replace(/[^0-9]/gim, "");
    };
    this.format = function(number) {
      if (!number) {
        return;
      }
      number = this.clean(number);
      return '+' + number.substr(0, 1) + ' (' + number.substr(1, 3) + ') ' + number.substr(4, 3) + '-' + number.substr(7, 2) + '-' + number.substr(9, 2);
    };
    this.sms = function(number) {
      $rootScope.sms_number = number;
      return $('#sms-modal').modal('show');
    };
    return this;
  });

}).call(this);

(function() {
  angular.module('Egerep').service('PusherService', function($http) {
    var init;
    this.bind = function(channel, callback) {
      if (this.pusher === void 0) {
        init();
      }
      return this.channel.bind("App\\Events\\" + channel, callback);
    };
    init = (function(_this) {
      return function() {
        _this.pusher = new Pusher('2d212b249c84f8c7ba5c', {
          encrypted: true,
          cluster: 'eu'
        });
        return _this.channel = _this.pusher.subscribe('egerep');
      };
    })(this);
    return this;
  });

}).call(this);

(function() {
  angular.module('Egerep').service('RecommendationService', function(Recommendations, RecommendationTypes) {
    this.get = function(tutor, grade) {
      var recommendation;
      recommendation = this.getRecommendation(tutor, grade);
      recommendation.type_text = RecommendationTypes[recommendation.type];
      return recommendation;
    };
    this.getRecommendation = function(tutor, grade) {
      var month;
      month = moment().format('M');
      if (grade !== 10) {
        if (month >= 7 && month <= 10) {
          if (tutor.meeting_count >= 2) {
            return Recommendations[1];
          } else {
            if (tutor.meeting_count === 1) {
              return Recommendations[2];
            } else {
              if (tutor.active_clients_count >= 2) {
                return Recommendations[3];
              } else {
                return Recommendations[4];
              }
            }
          }
        } else {
          if (month >= 11 || month <= 2) {
            if (tutor.meeting_count >= 2) {
              return Recommendations[5];
            } else {
              if (tutor.meeting_count === 1) {
                return Recommendations[6];
              } else {
                if (tutor.active_clients_count >= 2) {
                  return Recommendations[7];
                } else {
                  return Recommendations[8];
                }
              }
            }
          } else {
            if (tutor.meeting_count >= 2) {
              return Recommendations[9];
            } else {
              return Recommendations[10];
            }
          }
        }
      } else {
        if (tutor.meeting_count >= 2) {
          return Recommendations[11];
        } else {
          if (tutor.meeting_count === 1) {
            return Recommendations[12];
          } else {
            if (tutor.active_clients_count >= 2) {
              return Recommendations[13];
            } else {
              return Recommendations[14];
            }
          }
        }
      }
    };
    return this;
  });

}).call(this);

(function() {
  angular.module('Egerep').service('SvgMap', function() {
    this.show = function() {
      $('#svg-modal').modal('show');
    };
    return this;
  });

}).call(this);

(function() {
  angular.module('Egerep').service('TutorService', function($http) {
    this.translit = {
      'А': 'A',
      'Б': 'B',
      'В': 'V',
      'Г': 'G',
      'Д': 'D',
      'Е': 'E',
      'Ё': 'E',
      'Ж': 'Gh',
      'З': 'Z',
      'И': 'I',
      'Й': 'Y',
      'К': 'K',
      'Л': 'L',
      'М': 'M',
      'Н': 'N',
      'О': 'O',
      'П': 'P',
      'Р': 'R',
      'С': 'S',
      'Т': 'T',
      'У': 'U',
      'Ф': 'F',
      'Х': 'H',
      'Ц': 'C',
      'Ч': 'Ch',
      'Ш': 'Sh',
      'Щ': 'Sch',
      'Ъ': 'Y',
      'Ы': 'Y',
      'Ь': 'Y',
      'Э': 'E',
      'Ю': 'Yu',
      'Я': 'Ya',
      'а': 'a',
      'б': 'b',
      'в': 'v',
      'г': 'g',
      'д': 'd',
      'е': 'e',
      'ё': 'e',
      'ж': 'gh',
      'з': 'z',
      'и': 'i',
      'й': 'y',
      'к': 'k',
      'л': 'l',
      'м': 'm',
      'н': 'n',
      'о': 'o',
      'п': 'p',
      'р': 'r',
      'с': 's',
      'т': 't',
      'у': 'u',
      'ф': 'f',
      'х': 'h',
      'ц': 'c',
      'ч': 'ch',
      'ш': 'sh',
      'щ': 'sch',
      'ъ': 'y',
      'ы': 'y',
      'ь': 'y',
      'э': 'e',
      'ю': 'yu',
      'я': 'ya'
    };
    this.default_tutor = {
      gender: "male",
      branches: [],
      phones: [],
      subjects: [],
      grades: [],
      svg_map: [],
      markers: [],
      state: 0,
      in_egecentr: 0
    };
    this.getFiltered = function(search_data) {
      return $http.post('api/tutors/filtered', search_data);
    };
    this.select = function(search_data) {
      return $http.post('api/tutors/select', search_data);
    };
    this.getDebtMap = function(search_data) {
      return $http.post('api/debt/map', search_data);
    };
    this.getDebtors = function() {
      return $http.get('api/debt');
    };
    this.generateLogin = function(tutor) {
      var i, len, letter, login, ref;
      login = '';
      ref = tutor.last_name.toLowerCase();
      for (i = 0, len = ref.length; i < len; i++) {
        letter = ref[i];
        login += this.translit[letter];
      }
      login = login.slice(0, 3);
      login += '_' + this.translit[tutor.first_name.toLowerCase()[0]] + this.translit[tutor.middle_name.toLowerCase()[0]];
      return login;
    };
    this.generatePassword = function() {
      return Math.floor(10000000 + Math.random() * 89999999);
    };
    return this;
  });

}).call(this);

(function() {
  angular.module('Egerep').service('UserService', function(User, $rootScope, $timeout) {
    var system_user;
    this.users = User.query();
    $timeout((function(_this) {
      return function() {
        return _this.current_user = $rootScope.$$childTail.user;
      };
    })(this));
    system_user = {
      color: '#999999',
      login: 'system',
      id: 0
    };
    this.get = function(user_id) {
      return this.getUser(user_id);
    };
    this.getUser = function(user_id) {
      return _.findWhere(this.users, {
        id: parseInt(user_id)
      }) || system_user;
    };
    this.getLogin = function(user_id) {
      return this.getUser(parseInt(user_id)).login;
    };
    this.getColor = function(user_id) {
      return this.getUser(parseInt(user_id)).color;
    };
    this.getWithSystem = function(only_active) {
      var users;
      if (only_active == null) {
        only_active = true;
      }
      users = this.getAll(only_active);
      users.unshift(system_user);
      return users;
    };
    this.getAll = function(only_active) {
      if (only_active == null) {
        only_active = true;
      }
      if (only_active) {
        return _.filter(this.users, function(user) {
          return user.rights.length && user.rights.indexOf('35') === -1;
        });
      } else {
        return this.users;
      }
    };
    this.toggle = function(entity, user_id, Resource) {
      var new_user_id, obj;
      if (Resource == null) {
        Resource = false;
      }
      new_user_id = entity[user_id] ? 0 : this.current_user.id;
      if (Resource) {
        return Resource.update((
          obj = {
            id: entity.id
          },
          obj["" + user_id] = new_user_id,
          obj
        ), function() {
          return entity[user_id] = new_user_id;
        });
      } else {
        return entity[user_id] = new_user_id;
      }
    };
    this.getBannedUsers = function() {
      return _.filter(this.users, function(user) {
        return user.rights.length && user.rights.indexOf('35') !== -1;
      });
    };
    this.getBannedHaving = function(condition_obj) {
      return _.filter(this.users, function(user) {
        return user.rights.indexOf('35') !== -1 && condition_obj && condition_obj[user.id];
      });
    };
    this.getActiveInAnySystem = function(with_system) {
      var users;
      if (with_system == null) {
        with_system = true;
      }
      users = _.chain(this.users).filter(function(user) {
        return user.rights.indexOf('35') === -1 || user.rights.indexOf('34') === -1;
      }).sortBy('login').value();
      if (with_system) {
        users.unshift(system_user);
      }
      return users;
    };
    this.getBannedInBothSystems = function() {
      return _.chain(this.users).filter(function(user) {
        return user.rights.indexOf('35') !== -1 && user.rights.indexOf('34') !== -1;
      }).sortBy('login').value();
    };
    return this;
  });

}).call(this);

//# sourceMappingURL=app.js.map
