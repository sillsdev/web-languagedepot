'use strict';

angular.module('sa.projects', [
    'sa.services'
  ])
  .controller('ProjectsCtrl', [ '$scope', function($scope) {

  }])
  .controller('PieCtrl', [ '$scope', 'PublicProjectService', function($scope, PublicProjectService) {
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

//      console.log(data);

      nv.addGraph(function() {
        var chart = nv.models.pieChart().x(function(d) {
          return d.type;
        }).y(function(d) {
          return d.value
        }).showLabels(true);

        d3.select(".chartPie").datum(data).transition().duration(1200).call(chart);

        return chart;
      });

    });

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
/*
    var chart = d3.select(".chartPublic")
        .attr("width", width + margin.left + margin.right)
        .attr("height", height + margin.top + margin.bottom)
      .append("g")
        .attr("transform", "translate(" + margin.left + "," + margin.top + ")");
*/
    var data = PublicProjectService.query(function() {
      var sum = 0;
      data.forEach(function(d) {
        d.created_on = parseDate(d.created_on);
        d.total = sum++;
      });
      console.log(data);
      
      nv.addGraph(function() {
	  var chart = nv.models.lineChart();
	  chart.x(function(d) {
	    return d.created_on;
	  });
	  chart.y(function(d) {
	    return d.total;
	  });

	  chart.xAxis
	    .tickFormat(d3.format(',f'));

	  chart.yAxis
	    .tickFormat(d3.format(',f'));

//	  chart.y2Axis
//	    .tickFormat(d3.format(',f'));

	  d3.select('.chartPublic')
	    .datum(data)
	    .transition().duration(500)
	    .call(chart)
	    ;

	  nv.utils.windowResize(chart.update);

	  return chart;
	});

/*      
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
*/
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
