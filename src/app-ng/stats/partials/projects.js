'use strict';

angular.module('sa.projects', [
    'sa.services'
  ])
  .controller('ProjectsCtrl', [ '$scope', 'ProjectService', function($scope, ProjectService) {

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

    var chart = d3.select(".chart")
        .attr("width", width + margin.left + margin.right)
        .attr("height", height + margin.top + margin.bottom)
      .append("g")
        .attr("transform", "translate(" + margin.left + "," + margin.top + ")");

    $scope.projects = ProjectService.query(function() {
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
