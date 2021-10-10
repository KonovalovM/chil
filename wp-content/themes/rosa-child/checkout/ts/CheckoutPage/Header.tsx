import React from 'react';
import { getCalendarString } from '../Utilities/modelHelper';
import { getDeliveryModeText } from '../Utilities/formatHelper';
import { OloRestaurant, OloRestaurantCalendar } from '../OloAPI';

interface HeaderProps {
  restaurant: OloRestaurant;
  calendar: OloRestaurantCalendar;
  deliverymode: string;
}

export function Header(props: HeaderProps) {
  const { restaurant, calendar, deliverymode } = props;
  const backgroundStyle = {
    backgroundImage: `url("${restaurant.img})}"`,
  };

  return (
    <div className="header" style={backgroundStyle}>
      <div className="overlay">
        <div className="info">
          <small>{getDeliveryModeText(deliverymode).toUpperCase()}</small>
          <h1>{restaurant.name}</h1>
          <small>{restaurant.address}</small>
          <small>{getCalendarString(calendar, restaurant.timezone)}</small>
          <small>{restaurant.phone}</small>
        </div>
      </div>
    </div>
  );
}
