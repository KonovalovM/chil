import React, { useCallback } from 'react';
import { OloRestaurantMenuProductModifierChoice } from '../../OloAPI';
import { getCostString } from '../../Utilities/formatHelper';
import { getAsset } from '../../Utilities/assetsHelper';

export interface ChoiceComponentProps {
  choice: OloRestaurantMenuProductModifierChoice;
  quantity: string;
  removeChoice: (choiceId: string) => void;
}

export const ChoiceComponent = ({ choice, quantity, removeChoice }: ChoiceComponentProps) => {
  const removeChoiceHandler = useCallback(() => {
    removeChoice(choice.id);
  }, [choice.id, removeChoice]);

  return (
    <div key={choice.id} className="choice">
      <img src={choice.img} />
      <div className="info">
        <h3>{`${choice.name} (${quantity})`}</h3>
        <strong>{!choice.cost ? '' : `+ ${getCostString(choice.cost)}`}</strong>
      </div>
      <button onClick={removeChoiceHandler}>
        <img src={getAsset('remove.svg')} />
      </button>
    </div>
  );
};
