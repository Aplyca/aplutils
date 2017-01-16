/*
 * Fluster2 0.1.1
 * Copyright (C) 2009 Fusonic GmbH
 *
 * This file is part of Fluster2.
 *
 * Fluster2 is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * Fluster2 is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library. If not, see <http://www.gnu.org/licenses/>.
 */

function Fluster2(_map, _debug) {
    var map = _map;
    var projection = new Fluster2ProjectionOverlay(map);
    var me = this;
    var clusters = new Object();
    var markersLeft = new Object();
    this.debugEnabled = _debug;
    this.gridSize = 60;
    this.markers = new Array();
    this.currentZoomLevel = -1;
    this.styles = {
        0: {
            image: 'http://gmaps-utility-library.googlecode.com/svn/trunk/markerclusterer/1.0/images/m1.png',
            textColor: '#FFFFFF',
            width: 53,
            height: 52
        },
        10: {
            image: 'http://gmaps-utility-library.googlecode.com/svn/trunk/markerclusterer/1.0/images/m2.png',
            textColor: '#FFFFFF',
            width: 56,
            height: 55
        },
        20: {
            image: 'http://gmaps-utility-library.googlecode.com/svn/trunk/markerclusterer/1.0/images/m3.png',
            textColor: '#FFFFFF',
            width: 66,
            height: 65
        }
    };
    var zoomChangedTimeout = null;

    function createClusters() {
        var zoom = map.getZoom();
        if (clusters[zoom]) {
            me.debug('Clusters for zoom level ' + zoom + ' already initialized.')
        } else {
            var clustersThisZoomLevel = new Array();
            var clusterCount = 0;
            var markerCount = me.markers.length;
            for (var i = 0; i < markerCount; i++) {
                var marker = me.markers[i];
                var markerPosition = marker.getPosition();
                var done = false;
                for (var j = clusterCount - 1; j >= 0; j--) {
                    var cluster = clustersThisZoomLevel[j];
                    if (cluster.contains(markerPosition)) {
                        cluster.addMarker(marker);
                        done = true;
                        break
                    }
                }
                if (!done) {
                    var cluster = new Fluster2Cluster(me, marker);
                    clustersThisZoomLevel.push(cluster);
                    clusterCount++
                }
            }
            clusters[zoom] = clustersThisZoomLevel;
            me.debug('Initialized ' + clusters[zoom].length + ' clusters for zoom level ' + zoom + '.')
        }
        if (clusters[me.currentZoomLevel]) {
            for (var i = 0; i < clusters[me.currentZoomLevel].length; i++) {
                clusters[me.currentZoomLevel][i].hide()
            }
        }
        me.currentZoomLevel = zoom;
        showClustersInBounds()
    }
    function showClustersInBounds() {
        var mapBounds = map.getBounds();
        for (var i = 0; i < clusters[me.currentZoomLevel].length; i++) {
            var cluster = clusters[me.currentZoomLevel][i];
            if (mapBounds.contains(cluster.getPosition())) {
                cluster.show()
            }
        }
    }
    this.zoomChanged = function () {
        window.clearInterval(zoomChangedTimeout);
        zoomChangedTimeout = window.setTimeout(createClusters, 500)
    };
    this.getMap = function () {
        return map
    };
    this.getProjection = function () {
        return projection.getP()
    };
    this.debug = function (message) {
        if (me.debugEnabled) {
            console.log('Fluster2: ' + message)
        }
    };
    this.addMarker = function (_marker) {
        me.markers.push(_marker)
    };
    this.getStyles = function () {
        return me.styles
    };
    this.initialize = function () {
        google.maps.event.addListener(map, 'zoom_changed', this.zoomChanged);
        google.maps.event.addListener(map, 'dragend', showClustersInBounds);
        window.setTimeout(createClusters, 1000)
    }
}

