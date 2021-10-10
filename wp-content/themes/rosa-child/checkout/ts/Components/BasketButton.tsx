import React from 'react';
import { observer } from 'mobx-react';
import classnames from 'classnames';
import { LoadingStatus } from '../Common/Types';
import { withBasketState, BasketState, openBasket } from '../State/Basket';
import { getAsset } from '../Utilities/assetsHelper';

export const BasketButton = withBasketState(
  observer(({ basketState }: { basketState: BasketState }) => {
    if (basketState.status !== LoadingStatus.success) {
      return null;
    }

    const productsQuantity = basketState.data ? basketState.data.products.length : 0;
    const count = !productsQuantity ? null : productsQuantity <= 9 ? `${productsQuantity}` : '+9';

    return (
      <button className="basketButton" onClick={openBasket}>
        <span className={classnames('counter', { visible: count })}>{count}</span>
        <img src={getAsset('basket.svg')} />
      </button>
    );
  })
);
