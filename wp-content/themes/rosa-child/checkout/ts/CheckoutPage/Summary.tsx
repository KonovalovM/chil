import React, { useState, useCallback } from 'react';
import filter from 'lodash/filter';
import concat from 'lodash/concat';
import map from 'lodash/map';
import { OloBasketProduct, OloBasketValidation, OloCoupon } from '../OloAPI';
import { getCostString } from '../Utilities/formatHelper';

interface SummaryProps {
  basketProducts: OloBasketProduct[];
  basketValidation: OloBasketValidation;
  coupon: OloCoupon|undefined;
  updateCoupon: (coupon: OloCoupon|undefined) => void;
  allowsTip: boolean;
  tip: number;
}

export function Summary(props: SummaryProps) {
  const { basketProducts, basketValidation, coupon, updateCoupon, allowsTip, tip } = props;

  return (
    <div className="summary">
      <ProductsList basketProducts={basketProducts} />
      <BasketTotals
        basketValidation={basketValidation}
        coupon={coupon}
        updateCoupon={updateCoupon}
        allowsTip={allowsTip}
        tip={tip}
      />
    </div>
  );
}

function ProductsList(props: { basketProducts: OloBasketProduct[] }) {
  const customizableProducts = filter(props.basketProducts, basketProduct => basketProduct.choices.length > 0);
  const noCustomizableProducts = filter(props.basketProducts, basketProduct => !basketProduct.choices.length);
  const sorteredBasketProducts = concat(customizableProducts, noCustomizableProducts);

  return (
    <div className="products">
      {map(sorteredBasketProducts, (basketProduct, index) => {
        const { id, name, quantity, totalcost } = basketProduct;

        const choicesText = basketProduct.customdata;

        return (
          <div className="product" key={id}>
            <div className="title">
              <h2>{`${name} (${quantity})`}</h2>
              <strong>{getCostString(totalcost)}</strong>
            </div>
            <span>{choicesText}</span>
          </div>
        );
      })}
    </div>
  );
}

interface BasketTotalsProps {
  basketValidation: OloBasketValidation;
  coupon: OloCoupon|undefined;
  updateCoupon: (coupon: OloCoupon|undefined) => void;
  allowsTip: boolean;
  tip: number;
}

function BasketTotals(props: BasketTotalsProps) {
  const { basketValidation, coupon, updateCoupon, allowsTip, tip } = props;

  return (
    <div className="totals">
      { coupon ? null : <CouponInput updateCoupon={updateCoupon}/> }
      <div className="subtotals">
        <span>
          <p>Subtotal</p>
          <strong>{getCostString(basketValidation.subtotal)}</strong>
        </span>
        <span>
          <p>Taxes</p>
          <strong>{getCostString(basketValidation.tax)}</strong>
        </span>
        { basketValidation.customerhandoffcharge ? (
          <span>
            <p>Delivery Fee</p>
            <strong>{getCostString(basketValidation.customerhandoffcharge)}</strong>
          </span>) : null}
        { coupon ? <CouponComponent coupon={coupon} updateCoupon={updateCoupon}/> : null }
        { allowsTip ? (
          <span>
            <p>Tip</p>
            <strong>{getCostString(tip)}</strong>
          </span>) : null}
      </div>
      <div className="total">
        <strong>Your Total</strong>
        <strong>{getCostString(basketValidation.total)}</strong>
      </div>
    </div>
  );
}

function CouponInput({ updateCoupon } : { updateCoupon: (coupon: OloCoupon|undefined) => void }) {

  const [couponcode, setCouponcode] = useState<string>('');

  const onChange =  useCallback((event: React.FormEvent<HTMLInputElement>) => {
    setCouponcode(event.currentTarget.value);
  }, [setCouponcode]);

  const applyHandler = useCallback(() => {
    if(!couponcode.length) {
      return;
    }
    updateCoupon({
      couponcode
    });
  }, [couponcode, updateCoupon]);

  const onKeyDown = useCallback((event: React.KeyboardEvent<HTMLInputElement>) => {
    if (event.key === 'Enter') {
      event.preventDefault();
      event.stopPropagation();
      applyHandler();
    }
  }, [applyHandler, couponcode]);

  return (
    <div className="couponInput">
      <input type="text" value={couponcode} onChange={onChange} onKeyDown={onKeyDown} placeholder="Coupon Code"/>
      <button type="button" onClick={applyHandler}>Apply</button>
    </div>
  );
}

function CouponComponent({ coupon, updateCoupon }: { coupon: OloCoupon, updateCoupon: (coupon: OloCoupon|undefined) => void }) {

  const removeHandler = useCallback(() => {
    updateCoupon(undefined);
  }, [updateCoupon]);

  return (
    <span>
      <p>{`Discount (${coupon.couponcode})`}</p>
      <div className="discountContainer">
        <strong>{"-" + getCostString(coupon.coupondiscount || 0)}</strong>
        <button onClick={removeHandler} type="button" className="removeButton">
          Remove
        </button>
      </div>
    </span>
  );
}