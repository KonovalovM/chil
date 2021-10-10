import React, { useState, useEffect } from 'react';
import { City, getCities } from './Cities';
import { OloRestaurant, getRestaurants } from '../OloAPI';
import { getTerms, calculateViewport } from './utils';
import { MapView } from './MapView';
import { RestaurantList } from './RestaurantList';
import { SearchBox } from './SearchBox';
import { BasketButton, LoginButton } from '../Components';
import { makeWithPromise } from '../Utilities/componentHelper';

const withRestaurants = makeWithPromise('restaurants', async () => getRestaurants());

const getRestaurantText = (restaurant: OloRestaurant) => [restaurant.name, restaurant.address].join(' ').toLowerCase();

export const RestaurantsPage = withRestaurants(({ restaurants }: { restaurants: OloRestaurant[] }) => {
  const cities = getCities();
  const terms = getTerms(restaurants, cities);
  const defaultViewport = {
    latitude: 30.293804914285715,
    longitude: -97.73555605714284,
    zoom: 11
  } // Austin viewport

  useEffect(() => scrollTo(0, 0), [restaurants]);
  const [currentSearchText, setCurrentSearchText] = useState<string>('');

  const [foundRestaurants, foundCity] = searchRestaurants(currentSearchText, restaurants, cities);
  // the viewport prioritize the default viewport (austin)
  const viewport = foundRestaurants.length === restaurants.length ?  defaultViewport : calculateViewport(foundRestaurants);

  return (
    <div className="restaurantsPage">
      <div className="main-content">
        <MapView restaurants={restaurants} viewport={viewport} />
      </div>
      <div className="sidebar">
        <div className="buttonGroup">
          <LoginButton />
          <BasketButton />
        </div>
        <SearchBox submitCallback={setCurrentSearchText} terms={terms} />
        <RestaurantList restaurants={foundRestaurants} currentCity={foundCity} />
      </div>
    </div>
  );
});

const searchRestaurants = (searchText: string, restaurants: OloRestaurant[], cities: City[]): [OloRestaurant[], City | null] => {
  const text = searchText.toLowerCase();
  if (text === '') {
    return [restaurants, null];
  }

  const filteredCities = cities.filter(city => city.searchText.includes(text));
  const foundCity = filteredCities.length !== 0 ? filteredCities[0] : null;
  const foundCityId = foundCity ? foundCity.id : null;

  const foundRestaurants = restaurants.filter(restaurant => getRestaurantText(restaurant).includes(text) || restaurant.city === foundCityId);

  return [foundRestaurants, foundCity];
};
