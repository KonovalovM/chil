import React from 'react';
import { useParams } from 'react-router-dom';
import { OloRestaurant, getRestaurant } from '../OloAPI';
import memoize from 'memoizee';
import { ErrorPage, LoadingPage } from '../Components';
import { usePromise } from '../Utilities/Hooks';
import { LoadingStatus } from '../Common/Types';

const getRestaurantCached = memoize(
  async (restaurantId: string): Promise<OloRestaurant> => {
    return getRestaurant(restaurantId);
  },
  { primitive: true, maxAge: 5 * 60 * 1000 }
);

export function withRestaurant<PropType extends { restaurant: OloRestaurant }>(
  WrappedComponent: React.FunctionComponent<PropType>
): React.FunctionComponent<Omit<PropType, 'restaurant'>> {
  return (props: Omit<PropType, 'restaurant'>) => {
    const params: { restaurantId?: string } = useParams();
    const restaurantId = params.restaurantId;

    if (!restaurantId) {
      return <ErrorPage error={new Error("No restaurant selected")} />;
    }

    const restaurantStatus = usePromise(async () => getRestaurantCached(restaurantId), [restaurantId]);

    if (restaurantStatus.status === LoadingStatus.error) {
      return <ErrorPage error={ new Error(restaurantStatus.error.message)} />;
    }

    if (restaurantStatus.status === LoadingStatus.progress) {
      return <LoadingPage />;
    }

    const restaurant = restaurantStatus.value;

    if (!restaurant.availability.available) {
      return <ErrorPage error={new Error(`The restaurant is not currently available: ${restaurant.availability.description}`)} />;
    }

    return <WrappedComponent {...(props as PropType)} restaurant={restaurant} />;
  };
}
