import React, { useEffect, useState, useCallback } from 'react';
import { Link } from 'react-router-dom';
import ReactMapGL, { Marker, NavigationControl } from 'react-map-gl';
import map from 'lodash/map';
import { OloRestaurant } from '../OloAPI';
import { Viewport } from './Viewport';
import { getAsset } from '../Utilities/assetsHelper';

export const MapView = ({ restaurants, viewport }: { restaurants: OloRestaurant[]; viewport: Viewport }) => {
  const [currentViewport, setCurrentViewport] = useState<Viewport>(viewport);

  useEffect(() => setCurrentViewport(viewport), [setCurrentViewport, viewport]);

  const onViewportChange = useCallback((newViewport: Viewport) => setCurrentViewport(newViewport), [setCurrentViewport]);

  return (
    <ReactMapGL {...currentViewport} width="100%" height="100%" mapboxApiAccessToken={MAPBOX_API_ACCESS_TOKEN} onViewportChange={onViewportChange}>
      <React.Fragment>
        {map(restaurants, (restaurant: OloRestaurant) => {
          const longitude = restaurant.longitude;
          const latitude = restaurant.latitude;
          return (
            <Marker key={restaurant.id} longitude={longitude} latitude={latitude} offsetTop={-64} offsetLeft={-24}>
              <div>
                <Link to={`/${restaurant.id}/menu`}>
                  <img src={getAsset('marker.svg')} />
                </Link>
              </div>
            </Marker>
          );
        })}
      </React.Fragment>
      <div className="navControl">
        <NavigationControl />
      </div>
    </ReactMapGL>
  );
};
