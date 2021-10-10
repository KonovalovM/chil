import React from 'react';
import map from 'lodash/map';
import { Link } from 'react-router-dom';
import { OloRestaurant } from '../OloAPI';
import { City } from './Cities';
import { RestaurantComponent } from './RestaurantComponent';

export interface RestaurantListProps {
  restaurants: OloRestaurant[];
  currentCity: City | null;
}

export function RestaurantList(props: RestaurantListProps) {
  const { restaurants, currentCity } = props;

  if (restaurants.length === 0) {
    return <div className="emptyRestaurantList">No results</div>;
  }

  return (
    <div className="restaurantList">
      {map(restaurants, restaurant => (
        <Link to={`/${restaurant.id}/menu`} key={restaurant.id}>
          <RestaurantComponent restaurant={restaurant} currentCity={currentCity} />
        </Link>
      ))}
    </div>
  );
}
