'use strict';

angular.module('sa.projects', [
    'sa.services'
  ])
  .controller('ProjectsCtrl', [ '$scope', function($scope) {

  }])
  .controller('PieCtrl', [ '$scope', 'PublicProjectService', function($scope, PublicProjectService) {

    var margin = {top: 20, right: 40, bottom: 20, left: 40},
    width =  750 - margin.left - margin.right,
    height = 300 - margin.top - margin.bottom,
    radius = Math.min(width, height) / 2;

    var color = d3.scale.ordinal()
    .range(["#98abc5", "#8a89a6", "#7b6888", "#6b486b", "#a05d56", "#d0743c", "#ff8c00"]);

    var arc = d3.svg.arc()
        .outerRadius(radius - 10)
        .innerRadius(0);

    var pie = d3.layout.pie()
        .sort(null)
        .value(function(d) { return d.value; });

    var chart = d3.select(".chartPie")
        .attr("width", width + margin.left + margin.right)
        .attr("height", height + margin.top + margin.bottom)
      .append("g")
        .attr("transform", "translate(" + width / 2 + "," + height / 2 + ")");

    var projects = PublicProjectService.query(function() {
      var temp = {};
      projects.forEach(function(d) {
//        console.log(d);
        if (temp[d.type] == undefined) {
          temp[d.type] = 1;
        } else {
          temp[d.type]++;
        }
      });
      var data = [];
      for (var key in temp) {
        data.push({type: key, value: temp[key]});
      }

      console.log(data);

      var g = chart.selectAll(".arc")
          .data(pie(data))
        .enter().append("g")
          .attr("class", "arc");

      g.append("path")
          .attr("d", arc)
          .style("fill", function(d) { return color(d.data.type); });

      g.append("text")
          .attr("transform", function(d) { return "translate(" + arc.centroid(d) + ")"; })
          .attr("dy", ".35em")
          .style("text-anchor", "middle")
          .text(function(d) { return d.data.type; });


    });

    function type(d) {
      d.value = +d.value; // coerce to number
      return d;
    }

  }])
  .controller('PublicProjectsCtrl', [ '$scope', 'PublicProjectService', function($scope, PublicProjectService) {

    var margin = {top: 20, right: 40, bottom: 20, left: 40},
    width =  750 - margin.left - margin.right,
    height = 300 - margin.top - margin.bottom;

    var parseDate = d3.time.format("%Y-%m-%dT%H:%M:%S%Z").parse;

    var x = d3.time.scale()
        .range([0, width]);

    var y = d3.scale.linear()
        .range([height, 0]);

    var xAxis = d3.svg.axis()
        .scale(x)
        .orient("bottom");

    var yAxis = d3.svg.axis()
        .scale(y)
        .orient("left");

    var line = d3.svg.line()
        .x(function(d) { return x(d.created_on); })
        .y(function(d) { return y(d.total); });

    var chart = d3.select(".chartPublic")
        .attr("width", width + margin.left + margin.right)
        .attr("height", height + margin.top + margin.bottom)
      .append("g")
        .attr("transform", "translate(" + margin.left + "," + margin.top + ")");

    $scope.projects = PublicProjectService.query(function() {
      var sum = 0;
      $scope.projects.forEach(function(d) {
//        console.log(d);
        d.created_on = parseDate(d.created_on);
        d.total = sum++;
      })
      x.domain(d3.extent($scope.projects, function(d) { return d.created_on; }));
      y.domain(d3.extent($scope.projects, function(d) { return d.total; }));

      chart.append("g")
          .attr("class", "x axis")
          .attr("transform", "translate(0," + height + ")")
          .call(xAxis);

      chart.append("g")
          .attr("class", "y axis")
          .call(yAxis)
        .append("text")
          .attr("transform", "rotate(-90)")
          .attr("y", 6)
          .attr("dy", ".9em")
          .style("text-anchor", "end")
          .text("Total Projects");

      chart.append("path")
        .datum($scope.projects)
        .attr("class", "line")
        .attr("d", line);
    });

    function type(d) {
      d.value = +d.value; // coerce to number
      return d;
    }

  }])
  .controller('PrivateProjectsCtrl', [ '$scope', 'PrivateProjectService', function($scope, PrivateProjectService) {

    var margin = {top: 20, right: 40, bottom: 20, left: 40},
    width =  750 - margin.left - margin.right,
    height = 300 - margin.top - margin.bottom;

    var parseDate = d3.time.format("%Y-%m-%dT%H:%M:%S%Z").parse;

    var x = d3.time.scale()
        .range([0, width]);

    var y = d3.scale.linear()
        .range([height, 0]);

    var xAxis = d3.svg.axis()
        .scale(x)
        .orient("bottom");

    var yAxis = d3.svg.axis()
        .scale(y)
        .orient("left");

    var line = d3.svg.line()
        .x(function(d) { return x(d.created_on); })
        .y(function(d) { return y(d.total); });

    var chart = d3.select(".chartPrivate")
        .attr("width", width + margin.left + margin.right)
        .attr("height", height + margin.top + margin.bottom)
      .append("g")
        .attr("transform", "translate(" + margin.left + "," + margin.top + ")");

    $scope.projects = PrivateProjectService.query(function() {
      var sum = 0;
      $scope.projects.forEach(function(d) {
//        console.log(d);
        d.created_on = parseDate(d.created_on);
        d.total = sum++;
      })
      x.domain(d3.extent($scope.projects, function(d) { return d.created_on; }));
      y.domain(d3.extent($scope.projects, function(d) { return d.total; }));

      chart.append("g")
          .attr("class", "x axis")
          .attr("transform", "translate(0," + height + ")")
          .call(xAxis);

      chart.append("g")
          .attr("class", "y axis")
          .call(yAxis)
        .append("text")
          .attr("transform", "rotate(-90)")
          .attr("y", 6)
          .attr("dy", ".9em")
          .style("text-anchor", "end")
          .text("Total Projects");

      chart.append("path")
        .datum($scope.projects)
        .attr("class", "line")
        .attr("d", line);
    });

    function type(d) {
      d.value = +d.value; // coerce to number
      return d;
    }

  }])
  ;
