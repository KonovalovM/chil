import React from 'react';
import map from 'lodash/map';
import find from 'lodash/find';
import concat from 'lodash/concat';

import { OloRestaurantMenuProductModifier, OloBasketNewProductChoice } from '../../OloAPI';
import { getSelectedChoicesInModifier, getSelectedChoiceQuantity } from '../../Utilities/modelHelper';
import { getAsset } from '../../Utilities/assetsHelper';
import { ChoiceComponent } from './ChoiceComponent';

export interface ModifierComponentProps {
  modifier: OloRestaurantMenuProductModifier;
  extraModifier?: OloRestaurantMenuProductModifier;
  selectedChoices: OloBasketNewProductChoice[];
  skipped: boolean;
  removeChoice: (choiceId: string) => unknown;
}

export const ModifierComponent = ({ modifier, extraModifier, selectedChoices, skipped, removeChoice }: ModifierComponentProps) => {
  if (skipped) {
    return (
      <div className="skippedElement">
        <img src={getAsset('remove.svg')} />
        <h3>{'Skipped ' + modifier.description}</h3>
      </div>
    );
  }

  const choices = modifier.choices;
  const extraChoices = extraModifier ? extraModifier.choices : [];
  const allChoices = concat(choices, extraChoices);

  const selectedChoicesInModifier = getSelectedChoicesInModifier(modifier, selectedChoices);
  const selectedChoicesInExtraModifier = extraModifier ? getSelectedChoicesInModifier(extraModifier, selectedChoices) : [];
  const allSelectedChoicesInModifiers = concat(selectedChoicesInModifier, selectedChoicesInExtraModifier);

  return (
    <>
      {map(allSelectedChoicesInModifiers, selectedChoice => {
        const quantity = getSelectedChoiceQuantity(allSelectedChoicesInModifiers, selectedChoice.choiceid, modifier.fractional);
        const choice = find(allChoices, { id: selectedChoice.choiceid });
        return choice ? <ChoiceComponent key={choice.id} quantity={quantity} choice={choice} removeChoice={removeChoice} /> : null;
      })}
    </>
  );
};
