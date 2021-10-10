import React, { useCallback } from 'react';
import sumBy from 'lodash/sumBy';
import classnames from 'classnames';
import { OloRestaurantMenuProductModifier, OloBasketNewProductChoice } from '../../OloAPI';
import { getSelectedChoicesInModifier } from '../../Utilities/modelHelper';
import { getAsset } from '../../Utilities/assetsHelper';

export interface ExtraButtonProps {
  extraModifier: OloRestaurantMenuProductModifier;
  selectedChoices: OloBasketNewProductChoice[];
  openExtraChoices: (modifierId: string) => unknown;
}

export const ExtraButton = ({ extraModifier, selectedChoices, openExtraChoices }: ExtraButtonProps) => {
  const extraSelectedChoices = getSelectedChoicesInModifier(extraModifier, selectedChoices);
  const numExtraChoices = sumBy(extraSelectedChoices, 'quantity');
  const openExtraChoicesHandler = useCallback(() => openExtraChoices(extraModifier.id), [openExtraChoices, extraModifier.id]);

  return (
    <div className="optionButton marginRigth">
      <button onClick={openExtraChoicesHandler}>
        <div className="wrapper">
          <img src={getAsset('option_add.svg')} />
          <div className={classnames('quantity', { selected: numExtraChoices > 0 })}>{numExtraChoices}</div>
        </div>
      </button>
      <h3>Add extra</h3>
    </div>
  );
};
