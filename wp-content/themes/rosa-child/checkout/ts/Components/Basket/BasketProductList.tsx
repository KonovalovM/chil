import find from 'lodash/find';
import map from 'lodash/map';
import React, { useCallback } from 'react';
import { useHistory } from 'react-router-dom';

import { cleanBasket, closeBasket } from '../../State/Basket';
import { getCostString } from '../../Utilities/formatHelper';
import { LoadingStatus } from '../../Common/Types';

import { OloBasketProduct, OloRestaurantMenuProduct } from '../../OloAPI';
import { BasketProductComponent } from './BasketProductComponent';

interface BasketProductListProps {
  restaurantId: string;
  basketProducts: OloBasketProduct[];
  products: OloRestaurantMenuProduct[];
  productsStatus: LoadingStatus;
  total: number;
}

export function BasketProductList({ restaurantId, basketProducts, products, productsStatus, total }: BasketProductListProps) {
  const history = useHistory();
  const gotToCheckout = useCallback(() => {
    closeBasket();
    const checkoutUrl = `/${restaurantId}/checkout`;
    history.push(checkoutUrl);
  }, [history]);

  const handleCleanBasket = useCallback(() => {
    cleanBasket();
    history.push(`/${restaurantId}/menu`);
  }, [history]);

  return (
    <>
      <div className="list">
        {map(basketProducts, (basketProduct, index) => {
          return (
            <BasketProductComponent
              key={basketProduct.id}
              restaurantId={restaurantId}
              basketProduct={basketProduct}
              product={find(products, { id: basketProduct.productId })}
              productsStatus={productsStatus}
              index={index + 1}
            />
          );
        })}

        <div className="cleanBasket">
          <button onClick={handleCleanBasket}>Clean Basket</button>
        </div>
      </div>
      <div className="checkout">
        <button onClick={gotToCheckout}>{'GO TO CHECKOUT ' + getCostString(total)}</button>
      </div>
    </>
  );
}