function Fluster2Cluster(_fluster, _marker) {
    var markerPosition = _marker.getPosition();
    this.fluster = _fluster;
    this.markers = [];
    this.bounds = null;
    this.marker = null;
    this.lngSum = 0;
    this.latSum = 0;
    this.center = markerPosition;
    this.map = this.fluster.getMap();
    var me = this;
    var projection = _fluster.getProjection();
    var gridSize = _fluster.gridSize;
    var position = projection.fromLatLngToDivPixel(markerPosition);
    var positionSW = new google.maps.Point(position.x - gridSize, position.y + gridSize);
    var positionNE = new google.maps.Point(position.x + gridSize, position.y - gridSize);
    this.bounds = new google.maps.LatLngBounds(projection.fromDivPixelToLatLng(positionSW), projection.fromDivPixelToLatLng(positionNE));
    this.addMarker = function (_marker) {
        this.markers.push(_marker)
    };
    this.show = function () {
        if (this.markers.length == 1) {
            this.markers[0].setMap(me.map)
        } else if (this.markers.length > 1) {
            for (var i = 0; i < this.markers.length; i++) {
                this.markers[i].setMap(null)
            }
            if (this.marker == null) {
                this.marker = new Fluster2ClusterMarker(this.fluster, this);
                if (this.fluster.debugEnabled) {
                    google.maps.event.addListener(this.marker, 'mouseover', me.debugShowMarkers);
                    google.maps.event.addListener(this.marker, 'mouseout', me.debugHideMarkers)
                }
            }
            this.marker.show()
        }
    };
    this.hide = function () {
        if (this.marker != null) {
            this.marker.hide()
        }
    };
    this.debugShowMarkers = function () {
        for (var i = 0; i < me.markers.length; i++) {
            me.markers[i].setVisible(true)
        }
    };
    this.debugHideMarkers = function () {
        for (var i = 0; i < me.markers.length; i++) {
            me.markers[i].setVisible(false)
        }
    };
    this.getMarkerCount = function () {
        return this.markers.length
    };
    this.contains = function (_position) {
        return me.bounds.contains(_position)
    };
    this.getPosition = function () {
        return this.center
    };
    this.getBounds = function () {
        return this.bounds
    };
    this.getMarkerBounds = function () {
        var bounds = new google.maps.LatLngBounds(me.markers[0].getPosition(), me.markers[0].getPosition());
        for (var i = 1; i < me.markers.length; i++) {
            bounds.extend(me.markers[i].getPosition())
        }
        return bounds
    };
    this.addMarker(_marker)
}

function Fluster2ClusterMarker(_fluster, _cluster) {
    this.fluster = _fluster;
    this.cluster = _cluster;
    this.position = this.cluster.getPosition();
    this.markerCount = this.cluster.getMarkerCount();
    this.map = this.fluster.getMap();
    this.style = null;
    this.div = null;
    var styles = this.fluster.getStyles();
    for (var i in styles) {
        if (this.markerCount > i) {
            this.style = styles[i]
        } else {
            break
        }
    }
    google.maps.OverlayView.call(this);
    this.setMap(this.map);
    this.draw()
};
Fluster2ClusterMarker.prototype = new google.maps.OverlayView();
Fluster2ClusterMarker.prototype.draw = function () {
    if (this.div == null) {
        var me = this;
        this.div = document.createElement('div');
        this.div.style.position = 'absolute';
        this.div.style.width = this.style.width + 'px';
        this.div.style.height = this.style.height + 'px';
        this.div.style.lineHeight = this.style.height + 'px';
        this.div.style.background = 'transparent url("' + this.style.image + '") 50% 50% no-repeat';
        this.div.style.color = this.style.textColor;
        this.div.style.textAlign = 'center';
        this.div.style.fontFamily = 'Arial, Helvetica';
        this.div.style.fontSize = '11px';
        this.div.style.fontWeight = 'bold';
        this.div.innerHTML = this.markerCount;
        this.div.style.cursor = 'pointer';
        google.maps.event.addDomListener(this.div, 'click', function () {
            me.map.fitBounds(me.cluster.getMarkerBounds())
        });
        this.getPanes().overlayLayer.appendChild(this.div)
    }
    var position = this.getProjection().fromLatLngToDivPixel(this.position);
    this.div.style.left = (position.x - parseInt(this.style.width / 2)) + 'px';
    this.div.style.top = (position.y - parseInt(this.style.height / 2)) + 'px'
};
Fluster2ClusterMarker.prototype.hide = function () {
    this.div.style.display = 'none'
};
Fluster2ClusterMarker.prototype.show = function () {
    this.div.style.display = 'block'
};

function Fluster2ProjectionOverlay(map) {
    google.maps.OverlayView.call(this);
    this.setMap(map);
    this.getP = function () {
        return this.getProjection()
    }
}
Fluster2ProjectionOverlay.prototype = new google.maps.OverlayView();
Fluster2ProjectionOverlay.prototype.draw = function () {};