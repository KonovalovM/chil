import React from 'react';
import { City } from './Cities';
import { haversineDistance } from './utils';

import { getCalendarString } from '../Utilities/modelHelper';
import { usePromise } from '../Utilities/Hooks';

import { LoadingStatus } from '../Common/Types';
import { getRestaurantCalendar, OloRestaurant } from '../OloAPI';

export function RestaurantComponent({ restaurant, currentCity }: { restaurant: OloRestaurant; currentCity: City | null }) {
  const calendarStatus = usePromise(async () => getRestaurantCalendar(restaurant.id, 0), [restaurant.id]);

  let calendarText = '';
  if (calendarStatus.status === LoadingStatus.progress) {
    calendarText = 'Loading calendar...';
  } else if (calendarStatus.status === LoadingStatus.error) {
    calendarText = 'Can not show the calendar';
  } else {
    calendarText = getCalendarString(calendarStatus.value, restaurant.timezone);
  }

  return (
    <div className="restaurant">
      <div className="restaurantImg">
        <span />
        <img src={restaurant.img} />
      </div>
      <div className="restaurantInfo">
        <div className="title">
          <h2>{restaurant.name}</h2>
          <div className="underline">
            <span className="background" />
            <span className="highlight" />
          </div>
        </div>
        <div className="address">
          <p>{restaurant.address}</p>
          <div className="arrowIcon">
            <div className="circle">
              <div className="arrow" />
            </div>
          </div>
        </div>
        <p>{calendarText + '  ' + getDistanceText(restaurant, currentCity)}</p>
        <p>{restaurant.phone}</p>
      </div>
    </div>
  );
}

function getDistanceText(restaurant: OloRestaurant, currentCity: City | null) {
  if (currentCity === null) {
    return '';
  }

  const lat1 = restaurant.latitude;
  const lon1 = restaurant.longitude;

  const lat2 = currentCity.latitude;
  const lon2 = currentCity.longitude;
  const d = haversineDistance(lat1, lon1, lat2, lon2, true);
  return `${d} Miles away`;
}
