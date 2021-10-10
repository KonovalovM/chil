import React, { useEffect } from 'react';
import { observer } from 'mobx-react';
import { RouteComponentProps } from 'react-router-dom';

import { NavigationBar } from '../Components';
import { makeWithPromiseWithProps } from '../Utilities/componentHelper';
import { UserState, withUserState } from '../State/User';
import { LoadingStatus } from '../Common/Types';
import { OloOrderStatus, OloUser, OloRestaurant, getOrderStatus, getRestaurant } from '../OloAPI';

import { OrderInstructions } from './OrderInstructions';
import { OrderDetails } from './OrderDetails';

interface OrderPageProps extends RouteComponentProps<{ orderId: string }> {
  userState: UserState;
  orderStatus: OloOrderStatus;
  restaurant: OloRestaurant;
}

const withOrderStatus = makeWithPromiseWithProps(
  'orderStatus',
  (props: OrderPageProps) => async () => getOrderStatus(props.match.params.orderId),
  (props: OrderPageProps) => [props.match.params.orderId]
);

const withRestaurant = makeWithPromiseWithProps(
  'restaurant',
  (props: OrderPageProps) => async () => getRestaurant(props.orderStatus.vendorid),
  (props: OrderPageProps) => [props.orderStatus.vendorid]
);

export const OrderPage = withOrderStatus(
  withRestaurant(
    withUserState(observer(({userState, orderStatus, restaurant } : OrderPageProps) => {

      useEffect(() => window.scrollTo(0, 0), [userState, orderStatus, restaurant]);

      let userName = 'Guest';
      if(userState.status === LoadingStatus.success && userState.data && userState.data.firstname) {
        userName = userState.data.firstname;
      }

      return (
        <div className="orderPage">
          <NavigationBar />
          <div className="content">
            <div className="mainContent">
              <OrderInstructions orderStatus={orderStatus} userName={userName} restaurant={restaurant} />
            </div>
            <div className="sidebar">
              <OrderDetails orderStatus={orderStatus} restaurant={restaurant} />
            </div>
          </div>
        </div>
      );

    }))
  )
);
