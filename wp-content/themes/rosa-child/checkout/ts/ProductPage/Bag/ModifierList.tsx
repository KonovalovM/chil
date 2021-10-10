import React from 'react';
import map from 'lodash/map';
import filter from 'lodash/filter';
import find from 'lodash/find';
import { isValidExtraModifier, getSelectedChoicesInModifier, getNoHiddeModifier } from '../../Utilities/modelHelper';
import { OloRestaurantMenuProductModifier, OloBasketNewProductChoice } from '../../OloAPI';
import { ModifierComponent } from './ModifierComponent';

export interface ModifierListProps {
  modifiers: OloRestaurantMenuProductModifier[];
  selectedChoices: OloBasketNewProductChoice[];
  skippedModifiers: string[];
  removeChoice: (choiceId: string) => unknown;
}

export const ModifierList = ({ modifiers, selectedChoices, skippedModifiers, removeChoice }: ModifierListProps) => {

  const nonEmptyModifiers = modifiers.filter(modifier => {
    if (skippedModifiers.find(id => id === modifier.id)) {
      return true;
    }
    if (!getSelectedChoicesInModifier(modifier, selectedChoices).length) {
      return false;
    }
    return true;
  });

  if (!nonEmptyModifiers.length) {
    return (
      <div className="emptyMessage">
        <p>Add some choices to create your meal.</p>
      </div>
    );
  }

  const extraModifiers = filter(modifiers, isValidExtraModifier);

  return (
    <>
      {map(nonEmptyModifiers, modifier => {
        return (
          <div key={modifier.id} className="modifier">
            <h2>{modifier.description}</h2>
            <div>
              <ModifierComponent
                modifier={modifier}
                extraModifier={find(extraModifiers, { extraOf: modifier.id })}
                selectedChoices={selectedChoices}
                skipped={skippedModifiers.some(id => id === modifier.id)}
                removeChoice={removeChoice}
              />
            </div>
          </div>
        );
      })}
    </>
  );
};
