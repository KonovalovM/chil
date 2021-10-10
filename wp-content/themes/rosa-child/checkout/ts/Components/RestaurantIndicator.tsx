import React from 'react';
import { useParams, Link } from 'react-router-dom';
import { LoadingStatus } from '../Common/Types';
import { usePromise } from '../Utilities/Hooks';
import { getRestaurant } from '../OloAPI';

export function RestaurantIndicator() {
  const params: { restaurantId?: string } = useParams();
  const restaurantId = params.restaurantId;
  if (!restaurantId) {
    return null;
  }
  return <RestaurantIndicatorComponent restaurantId={restaurantId} />;
}

function RestaurantIndicatorComponent({ restaurantId }: { restaurantId: string }) {
  const restaurantStatus = usePromise(async () => getRestaurant(restaurantId), [restaurantId]);
  if (restaurantStatus.status === LoadingStatus.progress) {
    return null;
  }
  if (restaurantStatus.status === LoadingStatus.error) {
    return null;
  }
  const restaurant = restaurantStatus.value;
  return (
    <div className="restaurantIndicator">
      <span>Ordering at&nbsp;</span>
      <Link to={`/${restaurantId}/menu`}>{restaurant.name.replace("Chi'lantro", "")}</Link>
    </div>
  );
}

export function RestaurantIndicatorBar() {
  return (
    <div className="restaurantIndicatorBar">
      <RestaurantIndicator />
    </div>
  );
}
