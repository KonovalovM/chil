import map from 'lodash/map';

import React from 'react';
import { observer } from 'mobx-react';
import Modal from 'react-modal';
import { LoadingStatus } from '../../Common/Types';
import { withBasketState, BasketState, closeBasket } from '../../State/Basket';
import { OloBasket, getRestaurantMenuProductWithModifiers } from '../../OloAPI';
import { BasketProductList } from './BasketProductList';
import { getAsset } from '../../Utilities/assetsHelper';
import { usePromise } from '../../Utilities/Hooks';

export const BasketComponent = withBasketState(
  observer(({ basketState }: { basketState: BasketState }) => {
    let template = null;
    if (basketState.status === LoadingStatus.error) {
      template = <ErrorBasket />;
    } else if (basketState.status === LoadingStatus.progress) {
      template = <LoadingBasket />;
    } else {
      const basket = basketState.data;
      template = basket ? <BasketComponentWithBasket basket={basket} /> : <EmptyBasket />;
    }

    return (
      <Modal
        isOpen={basketState.open}
        overlayClassName="modalOverlay"
        className="rightContent"
        closeTimeoutMS={200}
        shouldCloseOnOverlayClick={true}
        onRequestClose={closeBasket}
      >
        <div className="basket">
          <div className="header">
            <h1>MY BASKET</h1>
            <button className="closeButton" onClick={closeBasket}>
              <img src={getAsset('close.svg')} />
            </button>
          </div>
          {template}
        </div>
      </Modal>
    );
  })
);

const BasketComponentWithBasket = (props: { basket: OloBasket }) => {
  const basket = props.basket;
  const productsStatus = usePromise(
    async () => Promise.all(map(basket.products, basketProduct => getRestaurantMenuProductWithModifiers(basket.restaurantId, basketProduct.productId))),
    [basket.restaurantId, basket.products]
  );

  if (!basket.products.length) {
    return <EmptyBasket />;
  }

  const products = productsStatus.status === LoadingStatus.success ? productsStatus.value : [];
  return (
    <BasketProductList
      restaurantId={basket.restaurantId}
      basketProducts={basket.products}
      products={products}
      productsStatus={productsStatus.status}
      total={basket.subtotal}
    />
  );
};

const EmptyBasket = () => {
  return (
    <div className="emptyBasket">
      <img src={getAsset('logo.svg')} />
      <p>Your basket is empty!</p>
      <p>Start adding some delicious food.</p>
    </div>
  );
};

const LoadingBasket = () => {
  return <b>Loading ...</b>;
};

const ErrorBasket = () => {
  return <b>Error</b>;
};
