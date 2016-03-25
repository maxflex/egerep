(function() {
  var indexOf = [].indexOf || function(item) { for (var i = 0, l = this.length; i < l; i++) { if (i in this && this[i] === item) return i; } return -1; };

  angular.module("Egerep", ['ngSanitize', 'ngResource', 'ngMaterial', 'ngMap', 'ngAnimate', 'ui.sortable', 'ui.bootstrap']).config([
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
    return $rootScope.ajaxEnd = function() {
      ajaxEnd();
      return $rootScope.saving = false;
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
  angular.module('Egerep').controller('AccountsCtrl', function($scope, Account, PaymentMethods, DebtTypes) {
    var getAccountEndDate, getAccountStartDate, getCalendarStartDate;
    $scope.PaymentMethods = PaymentMethods;
    $scope.DebtTypes = DebtTypes;
    $scope.current_scope = $scope;
    getAccountStartDate = function(index) {
      if (index > 0) {
        return moment($scope.tutor.accounts[index - 1].date_end).add(1, 'days').toDate();
      } else {
        return new Date($scope.first_attachment_date);
      }
    };
    getAccountEndDate = function(index) {
      if ((index + 1) === $scope.tutor.accounts.length) {
        return '';
      } else {
        return moment($scope.tutor.accounts[index + 1].date_end).subtract(1, 'days').toDate();
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
      $scope.selected_account = $scope.tutor.accounts[index];
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
      return $.each($scope.tutor.accounts, function(index, account) {
        return Account.update(account);
      });
    };
    $scope.getFakeDates = function() {
      var current_date, dates;
      dates = [];
      current_date = moment().subtract(10, 'days').format('YYYY-MM-DD');
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
        current_date = moment($scope.first_attachment_date).format('YYYY-MM-DD');
      } else {
        current_date = moment($scope.tutor.accounts[index - 1].date_end).add(1, 'days').format('YYYY-MM-DD');
      }
      while (current_date <= $scope.tutor.accounts[index].date_end) {
        dates.push(current_date);
        current_date = moment(current_date).add(1, 'days').format('YYYY-MM-DD');
      }
      return dates;
    };
    getCalendarStartDate = function() {
      var date_end;
      if ($scope.tutor.accounts.length > 0) {
        date_end = $scope.tutor.accounts[$scope.tutor.accounts.length - 1].date_end;
        return moment(date_end).add(1, 'days').toDate();
      } else {
        return new Date($scope.first_attachment_date);
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
    return $scope.addAccount = function() {
      return Account.save({
        date_end: convertDate($scope.new_account_date_end),
        tutor_id: $scope.tutor.id
      }, function(new_account) {
        $scope.tutor.accounts.push(new_account);
        return $scope.closeDialog('add-account');
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
      return markerClusterer = new MarkerClusterer($scope.map, $scope.markers);
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
  angular.module('Egerep').controller("ClientsIndex", function($scope, $timeout, Client) {
    return $scope.clients = Client.query();
  }).controller("ClientsForm", function($scope, $rootScope, $timeout, $interval, $http, Client, Request, RequestList, User, RequestStates, Subjects, Grades, Attachment, ReviewStates, ArchiveStates, ReviewScores, Archive, Review, ApiService, UserService) {
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
    $scope.fake_user = {
      id: 0,
      login: 'system'
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

(function() {
  angular.module('Egerep').controller('LoginCtrl', function($scope, $http) {
    return $scope.checkFields = function() {
      return $http.post('login', {
        login: $scope.login,
        password: $scope.password
      }).then(function(response) {
        if (response.data === true) {
          return location.reload();
        }
      });
    };
  });

}).call(this);

(function() {
  angular.module('Egerep').factory('Request', function($resource) {
    return $resource('api/requests/:id', {}, {
      update: {
        method: 'PUT'
      }
    });
  }).controller('RequestsIndex', function($scope, Request) {
    return $scope.requests = Request.query();
  }).controller('RequestsForm', function($scope) {
    return console.log('here');
  });

}).call(this);

(function() {
  angular.module('Egerep').controller("TutorsIndex", function($scope, $rootScope, $timeout, $http, Tutor, TutorStates, UserService, PusherService) {
    var loadTutors;
    $rootScope.frontend_loading = true;
    $scope.Tutor = Tutor;
    $scope.TutorStates = TutorStates;
    $scope.UserService = UserService;
    $scope.state = localStorage.getItem('tutors_index_state');
    $scope.user_id = localStorage.getItem('tutors_index_user_id');
    PusherService.init('ResponsibleUserChanged', function(data) {
      var tutor;
      if (tutor = findById($scope.tutors, data.tutor_id)) {
        tutor.responsible_user_id = data.responsible_user_id;
        return $scope.$apply();
      }
    });
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
      $http.get("api/tutors" + params).then(function(response) {
        $rootScope.frontendStop();
        $scope.data = response.data;
        return $scope.tutors = $scope.data.data;
      });
      return $http.post("api/tutors/counts", {
        state: $scope.state,
        user_id: $scope.user_id
      }).then(function(response) {
        $scope.state_counts = response.data.state_counts;
        $scope.user_counts = response.data.user_counts;
        return $timeout(function() {
          $('#change-state option, #change-user option').each(function(index, el) {
            $(el).data('subtext', $(el).attr('data-subtext'));
            return $(el).data('content', $(el).attr('data-content'));
          });
          return $('#change-state, #change-user').selectpicker('refresh');
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
  }).controller("TutorsForm", function($scope, $rootScope, $timeout, Tutor, SvgMap, Subjects, Grades, ApiService, TutorStates, Genders, Workplaces, Branches, BranchService, TutorService) {
    var bindCropper, bindFileUpload, filterMarkers;
    $scope.SvgMap = SvgMap;
    $scope.Subjects = Subjects;
    $scope.Grades = Grades;
    $scope.Genders = Genders;
    $scope.TutorStates = TutorStates;
    $scope.Workplaces = Workplaces;
    $scope.Branches = Branches;
    $scope.BranchService = BranchService;
    $rootScope.frontend_loading = true;
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
          success: function() {
            ajaxEnd();
            $scope.tutor.has_photo_cropped = true;
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
        minContainerHeight: 700,
        minContainerWidth: 700,
        minCropBoxWidth: 120,
        minCropBoxHeight: 150,
        preview: '.img-preview',
        viewMode: 1,
        crop: function(e) {
          var width;
          width = $('#photo-edit').cropper('getCropBoxData').width;
          if (width >= 240) {
            return $('.cropper-line, .cropper-point').css('background-color', '#158E51');
          } else {
            return $('.cropper-line, .cropper-point').css('background-color', '#D9534F');
          }
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
          $scope.tutor.photo_extension = response.result;
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
      return $timeout(function() {
        return $('#photo-edit').cropper('resize');
      }, 100);
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
            bindCropper();
            return bindFileUpload();
          }, 1000);
          return $rootScope.frontendStop();
        });
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
    $scope.$watch('tutor.in_egecentr', function(newVal, oldVal) {
      if (newVal && !$scope.tutor.login && $scope.tutor.first_name && $scope.tutor.last_name && $scope.tutor.middle_name) {
        $scope.tutor.login = TutorService.generateLogin($scope.tutor);
        return $scope.tutor.password = TutorService.generatePassword();
      }
    });
    $scope.svgSave = function() {
      return $scope.tutor.svg_map = SvgMap.save();
    };
    $scope.yearDifference = function(year) {
      return moment().format("YYYY") - year;
    };
    $scope.add = function() {
      $scope.saving = true;
      return Tutor.save($scope.tutor, function(tutor) {
        return window.location = laroute.route('tutors.edit', {
          tutors: tutor.id
        });
      });
    };
    $scope.edit = function() {
      $scope.saving = true;
      filterMarkers();
      return $scope.tutor.$update().then(function(response) {
        return $scope.saving = false;
      });
    };
    $scope.marker_id = 1;
    filterMarkers = function() {
      var new_markers;
      new_markers = [];
      $.each($scope.tutor.markers, function(index, marker) {
        return new_markers.push(_.pick(marker, 'lat', 'lng', 'type', 'metros'));
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
            return $scope.tutor.markers.splice(index, 1);
          }
        });
      });
    };
    $scope.bindMarkerChangeType = function(marker) {
      return google.maps.event.addListener(marker, 'click', function(event) {
        if (this.type === 'green') {
          this.type = 'red';
          return this.setIcon(ICON_RED);
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
        $.each($scope.tutor.markers, function(index, marker) {
          var new_marker;
          new_marker = newMarker($scope.marker_id++, new google.maps.LatLng(marker.lat, marker.lng), $scope.map, marker.type);
          new_marker.metros = marker.metros;
          new_marker.setMap($scope.map);
          $scope.bindMarkerDelete(new_marker);
          $scope.bindMarkerChangeType(new_marker);
          return markers.push(new_marker);
        });
        return $scope.tutor.markers = markers;
      });
    };
    return $scope.saveMarkers = function() {
      return $('#gmap-modal').modal('hide');
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
        entityType: '@'
      },
      controller: function($scope, $timeout, Comment) {
        $scope.$watch('entityId', function(newVal, oldVal) {
          return $scope.comments = Comment.query({
            entity_type: $scope.entityType,
            entity_id: newVal
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
        $scope.remove = function(comment) {
          $scope.comments = _.without($scope.comments, _.findWhere($scope.comments, {
            id: comment.id
          }));
          return comment.$remove();
        };
        $scope.edit = function(comment, event) {
          var element, old_text;
          old_text = comment.comment;
          element = $(event.target);
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
            if (element.attr('contenteditable')) {
              console.log(old_text);
              return element.removeAttr('contenteditable').html(old_text);
            }
          });
        };
        return $scope.submitComment = function(event) {
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
          }
          if (event.keyCode === 27) {
            return $(event.target).blur();
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
      controller: function($scope) {
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
        return $timeout(function() {
          return $($element).selectpicker({
            noneSelectedText: $scope.noneText
          });
        });
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
      controller: function($scope, $timeout, $rootScope, PhoneService) {
        $scope.PhoneService = PhoneService;
        $rootScope.dataLoaded.promise.then(function(data) {
          return $scope.level = $scope.entity.phones.length || 1;
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
        $scope.isMobile = function(number) {
          return parseInt(number[4]) === 9 || parseInt(number[1]) === 9;
        };
        return $scope.sms = function(number) {
          $('#sms-modal').modal('show');
          return $scope.$parent.sms_number = number;
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
        noneText: '@'
      },
      templateUrl: 'directives/plural',
      controller: function($scope, $element, $attrs, $timeout) {
        $scope.textOnly = $attrs.hasOwnProperty('textOnly');
        return $scope.when = {
          'age': ['год', 'года', 'лет'],
          'student': ['ученик', 'ученика', 'учеников'],
          'minute': ['минуту', 'минуты', 'минут'],
          'meeting': ['встреча', 'встречи', 'встреч'],
          'score': ['балл', 'балла', 'баллов'],
          'rubbles': ['рубль', 'рубля', 'рублей']
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
      controller: function($scope, $timeout, Sms) {
        $scope.mass = false;
        $scope.smsCount = function() {
          return SmsCounter.count($scope.message || '').messages;
        };
        $scope.send = function() {
          var sms;
          if ($scope.message) {
            sms = new Sms({
              message: $scope.message,
              to: $scope.number,
              mass: $scope.mass
            });
            return sms.$save();
          }
        };
        return $scope.$watch('number', function(newVal, oldVal) {
          console.log($scope.$parent.formatDateTime($scope.created_at));
          if (newVal) {
            return $scope.history = Sms.query({
              number: newVal
            });
          }
        });
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
  angular.module('Egerep').value('Destinations', {
    r_k: 'репетитор едет к клиенту',
    k_r: 'клиент едет к репетитору'
  }).value('Workplaces', {
    0: 'не работает в ЕГЭ-Центре',
    1: 'работает в ЕГЭ-Центре'
  }).value('Genders', {
    male: 'мужской',
    female: 'женский'
  }).value('TutorStates', {
    0: 'не установлено',
    1: 'на проверку',
    2: 'к закрытию',
    3: 'закрыто',
    4: 'к одобрению',
    5: 'одобрено'
  }).value('DebtTypes', {
    0: 'не доплатил',
    1: 'переплатил'
  }).value('PaymentMethods', {
    0: 'не установлено',
    1: 'стандартный расчет',
    2: 'яндекс.деньги',
    3: 'перевод на сотовый',
    4: 'перевод на карту'
  }).value('RequestStates', {
    "new": 'невыполненные',
    awaiting: 'в ожидании',
    finished: 'выполненные',
    deny: 'отказы'
  }).value('ArchiveStates', {
    impossible: 'невозможно',
    possible: 'возможно'
  }).value('ReviewStates', {
    unpublished: 'не опубликован',
    published: 'опубликован'
  }).value('ReviewScores', {
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
    11: 'не берет',
    12: 'не помнит',
    13: 'недоступен',
    14: 'позвонить позже'
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
      10: 'английский'
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
      10: 'Английский язык'
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
      10: 'английскому языку'
    },
    short: ['М', 'Ф', 'Р', 'Л', 'А', 'Ис', 'О', 'Х', 'Б', 'Ин'],
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
      10: 'АНГ'
    },
    short_eng: ['math', 'phys', 'rus', 'lit', 'eng', 'his', 'soc', 'chem', 'bio', 'inf']
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

  angular.module('Egerep').factory('Account', function($resource) {
    return $resource(apiPath('accounts'), {
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
    }, updateMethod());
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
  angular.module('Egerep').service('PhoneService', function($http) {
    this.call = function(number) {
      return location.href = "sip:" + number.replace(/[^0-9]/g, '');
    };
    return this;
  });

}).call(this);

(function() {
  angular.module('Egerep').service('PusherService', function($http) {
    this.init = function(channel, callback) {
      this.pusher = new Pusher('2d212b249c84f8c7ba5c', {
        encrypted: true,
        cluster: 'eu'
      });
      this.channel = this.pusher.subscribe('egerep');
      return this.channel.bind("App\\Events\\" + channel, callback);
    };
    return this;
  });

}).call(this);

(function() {
  angular.module('Egerep').service('SvgMap', function() {
    this.map = new SVGMap({
      iframeId: 'map',
      clicable: true,
      places: [],
      placesHash: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 32, 33, 35, 36, 37, 38, 39, 40, 41, 42, 43, 44, 45, 46, 47, 48, 49, 50, 51, 52, 53, 54, 55, 56, 57, 58, 59, 60, 61, 62, 63, 64, 65, 66, 67, 68, 69, 70, 71, 72, 74, 75, 76, 77, 78, 79, 80, 81, 82, 83, 84, 85, 86, 87, 88, 89, 90, 91, 92, 93, 94, 95, 96, 97, 98, 99, 100, 101, 102, 103, 104, 105, 106, 107, 108, 109, 110, 111, 112, 113, 114, 115, 116, 117, 118, 119, 120, 121, 122, 123, 124, 125, 126, 127, 128, 129, 130, 131, 132, 133, 134, 135, 136, 137, 138, 139, 140, 141, 142, 143, 144, 145, 146, 147, 148, 149, 150, 151, 152, 153, 154, 155, 156, 157, 158, 159, 160, 161, 162, 163, 164, 165, 166, 180, 186, 187, 188, 189, 190, 191, 192, 193, 194, 195, 196, 197, 198, 199, 200, 201, 202, 203, 204, 205, 206, 207, 208, 209, 210, 211],
      groups: [
        {
          "id": "1",
          "title": "внутри кольца",
          "points": [4, 8, 12, 15, 18, 19, 38, 47, 48, 51, 54, 56, 58, 60, 63, 66, 68, 71, 74, 82, 83, 86, 90, 91, 92, 102, 104, 109, 111, 120, 122, 126, 129, 131, 132, 133, 137, 138, 140, 153, 156, 157, 158, 187, 188, 189, 190, 191, 192, 193, 194, 195, 196, 198, 199]
        }, {
          "id": "2",
          "title": "красная север",
          "points": [55, 106, 124, 145, 154]
        }, {
          "id": "3",
          "title": "красная юг",
          "points": [33, 108, 125, 148, 151, 164, 209, 210]
        }, {
          "id": "4",
          "title": "зеленая север",
          "points": [9, 28, 29, 36, 112, 123]
        }, {
          "id": "5",
          "title": "зеленая юг",
          "points": [2, 39, 44, 46, 50, 53, 88, 152, 197, 202, 204]
        }, {
          "id": "6",
          "title": "синяя запад",
          "points": [32, 59, 62, 75, 76, 77, 93, 121, 127, 186, 203]
        }, {
          "id": "7",
          "title": "синяя восток",
          "points": [13, 42, 94, 95, 119, 161, 163]
        }, {
          "id": "8",
          "title": "голубая",
          "points": [11, 62, 64, 99, 128, 149, 150, 186]
        }, {
          "id": "9",
          "title": "оранжевая север",
          "points": [5, 10, 20, 26, 72, 113, 117]
        }, {
          "id": "10",
          "title": "оранжевая юг",
          "points": [3, 16, 43, 52, 65, 84, 85, 110, 135, 159, 166]
        }, {
          "id": "11",
          "title": "фиолетовая север",
          "points": [14, 87, 100, 103, 130, 141, 142, 162]
        }, {
          "id": "12",
          "title": "фиолетовая юг",
          "points": [30, 35, 57, 61, 107, 115, 134, 205, 206, 211]
        }, {
          "id": "13",
          "title": "желтая",
          "points": [1, 81, 96, 101, 114, 160, 180]
        }, {
          "id": "14",
          "title": "серая север",
          "points": [6, 17, 27, 37, 89, 97, 116, 136]
        }, {
          "id": "15",
          "title": "серая юг",
          "points": [7, 23, 45, 78, 79, 80, 105, 118, 139, 143, 147, 155, 165]
        }, {
          "id": "16",
          "title": "светло-зеленая",
          "points": [21, 31, 41, 49, 53, 57, 67, 70, 98, 101, 107, 114, 200, 201, 202]
        }, {
          "id": "17",
          "title": "бутовская",
          "points": [22, 23, 24, 84, 144, 146, 147, 207, 208]
        }, {
          "id": "18",
          "title": "каховская",
          "points": [25, 45, 46, 118, 197]
        }
      ]
    });
    this.show = function(points) {
      var map;
      $('#svg-modal').modal('show');
      map = this.map;
      map.init();
      map.selected = {};
      map.deselectAll();
      map.select(points);
      $(".legend a").unbind('click');
      return $(".legend a").on('click', function() {
        var id;
        id = $(this).attr("data-rel");
        return map.toggleGroup(id);
      });
    };
    this.save = function() {
      $('#svg-modal').modal('hide');
      return this.map.save();
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
    this.getFiltered = function(search_data) {
      return $http.post('api/tutors/filtered', search_data);
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
    this.getUser = function(user_id) {
      return _.findWhere(this.users, {
        id: user_id
      }) || system_user;
    };
    this.getLogin = function(user_id) {
      return this.getUser(user_id).login;
    };
    this.getColor = function(user_id) {
      return this.getUser(user_id).color;
    };
    this.getWithSystem = function() {
      var users;
      users = _.clone(this.users);
      users.unshift(system_user);
      return users;
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
    return this;
  });

}).call(this);

//# sourceMappingURL=app.js.map
