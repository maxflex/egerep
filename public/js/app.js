(function() {
  angular.module("Egerep", ['ngSanitize', 'ngResource', 'ngMaterial', 'ngMap']).config([
    '$compileProvider', function($compileProvider) {
      return $compileProvider.aHrefSanitizationWhitelist(/^\s*(https?|ftp|mailto|chrome-extension|sip):/);
    }
  ]).run(function($rootScope) {
    $rootScope.laroute = laroute;
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
    $rootScope.toggleEnum = function(ngModel, status, ngEnum) {
      var status_id, statuses;
      statuses = Object.keys(ngEnum);
      status_id = statuses.indexOf(ngModel[status]);
      status_id++;
      if (status_id > (statuses.length - 1)) {
        status_id = 0;
      }
      return ngModel[status] = statuses[status_id];
    };
    $rootScope.formatDateTime = function(date) {
      return moment(date).format("DD.MM.YY в HH:mm");
    };
    $rootScope.dialog = function(id) {
      return $("#" + id).modal('show');
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
        $timeout(function() {
          return $scope.comments = Comment.query({
            entity_type: $scope.entityType,
            entity_id: $scope.entityId
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
  angular.module('Egerep').directive('phones', function() {
    return {
      restrict: 'E',
      templateUrl: 'directives/phones',
      scope: {
        entity: '='
      },
      controller: function($scope, $timeout) {
        $timeout(function() {
          return $scope.level = $scope.entity.phone3 !== "" ? 3 : $scope.entity.phone2 !== "" ? 2 : 1;
        }, 100);
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
        $scope.sms = function(number) {
          $('#sms-modal').modal('show');
          return $scope.$parent.sms_number = number;
        };
        return $scope.call = function(number) {
          return location.href = "sip:" + number.replace(/[^0-9]/g, '');
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
        title: '@'
      },
      templateUrl: 'directives/ngselect',
      controller: function($scope, $element, $attrs, $timeout) {
        $scope.title = $attrs.title;
        $scope.multiple = $attrs.hasOwnProperty('multiple');
        return $scope.$watch('model', function(newVal, oldVal) {
          console.log(newVal, oldVal);
          if (newVal === void 0) {
            return;
          }
          if (oldVal === void 0) {
            spe($element, 'предмет');
          }
          if (oldVal !== void 0) {
            return spRefresh($element);
          }
        });
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
  angular.module('Egerep').controller("ClientsIndex", function($scope, $timeout, Client) {
    return $scope.clients = Client.query();
  }).controller("ClientsForm", function($scope, $timeout, $interval, $http, Client, User, RequestStatus, Subjects) {
    $scope.RequestStatus = RequestStatus;
    $scope.Subjects = Subjects;
    $scope.edit = function() {
      $scope.ajaxStart();
      return $scope.client.$update().then(function(response) {
        return $scope.ajaxEnd();
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
          if (client.subject_list !== null) {
            $scope.selected_list_id = client.subject_list[0];
            if (client.attachments[$scope.selected_list_id]) {
              return $scope.selected_attachment = client.attachments[$scope.selected_list_id][0];
            }
          }
        });
      }
    });
    $scope.attachmentExists = function(subject_id, tutor_id) {
      if ($scope.client.attachments[subject_id] === void 0) {
        return false;
      }
      return _.findWhere($scope.client.attachments[subject_id], {
        tutor_id: tutor_id
      }) !== void 0;
    };
    $scope.selectAttachment = function(tutor_id) {
      return $scope.selected_attachment = _.findWhere($scope.client.attachments[$scope.selected_list_id], {
        tutor_id: tutor_id
      });
    };
    $scope.setList = function(subject_id) {
      console.log(subject_id);
      return $scope.selected_list_id = subject_id;
    };
    $scope.selectRequest = function(request) {
      return $scope.selected_request = request;
    };
    $scope.toggleUser = function() {
      var new_user;
      new_user = _.find($scope.users, function(user) {
        return user.id > $scope.selected_request.user.id;
      });
      if (new_user === void 0) {
        new_user = $scope.users[0];
      }
      return $scope.selected_request.user = new_user;
    };
    $scope.getUser = function(user_id) {
      return _.findWhere($scope.users, {
        id: user_id
      });
    };
    $scope.addListSubject = function() {
      $scope.client.subject_list.push($scope.list_subject_id);
      $scope.client.lists[$scope.list_subject_id] = [];
      $scope.selected_list_id = $scope.list_subject_id;
      delete $scope.list_subject_id;
      return $('#add-subject').modal('hide');
    };
    $scope.addListTutor = function() {
      $scope.client.lists[$scope.selected_list_id].push($scope.list_tutor_id);
      delete $scope.list_tutor_id;
      return $('#add-tutor').modal('hide');
    };
    $scope.newAttachment = function(tutor_id, subject_id) {
      var new_attachment;
      if (!$scope.client.attachments[subject_id]) {
        $scope.client.attachments[subject_id] = [];
      }
      new_attachment = {
        tutor_id: tutor_id,
        client_id: $scope.id
      };
      $scope.client.attachments[subject_id].push(new_attachment);
      return $scope.selected_attachment = new_attachment;
    };
    $scope.addRequest = function() {
      var new_request;
      new_request = {
        id: null,
        client_id: $scope.id,
        status: 'new'
      };
      $scope.client.requests.push(new_request);
      return $scope.selected_request = new_request;
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
    return $scope.$watch('selected_attachment', function(newVal, oldVal) {
      if (newVal === void 0) {
        return;
      }
      if (oldVal === void 0) {
        sp('attachment-subjects', 'предмет');
      }
      if (oldVal !== void 0) {
        return spRefresh('attachment-subjects');
      }
    });
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
  angular.module('Egerep').run(function($rootScope) {
    return $rootScope.genders = {
      male: 'Мужской',
      female: 'Женский'
    };
  }).controller("TutorsIndex", function($scope, $timeout, Tutor) {
    return $scope.tutors = Tutor.query();
  }).controller("TutorsForm", function($scope, $timeout, $interval, Tutor, SvgMap, Subjects, Grades) {
    var _setMarkers;
    $scope.SvgMap = SvgMap;
    $scope.Subjects = Subjects;
    $scope.Grades = Grades;
    $timeout(function() {
      if ($scope.id > 0) {
        return $scope.tutor = Tutor.get({
          id: $scope.id
        });
      }
    });
    $scope.$watch('tutor.subjects', function(newVal, oldVal) {
      if (newVal === void 0) {
        return;
      }
      if (oldVal === void 0) {
        sp('tutor-subjects', 'предмет');
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
        sp('tutor-grades', 'калссы');
      }
      if (oldVal !== void 0) {
        return spRefresh('tutor-grades');
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
    $scope.editOld = function() {
      var old_markers;
      $scope.saving = true;
      $scope.tutor['svg_map[]'] = $scope.tutor.svg_map;
      old_markers = _setMarkers();
      return Tutor.update($scope.tutor, {
        id: $scope.id
      }, function() {
        $scope.tutor.markers = old_markers;
        return $scope.saving = false;
      });
    };
    $scope.edit = function() {
      var old_markers;
      $scope.saving = true;
      $scope.tutor['svg_map[]'] = $scope.tutor.svg_map;
      old_markers = _setMarkers();
      return $scope.tutor.$update().then(function(response) {
        $scope.tutor.markers = old_markers;
        return $scope.saving = false;
      });
    };
    _setMarkers = function() {
      var new_markers, old_markers;
      new_markers = [];
      old_markers = $scope.tutor.markers;
      delete $scope.tutor.markers;
      $.each(old_markers, function(index, marker) {
        return new_markers.push(_.pick(marker, 'lat', 'lng', 'type'));
      });
      $scope.tutor['markers[]'] = new_markers;
      return old_markers;
    };
    $scope.marker_id = 1;
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
          return $scope.gmap.setZoom(13);
        }
      }
    };
    $scope.gmapAddMarker = function(event) {
      var marker;
      marker = newMarker($scope.marker_id++, event.latLng, $scope.map);
      $scope.tutor.markers.push(marker);
      marker.setMap($scope.gmap);
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
      var markers;
      markers = [];
      $.each($scope.tutor.markers, function(index, marker) {
        marker = newMarker($scope.marker_id++, new google.maps.LatLng(marker.lat, marker.lng), $scope.map, marker.type);
        marker.setMap($scope.map);
        $scope.bindMarkerDelete(marker);
        $scope.bindMarkerChangeType(marker);
        return markers.push(marker);
      });
      return $scope.tutor.markers = markers;
    };
    return $scope.saveMarkers = function() {
      return $('#gmap-modal').modal('hide');
    };
  });

}).call(this);

(function() {
  angular.module('Egerep').value('RequestStatus', {
    "new": 'новая',
    finished: 'выполненная'
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
    all: ['математика', 'физика', 'русский', 'литература', 'английский', 'история', 'обществознание', 'химия', 'биология', 'информатика'],
    full: ['Математика', 'Физика', 'Русский язык', 'Литература', 'Английский язык', 'История', 'Обществознание', 'Химия', 'Биология', 'Информатика'],
    dative: ['математике', 'физике', 'русскому языку', 'литературе', 'английскому языку', 'истории', 'обществознанию', 'химии', 'биологии', 'информатике'],
    short: ['М', 'Ф', 'Р', 'Л', 'А', 'Ис', 'О', 'Х', 'Б', 'Ин'],
    three_letters: ['МАТ', 'ФИЗ', 'РУС', 'ЛИТ', 'АНГ', 'ИСТ', 'ОБЩ', 'ХИМ', 'БИО', 'ИНФ'],
    short_eng: ['math', 'phys', 'rus', 'lit', 'eng', 'his', 'soc', 'chem', 'bio', 'inf']
  });

}).call(this);

(function() {
  var apiPath, updateMethod;

  angular.module('Egerep').factory('Sms', function($resource) {
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
    }, updateMethod());
  });

  apiPath = function(entity) {
    return "api/" + entity + "/:id";
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
  angular.module('Egerep').service('SvgMap', function() {
    this.map = new SVGMap({
      iframeId: 'map',
      clicable: true,
      places: [],
      placesHash: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 32, 33, 35, 36, 37, 38, 39, 40, 41, 42, 43, 44, 45, 46, 47, 48, 49, 50, 51, 52, 53, 54, 55, 56, 57, 58, 59, 60, 61, 62, 63, 64, 65, 66, 67, 68, 69, 70, 71, 72, 74, 75, 76, 77, 78, 79, 80, 81, 82, 83, 84, 85, 86, 87, 88, 89, 90, 91, 92, 93, 94, 95, 96, 97, 98, 99, 100, 101, 102, 103, 104, 105, 106, 107, 108, 109, 110, 111, 112, 113, 114, 115, 116, 117, 118, 119, 120, 121, 122, 123, 124, 125, 126, 127, 128, 129, 130, 131, 132, 133, 134, 135, 136, 137, 138, 139, 140, 141, 142, 143, 144, 145, 146, 147, 148, 149, 150, 151, 152, 153, 154, 155, 156, 157, 158, 159, 160, 161, 162, 163, 164, 165, 166, 180, 186, 187, 188, 189, 190, 191, 192, 193, 194, 195, 196, 197, 198, 199, 200, 201, 202, 203, 204, 205, 206, 207, 208],
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
          "points": [33, 108, 125, 148, 151, 164]
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
          "points": [30, 35, 57, 61, 107, 115, 134, 205, 206]
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

//# sourceMappingURL=app.js.map
