import React from 'react';
import { formatTime } from '../Utilities/datetimeHelper';
import { getDateString } from '../Utilities/modelHelper';
import { getDeliveryModeText } from '../Utilities/formatHelper';
import { OloOrderStatus, OloRestaurant, OloUser } from '../OloAPI';

interface OrderInstructionsProps {
  orderStatus: OloOrderStatus;
  restaurant: OloRestaurant;
  userName: string;
}

export const OrderInstructions = (props: OrderInstructionsProps) => {
  const { orderStatus, restaurant, userName } = props;

  return (
    <div className="orderInstructions">
      <div className="overlay">
        <div className="orderInstructionsContainer">
          <div className="instructions">
            <div className="header">
              <h1>{`rejoice, ${userName}`}</h1>
              <small>We received your order.</small>
            </div>

            <p>{`${getDeliveryModeText(orderStatus.deliverymode)}, ${formatTime(orderStatus.readytime)}`}</p>
            <p>{`${getDateString(orderStatus.readytime, restaurant.timezone)} at ${restaurant.address}`}</p>

            <span className="lineSeparator" />

            <a href={`tel:${restaurant.phone}`}>{`Call ${restaurant.phone}`}</a>
          </div>
        </div>
      </div>
    </div>
  );
};