import React, { useState } from 'react';
import ReactMapGL, { Marker, NavigationControl } from 'react-map-gl';
import { getAsset } from '../Utilities/assetsHelper';
import { OloRestaurant } from '../OloAPI';

interface Viewport {
  latitude: number;
  longitude: number;
  zoom: number;
}

export const MapView = ({ restaurant }: { restaurant: OloRestaurant }) => {
  const [viewport, setViewPort] = useState<Viewport>({
    latitude: restaurant.latitude,
    longitude: restaurant.longitude,
    zoom: 15,
  });

  const onViewportChange = (newViwport: Viewport) => {
    setViewPort(newViwport);
  };

  return (
    <ReactMapGL {...viewport} width="100%" height="150px" mapboxApiAccessToken={MAPBOX_API_ACCESS_TOKEN} onViewportChange={onViewportChange}>
      <Marker longitude={restaurant.longitude} latitude={restaurant.latitude} offsetTop={-64} offsetLeft={-24}>
        <div>
          <a href="#">
            <img src={getAsset('marker.svg')} />
          </a>
        </div>
      </Marker>
      <div className="navControl">
        <NavigationControl />
      </div>
    </ReactMapGL>
  );
};
