import React from 'react';
import classnames from 'classnames';
import { Mode } from '../Types';

export interface AddToBasketButtonProps {
  isProductValid: boolean;
  mode: Mode;
  addToBasket: () => unknown;
}

export function AddToBasketButton({ isProductValid, mode, addToBasket }: AddToBasketButtonProps) {
  return (
    <button className={classnames('addToBasketButton', { able: isProductValid })} onClick={addToBasket}>
      {mode === Mode.creation ? 'ADD TO BASKET' : 'SAVE CHANGES'}
    </button>
  );
}
