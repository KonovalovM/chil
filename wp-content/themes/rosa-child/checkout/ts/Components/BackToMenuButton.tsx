import React from 'react';
import classnames from 'classnames';
import { useParams, useLocation, Link } from 'react-router-dom';
import { LoadingStatus } from '../Common/Types';
import { usePromise } from '../Utilities/Hooks';
import { getBasketData } from '../State/Basket';

export function BackToMenuButton({ extraClass }:{ extraClass?:string }) {
  const basket = getBasketData();

  const params: { restaurantId?: string } = useParams();
  const location = useLocation();
  if(location.pathname.includes("/menu")) {
    return null;
  }

  const restaurantId = basket ? basket.restaurantId : params.restaurantId;

  return (
    <div className={classnames("backToMenuButton", extraClass)}>
      <Link to={restaurantId ? `/${restaurantId}/menu` : '/' }>
        <span className="desktopText">Back To Menu</span>
        <span className="mobileText">Back</span>
      </Link>
    </div>
  );
}
