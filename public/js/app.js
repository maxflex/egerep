(function() {
  var indexOf = [].indexOf || function(item) { for (var i = 0, l = this.length; i < l; i++) { if (i in this && this[i] === item) return i; } return -1; };

  angular.module("Egerep", ['ngSanitize', 'ngResource', 'ngMaterial', 'ngMap', 'ngAnimate', 'ui.sortable', 'ui.bootstrap', 'angular-ladda', 'mwl.calendar']).config([
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
    $rootScope.laroute = laroute;
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
    $rootScope.toggleEnum = function(ngModel, status, ngEnum, skip_values, allowed_user_ids, recursion) {
      var ref, ref1, ref2, status_id, statuses;
      if (skip_values == null) {
        skip_values = [];
      }
      if (allowed_user_ids == null) {
        allowed_user_ids = [];
      }
      if (recursion == null) {
        recursion = false;
      }
      if (!recursion && ((ref = parseInt(ngModel[status]), indexOf.call(skip_values, ref) >= 0) || (isNaN(parseInt(ngModel[status])) && skip_values.indexOf(ngModel[status]) !== -1)) && (ref1 = $rootScope.$$childHead.user.id, indexOf.call(allowed_user_ids, ref1) < 0)) {
        return;
      }
      statuses = Object.keys(ngEnum);
      status_id = statuses.indexOf(ngModel[status].toString());
      status_id++;
      if (status_id > (statuses.length - 1)) {
        status_id = 0;
      }
      ngModel[status] = statuses[status_id];
      if (((isNaN(parseInt(ngModel[status])) && skip_values.indexOf(ngModel[status]) !== -1) || indexOf.call(skip_values, status_id) >= 0) && (ref2 = $rootScope.$$childHead.user.id, indexOf.call(allowed_user_ids, ref2) < 0)) {
        return $rootScope.toggleEnum(ngModel, status, ngEnum, skip_values, allowed_user_ids, true);
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
  angular.module('Egerep').controller('AccountsHiddenCtrl', function($scope, Grades, Attachment) {
    var bindDraggable;
    bindArguments($scope, arguments);
    angular.element(document).ready(function() {
      return bindDraggable();
    });
    return bindDraggable = function() {
      $(".client-draggable").draggable({
        helper: 'clone',
        revert: 'invalid',
        appendTo: 'body',
        activeClass: 'drag-active',
        start: function(event, ui) {
          return $(this).css("visibility", "hidden");
        },
        stop: function(event, ui) {
          return $(this).css("visibility", "visible");
        }
      });
      return $(".client-droppable").droppable({
        tolerance: 'pointer',
        hoverClass: 'client-droppable-hover',
        drop: function(event, ui) {
          var client, client_id;
          client_id = $(ui.draggable).data('id');
          client = $scope.findById($scope.clients, client_id);
          if (client.archive_state !== 'possible') {
            $scope.clients = removeById($scope.clients, client_id);
            Attachment.update({
              id: client.attachment_id,
              hide: 0
            });
            $scope.visible_clients_count++;
          }
          return $scope.$apply();
        }
      });
    };
  }).controller('AccountsCtrl', function($rootScope, $scope, $http, $timeout, Account, PaymentMethods, Archive, Grades, Attachment, AttachmentState, AttachmentStates, Weekdays, PhoneService, AttachmentVisibility, DebtTypes, YesNo, Tutor, ArchiveStates, Checked) {
    var bindDraggable, getAccountEndDate, getAccountStartDate, getCalendarStartDate, getCommission, moveCursor, renderData;
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
        bindDraggable();
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
      return moment(date).day();
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
      return $.each($scope.tutor.last_accounts, function(index, account) {
        return Account.update(account);
      });
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
    return bindDraggable = function() {
      $(".client-draggable").draggable({
        helper: 'clone',
        revert: 'invalid',
        appendTo: 'body',
        activeClass: 'drag-active',
        start: function(event, ui) {
          return $(this).css("visibility", "hidden");
        },
        stop: function(event, ui) {
          return $(this).css("visibility", "visible");
        }
      });
      return $(".client-droppable").droppable({
        tolerance: 'pointer',
        hoverClass: 'client-droppable-hover',
        drop: function(event, ui) {
          var client, client_id;
          client_id = $(ui.draggable).data('id');
          client = $scope.findById($scope.clients, client_id);
          if (client.archive_state !== 'possible') {
            $scope.clients = removeById($scope.clients, client_id);
            ajaxStart();
            Attachment.update({
              id: client.attachment_id,
              hide: 1
            }, function() {
              return ajaxEnd();
            });
            $scope.hidden_clients_count++;
          }
          return $scope.$apply();
        }
      });
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
              if (ref = client_metro.station_id.toString(), indexOf.call(marker.tutor.svg_map, ref) >= 0) {
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
    var bindDroppable, bindTutorMarkerEvents, filterMarkers, rebindDraggable, repaintChosen, saveSelectedList, showClientOnMap, showTutorsOnMap, unsetAllMarkers, unsetSelected;
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
      return $scope.client.$update().then(function() {
        return $scope.ajaxEnd();
      });
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
    $scope.setList = function(list) {
      $scope.selected_list = list;
      if ($scope.list_map) {
        $scope.showListMap();
      }
      return delete $scope.selected_attachment;
    };
    $scope.listExists = function(subject_id) {
      return _.findWhere($scope.selected_request.lists, {
        subject_id: parseInt(subject_id)
      }) !== void 0;
    };
    $scope.selectRequest = function(request) {
      $scope.selected_request = request;
      return delete $scope.selected_list;
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
        return $scope.client.markers = markers;
      });
    };
    $scope.saveMarkers = function() {
      return $('#gmap-modal').modal('hide');
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
    var TRANSPARENT_MARKER, bindTutorMarkerEvents, clicks, findIntersectingMetros, markerClusterer, rebindDraggable, repaintChosen, showClientOnMap, showTutorsOnMap, unsetAllMarkers;
    bindArguments($scope, arguments);
    TRANSPARENT_MARKER = 0.3;
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
        revert: function(valid) {
          if (valid) {
            return true;
          }
          $scope.tutor_list = removeById($scope.tutor_list, $scope.dragging_tutor.id);
          $scope.tutor_ids = _.without($scope.tutor_ids, $scope.dragging_tutor.id);
          $scope.$apply();
          return repaintChosen();
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
          new_marker = newMarker($scope.marker_id++, new google.maps.LatLng(marker.lat, marker.lng), $scope.map, marker.type);
          new_marker.metros = marker.metros;
          new_marker.tutor = tutor;
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
              if (ref = client_metro.station_id.toString(), indexOf.call(marker.tutor.svg_map, ref) >= 0) {
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
          marker.setIcon(ICON_BLUE);
        }
        if ((ref1 = marker.tutor.id, indexOf.call($scope.tutor_ids, ref1) < 0) && marker.chosen) {
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
  angular.module('Egerep').controller('EmptyCtrl', function() {});

}).call(this);

(function() {
  angular.module('Egerep').controller('GraphController', function($scope, $timeout, $http, $rootScope, SvgMap) {
    var getDistance, getDistanceObject;
    bindArguments($scope, arguments);
    $scope.map_loaded = false;
    angular.element(document).ready(function() {
      return $timeout(function() {
        SvgMap.show();
        SvgMap.el().find('#stations > g > g').each(function(index, el) {
          $(el).on('mouseenter', function() {
            $scope.hovered_station_id = parseInt($(this).attr('id').replace(/[^\d]/g, ''));
            return $scope.$apply();
          });
          return $(el).on('mouseleave', function() {
            $scope.hovered_station_id = void 0;
            return $scope.$apply();
          });
        });
        SvgMap.map.options.clickCallback = function(id) {
          if (SvgMap.map.getSelected().length > 2) {
            SvgMap.map.deselectAll();
            SvgMap.map.select(id);
          }
          return $scope.selected = SvgMap.map.getSelected();
        };
        return $scope.map_loaded = true;
      }, 500);
    });
    $scope.$watch('selected', function(newVal, oldVal) {
      if (newVal === void 0) {
        return;
      }
      if (newVal.length === 2) {
        return $scope.new_distance = getDistance(newVal[0], newVal[1]);
      }
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
        $scope.selected = [];
        return SvgMap.map.deselectAll();
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
      from = Math.min(from, to);
      to = Math.max(from, to);
      return _.find($scope.distances, {
        from: from,
        to: to
      });
    };
  });

}).call(this);

(function() {
  angular.module('Egerep').controller('LoginCtrl', function($scope, $http) {
    angular.element(document).ready(function() {
      return $scope.l = Ladda.create(document.querySelector('#login-submit'));
    });
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
  angular.module('Egerep').controller('PeriodsIndex', function($scope, $timeout, $rootScope, $http, PaymentMethods, DebtTypes) {
    var getCommission, load;
    bindArguments($scope, arguments);
    $rootScope.frontend_loading = true;
    $timeout(function() {
      load($scope.page);
      return $scope.current_page = $scope.page;
    });
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
      load($scope.current_page);
      return paginate('periods', $scope.current_page);
    };
    return load = function(page) {
      var params;
      params = '?page=' + page;
      return $http.get("api/periods" + params).then(function(response) {
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
  });

}).call(this);

//# sourceMappingURL=app.js.map
