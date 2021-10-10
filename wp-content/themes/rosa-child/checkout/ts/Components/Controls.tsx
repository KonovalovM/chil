import React, { useCallback } from 'react';
import { getAsset } from '../Utilities/assetsHelper';

interface QuantityControlProps {
  quantity: number;
  minimumquantity: number;
  maximumquantity: number;
  quantityincrement: number;
  setQuantity: (quantity: number) => unknown;
}

export const QuantityControl = (props: QuantityControlProps) => {
  const { quantity, minimumquantity, maximumquantity, quantityincrement, setQuantity } = props;

  const modifyQuantity = useCallback(
    (increase: number) => {
      return () => {
        const newQuantity = quantity + increase;
        if (newQuantity < minimumquantity || newQuantity > maximumquantity) {
          return;
        }
        setQuantity(newQuantity);
      };
    },
    [quantity, minimumquantity, maximumquantity, setQuantity]
  );

  return (
    <div className="quantityControl">
      <button onClick={modifyQuantity(-quantityincrement)}>
        <img src={getAsset('decrease.svg')} />
      </button>
      <p>{quantity}</p>
      <button onClick={modifyQuantity(quantityincrement)}>
        <img src={getAsset('increase.svg')} />
      </button>
    </div>
  );
};
