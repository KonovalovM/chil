import map from 'lodash/map';
import filter from 'lodash/filter';
import trim from 'lodash/trim';
import React from 'react';

import { getCostString } from '../Utilities/formatHelper';
import { OloOrderStatus, OloOrderStatusProduct, OloRestaurant } from '../OloAPI';
import { MapView } from './MapView';

export const OrderDetails = ({ orderStatus, restaurant }: { orderStatus: OloOrderStatus; restaurant: OloRestaurant }) => {
  const addressDetail = filter(
    map([restaurant.crossstreet, restaurant.city, restaurant.state, restaurant.zip], value => trim(value, ' ')),
    value => value !== ''
  ).join(', ');
  const directionsLink = `http://maps.google.com/?daddr=${restaurant.latitude},${restaurant.longitude}`;

  const paymentDetails = [
    {
      description: 'Subtotal',
      amount: orderStatus.subtotal
    },
    {
      description: 'Taxes',
      amount: orderStatus.salestax
    }
  ];

  if(orderStatus.customerhandoffcharge) {
    paymentDetails.push({
      description: 'Delivery Fee',
      amount: orderStatus.customerhandoffcharge,
    });
  }

  if(orderStatus.tip) {
    paymentDetails.push({
      description: 'Tip',
      amount: orderStatus.tip,
    });
  }

  return (
    <div className="orderDetails">
      <MapView restaurant={restaurant} />
      <div className="orderDetailsGroup">
        <h2>{restaurant.streetaddress}</h2>
        <small>{addressDetail}</small>

        <a className="buttonWrapper" href={directionsLink} target="_blank">
          <button>Need directions?</button>
        </a>
      </div>

      <div className="orderDetailsGroup dark">
        <h3>{`My Order (${orderStatus.products.length})`}</h3>
      </div>

      <div className="orderDetailsGroup">
        {map(orderStatus.products, (orderProduct, index) => (
          <OrderProductComponent key={index} orderProduct={orderProduct} index={index + 1} />
        ))}
      </div>
      <div className="subtotals">
        <div className="subtotalsContainer">
          {map(paymentDetails, ({ description, amount }, index) => (
            <div className="info" key={index}>
              <small>{description}</small>
              <strong>{getCostString(amount)}</strong>
          </div>
          ))}
          {orderStatus.discount ?
            (<div className="info">
              <small>Discount</small>
              <strong>{"-" + getCostString(orderStatus.discount)}</strong>
            </div>)
            : null
          }
        </div>
      </div>
      <div className="orderDetailsGroup">
        <div className="total">
          <strong>Your Total</strong>
          <strong>{getCostString(orderStatus.total)}</strong>
        </div>
      </div>
      <div className="orderDetailsGroup disclaimer">
        <small>
          We are unable to cancel orders once they have been placed. We're happy to help if you have any questions or concerns -- just shoot us an email
          <a href="mailto: team@chilantrobbq.com"> team@chilantrobbq.com</a> Thanks!
        </small>
      </div>
    </div>
  );
};

const OrderProductComponent = ({ orderProduct, index }: { orderProduct: OloOrderStatusProduct; index: number }) => {
  const choicesText = orderProduct.choices.length > 1 ? orderProduct.custompassthroughdata : '';

  return (
    <div className="orderProductComponent">
      <h3>{`${index}.`}</h3>
      <div className="header">
        <h3>{`${orderProduct.name} (${orderProduct.quantity})`}</h3>
        <strong>{getCostString(orderProduct.totalcost)}</strong>
      </div>
      {choicesText ? <small>{choicesText}</small> : null}
      <small>{orderProduct.specialinstructions}</small>
    </div>
  );
};
