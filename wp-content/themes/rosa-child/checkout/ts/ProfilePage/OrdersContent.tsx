import map from 'lodash/map';
import memoize from 'lodash/memoize';

import React, { useState, useCallback } from 'react';
import { useHistory } from 'react-router-dom';
import { LoadingStatus } from '../Common/Types';
import { LoadingPage, ErrorPage, ConfirmModal } from '../Components';
import { getCostString } from '../Utilities/formatHelper';
import { formatDate } from '../Utilities/datetimeHelper';
import { usePromise } from '../Utilities/Hooks';
import { OloUser, OloBasket, OloOrderStatus } from '../OloAPI';
import { createFromOrder, openBasket, cleanBasket } from '../State/Basket';
import { setIsLoading, setErrorModalMessage } from '../State/Global';
import { getRecentOrders, requireAuthtoken } from '../State/User';

export const OrdersContent = ({ user, basket }: { user: OloUser; basket: OloBasket | undefined }) => {
  const [currentOrderid, setCurrentOrderid] = useState<string | undefined>();
  const [isConfirmModalOpen, setIsConfirmModalOpen] = useState<boolean>(false);

  const ordersStatus = usePromise(async () => getRecentOrders(), [user]);

  const attemptReorder = useCallback(
    memoize((orderid: string) => async () => {
      if (basket && basket.products.length > 0) {
        setCurrentOrderid(orderid);
        setIsConfirmModalOpen(true);
        return;
      }
      //only reorder when the basket is empty
      try {
        setIsLoading(true);
        await createFromOrder(requireAuthtoken(), orderid);
        setIsLoading(false);
        openBasket();
      } catch (error) {
        setIsLoading(false);
        setErrorModalMessage(error);
      }
    }),
    [setCurrentOrderid, setIsConfirmModalOpen]
  );

  /* Modal */
  const confirmReorder = useCallback(async () => {
    setIsConfirmModalOpen(false);

    try {
      setIsLoading(true);
      await cleanBasket();
      await createFromOrder(requireAuthtoken(), currentOrderid!);
      setIsLoading(false);
      openBasket();
    } catch (error) {
      setIsLoading(false);
      setErrorModalMessage(error);
    } finally {
      setCurrentOrderid(undefined);
    }
  }, [setIsConfirmModalOpen, currentOrderid, setCurrentOrderid, user]);

  const cancelReorder = useCallback(() => {
    setIsConfirmModalOpen(false);
    setCurrentOrderid(undefined);
  }, [setIsConfirmModalOpen, setCurrentOrderid]);

  if (ordersStatus.status === LoadingStatus.progress) {
    return <LoadingPage />;
  }
  if (ordersStatus.status === LoadingStatus.error) {
    return <ErrorPage error={ordersStatus.error} />;
  }

  const orders = ordersStatus.value;

  return (
    <div className="ordersContent">
      <h1>My Orders</h1>
      <strong>{`My Orders ${orders.length} orders`}</strong>
      <div className="orders">
        {map(orders, order => (
          <OrderStatusComponent key={order.id} order={order} reorder={attemptReorder(order.id)} />
        ))}
      </div>

      <ConfirmModal
        isOpen={isConfirmModalOpen}
        message={`You already have some items in your cart.
                  In order to continue, please clear your cart.`}
        confirmMessage="clean cart"
        confirmCallback={confirmReorder}
        cancelCallback={cancelReorder}
      />
    </div>
  );
};

function OrderStatusComponent(props: { order: OloOrderStatus; reorder: () => unknown }) {
  const { order, reorder } = props;
  const history = useHistory();

  const goToOrderPage = useCallback(() => {
    const link = `/order/${order.id}`;
    history.push(link);
  }, [order, history]);

  const reorderHandler = useCallback(
    (event: React.MouseEvent) => {
      event.stopPropagation();
      reorder();
    },
    [order, reorder]
  );

  return (
    <div className="order" onClick={goToOrderPage}>
      <div className="header">
        <div className="dateAndAddress">
          <h3>{`${formatDate(order.timeplaced)} • ${order.vendorname}`}</h3>
        </div>
        <strong>{getCostString(order.total)}</strong>
      </div>
      {order.products.length > 0 ? (
        <div className="info">
          <h2>{order.products[0].name}</h2>
          <button className="reorder" onClick={reorderHandler}>
            reorder
          </button>
        </div>
      ) : null}
    </div>
  );
}
