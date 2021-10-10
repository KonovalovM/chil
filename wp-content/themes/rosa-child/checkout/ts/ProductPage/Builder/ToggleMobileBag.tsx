import React from 'react';
import sumBy from 'lodash/sumBy';
import { OloRestaurantMenuProduct, OloBasketNewProductChoice } from '../../OloAPI';
import { calculateCaloriesForSelectedChoices, calculatePriceForSelectedChoices } from '../../Utilities/modelHelper';
import { getCostString } from '../../Utilities/formatHelper';
import { getAsset } from '../../Utilities/assetsHelper';

interface ToggleMobileBagProps {
  product: OloRestaurantMenuProduct;
  selectedChoices: OloBasketNewProductChoice[];
  toggleMobileBag: () => unknown;
}

export function ToggleMobileBag({ product, selectedChoices, toggleMobileBag }: ToggleMobileBagProps) {
  return (
    <div className="toggleMobileBag" onClick={toggleMobileBag}>
      <div className="info">
        <h2>{product.name}</h2>
        <span>
          <p>{sumBy(selectedChoices, 'quantity') + ' items'}</p>
          <p>{calculateCaloriesForSelectedChoices(product, selectedChoices) + ' CAL'}</p>
        </span>
      </div>
      <strong>{getCostString(calculatePriceForSelectedChoices(product, selectedChoices))}</strong>
      <img src={getAsset('down-arrow.svg')} />
    </div>
  );
}
