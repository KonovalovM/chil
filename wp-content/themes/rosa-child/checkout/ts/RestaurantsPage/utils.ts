import mean from 'lodash/mean';
import min from 'lodash/min';
import max from 'lodash/max';
import uniq from 'lodash/uniq';
import map from 'lodash/map';
import concat from 'lodash/concat';
import { OloRestaurant } from '../OloAPI';
import { City } from './Cities';
import { Viewport } from './Viewport';

const usaCenterLatitude = 39.381266;
const usaCenterLongitude = -97.922211;
const minimunZoom = 4;

export function calculateViewport(restaurants: OloRestaurant[]): Viewport {
  if (!restaurants.length) {
    return { latitude: usaCenterLatitude, longitude: usaCenterLongitude, zoom: minimunZoom };
  }

  const maxZoom = 15;

  const longitudes = map(restaurants, 'longitude');
  const latitudes = map(restaurants, 'latitude');

  const longitude = mean(longitudes);
  const latitude = mean(latitudes);

  const minLongitude = min(longitudes);
  const maxLongitude = max(longitudes);

  let zoom = maxZoom;
  if (maxLongitude !== undefined && minLongitude !== undefined) {
    let maxDistance = maxLongitude - minLongitude;
    maxDistance = Math.abs(maxDistance);
    if (maxDistance !== 0) {
      const exponent = Math.log2(360 / maxDistance);
      zoom = exponent !== 0 ? Math.round(exponent) : 20;
      zoom = zoom > 1 ? zoom - 2 : 0;
    }
  }

  const viewport = {
    latitude,
    longitude,
    zoom,
  };
  return viewport;
}

//from: https://stackoverflow.com/questions/14560999/using-the-haversine-formula-in-javascript
export function haversineDistance(lat1: number, lon1: number, lat2: number, lon2: number, isMiles: boolean) {
  const toRad = (x: number) => {
    return (x * Math.PI) / 180;
  };

  const earthRadius = 6371; // km

  const x1 = lat2 - lat1;
  const dLat = toRad(x1);
  const x2 = lon2 - lon1;
  const dLon = toRad(x2);
  const a = Math.sin(dLat / 2) * Math.sin(dLat / 2) + Math.cos(toRad(lat1)) * Math.cos(toRad(lat2)) * Math.sin(dLon / 2) * Math.sin(dLon / 2);
  const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
  let d = earthRadius * c;

  if (isMiles) d /= 1.60934;
  d = Math.round(d * 10) / 10;
  return d;
}

export function getTerms(restaurants: OloRestaurant[], cities: City[]) {
  const names = map(restaurants, 'name');
  const addresses = map(restaurants, 'address');
  const states = map(cities, 'state');
  const citiesNames = map(cities, 'name');
  const terms = uniq(concat(names, addresses, states, citiesNames));
  return terms;
}
