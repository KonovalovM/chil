export interface City {
  id: string;
  name: string;
  code: string;
  state: string;
  stateCode: string;
  latitude: number;
  longitude: number;
  searchText: string;
}

const cities = [
  {
    id: 'Austin',
    name: 'austin',
    code: 'aus',
    state: 'texas',
    stateCode: 'tx',
    latitude: 30.266666,
    longitude: -97.73333,
    searchText: '',
  },
  {
    id: 'New York ',
    name: 'new york city',
    code: 'nyc',
    state: 'new york',
    stateCode: 'ny',
    latitude: 40.73061,
    longitude: -73.935242,
    searchText: '',
  },
];

export const getCities = () => {
  cities.forEach((city: City) => {
    const searchTextArray = [city.name, city.code, city.state, city.stateCode];
    let searchText = searchTextArray.join(' ');
    searchText = searchText.toLowerCase();

    city.searchText = searchText;
  });
  return cities;
};
