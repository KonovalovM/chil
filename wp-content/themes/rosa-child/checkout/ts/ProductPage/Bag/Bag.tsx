import React, { useEffect, useRef } from 'react';
import sumBy from 'lodash/sumBy';
import classnames from 'classnames';
import { OloRestaurantMenuProduct, OloBasketNewProductChoice } from '../../OloAPI';
import { calculateCaloriesForSelectedChoices, calculatePriceForSelectedChoices, getNoHiddeModifier } from '../../Utilities/modelHelper';
import { getCostString } from '../../Utilities/formatHelper';
import { ModifierList } from './ModifierList';
import { AddToBasketButton } from './AddToBasketButton';
import { Mode } from '../Types';

export interface BagProps {
  product: OloRestaurantMenuProduct;
  mode: Mode;
  selectedChoices: OloBasketNewProductChoice[];
  skippedModifiers: string[];
  shownInMobile: boolean;
  isProductValid: boolean;
  removeChoice: (choiceId: string) => unknown;
  addToBasket: () => unknown;
}

export const Bag = ({ product, mode, selectedChoices, skippedModifiers, shownInMobile, isProductValid, removeChoice, addToBasket }: BagProps) => {
  const modifiersRef = useRef<HTMLDivElement>(null);

  useEffect(() => {
    const modifiersElement = modifiersRef.current;
    if(!modifiersElement) {
      return;
    }

    if(typeof modifiersElement.scroll === 'undefined') {
      return;
    }
    modifiersElement.scroll({ top: modifiersElement.scrollHeight, left: 0, behavior: 'smooth' });
  }, [modifiersRef, selectedChoices, skippedModifiers]);

  const noHiddenModifiers = product.modifiers ? getNoHiddeModifier(product.modifiers) : [];
  if(noHiddenModifiers.length===0) {
    return null;
  }

  return (
    <div className={classnames('bag', { mobileVisible: shownInMobile })}>
      <div className={classnames({ desktopPosition: !shownInMobile }, { mobilePosition: shownInMobile })}>
        <div className="product">
          <h1>{product.name}</h1>
          <div className="info">
            <div className="totals">
              <p>{sumBy(selectedChoices, 'quantity') + ' items'}</p>
              <p>{calculateCaloriesForSelectedChoices(product, selectedChoices) + ' CAL'}</p>
            </div>
            <strong>{getCostString(calculatePriceForSelectedChoices(product, selectedChoices))}</strong>
          </div>
        </div>
        <div className="modifiers" ref={modifiersRef}>
          <ModifierList modifiers={noHiddenModifiers} selectedChoices={selectedChoices} skippedModifiers={skippedModifiers} removeChoice={removeChoice} />
        </div>
        <AddToBasketButton isProductValid={isProductValid} mode={mode} addToBasket={addToBasket} />
      </div>
    </div>
  );
};
