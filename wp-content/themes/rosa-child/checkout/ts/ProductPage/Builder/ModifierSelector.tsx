import React from 'react';
import map from 'lodash/map';

import { OloRestaurantMenuProductModifier, OloBasketNewProductChoice } from '../../OloAPI';
import { ChoiceSelector } from './ChoiceSelector';
import { getSelectedChoicesInModifier, getSelectedChoiceQuantity, isModifierFull, isModifierOptional } from '../../Utilities/modelHelper';

import { ExtraButton } from './ExtraButton';
import { SkipButton } from './SkipButton';

export interface ModifierSelectorProps {
  modifier: OloRestaurantMenuProductModifier;
  error: string;
  selectedChoices: OloBasketNewProductChoice[];
  skipped: boolean;
  extraModifier?: OloRestaurantMenuProductModifier;
  addChoice: (choiceId: string) => unknown;
  setModifierSkipped: (modifierId: string, skipped: boolean) => unknown;
  openExtraChoices: (modifierId: string) => unknown;
  openChoiceInfo: (modifierId: string) => unknown;
}

export const ModifierSelector = (props: ModifierSelectorProps) => {
  const { modifier, error, selectedChoices, skipped, extraModifier, addChoice, setModifierSkipped, openExtraChoices, openChoiceInfo } = props;
  const selectedChoicesInModifier = getSelectedChoicesInModifier(modifier, selectedChoices);
  const selectedChoicesInExtraModifier = extraModifier ? getSelectedChoicesInModifier(extraModifier, selectedChoices) : [];

  const available = modifier.availability.available;
  const availabilityDescription = modifier.availability.description;

  const mandatory = modifier.mandatory;

  return (
    <div className="modifier">
      <h1>{modifier.description}</h1>
      <div className="errorMessage">{error}</div>
      {!available ? <div className="errorMessage">{availabilityDescription}</div> : null}
      <div className="choicesContainer">
        <div className="choices">
          {map(modifier.choices, choice => {
            const quantity = getSelectedChoiceQuantity(selectedChoicesInModifier, choice.id, modifier.fractional);
            return (
              <ChoiceSelector key={choice.id} choice={choice} quantity={quantity} mandatory={mandatory} addChoice={addChoice} openChoiceInfo={openChoiceInfo} />
            );
          })}
          {isModifierOptional(modifier) ? <SkipButton modifierId={modifier.id} skipped={skipped} setModifierSkipped={setModifierSkipped} /> : null}
          {extraModifier && shouldRenderExtraButton(selectedChoicesInModifier, selectedChoicesInExtraModifier, modifier.maxaggregatequantity) ? (
            <ExtraButton extraModifier={extraModifier} selectedChoices={selectedChoices} openExtraChoices={openExtraChoices} />
          ) : null}
        </div>
        {!available ? <div className="overlay" /> : null}
      </div>
    </div>
  );
};

function shouldRenderExtraButton(
  selectedChoicesInModifier: OloBasketNewProductChoice[],
  selectedChoicesInExtraModifier: OloBasketNewProductChoice[],
  maxaggregatequantity: number
) {
  if (!isModifierFull(selectedChoicesInModifier, maxaggregatequantity) && !selectedChoicesInExtraModifier.length) {
    return false;
  }

  return true;
}
