(function() {
  var indexOf = [].indexOf || function(item) { for (var i = 0, l = this.length; i < l; i++) { if (i in this && this[i] === item) return i; } return -1; };

  angular.module("Egerep", ['ngSanitize', 'ngResource', 'ngMaterial', 'ngMap', 'ngAnimate', 'ui.sortable', 'ui.bootstrap', 'angular-ladda']).config([
    '$compileProvider', function($compileProvider) {
      return $compileProvider.aHrefSanitizationWhitelist(/^\s*(https?|ftp|mailto|chrome-extension|sip):/);
    }
  ]).run(function($rootScope, $q) {
    $rootScope.laroute = laroute;
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
      if (!recursion && (ref = parseInt(ngModel[status]), indexOf.call(skip_values, ref) >= 0) && (ref1 = $rootScope.$$childHead.user.id, indexOf.call(allowed_user_ids, ref1) < 0)) {
        return;
      }
      statuses = Object.keys(ngEnum);
      status_id = statuses.indexOf(ngModel[status].toString());
      status_id++;
      if (status_id > (statuses.length - 1)) {
        status_id = 0;
      }
      ngModel[status] = statuses[status_id];
      if (indexOf.call(skip_values, status_id) >= 0 && (ref2 = $rootScope.$$childHead.user.id, indexOf.call(allowed_user_ids, ref2) < 0)) {
        return $rootScope.toggleEnum(ngModel, status, ngEnum, skip_values, allowed_user_ids, true);
      }
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
  angular.module('Egerep').filter('cut', function() {
    return function(value, wordwise, max, tail) {
      var lastspace;
      if (!value) {
        return 'имя не указано';
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
      return value + (tail || '…');
    };
  }).controller('AccountsHiddenCtrl', function($scope, Grades, Attachment) {
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
          $scope.clients = removeById($scope.clients, client_id);
          Attachment.update({
            id: client.attachment_id,
            hide: 0
          });
          $scope.visible_clients_count++;
          return $scope.$apply();
        }
      });
    };
  }).controller('AccountsCtrl', function($rootScope, $scope, $http, $timeout, Account, PaymentMethods, DebtTypes, AccountPeriods, Grades, Attachment) {
    var bindDraggable, getAccountEndDate, getAccountStartDate, getCalendarStartDate, getCommission, moveCursor, renderData;
    bindArguments($scope, arguments);
    $scope.current_scope = $scope;
    $scope.current_period = 0;
    angular.element(document).ready(function() {
      return $scope.loadPage();
    });
    $scope.loadPage = function(type) {
      $rootScope.frontend_loading = true;
      return $http.get("api/accounts/" + $scope.tutor_id + "?type=" + AccountPeriods[$scope.current_period]).success(function(response) {
        renderData(response);
        return $scope.current_period++;
      });
    };
    renderData = function(data) {
      $scope.tutor = data.tutor;
      $scope.date_limit = data.date_limit;
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
            return $scope.tutor.accounts = removeById($scope.tutor.accounts, account.id);
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
        current_date = moment($scope.date_limit).format('YYYY-MM-DD');
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
      $('tr[class^=\'tr-\']').removeClass('selected');
      $('.tr-' + date).addClass('selected');
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
          y--;
          break;
        case "down":
          y++;
      }
      if (x < 0 || y < 0) {
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
    $scope.periodsCursor = function(y, x, event) {
      var i, original_element;
      original_element = $("#i-" + y + "-" + x);
      if (original_element.val() === "0" && original_element.val().length) {
        i = y - 1;
        while (i > 0) {
          if ($('#i-' + i + '-' + x).length && $('#i-' + i + '-' + x).val()) {
            original_element.val($('#i-' + i + '-' + x).val());
            break;
          }
          i--;
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
          $scope.clients = removeById($scope.clients, client_id);
          Attachment.update({
            id: client.attachment_id,
            hide: 1
          });
          $scope.hidden_clients_count++;
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
      return indexOf.call($scope.list.tutor_ids, tutor_id) >= 0;
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
      if (indexOf.call($scope.list.tutor_ids, tutor_id) >= 0) {
        $scope.list.tutor_ids = _.without($scope.list.tutor_ids, tutor_id);
      } else {
        $scope.list.tutor_ids.push(tutor_id);
      }
      repaintChosen();
      return $scope.list.$update();
    };
    repaintChosen = function() {
      return $scope.markers.forEach(function(marker) {
        var ref, ref1;
        if ((ref = marker.tutor.id, indexOf.call($scope.list.tutor_ids, ref) >= 0) && !marker.chosen) {
          marker.chosen = true;
          marker.setOpacity(1);
          marker.setIcon(ICON_BLUE);
        }
        if ((ref1 = marker.tutor.id, indexOf.call($scope.list.tutor_ids, ref1) < 0) && marker.chosen) {
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
      return showClientOnMap();
    });
  });

}).call(this);

(function() {
  angular.module('Egerep').factory('Attachment', function($resource) {
    return $resource('api/attachments/:id', {}, {
      update: {
        method: 'PUT'
      }
    });
  }).controller('AttachmentsIndex', function($rootScope, $scope, $timeout, $http, AttachmentStates) {
    var loadAttachments;
    _.extend(AttachmentStates, {
      all: 'все'
    });
    bindArguments($scope, arguments);
    $rootScope.frontend_loading = true;
    $scope.changeList = function(state_id) {
      $scope.chosen_state_id = state_id;
      $scope.current_page = 1;
      ajaxStart();
      loadAttachments(1);
      ajaxEnd();
      return window.history.pushState(state_id, '', 'attachments/' + state_id.toLowerCase());
    };
    $timeout(function() {
      loadAttachments($scope.page);
      return $scope.current_page = $scope.page;
    });
    $scope.pageChanged = function() {
      loadAttachments($scope.current_page);
      return paginate('attachments', $scope.current_page);
    };
    return loadAttachments = function(page) {
      var params;
      params = '?page=' + page;
      if ($scope.chosen_state_id) {
        params += '&state=' + $scope.chosen_state_id;
      } else {
        params += '&state=' + 'new';
      }
      return $http.get("api/attachments" + params).then(function(response) {
        $rootScope.frontendStop();
        $scope.data = response.data;
        return $scope.attachments = $scope.data.data;
      });
    };
  });

}).call(this);

(function() {
  angular.module('Egerep').controller("ClientsIndex", function($scope, $timeout, Client) {
    return $scope.clients = Client.query();
  }).controller("ClientsForm", function($scope, $rootScope, $timeout, $interval, $http, Client, Request, RequestList, User, RequestStates, Subjects, Grades, Attachment, ReviewStates, ArchiveStates, AttachmentStates, ReviewScores, Archive, Review, ApiService, UserService) {
    var filterMarkers, saveSelectedList, unsetSelected;
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
      $scope.ajaxStart();
      filterMarkers();
      return $scope.client.$update().then(function(response) {
        return $scope.ajaxEnd();
      });
    };
    $timeout(function() {
      $('.teacher-remove-droppable').droppable({
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
          sp('list-subjects', 'выберите предмет');
          $scope.parseHash();
          return $rootScope.frontendStop();
        });
      }
    });
    saveSelectedList = function() {
      return RequestList.update($scope.selected_list);
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
      return Attachment.save({
        grade: $scope.client.grade,
        tutor_id: tutor_id,
        subjects: $scope.selected_list.subjects,
        request_list_id: $scope.selected_list.id
      }, function(new_attachment) {
        $scope.selected_attachment = new_attachment;
        return $scope.selected_list.attachments.push(new_attachment);
      });
    };
    $scope.addRequest = function() {
      var new_request;
      new_request = new Request({
        client_id: $scope.id
      });
      return new_request.$save().then(function(data) {
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
      $scope.tutor_ids = [];
      matches = newVal.match(/Репетитор [\d]+/gi);
      return $.each(matches, function(index, match) {
        var tutor_id;
        tutor_id = match.match(/[\d]+/gi);
        return $scope.tutor_ids.push(parseInt(tutor_id));
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
    $scope.marker_id = 1;
    filterMarkers = function() {
      var new_markers;
      new_markers = [];
      $.each($scope.client.markers, function(index, marker) {
        return new_markers.push(_.pick(marker, 'lat', 'lng', 'type', 'metros'));
      });
      return $scope.client.markers = new_markers;
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
      marker = newMarker($scope.marker_id++, event.latLng, $scope.map);
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
          console.log('id', t.id, m.id);
          if (m !== void 0 && t.id === m.id) {
            return $scope.client.markers.splice(index, 1);
          }
        });
      });
    };
    $scope.bindMarkerChangeType = function(marker) {
      return google.maps.event.addListener(marker, 'click', function(event) {
        if (this.type === 'green') {
          this.type = 'red';
          return this.setIcon(ICON_RED);
        } else if (this.type === 'red') {
          this.type = 'blue';
          return this.setIcon(ICON_BLUE);
        } else {
          this.type = 'green';
          return this.setIcon(ICON_GREEN);
        }
      });
    };
    $scope.searchMap = function(address) {
      return $scope.geocoder.geocode({
        address: address + ', московская область',
        bounds: $scope.RECOM_BOUNDS
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
        $.each($scope.client.markers, function(index, marker) {
          var new_marker;
          new_marker = newMarker($scope.marker_id++, new google.maps.LatLng(marker.lat, marker.lng), $scope.map, marker.type);
          new_marker.metros = marker.metros;
          new_marker.setMap($scope.map);
          $scope.bindMarkerDelete(new_marker);
          $scope.bindMarkerChangeType(new_marker);
          return markers.push(new_marker);
        });
        return $scope.client.markers = markers;
      });
    };
    return $scope.saveMarkers = function() {
      return $('#gmap-modal').modal('hide');
    };
  });

}).call(this);

//# sourceMappingURL=app.js.map
